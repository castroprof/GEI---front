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
            <div class="portfolio-item" onclick="abrirModal('<?= htmlspecialchars($proj['image']) ?>','<?= htmlspecialchars($proj['title']) ?>','<?= htmlspecialchars($proj['description']) ?>')">
                <img src="<?= htmlspecialchars($proj['image']) ?>" alt="<?= htmlspecialchars($proj['title']) ?>">
                <div class="portfolio-overlay">
                    <h3><?= htmlspecialchars($proj['title']) ?></h3>
                    <p><?= substr(htmlspecialchars($proj['description']),0,60) ?>...</p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; width:100%; padding:40px;">Nenhum projeto encontrado no portfólio.</p>
    <?php endif; ?>
</section>

<!-- Modal Pop-up -->
<div class="modal-overlay" id="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="fecharModal()">&times;</button>
        <div class="modal-img-container">
            <img id="modal-img" src="" alt="Projeto">
        </div>
        <h3 id="modal-title"></h3>
        <p id="modal-description"></p>
    </div>
</div>

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
    height:250px;
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
    padding:15px;
    background: rgba(130, 61, 44, 0.85);
    color:#fff;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}
.portfolio-item:hover .portfolio-overlay { transform: translateY(0); }
.portfolio-overlay h3 { margin:0 0 10px 0; font-size:1.2em; }
.portfolio-overlay p { margin:0; font-size:0.95em; line-height:1.3em; }

/* Modal */
.modal-overlay {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:#823d2c;
    justify-content:center;
    align-items:center;
    z-index:1000;
    padding:20px;
}
.modal-content {
    position:relative;
    max-width:90%;
    width:800px;
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    text-align:center;
    padding:20px;
}
.modal-close {
    position:absolute;
    top:10px;
    right:10px;
    background:#dc3545;
    color:#fff;
    border:none;
    border-radius:50%;
    width:35px;
    height:35px;
    font-size:20px;
    cursor:pointer;
    z-index:10;
}
.modal-img-container {
    width:100%;
    max-height:50vh;
    overflow:hidden;
    cursor:zoom-in;
}
.modal-img-container img {
    width:100%;
    height:auto;
    max-height:50vh;
    object-fit:contain;
    transition: transform 0.3s ease;
}
.modal-content h3 { margin-top:15px; color:#823d2c; }
.modal-content p { margin-top:10px; font-size:1em; line-height:1.5em; color:#333; }

/* Responsivo */
@media(max-width:768px){
    .portfolio-header h1 { font-size:2em; }
    .portfolio-header p { font-size:1em; }
    .portfolio-item img { height:200px; }
    .modal-img-container { max-height:40vh; }
}
@media(max-width:480px){
    .portfolio-grid { grid-template-columns:1fr; }
    .modal-img-container { max-height:35vh; }
}
</style>

<script>
// Modal e Zoom
function abrirModal(img, title, desc){
    const modal = document.getElementById('modal');
    const modalImgContainer = document.querySelector('.modal-img-container');
    const modalImg = document.getElementById('modal-img');
    modal.style.display='flex';
    modalImg.src = img;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-description').textContent = desc;

    // Reset zoom
    modalImg.style.transform = 'scale(1)';
    let isZoomed = false;

    modalImgContainer.style.cursor = 'zoom-in';
    modalImgContainer.onmousemove = function(e){
        if(isZoomed){
            const rect = modalImgContainer.getBoundingClientRect();
            const x = ((e.clientX - rect.left)/rect.width)*100;
            const y = ((e.clientY - rect.top)/rect.height)*100;
            modalImg.style.transformOrigin = `${x}% ${y}%`;
        }
    };
    modalImgContainer.onclick = function(){
        isZoomed = !isZoomed;
        modalImg.style.transform = isZoomed ? 'scale(2)' : 'scale(1)';
        modalImgContainer.style.cursor = isZoomed ? 'zoom-out' : 'zoom-in';
    };
}

function fecharModal(){
    document.getElementById('modal').style.display='none';
    const modalImg = document.getElementById('modal-img');
    modalImg.style.transform = 'scale(1)';
}
</script>

<?php include 'footer.php'; ?>
