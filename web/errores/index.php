<?php

$pdo = require_once __DIR__ . "/../../config/bootstrap.php";

?>

<!DOCTYPE html>
<html lang="es">

<?php
  $title_name = "Error - MarginLab";
  $page_css = "modules/errores.css";
  require_once BASE_DIR . "/web/templates/head.php";
?>

<body>

  <?php require_once BASE_DIR . "/web/templates/header.php" ?>

  <main>
    
      <div class="denegado">
        <h3>
          404 - Página no encontrada
        </h3>
        <a id="volver" href="<?= $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php'?>">
          <img src="<?= BASE_URL ?>/img/back.svg" alt="Volver" class="flecha">
          <span>Volver</span>
        </a>
      </div>

  </main>

  <?php require_once BASE_DIR . "/web/templates/footer.php" ?>

</body>

</html>