<?php
// Carga la configuración básica
require_once __DIR__ . "/../config/bootstrap.php";
?>

<!DOCTYPE html>
<html lang="es">

<?php
$title_name = "Bienvenido a MarginLab";
$page_css = "home.css"; // Cargamos los estilos de la landing
require_once BASE_DIR . "/public/templates/head.php";
?>

<body>

  <?php require_once BASE_DIR . "/public/templates/header.php" ?>

  <main class="landing">

    <section class="hero-section">
      <h1>Bienvenido a <span>MarginLab</span></h1>

      <p>
        Herramienta de análisis y cálculo de márgenes comerciales.
        Optimiza presupuestos, controla costes y asegura la rentabilidad
        de tus operaciones en tiempo real.
        <br><br>
        <em>Versión Open Source para demostración técnica.</em>
      </p>

      <a href="<?= BASE_URL ?>/buscador" class="cta-button">
        Iniciar MarginLab Playground 🚀
      </a>
    </section>

  </main>

  <?php require_once BASE_DIR . "/public/templates/footer.php" ?>

</body>

</html>