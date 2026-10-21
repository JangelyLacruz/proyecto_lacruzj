// Importaciones
import {
  enviarFormulario, listarDataTable, pedirDatosAjax, reiniciarDataModuloSS, rutaAbsoluta, cargarPrecioDolar
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js";

//#region [ CONFIGURACIÓN DE LA AYUDA INTERACTIVA ] COMIENZO
driverAyuda('compras', {
  pasos: [
    {
      element: '#dashboardCompras',
      popover: {
        title: 'Dashboard de Recepciones',
        description: 'Aquí puedes ver métricas importantes: total de recepciones registradas, proveedores distintos y total de artículos recibidos.',
        side: 'bottom',
        align: 'start'
      }
    },
    {
      element: 'button[data-bs-target=".modalRegistrar"]',
      popover: {
        title: 'Registrar Recepción',
        description: 'Haz clic aquí para registrar una nueva recepción. Puedes agregar múltiples productos o materias primas en una misma recepción.',
        side: 'bottom',
        align: 'start'
      }
    },
    {
      element: '.tabla-ajax',
      popover: {
        title: 'Lista de Recepciones',
        description: 'Aquí puedes ver todas las recepciones registradas, con su fecha, proveedor y cantidad de artículos.',
        side: 'top'
      }
    },
    {
      element: '.botonVer',
      popover: {
        title: 'Ver Detalles',
        description: 'Haz clic aquí para ver todos los detalles de una recepción específica.',
        side: 'left'
      }
    },
    {
      element: '.botonEditar',
      popover: {
        title: 'Editar Recepción',
        description: 'Modifica los datos de una recepción existente.',
        side: 'left'
      }
    },
    {
      element: '.botonEliminar',
      popover: {
        title: 'Eliminar Recepción',
        description: 'Elimina una recepción del sistema. Esta acción no se puede deshacer.',
        side: 'left'
      }
    },
    {
      popover: {
        title: '¡Ayuda completada!',
        description: 'Ya conoces la gestión de recepciones. Puedes registrar recepciones con múltiples artículos y llevar el control de tu inventario.',
        side: 'top'
      }
    }
  ]
});
//#endregion [ CONFIGURACIÓN DE LA AYUDA INTERACTIVA ] FIN

// Variables globales
let proveedoresOptionsCache = '';
let itemsCache = {
  'materia_prima': null,
  'producto': null
};
let itemsCompra = []; // items temporales para la recepcion
let tasaDolar = 1.0; // Tasa de cambio oficial en pantalla (BCV / Dólar API)

// Funciones del modulo

// Obtener la tasa de cambio del dólar mostrada actualmente en la página
function obtenerTasaDolarEnPantalla() {
  // 1. Del texto mostrado en la barra superior del sistema (.precio_dolar)
  let textoHeader = $('.precio_dolar').first().text().trim().replace(/\s+/g, '').replace(',', '.');
  let tasa = parseFloat(textoHeader);
  if (!isNaN(tasa) && tasa > 1.0) {
    tasaDolar = tasa;
    return tasa;
  }

  // 2. Del caché de sesión 'infoDolarWS' guardado por global.js
  try {
    let ws = JSON.parse(sessionStorage.getItem('infoDolarWS') || '{}');
    if (ws && ws.precio) {
      let p = parseFloat(String(ws.precio).replace(',', '.'));
      if (!isNaN(p) && p > 1.0) {
        tasaDolar = p;
        return p;
      }
    }
  } catch (e) {}

  // 3. Del input oculto .precioDolar si estuviera presente
  let inputVal = parseFloat($('.precioDolar').val());
  if (!isNaN(inputVal) && inputVal > 1.0) {
    tasaDolar = inputVal;
    return inputVal;
  }

  return tasaDolar > 1.0 ? tasaDolar : 1.0;
}

// Cargar y asegurar la tasa de cambio mostrada en la página
async function cargarTasaDolar() {
  try {
    let tasa = obtenerTasaDolarEnPantalla();
    if (tasa <= 1.0 && typeof cargarPrecioDolar === 'function') {
      try {
        await cargarPrecioDolar();
        tasa = obtenerTasaDolarEnPantalla();
      } catch (err) {}
    }

    if (tasa <= 1.0) {
      try {
        let res = await pedirDatosAjax({
          modulo: 'compras',
          noGuardarLocal: true,
          datosPe: { accion: 'obtenerTasaDolar' }
        });
        if (res && res.tasa && parseFloat(res.tasa) > 1.0) {
          tasa = parseFloat(res.tasa);
        }
      } catch (err2) {}
    }

    if (tasa > 1.0) {
      tasaDolar = tasa;
    }

    // Recalcular filas abiertas y total general
    $('.fila-compra').each(function () {
      recalcularFila($(this));
    });
    actualizarGranTotalCompra();
  } catch (e) {
    console.error("Error al cargar tasa dólar:", e);
  }
}

// Recalcular subtotal y conversión de una fila específica
function recalcularFila(fila) {
  let tasaActual = obtenerTasaDolarEnPantalla();
  if (tasaActual > 1.0) tasaDolar = tasaActual;

  let cantidad = parseFloat(fila.find('.inputCantidadFila').val()) || 0;
  let precioInput = parseFloat(fila.find('.inputPrecioFila').val()) || 0;
  if (cantidad > 9999999) cantidad = 9999999;
  if (precioInput > 999999999.99) precioInput = 999999999.99;
  let moneda = fila.find('.inputMonedaFila').val() || 'BS';

  let precioBs = 0;
  let precioUsd = 0;
  let equivTexto = '';
  let tooltip = '';

  if (moneda === '$') {
    precioBs = precioInput * tasaDolar;
    let totalBs = precioBs * cantidad;

    if (precioInput > 0) {
      if (cantidad > 1) {
        equivTexto = `≈ ${precioBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs (Tot: ${totalBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs)`;
      } else {
        equivTexto = `≈ ${precioBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs`;
      }
    } else {
      equivTexto = `≈ 0.00 Bs`;
    }

    tooltip = `Precio en $: ${precioInput.toFixed(2)} $ | Equivalente: ${precioBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs (Tasa: ${tasaDolar.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs/$)`;
    fila.find('.texto-equiv-bs')
      .removeClass('text-primary')
      .addClass('text-success en-dolar')
      .text(equivTexto)
      .attr('title', tooltip);
  } else {
    precioBs = precioInput;
    precioUsd = tasaDolar > 0 ? (precioInput / tasaDolar) : 0;
    let totalUsd = precioUsd * cantidad;

    if (precioInput > 0) {
      if (cantidad > 1) {
        equivTexto = `≈ ${precioUsd.toFixed(2)} $ (Tot: ${totalUsd.toFixed(2)} $)`;
      } else {
        equivTexto = `≈ ${precioUsd.toFixed(2)} $`;
      }
    } else {
      equivTexto = `≈ 0.00 $`;
    }

    tooltip = `Precio en Bs: ${precioBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs | Equivalente: ${precioUsd.toFixed(2)} $ (Tasa: ${tasaDolar.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs/$)`;
    fila.find('.texto-equiv-bs')
      .removeClass('text-success en-dolar')
      .addClass('text-primary')
      .text(equivTexto)
      .attr('title', tooltip);
  }

  fila.find('.inputPrecioFila').attr('title', tooltip);
  fila.find('.inputPrecioBsFila').val(precioBs.toFixed(2));

  actualizarGranTotalCompra();
}

function actualizarGranTotalCompra() {
  let tasaActual = obtenerTasaDolarEnPantalla();
  if (tasaActual > 1.0) tasaDolar = tasaActual;

  let totalBs = 0;
  $('.fila-compra').each(function () {
    let cant = parseFloat($(this).find('.inputCantidadFila').val()) || 0;
    let pBs = parseFloat($(this).find('.inputPrecioBsFila').val()) || 0;
    if (cant > 9999999) cant = 9999999;
    if (pBs > 999999999.99) pBs = 999999999.99;
    totalBs += (cant * pBs);
  });

  if (!isFinite(totalBs) || totalBs > 999999999999) {
    totalBs = 999999999999;
  }

  let totalUsd = tasaDolar > 0 ? (totalBs / tasaDolar) : 0;
  let strBs = totalBs.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Bs';
  let strUsd = '(' + totalUsd.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' $)';

  $('#totalEstimadoCompraBs').text(strBs).attr('title', strBs);
  $('#totalEstimadoCompraUsd').text(strUsd).attr('title', strUsd);
}

// Carga inicial de proveedores para cachear las opciones
async function cargarOpcionesProveedores() {
  try {
    let proveedores = await pedirDatosAjax({
      modulo: 'proveedores',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    });

    let opciones = '<option value="">Seleccionar proveedor...</option>';

    if (Array.isArray(proveedores)) {
      if (proveedores.length === 0) {
        // alert("Advertencia: No hay proveedores registrados.");
      }
      proveedores.forEach(p => {
        // Soporte para mayúsculas/minúsculas y diferentes llaves posibles
        let rif = p.rif_proveedor || p.RIF_PROVEEDOR || p.id_proveedor;
        let razon = p.razon_social_proveedor || p.RAZON_SOCIAL_PROVEEDOR || p.nombre_proveedor;

        if (rif && razon) {
          opciones += `<option value="${rif}">${razon}</option>`;
        }
      });
    } else {
      console.error("Error: Proveedores no es array", proveedores);
      // alert("Error al cargar proveedores. Revise la consola.");
    }

    proveedoresOptionsCache = opciones;

    // Actualizar selects existentes (si el modal ya estaba abierto)
    $('.selectProveedorFila').html(opciones);

  } catch (e) {
    console.error("Error fatal en cargarOpcionesProveedores:", e);
    proveedoresOptionsCache = '<option value="">Error de conexión</option>';
  }
}

// Carga items por tipo y retorna las opciones HTML
async function obtenerOpcionesItems(tipo, seleccionado = null) {
  // Si no está en caché, buscar datos
  if (!itemsCache[tipo]) {
    let items;
    if (tipo === 'producto') {
      // Pedimos las PRESENTACIONES de productos (cada una tiene su propio id_presentacion_producto
      // que es el ID que se guarda en productos_compras)
      items = await pedirDatosAjax({
        modulo: 'compras',
        noGuardarLocal: true,
        datosPe: { accion: 'listarProductosParaCompra' }
      });
    } else {
      items = await pedirDatosAjax({
        modulo: 'materiasPrimas',
        noGuardarLocal: true,
        datosPe: { accion: 'listar' }
      });
    }
    // Guardar en caché el array de datos, validando que sea array
    itemsCache[tipo] = Array.isArray(items) ? items : [];
  }

  // Construir HTML usando los datos en caché
  let opcionesHtml = '<option value="">Seleccione artículo</option>';
  let items = itemsCache[tipo];

  if (items.length > 0) {
    items.forEach(item => {
      let id, nombre, idUnidadMedida, nombreUnidad;

      switch (tipo) {
        case 'producto':
          // id_presentacion_producto es exactamente el campo que guarda productos_compras
          id = item.id_presentacion_producto;
          nombre = item.nombre_producto;
          idUnidadMedida = item.id_unidad_medida;
          nombreUnidad = item.nombre_unidad_medida;
          break;
        case 'materia_prima':
          id = item.id_materia_prima;
          nombre = item.nombre_materia_prima;
          idUnidadMedida = item.id_unidad_medida;
          nombreUnidad = item.nombre_unidad_medida;
          break;
      }

      // Comparación laxa (==) por si string vs number
      let selectedAttr = (seleccionado && id == seleccionado) ? 'selected' : '';

      opcionesHtml += `<option value="${id}" 
                data-unidad-id="${idUnidadMedida}" 
                data-unidad-nombre="${nombreUnidad}" ${selectedAttr}>
                ${nombre}
            </option>`;
    });
  }

  return opcionesHtml;
}

// Agregar fila al grid de recepciones
async function agregarFila() {
  let index = $('.fila-compra').length;

  // Asegurar que hay proveedores cargados
  if (!proveedoresOptionsCache) {
    await cargarOpcionesProveedores();
  }

  // HTML de la fila adaptado a CSS Grid
  let filaHtml = [
    '<div class="grid-compras-row fila-compra">',
    '<div>',
    `<select class="form-select selectProveedorFila" name="proveedor_${index}">`,
    proveedoresOptionsCache,
    '</select>',
    '</div>',
    '<div>',
    '<select class="form-select selectTipoFila">',
    '<option value="materia_prima">Materia Prima</option>',
    '<option value="producto">Producto</option>',
    '</select>',
    '</div>',
    '<div>',
    '<select class="form-select selectArticuloFila">',
    '<option value="">Cargando...</option>',
    '</select>',
    '</div>',
    '<div>',
    '<input type="text" class="form-control inputUnidadFila"',
    ' disabled style="background-color:#f8f9fa;">',
    '<input type="hidden" class="inputUnidadIdFila">',
    '</div>',
    '<div>',
    '<input type="number" class="form-control inputCantidadFila"',
    ' step="0.01" min="0.01" value="1">',
    '</div>',
    '<div>',
    '<div class="input-group input-group-sm input-group-precio">',
    '<button type="button" class="btn btnMonedaFila" data-moneda="BS" title="Clic para alternar entre Bs y $">Bs</button>',
    '<input type="number" class="form-control inputPrecioFila text-end" step="0.01" min="0" placeholder="0.00" value="0.00">',
    '</div>',
    '<div class="texto-equiv-bs text-end" title="Conversión de moneda">≈ 0.00 $</div>',
    '<input type="hidden" class="inputMonedaFila" value="BS">',
    '<input type="hidden" class="inputPrecioBsFila" value="0">',
    '</div>',
    '<div class="text-center">',
    '<button type="button" class="btn-borrar btnEliminarFila" title="Eliminar línea">',
    '<span style="font-size:1.5rem;font-weight:bold;line-height:1;">\u00d7</span>',
    '</button>',
    '</div>',
    '</div>',
  ].join('');

  let nuevaFila = $(filaHtml);
  let contenedor = $('#contenedorItems');

  contenedor.append(nuevaFila);

  // Heredar el proveedor de la primera fila si ya hay alguno seleccionado, o del select superior
  let proveedorActual = $('.selectProveedorFila').not(nuevaFila.find('.selectProveedorFila')).first().val() || $('.selectProveedorAct').val();
  if (proveedorActual) {
    nuevaFila.find('.selectProveedorFila').val(proveedorActual);
  }

  let itemSelect = nuevaFila.find('.selectArticuloFila');
  let opciones = await obtenerOpcionesItems('materia_prima');
  itemSelect.html(opciones);

  actualizarContador();
  actualizarBloqueoProveedor();
  recalcularFila(nuevaFila);

  // Auto-scroll al fondo del contenedor correcto
  if (contenedor.length) {
    contenedor.scrollTop(contenedor[0].scrollHeight);
  }
}

// Renderizar items en edicion
async function actualizarTablaItems() {
  let contenedor = $('#contenedorItems');
  contenedor.empty();

  if (!itemsCompra || itemsCompra.length === 0) {
    agregarFila(); // Si no hay items, agregar una fila vacía
    return;
  }

  // Asegurar proveedores cargados
  if (!proveedoresOptionsCache) {
    await cargarOpcionesProveedores();
  }

  // Iterar y crear filas
  for (let i = 0; i < itemsCompra.length; i++) {
    let item = itemsCompra[i];
    let index = i;

    // Pre-seleccionar Proveedor
    let pId = String(item.proveedorId || '').trim();
    let provOptions = proveedoresOptionsCache.replace(`value="${pId}"`, `value="${pId}" selected`);
    let precioVal = parseFloat(item.precio || 0).toFixed(2);

    let filaHtml = `
            <div class="grid-compras-row fila-compra">
                <!-- Proveedor -->
                <div>
                    <select class="form-select selectProveedorFila" name="proveedor_${index}">
                        ${provOptions}
                    </select>
                </div>

                <!-- Tipo -->
                <div>
                    <select class="form-select selectTipoFila">
                        <option value="materia_prima" ${item.tipo === 'materia_prima' ? 'selected' : ''}>Materia Prima</option>
                        <option value="producto" ${item.tipo === 'producto' ? 'selected' : ''}>Producto</option>
                    </select>
                </div>
                
                <!-- Artículo -->
                <div>
                    <select class="form-select selectArticuloFila">
                        <!-- Se llenará dinámicamente -->
                        <option value="">Cargando...</option>
                    </select>
                </div>
                
                <!-- Unidad -->
                <div>
                    <input type="text" class="form-control inputUnidadFila" disabled 
                        style="background-color: #f8f9fa;" value="${item.nombre_unidad || ''}">
                    <input type="hidden" class="inputUnidadIdFila" value="${item.id_unidad_medida || ''}">
                </div>
                
                <!-- Cantidad -->
                <div>
                    <input type="number" class="form-control inputCantidadFila" step="0.01" min="0.01" value="${item.cantidad}">
                </div>

                <!-- Precio -->
                <div>
                    <div class="input-group input-group-sm input-group-precio">
                        <button type="button" class="btn btnMonedaFila" data-moneda="BS" title="Clic para alternar entre Bs y $">Bs</button>
                        <input type="number" class="form-control inputPrecioFila text-end" step="0.01" min="0" placeholder="0.00" value="${precioVal}">
                    </div>
                    <div class="texto-equiv-bs text-end" title="Conversión de moneda">≈ 0.00 $</div>
                    <input type="hidden" class="inputMonedaFila" value="BS">
                    <input type="hidden" class="inputPrecioBsFila" value="${precioVal}">
                </div>
                
                <!-- Acciones -->
                <div class="text-center">
                    <button type="button" class="btn-borrar btnEliminarFila" title="Eliminar línea">
                        <span style="font-size: 1.5rem; font-weight: bold; line-height: 1; margin-top: -3px;">&times;</span>
                    </button>
                </div>
            </div>
        `;

    let nuevaFila = $(filaHtml);
    contenedor.append(nuevaFila);

    // Setear proveedor asegurando que la opción exista
    if (pId) {
      let selectProv = nuevaFila.find('.selectProveedorFila');
      if (selectProv.find(`option[value="${pId}"]`).length === 0) {
        let nombreProv = item.proveedorNombre || pId;
        selectProv.append(`<option value="${pId}">${nombreProv}</option>`);
      }
      selectProv.val(pId);
    }

    // Cargar opciones del articulo
    let selectArticulo = nuevaFila.find('.selectArticuloFila');
    let aId = String(item.id || '').trim();

    // Pasamos el ID para que venga con 'selected' desde el principio
    let opciones = await obtenerOpcionesItems(item.tipo, aId);
    selectArticulo.html(opciones);

    recalcularFila(nuevaFila);
  }

  actualizarContador();
  actualizarGranTotalCompra();

  // Auto-scroll al fondo
  let scrollContainer = $('#contenedorItems');
  if (scrollContainer.length) {
    scrollContainer.scrollTop(scrollContainer[0].scrollHeight);
  }
}

function actualizarContador() {
  let count = $('.fila-compra').length;
  $('#contadorFilas').text(`Items: ${count}`);
}

// Bloquea/Desbloquea el select de proveedor según la cantidad de filas.
// Con 2+ filas el proveedor queda fijo; con 1 fila se puede cambiar.
function actualizarBloqueoProveedor() {
  let filas = $('.fila-compra').length;
  if (filas > 1) {
    // Bloquear todos los selects de proveedor
    $('.selectProveedorFila').prop('disabled', true)
      .css({ 'background-color': '#f8f9fa', 'cursor': 'not-allowed', 'opacity': '0.8' });
  } else {
    // Desbloquear cuando queda solo una fila
    $('.selectProveedorFila').prop('disabled', false)
      .css({ 'background-color': '', 'cursor': '', 'opacity': '' });
  }
}

// Sin eventos directos aqui

async function cargarDatosCompra(id, modo = 'editar') {
  try {
    let respuesta = await pedirDatosAjax({
      modulo: 'compras',
      noGuardarLocal: true,
      datosPe: { accion: 'seleccionarUno', id_compra: id }
    });

    if (!respuesta || respuesta.length === 0) {
      Swal.fire('Error', 'No se pudieron cargar los datos de la recepción', 'error');
      return;
    }

    if (modo === 'editar') {
      let modal = $('.modalRegistrar');
      let form = modal.find('form');
      let header = respuesta[0];

      modal.find('.modal-title').html(
        '<i class="fas fa-edit me-2"></i> Actualizar Recepción'
      );
      form.find('input[name="accion"]').val('actualizar');
      form.find('input[name="id_compra"]').val(id);
      form.find('.textoSubmit').text('Actualizar Todo');
      $('#btnLimpiarFormulario').hide();

      let fechaFormat = header.fecha_compra.replace(' ', 'T').substring(0, 16);
      form.find('input[name="fecha_compra"]').val(fechaFormat);

      modal.find('.modo-edicion').removeClass('d-none').addClass('d-flex');
      await cargarOpcionesProveedores();

      let rifTarget = String(header.rif_proveedor || '').trim();
      let provNombre = header.PROVEEDOR || rifTarget;
      if (rifTarget && !proveedoresOptionsCache.includes(`value="${rifTarget}"`)) {
        proveedoresOptionsCache += `<option value="${rifTarget}">${provNombre}</option>`;
      }

      modal.find('.selectProveedorAct')
        .prop('disabled', false)
        .html(proveedoresOptionsCache)
        .val(rifTarget);

      itemsCompra = [];
      respuesta.forEach(item => {
        itemsCompra.push({
          proveedorId: item.rif_proveedor || rifTarget,
          proveedorNombre: item.PROVEEDOR || provNombre,
          tipo: item.TIPO.toLowerCase(),
          id: item.id_item,
          nombre: item.ARTICULO,
          cantidad: parseFloat(item.cantidad_raw),
          precio: parseFloat(item.precio_unitario || 0),
          id_unidad_medida: item.id_unidad_medida,
          nombre_unidad: item.nombre_unidad_medida || 'UNID'
        });
      });

      await cargarTasaDolar();
      await actualizarTablaItems();

      // En modo edición asegurar que los selects de proveedor de cada fila tengan el RIF seleccionado
      if (rifTarget) {
        $('.selectProveedorFila').each(function () {
          if ($(this).find(`option[value="${rifTarget}"]`).length === 0) {
            $(this).append(`<option value="${rifTarget}">${provNombre}</option>`);
          }
          $(this).val(rifTarget);
        });
      }

      // En modo edición se pueden cambiar tipo y artículo de cada fila
      // y agregar/eliminar filas igual que en registro
      $('.selectTipoFila, .selectArticuloFila').prop('disabled', false);
      $('.btnEliminarFila').show();
      $('#btnAgregarFila').show();

      modal.modal('show');
    }

  } catch (error) {
    console.error(error);
    Swal.fire('Error', 'Hubo un problema al cargar la recepción', 'error');
  }
}

function resetearFormularioCompra() {
  let modal = $('.modalRegistrar');
  let form = modal.find('form');

  modal.find('.modal-title').html(
    '<i class="fas fa-shopping-cart me-2"></i> Registrar Recepción'
  );
  form.find('input[name="accion"]').val('registrar');
  form.find('input[name="id_compra"]').val('');
  form.find('.textoSubmit').text('Guardar Todo');
  $('#btnLimpiarFormulario').show();

  modal.find('.modo-edicion').addClass('d-none').removeClass('d-flex');

  form.find('input, select, button').prop('disabled', false);
  $('#btnAgregarFila').prop('disabled', false).show();

  itemsCompra = [];
  $('#contenedorItems').empty();

  form[0].reset();

  let now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  form.find('input[name="fecha_compra"]').val(now.toISOString().slice(0, 16));

  cargarTasaDolar();
  agregarFila();
}

async function verDetallesCompra(idCompra) {
  try {
    let respuesta = await pedirDatosAjax({
      modulo: 'compras',
      noGuardarLocal: true,
      datosPe: { accion: 'seleccionarUno', id_compra: idCompra }
    });

    if (!respuesta || respuesta.length === 0) {
      Swal.fire('Error', 'No se encontraron datos de la recepción', 'error');
      return;
    }

    let header = respuesta[0];

    $('#verIdCompra').text(header.id_compra || '-');
    $('#verProveedorCompra').text(header.PROVEEDOR || '-');
    $('#verNroFacturaCompra').text(header.nro_factura || '-');
    $('#verNroControlCompra').text(header.nro_control || '-');

    let fechaFormat = header.fecha_compra ?
      new Date(header.fecha_compra.replace(' ', 'T')).toLocaleString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
      }) : '-';
    $('#verFechaCompra').text(fechaFormat);

    let st = parseInt(header.status || 1);
    if (st === 2 || header.estado_compra === 'Recibido') {
      $('#verEstadoCompra').html('<span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Recibido</span>');
    } else {
      $('#verEstadoCompra').html('<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i> Pendiente por recibir</span>');
    }

    $('#btnImprimirModalVer').data('id', header.id_compra);
    $('#btnPlanillaRetencionModalVer').data('id', header.id_compra);

    let tbody = $('#verItemsBody');
    tbody.empty();

    let totalBaseBs = 0;
    respuesta.forEach(item => {
      let cant = parseFloat(item.cantidad_raw) || 0;
      let precio = parseFloat(item.precio_unitario) || 0;
      let subtotal = cant * precio;
      totalBaseBs += subtotal;

      let tipoLabel = item.TIPO === 'materia_prima' ? 'Materia Prima' : 'Producto';
      let inicial = (item.PROVEEDOR || '-').charAt(0).toUpperCase();

      let row = `
        <div class="grid-compras-ver-row" style="background-color: white;">
            <div>
                <div class="d-flex align-items-center gap-2 h-100 px-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
                         style="width:26px;height:26px;font-size:.7rem;background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                        ${inicial}
                    </div>
                    <span style="font-size:.82rem;">${item.PROVEEDOR || '-'}</span>
                </div>
            </div>
            <div>
                <span class="badge rounded-pill px-2 py-1"
                      style="font-size:.75rem;background:${tipoLabel === 'Producto' ? 'linear-gradient(135deg,#10b981,#34d399)' : 'linear-gradient(135deg,#8b5cf6,#a78bfa)'}">
                    ${tipoLabel}
                </span>
            </div>
            <div style="font-size:.85rem;font-weight:500;">${item.ARTICULO || '-'}</div>
            <div>
                <span class="badge bg-light text-dark border" style="font-size:.8rem;">
                    ${item.nombre_unidad_medida || 'UNID'}
                </span>
            </div>
            <div>
                <span class="fw-bold text-dark" style="font-size:.9rem;">${cant.toFixed(2)}</span>
            </div>
            <div class="text-end pe-2">
                <span class="fw-bold text-primary" style="font-size:.85rem;">${precio.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs</span>
                <span class="text-muted d-block small" style="font-size:.72rem;">≈ ${(tasaDolar > 0 ? (precio / tasaDolar).toFixed(2) : '0.00')} $</span>
            </div>
        </div>
      `;
      tbody.append(row);
    });

    let modal = new bootstrap.Modal($('#modalVerCompra')[0]);
    modal.show();

  } catch (error) {
    console.error('Error al cargar detalles de recepción:', error);
    Swal.fire('Error', 'Hubo un problema al cargar los detalles de la recepción', 'error');
  }
}
// Fin de funciones

