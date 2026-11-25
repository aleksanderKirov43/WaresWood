<?php

declare(strict_types=1);

namespace WaresWood\Admin;

use WaresWood\App\Session;

/**
 * Перенос введённых значений и ошибок валидации через редирект обратно к форме.
 */
final class FormState
{
    private const OLD_INPUT_KEY = 'form_old_input';

    private const ERRORS_KEY = 'form_errors';

    public function __construct(
        private readonly Session $session,
    ) {
    }

    /**
     * @param array<string, string> $input
     * @param array<string, string> $errors
     */
    public function remember(array $input, array $errors): void
    {
        $this->session->set(self::OLD_INPUT_KEY, $input);
        $this->session->set(self::ERRORS_KEY, $errors);
    }

    /**
     * Возвращает и очищает сохранённое состояние формы.
     *
     * @return array{old: array<string, string>, errors: array<string, string>}
     */
    public function take(): array
    {
        $old = $this->session->get(self::OLD_INPUT_KEY);
        $errors = $this->session->get(self::ERRORS_KEY);

        $this->session->remove(self::OLD_INPUT_KEY);
        $this->session->remove(self::ERRORS_KEY);

        return [
            'old' => is_array($old) ? $old : [],
            'errors' => is_array($errors) ? $errors : [],
        ];
    }
}
