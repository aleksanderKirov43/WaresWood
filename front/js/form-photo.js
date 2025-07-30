let currentIndex = 0;

function openImageModal(index) {
  if (!window.productImages || !window.productImages[index]) return;

  currentIndex = index;
  const modal = document.getElementById("imageModal");
  const img = document.getElementById("modalImage");
  img.src = "/front/images/" + window.productImages[index];
  modal.style.display = "block";
}

function closeImageModal() {
  document.getElementById("imageModal").style.display = "none";
}

function prevImage() {
  if (!window.productImages) return;

  currentIndex = (currentIndex - 1 + window.productImages.length) % window.productImages.length;
  document.getElementById("modalImage").src = "/front/images/" + window.productImages[currentIndex];
}

function nextImage() {
  if (!window.productImages) return;

  currentIndex = (currentIndex + 1) % window.productImages.length;
  document.getElementById("modalImage").src = "/front/images/" + window.productImages[currentIndex];
}

// Закрытие по клику вне модалки
window.onclick = function (event) {
  const modal = document.getElementById("imageModal");
  if (event.target === modal) modal.style.display = "none";
};
