<?php
include 'products.php';

if (!isset($_GET['id']) || !isset($products[$_GET['id']])) {
    echo "Товар не найден";
    exit;
}

$product = $products[$_GET['id']];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($product['title']) ?></title>
  <style>
    .thumbnail { width: 100px; margin: 5px; cursor: pointer; }
    #mainImage { max-width: 400px; display: block; margin-bottom: 15px; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($product['title']) ?></h1>
  <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
  <strong>Цена: <?= htmlspecialchars($product['price']) ?></strong>

  <div>
    <img id="mainImage" src="/front/images/<?= htmlspecialchars($product['images'][0]) ?>" alt="Главное изображение" />
    <div>
      <?php foreach ($product['images'] as $img): ?>
        <img class="thumbnail" src="/front/images/<?= htmlspecialchars($img) ?>" alt="Доп. изображение" onclick="document.getElementById('mainImage').src=this.src;" />
      <?php endforeach; ?>
    </div>
  </div>

  <button onclick="document.getElementById('modalForm').style.display='block'">Заказать</button>

  <!-- Модальное окно с формой заказа -->
  <div id="modalForm" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc;">
    <h2>Форма заказа</h2>
    <form method="post" action="send_order.php">
      <input type="hidden" name="product" value="<?= htmlspecialchars($product['title']) ?>">
      <p><input type="text" name="name" placeholder="Ваше имя" required></p>
      <p><input type="tel" name="phone" placeholder="Телефон" required></p>
      <p><textarea name="comment" placeholder="Комментарий"></textarea></p>
      <p><button type="submit">Отправить</button></p>
      <p><button type="button" onclick="document.getElementById('modalForm').style.display='none'">Закрыть</button></p>
    </form>
  </div>
</body>
</html>
