<?php

require_once __DIR__ . "/../../config/bootstrap.php";

?>

<!DOCTYPE html>
<html lang="es">

<?php
  $title_name = "Error - MarginLab";
  require_once BASE_DIR . "/public/templates/head.php";
?>

<body>

  <?php require_once BASE_DIR . "/public/templates/header.php" ?>

  <main>

    <section class="sect">
      <div class="denegado">
        <h3>
          404 - Página no encontrada
        </h3>
        <a id="volver" href="<?= $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php'?>">
          <img src="<?= BASE_URL ?>/img/back.svg" alt="Volver" class="flecha">
          <span>Volver</span>
        </a>
      </div>
    </section>

  </main>

  <?php require_once BASE_DIR . "/public/templates/footer.php" ?>

</body>

</html>