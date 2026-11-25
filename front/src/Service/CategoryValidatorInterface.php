<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\CategoryInput;

/**
 * Проверка данных категории перед сохранением.
 */
interface CategoryValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(CategoryInput $input): void;
}
