<?php 
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="cedula_usuario">

<?php 
    $instruccionesLista=[
        'encabezado'=>'Gestionar Usuarios',
        'tituloBtnReg'=>'Registrar Usuario',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Registro de Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="registrar">
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control noRepetir" name="cedula_usuario" pattern="<?php echo regexCedula ?>" minlength="<?php echo minRegexCedula ?>" maxlength="<?php echo maxRegexCedula ?>" required placeholder="Ej: 12345678">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: CARLOS">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: ALFARO">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono_usuario" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required placeholder="Ej: 0412-1234567">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control noRepetir" name="correo_usuario" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" required placeholder="Ej: usuario@empresa.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control noRepetir" name="usuario_usuario" pattern="<?php echo regexUsuario ?>" minlength="<?php echo minRegexUsuario ?>" maxlength="<?php echo maxRegexUsuario ?>" required placeholder="Ingrese el nombre de usuario">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Rol </label>
                            <select class="form-select selectRoles" name="id_rol" pattern="<?php echo regexId ?>" minlength="<?php echo minRegexId ?>" maxlength="<?php echo maxRegexId ?>" required>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fi fi-br-lock-alt"></i></span>
                                <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" required placeholder="Mínimo 8 caracteres">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_clave" class="form-label">Confirmar Contraseña </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fi fi-br-lock-alt"></i></span>
                                <input type="password" class="form-control" name="contrasena2_usuario"  id="contrasena2_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" required placeholder="Repita la contraseña">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Usuario
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
                    <i class="fas fa-edit me-2"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="cedula_usuario" class="formularioActualizar">
                        
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Nombre</label>
                            <input type="text" class="form-control formularioActualizar" name="nombre_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: CARLOS">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Apellido</label>
                            <input type="text" class="form-control formularioActualizar" name="apellido_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: ALFARO">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control formularioActualizar" name="telefono_usuario" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required placeholder="Ej: 0412-1234567">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control noRepetir formularioActualizar" name="correo_usuario" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" required placeholder="Ej: usuario@empresa.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control noRepetir formularioActualizar" name="usuario_usuario" pattern="<?php echo regexUsuario ?>" minlength="<?php echo minRegexUsuario ?>" maxlength="<?php echo maxRegexUsuario ?>" required placeholder="Ingrese el nombre de usuario">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol </label>
                            <select class="form-select selectRoles formularioActualizar" name="id_rol" pattern="<?php echo regexId ?>" minlength="<?php echo minRegexId ?>" maxlength="<?php echo maxRegexId ?>" required>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fi fi-br-lock-alt"></i></span>
                                <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_clave" class="form-label">Confirmar Contraseña </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fi fi-br-lock-alt"></i></span>
                                <input type="password" class="form-control" name="contrasena2_usuario"  id="contrasena2_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ FORMULARIO EDITAR ] FIN -->