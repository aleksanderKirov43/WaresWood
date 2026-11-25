<?php

declare(strict_types=1);

namespace WaresWood\Admin;

/**
 * Формирование HTTP-ответов админки.
 */
final class Responder
{
    public function __construct(
        private readonly string $basePath,
    ) {
    }

    /**
     * Редирект после команды (шаблон POST/Redirect/GET): повторное обновление
     * страницы не приводит к повторному выполнению действия.
     *
     * @param array<string, string> $query
     */
    public function redirect(array $query = []): never
    {
        $query = array_filter($query, static fn (string $value): bool => $value !== '');
        $url = $this->basePath . ($query === [] ? '' : '?' . http_build_query($query));

        header('Location: ' . $url, true, 303);

        exit;
    }

    /**
     * Заголовки, запрещающие кеширование и индексацию страниц админки.
     */
    public function sendSecurityHeaders(): void
    {
        header('X-Robots-Tag: noindex, nofollow');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: same-origin');
        header('Cache-Control: no-store, no-cache, must-revalidate');
    }
}
