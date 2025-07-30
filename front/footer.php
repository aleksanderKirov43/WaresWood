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
    <p class="footer-sign">© 2025 mr.Крендель | Freelance Developer </p>
  </div>
</footer>

  <script>
    window.productImageMap = <?= json_encode(array_map(
      fn($p) => array_map(
        fn($img) => $img,
        $p['images']
      ),
      $products
    )) ?>;
  </script>

  <script>
  window.productImages = <?= json_encode($product['images']) ?>;
  </script>
  <script src="/front/js/telegram.js"></script> 
  <script src="/front/js/notifications.js"></script> 
  <script src="/front/js/form-photo.js"></script> 
  <script src="/front/js/product-slider.js"></script> 
  <script src="/front/js/main.js"></script>
  <script src="/front/js/slider.js"></script>
