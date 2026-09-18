<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="usuarios">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Usuarios',
  'tituloBtnReg' => 'Agregar Usuarios',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<form class="formularioAjax validar" novalidate>
  <div class="modalDatosUsuarios modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content border-0">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
          <h5 class="modal-title" id="registrarUsuarioModalLabel">
            <i class="fas fa-user-plus me-2"></i> Agregar Nuevo Usuario
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-4 mb-3">
              <label for="codigo_cedula_usuario" class="form-label">Cédula</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectCodigoCedula"
                  data-cuerpo=".inputCedula"
                  class="input-group-text selectCodigoCedula"
                  name="codigo_cedula_usuario"
                  required>
                  <option value="V">V</option>
                  <option value="E">E</option>
                  <option value="J">J</option>
                  <option value="G">G</option>
                  <option value="C">C</option>
                  <option value="P">P</option>
                </select>
                <input
                  type="text"
                  class=" form-control inputCedula noRepetir"
                  name="cedula_usuario"
                  pattern="<?php echo regexCedulaRifLetra ?>"
                  minlength="<?php echo minRegexCedulaRif ?>"
                  maxlength="<?php echo maxRegexCedulaRif ?>"
                  minlengthC="<?php echo minRegexCedulaRif ?>"
                  maxlengthC="<?php echo maxRegexCedulaRif ?>"
                  required
                  data-prefijo=".selectCodigoCedula"
                  data-cuerpo=".inputCedula">
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label for="cedula" class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: CARLOS">
            </div>
            <div class="col-md-4 mb-3">
              <label for="cedula" class="form-label">Apellido</label>
              <input type="text" class="form-control" name="apellido_usuario" pattern="<?php echo regexNombrePer ?>" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" required placeholder="Ej: ALFARO">
            </div>
            <div class="col-md-4 mb-3">
              <label for="prefijo_telefono_usuario" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoUsuario"
                  class="input-group-text selectPrefijoTelefono"
                  name="prefijo_telefono_usuario"
                  required>
                  <option value="0416">0416</option>
                  <option value="0426">0426</option>
                  <option value="0424">0424</option>
                  <option value="0414">0414</option>
                  <option value="0412">0412</option>
                  <option value="0422">0422</option>
                  <option value="0212">0212</option>
                  <option value="0251">0251</option>
                  <option value="0241">0241</option>
                  <option value="0257">0257</option>
                  <option value="0257">0257</option>
                </select>
                <input
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoUsuario"
                  type="text"
                  class="form-control telefonoUsuario noRepetir"
                  name="telefono_usuario"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>"
                  required>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label for="correo" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control noRepetir" name="correo_usuario" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" required placeholder="Ej: usuario@empresa.com">
            </div>
            <div class="col-md-4 mb-3">
              <label for="nombre" class="form-label">Nombre de Usuario</label>
              <input type="text" class="form-control noRepetir" name="usuario_usuario" pattern="<?php echo regexUsuario ?>" minlength="<?php echo minRegexUsuario ?>" maxlength="<?php echo maxRegexUsuario ?>" required placeholder="Ingrese el nombre de usuario">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Rol </label>
              <select class="form-select selectRoles" name="id_rol" pattern="<?php echo regexId ?>" minlength="<?php echo minRegexId ?>" maxlength="<?php echo maxRegexId ?>" required>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Foto de pérfil </label>
              <input type="file" class="form-control" name="foto_usuario">
            </div>
            <div class="col-md-12 mb-3">
              <label for="clave" class="form-label">Dirección</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fi fi-br-direction-signal-arrow"></i></span>
                <input type="text" class="form-control" name="direccion_usuario" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" pattern="<?php echo regexDescripcion ?>" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="clave" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
                <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" required placeholder="Mínimo 8 caracteres">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="confirmar_clave" class="form-label">Confirmar Contraseña </label>
              <div class="input-group">
                <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
                <input type="password" class="form-control" name="contrasena2_usuario" id="contrasena2_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" required placeholder="Repita la contraseña">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button
            type="button"
            class="btn btn-primary"
            style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
            data-bs-toggle="modal" data-bs-target=".modalPreguntasSeguridad">
            <i class="fas fa-save me-2"></i> Siguiente
          </button>
        </div>

      </div>
    </div>
  </div>

  <div class="modalPreguntasSeguridad modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content border-0">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
          <h5 class="modal-title" id="registrarUsuarioModalLabel">
            <i class="fas fa-user-plus me-2"></i> Preguntas de seguridad
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="containerPreguntas"></div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target=".modalDatosUsuarios">
            <i class="fas fa-times me-2"></i> Atrás
          </button>
          <button
            type="submit"
            class="btn btn-primary"
            style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Registrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Preguntas de seguridad  -->
  <!-- <div class="modal fade modalPreguntasSeguridad" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Preguntas de seguridad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="containerPreguntas"></div>
        </div>
        <div class="modal-footer">
          <div class="d-flex mt-3">
            <button class="btn btn-secundary btn-lg" type="button" data-bs-toggle="modal" data-bs-target=".modalDatosUsuarios">
              <span id="btnRegistroText">Atrás</span>
            </button>
            <button class="btn btn-primary btn-lg btnSiguiente" type="submit" disabled>
              <span id="btnRegistroText">Registrar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div> -->
</form>
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
              <label for="prefijo_telefono_usuario" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoUsuario"
                  class="input-group-text formularioActualizar selectPrefijoTelefono"
                  name="prefijo_telefono_usuario"
                  required>
                  <option value="0416">0416</option>
                  <option value="0426">0426</option>
                  <option value="0424">0424</option>
                  <option value="0414">0414</option>
                  <option value="0412">0412</option>
                  <option value="0422">0422</option>
                  <option value="0212">0212</option>
                  <option value="0251">0251</option>
                  <option value="0241">0241</option>
                  <option value="0257">0257</option>
                  <option value="0257">0257</option>
                </select>
                <input
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoUsuario"
                  type="text"
                  class="form-control formularioActualizar telefonoUsuario noRepetir"
                  name="telefono_usuario"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>"
                  required>
              </div>
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
            <div class="col-md-12 mb-3">
              <label for="clave" class="form-label">Dirección</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fi fi-br-direction-signal-arrow"></i></span>
                <input type="text" class="form-control" name="direccion_usuario" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" pattern="<?php echo regexDescripcion ?>" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">

              <label for="clave" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
                <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="confirmar_clave" class="form-label">Confirmar Contraseña </label>
              <div class="input-group">
                <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
                <input type="password" class="form-control" name="contrasena2_usuario" id="contrasena2_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
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