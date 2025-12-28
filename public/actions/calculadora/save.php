<?php

    //PHP para hacer update de la tabla -----> GUADAR CAMBIOS EN LA MISMA VERSIÓN
    //se ejecuta solo si la version no es la inicial

    $pdo = require_once "../../../config/bootstrap.php";

    $n = limpiar_dato($_GET['n']); //numero de articulos
    $cod_presupuesto = limpiar_dato($_GET['id']); //id de la oferta
    $cod_presupuesto_visual = limpiar_dato($_GET['id_visual'] ?? $cod_presupuesto);
    $version = limpiar_dato($_GET['v']); //version de la oferta

    // ejecutar solo si la version no es la inicial (versión 0 suele ser la importada/original)
    if($version == 1) redirigir("/calculadora/?id=$cod_presupuesto_visual&version=$version");

    try {
        $pdo->beginTransaction();

        // 1. Actualizar la tabla `presupuestos` (Cabecera)
        
        $obs_comercial = isset($_POST['obsComercial']) ? $_POST['obsComercial'] : '';
        $obs_revision = isset($_POST['obsRevision']) ? $_POST['obsRevision'] : '';

        $queryPresupuesto = "UPDATE presupuestos 
                             SET obs_comercial = :obs_comercial, 
                                 obs_revision = :obs_revision 
                             WHERE cod_presupuesto = :cod_presupuesto AND version = :version";
        
        $stmtPresupuesto = $pdo->prepare($queryPresupuesto);
        $stmtPresupuesto->execute([
            ':obs_comercial' => $obs_comercial,
            ':obs_revision' => $obs_revision,
            ':cod_presupuesto' => $cod_presupuesto,
            ':version' => $version
        ]);

        // 2. Actualizar la tabla `articulos` (Líneas)
        $queryArticulo = "UPDATE articulos 
                          SET unidades = :unidades,
                              precio_venta = :precio_venta,
                              descuento_venta = :descuento_venta,
                              precio_compra = :precio_compra,
                              descuento_factura = :descuento_factura,
                              palet = :palet,
                              cantidad = :cantidad,
                              plv = :plv,
                              extra = :extra,
                              porte = :porte,
                              rappel = :rappel
                          WHERE cod_presupuesto = :cod_presupuesto 
                            AND version = :version 
                            AND cod_art = :cod_art";
        
        $stmtArticulo = $pdo->prepare($queryArticulo);

        for ($i=0 ; $i<$n ; $i++) {
            // Recogemos datos de cada línea
            // Nota: proveedor y descripcion no se suelen editar en este paso, solo los valores numéricos
            $cod_art = $_POST["f".$i."-cod_art"];
            
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

            // Ejecutamos la actualización para cada artículo
            $stmtArticulo->execute([
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
                ':rappel' => !empty($rappel) ? $rappel : 0,
                ':cod_presupuesto' => $cod_presupuesto,
                ':version' => $version,
                ':cod_art' => $cod_art
            ]);
        }

        $pdo->commit();
        
        // Redirigir a la página de inicio
        redirigir("/calculadora/?id=$cod_presupuesto_visual&version=$version");

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al guardar versión: " . $e->getMessage());
        echo 'Error al guardar la versión';
    }

?>