<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\StorageException;
use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\UploadedImage;

/**
 * Хранение фотографий товаров в локальном каталоге вне зоны деплоя.
 *
 * Загруженный файл никогда не сохраняется «как есть»: при доступной GD-библиотеке
 * он перекодируется. Это отсекает файлы-полиглоты (картинка с PHP-кодом внутри)
 * и попутно убирает EXIF и лишний вес.
 */
final class LocalImageStorage implements ImageStorageInterface
{
    private const MAX_FILE_SIZE = 8 * 1024 * 1024;

    private const MAX_DIMENSION = 2000;

    private const JPEG_QUALITY = 85;

    /** Допустимые MIME-типы и соответствующие им расширения. */
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private readonly string $uploadsDir,
        private readonly string $uploadsUrl,
        private readonly DirectoryGuardInterface $directoryGuard,
    ) {
    }

    public function store(UploadedImage $image): string
    {
        $this->assertUploadSucceeded($image);

        $mimeType = $this->detectMimeType($image->temporaryPath);
        $extension = self::ALLOWED_TYPES[$mimeType];

        $this->directoryGuard->ensurePublicUploadDirectory($this->uploadsDir);

        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
        $target = $this->uploadsDir . '/' . $fileName;

        if (!$this->reencode($image->temporaryPath, $mimeType, $target)) {
            if (!$this->moveWithoutReencoding($image->temporaryPath, $target)) {
                throw new StorageException('Не удалось сохранить загруженное изображение');
            }
        }

        @chmod($target, 0644);

        return $this->uploadsUrl . '/' . $fileName;
    }

    public function storeMany(array $images): array
    {
        $stored = [];

        try {
            foreach ($images as $image) {
                $stored[] = $this->store($image);
            }
        } catch (ValidationException | StorageException $exception) {
            // Частично загруженный набор откатываем, чтобы не оставлять файлы-сироты.
            foreach ($stored as $webPath) {
                $this->delete($webPath);
            }

            throw $exception;
        }

        return $stored;
    }

    public function delete(string $webPath): void
    {
        $prefix = $this->uploadsUrl . '/';

        // Файлы вне каталога загрузок (например, исторические /front/images/*)
        // принадлежат репозиторию и удаляться из админки не должны.
        if (!str_starts_with($webPath, $prefix)) {
            return;
        }

        $fileName = basename(substr($webPath, strlen($prefix)));

        if ($fileName === '' || $fileName === '.' || $fileName === '..') {
            return;
        }

        $realPath = realpath($this->uploadsDir . '/' . $fileName);
        $realBase = realpath($this->uploadsDir);

        if ($realPath === false || $realBase === false) {
            return;
        }

        // Повторная проверка после разрешения симлинков — защита от выхода за каталог.
        if (!str_starts_with($realPath, $realBase . DIRECTORY_SEPARATOR)) {
            return;
        }

        @unlink($realPath);
    }

    private function assertUploadSucceeded(UploadedImage $image): void
    {
        if ($image->error !== UPLOAD_ERR_OK) {
            throw new ValidationException(['images' => $this->describeUploadError($image->error)]);
        }

        if ($image->size <= 0) {
            throw new ValidationException(['images' => 'Загруженный файл пуст']);
        }

        if ($image->size > self::MAX_FILE_SIZE) {
            throw new ValidationException([
                'images' => sprintf('Файл больше %d МБ', (int) (self::MAX_FILE_SIZE / 1024 / 1024)),
            ]);
        }

        if (!is_uploaded_file($image->temporaryPath)) {
            throw new ValidationException(['images' => 'Файл не является корректной загрузкой']);
        }
    }

    private function detectMimeType(string $path): string
    {
        $imageInfo = @getimagesize($path);

        if ($imageInfo === false) {
            throw new ValidationException(['images' => 'Файл не является изображением']);
        }

        $mimeType = (string) ($imageInfo['mime'] ?? '');

        // finfo смотрит на содержимое независимо от getimagesize: типы должны совпасть.
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if ($finfo !== false) {
                $detected = finfo_file($finfo, $path);
                finfo_close($finfo);

                if (!is_string($detected) || $detected !== $mimeType) {
                    throw new ValidationException(['images' => 'Не удалось определить тип изображения']);
                }
            }
        }

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new ValidationException([
                'images' => 'Допустимы только изображения в форматах JPEG, PNG и WebP',
            ]);
        }

        return $mimeType;
    }

    /**
     * @return bool false, если GD недоступна и нужен резервный путь.
     */
    private function reencode(string $sourcePath, string $mimeType, string $target): bool
    {
        if (!function_exists('imagecreatefromstring')) {
            return false;
        }

        $contents = file_get_contents($sourcePath);

        if ($contents === false) {
            throw new StorageException('Не удалось прочитать загруженный файл');
        }

        $source = @imagecreatefromstring($contents);
        unset($contents);

        if ($source === false) {
            throw new ValidationException(['images' => 'Изображение повреждено и не может быть обработано']);
        }

        try {
            $image = $this->downscale($source);

            $written = match ($mimeType) {
                'image/jpeg' => imagejpeg($image, $target, self::JPEG_QUALITY),
                'image/png' => imagepng($image, $target),
                'image/webp' => function_exists('imagewebp')
                    ? imagewebp($image, $target, self::JPEG_QUALITY)
                    : false,
                default => false,
            };

            if ($image !== $source) {
                imagedestroy($image);
            }

            if ($written === false) {
                @unlink($target);

                return false;
            }
        } finally {
            imagedestroy($source);
        }

        return true;
    }

    /**
     * Уменьшает изображение до MAX_DIMENSION по большей стороне.
     *
     * @param \GdImage $source
     *
     * @return \GdImage Исходный ресурс, если уменьшение не требуется.
     */
    private function downscale(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $longest = max($width, $height);

        if ($longest <= self::MAX_DIMENSION) {
            return $source;
        }

        $ratio = self::MAX_DIMENSION / $longest;
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        // Сохраняем прозрачность PNG и WebP.
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        if (!imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height)) {
            imagedestroy($resized);

            return $source;
        }

        return $resized;
    }

    private function moveWithoutReencoding(string $sourcePath, string $target): bool
    {
        return move_uploaded_file($sourcePath, $target);
    }

    private function describeUploadError(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Файл превышает допустимый размер загрузки',
            UPLOAD_ERR_PARTIAL => 'Файл был загружен не полностью',
            UPLOAD_ERR_NO_FILE => 'Файл не был выбран',
            UPLOAD_ERR_NO_TMP_DIR => 'На сервере отсутствует временный каталог для загрузок',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'Загрузка остановлена расширением PHP',
            default => 'Не удалось загрузить файл',
        };
    }
}
