document.addEventListener('DOMContentLoaded', function () {
  const images = window.productImages || [];
  let currentIndex = 0;

  const modal = document.getElementById("imageModal");
  const modalImage = document.getElementById("modalImage");

  if (!modal || !modalImage) return;

  window.openImageModal = function(index) {
    currentIndex = index;
    modalImage.src = "/front/images/" + images[currentIndex];
    modal.style.display = "block";
  };

  window.closeImageModal = function() {
    modal.style.display = "none";
  };

  window.prevImage = function() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    modalImage.src = "/front/images/" + images[currentIndex];
  };

  window.nextImage = function() {
    currentIndex = (currentIndex + 1) % images.length;
    modalImage.src = "/front/images/" + images[currentIndex];
  };

  // Закрытие по фону
  window.addEventListener('click', function(event) {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
});
