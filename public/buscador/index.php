<?php

//VERIFICAR QUE EL USUARIO ESTÁ AUTENTICADO Y CREAR CONEXIÓN A LA BASE DE DATOS PDO
$pdo = require_once __DIR__ . "/../../config/bootstrap.php";

//OBTENER CODIGOS DE OFERTA DE LA BASE DE DATOS
$query = "SELECT DISTINCT cod_presupuesto FROM articulos ORDER BY cod_presupuesto ASC";
$stmt = $pdo->query($query);
$codigos = $stmt->fetchAll(PDO::FETCH_ASSOC);


//PROCESAMIENTO DE FORMULARIO
if (isset($_POST['cod_presupuesto'])) {
  $cod_presupuesto = limpiar_dato($_POST['cod_presupuesto']);

  //consulta para contar las distintas versiones del presupuesto
  $query = "SELECT count(DISTINCT version) FROM articulos WHERE cod_presupuesto = :cod_presupuesto";
            
  // Preparar y ejecutar la consulta con PDO
  $stmt = $pdo->prepare($query);
  $stmt->execute([':cod_presupuesto' => $cod_presupuesto]);
  $nversiones = $stmt->fetchColumn();

  if ($nversiones == 0){
    redirigir("errores");
  } elseif($nversiones == 1) {
    redirigir("calculadora/?id=$cod_presupuesto&version=1");
  } else {
    redirigir("buscador/select.php?id=$cod_presupuesto");
  }

}

// Cierre de conexión opcional (PDO se cierra automáticamente al finalizar el script)
$pdo = null;

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
                <h2 >Introduce el identificador del presupuesto</h2>
                <svg width="1440" height="2" viewBox="0 0 1440 2" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 1H1440" stroke="black"/>
                </svg>
                <div class="bottom">
                    <datalist id="codigos">
                        <!--BUCLE PHP PARA IMPRIMIR LOS CODIGOS DE cod_presupuesto-->
                        <?php foreach ($codigos as $codigo) { ?>
                            <option value="<?= $codigo['cod_presupuesto'] ?>">
                        <?php } ?>
                    </datalist>

                    <div class="campo-input">
                        <input type="text" name="cod_presupuesto" list="codigos" required autocomplete="off">
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