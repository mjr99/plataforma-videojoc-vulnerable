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
        // 💡 CAMBIO 1: La consulta busca tanto el usuario COMO la contraseña
        // Recuerda: la columna se llama 'password_hash', pero almacena texto plano.
        $sql = "SELECT * FROM usuaris WHERE nom_usuari = ? AND password_hash = ?"; 
        $stmt = $conn->prepare($sql);
        
        // 💡 CAMBIO 2: Vincula el usuario Y la contraseña (texto plano)
        $stmt->bind_param("ss", $usuario, $contrasenya);
        $stmt->execute();
        $result = $stmt->get_result();

        // 💡 CAMBIO 3: Si encuentra 1 fila, es correcto.
        if ($fila = $result->fetch_assoc()) {             
            // Éxito:
            $_SESSION['usuario'] = $fila['nom_usuari'];
            $_SESSION['email'] = $fila['email'];
            $_SESSION['nombre'] = $fila['nom_complet'];

            header("Location: /bakend/jocs/plataforma.php");
            exit();
            
        } else {
            // El usuario o la contraseña son incorrectos
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

