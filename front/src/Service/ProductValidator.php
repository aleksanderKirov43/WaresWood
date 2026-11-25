<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\ValidationException;
use WaresWood\Service\Model\ProductInput;

/**
 * Правила заполнения карточки товара.
 */
final class ProductValidator implements ProductValidatorInterface
{
    private const TITLE_MAX_LENGTH = 200;

    private const DESCRIPTION_MAX_LENGTH = 4000;

    private const PRICE_UNIT_MAX_LENGTH = 60;

    private const PRICE_MAX = 100_000_000;

    public function validate(ProductInput $input): void
    {
        $errors = [];

        if ($input->categoryId === '') {
            $errors['categoryId'] = 'Выберите категорию.';
        }

        $titleLength = mb_strlen(trim($input->title), 'UTF-8');

        if ($titleLength === 0) {
            $errors['title'] = 'Укажите название товара.';
        } elseif ($titleLength > self::TITLE_MAX_LENGTH) {
            $errors['title'] = sprintf('Название длиннее %d символов.', self::TITLE_MAX_LENGTH);
        }

        if (mb_strlen($input->description, 'UTF-8') > self::DESCRIPTION_MAX_LENGTH) {
            $errors['description'] = sprintf('Описание длиннее %d символов.', self::DESCRIPTION_MAX_LENGTH);
        }

        if ($input->price < 0) {
            $errors['price'] = 'Цена не может быть отрицательной.';
        } elseif ($input->price > self::PRICE_MAX) {
            $errors['price'] = 'Цена выглядит нереалистично большой.';
        }

        if (mb_strlen($input->priceUnit, 'UTF-8') > self::PRICE_UNIT_MAX_LENGTH) {
            $errors['priceUnit'] = sprintf('Единица измерения длиннее %d символов.', self::PRICE_UNIT_MAX_LENGTH);
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
