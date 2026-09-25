//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, extraerDatosAjax, cargarInputsActualizarQNR,
  validarEnTiempoReal, rutaFotos, funcionMandarError, funcionEliminaError,
  pedirDatosAjax, objCacheSS, alertasAjax, validarTodosLosCampos,
  convertirHTMLJSON, regexDescripcion, minRegexDescripcion, maxRegexDescripcion,
  mostrarOcultarSpinnerCarga, modoDev
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
async function habilitarDeshabilitarBotonRecContrasena() {
  let cedula = $(this).val();
  let codigo = $(this).closest('.form-group').find('.selectCodigoCedulaRecContrasena').val()

  if (cedula == "") {
    funcionEliminaError(this)
    return;
  }

  cedula = codigo + cedula;
  let usuario = await pedirDatosAjax({
    'modulo': 'usuarios',
    'datosPe': {
      accion: 'listar',
      'tipoConsulta': 'verificarExistencia',
      'cedula_usuario': cedula
    }
  })
  if (usuario.cedula_usuario) {
    $(this).closest('form').find('.btnContinuar').prop('disabled', false);
    funcionEliminaError(this);
    objCacheSS.setItem('usuario/cedula_recuperar_clave', usuario.cedula_usuario);
  } else {
    objCacheSS.removeItem('usuario/cedula_recuperar_clave');
    $(this).closest('form').find('.btnContinuar').prop('disabled', true);
    funcionMandarError(this, 'El número de cédula no coincide con ningún registro')
  }
}
function habilitarDeshabilitarBoton(form, botonDeshabilitar) {
  let elementosInvalidos = $(form).find('select.error, input.error');
  if (elementosInvalidos.length > 0) {
    botonDeshabilitar.attr('disabled', true);
  } else {
    botonDeshabilitar.attr('disabled', false);
  }
}
async function validarFormsCompletos() {
  let form = $(this).closest('.modal');
  let hayInvalidos = await validarTodosLosCampos(form, 'usuarios');

  if (hayInvalidos) {
    $(this).attr('disabled', true);
    return;
  } else {
    $(this).attr('disabled', false);
  }
  if (form.hasClass('modalDatosUsuarios')) {
    form.modal('hide');
    $('.modalPreguntasSeguridad').modal('show');
  } else if (form.hasClass('modalPreguntasSeguridad')) {

    let resultado = await alertasAjax({
      'tipo': 'preguntar',
      'titulo': '¿Desea enviar el formulario?',
      'texto': 'Presione el botón de "Aceptar" para efectuar el registro'
    })
    if (!resultado?.isConfirmed) return;

    let datosJSON = convertirHTMLJSON({
      elemento: form.closest('form'),
      tipoCuerpo: 'JSONPuro',
      camposFuera: ['Anti-CSRF']
    });
    try {
      mostrarOcultarSpinnerCarga('mostrar')
      resultado = await pedirDatosAjax({
        JSONstring: true,
        modulo: 'usuarios',
        datosPe: datosJSON
      })
      alertasAjax(resultado);
    } finally {
      mostrarOcultarSpinnerCarga('ocultar')
    }
  }
}
async function eliminarRestablecerPregunta() {

  let containerPreg = $(this).closest('.containerPreguntas');
  let idPreguntaSel = $(this).val();
  let demasSelects = containerPreg.find('.selectPregunta').not($(this));
  let todasLasPreguntas = await pedirDatosAjax({
    'modulo': 'preguntas-seguridad',
    'datosPe': {
      'accion': 'listar'
    }
  });
  let preguntasSeleccionadas = {}

  //Eliminamos la opcion de los demas selects
  if (idPreguntaSel != '') {
    preguntasSeleccionadas[idPreguntaSel] = true;
    demasSelects.find(`option[value="${idPreguntaSel}"]`).addClass('d-none');
  }

  demasSelects.each((c, select) => {
    if ($(select).val() != '') preguntasSeleccionadas[$(select).val()] = true;
  })

  // Mostramos en todos los selects las que no estén seleccionadas
  todasLasPreguntas.forEach(pregunta => {
    if (!preguntasSeleccionadas[pregunta.id_pregunta]) {
      containerPreg.find(`option[value="${pregunta.id_pregunta}"]`).removeClass('d-none')
    }
  })

}
async function solicitarValidacionDeSeguridad() {
  if ($(this).prop('disabled')) return;
  let metodoVerificacion = $(this).closest('.formularioRecuperarContrasena').find('.selectMetodoRecuperacion').val();
  let cedula = objCacheSS.getItem('usuario/cedula_recuperar_clave')

  if (metodoVerificacion == '' || cedula == '') {
    return alertasAjax({
      'tipo': 'simple',
      'icono': 'warning',
      'titulo': 'Cédula inexistente',
      'textp': 'La cédula no se encuentra registrada'
    })
  }

  if (metodoVerificacion == '3') { // Preguntas de seguridad
    let preguntas;
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      preguntas = await pedirDatosAjax({
        'modulo': 'usuarios',
        'datosPe': {
          'accion': 'listar',
          'tipoConsulta': 'solicitarPreguntasSeguridad',
          'cedula_usuario': cedula,
        }
      })
    } finally {
      mostrarOcultarSpinnerCarga('ocultar')
    }
    if (preguntas?.icono == 'error') return alertasAjax(preguntas);
    let indiceP1 = Math.floor(Math.random() * 6);
    let indiceP2 = Math.floor(Math.random() * 6);
    while (indiceP2 === indiceP1) {
      indiceP2 = Math.floor(Math.random() * 6);
    }
    let htmlPreguntas = `
      <div class="mb-2 row">
        <div class="form-group col-lg-8">
          <label>Pregunta #1: </label>
          <input type="hidden" name="preguntas_respuestas-0-id_pregunta" value="${preguntas[indiceP1]['id_pregunta']}">
          <span class="form-control">${preguntas[indiceP1]['texto_pregunta']}</span>
        </div>
        <div class="form-group col-lg-4">
          <label>Respuesta #1: </label>
          <input type="text" class="form-control inputRespuestasPreguntasRecCon" name="preguntas_respuestas-0-respuesta"
            pattern="${regexDescripcion}"
            minlength="${minRegexDescripcion}"
            maxlength="${maxRegexDescripcion}"
            required
          >
        </div>
      </div>
      <div class="mb-5 row">
        <div class="form-group col-lg-8">
          <label>Pregunta #2: </label>
          <input type="hidden" name="preguntas_respuestas-1-id_pregunta" value="${preguntas[indiceP2]['id_pregunta']}">
          <span class="form-control">${preguntas[indiceP2]['texto_pregunta']}</span>
        </div>
        <div class="form-group col-lg-4">
          <label>Respuesta #2: </label>
          <input type="text" class="form-control inputRespuestasPreguntasRecCon" name="preguntas_respuestas-1-respuesta"
            pattern="${regexDescripcion}"
            minlength="${minRegexDescripcion}"
            maxlength="${maxRegexDescripcion}"
            required
          >
        </div>
      </div>
    `;

    $(this).closest('.modal').modal('hide');
    let modalResponder = $('.modalResponderPreguntas');
    modalResponder.find('.containerPreguntas').empty().append(htmlPreguntas);
    modalResponder.find('[name="cedula_usuario"]').val(cedula)
    modalResponder.modal('show');
    $('.modalCambiarContrasena').find('.btnAtras').attr('data-bs-target', '.modalResponderPreguntas')

  } else if (metodoVerificacion == '1' || metodoVerificacion == '2') { //mensaje sms o correo
    let respuesta;
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      respuesta = await pedirDatosAjax({
        'modulo': 'usuarios',
        'datosPe': {
          'accion': 'solicitarCodigoRecContrasena',
          'cedula_usuario': cedula,
          'tipo_metodo': metodoVerificacion
        }
      })
    } finally {
      mostrarOcultarSpinnerCarga('ocultar')
    }
    if (respuesta?.icono == 'error') return alertasAjax(respuesta);

    $(this).closest('.modal').modal('hide');
    let modalResponder = $('.modalVerificarCodigo');
    modalResponder.find('[name="cedula_usuario"]').val(cedula)
    modalResponder.modal('show');
    $('.modalCambiarContrasena').find('.btnAtras').attr('data-bs-target', '.modalVerificarCodigo')
    modalResponder.find('.spanCorreo').text(respuesta.correo)
  }
}
async function cargarPreguntasSeguridad() {
  let containerPreguntas = $('.modalPreguntasSeguridad').find('.containerPreguntas')
  let htmlPreguntas = '';
  for (let i = 1; i < 7; i++) {
    htmlPreguntas += `
        <div class="mb-2 row">
          <div class="form-group col-lg-8">
            <label>Pregunta #${i}: </label>
            <select class="form-control selectPregunta" name="preguntas_respuestas-${i - 1}-id_pregunta" required></select>
          </div>
          <div class="form-group col-lg-4">
            <label>Respuesta #${i}: </label>
            <input
              type="text" 
              class="form-control inputRespuestaPregunta" 
              name="preguntas_respuestas-${i - 1}-respuesta"
              required
              minlength="${minRegexDescripcion}"
              maxlength="${maxRegexDescripcion}"
              pattern="${regexDescripcion}"
            >
          </div>
        </div>
      `;
  }
  containerPreguntas.empty().append(htmlPreguntas);
  extraerDatosAjax({
    'modulosPeticion': ['preguntas-seguridad'],
    'accionesPeticion': [{ 'accion': 'listar' }],
    'tipoElemento': ['select'],
    'elementosDestino': [$('.selectPregunta')],
    'datosInsertar': [
      {
        'value': 'id_pregunta',
        'texto': 'texto_pregunta',
        'textoDefault': 'Seleccione una opción'
      }
    ]
  });
}

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  if ($('.nombreVista').val() != 'login') {
    await listarDataTable({
      encabezados: {
        "cedula_usuario": "CÉDULA",
        "foto_usuario": "FOTO",
        "nombre_rol": "ROL",
        "nombre_usuario": "NOMBRE",
        "apellido_usuario": "APELLIDO",
        "telefono_usuario": "TELÉFONO",
        "correo_usuario": "CORREO",
        "usuario_usuario": "USUARIO",
      },
      informacionPe: {
        'modulo': 'usuarios',
        'datosPe': {
          'accion': 'listar'
        }
      },
      campoIdBtn: 'cedula_usuario',
      botones: 'CRUD',
      infoTratoEspecial: {
        foto_usuario: (info) => {
          let foto = info.valor != '' ? info.valor : 'perfilDefaultUsuario.png';
          return `
            <img 
              src="${rutaFotos}usuarios/${foto}"
              class="estiloFotoRegistro fotoRegistro shadow-sm"
              data-modulo="usuarios"
              data-tabla_bd="usuarios"
              data-campo_id="cedula_usuario"
              data-valor_id="${info.fila.cedula_usuario}"
              data-campo_foto="foto_usuario"
              data-accion_act="actualizarFoto"
              data-accion_eli="eliminarFoto"
              data-label_foto="Actualizar Foto de Perfil"
              data-texto_alerta="Tu foto de perfil volverá a la configuración predeterminada"
              data-foto_default="perfilDefaultUsuario.png"
            >`;
        }
      },
      visualizacionResponsiveColumnas: {
        "cedula_usuario": 1,
        "nombre_usuario": 2,
        "apellido_usuario": 3,
        "foto_usuario": 4,
        "nombre_rol": 5,
        "telefono_usuario": 4,
        "correo_usuario": 6,
        "usuario_usuario": 7,
      },
    });
    extraerDatosAjax({
      'modulosPeticion': ['roles'],
      'accionesPeticion': [{ 'accion': 'listar' }],
      'tipoElemento': ['select'],
      'elementosDestino': [$('.selectRoles')],
      'datosInsertar': [
        {
          'value': 'id_rol',
          'texto': 'nombre_rol',
          'textoDefault': 'Seleccione un rol'
        }
      ]
    })
    driverAyuda('usuarios', {
      pasos:
        [
          {
            element: 'button[data-bs-target=".modalRegistrar"]',
            popover: {
              title: 'Registrar Usuarios',
              description: 'Haz clic aquí para registrar un nuevo usuario del sistema.',
              side: 'bottom',
              align: 'start'
            }
          },
          {
            element: '.tabla-ajax',
            popover: {
              title: 'Lista de Usuarios',
              description: 'Aquí puedes ver todos los usuarios registradas del sistema.',
              side: 'top'
            }
          },
          {
            element: '.botonEditar',
            popover: {
              title: 'Editar Usuarios',
              description: 'Modifica el nombre, rol, foto de perfil etc de cualquier usuario haciendo clic en este botón.',
              side: 'left'
            }
          },
          {
            element: '.botonEliminar',
            popover: {
              title: 'Eliminar Usuarios',
              description: 'En este boton podras Eliminar los usuarios del sistema.',
              side: 'left'
            }
          },
          {
            popover: {
              title: '¡Ayuda completada!',
              description: 'Ya conoces la gestion de usuarios. Da click en finaliar para acabar la ayuda.',
              side: 'top'
            }
          }
        ]
    });
  }
  cargarPreguntasSeguridad()
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', async function (e) {
  e.preventDefault();
  let resultado
  if ($(this).hasClass('login')) {
    const respuestaCaptcha = grecaptcha.getResponse();
    if (respuestaCaptcha === "" && !modoDev) {
      e.preventDefault();
      e.stopImmediatePropagation();
      Swal.fire({
        icon: 'warning',
        title: 'Validación Requerida',
        text: 'Por favor, resuelva el puzle de seguridad para poder ingresar.',
        confirmButtonColor: '#4e54c8'
      });
      return false;
    }
    resultado = await enviarFormulario({
      'formulario': this,
      'modulo': 'usuarios',
    });
  } else {
    resultado = await enviarFormulario({
      'formulario': this,
      'modulo': 'usuarios',
      'tipoCuerpo': 'JSON'
    });
  }


  if ($(this).hasClass('login') && resultado?.icono == 'error') {
    grecaptcha.reset();
  }
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  form.find('[name="prefijo_telefono_usuario"]').val(datos.telefono_usuario.slice(0, 4));
  form.find('[name="telefono_usuario"]').val(datos.telefono_usuario.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input change blur', '.validar input, .validar select')
$(document).on('input change blur', '.validar input, .validar select', async function () {
  await validarEnTiempoReal(this, 'usuarios');
  let modal = $(this).closest('.modal');
  if (modal.hasClass('modalDatosUsuarios') || modal.hasClass('modalPreguntasSeguridad')) {
    let boton = modal.find('.btnSiguiente');
    habilitarDeshabilitarBoton(modal, boton);
  }
});

// Evento para validar el form completo antes de pasar el siguiente modal de preguntas de seguridad
$(document).off('click', '.btnSiguiente')
$(document).on('click', '.btnSiguiente', async function () {
  validarFormsCompletos.call(this);
});

// Evento para evitar que una pregunta pueda ser seleccionada dos veces
$(document).off('change', '.selectPregunta ')
$(document).on('change', '.selectPregunta ', async function () {
  eliminarRestablecerPregunta.call(this)
});

//Evento para validar existencia de usuario
$(document).off('input', '.inputCedulaRecContrasena')
$(document).on('input', '.inputCedulaRecContrasena', async function () {
  await habilitarDeshabilitarBotonRecContrasena.call(this);
})

//Evento para solicitar la validacion (correo, sms, preguntas)
$(document).off('click', '.btnContinuar')
$(document).on('click', '.btnContinuar', async function () {
  solicitarValidacionDeSeguridad.call(this);
});

//Evento para enviar las respuestas a las preguntas de seguridad
$(document).off('click', '.enviarPreguntasSeg')
$(document).on('click', '.enviarPreguntasSeg', async function () {
  let respuesta = await enviarFormulario({
    formulario: $(this).closest('.modal'),
    modulo: 'usuarios',
    tipoCuerpo: 'JSON'
  });
  let cedula = objCacheSS.getItem('usuario/cedula_recuperar_clave');
  let modal = $('.modalCambiarContrasena');
  if (respuesta?.icono == 'success') {
    $(this).closest('.modal').modal('hide');
    modal.modal('show').find('[name="cedula_usuario"]').val(cedula);
    modal.find('[name="hashContrasena"]').val(respuesta.codigoRestauracion);
    modal.find('[name="contrasena1_usuario"], [name="contrasena2_usuario"]').val('')
  } else {
    modal.find('[name="hashContrasena"]').val('');
  }
});

//Evento para enviar la contrasena nueva
$(document).off('click', '.btnCambiarContrasena')
$(document).on('click', '.btnCambiarContrasena', async function () {
  let respuesta = await enviarFormulario({
    formulario: $(this).closest('.modal'),
    modulo: 'usuarios',
  });
  if (respuesta?.icono == 'success') {
    $(this).closest('.modal').modal('hide');
  }
});

//Evento para enviar el codigo de seguridad enviado al correo/celular
$(document).off('click', '.enviarCogSeguridad')
$(document).on('click', '.enviarCogSeguridad', async function () {
  let respuesta = await enviarFormulario({
    formulario: $(this).closest('.modal'),
    modulo: 'usuarios',
  });

  let cedula = objCacheSS.getItem('usuario/cedula_recuperar_clave');
  let modal = $('.modalCambiarContrasena');
  if (respuesta?.icono == 'success') {
    $(this).closest('.modal').modal('hide');
    modal.modal('show').find('[name="cedula_usuario"]').val(cedula);
    modal.find('[name="hashContrasena"]').val(respuesta.codigoRestauracion);
    modal.find('[name="contrasena1_usuario"], [name="contrasena2_usuario"]').val('')
  } else {
    modal.find('[name="hashContrasena"]').val('');
  }

});

//#endregion [DELEGACIÓN DE EVENTOS] FIN
