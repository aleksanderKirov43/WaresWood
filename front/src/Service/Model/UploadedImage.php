<?php

declare(strict_types=1);

namespace WaresWood\Service\Model;

/**
 * Одно загруженное через форму изображение. Структура данных без поведения.
 */
final class UploadedImage
{
    /**
     * @param int $error Код ошибки загрузки из UPLOAD_ERR_*.
     */
    public function __construct(
        public readonly string $temporaryPath,
        public readonly string $originalName,
        public readonly int $size,
        public readonly int $error,
    ) {
    }
}
