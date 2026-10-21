<?php
use src\modelos\ordenesEntregasPresupuestosModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["accion"]) &&
  isset($_SESSION['cedula'])
) {
  $accion  = $_POST["accion"];
  $objeto  = new ordenesEntregasPresupuestosModelo();
  ob_clean();

  $resultado = [
    'tipo'   => 'simple',
    'icono'  => 'error',
    'titulo' => 'Acción no válida',
    'texto'  => 'La acción solicitada no existe',
  ];

  switch ($accion) {
    case 'listar':
      $resultado = $objeto->listarOrdenes($_POST);
      break;
    case 'listarMetodosPago':
      $resultado = $objeto->listarMetodosPago($_POST);
      break;
    case 'obtenerUno':
      $resultado = $objeto->obtenerOrden($_POST);
      break;
    case 'obtenerDetalle':
      $resultado = $objeto->obtenerDetalleOrden($_POST);
      break;
    case 'registrar':
      $resultado = $objeto->registrarOrden($_POST);
      break;
    case 'registrarPago':
      $resultado = $objeto->registrarPago($_POST);
      break;
    case 'despachar':
      $resultado = $objeto->despacharOrden($_POST);
      break;
    case 'anular':
      $resultado = $objeto->anularOrden($_POST);
      break;
    case 'procesarPresupuesto':
      $resultado = $objeto->procesarPresupuesto($_POST);
      break;
    case 'imprimirPlanilla':
      $resultado = $objeto->impresionPlanillaOEP($_POST);
      break;
    case 'obtenerDatosRetencion':
      $resultado = $objeto->obtenerDatosRetencion($_POST);
      break;
    case 'generarRetencionIVA':
      $resultado = $objeto->generarRetencionIVA($_POST);
      break;
  }
  
  $objeto->DECORE($resultado);
  exit();

} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('ordenesEntregasPresupuestos', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/ordenesEntregasPresupuestos/ordenesEntregasPresupuestos.php";
} else {
  http_response_code(405);
  echo json_encode([
    'tipo'   => 'simple',
    'titulo' => 'Método no permitido',
    'texto'  => 'Solo se permiten peticiones GET y POST',
    'icono'  => 'error',
  ]);
}