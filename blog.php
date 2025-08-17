<?php include 'head.php'; ?>
<?php include 'nav.php'; ?>

  <!-- BARRA DE PESQUISA -->
  <form class="search-bar" onsubmit="event.preventDefault(); alert('Busca ainda não implementada');">
    <input type="search" placeholder="Buscar no blog..." />
    <button type="submit" style="background: none; border: none; cursor: pointer;">
     <link rel="stylesheet" href="css/blog.css" />
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
    </button>
  </form>

  <!-- POSTS -->
  <section class="posts-container">
    <article class="post">
      <div class="post-content">
        <h3>FÁBIO LOPES</h3>
        <p>Membro do GEI e Professor de Física e Matemática do Ensino Superior desde 2007. Mestre em Ensino de Ciências e Doutor em Física Aplicada...</p>
        <div class="post-meta">Iyamopo Cerâmica • 4 de jun. • 2 min</div>
        <div class="post-stats">
          <span>❤️ 0</span>
          <span>💬 0</span>
          <span>👁 6</span>
        </div>
      </div>
      <img class="post-image" src="https://static.wixstatic.com/media/607130_1273c70574fa462bb1987d4012f46b31~mv2.jpg" alt="Fábio Lopes">
    </article>

    <article class="post">
      <div class="post-content">
        <h3>SOCIEDADES AFRICANAS E O CONCEITO DE GÊNERO</h3>
        <p>O Grupo de Estudos Iyamopo (GEI) pretende explorar temas como cultura, filosofia e religião dos povos africanos...</p>
        <div class="post-meta">Iyamopo Cerâmica • 4 de jun. • 1 min</div>
        <div class="post-stats">
          <span>❤️ 0</span>
          <span>💬 0</span>
          <span>👁 9</span>
        </div>
      </div>
      <img class="post-image" src="https://static.wixstatic.com/media/607130_39f26db1b9e245f7a56e911ffaddcd8e~mv2.png" alt="Sociedades Africanas">
    </article>

    <article class="post">
      <div class="post-content">
        <h3>GALERIA DE ARTE DO GEI</h3>
        <p>A galeria de arte do Grupo de Estudos Iyamopo é um novo espaço online que visa promover a diversidade da cultura africana...</p>
        <div class="post-meta">Iyamopo Cerâmica • 31 de mai. • 1 min</div>
        <div class="post-stats">
          <span>❤️ 0</span>
          <span>💬 0</span>
          <span>👁 6</span>
        </div>
      </div>
      <img class="post-image" src="https://static.wixstatic.com/media/607130_40eecb8e897a42c5a62f87dcece163d2~mv2.png" alt="Galeria de Arte">
    </article>
  </section>

<?php include 'footer.php'; ?>
