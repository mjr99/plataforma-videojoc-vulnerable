<?php
// PHP Script para la plataforma principal de juegos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verificar si el usuario está logeado. Si no, redirigir al login.
if (!isset($_SESSION['usuari_id'])) {
    header("Location: ./../../index.php");
    exit();
}

// Incluye el archivo de conexión y establece $conn
require_once './datosservidor.php';

// Datos del usuario desde la sesión
$userId = $_SESSION['usuari_id'];
$nombre_usuario = $_SESSION['usuario']; 
$joc_id = 1;
$puntos = 0;

$imagen_perfil = 'uploads/default.jpg'; // Corregida la ruta inicial

// A) Obtener Progreso
$sql_progres = "SELECT nivell_actual, puntuacio_maxima FROM progres_usuari WHERE usuari_id = ? AND joc_id = ?";
$stmt_progres = $conn->prepare($sql_progres);

if ($stmt_progres) {
    $stmt_progres->bind_param("ii", $userId, $joc_id);
    $stmt_progres->execute();
    $result_progres = $stmt_progres->get_result();
    $progres = $result_progres->fetch_assoc();

    $nivel = $progres['nivell_actual'] ?? 1;
    $puntos = $progres['puntuacio_maxima'] ?? 0;
    $stmt_progres->close();
}

// B) Obtener Imagen de Perfil
// Asumo que la columna 'id' de perfil_usuario se relaciona con 'usuari_id'
$sql_img = "SELECT foto_perfil FROM perfil_usuario WHERE id = ?";
$stmt_img = $conn->prepare($sql_img);

if ($stmt_img) {
    $stmt_img->bind_param("i", $userId);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();

    if ($fila_img = $result_img->fetch_assoc()) {
        // Usamos la columna 'foto_perfil' que has confirmado
        if (!empty($fila_img['foto_perfil'])) {
            // Se asume que las rutas de imagen son relativas al directorio raíz (/)
            $imagen_perfil = $fila_img['foto_perfil'];
        }
    }
    $stmt_img->close();
}

// 3. Cerrar la conexión AHORA, después de TODAS las consultas necesarias.
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

// Para asegurar la ruta correcta en el HTML
$ruta_imagen = (strpos($imagen_perfil, './') === 0 || strpos($imagen_perfil, 'uploads/') === 0) 
    ? './../../' . ltrim($imagen_perfil, './') 
    : $imagen_perfil;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css_base/plataforma.css">
    <title>Plataforma de Juegos</title>
</head>
<body>
    <main>
        <header class="encabezado">
            <h1>Juegos Pau & Marc - 2025</h1>
            
            <div class="avatar-container">
                <!-- Contenedor principal del avatar y el pop-up -->
                <a href="./perfil.php" class="avatar-link">
                    <!-- Ruta de imagen corregida -->
                    <img src="<?= htmlspecialchars($ruta_imagen) ?>" alt="Perfil" class="avatar">
                </a>
                
                <!-- Contenedor del pop-up de información -->
                <div class="perfil-info">
                    <p class="user-name-title"><?= htmlspecialchars($nombre_usuario); ?></p>
                    <hr class="neon-divider">
                    <p>Nivell: <span><?= htmlspecialchars($nivel) ?></span></p>
                    <p>Punts: <span><?= htmlspecialchars($puntos) ?></span></p>
                    <a href="./perfil.php" class="profile-link">Editar Perfil</a>
                </div>
            </div>
        </header>

        <section>
            <!-- Soporte de Portadas: Starblast (El único visible con imagen) -->
            <a href="./../../joc/index.php">
                <article> <!-- Eliminada la variable de estilo inline -->
                    <figure>
                        <!-- La imagen Starblast sí tiene una ruta -->
                        <img src="./../../img/Portada-STARBLAST.png" alt="Portada Starblast">
                        <figcaption>STARBLAST</figcaption>
                    </figure>
                </article>
            </a>

            <article> <!-- Eliminada la variable de estilo inline -->
                <figure>
                    <img src="./../../img/Portada-Incognito.png" alt="Juego Desconocido">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>

            <article> <!-- Eliminada la variable de estilo inline -->
                <figure>
                    <img src="./../../img/Portada-Incognito.png" alt="Juego Desconocido">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>

            <article> <!-- Eliminada la variable de estilo inline -->
                <figure>
                    <img src="./../../img/Portada-Incognito.png" alt="Juego Desconocido">
                    <figcaption>?????????</figcaption>
                </figure>
            </article>
        </section>
    </main>
</body>
</html>