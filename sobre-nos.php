<?php
// Ativa exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

// Conexão
$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) die("Erro de conexão: ".$conn->connect_error);

// Buscar o conteúdo mais recente de sobre_nos
$res = $conn->query("SELECT * FROM sobre_nos ORDER BY id DESC LIMIT 1");
$sobre = $res->num_rows > 0 ? $res->fetch_assoc() : null;

// Função para formatar texto
function formatarTexto($texto){
    $linhas = explode("\n", $texto);
    $html = '';
    foreach($linhas as $linha){
        $linha = trim($linha);
        if(!empty($linha)){
            $html .= "<p>".htmlspecialchars($linha)."</p>";
        }
    }
    return $html;
}

// Conteúdo formatado
$conteudoText1 = $sobre ? formatarTexto($sobre['text1'] ?? '') : '<p>Nenhum conteúdo cadastrado.</p>';
$conteudoText2 = $sobre ? formatarTexto($sobre['text2'] ?? '') : '<p>Nenhum conteúdo cadastrado.</p>';

// Imagens
$img1 = !empty($sobre['img1']) ? $sobre['img1'] : 'https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg';
$img2 = !empty($sobre['img2']) ? $sobre['img2'] : $img1;
$img3 = !empty($sobre['img3']) ? $sobre['img3'] : $img1;
$img4 = !empty($sobre['img4']) ? $sobre['img4'] : $img1;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sobre Nós - GEI</title>
<link rel="stylesheet" href="css/navbar.css" />
<link rel="stylesheet" href="css/sobre-nos.css" />
<style>
  .bloco { display:flex; align-items:center; gap:20px; margin:30px 0; flex-wrap:wrap; }
  .img-esquerda, .img-direita { width:300px; border-radius:10px; }
  .conteudo { max-width:600px; color:#333; }
  .conteudo h2 { font-size:24px; margin-bottom:10px; color:#823d2c; }
  .conteudo p { margin-bottom:10px; line-height:1.6; }
  .autor { font-style:italic; margin-top:10px; }
  .botao { display:inline-block; margin-top:10px; padding:10px 20px; background:#823d2c; color:#fff; text-decoration:none; border-radius:5px; transition:0.3s; }
  .botao:hover { background:#5a2b1f; }
</style>
</head>
<body>

<section class="sobre-section">
  <div class="bloco">
    <img src="<?= htmlspecialchars($img1) ?>" alt="Imagem 1" class="img-esquerda">
    <div class="conteudo">
      <h2>Sobre Nós:</h2>
      <?= $conteudoText1 ?>
      <p class="autor"><strong>Prof. Dr. Fábio Lopes</strong></p>
    </div>
    <img src="<?= htmlspecialchars($img2) ?>" alt="Imagem 2" class="img-direita">
  </div>

  <div class="bloco">
    <img src="<?= htmlspecialchars($img3) ?>" alt="Imagem 3" class="img-esquerda">
    <div class="conteudo">
      <h2>Nossa Missão:</h2>
      <?= $conteudoText2 ?>
      <a href="galeria.php" class="botao">Conheça Mais</a>
    </div>
    <img src="<?= htmlspecialchars($img4) ?>" alt="Imagem 4" class="img-direita">
  </div>
</section>

</body>
</html>
