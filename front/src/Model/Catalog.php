<?php

declare(strict_types=1);

namespace WaresWood\Model;

/**
 * Полный каталог сайта. Структура данных без поведения.
 */
final class Catalog
{
    /**
     * @param list<Category> $categories Категории в порядке отображения на сайте.
     */
    public function __construct(
        public readonly array $categories,
    ) {
    }
}
