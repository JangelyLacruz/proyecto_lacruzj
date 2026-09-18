<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="bancos">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestión de Bancos',
  'tituloBtnReg' => 'Agregar Bancos',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ MODAL REGISTRAR ] -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-university me-2"></i> Nuevo Registro de Banco
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-12 mb-3">
              <label class="form-label fw-bold">Nombre del Banco</label>
              <input type="text" class="form-control noRepetir" name="nombre_banco" required
                pattern="<?php echo regexNombreObj ?>"
                minlength="3" maxlength="50">
              <div class="form-text">Ingrese el nombre oficial de la entidad bancaria.</div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Registrar Banco
          </button>
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
        <h5 class="modal-title">
          <i class="fas fa-edit me-2"></i> Actualizar Banco
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_banco">

            <div class="col-12 mb-3">
              <label class="form-label fw-bold">Nombre del Banco</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="nombre_banco" required
                pattern="<?php echo regexNombreObj ?>"
                minlength="3" maxlength="50">
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-sync-alt me-2"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>