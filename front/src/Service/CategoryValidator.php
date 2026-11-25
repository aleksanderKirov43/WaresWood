<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\CategoryInput;

/**
 * Правила заполнения категории каталога.
 */
final class CategoryValidator implements CategoryValidatorInterface
{
    private const TITLE_MAX_LENGTH = 200;

    private const SUBTITLE_MAX_LENGTH = 300;

    public function validate(CategoryInput $input): void
    {
        $errors = [];

        $titleLength = mb_strlen(trim($input->title), 'UTF-8');

        if ($titleLength === 0) {
            $errors['title'] = 'Укажите название категории.';
        } elseif ($titleLength > self::TITLE_MAX_LENGTH) {
            $errors['title'] = sprintf('Название длиннее %d символов.', self::TITLE_MAX_LENGTH);
        }

        if (mb_strlen($input->subtitle, 'UTF-8') > self::SUBTITLE_MAX_LENGTH) {
            $errors['subtitle'] = sprintf('Подзаголовок длиннее %d символов.', self::SUBTITLE_MAX_LENGTH);
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
