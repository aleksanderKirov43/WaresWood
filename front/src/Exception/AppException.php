<?php

declare(strict_types=1);

namespace WaresWood\Exception;

use RuntimeException;

/**
 * Базовое исключение приложения.
 *
 * Позволяет отделить ожидаемые прикладные ошибки, которые можно показать
 * администратору, от непредвиденных сбоев среды выполнения.
 */
class AppException extends RuntimeException
{
}
