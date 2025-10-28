<?php
// db_mysqli.php
// Dades de connexió
$host = "172.18.33.247"; // usa localhost si el servidor MySQL está en la misma máquina
$user = "usuariweb";
$password = "password123"; // sin contraseña
$database = "plataforma_videojocs";

// Connexió MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Comprovació de la connexió
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

#echo "La connexió a la BD s'ha realitzat amb èxit!";
?>