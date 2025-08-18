<?php include 'head.php'; ?>
<body style="background:#f5f0eb;">
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

$conn->query("CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL DEFAULT 'Outros'
)");

$catsRes = $conn->query("SELECT DISTINCT category FROM produtos");
$categorias = [];
while($c = $catsRes->fetch_assoc()) $categorias[] = $c['category'];

$selectedCategory = $_GET['category'] ?? 'Todos';
$minPrice = $_GET['min'] !== '' ? floatval($_GET['min']) : 0;
$maxPrice = $_GET['max'] !== '' ? floatval($_GET['max']) : 1000000;

$where = [];
if($selectedCategory != 'Todos') $where[] = "category = '".$conn->real_escape_string($selectedCategory)."'";
$where[] = "price BETWEEN $minPrice AND $maxPrice";
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

<div class="filter-mobile-btn" onclick="toggleSidebarMobile()">Filtros</div>

<div class="loja-container">
    <aside id="sidebar">
        <button class="sidebar-close" onclick="toggleSidebarMobile()">×</button>
        <h3>Categorias</h3>
        <a href="?category=Todos&min=0&max=1000000" class="botao <?= $selectedCategory=='Todos'?'active':'' ?>">Todos</a>
        <?php foreach($categorias as $cat): ?>
            <a href="?category=<?= urlencode($cat) ?>&min=<?= $minPrice ?>&max=<?= $maxPrice ?>" class="botao <?= $selectedCategory==$cat?'active':'' ?>"><?= htmlspecialchars($cat) ?></a>
        <?php endforeach; ?>

        <h3>Filtrar por preço</h3>
        <div class="filtro-preco">
            <input type="range" id="preco-min" min="0" max="1000000" value="<?= $minPrice ?>" step="10">
            <input type="range" id="preco-max" min="0" max="1000000" value="<?= $maxPrice ?>" step="10">
            <div class="valores">
                <span id="val-min">R$ <?= $minPrice ?></span> - <span id="val-max">R$ <?= $maxPrice ?></span>
            </div>
            <button id="btnFiltrar" class="botao">Filtrar</button>
        </div>
    </aside>

    <main class="main-content">
        <?php if($res->num_rows > 0): ?>
        <div class="produtos-grid">
            <?php while($p = $res->fetch_assoc()): ?>
                <div class="produto" onclick="abrirModal('<?= htmlspecialchars($p['image']) ?>','<?= htmlspecialchars($p['name']) ?>','<?= number_format($p['price'],2,',','.') ?>','<?= htmlspecialchars($p['description']) ?>')">
                    <div class="produto-img">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </div>
                    <div class="produto-info">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <span>R$ <?= number_format($p['price'],2,',','.') ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <p class="no-produtos">Nenhum produto encontrado.</p>
        <?php endif; ?>
    </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="fecharModal()">×</button>
        <div class="modal-img-container">
            <img id="modal-img" src="" alt="Produto">
        </div>
        <h3 id="modal-title"></h3>
        <span id="modal-price"></span>
        <p id="modal-description"></p>
        <a id="modal-whatsapp" class="modal-comprar" target="_blank">Comprar WhatsApp</a>
    </div>
</div>

