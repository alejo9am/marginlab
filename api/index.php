<?php
// Carga la configuración básica
$pdo = require_once __DIR__ . "/../config/bootstrap.php";
?>

<!DOCTYPE html>
<html lang="es">

<?php
$title_name = "MarginLab - Control de Márgenes";
$page_css = "home.css";
require_once BASE_DIR . "/api/templates/head.php";
?>

<body class="landing-body">

  <?php require_once BASE_DIR . "/api/templates/header.php" ?>

  <main class="landing-container">

    <section class="hero-content">
      <h1 class="hero-title">Control total sobre tus <br>márgenes comerciales</h1>

      <p class="hero-subtitle">
        Automatiza cálculos complejos de descuentos en cascada y rappels. <br>
        Una adaptación pública de software real para demostración técnica.
      </p>

      <div class="cta-wrapper">
        <a href="<?= BASE_URL ?>/actions/initSandbox.php" class="btn-primary-lg">
          Iniciar Demo Privada
        </a>
        <p class="micro-copy">Sin registro • Sesión aislada y segura • Datos efímeros</p>
      </div>
    </section>

  </main>

  <?php require_once BASE_DIR . "/api/templates/footer.php" ?>

</body>

</html>