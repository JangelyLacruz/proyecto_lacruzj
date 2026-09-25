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
      "id_metodo_pago": "ID",
      "nombre_metodo_pago": "NOMBRE",
      "necesita_moneda": "MONEDA",
      "necesita_banco_emisor": "B. EMISOR",
      "necesita_banco_receptor": "B. RECEPTOR",
      "necesita_referencia": "REFERENCIA",
      "mostrar_ecommerce": "ECOMMERCE",
    },
    informacionPe: {
      'modulo': 'metodos-pago',
      'datosPe': { 'accion': 'listar' }
    },
    campoIdBtn: 'id_metodo_pago',
    botones: 'CRUD',
    infoTratoEspecial: {
      necesita_moneda: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_banco_emisor: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_banco_receptor: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_referencia: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      mostrar_ecommerce: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
    },
    visualizacionResponsiveColumnas: {
      "id_metodo_pago": 1,
      "nombre_metodo_pago": 1,
      "necesita_moneda": 2,
      "necesita_banco_emisor": 3,
      "necesita_banco_receptor": 4,
      "necesita_referencia": 5,
      "mostrar_ecommerce": 6,
    },
  });
  driverAyuda('metodos-pago', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Método de Pago',
          description: 'Haz clic aquí para agregar un nuevo método de pago al sistema. Ejemplos: Transferencia, Pago Móvil, Zelle, Efectivo, etc.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Métodos de Pago',
          description: 'Aquí puedes ver todos los métodos de pago registrados y sus características.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Método',
          description: 'Modifica las características de cualquier método de pago haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Método',
          description: 'Elimina métodos de pago que ya no sean necesarios. Ten cuidado porque puede afectar pagos registrados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de métodos de pago. Estos se utilizan en el proceso de facturación y cobros.',
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
    'modulo': 'metodos-pago'
  });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_metodo_pago',
    modulo: 'metodos-pago',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();

  // Obtener los datos (esta función ya llena los inputs de texto automáticamente)
  const datos = await obtenerDatosRegistro({ boton: this, campoId: 'id_metodo_pago', modulo: 'metodos-pago' });
  const form = $($(this).attr('data-bs-target')).find('form');

  /* // Sincronizar switches
  form.find('input[type="checkbox"]').each(function() {
    const name = $(this).attr('name');
    $(this).prop('checked', datos[name] == 1);
  }); */

  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'metodos-pago');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
