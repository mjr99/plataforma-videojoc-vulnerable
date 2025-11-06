<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye el archivo de conexión
require_once '/var/www/html/bakend/jocs/datosservidor.php';

// Verifica que la conexión esté activa
if (!$conn) {
    die("❌ Error de conexión con la base de datos<br>");
}

// =================================================================
// 1. OBTENER ID DEL USUARIO DESDE LA SESIÓN
// =================================================================
$userId = $_SESSION['usuari_id'] ?? null; 

if (!$userId) {
    // Si no hay ID de usuario en sesión, redirigir a login
    header('Location: ./../../index.php'); 
    exit();
}
// Obtenemos el nombre de usuario de la sesión para el título del perfil
$nom_usuari_session = $_SESSION['usuario'] ?? 'usuari';


// =================================================================
// FASE DE LECTURA DE DATOS DE PERFIL (Para mostrar en el formulario)
// =================================================================
$apodo_actual = '';
$bio_actual = '';
$foto_actual = '';
$perfilExiste = false;

// Intentamos obtener el perfil del usuario actual (ID 1)
$sql_select = "SELECT apodo, foto_perfil, bio FROM perfil_usuario WHERE id = ?";
$stmt_select = $conn->prepare($sql_select);

if (!$stmt_select) {
    die("❌ Error preparando la consulta de lectura de perfil: " . $conn->error . "<br>");
}

$stmt_select->bind_param("i", $userId);
$stmt_select->execute();
$resultado = $stmt_select->get_result();

if ($resultado->num_rows > 0) {
    // Si se encuentra un registro, cargamos los datos y marcamos que el perfil existe
    $datos = $resultado->fetch_assoc();
    $apodo_actual = htmlspecialchars($datos['apodo'] ?? '');
    $bio_actual = htmlspecialchars($datos['bio'] ?? '');
    $foto_actual = htmlspecialchars($datos['foto_perfil'] ?? '');
    $perfilExiste = true;
}
$stmt_select->close();


// ==============================================================
// FASE DE PROCESAMIENTO POST (Guardar Perfil o Credenciales) ===
// ==============================================================

// 2. Recolección de datos del formulario POST
$apodo = $_POST['apodo'] ?? $apodo_actual;
$bio = $_POST['bio'] ?? $bio_actual;
$usuari = $_POST['usuari'] ?? '';
$psswrd = $_POST['psswrd'] ?? '';
$foto = $_FILES['foto'] ?? null;
$rutaFoto = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['tencar_sessio'])) {
        session_destroy();
        header("Location: ./../../index.php");
        exit();
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
            $set_clauses[] = "password_hash = ?";
            $params[] = &$psswrd;
            $types .= 's';
        }

        // Construir y ejecutar la consulta de credenciales
        $sql_update_login = "UPDATE usuaris SET " . implode(", ", $set_clauses) . " WHERE id = ?";
        $types .= 'i';
        $params[] = &$userId;
        
        $stmt_login = $conn->prepare($sql_update_login);
        if (!$stmt_login) {
            die("❌ Error preparando la consulta de credenciales: " . $conn->error . "<br>");
        }
        
        call_user_func_array([$stmt_login, 'bind_param'], array_merge([$types], $params));

        if ($stmt_login->execute()) {
            $stmt_login->close();
            // Redirigir a login tras cambio de credenciales
            header('Location: ./../../index.php');
            exit();
        } else {
            die("❌ Error al actualizar credenciales: " . $stmt_login->error . "<br>");
        }
    } 
    
    // B. Lógica de GUARDAR PERFIL (INSERT o UPDATE)
    
    // Solo procedemos si el usuario envió datos para el perfil (apodo o bio)
    if (!empty($apodo) || !empty($bio) || ($foto && $foto['tmp_name'])) { 
        
        // La foto_perfil siempre debe tener un valor. Usamos la actual si no se sube una nueva.
        $rutaFoto = $foto_actual; 
        
        // 1. Procesar imagen si se ha subido
        if ($foto && $foto['tmp_name']) {
            $nombreArchivo = uniqid() . '_' . basename($foto['name']);
            $rutaServidor = '/var/www/html/uploads/' . $nombreArchivo;
            $rutaWeb = 'uploads/' . $nombreArchivo; 

            if (!move_uploaded_file($foto['tmp_name'], $rutaServidor)) {
                die("❌ Error al subir la imagen. Verifica permisos y carpeta.<br>");
            }
            $rutaFoto = $rutaWeb; // Sobrescribe la ruta actual con la nueva
        }

        // Asignamos los valores recibidos o los valores actuales si el campo está vacío en el post
        $apodo_final = !empty($apodo) ? $apodo : $apodo_actual;
        $bio_final = !empty($bio) ? $bio : $bio_actual;
                
        // 2. Determinar si es INSERT o UPDATE
        if ($perfilExiste) {
            // OPCIÓN 2: El perfil YA existe -> Usamos UPDATE
            $sql = "UPDATE perfil_usuario SET apodo = ?, foto_perfil = ?, bio = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $apodo, $rutaFoto, $bio, $userId);
        } else {
            // OPCIÓN 1: El perfil NO existe -> Usamos INSERT
            $sql = "INSERT INTO perfil_usuario (apodo, foto_perfil, bio, id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $apodo, $rutaFoto, $bio, $userId);
        }
        
        if (!$stmt) {
            die("❌ Error preparando la consulta de perfil: " . $conn->error . "<br>");
        }
        
        if ($stmt->execute()) {
            $stmt->close();
            // Forzamos la actualización de la variable de estado después de un INSERT exitoso
            $perfilExiste = true; 
            // Redirigir a la misma página para ver el mensaje de éxito
            header("Location: perfil.php?status=success");
            exit();
        } else {
            die("❌ Error al guardar/actualizar el perfil: " . $stmt->error . "<br>");
        }
    }
} 

