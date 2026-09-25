//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "id_ruta": "ID",
      "nombre_ruta": "NOMBRE",
      "precio_ruta": "PRECIO",
      "minimo_km_ruta": "MÍNIMO KM",
      "maximo_km_ruta": "MÁXIMO KM",
    },
    informacionPe: {
      'modulo': 'rutas',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_ruta',
    botones: 'CRUD',
    infoTratoEspecial: {
      precio_ruta: (info) => { return info.valor + '$/Km' },
      minimo_km_ruta: (info) => { return info.valor + ' Km' },
      maximo_km_ruta: (info) => { return info.valor + ' Km' },
    },
    visualizacionResponsiveColumnas: {
      "nombre_ruta": 1,
      "id_ruta": 2,
      "precio_ruta": 3,
      "minimo_km_ruta": 4,
      "maximo_km_ruta": 5,
    },
  });
  driverAyuda('rutas', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Rutas',
            description: 'Haz clic aquí para registrar una nueva ruta al sistema.Las ruta son necesarias para las direcciones del sistema',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Rutas',
            description: 'Aquí puedes ver todas las rutas registradas del sistema.',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Rutas',
            description: 'Modifica el nombre, precio, Minimo Km y Maximo KM de cualquier ruta haciendo clic en este botón.',
            side: 'left'
          }
        },
        {
          element: '.botonEliminar',
          popover: {
            title: 'Eliminar Rutas',
            description: 'Elimina las Rutas que ya no sean necesarios.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completada!',
            description: 'Ya conoces la gestion de rutas. Da click en finaliar para acabar la ayuda.',
            side: 'top'
          }
        }
      ]
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'rutas'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_ruta',
    modulo: 'rutas',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_ruta',
    modulo: 'rutas',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  cargarInputsActualizarQNR.call(form);
  form.find('.dineroPositivo').trigger('input')
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'rutas');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN