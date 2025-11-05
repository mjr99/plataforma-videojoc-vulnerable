<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './datosservidor.php';

$joc_id = 1;
$usuari_id = $_SESSION['usuari_id'] ?? null;

if ($usuari_id) {
    $sql = "SELECT nivell_actual, puntuacio_maxima FROM progres_usuari WHERE usuari_id = ? AND joc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuari_id, $joc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $progres = $result->fetch_assoc();

    $nivell = $progres['nivell_actual'] ?? 1;
    $punts = $progres['puntuacio_maxima'] ?? 0;
}
?>
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
            require_once './datosservidor.php';
            $sql = "SELECT foto_perfil FROM perfil_usuario ORDER BY id DESC LIMIT 1";
            $result = $conn->query($sql);
            $foto = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['foto_perfil'] : 'uploads/default.jpg';
            ?>
            <div class="avatar-container">
                <a href="./perfil.php">
                    <span><?php echo $nom_usuari; ?></span>
                    <div class="perfil">
                        <p>Usuari: <?= $_SESSION['usuario'] ?></p>
                        <p>Nivell: <?= $nivell ?></p>
                        <p>Punts: <?= $punts ?></p>
                    </div>

                    <img src="/<?= $foto ?>" alt="Perfil" class="avatar">
                </a>
            </div>
        </header>

        <section>
            <a href="./../../joc/index.php">
                <article style="--avarage-color: #94af95ff">
                    <figure>
                        <img src="./../../img/nave.jpg">
                        <figcaption>STARBLAST</figcaption>
                    </figure>
                </article>
            </a>

            <article style="--avarage-color: #3c3c3d">
                <figure>
                    <img src="">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>

            <article style="--avarage-color: #b47460">
                <figure>
                    <img src="">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>

            <article style="--avarage-color: #60a6ce">
                <figure>
                    <img src="">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>
        </section>
    </main>
</body>
</html>
