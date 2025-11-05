<?php
// Incluye el archivo de conexión
require_once '/var/www/html/bakend/jocs/datosservidor.php';

// Verifica que la conexión esté activa
if (!$conn) {
    die("❌ Error de conexión con la base de datos<br>");
}

// 1. Recolección de datos
$apodo = $_POST['apodo'] ?? '';
$bio = $_POST['bio'] ?? '';
$usuari = $_POST['usuari'] ?? '';
$psswrd = $_POST['psswrd'] ?? '';
$foto = $_FILES['foto'] ?? null;
$rutaFoto = '';

// 2. Encapsulación y Validación POST (Similar a registre.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar que al menos un conjunto de campos fue rellenado
    if (empty($apodo) && empty($bio) && empty($usuari) && empty($psswrd) && (empty($foto) || empty($foto['tmp_name']))) {
        die("⚠️ Debes rellenar al menos un campo de credenciales o de perfil.<br>");
    }

    // A. Lógica de ACTUALIZACIÓN de CREDENCIALES (PRIORIDAD)
    if (!empty($usuari) || !empty($psswrd)) {
        
        $set_clauses = [];
        $params = [];
        $types = '';

        if (!empty($usuari)) {
            $set_clauses[] = "nom_usuari = ?";
            $params[] = &$usuari;
            $types .= 's';
        }

        if (!empty($psswrd)) {
            $set_clauses[] = "password_hash = ?"; // Contraseña sin hashear (vulnerable)
            $params[] = &$psswrd;
            $types .= 's';
        }

        // Construir y ejecutar la consulta de credenciales
        $sql_update_login = "UPDATE usuaris SET " . implode(", ", $set_clauses) . " WHERE id_usuari = ?";
        $types .= 'i';
        $params[] = &$userId;
        
        $stmt_login = $conn->prepare($sql_update_login);
        if (!$stmt_login) {
            die("❌ Error preparando la consulta de credenciales: " . $conn->error . "<br>");
        }
        
        call_user_func_array([$stmt_login, 'bind_param'], array_merge([$types], $params));

        if ($stmt_login->execute()) {
            $stmt_login->close();
            // Redirigir a login tras cambio de credenciales (flujo simplificado y directo)
            header('Location: login.php');
            exit();
        } else {
            die("❌ Error al actualizar credenciales: " . $stmt_login->error . "<br>");
        }
    } 
    
    // B. Lógica de ACTUALIZACIÓN de PERFIL (Solo si se rellenaron los campos de perfil)
    
    // Si se llegó a este punto, significa que no hubo redirección de credenciales.
    if (!empty($apodo) && !empty($bio)) { 
        
        // 1. Procesar imagen si se ha subido
        if ($foto && $foto['tmp_name']) {
            $nombreArchivo = uniqid() . '_' . basename($foto['name']);
            $rutaServidor = '/var/www/html/uploads/' . $nombreArchivo;
            $rutaWeb = 'uploads/' . $nombreArchivo; 

            if (!move_uploaded_file($foto['tmp_name'], $rutaServidor)) {
                die("❌ Error al subir la imagen. Verifica permisos y carpeta.<br>");
            }
            $rutaFoto = $rutaWeb;
        }

        // 2. Actualizar en la base de datos (UPDATE)
        $sql = "UPDATE perfil_usuario SET apodo = ?, foto_perfil = ?, bio = ? WHERE id_usuari = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("❌ Error preparando la consulta de perfil: " . $conn->error . "<br>");
        }
        
        $stmt->bind_param("sssi", $apodo, $rutaFoto, $bio, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            // Redirigir a la misma página o a una de éxito (flujo simplificado)
            header("Location: perfil.php?status=success");
            exit();
        } else {
            die("❌ Error al guardar el perfil: " . $stmt->error . "<br>");
        }
    }
} 

$conn->close();

// Si se recibe un mensaje de éxito por GET (redirigido desde la línea 99), lo mostramos.
$mensaje_exito = ($_GET['status'] ?? '') === 'success' ? "✅ Perfil guardat correctament!" : '';

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
        <div class="titulo">
            <h1>🎮 Personalitza el teu perfil</h1>
            <div>
                <img src="" alt="">
                <h2>usuari</h2>
            </div>
        </div>
        
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="apodo">Apodo</label>
                <input type="text" name="apodo" id="apodo" placeholder="Tria un apodo">
            </div>
            <div class="form-group">
                <label for="foto">Foto de perfil</label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </div>

            <div class="separador-perfil"></div>

            <div class="form-group">
                <label for="usuari">Cambiar nom d'usuari</label>
                <input type="text" name="usuari" id="usuari" placeholder="Cambiar nom d'usuari">
            </div>
            <div class="form-group">
                <label for="usuari">Cambiar contrasenya</label>
                <input type="password" name="psswrd" id="psswrd" placeholder="Cambiar contrasenya">
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
