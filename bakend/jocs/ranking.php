<?php
require_once './datosservidor.php';

$sql = "SELECT u.nom_usuari, p.punts, p.data_partida 
        FROM partides p 
        JOIN usuaris u ON p.usuari_id = u.id 
        ORDER BY p.punts DESC 
        LIMIT 10";

$result = $conn->query($sql);
$ranking = [];

while ($row = $result->fetch_assoc()) {
    $ranking[] = $row;
}

header('Content-Type: application/json');
echo json_encode($ranking);
?>
