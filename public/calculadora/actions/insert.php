<?php

    //PHP para insertar valores modficados -----> CREAR UNA NUEVA VERSIÓN

    require_once "../../../config/bootstrap.php";

    $n = limpiar_dato($_GET['n']); //numero de articulos
    $cod_presupuesto = limpiar_dato($_GET['id']); //id de la oferta
    $version = limpiar_dato($_GET['v']); //version de la oferta


    //OBTENER LA ÚLTIMA VERSIÓN DE LA OFERTA
    $query = "SELECT version FROM presupuestos WHERE cod_presupuesto = :cod_presupuesto ORDER BY version DESC LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':cod_presupuesto' => $cod_presupuesto]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $last = $row['version'];
    } else {
        // Si no hay versiones previas, empezamos en 0 o manejamos el error
        $last = 0; 
    }

    $insertVers = $last + 1;

    try {
        $pdo->beginTransaction();

        // 1. Insertar en la tabla `presupuestos` (Cabecera)
        
        $nombre_version = $_POST["nombre_version"];
        $obs_comercial = $_POST['obsComercial'];
        $obs_revision = $_POST['obsRevision'];
        $cod_cliente = $_POST['cod_cliente'];
        $nomb_cliente = $_POST['nomb_cliente'];
        $referencia = $_POST['referencia'];
        $obra = $_POST['obra'];

        $queryPresupuesto = "INSERT INTO presupuestos (cod_presupuesto, version, nombre_version, obs_comercial, obs_revision, cod_cliente, nomb_cliente, referencia, obra) 
                             VALUES (:cod_presupuesto, :version, :nombre_version, :obs_comercial, :obs_revision, :cod_cliente, :nomb_cliente, :referencia, :obra)";
        
        $stmtPresupuesto = $pdo->prepare($queryPresupuesto);
        $stmtPresupuesto->execute([
            ':cod_presupuesto' => $cod_presupuesto,
            ':version' => $insertVers,
            ':nombre_version' => $nombre_version,
            ':obs_comercial' => $obs_comercial,
            ':obs_revision' => $obs_revision,
            ':cod_cliente' => $cod_cliente,
            ':nomb_cliente' => $nomb_cliente,
            ':referencia' => $referencia,
            ':obra' => $obra
        ]);


        // 2. Insertar en la tabla `articulos` (Líneas)
        $queryArticulo = "INSERT INTO articulos (cod_presupuesto, version, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta,
                                            precio_compra, descuento_factura, palet, cantidad, plv, extra, porte, rappel)
                          VALUES (:cod_presupuesto, :version, :proveedor, :cod_art, :descripcion, :unidades, :precio_venta, :descuento_venta,
                                            :precio_compra, :descuento_factura, :palet, :cantidad, :plv, :extra, :porte, :rappel)";
        
        $stmtArticulo = $pdo->prepare($queryArticulo);

        for ($i=0 ; $i<$n ; $i++) {
            // Recogemos datos de cada línea
            $proveedor = $_POST["f".$i."-proveedor"];
            $cod_art = $_POST["f".$i."-cod_art"];
            $descripcion = $_POST["f".$i."-descripcion"];
            $unidades = $_POST["f".$i."-unidades"];
            $precio_venta = $_POST["f".$i."-precio_venta"];
            $descuento_venta = $_POST["f".$i."-descuento_venta"];
            $precio_compra = $_POST["f".$i."-precio_compra"];
            $descuento_factura = $_POST["f".$i."-descuento_factura"];
            $palet = $_POST["f".$i."-palet"];
            $cantidad = $_POST["f".$i."-cantidad"];
            $plv = $_POST["f".$i."-plv"];
            $extra = $_POST["f".$i."-extra"];
            $porte = $_POST["f".$i."-porte"];
            $rappel = $_POST["f".$i."-rappel"];

            // Ejecutamos la inserción para cada artículo
            $stmtArticulo->execute([
                ':cod_presupuesto' => $cod_presupuesto,
                ':version' => $insertVers,
                ':proveedor' => $proveedor,
                ':cod_art' => $cod_art,
                ':descripcion' => $descripcion,
                ':unidades' => !empty($unidades) ? $unidades : 0,
                ':precio_venta' => !empty($precio_venta) ? $precio_venta : 0,
                ':descuento_venta' => !empty($descuento_venta) ? $descuento_venta : 0,
                ':precio_compra' => !empty($precio_compra) ? $precio_compra : 0,
                ':descuento_factura' => !empty($descuento_factura) ? $descuento_factura : 0,
                ':palet' => !empty($palet) ? $palet : 0,
                ':cantidad' => !empty($cantidad) ? $cantidad : 0,
                ':plv' => !empty($plv) ? $plv : 0,
                ':extra' => !empty($extra) ? $extra : 0,
                ':porte' => !empty($porte) ? $porte : 0,
                ':rappel' => !empty($rappel) ? $rappel : 0
            ]);
        }

        $pdo->commit();
        
        // Redirigir a la página de inicio
        redirigir("/calculadora/?id=$cod_presupuesto&version=$insertVers");

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al crear nueva versión: " . $e->getMessage());
        echo 'Error al crear la nueva versión: ' . $e->getMessage();
    }

?>

