<?php 
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="id_presentacion">

<?php 
    $instruccionesLista=[
        'encabezado'=>'Gestionar Presentaciones',
        'tituloBtnReg'=>'Registrar Presentación',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Registro de Nueva Presentación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="registrar">
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Nombre de la Presentación</label>
                            <input type="text" class="form-control noRepetir" name="nombre_rol" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Unidad de medida</label>
                            <select class="form-select selectUnidadMedida" name="id_unidad_medida"></select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Cantidad que contiene</label>
                            <input type="text" class="form-control" name="cantidad_pmp" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Rol
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
                    <i class="fas fa-edit me-2"></i> Editar Rol
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id_presentacion" class="formularioActualizar">
                        
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Nombre de la Presentación</label>
                            <input type="text" class="form-control noRepetir formularioActualizar" name="nombre_rol" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Unidad de medida</label>
                            <select class="form-select selectUnidadMedida formularioActualizar" name="id_unidad_medida"></select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="cedula" class="form-label">Cantidad que contiene</label>
                            <input type="text" class="form-control formularioActualizar" name="cantidad_pmp" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ FORMULARIO EDITAR ] FIN -->