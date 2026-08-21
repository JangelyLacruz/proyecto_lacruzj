<input type="hidden" class="nombreVista" value="reportes">

<style>
  #mainContent {
    background-color: #f7f9fc;
    min-height: 100vh;
    font-family: 'Inter', 'Segoe UI', sans-serif;
  }
  .dash-card {
    background: #ffffff;
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease-in-out;
  }
  .dash-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  }
  .kpi-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 1px;
  }
  .kpi-value {
    font-size: 3rem;
    font-weight: 700;
    margin-top: 10px;
  }
  .color-warning { color: #f39c12; }
  .color-success { color: #2ecc71; }
  .color-purple { color: #6f42c1; }
  .color-info { color: #00d2ff; }

  .dash-btn {
    background-color: #6554C0;
    border-color: #6554C0;
    color: #fff;
    border-radius: 8px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
  }
  .dash-btn:hover {
    background-color: #5543a0;
    color: #fff;
  }
  .dash-input {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background-color: #fff;
  }
  
  .nav-tabs-custom {
    border-bottom: 2px solid #eef2f5;
    margin-bottom: 2rem;
  }
  .nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    background: transparent;
  }
  .nav-tabs-custom .nav-link.active {
    color: #2c3e50;
    border-bottom: 3px solid #6554C0;
    background: transparent;
  }

  /* Clases para manejo de estados vacíos */
  .chart-container {
    position: relative;
    height: 100%;
    width: 100%;
  }
  
  .empty-state {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    z-index: 10;
  }
  
  .empty-state.active {
    display: flex;
  }
  
  .empty-state i {
    font-size: 3rem;
    color: #dcdcdc;
    margin-bottom: 1rem;
  }
  
  .empty-state p {
    color: #6c757d;
    font-weight: 500;
    margin: 0;
  }

  .btn-export {
    background: transparent;
    border: none;
    font-size: 1.2rem;
    padding: 0;
    cursor: pointer;
    transition: transform 0.2s;
  }
  .btn-export:hover {
    transform: scale(1.1);
  }
</style>

<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">
    <!-- TOP LEVEL TABS -->
    <ul class="nav nav-pills mb-4" id="mainReportesTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="doc-reports-tab" data-bs-toggle="pill" data-bs-target="#doc-reports" type="button" role="tab">Reportes Generales</button>
      </li>
      <?php
      use src\modelos\accesosModelo;
      $objAcceso = new accesosModelo();
      if (!$objAcceso->validarPermisos('reportesEstadisticos', 'ver reportes estadísticos')):
      ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-reports-tab" data-bs-toggle="pill" data-bs-target="#stats-reports" type="button" role="tab">Reportes Estadísticos</button>
      </li>
      <?php endif; ?>
    </ul>

    <div class="tab-content" id="mainReportesTabsContent">
      <!-- TAB 1: REPORTES DOCUMENTALES -->
      <div class="tab-pane fade show active" id="doc-reports" role="tabpanel">
        <div class="container-fluid py-2">
          
          <!-- Encabezado de la Sección -->
          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
              <h3 class="m-0 fw-bold text-dark">
                <i class="fas fa-file-invoice text-primary me-2"></i>Reportes Generales
              </h3>
              <p class="text-muted m-0 mt-1" style="font-size: 0.9rem;">
                Generación e impresión de reportes y balances en formato PDF
              </p>
            </div>
            <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
              <i class="far fa-calendar-alt text-primary me-2"></i>
              <span class="fw-semibold text-secondary" style="font-size: 0.9rem;"><?php echo date('d/m/Y'); ?></span>
            </div>
          </div>

          <!-- SECCIÓN 1: REPORTES PARAMÉTRICOS (CON FILTROS) -->
          <div class="row g-4 mb-4">
            
            <!-- Tarjeta 1: Reportes de Ventas -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-chart-line me-2"></i>Reportes de Ventas
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <form id="formReporteVentas" class="h-100 d-flex flex-column justify-content-between">
                    <div>
                      <div class="form-group mb-3">
                        <label for="tipo_producto_ventas" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Tipo de Item</label>
                        <select class="form-select rounded-3 shadow-sm" id="tipo_producto_ventas" name="tipo_producto">
                          <option value="todos">Todos los Items</option>
                          <option value="productos">Solo Productos</option>
                          <option value="servicios">Solo Servicios</option>
                          <option value="especifico">Item Específico</option>
                        </select>
                      </div>

                      <div class="form-group mb-3" id="div_item_especifico_ventas" style="display: none;">
                        <label for="filtro_items_ventas" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Filtrar Items</label>
                        <select class="form-select rounded-3 shadow-sm mb-2" id="filtro_items_ventas">
                          <option value="todos">Todos los tipos</option>
                          <option value="productos">Solo Productos</option>
                          <option value="servicios">Solo Servicios</option>
                        </select>

                        <label for="id_item_ventas" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Seleccionar Item</label>
                        <select class="form-select rounded-3 shadow-sm" id="id_item_ventas" name="id_item">
                          <option value="">Cargando items...</option>
                        </select>
                      </div>

                      <div class="form-group mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Intervalo de Tiempo</label>
                        <div class="input-daterange" id="Datepicker0">
                          <div class="input-group shadow-sm rounded-3">
                            <input type="text" name="fecha_desde" class="form-control text-center" placeholder="Desde">
                            <span class="input-group-text bg-light">-</span>
                            <input type="text" name="fecha_hasta" class="form-control text-center" placeholder="Hasta">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-3 pt-2">
                      <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                        <i class="fas fa-file-pdf me-2"></i>Generar Reporte de Ventas
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Tarjeta 2: Reportes de Compras -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-shopping-cart me-2"></i>Reportes de Compras
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <form id="formReporteCompras" class="h-100 d-flex flex-column justify-content-between">
                    <div>
                      <div class="form-group mb-3">
                        <label for="tipo_materia" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Tipo de Item</label>
                        <select class="form-select rounded-3 shadow-sm" id="tipo_materia" name="tipo_item">
                          <option value="todos">Todos</option>
                          <option value="especifico">Materia Prima Específica</option>
                        </select>
                      </div>

                      <div class="form-group mb-3" id="div_materia_especifica" style="display: none;">
                        <label for="id_materia" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Seleccionar Materia Prima</label>
                        <select class="form-select rounded-3 shadow-sm" id="id_materia" name="id_materia">
                          <option value="">Seleccione una materia prima</option>
                        </select>
                      </div>

                      <div class="form-group mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Intervalo de Tiempo</label>
                        <div class="input-daterange" id="Datepicker1">
                          <div class="input-group shadow-sm rounded-3">
                            <input type="text" name="fecha_desde" class="form-control text-center" placeholder="Desde">
                            <span class="input-group-text bg-light">-</span>
                            <input type="text" name="fecha_hasta" class="form-control text-center" placeholder="Hasta">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-3 pt-2">
                      <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                        <i class="fas fa-file-pdf me-2"></i>Generar Reporte de Compras
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Tarjeta 3: Cierre de Caja -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-cash-register me-2"></i>Cierre de Caja
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <form id="formCierreCaja" class="h-100 d-flex flex-column justify-content-between">
                    <div>
                      <p class="text-muted mb-3" style="font-size: 0.88rem;">
                        Genera el balance y arqueo diario consolidado de todas las transacciones monetarias registradas.
                      </p>

                      <div class="form-group mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Seleccionar Fecha de Cierre</label>
                        <div class="input-daterange" id="Datepicker2">
                          <div class="input-group shadow-sm rounded-3">
                            <input type="text" name="fecha_cierre" class="form-control text-center" placeholder="Seleccione fecha (dd-mm-aaaa)">
                            <span class="input-group-text bg-light"><i class="far fa-calendar-check text-primary"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-3 pt-2">
                      <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                        <i class="fas fa-file-pdf me-2"></i>Generar Cierre de Caja
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>

          <!-- SECCIÓN 2: INVENTARIOS Y CATÁLOGOS -->
          <div class="row g-4 mb-4">
            
            <!-- Tarjeta 4: Reporte de Servicios -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-concierge-bell me-2"></i>Catálogo de Servicios
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    Genera un listado completo de todos los servicios ofrecidos por la empresa junto con sus tarifas y precios vigentes.
                  </p>
                  <form id="formReporteServicios">
                    <input type="hidden" name="reporte" value="reporteServicios">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                      <i class="fas fa-file-pdf me-2"></i>Generar Reporte de Servicios
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Tarjeta 5: Inventario de Productos -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-boxes me-2"></i>Inventario de Productos
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    Genera un informe completo del stock actual de productos terminados clasificados por su respectiva categoría.
                  </p>
                  <form id="formReporteProductos">
                    <input type="hidden" name="reporte" value="reporteProductos">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                      <i class="fas fa-file-pdf me-2"></i>Generar Reporte de Productos
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Tarjeta 6: Inventario de Materias Primas -->
            <div class="col-lg-4 col-md-6">
              <div class="card shadow-sm border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border-radius: 1rem 1rem 0 0;">
                  <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="fas fa-flask me-2"></i>Inventario de Materias Primas
                  </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    Genera un listado consolidado de todas las materias primas, insumos registrados y sus unidades de medida.
                  </p>
                  <form id="formReporteMateriaPrima">
                    <input type="hidden" name="reporte" value="reporteMateriaPrima">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4e54c8, #6554C0); border: none;">
                      <i class="fas fa-file-pdf me-2"></i>Generar Reporte de Materias Primas
                    </button>
                  </form>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>
      
      <!-- TAB 2: REPORTES ESTADISTICOS -->
      <?php if (!$objAcceso->validarPermisos('reportesEstadisticos', 'ver reportes estadísticos')): ?>
      <div class="tab-pane fade" id="stats-reports" role="tabpanel">
        <?php include_once "src/vistas/reportesEstadisticos/reportesEstadisticos.php"; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>