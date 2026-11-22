//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, pedirDatosAjax, enviarFormulario,
  alertasAjax, reiniciarDataTables, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"
//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES GLOBALES] COMIENZO
let dtPagos;
let dtOEPs;
let oepsSeleccionadas = {};
let metodosPagoOptions = '<option value="">Seleccione...</option>';
let monedasOptions = '';
let bancosOptions = '<option value="">Seleccione...</option>';
let tasaDolar = 1;
let oepActual = null;
//#endregion [VARIABLES GLOBALES] FIN

//#region [FUNCIONES DEL MODULO] COMIENZO

function registrarTutorial() {
  driverAyuda('pagos', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Pago',
          description: 'Haz clic aquí para registrar un nuevo pago asociado a una Orden de Entrega Presupuesto (OEP).',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Pagos',
          description: 'Aquí puedes ver todos los pagos registrados, su monto total y los comprobantes asociados.',
          side: 'top'
        }
      },
      {
        element: '.btnVerDetalle',
        popover: {
          title: 'Ver Detalles del Pago',
          description: 'Consulta todos los detalles de un pago, incluyendo el desglose por método y los comprobantes adjuntos.',
          side: 'left'
        }
      },
      {
        element: '.btnEditar',
        popover: {
          title: 'Editar Pago',
          description: 'Modifica los detalles de un pago existente. Puedes agregar o eliminar comprobantes.',
          side: 'left'
        }
      },
      {
        element: '.btnEliminar',
        popover: {
          title: 'Eliminar Pago',
          description: 'Anula el pago seleccionado. La OEP quedará con el saldo pendiente correspondiente.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de pagos. Recuerda que cada pago se asocia a una OEP y puede tener múltiples métodos de pago.',
          side: 'top'
        }
      }
    ]
  });
}

//#region [HELPERS DE MONTO] COMIENZO
function parsearMonto(rawStr) {
  let s = (rawStr || '').trim();
  if (s === '' || s === '0') return 0;
  // Formato con coma decimal "1.500,50" o "0,50" (máscara dinero europea)
  if (s.includes(',')) {
    // Quitar puntos de miles, reemplazar coma por punto
    let clean = s.replace(/\./g, '').replace(',', '.');
    return parseFloat(clean) || 0;
  }
  // Formato con punto decimal y sin coma (ej: "1500.50")
  if (s.includes('.')) {
    return parseFloat(s) || 0;
  }
  // Solo dígitos sin separadores: tomar el valor directo como entero
  return parseFloat(s.replace(/\D/g, '')) || 0;
}


//El backend espera formato europeo con coma decimal: "0,50", "1500,50"

function formatearMontoEnvio(monto) {
  return parseFloat(monto).toFixed(2).replace('.', ',');
}
//#endregion [HELPERS DE MONTO] FIN

//#region [FUNCIONES DEL MODULO] COMIENZO

async function cargarCatalogos() {
  try {
    let pMetodos = pedirDatosAjax({
      modulo: "metodos-pago",
      noGuardarLocal: true,
      datosPe: { accion: "listar" }
    });

    let pMonedas = pedirDatosAjax({
      modulo: "monedas",
      noGuardarLocal: true,
      datosPe: { accion: "listar" }
    });

    let pBancos = pedirDatosAjax({
      modulo: "bancos",
      noGuardarLocal: true,
      datosPe: { accion: "listar" }
    });

    let [resMetodos, resMonedas, resBancos] = await Promise.all([pMetodos, pMonedas, pBancos]);

    if (Array.isArray(resMetodos)) {
      metodosPagoOptions = '<option value="">Seleccione...</option>' + resMetodos.filter(m => m.status == 1).map(m => `<option value="${m.id_metodo_pago}" data-moneda="${m.necesita_moneda}" data-emisor="${m.necesita_banco_emisor}" data-receptor="${m.necesita_banco_receptor}" data-ref="${m.necesita_referencia}">${m.nombre_metodo_pago}</option>`).join('');
    }
    if (Array.isArray(resMonedas)) {
      monedasOptions = resMonedas.filter(m => m.status == 1).map(m => `<option value="${m.id_moneda}" data-valor="${m.valor_moneda}" data-simbolo="${m.simbolo_moneda || ''}" data-nombre="${m.nombre_moneda}">${m.nombre_moneda}</option>`).join('');
      let monDolar = resMonedas.find(m => m.nombre_moneda.toUpperCase() === 'DÓLAR' || m.nombre_moneda.toUpperCase() === 'DOLAR');
      if (monDolar) tasaDolar = parseFloat(monDolar.valor_moneda);
    }
    if (Array.isArray(resBancos)) {
      bancosOptions = '<option value="">Seleccione...</option>' + resBancos.filter(b => b.status == 1).map(b => `<option value="${b.id_banco}">${b.nombre_banco}</option>`).join('');
    }
  } catch (e) {
    console.log("Error cargando catalogos: ", e);
  }
}

