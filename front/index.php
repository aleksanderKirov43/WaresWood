
<?php
include 'products.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Wares & Wood</title>

  <link rel="stylesheet" href="/front/css/style.css"/>
  <link rel="stylesheet" href="/front/css/slider.css"/>


</head>
<script src="/front/js/slider.js"></script>
<body>

<header>
  <div class="container">
    <!-- Десктоп: меню -->
    <div class="desktop-bar">
      <nav class="header_nav" id="nav-desktop">
        <ul>
          <li><a href="#catalog">Каталог</a></li>
          <li><a href="#map">Где купить</a></li>
          <li><a href="#delivery">Доставка</a></li>
          <li><a href="#contact">Контакты</a></li>
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
        <li><a href="#catalog">Каталог</a></li>
        <li><a href="#map">Где купить</a></li>
        <li><a href="#delivery">Доставка</a></li>
        <li><a href="#contact">Контакты</a></li>
      </ul>
    </nav>

  </div>
</header>


<main class="utp-block">
<!-- Слайдер -->
<section class="section-slider">
  <div class="slider-container">
    <div class="slider-track">
      <!-- Слайд 1 -->
      <div class="slide">
        <div class="slide-content container">
          <div>
            <h1 class="utp-title">Стеллажи из массива дерева от производителя в Москве</h1>
            <p>Производство и продажа в Москве. Доставка по всей России от 1 штуки.</p>
            <div class="slide-buttons">
              <button class="btn"><a href="#catalog">Подробнее</a></button>
              <button class="btn btn-secondary btn-call">Заказать звонок</button>
            </div>
          </div>
          <div>
            <img src="/front/images/f.jpg" alt="Стеллажи из массива дерева">
          </div>
        </div>
      </div>
      <!-- Слайд 2 -->
      <div class="slide">
        <div class="slide-content container">
          <div>
            <h1 class="utp-title">Надежные деревянные конструкции для дома и офиса</h1>
            <p>Эстетика, надёжность и долговечность — всё в одном решении.</p>
            <div class="slide-buttons">
              <button class="btn"><a href="#catalog">Подробнее</a></button>
              <button class="btn btn-secondary btn-call">Заказать звонок</button>
            </div>
          </div>
          <div>
            <img src="/front/images/product-s1.jpg" alt="Деревянные конструкции">
          </div>
        </div>
      </div>
      <!-- Слайд 3 -->
      <div class="slide">
        <div class="slide-content container">
          <div>
            <h1 class="utp-title">Индивидуальное производство по вашим размерам</h1>
            <p>Работаем по эскизам, подбираем материалы и воплощаем в жизнь.</p>
            <br>
            <br>
            <div class="slide-buttons">
              <button class="btn"><a href="#catalog">Подробнее</a></button>
              <button class="btn btn-secondary btn-call">Заказать звонок</button>
            </div>
          </div>
          <div>
            <img src="/front/images/product-s2.jpg" alt="Индивидуальное производство">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="slider-controls">
    <button class="slider-btn prev">‹</button>
    <button class="slider-btn next">›</button>
  </div>
  <div class="slider-dots">
  <span class="slider-dot active"></span>
  <span class="slider-dot"></span>
  <span class="slider-dot"></span>
</div>
</section>

<section class="advantages container">
  <div class="advantages-list">
    <div class="advantage">
      <img src="/front/images/icon1.png" alt="Доставка" class="adv-icon" />
      <p>Недорогая доставка — за типовой стеллаж 150х80х30 см доставка 800 руб</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon2.png" alt="Сборка" class="adv-icon" />
      <p>Простая сборка — все крепления и ключики для сборки кладем в комплект, дополнительный инструмент не понадобится</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon3.png" alt="Связь" class="adv-icon" />
      <p>Всегда на связи — если у вас возникнут вопросы по сборке, доставке или иные вопросы, мы на связи 24/7</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon4.png" alt="Гарантия" class="adv-icon" />
      <p>Гарантия на стеллажи 12 мес</p>
    </div>
  </div>
  </section>
  <div class="button-position">
  <button class="btn-call">Заказать звонок</button>
  </div>
  <hr id ="catalog" class="divider">

