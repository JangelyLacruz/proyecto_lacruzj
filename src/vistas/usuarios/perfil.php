<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Mi Perfil
                    </h4>
                </div>
                <div class="card-body">
                    <form id="formPerfil" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-4">Información Personal</h5>
                                
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedula" readonly>
                                    <div class="form-text">La cédula no puede ser modificada.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" required>
                                    <div class="invalid-feedback" id="error-nombre"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    <div class="form-text">Formato: 0412-1234567 o 04121234567</div>
                                    <div class="invalid-feedback" id="error-telefono"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                    <div class="invalid-feedback" id="error-correo"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="id_rol" class="form-label">Rol</label>
                                    <select class="form-control" id="id_rol" name="id_rol" required>
                                        <option value="">Seleccione un rol</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-rol"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-4">Cambiar Contraseña</h5>
                                <div class="form-text mb-3">
                                    Completa estos campos solo si deseas cambiar tu contraseña.
                                </div>

                                <div class="mb-3">
                                    <label for="clave_actual" class="form-label">Contraseña Actual</label>
                                    <input type="password" class="form-control" id="clave_actual" name="clave_actual">
                                    <div class="invalid-feedback" id="error-clave_actual"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="clave_nueva" name="clave_nueva" minlength="6">
                                    <div class="form-text">Mínimo 6 caracteres.</div>
                                    <div class="invalid-feedback" id="error-clave_nueva"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="6">
                                    <div class="invalid-feedback" id="error-confirmar_clave"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2" id="btnGuardar">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar Cambios
                                </button>
                                <a href="index.php?c=loginControlador&m=home" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver al Inicio
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




