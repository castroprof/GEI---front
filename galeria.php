<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeria</title>
  <link rel="stylesheet" href="css/galeria.css" />
  <link rel="stylesheet" href="css/navbar.css" />
  <style>
    .gal { color: white; }
  </style>
</head>
<body>
  <section class="galeria-section">
    <h2 class="gal">Galeria</h2>
    <div class="galeria">
      <?php
        // Conexão com o banco
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "db_gei";

        $conn = new mysqli($host, $user, $pass, $db);
        if ($conn->connect_error) die("Erro de conexão: " . $conn->connect_error);

        // Consulta imagens
        $res = $conn->query("SELECT * FROM galeria ORDER BY id DESC");
        if($res->num_rows > 0){
          while($row = $res->fetch_assoc()){
            $src = htmlspecialchars($row['image']);
            $title = htmlspecialchars($row['title']);
            $desc = htmlspecialchars($row['description']);
            // Passa a descrição para o popup
            echo "<img src=\"$src\" alt=\"$title\" onclick=\"abrirPopup('$src', '$desc')\">";
          }
        } else {
          echo "<p style='color:white;'>Nenhuma imagem cadastrada.</p>";
        }
      ?>
    </div>

    <div class="popup-overlay" id="popup">
      <div class="popup-controls">
        <button class="popup-expand">⤢</button>
        <button class="popup-close" onclick="fecharPopup()">✕</button>
      </div>
      <img src="" class="popup-img" id="popup-img">
      <div class="popup-description" id="popup-desc">Imagem selecionada da galeria</div>
      <a class="popup-whatsapp" id="popup-whatsapp" target="_blank">Comprar no WhatsApp</a>
    </div>
  </section>

  <script src="js/galeria.js"></script>
  <script>
    // Função de abrir popup modificada para exibir a descrição do banco
    function abrirPopup(src, desc){
      const popup = document.getElementById('popup');
      const popupImg = document.getElementById('popup-img');
      const popupDesc = document.getElementById('popup-desc');

      popup.style.display = 'flex';
      popupImg.src = src;
      popupDesc.innerText = desc || 'Imagem selecionada da galeria';
    }

    function fecharPopup(){
      document.getElementById('popup').style.display = 'none';
    }
  </script>
</body>
</html>