// Eventos de la vista
$(async function () {
  sessionStorage.removeItem("cachingModulos");
  const permisos = await pedirDatosAjax({
    modulo: 'accesos',
    noGuardarLocal: true,
    datosPe: {
      accion: 'listarPorRol'
    }
  });

  await listarDataTable({
    encabezados: {
      "id_compra": "# RECEPCIÓN",
      "fecha_compra": "FECHA",
      "PROVEEDOR": "PROVEEDOR",
      "total_articulos": "ARTÍCULOS",
      "estado_compra": "ESTADO"
    },
    informacionPe: {
      'modulo': 'compras',
      'datosPe': { 'accion': 'listar' }
    },
    campoIdBtn: 'id_compra',
    infoTratoEspecial: {
      total_articulos: (info) => {
        let n = parseInt(info.valor) || 0;
        if (n === 0) return `<span class="badge bg-secondary rounded-pill px-2">Sin artículos</span>`;
        return `
        <span class="badge rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1"
              style="background:linear-gradient(135deg,#4e54c8,#8f94fb);font-size:.8rem;">
            <i class="fi fi-rr-box" style="font-size:.85rem;margin-top:1px;"></i>
            ${n} artículo${n !== 1 ? 's' : ''}
        </span>`;
      },
      fecha_compra: (info) => {
        if (!info.valor) return '-';
        let d = new Date(info.valor.replace(' ', 'T'));
        let fechaTexto = d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        let horaTexto = d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        return `
                    <div class="d-flex flex-column align-items-center lh-sm">
                        <span class="fw-semibold" style="font-size:.85rem;">${fechaTexto}</span>
                        <span class="text-muted d-flex align-items-center mt-1" style="font-size:.75rem;">
                            <i class="fi fi-rr-clock me-1"></i>${horaTexto}
                        </span>
                    </div>`;
      },
      id_compra: (info) => {
        return `<span class="badge bg-light text-dark border fw-bold px-2" style="font-size:.85rem;">#${info.valor}</span>`;
      },
      PROVEEDOR: (info) => {
        if (!info.valor) return '-';
        let inicial = info.valor.charAt(0).toUpperCase();
        return `
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
                             style="width:28px;height:28px;font-size:.75rem;background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                            ${inicial}
                        </div>
                        <span style="font-size:.85rem;">${info.valor}</span>
                    </div>`;
      },
      estado_compra: (info) => {
        let st = parseInt(info.fila ? info.fila.status : (info.valor === 'Recibido' ? 2 : 1));
        if (st === 2 || info.valor === 'Recibido') {
          return `<span class="badge rounded-pill px-3 py-2 text-bg-success d-inline-flex align-items-center gap-1 shadow-sm" style="font-size:.8rem;">
                    <i class="fas fa-check-circle"></i> Recibido
                  </span>`;
        }
        return `<span class="badge rounded-pill px-3 py-2 text-bg-warning text-dark d-inline-flex align-items-center gap-1 shadow-sm" style="font-size:.8rem;">
                  <i class="fas fa-clock"></i> Pendiente por recibir
                </span>`;
      }
    },
    botones: function (info) {
      let id = info['fila']['id_compra'];
      let st = parseInt(info['fila']['status'] || 1);
      let boton = '<ul class="list-inline me-auto mb-0">';

      if (permisos['compras']) {
        // Solo las recepciones en estado Pendiente (status == 1) se pueden recepcionar o editar
        if (st === 1) {
          if (permisos['compras'].includes('actualizar')) {
            boton += `
              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Declarar como Recibido">
                  <a href="#" value="${id}" class="botonRecepcionar avtar avtar-xs btn-link-primary btn-pc-default">
                      <i class="fi fi-rs-box-alt fs-3 iconoCentrado text-primary"></i>
                  </a>
              </li>`;
            boton += `
              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                  <a href="#" value="${id}" class="botonEditar avtar avtar-xs btn-link-success btn-pc-default">
                      <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
                  </a>
              </li>`;
          }
        }

        if (permisos['compras'].includes('eliminar')) {
          boton += `
            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                <a href="#" value="${id}" class="botonEliminar avtar avtar-xs btn-link-danger btn-pc-default">
                    <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
                </a>
            </li>`;
        }
      }

      // Imprimir orden de recepcion
      boton += `
            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Imprimir Orden de Recepción">
                <a href="#" value="${id}" class="botonImprimir avtar avtar-xs btn-link-secondary btn-pc-default">
                    <i class="fi fi-rs-print fs-3 iconoCentrado"></i>
                </a>
            </li>`;

      // Imprimir planilla de retención SENIAT
      boton += `
            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Planilla de Retención SENIAT">
                <a href="#" value="${id}" class="botonPlanillaRetencion avtar avtar-xs btn-link-success btn-pc-default">
                    <i class="fi fi-rs-document-signed fs-3 iconoCentrado text-success"></i>
                </a>
            </li>`;

      boton += `
            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver Detalles">
                <a href="#" value="${id}" class="botonVer avtar avtar-xs btn-link-info btn-pc-default">
                    <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
                </a>
            </li>`;
      boton += '</ul>';
      return boton;
    }
  });

  cargarOpcionesProveedores();
  cargarTasaDolar();
});

