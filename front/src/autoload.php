<?php

declare(strict_types=1);

/**
 * Минимальный PSR-4 автозагрузчик.
 *
 * Composer на shared-хостинге не используется, поэтому классы префикса
 * WaresWood\ отображаются напрямую в структуру каталога front/src.
 */

require_once __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'WaresWood\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require $path;
    }
});
