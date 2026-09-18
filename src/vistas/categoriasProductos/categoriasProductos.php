<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="categoriasProductos">
<?php
$instruccionesLista = [
  'encabezado' => 'Categorías de Productos',
  'tituloBtnReg' => 'Agregar Categoría de Productos',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fi fi-rr-tags me-2"></i> Agregar Nueva Categoría
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">

            <div class="col-12 mb-3">
              <label class="form-label">Nombre de la Categoría</label>
              <input type="text" class="form-control text-uppercase noRepetir" name="nombre_categoria_producto"
                pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$"
                minlength="3"
                maxlength="50"
                placeholder="Ej: BEBIDAS"
                required>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">¿Requiere Materia Prima al producirse?</label>
              <select class="form-select" name="necesitan_materias_primas" required>
                <option value="">Seleccione una opción...</option>
                <option value="1">SÍ, son productos que fabricamos</option>
                <option value="0">NO, son productos de reventa</option>
              </select>
            </div>

          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fi fi-rr-cross me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fi fi-rr-disk me-2"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fi fi-rr-edit me-2"></i> Editar Categoría
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_categoria_producto" class="formularioActualizar">

            <div class="col-12 mb-3">
              <label class="form-label">Nombre de la Categoría</label>
              <input type="text" class="form-control text-uppercase noRepetir formularioActualizar" name="nombre_categoria_producto"
                pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$"
                minlength="3"
                maxlength="50"
                required>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">¿Requiere Materia Prima al producirse?</label>
              <select class="form-select formularioActualizar" name="necesitan_materias_primas" required>
                <option value="">Seleccione una opción...</option>
                <option value="1">SÍ, son productos que fabricamos</option>
                <option value="0">NO, son productos de reventa</option>
              </select>
            </div>

          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fi fi-rr-cross me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fi fi-rr-disk me-2"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
