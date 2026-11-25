<?php

declare(strict_types=1);

namespace WaresWood\Service\Model;

/**
 * Данные товара, пришедшие из формы админки. Структура данных без поведения.
 */
final class ProductInput
{
    public function __construct(
        public readonly string $categoryId,
        public readonly string $title,
        public readonly string $description,
        public readonly int $price,
        public readonly string $priceUnit,
    ) {
    }
}
