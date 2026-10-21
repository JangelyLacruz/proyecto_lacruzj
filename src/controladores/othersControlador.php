<?php

use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

  $archivo = "src/vistas/others/";
  if (isset($_SESSION['url2']) && $_SESSION['url2'] != "") {
    if (is_file($archivo . $_SESSION['url2'] . ".php")) {
      $archivo .= $_SESSION['url2'] . ".php";
      $_SESSION['vistaActual'] = $_SESSION['url2'];
    }
  } elseif (isset($_SESSION['url1']) && $_SESSION['url1'] != "") {
    if (is_file($archivo . $_SESSION['url1'] . ".php")) {
      $archivo .= $_SESSION['url1'] . ".php";
      $_SESSION['vistaActual'] = $_SESSION['url1'];
    }
  }

  if (($_SESSION['url1'] ?? '') == 'landing') {
    ob_clean();
    require 'src/config/inc/head.php';
    require_once $archivo;
    require_once 'src/config/inc/script.php';
  }else{
    $objComponentes = new componentesModelo();
    require_once "src/config/inc/header.php";
    echo $objComponentes->sidebar();
    require_once $archivo;
  }
}
