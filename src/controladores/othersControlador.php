<?php

use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

  $carpetaVistas = dirname(__DIR__) . "/vistas/others/";

  if (($_SESSION['url1'] ?? '') == 'landing') {
    $archivo = $carpetaVistas . "landing.php";
    $_SESSION['vistaActual'] = 'landing';
    if (!defined('DIR_IMG')) {
      define('DIR_IMG', 'src/assets/images/');
    }
    ob_clean();
    require 'src/config/inc/head.php';
    require_once $archivo;
    require_once 'src/config/inc/script.php';
    exit();
  }

  $archivo = $carpetaVistas;
  if (isset($_SESSION['url2']) && $_SESSION['url2'] != "") {
    if (is_file($carpetaVistas . $_SESSION['url2'] . ".php")) {
      $archivo .= $_SESSION['url2'] . ".php";
      $_SESSION['vistaActual'] = $_SESSION['url2'];
    }
  } elseif (isset($_SESSION['url1']) && $_SESSION['url1'] != "") {
    if (is_file($carpetaVistas . $_SESSION['url1'] . ".php")) {
      $archivo .= $_SESSION['url1'] . ".php";
      $_SESSION['vistaActual'] = $_SESSION['url1'];
    }
  }

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once $archivo;
}
