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
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL
)");
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
$conn->query("CREATE TABLE IF NOT EXISTS sobre_nos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text TEXT NOT NULL,
    photo VARCHAR(255)
)");

// CRUD - Save
if(isset($_POST['action']) && $_POST['action']=='save'){
    $section = $_POST['section'];
    $id = $_POST['id'] ?? null;

    if($section=='posts'){
        $title = $_POST['title'];
        $description = $_POST['description'];
        $image = $_POST['image'];
        $link = $_POST['link'];
        if($id){
            $stmt = $conn->prepare("UPDATE posts SET title=?, description=?, image=?, link=? WHERE id=?");
            $stmt->bind_param("ssssi",$title,$description,$image,$link,$id);
        } else {
            $stmt = $conn->prepare("INSERT INTO posts (title,description,image,link) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss",$title,$description,$image,$link);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    if($section=='depoimentos'){
        $name = $_POST['name'];
        $text = $_POST['text'];
        if($id){
            $stmt = $conn->prepare("UPDATE depoimentos SET name=?, text=? WHERE id=?");
            $stmt->bind_param("ssi",$name,$text,$id);
        } else {
            $stmt = $conn->prepare("INSERT INTO depoimentos (name,text) VALUES (?,?)");
            $stmt->bind_param("ss",$name,$text);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    if($section=='galeria'){
        $title = $_POST['title'];
        $image = $_POST['image'];
        if($id){
            $stmt = $conn->prepare("UPDATE galeria SET title=?, image=? WHERE id=?");
            $stmt->bind_param("ssi",$title,$image,$id);
        } else {
            $stmt = $conn->prepare("INSERT INTO galeria (title,image) VALUES (?,?)");
            $stmt->bind_param("ss",$title,$image);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    if($section=='sobre-nos'){
        $text = $_POST['text'];
        $photo = $_POST['photo'] ?? '';
        if($id){
            $stmt = $conn->prepare("UPDATE sobre_nos SET text=?, photo=? WHERE id=?");
            $stmt->bind_param("ssi",$text,$photo,$id);
        } else {
            $stmt = $conn->prepare("INSERT INTO sobre_nos (text,photo) VALUES (?,?)");
            $stmt->bind_param("ss",$text,$photo);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// CRUD - Delete
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $section = $_GET['section'];
    $id = intval($_GET['id']);
    if($section=='posts') $conn->query("DELETE FROM posts WHERE id=$id");
    if($section=='depoimentos') $conn->query("DELETE FROM depoimentos WHERE id=$id");
    if($section=='galeria') $conn->query("DELETE FROM galeria WHERE id=$id");
    if($section=='sobre-nos') $conn->query("DELETE FROM sobre_nos WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Função para renderizar tabelas
function renderTable($table, $columns, $section){
    global $conn;
    $res = $conn->query("SELECT * FROM $table ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo '<tr data-id="'.$row['id'].'">';
        foreach($columns as $col){
            if(strpos($col,'image')!==false || strpos($col,'photo')!==false){
                echo '<td><img src="'.htmlspecialchars($row[$col]).'" width="60"/></td>';
            } else {
                echo '<td>'.htmlspecialchars($row[$col]).'</td>';
            }
        }
        echo '<td>
                <button class="btn btn-secondary btn-edit" data-section="'.$section.'" data-id="'.$row['id'].'">Editar</button>
                <a href="?action=delete&section='.$section.'&id='.$row['id'].'" class="btn btn-danger">Excluir</a>
              </td>';
        echo '</tr>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Administrativo - GEI</title>
<style>
body {font-family: sans-serif;margin:0;padding:0;}
nav{background:#333;color:#fff;padding:10px;display:flex;gap:5px;}
nav button{background:#555;color:#fff;border:none;padding:5px 10px;cursor:pointer;}
nav button.active{background:#f90;color:#000;}
.section{padding:20px;}
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
  <button class="active" data-section="posts">Postagens</button>
  <button data-section="depoimentos">Depoimentos</button>
  <button data-section="galeria">Galeria</button>
  <button data-section="sobre-nos">Sobre Nós</button>
</nav>

<main>
<!-- Postagens -->
<section id="posts" class="section">
  <button id="btn-add-post" class="btn btn-primary">Adicionar Postagem</button>
  <form id="form-post" style="display:none;" method="POST">
    <input type="hidden" name="id" id="post-id">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="section" value="posts">
    <label>Título</label><input type="text" name="title" id="post-title" required>
    <label>Descrição</label><textarea name="description" id="post-description" required></textarea>
    <label>URL da Imagem</label><input type="text" name="image" id="post-image" required>
    <label>Link</label><input type="text" name="link" id="post-link" required>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <button type="button" class="btn btn-secondary cancel-btn">Cancelar</button>
  </form>
  <table>
    <thead><tr><th>Título</th><th>Descrição</th><th>Imagem</th><th>Link</th><th>Ações</th></tr></thead>
    <tbody><?php renderTable('posts',['title','description','image','link'],'posts'); ?></tbody>
  </table>
</section>

<!-- Depoimentos -->
<section id="depoimentos" class="section" style="display:none;">
  <button id="btn-add-dep" class="btn btn-primary">Adicionar Depoimento</button>
  <form id="form-dep" style="display:none;" method="POST">
    <input type="hidden" name="id" id="dep-id">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="section" value="depoimentos">
    <label>Nome</label><input type="text" name="name" id="dep-name" required>
    <label>Depoimento</label><textarea name="text" id="dep-text" required></textarea>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <button type="button" class="btn btn-secondary cancel-btn">Cancelar</button>
  </form>
  <table>
    <thead><tr><th>Nome</th><th>Depoimento</th><th>Ações</th></tr></thead>
    <tbody><?php renderTable('depoimentos',['name','text'],'depoimentos'); ?></tbody>
  </table>
</section>

<!-- Galeria -->
<section id="galeria" class="section" style="display:none;">
  <button id="btn-add-gal" class="btn btn-primary">Adicionar Imagem</button>
  <form id="form-gal" style="display:none;" method="POST">
    <input type="hidden" name="id" id="gal-id">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="section" value="galeria">
    <label>Título</label><input type="text" name="title" id="gal-title" required>
    <label>URL da Imagem</label><input type="text" name="image" id="gal-image" required>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <button type="button" class="btn btn-secondary cancel-btn">Cancelar</button>
  </form>
  <table>
    <thead><tr><th>Título</th><th>Imagem</th><th>Ações</th></tr></thead>
    <tbody><?php renderTable('galeria',['title','image'],'galeria'); ?></tbody>
  </table>
</section>

<!-- Sobre Nós -->
<section id="sobre-nos" class="section" style="display:none;">
  <button id="btn-add-sobre" class="btn btn-primary">Adicionar/Editar</button>
  <form id="form-sobre" style="display:none;" method="POST">
    <input type="hidden" name="id" id="sobre-id">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="section" value="sobre-nos">
    <label>Texto</label><textarea name="text" id="sobre-text" required></textarea>
    <label>URL da Foto</label><input type="text" name="photo" id="sobre-photo">
    <button type="submit" class="btn btn-primary">Salvar</button>
    <button type="button" class="btn btn-secondary cancel-btn">Cancelar</button>
  </form>
  <table>
    <thead><tr><th>Texto</th><th>Foto</th><th>Ações</th></tr></thead>
    <tbody><?php renderTable('sobre_nos',['text','photo'],'sobre-nos'); ?></tbody>
  </table>
</section>

<script>
// Alterna seções
document.querySelectorAll('nav button').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.section').forEach(sec=>sec.style.display='none');
    document.querySelectorAll('nav button').forEach(b=>b.classList.remove('active'));
    document.getElementById(btn.dataset.section).style.display='block';
    btn.classList.add('active');
  });
});

// Mostra/edita formulários
function setupForm(section){
  const btnAdd = document.getElementById('btn-add-'+section);
  const form = document.getElementById('form-'+section);
  const cancel = form.querySelector('.cancel-btn');

  btnAdd.addEventListener('click',()=>{
    form.style.display='block';
    form.reset();
    form.querySelector('input[name="id"]').value='';
  });

  cancel.addEventListener('click',()=> form.style.display='none');

  // Editar
  document.querySelectorAll('.btn-edit[data-section="'+section+'"]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const tr = btn.closest('tr');
      form.style.display='block';
      form.querySelector('input[name="id"]').value = tr.dataset.id;
      // Preencher campos
      tr.querySelectorAll('td').forEach((td,i)=>{
        const input = form.querySelectorAll('input,textarea')[i];
        if(input){
          if(td.querySelector('img')) input.value = td.querySelector('img').src;
          else input.value = td.innerText;
        }
      });
    });
  });
}

['post','dep','gal','sobre'].forEach(s=>setupForm(s));
</script>
</body>
</html>
