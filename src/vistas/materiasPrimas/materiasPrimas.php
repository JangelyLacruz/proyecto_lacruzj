<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="materiasPrimas">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Materias Primas',
  'tituloBtnReg' => 'Agregar Materias Primas',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarUsuarioModalLabel">
          <i class="fas fa-user-plus me-2"></i> Agregar nueva Materia Prima
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de la Materia Prima</label>
              <input type="text" class="form-control noRepetir" name="nombre_materia_prima" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Unidad de medida</label>
              <select class="form-select selectUnidadMedida" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Actual</label>
              <input type="number" class="form-control" name="stock_materia_prima" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Mínimo</label>
              <input type="number" class="form-control" name="stock_minimo_materia_prima" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Precio Unitario ($)</label>
              <input type="number" step="0.01" class="form-control" name="precio_materia_prima" pattern="<?php echo regexPrecio ?>" minlength="<?php echo minRegexPrecio ?>" maxlength="<?php echo maxRegexPrecio ?>" required>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Presentaciones Disponibles</label>
              <div class="form-text mb-2">Seleccione las presentaciones para esta materia prima</div>
              <div class="contenedor-presentaciones row"></div>
              <div class="mt-3">
                <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas">
                  <i class="fas fa-check-square me-1"></i> Seleccionar todas
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas">
                  <i class="fas fa-square me-1"></i> Deseleccionar todas
                </button>
              </div>
            </div>

            <div class="inputs-ocultos-presentaciones">
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Materia Prima
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-edit me-2"></i> Editar Materia Prima
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_materia_prima" class="formularioActualizar">

            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de la Materia Prima</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="nombre_materia_prima" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Unidad de medida</label>
              <select class="form-select selectUnidadMedida formularioActualizar" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Actual</label>
              <input type="number" class="form-control formularioActualizar" name="stock_materia_prima" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Mínimo</label>
              <input type="number" class="form-control formularioActualizar" name="stock_minimo_materia_prima" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Precio Unitario ($)</label>
              <input type="number" step="0.01" class="form-control formularioActualizar" name="precio_materia_prima" pattern="<?php echo regexPrecio ?>" minlength="<?php echo minRegexPrecio ?>" maxlength="<?php echo maxRegexPrecio ?>" required>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Presentaciones Disponibles</label>
              <div class="form-text mb-2 ">Seleccione las presentaciones para esta materia prima</div>

              <div class="contenedor-presentaciones row formularioActualizar"></div>

              <div class="mt-3">
                <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas">
                  <i class="fas fa-check-square me-1"></i> Seleccionar todas
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas">
                  <i class="fas fa-square me-1"></i> Deseleccionar todas
                </button>
              </div>
            </div>

            <div class="inputs-ocultos-presentaciones">
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