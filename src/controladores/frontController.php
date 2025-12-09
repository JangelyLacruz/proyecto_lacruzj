<?php

namespace src\controladores;

class frontController
{
    private $url;
    private $controladores;
    private $archivo;

    public function __construct()
    {
        if (isset($_GET["views"]) && $_GET["views"] != "") {

            if (!isset($_SESSION['cedula']) || $_SESSION['cedula'] == "") {
                $this->archivo = "src/controladores/usuariosControlador.php";
            } else {
                $this->url = explode("/", $_GET['views']);
                $_SESSION['vistaActual'] = $this->url[0];
                $this->url = $this->url[0];

                $this->controladores = [
                    'clientes',
                    'compras',
                    'config',
                    'cuentasCobrar',
                    'facturas',
                    'iva',
                    'login',
                    'materiasPrimas',
                    'presentaciones',
                    'presupuestos',
                    'productos',
                    'proveedores',
                    'reportes',
                    'unidadesMedidas',
                    'materias-primas',
                    'proveedores',
                    'recepciones',
                    'usuarios',
                    'roles',
                    'permisos',
                    'cambiosIva',
                    'metodos-pago',
                    'monedas',
                    
                ];

                if (in_array($this->url, $this->controladores)) {
                    if (is_file("src/controladores/" . $this->url . "Controlador.php")) {
                        $this->archivo = "src/controladores/" . $this->url . "Controlador.php";
                        $_SESSION['vistaActual'] = $this->url;
                    }
                }
            }

            $this->llamarArchivo();
        } elseif ($this->url == "" || $this->url == "home" || $this->url = null) {

            if (isset($_SESSION['cedula'])) {
                require_once "src/config/inc/header.php";
                require_once "src/config/inc/sidebar.php";
                require_once "src/vistas/usuarios/dashboard.php";
                $_SESSION['vistaActual'] = 'usuarios';
            } else {
                require_once "src/vistas/usuarios/login.php";
                $_SESSION['vistaActual'] = 'login';
            }
        }
    }
    private function llamarArchivo()
    {
        if (file_exists($this->archivo)) {
            $urlActual = explode("/", $_GET['views']);
            $url1 = isset($urlActual[0]) ? $urlActual[0] : "";
            $url2 = isset($urlActual[1]) ? $urlActual[1] : "";
            require_once $this->archivo;
        } elseif (isset($_SESSION['cedula'])) {
            require_once "src/config/inc/header.php";
            require_once "src/config/inc/sidebar.php";
            require_once "src/vistas/usuarios/dashboard.php";
            $_SESSION['vistaActual'] = 'usuarios';
        } else {
            require_once "src/vistas/usuarios/login.php";
            $_SESSION['vistaActual'] = 'login';
        }
    }
}
