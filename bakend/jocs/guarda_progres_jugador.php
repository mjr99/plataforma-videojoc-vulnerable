<?php
session_start();
require_once 'datosservidor.php';

$data = json_decode(file_get_contents("php://input"), true);
$nivell = $data['nivell'] ?? 1;
$punts = $data['punts'] ?? 0;
$joc_id = 1; // ID del juego STARBLAST
$id = $_SESSION['usuari_id'] ?? null;

if ($id) {
    $sql = "INSERT INTO progres_usuari (usuari_id, joc_id, nivell_actual, puntuacio_maxima, partides_jugades, ultima_partida)
            VALUES (?, ?, ?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE 
              nivell_actual = VALUES(nivell_actual),
              puntuacio_maxima = GREATEST(puntuacio_maxima, VALUES(puntuacio_maxima)),
              partides_jugades = partides_jugades + 1,
              ultima_partida = NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $id, $joc_id, $nivell, $punts, $partides = 1);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
