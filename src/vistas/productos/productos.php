<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado' => 'Gestionar Productos',
  'tituloBtnReg' => 'Agregar Producto',
];
echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="productos">

<!-- Plantilla fila materias primas -->
<template class="plantillaFilaMP d-none">
  <tr>
    <td>
      <select
        class="form-select select-materia-prima"
        name="materias_primas-[COD-FILA]-id_materia_prima">
      </select>
    </td>
    <td>
      <input
        type="text"
        name="materias_primas-[COD-FILA]-cantidad_materia_prima"
        class="form-control input-cantidad-materia dineroPositivo"
        placeholder="Cantidad">
    </td>
    <td class="costo-unitario"></td>
    <td class=" subtotal"></td>
    <td class="d-flex justify-content-center align-items-center">
      <button type="button" class="btn btn-sm btn-danger btn-eliminar-materia">
        <i class="fi fi-rr-trash-check p-1 fs-5"></i>
      </button>
    </td>
  </tr>
</template>

<!-- Form Registrar -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fas fa-box me-2"></i> Agregar Nuevo Producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-4 mb-3">
              <label class="form-label">Nombre del Producto</label>
              <input type="text" class="form-control noRepetir" name="nombre_producto" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Stock Inicial</label>
              <input type="text" class="form-control dineroPositivo" name="stock_producto" pattern="<?php echo regexPrecioFront ?>" minlength="<?php echo minRegexPrecioFront ?>" maxlength="<?php echo maxRegexPrecioFront ?>" value="0" required>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Stock Mínimo</label>
              <input type="text" class="form-control dineroPositivo" name="stock_minimo_producto" pattern="<?php echo regexPrecioFront ?>" minlength="<?php echo minRegexPrecioFront ?>" maxlength="<?php echo maxRegexPrecioFront ?>" value="5" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Unidad de medida</label>
              <select class="form-select selectUnidadMedida" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Categoría del Producto</label>
              <select class="form-select selectCategoriaProducto selectCategoria" name="id_categoria_producto" required>
                <option value="">Seleccione una categoría</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Precio Divisas ($)</label>
              <input
                type="text"
                class="form-control dineroPositivo"
                name="precio_producto"
                pattern="<?php echo regexPrecioFront; ?>"
                minlength="<?php echo minRegexPrecioFront; ?>"
                maxlength="<?php echo maxRegexPrecioFront; ?>"
                required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Precio BCV (Bs)</label>
              <input disabled type="text" class="form-control precioProductoBCV">
            </div>
            <div class="col-12 mb-3 campos-fabricado" style="display: none;">
              <label class="form-label">Materias Primas para Fabricación</label>
              <div class="form-text mb-2">Agregue las materias primas necesarias para fabricar este producto</div>
              <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriasPrimas">
                  <thead>
                    <tr>
                      <th>Materia Prima</th>
                      <th>Cantidad Requerida</th>
                      <th>Costo Unitario ($)</th>
                      <th>Subtotal ($)</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody class="cuerpoTablaMateriasPrimas"></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" class="text-end"><strong>Total Costo:</strong></td>
                      <td colspan="2">
                        <strong id="totalCostoMaterias">0.00$</strong>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <button type="button" class="btn btn-primary btn-sm" id="btnAgregarMateriaPrima"><i class="fas fa-plus me-1"></i> Agregar Materia Prima</button>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Presentaciones Disponibles</label>
              <div class="form-text mb-2">Seleccione las presentaciones para este producto</div>
              <div class="mb-3 justify-content-between d-flex">
                <div>
                  <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas"><i class="fas fa-check-square me-1"></i> Seleccionar todas</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas"><i class="fas fa-square me-1"></i> Deseleccionar todas</button>
                </div>
                <div class="d-flex">
                  <button type="button" class="btn btn-primary btn-sm d-flex me-1"><i class="fi fi-rs-camera p-1 me-1"></i>Foto de la presentación</button>
                  <button type="button" class="btn btn-success btn-sm d-flex"><i class="fi fi-rs-marketplace-store p-1 me-1"></i>Mostrar o no en el ecommerce</button>
                </div>
              </div>
              <div class="contenedor-presentaciones row"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cancelar</button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"><i class="fas fa-save me-2"></i> Guardar Producto</button>
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
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Editar Producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_producto" class="formularioActualizar">
            <div class="col-md-3 mb-3">
              <label class="form-label">Nombre del Producto</label>
              <input type="text" class="form-control noRepetir formularioActualizar" name="nombre_producto" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Inicial</label>
              <input type="text" class="form-control dineroPositivo formularioActualizar" name="stock_producto" pattern="<?php echo regexPrecioFront ?>" minlength="<?php echo minRegexPrecioFront ?>" maxlength="<?php echo maxRegexPrecioFront ?>" value="0" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stock Mínimo</label>
              <input type="text" class="form-control dineroPositivo formularioActualizar" name="stock_minimo_producto" pattern="<?php echo regexPrecioFront ?>" minlength="<?php echo minRegexPrecioFront ?>" maxlength="<?php echo maxRegexPrecioFront ?>" value="5" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Unidad de medida</label>
              <select class="form-select selectUnidadMedida formularioActualizar" name="id_unidad_medida" required></select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Categoría del Producto</label>
              <select class="form-select selectCategoriaProducto selectCategoria formularioActualizar" name="id_categoria_producto" required>
                <option value="">Seleccione una categoría</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Mostrar en E-Commerce</label>
              <select class="form-select formularioActualizar" name="mostrar_ecommerce" required>
                <option value="1">Sí, mostrar en tienda</option>
                <option value="0">No, esconder de tienda</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Precio Divisas ($)</label>
              <input
                type="text"
                class="form-control dineroPositivo formularioActualizar "
                name="precio_producto"
                pattern="<?php echo regexPrecioFront; ?>"
                minlength="<?php echo minRegexPrecioFront; ?>"
                maxlength="<?php echo maxRegexPrecioFront; ?>"
                required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Precio BCV (Bs)</label>
              <input disabled type="text" class="form-control precioProductoBCV">
            </div>
            <div class="col-12 mb-3 campos-fabricado" style="display: none;">
              <label class="form-label">Materias Primas para Fabricación</label>
              <div class="form-text mb-2">Agregue las materias primas necesarias para fabricar este producto</div>
              <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriasPrimas">
                  <thead>
                    <tr>
                      <th>Materia Prima</th>
                      <th>Cantidad Requerida</th>
                      <th>Costo Unitario ($)</th>
                      <th>Subtotal ($)</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody class="cuerpoTablaMateriasPrimas"></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" class="text-end"><strong>Total Costo:</strong></td>
                      <td colspan="2">
                        <strong id="totalCostoMaterias">0.00 Bs</strong>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <button type="button" class="btn btn-primary btn-sm" id="btnAgregarMateriaPrima"><i class="fas fa-plus me-1"></i> Agregar Materia Prima</button>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Presentaciones Disponibles</label>
              <div class="form-text mb-2">Seleccione las presentaciones para este producto</div>
              <div class="contenedor-presentaciones row"></div>
              <div class="mt-3">
                <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas"><i class="fas fa-check-square me-1"></i> Seleccionar todas</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas"><i class="fas fa-square me-1"></i> Deseleccionar todas</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cancelar</button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"><i class="fas fa-save me-2"></i> Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal detalles presentaciones -->
<div class="modal fade modalVerPresentaciones" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Presentaciones del producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover tabla-presentaciones">
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>