document.addEventListener('DOMContentLoaded', () => {
  console.log('telegram.js загружен');

  const modal = document.getElementById('modalForm');
  const modalClose = document.getElementById('modalClose');
  const modalForm = document.getElementById('modalContactForm');
  const contactForm = document.getElementById('contactForm');
  const modalComment = modalForm?.querySelector('textarea[name="comment"]');

  let currentTitle = '';

  // Все кнопки .btn-call (в карточках, баннерах и т.д.)
  document.querySelectorAll('.btn-call').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.product-card');
      const name = card?.querySelector('h1,h2,h3,h4')?.textContent.trim();
      currentTitle = name ? `Заказ: ${name}` : 'Заказ звонка с сайта';

      if (modalComment) modalComment.value = '';
      openModal();
    });
  });

  // Кнопка в product.php
  const singleProductTitle = document.querySelector('.product-right h1')?.textContent.trim();
  const singleProductBtn = document.querySelector('.product-right .btn-call');
  if (singleProductBtn && singleProductTitle) {
    singleProductBtn.addEventListener('click', () => {
      currentTitle = `Заказ: ${singleProductTitle}`;
      if (modalComment) modalComment.value = '';
      openModal();
    });
  }

  function openModal() {
    if (!modal || !modalForm) return;
    modal.style.display = 'flex';
    modalForm.reset();
    if (modalComment) modalComment.focus();
  }

  modalClose?.addEventListener('click', () => (modal.style.display = 'none'));
  modal?.addEventListener('click', e => {
    if (e.target === modal) modal.style.display = 'none';
  });

  async function sendData(form, extraTitle = '') {
    const fd = new FormData(form);
    fd.append('title', extraTitle);

    try {
      const res = await fetch('/send.php', {
        method: 'POST',
        body: fd
      });

      if (!res.ok) throw new Error(await res.text());
      showNotification('✅ Заявка отправлена!');
      form.reset();
      if (form === modalForm && modal) modal.style.display = 'none';
    } catch (err) {
      console.error('Ошибка при отправке:', err.message);
      alert('❌ Ошибка при отправке');
    }
  }

  modalForm?.addEventListener('submit', e => {
    e.preventDefault();
    sendData(modalForm, currentTitle);
  });

  contactForm?.addEventListener('submit', e => {
    e.preventDefault();
    sendData(contactForm, 'Заявка на звонок');
  });
});
