<?php

declare(strict_types=1);

namespace WaresWood\Service;

/**
 * Генерация идентификаторов сущностей каталога.
 */
interface IdGeneratorInterface
{
    /**
     * Строит идентификатор из названия, избегая совпадений с занятыми.
     *
     * @param list<string> $taken Уже используемые идентификаторы.
     */
    public function generate(string $source, array $taken): string;
}
