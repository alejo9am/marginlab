<?php

//VERIFICAR QUE EL USUARIO ESTÁ AUTENTICADO Y CREAR CONEXIÓN A LA BASE DE DATOS PDO
$pdo = require_once __DIR__ . "/../../config/bootstrap.php";


//OBTENER CODIGOS DE OFERTA DE LA BASE DE DATOS
try {
  if (isset($_SESSION['sandbox_prefix'])) {
    // Buscamos solo los presupuestos que coincidan con el prefijo del usuario.
    // Usamos SUBSTRING para quitarle el prefijo y mostrar solo el ID "bonito" (ej: 10001)
    $prefix = $_SESSION['sandbox_prefix'];

    $query = "SELECT DISTINCT SUBSTRING(cod_presupuesto, LENGTH(:prefix) + 1) as id_visual 
                  FROM presupuestos 
                  WHERE cod_presupuesto LIKE :pattern 
                  ORDER BY cod_presupuesto ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
      ':prefix' => $prefix,
      ':pattern' => $prefix . '%'
    ]);

    $codigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  // Si falla la carga de la lista, no rompemos la página, solo dejamos la lista vacía
  error_log("Error cargando buscador: " . $e->getMessage());
  $codigos = [];
}


//PROCESAMIENTO DE FORMULARIO
if (isset($_POST['cod_presupuesto'])) {
  $cod_presupuesto = limpiar_dato($_POST['cod_presupuesto']);

  //consulta para contar las distintas versiones del presupuesto
  $query = "SELECT count(DISTINCT version) FROM articulos WHERE cod_presupuesto = :cod_presupuesto";

  // Preparar y ejecutar la consulta con PDO
  $stmt = $pdo->prepare($query);
  $stmt->execute([':cod_presupuesto' => $cod_presupuesto]);
  $nversiones = $stmt->fetchColumn();

  $id_para_url = $cod_presupuesto;
  if (isset($_SESSION['sandbox_prefix'])) {
    $id_para_url = str_replace($_SESSION['sandbox_prefix'], '', $cod_presupuesto);
  }

  if ($nversiones == 0) {
    redirigir("errores");
  } elseif ($nversiones == 1) {
    redirigir("calculadora/?id=$id_para_url&version=1");
  } else {
    redirigir("buscador/select.php?id=$id_para_url");
  }
}

?>

<!DOCTYPE html>
<html lang="es">
<?php
$title_name = "Buscador de Presupuestos";
$page_css = "modules/buscador.css";
require_once BASE_DIR . "/public/templates/head.php";
?>

<body>

  <?php require_once BASE_DIR . "/public/templates/header.php" ?>
  <main>
    <section class="sect">
      <form id="formBusqueda" method="post" action="./index.php">
        <h2>Introduce el identificador del presupuesto</h2>
        <svg width="1440" height="2" viewBox="0 0 1440 2" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H1440" stroke="black" />
        </svg>
        <div class="bottom">
          <datalist id="codigos">
            <?php foreach ($codigos as $codigo) { ?>
              <option value="<?= $codigo['id_visual'] ?>">
              <?php } ?>
          </datalist>

          <div class="campo-input">
            <input type="text" name="cod_presupuesto" list="codigos" required autocomplete="off" placeholder="Ej: 10001">
            <label>Código de presupuesto</label>
          </div>

          <button type="submit" id="buscar">BUSCAR</button>
        </div>
      </form>
    </section>


  </main>

  <?php require_once BASE_DIR . "/public/templates/footer.php" ?>

</body>

</html>