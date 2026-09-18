<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado' => 'Gestionar Sucursales de Empresas de Envios',
  'tituloBtnReg' => 'Agregar Sucursales',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<input type="hidden" class="nombreVista" value="sucursalesEmpresasEnvios">

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarUsuarioModalLabel">
          <i class="fas fa-user-plus me-2"></i> Agregar Nueva Sucursal de Empresa de Envíos
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" novalidate>
        <input type="hidden" name="accion" value="registrar" required>
        <input type="hidden" class="inputLatitud" name="latitud_sucursal" required>
        <input type="hidden" class="inputLongitud" name="longitud_sucursal" required>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Empresa De Envíos</label>
              <select class="selectEmpresaEnvios form-control" name="id_empresa_envios" required></select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de la Sucursal</label>
              <input type="text" class="form-control " name="nombre_sucursal_empresa" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <div id="mapSucursal1" class="divMapaPago"></div>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">URL de la dirección</label>
              <input type="text" class="form-control inputURL" name="url_direccion" pattern="<?php echo regexUrl ?>" minlength="<?php echo minRegexUrl ?>" maxlength="<?php echo maxRegexUrl ?>" required>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- [ FORMULARIO REGISTRAR ] FIN -->

<!-- [ FORMULARIO EDITAR ] COMIENZO -->
<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-edit me-2"></i> Editar
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" novalidate>
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id_sucursal_empresa_envios">
        <input type="hidden" class="inputLatitud" name="latitud_sucursal">
        <input type="hidden" class="inputLongitud" name="longitud_sucursal">

        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Empresa De Envíos</label>
              <select class="selectEmpresaEnvios form-control" name="id_empresa_envios" required></select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de la Sucursal</label>
              <input type="text" class="form-control " name="nombre_sucursal_empresa" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <div id="mapSucursal2" class="divMapaPago"></div>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">URL de la dirección (Sólo si la quiere cambiar)</label>
              <input type="text" class="form-control inputURL" name="url_direccion" pattern="<?php echo regexUrl ?>" minlength="<?php echo minRegexUrl ?>" maxlength="<?php echo maxRegexUrl ?>">
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- [ FORMULARIO EDITAR ] FIN -->