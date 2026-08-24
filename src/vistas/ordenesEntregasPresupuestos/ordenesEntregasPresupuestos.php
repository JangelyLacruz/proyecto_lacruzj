?<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/ordenesEntregasPresupuestos.css">
<input type="hidden" class="nombreVista" value="ordenesEntregasPresupuestos">

<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado'    => 'Gestionar Órdenes de Entregas y Presupuestos',
  'tituloBtnReg'  => 'Nueva Orden',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- ================================================================
     Ventana principal para registrar una nueva orden
     ================================================================ -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header text-white fact-header-grad">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-file-invoice-dollar"></i>
          <span id="tituloModalOrden">Nueva Orden de Entrega</span>
        </h5>
        <button type="button" class="btn-close btn-close-white"
          data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formOrden" method="POST" novalidate>
          <input type="hidden" name="accion" value="registrar">

          <!-- SECCIÓN: Datos generales del cliente y la orden -->
          <div class="row g-3 mb-3">

            <!-- Campo para buscar cliente mediante modal -->
            <div class="col-md-3">
              <label class="form-label fw-semibold">
                Cédula / RIF del Cliente
                <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="text"
                  class="form-control"
                  id="inputCedulaClienteOrden"
                  name="rif_cedula_cliente"
                  readonly
                  placeholder="Seleccione un cliente..."
                  required>
                <button class="btn btn-outline-primary" type="button" id="btnBuscarClienteOrden">
                  <i class="fi fi-rs-search"></i> Buscar
                </button>
              </div>
            </div>

            <!-- Este campo es de solo lectura y se llena solito con el nombre del cliente -->
            <div class="col-md-5">
              <label class="form-label fw-semibold">Nombre del Cliente</label>
              <input type="text"
                class="form-control"
                id="nombreClienteOrden"
                readonly
                placeholder="Se completa automáticamente">
            </div>

            <!-- Fecha actual (la ponemos automáticamente al abrir) -->
            <div class="col-md-2">
              <label class="form-label fw-semibold">Fecha</label>
              <input type="text"
                class="form-control"
                id="fechaOrdenDisplay"
                readonly>
            </div>

            <!-- Total a pagar (se va sumando solo) -->
            <div class="col-md-2">
              <label class="form-label fw-semibold">Total General</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="text"
                  class="form-control fw-bold text-success"
                  id="totalGeneralOrden"
                  name="total_general"
                  readonly
                  value="0.00">
              </div>
            </div>

          </div>

          <!-- Pestañas para movernos entre productos, servicios y delivery -->
          <ul class="nav nav-tabs" id="tabsOrden" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab"
                data-bs-target="#tabProductosOrden" type="button">
                <i class="fi fi-rs-box me-1"></i>
                Productos
                <span class="badge bg-primary ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeProdOrden">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab"
                data-bs-target="#tabServiciosOrden" type="button">
                <i class="fi fi-tr-room-service"></i>
                Servicios
                <span class="badge bg-success ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeServOrden">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation" id="liTabDeliveryOrden">
              <button class="nav-link" data-bs-toggle="tab"
                data-bs-target="#tabDeliveryOrden" type="button" id="btnTabDeliveryOrden">
                <i class="fi fi-rs-truck-side me-1"></i>
                Delivery
                <span class="badge bg-secondary ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeDelOrden">No</span>
              </button>
            </li>
          </ul>

          <div class="tab-content mt-3 border rounded p-3">

            <!-- Contenido de la pestaña de productos -->
            <div class="tab-pane fade show active"
              id="tabProductosOrden" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                  <i class="fi fi-rs-box me-2"></i>Productos
                </h6>
                <button type="button"
                  class="btn text-white btn-sm"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb);"
                  id="btnAgregarProductoOrden">
                  <i class="fi fi-rs-plus me-1"></i>Agregar Producto
                </button>
              </div>
              <div id="contenedorProductosOrden">
                <div class="fact-empty-state">
                  <i class="fi fi-rs-box-open"></i>
                  <p>No hay productos agregados</p>
                </div>
              </div>

              <div class="d-flex justify-content-end mt-2">
                <span class="badge bg-light text-dark border">
                  Subtotal productos:
                  <strong id="subtotalProdOrden">$0.00</strong>
                </span>
              </div>
            </div>

            <!-- Contenido de la pestaña de servicios -->
            <div class="tab-pane fade"
              id="tabServiciosOrden" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                  <i class="fi fi-tr-room-service"></i>Servicios
                </h6>
                <button type="button"
                  class="btn text-white btn-sm"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb);"
                  id="btnAgregarServicioOrden">
                  <i class="fi fi-rs-plus me-1"></i>Agregar Servicio
                </button>
              </div>

              <!-- ALERTA DE STOCK PARA SERVICIOS -->
              <div class="alert alert-warning d-flex align-items-center p-2 mb-3" role="alert" style="font-size: 0.9rem;">
                <i class="fi fi-rs-exclamation me-2 fs-5"></i>
                <div>
                  <strong>Aviso:</strong> El stock de los materiales consumidos por los servicios <u>no se descuenta ahora</u>. Se descontará cuando la Orden de Servicio pase a estado <strong>"Ejecutado"</strong>. Asegúrese de contar con stock suficiente para ese momento.
                </div>
              </div>
              <div id="contenedorServiciosOrden">
                <div class="fact-empty-state">
                  <i class="fi fi-tr-room-service"></i>
                  <p>No hay servicios agregados</p>
                </div>
              </div>
              <div class="d-flex justify-content-end mt-2">
                <span class="badge bg-light text-dark border">
                  Subtotal servicios:
                  <strong id="subtotalServOrden">$0.00</strong>
                </span>
              </div>
            </div>

            <!-- Contenido de la pestaña para solicitar delivery -->
            <div class="tab-pane fade"
              id="tabDeliveryOrden" role="tabpanel">
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input"
                    type="checkbox"
                    id="chkDeliveryOrden"
                    name="incluye_delivery">
                  <label class="form-check-label fw-semibold"
                    for="chkDeliveryOrden">
                    ¿Incluir Delivery?
                  </label>
                </div>
              </div>

              <div id="seccionDeliveryOrden" class="d-none">
                <div class="row g-3">
                  <!-- Contenedor del mapita para elegir la ubicación -->
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-semibold mb-0">
                        <i class="fi fi-rs-marker me-1"></i>Seleccione la ubicación de entrega
                      </label>
                      <button type="button" class="btn btn-outline-primary btn-sm" id="btnMiUbicacionOrden">
                        <i class="fi fi-rs-navigation me-1"></i>Mi ubicación
                      </button>
                    </div>
                    <div id="mapaDeliveryOrden" class="rounded border" style="height: 350px; z-index: 1;"></div>
                  </div>
                  <!-- Datos que calculamos automáticamente con el mapa -->
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Dirección detectada</label>
                    <input type="text" class="form-control form-control-sm" id="direccionDeliveryOrden" readonly placeholder="Haga clic en el mapa...">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Distancia</label>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" id="distanciaDeliveryOrden" readonly value="0">
                      <span class="input-group-text">km</span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Ruta asignada</label>
                    <input type="text" class="form-control form-control-sm" id="rutaAsignadaDeliveryOrden" readonly placeholder="—">
                    <input type="hidden" id="idRutaDeliveryOrden" value="">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Costo Delivery</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">$</span>
                      <input type="text" class="form-control" id="costoDeliveryOrden" readonly value="0.00">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">REPARTIDOR (Opcional)</label>
                    <div class="input-group input-group-sm mb-1 shadow-sm rounded">
                      <span class="input-group-text bg-white text-muted border-end-0" id="iconRepartidorOrden">
                        <i class="fi fi-rs-motorcycle"></i>
                      </span>
                      <input type="text" class="form-control text-uppercase border-start-0 ps-0" id="inputCedulaRepartidorOrden" placeholder="Ej: V12345678" maxlength="15" style="box-shadow: none;">
                    </div>
                    <div id="feedbackRepartidorOrden" class="text-center" style="min-height: 20px; font-size: 0.85em;"></div>
                    <input type="hidden" id="selectRepartidorOrden" value="">
                  </div>
                  <!-- Estos campos ocultos guardan las coordenadas para la base de datos -->
                  <input type="hidden" id="latDeliveryOrden" value="">
                  <input type="hidden" id="lngDeliveryOrden" value="">
                </div>
              </div>
            </div>

          </div><!-- /Fin del contenedor de pestañas -->

          <div id="resumenVolumenOrden" class="mt-3 p-2 bg-light rounded border-start border-4 border-info shadow-sm d-none">
            <small class="text-muted fw-bold d-block mb-2"><i class="fi fi-rs-info me-1"></i>Resumen de Consumo de Stock (General)</small>
            <div id="listaResumenVolumen" class="small" style="max-height: 120px; overflow-y: auto; padding-right: 5px;"></div>
          </div>

          <!-- Área de totales donde mostramos el resumen de la orden -->
          <div class="fact-totales-panel mt-3">
            <div class="fact-total-row">
              <span>Subtotal Productos</span>
              <span id="resumenProdOrden">$0.00</span>
            </div>
            <div class="fact-total-row">
              <span>Subtotal Servicios</span>
              <span id="resumenServOrden">$0.00</span>
            </div>
            <div class="fact-total-row" id="rowDeliveryResumen"
              style="display:none;">
              <span>Delivery</span>
              <span id="resumenDeliveryOrden">$0.00</span>
            </div>
            <div class="fact-total-row fact-total-grande">
              <span>TOTAL GENERAL</span>
              <span id="resumenTotalOrden">$0.00</span>
            </div>
          </div>

          <input type="hidden" id="estadoSeleccionadoOrden" name="estado_orden" value="2">

        </form>
      </div><!-- /Fin del cuerpo de la ventana -->

      <div class="modal-footer">
        <button type="button"
          class="btn btn-danger"
          data-bs-dismiss="modal">
          <i class="fi fi-rs-cross me-1"></i>Cancelar
        </button>
        <button type="button"
          class="btn btn-primary px-4"
          style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
          id="btnGuardarOrden"
          disabled>
          <i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================================================================
     El modal de productos se arma con Javascript cuando lo necesitamos
     ================================================================ -->

