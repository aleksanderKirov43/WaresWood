<?php

declare(strict_types=1);

/**
 * Слой совместимости между хранилищем каталога и публичными страницами.
 *
 * Страницы index.php, product.php и footer.php исторически работают с массивом
 * $products, поэтому модель каталога разворачивается здесь в привычную структуру.
 * Сам каталог живёт в data/products.json — вне front/ и deploy/, которые
 * перезаливаются FTP-деплоем при каждом push в main.
 *
 * @var array<string, array{
 *     title: string,
 *     subtitle: string,
 *     items: array<string, array{
 *         title: string,
 *         description: string,
 *         images: list<string>,
 *         price: string,
 *         priceUnit: string
 *     }>
 * }> $products
 */

require_once __DIR__ . '/src/autoload.php';

$products = [];

try {
    $wwContainer = new WaresWood\App\Container(dirname(__DIR__));
    $wwPriceFormatter = $wwContainer->priceFormatter();

    foreach ($wwContainer->catalogService()->getCatalog()->categories as $wwCategory) {
        $wwItems = [];

        foreach ($wwCategory->products as $wwProduct) {
            $wwItems[$wwProduct->id] = [
                'title' => $wwProduct->title,
                'description' => $wwProduct->description,
                'images' => $wwProduct->images,
                'price' => $wwPriceFormatter->format($wwProduct->price),
                'priceUnit' => $wwProduct->priceUnit,
            ];
        }

        $products[$wwCategory->id] = [
            'title' => $wwCategory->title,
            'subtitle' => $wwCategory->subtitle,
            'items' => $wwItems,
        ];
    }

    unset($wwContainer, $wwPriceFormatter, $wwCategory, $wwProduct, $wwItems);
} catch (Throwable $wwException) {
    // Проблема с хранилищем не должна ронять витрину: показываем пустой каталог.
    error_log('WaresWood: не удалось загрузить каталог — ' . $wwException->getMessage());

    $products = [];

    unset($wwException);
}
