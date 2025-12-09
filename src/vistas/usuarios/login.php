<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/login.css">
<input type="hidden" class="nombreVista" value="login">
<div class="container-fluid h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-8 col-lg-6 col-xl-12">
            <div class="card shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="src/assets/images/logo.png" alt="Logo Multiservicios Lacruz" class="mb-3" style="width: 200px;">
                    </div>
                    <form method="POST" class="formularioAjax" action="usuarios" novalidate>
                        <input type="hidden" name="accion" value="iniciarSesion">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="usuario_usuario" placeholder="Usuario" required>
                            <label for="usuario">Usuario</label>
                            <div class="invalid-feedback">
                                Por favor ingrese su nombre de usuario.
                            </div>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" name="contrasena1_usuario" placeholder="Contraseña" required>
                            <label for="clave">Contraseña</label>
                            <div class="invalid-feedback">
                                Por favor ingrese su contraseña.
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <span id="btnText">Ingresar</span>
                                <div id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                    <div class="text-center">
                        <p class="text-white-50 mb-0">¿No tienes una cuenta?</p>
                        <a href="#" class="text-white text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#registroModal">
                            Regístrate aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Registro -->
<div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroModalLabel">Registro de Oficinista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="formularioAjax validar login" method="POST" action="usuarios" id="registroForm" novalidate>
                    <input type="hidden" name="accion" value="registrar">
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" name="cedula_usuario" minlength="<?php echo minRegexCedula ?>" maxlength="<?php echo maxRegexCedula ?>" pattern="<?php echo regexCedula ?>" required>
                        <div class="form-text">Solo números (6-10 dígitos)</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre_usuario" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" pattern="<?php echo regexNombrePer ?>" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido_usuario" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" pattern="<?php echo regexNombrePer ?>" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono_usuario" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" pattern="<?php echo regexTelefono ?>" required>
                        <div class="form-text">Formato: xxxx-xxxxxxx</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" name="correo_usuario" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" pattern="<?php echo regexCorreo ?>" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" name="usuario_usuario" minlength="<?php echo minRegexUsuario ?>" maxlength="<?php echo maxRegexUsuario ?>" pattern="<?php echo regexUsuario ?>" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" pattern="<?php echo regexContrasena ?>" required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Repetir Contraseña</label>
                        <input type="password" class="form-control" name="contrasena2_usuario" id="contrasena2_usuario" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" pattern="<?php echo regexContrasena ?>" required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg" type="submit" name="btnRegistro">
                            <span id="btnRegistroText">Registrarse</span>
                            <div id="btnRegistroLoading" class="spinner-border spinner-border-sm d-none" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>