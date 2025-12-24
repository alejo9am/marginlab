<?php

//VERIFICAR QUE EL USUARIO ESTÁ AUTENTICADO Y CREAR CONEXIÓN A LA BASE DE DATOS PDO
require_once __DIR__ . "/../../config/bootstrap.php";

//OBTENER CODIGOS DE OFERTA DE LA BASE DE DATOS
$query = "SELECT DISTINCT cod_oferta FROM lineas_oferta";
$stmt = $pdo->query($query);
$codigos = $stmt->fetchAll(PDO::FETCH_ASSOC);


//PROCESAMIENTO DE FORMULARIO
if (isset($_POST['cod_presupuesto'])) {
  $cod_presupuesto = $_POST['cod_presupuesto'];

  $query = "SELECT DISTINCT version FROM lineas_oferta WHERE cod_oferta = :cod_oferta ORDER BY version DESC";
            
  // Preparar y ejecutar la consulta con PDO
  $stmt = $pdo->prepare($query);
  $stmt->execute([':cod_oferta' => $cod_presupuesto]);

  // Obtener resultados
  $filaPresupuesto = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $nversiones = sizeof($filaPresupuesto);

  if ($nversiones == 0){
    header("Location: ../error/index.php?cod=notfound");
    exit();
  } elseif($nversiones == 1) {
    if (1==1) {
    //if (permitido($depart, $user)){

      if($depart=='VENTAS') {
        header("Location: ../ventas/index.php?id=$cod_presupuesto&version=0");
        exit();
      }
      else {
        header("Location: ../compras/index.php?id=$cod_presupuesto&version=0");
        exit();
      }
      
    } else {
      header("Location: ../error/index.php?cod=denied");
      exit();
    }
  } else {
    header("Location: select.php?id=$cod_presupuesto");
    exit();
  }

}

// // Cierre de conexión opcional (PDO se cierra automáticamente al finalizar el script)
// $pdo = null;

?>

<!DOCTYPE html>
<html lang="es">
<?php require_once "../templates/head.php" ?>
<body>

    <?php require_once "../templates/header.php" ?>

    <main>
        <section class="sect">
            <form id="formBusqueda" method="post" action="./index.php">
                <h2 >Introduce el identificador del presupuesto</h2>
                <svg width="1440" height="2" viewBox="0 0 1440 2" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 1H1440" stroke="black"/>
                </svg>
                <div class="bottom">
                    <datalist id="codigos">
                        <!--BUCLE PHP PARA IMPRIMIR LOS CODIGOS DE cod_oferta-->
                        <?php foreach ($codigos as $codigo) { ?>
                            <option value="<?= $codigo['cod_oferta'] ?>">
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

    <?php require_once "../templates/footer.php" ?>
    
</body>
</html>