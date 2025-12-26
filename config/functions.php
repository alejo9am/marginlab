<?php
// config/functions.php

/**
 * Redirige a una ruta interna usando BASE_URL
 * @param string $ruta Ejemplo: "buscador/select.php" o "errores"
 */
function redirigir($ruta)
{
  // Si la ruta ya incluye http, asumimos que es externa o absoluta,
  // pero para este caso asumimos rutas internas del proyecto.

  // Limpiamos barras duplicadas
  $url = BASE_URL . '/' . ltrim($ruta, '/');

  header("Location: $url");
  exit();
}

/**
 * Limpia datos de entrada para evitar XSS (Bonus de seguridad)
 */
function limpiar_dato($dato)
{
  return htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
}