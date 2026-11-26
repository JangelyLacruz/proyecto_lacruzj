<?php

use src\modelos\clientesModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];

  $objeto = new clientesModelo();
  ob_clean();
  $resultado=[
    'icono'=>'error',
    'titulo'=>'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarClientes($_POST);
      echo json_encode($resultado);
      exit();
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarClientes($_POST);
      echo json_encode($resultado);
      exit();
    case 'registrar':
      $resultado = $objeto->registrarClientes($_POST);
      echo json_encode($resultado);
      exit();
    case 'actualizar':
      $resultado = $objeto->actualizarClientes($_POST);
      echo json_encode($resultado);
      exit();
    case 'eliminar':
      $resultado = $objeto->eliminarClientes($_POST);
      echo json_encode($resultado);
      exit();
    default:
      $objeto->DECORE([
        'icono' => 'error',
        'titulo' => 'Acción no reconocida'
      ]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/clientes/clientes.php";
}
