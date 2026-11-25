<?php

declare(strict_types=1);

namespace WaresWood\Repository;

use JsonException;
use WaresWood\Exception\StorageException;
use WaresWood\Model\Catalog;
use WaresWood\Service\DirectoryGuardInterface;

/**
 * Хранение каталога в JSON-файле.
 *
 * Запись атомарная: JSON пишется во временный файл и переименовывается, поэтому
 * читатели никогда не видят частично записанный каталог. Параллельные записи
 * сериализуются через отдельный lock-файл.
 */
final class JsonCatalogRepository implements CatalogRepositoryInterface
{
    public function __construct(
        private readonly string $catalogFile,
        private readonly string $lockFile,
        private readonly CatalogMapper $mapper,
        private readonly DirectoryGuardInterface $directoryGuard,
    ) {
    }

    public function load(): Catalog
    {
        if (!is_file($this->catalogFile)) {
            return new Catalog([]);
        }

        $json = file_get_contents($this->catalogFile);

        if ($json === false) {
            throw new StorageException('Не удалось прочитать файл каталога');
        }

        if (trim($json) === '') {
            return new Catalog([]);
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new StorageException('Файл каталога содержит некорректный JSON', 0, $exception);
        }

        if (!is_array($decoded)) {
            throw new StorageException('Файл каталога должен содержать JSON-объект');
        }

        return $this->mapper->fromArray($decoded);
    }

    public function save(Catalog $catalog): void
    {
        $this->directoryGuard->ensureProtectedDirectory(dirname($this->catalogFile));

        try {
            $json = json_encode(
                $this->mapper->toArray($catalog),
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            );
        } catch (JsonException $exception) {
            throw new StorageException('Не удалось сериализовать каталог в JSON', 0, $exception);
        }

        $lock = $this->acquireLock();

        try {
            $temporary = $this->catalogFile . '.' . bin2hex(random_bytes(6)) . '.tmp';

            if (file_put_contents($temporary, $json) === false) {
                throw new StorageException('Не удалось записать временный файл каталога');
            }

            if (!rename($temporary, $this->catalogFile)) {
                @unlink($temporary);

                throw new StorageException('Не удалось заменить файл каталога');
            }

            @chmod($this->catalogFile, 0644);
        } finally {
            $this->releaseLock($lock);
        }
    }

    /**
     * @return resource
     */
    private function acquireLock()
    {
        $handle = fopen($this->lockFile, 'c');

        if ($handle === false) {
            throw new StorageException('Не удалось открыть файл блокировки каталога');
        }

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);

            throw new StorageException('Не удалось получить блокировку на запись каталога');
        }

        return $handle;
    }

    /**
     * @param resource $handle
     */
    private function releaseLock($handle): void
    {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
