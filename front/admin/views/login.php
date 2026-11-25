<?php

declare(strict_types=1);

/**
 * @var string      $csrf
 * @var string|null $error
 * @var string      $login
 */
?>
<section class="card card--narrow">
    <h1 class="card__title">Вход в админку</h1>

    <?php if ($error !== null): ?>
        <div class="alert alert--error"><?= ww_e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/front/admin/" class="form" autocomplete="off">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">

        <label class="field">
            <span class="field__label">Логин</span>
            <input class="field__input" type="text" name="login" value="<?= ww_e($login) ?>" required autofocus>
        </label>

        <label class="field">
            <span class="field__label">Пароль</span>
            <input class="field__input" type="password" name="password" required>
        </label>

        <button type="submit" class="button button--primary">Войти</button>
    </form>
</section>
