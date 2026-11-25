<?php

declare(strict_types=1);

use WaresWood\Model\Category;

/**
 * @var Category|null            $category
 * @var string                   $csrf
 * @var array<string, string>    $errors
 * @var array<string, string>    $old
 */

$titleValue = $old['title'] ?? ($category?->title ?? '');
$subtitleValue = $old['subtitle'] ?? ($category?->subtitle ?? '');
?>
<section class="card">
    <h1 class="card__title"><?= $category === null ? 'Новая категория' : 'Редактирование категории' ?></h1>

    <form method="post" action="/front/admin/" class="form">
        <input type="hidden" name="action" value="category.save">
        <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
        <input type="hidden" name="id" value="<?= ww_e($category?->id ?? '') ?>">

        <label class="field">
            <span class="field__label">Название категории</span>
            <input class="field__input<?= isset($errors['title']) ? ' field__input--invalid' : '' ?>"
                   type="text" name="title" value="<?= ww_e($titleValue) ?>" required maxlength="200">
            <?php if (isset($errors['title'])): ?>
                <span class="field__error"><?= ww_e($errors['title']) ?></span>
            <?php endif; ?>
        </label>

        <label class="field">
            <span class="field__label">Подзаголовок</span>
            <input class="field__input<?= isset($errors['subtitle']) ? ' field__input--invalid' : '' ?>"
                   type="text" name="subtitle" value="<?= ww_e($subtitleValue) ?>" maxlength="300">
            <span class="field__hint">
                Выводится под названием категории на главной. Например: «Цена указана за одно стандартное изделие.»
            </span>
            <?php if (isset($errors['subtitle'])): ?>
                <span class="field__error"><?= ww_e($errors['subtitle']) ?></span>
            <?php endif; ?>
        </label>

        <?php if ($category !== null): ?>
            <p class="field__hint">
                Идентификатор <code><?= ww_e($category->id) ?></code> не меняется при переименовании,
                чтобы не сломать существующие ссылки.
            </p>
        <?php endif; ?>

        <div class="form__actions">
            <button type="submit" class="button button--primary">Сохранить</button>
            <a class="button button--ghost" href="/front/admin/">Отмена</a>
        </div>
    </form>
</section>
