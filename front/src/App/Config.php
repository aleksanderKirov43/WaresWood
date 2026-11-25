<?php

declare(strict_types=1);

namespace WaresWood\App;

/**
 * Конфигурация администратора. Структура данных без поведения.
 */
final class Config
{
    public function __construct(
        public readonly string $adminLogin,
        public readonly string $adminPasswordHash,
    ) {
    }
}
