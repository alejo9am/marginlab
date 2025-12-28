<?php

require_once "../../config/bootstrap.php";

$id = limpiar_dato($_GET['id']);

$query = "SELECT DISTINCT version, nombre_version FROM presupuestos WHERE cod_presupuesto= :cod_presupuesto ORDER BY version DESC";

//ejecutar la consulta
$stmt = $pdo->prepare($query);
$stmt->execute([':cod_presupuesto' => $id]);
//vector con las versiones
$versiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['version'])) {            

  $versionSeleccionada = limpiar_dato($_POST['version']);

  redirigir("calculadora/index.php?id=$id&version=$versionSeleccionada");
}


?>

<!DOCTYPE html>
<html lang="es">

<?php
  $title_name = "Seleccionar Versión del Presupuesto";
  $page_css = "modules/buscador.css";
  require_once BASE_DIR . "/public/templates/head.php";
?>

<body>

    <?php require_once BASE_DIR . "/public/templates/header.php" ?>

    <main>

        <section class="sect">
            <form id="formBusqueda" method="post" action="./select.php?id=<?=$id?>">
                <h2 >Selecciona la versión del presupuesto</h2>
                <svg width="1440" height="2" viewBox="0 0 1440 2" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 1H1440" stroke="black"/>
                </svg>
                <div class="bottom">
                    <select name="version">
                        <?php foreach ($versiones as $version) { ?>
                            <option value="<?=$version['version']?>">
                                V<?=$version['version']?> - <?=$version['nombre_version'] ?? 'Versión ' . $version['version'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button type="submit" id="buscar">BUSCAR</button>
                </div>
            </form>
        </section>
        

    </main>

    <?php require_once BASE_DIR . "/public/templates/footer.php" ?>
    
</body>
</html>