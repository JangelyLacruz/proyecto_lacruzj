<input type="hidden" class="nombreVista" value="pagos">

<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado'    => 'Gestionar Pagos',
  'tituloBtnReg'  => 'Agregar Pagos',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- ================================================================
     Ventana para registrar/actualizar un pago
     ================================================================ -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-money"></i>
          <span class="tituloModal">Agregar Pago</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body bg-light">
        <form class="formularioAjax validar form-group" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="registrar">
          <input type="hidden" name="id_pago" id="inputIdPago" value="" disabled>

          <!-- SECCIÓN: Selección de OEP -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h6 class="mb-3 text-primary"><i class="fi fi-rs-file-invoice-dollar me-2"></i>Orden Asociada</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-muted small">CÓDIGO DE ORDEN (OEP) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="id_orden_entrega_presupuesto" id="inputIdOrdenPago" readonly placeholder="Seleccione una Orden..." required>
                    <button class="btn btn-outline-primary" type="button" id="btnSeleccionarOEP">
                      <i class="fi fi-rs-search"></i> Buscar
                    </button>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-muted small">CLIENTE</label>
                  <input type="text" class="form-control" id="inputClienteOEP" readonly placeholder="...">
                </div>
              </div>

              <!-- Totales de la Orden Seleccionada -->
              <div class="d-flex justify-content-between text-white p-3 rounded mt-3 d-none" id="infoTotalesOEP" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <div>
                  <small class="d-block mb-1"><i class="fi fi-rs-box me-1"></i>TOTAL ORDEN</small>
                  <h5 class="mb-0" id="totalOrdenSel">$0.00</h5>
                </div>
                <div>
                  <small class="d-block mb-1"><i class="fi fi-rs-check-circle me-1"></i>YA PAGADO</small>
                  <h5 class="mb-0 text-white" id="pagadoOrdenSel">$0.00</h5>
                </div>
                <div>
                  <small class="d-block mb-1"><i class="fi fi-rs-info me-1"></i>RESTANTE POR PAGAR</small>
                  <h5 class="mb-0 text-warning" id="restanteOrdenSel">$0.00</h5>
                </div>
              </div>
            </div>
          </div>

          <!-- Detalles de Pago (Registro) -->
          <div id="contenedorDetallesPagoModulo">
            <!-- Los detalles se agregarán aquí dinámicamente -->
          </div>

          <button type="button" class="btn btn-outline-primary w-100 border-dashed mb-4 d-none" id="btnAgregarOtroPagoModulo">
            <i class="fi fi-rs-plus me-1"></i>Agregar otro detalle de pago
          </button>

          <!-- Comprobantes de Pago -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <h6 class="mb-3 text-primary"><i class="fi fi-rs-picture me-2"></i>Comprobantes de Pago <small class="text-muted">(Opcional, Máx 3 por Orden)</small></h6>
              
              <!-- Contenedor para mostrar comprobantes existentes al editar -->
              <div id="contenedorComprobantesExistentes" class="mb-3 row g-2"></div>

              <div class="mb-3">
                <input class="form-control" type="file" id="inputComprobantesPago" name="comprobantes[]" accept="image/jpeg, image/png, image/webp" multiple>
                <small class="text-muted mt-1 d-block">Sube los comprobantes de tus transferencias o depósitos (Formato JPG, PNG, WEBP).</small>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn text-white btnEnviarFormulario" style="background: linear-gradient(135deg, #4e54c8, #8f94fb)">Guardar Pago</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Plantilla Oculta para los Detalles del Pago -->
<div class="d-none plantillaDetallePago">
  <div class="card border-0 shadow-sm mb-3 fila-pago">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0 text-primary"><i class="fi fi-rs-receipt me-2"></i>Detalle de Pago <span class="nroDetalle"></span></h6>
        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago d-none"><i class="fi fi-rs-trash"></i></button>
      </div>
      <div class="row g-3">
        <div class="col-md-4 col-lg-3 contenedorMetodoPago">
          <label class="form-label text-muted small">MÉTODO DE PAGO</label>
          <select class="form-select form-select-sm sel-metodo-pago">
            <!-- Llenado por JS -->
          </select>
        </div>
        <div class="col-md-4 col-lg-3 d-none col-banco-emisor">
          <label class="form-label text-muted small">BANCO EMISOR</label>
          <select class="form-select form-select-sm sel-banco-emisor">
            <!-- Llenado por JS -->
          </select>
        </div>
        <div class="col-md-4 col-lg-3 d-none col-banco-receptor">
          <label class="form-label text-muted small">BANCO RECEPTOR</label>
          <select class="form-select form-select-sm sel-banco-receptor">
            <!-- Llenado por JS -->
          </select>
        </div>
        <div class="col-md-4 col-lg-3 d-none col-referencia-pago">
          <label class="form-label text-muted small">REFERENCIA</label>
          <input type="text" class="form-control form-control-sm input-referencia-pago" placeholder="N° de Referencia">
        </div>
        <div class="col-md-4 col-lg-3 col-moneda-pago d-none">
          <label class="form-label text-muted small">MONEDA</label>
          <select class="form-select form-select-sm sel-moneda-pago">
            <!-- Llenado por JS -->
          </select>
        </div>
        <div class="col-md-4 col-lg-3 contenedorMontoPago">
          <label class="form-label text-muted small">MONTO PAGADO</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fi fi-rs-money"></i></span>
            <input type="text" class="form-control input-monto-pago dinero" value="0.00">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Seleccionar OEP -->
<div class="modal fade" id="modalSelOEP" tabindex="-1" style="z-index: 1060;">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">Seleccionar Orden (OEP) con Saldo Pendiente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelOEP">
          <thead>
            <tr>
              <th>Código OEP</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Total Orden</th>
              <th>Restante</th>
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

<!-- Ventana para Ver los Detalles de un Pago -->
<div class="modal fade modalDetallesPago" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-eye"></i>
          Detalle del Pago
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light" id="contenidoDetallePago">
        <!-- Renderizado dinámicamente en JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

