document.addEventListener('DOMContentLoaded', () => {
const sliderContainer = document.querySelector('.slider-container');
const sliderTrack = document.querySelector('.slider-track');
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.slider-dot');
const prevBtn = document.querySelector('.slider-btn.prev');
const nextBtn = document.querySelector('.slider-btn.next');

let currentSlide = 0;
const totalSlides = slides.length;

function updateSlider() {
  sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
  dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
}

function goToSlide(n) {
  if (n < 0) n = totalSlides - 1;
  if (n >= totalSlides) n = 0;
  currentSlide = n;
  updateSlider();
}

prevBtn.addEventListener('click', () => {
  goToSlide(currentSlide - 1);
});

nextBtn.addEventListener('click', () => {
  goToSlide(currentSlide + 1);
});

dots.forEach((dot, i) => {
  dot.addEventListener('click', () => goToSlide(i));
});

// --- Свайпы ---

let startX = 0;
let isDragging = false;
let currentTranslate = 0;
let animationID = 0;

sliderContainer.addEventListener('touchstart', e => {
  startX = e.touches[0].clientX;
  isDragging = true;
});

sliderContainer.addEventListener('touchmove', e => {
  if (!isDragging) return;
  const currentX = e.touches[0].clientX;
  const diff = startX - currentX;
  // Можно сделать лёгкий перетяг слайдов (по желанию)
});

sliderContainer.addEventListener('touchend', e => {
  if (!isDragging) return;
  isDragging = false;
  const endX = e.changedTouches[0].clientX;
  const diff = startX - endX;

  if (diff > 50) {
    // свайп влево — следующий слайд
    goToSlide(currentSlide + 1);
  } else if (diff < -50) {
    // свайп вправо — предыдущий слайд
    goToSlide(currentSlide - 1);
  }
  // если свайп меньше 50px — ничего не меняем
});

// Инициализация
updateSlider();
});