function inicializarTablas() {
  dtPagos = listarDataTable({
    selectorTabla: ".tabla-ajax",
    encabezados: {
      "id_pago": "CÓDIGO PAGO",
      "id_orden_entrega_presupuesto": "OEP ASOCIADA",
      "CLIENTE": "CLIENTE",
      "fecha_pago": "FECHA",
      "monto_total_dolares": "TOTAL PAGADO",
      "cant_comprobantes": "COMPROBANTES"
    },
    informacionPe: {
      modulo: "pagos",
      datosPe: { accion: "listar" }
    },
    infoTratoEspecial: {
      monto_total_dolares: ({ valor }) => `<span class="badge bg-success">$ ${parseFloat(valor).toFixed(2)}</span>`,
      cant_comprobantes: ({ valor }) => valor > 0 ? `<span class="badge bg-info">${valor}</span>` : `<span class="badge bg-secondary">0</span>`
    },
    botones: ({ fila }) => `
      <ul class="list-inline mb-0">
        <li class="list-inline-item align-bottom">
          <a href="#" value="${fila.id_pago}" data-id="${fila.id_pago}" class="btnVerDetalle avtar avtar-xs btn-link-info" title="Ver Detalles">
            <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
          </a>
        </li>
        <li class="list-inline-item align-bottom">
          <a href="#" value="${fila.id_pago}" data-id="${fila.id_pago}" class="btnEditar avtar avtar-xs btn-link-success" title="Editar Pago">
            <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
          </a>
        </li>
        <li class="list-inline-item align-bottom">
          <a href="#" value="${fila.id_pago}" data-id="${fila.id_pago}" class="btnEliminar avtar avtar-xs btn-link-danger" title="Eliminar Pago">
            <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
          </a>
        </li>
      </ul>
    `
  });

  dtOEPs = listarDataTable({
    selectorTabla: "#dtSelOEP",
    encabezados: {
      "id_orden_entrega_presupuesto": "CÓDIGO OEP",
      "CLIENTE": "CLIENTE",
      "fecha_orden_entrega_presupuesto": "FECHA",
      "total_orden": "TOTAL ORDEN",
      "restante": "RESTANTE"
    },
    informacionPe: {
      modulo: "pagos",
      datosPe: { accion: "obtenerOEPs" }
    },
    infoTratoEspecial: {
      total_orden: ({ valor }) => `$ ${parseFloat(valor).toFixed(2)}`,
      restante: ({ valor }) => `<span class="text-danger fw-bold">$ ${parseFloat(valor).toFixed(2)}</span>`
    },
    botones: ({ fila }) => {
      oepsSeleccionadas[fila.id_orden_entrega_presupuesto] = fila;
      return `<button type="button" class="btn btn-sm btn-success btnElegirOEP" data-id="${fila.id_orden_entrega_presupuesto}"><i class="fi fi-rs-check"></i> Elegir</button>`;
    }
  });
}

function actualizarBotonesEliminar() {
  let filas = $("#contenedorDetallesPagoModulo .fila-pago");
  if (filas.length <= 1) {
    filas.find(".btn-eliminar-pago").addClass("d-none");
  } else {
    filas.find(".btn-eliminar-pago").removeClass("d-none");
  }
  filas.each(function(idx) {
    $(this).find(".nroDetalle").text(`#${idx + 1}`);
  });
}

