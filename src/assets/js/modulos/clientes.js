//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,
  funcionEliminaError, funcionMandarError
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"
//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "rif_cedula_cliente": "CÉDULA/RIF",
      "razon_social_cliente": "RAZÓN SOCIAL",
      "telefono_cliente": "TELÉFONO",
      "correo_cliente": "CORREO ELECTRÓNICO",
      "direccion_cliente": "DIRECCIÓN",
    },
    informacionPe: {
      'modulo': 'clientes',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'rif_cedula_cliente',
    botones: 'CRUD',
  });
  driverAyuda('clientes', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Cliente',
          description: 'Haz clic aquí para agregar un nuevo cliente al sistema. Los clientes son necesarios para generar facturas y pedidos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Clientes',
          description: 'Aquí puedes ver todos los clientes registrados con su información de contacto y dirección.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Cliente',
          description: 'Modifica la información de cualquier cliente haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Cliente',
          description: 'Elimina clientes que ya no sean necesarios. Ten cuidado porque esto puede afectar facturas y pedidos asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de clientes. Puedes registrar, editar o eliminar clientes para mantener actualizada tu base de datos.',
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
    'modulo': 'clientes',
  });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'rif_cedula_cliente',
    modulo: 'clientes',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'rif_cedula_cliente',
    modulo: 'clientes',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  form.find('[name="prefijo_telefono_cliente"]').val(datos.telefono_cliente.slice(0, 4));
  form.find('[name="telefono_cliente"]').val(datos.telefono_cliente.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select, .validar textarea')
$(document).on('input', '.validar input, .validar select, .validar textarea', function () {
  validarEnTiempoReal(this, 'clientes');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN