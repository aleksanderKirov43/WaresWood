<?php

declare(strict_types=1);

namespace WaresWood\Service\Model;

/**
 * Данные категории, пришедшие из формы админки. Структура данных без поведения.
 */
final class CategoryInput
{
    public function __construct(
        public readonly string $title,
        public readonly string $subtitle,
    ) {
    }
}
