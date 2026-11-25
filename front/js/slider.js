document.addEventListener('DOMContentLoaded', () => {
  const sliderContainer = document.querySelector('.slider-container');
  const sliderTrack = document.querySelector('.slider-track');
  const slides = document.querySelectorAll('.section-slider .slide');
  const dots = document.querySelectorAll('.section-slider .slider-dot');
  const prevBtn = document.querySelector('.slider-btn.prev');
  const nextBtn = document.querySelector('.slider-btn.next');

  // Без разметки баннера ничего не делаем — иначе падение здесь
  // остановило бы и остальные обработчики на странице.
  if (!sliderContainer || !sliderTrack || slides.length === 0) {
    return;
  }

  let currentSlide = 0;
  const totalSlides = slides.length;

  function updateSlider() {
    // translateX в процентах считается от ширины самого track, а не контейнера.
    // Track шире контейнера на число слайдов, поэтому двигаем в пикселях.
    const offset = currentSlide * sliderContainer.clientWidth;
    sliderTrack.style.transform = `translateX(-${offset}px)`;
    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
  }

  function goToSlide(n) {
    if (n < 0) {
      n = totalSlides - 1;
    }

    if (n >= totalSlides) {
      n = 0;
    }

    currentSlide = n;
    updateSlider();
  }

  prevBtn?.addEventListener('click', () => goToSlide(currentSlide - 1));
  nextBtn?.addEventListener('click', () => goToSlide(currentSlide + 1));

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => goToSlide(i));
  });

  let startX = 0;
  let isDragging = false;

  sliderContainer.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
    isDragging = true;
  }, { passive: true });

  sliderContainer.addEventListener('touchend', e => {
    if (!isDragging) {
      return;
    }

    isDragging = false;
    const endX = e.changedTouches[0].clientX;
    const diff = startX - endX;

    if (diff > 50) {
      goToSlide(currentSlide + 1);
    } else if (diff < -50) {
      goToSlide(currentSlide - 1);
    }
  }, { passive: true });

  window.addEventListener('resize', updateSlider);

  updateSlider();
});
