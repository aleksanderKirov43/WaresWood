<?php

declare(strict_types=1);

namespace WaresWood\App;

use Throwable;
use WaresWood\Exception\ConfigException;

/**
 * Загрузка конфигурации администратора из файла вне зоны деплоя.
 *
 * Файл не попадает в git и не перезаливается FTP-деплоем, поэтому хеш пароля
 * не утекает в репозиторий и сохраняется между релизами.
 */
final class ConfigLoader
{
    public function __construct(
        private readonly string $configFile,
    ) {
    }

    /**
     * Создана ли конфигурация администратора.
     */
    public function exists(): bool
    {
        return is_file($this->configFile);
    }

    /**
     * @throws ConfigException Если файл отсутствует или имеет неверный формат.
     */
    public function load(): Config
    {
        if (!$this->exists()) {
            throw new ConfigException('Файл конфигурации администратора не найден');
        }

        try {
            /** @var mixed $raw */
            $raw = require $this->configFile;
        } catch (Throwable $exception) {
            throw new ConfigException('Не удалось прочитать конфигурацию администратора', 0, $exception);
        }

        if (!is_array($raw)) {
            throw new ConfigException('Конфигурация администратора должна возвращать массив');
        }

        $login = $raw['admin']['login'] ?? null;
        $hash = $raw['admin']['password_hash'] ?? null;

        if (!is_string($login) || $login === '' || !is_string($hash) || $hash === '') {
            throw new ConfigException('В конфигурации администратора отсутствует логин или хеш пароля');
        }

        return new Config($login, $hash);
    }
}