$conn->close();

// Mensaje de éxito si viene de la redirección GET
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
        <!-- TÍTULO PRINCIPAL DENTRO DEL RECUADRO -->
        <div class="titulo">
            <h1>🎮 Personalitza el teu perfil 🎮</h1>
        </div>

        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <!-- INICIO DEL CONTENEDOR DE DOBLE COLUMNA -->
            <div class="perfil-grid">
                
                <!-- COLUMNA IZQUIERDA: Imagen y Nombre de Usuario -->
                <div class="columna-izquierda">
                    
                    <!-- 1. INPUT FILE OCULTO -->
                    <!-- Damos a este input el ID 'foto-upload' y lo vinculamos a la etiqueta que envolverá la imagen -->
                    <input type="file" name="foto" id="foto-upload" accept="image/*" style="display: none;">

                    <!-- 2. RECUADRO DE IMAGEN COMO ÁREA CLICABLE -->
                    <!-- La etiqueta 'label' apunta al input oculto, haciendo que el clic funcione -->
                    <label for="foto-upload" class="imagen-label">
                        <div class="imagen-recuadro">
                            <img src="/<?= $foto_actual ?: 'uploads/default.jpg' ?>" alt="Foto de perfil">
                            <!-- Icono o texto para indicar que se puede cambiar la foto (opcional, para accesibilidad) -->
                            <div class="cambiar-foto-overlay">
                                📸
                            </div>
                        </div>
                    </label>
                    
                    <!-- Pie de imagen (Nombre de usuario) -->
                    <h2 class="nombre-usuario"><?= $nom_usuari_session ?></h2>

                    <div class="estadisticas">
                        <h2> IN COMING... </h2>
                    </div>

                </div>

                <!-- SEPARADOR VERTICAL (para que se vea entre las dos columnas) -->
                <div class="separador-vertical"></div>

                <!-- COLUMNA DERECHA: Todos los formularios y botones -->
                <div class="columna-derecha">
                    
                    <!-- 1. CAMBIAR USUARIO Y CONTRASEÑA -->
                    <div class="form-group">
                        <input type="text" name="usuari" id="usuari" placeholder="Cambiar nom d'usuari">
                    </div>
                    <div class="form-group">
                        <input type="password" name="psswrd" id="psswrd" placeholder="Cambiar contrasenya">
                    </div>
                    
                    <!-- SEPARADOR HORIZONTAL -->
                    <div class="separador-perfil"></div>

                    <!-- 2. APODO, FOTO Y BIOGRAFÍA -->
                    <div class="form-group">
                        <input type="text" name="apodo" id="apodo" placeholder="<?= $perfilExiste ? 'Cambiar apodo (' . $apodo_actual . ')' : 'Tria un apodo' ?>"value="<?= $apodo_actual ?>">
                    </div>
                    <!--<div class="form-group">
                        <input type="file" name="foto" id="foto" accept="image/*">
                    </div>-->
                    <div class="form-group">
                        <textarea name="bio" id="bio" rows="4" placeholder="<?= $perfilExiste ? 'Cambiar biografia (' . $bio_actual . ')' : 'Explica alguna cosa sobre tu...' ?> "value=""><?= $bio_actual ?></textarea>
                    </div>

                    <!-- 4. BOTONES (Contenedor al final de la columna derecha) -->
                    <div class="contenedor-botones">
                        <!-- Usamos las clases originales del botón: .guardar y .tencar -->
                        <button type="submit" name="guardar_perfil" class="guardar">💾 Guardar perfil</button> 
                        <button type="submit" name="tencar_sessio" class="tencar">⛔ Tencar sessio</button> 
                    </div>
                </div>
            </div> <!-- Fin de perfil-grid -->
        </form>
</body>
</html>
