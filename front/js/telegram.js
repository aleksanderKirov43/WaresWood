document.addEventListener('DOMContentLoaded', () => {
  const modal       = document.getElementById('modalForm');
  const modalClose  = document.getElementById('modalClose');
  const modalForm   = modal?.querySelector('form');
  const contactForm = document.getElementById('contactForm');
  const modalComment= modalForm?.querySelector('textarea[name="comment"]');

  let currentTitle = '';

  // 1. Кнопка «Заказать звонок»
  document.querySelectorAll('.btn-call').forEach(btn => {
    btn.addEventListener('click', () => {
      currentTitle = 'Заказ звонка с сайта';
      if (modalComment) modalComment.value = '';
      openModal();
    });
  });

  // 2. Кнопки «Заказать» внутри карточек товара
  document.querySelectorAll('.product-card .btn-order').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.product-card');
      const name = card?.querySelector('h3')?.textContent.trim() || '';
      currentTitle = `Заказ: ${name}`;
      if (modalComment) modalComment.value = '';
      openModal();
    });
  });

  // 3. Кнопка на отдельной странице товара (product.php)
  const productTitle = document.querySelector('.product-right h1')?.textContent.trim();
  const singleProductBtn = document.querySelector('.product-right .btn-call');
  if (singleProductBtn && productTitle) {
    singleProductBtn.addEventListener('click', () => {
      currentTitle = `Заказ: ${productTitle}`;
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
  modal?.addEventListener('click',  e => { if (e.target === modal) modal.style.display = 'none'; });

  async function sendData(form, extraTitle = '') {
    const fd = new FormData(form);
    const payload = {
      title:   extraTitle,
      name:    fd.get('name')    ?.trim() || '',
      phone:   fd.get('phone')   ?.trim() || '',
      comment: fd.get('comment') ?.trim() || '',
    };

    try {
      const r = await fetch('/send.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify(payload),
      });

      if (!r.ok) throw new Error(await r.text());
      showNotification('✅ Заявка отправлена!');
      form.reset();
      if (form === modalForm && modal) modal.style.display = 'none';
    } catch (err) {
      console.error('Ошибка:', err.message);
      alert('❌ Ошибка при отправке');
    }
  }

  modalForm?.addEventListener('submit',  e => {
    e.preventDefault();
    sendData(modalForm, currentTitle);
  });

  contactForm?.addEventListener('submit', e => {
    e.preventDefault();
    sendData(contactForm, 'Заявка на звонок');
  });
});
