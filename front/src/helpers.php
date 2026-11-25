<?php

declare(strict_types=1);

if (!function_exists('ww_e')) {
    /**
     * Экранирование значения для вывода в HTML-шаблоне.
     */
    function ww_e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