// Eventos del Modal
$(document).on('show.bs.modal', '.modalRegistrar', async function () {
  let form = $(this).find('form');
  let accion = form.find('input[name="accion"]').val();
  await cargarTasaDolar();

  if (accion !== 'actualizar') {
    itemsCache = { 'materia_prima': null, 'producto': null };
    $('#contenedorItems').empty();
    await cargarOpcionesProveedores();
    await obtenerOpcionesItems('materia_prima');
    await obtenerOpcionesItems('producto');
    agregarFila();
  }
});

$(document).on('hidden.bs.modal', '.modalRegistrar', function () {
  resetearFormularioCompra();
});

// Dashboard de estadisticas
$(document).on('draw.dt', '.tabla-ajax', function () {
  let datosTabla = $(this).DataTable().rows().data().toArray();
  let totalCompras = datosTabla.length;
  let totalArticulos = datosTabla.reduce((acc, c) => acc + (parseInt(c.total_articulos) || 0), 0);
  let proveedoresUnicos = [...new Set(datosTabla.map(c => c.rif_proveedor).filter(Boolean))].length;

  $('#dashboardCompras').remove(); // evitar duplicados

  let tarjetas = [
    {
      label: 'Total Recepciones',
      valor: totalCompras,
      sub: 'registradas',
      icono: 'fi-rr-shopping-cart',
      color1: '#4e54c8', color2: '#8f94fb'
    },
    {
      label: 'Proveedores',
      valor: proveedoresUnicos,
      sub: 'distintos en lista actual',
      icono: 'fi-rr-building',
      color1: '#f59e0b', color2: '#fbbf24'
    },
    {
      label: 'Total Artículos',
      valor: totalArticulos,
      sub: 'productos + materias primas',
      icono: 'fi-rr-boxes',
      color1: '#10b981', color2: '#34d399'
    }
  ];

  let cols = tarjetas.map(t => `
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100"
                 style="border-left:4px solid ${t.color1}!important;border-radius:12px;overflow:hidden;">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px;height:52px;background:linear-gradient(135deg,${t.color1},${t.color2});">
                        <i class="fi ${t.icono} text-white" style="font-size:1.4rem;line-height:1;margin-top:2px;"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase"
                             style="letter-spacing:.6px;font-size:.72rem;">${t.label}</div>
                        <div class="fw-bold" style="font-size:1.8rem;line-height:1.1;color:${t.color1};">${t.valor}</div>
                        <div class="text-muted" style="font-size:.75rem;">${t.sub}</div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

  let dashHTML = `<div class="row mb-4 g-3" id="dashboardCompras">${cols}</div>`;
  $(this).closest('.card').before(dashHTML);
});

$(document).on('click', '#btnAgregarFila, #btnAgregarFilaAct', function () {
  agregarFila();
});

$(document).on('click', '#btnLimpiarFormulario', function () {
  resetearFormularioCompra();
});

$(document).on('change', '.selectTipoFila', async function () {
  let fila = $(this).closest('.fila-compra');
  let tipo = $(this).val();
  let selectArticulo = fila.find('.selectArticuloFila');
  let inputUnidad = fila.find('.inputUnidadFila');
  let inputUnidadId = fila.find('.inputUnidadIdFila');

  let opciones = await obtenerOpcionesItems(tipo);
  selectArticulo.html(opciones);

  inputUnidad.val('');
  inputUnidadId.val('');
});

$(document).on('change', '.selectArticuloFila', function () {
  let fila = $(this).closest('.fila-compra');
  let selectedOption = $(this).find('option:selected');

  let unidadId = selectedOption.data('unidad-id');
  let unidadNombre = selectedOption.data('unidad-nombre');

  fila.find('.inputUnidadFila').val(unidadNombre || '');
  fila.find('.inputUnidadIdFila').val(unidadId || '');
});

$(document).on('click', '.btnEliminarFila', function () {
  let filas = $('.fila-compra').length;
  if (filas > 1) {
    $(this).closest('.fila-compra').remove();
    actualizarContador();
    actualizarBloqueoProveedor();
    recalcularTotalesGenerales();
  } else {
    let fila = $(this).closest('.fila-compra');
    fila.find('select').each(function () {
      if ($(this).hasClass('selectTipoFila')) {
        $(this).val('materia_prima');
      } else {
        $(this).val('');
      }
    });

    fila.find('.selectTipoFila').trigger('change');

    fila.find('.inputUnidadFila').val('');
    fila.find('.inputUnidadIdFila').val('');
    fila.find('.inputCantidadFila').val('1');
    fila.find('.inputPrecioFila').val('0.00');
    recalcularFila(fila);
    // Con solo 1 fila, habilitar proveedor
    actualizarBloqueoProveedor();
  }
});

// Enviar formulario y recolectar datos de la tabla
$(document).on('submit', '.formularioAjax', async function (e) {
  e.preventDefault();

  let detalles = [];
  let itemsInvalidos = 0;
  let provCabecera = $('#selectProveedorEdicion').val() || $('.selectProveedorAct').val();
  let tasaActual = obtenerTasaDolarEnPantalla();
  if (tasaActual > 1.0) tasaDolar = tasaActual;

  $('.fila-compra').each(function () {
    let fila = $(this);
    let proveedorId = fila.find('.selectProveedorFila').val() || provCabecera;
    let tipo = fila.find('.selectTipoFila').val();
    let itemId = fila.find('.selectArticuloFila').val();
    let itemNombre = fila.find('.selectArticuloFila option:selected').text();
    let unidadId = fila.find('.inputUnidadIdFila').val();
    let cantidad = parseFloat(fila.find('.inputCantidadFila').val()) || 0;
    
    // Obtener precio y moneda directamente de los inputs
    let precioInput = parseFloat(fila.find('.inputPrecioFila').val()) || 0;
    let moneda = fila.find('.inputMonedaFila').val() || 'BS';

    let precioBs = (moneda === '$') ? (precioInput * tasaDolar) : precioInput;
    precioBs = Math.round(precioBs * 100) / 100;
    fila.find('.inputPrecioBsFila').val(precioBs.toFixed(2));

    if (!itemId) {
      itemsInvalidos++;
      return;
    }

    if (!proveedorId) {
      itemsInvalidos++;
      return;
    }

    if (cantidad <= 0) {
      itemsInvalidos++;
      return;
    }

    detalles.push({
      proveedorId: proveedorId,
      tipo: tipo,
      id: itemId,
      nombre: itemNombre,
      cantidad: cantidad,
      precio: precioBs,
      id_unidad_medida: unidadId
    });
  });

  if (itemsInvalidos > 0 && detalles.length === 0) {
    Swal.fire('Error', 'Verifique que todos los item tengan proveedor, artículo y cantidad válida.', 'warning');
    return;
  }

  if (detalles.length === 0) {
    Swal.fire('Error', 'Debe agregar al menos un artículo válido', 'warning');
    return;
  }

  $(this).find('input[name="rif_proveedor"]').remove();
  $(this).find('input[name="detalles"]').remove();

  $('<input>', {
    type: 'hidden',
    name: 'rif_proveedor',
    value: detalles[0].proveedorId
  }).appendTo(this);

  $('<input>', {
    type: 'hidden',
    name: 'detalles',
    value: JSON.stringify(detalles)
  }).appendTo(this);

  let respuesta = await enviarFormulario({
    'formulario': this,
    'modulo': 'compras'
  });

  // Limpiar grid si el registro fue exitoso
  if (respuesta && respuesta.icono === 'success') {
    $('#contenedorItems').empty();
    agregarFila(); // Agregar una fila vacía para la siguiente carga
    actualizarGranTotalCompra();
  }
});
// Evento para cargar datos en el modal de registro (Edición)
$(document).on("click", ".botonEditar", function (e) {
  e.preventDefault();
  let id = $(this).attr("value"); // El ID viene en el atributo value del botón
  cargarDatosCompra(id, 'editar');
});

// Evento para ver datos (Modal dedicado de solo lectura)
$(document).on("click", ".botonVer", function (e) {
  e.preventDefault();
  let id = $(this).attr("value");
  verDetallesCompra(id);
});

// Evento para eliminar recepcion (con confirmación)
$(document).on("click", ".botonEliminar", function (e) {
  e.preventDefault();
  let id = $(this).attr("value");

  Swal.fire({
    title: '¿Eliminar Recepción #' + id + '?',
    text: 'Esta acción desactivará la recepción y sus artículos. No se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      let respuesta = await pedirDatosAjax({
        modulo: 'compras',
        noGuardarLocal: true,
        datosPe: { accion: 'eliminar', id_compra: id }
      });
      if (respuesta && respuesta.icono === 'success') {
        Swal.fire('Eliminado', respuesta.texto || 'Recepción eliminada correctamente.', 'success');
        reiniciarDataModuloSS('compras');
        $('.tabla-ajax').DataTable().ajax.reload(null, false);
      } else {
        Swal.fire('Error', (respuesta && respuesta.texto) || 'No se pudo eliminar la recepción.', 'error');
      }
    }
  });
});

// Restaurar estado al cerrar el modal (para que el próximo "Registrar" esté limpio)
$('.modalRegistrar').on('hidden.bs.modal', function () {
  resetearFormularioCompra();
});

// Validación en tiempo real: campo cantidad de cada fila
$(document).on('input keyup paste change', '.inputCantidadFila', function () {
  let val = parseFloat($(this).val());
  if (val > 9999999) {
    val = 9999999;
    $(this).val(val);
  }
  let invalido = isNaN(val) || val <= 0;
  $(this).toggleClass('is-invalid', invalido);
  if (invalido) {
    $(this).next('.invalid-feedback').remove();
    $(this).after(
      '<div class="invalid-feedback">La cantidad debe ser mayor a 0</div>'
    );
  } else {
    $(this).next('.invalid-feedback').remove();
  }
  recalcularFila($(this).closest('.fila-compra'));
});

// Validación y recálculo en tiempo real: campo precio de cada fila
$(document).on('input keyup paste change', '.inputPrecioFila', function () {
  let val = parseFloat($(this).val());
  if (val > 999999999.99) {
    val = 999999999.99;
    $(this).val(val);
  }
  let invalido = isNaN(val) || val < 0;
  $(this).toggleClass('is-invalid', invalido);
  recalcularFila($(this).closest('.fila-compra'));
});

// Alternar moneda entre Bolívares y Dólares con conversión automática
$(document).on('click', '.btnMonedaFila', function (e) {
  e.preventDefault();
  let btn = $(this);
  let fila = btn.closest('.fila-compra');
  let inputMoneda = fila.find('.inputMonedaFila');
  let monedaActual = inputMoneda.val() || 'BS';

  let inputPrecio = fila.find('.inputPrecioFila');
  let precioActual = parseFloat(inputPrecio.val()) || 0;

  // Asegurar que usamos la tasa en pantalla más reciente
  let tasaActual = obtenerTasaDolarEnPantalla();
  if (tasaActual > 1.0) tasaDolar = tasaActual;

  if (monedaActual === 'BS') {
    // Pasar a $
    inputMoneda.val('$');
    btn.addClass('moneda-dolar')
       .attr('data-moneda', '$')
       .text('$');
    if (precioActual > 0 && tasaDolar > 0) {
      inputPrecio.val((precioActual / tasaDolar).toFixed(2));
    }
  } else {
    // Pasar a BS
    inputMoneda.val('BS');
    btn.removeClass('moneda-dolar')
       .attr('data-moneda', 'BS')
       .text('Bs');
    if (precioActual > 0 && tasaDolar > 0) {
      inputPrecio.val((precioActual * tasaDolar).toFixed(2));
    }
  }

  recalcularFila(fila);
});

// Sincronización y validación del proveedor en cabecera y filas
$(document).on('change', '.selectProveedorAct', function () {
  let pVal = $(this).val();
  let pText = $(this).find('option:selected').text();
  if (pVal) {
    $('.selectProveedorFila').each(function () {
      if ($(this).find(`option[value="${pVal}"]`).length === 0) {
        $(this).append(`<option value="${pVal}">${pText}</option>`);
      }
      $(this).val(pVal).removeClass('is-invalid');
    });
  }
});

// Validación en tiempo real: proveedor requerido y sincronización
$(document).on('change', '.selectProveedorFila', function () {
  let pVal = $(this).val();
  let pText = $(this).find('option:selected').text();
  let invalido = !pVal;
  $(this).toggleClass('is-invalid', invalido);
  if (pVal) {
    let act = $('.selectProveedorAct');
    if (act.find(`option[value="${pVal}"]`).length === 0) {
      act.append(`<option value="${pVal}">${pText}</option>`);
    }
    act.val(pVal);
    // Si hay más de una fila o para mantener coherencia en la compra
    $('.selectProveedorFila').each(function () {
      if (!$(this).val()) {
        $(this).val(pVal);
      }
    });
  }
});

// Validación en tiempo real: artículo requerido
$(document).on('change', '.selectArticuloFila', function () {
  let invalido = !$(this).val();
  $(this).toggleClass('is-invalid', invalido);
});

// Evento para recepcionar (declarar como recibido e incrementar inventario)
$(document).on("click", ".botonRecepcionar", function (e) {
  e.preventDefault();
  let id = $(this).attr("value");

  Swal.fire({
    title: '¿Declarar mercancía como Recibida?',
    text: `Al procesar la recepción #${id}, se sumará el stock de sus artículos al inventario y la recepción quedará bloqueada para futuras modificaciones.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, recepcionar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      let respuesta = await pedirDatosAjax({
        modulo: 'compras',
        noGuardarLocal: true,
        datosPe: { accion: 'recepcionar', id_compra: id }
      });
      if (respuesta && respuesta.icono === 'success') {
        Swal.fire('¡Recepcionada!', respuesta.texto || 'Mercancía recibida e inventario actualizado correctamente.', 'success');
        reiniciarDataModuloSS('compras');
        reiniciarDataModuloSS('inventario');
        $('.tabla-ajax').DataTable().ajax.reload(null, false);
      } else {
        Swal.fire('Error', (respuesta && respuesta.texto) || 'No se pudo procesar la recepción.', 'error');
      }
    }
  });
});

