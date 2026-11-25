<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\AuthException;

/**
 * Аутентификация администратора.
 */
interface AuthServiceInterface
{
    public function isAuthenticated(): bool;

    public function currentLogin(): ?string;

    /**
     * @throws AuthException Если пара логин/пароль неверна или вход заблокирован.
     */
    public function login(string $login, string $password): void;

    public function logout(): void;
}
