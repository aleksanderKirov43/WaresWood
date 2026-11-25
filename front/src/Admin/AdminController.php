<?php

declare(strict_types=1);

namespace WaresWood\Admin;

use Throwable;
use WaresWood\App\Container;
use WaresWood\Exception\AppException;
use WaresWood\Exception\ValidationException;
use WaresWood\Model\Catalog;
use WaresWood\Service\CatalogServiceInterface;
use WaresWood\Service\Model\CategoryInput;
use WaresWood\Service\Model\ProductInput;

/**
 * Маршрутизация и обработка запросов админки каталога.
 *
 * Контроллер только разбирает запрос, вызывает сервисы и выбирает представление;
 * бизнес-правила живут в слое Service.
 */
final class AdminController
{
    public function __construct(
        private readonly Container $container,
        private readonly Request $request,
        private readonly Responder $responder,
    ) {
    }

    public function handle(): void
    {
        $session = $this->container->session();
        $session->start();

        $this->responder->sendSecurityHeaders();

        // Пока нет конфигурации администратора, доступен только помощник настройки.
        if (!$this->container->configLoader()->exists()) {
            $this->setupController()->handle($this->request);

            return;
        }

        try {
            $auth = $this->container->authService();

            if ($this->request->isPost() && $this->request->action() === 'logout') {
                $this->container->csrfGuard()->assertValid($this->request->string('csrf'));
                $auth->logout();
                $session->addFlash('success', 'Вы вышли из админки.');

                $this->responder->redirect();
            }

            if (!$auth->isAuthenticated()) {
                $this->renderLogin();

                return;
            }

            if ($this->request->isPost()) {
                $this->container->csrfGuard()->assertValid($this->request->string('csrf'));
                $this->handleCommand();

                return;
            }

            $this->renderPage();
        } catch (AppException $exception) {
            $this->fail($exception->getMessage());
        } catch (Throwable $exception) {
            error_log('WaresWood admin: ' . $exception->getMessage());

            $this->fail('Внутренняя ошибка. Подробности записаны в лог сервера.');
        }
    }

    /**
     * Обработка ошибки. Для команд достаточно вернуть пользователя к каталогу,
     * но при ошибке на самой странице каталога редирект вёл бы в бесконечный цикл,
     * поэтому GET-запросы получают страницу с описанием проблемы.
     */
    private function fail(string $message): never
    {
        if ($this->request->isPost()) {
            $this->container->session()->addFlash('error', $message);

            $this->responder->redirect();
        }

        $this->container->view()->renderPage('error', 'Ошибка', [
            'message' => $message,
            'authenticated' => false,
            'flashes' => [],
        ]);

        exit;
    }

