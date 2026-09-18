// Importaciones
import {
  enviarFormulario, listarDataTable, pedirDatosAjax, reiniciarDataModuloSS, rutaAbsoluta
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

// Funciones del modulo

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

  // Heredar el proveedor de la primera fila si ya hay alguno seleccionado
  let proveedorActual = $('.selectProveedorFila').not(nuevaFila.find('.selectProveedorFila')).first().val();
  if (proveedorActual) {
    nuevaFila.find('.selectProveedorFila').val(proveedorActual);
  }

  let itemSelect = nuevaFila.find('.selectArticuloFila');
  let opciones = await obtenerOpcionesItems('materia_prima');
  itemSelect.html(opciones);

  actualizarContador();
  actualizarBloqueoProveedor();

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

    // Setear proveedor usando .val() después de append (más robusto que string replace)
    if (pId) {
      nuevaFila.find('.selectProveedorFila').val(pId);
    }

    // Cargar opciones del articulo
    let selectArticulo = nuevaFila.find('.selectArticuloFila');
    let aId = String(item.id || '').trim();

    // Pasamos el ID para que venga con 'selected' desde el principio
    let opciones = await obtenerOpcionesItems(item.tipo, aId);
    selectArticulo.html(opciones);
  }

  actualizarContador();

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
      if (!proveedoresOptionsCache) await cargarOpcionesProveedores();
      modal.find('.selectProveedorAct')
        .prop('disabled', false)
        .html(proveedoresOptionsCache)
        .val(header.rif_proveedor);

      itemsCompra = [];
      respuesta.forEach(item => {
        itemsCompra.push({
          proveedorId: item.rif_proveedor,
          tipo: item.TIPO.toLowerCase(),
          id: item.id_item,
          nombre: item.ARTICULO,
          cantidad: parseFloat(item.cantidad_raw),
          id_unidad_medida: item.id_unidad_medida,
          nombre_unidad: item.nombre_unidad_medida || 'UNID'
        });
      });

      await actualizarTablaItems();

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

    let tbody = $('#verItemsBody');
    tbody.empty();

    respuesta.forEach(item => {
      let tipoLabel = item.TIPO === 'materia_prima' ? 'Materia Prima' : 'Producto';
      let inicial = (item.PROVEEDOR || '-').charAt(0).toUpperCase();

      let row = `
                <div class="grid-compras-row" style="background-color: white;">
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
                        <span class="fw-bold text-dark" style="font-size:.9rem;">${parseFloat(item.cantidad_raw).toFixed(2)}</span>
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
});

// Eventos del Modal
$(document).on('show.bs.modal', '.modalRegistrar', async function () {
  let form = $(this).find('form');
  let accion = form.find('input[name="accion"]').val();

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
    // Con solo 1 fila, habilitar proveedor
    actualizarBloqueoProveedor();
  }
});

// Enviar formulario y recolectar datos de la tabla
$(document).on('submit', '.formularioAjax', async function (e) {
  e.preventDefault();

  let detalles = [];
  let itemsInvalidos = 0;

  $('.fila-compra').each(function () {
    let fila = $(this);
    let proveedorId = fila.find('.selectProveedorFila').val();
    let tipo = fila.find('.selectTipoFila').val();
    let itemId = fila.find('.selectArticuloFila').val();
    let itemNombre = fila.find('.selectArticuloFila option:selected').text();
    let unidadId = fila.find('.inputUnidadIdFila').val();
    let cantidad = parseFloat(fila.find('.inputCantidadFila').val());

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
$(document).on('input', '.inputCantidadFila', function () {
  let val = parseFloat($(this).val());
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
});

// Validación en tiempo real: proveedor requerido
$(document).on('change', '.selectProveedorFila', function () {
  let invalido = !$(this).val();
  $(this).toggleClass('is-invalid', invalido);
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

async function imprimirOrden(id) {
  try {
    const respuesta = await pedirDatosAjax({
      modulo: 'compras',
      noGuardarLocal: true,
      datosPe: {
        accion: 'imprimir',
        id_compra: id
      }
    });

    if (respuesta && respuesta.icono === 'error') {
      Swal.fire({
        icon: respuesta.icono,
        title: respuesta.titulo || 'Error',
        text: respuesta.texto || 'No se pudo generar la orden de recepción.'
      });
    }
  } catch (error) {
    console.error('Error al generar la orden de recepción:', error);
  }
}

// Evento para abrir modal de registro rápido de proveedor
$(document).on("click", "#btnModalNuevoProveedor", function (e) {
  e.preventDefault();
  $('#modalRegistrarProveedorRapido').modal('show');
});

// Manejo de modal apilado para que aparezca por encima de modalRegistrar
$(document).on('show.bs.modal', '#modalRegistrarProveedorRapido', function () {
  $(this).css('z-index', 1070);
  setTimeout(function () {
    $('.modal-backdrop').not('.modal-stack').last().css('z-index', 1065).addClass('modal-stack');
  }, 10);
});

$(document).on('hidden.bs.modal', '#modalRegistrarProveedorRapido', function () {
  if ($('.modal.show').length > 0) {
    $('body').addClass('modal-open');
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

  if (!rifNum || !razon || !telNum || !correo) {
    Swal.fire('Campos obligatorios', 'Por favor complete todos los campos requeridos del proveedor.', 'warning');
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
    Swal.fire({
      icon: 'success',
      title: 'Proveedor Registrado',
      text: 'El proveedor ha sido registrado exitosamente y asignado a esta recepción.'
    });

    form.reset();
    $('#modalRegistrarProveedorRapido').modal('hide');

    // Recargar proveedores cacheados
    await cargarOpcionesProveedores();

    let rifCompleto = rifPrefijo + rifNum;
    $('.selectProveedorFila').val(rifCompleto);
    $('.selectProveedorAct').val(rifCompleto);
  } else {
    Swal.fire({
      icon: (respuesta && respuesta.icono) || 'error',
      title: (respuesta && respuesta.titulo) || 'Error',
      text: (respuesta && respuesta.texto) || 'No se pudo registrar el proveedor.'
    });
  }
});
//#endregion [ EVENTOS ] FIN
