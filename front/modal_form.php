<!-- modal_form.php -->
<div id="modalForm" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="modalClose" class="modal-close">&times;</span>
    <h2>Оставьте заявку</h2>
    <form id="modalContactForm" class="contact-form" method="post" action="/send.php">
      <input type="hidden" id="productInput" name="title" value="" />
      <input type="text" name="name" placeholder="Имя" required/>
      <input type="tel" name="phone" placeholder="Телефон*" />
      <textarea name="comment" placeholder="Комментарий"></textarea>
      <button type="submit" class="btn-order">Отправить</button>
    </form>
  </div>
</div>
