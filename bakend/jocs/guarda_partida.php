<?php
require_once './datosservidor.php';

$data = json_decode(file_get_contents("php://input"), true);
$usuari_id = $data['usuari_id'] ?? null;
$joc_id = $data['joc_id'] ?? 1;
$nivell_final = $data['nivell_final'] ?? 1;
$punts = $data['punts'] ?? 0;

$sql = "INSERT INTO partides (usuari_id, joc_id, nivell_final, punts, data_partida) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuari_id, $joc_id, $nivell_final, $punts);
$stmt->execute();

echo json_encode(["status" => "ok", "message" => "Partida guardada"]);
?>