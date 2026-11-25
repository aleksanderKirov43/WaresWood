<?php

declare(strict_types=1);

/**
 * @var string $message
 */
?>
<section class="card card--narrow">
    <h1 class="card__title">Не получилось</h1>

    <div class="alert alert--error"><?= ww_e($message) ?></div>

    <a class="button button--primary" href="/front/admin/">Вернуться к каталогу</a>
</section>
