<?php
  require_once  "../../config/bootstrap.php";

  //imprimir los valores guardados en GET
  $_GET = array_map('htmlspecialchars', $_GET);

  echo "<pre>";
  print_r($_GET);
  echo "</pre>";
?>