<!-- ================================================================
     Modal para Seleccionar Cliente
     ================================================================ -->
<div class="modal fade" id="modalSelClienteOrden" tabindex="-1" style="z-index: 1060;">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white fact-header-grad">
        <h5 class="modal-title"><i class="fi fi-rs-users me-2"></i>Seleccionar Cliente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelClienteOrden">
          <thead>
            <tr>
              <th>Cédula / RIF</th>
              <th>Nombre / Razón Social</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Llenado dinámicamente -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     Ventana para mirar con calma todos los detalles de una orden
     ================================================================ -->
<div class="modal fade modalDetallesOrden" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header fact-header-grad text-white">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-eye"></i>
          Detalle de Orden de Entrega
        </h5>
        <button type="button" class="btn-close btn-close-white"
          data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <!-- ALERTA DE STOCK PARA SERVICIOS -->
        <div class="alert alert-warning d-flex align-items-center p-2 mb-3" role="alert" style="font-size: 0.9rem;">
          <i class="fi fi-rs-exclamation me-2 fs-5"></i>
          <div>
            <strong>Aviso:</strong> El stock de los materiales consumidos por los servicios de esta orden <u>no se descuenta aquí</u>. Se descontará cuando sus respectivas Órdenes de Servicio pasen a estado <strong>"Ejecutado"</strong>.
          </div>
        </div>
        <div id="contenidoDetalleOrden">
          <!-- Todo esto lo llenamos usando Javascript dependiendo de la orden -->
        </div>
      </div>

      <div class="modal-footer">
        <div id="botonesExtraDetalle" class="me-auto"></div>
        <button type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">Cerrar</button>
        <button type="button"
          class="btn btn-danger"
          id="btnAnularOrdenModal">
          <i class="fi fi-rs-ban me-1"></i>Anular Orden
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================================================================
     Ventana pequeñita para elegir el estado en que quedará la orden
     ================================================================ -->
