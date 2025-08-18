<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Postagens Recentes - GEI</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    margin: 0; padding: 0;
  }
  .posts {
    max-width: 960px;
    margin: 50px auto;
    padding: 0 20px;
    position: relative;
  }
  h2 {
    text-align: center;
    margin-bottom: 20px;
  }
  .cards {
    overflow: hidden;
    max-width: 960px;
    width: 100%;
    margin: 0 auto;
    position: relative;
    padding: 0 10px;
  }
  .cards-track {
    display: flex;
    gap: 20px;
    transition: transform 0.5s ease;
  }
  .card {
    flex: 0 0 300px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    text-decoration: none;
    color: #333;
    user-select: none;
    cursor: pointer;
  }
  .card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
  }
  .content {
    padding: 15px;
  }
  .content h4 {
    margin: 0 0 8px 0;
    font-size: 18px;
  }
  .content p {
    margin: 0;
    font-size: 14px;
    color: #666;
  }
  .nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0,0,0,0.5);
    border: none;
    color: white;
    font-size: 22px;
    padding: 10px 14px;
    cursor: pointer;
    border-radius: 50%;
    user-select: none;
    z-index: 100;
    transition: background-color 0.3s;
  }
  .nav-btn:hover { background-color: rgba(0,0,0,0.8); }
  .nav-left { left: -25px; }
  .nav-right { right: -25px; }
</style>
</head>
<body>
  <section class="posts">
    <h2>Postagens Recentes</h2>
    <button class="nav-btn nav-left" id="btn-prev">&#8592;</button>
    <button class="nav-btn nav-right" id="btn-next">&#8594;</button>

    <div class="cards">
      <div class="cards-track" id="cards-track">
        <?php
        // Conexão com o banco
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "db_gei";
        $conn = new mysqli($host, $user, $pass, $db);
        if($conn->connect_error) die("Erro de conexão: " . $conn->connect_error);

        // Criar tabela posts se não existir
        $conn->query("
          CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            image VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            likes INT DEFAULT 0,
            views INT DEFAULT 0
          )
        ");

        // Buscar posts
        $res = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
        if($res->num_rows > 0){
            while($post = $res->fetch_assoc()){
                $post_id = $post['id'];
                echo '<div class="card" onclick="window.location.href=\'blog.php?view='.$post_id.'\'">';
                echo '<img src="'. htmlspecialchars($post['image']) .'" alt="'. htmlspecialchars($post['title']) .'" />';
                echo '<div class="content">';
                echo '<h4>'. htmlspecialchars($post['title']) .'</h4>';
                echo '<p>'. htmlspecialchars(substr($post['description'],0,100)) .'...</p>';
                echo '</div></div>';
            }
        } else {
            echo '<p>Nenhum post encontrado.</p>';
        }
        ?>
      </div>
    </div>
  </section>

<script>
  const cardsTrack = document.getElementById('cards-track');
  let cards = Array.from(cardsTrack.children);
  const gap = 20; 
  let cardWidth = cards[0] ? cards[0].offsetWidth : 300;
  let totalCards = cards.length;
  let currentIndex = 0;

  function updateSlide() {
    const offset = -(cardWidth + gap) * currentIndex;
    cardsTrack.style.transition = 'transform 0.5s ease';
    cardsTrack.style.transform = `translateX(${offset}px)`;
  }
  function showNext() {
    currentIndex++;
    if(currentIndex >= totalCards) currentIndex = 0;
    updateSlide();
  }
  function showPrev() {
    currentIndex--;
    if(currentIndex < 0) currentIndex = totalCards - 1;
    updateSlide();
  }
  document.getElementById('btn-next').addEventListener('click', showNext);
  document.getElementById('btn-prev').addEventListener('click', showPrev);

  updateSlide();
  window.addEventListener('resize', () => {
    cards = Array.from(cardsTrack.children);
    cardWidth = cards[0].offsetWidth;
    totalCards = cards.length;
    updateSlide();
  });
</script>
</body>
</html>
