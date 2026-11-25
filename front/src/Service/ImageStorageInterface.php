<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\StorageException;
use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\UploadedImage;

/**
 * Хранилище фотографий товаров.
 */
interface ImageStorageInterface
{
    /**
     * Сохраняет изображение и возвращает его веб-путь.
     *
     * @throws ValidationException Если файл не является допустимым изображением.
     * @throws StorageException    Если файл не удалось записать.
     */
    public function store(UploadedImage $image): string;

    /**
     * @param list<UploadedImage> $images
     *
     * @return list<string> Веб-пути сохранённых изображений.
     *
     * @throws ValidationException
     * @throws StorageException
     */
    public function storeMany(array $images): array;

    /**
     * Удаляет файл по веб-пути. Пути вне каталога загрузок игнорируются.
     */
    public function delete(string $webPath): void;
}
