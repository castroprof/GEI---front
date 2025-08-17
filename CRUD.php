<?php
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

// Criação das tabelas
$conn->query("CREATE TABLE IF NOT EXISTS depoimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    text TEXT NOT NULL
)");
$conn->query("CREATE TABLE IF NOT EXISTS galeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
)");
$conn->query("CREATE TABLE IF NOT EXISTS hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
)");
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL
)");
$conn->query("CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL
)");
$conn->query("CREATE TABLE IF NOT EXISTS sobre_nos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text TEXT NOT NULL
)");

// Cria pasta uploads se não existir
if(!is_dir('uploads')) mkdir('uploads');

// Função de upload de imagem (mantida para outros sections)
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
    $description = $_POST['description'] ?? ''; // Novo campo
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
            // Atualiza ou cria se não existir
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
            $text = $_POST['text'] ?? '';
            if($id){
                $stmt = $conn->prepare("UPDATE sobre_nos SET text=? WHERE id=?");
                $stmt->bind_param("si",$text,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO sobre_nos (text) VALUES (?)");
                $stmt->bind_param("s",$text);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'produtos':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $image = uploadImage('image');
            if($id){
                $stmt = $conn->prepare("UPDATE produtos SET name=?, description=?, price=?, image=? WHERE id=?");
                $stmt->bind_param("ssdsi",$name,$description,$price,$image,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO produtos (name,description,price,image) VALUES (?,?,?,?)");
                $stmt->bind_param("ssds",$name,$description,$price,$image);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'posts':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = uploadImage('image');
            $link = $_POST['link'] ?? '';
            if($id){
                $stmt = $conn->prepare("UPDATE posts SET title=?, description=?, image=?, link=? WHERE id=?");
                $stmt->bind_param("ssssi",$title,$description,$image,$link,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO posts (title,description,image,link) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$title,$description,$image,$link);
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
            $cell = isset($row[$col]) ? $row[$col] : '';
            echo '<td>'.htmlspecialchars($cell, ENT_QUOTES, 'UTF-8').'</td>';
        }
        echo '<td>
                <button class="btn btn-secondary btn-edit" data-section="'.$section.'" data-id="'.$row['id'].'">Editar</button>
                <a href="?action=delete&section='.$section.'&id='.$row['id'].'" class="btn btn-danger">Excluir</a>
              </td>';
        echo '</tr>';
    }
}

// Seções do CRUD
$secoes = [
    'depoimentos'=>['name','text'],
    'galeria'=>['title','image'],
    'hero'=>['title','subtitle','image'],
    'posts'=>['title','description','image','link'],
    'produtos'=>['name','description','price','image'],
    'sobre_nos'=>['text']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Administrativo - GEI</title>
<style>
body {font-family:sans-serif;margin:0;padding:0;}
nav{background:#333;color:#fff;padding:10px;display:flex;gap:5px;flex-wrap:wrap;}
nav button{background:#555;color:#fff;border:none;padding:5px 10px;cursor:pointer;}
nav button.active{background:#f90;color:#000;}
.section{padding:20px;display:none;}
.section.active{display:block;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{border:1px solid #ccc;padding:5px;text-align:left;}
.btn{padding:5px 10px;margin:2px;cursor:pointer;}
.btn-primary{background:#28a745;color:#fff;border:none;}
.btn-secondary{background:#007bff;color:#fff;border:none;}
.btn-danger{background:#dc3545;color:#fff;border:none;}
form{margin-bottom:10px;}
form input, form textarea{display:block;width:100%;margin-bottom:5px;padding:5px;}
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
    foreach($cols as $col){
    $label = ucfirst(str_replace('_',' ',$col));
    echo '<label>'.$label.'</label>';

    if(in_array($col, ['image'])){
        echo '<input type="file" name="image" id="'.$sec.'-image">';
        echo '<input type="hidden" name="image_old" id="'.$sec.'-image_old">';
    }
    elseif($col=='description' || $col=='text'){
        echo '<textarea name="'.$col.'" id="'.$sec.'-'.$col.'" required></textarea>';
    } else {
        echo '<input type="text" name="'.$col.'" id="'.$sec.'-'.$col.'" required>';
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
// Alterna seções
document.querySelectorAll('nav button').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.section').forEach(sec=>sec.classList.remove('active'));
    document.querySelectorAll('nav button').forEach(b=>b.classList.remove('active'));
    document.getElementById(btn.dataset.section).classList.add('active');
    btn.classList.add('active');
  });
});

// Função para forms
function setupForm(section){
  const btnAdd = document.getElementById('btn-add-'+section);
  const form = document.getElementById('form-'+section);
  const cancel = form.querySelector('.cancel-btn');

  if(btnAdd){
    btnAdd.addEventListener('click',()=>{
      form.style.display='block';
      form.reset();
      form.querySelector('input[name="id"]').value='';
    });
  }

  cancel.addEventListener('click',()=> form.style.display='none');

  document.querySelectorAll('.btn-edit[data-section="'+section+'"]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const tr = btn.closest('tr');
      form.style.display='block';
      form.querySelector('input[name="id"]').value = tr.dataset.id;
      const inputs = form.querySelectorAll('input,textarea');
      tr.querySelectorAll('td').forEach((td,i)=>{
        const input = inputs[i];
        if(input){
          input.value = td.innerText;
        }
      });
    });
  });
}

Object.keys(<?php echo json_encode($secoes); ?>).forEach(s=>setupForm(s));
</script>
</body>
</html>
