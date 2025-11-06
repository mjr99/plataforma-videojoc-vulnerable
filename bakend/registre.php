<?php
// PHP Script para manejar el registro de nuevos usuarios.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye el archivo de conexión
require_once '/var/www/html/bakend/jocs/datosservidor.php';

// Verifica que la conexión esté activa
if (!$conn) {
    die("❌ Error de conexión con la base de datos<br>");
}

$mensaje = ''; // Mensaje para mostrar errores o éxito

// =================================================================
// LÓGICA DE REGISTRO
// =================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolección y sanitización básica de datos
    $nom_complet = trim($_POST['nombre'] ?? '');
    $nom_usuari = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasenya = trim($_POST['contrasenya'] ?? ''); // Contraseña en texto plano

    // 2. Validación mínima de campos
    if (empty($nom_complet) || empty($nom_usuari) || empty($email) || empty($contrasenya)) {
        $mensaje = "❌ Totes les dades són obligatòries.";
    } else {
        // 3. Preparar la consulta de inserción (manteniendo la columna password_hash para la contraseña en texto plano)
        $sql = "INSERT INTO usuaris (nom_complet, nom_usuari, email, password_hash) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            // Error al preparar la consulta
            die("❌ Error preparant la consulta d'inserció: " . $conn->error . "<br>");
        }

        // 4. Vincular parámetros y ejecutar
        $stmt->bind_param("ssss", $nom_complet, $nom_usuari, $email, $contrasenya);

        if ($stmt->execute()) {
            // Inserción exitosa. Redirigir al login
            $stmt->close();
            $conn->close();
            // Mensaje de éxito antes de la redirección
            $_SESSION['registro_exito'] = "✅ Usuari registrat correctament! Ja pots iniciar sessió.";
            header("Location: ./../../index.php");
            exit();
        } else {
            // Error en la ejecución (ej: nombre de usuario duplicado)
            if ($conn->errno === 1062) { // Código de error SQL para entrada duplicada
                $mensaje = "❌ El nom d'usuari o el correu electrònic ja existeixen.";
            } else {
                $mensaje = "❌ Error al registrar l'usuari: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css_base/registre.css">
    <title>Document</title>
</head>
<body>
    <form action="registre.php" method="POST">    
        <div class="border">
            <div class="login">
                <h2>Creant Usuari</h2>
                <div class="inputBx">
                    <input type="text" name="nombre" placeholder="Nom Complet" required>
                </div>
                <div class="inputBx">
                    <input type="email" name="email" placeholder="Correu electrònic" required>
                </div>
                <div class="inputBx">
                    <input type="text" name="usuario" placeholder="Usuari" required>
                </div>
                <div class="inputBx">
                    <input type="password" name="contrasenya" placeholder="Contrasenya" required>
                </div>
                <div class="inputBx">
                    <input type="submit" value="CONTINUAR">
                </div>
                
                <?php if ($mensaje): ?>
                    <div style="color: red; font-weight: bold; text-align: center; margin-top: 10px;">
                        <?= $mensaje ?>
                    </div>
                <?php endif; ?>

                <div class="links">
                    <a href="./../index.php">Iniciar sessió</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>

