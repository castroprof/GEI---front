<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeria</title>
  <link rel="stylesheet" href="css/navbar.css" />
  <link rel="stylesheet" href="css/galeria.css" />
</head>
<body>
  <section class="galeria-section">
    <h2>Galeria</h2>
    <div class="galeria">
      <?php
        $imagens = [
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg",
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg",
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg",
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg",
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg",
          "https://static.wixstatic.com/media/607130_1e71c6abd469429089ee8d45ff7fa7a4~mv2.jpg"
        ];

        foreach ($imagens as $src) {
          echo "<img src=\"$src\" onclick=\"abrirPopup('$src')\">";
        }
      ?>
    </div>

    <div class="popup-overlay" id="popup">
      <div class="popup-controls">
        <button class="popup-expand">⤢</button>
        <button class="popup-close" onclick="fecharPopup()">✕</button>
      </div>
      <img src="" class="popup-img" id="popup-img">
      <div class="popup-description" id="popup-desc">Descrição</div>
      <a class="popup-whatsapp" id="popup-whatsapp" target="_blank">Comprar no WhatsApp</a>
    </div>
  </section>

  <script src="js/galeria.js"></script>
</body>
</html>
