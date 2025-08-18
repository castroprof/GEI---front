const precoMin = document.getElementById('preco-min');
const precoMax = document.getElementById('preco-max');
const valMin = document.getElementById('val-min');
const valMax = document.getElementById('val-max');

function atualizarValores() {
  let min = parseInt(precoMin.value);
  let max = parseInt(precoMax.value);

  if (min > max - 10) {
    precoMin.value = max - 10;
    min = max - 10;
  }

  if (max < min + 10) {
    precoMax.value = min + 10;
    max = min + 10;
  }

  valMin.textContent = `R$ ${min}`;
  valMax.textContent = `R$ ${max}`;
}

precoMin.addEventListener('input', atualizarValores);
precoMax.addEventListener('input', atualizarValores);

atualizarValores();


const produtos = document.querySelectorAll('.produto');
const modal = document.getElementById('modal');
const modalImg = document.getElementById('modal-img');
const modalTitle = document.getElementById('modal-title');
const modalPrice = document.getElementById('modal-price');
const modalDesc = document.getElementById('modal-description');
const modalWhatsapp = document.getElementById('modal-whatsapp');

const produtosInfo = Array.from(produtos).map(p => ({
  img: p.querySelector('img').src,
  title: p.querySelector('h4').textContent,
  price: p.querySelector('span').textContent,
  desc: 'Descrição detalhada do produto aqui.', // Pode ser substituído por dados reais
  link: 'https://wa.me/5511999999999?text=Quero%20este%20produto:%20' + encodeURIComponent(p.querySelector('h4').textContent)
}));

let currentIndex = 0;

produtos.forEach((produto, index) => {
  produto.addEventListener('click', () => {
    abrirModal(index);
  });
});

function abrirModal(index) {
  currentIndex = index;
  const p = produtosInfo[index];
  modalImg.src = p.img;
  modalTitle.textContent = p.title;
  modalPrice.textContent = p.price;
  modalDesc.textContent = p.desc;
  modalWhatsapp.href = p.link;
  modal.style.display = 'flex';
}

function fecharModal() {
  modal.style.display = 'none';
}

function navegar(direcao) {
  currentIndex = (currentIndex + direcao + produtosInfo.length) % produtosInfo.length;
  abrirModal(currentIndex);
}
