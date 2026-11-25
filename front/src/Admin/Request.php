<?php

declare(strict_types=1);

namespace WaresWood\Admin;

/**
 * Входящий HTTP-запрос админки.
 *
 * Обёртка изолирует прикладной код от суперглобальных массивов и приводит
 * значения к нужным типам: всё, что приходит от клиента, изначально строка.
 */
final class Request
{
    /**
     * @param array<mixed> $query
     * @param array<mixed> $body
     * @param array<mixed> $files
     */
    public function __construct(
        private readonly string $method,
        private readonly array $query,
        private readonly array $body,
        private readonly array $files,
        private readonly string $scriptName,
    ) {
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Действие берётся из тела для команд и из query-строки для страниц.
     */
    public function action(): string
    {
        $action = $this->body['action'] ?? $this->query['action'] ?? 'catalog';

        return is_string($action) && $action !== '' ? $action : 'catalog';
    }

    public function string(string $key): string
    {
        $value = $this->body[$key] ?? $this->query[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Значение без нормализации. Нужно для пароля, где значимы любые символы,
     * включая пробелы по краям.
     */
    public function raw(string $key): string
    {
        $value = $this->body[$key] ?? $this->query[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /**
     * Многострочные поля не обрезаются по краям построчно, только целиком.
     */
    public function text(string $key): string
    {
        $value = $this->body[$key] ?? $this->query[$key] ?? '';

        return is_string($value) ? trim(str_replace("\r\n", "\n", $value)) : '';
    }

    public function int(string $key): int
    {
        // Пользователи вводят цену как «1 500» или «1500 руб.» — оставляем цифры.
        $digits = preg_replace('/\D+/', '', $this->string($key)) ?? '';

        return $digits === '' ? 0 : (int) $digits;
    }

    /**
     * @return array<mixed>|null
     */
    public function files(string $key): ?array
    {
        $field = $this->files[$key] ?? null;

        return is_array($field) ? $field : null;
    }

    /**
     * Отправленные поля формы для восстановления ввода после ошибки валидации.
     *
     * @return array<string, string>
     */
    public function scalarBody(): array
    {
        $result = [];

        foreach ($this->body as $key => $value) {
            if (is_string($key) && is_scalar($value)) {
                $result[$key] = (string) $value;
            }
        }

        unset($result['csrf'], $result['action']);

        return $result;
    }

    public function scriptName(): string
    {
        return $this->scriptName;
    }
}
