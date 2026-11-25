<?php

declare(strict_types=1);

namespace WaresWood\Exception;

/**
 * Ошибка аутентификации, проверки CSRF-токена или превышение лимита попыток входа.
 */
final class AuthException extends AppException
{
}
