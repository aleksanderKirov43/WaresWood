/**
 * Клиентские удобства админки: подтверждение опасных действий и предпросмотр
 * выбранных файлов до отправки формы.
 */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
    const container = document.getElementById(input.dataset.preview);

    if (!container) {
      return;
    }

    input.addEventListener('change', () => {
      // Object URL освобождается после загрузки, иначе файлы висят в памяти.
      container.textContent = '';

      Array.from(input.files || []).forEach((file) => {
        if (!file.type.startsWith('image/')) {
          return;
        }

        const figure = document.createElement('figure');
        figure.className = 'gallery__item';

        const image = document.createElement('img');
        const url = URL.createObjectURL(file);
        image.src = url;
        image.alt = '';
        image.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });

        const caption = document.createElement('figcaption');
        caption.className = 'gallery__actions';
        caption.textContent = 'Будет добавлено при сохранении';

        figure.append(image, caption);
        container.append(figure);
      });
    });
  });
});
