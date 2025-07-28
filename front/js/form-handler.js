document.addEventListener('DOMContentLoaded', () => {
  /* ---------- DOM ---------- */
  const modal        = document.getElementById('modalForm');
  const modalClose   = document.getElementById('modalClose');
  const modalForm    = modal.querySelector('form');
  const contactForm  = document.getElementById('contactForm');
  const modalComment = modalForm.querySelector('textarea[name="comment"]');

  /* ---------- Переменная для заголовка ---------- */
  let currentTitle = '';

  /* ---------- Открытие модалки ---------- */
  // «Заказать звонок»
  document.querySelectorAll('.btn-call').forEach(btn => {
    btn.addEventListener('click', () => {
      currentTitle = 'Заказ звонка с сайта';
      modalComment.value = '';
      openModal();
    });
  });

  // «Заказать» на карточке
  document.querySelectorAll('.product-card .btn-order').forEach(btn => {
    btn.addEventListener('click', () => {
      const card  = btn.closest('.product-card');
      const title = card?.querySelector('h3')?.textContent.trim() || '';
      currentTitle = `Заказ: ${title}`;
      modalComment.value = '';
      openModal();
    });
  });

  function openModal() {
    modal.style.display = 'flex';
    modalForm.reset();
    modalComment.focus();
  }

  /* ---------- Закрытие модалки ---------- */
  modalClose.addEventListener('click', () => (modal.style.display = 'none'));
  modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

  /* ---------- Универсальная отправка ---------- */
  async function sendForm(form, title) {
    const fd = new FormData(form);
    fd.append('title', title);           // добавляем заголовок к FormData

    try {
      const res = await fetch('/send.php', {
        method: 'POST',
        body  : fd                       // никаких headers!
      });

      if (!res.ok) throw new Error(await res.text());
     showNotification('✅ Заявка отправлена!');
      form.reset();
      if (form === modalForm) modal.style.display = 'none';
    } catch (err) {
      console.error('❌ Ошибка:', err.message);
      alert('❌ Ошибка при отправке');
    }
  }

  /* ---------- Обработчики submit ---------- */
  modalForm  .addEventListener('submit', e => { e.preventDefault(); sendForm(modalForm,  currentTitle); });
  contactForm.addEventListener('submit', e => { e.preventDefault(); sendForm(contactForm,'Заявка на звонок'); });
});
