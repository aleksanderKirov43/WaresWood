<?php include 'header.php'; ?>
<?php include 'products.php';

$id = $_GET['id'] ?? '';
$product = null;

foreach ($products as $category) {
    if (isset($category['items'][$id])) {
        $product = $category['items'][$id];
        break;
    }
}

if (!$product) {
    echo "Товар не найден";
    exit;
}
?>

<section class="product-page container">
  <div class="product-wrapper">

    <div class="product-left">
      <?php if ($product['images'] !== []): ?>
        <div class="main-image">
          <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="Главное изображение" onclick="openImageModal(0)">
        </div>
        <div class="thumbnails">
          <?php foreach ($product['images'] as $index => $img): ?>
            <img class="thumbnail"
                 src="<?= htmlspecialchars($img) ?>"
                 alt="Миниатюра <?= $index ?>"
                 onclick="openImageModal(<?= $index ?>)">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="product-right">
      <h1><?= htmlspecialchars($product['title']) ?></h1>
      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <p class="product-price">
        <strong>Цена: <?= htmlspecialchars($product['price']) ?></strong>
        <?php if ($product['priceUnit'] !== ''): ?>
          <span class="price-label"><?= htmlspecialchars($product['priceUnit']) ?></span>
        <?php endif; ?>
      </p>
      <button class="btn-call">Заказать</button>
    </div>
  </div>
</section>

<div id="imageModal" class="image-modal">
  <span class="close-modal" onclick="closeImageModal()">&times;</span>
  <img id="modalImage" class="modal-content-image" src="" alt="Просмотр изображения">

  <div class="modal-controls">
    <span class="modal-arrow left" onclick="prevImage()">‹</span>
    <span class="modal-arrow right" onclick="nextImage()">›</span>
  </div>
</div>



  <script>
    window.productImages = <?= json_encode($product['images']) ?>;
  </script>
  
  <?php include 'footer.php'; ?>
  <?php include 'modal_form.php'; ?>




