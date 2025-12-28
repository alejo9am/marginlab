<?php

    //PHP para borrar una versión de un presupuesto concreto

    $pdo = require_once('../../../config/bootstrap.php');

    //CONSULTA A LA BASE DE DATOS USANDO EL PDO
    $cod_oferta = limpiar_dato($_GET['id']);
    $version = limpiar_dato($_GET['version']);

    // primero borramosla info de la tabla general de presupuestos
    $query = "DELETE FROM presupuestos WHERE cod_presupuesto = :cod_oferta AND version = :version";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cod_oferta', $cod_oferta);
    $stmt->bindParam(':version', $version);
    $stmt->execute();

    // ahora borramos todos los items de esa versión
    $query = "DELETE FROM articulos WHERE cod_presupuesto = :cod_oferta AND version = :version";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cod_oferta', $cod_oferta);
    $stmt->bindParam(':version', $version);
    $stmt->execute();

    // redirigimos a la página principal de la calculadora con el id del presupuesto
    redirigir("/calculadora/?id=" . $cod_oferta);
?>