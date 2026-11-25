<?php

declare(strict_types=1);

/**
 * @var string|null $error
 * @var string|null $generatedConfig
 * @var string      $login
 * @var string      $configFile
 */
?>
<section class="card">
    <h1 class="card__title">Первичная настройка</h1>

    <p class="card__text">
        Админка ещё не настроена: на сервере нет файла
        <code><?= ww_e($configFile) ?></code>.
        Задайте логин и пароль — здесь будет сформировано содержимое этого файла.
        Файл нужно создать вручную, чтобы доступ к админке не мог задать посторонний.
    </p>

    <?php if ($error !== null): ?>
        <div class="alert alert--error"><?= ww_e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/front/admin/" class="form" autocomplete="off">
        <input type="hidden" name="action" value="setup">

        <label class="field">
            <span class="field__label">Логин</span>
            <input class="field__input" type="text" name="login" value="<?= ww_e($login) ?>" required>
        </label>

        <label class="field">
            <span class="field__label">Пароль</span>
            <input class="field__input" type="password" name="password" minlength="12" required>
            <span class="field__hint">Не короче 12 символов. Сам пароль нигде не сохраняется — только его хеш.</span>
        </label>

        <button type="submit" class="button button--primary">Сформировать файл</button>
    </form>

    <?php if ($generatedConfig !== null): ?>
        <h2 class="card__subtitle">Содержимое файла</h2>
        <p class="card__text">
            Создайте по FTP файл <code><?= ww_e($configFile) ?></code> с этим содержимым
            и обновите страницу.
        </p>
        <textarea class="code" rows="14" readonly><?= ww_e($generatedConfig) ?></textarea>
    <?php endif; ?>
</section>
