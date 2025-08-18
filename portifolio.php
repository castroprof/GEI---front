<?php include 'head.php'; ?>

<body>
<?php include 'nav.php'; ?>

<link rel="stylesheet" href="css/portfolio.css">

<?php
// Conexão com o banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) die("Erro de conexão: ".$conn->connect_error);

// Cria tabela se não existir
$conn->query("
CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
)");

// Busca os projetos
$sql = "SELECT * FROM portfolio ORDER BY id DESC";
$res = $conn->query($sql);
?>

<section class="portfolio-header">
    <h1>Meu Portfólio</h1>
    <p>Explorando arte, cultura e design com inspiração africana.</p>
</section>

<section class="portfolio-grid">
    <?php if($res->num_rows > 0): ?>
        <?php while($proj = $res->fetch_assoc()): ?>
            <div class="portfolio-item">
                <img src="<?= htmlspecialchars($proj['image']) ?>" alt="<?= htmlspecialchars($proj['title']) ?>">
                <div class="portfolio-overlay">
                    <h3><?= htmlspecialchars($proj['title']) ?></h3>
                    <p><?= htmlspecialchars($proj['description']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; width:100%; padding:40px;">Nenhum projeto encontrado no portfólio.</p>
    <?php endif; ?>
</section>

<style>
body { background:#f5f0eb; color:#333; font-family: 'Segoe UI', sans-serif; margin:0; padding:0; }

.portfolio-header {
    text-align:center;
    padding:50px 20px 20px 20px;
    background: linear-gradient(135deg, #823d2c, #e4d6c3);
    color:#fff;
}
.portfolio-header h1 { font-size:3em; margin-bottom:10px; }
.portfolio-header p { font-size:1.2em; max-width:700px; margin:0 auto; }

.portfolio-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
    padding:40px 20px;
}
.portfolio-item {
    position:relative;
    overflow:hidden;
    border-radius:15px;
    cursor:pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.portfolio-item img {
    width:100%;
    height:300px;
    object-fit:cover;
    transition: transform 0.5s ease;
    display:block;
}
.portfolio-item:hover img { transform: scale(1.1); }

.portfolio-overlay {
    position:absolute;
    bottom:0;
    left:0;
    width:100%;
    padding:20px;
    background: rgba(130, 61, 44, 0.85);
    color:#fff;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}
.portfolio-item:hover .portfolio-overlay { transform: translateY(0); }
.portfolio-overlay h3 { margin:0 0 10px 0; font-size:1.2em; }
.portfolio-overlay p { margin:0; font-size:0.95em; line-height:1.3em; }

/* Responsivo */
@media(max-width:768px){
    .portfolio-header h1 { font-size:2em; }
    .portfolio-header p { font-size:1em; }
    .portfolio-item img { height:200px; }
}
</style>

<?php include 'footer.php'; ?>
