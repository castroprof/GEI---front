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

// Buscar integrantes
$sql = "SELECT * FROM integrantes ORDER BY nome ASC";
$res = $conn->query($sql);
?>

<section class="integrantes-container">
    <h1>Nosso Grupo de Pesquisadores</h1>
    <div class="integrantes-grid">
        <?php
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
                $descricao = htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8');
                $imagem = htmlspecialchars($row['imagem'], ENT_QUOTES, 'UTF-8');
                $linkedin = htmlspecialchars($row['linkedin'] ?? '');
                $lattes = htmlspecialchars($row['lattes'] ?? '');
                $facebook = htmlspecialchars($row['facebook'] ?? '');
                $instagram = htmlspecialchars($row['instagram'] ?? '');
                
                echo '<div class="integrante-card">
                    <div class="avatar-wrapper">
                        <img src="'.$imagem.'" alt="'.$nome.'" class="avatar">
                    </div>
                    <h3>'.$nome.'</h3>
                    <p>'.$descricao.'</p>
                    <div class="social-icons">';

                        // LinkedIn
                        echo '<a '.($linkedin ? 'href="'.$linkedin.'" target="_blank"' : '').' class="icon linkedin" title="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.23 8h4.54v16H.23V8zm7.25 0h4.36v2.2h.06c.61-1.15 2.1-2.35 4.32-2.35 4.63 0 5.48 3.05 5.48 7.01V24h-4.55v-6.72c0-1.6-.03-3.65-2.22-3.65-2.22 0-2.56 1.74-2.56 3.54V24H7.48V8z"/>
                            </svg>
                        </a>';

                        // Lattes
                        echo '<a '.($lattes ? 'href="'.$lattes.'" target="_blank"' : '').' class="icon lattes" title="Lattes">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                        </a>';

                        // Facebook
                        echo '<a '.($facebook ? 'href="'.$facebook.'" target="_blank"' : '').' class="icon facebook" title="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2v1.8h2.5l-.4 3H14v7A10 10 0 0 0 22 12z"/>
                            </svg>
                        </a>';

                        // Instagram
                        echo '<a '.($instagram ? 'href="'.$instagram.'" target="_blank"' : '').' class="icon instagram" title="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 2 .2 2.5.4.6.2 1 .5 1.5 1s.8.9 1 1.5c.2.5.3 1.3.4 2.5.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 2-.4 2.5-.2.6-.5 1-1 1.5s-.9.8-1.5 1c-.5.2-1.3.3-2.5.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-2-.2-2.5-.4-.6-.2-1-.5-1.5-1s-.8-.9-1-1.5c-.2-.5-.3-1.3-.4-2.5C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-2 .4-2.5.2-.6.5-1 1-1.5s.9-.8 1.5-1c.5-.2 1.3-.3 2.5-.4C8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.1-1.1.1-1.7.2-2.1.3-.5.1-.8.3-1.2.6-.4.3-.7.7-.9 1.2-.1.4-.2 1-.3 2.1-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1.1.2 1.7.3 2.1.1.5.3.8.6 1.2.3.4.7.7 1.2.9.4.1 1 .2 2.1.3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1.1-.1 1.7-.2 2.1-.3.5-.1.8-.3 1.2-.6.4-.3.7-.7.9-1.2.1-.4.2-1 .3-2.1.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c-.1-1.1-.2-1.7-.3-2.1-.1-.5-.3-.8-.6-1.2-.3-.4-.7-.7-1.2-.9-.4-.1-1-.2-2.1-.3-1.2-.1-1.6-.1-4.7-.1zm0 3a6 6 0 1 1 0 12 6 6 0 0 1 0-12zm0 1.8a4.2 4.2 0 1 0 0 8.4 4.2 4.2 0 0 0 0-8.4zm6.4-.9a1.4 1.4 0 1 1-2.8 0 1.4 1.4 0 0 1 2.8 0z"/>
                            </svg>
                        </a>';

                echo '</div></div>';
            }
        } else {
            echo "<p>Nenhum integrante cadastrado.</p>";
        }
        ?>
    </div>
</section>

<style>
.integrantes-container {
    max-width: 1200px;
    margin: 100px auto 50px;
    padding: 0 20px;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.integrantes-container h1 {
    margin-bottom: 50px;
    font-size: 36px;
    font-weight: 700;
    color: #823d2c;
}

.integrantes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 40px;
}

.integrante-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    padding: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.integrante-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.avatar-wrapper {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    overflow: hidden;
    border-radius: 50%;
    border: 4px solid #823d2c;
}

.avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.integrante-card h3 {
    margin: 10px 0 8px;
    font-size: 20px;
    color: #823d2c;
    font-weight: 600;
}

.integrante-card p {
    font-size: 14px;
    color: #555;
    line-height: 1.5;
    padding: 0 5px;
}

.social-icons {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    gap: 12px;
}

.social-icons a {
    width: 36px;
    height: 36px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background: #823d2c;
    transition: transform 0.2s, opacity 0.2s;
}

.social-icons a:hover {
    transform: scale(1.2);
    opacity: 0.8;
}

.social-icons a svg {
    width: 20px;
    height: 20px;
}

@media (max-width: 768px) {
    .avatar-wrapper {
        width: 100px;
        height: 100px;
    }
    .integrante-card h3 {
        font-size: 18px;
    }
    .integrante-card p {
        font-size: 13px;
    }
    .social-icons a {
        width: 30px;
        height: 30px;
    }
    .social-icons a svg {
        width: 16px;
        height: 16px;
    }
}
</style>

<?php include 'footer.php'; ?>
