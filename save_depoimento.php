<?php
include 'config.php';

$id = $_POST['id'] ?? null;
$nome = $_POST['name'] ?? '';
$texto = $_POST['text'] ?? '';

if($id){
    $stmt = $pdo->prepare("UPDATE depoimentos SET nome=?, texto=? WHERE id=?");
    $stmt->execute([$nome, $texto, $id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO depoimentos (nome,texto) VALUES (?,?)");
    $stmt->execute([$nome,$texto]);
    $id = $pdo->lastInsertId();
}

echo json_encode(['success'=>true,'id'=>$id]);
?>
