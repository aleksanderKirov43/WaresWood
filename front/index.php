<?php include 'header.php'
?>

<?php
include 'products.php';
?>

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
      <p><strong>Доставка — от 800 ₽.</strong> Типовой стеллаж 150×80×30 см доставим всего за 800 рублей.</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon2.png" alt="Сборка" class="adv-icon" />
      <p><strong>В комплекте — всё для сборки.</strong> Никаких дополнительных инструментов не нужно.</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon3.png" alt="Связь" class="adv-icon" />
      <p><strong>На связи c 9:00 до 21:00</strong> — Ответим на вопросы по сборке, доставке и не только.</p>
    </div>
    <div class="advantage">
      <img src="/front/images/icon4.png" alt="Гарантия" class="adv-icon" />
      <p><strong>Гарантия 12 мес.</strong> Мы уверены в качестве — гарантия на все стеллажи.</p>
    </div>
  </div>
  </section>
  <div class="button-position">
  <button class="btn btn-call">Заказать звонок</button>
  </div>
  <hr id ="catalog" class="divider">

<section class="products container">
  <div class="products-list">
<?php foreach ($products as $categoryId => $category): ?>
  <section class="products container">
    <h2 class="products-title"><?= htmlspecialchars($category['title']) ?></h2>
    <p class="products-subtitle">Цена указана за одно стандартное изделие.</p>

    <div class="products-list">
      <?php foreach ($category['items'] as $id => $product): ?>
        <article class="product-card" data-id="<?= $id ?>">
          <div class="product-image-wrapper">
            <img 
              src="/front/images/<?= htmlspecialchars($product['images'][0]) ?>" 
              alt="<?= htmlspecialchars($product['title']) ?>" 
              class="product-img"
            />
            <div class="image-dots-overlay">
              <?php foreach ($product['images'] as $index => $img): ?>
                <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
              <?php endforeach; ?>
            </div>
          </div>
          <h4><?= htmlspecialchars($product['title']) ?></h4>
          <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
          <p><strong>Цена: <?= htmlspecialchars($product['price']) ?></strong></p>
          <a class="btn-detailed" href="/front/product.php?id=<?= urlencode($id) ?>">Подробнее</a>
          <button class=" btn btn-call">Заказать</button>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
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
  <h3 class="delivery-subtitle">НАШЕ ПРОИЗВОДСТВО:</h3>

  <div class="delivery-blocks">
    <div class="delivery-block">
      <h4>Склад д.Масюгино</h4>
      <p>Пн - сб работает по договоренности<br>телефон: <a href="tel:+79226677059">+7 (922) 667‑70‑59</a><br>Воскресенье — выходной.</p>
      <p>Московская область, д. Масюгино, коттеджный посёлок Высоковские Дачи, 48<br>Уточняйте, как проехать по телефону.</p>
    </div>

  <h3 class="delivery-subtitle">ДОСТАВКА ПО МОСКВЕ И МОСКОВСКОЙ ОБЛАСТИ:</h3>
  <p>Доставка по Москве в пределах МКАД — <strong>от 800 руб.</strong><br>
     Доставка по Московской области — <strong>стоимость рассчитывается индивидуально.</strong></p>

  <h3 id="map" class="delivery-subtitle">ДОСТАВКА В РЕГИОНЫ:</h3>
  <p>Доставка до транспортной компании — <strong>500 руб.</strong><br>
     Доставка в регионы: <strong>1–7 дней</strong>, стоимость рассчитывается индивидуально.</p>

  <p id="contact"><strong>Количество, размер и наличие товара уточняйте:</strong><br>
     Телефон: <a href="tel:+79226677059">+7 (922) 667‑70‑59</a><br>
     E-mail: <a href="teating@mail.ru">teating@mail.ru</a></p>

  <div class="delivery-map">
    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Aaf29a614b51fb2d4b02c5b0c90fa2fdcf9ca23f16a7de7f4cc3a43e39e839107&amp;source=constructor" width="1200" height="400" frameborder="0"></iframe>
  </div>
</section>

</main>

<?php include 'footer.php'; ?>
<?php include 'modal_form.php'; ?>

</body>
</html>
