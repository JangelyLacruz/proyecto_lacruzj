//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  listarDataTable({
    encabezados: {
      "id_empresa_envios": "ID",
      "nombre_empresa": "NOMBRE",
    },
    informacionPe: {
      'modulo': 'empresasEnvios',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_empresa_envios',
    botones: 'CRUD',
    visualizacionResponsiveColumnas: {
      "nombre_empresa": 1,
      "id_empresa_envios": 2,
    },
  });
  driverAyuda('empresasEnvios', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Empresa de Envíos',
          description: 'Haz clic aquí para agregar una nueva empresa de envíos al sistema. Estas empresas se utilizan para gestionar los envíos de pedidos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Empresas de Envíos',
          description: 'Aquí puedes ver todas las empresas de envíos registradas en el sistema.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Empresa',
          description: 'Modifica el nombre de cualquier empresa de envíos haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Empresa',
          description: 'Elimina empresas de envíos que ya no sean necesarias. Ten cuidado porque esto puede afectar pedidos asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de empresas de envíos. Puedes registrar, editar o eliminar empresas para gestionar los envíos de tus pedidos.',
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
    'modulo': 'empresasEnvios'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_empresa_envios',
    modulo: 'empresasEnvios',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_empresa_envios',
    modulo: 'empresasEnvios',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'empresasEnvios');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN