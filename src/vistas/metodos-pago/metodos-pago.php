<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="metodos-pago">

<?php
$instruccionesLista = [
  'encabezado' => 'Configuración de Métodos de Pago',
  'tituloBtnReg' => 'Agregar Métodos de Pago',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ MODAL REGISTRAR ] -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i> Nuevo Método</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <input type="hidden" name="accion" value="registrar">
          <div class="col-md-12 mb-4">
            <label class="form-label fw-bold">Nombre del Método</label>
            <input type="text" class="form-control noRepetir" name="nombre_metodo_pago" required pattern="<?php echo regexNombreObj ?>" minlength="3" maxlength="50">
          </div>
          <div class="card bg-light p-3">
            <h6 class="border-bottom pb-2 mb-3">Requerimientos de Información</h6>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="necesita_moneda" value="1" id="regMon">
              <label class="form-check-label" for="regMon">¿Requiere especificar Moneda?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="necesita_banco_emisor" value="1" id="regBE">
              <label class="form-check-label" for="regBE">¿Requiere Banco Emisor?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="necesita_banco_receptor" value="1" id="regBR">
              <label class="form-check-label" for="regBR">¿Requiere Banco Receptor?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="necesita_referencia" value="1" id="regRef">
              <label class="form-check-label" for="regRef">¿Requiere Número de Referencia?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="mostrar_ecommerce" value="1">
              <label class="form-check-label" for="regRef">¿Se debe mostrar en el ecommerce?</label>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- [ MODAL ACTUALIZAR ] -->
<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Editar Método</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <input type="hidden" name="accion" value="actualizar">
          <input type="hidden" class="form-control formularioActualizar" name="id_metodo_pago" required pattern="<?php echo regexId ?>">

          <div class="col-md-12 mb-4">
            <label class="form-label fw-bold">Nombre del Método</label>
            <input type="text" class="form-control formularioActualizar noRepetir" name="nombre_metodo_pago" required pattern="<?php echo regexNombreObj ?>" minlength="3" maxlength="50">
          </div>
          <div class="card bg-light p-3">
            <h6 class="border-bottom pb-2 mb-3">Requerimientos de Información</h6>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input formularioActualizar" value="1" type="checkbox" name="necesita_moneda" id="regMon">
              <label class="form-check-label" for="regMon">¿Requiere especificar Moneda?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input formularioActualizar" value="1" type="checkbox" name="necesita_banco_emisor" id="regBE">
              <label class="form-check-label" for="regBE">¿Requiere Banco Emisor?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input formularioActualizar" value="1" type="checkbox" name="necesita_banco_receptor" id="regBR">
              <label class="form-check-label" for="regBR">¿Requiere Banco Receptor?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input formularioActualizar" value="1" type="checkbox" name="necesita_referencia" id="regRef">
              <label class="form-check-label" for="regRef">¿Requiere Número de Referencia?</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input formularioActualizar" type="checkbox" name="mostrar_ecommerce" value="1">
              <label class="form-check-label" for="regRef">¿Se debe mostrar en el ecommerce?</label>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-sync-alt me-2"></i> Actualizar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>