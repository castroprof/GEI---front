<nav class="topbar" id="topbar">
  <div class="container">
    <div class="logo">
      <span><strong>GEI Grupo de Estudos Iyamopo</strong></span>
    </div>
    <div class="menu">
      <a href="index.php">Página Inicial</a>
      <a href="blog.php" class="active">Blog</a>
      <a href="loja.php">Loja</a>
      <a href="portifolio.php">Portfólio</a>
    </div>
  </div>
</nav>

<style>
  .topbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #7a3e2a; /* Marrom escuro parecido */
    color: white;
    z-index: 1000;
    transition: top 0.3s ease;
    height: 60px;
    display: flex;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    box-sizing: border-box;
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

  .logo {
    font-weight: 700;
    font-size: 16px;
  }

  .menu {
    display: flex;
    gap: 25px;
  }

  .menu a {
    color: white;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    line-height: 60px;
    padding: 0 5px;
    transition: opacity 0.2s ease;
  }

  .menu a:hover,
  .menu a.active {
    text-decoration: underline;
    opacity: 0.85;
  }

  @media (max-width: 768px) {
    .topbar .container {
      padding: 0 15px;
    }

    .menu a {
      font-size: 13px;
      gap: 15px;
    }
  }
</style>

<script>
  let lastScrollTop = 0;
  const topbar = document.getElementById('topbar');
  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    if (currentScroll > lastScrollTop) {
      // rolando para baixo, esconder navbar
      topbar.style.top = '-60px';
    } else {
      // rolando para cima, mostrar navbar
      topbar.style.top = '0';
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });
</script>
