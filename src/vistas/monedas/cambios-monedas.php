<?php 
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="id_cambio_moneda">

<?php 
    $instruccionesLista=[
        'encabezado'=>'Gestionar Cambio de Divisas',
        'tituloBtnReg'=>'Actualizar Cambio',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO ACTUALIZAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Actualizar Cambio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="<?php echo APP_URL ?>monedas" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizarValor">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Moneda</label>
                            <select class="form-select selectMonedas" name="id_moneda" pattern="<?php echo regexId ?>" minlength="<?php echo minRegexId ?>" maxlength="<?php echo maxRegexId ?>" required>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nuevo Valor</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fi fi-br-percentage"></i></span>
                                <input type="text" class="form-control" name="valor_moneda" pattern="<?php echo regexPrecio ?>" minlength="<?php echo minRegexPrecio ?>" maxlength="<?php echo maxRegexPrecio ?>" placeholder="12.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Actualizar Valor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ FORMULARIO ACTUALIZAR ] FIN -->