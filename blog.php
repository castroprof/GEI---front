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

// Inserir comentário
if (isset($_POST['action']) && $_POST['action'] == 'comment') {
    $post_id = intval($_POST['post_id']);
    $name = $_POST['name'] ?? 'Anônimo';
    $comment = $_POST['comment'] ?? '';
    $created_at = date('Y-m-d H:i:s');
    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random";
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comentarios (post_id, name, comment, avatar, created_at) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $post_id, $name, $comment, $avatar, $created_at);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Contar visualização
if (isset($_GET['view'])) {
    $post_id = intval($_GET['view']);
    $conn->query("UPDATE posts SET views = views + 1 WHERE id = $post_id");
}

// Processar like via Ajax
if (isset($_POST['like']) && isset($_POST['post_id']) && isset($_POST['ajax'])) {
    $post_id = intval($_POST['post_id']);
    $conn->query("UPDATE posts SET likes = likes + 1 WHERE id = $post_id");
    $likes = $conn->query("SELECT likes FROM posts WHERE id = $post_id")->fetch_assoc()['likes'];
    echo $likes;
    exit;
}

// Busca
$search = $_GET['search'] ?? '';
$search_sql = $conn->real_escape_string($search);
$sql = "SELECT * FROM posts WHERE title LIKE '%$search_sql%' ORDER BY created_at DESC";
$res = $conn->query($sql);
?>

<!-- BARRA DE PESQUISA -->
<form class="search-bar" method="GET">
  <input type="search" name="search" placeholder="Buscar no blog..." value="<?= htmlspecialchars($search, ENT_QUOTES) ?>" />
  <button type="submit">
    <link rel="stylesheet" href="css/blog.css" />
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
      <circle cx="11" cy="11" r="8"></circle>
      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
  </button>
</form>

<!-- POSTS -->
<section class="posts-container">
<?php
if ($res->num_rows > 0) {
    while ($post = $res->fetch_assoc()) {
        $post_id = $post['id'];
        $wordCount = str_word_count(strip_tags($post['description']));
        $readTime = ceil($wordCount / 200);
        $commentsRes = $conn->query("SELECT * FROM comentarios WHERE post_id = $post_id ORDER BY created_at DESC");
        $commentsCount = $commentsRes->num_rows;
?>
<article class="post" data-id="<?= $post_id ?>">
    <div class="post-content">
        <h3><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars(substr($post['description'], 0, 150), ENT_QUOTES, 'UTF-8') ?>...</p>
        <div class="post-meta">
            Publicado em <?= date("d M", strtotime($post['created_at'])) ?> • <?= $readTime ?> min
        </div>
        <div class="post-stats">
            <button class="like-btn" data-id="<?= $post_id ?>">
              <svg xmlns="http://www.w3.org/2000/svg" class="like-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#823d2c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <span class="like-count"><?= $post['likes'] ?></span>
            </button>
            <span>💬 <?= $commentsCount ?></span>
            <span>👁 <?= $post['views'] ?></span>
        </div>
        <button class="botao open-modal" data-id="<?= $post_id ?>">Ler mais</button>
    </div>
    <img class="post-image" src="<?= htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>">
</article>

<!-- MODAL -->
<div class="modal" id="modal-<?= $post_id ?>" style="display:none;">
    <div class="modal-content">
        <span class="close" data-id="<?= $post_id ?>">&times;</span>
        <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= nl2br(htmlspecialchars($post['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        <div class="post-meta">
            Publicado em <?= date("d M Y", strtotime($post['created_at'])) ?> • <?= $readTime ?> min
        </div>

        <button class="like-btn" data-id="<?= $post_id ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="like-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#823d2c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
          <span class="like-count"><?= $post['likes'] ?></span>
        </button>

        <div class="comments">
            <h4>Comentários (<?= $commentsCount ?>)</h4>
            <?php
            if ($commentsCount > 0) {
                while ($c = $commentsRes->fetch_assoc()) {
                    echo '<div class="comment">';
                    echo '<img src="'.$c['avatar'].'" alt="Avatar" class="comment-avatar">';
                    echo '<div class="comment-body">';
                    echo '<strong>'.htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8').'</strong>';
                    echo '<p>'.htmlspecialchars($c['comment'], ENT_QUOTES, 'UTF-8').'</p>';
                    echo '<small>'.date("d M Y H:i", strtotime($c['created_at'])).'</small>';
                    echo '</div></div>';
                }
            } else {
                echo '<p>Nenhum comentário ainda.</p>';
            }
            ?>
        </div>

        <form class="comment-form" method="POST">
            <input type="hidden" name="action" value="comment">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <input type="text" name="name" placeholder="Seu nome" required>
            <textarea name="comment" placeholder="Seu comentário" required></textarea>
            <button type="submit" class="botao">Enviar</button>
        </form>
    </div>
</div>
<?php
    }
} else {
    echo "<p>Nenhum post encontrado.</p>";
}
?>
</section>

<style>
.botao { display:inline-block; padding:12px 20px; background-color:#823d2c; color:#fff; border-radius:6px; font-weight:bold; margin-top:10px; border:none; cursor:pointer; transition:0.3s;}
.botao:hover{ background-color:#5a2b1f; }
.like-btn{ background:none; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; }
.like-icon{ stroke:#823d2c; }
.modal{ position:fixed; top:0; left:0; width:100%; height:100%; backdrop-filter:blur(5px); background:rgba(0,0,0,0.4); display:flex; justify-content:center; align-items:center; z-index:1000;}
.modal-content{ background:#fff; padding:20px; border-radius:8px; width:90%; max-width:700px; max-height:90%; overflow-y:auto; position:relative;}
.close{ position:fixed; top:15px; right:20px; font-size:28px; cursor:pointer; background:#823d2c; color:#fff; border-radius:50%; width:35px; height:35px; text-align:center; line-height:35px; z-index:1100;}
.comment{ display:flex; gap:10px; margin-bottom:10px;}
.comment-avatar{ width:40px; height:40px; border-radius:50%;}
.comment-body{ background:#f1f1f1; padding:8px; border-radius:5px; flex:1;}
.comment-form input, .comment-form textarea{ width:100%; margin-bottom:8px; padding:8px; border-radius:5px; border:1px solid #ccc;}
.search-bar input::placeholder{ color:#fff;}
</style>

<script>
// Abrir modal
document.querySelectorAll('.open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        document.getElementById('modal-' + id).style.display = 'flex';
    });
});

// Fechar modal
document.querySelectorAll('.close').forEach(span => {
    span.addEventListener('click', () => {
        const id = span.dataset.id;
        document.getElementById('modal-' + id).style.display = 'none';
    });
});

// Fechar ao clicar fora
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
});

// Likes via Ajax
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const post_id = btn.dataset.id;
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'like=1&ajax=1&post_id=' + post_id
        })
        .then(res => res.text())
        .then(likes => {
            // Atualiza todos os botões de like do mesmo post
            document.querySelectorAll('.like-btn[data-id="'+post_id+'"]').forEach(b => {
                b.querySelector('.like-count').textContent = likes;
            });
        })
        .catch(err => console.error(err));
    });
});
</script>

<?php include 'footer.php'; ?>