<section class="products container">
  <h1>Каталог товаров</h1>
  <div>
    <?php foreach ($products as $id => $product): ?>
      <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <h2><?= htmlspecialchars($product['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Цена: <?= htmlspecialchars($product['price']) ?></strong></p>
        <a href="product.php?id=<?= urlencode($id) ?>">Подробнее</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>  

<!-- <section class="products container">
  <h2 class="products-title">Стеллажи прованс на 6 полок</h2>
  <p class="products-subtitle">Цена указана за одно стандартное изделие.</p>

  <div class="products-list">
    <article class="product-card">
      <img src="/front/images/product-s1.jpg" alt="Ячейка К 80" class="product-img" />
      <h3>Стеллаж 182х39х32 см</h3>
      <p>Размеры:<br>Высота: 1820 мм.<br>Ширина: 390 мм.<br>Глубина: 320 мм.</p>
      <p class="price-label">ЦЕНА УКАЗАНА БЕЗ УЧЁТА ДОСТАВКИ</p>
      <p class="price">1 500 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>

    <article class="product-card">
      <img src="/front/images/product-s2.jpg" alt="Стеллаж 182х39х32 см" class="product-img" />
      <h3>Стеллаж 182х76х32 см</h3>
      <p>Размеры:<br>Высота: 1820 мм.<br>Ширина: 760 мм.<br>Глубина: 320 мм.</p>
      <p class="price-label">ЦЕНА УКАЗАНА БЕЗ УЧЁТА ДОСТАВКИ</p>
      <p class="price">1 500 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>

    <article class="product-card">
      <img src="/front/images/product-s3.jpg" alt="182х76х32 см" class="product-img" />
      <h3>Стеллаж 182х113х32 см</h3>
      <p>Размеры:<br>Высота: 1820 мм.<br>Ширина: 1130 мм.<br>Глубина: 320 мм.</p>
      <p class="price-label">ЦЕНА УКАЗАНА БЕЗ УЧЁТА ДОСТАВКИ</p>
      <p class="price">1 500 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>
  </div>
</section>

<section class="background-fon-orange">
<div class="products container">
  <h2 class="products-title">Решётки с запилом из сосны для беседок и ограждений. Двойная ячейка</h2>
  <p class="products-subtitle">Цена указана за одно стандартное изделие.</p>

  <div class="products-list">
    <article class="product-card">
      <img src="/front/images/product-k30x90.jpg" alt="Ячейка К 30х90" class="product-img" />
      <h3>Ячейка К 30х90</h3>
      <p>Решетка с запилом<br>брусок 20х30 мм.<br>без обвязки</p>
      <p class="price-label">ЦЕНА УКАЗАНА ЗА 1 КВ.М</p>
      <p class="price">1 600 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>

    <article class="product-card">
      <img src="/front/images/product-k30x120.jpg" alt="Ячейка К 30х120" class="product-img" />
      <h3>Ячейка К 30х120</h3>
      <p>Решетка с запилом<br>брусок 20х30 мм.<br>без обвязки</p>
      <p class="price-label">ЦЕНА УКАЗАНА ЗА 1 КВ.М</p>
      <p class="price">1 550 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>

    <article class="product-card">
      <img src="/front/images/product-k30x150.jpg" alt="Ячейка К 30х150" class="product-img" />
      <h3>Ячейка К 30х150</h3>
      <p>Решетка с запилом<br>брусок 20х30 мм.<br>без обвязки</p>
      <p class="price-label">ЦЕНА УКАЗАНА ЗА 1 КВ.М</p>
      <p class="price">1 500 руб.</p>
      <button class="btn-order">Заказать</button>
    </article>
    </div>
  </div>
</section> -->

<section class="background-fon-gray">
<div class="features-section container">
  <h2 class="products-title">Производим шпалеры типовые и по индивидуальным проектам!</h2>

  <div class="features-list">
    <div class="feature-row">
      <img src="/front/images/solid-wood.png" alt="Цельная древесина">
      <p>Цельная, а не клееная древесина</p>
    </div>
    <div class="feature-row">
      <img src="/front/images/painting.png" alt="Обработка и покраска">
      <p>Качественная обработка и покраска</p>
    </div>
    <div class="feature-row">
      <img src="/front/images/variety.png" alt="Ассортимент">
      <p>Огромный ассортимент</p>
    </div>
    <div class="feature-row">
      <img src="/front/images/custom-size.png" alt="Индивидуальные размеры">
      <p>Индивидуальные размеры</p>
    </div>
   </div>
  </div>
</section>

<section class="delivery-section container">
  <h2  id="delivery" class="products-title">Доставка и самовывоз!</h2>
  <h3 class="delivery-subtitle">НАШИ СКЛАДЫ:</h3>

  <div class="delivery-blocks">
    <div class="delivery-block">
      <h4>Склад Красногорск</h4>
      <p>Пн - сб работает по договоренности<br>телефон: <a href="tel:+79169662735">+7 (916) 966 27 35</a><br>Воскресенье — выходной.</p>
      <p>Московская область, г. Красногорск, ул. Заводская<br>Уточняйте, как проехать по телефону.</p>
    </div>

    <div class="delivery-block">
      <h4>Склад Волоколамск</h4>
      <p>Московская область, г. Волоколамск, ул. Пушкарская Слобода, д.47<br>
         Пн - пт: с 09:00 до 17:00<br>
         Суббота, воскресенье — выходной.</p>
    </div>
  </div>

  <h3 class="delivery-subtitle">ДОСТАВКА ПО МОСКВЕ И МОСКОВСКОЙ ОБЛАСТИ:</h3>
  <p>Доставка по Москве в пределах МКАД — <strong>от 1 500 руб.</strong><br>
     Доставка по Московской области — <strong>стоимость рассчитывается индивидуально.</strong></p>

  <h3 id="map" class="delivery-subtitle">ДОСТАВКА В РЕГИОНЫ:</h3>
  <p>Доставка до транспортной компании — <strong>500 руб.</strong><br>
     Доставка в регионы: <strong>1–7 дней</strong>, стоимость рассчитывается индивидуально.</p>

  <p id="contact"><strong>Количество, размер и наличие товара уточняйте:</strong><br>
     Телефон: <a href="tel:+79169662735">+7 (916) 966 27 35</a><br>
     E-mail: <a href="mailto:916506@mail.ru">916506@mail.ru</a></p>

  <div class="delivery-map">
    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Aexample&source=constructor" width="100%" height="400" frameborder="0"></iframe>
  </div>
</section>

</main>

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

<!-- Всплывающая форма -->
<div id="modalForm" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="modalClose" class="modal-close">&times;</span>
    <h2>Оставьте заявку</h2>
    <form id="modalContactForm" class="contact-form">
      <input type="text" name="name" placeholder="Имя" />
      <input type="tel" name="phone" placeholder="Телефон*" required />
      <textarea name="comment" placeholder="Комментарий"></textarea>
      <button type="submit" class="btn-order">Отправить</button>
    </form>
  </div>
</div>


  <script src="/front/js/main.js"></script>
  <script src="/front/js/form-handler.js"></script>
  <!-- <script src="/front/js/telegram.js"></script> -->
</body>
</html>
