<input type="hidden" class="nombreVista" value="login">
<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/login.css">
<input type="hidden" class="nombreVista" value="login">

<div class="container-fluid h-100">
  <div class="row d-flex justify-content-center align-items-center h-100">
    <div class="col-md-8 col-lg-6 col-xl-12">
      <div class="card shadow-lg">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Logo Multiservicios Lacruz" class="mb-3" style="width: 200px;">
          </div>
          <form method="POST" class="formularioAjax login" action="usuarios" novalidate>
            <input type="hidden" name="accion" value="iniciarSesion">
            <input type="hidden" name="Anti-CSRF" value="83eu92839dh9d">
            <div class="form-floating mb-3">
              <input
                type="text"
                class="form-control"
                name="usuario_usuario"
                placeholder="Usuario"
                required
                autocomplete="username">
              <label for="usuario">Usuario</label>
            </div>
            <div class="col- input-group input-group mb-4">
              <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
              <input
                type="password"
                class="form-control"
                name="contrasena1_usuario"
                placeholder="Contraseña"
                required
                autocomplete="current-password">
            </div>
            <div class="d-flex justify-content-center mb-4">
              <div class="g-recaptcha" data-sitekey="6LdSVPgsAAAAAMmHarx-2gZ5zUWdGHM91UouPOE-"></div>
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
          <div class="d-flex justify-content-between">
            <a href="#" class="text-white text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#registroModal">
              Regístrate aquí
            </a>
            <a href="#" class="text-white text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#recuperarContrasenaModal">
              ¿Olvidaste tu contraseña?
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Registro -->
<form class="validar login formRegistroUsuario" method="POST" action="usuarios" id="registroForm" novalidate>
  <!-- Datos del usuarios -->
  <div class="modal fade modalDatosUsuarios" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Registro de usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="Anti-CSRF" value="83eu92839dh9d">
          <input type="hidden" name="accion" value="registrar">
          <input type="hidden" name="id_rol" value="3">

          <div class="form-group mb-3">
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
            <div class="form-text">Solo números (7-10 dígitos)</div>
          </div>
          <div class="form-group mb-3">
            <label for="cedula" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre_usuario" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" pattern="<?php echo regexNombrePer ?>" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group mb-3">
            <label for="cedula" class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido_usuario" minlength="<?php echo minRegexNombrePer ?>" maxlength="<?php echo maxRegexNombrePer ?>" pattern="<?php echo regexNombrePer ?>" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group mb-3">
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
            <div class="form-text">Formato: xxxx-xxxxxxx</div>
          </div>
          <div class="form-group mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control noRepetir" name="correo_usuario" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" pattern="<?php echo regexCorreo ?>" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group mb-3">
            <label for="direccion_usuario" class="form-label">Dirección</label>
            <input type="email" class="form-control" name="direccion_usuario" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" pattern="<?php echo regexDescripcion ?>" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group mb-3">
            <label for="nombre" class="form-label">Nombre de usuario</label>
            <input type="text"
              class="form-control noRepetir"
              name="usuario_usuario"
              minlength="<?php echo minRegexUsuario ?>"
              maxlength="<?php echo maxRegexUsuario ?>"
              pattern="<?php echo regexUsuario ?>"
              required
              autocomplete="username">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group col-md-12 mb-3">
            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
              <input type="password" class="form-control" name="contrasena1_usuario" id="contrasena1_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
            </div>
          </div>
          <div class="form-group col-md-12 mb-3">
            <label for="confirmar_clave" class="form-label">Repetir Contraseña </label>
            <div class="input-group">
              <span class="input-group-text"><i class="btnMostrarOcultarContrasena fi fi-rs-eye"></i></span>
              <input type="password" class="form-control" name="contrasena2_usuario" id="contrasena2_usuario" pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>" maxlength="<?php echo maxRegexContrasena ?>" placeholder="OPCIONAL">
            </div>
          </div>
          <div class="d-grid">
            <button class="btn btn-primary btn-lg btnSiguiente" type="button" disabled>
              <span id="btnRegistroText">Siguiente</span>
              <div id="btnRegistroLoading" class="spinner-border spinner-border-sm d-none" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Preguntas de seguridad  -->
  <div class="modal fade modalPreguntasSeguridad" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Preguntas de seguridad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="containerPreguntas"></div>
          <div class="d-flex mt-3">
            <button class="btn btn-secundary btn-lg" type="button" data-bs-toggle="modal" data-bs-target=".modalDatosUsuarios">
              <span id="btnRegistroText">Atrás</span>
            </button>
            <button class="btn btn-primary btn-lg btnSiguiente" type="button" disabled>
              <span id="btnRegistroText">Registrar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Form recuperar contraseña -->
