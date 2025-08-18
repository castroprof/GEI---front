let depoimentos = [
  {nome:"João", texto:"Adorei o serviço!", foto:"https://randomuser.me/api/portraits/men/1.jpg"},
  {nome:"Maria", texto:"Recomendo muito!", foto:"https://randomuser.me/api/portraits/women/2.jpg"},
  {nome:"Carlos", texto:"Excelente experiência.", foto:"https://randomuser.me/api/portraits/men/3.jpg"}
];

let indiceAtual = 0;

function mostrarDepoimento() {
  const card = document.getElementById('depoimento-card');
  const depo = depoimentos[indiceAtual];
  card.innerHTML = `
    <img src="${depo.foto}" alt="${depo.nome}" style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
    <h4>${depo.nome}</h4>
    <p>${depo.texto}</p>
  `;
}

function proximoDepoimento() {
  indiceAtual = (indiceAtual + 1) % depoimentos.length;
  mostrarDepoimento();
}

function anteriorDepoimento() {
  indiceAtual = (indiceAtual - 1 + depoimentos.length) % depoimentos.length;
  mostrarDepoimento();
}

// Abrir/Fechar modal
function abrirFormulario() {
  document.getElementById('modal-depoimento').style.display = 'flex';
}

function fecharFormulario() {
  document.getElementById('modal-depoimento').style.display = 'none';
}

// Adicionar depoimento
function adicionarDepoimento() {
  const nome = document.getElementById('nome-depoimento').value.trim();
  const texto = document.getElementById('texto-depoimento').value.trim();

  if(nome && texto) {
    // Gerar foto aleatória usando Random User API
    const genero = Math.random() < 0.5 ? "men" : "women";
    const numero = Math.floor(Math.random() * 100); // número entre 0 e 99
    const foto = `https://randomuser.me/api/portraits/${genero}/${numero}.jpg`;

    depoimentos.push({nome, texto, foto});
    indiceAtual = depoimentos.length - 1; // mostra o depoimento adicionado
    mostrarDepoimento();
    fecharFormulario();

    // Limpar campos
    document.getElementById('nome-depoimento').value = '';
    document.getElementById('texto-depoimento').value = '';
  } else {
    alert("Preencha nome e depoimento!");
  }
}

// Mostrar o primeiro depoimento ao carregar
mostrarDepoimento();
