document.addEventListener('DOMContentLoaded', () => {
  const productCards = document.querySelectorAll('.product-card');

  productCards.forEach(card => {
    const img = card.querySelector('.product-img');
    const dots = card.querySelectorAll('.dot');
    const images = Array.from(dots).map(dot => {
      const index = dot.dataset.index;
      return `/front/images/${dot.closest('.product-card').dataset.id && index ? getImageByIndex(card.dataset.id, index) : ''}`;
    });

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

    // Свайп
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
