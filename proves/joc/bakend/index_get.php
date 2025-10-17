<?php
// index_get.php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $nom = $_GET['nom'] ?? '';
  $preu = $_GET['preu'] ?? 0;
  $rol = $_GET['rol'] ?? 'usuari';

  echo "<h2>Resultats de la cerca</h2>";
  echo "<p>Nom del producte: $nom </p>";
  echo "<p>Preu: $preu € </p>";

    if($rol === "admin") {
        echo "<h1>benvingut admin</h1>";
    }

    if($rol === "usuari") {
        echo "<h3>benvingut a la plebe!!</h3>";
    }
}
?>