<?php

declare(strict_types=1);

use WaresWood\Model\Catalog;
use WaresWood\Service\PriceFormatter;

/**
 * @var Catalog        $catalog
 * @var PriceFormatter $priceFormatter
 * @var bool           $isEmpty
 * @var string         $csrf
 */
?>
<section class="toolbar">
    <h1 class="toolbar__title">Каталог</h1>
    <div class="toolbar__actions">
        <a class="button button--ghost" href="/front/admin/?action=category.form">Добавить категорию</a>
        <?php if (!$isEmpty): ?>
            <a class="button button--primary" href="/front/admin/?action=product.form">Добавить товар</a>
        <?php endif; ?>
    </div>
</section>

<?php if ($isEmpty): ?>
    <div class="empty">
        <p>Каталог пуст. Начните с категории — например «Стеллажи» — и добавьте в неё товары.</p>
        <a class="button button--primary" href="/front/admin/?action=category.form">Создать первую категорию</a>
    </div>
<?php endif; ?>

<?php foreach ($catalog->categories as $categoryIndex => $category): ?>
    <section class="category">
        <header class="category__header">
            <div>
                <h2 class="category__title"><?= ww_e($category->title) ?></h2>
                <?php if ($category->subtitle !== ''): ?>
                    <p class="category__subtitle"><?= ww_e($category->subtitle) ?></p>
                <?php endif; ?>
                <p class="category__meta">
                    id: <code><?= ww_e($category->id) ?></code>
                    · товаров: <?= count($category->products) ?>
                </p>
            </div>

            <div class="category__actions">
                <form method="post" action="/front/admin/" class="inline-form">
                    <input type="hidden" name="action" value="category.move">
                    <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= ww_e($category->id) ?>">
                    <button type="submit" name="direction" value="up" class="button button--icon"
                            title="Поднять категорию" <?= $categoryIndex === 0 ? 'disabled' : '' ?>>&uarr;</button>
                    <button type="submit" name="direction" value="down" class="button button--icon"
                            title="Опустить категорию"
                        <?= $categoryIndex === count($catalog->categories) - 1 ? 'disabled' : '' ?>>&darr;</button>
                </form>

                <a class="button button--ghost"
                   href="/front/admin/?action=category.form&amp;id=<?= urlencode($category->id) ?>">Изменить</a>

                <a class="button button--ghost"
                   href="/front/admin/?action=product.form&amp;categoryId=<?= urlencode($category->id) ?>">
                    Добавить товар
                </a>

                <form method="post" action="/front/admin/" class="inline-form"
                      data-confirm="Удалить категорию «<?= ww_e($category->title) ?>» вместе со всеми товарами и фотографиями?">
                    <input type="hidden" name="action" value="category.delete">
                    <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= ww_e($category->id) ?>">
                    <button type="submit" class="button button--danger">Удалить</button>
                </form>
            </div>
        </header>

        <?php if ($category->products === []): ?>
            <p class="category__empty">В категории пока нет товаров.</p>
        <?php else: ?>
            <ul class="product-list">
                <?php foreach ($category->products as $productIndex => $product): ?>
                    <li class="product-row">
                        <div class="product-row__preview">
                            <?php if ($product->images !== []): ?>
                                <img src="<?= ww_e($product->images[0]) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span class="product-row__no-photo">нет фото</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-row__body">
                            <h3 class="product-row__title"><?= ww_e($product->title) ?></h3>
                            <p class="product-row__price">
                                <?= ww_e($priceFormatter->format($product->price)) ?>
                                <?php if ($product->priceUnit !== ''): ?>
                                    <span class="product-row__unit"><?= ww_e($product->priceUnit) ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="product-row__meta">
                                id: <code><?= ww_e($product->id) ?></code>
                                · фото: <?= count($product->images) ?>
                            </p>
                        </div>

                        <div class="product-row__actions">
                            <form method="post" action="/front/admin/" class="inline-form">
                                <input type="hidden" name="action" value="product.move">
                                <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= ww_e($product->id) ?>">
                                <button type="submit" name="direction" value="up" class="button button--icon"
                                        title="Поднять товар" <?= $productIndex === 0 ? 'disabled' : '' ?>>&uarr;</button>
                                <button type="submit" name="direction" value="down" class="button button--icon"
                                        title="Опустить товар"
                                    <?= $productIndex === count($category->products) - 1 ? 'disabled' : '' ?>>&darr;</button>
                            </form>

                            <a class="button button--ghost"
                               href="/front/admin/?action=product.form&amp;id=<?= urlencode($product->id) ?>">Изменить</a>

                            <a class="button button--ghost" target="_blank" rel="noopener"
                               href="/front/product.php?id=<?= urlencode($product->id) ?>">На сайте</a>

                            <form method="post" action="/front/admin/" class="inline-form"
                                  data-confirm="Удалить товар «<?= ww_e($product->title) ?>» и его фотографии?">
                                <input type="hidden" name="action" value="product.delete">
                                <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= ww_e($product->id) ?>">
                                <button type="submit" class="button button--danger">Удалить</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
