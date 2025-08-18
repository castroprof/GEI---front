<?php
session_start();
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

    header("Location: ".$_SERVER['PHP_SELF']."?section=".$section);
    exit;
}

// CRUD - Delete
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $section = $_GET['section'];
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM $section WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']."?section=".$section);
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

// Aba ativa via GET
$activeSection = $_GET['section'] ?? 'posts';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Administrativo - GEI</title>
<style>
/* RESET E TIPOGRAFIA */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI', sans-serif;}
body{background:#f4f6f8;color:#333;}

/* NAVEGAÇÃO */
nav{display:flex;gap:10px;padding:15px;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,0.1);border-radius:0 0 10px 10px;flex-wrap:wrap;}
nav button{padding:10px 16px;cursor:pointer;border-radius:8px;border:none;background:#e0e0e0;color:#333;font-weight:600;transition:0.3s;}
nav button.active{background:#823d2c;color:#fff;box-shadow:0 4px 12px rgba(130,61,44,0.3);}
nav button:hover{background:#d5d5d5;}

/* SEÇÕES */
.section{display:none;background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:20px;transition:all 0.3s;}
.section.active{display:block;}

/* TABELA */
table{width:100%;border-collapse:separate;border-spacing:0 8px;margin-top:15px;}
th, td{padding:12px 15px;text-align:left;}
th{background:#f0f0f0;color:#555;font-weight:600;border-radius:8px 8px 0 0;}
tbody tr{background:#fff;transition:0.3s;box-shadow:0 2px 6px rgba(0,0,0,0.05);}
tbody tr:hover{background:#fef9f7;}
td img{width:80px;height:60px;object-fit:cover;border-radius:6px;}

/* BOTÕES */
.btn{padding:8px 14px;cursor:pointer;border-radius:8px;font-weight:600;transition:0.3s;border:none;}
.btn-primary{background:#28a745;color:#fff;}
.btn-primary:hover{background:#218838;}
.btn-secondary{background:#ffc107;color:#333;}
.btn-secondary:hover{background:#e0a800;}
.btn-danger{background:#dc3545;color:#fff;}
.btn-danger:hover{background:#a71d2a;transform:translateY(-2px);}

/* FORMULÁRIOS */
form{margin-top:15px;background:#f9f9f9;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
form label{display:block;margin-bottom:6px;font-weight:600;color:#555;}
form input, form textarea, form select{width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-bottom:12px;transition:0.3s;}
form input:focus, form textarea:focus, form select:focus{border-color:#823d2c;outline:none;background:#fff;}
form textarea{resize:vertical;min-height:80px;}

/* Preview de imagem */
img.preview{width:150px;height:100px;object-fit:cover;border-radius:8px;margin-top:8px;display:none;}

/* Categoria nova */
#new-category-container{display:none;margin-bottom:12px;}
</style>
</head>
<body>

<nav>
  <?php foreach($secoes as $sec=>$cols): ?>
    <button data-section="<?= $sec ?>" class="<?= $sec==$activeSection?'active':'' ?>"><?= ucfirst(str_replace('_',' ',$sec)) ?></button>
  <?php endforeach; ?>
</nav>

<main>
<?php
foreach($secoes as $sec=>$cols){
    echo '<section id="'.$sec.'" class="section '.($sec==$activeSection?'active':'').'">';
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
            echo '<img src="" class="preview">';
        }
    } else {
        foreach($cols as $col){
            $label = ucfirst(str_replace('_',' ',$col));
            echo '<label>'.$label.'</label>';
            if($col == 'image' || $col == 'imagem'){
                echo '<input type="file" name="'.$col.'">';
                echo '<input type="hidden" name="'.$col.'_old">';
                echo '<img src="" class="preview">';
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
  btnAdd.addEventListener('click',()=>{
    form.style.display='block';
    form.scrollIntoView({behavior:"smooth"});
    form.reset();
    form.querySelectorAll('.preview').forEach(p=>p.style.display='none');
  });
  cancel.addEventListener('click',()=>{form.style.display='none';});

  form.querySelectorAll('input[type=file]').forEach(input=>{
    input.addEventListener('change',e=>{
      const file = e.target.files[0];
      if(file){
        const preview = input.parentElement.querySelector('.preview');
        preview.src = URL.createObjectURL(file);
        preview.style.display='block';
      }
    });
  });

  form.querySelectorAll('select[name=category]').forEach(sel=>{
    sel.addEventListener('change',()=>{
      const container = form.querySelector('#new-category-container');
      if(sel.value=='add_new'){container.style.display='block';}else{container.style.display='none';}
    });
  });
}

<?php foreach($secoes as $sec=>$cols){echo "setupForm('$sec');";} ?>

document.querySelectorAll('.btn-edit').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const section = btn.dataset.section;
    const form = document.getElementById('form-'+section);
    const id = btn.dataset.id;
    form.style.display='block';
    form.scrollIntoView({behavior:"smooth"});
    document.getElementById(section+'-id').value=id;

    const row = btn.closest('tr');
    const inputs = form.querySelectorAll('input,textarea,select');
    inputs.forEach(input=>{
      const name = input.name;
      if(row.querySelector('td')){
        let tdValue = row.querySelector('td').innerText;
        if(input.tagName=='TEXTAREA' || input.tagName=='INPUT'){
          input.value = tdValue;
        } else if(input.tagName=='SELECT'){
          Array.from(input.options).forEach(opt=>{if(opt.value==tdValue) opt.selected=true;});
        }
      }
    });
  });
});
</script>
</body>
</html>