<div class="modal fade" id="modalEstadosOrden" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white fact-pagos-grad">
        <h6 class="modal-title"><i class="fi fi-rs-settings me-2"></i>Estado de la Orden</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">Seleccione el estado de la Orden antes de guardar:</p>
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-outline-success btn-estado-orden text-start" data-estado="1">
            <i class="fi fi-rs-check-circle me-2"></i>1. Procesada y Pagada
          </button>
          <button type="button" class="btn btn-outline-warning btn-estado-orden text-start" data-estado="2">
            <i class="fi fi-rs-time-fast me-2"></i>2. Procesada y sin pagar
          </button>
          <button type="button" class="btn btn-outline-success btn-estado-orden text-start" data-estado="3">
            <i class="fi fi-rs-truck-side me-2"></i>3. Pagada y despachada
          </button>
          <button type="button" class="btn btn-outline-info btn-estado-orden text-start" data-estado="4">
            <i class="fi fi-rs-box-alt me-2"></i>4. Despachada y sin pagar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Seleccionar Producto -->
<div class="modal fade" id="modalSelProdOrden" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white fact-purple-blue-grad">
        <h5 class="modal-title">Seleccionar Producto</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelProdOrden">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Stock</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Se llena dinámicamente mediante JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Seleccionar Servicio -->
