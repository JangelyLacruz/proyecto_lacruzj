//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,
  funcionEliminaError, funcionMandarError
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"
//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
// Función para mostrar u ocultar el número de control según la selección (J / otros)
function toggleNumeroControl(elemento) {
  let $select = $(elemento);
  if (!$select.length) return;

  let $form = $select.closest('form');
  let $contenedor = $form.find('.contenedorNumControl');
  let $input = $form.find('input[name="numero_control_factura"]');

  if ($contenedor.length && $input.length) {
    if ($select.val() === 'J') {
      $contenedor.show();
      
      // Habilitar la entrada
      $input.prop('disabled', false)
            .removeAttr('disabled')
            .removeClass('is-invalid invalid'); 
      
      
      if (typeof funcionEliminaError === 'function') {
        funcionEliminaError($input[0]);
      }
    } else {
      $contenedor.hide();
      $input.val('');
      $input.prop('disabled', true)
            .attr('disabled', 'disabled');
      
      if (typeof funcionEliminaError === 'function') {
        funcionEliminaError($input[0]);
      }
    }
  }
}

window.toggleNumeroControl = toggleNumeroControl;
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
(async function inicializarModulo() {
//$(document).on('DOMContentLoaded', async function (e) {
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
}) ();

// Escuchar cambios en el selector de tipo RIF/Cédula
$(document).off('change', '.selectCodigoRIF');
$(document).on('change', '.selectCodigoRIF', function () {
  toggleNumeroControl(this);
});

// Reseteo al abrir modal Registrar
$(document).off('show.bs.modal', '.modalRegistrar');
$(document).on('show.bs.modal', '.modalRegistrar', function () {
  let $select = $(this).find('.selectCodigoRIF');
  setTimeout(function () {
    toggleNumeroControl($select);
  }, 50);
});

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
 
  let $modal = $($(this).attr('data-bs-target'));
  let form = $modal.find('form');

  // Evaluar letra del RIF/Cédula 
  let letraRif = datos.rif_cedula_cliente ? datos.rif_cedula_cliente.charAt(0).toUpperCase() : '';
  let contenedor = form.find('.contenedorNumControl');
  let inputNum = form.find('.inputNumControl');

  // Si la letra es 'J', mostramos el campo en el modal de editar
  if (letraRif === 'J') {
    contenedor.show();
    inputNum.prop('disabled', false).removeAttr('disabled');
    inputNum.val(datos.numero_control_factura || '');
  } else {
    contenedor.hide();
    inputNum.prop('disabled', true).attr('disabled', 'disabled');
    inputNum.val('');
  }
 
  form.find('[name="prefijo_telefono_cliente"]').val(datos.telefono_cliente.slice(0, 4));
  form.find('[name="telefono_cliente"]').val(datos.telefono_cliente.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input', '.inputNumControl');
$(document).on('input', '.inputNumControl', function () {
  let $this = $(this);
  // Convertir a mayúsculas
  let val = $this.val().toUpperCase();
  if (val.length > 10) {
    val = val.slice(0, 10);
  }
  $this.val(val);
});

$(document).off('input change', '.validar input, .validar select, .validar textarea')
$(document).on('input change', '.validar input, .validar select, .validar textarea', function () {
 if ($(this).hasClass('selectCodigoRIF')) {
    toggleNumeroControl(this);
    return;
  }

  if (!$(this).prop('disabled')) {
    validarEnTiempoReal(this, 'clientes');
  }

});
//#endregion [DELEGACIÓN DE EVENTOS] FIN