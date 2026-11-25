<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\ProductInput;

/**
 * Проверка данных товара перед сохранением.
 */
interface ProductValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(ProductInput $input): void;
}
