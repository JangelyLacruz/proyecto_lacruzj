//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
async function inicializarModulo() {
  await listarDataTable({
    encabezados: {
      "id_rol": "ID DEL ROL",
      "nombre_rol": "NOMBRE DEL ROL",
    },
    informacionPe: {
      'modulo': 'roles',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_rol',
    botones: 'CRUD',
  });
  
  registrarTutorial();
  
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'roles') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
}

function registrarTutorial() {
  driverAyuda('roles', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Rol',
          description: 'Haz clic aquí para agregar un nuevo rol al sistema. Los roles son asignados a los usuarios para controlar sus permisos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Roles',
          description: 'Aquí puedes ver todos los roles registrados en el sistema.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Rol',
          description: 'Modifica el nombre de cualquier rol haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Rol',
          description: 'Elimina roles que ya no sean necesarios en el sistema.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de roles. Da click en finalizar para acabar la ayuda.',
          side: 'top'
        }
      }
    ]
  });
}

function submitFormularioAjax(e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'roles'
  });
}

function clickBotonEliminar(e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_rol',
    modulo: 'roles',
  });
}

async function clickBotonEditar(e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_rol',
    modulo: 'roles',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
}

function inputValidarTiempoReal() {
  validarEnTiempoReal(this, 'roles');
}
//#region [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await inicializarModulo.call(this);
});

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  submitFormularioAjax.call(this, e);
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  clickBotonEliminar.call(this, e);
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', function (e) {
  clickBotonEditar.call(this, e);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  inputValidarTiempoReal.call(this);
});

//#endregion [DELEGACIÓN DE EVENTOS] FIN