// Evento para imprimir orden de recepcion desde tabla
$(document).on("click", ".botonImprimir", function (e) {
  e.preventDefault();
  let id = $(this).attr("value");
  imprimirOrden(id);
});

// Evento para imprimir orden de recepcion desde modal de detalles
$(document).on("click", "#btnImprimirModalVer", function (e) {
  e.preventDefault();
  let id = $(this).data('id');
  if (id) {
    imprimirOrden(id);
  }
});

// Evento para imprimir planilla de retención desde tabla
$(document).on("click", ".botonPlanillaRetencion", function (e) {
  e.preventDefault();
  let id = $(this).attr("value");
  imprimirPlanillaRetencion(id);
});

// Evento para imprimir planilla de retención desde modal de detalles
$(document).on("click", "#btnPlanillaRetencionModalVer", function (e) {
  e.preventDefault();
  let id = $(this).data('id');
  if (id) {
    imprimirPlanillaRetencion(id);
  }
});

async function imprimirOrden(id) {
  try {
    let formData = new FormData();
    formData.append('accion', 'imprimir');
    formData.append('id_compra', id);

    const response = await fetch('?modulo=compras', {
      method: 'POST',
      body: formData,
      headers: {
        'X-TOKEN-CSRF': $('meta[name="TOKEN_CSRF"]').attr("content")
      }
    });

    const clonada = response.clone();
    try {
      const res = await response.json();
      if (res && (res.tipo || res.icono)) {
        Swal.fire({
          icon: res.icono || 'error',
          title: res.titulo || 'Error',
          text: res.texto || 'No se pudo generar la orden de recepción.'
        });
        return;
      }
    } catch (e) {}

    let blob = await clonada.blob();
    let url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');
    setTimeout(() => window.URL.revokeObjectURL(url), 1000);
  } catch (error) {
    console.error('Error al generar la orden de recepción:', error);
    Swal.fire('Error', 'No se pudo generar la orden de recepción.', 'error');
  }
}

