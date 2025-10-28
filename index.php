<?php
session_start();
require_once '/var/www/html/bakend/jocs/datosservidor.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenya = trim($_POST['contrasenya'] ?? '');

    if ($usuario === '' || $contrasenya === '') {
        $mensaje = "❌ Faltan campos por rellenar";
    } else {
        $sql = "SELECT * FROM usuaris WHERE nom_usuari = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $fila = $result->fetch_assoc();
            $hash_guardado = $fila['password_hash'];

            if (password_verify($contrasenya, $hash_guardado)) {
                $_SESSION['usuario'] = $fila['nom_usuari'];
                $_SESSION['email'] = $fila['email'];
                $_SESSION['nombre'] = $fila['nom_complet'];

                header("Location: /bakend/jocs/plataforma.php");
                exit();
            } else {
                $mensaje = "❌ Contraseña incorrecta";
            }
        } else {
            $mensaje = "❌ Usuario no encontrado";
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

