//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
async function inicializarModulo() {
  await listarDataTable({
    encabezados: {
      "id_banco": "ID",
      "nombre_banco": "NOMBRE DEL BANCO",
    },
    informacionPe: {
      'modulo': 'bancos',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_banco',
    botones: 'CRUD',
  });
  driverAyuda('bancos', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Bancos',
            description: 'Haz clic aquí para agregar un nuevo banco al sistema. Los bancos se necesitan para la gestion de las ordenes de entrega.',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Bancos',
            description: 'Aquí puedes ver todos los bancos registrados.',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Bancos',
            description: 'Modifica el nombre de cualquier banco haciendo clic en este botón.',
            side: 'left'
          }
        },
        {
          element: '.botonEliminar',
          popover: {
            title: 'Eliminar Bancos',
            description: 'Elimina los bancos que ya no sean necesarios. Ten cuidado porque esto puede afectar la gestion de ordenes de entrega.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completada!',
            description: 'Ya conoces la gestion de bancos. Da click en finaliar para acabar la ayuda.',
            side: 'top'
          }
        }
      ]
  });

  // Agregamos la clase 'noRepetir' al campo de nombre para activar la validación de unicidad
  $('input[name="nombre_banco"]').addClass('noRepetir');
}
function submitFormularioAjax(e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'bancos',
  });
}
function clickBotonEliminar(e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_banco',
    modulo: 'bancos',
  });
}
async function clickBotonEditar(e) {
  e.preventDefault();
  const modalTarget = $(this).attr('data-bs-target');
  const form = $(modalTarget).find('form');

  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_banco',
    modulo: 'bancos',
  });
  // Aseguramos que en el formulario de edición también tenga las clases necesarias
  form.find('input[name="nombre_banco"]').addClass('noRepetir formularioActualizar');
  // Prepara la validación para campos que no deben repetirse en la actualización
  cargarInputsActualizarQNR.call(form);
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
}
function inputValidarTiempoReal() {
  validarEnTiempoReal(this, 'bancos');
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  inicializarModulo.call(this);
});

// Evento para el envío de formularios (Registro y Actualización)
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  submitFormularioAjax.call(this, e);
});

// Evento para la eliminación de registros
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  clickBotonEliminar.call(this, e);
});

// Evento para los botones de editar (Cargar datos en el modal)
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', function (e) {
  clickBotonEditar.call(this, e);
});

// Evento para validar en tiempo real según los patrones definidos
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  inputValidarTiempoReal.call(this);
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN