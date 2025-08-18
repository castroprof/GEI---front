<?php include 'head.php'; ?>

<body style="background: linear-gradient(180deg, #823d2c 0%, rgba(130,61,44,0.95) 100%);">
<?php include 'nav.php'; ?>

<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

$conn = new mysqli($host, $user, $pass);
if($conn->connect_error) die("Erro de conexão: ".$conn->connect_error);
$conn->query("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);

$conn->query("
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL DEFAULT 'Outros'
)
");

$catsRes = $conn->query("SELECT DISTINCT category FROM produtos");
$categorias = [];
while($c = $catsRes->fetch_assoc()) {
    $categorias[] = $c['category'];
}

$selectedCategory = $_GET['category'] ?? 'Todos';
$minPrice = $_GET['min'] ?? 0;
$maxPrice = $_GET['max'] ?? 10000;

$where = [];
if($selectedCategory != 'Todos') $where[] = "category = '" . $conn->real_escape_string($selectedCategory) . "'";
$where[] = "price BETWEEN ".floatval($minPrice)." AND ".floatval($maxPrice);
$whereSQL = implode(" AND ", $where);
$sql = "SELECT * FROM produtos";
if($whereSQL) $sql .= " WHERE $whereSQL";
$sql .= " ORDER BY id DESC";

$res = $conn->query($sql);
?>

<section class="loja-header">
    <h1>Loja</h1>
    <p>Explore nossos produtos com design elegante e qualidade excepcional.</p>
</section>

<div class="loja-container">

    <div class="filter-icon" onclick="toggleSidebar()">&#128269; Filtros</div>

    <aside id="sidebar">
        <h3>Categorias</h3>
        <a href="?category=Todos&min=0&max=10000" class="botao">Todos</a>
        <?php foreach($categorias as $cat): ?>
            <a href="?category=<?= urlencode($cat) ?>&min=<?= $minPrice ?>&max=<?= $maxPrice ?>" class="botao"><?= htmlspecialchars($cat) ?></a>
        <?php endforeach; ?>

        <h3>Filtrar por preço</h3>
        <div class="filtro-preco">
            <input type="range" id="preco-min" min="0" max="10000" value="<?= $minPrice ?>" step="10">
            <input type="range" id="preco-max" min="0" max="10000" value="<?= $maxPrice ?>" step="10">
            <div class="valores">
                <span id="val-min">R$ <?= $minPrice ?></span> - <span id="val-max">R$ <?= $maxPrice ?></span>
            </div>
            <button id="btnFiltrar" class="botao">Filtrar</button>
        </div>
    </aside>

    <main class="main-content">
        <div class="produtos-grid">
            <?php if($res->num_rows > 0): ?>
                <?php while($p = $res->fetch_assoc()): ?>
                    <div class="produto" onclick="abrirModal('<?= htmlspecialchars($p['image']) ?>','<?= htmlspecialchars($p['name']) ?>','<?= number_format($p['price'],2,',','.') ?>','<?= htmlspecialchars($p['description']) ?>')">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="produto-info">
                            <h3><?= htmlspecialchars($p['name']) ?></h3>
                            <span>R$ <?= number_format($p['price'],2,',','.') ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; width:100%; padding:40px;">Nenhum produto encontrado.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="fecharModal()">&times;</button>
        <img id="modal-img" src="" alt="Produto">
        <h3 id="modal-title"></h3>
        <span id="modal-price"></span>
        <p id="modal-description"></p>
        <a id="modal-whatsapp" class="modal-comprar" target="_blank">Comprar WhatsApp</a>
    </div>
</div>

<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f0eb; margin:0; padding:0; color:#333; }

.loja-header { text-align:center; padding:50px 20px 20px 20px; background: linear-gradient(135deg, #823d2c, #e4d6c3); color:#fff; }
.loja-header h1 { font-size:3em; margin-bottom:10px; }
.loja-header p { font-size:1.2em; max-width:700px; margin:0 auto; }

.loja-container { display:flex; gap:20px; padding:40px 20px; }

/* Sidebar */
aside#sidebar {
    width:220px; background:#fff; padding:20px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, opacity 0.3s ease;
}
aside h3 { margin-bottom:10px; color:#823d2c; border-bottom:1px solid #e4d6c3; padding-bottom:5px; }
aside a.botao { display:block; margin:8px 0; text-align:center; background:#823d2c; color:#fff; border-radius:8px; padding:10px 0; transition:0.3s; text-decoration:none; }
aside a.botao:hover { background:#5a2b1f; }
.filtro-preco { display:flex; flex-direction:column; gap:10px; margin-top:20px; }
.filtro-preco .valores { text-align:center; }
.filtro-preco button.botao { margin-top:10px; }

/* Main grid */
.main-content { flex:1; }
.produtos-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; }
.produto { background:#fff; border-radius:15px; overflow:hidden; cursor:pointer; box-shadow:0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
.produto img { width:100%; height:250px; object-fit:cover; display:block; transition: transform 0.5s; }
.produto:hover img { transform: scale(1.1); }
.produto-info { padding:15px; text-align:center; }
.produto-info h3 { margin:0 0 5px 0; font-size:1.2em; color:#823d2c; }
.produto-info span { font-weight:bold; color:#5a2b1f; }

/* Modal */
.modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1000; }
.modal-content { background:#fff; padding:20px; border-radius:15px; max-width:400px; width:90%; text-align:center; position:relative; box-shadow:0 4px 15px rgba(0,0,0,0.3); }
.modal-content img { width:100%; max-height:300px; object-fit:cover; border-radius:10px; margin-bottom:15px; }
.modal-close { position:absolute; top:10px; right:10px; background:#dc3545; color:#fff; border:none; border-radius:50%; width:35px; height:35px; font-size:20px; cursor:pointer; }
.modal-comprar { display:inline-block; margin-top:15px; padding:12px 20px; background:#25d366; color:#fff; border-radius:50px; font-weight:bold; text-decoration:none; transition:0.3s; }
.modal-comprar:hover { background:#1ebd5a; }

/* Filter mobile */
.filter-icon { display:none; cursor:pointer; font-size:22px; background:#823d2c; color:#fff; padding:10px 15px; border-radius:10px; text-align:center; margin-bottom:15px; }

/* Responsivo */
@media(max-width:768px){
    .loja-container { flex-direction:column; }
    aside#sidebar { width:100%; display:none; margin-bottom:15px; }
    .produtos-grid { grid-template-columns: repeat(2,1fr); }
    .filter-icon { display:block; }
}
</style>

<script>
// Modal
function abrirModal(img, title, price, desc){
    document.getElementById('modal').style.display='flex';
    document.getElementById('modal-img').src = img;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-price').textContent = 'R$ ' + price;
    document.getElementById('modal-description').textContent = desc;

    const wa = document.getElementById('modal-whatsapp');
    const numero = "SEUNUMERO";
    const msg = `Olá, quero comprar o produto: ${title} por ${price}`;
    wa.href = `https://wa.me/${numero}?text=${encodeURIComponent(msg)}`;
}
function fecharModal(){ document.getElementById('modal').style.display='none'; }

// Sliders
const sliderMin = document.getElementById('preco-min');
const sliderMax = document.getElementById('preco-max');
const valMin = document.getElementById('val-min');
const valMax = document.getElementById('val-max');
const btnFiltrar = document.getElementById('btnFiltrar');

function atualizarValores(){
    let min = parseInt(sliderMin.value);
    let max = parseInt(sliderMax.value);
    if(min>max) sliderMin.value=max;
    if(max<min) sliderMax.value=min;
    valMin.textContent = `R$ ${sliderMin.value}`;
    valMax.textContent = `R$ ${sliderMax.value}`;
}
sliderMin.addEventListener('input', atualizarValores);
sliderMax.addEventListener('input', atualizarValores);
btnFiltrar.addEventListener('click', ()=>{
    let min = sliderMin.value;
    let max = sliderMax.value;
    let categoria = "<?= $selectedCategory ?>";
    window.location.href=`?category=${encodeURIComponent(categoria)}&min=${min}&max=${max}`;
});
atualizarValores();

// Toggle sidebar mobile
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    if(window.innerWidth <=768){
        sidebar.style.display = (sidebar.style.display==='block')?'none':'block';
    }
}
</script>

<?php include 'footer.php'; ?>
