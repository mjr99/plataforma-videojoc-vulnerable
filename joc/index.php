<?php
session_start();
$nom_usuari = $_SESSION['usuario'] ?? 'Jugador';
?>
<!DOCTYPE html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Joc interactiu HTML, CSS i JavaScript</title>
    <meta name="description" content="Joc JS per treballar la manipulació del DOM, la gestió d'esdeveniments i la POO." />
    <meta name="author" content="Pau Torres y Marc Jimenez" />
    <meta name="copyright" content="Pau Torres y Marc Jimenez" />
    <link rel="stylesheet" href="./css/index.css" />
    <script>
      window.config = <?php echo json_encode(['nomUsuari' => $nom_usuari,]); ?>;
    </script>
    <script src="./js/classes.js" defer></script>
    <script src="./js/main.js" defer></script>
  </head>
  <body>
    <div id="pantalla"></div>
    <div class="cabecera-nav">
      <button class="atras" onclick="history.back()"></button>
      <button class="reiniciar" onclick="location.reload()"></button>
    </div>
    <div id="infoPartida">
    </div>
  </body>
</html>