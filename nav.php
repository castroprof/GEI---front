<nav class="topbar" id="topbar">
  <div class="container">
    <div class="logo">
      <span><strong>GEI Grupo de Estudos Iyamopo</strong></span>
    </div>
    <div class="menu" id="menu">
      <a href="index.php">Página Inicial</a>
      <a href="blog.php">Blog</a>
      <a href="loja.php">Loja</a>
      <a href="portifolio.php">Portfólio</a>
      <a href="integrantes.php">Integrantes</a> <!-- NOVO LINK ADICIONADO -->
    </div>
    <div class="hamburger" id="hamburger">&#9776;</div>
  </div>
</nav>

<style>
  * { margin:0; padding:0; box-sizing:border-box; }

  .topbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #7a3e2a;
    color: white;
    z-index: 1000;
    transition: top 0.3s ease;
    height: 60px;
    display: flex;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .topbar .container {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .logo { font-weight: 700; font-size: 16px; }
  .menu { display: flex; gap: 25px; }
  .menu a {
    color: white;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    line-height: 60px;
    transition: opacity 0.2s ease;
  }
  .menu a:hover, .menu a.active { text-decoration: underline; opacity: 0.85; }

  .hamburger {
    display: none;
    font-size: 28px;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .menu {
      position: fixed;
      top: 60px;
      right: -250px;
      width: 200px;
      height: calc(100% - 60px);
      background: #7a3e2a;
      flex-direction: column;
      gap: 20px;
      padding: 20px;
      transition: right 0.3s ease;
    }
    .menu.show { right: 0; }
    .hamburger { display: block; }
  }
</style>

<script>
  const topbar = document.getElementById('topbar');
  const hamburger = document.getElementById('hamburger');
  const menu = document.getElementById('menu');
  let lastScrollTop = 0;

  window.addEventListener('scroll', () => {
    let st = window.pageYOffset || document.documentElement.scrollTop;
    if (st > lastScrollTop && st > 60) {
      // Rolando para baixo -> esconde
      topbar.style.top = '-60px';
    } else {
      // Rolando para cima -> mostra
      topbar.style.top = '0';
    }
    lastScrollTop = st <= 0 ? 0 : st;
  });

  hamburger.addEventListener('click', () => {
    menu.classList.toggle('show');
  });
</script>