<div class="modal fade" id="modalSelServOrden" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white fact-purple-blue-grad">
        <h5 class="modal-title">Seleccionar Servicio</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelServOrden">
          <thead>
            <tr>
              <th>Servicio</th>
              <th>Precio</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Se llena dinámicamente mediante JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Ubicación de cada Servicio -->
<div class="modal fade" id="modalUbicacionServicioOrden" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
        <h5 class="modal-title"><i class="fi fi-rs-marker me-2"></i>Ubicación del Servicio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-semibold mb-0">Haga clic en el mapa para ubicar el servicio</label>
              <div>
                <button type="button" class="btn btn-outline-info btn-sm me-1 d-none" id="btnCopiarUbicacionDelivery">
                  <i class="fi fi-rs-copy me-1"></i>Copiar del Delivery
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" id="btnMiUbicacionServicio">
                  <i class="fi fi-rs-navigation me-1"></i>Mi ubicación
                </button>
              </div>
            </div>
            <div id="mapaUbicacionServicio" class="rounded border" style="height: 350px; z-index: 1;"></div>
          </div>
          <div class="col-md-12">
            <label class="form-label fw-semibold">Dirección aproximada</label>
            <input type="text" class="form-control form-control-sm" id="direccionServicioOrden" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Distancia calculada</label>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control" id="distanciaServicioOrden" readonly value="0">
              <span class="input-group-text">km</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Ruta detectada</label>
            <input type="text" class="form-control form-control-sm" id="rutaAsignadaServicioOrden" readonly>
            <input type="hidden" id="idRutaServicioOrden" value="">
            <input type="hidden" id="latServicioOrden" value="">
            <input type="hidden" id="lngServicioOrden" value="">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success text-white" id="btnConfirmarUbicacionServicio">Confirmar Ubicación</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Registrar Nuevo Repartidor -->
<div class="modal fade" id="modalRegistroRepartidorOrden" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white fact-repartidor-grad">
        <h5 class="modal-title"><i class="fi fi-rs-motorcycle me-2"></i>Registrar Nuevo Repartidor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light">
        <form id="formRegistroRepartidorOrden" class="px-2 py-1">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small mb-1"><i class="fi fi-rs-id-badge me-1"></i>CÉDULA DEL REPARTIDOR</label>
            <input type="text" class="form-control fw-bold text-primary shadow-sm" id="floatingCedula" name="cedula_repartidor" readonly style="background-color: #e9ecef; border: 1px solid #ced4da;">
            <input type="hidden" name="codigo_rif_cedula_repartidor" value="">
          </div>
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted small mb-1"><i class="fi fi-rs-user me-1"></i>NOMBRE</label>
              <input type="text" class="form-control text-uppercase shadow-sm" id="inputNombreRepartidorReg" name="nombre_repartidor" required placeholder="Ej: Juan">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted small mb-1"><i class="fi fi-rs-user me-1"></i>APELLIDO</label>
              <input type="text" class="form-control text-uppercase shadow-sm" id="inputApellidoRepartidorReg" name="apellido_repartidor" required placeholder="Ej: Pérez">
            </div>
          </div>
          <div class="mb-1">
            <label class="form-label fw-bold text-muted small mb-1"><i class="fi fi-rs-smartphone me-1"></i>TELÉFONO <span class="fw-normal">(Ej: 04141234567)</span></label>
            <input type="text" class="form-control shadow-sm" id="inputTelefonoRepartidorReg" name="telefono_repartidor" maxlength="11" required placeholder="11 dígitos">
          </div>
          <div id="feedbackTelefonoRepartidorReg" class="text-center small" style="min-height: 20px;"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn text-white fact-purple-blue-grad" id="btnGuardarRepartidorOrden" disabled>Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Detalles del Pago -->