<form class="formularioRecuperarContrasena validar login" method="POST" action="usuarios" id="registroForm" novalidate>
  <!-- Modal de buscar cedula -->
  <div class="modal fade" id="recuperarContrasenaModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Recuperar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="Anti-CSRF" value="83eu92839dh9d">
          <div class="form-group mb-3">
            <label for="codigo_cedula_usuario" class="form-label">Introduzca su cédula de identidad</label>
            <div class="input-group">
              <select
                data-prefijo=".selectCodigoCedulaRecContrasena"
                data-cuerpo=".inputCedulaRecContrasena"
                class="input-group-text selectCodigoCedulaRecContrasena"
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
                class=" form-control inputCedulaRecContrasena"
                name="cedula_usuario"
                pattern="<?php echo regexCedulaRifLetra ?>"
                minlength="<?php echo minRegexCedulaRif ?>"
                maxlength="<?php echo maxRegexCedulaRif ?>"
                minlengthC="<?php echo minRegexCedulaRif ?>"
                maxlengthC="<?php echo maxRegexCedulaRif ?>"
                required
                data-prefijo=".selectCodigoCedulaRecContrasena"
                data-cuerpo=".inputCedulaRecContrasena">
            </div>
            <div class="form-text">Solo números (7-10 dígitos)</div>
          </div>
          <div class="form-group mb-3">
            <label for="codigo_cedula_usuario" class="form-label">Método de recuperación</label>
            <select class="form-control selectMetodoRecuperacion" name="metodo_recuperacion" required>
              <option value="1">Código SMS</option>
              <option value="2">Correo Electrónico</option>
              <option value="3">Preguntas de Seguridad</option>
            </select>
          </div>
          <div class="d-grid mb-3">
            <button class="btn btn-primary btn-lg btnContinuar" disabled type="button">
              <span>Continuar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal responder preguntas de seguridad -->
  <div class="modal fade modalResponderPreguntas" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Recuperar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="accion" value="validarMetodoRecContrasena">
          <input type="hidden" name="tipo_metodo" value="3">
          <input type="hidden" name="cedula_usuario">
          <div class="containerPreguntas"></div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button class="btn btn-secundary btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#recuperarContrasenaModal">
            <span id="btnRegistroText">Atrás</span>
          </button>
          <button class="btn btn-primary btn-lg enviarPreguntasSeg" type="button">
            <span>Envíar</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal cambiar contraseña -->
  <div class="modal fade modalCambiarContrasena" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registroModalLabel">Recuperar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="accion" value="cambiarContrasena">
          <input type="hidden" name="hashContrasena">
          <input type="hidden" name="cedula_usuario">
          <div class="form-group col-lg-12 mb-2">
            <label>Nueva Contraseña: </label>
            <input type="password" class="form-control" name="contrasena1_usuario"
              minlength="<?php echo minRegexContrasena ?>"
              maxlength="<?php echo maxRegexContrasena ?>"
              pattern="<?php echo regexContrasena ?>"
              required
              autocomplete="new-password">
          </div>
          <div class="form-group col-lg-12">
            <label>Confirmar Contraseña: </label>
            <input type="password" class="form-control" name="contrasena2_usuario"
              minlength="<?php echo minRegexContrasena ?>"
              maxlength="<?php echo maxRegexContrasena ?>"
              pattern="<?php echo regexContrasena ?>"
              required
              autocomplete="new-password">
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button class="btn btn-secundary btn-lg btnAtras" type="button" data-bs-toggle="modal" data-bs-target="">
            <span id="btnRegistroText">Atrás</span>
          </button>
          <button class="btn btn-primary btn-lg btnCambiarContrasena" type="button">
            <span>Envíar</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal verificar código (correo) -->
  <div class="modal fade modalVerificarCodigo" tabindex="-1" aria-labelledby="verificarCodigoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="verificarCodigoLabel">Introduce el código de seguridad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center p-4">
          <div class="mb-3">
            <img src="<?php echo APP_URL ?>src/assets/images/numerico.png" alt="email" style="width:72px;" class="mb-2">
            <p class="mb-2">
              Hemos enviado un código de seguridad al correo <span class="spanCorreo"></span>. Por favor,
              introdúcelo aquí para continuar.</p>
          </div>
          <div class="mb-3">
            <input type="hidden" name="accion" value="validarMetodoRecContrasena">
            <input type="hidden" name="tipo_metodo" value="2">
            <input type="hidden" name="cedula_usuario">
            <input type="text" name="codigo_recuperacion" class="form-control form-control-lg text-center codigoSeguridadInput" placeholder="Código de 6 dígitos" maxlength="6" required style="letter-spacing:6px;font-size:1.25rem;">
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button class="btn btn-secundary btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#recuperarContrasenaModal">
            <span id="btnRegistroText">Atrás</span>
          </button>
          <button class="btn btn-primary btn-lg enviarCogSeguridad" type="button">
            <span>Envíar</span>
          </button>
        </div>
      </div>
    </div>
  </div>

</form>