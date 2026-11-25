<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Wares & Wood</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/front/images/favicon.png">

<?php
/**
 * Версия статики по времени изменения файла — сбрасывает кэш браузера
 * после деплоя. Без этого старый product-slider.js продолжает клеить
 * префикс /front/images/ к уже полным путям из /uploads/products/.
 */
$wwAsset = static function (string $relativePath): string {
    $absolute = __DIR__ . '/' . ltrim($relativePath, '/');
    $version = is_file($absolute) ? (string) filemtime($absolute) : (string) time();

    return '/front/' . ltrim($relativePath, '/') . '?v=' . $version;
};
?>

  <link rel="stylesheet" href="<?= htmlspecialchars($wwAsset('css/style.css')) ?>"/>
  <link rel="stylesheet" href="<?= htmlspecialchars($wwAsset('css/slider.css')) ?>"/>
  <link rel="stylesheet" href="<?= htmlspecialchars($wwAsset('css/product.css')) ?>"/>

  <script src="<?= htmlspecialchars($wwAsset('js/telegram.js')) ?>" defer></script>
  <script src="<?= htmlspecialchars($wwAsset('js/notifications.js')) ?>" defer></script>
  <script src="<?= htmlspecialchars($wwAsset('js/form-photo.js')) ?>" defer></script>
  <script src="<?= htmlspecialchars($wwAsset('js/product-slider.js')) ?>" defer></script>
  <script src="<?= htmlspecialchars($wwAsset('js/main.js')) ?>" defer></script>
  <script src="<?= htmlspecialchars($wwAsset('js/slider.js')) ?>" defer></script>

</head>


<body>

<header>
  <div class="container">
    <!-- Десктоп: меню -->
    <div class="desktop-bar">
      <nav class="header_nav" id="nav-desktop">
        <ul>
          <li><a href="/front/index.php#catalog">Каталог</a></li>
          <li><a href="/front/index.php#map">Где купить</a></li>
          <li><a href="/front/index.php#delivery">Доставка</a></li>
          <li><a href="/front/index.php#contact">Контакты</a></li>
        </ul>
      </nav>
      <p class="phone" ><a href="tel:+79226677059">+7 (922) 667‑70‑59</a></p>
    </div>

    <!-- Мобилка: бургер -->
    <div class="mobile-bar">
      <div class="burger" id="burger">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <p class="phone"><a href="tel:+79226677059">+7 (922) 667‑70‑59</a></p>
    </div>

    <!-- Мобильное меню -->
    <nav class="header_nav" id="nav-mobile">
      <ul>
        <li><a href="/front/index.php#catalog">Каталог</a></li>
        <li><a href="/front/index.php#map">Где купить</a></li>
        <li><a href="/front/index.php#delivery">Доставка</a></li>
        <li><a href="/front/index.php#contact">Контакты</a></li>
      </ul>
    </nav>

  </div>
</header>
