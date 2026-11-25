<?php

declare(strict_types=1);

namespace WaresWood\App;

use WaresWood\Admin\Request;
use WaresWood\Admin\Responder;
use WaresWood\Repository\CatalogMapper;
use WaresWood\Repository\CatalogRepositoryInterface;
use WaresWood\Repository\JsonCatalogRepository;
use WaresWood\Service\AuthServiceInterface;
use WaresWood\Service\CatalogService;
use WaresWood\Service\CatalogServiceInterface;
use WaresWood\Service\CategoryValidator;
use WaresWood\Service\CategoryValidatorInterface;
use WaresWood\Service\CsrfGuard;
use WaresWood\Service\DirectoryGuard;
use WaresWood\Service\DirectoryGuardInterface;
use WaresWood\Service\FileLoginThrottle;
use WaresWood\Service\IdGeneratorInterface;
use WaresWood\Service\ImageStorageInterface;
use WaresWood\Service\LocalImageStorage;
use WaresWood\Service\LoginThrottleInterface;
use WaresWood\Service\PriceFormatter;
use WaresWood\Service\ProductValidator;
use WaresWood\Service\ProductValidatorInterface;
use WaresWood\Service\SessionAuthService;
use WaresWood\Service\SlugIdGenerator;
use WaresWood\Service\UploadedImageCollector;

/**
 * Сборка зависимостей приложения.
 *
 * Composer и DI-контейнеры на этом хостинге недоступны, поэтому проводка
 * выполняется явно. Все зависимости создаются лениво и кешируются.
 */
final class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    /**
     * @param string $publicRoot Корень сайта: на сервере это public_html,
     *                           локально — корень репозитория.
     */
    public function __construct(
        private readonly string $publicRoot,
    ) {
    }

    public function paths(): Paths
    {
        return $this->resolve(Paths::class, function (): Paths {
            $dataDir = $this->publicRoot . '/data';
            $uploadsDir = $this->publicRoot . '/uploads/products';

            return new Paths(
                publicRoot: $this->publicRoot,
                dataDir: $dataDir,
                catalogFile: $dataDir . '/products.json',
                lockFile: $dataDir . '/products.lock',
                configFile: $dataDir . '/admin.config.php',
                throttleFile: $dataDir . '/login-attempts.json',
                uploadsDir: $uploadsDir,
                uploadsUrl: '/uploads/products',
            );
        });
    }

    /**
     * Единственная точка чтения суперглобальных массивов: дальше по коду
     * используется только объект запроса.
     */
    public function request(): Request
    {
        return $this->resolve(Request::class, static fn (): Request => new Request(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $_GET,
            $_POST,
            $_FILES,
            (string) ($_SERVER['SCRIPT_NAME'] ?? '/front/admin/index.php'),
        ));
    }

    public function responder(): Responder
    {
        return $this->resolve(
            Responder::class,
            fn (): Responder => new Responder($this->request()->scriptName()),
        );
    }

    public function configLoader(): ConfigLoader
    {
        return $this->resolve(
            ConfigLoader::class,
            fn (): ConfigLoader => new ConfigLoader($this->paths()->configFile),
        );
    }

    public function config(): Config
    {
        return $this->resolve(Config::class, fn (): Config => $this->configLoader()->load());
    }

    public function session(): Session
    {
        return $this->resolve(Session::class, static fn (): Session => new Session());
    }

    public function view(): View
    {
        return $this->resolve(
            View::class,
            fn (): View => new View($this->publicRoot . '/front/admin/views'),
        );
    }

    public function directoryGuard(): DirectoryGuardInterface
    {
        return $this->resolve(
            DirectoryGuardInterface::class,
            static fn (): DirectoryGuardInterface => new DirectoryGuard(),
        );
    }

    public function catalogRepository(): CatalogRepositoryInterface
    {
        return $this->resolve(CatalogRepositoryInterface::class, fn (): CatalogRepositoryInterface => new JsonCatalogRepository(
            $this->paths()->catalogFile,
            $this->paths()->lockFile,
            new CatalogMapper(),
            $this->directoryGuard(),
        ));
    }

    public function imageStorage(): ImageStorageInterface
    {
        return $this->resolve(ImageStorageInterface::class, fn (): ImageStorageInterface => new LocalImageStorage(
            $this->paths()->uploadsDir,
            $this->paths()->uploadsUrl,
            $this->directoryGuard(),
        ));
    }

    public function idGenerator(): IdGeneratorInterface
    {
        return $this->resolve(
            IdGeneratorInterface::class,
            static fn (): IdGeneratorInterface => new SlugIdGenerator(),
        );
    }

    public function productValidator(): ProductValidatorInterface
    {
        return $this->resolve(
            ProductValidatorInterface::class,
            static fn (): ProductValidatorInterface => new ProductValidator(),
        );
    }

    public function categoryValidator(): CategoryValidatorInterface
    {
        return $this->resolve(
            CategoryValidatorInterface::class,
            static fn (): CategoryValidatorInterface => new CategoryValidator(),
        );
    }

    public function catalogService(): CatalogServiceInterface
    {
        return $this->resolve(CatalogServiceInterface::class, fn (): CatalogServiceInterface => new CatalogService(
            $this->catalogRepository(),
            $this->imageStorage(),
            $this->idGenerator(),
            $this->productValidator(),
            $this->categoryValidator(),
        ));
    }

    public function loginThrottle(): LoginThrottleInterface
    {
        return $this->resolve(LoginThrottleInterface::class, fn (): LoginThrottleInterface => new FileLoginThrottle(
            $this->paths()->throttleFile,
            $this->directoryGuard(),
        ));
    }

    public function authService(): AuthServiceInterface
    {
        return $this->resolve(AuthServiceInterface::class, fn (): AuthServiceInterface => new SessionAuthService(
            $this->session(),
            $this->config(),
            $this->loginThrottle(),
            $this->clientIp(),
        ));
    }

    public function csrfGuard(): CsrfGuard
    {
        return $this->resolve(CsrfGuard::class, fn (): CsrfGuard => new CsrfGuard($this->session()));
    }

    public function uploadedImageCollector(): UploadedImageCollector
    {
        return $this->resolve(
            UploadedImageCollector::class,
            static fn (): UploadedImageCollector => new UploadedImageCollector(),
        );
    }

    public function priceFormatter(): PriceFormatter
    {
        return $this->resolve(PriceFormatter::class, static fn (): PriceFormatter => new PriceFormatter());
    }

    /**
     * IP берётся только из REMOTE_ADDR: заголовки вида X-Forwarded-For
     * подделываются клиентом, а троттлинг по ним обходился бы одним запросом.
     */
    private function clientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        return is_string($ip) && $ip !== '' ? $ip : 'unknown';
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $id
     * @param callable(): T   $factory
     *
     * @return T
     */
    private function resolve(string $id, callable $factory): object
    {
        if (!isset($this->instances[$id])) {
            $this->instances[$id] = $factory();
        }

        /** @var T */
        return $this->instances[$id];
    }
}
