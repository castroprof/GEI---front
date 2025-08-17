<?php
// Começa imediatamente com PHP — sem linhas em branco antes
// Conexão com banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Tabela depoimentos deve ter: id, nome, texto, foto
$conn->query("CREATE TABLE IF NOT EXISTS depoimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    foto VARCHAR(255) NOT NULL
)");

// Inserir novo depoimento enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome']) && isset($_POST['texto'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $texto = $conn->real_escape_string($_POST['texto']);

    // Gera foto aleatória
    $genero = rand(0,1) ? 'men' : 'women';
    $numero = rand(0,99);
    $foto = "https://randomuser.me/api/portraits/$genero/$numero.jpg";

    $conn->query("INSERT INTO depoimentos (nome, texto, foto) VALUES ('$nome', '$texto', '$foto')");

    // Redireciona para evitar reenvio de form
    echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
    exit;
}

// Busca depoimentos do banco
$result = $conn->query("SELECT * FROM depoimentos ORDER BY id ASC");
$depoimentos = [];
while ($row = $result->fetch_assoc()) {
    $depoimentos[] = $row;
}
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
.botao:hover { background-color: #422016; }
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
#modal-depoimento input, #modal-depoimento textarea {
    width:100%;
    margin-bottom:15px;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}
#modal-depoimento h3 { margin-bottom: 20px; color: #5a2b1f; }
#modal-depoimento button { margin-top: 10px; }
textarea { resize: none; }
</style>
</head>
<body>

<section class="depoimentos-section">
  <h2>Depoimentos</h2>
  <div class="depoimento-wrapper">
    <button class="seta seta-esquerda" onclick="anteriorDepoimento()">←</button>
    <div class="depoimento-card" id="depoimento-card"></div>
    <button class="seta seta-direita" onclick="proximoDepoimento()">→</button>
  </div>
  <button class="botao" onclick="abrirFormulario()">Adicionar Depoimento</button>

  <!-- Modal -->
  <div id="modal-depoimento">
    <div class="modal-content">
      <h3>Adicionar Depoimento</h3>
      <form method="POST" onsubmit="return enviarDepoimento(this)">
        <input type="text" name="nome" id="nome-depoimento" placeholder="Seu nome" required>
        <textarea name="texto" id="texto-depoimento" placeholder="Escreva seu depoimento" required></textarea>
        <button type="submit" class="botao">Enviar</button>
        <button type="button" class="botao" onclick="fecharFormulario()" style="background-color:#ccc; color:#333; margin-left:10px;">Cancelar</button>
      </form>
    </div>
  </div>
</section>

<script>
let depoimentos = <?php echo json_encode($depoimentos); ?>;
let indiceAtual = 0;

function mostrarDepoimento() {
  const card = document.getElementById('depoimento-card');
  if(depoimentos.length===0){
      card.innerHTML = "<p>Nenhum depoimento ainda.</p>";
      return;
  }
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

function abrirFormulario() { document.getElementById('modal-depoimento').style.display = 'flex'; }
function fecharFormulario() { document.getElementById('modal-depoimento').style.display = 'none'; }

function enviarDepoimento(form) {
  // deixa o envio normal acontecer (POST) e fecha modal
  fecharFormulario();
  return true;
}

mostrarDepoimento();
</script>

</body>
</html>