function agregarFilaPago(datos = null) {
  try {
    let numFilas = $("#contenedorDetallesPagoModulo .fila-pago").length + 1;
    let $nuevo = $(".plantillaDetallePago .fila-pago").clone();
    
    if ($nuevo.length === 0) {
      throw new Error("No se encontró la plantillaDetallePago en el HTML.");
    }
    
    $nuevo.find(".nroDetalle").text(`#${numFilas}`);
    $nuevo.find(".sel-metodo-pago").html(metodosPagoOptions).prop("selectedIndex", 0);
    $nuevo.find(".sel-moneda-pago").html(monedasOptions).prop("selectedIndex", 0);
    $nuevo.find(".sel-banco-emisor").html(bancosOptions).prop("selectedIndex", 0);
    $nuevo.find(".sel-banco-receptor").html(bancosOptions).prop("selectedIndex", 0);
    $nuevo.find(".input-referencia-pago").val("");
    $nuevo.find(".input-monto-pago").val("");
    $nuevo.find(".span-simbolo-moneda").text("$");
    $nuevo.find(".col-moneda-pago, .col-banco-emisor, .col-banco-receptor, .col-referencia-pago").addClass("d-none");

    if (datos) {
      $nuevo.find(".sel-metodo-pago").val(datos.id_metodo_pago);
      $nuevo.find(".sel-metodo-pago").trigger("change");

      if (datos.id_moneda) {
        $nuevo.find(".sel-moneda-pago").val(datos.id_moneda);
      }
      if (datos.id_banco_emisor) {
        $nuevo.find(".sel-banco-emisor").val(datos.id_banco_emisor);
      }
      if (datos.id_banco_receptor) {
        $nuevo.find(".sel-banco-receptor").val(datos.id_banco_receptor);
      }
      if (datos.referencia_pago) {
        $nuevo.find(".input-referencia-pago").val(datos.referencia_pago);
      }
      
      let montoFormateado = datos.monto_pago ? datos.monto_pago.toString().replace('.', ',') : '';
      $nuevo.find(".input-monto-pago").val(montoFormateado);
    } else {
      $nuevo.find(".sel-metodo-pago").trigger("change");
    }

    $("#contenedorDetallesPagoModulo").append($nuevo);
    $nuevo.css("display", "");
    actualizarBotonesEliminar();
    calcularRestante();
  } catch (e) {
    console.error("Error en agregarFilaPago: ", e);
  }
}

