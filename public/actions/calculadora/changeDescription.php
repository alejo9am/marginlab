<?php

//PHP para cambiar el nombre de la version
    
$pdo = require_once "../../../config/bootstrap.php";

$version = limpiar_dato($_POST['version']); //version de la oferta
$cod_presupuesto = limpiar_dato($_POST['id']); //id de la oferta
$cod_presupuesto_visual = limpiar_dato($_POST['id_visual'] ?? $cod_presupuesto);
$newVersion = limpiar_dato($_POST['nombre_version']); //nuevo nombre de la version

//CONSULTA A LA BASE DE DATOS usanso el pdo maximizando la seguridad
try {
  $query = "UPDATE presupuestos SET nombre_version=:newVersion WHERE cod_presupuesto = :codPresupuesto AND version = :version";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['newVersion' => $newVersion, 'codPresupuesto' => $cod_presupuesto, 'version' => $version]);
  if ($stmt->rowCount() > 0) {
    redirigir("/calculadora/?id=$cod_presupuesto_visual&version=$version");
  } else {
    echo "Error al actualizar la descripción.";
  }
} catch (PDOException $e) {
  error_log("Error al actualizar la descripción: " . $e->getMessage());
  echo "Error al actualizar la descripción.";
}

?>