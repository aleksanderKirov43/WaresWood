<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\NotFoundException;
use WaresWood\Exception\StorageException;
use WaresWood\Exception\ValidationException;
use WaresWood\Model\Catalog;
use WaresWood\Model\Category;
use WaresWood\Model\Product;
use WaresWood\Service\Model\CategoryInput;
use WaresWood\Service\Model\ProductInput;
use WaresWood\Service\Model\UploadedImage;

/**
 * Сценарии администрирования каталога.
 */
interface CatalogServiceInterface
{
    public function getCatalog(): Catalog;

    /**
     * @throws NotFoundException
     */
    public function getCategory(string $categoryId): Category;

    /**
     * @throws NotFoundException
     */
    public function getProduct(string $productId): Product;

    /**
     * Категория, в которой находится товар.
     *
     * @throws NotFoundException
     */
    public function getProductCategoryId(string $productId): string;

    /**
     * @return string Идентификатор созданной категории.
     *
     * @throws ValidationException
     * @throws StorageException
     */
    public function createCategory(CategoryInput $input): string;

    /**
     * @throws NotFoundException
     * @throws ValidationException
     * @throws StorageException
     */
    public function updateCategory(string $categoryId, CategoryInput $input): void;

    /**
     * Удаляет категорию вместе со всеми товарами и их фотографиями.
     *
     * @throws NotFoundException
     * @throws StorageException
     */
    public function deleteCategory(string $categoryId): void;

    /**
     * @param int $offset Сдвиг в порядке отображения: -1 вверх, 1 вниз.
     *
     * @throws NotFoundException
     * @throws StorageException
     */
    public function moveCategory(string $categoryId, int $offset): void;

    /**
     * @param list<UploadedImage> $uploads
     *
     * @return string Идентификатор созданного товара.
     *
     * @throws NotFoundException
     * @throws ValidationException
     * @throws StorageException
     */
    public function createProduct(ProductInput $input, array $uploads): string;

    /**
     * Обновляет товар; при смене категории переносит его без изменения идентификатора.
     *
     * @param list<UploadedImage> $uploads
     *
     * @throws NotFoundException
     * @throws ValidationException
     * @throws StorageException
     */
    public function updateProduct(string $productId, ProductInput $input, array $uploads): void;

    /**
     * @throws NotFoundException
     * @throws StorageException
     */
    public function deleteProduct(string $productId): void;

    /**
     * @param int $offset Сдвиг в порядке отображения внутри категории.
     *
     * @throws NotFoundException
     * @throws StorageException
     */
    public function moveProduct(string $productId, int $offset): void;

    /**
     * @throws NotFoundException
     * @throws StorageException
     */
    public function deleteProductImage(string $productId, string $webPath): void;

    /**
     * Делает фотографию главной, перемещая её в начало списка.
     *
     * @throws NotFoundException
     * @throws StorageException
     */
    public function makeProductImagePrimary(string $productId, string $webPath): void;
}
