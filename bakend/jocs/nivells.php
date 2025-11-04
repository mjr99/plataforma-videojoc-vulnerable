<?php
require_once 'datosservidor.php';

$joc_id = $_GET['joc_id'] ?? 1;

$sql = "SELECT nivell, nom_nivell, configuracio_json, puntuacio_minima FROM nivells_joc WHERE joc_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $joc_id);
$stmt->execute();
$result = $stmt->get_result();

$nivells = [];
while ($row = $result->fetch_assoc()) {
    $nivells[] = $row;
}

header('Content-Type: application/json');
echo json_encode($nivells);
?>
