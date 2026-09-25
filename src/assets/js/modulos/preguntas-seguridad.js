//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, obtenerDatosRegistro, eliminarRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "id_pregunta": "ID DE LA PREGUNTA",
      "texto_pregunta": "PREGUNTA",
    },
    informacionPe: {
      'modulo': 'preguntas-seguridad',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_pregunta',
    botones: 'CRUD',
    visualizacionResponsiveColumnas: {
      "id_pregunta": 1,
      "texto_pregunta": 2,
    }
  });
  driverAyuda('preguntas_seguridad', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Pregunta',
            description: 'Haz clic aquí para agregar una nueva pregunta al sistema para que sea usada en las validaciones de seguridad de los usuarios',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Preguntas de seguridad',
            description: 'Aquí podrás ver la lista de preguntas registradas hasta el momento.',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Preguntas',
            description: 'Modifica el enunciado de las preguntas.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completada!',
            description: 'Ya conoces la gestión de preguntas de seguidad. Da click en finaliar para acabar la ayuda.',
            side: 'top'
          }
        }
      ]
  });
});

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'preguntas-seguridad'
  });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_pregunta',
    modulo: 'preguntas-seguridad',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_pregunta',
    modulo: 'preguntas-seguridad',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'preguntas-seguridad');
});

//#endregion [DELEGACIÓN DE EVENTOS] FIN