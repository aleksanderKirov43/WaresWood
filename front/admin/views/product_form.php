<?php

declare(strict_types=1);

use WaresWood\Model\Category;
use WaresWood\Model\Product;

/**
 * @var Product|null          $product
 * @var list<Category>        $categories
 * @var string                $currentCategoryId
 * @var string                $csrf
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */

$titleValue = $old['title'] ?? ($product?->title ?? '');
$descriptionValue = $old['description'] ?? ($product?->description ?? '');
$priceValue = $old['price'] ?? (string) ($product?->price ?? '');
$priceUnitValue = $old['priceUnit'] ?? ($product?->priceUnit ?? 'за изделие');
$categoryValue = $old['categoryId'] ?? $currentCategoryId;
?>
<section class="card">
    <h1 class="card__title"><?= $product === null ? 'Новый товар' : 'Редактирование товара' ?></h1>

    <?php if ($categories === []): ?>
        <div class="alert alert--error">
            Сначала создайте хотя бы одну категорию — товар должен куда-то попасть.
        </div>
        <a class="button button--primary" href="/front/admin/?action=category.form">Создать категорию</a>
    <?php else: ?>
        <form method="post" action="/front/admin/" class="form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="product.save">
            <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
            <input type="hidden" name="id" value="<?= ww_e($product?->id ?? '') ?>">

            <label class="field">
                <span class="field__label">Категория</span>
                <select class="field__input<?= isset($errors['categoryId']) ? ' field__input--invalid' : '' ?>"
                        name="categoryId" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= ww_e($category->id) ?>"
                            <?= $category->id === $categoryValue ? 'selected' : '' ?>>
                            <?= ww_e($category->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($product !== null): ?>
                    <span class="field__hint">Смена категории переносит товар, ссылка на него не меняется.</span>
                <?php endif; ?>
                <?php if (isset($errors['categoryId'])): ?>
                    <span class="field__error"><?= ww_e($errors['categoryId']) ?></span>
                <?php endif; ?>
            </label>

            <label class="field">
                <span class="field__label">Название</span>
                <input class="field__input<?= isset($errors['title']) ? ' field__input--invalid' : '' ?>"
                       type="text" name="title" value="<?= ww_e($titleValue) ?>" required maxlength="200">
                <?php if (isset($errors['title'])): ?>
                    <span class="field__error"><?= ww_e($errors['title']) ?></span>
                <?php endif; ?>
            </label>

            <label class="field">
                <span class="field__label">Описание</span>
                <textarea class="field__input field__input--textarea<?= isset($errors['description']) ? ' field__input--invalid' : '' ?>"
                          name="description" rows="7" maxlength="4000"><?= ww_e($descriptionValue) ?></textarea>
                <span class="field__hint">Переводы строк сохраняются и отображаются на сайте как есть.</span>
                <?php if (isset($errors['description'])): ?>
                    <span class="field__error"><?= ww_e($errors['description']) ?></span>
                <?php endif; ?>
            </label>

            <div class="field-row">
                <label class="field">
                    <span class="field__label">Цена, руб.</span>
                    <input class="field__input<?= isset($errors['price']) ? ' field__input--invalid' : '' ?>"
                           type="text" inputmode="numeric" name="price" value="<?= ww_e($priceValue) ?>">
                    <?php if (isset($errors['price'])): ?>
                        <span class="field__error"><?= ww_e($errors['price']) ?></span>
                    <?php endif; ?>
                </label>

                <label class="field">
                    <span class="field__label">За что цена</span>
                    <input class="field__input<?= isset($errors['priceUnit']) ? ' field__input--invalid' : '' ?>"
                           type="text" name="priceUnit" value="<?= ww_e($priceUnitValue) ?>" maxlength="60">
                    <span class="field__hint">Например: «за изделие» или «за 1 кв. м».</span>
                    <?php if (isset($errors['priceUnit'])): ?>
                        <span class="field__error"><?= ww_e($errors['priceUnit']) ?></span>
                    <?php endif; ?>
                </label>
            </div>

            <label class="field">
                <span class="field__label">Добавить фотографии</span>
                <input class="field__input<?= isset($errors['images']) ? ' field__input--invalid' : '' ?>"
                       type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple
                       data-preview="upload-preview">
                <span class="field__hint">
                    JPEG, PNG или WebP, до 8 МБ каждая. Большие снимки автоматически уменьшаются.
                </span>
                <?php if (isset($errors['images'])): ?>
                    <span class="field__error"><?= ww_e($errors['images']) ?></span>
                <?php endif; ?>
            </label>

            <div class="gallery" id="upload-preview"></div>

            <div class="form__actions">
                <button type="submit" class="button button--primary">Сохранить</button>
                <a class="button button--ghost" href="/front/admin/">Вернуться к каталогу</a>
            </div>
        </form>

        <?php if ($product !== null): ?>
            <h2 class="card__subtitle">Фотографии товара</h2>

            <?php if ($product->images === []): ?>
                <p class="card__text">Фотографий пока нет. Добавьте их через форму выше.</p>
            <?php else: ?>
                <p class="card__text">
                    Первая фотография — главная: она показывается в каталоге и в списке товаров.
                </p>

                <div class="gallery">
                    <?php foreach ($product->images as $index => $image): ?>
                        <figure class="gallery__item<?= $index === 0 ? ' gallery__item--primary' : '' ?>">
                            <img src="<?= ww_e($image) ?>" alt="" loading="lazy">

                            <figcaption class="gallery__actions">
                                <?php if ($index === 0): ?>
                                    <span class="gallery__badge">Главная</span>
                                <?php else: ?>
                                    <form method="post" action="/front/admin/" class="inline-form">
                                        <input type="hidden" name="action" value="image.primary">
                                        <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= ww_e($product->id) ?>">
                                        <input type="hidden" name="image" value="<?= ww_e($image) ?>">
                                        <button type="submit" class="button button--small">Сделать главной</button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" action="/front/admin/" class="inline-form"
                                      data-confirm="Удалить эту фотографию?">
                                    <input type="hidden" name="action" value="image.delete">
                                    <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= ww_e($product->id) ?>">
                                    <input type="hidden" name="image" value="<?= ww_e($image) ?>">
                                    <button type="submit" class="button button--small button--danger">Удалить</button>
                                </form>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</section>
