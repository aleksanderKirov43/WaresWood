<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\StorageException;

/**
 * Подготовка каталогов, создаваемых приложением во время работы.
 */
interface DirectoryGuardInterface
{
    /**
     * Создаёт каталог и закрывает его от прямого доступа по HTTP.
     *
     * @throws StorageException
     */
    public function ensureProtectedDirectory(string $path): void;

    /**
     * Создаёт каталог, доступный по HTTP, но с запретом выполнения скриптов.
     *
     * @throws StorageException
     */
    public function ensurePublicUploadDirectory(string $path): void;
}