<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f0eb; margin:0; color:#333; }
.loja-header { text-align:center; padding:60px 20px; background: linear-gradient(135deg, #823d2c, #e4d6c3); color:#fff; border-bottom-left-radius:30px; border-bottom-right-radius:30px; }
.loja-header h1 { font-size:2.5em; margin-bottom:5px; }
.loja-header p { font-size:1em; max-width:700px; margin:0 auto; }
.loja-container { display:flex; gap:20px; padding:40px 20px; }

aside#sidebar { width:220px; background:#fff; padding:20px; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.1); transition:0.3s; font-size:0.9em; }
.sidebar-close { display:none; background:none; border:none; font-size:25px; position:absolute; top:10px; right:10px; cursor:pointer; }
aside h3 { margin-bottom:10px; color:#823d2c; border-bottom:1px solid #e4d6c3; padding-bottom:5px; font-size:1em; }
aside a.botao { display:block; margin:6px 0; text-align:center; background:#823d2c; color:#fff; border-radius:8px; padding:8px 0; text-decoration:none; transition:0.3s; font-size:0.9em; }
aside a.botao.active, aside a.botao:hover { background:#5a2b1f; }
.filtro-preco { display:flex; flex-direction:column; gap:8px; margin-top:15px; }
.filtro-preco .valores { text-align:center; font-weight:bold; font-size:0.9em; }
.filtro-preco button.botao { margin-top:10px; }

.main-content { flex:1; }
.produtos-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:20px; }
.produto { background:#fff; border-radius:15px; overflow:hidden; cursor:pointer; box-shadow:0 6px 20px rgba(0,0,0,0.1); transition:0.3s; text-align:center; display:flex; flex-direction:column; }
.produto:hover { transform: translateY(-5px); box-shadow:0 8px 25px rgba(0,0,0,0.2); }
.produto-img { flex:1; display:flex; align-items:center; justify-content:center; padding:10px; min-height:150px; max-height:300px; }
.produto-img img { max-width:100%; max-height:100%; object-fit:contain; }
.produto-info { padding:10px; }
.produto-info h3 { margin:5px 0; font-size:1em; color:#823d2c; }
.produto-info span { font-weight:bold; color:#5a2b1f; }

.no-produtos { text-align:center; padding:40px; font-size:1.2em; }

/* Modal */
.modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(245,240,235,0.95); justify-content:center; align-items:center; z-index:1000; padding:20px; overflow:auto; }
.modal-content { background:#fff; padding:20px; border-radius:15px; max-width:90%; width:100%; text-align:center; position:relative; box-shadow:0 6px 20px rgba(0,0,0,0.3); }
.modal-img-container { max-width:50vw; max-height:50vh; margin:0 auto; overflow:hidden; position:relative; cursor:zoom-in; }
.modal-img-container img { width:100%; height:100%; object-fit:contain; transition: transform 0.3s ease; }

.modal-close { position:absolute; top:10px; right:10px; background:#dc3545; color:#fff; border:none; border-radius:50%; width:40px; height:40px; font-size:22px; cursor:pointer; z-index:1001; }

.modal-comprar { display:inline-block; margin-top:15px; padding:12px 20px; background:#25d366; color:#fff; border-radius:50px; font-weight:bold; text-decoration:none; transition:0.3s; }
.modal-comprar:hover { background:#1ebd5a; }

.filter-mobile-btn {
    display:none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background:#823d2c;
    color:#fff;
    padding:12px 18px;
    border-radius:50px;
    cursor:pointer;
    font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
    z-index:1001;
    transition:0.3s;
}
.filter-mobile-btn:hover { background:#5a2b1f; }

@media(max-width:768px){
    .loja-container { flex-direction:column; }
    aside#sidebar { 
        position:fixed; top:0; right:-100%; height:100%; width:250px; z-index:1000; 
        border-radius:0 0 0 15px;
        transition: right 0.3s ease; font-size:0.9em;
    }
    aside#sidebar.active { right:0; }
    .sidebar-close { display:block; }
    .produtos-grid { grid-template-columns: repeat(2,1fr); }
    .filter-mobile-btn { display:block; }
}
@media(max-width:480px){
    .produtos-grid { grid-template-columns: 1fr; }
    .modal-img-container { max-width:90vw; max-height:50vh; }
}
</style>

<script>
// Modal
function abrirModal(img, title, price, desc){
    const modal = document.getElementById('modal');
    const modalImgContainer = document.querySelector('.modal-img-container');
    const modalImg = document.getElementById('modal-img');
    modal.style.display='flex';
    modalImg.src = img;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-price').textContent = 'R$ ' + price;
    document.getElementById('modal-description').textContent = desc;

    const wa = document.getElementById('modal-whatsapp');
    const numero = "SEUNUMERO";
    const msg = `Olá, quero comprar o produto: ${title} por ${price}`;
    wa.href = `https://wa.me/${numero}?text=${encodeURIComponent(msg)}`;

    // Zoom funcional
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
btnFiltrar.addEventListener('click', ()=> {
    let min = sliderMin.value;
    let max = sliderMax.value;
    let categoria = "<?= $selectedCategory ?>";
    window.location.href=`?category=${encodeURIComponent(categoria)}&min=${min}&max=${max}`;
});
atualizarValores();

// Toggle sidebar mobile
function toggleSidebarMobile(){
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
</script>

<?php include 'footer.php'; ?>
