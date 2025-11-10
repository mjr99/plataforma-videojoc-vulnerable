<?php
// Asegúrate de que este path sea correcto para tu entorno
require_once(__DIR__ . '/datosservidor.php');

// Consulta para obtener el top 5
$sql = "SELECT u.nom_complet, p.puntuacio_maxima
        FROM progres_usuari p
        JOIN usuaris u ON p.usuari_id = u.id
        ORDER BY p.puntuacio_maxima DESC
        LIMIT 5";
$result = $conn->query($sql);
$pos = 1;

// Cierra la conexión si existe
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏆 Ranking Galàctic</title>
    <!-- Usa el nuevo CSS -->
    <link rel="stylesheet" href="/css_base/ranking.css">
    <!-- Importamos la fuente Quicksand -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Contenedor principal con el estilo del recuadro grande -->
    <div class="perfil-container">
        
        <!-- TÍTULO PRINCIPAL (Adaptado del estilo de perfil) -->
        <div class="titulo">
            <div class="cabecera-nav">
                <!-- Botón de volver atrás con el estilo de flecha -->
                <button class="atras" onclick="history.back()"></button>
            </div>
            <h1>🏆 Rànquing de Jugadors</h1>
        </div>

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
                    <tr class="<?= ($pos == 1) ? 'first-place' : (($pos == 2) ? 'second-place' : (($pos == 3) ? 'third-place' : '')) ?>">
                        <td class="pos"><?= $pos ?></td>
                        <td><?= htmlspecialchars($row['nom_complet']) ?></td>
                        <td class="score"><?= $row['puntuacio_maxima'] ?></td>
                    </tr>
                    <?php $pos++; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Botón de volver con el estilo gradiente -->
        <a href="/joc/index.php" class="volver guardar">↻ Tornar al joc ↻</a>
        
    </div>
</body>
</html>