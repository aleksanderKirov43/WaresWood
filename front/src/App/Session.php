<?php

declare(strict_types=1);

namespace WaresWood\App;

/**
 * Управление PHP-сессией админки с безопасными параметрами cookie.
 */
final class Session
{
    private const COOKIE_NAME = 'wwadmin';
    private const FLASH_KEY = '__flash';

    private bool $started = false;

    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;

            return;
        }

        session_name(self::COOKIE_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $this->isHttps(),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
        $this->started = true;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Пересоздание идентификатора сессии — защита от фиксации сессии при входе.
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->started = false;
    }

    /**
     * @param 'success'|'error' $type
     */
    public function addFlash(string $type, string $message): void
    {
        $messages = $_SESSION[self::FLASH_KEY] ?? [];
        $messages[] = ['type' => $type, 'message' => $message];
        $_SESSION[self::FLASH_KEY] = $messages;
    }

    /**
     * Возвращает и очищает накопленные одноразовые сообщения.
     *
     * @return list<array{type: string, message: string}>
     */
    public function takeFlashes(): array
    {
        $messages = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);

        return is_array($messages) ? array_values($messages) : [];
    }

    private function isHttps(): bool
    {
        if (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off') {
            return true;
        }

        // За обратным прокси хостинга схема приходит в заголовке.
        return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }
}
