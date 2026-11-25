<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\App\Session;
use WaresWood\Exception\AuthException;

/**
 * Защита изменяющих запросов от подделки межсайтовых запросов.
 */
final class CsrfGuard
{
    private const SESSION_KEY = 'csrf_token';

    public function __construct(
        private readonly Session $session,
    ) {
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    /**
     * @throws AuthException Если токен отсутствует или не совпадает с сессионным.
     */
    public function assertValid(?string $token): void
    {
        $expected = $this->session->get(self::SESSION_KEY);

        if (!is_string($expected) || $expected === '' || $token === null || !hash_equals($expected, $token)) {
            throw new AuthException('Форма устарела. Обновите страницу и повторите действие.');
        }
    }
}
