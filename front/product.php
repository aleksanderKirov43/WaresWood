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
    .modal { position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:1000; }
    .modal-content { position:relative; }
    .modal-close { position:absolute; top:5px; right:10px; cursor:pointer; font-size:20px; }
    .btn-order { padding: 10px 20px; background: #333; color: #fff; border: none; cursor: pointer; }
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

  <button class="btn-order" onclick="openModal('<?= htmlspecialchars($product['title'], ENT_QUOTES) ?>')">Заказать</button>

  <!-- Модальное окно с формой -->
<?php include '/front/modal_form.php'; ?>

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
