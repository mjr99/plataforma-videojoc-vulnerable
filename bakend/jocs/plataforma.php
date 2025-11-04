<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css_base/iniciojuego.css">
    <title>Document</title>
</head>
<body>
    <?php
        session_start();
        $nom_usuari = $_SESSION['usuario'] ?? 'Jugador';

        ?>

        <script>
        const usuari = <?php echo $nom_usuari; ?>
        // Guardamos el nombre del usuario en sessionStorage
        sessionStorage.setItem("nom_usuari", "<?php echo $nom_usuari; ?>");
        </script>

    <main>
        <header>
            <h1>Juegos Pau & Marc - 2025</h1>

            <?php
            require_once '/var/www/html/bakend/jocs/datosservidor.php';
            $sql = "SELECT foto_perfil FROM perfil_usuario ORDER BY id DESC LIMIT 1";
            $result = $conn->query($sql);
            $foto = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['foto_perfil'] : 'uploads/default.jpg';
            ?>
            <div class="avatar-container">
                <a href="/bakend/jocs/perfil.php">
                    <span><?php echo $nom_usuari; ?></span>
                    <p>Nivell recuperat: <?php echo $_SESSION['nivell']; ?></p>
                    <p>Punts recuperats: <?php echo $_SESSION['punts']; ?></p>

                    <img src="/<?= $foto ?>" alt="Perfil" class="avatar">
                </a>
            </div>
        </header>

        <section>
            <a href="./../../joc/index.php">
                <article style="--avarage-color: #afa294">
                    <figure>
                        <img src="./../../img/nave.jpg">
                        <figcaption>STARBLAST</figcaption>
                    </figure>
                </article>
            </a>

            <article style="--avarage-color: #3c3c3d">
                <figure>
                    <img src="https://i.imgur.com/MwRrRSd.jpeg">
                    <figcaption>Bibliomania</figcaption>
                </figure>
            </article>

            <article style="--avarage-color: #b47460">
                <figure>
                    <img src="https://i.imgur.com/7FQ6L5j.jpeg">
                    <figcaption>Dandadan</figcaption>
                </figure>
            </article>

            <article style="--avarage-color: #60a6ce">
                <figure>
                    <img src="https://i.imgur.com/IQSq88g.jpeg">
                    <figcaption>The Summer Hikaru Died</figcaption>
                </figure>
            </article>
        </section>
    </main>
</body>
</html>