    private function renderLogin(): void
    {
        $session = $this->container->session();
        $error = null;

        if ($this->request->isPost() && $this->request->action() === 'login') {
            try {
                $this->container->csrfGuard()->assertValid($this->request->string('csrf'));
                $this->container->authService()->login(
                    $this->request->string('login'),
                    $this->request->raw('password'),
                );

                $this->responder->redirect();
            } catch (AppException $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->container->view()->renderPage('login', 'Вход в админку', [
            'csrf' => $this->container->csrfGuard()->token(),
            'error' => $error,
            'login' => $this->request->string('login'),
            'flashes' => $session->takeFlashes(),
            'authenticated' => false,
        ]);
    }

    /**
     * Изменяющие действия. Каждая ветка завершается редиректом.
     */
    private function handleCommand(): void
    {
        $session = $this->container->session();
        $catalog = $this->container->catalogService();
        $action = $this->request->action();

        switch ($action) {
            case 'category.save':
                $this->saveCategory($catalog);

            case 'category.delete':
                $catalog->deleteCategory($this->request->string('id'));
                $session->addFlash('success', 'Категория и её товары удалены.');

                $this->responder->redirect();

            case 'category.move':
                $catalog->moveCategory($this->request->string('id'), $this->direction());

                $this->responder->redirect();

            case 'product.save':
                $this->saveProduct($catalog);

            case 'product.delete':
                $catalog->deleteProduct($this->request->string('id'));
                $session->addFlash('success', 'Товар удалён.');

                $this->responder->redirect();

            case 'product.move':
                $catalog->moveProduct($this->request->string('id'), $this->direction());

                $this->responder->redirect();

            case 'image.delete':
                $catalog->deleteProductImage($this->request->string('id'), $this->request->string('image'));
                $session->addFlash('success', 'Фотография удалена.');

                $this->responder->redirect([
                    'action' => 'product.form',
                    'id' => $this->request->string('id'),
                ]);

            case 'image.primary':
                $catalog->makeProductImagePrimary($this->request->string('id'), $this->request->string('image'));
                $session->addFlash('success', 'Фотография сделана главной.');

                $this->responder->redirect([
                    'action' => 'product.form',
                    'id' => $this->request->string('id'),
                ]);

            default:
                $session->addFlash('error', 'Неизвестное действие.');

                $this->responder->redirect();
        }
    }

    private function saveCategory(CatalogServiceInterface $catalog): never
    {
        $session = $this->container->session();
        $id = $this->request->string('id');
        $input = new CategoryInput($this->request->string('title'), $this->request->text('subtitle'));

        try {
            if ($id === '') {
                $catalog->createCategory($input);
                $session->addFlash('success', 'Категория добавлена.');
            } else {
                $catalog->updateCategory($id, $input);
                $session->addFlash('success', 'Категория сохранена.');
            }
        } catch (ValidationException $exception) {
            $this->formState()->remember($this->request->scalarBody(), $exception->errors());

            $this->responder->redirect(['action' => 'category.form', 'id' => $id]);
        }

        $this->responder->redirect();
    }

    private function saveProduct(CatalogServiceInterface $catalog): never
    {
        $session = $this->container->session();
        $id = $this->request->string('id');

        $input = new ProductInput(
            $this->request->string('categoryId'),
            $this->request->string('title'),
            $this->request->text('description'),
            $this->request->int('price'),
            $this->request->string('priceUnit'),
        );

        $uploads = $this->container->uploadedImageCollector()->collect($this->request->files('images'));

        try {
            if ($id === '') {
                $id = $catalog->createProduct($input, $uploads);
                $session->addFlash('success', 'Товар добавлен.');
            } else {
                $catalog->updateProduct($id, $input, $uploads);
                $session->addFlash('success', 'Товар сохранён.');
            }
        } catch (ValidationException $exception) {
            $this->formState()->remember($this->request->scalarBody(), $exception->errors());

            $this->responder->redirect(['action' => 'product.form', 'id' => $id]);
        }

        $this->responder->redirect(['action' => 'product.form', 'id' => $id]);
    }

    /**
     * Страницы админки.
     */
    private function renderPage(): void
    {
        $view = $this->container->view();
        $catalogService = $this->container->catalogService();
        $session = $this->container->session();
        $formState = $this->formState()->take();

        $shared = [
            'csrf' => $this->container->csrfGuard()->token(),
            'flashes' => $session->takeFlashes(),
            'errors' => $formState['errors'],
            'old' => $formState['old'],
            'authenticated' => true,
            'adminLogin' => $this->container->authService()->currentLogin(),
            'basePath' => $this->request->scriptName(),
        ];

        switch ($this->request->action()) {
            case 'category.form':
                $id = $this->request->string('id');
                $category = $id === '' ? null : $catalogService->getCategory($id);

                $view->renderPage('category_form', $category === null ? 'Новая категория' : 'Категория', $shared + [
                    'category' => $category,
                ]);

                return;

            case 'product.form':
                $id = $this->request->string('id');
                $product = $id === '' ? null : $catalogService->getProduct($id);
                $catalog = $catalogService->getCatalog();

                $view->renderPage('product_form', $product === null ? 'Новый товар' : 'Товар', $shared + [
                    'product' => $product,
                    'categories' => $catalog->categories,
                    'currentCategoryId' => $product === null
                        ? $this->request->string('categoryId')
                        : $catalogService->getProductCategoryId($product->id),
                ]);

                return;

            default:
                $catalog = $catalogService->getCatalog();

                $view->renderPage('catalog', 'Каталог', $shared + [
                    'catalog' => $catalog,
                    'priceFormatter' => $this->container->priceFormatter(),
                    'isEmpty' => $this->isEmpty($catalog),
                ]);
        }
    }

    private function isEmpty(Catalog $catalog): bool
    {
        return $catalog->categories === [];
    }

    /**
     * Направление перемещения элемента в списке.
     */
    private function direction(): int
    {
        return $this->request->string('direction') === 'up' ? -1 : 1;
    }

    private function formState(): FormState
    {
        return new FormState($this->container->session());
    }

    private function setupController(): SetupController
    {
        return new SetupController(
            $this->container->view(),
            $this->container->paths(),
            $this->responder,
        );
    }
}
