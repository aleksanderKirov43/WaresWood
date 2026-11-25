<?php

declare(strict_types=1);

namespace WaresWood\App;

use Throwable;
use WaresWood\Exception\AppException;

/**
 * Простой рендерер PHP-шаблонов админки.
 */
final class View
{
    public function __construct(
        private readonly string $templateDir,
    ) {
    }

    /**
     * Рендер страницы: шаблон оборачивается в общий layout.
     *
     * @param array<string, mixed> $data
     */
    public function renderPage(string $template, string $title, array $data = []): void
    {
        $content = $this->capture($template, $data);

        echo $this->capture('layout', [
            'title' => $title,
            'content' => $content,
        ] + $data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws AppException Если шаблон отсутствует или выбросил исключение.
     */
    public function capture(string $template, array $data = []): string
    {
        $path = $this->templateDir . '/' . $template . '.php';

        if (!is_file($path)) {
            throw new AppException(sprintf('Шаблон «%s» не найден', $template));
        }

        ob_start();

        try {
            (static function (string $__path, array $__data): void {
                extract($__data, EXTR_SKIP);

                require $__path;
            })($path, $data);
        } catch (Throwable $exception) {
            ob_end_clean();

            throw new AppException('Ошибка рендеринга шаблона: ' . $exception->getMessage(), 0, $exception);
        }

        return (string) ob_get_clean();
    }
}
