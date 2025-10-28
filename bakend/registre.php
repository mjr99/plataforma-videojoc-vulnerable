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
                
                <div class="links">
                    <a href="./index.php">Iniciar sessió</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
<?php
session_start();
#echo "✅ Sesión iniciada<br>";

// Incluye el archivo de conexión
require_once './jocs/datosservidor.php';
#echo "✅ Conexión incluida<br>";

// Verifica que la conexión esté activa
if (!$conn) {
    die("❌ Error de conexión con la base de datos<br>");
}
#echo "✅ Conexión con la base de datos OK<br>";

// Recoge los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$contrasenya = $_POST['contrasenya'] ?? '';

#echo "📥 Datos recibidos:<br>";
#echo "Nombre: $nombre<br>";
#echo "Email: $email<br>";
#echo "Usuario: $usuario<br>";
#echo "Contraseña: (oculta)<br>";

// Validación básica
if (empty($nombre) || empty($email) || empty($usuario) || empty($contrasenya)) {
    die("❌ Faltan campos por rellenar<br>");
}
#echo "✅ Todos los campos están completos<br>";

// Encripta la contraseña
$hash = password_hash($contrasenya, PASSWORD_DEFAULT);
#echo "🔐 Contraseña encriptada<br>";

// Prepara el INSERT
$sql = "INSERT INTO usuaris (nom_complet, email, nom_usuari, password_hash) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Error preparando la consulta: " . $conn->error . "<br>");
}
#echo "✅ Consulta preparada<br>";

// Asocia los parámetros
$stmt->bind_param("ssss", $nombre, $email, $usuario, $hash);
#echo "✅ Parámetros vinculados<br>";

// Ejecuta el INSERT
if ($stmt->execute()) {
    #echo "✅ Usuario insertado correctamente<br>";

    // Crea la sesión
    $_SESSION['usuario'] = $usuario;
    $_SESSION['email'] = $email;
    $_SESSION['nombre'] = $nombre;
    #echo "✅ Sesión creada<br>";

    // Redirige
    #echo "➡️ Redirigiendo a dashboard.php...<br>";
    header("Location: ./../index.php");
    exit();
} else {
    #echo "❌ Error al insertar el usuario: " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();
#echo "✅ Conexión cerrada<br>";
?>