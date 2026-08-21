<?php

use src\modelos\reportesModelo;
use src\config\inc\componentesModelo;
use src\modelos\PDF;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $datos = json_decode(file_get_contents('php://input'), true) ?? $_POST;
  $reporte = $datos["reporte"] ?? '';
  $objReportes = new reportesModelo();

  ob_clean();
  $resultado = [
    "icono" => "error",
    'titulo' => 'No se reconoce el tipo de reporte'
  ];
  switch ($reporte) {
    case "reporteVentas":
      $resultado = $objReportes->reporteVentas($datos);
      break;
    case "reporteCompras":
      $resultado = $objReportes->reporteCompras($datos);
      break;
    case "reporteCierre":
      $resultado = $objReportes->reporteCierre($datos);
      break;
    case "reporteServicios":
      $resultado = $objReportes->reporteServicios();
      break;
    case "reporteProductos":
      $resultado = $objReportes->reporteProductos();
      break;
    case "reporteMateriaPrima":
      $resultado = $objReportes->reporteMateriaPrima();
      break;
  }
  $objReportes->DECORE($resultado);
  exit();
} else {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/reportes/reportes.php";
}
