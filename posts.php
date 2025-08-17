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
  padding: 0 10px; /* um pequeno padding para evitar corte */
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
  .nav-btn:hover {
    background-color: rgba(0,0,0,0.8);
  }
  .nav-left {
    left: -25px;
  }
  .nav-right {
    right: -25px;
  }
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
          $posts = [
            ['title' => 'FÁBIO LOPES','description' => 'Membro do GEI e Professor de Filosofia e Matemática...','image' => 'https://static.wixstatic.com/media/607130_1273c70574fa462bb1987d4012f46b31~mv2.jpg','link' => 'blog.html'],
            ['title' => 'SOCIEDADES AFRICANAS E O...','description' => 'O Grupo de Estudos Iyamopo (GEI) desenvolve pesquisas...','image' => 'https://static.wixstatic.com/media/607130_39f26db1b9e245f7a56e911ffaddcd8e~mv2.png','link' => 'blog.html'],
            ['title' => 'Conheça a galeria de arte do Gru...','description' => 'A galeria de arte do Grupo de Estudos Iyamopo...','image' => 'https://static.wixstatic.com/media/607130_40eecb8e897a42c5a62f87dcece163d2~mv2.png','link' => 'blog.html'],
            ['title' => 'Outro post repetido...','description' => 'Mais conteúdo do GEI sendo exibido aqui...','image' => 'https://static.wixstatic.com/media/607130_1273c70574fa462bb1987d4012f46b31~mv2.jpg','link' => 'blog.html'],
            ['title' => 'Mais um post...','description' => 'Descrição de outro conteúdo exemplo...','image' => 'https://static.wixstatic.com/media/607130_39f26db1b9e245f7a56e911ffaddcd8e~mv2.png','link' => 'blog.html'],
            ['title' => 'Último post de teste...','description' => 'Aqui finalizamos os 6 posts exemplo...','image' => 'https://static.wixstatic.com/media/607130_40eecb8e897a42c5a62f87dcece163d2~mv2.png','link' => 'blog.html']
          ];

          foreach ($posts as $post) {
            echo '<a href="'. $post['link'] .'" class="card">';
            echo '<img src="'. $post['image'] .'" alt="'. htmlspecialchars($post['title']) .'" />';
            echo '<div class="content">';
            echo '<h4>'. htmlspecialchars($post['title']) .'</h4>';
            echo '<p>'. htmlspecialchars($post['description']) .'</p>';
            echo '</div></a>';
          }
        ?>
      </div>
    </div>
  </section>

<script>
  const cardsTrack = document.getElementById('cards-track');
let cards = Array.from(cardsTrack.children);
const gap = 20; // px
let cardWidth = cards[0].offsetWidth; // pega largura real do card
let totalCards = cards.length;
let currentIndex = 0;

// Função para mover o carrossel
function updateSlide() {
  const offset = -(cardWidth + gap) * currentIndex;
  cardsTrack.style.transition = 'transform 0.5s ease';
  cardsTrack.style.transform = `translateX(${offset}px)`;
}

// Função para ir para o próximo card
function showNext() {
  currentIndex++;
  if(currentIndex >= totalCards) {
    currentIndex = 0;
    cardsTrack.style.transition = 'none'; // remove transição
    updateSlide();
    // força reflow para garantir que a transição será aplicada no próximo update
    void cardsTrack.offsetWidth;
  }
  updateSlide();
}

// Função para voltar
function showPrev() {
  currentIndex--;
  if(currentIndex < 0) {
    currentIndex = totalCards - 1;
    cardsTrack.style.transition = 'none';
    updateSlide();
    void cardsTrack.offsetWidth;
  }
  updateSlide();
}

// Botões
document.getElementById('btn-next').addEventListener('click', showNext);
document.getElementById('btn-prev').addEventListener('click', showPrev);

// Inicializa
updateSlide();

// Ajusta largura dos cards se a tela mudar
window.addEventListener('resize', () => {
  cardWidth = cards[0].offsetWidth;
  updateSlide();
});


</script>

</body>
</html>