function vaciarFormulario() {
  try {
    const form = $(".formularioAjax");
    if (form.length > 0) {
      form[0].reset();
    }
    form.find("input[name=accion]").val("registrar");
    $("#inputIdPago").val("").prop("disabled", true);
    $(".tituloModal").text("Detalles del Pago");
    oepActual = null;
    $("#infoTotalesOEP").addClass("d-none");
    $("#btnAgregarOtroPagoModulo").addClass("d-none");
    $("#contenedorDetallesPagoModulo").empty();
    $("#contenedorComprobantesExistentes").empty();
    $("#totalOrdenSel").text("$0.00");
    $("#pagadoOrdenSel").text("$0.00");
    $("#restanteOrdenSel").text("$0.00");
    $('.btnEnviarFormulario').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary').css('background', 'linear-gradient(135deg, #1a56db, #0ea5e9)');
  } catch (e) {
    console.error("Error en vaciarFormulario: ", e);
  }
}

function calcularRestante() {
  if (!oepActual) return;
  
  let totalOrden = parseFloat(oepActual.total_orden) || 0;
  let restanteOriginal = (oepActual.restante !== null && oepActual.restante !== undefined) ? parseFloat(oepActual.restante) : totalOrden;
  
  let sumPagadoEnModal = 0;
  let ultimaMonedaSeleccionada = null;

  $('#contenedorDetallesPagoModulo .fila-pago').each(function () {
    let rawVal = $(this).find('.input-monto-pago').val() || '0';
    let valInput = parsearMonto(rawVal);
    let optMetodo = $(this).find('.sel-metodo-pago option:selected');
    let reqMoneda = optMetodo.data('moneda') == 1;

    let tasaMonedaSeleccionada = 1; // Por defecto Bolívares tiene valor 1
    let simboloMonedaFila = '$';

    if (reqMoneda) {
      let optMoneda = $(this).find('.sel-moneda-pago option:selected');
      if (optMoneda.length && optMoneda.val() !== '') {
        tasaMonedaSeleccionada = parseFloat(optMoneda.data('valor') || 1);
        let simb = optMoneda.data('simbolo');
        let nomb = (optMoneda.data('nombre') || '').toUpperCase();
        simboloMonedaFila = simb || (nomb === 'DÓLAR' || nomb === 'DOLAR' ? '$' : (nomb === 'BÓLIVAR' || nomb === 'BOLIVAR' || nomb === 'BS' ? 'Bs' : nomb));
        ultimaMonedaSeleccionada = {
          id_moneda: optMoneda.val(),
          nombre: nomb,
          simbolo: simboloMonedaFila,
          valor: tasaMonedaSeleccionada
        };
      }
    } else {
      let nombreMetodo = (optMetodo.text() || '').toUpperCase();
      if (nombreMetodo.includes('PAGO MÓVIL') || nombreMetodo.includes('PAGO MOVIL') || nombreMetodo.includes('BIOPAGO') || nombreMetodo.includes('PUNTO DE VENTA') || nombreMetodo.includes('BOLÍVAR') || nombreMetodo.includes('BOLIVAR')) {
        simboloMonedaFila = 'Bs';
        ultimaMonedaSeleccionada = {
          id_moneda: '',
          nombre: 'BOLÍVAR',
          simbolo: 'Bs',
          valor: 1
        };
      }
    }

    // Actualizamos el span de la fila con el símbolo de la moneda
    $(this).find('.span-simbolo-moneda').text(simboloMonedaFila);

    // Formula universal: (monto * valor_de_su_moneda_en_bs) / valor_del_dolar_en_bs
    let montoEnDolares = (valInput * tasaMonedaSeleccionada) / tasaDolar;
    sumPagadoEnModal += montoEnDolares;
  });

  let nuevoRestante = restanteOriginal - sumPagadoEnModal;
  let excede = nuevoRestante < -0.01;
  let restanteBs = nuevoRestante * tasaDolar;

  // Determinamos qué moneda y conversión mostrar en el RESTANTE
  let htmlRestante = '';
  if (ultimaMonedaSeleccionada && ultimaMonedaSeleccionada.nombre !== 'DÓLAR' && ultimaMonedaSeleccionada.nombre !== 'DOLAR') {
    if (ultimaMonedaSeleccionada.nombre === 'BÓLIVAR' || ultimaMonedaSeleccionada.nombre === 'BOLIVAR' || ultimaMonedaSeleccionada.nombre === 'BS') {
      let montoEnBs = restanteBs;
      if (excede) {
        htmlRestante = `<span class="text-danger">Bs ${montoEnBs.toFixed(2)}</span> <small class="text-danger fw-normal fs-6"> / $ ${nuevoRestante.toFixed(2)} — ¡Excede el monto!</small>`;
      } else {
        htmlRestante = `Bs ${montoEnBs.toFixed(2)} <small class="text-white fw-normal fs-6"> / $ ${nuevoRestante.toFixed(2)}</small>`;
      }
    } else {
      // Moneda extranjera (ej. Euro): se convierte de acuerdo al dólar y bolívar
      let montoMoneda = ultimaMonedaSeleccionada.valor > 0 ? (restanteBs / ultimaMonedaSeleccionada.valor) : 0;
      let simb = ultimaMonedaSeleccionada.simbolo || '€';
      if (excede) {
        htmlRestante = `<span class="text-danger">${simb} ${montoMoneda.toFixed(2)}</span> <small class="text-danger fw-normal fs-6"> / Bs ${restanteBs.toFixed(2)} — ¡Excede el monto!</small>`;
      } else {
        htmlRestante = `${simb} ${montoMoneda.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${restanteBs.toFixed(2)}</small>`;
      }
    }
  } else {
    // Dólar por defecto
    if (excede) {
      htmlRestante = `<span class="text-danger">$${nuevoRestante.toFixed(2)}</span> <small class="text-danger fw-normal fs-6"> / Bs ${restanteBs.toFixed(2)} — ¡Excede el monto!</small>`;
    } else {
      htmlRestante = `$${nuevoRestante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${restanteBs.toFixed(2)}</small>`;
    }
  }

  $('#restanteOrdenSel').html(htmlRestante);

  if (excede) {
    $('.btnEnviarFormulario').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary').css('background', '');
  } else {
    $('.btnEnviarFormulario').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary').css('background', 'linear-gradient(135deg, #1a56db, #0ea5e9)');
  }
}

//#endregion [FUNCIONES DEL MODULO] FIN

//#region [ EVENTOS DEL DOM Y EJECUCION ] COMIENZO
document.addEventListener("DOMContentLoaded", async () => {
  //  Registrar el tutorial primero
  registrarTutorial();
  
  await cargarCatalogos();
  inicializarTablas();
  
  // Verificar si hay un driver pendiente para pagos
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'pagos') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

$(document).off('click', '[data-bs-target=".modalRegistrar"]').on('click', '[data-bs-target=".modalRegistrar"]', function () {
  vaciarFormulario();
  reiniciarDataTables(dtOEPs);
});

$(document).off("click", "#btnSeleccionarOEP").on("click", "#btnSeleccionarOEP", function () {
  $("#modalSelOEP").modal("show");
});

$(document).off("click", "#btnAgregarOtroPagoModulo").on("click", "#btnAgregarOtroPagoModulo", function () {
  agregarFilaPago();
});

$(document).off("click", ".btn-eliminar-pago").on("click", ".btn-eliminar-pago", function () {
  $(this).closest(".fila-pago").remove();
  actualizarBotonesEliminar();
  calcularRestante();
});

$(document).off("input", "#contenedorDetallesPagoModulo .input-monto-pago").on("input", "#contenedorDetallesPagoModulo .input-monto-pago", function() {
  // setTimeout(0) ensures we read the value AFTER the dinero mask has reformatted it
  setTimeout(calcularRestante, 0);
});
$(document).off("blur", "#contenedorDetallesPagoModulo .input-monto-pago").on("blur", "#contenedorDetallesPagoModulo .input-monto-pago", function() {
  setTimeout(calcularRestante, 0);
});
$(document).off("change", "#contenedorDetallesPagoModulo .sel-moneda-pago").on("change", "#contenedorDetallesPagoModulo .sel-moneda-pago", calcularRestante);

$(document).off("change", "#contenedorDetallesPagoModulo .sel-metodo-pago").on("change", "#contenedorDetallesPagoModulo .sel-metodo-pago", function () {
  let opt = $(this).find('option:selected');
  let fila = $(this).closest('.fila-pago');

  let reqMoneda = opt.data('moneda') == 1;
  let reqEmisor = opt.data('emisor') == 1;
  let reqReceptor = opt.data('receptor') == 1;
  let reqRef = opt.data('ref') == 1;

  if (reqMoneda) fila.find('.col-moneda-pago').removeClass('d-none'); else fila.find('.col-moneda-pago').addClass('d-none');
  if (reqEmisor) fila.find('.col-banco-emisor').removeClass('d-none'); else fila.find('.col-banco-emisor').addClass('d-none');
  if (reqReceptor) fila.find('.col-banco-receptor').removeClass('d-none'); else fila.find('.col-banco-receptor').addClass('d-none');
  if (reqRef) fila.find('.col-referencia-pago').removeClass('d-none'); else fila.find('.col-referencia-pago').addClass('d-none');

  if (!reqMoneda) fila.find('.sel-moneda-pago').prop('selectedIndex', 0);
  if (!reqEmisor) fila.find('.sel-banco-emisor').prop('selectedIndex', 0);
  if (!reqReceptor) fila.find('.sel-banco-receptor').prop('selectedIndex', 0);
  if (!reqRef) fila.find('.input-referencia-pago').val('');
  
  calcularRestante();
});

$(document).off("click", ".btnElegirOEP").on("click", ".btnElegirOEP", function () {
  try {
    let id = $(this).data("id");
    let oep = oepsSeleccionadas[id];

    if (oep) {
      oepActual = oep;
      $("#inputIdOrdenPago").val(oep.id_orden_entrega_presupuesto);
      $("#inputClienteOEP").val(oep.CLIENTE);

      let total = parseFloat(oep.total_orden || 0);
      let restante = (oep.restante !== null && oep.restante !== undefined) ? parseFloat(oep.restante) : total;
      let cancelado = total - restante;

      $("#totalOrdenSel").text(`$${total.toFixed(2)}`);
      $("#pagadoOrdenSel").html(`$${cancelado.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(cancelado * tasaDolar).toFixed(2)}</small>`);
      $("#restanteOrdenSel").html(`$${restante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(restante * tasaDolar).toFixed(2)}</small>`);

      $("#infoTotalesOEP").removeClass("d-none");
      $("#btnAgregarOtroPagoModulo").removeClass("d-none");
      
      $("#contenedorDetallesPagoModulo").empty();
      agregarFilaPago();

      $("#modalSelOEP").modal("hide");
    }
  } catch (e) {
    console.error("Error en btnElegirOEP: ", e);
  }
});

$(document).off("click", ".btnEnviarFormulario").on("click", ".btnEnviarFormulario", async function () {
  const form = $(".formularioAjax");
  if (!oepActual || !$("#inputIdOrdenPago").val()) {
    Swal.fire('Atención', 'Debe seleccionar una Orden para asociar el pago', 'warning');
    return;
  }

  let pagosArr = [];
  let valido = true;

  $("#contenedorDetallesPagoModulo .fila-pago").each(function() {
    let optMetodo = $(this).find('.sel-metodo-pago option:selected');
    let reqMoneda = optMetodo.data('moneda') == 1;
    let reqEmisor = optMetodo.data('emisor') == 1;
    let reqReceptor = optMetodo.data('receptor') == 1;
    let reqRef = optMetodo.data('ref') == 1;

    let metodo = $(this).find(".sel-metodo-pago").val();
    let moneda = reqMoneda ? ($(this).find(".sel-moneda-pago").val() || null) : null;
    let bancoE = reqEmisor ? ($(this).find(".sel-banco-emisor").val() || null) : null;
    let bancoR = reqReceptor ? ($(this).find(".sel-banco-receptor").val() || null) : null;
    let ref = reqRef ? ($(this).find(".input-referencia-pago").val() || null) : null;
    let rawMonto = $(this).find(".input-monto-pago").val() || '0';
    let monto = parsearMonto(rawMonto);

    let filaValida = true;
    if (!metodo || isNaN(monto) || monto <= 0) filaValida = false;
    if (reqMoneda && !moneda) filaValida = false;
    if (reqEmisor && !bancoE) filaValida = false;
    if (reqReceptor && !bancoR) filaValida = false;
    if (reqRef && !ref) filaValida = false;

    if (!filaValida) {
      valido = false;
      $(this).addClass("border-danger");
    } else {
      $(this).removeClass("border-danger");
      pagosArr.push({
        id_metodo_pago: metodo,
        id_moneda: moneda,
        id_banco_emisor: bancoE,
        id_banco_receptor: bancoR,
        referencia_pago: ref,
        monto_pago: formatearMontoEnvio(monto)
      });
    }
  });

  if (!valido || pagosArr.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Atención',
      text: 'Campos faltantes o valor de monto igual a 0.'
    });
    return;
  }

  // Validar que la suma no exceda el restante
  let sumEnvio = 0;
  pagosArr.forEach((p, idx) => {
    let optMetodo = $('#contenedorDetallesPagoModulo .fila-pago').eq(idx).find('.sel-metodo-pago option:selected');
    let reqMoneda = optMetodo.length ? optMetodo.data('moneda') == 1 : false;

    let tasaMonedaSeleccionada = 1;

    if (reqMoneda) {
      let optMoneda = $('#contenedorDetallesPagoModulo .fila-pago').eq(idx).find('.sel-moneda-pago option:selected');
      if (optMoneda.val() !== '') {
        tasaMonedaSeleccionada = parseFloat(optMoneda.data('valor') || 1);
      }
    }

    let parsedMonto = parsearMonto(p.monto_pago);
    sumEnvio += (parsedMonto * tasaMonedaSeleccionada) / tasaDolar;
  });

  let restanteMax = parseFloat(oepActual.restante) || 0;
  if (sumEnvio > restanteMax + 0.01) {
    Swal.fire('Atención', 'El monto total de los pagos excede el restante de la Orden.', 'warning');
    return;
  }

  let files = $('#inputComprobantesPago')[0].files;
  let nuevos = files ? files.length : 0;
  let existentes = $("#contenedorComprobantesExistentes .comp-card").length;
  if (existentes + nuevos > 3) {
    Swal.fire('Atención', 'Solo puedes subir un máximo de 3 comprobantes por Orden', 'warning');
    return;
  }
  for (let i = 0; i < nuevos; i++) {
    let f = files[i];
    let ext = f.name.split('.').pop().toLowerCase();
    if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
      Swal.fire('Atención', 'El archivo ' + f.name + ' no tiene un formato válido (solo JPG, PNG y WEBP)', 'warning');
      return;
    }
  }

  // Remove old hidden input if exists
  form.find('input[name="pagos"]').remove();
  // Append pagos array as a hidden field so it gets collected by enviarFormulario
  form.append(`<input type="hidden" name="pagos" value='${JSON.stringify(pagosArr)}'>`);

  let resp = await enviarFormulario({
    formulario: form[0],
    modulo: "pagos",
    archivo: true
  });
  
  if (resp && (resp.tipo === "limpiarYcerrar" || (resp.tipo === "simple" && resp.icono === "success"))) {
    $(".modalRegistrar").modal("hide");
    reiniciarDataTables(dtPagos);
    reiniciarDataTables(dtOEPs);
  }
});

