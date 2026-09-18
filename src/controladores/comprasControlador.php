<?php

use src\modelos\comprasModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];

  $objeto = new comprasModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarCompra($_POST);
      break;
    case 'listarProductosParaCompra':
      $resultado = $objeto->listarProductosParaCompra();
      break;
    case 'registrar':
      $resultado = $objeto->registrarCompra($_POST);
      break;
    case 'actualizar':
      $resultado = $objeto->actualizarCompra($_POST);
      break;
    case 'recepcionar':
      $resultado = $objeto->cambiarEstadoRecepcion($_POST);
      break;
    case 'imprimir':
      $resultado = $objeto->imprimirOrdenCompra($_POST);
      break;
    case 'eliminar':
      $resultado = $objeto->eliminarCompra($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('compras', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/compras/compras.php";
}
