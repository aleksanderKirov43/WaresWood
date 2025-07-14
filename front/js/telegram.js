document.addEventListener('DOMContentLoaded', () => {
  /* --------- DOM‑ссылки --------- */
  const modal       = document.getElementById('modalForm');
  const modalClose  = document.getElementById('modalClose');
  const modalForm   = modal.querySelector('form');
  const contactForm = document.getElementById('contactForm');
  const modalComment= modalForm.querySelector('textarea[name="comment"]');

  /* --------- Переменная под заголовок --------- */
  let currentTitle = '';

  /* --------- Открытие модалки --------- */
  // 1. Кнопка «Заказать звонок»
  document.querySelectorAll('.btn-call').forEach(btn => {
    btn.addEventListener('click', () => {
      currentTitle = 'Заказ звонка с сайта';
      modalComment.value = '';                 // комментарий оставляем пустым
      openModal();
    });
  });

  // 2. Кнопки «Заказать» внутри карточек товара
  document.querySelectorAll('.product-card .btn-order').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.product-card');
      const name = card?.querySelector('h3')?.textContent.trim() || '';
      currentTitle = `Заказ: ${name}`;
      modalComment.value = '';                 // или pre-fill, если нужно
      openModal();
    });
  });

  function openModal() {
    modal.style.display = 'flex';
    modalForm.reset();                         // чистим поля имени/телефона
    modalComment.focus();
  }

  /* --------- Закрытие модалки --------- */
  modalClose.addEventListener('click', () => (modal.style.display = 'none'));
  modal.addEventListener('click',  e => { if (e.target === modal) modal.style.display = 'none'; });

  /* --------- Универсальная отправка --------- */
  async function sendData(form, extraTitle = '') {
    const fd = new FormData(form);

    const payload = {
      title:   extraTitle,                                // заголовок
      name:    fd.get('name')    ?.trim() || '',
      phone:   fd.get('phone')   ?.trim() || '',
      comment: fd.get('comment') ?.trim() || '',
    };

    console.log('📦 Отправка:', payload);

    try {
      const r = await fetch('http://a1147424.xsph.ru/send', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify(payload),
      });

      if (!r.ok) throw new Error(await r.text());
      alert('✅ Заявка отправлена!');
      form.reset();
      if (form === modalForm) modal.style.display = 'none';
    } catch (err) {
      console.error('Ошибка:', err.message);
      alert('❌ Ошибка при отправке');
    }
  }

  /* --------- Обработчики submit --------- */
  modalForm.addEventListener('submit',  e => { e.preventDefault(); sendData(modalForm, currentTitle); });
  contactForm.addEventListener('submit',e => { e.preventDefault(); sendData(contactForm, 'Заявка на звонок'); });
});
