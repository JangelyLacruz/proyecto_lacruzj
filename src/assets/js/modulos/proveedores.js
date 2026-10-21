//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,
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
  let $input = $form.find('input[name="nro_control_factura"]');

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
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "rif_proveedor": "RIF",
      "razon_social_proveedor": "RAZON SOCIAL",
      "nro_control_factura": "NRO DE CONTROL",
      "telefono_proveedor": "TELÉFONO",
      "correo_proveedor": "CORREO ELECTRÓNICO",
      "direccion_proveedor": "DIRECCIÓN",
    },
    informacionPe: {
      'modulo': 'proveedores',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'rif_proveedor',
    botones: 'CRUD',
  });
  driverAyuda('proveedores', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Proveedor',
          description: 'Haz clic aquí para agregar un nuevo proveedor al sistema. Los proveedores se utilizan en las compras de productos y materias primas.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Proveedores',
          description: 'Aquí puedes ver todos los proveedores registrados con su información de contacto.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Proveedor',
          description: 'Modifica los datos de cualquier proveedor haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Proveedor',
          description: 'Elimina proveedores que ya no sean necesarios. Ten cuidado porque puede afectar compras asociadas.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de proveedores. Puedes registrar, editar o eliminar proveedores para mantener actualizada tu base de datos.',
          side: 'top'
        }
      }
    ]
  });
})

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
    'modulo': 'proveedores'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos= await obtenerDatosRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });

  let $modal = $($(this).attr('data-bs-target'));
  let form = $modal.find('form');
  cargarInputsActualizarQNR.call(form, datos);

  // Evaluar letra del RIF/Cédula 
  let letraRif = datos.rif_proveedor ? datos.rif_proveedor.charAt(0).toUpperCase() : '';
  let $selectRif = form.find('.selectCodigoRIF');
  if ($selectRif.length) {
    $selectRif.val(letraRif).trigger('change');
  }
  let contenedor = form.find('.contenedorNumControl');
  let inputNum = form.find('.inputNumControl');

  // Si la letra es 'J', mostramos el campo en el modal de editar
  if (letraRif === 'J') {
    contenedor.show();
    inputNum.prop('disabled', false).removeAttr('disabled');
    inputNum.val(datos.nro_control_factura || '');
  } else {
    contenedor.hide();
    inputNum.prop('disabled', true).attr('disabled', 'disabled');
    inputNum.val('');
  }

  form.find('[name="prefijo_telefono_proveedor"]').val(datos.telefono_proveedor.slice(0,4));
  form.find('[name="telefono_proveedor"]').val(datos.telefono_proveedor.slice(4));
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

$(document).off('input', '.validar input, .validar select, .validar textarea');
$(document).on('input', '.validar input, .validar select, .validar textarea', function () {
  if ($(this).hasClass('selectCodigoRIF')) {
      toggleNumeroControl(this);
      return;
    }
  
    if (!$(this).prop('disabled')) {
      validarEnTiempoReal(this, 'proveedores');
    }
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN