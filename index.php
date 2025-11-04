<?php
session_start();
require_once '/var/www/html/bakend/jocs/datosservidor.php';

$mensaje = '';
$joc_id = 1; // ID del juego STARBLAST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenya = trim($_POST['contrasenya'] ?? '');

    if ($usuario === '' || $contrasenya === '') {
        $mensaje = "❌ Faltan campos por rellenar";
    } else {
        $sql = "SELECT * FROM usuaris WHERE nom_usuari = ? AND password_hash = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $contrasenya);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            // Guardar datos del usuario en sesión
            $_SESSION['usuario'] = $fila['nom_usuari'];
            $_SESSION['email'] = $fila['email'];
            $_SESSION['nombre'] = $fila['nom_complet'];
            $_SESSION['usuari_id'] = $fila['id'];

            // Recuperar progreso del juego STARBLAST
            $sql = "SELECT nivell_actual, puntuacio_maxima FROM progres_usuari WHERE usuari_id = ? AND joc_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $fila['id'], $joc_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $progres = $result->fetch_assoc();

            $_SESSION['nivell'] = $progres['nivell_actual'] ?? 1;
            $_SESSION['punts'] = $progres['puntuacio_maxima'] ?? 0;

            header("Location: /bakend/jocs/plataforma.php");
            exit();
        } else {
            $mensaje = "❌ Usuario o contraseña incorrectos";
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css_base/index.css">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="POST">   
            <div class="border">
            <div class="login">
                <h2>Iniciar session</h2>
                <div class="inputBx">
                    <input type="text" name="usuario" placeholder="Usuario">
                </div>
                <div class="inputBx">
                    <input type="password" name="contrasenya" placeholder="Contraseña">
                </div>
                <div class="inputBx">
                    <input type="submit" value="CONTINUAR">
                </div>
                <?php if ($mensaje): ?>
                <div style="color: red; font-weight: bold; text-align: center;">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
                <div class="links">
                    <a href="./bakend/registre.php">Registrar-se</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>

