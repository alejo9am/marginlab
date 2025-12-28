<?php

$pdo = require_once __DIR__ . "/../../config/bootstrap.php";

// Configuración de la persistencia
define('COOKIE_NAME', 'marginlab_token');
define('COOKIE_DAYS', 7); // La sesión dura 7 días

// --------------------------------------------------------------------------
// FASE 1: COMPROBACIÓN SI YA TIENE LA COOKIE DE SANDBOX ACTIVA
// --------------------------------------------------------------------------

$token_seguro = null;

if (isset($_COOKIE[COOKIE_NAME])) {
  $token = $_COOKIE[COOKIE_NAME];
  // Validamos que el token sea hexadecimal para evitar inyecciones
  if (ctype_xdigit($token)) {
    $posible_prefix = 'sbx_' . $token . '_';

    // Verificamos si este sandbox sigue vivo en la base de datos
    // (El limpiador podría haberlo borrado si pasaron más de 7 días)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM presupuestos WHERE cod_presupuesto LIKE :pattern");
    $stmtCheck->execute([':pattern' => $posible_prefix . '%']);

    if ($stmtCheck->fetchColumn() > 0) {
      // Restauramos la sesión y le mandamos dentro.
      $_SESSION['sandbox_prefix'] = $posible_prefix;
      redirigir("/buscador");
    } else {
      // El usuario tiene cookie, pero no datos. Reutilizamos el token.
      $token_seguro = $token;
    }
  }
}

// --------------------------------------------------------------------------
// FASE 2: CREACIÓN (USUARIO NUEVO O CADUCADO)
// --------------------------------------------------------------------------

try {
  // Generamos nuevo Token Seguro y Prefijo si no lo hemos recuperado
  if ($token_seguro === null) {
    // Usamos random_bytes para seguridad criptográfica
    try {
      $token_seguro = bin2hex(random_bytes(16));
    } catch (Exception $e) {
      // Fallback por si falla el generador de entropía del sistema
      $token_seguro = substr(md5(mt_rand()), 0, 16);
    }
  }

  $prefix = 'sbx_' . $token_seguro . '_';


  // Guardamos la Cookie en el navegador
  setcookie(
    COOKIE_NAME,
    $token_seguro,
    time() + (86400 * COOKIE_DAYS),
    "/", // Ruta raíz
    "",  // Dominio (automático)
    false, // Secure (True si usas HTTPS)
    true // HttpOnly (Protección contra XSS)
  );

  $_SESSION['sandbox_prefix'] = $prefix;

  // Hacemos la clonación de las plantillas maestras
  // IDs de las Plantillas Maestras
  $plantillas = ['10001', '20002', '30003'];

  $pdo->beginTransaction();

  // Preparamos las consultas de clonado
  // Nota: concatena el prefijo al ID original
  $sqlHead = "INSERT INTO presupuestos (cod_presupuesto, version, nombre_version, cod_cliente, nomb_cliente, referencia, obra, obs_comercial, obs_revision)
                SELECT CONCAT(:prefix, cod_presupuesto), version, nombre_version, cod_cliente, nomb_cliente, referencia, obra, obs_comercial, obs_revision
                FROM presupuestos 
                WHERE cod_presupuesto = :original_id";

  $sqlLines = "INSERT INTO articulos (cod_presupuesto, version, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, precio_compra, descuento_factura, palet, cantidad, plv, extra, porte, rappel)
                 SELECT CONCAT(:prefix, cod_presupuesto), version, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, precio_compra, descuento_factura, palet, cantidad, plv, extra, porte, rappel
                 FROM articulos 
                 WHERE cod_presupuesto = :original_id";

  $stmtHead = $pdo->prepare($sqlHead);
  $stmtLines = $pdo->prepare($sqlLines);

  // Ejecutamos el clonado para cada plantilla
  foreach ($plantillas as $id) {
    $stmtHead->execute([':prefix' => $prefix, ':original_id' => $id]);
    $stmtLines->execute([':prefix' => $prefix, ':original_id' => $id]);
  }

  // GARBAGE COLLECTOR (Limpieza de basura vieja)
  // Con un 5% de probabilidad, borramos sandboxes caducados (> COOKIE_DAYS)
  if (rand(1, 100) <= 5) {
    $segundos = 86400 * COOKIE_DAYS;
    // Borramos cabeceras (las líneas se borran solas por ON DELETE CASCADE en MySQL)
    $pdo->query("DELETE FROM presupuestos WHERE cod_presupuesto LIKE 'sbx_%' AND created_at < (NOW() - INTERVAL $segundos SECOND)");
  }

  $pdo->commit();
  redirigir("/buscador");

} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  error_log("Error creando sandbox: " . $e->getMessage());
  die("Error crítico iniciando la demostración. Por favor, inténtelo de nuevo.");
}