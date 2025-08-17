<?php
include 'config.php';

$id = $_POST['id'] ?? null;
$titulo = $_POST['title'] ?? '';
$resumo = $_POST['description'] ?? '';
$conteudo = $_POST['content'] ?? '';
$imagem = $_POST['image'] ?? '';
$autor = $_POST['author'] ?? '';
$categoria = $_POST['category'] ?? '';
$data_publicacao = $_POST['date'] ?? date('Y-m-d');
$tempo_leitura = $_POST['reading_time'] ?? '';

if($id) {
    $stmt = $pdo->prepare("UPDATE blog_posts SET titulo=?, resumo=?, conteudo=?, imagem=?, autor=?, categoria=?, data_publicacao=?, tempo_leitura=? WHERE id=?");
    $stmt->execute([$titulo, $resumo, $conteudo, $imagem, $autor, $categoria, $data_publicacao, $tempo_leitura, $id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO blog_posts (titulo,resumo,conteudo,imagem,autor,categoria,data_publicacao,tempo_leitura) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$titulo, $resumo, $conteudo, $imagem, $autor, $categoria, $data_publicacao, $tempo_leitura]);
    $id = $pdo->lastInsertId();
}

echo json_encode(['success'=>true,'id'=>$id]);
?>
