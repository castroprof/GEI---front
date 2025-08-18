<?php
include 'head.php';


$host="localhost"; $user="root"; $pass=""; $db="db_gei";
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die("Erro de conexão: ".$conn->connect_error);

$res = $conn->query("SELECT * FROM hero ORDER BY id DESC LIMIT 1");
$hero = $res->fetch_assoc();

$background = htmlspecialchars($hero['image'] ?? 'default-hero.jpg'); // imagem padrão caso não tenha
$title = htmlspecialchars($hero['title'] ?? 'Entenda e Aprenda Sobre Cultura Africana');
$subtitle = htmlspecialchars($hero['subtitle'] ?? 'Descubra a riqueza da cultura africana conosco');
$link = htmlspecialchars($hero['link'] ?? '#');
?>
<section class="hero" style="background-image: url('<?= $background ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
  <div class="hero-content">
    <h1><?= $title ?></h1>
    <p><?= $subtitle ?></p>
    <a href="<?= $link ?>">Saiba Mais</a>
  </div>
</section>
