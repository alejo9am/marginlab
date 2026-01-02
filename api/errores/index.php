<?php

$pdo = require_once __DIR__ . "/../../config/bootstrap.php";

?>

<!DOCTYPE html>
<html lang="es">

<?php
$title_name = "Error - MarginLab";
$page_css = "modules/errores.css";
require_once BASE_DIR . "/api/templates/head.php";
?>

<body>

  <?php require_once BASE_DIR . "/api/templates/header.php" ?>

  <main>

    <div class="denegado">
      <h3>
        404 - Página no encontrada
      </h3>
      <a id="volver" href="<?= $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php' ?>">
        <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path fill="#f0eeee" d="m3.825 9 5.6 5.6L8 16 0 8l8-8 1.425 1.4-5.6 5.6H16v2z"/></svg>
        <span>Volver</span>
      </a>
    </div>

  </main>

  <?php require_once BASE_DIR . "/api/templates/footer.php" ?>

</body>

</html>