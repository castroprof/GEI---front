<?php
session_start();

// Ativa exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_gei";

// Conexão
$conn = new mysqli($host,$user,$pass,$db);
if ($conn->connect_error) {
    $tmpConn = new mysqli($host, $user, $pass);
    if ($tmpConn->connect_error) die("Erro: ".$tmpConn->connect_error);
    $tmpConn->query("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $tmpConn->close();
    $conn = new mysqli($host,$user,$pass,$db);
    if($conn->connect_error) die("Erro de conexão após criar DB: ".$conn->connect_error);
}

// Criação das tabelas (se não existirem)
$conn->query("CREATE TABLE IF NOT EXISTS depoimentos (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, text TEXT NOT NULL)");
$conn->query("CREATE TABLE IF NOT EXISTS galeria (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT, image VARCHAR(255) NOT NULL)");
$conn->query("CREATE TABLE IF NOT EXISTS hero (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL)");
$conn->query("CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, image VARCHAR(255) NOT NULL, likes INT NOT NULL DEFAULT 0, views INT NOT NULL DEFAULT 0)");
$conn->query("CREATE TABLE IF NOT EXISTS produtos (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DECIMAL(10,2) NOT NULL, image VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL DEFAULT 'Outros')");
$conn->query("CREATE TABLE IF NOT EXISTS sobre_nos (id INT AUTO_INCREMENT PRIMARY KEY, text1 TEXT NOT NULL, text2 TEXT NOT NULL, img1 VARCHAR(255) NOT NULL, img2 VARCHAR(255) NOT NULL, img3 VARCHAR(255) NOT NULL, img4 VARCHAR(255) NOT NULL)");
$conn->query("CREATE TABLE IF NOT EXISTS integrantes (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL, descricao TEXT NOT NULL, imagem VARCHAR(255) NOT NULL)");
$conn->query("CREATE TABLE IF NOT EXISTS portfolio (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, image VARCHAR(255) NOT NULL)");

// Cria pasta uploads se não existir
if(!is_dir('uploads')) mkdir('uploads');

// Função de upload de imagem
function uploadImage($field){
    if(isset($_FILES[$field]) && $_FILES[$field]['error']==0){
        $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        $filename = uniqid().'.'.$ext;
        move_uploaded_file($_FILES[$field]['tmp_name'], 'uploads/'.$filename);
        return 'uploads/'.$filename;
    }
    return $_POST[$field.'_old'] ?? '';
}

// CRUD - Save
if(isset($_POST['action']) && $_POST['action']=='save'){
    $section = $_POST['section'];
    $id = $_POST['id'] ?? null;

    switch($section){
        case 'depoimentos':
            $name = $_POST['name'] ?? '';
            $text = $_POST['text'] ?? '';
            if($id){
                $stmt = $conn->prepare("UPDATE depoimentos SET name=?, text=? WHERE id=?");
                $stmt->bind_param("ssi",$name,$text,$id);
                
            } else {
                $stmt = $conn->prepare("INSERT INTO depoimentos (name,text) VALUES (?,?)");
                $stmt->bind_param("ss",$name,$text);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'galeria':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = uploadImage('image');
            if($id){
                $stmt = $conn->prepare("UPDATE galeria SET title=?, description=?, image=? WHERE id=?");
                $stmt->bind_param("sssi",$title,$description,$image,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO galeria (title, description, image) VALUES (?,?,?)");
                $stmt->bind_param("sss",$title,$description,$image);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'hero':
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $image = uploadImage('image');
            $resHero = $conn->query("SELECT id FROM hero LIMIT 1");
            if($resHero->num_rows > 0){
                $rowHero = $resHero->fetch_assoc();
                $idHero = $rowHero['id'];
                $stmt = $conn->prepare("UPDATE hero SET title=?, subtitle=?, image=? WHERE id=?");
                $stmt->bind_param("sssi",$title,$subtitle,$image,$idHero);
            } else {
                $stmt = $conn->prepare("INSERT INTO hero (title,subtitle,image) VALUES (?,?,?)");
                $stmt->bind_param("sss",$title,$subtitle,$image);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'sobre_nos':
            $text1 = $_POST['text1'] ?? '';
            $text2 = $_POST['text2'] ?? '';
            $img1 = uploadImage('img1');
            $img2 = uploadImage('img2');
            $img3 = uploadImage('img3');
            $img4 = uploadImage('img4');
            $resSobre = $conn->query("SELECT id FROM sobre_nos LIMIT 1");
            if($resSobre->num_rows > 0){
                $row = $resSobre->fetch_assoc();
                $idSobre = $row['id'];
                $stmt = $conn->prepare("UPDATE sobre_nos SET text1=?, text2=?, img1=?, img2=?, img3=?, img4=? WHERE id=?");
                $stmt->bind_param("ssssssi", $text1, $text2, $img1, $img2, $img3, $img4, $idSobre);
            } else {
                $stmt = $conn->prepare("INSERT INTO sobre_nos (text1, text2, img1, img2, img3, img4) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("ssssss", $text1, $text2, $img1, $img2, $img3, $img4);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'produtos':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $category = $_POST['category'] ?? 'Outros';
            $new_category = $_POST['new_category'] ?? '';
            if(!empty($new_category)) $category = $new_category;
            $image = uploadImage('image');
            if($id){
                $stmt = $conn->prepare("UPDATE produtos SET name=?, description=?, price=?, image=?, category=? WHERE id=?");
                $stmt->bind_param("ssdssi", $name, $description, $price, $image, $category, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO produtos (name, description, price, image, category) VALUES (?,?,?,?,?)");
                $stmt->bind_param("ssdss", $name, $description, $price, $image, $category);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'posts':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = uploadImage('image');
            $likes = $_POST['likes'] ?? 0;
            $views = $_POST['views'] ?? 0;
            if($id){
                $stmt = $conn->prepare("UPDATE posts SET title=?, description=?, image=?, likes=?, views=? WHERE id=?");
                $stmt->bind_param("sssiii",$title,$description,$image,$likes,$views,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO posts (title, description, image, likes, views) VALUES (?,?,?,?,?)");
                $stmt->bind_param("sssii",$title,$description,$image,$likes,$views);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'integrantes':
            $nome = $_POST['nome'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $imagem = uploadImage('imagem');
            if($id){
                $stmt = $conn->prepare("UPDATE integrantes SET nome=?, descricao=?, imagem=? WHERE id=?");
                $stmt->bind_param("sssi",$nome,$descricao,$imagem,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO integrantes (nome, descricao, imagem) VALUES (?,?,?)");
                $stmt->bind_param("sss",$nome,$descricao,$imagem);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'portfolio':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = uploadImage('image');
            if($id){
                $stmt = $conn->prepare("UPDATE portfolio SET title=?, description=?, image=? WHERE id=?");
                $stmt->bind_param("sssi",$title,$description,$image,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO portfolio (title, description, image) VALUES (?,?,?)");
                $stmt->bind_param("sss",$title,$description,$image);
            }
            $stmt->execute();
            $stmt->close();
            break;
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// CRUD - Delete
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $section = $_GET['section'];
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM $section WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Renderização das tabelas
function renderTable($table, $columns, $section){
    global $conn;
    $res = $conn->query("SELECT * FROM $table ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo '<tr data-id="'.$row['id'].'">';
        foreach($columns as $col){
            $cell = $row[$col] ?? '';
            if(($col == 'image' || $col == 'imagem' || strpos($col,'img')===0) && !empty($cell)){
                echo '<td><img src="'.htmlspecialchars($cell).'" style="width:80px;height:60px;object-fit:cover;border-radius:6px;"></td>';
            } else {
                echo '<td>'.htmlspecialchars($cell, ENT_QUOTES, 'UTF-8').'</td>';
            }
        }
        echo '<td>
                <button class="btn btn-secondary btn-edit" data-section="'.$section.'" data-id="'.$row['id'].'">Editar</button>
                <a href="?action=delete&section='.$section.'&id='.$row['id'].'" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir este item?\')">Excluir</a>
              </td>';
        echo '</tr>';
    }
}

// Seções do CRUD
$secoes = [
    'depoimentos'=>['name','text'],
    'galeria'=>['title','description','image'],
    'hero'=>['title','subtitle','image'],
    'posts'=>['title','description','image','likes','views'],
    'produtos'=>['name','description','price','image','category'],
    'sobre_nos'=>['text1','text2','img1','img2','img3','img4'],
    'integrantes'=>['nome','descricao','imagem'],
    'portfolio'=>['title','description','image'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Administrativo - GEI</title>
<style>
body {font-family:sans-serif;margin:0;padding:0;background:#f9f9f9;}
nav{background:#823d2c;color:#fff;padding:10px;display:flex;gap:5px;flex-wrap:wrap;}
nav button{background:#b55c44;color:#fff;border:none;padding:8px 12px;cursor:pointer;border-radius:5px;transition: background 0.3s;}
nav button.active{background:#fff;color:#823d2c;font-weight:bold;}
.section{padding:20px;display:none;background:#fff;margin:10px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.section.active{display:block;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{border:1px solid #ccc;padding:8px;text-align:left;}
th{background:#f5f5f5;}
.btn{padding:6px 12px;margin:2px;cursor:pointer;border-radius:5px;transition:0.3s;}
.btn-primary{background:#823d2c;color:#fff;border:none;}
.btn-primary:hover{background:#5a2b1f;}
.btn-secondary{background:#555;color:#fff;border:none;}
.btn-secondary:hover{background:#333;}
.btn-danger{background:#dc3545;color:#fff;border:none;}
.btn-danger:hover{background:#a71d2a;}
form{margin-bottom:15px;}
form input, form textarea, form select{display:block;width:100%;margin-bottom:8px;padding:8px;border-radius:5px;border:1px solid #ccc;box-sizing:border-box;}
form textarea{resize:none;}
img.preview{width:150px;height:100px;object-fit:cover;border-radius:6px;margin-bottom:8px;}
#new-category-container{display:none;}
</style>
</head>
<body>

<nav>
  <?php foreach($secoes as $sec=>$cols): ?>
    <button data-section="<?= $sec ?>"><?= ucfirst(str_replace('_',' ',$sec)) ?></button>
  <?php endforeach; ?>
</nav>

<main>
<?php
foreach($secoes as $sec=>$cols){
    echo '<section id="'.$sec.'" class="section '.($sec=='posts'?'active':'').'">';
    echo '<button id="btn-add-'.$sec.'" class="btn btn-primary">Adicionar</button>';
    echo '<form id="form-'.$sec.'" style="display:none;" method="POST" enctype="multipart/form-data">';
    echo '<input type="hidden" name="id" id="'.$sec.'-id">';
    echo '<input type="hidden" name="action" value="save">';
    echo '<input type="hidden" name="section" value="'.$sec.'">';

    if($sec == 'sobre_nos'){
        echo '<label>Texto 1</label><textarea name="text1" required></textarea>';
        echo '<label>Texto 2</label><textarea name="text2" required></textarea>';
        for($i=1;$i<=4;$i++){
            echo '<label>Imagem '.$i.'</label>';
            echo '<input type="file" name="img'.$i.'">';
            echo '<input type="hidden" name="img'.$i.'_old">';
            echo '<img src="" class="preview" style="display:none;">';
        }
    } else {
        foreach($cols as $col){
            $label = ucfirst(str_replace('_',' ',$col));
            echo '<label>'.$label.'</label>';
            if($col == 'image' || $col == 'imagem'){
                echo '<input type="file" name="'.$col.'">';
                echo '<input type="hidden" name="'.$col.'_old">';
                echo '<img src="" class="preview" style="display:none;">';
            } elseif($col=='description' || $col=='text' || $col=='descricao'){
                echo '<textarea name="'.$col.'" required></textarea>';
            } elseif($col=='category'){
                $resCat = $conn->query("SELECT DISTINCT category FROM produtos");
                echo '<select name="category">';
                echo '<option value="">-- Selecione a categoria --</option>';
                while($cat = $resCat->fetch_assoc()){
                    echo '<option value="'.$cat['category'].'">'.$cat['category'].'</option>';
                }
                echo '<option value="add_new">Adicionar nova categoria</option>';
                echo '</select>';
                echo '<div id="new-category-container"><input type="text" name="new_category" placeholder="Digite nova categoria"></div>';
            } else {
                echo '<input type="text" name="'.$col.'" required>';
            }
        }
    }

    echo '<button type="submit" class="btn btn-primary">Salvar</button>';
    echo '<button type="button" class="btn btn-secondary cancel-btn">Cancelar</button>';
    echo '</form>';

    echo '<table><thead><tr>';
    foreach($cols as $col) echo '<th>'.ucfirst(str_replace('_',' ',$col)).'</th>';
    echo '<th>Ações</th></tr></thead><tbody>';
    renderTable($sec,$cols,$sec);
    echo '</tbody></table>';
    echo '</section>';
}
?>
<script>
document.querySelectorAll('nav button').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.section').forEach(sec=>sec.classList.remove('active'));
    document.querySelectorAll('nav button').forEach(b=>b.classList.remove('active'));
    document.getElementById(btn.dataset.section).classList.add('active');
    btn.classList.add('active');
  });
});

function setupForm(section){
  const btnAdd = document.getElementById('btn-add-'+section);
  const form = document.getElementById('form-'+section);
  const cancel = form.querySelector('.cancel-btn');

  if(btnAdd){
    btnAdd.addEventListener('click',()=>{
      form.style.display='block';
      form.reset();
      form.querySelector('input[name="id"]').value='';
      form.querySelectorAll('img.preview').forEach(img=>img.style.display='none');
      form.querySelectorAll('input[type="hidden"][name$="_old"]').forEach(h=>h.value='');
      const newCatCont = form.querySelector('#new-category-container');
      if(newCatCont) newCatCont.style.display='none';
    });
  }

  cancel.addEventListener('click',()=> form.style.display='none');
}

<?php foreach(array_keys($secoes) as $s): ?>
setupForm('<?= $s ?>');
<?php endforeach; ?>

// Mostrar input nova categoria
document.querySelectorAll('select[name="category"]').forEach(sel=>{
    sel.addEventListener('change',()=>{
        const container = sel.closest('form').querySelector('#new-category-container');
        if(sel.value === 'add_new') container.style.display='block';
        else container.style.display='none';
    });
});

// Editar registros
document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const section = btn.dataset.section;
        const tr = btn.closest('tr');
        const form = document.getElementById('form-'+section);
        form.style.display = 'block';
        form.querySelector('input[name="id"]').value = tr.dataset.id;

        const fields = Array.from(form.querySelectorAll('input:not([type=file]):not([type=hidden]), textarea, select'));
const tds = tr.querySelectorAll('td');
let fieldIndex = 0;

for (let i = 0; i < tds.length - 1; i++) { // ignora o último (Ações)
    const td = tds[i];
    const input = fields[fieldIndex];
    const fieldName = input?.name;

    if (!input || !fieldName) continue;

    if (input.tagName === 'TEXTAREA') {
        input.value = td.textContent.trim();
    } else if (input.tagName === 'SELECT') {
        const val = td.textContent.trim();
        let found = false;
        for (let opt of input.options) {
            if (opt.value === val) {
                opt.selected = true;
                found = true;
                break;
            }
        }
        if (!found && input.name === 'category') {
            const newCatInput = form.querySelector('input[name="new_category"]');
            if (newCatInput) {
                newCatInput.value = val;
                const container = form.querySelector('#new-category-container');
                if (container) container.style.display = 'block';
            }
        }
    } else if (input.type === 'text' || input.type === 'number') {
        input.value = td.textContent.trim();
    }

    // Se for imagem, atualiza a prévia e o campo _old
    if (td.querySelector('img')) {
        const imgSrc = td.querySelector('img').src;
        const preview = form.querySelectorAll('img.preview')[fieldIndex];
        const hiddenOld = form.querySelector(`input[name="${fieldName}_old"]`);
        if (preview && hiddenOld) {
            preview.src = imgSrc;
            preview.style.display = 'block';
            hiddenOld.value = imgSrc;
        }
    }

    fieldIndex++;
}

    });
});
</script>
</body>
</html>
