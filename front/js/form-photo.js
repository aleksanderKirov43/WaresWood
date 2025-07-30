window.images = window.productImages || [];
window.currentIndex = 0;

window.openImageModal = function(index) {
  currentIndex = index;
  const modal = document.getElementById("imageModal");
  const img = document.getElementById("modalImage");
  img.src = "/front/images/" + images[index];
  modal.style.display = "block";
}

window.closeImageModal = function() {
  document.getElementById("imageModal").style.display = "none";
}

window.prevImage = function() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  document.getElementById("modalImage").src = "/front/images/" + images[currentIndex];
}

window.nextImage = function() {
  currentIndex = (currentIndex + 1) % images.length;
  document.getElementById("modalImage").src = "/front/images/" + images[currentIndex];
}

// Закрытие по клику вне модалки
window.onclick = function(event) {
  const modal = document.getElementById("imageModal");
  if (event.target === modal) modal.style.display = "none";
}
