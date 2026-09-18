<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="producciones">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Producciones',
  'tituloBtnReg' => 'Agregar Orden de Producción',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<div class="modal fade modalRegistrar" tabindex="-1" aria-labelledby="modalRegistrarLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <h5 class="modal-title" id="modalRegistrarLabel">
          <i class="fas fa-industry me-2"></i> Agregar Orden de Producción
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <input type="hidden" name="accion" value="registrar">

          <div class="row mb-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-success" id="btnAgregarProducto" style="min-width: 200px; padding: 10px 20px;">
                  <i class="fas fa-plus-circle me-2"></i> Agregar Producto
                </button>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-12">
              <div class="bg-light p-3 rounded">
                <div class="row fw-bold text-muted small">
                  <div class="col-md-5">PRODUCTO</div>
                  <div class="col-md-3">UNIDAD DE MEDIDA</div>
                  <div class="col-md-2">CANTIDAD</div>
                  <div class="col-md-2">ACCIÓN</div>
                </div>
              </div>
            </div>
          </div>

          <div id="contenedorProductos" class="contenedorDetalles mb-3">

          </div>

        </div>

        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Producción
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade modalActualizar" tabindex="-1" aria-labelledby="modalActualizarLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <h5 class="modal-title" id="modalActualizarLabel">
          <i class="fas fa-edit me-2"></i> Editar Producción
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <input type="hidden" name="accion" value="actualizar">
          <input type="hidden" name="id_produccion" value="">

          <div class="row mb-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-success" id="btnAgregarProductoActualizar" style="min-width: 200px; padding: 10px 20px;">
                  <i class="fas fa-plus-circle me-2"></i> Agregar Producto
                </button>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-12">
              <div class="bg-light p-3 rounded">
                <div class="row fw-bold text-muted small">
                  <div class="col-md-5">PRODUCTO</div>
                  <div class="col-md-3">UNIDAD DE MEDIDA</div>
                  <div class="col-md-2">CANTIDAD</div>
                  <div class="col-md-2">ACCIÓN</div>
                </div>
              </div>
            </div>
          </div>

          <div id="contenedorProductosActualizar" class="contenedorDetalles">

          </div>
        </div>

        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-warning text-white px-4" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<template id="templateProductoFila">
  <div class="fila-producto card mb-3 border-start border-4 border-primary" data-indice="[INDICE]">
    <div class="card-body py-2">
      <div class="row align-items-center">
        <div class="col-md-5">
          <select class="form-select form-select-sm selectProductos"
            name="productos[[INDICE]][id_producto]"
            required>
            <option value="">Seleccione Producto</option>
          </select>
        </div>

        <div class="col-md-3">
          <input type="text"
            class="form-control form-control-sm bg-light unidad-medida-texto"
            name="productos[[INDICE]][nombre_unidad_medida]"
            placeholder="Seleccione un producto"
            readonly
            disabled>
        </div>

        <div class="col-md-2">
          <input type="number"
            class="form-control form-control-sm cantidad-producto"
            name="productos[[INDICE]][cantidad_producida]"
            min="0.01"
            step="0.01"
            value="1"
            required>
        </div>

        <div class="col-md-2">
          <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-fila w-100"
              title="Eliminar producto">
              <i class="fas fa-trash-alt me-1"></i> Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- Modal para Consultar Producción -->
<div class="modal fade modalConsultar" tabindex="-1" aria-labelledby="modalConsultarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #17a2b8, #138496);">
        <h5 class="modal-title" id="modalConsultarLabel">
          <i class="fas fa-info-circle me-2"></i> Consultar Producción
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <div class="mb-2">
              <small class="text-muted">ID Producción</small>
              <h6 class="mb-0 textoIdProduccion">-</h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-2">
              <small class="text-muted">Fecha de Producción</small>
              <h6 class="mb-0 textoFechaProduccion">-</h6>
            </div>
          </div>
        </div>
        <hr>
        <h6 class="mb-3"><i class="fas fa-boxes me-2"></i>Productos Producidos</h6>
        <div class="table-responsive">
          <table class="table table-striped table-bordered text-center">
            <thead class="table-dark">
              <tr>
                <th>Producto</th>
                <th>Cantidad Producida</th>
                <th>Unidad de Medida</th>
              </tr>
            </thead>
            <tbody id="tbodyProductosConsulta">
              <tr>
                <td colspan="3" class="text-center text-muted">Cargando...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>