<div class="modal fade" id="modalPagosOrden" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white fact-pagos-grad">
        <h5 class="modal-title" id="tituloModalPagosOrden"><i class="fi fi-rs-credit-card me-2"></i>Detalles del Pago</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light validar">
        <!-- Totales -->
        <div class="d-flex justify-content-between text-white p-3 rounded mb-4 fact-pagos-grad">
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-box me-1"></i>TOTAL A PAGAR</small>
            <h5 class="mb-0" id="pagoTotalPagar">$0.00</h5>
          </div>
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-check-circle me-1"></i>CANCELADO</small>
            <h5 class="mb-0 text-white" id="pagoCancelado">$0.00</h5>
          </div>
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-info me-1"></i>RESTANTE</small>
            <h5 class="mb-0 text-warning" id="pagoRestante">$0.00</h5>
          </div>
        </div>

        <!-- Detalles de Pago -->
        <div id="contenedorDetallesPago">
          <div class="card border-0 shadow-sm mb-3 fila-pago">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary"><i class="fi fi-rs-receipt me-2"></i>Detalle de Pago</h6>
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago d-none"><i class="fi fi-rs-trash"></i></button>
              </div>
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label text-muted small">MÉTODO DE PAGO</label>
                  <select class="form-select form-select-sm sel-metodo-pago">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-3 d-none col-banco-emisor">
                  <label class="form-label text-muted small">BANCO EMISOR</label>
                  <select class="form-select form-select-sm sel-banco-emisor">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-3 d-none col-banco-receptor">
                  <label class="form-label text-muted small">BANCO RECEPTOR</label>
                  <select class="form-select form-select-sm sel-banco-receptor">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-3 d-none col-referencia-pago">
                  <label class="form-label text-muted small">REFERENCIA</label>
                  <input type="text" class="form-control form-control-sm input-referencia-pago" placeholder="N° de Referencia">
                </div>
                <div class="col-md-3 col-moneda-pago">
                  <label class="form-label text-muted small">MONEDA</label>
                  <select class="form-select form-select-sm sel-moneda-pago">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-muted small">MONTO PAGADO</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fi fi-rs-money"></i></span>
                    <input type="text" class="form-control input-monto-pago dinero validado" pattern="<?php echo regexPrecioFront ?>" minlength="<?php echo minRegexPrecioFront ?>" maxlength="<?php echo maxRegexPrecioFront ?>" required placeholder="Ej: 12.00">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-primary w-100 border-dashed mb-3" id="btnAgregarOtroPago">
          <i class="fi fi-rs-plus me-1"></i>Agregar otro detalle de pago
        </button>

        <!-- Comprobantes de Pago -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h6 class="mb-3 text-primary"><i class="fi fi-rs-picture me-2"></i>Comprobantes de Pago <small class="text-muted">(Opcional, Máx 3 por Orden)</small></h6>
            <div class="mb-3">
              <input class="form-control" type="file" id="inputComprobantesPago" name="comprobantes[]" accept="image/jpeg, image/png" multiple>
              <small class="text-muted mt-1 d-block">Sube los comprobantes de tus transferencias o depósitos (Formato JPG, PNG).</small>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Volver</button>
        <button type="button" class="btn btn-primary px-4 fact-pagos-grad" id="btnConfirmarPago">Confirmar Pago</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver Ubicación de un Servicio en los Detalles -->
<div class="modal fade" id="modalMapaDetalleServicio" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
        <h5 class="modal-title"><i class="fi fi-rs-marker me-2"></i>Ubicación de Ejecución del Servicio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div id="contenedorMapaDetalleServicio" style="height: 400px; width: 100%; z-index: 1;"></div>
      </div>
    </div>
  </div>
</div>