async function imprimirPlanillaRetencion(id) {
  try {
    let formData = new FormData();
    formData.append('accion', 'imprimirPlanillaRetencion');
    formData.append('id_compra', id);

    const response = await fetch('?modulo=compras', {
      method: 'POST',
      body: formData,
      headers: {
        'X-TOKEN-CSRF': $('meta[name="TOKEN_CSRF"]').attr("content")
      }
    });

    const clonada = response.clone();
    try {
      const res = await response.json();
      if (res && (res.tipo || res.icono)) {
        Swal.fire({
          icon: res.icono || 'error',
          title: res.titulo || 'Error',
          text: res.texto || 'No se pudo generar la planilla de retención.'
        });
        return;
      }
    } catch (e) {}

    let blob = await clonada.blob();
    let url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');
    setTimeout(() => window.URL.revokeObjectURL(url), 1000);
  } catch (error) {
    console.error('Error al generar la planilla de retención:', error);
    Swal.fire('Error', 'No se pudo generar la planilla de retención.', 'error');
  }
}

// Evento para abrir modal de registro rápido de proveedor
$(document).on("click", "#btnModalNuevoProveedor", function (e) {
  e.preventDefault();
  e.stopPropagation();

  // Limpiar campos anteriores
  let form = $('#formRegistrarProveedorRapido');
  if (form.length) {
    form[0].reset();
    form.find('.is-invalid, .error').removeClass('is-invalid error');
  }

  let modalEl = document.getElementById('modalRegistrarProveedorRapido');
  let modalProv = bootstrap.Modal.getOrCreateInstance(modalEl);
  modalProv.show();
});

