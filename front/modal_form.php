<div id="modalForm" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="modalClose" class="modal-close">&times;</span>
    <h2>Оставьте заявку</h2>
    <form id="modalContactForm" class="contact-form">
      <input type="text" name="name" placeholder="Имя" />
      <input type="tel" name="phone" placeholder="Телефон*" required />
      <textarea name="comment" placeholder="Комментарий"></textarea>
      <button type="submit" class="btn-order">Отправить</button>
    </form>
  </div>
</div>

<!-- Контейнер для уведомлений -->
<div id="notification" style="
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #4CAF50;
  color: white;
  padding: 15px 25px;
  border-radius: 5px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.4s ease;
  z-index: 1100;
"></div>

<script>
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

function showNotification(message, isError = false) {
  const notification = document.getElementById('notification');
  notification.textContent = message;
  notification.style.background = isError ? '#f44336' : '#4CAF50';
  notification.style.opacity = '1';
  notification.style.pointerEvents = 'auto';

  setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.pointerEvents = 'none';
  }, 3000);
}
</script>
