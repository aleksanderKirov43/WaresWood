let images = [];
let currentIndex = 0;

function openImageModal(index) {
  currentIndex = index;
  const modal = document.getElementById("imageModal");
  const img = document.getElementById("modalImage");
  img.src = "/front/images/" + images[index];
  modal.style.display = "block";
}

function closeImageModal() {
  document.getElementById("imageModal").style.display = "none";
}

function prevImage() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  document.getElementById("modalImage").src = "/front/images/" + images[currentIndex];
}

function nextImage() {
  currentIndex = (currentIndex + 1) % images.length;
  document.getElementById("modalImage").src = "/front/images/" + images[currentIndex];
}

// Закрытие по клику вне модалки
window.onclick = function(event) {
  const modal = document.getElementById("imageModal");
  if (event.target === modal) modal.style.display = "none";
}
