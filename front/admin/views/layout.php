<?php

declare(strict_types=1);

/**
 * @var string                                        $title
 * @var string                                        $content
 * @var bool                                          $authenticated
 * @var string|null                                   $csrf
 * @var string|null                                   $adminLogin
 * @var list<array{type: string, message: string}>    $flashes
 */

$flashes = $flashes ?? [];
$authenticated = $authenticated ?? false;
$adminLogin = $adminLogin ?? null;
$csrf = $csrf ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= ww_e($title) ?> — админка Wares &amp; Wood</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/front/images/favicon.png">
    <link rel="stylesheet" href="/front/admin/assets/admin.css">
</head>
<body>
<header class="topbar">
    <div class="topbar__inner">
        <a class="topbar__brand" href="/front/admin/">Wares &amp; Wood — каталог</a>

        <?php if ($authenticated): ?>
            <div class="topbar__actions">
                <a class="topbar__link" href="/front/index.php" target="_blank" rel="noopener">Открыть сайт</a>
                <span class="topbar__user"><?= ww_e($adminLogin) ?></span>
                <form method="post" action="/front/admin/">
                    <input type="hidden" name="action" value="logout">
                    <input type="hidden" name="csrf" value="<?= ww_e($csrf) ?>">
                    <button type="submit" class="button button--ghost">Выйти</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="page">
    <?php foreach ($flashes as $flash): ?>
        <div class="alert alert--<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
            <?= ww_e($flash['message']) ?>
        </div>
    <?php endforeach; ?>

    <?= $content ?>
</main>

<script src="/front/admin/assets/admin.js" defer></script>
</body>
</html>
