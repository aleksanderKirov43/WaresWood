<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Service\Model\UploadedImage;

/**
 * Приведение массива $_FILES к списку моделей загруженных изображений.
 *
 * PHP для input с атрибутом multiple раскладывает данные «по полям», а не «по файлам»,
 * поэтому структуру приходится транспонировать. Пустые слоты (UPLOAD_ERR_NO_FILE)
 * отбрасываются: браузер присылает их, когда файл не выбран.
 */
final class UploadedImageCollector
{
    /**
     * @param array<mixed>|null $field Элемент $_FILES, соответствующий одному input.
     *
     * @return list<UploadedImage>
     */
    public function collect(?array $field): array
    {
        if ($field === null || !isset($field['tmp_name'])) {
            return [];
        }

        if (!is_array($field['tmp_name'])) {
            $field = array_map(static fn (mixed $value): array => [$value], $field);
        }

        $images = [];

        foreach (array_keys($field['tmp_name']) as $index) {
            $error = (int) ($field['error'][$index] ?? UPLOAD_ERR_NO_FILE);

            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $images[] = new UploadedImage(
                (string) ($field['tmp_name'][$index] ?? ''),
                (string) ($field['name'][$index] ?? ''),
                (int) ($field['size'][$index] ?? 0),
                $error,
            );
        }

        return $images;
    }
}
