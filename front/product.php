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
  <link rel="stylesheet" href="/front/css/style.css" />
  <link rel="stylesheet" href="/front/css/product.css" />
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

  <button class="btn-call" onclick="openModal('<?= htmlspecialchars($product['title'], ENT_QUOTES) ?>')">Заказать</button>

  <?php include 'modal_form.php'; ?>

  <script>
    function openModal(productTitle) {
      document.getElementById('productInput').value = productTitle;
      document.getElementById('modalForm').style.display = 'block';
    }

    document.getElementById('modalClose').onclick = function() {
      document.getElementById('modalForm').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('modalForm');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }
  </script>
</body>
</html>