// Cerrar específicamente el modal de proveedor rápido sin tocar el de compras
$(document).on("click", "#btnCerrarProveedorRapido, #btnCancelarProveedorRapido", function (e) {
  e.preventDefault();
  e.stopPropagation();
  let modalEl = document.getElementById('modalRegistrarProveedorRapido');
  let modalProv = bootstrap.Modal.getInstance(modalEl);
  if (modalProv) {
    modalProv.hide();
  } else {
    $('#modalRegistrarProveedorRapido').modal('hide');
  }
});

// Manejo de modal apilado para que aparezca por encima de modalRegistrar
$(document).on('show.bs.modal', '#modalRegistrarProveedorRapido', function () {
  $(this).css('z-index', 1065);
  setTimeout(function () {
    $('.modal-backdrop').not('.modal-stack').last().css('z-index', 1060).addClass('modal-stack modal-backdrop-proveedor');
  }, 10);
});

// Al cerrarse el modal de proveedor rápido, asegurar que modalRegistrar siga abierto y activo
$(document).on('hidden.bs.modal', '#modalRegistrarProveedorRapido', function () {
  $('.modal-backdrop-proveedor').remove();

  if ($('.modalRegistrar').hasClass('show') || $('.modalRegistrar').is(':visible')) {
    $('body').addClass('modal-open');
    $('.modalRegistrar').css('overflow-y', 'auto').focus();
    if ($('.modal-backdrop').length === 0) {
      $('<div class="modal-backdrop fade show"></div>').appendTo('body');
    }
  }
});

