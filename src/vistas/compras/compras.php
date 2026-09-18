<link rel="stylesheet" href="<?php echo APP_URL; ?>src/assets/css/compras.css">
<?php
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
    $instruccionesLista = [
        'encabezado'   => 'Gestionar Recepciones',
        'tituloBtnReg' => 'Agregar recepciones',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="compras">

<!-- Modal registro y edicion -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header modal-header-custom">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Registrar Recepción
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form class="formularioAjax" method="POST" action="" novalidate>
                <input type="hidden" name="accion"    value="registrar">
                <input type="hidden" name="id_compra" value="">

                <div class="modal-body p-0 modal-body-compras modal-body-custom">

                    <!-- Cabecera -->
                    <div class="header-global">

                        <!-- Atajo Nuevo Proveedor -->
                        <div class="d-flex align-items-center me-3">
                            <button type="button"
                                class="btn btn-outline-primary btn-sm rounded-pill d-flex align-items-center gap-1 shadow-sm px-3"
                                id="btnModalNuevoProveedor"
                                data-bs-toggle="modal"
                                data-bs-target="#modalRegistrarProveedorRapido"
                                title="Registrar nuevo proveedor si no existe">
                                <i class="fas fa-user-plus"></i>
                                <span>+ Proveedor</span>
                            </button>
                        </div>

                        <!-- Proveedor en edicion -->
                        <div class="d-none modo-edicion flex-grow-1 me-3 header-global-item">
                            <label for="selectProveedorEdicion" class="form-label small text-muted fw-semibold mb-1">
                                Proveedor
                            </label>
                            <select class="form-select selectProveedorAct"
                                name="rif_proveedor"
                                id="selectProveedorEdicion"
                                disabled>
                                <option value="">Seleccione proveedor...</option>
                            </select>
                        </div>

                        <!-- Fecha -->
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary icon-primary-lg"></i>
                            <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                                Fecha de Registro
                            </span>
                        </div>
                        <div class="input-date-container">
                            <input type="datetime-local"
                                class="form-control fw-bold text-primary border"
                                name="fecha_compra"
                                value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>

                    <!-- Tabla de items -->
                    <div class="grid-compras-container">

                        <!-- Cabecera tabla -->
                        <div class="grid-compras-header">
                            <div>Proveedor</div>
                            <div>Tipo</div>
                            <div>Artículo</div>
                            <div>Unidad</div>
                            <div>Cant.</div>
                            <div class="text-center">Del</div>
                        </div>

                        <!-- Filas dinámicas -->
                        <div id="contenedorItems">
                            <!-- JS insertará filas aquí -->
                        </div>
                    </div>

                    <!-- Footer tabla -->
                    <div class="p-3 bg-light border-top
                        d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"
                            id="contadorFilas">Items: 0</span>
                        <div class="d-flex flex-column align-items-center mx-auto">
                            <button type="button"
                                class="btn btn-primary rounded-circle shadow btn-add-item"
                                id="btnAgregarFila">
                                +
                            </button>
                            <div class="small text-muted mt-1 fw-semibold">
                                Agregar artículo
                            </div>
                        </div>
                        <span class="px-5"></span><!-- spacer para equilibrar el contador -->
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button"
                        class="btn btn-light px-4 btn-cancel-custom"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button"
                        class="btn btn-outline-secondary px-4 btn-cancel-custom"
                        id="btnLimpiarFormulario">
                        Limpiar
                    </button>
                    <button type="submit"
                        class="btn px-4 btn-save-custom">
                        <i class="fas fa-save me-2"></i>
                        <span class="textoSubmit">Guardar Todo</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal detalles -->
<div class="modal fade"
    id="modalVerCompra" tabindex="-1"
    aria-labelledby="modalVerCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header modal-header-custom">
                <h5 class="modal-title text-white fw-bold"
                    id="modalVerCompraLabel">
                    <i class="fas fa-eye me-2"></i>
                    Detalles de Recepción
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-0 modal-body-compras modal-body-custom">

                <!-- Datos de recepcion -->
                <div class="header-global">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hashtag me-2 text-primary icon-hashtag-sm"></i>
                        <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                            # Recepción
                        </span>
                    </div>
                    <div class="input-date-container">
                        <div class="form-control fw-bold text-primary info-display-box"
                            id="verIdCompra">-</div>
                    </div>
                </div>

                <!-- Fecha -->
                <div class="header-global border-top-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary icon-hashtag-sm"></i>
                        <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                            Fecha de Registro
                        </span>
                    </div>
                    <div class="input-date-container">
                        <div class="form-control fw-bold text-primary info-display-box"
                            id="verFechaCompra">-</div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="header-global border-top-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary icon-hashtag-sm"></i>
                        <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                            Estado
                        </span>
                    </div>
                    <div class="input-date-container">
                        <div class="form-control fw-bold info-display-box"
                            id="verEstadoCompra">-</div>
                    </div>
                </div>

                <!-- Items -->
                <div class="grid-compras-container">
                    <div class="grid-compras-header">
                        <div>Proveedor</div>
                        <div>Tipo</div>
                        <div>Artículo</div>
                        <div>Unidad</div>
                        <div>Cant.</div>
                    </div>
                    <div id="verItemsBody">
                        <!-- JS llenará esto -->
                    </div>
                </div>

            </div>

            <div class="modal-footer-custom justify-content-between">
                <button type="button"
                    class="btn btn-outline-primary px-4 btn-cancel-custom"
                    id="btnImprimirModalVer">
                    <i class="fas fa-print me-2"></i> Imprimir Orden
                </button>
                <button type="button"
                    class="btn btn-light px-4 btn-cancel-custom"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Registrar Proveedor Rápido -->
<div class="modal fade" id="modalRegistrarProveedorRapido" tabindex="-1" style="z-index: 1065;" aria-labelledby="modalRegistrarProveedorRapidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-custom" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title text-white fw-bold" id="modalRegistrarProveedorRapidoLabel">
                    <i class="fas fa-user-plus me-2"></i> Registrar Nuevo Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formRegistrarProveedorRapido" class="validar" method="POST" novalidate>
                <input type="hidden" name="accion" value="registrar">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">RIF / Cédula <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="input-group-text selectCodigoRIFRapido" name="codigo_rif_proveedor" required>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                    <option value="J" selected>J</option>
                                    <option value="G">G</option>
                                    <option value="C">C</option>
                                    <option value="P">P</option>
                                </select>
                                <input type="text"
                                    class="form-control inputRifRapido noRepetir"
                                    name="rif_proveedor"
                                    placeholder="Ej: 12345678"
                                    pattern="<?php echo regexCedulaRifLetra ?>"
                                    minlength="<?php echo minRegexCedulaRif ?>"
                                    maxlength="<?php echo maxRegexCedulaRif ?>"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control noRepetir"
                                name="razon_social_proveedor"
                                placeholder="Nombre o empresa proveedora"
                                pattern="<?php echo regexNombreObj ?>"
                                minlength="<?php echo minRegexNombreObj ?>"
                                maxlength="<?php echo maxRegexNombreObj ?>"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="input-group-text" name="prefijo_telefono_proveedor" required>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                    <option value="0251">0251</option>
                                    <option value="0212">0212</option>
                                </select>
                                <input type="text"
                                    class="form-control noRepetir"
                                    name="telefono_proveedor"
                                    placeholder="1234567"
                                    pattern="<?php echo regexTelefono ?>"
                                    minlength="<?php echo minRegexCuerpoTelefono ?>"
                                    maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email"
                                class="form-control noRepetir"
                                name="correo_proveedor"
                                placeholder="proveedor@ejemplo.com"
                                pattern="<?php echo regexCorreo ?>"
                                minlength="<?php echo minRegexCorreo ?>"
                                maxlength="<?php echo maxRegexCorreo ?>"
                                required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Dirección</label>
                            <textarea class="form-control"
                                name="direccion_proveedor"
                                rows="2"
                                placeholder="Dirección física del proveedor..."
                                pattern="<?php echo regexDescripcion ?>"
                                minlength="<?php echo minRegexDescripcion ?>"
                                maxlength="<?php echo maxRegexDescripcion ?>"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-custom justify-content-between bg-light p-3">
                    <button type="button" class="btn btn-light px-4 btn-cancel-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4 btn-save-custom" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>