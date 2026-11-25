<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\NotFoundException;
use WaresWood\Model\Catalog;
use WaresWood\Model\Category;
use WaresWood\Model\Product;
use WaresWood\Repository\CatalogRepositoryInterface;
use WaresWood\Service\Model\CategoryInput;
use WaresWood\Service\Model\ProductInput;

/**
 * Сценарии администрирования каталога.
 *
 * Модели каталога неизменяемы, поэтому любая правка собирает новую структуру
 * и сохраняет её целиком. Каталог небольшой, зато исключены частичные состояния.
 */
final class CatalogService implements CatalogServiceInterface
{
    public function __construct(
        private readonly CatalogRepositoryInterface $repository,
        private readonly ImageStorageInterface $imageStorage,
        private readonly IdGeneratorInterface $idGenerator,
        private readonly ProductValidatorInterface $productValidator,
        private readonly CategoryValidatorInterface $categoryValidator,
    ) {
    }

    public function getCatalog(): Catalog
    {
        return $this->repository->load();
    }

    public function getCategory(string $categoryId): Category
    {
        $catalog = $this->repository->load();

        return $catalog->categories[$this->requireCategoryIndex($catalog, $categoryId)];
    }

    public function getProduct(string $productId): Product
    {
        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);

        return $catalog->categories[$categoryIndex]->products[$productIndex];
    }

    public function getProductCategoryId(string $productId): string
    {
        $catalog = $this->repository->load();
        [$categoryIndex] = $this->requireProductLocation($catalog, $productId);

        return $catalog->categories[$categoryIndex]->id;
    }

    public function createCategory(CategoryInput $input): string
    {
        $this->categoryValidator->validate($input);

        $catalog = $this->repository->load();
        $id = $this->idGenerator->generate($input->title, $this->collectCategoryIds($catalog));

        $categories = $catalog->categories;
        $categories[] = new Category($id, trim($input->title), trim($input->subtitle), []);

        $this->repository->save(new Catalog($categories));

        return $id;
    }

    public function updateCategory(string $categoryId, CategoryInput $input): void
    {
        $this->categoryValidator->validate($input);

        $catalog = $this->repository->load();
        $index = $this->requireCategoryIndex($catalog, $categoryId);
        $current = $catalog->categories[$index];

        $this->repository->save($this->withCategory($catalog, $index, new Category(
            $current->id,
            trim($input->title),
            trim($input->subtitle),
            $current->products,
        )));
    }

    public function deleteCategory(string $categoryId): void
    {
        $catalog = $this->repository->load();
        $index = $this->requireCategoryIndex($catalog, $categoryId);

        $categories = $catalog->categories;
        $removed = $categories[$index];
        array_splice($categories, $index, 1);

        $this->repository->save(new Catalog($categories));

        // Файлы удаляем только после успешной записи: иначе сбой сохранения
        // оставил бы каталог со ссылками на уже удалённые фотографии.
        foreach ($removed->products as $product) {
            $this->deleteImages($product->images);
        }
    }

    public function moveCategory(string $categoryId, int $offset): void
    {
        $catalog = $this->repository->load();
        $index = $this->requireCategoryIndex($catalog, $categoryId);

        $categories = $this->shift($catalog->categories, $index, $offset);

        if ($categories === null) {
            return;
        }

        $this->repository->save(new Catalog($categories));
    }

    public function createProduct(ProductInput $input, array $uploads): string
    {
        $this->productValidator->validate($input);

        $catalog = $this->repository->load();
        $categoryIndex = $this->requireCategoryIndex($catalog, $input->categoryId);
        $id = $this->idGenerator->generate($input->title, $this->collectProductIds($catalog));

        $images = $this->imageStorage->storeMany($uploads);

        try {
            $category = $catalog->categories[$categoryIndex];
            $products = $category->products;
            $products[] = $this->buildProduct($id, $input, $images);

            $this->repository->save($this->withCategory(
                $catalog,
                $categoryIndex,
                $this->withProducts($category, $products),
            ));
        } catch (\Throwable $exception) {
            $this->deleteImages($images);

            throw $exception;
        }

        return $id;
    }

    public function updateProduct(string $productId, ProductInput $input, array $uploads): void
    {
        $this->productValidator->validate($input);

        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);
        $targetCategoryIndex = $this->requireCategoryIndex($catalog, $input->categoryId);

        $current = $catalog->categories[$categoryIndex]->products[$productIndex];
        $uploaded = $this->imageStorage->storeMany($uploads);

        try {
            $updated = $this->buildProduct($productId, $input, [...$current->images, ...$uploaded]);

            if ($targetCategoryIndex === $categoryIndex) {
                $products = $catalog->categories[$categoryIndex]->products;
                $products[$productIndex] = $updated;

                $catalog = $this->withCategory(
                    $catalog,
                    $categoryIndex,
                    $this->withProducts($catalog->categories[$categoryIndex], $products),
                );
            } else {
                $catalog = $this->moveProductBetweenCategories(
                    $catalog,
                    $categoryIndex,
                    $productIndex,
                    $targetCategoryIndex,
                    $updated,
                );
            }

            $this->repository->save($catalog);
        } catch (\Throwable $exception) {
            $this->deleteImages($uploaded);

            throw $exception;
        }
    }

    public function deleteProduct(string $productId): void
    {
        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);

        $category = $catalog->categories[$categoryIndex];
        $products = $category->products;
        $removed = $products[$productIndex];
        array_splice($products, $productIndex, 1);

        $this->repository->save($this->withCategory(
            $catalog,
            $categoryIndex,
            $this->withProducts($category, $products),
        ));

        $this->deleteImages($removed->images);
    }

    public function moveProduct(string $productId, int $offset): void
    {
        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);

        $category = $catalog->categories[$categoryIndex];
        $products = $this->shift($category->products, $productIndex, $offset);

        if ($products === null) {
            return;
        }

        $this->repository->save($this->withCategory(
            $catalog,
            $categoryIndex,
            $this->withProducts($category, $products),
        ));
    }

    public function deleteProductImage(string $productId, string $webPath): void
    {
        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);

        $category = $catalog->categories[$categoryIndex];
        $product = $category->products[$productIndex];
        $images = array_values(array_filter(
            $product->images,
            static fn (string $image): bool => $image !== $webPath,
        ));

        if (count($images) === count($product->images)) {
            throw new NotFoundException('Фотография не найдена в карточке товара');
        }

        $this->saveProduct($catalog, $categoryIndex, $productIndex, $this->withImages($product, $images));

        $this->imageStorage->delete($webPath);
    }

    public function makeProductImagePrimary(string $productId, string $webPath): void
    {
        $catalog = $this->repository->load();
        [$categoryIndex, $productIndex] = $this->requireProductLocation($catalog, $productId);

        $product = $catalog->categories[$categoryIndex]->products[$productIndex];
        $position = array_search($webPath, $product->images, true);

        if ($position === false) {
            throw new NotFoundException('Фотография не найдена в карточке товара');
        }

        if ($position === 0) {
            return;
        }

        $images = $product->images;
        array_splice($images, $position, 1);
        array_unshift($images, $webPath);

        $this->saveProduct($catalog, $categoryIndex, $productIndex, $this->withImages($product, $images));
    }

    /**
     * @param list<string> $images
     */
    private function buildProduct(string $id, ProductInput $input, array $images): Product
    {
        return new Product(
            $id,
            trim($input->title),
            trim($input->description),
            $input->price,
            trim($input->priceUnit),
            $images,
        );
    }

    /**
     * @param list<string> $images
     */
    private function withImages(Product $product, array $images): Product
    {
        return new Product(
            $product->id,
            $product->title,
            $product->description,
            $product->price,
            $product->priceUnit,
            $images,
        );
    }

    /**
     * @param list<Product> $products
     */
    private function withProducts(Category $category, array $products): Category
    {
        return new Category($category->id, $category->title, $category->subtitle, array_values($products));
    }

    private function withCategory(Catalog $catalog, int $index, Category $category): Catalog
    {
        $categories = $catalog->categories;
        $categories[$index] = $category;

        return new Catalog($categories);
    }

    private function saveProduct(Catalog $catalog, int $categoryIndex, int $productIndex, Product $product): void
    {
        $category = $catalog->categories[$categoryIndex];
        $products = $category->products;
        $products[$productIndex] = $product;

        $this->repository->save($this->withCategory(
            $catalog,
            $categoryIndex,
            $this->withProducts($category, $products),
        ));
    }

    private function moveProductBetweenCategories(
        Catalog $catalog,
        int $sourceIndex,
        int $productIndex,
        int $targetIndex,
        Product $product,
    ): Catalog {
        $source = $catalog->categories[$sourceIndex];
        $sourceProducts = $source->products;
        array_splice($sourceProducts, $productIndex, 1);

        $catalog = $this->withCategory($catalog, $sourceIndex, $this->withProducts($source, $sourceProducts));

        $target = $catalog->categories[$targetIndex];
        $targetProducts = $target->products;
        $targetProducts[] = $product;

        return $this->withCategory($catalog, $targetIndex, $this->withProducts($target, $targetProducts));
    }

    /**
     * Перемещает элемент списка на $offset позиций.
     *
     * @template T
     *
     * @param list<T> $items
     *
     * @return list<T>|null null, если сдвиг выходит за границы списка.
     */
    private function shift(array $items, int $index, int $offset): ?array
    {
        $target = $index + $offset;

        if ($offset === 0 || $target < 0 || $target >= count($items)) {
            return null;
        }

        $moved = $items[$index];
        array_splice($items, $index, 1);
        array_splice($items, $target, 0, [$moved]);

        return array_values($items);
    }

    private function requireCategoryIndex(Catalog $catalog, string $categoryId): int
    {
        foreach ($catalog->categories as $index => $category) {
            if ($category->id === $categoryId) {
                return $index;
            }
        }

        throw new NotFoundException(sprintf('Категория «%s» не найдена', $categoryId));
    }

    /**
     * @return array{0: int, 1: int} Индексы категории и товара внутри неё.
     */
    private function requireProductLocation(Catalog $catalog, string $productId): array
    {
        foreach ($catalog->categories as $categoryIndex => $category) {
            foreach ($category->products as $productIndex => $product) {
                if ($product->id === $productId) {
                    return [$categoryIndex, $productIndex];
                }
            }
        }

        throw new NotFoundException(sprintf('Товар «%s» не найден', $productId));
    }

    /**
     * @return list<string>
     */
    private function collectCategoryIds(Catalog $catalog): array
    {
        return array_map(static fn (Category $category): string => $category->id, $catalog->categories);
    }

    /**
     * Идентификаторы товаров уникальны в пределах всего каталога: публичная
     * страница product.php ищет товар по id, не зная категории.
     *
     * @return list<string>
     */
    private function collectProductIds(Catalog $catalog): array
    {
        $ids = [];

        foreach ($catalog->categories as $category) {
            foreach ($category->products as $product) {
                $ids[] = $product->id;
            }
        }

        return $ids;
    }

    /**
     * @param list<string> $images
     */
    private function deleteImages(array $images): void
    {
        foreach ($images as $image) {
            $this->imageStorage->delete($image);
        }
    }
}
