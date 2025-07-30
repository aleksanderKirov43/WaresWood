  document.getElementById('mainImage').addEventListener('click', function () {
    const src = this.src;
    const win = window.open();
    win.document.write('<img src="' + src + '" style="width:100%">');
  });