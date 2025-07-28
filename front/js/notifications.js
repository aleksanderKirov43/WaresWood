window.showNotification = function(message, isError = false) {
  let notification = document.getElementById('notification');

  // Если контейнера для уведомлений нет — создаём его и добавляем в body
  if (!notification) {
    notification = document.createElement('div');
    notification.id = 'notification';
    Object.assign(notification.style, {
      position: 'fixed',
      bottom: '20px',
      right: '20px',
      background: '#4CAF50',
      color: 'white',
      padding: '15px 25px',
      borderRadius: '5px',
      boxShadow: '0 2px 10px rgba(0,0,0,0.2)',
      opacity: '0',
      pointerEvents: 'none',
      transition: 'opacity 0.4s ease',
      zIndex: '1100',
      maxWidth: '300px',
      fontSize: '16px',
      fontWeight: '600',
      userSelect: 'none',
    });
    document.body.appendChild(notification);
  }

  notification.textContent = message;
  notification.style.background = isError ? '#f44336' : '#4CAF50';
  notification.style.opacity = '1';
  notification.style.pointerEvents = 'auto';

  // Скрываем уведомление через 3 секунды
  clearTimeout(window._notificationTimeout);
  window._notificationTimeout = setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.pointerEvents = 'none';
  }, 3000);
}
