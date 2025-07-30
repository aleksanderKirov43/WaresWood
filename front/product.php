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
          <img src="/front/images/<?= htmlspecialchars($product['images'][0]) ?>" alt="Главное изображение" onclick="openImageModal(0)">
      </div>
<div class="thumbnails">
  <?php foreach ($product['images'] as $index => $img): ?>
    <img class="thumbnail"
         src="/front/images/<?= htmlspecialchars($img) ?>"
         alt="Миниатюра <?= $index ?>"
         onclick="openImageModal(<?= $index ?>)">
  <?php endforeach; ?>
      </div>
    </div>

    <div class="product-right">
      <h1><?= htmlspecialchars($product['title']) ?></h1>
      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <p class="product-price"><strong>Цена: <?= htmlspecialchars($product['price']) ?></strong></p>
      <button class="btn-call">Заказать</button>
    </div>
  </div>
</section>


<div id="imageModal" class="image-modal">
  <span class="close-modal" onclick="closeImageModal()">&times;</span>
  <img id="modalImage" class="modal-content-image" src="" alt="Просмотр изображения">
  <div class="modal-controls">
    <button onclick="prevImage()">‹</button>
    <button onclick="nextImage()">›</button>
  </div>
</div>

  <?php include 'modal_form.php'; ?>
  <?php include 'footer.php'; ?>

<script>
  window.productImageMap = <?= json_encode(array_map(
    fn($p) => array_map(
      fn($img) => $img,
      $p['images']
    ),
    $products
  )) ?>;
</script>

