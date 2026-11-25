<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\AuthException;

/**
 * Ограничение числа неудачных попыток входа.
 */
interface LoginThrottleInterface
{
    /**
     * @param string $key Ключ ограничения, например IP-адрес клиента.
     *
     * @throws AuthException Если попытки временно заблокированы.
     */
    public function assertNotLocked(string $key): void;

    public function registerFailure(string $key): void;

    public function reset(string $key): void;
}
