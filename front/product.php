
<?php include 'header.php';?>

<?php include 'products.php';

if (!isset($_GET['id']) || !isset($products[$_GET['id']])) {
    echo "Товар не найден";
    exit;
}

$product = $products[$_GET['id']];
?>

<section class="product-page container">
  <div class="product-wrapper">

    <div class="product-left">
      <div class="main-image">
        <img id="mainImage" src="/front/images/<?= htmlspecialchars($product['images'][0]) ?>" alt="Главное изображение">
      </div>
      <div class="thumbnails">
        <?php foreach ($product['images'] as $img): ?>
          <img class="thumbnail" src="/front/images/<?= htmlspecialchars($img) ?>" alt="Доп. изображение" onclick="document.getElementById('mainImage').src=this.src;">
        <?php endforeach; ?>
      </div>
    </div>

    <div class="product-right">
      <h1><?= htmlspecialchars($product['title']) ?></h1>
      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <p class="product-price"><strong>Цена: <?= htmlspecialchars($product['price']) ?></strong></p>
      <button class="btn-call" onclick="openModal('<?= htmlspecialchars($product['title'], ENT_QUOTES) ?>')">Заказать</button>
    </div>
  </div>
</section>

  <?php include 'modal_form.php'; ?>
  <?php include 'footer.php'; ?>
  <?php include 'modal_form.php'; ?>

  <script src="/front/js/form-handler.js"></script>
  <script src="/front/js/notifications.js"></script>
  <script src="/front/js/form-photo.js"></script>
