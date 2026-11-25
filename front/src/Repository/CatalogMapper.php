<?php

declare(strict_types=1);

namespace WaresWood\Repository;

use WaresWood\Model\Catalog;
use WaresWood\Model\Category;
use WaresWood\Model\Product;

/**
 * Преобразование между моделями каталога и его представлением в JSON.
 *
 * Разбор намеренно устойчив к неполным данным: отсутствующие поля заполняются
 * значениями по умолчанию, чтобы ручная правка файла не ломала публичный сайт.
 */
final class CatalogMapper
{
    public const SCHEMA_VERSION = 1;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Catalog $catalog): array
    {
        $categories = [];

        foreach ($catalog->categories as $category) {
            $products = [];

            foreach ($category->products as $product) {
                $products[] = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'price' => $product->price,
                    'priceUnit' => $product->priceUnit,
                    'images' => $product->images,
                ];
            }

            $categories[] = [
                'id' => $category->id,
                'title' => $category->title,
                'subtitle' => $category->subtitle,
                'products' => $products,
            ];
        }

        return [
            'version' => self::SCHEMA_VERSION,
            'categories' => $categories,
        ];
    }

    /**
     * @param array<mixed> $raw
     */
    public function fromArray(array $raw): Catalog
    {
        $rawCategories = $raw['categories'] ?? [];

        if (!is_array($rawCategories)) {
            return new Catalog([]);
        }

        $categories = [];

        foreach ($rawCategories as $rawCategory) {
            if (!is_array($rawCategory)) {
                continue;
            }

            $id = $this->readString($rawCategory, 'id');

            if ($id === '') {
                continue;
            }

            $categories[] = new Category(
                $id,
                $this->readString($rawCategory, 'title'),
                $this->readString($rawCategory, 'subtitle'),
                $this->readProducts($rawCategory['products'] ?? []),
            );
        }

        return new Catalog($categories);
    }

    /**
     * @return list<Product>
     */
    private function readProducts(mixed $rawProducts): array
    {
        if (!is_array($rawProducts)) {
            return [];
        }

        $products = [];

        foreach ($rawProducts as $rawProduct) {
            if (!is_array($rawProduct)) {
                continue;
            }

            $id = $this->readString($rawProduct, 'id');

            if ($id === '') {
                continue;
            }

            $products[] = new Product(
                $id,
                $this->readString($rawProduct, 'title'),
                $this->readString($rawProduct, 'description'),
                (int) ($rawProduct['price'] ?? 0),
                $this->readString($rawProduct, 'priceUnit'),
                $this->readImages($rawProduct['images'] ?? []),
            );
        }

        return $products;
    }

    /**
     * @return list<string>
     */
    private function readImages(mixed $rawImages): array
    {
        if (!is_array($rawImages)) {
            return [];
        }

        $images = [];

        foreach ($rawImages as $image) {
            if (is_string($image) && $image !== '') {
                $images[] = $image;
            }
        }

        return $images;
    }

    /**
     * @param array<mixed> $source
     */
    private function readString(array $source, string $key): string
    {
        $value = $source[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}
