<?php

declare(strict_types=1);

namespace WaresWood\Service;

/**
 * Идентификаторы в виде транслитерированного slug.
 *
 * Идентификатор попадает в публичный URL товара (product.php?id=...), поэтому
 * из названия строится читаемая латиница, а не случайная строка.
 */
final class SlugIdGenerator implements IdGeneratorInterface
{
    private const MAX_LENGTH = 60;

    private const TRANSLITERATION = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    public function generate(string $source, array $taken): string
    {
        $slug = $this->slugify($source);

        if ($slug === '') {
            $slug = 'tovar';
        }

        $candidate = $slug;
        $suffix = 2;

        while (in_array($candidate, $taken, true)) {
            $candidate = $slug . '-' . $suffix;
            ++$suffix;
        }

        return $candidate;
    }

    private function slugify(string $source): string
    {
        $value = mb_strtolower(trim($source), 'UTF-8');
        $value = strtr($value, self::TRANSLITERATION);

        // Всё, что не осталось латиницей или цифрой (пробелы, знаки, иные алфавиты),
        // сворачивается в дефис.
        $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?? '';
        $value = trim($value, '-');

        if (mb_strlen($value, 'UTF-8') > self::MAX_LENGTH) {
            $value = trim(mb_substr($value, 0, self::MAX_LENGTH, 'UTF-8'), '-');
        }

        return $value;
    }
}
