<?php

declare(strict_types=1);

namespace WaresWood\Service;

/**
 * Приведение цены к виду, привычному для витрины: «1 500 руб.».
 */
final class PriceFormatter
{
    public function format(int $price): string
    {
        return number_format($price, 0, ',', ' ') . ' руб.';
    }
}
