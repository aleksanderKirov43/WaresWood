<?php

declare(strict_types=1);

namespace WaresWood\App;

/**
 * Пути приложения. Структура данных без поведения.
 *
 * Данные каталога и загруженные фотографии намеренно лежат вне front/ и deploy/:
 * эти каталоги при каждом push в main перезаливаются FTP-деплоем, поэтому любые
 * изменения, сделанные из админки внутри них, были бы затёрты.
 */
final class Paths
{
    public function __construct(
        public readonly string $publicRoot,
        public readonly string $dataDir,
        public readonly string $catalogFile,
        public readonly string $lockFile,
        public readonly string $configFile,
        public readonly string $throttleFile,
        public readonly string $uploadsDir,
        public readonly string $uploadsUrl,
    ) {
    }
}
