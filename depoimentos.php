<?php
// Conexão com banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Tabela depoimentos (nome compatível com CRUD)
$conn->query("CREATE TABLE IF NOT EXISTS depoimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    text TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
)");

// Inserir novo depoimento enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['text'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $text = $conn->real_escape_string($_POST['text']);

    // Gera imagem aleatória
    $genero = rand(0,1) ? 'men' : 'women';
    $numero = rand(0,99);
    $image = "https://randomuser.me/api/portraits/$genero/$numero.jpg";

    $conn->query("INSERT INTO depoimentos (name, text, image) VALUES ('$name', '$text', '$image')");

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
<style>
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

.depoimento-wrapper { display:flex; align-items:center; gap:10px; justify-content:center; margin-top:20px; }
.depoimento-card { padding:20px; text-align:center; max-width:300px; }
.seta { cursor:pointer; font-size:24px; background:none; border:none; }
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
        <input type="text" name="name" id="nome-depoimento" placeholder="Seu nome" required>
        <textarea name="text" id="texto-depoimento" placeholder="Escreva seu depoimento" required></textarea>
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
    <img src="${depo.image}" alt="${depo.name}" style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
    <h4>${depo.name}</h4>
    <p>${depo.text}</p>
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
  fecharFormulario();
  return true;
}

mostrarDepoimento();
</script>

</body>
</html>
