<?php
include 'head.php';
include 'nav.php';

// Conexão com o banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro de conexão: " . $conn->connect_error);

// Buscar integrantes
$sql = "SELECT * FROM integrantes ORDER BY nome ASC";
$res = $conn->query($sql);
?>

<section class="integrantes-container">
    <h1>Nosso Grupo de Pesquisadores</h1>
    <div class="integrantes-grid">
        <?php
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
                $descricao = htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8');
                $imagem = htmlspecialchars($row['imagem'], ENT_QUOTES, 'UTF-8'); // URL da imagem
                echo '
                <div class="integrante-card">
                    <div class="avatar-wrapper">
                        <img src="'.$imagem.'" alt="'.$nome.'" class="avatar">
                    </div>
                    <h3>'.$nome.'</h3>
                    <p>'.$descricao.'</p>
                </div>
                ';
            }
        } else {
            echo "<p>Nenhum integrante cadastrado.</p>";
        }
        ?>
    </div>
</section>

<style>
/* Container principal */
.integrantes-container {
    max-width: 1200px;
    margin: 100px auto 50px;
    padding: 0 20px;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.integrantes-container h1 {
    margin-bottom: 50px;
    font-size: 36px;
    font-weight: 700;
    color: #823d2c;
}

/* Grid de integrantes */
.integrantes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 40px;
}

/* Card individual */
.integrante-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    padding: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.integrante-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

/* Avatar circular */
.avatar-wrapper {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    overflow: hidden;
    border-radius: 50%;
    border: 4px solid #823d2c;
}

.avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Nome e descrição */
.integrante-card h3 {
    margin: 10px 0 8px;
    font-size: 20px;
    color: #823d2c;
    font-weight: 600;
}

.integrante-card p {
    font-size: 14px;
    color: #555;
    line-height: 1.5;
    padding: 0 5px;
}

/* Responsividade */
@media (max-width: 768px) {
    .avatar-wrapper {
        width: 100px;
        height: 100px;
    }
    .integrante-card h3 {
        font-size: 18px;
    }
    .integrante-card p {
        font-size: 13px;
    }
}
</style>

<?php include 'footer.php'; ?>
