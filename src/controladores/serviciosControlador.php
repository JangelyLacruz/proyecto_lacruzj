<?php

use src\modelos\serviciosModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {


  $accion = $_POST["accion"];
  $objeto = new serviciosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarServicios($_POST);
      break;
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarServicios($_POST);
      break;
    case 'registrar':
      $resultado = $objeto->registrarServicio($_POST);
      break;
    case 'actualizar':
      $resultado = $objeto->actualizarServicio($_POST);
      break;
    case 'eliminar':
      $resultado = $objeto->eliminarServicio($_POST);
      break;
    case 'actualizarFoto':
      $resultado = $objeto->actualizarFotoServicio($_POST);
      break;
    case 'eliminarFoto':
      $resultado = $objeto->eliminarFotoServicio($_POST);
      break;
    default:
      $resultado = [
        'icono' => 'error',
        'titulo' => 'Acción no reconocida'
      ];
      break;
  }

  $objeto->DECORE($resultado);
  exit();

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('servicios', 'ver');
  if($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/servicios/servicios.php";
} else {
  http_response_code(405);
  echo json_encode([
    'tipo'   => 'simple',
    'titulo' => 'Método no permitido',
    'texto'  => 'Solo se permiten peticiones GET y POST',
    'icono'  => 'error',
  ]);
}