$(document).off("click", ".btnEditar").on("click", ".btnEditar", async function () {
  let idPago = $(this).data("id");
  vaciarFormulario();
  
  let resp = await pedirDatosAjax({
    modulo: "pagos",
    datosPe: { accion: "obtenerUno", id_pago: idPago }
  });

  if (resp && resp.id_pago) {
    const form = $(".formularioAjax");
    form.find("input[name=accion]").val("actualizar");
    $("#inputIdPago").val(resp.id_pago).prop("disabled", false);
    $("#inputIdOrdenPago").val(resp.id_orden_entrega_presupuesto);
    $("#inputClienteOEP").val(resp.CLIENTE);
    $(".tituloModal").text(`Editar Pago: ${resp.id_pago}`);
    
    // Set global OEP actual for calculations
    oepActual = {
      id_orden_entrega_presupuesto: resp.id_orden_entrega_presupuesto,
      CLIENTE: resp.CLIENTE,
      total_orden: resp.total_orden,
      restante: resp.restante
    };

    let total = parseFloat(resp.total_orden || 0);
    let restante = (resp.restante !== null && resp.restante !== undefined) ? parseFloat(resp.restante) : total;
    let cancelado = total - restante;

    $("#totalOrdenSel").text(`$${total.toFixed(2)}`);
    $("#pagadoOrdenSel").html(`$${cancelado.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(cancelado * tasaDolar).toFixed(2)}</small>`);
    $("#restanteOrdenSel").html(`$${restante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(restante * tasaDolar).toFixed(2)}</small>`);

    $("#infoTotalesOEP").removeClass("d-none");
    $("#btnAgregarOtroPagoModulo").removeClass("d-none");

    $("#contenedorDetallesPagoModulo").empty();
    if (resp.detalles && resp.detalles.length > 0) {
      resp.detalles.forEach(det => agregarFilaPago(det));
    } else {
      agregarFilaPago();
    }
    calcularRestante();

    if (resp.comprobantes && resp.comprobantes.length > 0) {
      let h = "";
      resp.comprobantes.forEach(c => {
        h += `
          <div class="col-4 text-center comp-card" id="comp-${c.id_comprobante_pago}">
            <div class="position-relative border rounded p-1">
              <a href="src/assets/fotosModulos/comprobantes_pagos/${c.path_comprobante}" target="_blank">
                <img src="src/assets/fotosModulos/comprobantes_pagos/${c.path_comprobante}" class="img-fluid rounded" style="max-height: 100px;">
              </a>
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btnEliminarComprobante" data-id="${c.id_comprobante_pago}">
                <i class="fi fi-rs-trash"></i>
              </button>
            </div>
          </div>
        `;
      });
      $("#contenedorComprobantesExistentes").html(h);
    }

    $(".modalRegistrar").modal("show");
  }
});

