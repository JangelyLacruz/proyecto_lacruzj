<?php

use src\controladores\frontController;
use src\config\connect\errorBD;

require_once 'src/config/const.php';
try {
  require_once 'vendor/autoload.php';
  require_once 'src/config/inc/errorHandler.php';
  require_once 'src/config/inc/sesion.php';
  require_once 'src/config/inc/head.php';
  $controlador = new frontController;
  require_once 'src/config/inc/script.php';
} catch (errorBD $th) {

  $d = ($th->getDetalles() ?? []);
  $texto = 'Ha ocurrido un error en la base de datos';
  if (modoDev) {


    $texto +=
      // ' | titulo: ' . ($d['titulo'] ?? '') .
      // ' | linea: ' . ($d['linea'] ?? '') .
      // ' | código de error: ' . ($d['código de error'] ?? '') .
      // ' | mensaje de error: ' . ($d['mensaje de error'] ?? '') .
      ' | titulo: ' . ($d['titulo'] ?? '');
  }

  $mensajeError = [
    'tipo' => 'simple',
    'titulo' => 'Error en la Base de Datos',
    'texto' => $texto,
    'icono' => 'error',
  ];
  if (modoDev) {
    $mensajeError += $th->getDetalles();
  }
  ob_end_clean();
  $_SESSION['codigoRequest'] = 500;
  echo json_encode($mensajeError);
  exit();
} catch (\Throwable $th) {
  $mensajeError = [
    'tipo' => 'simple',
    'titulo' => 'Error interno',
    'texto' => 'Ha ocurrido un error en el servidor',
    'icono' => 'error',
  ];
  if (modoDev) {
    $mensajeError += [
      'titulo' => 'Error del codigo PHP',
      'linea' => $th->getLine(),
      'código de error' => $th->getCode(),
      'mensaje de error' => $th->getMessage(),
      'rastro' => $th->getTrace(),
    ];
  }
  ob_end_clean();
  $_SESSION['codigoRequest'] = 500;
  echo json_encode($mensajeError);
  exit();
}
