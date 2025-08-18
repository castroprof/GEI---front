function abrirPopup(src) {
  document.getElementById('popup-img').src = src;
  document.getElementById('popup-desc').innerText = 'Imagem selecionada da galeria';
  document.getElementById('popup-whatsapp').href = 'https://wa.me/5599999999999?text=Gostaria de comprar esta imagem: ' + encodeURIComponent(src);
  document.getElementById('popup').style.display = 'flex';
}

function fecharPopup() {
  document.getElementById('popup').style.display = 'none';
}
