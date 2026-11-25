<?php

declare(strict_types=1);

namespace WaresWood\Exception;

/**
 * Ошибка валидации пользовательского ввода.
 *
 * Хранит ошибки по полям, чтобы форма могла подсветить конкретные поля,
 * а не только вывести общее сообщение.
 */
final class ValidationException extends AppException
{
    /**
     * @param array<string, string> $errors Карта «поле => сообщение».
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
