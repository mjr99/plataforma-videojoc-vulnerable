<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "./datosservidor.php";

// ❌ No se valida la entrada
$joc_id = $_GET['joc'] ?? 1;
$nivell_num = $_GET['nivell'] ?? 1;

// ❌ Consulta SQL vulnerable a inyección
$sql = "SELECT configuracio_json, nom_nivell, puntuacio_minima FROM nivells_joc WHERE joc_id = $joc_id AND nivell = $nivell_num LIMIT 1";
$res = $conn->query($sql);
if ($res->num_rows === 0) die("Nivell no trobat.");
$row = $res->fetch_assoc();

// ❌ No se valida el JSON
$config = json_decode($row['configuracio_json'], true);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Play - <?php echo $row['nom_nivell']; ?></title> <!-- ❌ Sin escape -->
</head>
<body style="background:#000;color:#fff;">
    <h1><?php echo $row['nom_nivell']; ?></h1> <!-- ❌ Sin escape -->
    <p>Configuració llegida (JSON):</p>
    <pre><?php echo json_encode($config, JSON_PRETTY_PRINT); ?></pre> <!-- ❌ Sin escape -->

    <canvas id="jocCanvas" width="800" height="600" style="border:1px solid white;"></canvas>

    <script>
    const CONFIG = <?php echo json_encode($config); ?>;
    const puntuacioMinima = <?php echo $row['puntuacio_minima']; ?>;
    let puntuacio = 0;

    const canvas = document.getElementById("jocCanvas");
    const ctx = canvas.getContext("2d");

    CONFIG.enemics.forEach(enemic => {
        for (let i = 0; i < enemic.quantitat; i++) {
            const x = Math.random() * canvas.width;
            const y = Math.random() * canvas.height;
            ctx.fillStyle = "red";
            ctx.fillRect(x, y, 20, 20);
        }
    });

    // Simular puntuació i avançar de nivell (inseguro)
    setTimeout(() => {
        puntuacio = parseInt(prompt("Introdueix la teva puntuació:"));
        if (puntuacio >= puntuacioMinima) {
            const next = <?php echo $nivell_num + 1; ?>;
            window.location.href = `play.php?joc=<?php echo $joc_id; ?>&nivell=${next}`;
        } else {
            alert("No has arribat a la puntuació mínima. Torna-ho a intentar.");
        }
    }, 3000);
    </script>
</body>
</html>