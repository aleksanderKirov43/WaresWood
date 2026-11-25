<?php

declare(strict_types=1);

namespace WaresWood\Model;

/**
 * Товар каталога. Структура данных без поведения.
 */
final class Product
{
    /**
     * @param string       $id          Уникальный в пределах каталога идентификатор (используется в URL).
     * @param int          $price       Цена в рублях.
     * @param string       $priceUnit   Единица измерения цены, например «за изделие».
     * @param list<string> $images      Веб-пути изображений; первый считается главным.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly int $price,
        public readonly string $priceUnit,
        public readonly array $images,
    ) {
    }
}
