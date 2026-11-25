<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\App\Config;
use WaresWood\App\Session;
use WaresWood\Exception\AuthException;

/**
 * Аутентификация администратора на основе PHP-сессии.
 */
final class SessionAuthService implements AuthServiceInterface
{
    private const SESSION_KEY = 'admin_login';

    private const IDLE_TIMEOUT = 7200;

    private const ACTIVITY_KEY = 'admin_last_activity';

    public function __construct(
        private readonly Session $session,
        private readonly Config $config,
        private readonly LoginThrottleInterface $throttle,
        private readonly string $clientIp,
    ) {
    }

    public function isAuthenticated(): bool
    {
        $login = $this->session->get(self::SESSION_KEY);

        if (!is_string($login) || $login === '') {
            return false;
        }

        $lastActivity = (int) $this->session->get(self::ACTIVITY_KEY);

        // Смена логина в конфиге должна инвалидировать выданные сессии.
        if (!hash_equals($this->config->adminLogin, $login)) {
            $this->logout();

            return false;
        }

        if ($lastActivity > 0 && time() - $lastActivity > self::IDLE_TIMEOUT) {
            $this->logout();

            return false;
        }

        $this->session->set(self::ACTIVITY_KEY, time());

        return true;
    }

    public function currentLogin(): ?string
    {
        $login = $this->session->get(self::SESSION_KEY);

        return is_string($login) && $login !== '' ? $login : null;
    }

    public function login(string $login, string $password): void
    {
        $this->throttle->assertNotLocked($this->clientIp);

        $loginMatches = hash_equals($this->config->adminLogin, $login);
        $passwordMatches = password_verify($password, $this->config->adminPasswordHash);

        // Проверки выполняются обе, чтобы время ответа не выдавало,
        // существует ли указанный логин.
        if (!$loginMatches || !$passwordMatches) {
            $this->throttle->registerFailure($this->clientIp);

            throw new AuthException('Неверный логин или пароль.');
        }

        $this->throttle->reset($this->clientIp);

        $this->session->regenerate();
        $this->session->set(self::SESSION_KEY, $login);
        $this->session->set(self::ACTIVITY_KEY, time());
    }

    public function logout(): void
    {
        $this->session->remove(self::SESSION_KEY);
        $this->session->remove(self::ACTIVITY_KEY);
    }
}
