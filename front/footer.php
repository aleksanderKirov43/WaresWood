<footer class="site-footer">
  <div class="footer-inner container">
    <nav class="footer-nav">
      <ul>
        <li><a href="#catalog">Каталог</a></li>
        <li><a href="#map">Где купить</a></li>
        <li><a href="#delivery">Доставка</a></li>
        <li><a href="#contact">Контакты</a></li>
      </ul>
    </nav>
    <p class="footer-phone"><a href="tel:+79226677059">+7 (922) 667‑70‑59</a></p>
  </div>

  <div class="footer-sign-wrap">
    <p class="footer-sign">© 2025 mr.FD | Freelance Developer </p>
  </div>
</footer>

<?php
// $products задаётся в products.php до include; здесь защищаемся,
// чтобы футер можно было подключать и на страницах без каталога,
// а Intelephense не ругался на неопределённую переменную.
$catalogForMap = isset($products) && is_array($products) ? $products : [];
$map = [];

foreach ($catalogForMap as $cat) {
    if (!is_array($cat) || !isset($cat['items']) || !is_array($cat['items'])) {
        continue;
    }

    foreach ($cat['items'] as $id => $product) {
        $map[$id] = $product['images'] ?? [];
    }
}
?>
<script>
  window.productImageMap = <?= json_encode($map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>