$(document).off("change", "#inputComprobantesPago").on("change", "#inputComprobantesPago", function () {
  let files = this.files ? this.files.length : 0;
  let existentes = $("#contenedorComprobantesExistentes .comp-card").length;
  if (existentes + files > 3) {
    Swal.fire('Atención', 'Solo puedes subir un máximo de 3 comprobantes por Orden', 'warning');
    this.value = '';
  }
});

$(document).off("click", ".btnEliminarComprobante").on("click", ".btnEliminarComprobante", function () {
  let id = $(this).data("id");
  Swal.fire({
    title: '¿Eliminar comprobante?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      let resp = await pedirDatosAjax({
        modulo: "pagos",
        datosPe: { accion: "eliminarComprobante", id_comprobante_pago: id }
      });
      if (resp && resp.icono === "success") {
        alertasAjax(resp);
        $(`#comp-${id}`).fadeOut(300, function() { $(this).remove(); });
      }
    }
  });
});

$(document).off("click", ".btnEliminar").on("click", ".btnEliminar", function () {
  let id = $(this).data("id");
  Swal.fire({
    title: '¿Estás seguro?',
    text: "El pago se anulará y el estado de la OEP podría cambiar. Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      let resp = await pedirDatosAjax({
        modulo: "pagos",
        datosPe: { accion: "eliminar", id_pago: id }
      });
      if (resp && resp.icono === "success") {
        alertasAjax(resp);
        reiniciarDataTables(dtPagos);
        reiniciarDataTables(dtOEPs);
      }
    }
  });
});

