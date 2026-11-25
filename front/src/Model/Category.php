<?php

declare(strict_types=1);

namespace WaresWood\Model;

/**
 * Категория каталога. Структура данных без поведения.
 */
final class Category
{
    /**
     * @param list<Product> $products Товары в порядке отображения на сайте.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly array $products,
    ) {
    }
}