// Evento para enviar registro rápido de proveedor
$(document).on("submit", "#formRegistrarProveedorRapido", async function (e) {
  e.preventDefault();
  let form = this;
  let rifPrefijo = $(form).find('select[name="codigo_rif_proveedor"]').val();
  let rifNum = $(form).find('input[name="rif_proveedor"]').val().trim();
  let razon = $(form).find('input[name="razon_social_proveedor"]').val().trim();
  let telPrefijo = $(form).find('select[name="prefijo_telefono_proveedor"]').val();
  let telNum = $(form).find('input[name="telefono_proveedor"]').val().trim();
  let correo = $(form).find('input[name="correo_proveedor"]').val().trim();
  let direccion = $(form).find('textarea[name="direccion_proveedor"]').val().trim();

  if (!rifNum || !razon || !telNum || !correo || !direccion) {
    Swal.fire({
      icon: 'warning',
      title: 'Campos requeridos',
      text: 'Por favor complete todos los campos obligatorios del proveedor.',
      target: document.getElementById('modalRegistrarProveedorRapido')
    });
    return;
  }

  let respuesta = await pedirDatosAjax({
    modulo: 'proveedores',
    noGuardarLocal: true,
    datosPe: {
      accion: 'registrar',
      codigo_rif_proveedor: rifPrefijo,
      rif_proveedor: rifNum,
      razon_social_proveedor: razon,
      prefijo_telefono_proveedor: telPrefijo,
      telefono_proveedor: telNum,
      correo_proveedor: correo,
      direccion_proveedor: direccion
    }
  });

  if (respuesta && respuesta.icono === 'success') {
    let rifCompleto = rifPrefijo + rifNum;

    // 1. Cerrar solo el modal de proveedor
    let modalEl = document.getElementById('modalRegistrarProveedorRapido');
    let modalProv = bootstrap.Modal.getInstance(modalEl);
    if (modalProv) modalProv.hide();
    else $('#modalRegistrarProveedorRapido').modal('hide');

    form.reset();

    // 2. Recargar lista de proveedores en caché
    await cargarOpcionesProveedores();

    // 3. Garantizar que la nueva opción exista y seleccionarla en cabecera y filas
    let nuevaOpcion = `<option value="${rifCompleto}">${razon}</option>`;
    if (!$('#selectProveedorEdicion').find(`option[value="${rifCompleto}"]`).length) {
      $('#selectProveedorEdicion').append(nuevaOpcion);
      proveedoresOptionsCache += nuevaOpcion;
    }
    $('#selectProveedorEdicion').val(rifCompleto);

    $('.selectProveedorFila').each(function () {
      if (!$(this).find(`option[value="${rifCompleto}"]`).length) {
        $(this).append(nuevaOpcion);
      }
      $(this).val(rifCompleto).removeClass('is-invalid');
    });

    // 4. Asegurar que el modal de compras siga abierto y scrollable
    $('body').addClass('modal-open');
    $('.modalRegistrar').css('overflow-y', 'auto').focus();
    if ($('.modal-backdrop').length === 0) {
      $('<div class="modal-backdrop fade show"></div>').appendTo('body');
    }

    Swal.fire({
      icon: 'success',
      title: 'Proveedor Registrado',
      text: 'El proveedor ha sido registrado y seleccionado en la compra.',
      timer: 2200,
      showConfirmButton: false
    });
  } else {
    Swal.fire({
      icon: (respuesta && respuesta.icono) || 'error',
      title: (respuesta && respuesta.titulo) || 'Error',
      text: (respuesta && respuesta.texto) || 'No se pudo registrar el proveedor.',
      target: document.getElementById('modalRegistrarProveedorRapido')
    });
  }
});
//#endregion [ EVENTOS ] FIN
