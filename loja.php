<?php include 'head.php'; ?>

<body>
  <?php include 'nav.php'; ?>
 <link rel="stylesheet" href="css/loja.css"/>
  <aside>
    <h3>Buscar por</h3>
    <a href="#">Todos os produtos</a>
    <a href="#">Acessórios Culturais</a>
    <a href="#">Arte Iorubá</a>
    <a href="#">Literatura Africana</a>

    <h3>Filtrar</h3>
    <label><input type="checkbox" /> Arte Iorubá</label>
    <label><input type="checkbox" /> Literatura Africana</label>
    <label><input type="checkbox" /> Acessórios Culturais</label>

    <div class="filtro-preco">
      <label for="preco-min">Preço</label>
      <div class="slider-container">
        <input type="range" id="preco-min" min="40" max="300" value="40" step="10" />
        <input type="range" id="preco-max" min="40" max="300" value="300" step="10" />
      </div>
      <div class="valores">
        <span id="val-min">R$ 40</span> - <span id="val-max">R$ 300</span>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <div class="banner"></div>

    <h2 class="titulo-categoria">Todos os produtos</h2>
    <p class="descricao-categoria">
      Esta é a descrição da sua categoria. É um ótimo lugar para contar aos clientes sobre ela,
      conectar-se com seu público e chamar a atenção para seus produtos.
    </p>

    <div class="produtos-header">
      <span>9 produtos</span>
      <div class="ordenar">
        Ordenar:
        <select>
          <option>Recomendado</option>
          <option>Mais novos</option>
          <option>Preço (menor ao maior)</option>
          <option>Preço (maior ao menor)</option>
          <option>Nome A - Z</option>
          <option>Nome Z - A</option>
        </select>
      </div>
    </div>

    <div class="produtos-grid">
      <!-- produtos aqui -->
      <div class="produto">
        <img src="https://static.wixstatic.com/media/607130_d6d0a444fa464b96958cfceddd71215d~mv2.png/v1/fill/w_305,h_305,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/607130_d6d0a444fa464b96958cfceddd71215d~mv2.png" alt="colar" />
        <h4>Colar de Contas Iorubá</h4>
        <span>R$ 70,00</span>
      </div>
      <!-- repetir os outros produtos da mesma forma... -->
    </div>
  </main>

  <div class="modal-overlay" id="modal">
    <div class="modal-content">
      <button class="modal-close" onclick="fecharModal()">&times;</button>
      <img id="modal-img" src="" alt="Produto">
      <h4 id="modal-title"></h4>
      <span id="modal-price"></span>
      <p id="modal-description"></p>
      <a id="modal-whatsapp" class="whatsapp-link" href="#" target="_blank">Pedir no WhatsApp</a>
      <div class="modal-buttons">
        <button onclick="navegar(-1)">&larr; Anterior</button>
        <button onclick="navegar(1)">Próximo &rarr;</button>
      </div>
    </div>
  </div>

  <a
    class="whatsapp-button"
    href="https://wa.me/5511999999999"
    target="_blank"
    aria-label="Contato WhatsApp"
  >
    <img
      src="https://imagepng.org/wp-content/uploads/2017/08/whatsapp-icone-1.png"
      alt="WhatsApp"
    />
  </a>

  <script src="js/loja.js"></script>
</body>
</html>
