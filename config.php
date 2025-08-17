<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_gei'; // Nome do seu banco de dados

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