$(document).off("click", ".btnVerDetalle").on("click", ".btnVerDetalle", async function () {
  let idPago = $(this).data("id");
  let resp = await pedirDatosAjax({
    modulo: "pagos",
    noGuardarLocal: true,
    datosPe: { accion: "obtenerUno", id_pago: idPago }
  });

  if (resp && resp.id_pago) {
    let html = `
      <div class="row">
        <div class="col-md-6">
          <p><strong>CÓDIGO PAGO:</strong> <span class="badge bg-primary">${resp.id_pago}</span></p>
          <p><strong>FECHA:</strong> ${resp.fecha_pago}</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p><strong>OEP ASOCIADA:</strong> <span class="badge bg-dark">${resp.id_orden_entrega_presupuesto}</span></p>
        </div>
      </div>
      <hr>
      <h6 class="text-secondary"><i class="fi fi-rs-receipt me-2"></i>Detalles Monetarios</h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead class="table-light">
            <tr>
              <th>Método</th>
              <th>Moneda</th>
              <th class="text-end">Monto</th>
            </tr>
          </thead>
          <tbody>
    `;
    resp.detalles.forEach(d => {
      html += `
        <tr>
          <td>${d.nombre_metodo_pago}</td>
          <td>${d.simbolo_moneda} - ${d.nombre_moneda}</td>
          <td class="text-end">${parseFloat(d.monto_pago).toFixed(2)}</td>
        </tr>
      `;
    });
    html += `
          </tbody>
        </table>
      </div>
    `;

    if (resp.comprobantes && resp.comprobantes.length > 0) {
      html += `
        <h6 class="text-secondary mt-4"><i class="fi fi-rs-picture me-2"></i>Comprobantes</h6>
        <div class="row g-2">
      `;
      resp.comprobantes.forEach(c => {
        html += `
          <div class="col-sm-4 text-center">
            <a href="src/assets/fotosModulos/comprobantes_pagos/${c.path_comprobante}" target="_blank">
              <img src="src/assets/fotosModulos/comprobantes_pagos/${c.path_comprobante}" class="img-fluid img-thumbnail" style="max-height: 150px">
            </a>
          </div>
        `;
      });
      html += `</div>`;
    }

    $("#contenidoDetallePago").html(html);
    $(".modalDetallesPago").modal("show");
  }
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'pagos');
})

//#endregion [ EVENTOS DEL DOM Y EJECUCION ] FIN