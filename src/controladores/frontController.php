<?php

namespace src\controladores;

use src\config\connect\conexion;

class frontController extends conexion {

  private array|string $url = '';
  private array $vistasEstaticas;
  private array $controladores;
  private string $archivo;

  public function __construct() {
    $salidasFueraDeSesion = [
      'productos',
      'servicios',
      'rutas',
      'monedas',
      'usuarios',
      'preguntas-seguridad'
    ];
    $this->controladores = [
      'accesos',
      'bancos',
      'bitacora',
      'cambiosIva',
      'categoriasProductos',
      'chatbot',
      'clientes',
      'compras',
      'empresasEnvios',
      'exportar',
      'insumos',
      'inventario',
      'iva',
      'materiasPrimas',
      'mensajesWS',
      'metodos-pago',
      'modulos',
      'monedas',
      'ordenesEntregasPresupuestos',
      'ordenesServicios',
      'others',
      'pagos',
      'pedidos',
      'permisos',
      'preguntas-seguridad',
      'presentaciones',
      'presupuestos',
      'productos',
      'producciones',
      'proveedores',
      'repartidores',
      'reportes',
      'reportesEstadisticos',
      'recepciones',
      'roles',
      'rutas',
      'servicios',
      'sucursalesEmpresasEnvios',
      'unidadesMedidas',
      'usuarios',
    ];
    $this->vistasEstaticas = [
      'home',
      '404'
    ];

    $metodo = $_SERVER["REQUEST_METHOD"];
    $this->transformarCuerpoAPost();
    $accion = $_POST['accion'] ?? $_POST['reporte'] ?? '';


    if (isset($_GET["views"]) && $_GET["views"] != "") {
      $this->url = explode("/", $_GET['views']);
      $_SESSION['vistaActual'] = $this->url[0];
      $this->url = $this->url[0];

      if (in_array($this->url, $this->controladores) && isset($_SESSION['cedula'])) {
        if ($metodo == 'POST') $this->validarTokens();
        if (is_file("src/controladores/" . $this->url . "Controlador.php")) {
          $this->archivo = "src/controladores/" . $this->url . "Controlador.php";
          $_SESSION['vistaActual'] = $this->url;
        }
      } elseif (in_array($this->url, $this->vistasEstaticas)) {
        $this->archivo = "src\controladores\othersControlador.php";
        $_SESSION['vistaActual'] = $this->url;
      } elseif (in_array($this->url, $salidasFueraDeSesion)) {
        $this->archivo = "src/controladores/" . $this->url . "Controlador.php";
      } elseif (isset($_SESSION['cedula'])) {
        $this->redireccionarUsuario();
        return;
      } elseif (
        $_SESSION['vistaActual'] == 'usuarios' && (
          $accion == 'iniciarSesion' ||
          $accion == 'registrar' ||
          $accion == 'restaurarContraseña'
        )
      ) {
        $this->archivo = "src/controladores/usuariosControlador.php";
        $_SESSION['vistaActual'] = 'usuarios';
      } else {
        $this->archivo = "src/vistas/usuarios/login.php";
        $_SESSION['vistaActual'] = 'login';
      }
    } else {
      $this->redireccionarUsuario();
      exit();
    }
    $this->llamarArchivo();
  }
  private function llamarArchivo() {
    if (file_exists($this->archivo)) {
      $urlActual = explode("/", ($_GET['views'] ?? ''));
      $url1 = $urlActual[0] ?? "";
      $url2 = $urlActual[1] ?? "";
      $_SESSION['url1'] = $urlActual[0] ?? "";
      $_SESSION['url2'] = $urlActual[1] ?? "";
      require_once $this->archivo;
    } else {
      $this->redireccionarUsuario();
    }
  }
  private function transformarCuerpoAPost() {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
      if (isset($_POST['metadatos'])) {
        foreach (json_decode($_POST['metadatos'], true) as $clave => $valor) {
          $_POST[$clave] = $valor;
        }
        unset($_POST['metadatos']);
      } elseif (!empty(file_get_contents('php://input'))) {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (is_array($datos)) {
          foreach ($datos as $clave => $valor) {
            $_POST[$clave] = $valor;
          }
        }
      }
      foreach ($_FILES as $clave => $valor) {
        $_POST[$clave] = $valor;
      }
    }
  }
  private function validarTokens() {
    $tokenCSRFRecibido = $_SERVER['HTTP_X_TOKEN_CSRF'] ?? '';
    $tokenCSRFSesion = $_SESSION['TOKEN_CSRF'] ?? '';
    /* echo json_encode([
      'recibido'=>$tokenCSRFRecibido,
      'sesion'=>$tokenCSRFSesion
    ]);
    exit(); */
    if (empty($tokenCSRFSesion) || !hash_equals($tokenCSRFSesion, $tokenCSRFRecibido)) {
      $this->redireccionarUsuario();
      exit;
    }
  }
}
