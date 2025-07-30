document.addEventListener('DOMContentLoaded', () => {
const productCards = document.querySelectorAll('.product-card');

productCards.forEach(card => {
  const img = card.querySelector('.product-img');
  const dots = card.querySelectorAll('.dot');
  const id = card.dataset.id;
  const images = window.productImageMap[id] || [];

  let currentIndex = 0;

  function showImage(index) {
    currentIndex = index;
    img.src = images[index];
    dots.forEach(dot => dot.classList.remove('active'));
    dots[index].classList.add('active');
  }

  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      const index = parseInt(dot.dataset.index, 10);
      showImage(index);
    });
  });

  let startX = null;
  img.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
  });

  img.addEventListener('touchend', e => {
    if (startX === null) return;
    const endX = e.changedTouches[0].clientX;
    const diff = startX - endX;
    if (Math.abs(diff) > 30) {
      if (diff > 0 && currentIndex < images.length - 1) {
        showImage(currentIndex + 1);
      } else if (diff < 0 && currentIndex > 0) {
        showImage(currentIndex - 1);
      }
    }
    startX = null;
  });
});

  // Вспомогательная функция — мапит id к images[]
  function getImageByIndex(id, index) {
    if (!window.productImageMap || !window.productImageMap[id]) return '';
    return window.productImageMap[id][index] || '';
  }
});
