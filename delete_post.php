<?php
include 'config.php';
$id = $_POST['id'] ?? null;
if($id){
    $stmt = $pdo->prepare("DELETE FROM sobre WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'ID não informado']);
}
?>
