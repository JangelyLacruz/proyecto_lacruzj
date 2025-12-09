<div class="sidebar noselec" id="sidebar">
    <nav>
        <ul class="sidebar-menu">
            <li class="subMenuSidebar" data-bs-toggle="collapse" data-bs-target="#home-collapse">
                <a>
                    <i class="fi fi-rr-settings"></i>
                    <span>Configuraciones</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <div class="collapse" id="home-collapse">
                    <ul class="btn-toggle-nav list-unstyled ">
                        <li>
                            <a href="#">
                                <i class="fi fi-rr-ruler-horizontal"></i>
                                <span>Unidades de Medida</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li>
                <a href="<?php echo APP_URL ?>" title="Inicio">
                    <i class="fi fi-rr-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>usuarios">
                    <i class="fi fi-rr-user"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>roles">
                    <i class="fi fi-br-organization-chart"></i>
                    <span>Roles</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>cambiosIva">
                    <i class="fi fi-sr-tax-alt"></i>
                    <span>IVA</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>monedas">
                    <i class="fi fi-rr-money"></i>
                    <span>Monedas</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>monedas/cambios-monedas">
                    <i class="fi fi-rs-money-transfer-coin-arrow"></i>
                    <span>Cambio Monetario</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>metodos-pago">
                    <i class="fi fi-rr-credit-card"></i>
                    <span>Métodos de Pago</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>presentaciones">
                    <i class="fi fi-rr-soap"></i>
                    <span>Presentaciones</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>permisos">
                    <i class="fi fi-rr-user-key"></i>
                    <span>Permisos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>unidadesMedidas">
                    <i class="fi fi-rr-ruler-horizontal"></i>
                    <span>Unidades de Medida</span>
                </a>
            </li>
            
            
            

            <li>
                <a href="<?php echo APP_URL ?>clientes" title="Clientes">
                    <i class="fi fi-rr-users-medical"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>presupuestos" title="Presupuestos">
                    <i class="fi fi-rr-calculator"></i>
                    <span>Presupuestos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>ventas" title="Facturas">
                    <i class="fi fi-rr-file-invoice-dollar"></i>
                    <span>Facturación</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>cuentasCobrar" title="Cuentas por Cobrar">
                    <i class="fi fi-rr-file-invoice"></i>
                    <span>Cuentas por Cobrar</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>productos" title="Productos">
                    <i class="fi fi-rr-box-open"></i>
                    <span>Productos y Servicios</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>materiasPrimas" title="Materia Prima">
                    <i class="fi fi-rr-flask"></i>
                    <span>Materias Primas</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>proveedores" title="Proveedores">
                    <i class="fi fi-rr-seller"></i>
                    <span>Proveedores</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>compras" title="Facturas de Compra">
                    <i class="fi fi-rr-file-invoice-dollar"></i>
                    <span>Factura de Compra</span>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a href="<?php echo APP_URL ?>reportes" title="Reportes">
                    <i class="fi fi-rr-chart-histogram"></i>
                    <span>Reportes</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>configuracion" title="Configuración">
                    <i class="fi fi-rr-settings"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <li>
                <a href="assets/manual_de_usuario.pdf" title="Ayuda" target="blank">
                    <i class="fi fi-rr-info"></i>
                    <span>Ayuda</span>
                </a>
            </li>
            <li class="sidebar-divider"></li>
            <li>
                <a class="logout-btn" href="index.php?c=loginControlador&m=logout">
                    <i class="fi fi-rr-sign-out-alt"></i>
                    <span>Cerrar sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</div>