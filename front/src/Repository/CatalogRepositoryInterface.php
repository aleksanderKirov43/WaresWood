<?php

declare(strict_types=1);

namespace WaresWood\Repository;

use WaresWood\Exception\StorageException;
use WaresWood\Model\Catalog;

/**
 * Доступ к хранилищу каталога.
 */
interface CatalogRepositoryInterface
{
    /**
     * Читает каталог. Если хранилище ещё не создано — возвращает пустой каталог.
     *
     * @throws StorageException
     */
    public function load(): Catalog;

    /**
     * Полностью перезаписывает каталог.
     *
     * @throws StorageException
     */
    public function save(Catalog $catalog): void;
}
