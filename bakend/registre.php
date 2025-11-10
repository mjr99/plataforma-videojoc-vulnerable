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

// Variables para mantener los datos en el formulario si hay un error
$nom_complet = '';
$nom_usuari = '';
$email = '';
// La contraseña NUNCA se vuelve a llenar por seguridad

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
        
        // =================================================================
        // 3. VERIFICACIÓN PREVIA DE USUARIO/EMAIL DUPLICADO
        // =================================================================
        // Buscamos si el nombre de usuario O el email ya existen
        $sql_check = "SELECT nom_usuari, email FROM usuaris WHERE nom_usuari = ? OR email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $nom_usuari, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        $usuario_existe = false;
        $email_existe = false;

        if ($result_check->num_rows > 0) {
            // Recorremos los resultados para ver cuál de los dos es el duplicado
            while ($fila = $result_check->fetch_assoc()) {
                if ($fila['nom_usuari'] === $nom_usuari) {
                    $usuario_existe = true;
                }
                if ($fila['email'] === $email) {
                    $email_existe = true;
                }
            }
        }
        $stmt_check->close();

        // 4. Mostrar el mensaje específico de error o continuar con la inserción
        if ($usuario_existe) {
            // Mensaje específico solicitado: "Aquest nom d'usuari ja existeix"
            $mensaje = "❌ Aquest nom d'usuari ja existeix.";
        } elseif ($email_existe) {
            // Diferenciamos si el email ya existe
            $mensaje = "❌ Aquest correu electrònic ja està registrat.";
        } else {
            // =================================================================
            // 5. INSERCIÓN (Si el usuario y email son únicos)
            // =================================================================
            $sql = "INSERT INTO usuaris (nom_complet, nom_usuari, email, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                // Error al preparar la consulta
                die("❌ Error preparant la consulta d'inserció: " . $conn->error . "<br>");
            }

            // 6. Vincular parámetros y ejecutar
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
                // Error de inserción (capturado aquí por seguridad)
                $mensaje = "❌ Error al registrar l'usuari: " . $stmt->error;
                $stmt->close();
            }
        }
    }
}

// Cerrar conexión después de toda la lógica, si sigue abierta
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

?>


<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css_base/registre.css">
    <title>Registre d'Usuaris</title>
</head>
<body>
    <form action="registre.php" method="POST"> 	
        <div class="border">
            <div class="login">
                <h2>Creant Usuari</h2>
                
                <!-- Campos de registro (se añade value para mantener datos en caso de error) -->
                <div class="inputBx">
                    <input type="text" name="nombre" placeholder="Nom Complet" required value="<?= htmlspecialchars($nom_complet) ?>">
                </div>
                <div class="inputBx">
                    <input type="email" name="email" placeholder="Correu electrònic" required value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="inputBx">
                    <input type="text" name="usuario" placeholder="Usuari" required value="<?= htmlspecialchars($nom_usuari) ?>">
                </div>
                <div class="inputBx">
                    <!-- El campo de contraseña se deja vacío por seguridad -->
                    <input type="password" name="contrasenya" placeholder="Contrasenya" required>
                </div>

                <!-- Botón de registro -->
                <div class="inputBx">
                    <input type="submit" value="CONTINUAR">
                </div>

                <!-- Mensaje de estado -->
                <?php if ($mensaje): ?>
                    <div style="color: red; font-weight: bold; text-align: center; margin-top: 10px;">
                        <?= $mensaje ?>
                    </div>
                <?php endif; ?>
                
                <!-- Enlace a Login -->
                <div class="links">
                    <a href="./../../index.php">Iniciar sessió</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>