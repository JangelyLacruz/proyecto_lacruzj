<!-- Modal Ver Detalles de Recepción (Solo Lectura) -->
<div class="modal fade" id="modalVerCompra" tabindex="-1" aria-labelledby="modalVerCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header con mismo estilo que registro -->
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <h5 class="modal-title text-white fw-bold" id="modalVerCompraLabel">
                    <i class="fas fa-eye me-2"></i> Ver Detalles de Recepción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body con mismo fondo -->
            <div class="modal-body p-0 modal-body-compras" style="background-color: #f8f9fa;">

                <!-- Cabecera Global (similar al de registro) -->
                <div class="header-global">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary" style="font-size: 1.2rem;"></i>
                        <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">ID de
                            Recepción</span>
                    </div>
                    <div style="flex-grow: 1; max-width: 300px; min-width: 200px;">
                        <div class="form-control fw-bold text-primary"
                            style="border: 1px solid #dee2e6; background-color: white;" id="verIdCompra">-</div>
                    </div>
                </div>

                <div class="header-global" style="border-top: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary" style="font-size: 1.2rem;"></i>
                        <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">Fecha de
                            Registro</span>
                    </div>
                    <div style="flex-grow: 1; max-width: 300px; min-width: 200px;">
                        <div class="form-control fw-bold text-primary"
                            style="border: 1px solid #dee2e6; background-color: white;" id="verFechaCompra">-</div>
                    </div>
                </div>

                <!-- Grid con mismo estilo -->
                <div class="grid-compras-container">
                    <!-- Header Fijo -->
                    <div class="grid-compras-header">
                        <div>Proveedor</div>
                        <div>Tipo</div>
                        <div>Artículo</div>
                        <div>Unidad</div>
                        <div>Cant.</div>
                    </div>

                    <!-- Filas Dinámicas -->
                    <div id="verItemsBody">
                        <!-- JS llenará esto -->
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-white p-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                    style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>