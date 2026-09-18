<?php

// Incluimos el CSS propio del módulo de servicios
echo '<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/servicios.css">';


use src\config\inc\componentesModelo;

$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado'   => 'Gestionar Servicios',
  'tituloBtnReg' => 'Agregar Servicios',
];
echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="servicios">

<!-- Form Registrar -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fi fi-rs-broom me-2"></i> Agregar Nuevo Servicio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Nombre del Servicio <span class="text-danger">*</span></label>
              <input type="text" class="form-control noRepetir" name="nombre_servicio"
                pattern="<?php echo regexNombreObj ?>"
                minlength="<?php echo minRegexNombreObj ?>"
                maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Unidad de Medida <span class="text-danger">*</span></label>
              <select class="form-select selectUnidadMedida" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Precio ($) <span class="text-danger">*</span></label>
              <input type="text" class="form-control dinero" name="precio_servicio"
                pattern="<?php echo regexPrecioFront ?>"
                minlength="<?php echo minRegexPrecioFront ?>"
                maxlength="<?php echo maxRegexPrecioFront ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Mostrar en E-Commerce</label>
              <select class="form-select" name="mostrar_ecommerce" required>
                <option value="0">No, esconder de tienda</option>
                <option value="1">Sí, mostrar en tienda</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Foto del Servicio</label>
              <input type="file" class="form-control inputFotoServicio" name="foto_servicio" accept="image/*">
              <div class="previewFotoServicio mt-2 d-none text-center">
                <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" imgRespaldo="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Preview" class="img-thumbnail" style="max-height:120px; object-fit:cover; border-radius:8px;">
                <small class="d-block text-muted mt-1">Vista previa</small>
              </div>
            </div>

            <!-- Productos que consume el servicio -->
            <div class="col-12 mb-3">
              <div class="serv-productos-card">
                <div class="serv-productos-header">
                  <span class="serv-header-title">
                    <i class="fi fi-rs-box"></i> Productos que consume el servicio
                  </span>
                  <span class="serv-badge-count badgeCantProdServ">0 productos</span>
                </div>
                <div class="serv-productos-body">
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th style="width:130px">Cantidad</th>
                          <th style="width:70px" class="text-center">Quitar</th>
                        </tr>
                      </thead>
                      <tbody class="cuerpoTablaProductosServicio"></tbody>
                    </table>
                  </div>
                  <button type="button" class="btn serv-btn-agregar btnAbrirSelectorProdServ">
                    <i class="fi fi-rs-plus me-1"></i> Agregar Producto
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Servicio
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Form Actualizar -->
<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fi fi-rs-pen-circle me-2"></i> Editar Servicio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_servicio" class="formularioActualizar">
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Nombre del Servicio <span class="text-danger">*</span></label>
              <input type="text" class="form-control noRepetir formularioActualizar" name="nombre_servicio"
                pattern="<?php echo regexNombreObj ?>"
                minlength="<?php echo minRegexNombreObj ?>"
                maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Unidad de Medida <span class="text-danger">*</span></label>
              <select class="form-select selectUnidadMedida formularioActualizar" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Precio ($) <span class="text-danger">*</span></label>
              <input type="text" class="form-control formularioActualizar dinero" name="precio_servicio"
                pattern="<?php echo regexPrecioFront ?>"
                minlength="<?php echo minRegexPrecioFront ?>"
                maxlength="<?php echo maxRegexPrecioFront ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Mostrar en E-Commerce</label>
              <select class="form-select formularioActualizar" name="mostrar_ecommerce" required>
                <option value="0">No, esconder de tienda</option>
                <option value="1">Sí, mostrar en tienda</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Foto del Servicio</label>
              <div class="previewFotoServicioActual mb-2 text-center d-none">
                <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" imgRespaldo="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Foto actual" class="img-thumbnail fotoServicioActualImg" style="max-height:120px; object-fit:cover; border-radius:8px;">
                <small class="d-block text-muted mt-1">Foto actual del servicio</small>
              </div>
              <input type="file" class="form-control formularioActualizar inputFotoServicio" name="foto_servicio" accept="image/*">
              <div class="previewFotoServicio mt-2 d-none text-center">
                <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" imgRespaldo="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Preview" class="img-thumbnail" style="max-height:120px; object-fit:cover; border-radius:8px;">
                <small class="d-block text-muted mt-1">Nueva foto (preview)</small>
              </div>
            </div>

            <!-- Productos que consume el servicio -->
            <div class="col-12 mb-3">
              <div class="serv-productos-card">
                <div class="serv-productos-header">
                  <span class="serv-header-title">
                    <i class="fi fi-rs-box"></i> Productos que consume el servicio
                  </span>
                  <span class="serv-badge-count badgeCantProdServ">0 productos</span>
                </div>
                <div class="serv-productos-body">
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th style="width:130px">Cantidad</th>
                          <th style="width:70px" class="text-center">Quitar</th>
                        </tr>
                      </thead>
                      <tbody class="cuerpoTablaProductosServicio"></tbody>
                    </table>
                  </div>
                  <button type="button" class="btn serv-btn-agregar btnAbrirSelectorProdServ">
                    <i class="fi fi-rs-plus me-1"></i> Agregar Producto
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>