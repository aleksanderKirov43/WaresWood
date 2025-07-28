<div id="modalForm" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="modalClose" class="modal-close">&times;</span>
    <h2>Оставьте заявку</h2>
    <form id="modalContactForm" class="contact-form">
      <input type="text" name="name" placeholder="Имя" />
      <input type="tel" name="phone" placeholder="Телефон*" required />
      <input type="hidden" id="productInput" name="title" />
      <textarea name="comment" placeholder="Комментарий"></textarea>
      <button type="submit" class="btn-order">Отправить</button>
    </form>
  </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
  function openModal(productTitle) {
    document.getElementById('productInput').value = productTitle;
    document.getElementById('modalForm').style.display = 'flex';
  }

  document.getElementById('modalClose').onclick = function() {
    document.getElementById('modalForm').style.display = 'none';
  }

  window.onclick = function(event) {
    const modal = document.getElementById('modalForm');
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  }

  // Навесить слушатель на кнопку открытия из body, чтобы она была видна глобально
  window.openModal = openModal; // чтобы onclick в html сработал

  document.getElementById('modalContactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('/send.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(text => {
      if (text.trim() === 'OK') {
        showNotification('Ваша заявка успешно отправлена. Вам скоро перезвонят!');
        form.reset();
        document.getElementById('modalForm').style.display = 'none';
      } else {
        showNotification('Ошибка при отправке: ' + text, true);
      }
    })
    .catch(() => {
      showNotification('Ошибка сети. Попробуйте позже.', true);
    });
  });
});
</script>
