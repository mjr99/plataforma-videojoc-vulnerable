<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 // Asegúrate de que este archivo conecta correctamente
require_once(__DIR__ . '/datosservidor.php'); // ✅ correcto



// 🔍 Trazas iniciales
error_log("✅ guarda_progres_jugador.php llamado");

// Leer datos JSON enviados desde el juego
$data = json_decode(file_get_contents("php://input"), true);
$nomUsuari = $data['nomUsuari'] ?? null;
$punts = $data['punts'] ?? null;
$joc_id = 1;

// 🔍 Verificar datos recibidos
error_log("📦 Datos recibidos: nomUsuari=$nomUsuari, punts=$punts");

if (!$nomUsuari || !is_numeric($punts)) {
    error_log("❌ Datos inválidos: nomUsuari=$nomUsuari, punts=$punts");
    http_response_code(400);
    exit;
}

// 🔍 Buscar ID del usuario
$sql = "SELECT id FROM usuaris WHERE nom_usuari = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nomUsuari);
$stmt->execute();
$result = $stmt->get_result();
$fila = $result->fetch_assoc();
$usuari_id = $fila['id'] ?? null;

// 🔍 Verificar resultado de búsqueda
error_log("🔍 Resultado ID: usuari_id=$usuari_id");

if (!$usuari_id) {
    error_log("❌ Usuario no encontrado: $nomUsuari");
    http_response_code(404);
    exit;
}

// 🔧 Insertar o actualizar puntuación máxima
$sql = "INSERT INTO progres_usuari (usuari_id, joc_id, puntuacio_maxima)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
          puntuacio_maxima = GREATEST(puntuacio_maxima, VALUES(puntuacio_maxima))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuari_id, $joc_id, $punts);

if ($stmt->execute()) {
    error_log("✅ Progreso guardado: usuari_id=$usuari_id, joc_id=$joc_id, punts=$punts");
    http_response_code(200);
} else {
    error_log("❌ Error al guardar: " . $stmt->error);
    http_response_code(500);
}

$stmt->close();
$conn->close();
