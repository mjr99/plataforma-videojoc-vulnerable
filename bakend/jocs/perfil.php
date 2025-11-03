<?php
require_once '/var/www/html/bakend/jocs/datosservidor.php';

$apodo = $_POST['apodo'] ?? '';
$bio = $_POST['bio'] ?? '';
$foto = $_FILES['foto'] ?? null;
$rutaFoto = '';
$mensaje = '';

// Validación básica
if ($apodo === '' || $bio === '') {
    $mensaje = "❌ Faltan campos por rellenar.";
} else {
    // Procesar imagen si se ha subido
    if ($foto && $foto['tmp_name']) {
        $nombreArchivo = uniqid() . '_' . basename($foto['name']);
        $rutaServidor = '/var/www/html/uploads/' . $nombreArchivo; // Ruta física
        $rutaWeb = 'uploads/' . $nombreArchivo; // Ruta para mostrar en HTML

        if (move_uploaded_file($foto['tmp_name'], $rutaServidor)) {
            $rutaFoto = $rutaWeb;
        } else {
            $mensaje = "❌ Error al subir la imagen. Verifica permisos y carpeta.";
        }
    }

    // Insertar en la base de datos si no hubo error con la imagen
    if ($mensaje === '') {
        $sql = "INSERT INTO perfil_usuario (apodo, foto_perfil, bio) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $apodo, $rutaFoto, $bio);
        $stmt->execute();
        $stmt->close();
        $mensaje = "✅ Perfil guardat correctament!";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Perfil</title>
    <link rel="stylesheet" href="/css_base/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h1>🎮 Personalitza el teu perfil</h1>
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="apodo">Apodo</label>
                <input type="text" name="apodo" id="apodo" placeholder="Tria un apodo">
            </div>
            <div class="form-group">
                <label for="foto">Foto de perfil</label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </div>
            <div class="form-group">
                <label for="bio">Biografia</label>
                <textarea name="bio" id="bio" rows="4" placeholder="Explica alguna cosa sobre tu..."></textarea>
            </div>
            <button type="submit">💾 Guardar perfil</button>
        </form>
    </div>
</body>
</html>
