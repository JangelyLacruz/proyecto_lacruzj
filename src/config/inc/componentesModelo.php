<?php

namespace src\config\inc;

use src\config\connect\conexion;
use src\modelos\accesosModelo;

class componentesModelo extends conexion {

  private accesosModelo $objPermisos;

  public function __construct() {
    $this->objPermisos = new accesosModelo();
  }
  public function sidebar() {
    $arrayLiPrincipal = [
      [
        'url' => 'clientes',
        'texto' => 'Clientes',
        'modulo' => 'clientes',
        'permiso' => 'ver',
        'icono' => 'users-medical'
      ],
      [
        'url' => 'ordenesEntregasPresupuestos',
        'texto' => 'Órdenes de Entrega',
        'modulo' => 'facturacion',
        'icono' => 'receipt',
        'permiso' => 'ver',
      ],
      [
        'modulo' => 'pedidos',
        'permiso' => 'ver',
        'url' => 'pedidos',
        'icono' => 'order-food-mobile',
        'texto' => 'Pedidos',
      ],
      [
        'modulo' => 'pagos',
        'permiso' => 'ver',
        'url' => 'pagos',
        'icono' => 'credit-card',
        'texto' => 'Pagos',
      ],
      [
        'modulo' => 'inventario',
        'permiso' => 'ver inventario',
        'url' => 'inventario',
        'icono' => 'supplier-alt',
        'texto' => 'Inventario',
      ],
      [
        'modulo' => 'compras',
        'permiso' => 'ver',
        'url' => 'compras',
        'icono' => 'shopping-cart-add',
        'texto' => 'Recepciones',
      ],
      [
        'modulo' => 'ordenesServicios',
        'permiso' => 'ver',
        'url' => 'ordenesServicios',
        'icono' => 'ballot',
        'texto' => 'Órdenes de Servicios',
      ],
      [
        'modulo' => 'producciones',
        'permiso' => 'ver',
        'url' => 'producciones',
        'icono' => 'hands-bubbles',
        'texto' => 'Producciones',
      ],
      [
        'modulo' => 'reportes',
        'permiso' => 'ver reportes',
        'url' => 'reportes',
        'icono' => 'chart-histogram',
        'texto' => 'Reportes',
      ],
    ];
    $sidebarHTML = '
      <li class="liSidebar">
        <a href="' . APP_URL . 'home" class="aSidebar">
          <i class="fi fi-rr-home"></i>
          <span>Inicio</span>
        </a>
      </li>
    ';
    foreach ($arrayLiPrincipal as $li) {
      if (!$this->objPermisos->validarPermisos($li['modulo'], $li['permiso'])) {
        $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . $li['url'] . '" class="aSidebar">
            <i class="fi fi-rr-' . $li['icono'] . '"></i>
            <span>' . $li['texto'] . '</span>
          </a>
        </li>
      ';
      }
    }
    $arrayLiConfig = [
      [
        'modulo' => 'productos',
        'permiso' => 'ver',
        'url' => 'productos',
        'icono' => 'bin-bottles',
        'texto' => 'Productos',
      ],
      [
        'modulo' => 'materiasPrimas',
        'permiso' => 'ver',
        'url' => 'materiasPrimas',
        'icono' => 'flask',
        'texto' => 'Materias Primas',
      ],
      [
        'modulo' => 'servicios',
        'permiso' => 'ver',
        'url' => 'servicios',
        'icono' => 'broom',
        'texto' => 'Servicios',
      ],
      [
        'modulo' => 'proveedores',
        'permiso' => 'ver',
        'url' => 'proveedores',
        'icono' => 'seller',
        'texto' => 'Proveedores',
      ],
      [
        'modulo' => 'presentaciones',
        'permiso' => 'ver',
        'url' => 'presentaciones',
        'icono' => 'soap',
        'texto' => 'Presentaciones',
      ],
      [
        'modulo' => 'monedas',
        'permiso' => 'ver historial de cambio de las divisas',
        'url' => 'monedas/cambios-monedas',
        'icono' => 'money-transfer-coin-arrow',
        'texto' => 'Cambio Monetario',
      ],
      [
        'modulo' => 'repartidores',
        'permiso' => 'ver',
        'url' => 'repartidores',
        'icono' => 'person-carry-box',
        'texto' => 'Repartidores',
      ],
      [
        'modulo' => 'categoriasProductos',
        'permiso' => 'ver',
        'url' => 'categoriasProductos',
        'icono' => 'category',
        'texto' => 'Categorías',
      ],
      [
        'modulo' => 'unidadesMedidas',
        'permiso' => 'ver',
        'url' => 'unidadesMedidas',
        'icono' => 'ruler-horizontal',
        'texto' => 'Unidades de Medida',
      ],
      [
        'modulo' => 'metodos-pago',
        'permiso' => 'ver',
        'url' => 'metodos-pago',
        'icono' => 'credit-card',
        'texto' => 'Métodos de Pago',
      ],
      [
        'modulo' => 'monedas',
        'permiso' => 'ver',
        'url' => 'monedas',
        'icono' => 'money',
        'texto' => 'Monedas',
      ],
      [
        'modulo' => 'cambiosIva',
        'permiso' => 'ver historial de cambio del iva',
        'url' => 'cambiosIva',
        'icono' => 'tax-alt',
        'texto' => 'IVA',
      ],
      [
        'modulo' => 'empresasEnvios',
        'permiso' => 'ver',
        'url' => 'empresasEnvios',
        'icono' => 'shipping-fast',
        'texto' => 'Empresas de Envíos',
      ],
      [
        'modulo' => 'sucursalesEmpresasEnvios',
        'permiso' => 'ver',
        'url' => 'sucursalesEmpresasEnvios',
        'icono' => 'map-marker-home',
        'texto' => 'Sucursales de Envíos',
      ],
      [
        'modulo' => 'bancos',
        'permiso' => 'ver',
        'url' => 'bancos',
        'icono' => 'bank',
        'texto' => 'Bancos',
      ],
      [
        'modulo' => 'rutas',
        'permiso' => 'ver',
        'url' => 'rutas',
        'icono' => 'route',
        'texto' => 'Rutas',
      ],
      [
        'modulo' => 'usuarios',
        'permiso' => 'ver',
        'url' => 'usuarios',
        'icono' => 'user',
        'texto' => 'Usuarios',
      ],
      [
        'modulo' => 'accesos',
        'permiso' => 'ver',
        'url' => 'accesos',
        'icono' => 'lock',
        'texto' => 'Accesos',
      ],
      [
        'modulo' => 'roles',
        'permiso' => 'ver',
        'url' => 'roles',
        'icono' => 'organization-chart',
        'texto' => 'Roles',
      ],
      [
        'modulo' => 'modulos',
        'permiso' => 'ver',
        'url' => 'modulos',
        'icono' => 'module',
        'texto' => 'Módulos',
      ],
      [
        'modulo' => 'permisos',
        'permiso' => 'ver',
        'url' => 'permisos',
        'icono' => 'holding-hand-key',
        'texto' => 'Permisos',
      ],
      [
        'modulo' => 'bitacora',
        'permiso' => 'ver bitácora',
        'url' => 'bitacora',
        'icono' => 'file-spreadsheet',
        'texto' => 'Bitácora',
      ],
      [
        'modulo' => 'preguntas-seguridad',
        'permiso' => 'ver',
        'url' => 'preguntas-seguridad',
        'icono' => 'interrogation',
        'texto' => 'Preguntas de Seguridad',
      ],
    ];
    $lisConfig = '';
    foreach ($arrayLiConfig as $li) {
      if (!$this->objPermisos->validarPermisos($li['modulo'], $li['permiso'])) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . $li['url'] . '" class="aSidebar">
              <i class="fi fi-rr-' . $li['icono'] . '"></i>
              <span>' . $li['texto'] . '</span>
            </a>
          </li>
        ';
      };
    }
    if ($lisConfig != '') {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="#multiNivelCollapse1" data-bs-toggle="collapse" aria-expanded="false" class="aSubSidebar">
            <div class="d-flex align-items-center w-100">
              <i class="fi fi-rr-settings me-2"></i>
              <span class="text-truncate">Configuraciones</span>
            </div>
            <i class="fi fi-rr-angle-small-down menu-arrow transition-transform text-muted ms-2"></i>
          </a>
          <div class="collapse" id="multiNivelCollapse1">
            <ul class="list-unstyled mt-1 border-start border-light ms-1 ps-1 pe-0">
              ' . $lisConfig . '
            </ul>
          </div>
        </li>
      ';
    }
    $sidebarHTML = '
      <div class="sidebar noselec">
        <div class="iconoNombre d-flex flex-column align-items-center justify-content-center text-center mt-2 px-3 w-100">
          <img src="' . APP_URL . 'src/assets/images/logo.png" class="navbar-logo rounded m-0" alt="logo">
          <a class="navbar-brand m-0 text-dark fw-bolder fs-4 text-decoration-none w-100" href="#">J.LACRUZ C.A.</a>
        </div>
        <nav>
          <ul class="sidebar-menu list-unstyled px-0">
            ' . $sidebarHTML . '
            <li class="sidebar-divider"></li>
            <li class="liSidebar">
              <a class="logout-btn btnCerrarSession" href="#" class="aSidebar">
                <i class="fi fi-rr-sign-out-alt"></i>
                <span>Cerrar sesión</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="sidebarBackdrop"></div>
    ';
    return $sidebarHTML;
  }
  public function listaDataTable($instrucciones) {
    $encabezado = $instrucciones['encabezado'];
    $tituloBtnReg = $instrucciones['tituloBtnReg'] ?? null;
    $boton = !isset($tituloBtnReg) ? '' : '
      <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target=".modalRegistrar">
          <i class="fas fa-plus-circle"></i> ' . $tituloBtnReg . '
      </button>
    ';
    $listaDataTable = '
      <div class="main-content px-4" id="mainContent">
          <dvi class="container-fluid py-4">
              <div class="row mb-4">
                  <div class="col-md-6">
                      <h2 class="mb-0">' . $encabezado . '</h2>
                  </div>
                  <div class="col-md-6 text-end">
                      ' . $boton . '
                  </div>
              </div>
              <div class="card">
                  <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-striped table-hover tabla-ajax">
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    ';
    return $listaDataTable;
  }
}
