<?php
require_once(__DIR__ . '/datosservidor.php');
$sql = "SELECT u.nom_complet, p.puntuacio_maxima
        FROM progres_usuari p
        JOIN usuaris u ON p.usuari_id = u.id
        ORDER BY p.puntuacio_maxima DESC
        LIMIT 5";
$result = $conn->query($sql);
$pos = 1;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>🏆 Ranking Galàctic</title>
  <link rel="stylesheet" href="/css_base/ranking.css">
</head>
<body>
  <div class="contenedor">
    <h1>🏆 Rànquing de Jugadors</h1>
    <table class="ranking">
      <thead>
        <tr>
          <th>#</th>
          <th>Nom complet</th>
          <th>Puntuació</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $pos ?></td>
            <td><?= htmlspecialchars($row['nom_complet']) ?></td>
            <td><?= $row['puntuacio_maxima'] ?></td>
          </tr>
          <?php $pos++; ?>
        <?php endwhile; ?>
      </tbody>
    </table>
    <a href="/joc/index.php" class="volver">🔙 Tornar al joc</a>
  </div>
</body>
</html>
