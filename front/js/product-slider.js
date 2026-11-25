document.addEventListener('DOMContentLoaded', () => {
  const productCards = document.querySelectorAll('.product-card');

  productCards.forEach(card => {
    const img = card.querySelector('.product-img');
    const dots = card.querySelectorAll('.dot');
    const id = card.dataset.id;

    if (!img || !id || dots.length === 0) {
      return;
    }

    let currentIndex = 0;

    function images() {
      // Карту читаем при каждом переключении: она появляется в конце страницы,
      // а этот скрипт подключён в <head> и не должен зависеть от порядка загрузки.
      const map = window.productImageMap || {};
      return Array.isArray(map[id]) ? map[id] : [];
    }

    function showImage(index) {
      const list = images();

      if (!list[index]) {
        return;
      }

      currentIndex = index;
      // Пути уже полные (/uploads/products/...), префикс /front/images/ не нужен.
      img.src = list[index];
      dots.forEach(dot => dot.classList.remove('active'));

      if (dots[index]) {
        dots[index].classList.add('active');
      }
    }

    dots.forEach(dot => {
      dot.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();

        const index = parseInt(dot.dataset.index, 10);

        if (!Number.isNaN(index)) {
          showImage(index);
        }
      });
    });

    let startX = null;

    img.addEventListener('touchstart', e => {
      startX = e.touches[0].clientX;
    }, { passive: true });

    img.addEventListener('touchend', e => {
      if (startX === null) {
        return;
      }

      const list = images();
      const endX = e.changedTouches[0].clientX;
      const diff = startX - endX;

      if (Math.abs(diff) > 30) {
        if (diff > 0 && currentIndex < list.length - 1) {
          showImage(currentIndex + 1);
        } else if (diff < 0 && currentIndex > 0) {
          showImage(currentIndex - 1);
        }
      }

      startX = null;
    }, { passive: true });
  });
});
