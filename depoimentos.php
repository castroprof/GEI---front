<?php
$depoimentos = [
    ["nome"=>"João", "texto"=>"Adorei o serviço!", "foto"=>"https://randomuser.me/api/portraits/men/1.jpg"],
    ["nome"=>"Maria", "texto"=>"Recomendo muito!", "foto"=>"https://randomuser.me/api/portraits/women/2.jpg"],
    ["nome"=>"Carlos", "texto"=>"Excelente experiência.", "foto"=>"https://randomuser.me/api/portraits/men/3.jpg"]
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Depoimentos</title>
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/depoimentos.css">
<style>
/* Botão padrão do site */
.botao {
  display: inline-block;
  padding: 12px 20px;
  background-color: #5a2b1f;
  color: #fff;
  text-decoration: none;
  border-radius: 6px;
  font-weight: bold;
  margin-top: 10px;
  transition: background-color 0.3s;
  cursor: pointer;
  border: none;
}
.botao:hover {
  background-color: #422016;
}

/* Modal melhorado */
#modal-depoimento {
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.6);
    align-items:center;
    justify-content:center;
    z-index: 1000;
}

#modal-depoimento .modal-content {
    background:#fff;
    padding:30px 25px;
    border-radius:12px;
    max-width:450px;
    width:90%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    text-align: center;
}

#modal-depoimento input, 
#modal-depoimento textarea {
    width:100%;
    margin-bottom:15px;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

#modal-depoimento h3 {
    margin-bottom: 20px;
    color: #5a2b1f;
}

#modal-depoimento button {
    margin-top: 10px;
}

textarea {
  resize: none;
}
</style>
</head>
<body>

<section class="depoimentos-section">
  <h2>Depoimentos</h2>

  <div class="depoimento-wrapper">
    <button class="seta seta-esquerda" onclick="anteriorDepoimento()">←</button>

    <div class="depoimento-card" id="depoimento-card">
      <!-- Conteúdo será preenchido via JS -->
    </div>

    <button class="seta seta-direita" onclick="proximoDepoimento()">→</button>
  </div>

  <!-- Botão para adicionar depoimento com estilo do site -->
  <button class="botao" onclick="abrirFormulario()">Adicionar Depoimento</button>

  <!-- Modal -->
  <div id="modal-depoimento">
    <div class="modal-content">
      <h3>Adicionar Depoimento</h3>
      <input type="text" id="nome-depoimento" placeholder="Seu nome">
      <textarea id="texto-depoimento" placeholder="Escreva seu depoimento"></textarea>
      <button class="botao" onclick="adicionarDepoimento()">Enviar</button>
      <button class="botao" onclick="fecharFormulario()" style="background-color:#ccc; color:#333; margin-left:10px;">Cancelar</button>
    </div>
  </div>
</section>

<script>
let depoimentos = <?php echo json_encode($depoimentos); ?>;
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

// Modal
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
    const genero = Math.random() < 0.5 ? "men" : "women";
    const numero = Math.floor(Math.random() * 100);
    const foto = `https://randomuser.me/api/portraits/${genero}/${numero}.jpg`;

    depoimentos.push({nome, texto, foto});
    indiceAtual = depoimentos.length - 1;
    mostrarDepoimento();
    fecharFormulario();

    document.getElementById('nome-depoimento').value = '';
    document.getElementById('texto-depoimento').value = '';
  } else {
    alert("Preencha nome e depoimento!");
  }
}

mostrarDepoimento();
</script>

</body>
</html>
