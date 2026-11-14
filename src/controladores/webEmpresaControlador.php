<?php

use src\modelos\webEmpresaModelo;

// Cabeceras CORS para peticiones asíncronas
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, X-Token-Csrf");
  http_response_code(200);
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $accion = $_POST["accion"] ?? "";
  $modelo = new webEmpresaModelo();

  while (ob_get_level()) {
    ob_end_clean();
  }

  $resultado = [
    'status'  => 'error',
    'icono'   => 'error',
    'titulo'  => 'Acción no reconocida',
    'mensaje' => 'La acción solicitada no es válida en la web pública'
  ];

  switch ($accion) {

    case 'listarCatalogo':
      $resultado = $modelo->obtenerCatalogoProductos();
      break;

    case 'listarServicios':
      $resultado = $modelo->obtenerCatalogoServicios();
      break;

    case 'listarMonedas':
      $resultado = $modelo->obtenerMonedas();
      break;

    case 'verificarSesion':
      $resultado = $modelo->verificarSesionCliente();
      break;

    case 'registrarPedido':
      $resultado = $modelo->registrarPedidoWeb($_POST);
      break;

    case 'consultarPedidos':
      $resultado = $modelo->consultarPedidosCliente();
      break;

    case 'cerrarSesion':
      $resultado = $modelo->cerrarSesionWeb();
      break;
  }

  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, X-Token-Csrf");
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
  exit();

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

  // Limpiar cualquier encabezado previo del ERP para servir la página web limpia
  while (ob_get_level()) {
    ob_end_clean();
  }

  $rutaWeb = __DIR__ . "/../../pagina_empresa/index.html";
  if (file_exists($rutaWeb)) {
    $html = file_get_contents($rutaWeb);
    $urlBaseEmpresa = APP_URL . 'pagina_empresa/';

    // Reemplazar rutas relativas para apuntar con precisión absoluta a los recursos en pagina_empresa/
    $html = str_replace(
      [
        'href="./styles/',
        'src="./img/',
        'href="./img/',
        'src="./js/',
        "url('./img/",
        'url("./img/',
        'href="usuarios/login"'
      ],
      [
        'href="' . $urlBaseEmpresa . 'styles/',
        'src="' . $urlBaseEmpresa . 'img/',
        'href="' . $urlBaseEmpresa . 'img/',
        'src="' . $urlBaseEmpresa . 'js/',
        "url('" . $urlBaseEmpresa . "img/",
        'url("' . $urlBaseEmpresa . "img/",
        'href="' . APP_URL . 'usuarios/login"'
      ],
      $html
    );

    // Cabeceras de seguridad y prevención de caché para la web pública
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://unpkg.com; img-src 'self' data: https: blob:; font-src 'self' https://fonts.gstatic.com data:; connect-src *;");
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
  } else {
    echo "<h1>Error: No se encontró la portada de la página web.</h1>";
  }
  exit();
}
