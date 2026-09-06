//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, pedirDatosAjax, enviarFormulario,
  alertasAjax, reiniciarDataTables, validarEnTiempoReal,
  cambiarFormatos, encabezadosPeticiones
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES GLOBALES] COMIENZO
let productosOrden = [];
let serviciosOrden = [];
let cachePresentaciones = null;
let cacheServicios = null;
let cacheRutas = null;
let dtClientesOrden = null; // DataTable del modal de selección de clientes
let clientesSeleccionados = {}; // caché de clientes disponibles para selección
let mapaDelivery = null;
let marcadorDelivery = null;
let tasaBolivar = 1;
//#endregion [VARIABLES GLOBALES] FIN

//#region [HELPERS DE MONTO] COMIENZO

function parsearMonto(rawStr) {
  let s = (rawStr || '').trim();
  if (s === '' || s === '0') return 0;

  if (s.includes(',')) {
    let clean = s.replace(/\./g, '').replace(',', '.');
    return parseFloat(clean) || 0;
  }

  if (s.includes('.')) {
    return parseFloat(s) || 0;
  }

  return parseFloat(s.replace(/\D/g, '')) / 100 || 0;
}


function formatearMontoEnvio(monto) {
  return parseFloat(monto).toFixed(2).replace('.', ',');
}
//#endregion [HELPERS DE MONTO] FIN

let ivaActivo = 0;
const CENTRO_JLACRUZ = { lat: 10.063276, lng: -69.31708 };
const TOMTOM_API_KEY = 'plFhQVWfX5abG1DPt7jja56Syrqh7rY2';

//#region [FUNCIONES DEL MODULO] COMIENZO

// Nos encargamos de poner la fecha y hora actual en la pantallita de la Orden
function mostrarFechaActual() {
  let ahora = new Date();
  let dia = String(ahora.getDate()).padStart(2, '0');
  let mes = String(ahora.getMonth() + 1).padStart(2, '0');
  let anio = ahora.getFullYear();
  let h = String(ahora.getHours()).padStart(2, '0');
  let m = String(ahora.getMinutes()).padStart(2, '0');
  let ampm = ahora.getHours() >= 12 ? 'PM' : 'AM';
  let h12 = ahora.getHours() % 12 || 12;
  let h12s = String(h12).padStart(2, '0');
  $('#fechaOrdenDisplay').val(`${dia}-${mes}-${anio} ${h12s}:${m} ${ampm}`);
}

async function cargarDatosFinancieros() {
  let reqMonedas = await pedirDatosAjax({
    modulo: 'monedas', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  if (Array.isArray(reqMonedas)) {
    let usd = reqMonedas.find(m => m.nombre_moneda.toUpperCase() === 'DÓLAR' || m.nombre_moneda.toUpperCase() === 'DOLAR');
    if (usd) tasaBolivar = parseFloat(usd.valor_moneda);
  }

  let reqIva = await pedirDatosAjax({
    modulo: 'cambiosIva', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  if (Array.isArray(reqIva) && reqIva.length > 0) {
    let activo = [...reqIva].reverse().find(i => i.status == 1);
    if (activo) ivaActivo = parseFloat(activo.monto_cambio_iva);
  }
}

// Inicializa el DataTable de selección de clientes (se llama una sola vez)
function inicializarDtClientes() {
  if (dtClientesOrden) return; // ya fue inicializado
  dtClientesOrden = listarDataTable({
    selectorTabla: '#dtSelClienteOrden',
    encabezados: {
      'rif_cedula_cliente': 'CÉDULA / RIF',
      'razon_social_cliente': 'NOMBRE / RAZÓN SOCIAL',
      'telefono_cliente': 'TELÉFONO',
      'correo_cliente': 'CORREO',
    },
    informacionPe: {
      modulo: 'clientes',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    },
    botones: ({ fila }) => {
      // Guardamos la fila en el caché para recuperarla al elegir
      clientesSeleccionados[fila.rif_cedula_cliente] = fila;
      return `<button type="button" class="btn btn-sm btn-success btnElegirClienteOrden" data-id="${fila.rif_cedula_cliente}"><i class="fi fi-rs-check"></i> Elegir</button>`;
    }
  });
}

async function cargarPresentaciones() {
  if (cachePresentaciones) return cachePresentaciones;
  let items = await pedirDatosAjax({
    modulo: 'productos', noGuardarLocal: true,
    datosPe: { accion: 'listar', tipoConsulta: 'todasLasPresentaciones' }
  });
  cachePresentaciones = Array.isArray(items) ? items : [];
  return cachePresentaciones;
}

async function cargarServicios() {
  if (cacheServicios) return cacheServicios;
  let items = await pedirDatosAjax({
    modulo: 'servicios', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  cacheServicios = Array.isArray(items) ? items : [];
  return cacheServicios;
}

async function cargarRutas() {
  if (cacheRutas) return cacheRutas;
  let items = await pedirDatosAjax({
    modulo: 'rutas', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  cacheRutas = Array.isArray(items) ? items : [];
  return cacheRutas;
}

async function cargarRepartidores() {
  let items = await pedirDatosAjax({
    modulo: 'repartidores', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  let opts = '<option value="">Sin asignar</option>';
  if (Array.isArray(items)) {
    items.forEach(r => {
      let nombre = (r.nombre_repartidor || '') + ' ' + (r.apellido_repartidor || '');
      opts += `<option value="${r.cedula_repartidor}">${nombre.trim()}</option>`;
    });
  }
  $('#selectRepartidorOrden').html(opts);
}

// === A partir de aquí tenemos todo lo que hace funcionar el mapita del delivery ===
function destruirMapaDelivery() {
  if (mapaDelivery) {
    mapaDelivery.remove();
    mapaDelivery = null;
    marcadorDelivery = null;
  }
}

async function inicializarMapaDelivery() {
  // Por si acaso había un mapa de antes, lo borramos para no sobrecargar
  destruirMapaDelivery();

  // Le pedimos permiso al navegador para saber dónde está el usuario
  try {
    let permiso = await navigator.permissions.query({ name: 'geolocation' });
    if (permiso.state === 'denied') {
      Swal.fire('Permiso denegado', 'Active la geolocalización para usar el mapa de delivery', 'warning');
      $('#chkDeliveryOrden').prop('checked', false).trigger('change');
      return;
    }
  } catch (e) { /* Navegadores sin API permissions */ }

  let ubicacion;
  try {
    ubicacion = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000 });
    });
  } catch (e) {
    Swal.fire('Error GPS', 'No se pudo obtener su ubicación. Verifique los permisos de geolocalización.', 'warning');
    $('#chkDeliveryOrden').prop('checked', false).trigger('change');
    return;
  }

  let lat = ubicacion.coords.latitude;
  let lng = ubicacion.coords.longitude;

  // Levantamos el mapa centrado en la ubicación actual
  mapaDelivery = L.map('mapaDeliveryOrden').setView([lat, lng], 15);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="#">OpenStreetMap</a>'
  }).addTo(mapaDelivery);

  // Ponemos el marcador del local (nuestro punto de partida)
  const iconoJLACRUZ = L.divIcon({ className: 'iconoHamburguesa' });
  L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJLACRUZ })
    .addTo(mapaDelivery).bindPopup('J. LACRUZ C.A.');

  // Y este es el marcador de a dónde vamos a llevar el pedido
  marcadorDelivery = L.marker([lat, lng]).addTo(mapaDelivery).bindPopup('Ubicación de entrega');

  // Hacemos el cálculo inicial de cuántos kilómetros hay
  await actualizarDeliveryPorUbicacion({ lat, lng });

  // Si tocan otra parte del mapa, movemos el marcador para allá y recalculamos todo
  mapaDelivery.on('click', async function (e) {
    if (marcadorDelivery) mapaDelivery.removeLayer(marcadorDelivery);
    marcadorDelivery = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mapaDelivery);
    mapaDelivery.panTo([e.latlng.lat, e.latlng.lng]);
    await actualizarDeliveryPorUbicacion(e.latlng);
  });
}

async function actualizarDeliveryPorUbicacion(latlng) {
  // Guardamos la latitud y longitud en unos inputs ocultos para usarlos luego
  $('#latDeliveryOrden').val(latlng.lat);
  $('#lngDeliveryOrden').val(latlng.lng);

  // Usamos una API (TomTom) para calcular la ruta en auto hasta allá
  let distanciaKM = 0;
  try {
    let resp = await fetch(
      `https://api.tomtom.com/routing/1/calculateRoute/${CENTRO_JLACRUZ.lat},${CENTRO_JLACRUZ.lng}:${latlng.lat},${latlng.lng}/json?key=${TOMTOM_API_KEY}&travelMode=car`
    );
    let infoRuta = await resp.json();
    if (infoRuta.routes && infoRuta.routes[0]?.summary?.lengthInMeters) {
      distanciaKM = Math.ceil(infoRuta.routes[0].summary.lengthInMeters / 1000);
    } else {
      throw new Error('Sin ruta');
    }
  } catch (e) {
    console.warn('Usando distancia lineal como respaldo:', e);
    let centroLL = L.latLng(CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng);
    let puntoLL = L.latLng(latlng.lat, latlng.lng);
    distanciaKM = Math.ceil(centroLL.distanceTo(puntoLL) / 1000);
  }
  $('#distanciaDeliveryOrden').val(distanciaKM);

  // Revisamos en nuestras rutas guardadas cuál encaja con esta distancia
  let rutas = await cargarRutas();
  let rutaEncontrada = rutas.find(r => {
    let min = parseFloat(r.minimo_km_ruta);
    let max = parseFloat(r.maximo_km_ruta);
    return distanciaKM >= min && distanciaKM <= max;
  });

  if (rutaEncontrada) {
    let precioPorKm = parseFloat(rutaEncontrada.precio_ruta);
    let costoTotal = precioPorKm * distanciaKM;
    $('#rutaAsignadaDeliveryOrden').val(`${rutaEncontrada.nombre_ruta} ($${precioPorKm.toFixed(2)}/km)`);
    $('#idRutaDeliveryOrden').val(rutaEncontrada.id_ruta);
    $('#costoDeliveryOrden').val(costoTotal.toFixed(2));
  } else {
    $('#rutaAsignadaDeliveryOrden').val('Fuera de cobertura');
    $('#idRutaDeliveryOrden').val('');
    $('#costoDeliveryOrden').val('0.00');
  }
  calcularTotales();

  // Traducimos las coordenadas a una dirección de calle normal (texto)
  try {
    let geoResp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`);
    let geoData = await geoResp.json();
    $('#direccionDeliveryOrden').val(geoData.display_name || `${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  } catch (e) {
    $('#direccionDeliveryOrden').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  }
}
function calcularTotales() {
  let subProd = productosOrden.reduce((s, p) => s + (p.cantidad * p.precio), 0);
  let subServ = serviciosOrden.reduce((s, s2) => {
    let precio = s2.es_mapfre ? s2.precio_mapfre : s2.precio;
    return s + (s2.cantidad * precio);
  }, 0);
  //Ocultar Delivery si no existe al menos 1 producto.
  if (productosOrden.length > 0) {
    $('#liTabDeliveryOrden').show();
  } else {
    $('#liTabDeliveryOrden').hide();
    if ($('#btnTabDeliveryOrden').hasClass('active')) {
      let tabServicios = document.querySelector('#tabsOrden button[data-bs-target="#tabServiciosOrden"]');
      if (tabServicios) {
        let tab = new bootstrap.Tab(tabServicios);
        tab.show();
      }
    }
    if ($('#chkDeliveryOrden').is(':checked')) {
      $('#chkDeliveryOrden').prop('checked', false).trigger('change');
    }
  }

  let delivery = parseFloat($('#costoDeliveryOrden').val()) || 0;
  if (!$('#chkDeliveryOrden').is(':checked')) delivery = 0;

  let subtotalGeneral = subProd + subServ + delivery;
  let montoIva = subtotalGeneral * (ivaActivo / 100);
  let total = subtotalGeneral + montoIva;
  let totalBs = total * tasaBolivar;

  // Verificamos si algún servicio está incompleto (falta fecha o mapa)
  let serviciosIncompletos = serviciosOrden.some(s => !s.fecha_ejecucion || !s.id_ruta);

  // Aquí revisamos que tengamos suficiente de cada cosa antes de dejar Ordenr
  let consumoPorProducto = {};
  let consumoBloqueante = {}; // Solo para los productos directos

  // Contamos cuántos productos sueltos pusimos en la Orden (agrupados por id_producto real)
  productosOrden.forEach(p => {
    let id = p.id_producto;
    if (!consumoPorProducto[id]) consumoPorProducto[id] = {
      nombre: p.nombre_general || p.nombre,
      cantidad_volumen: 0,
      stock: p.stock,
      unidad: p.unidad
    };
    if (!consumoBloqueante[id]) consumoBloqueante[id] = 0;

    let vol = (p.cantidad * p.capacidad);
    consumoPorProducto[id].cantidad_volumen += vol;
    consumoBloqueante[id] += vol;
  });

  // Y le sumamos los materiales que gastan los servicios, para tener el total real
  serviciosOrden.forEach(s => {
    if (s.materiales) {
      s.materiales.forEach(m => {
        let id = m.id_producto;
        if (!consumoPorProducto[id]) consumoPorProducto[id] = {
          nombre: m.nombre_general || m.nombre,
          cantidad_volumen: 0,
          stock: m.stock,
          unidad: m.unidad || 'Unidades'
        };
        // Para servicios, la cantidad ya viene en volumen (cantidad_requerida)
        consumoPorProducto[id].cantidad_volumen += (m.cantidad_requerida * s.cantidad);
      });
    }
  });

  let errorStock = false;
  let htmlErrores = '';
  let htmlErroresNoBloqueantes = '';

  // Limpiamos las marcas de error rojo de antes para empezar frescos
  $('.cantProdOrden').removeClass('is-invalid');
  $('.fila-material-consumo').removeClass('table-danger text-danger');

  Object.keys(consumoPorProducto).forEach(id => {
    let cons = consumoPorProducto[id];
    let esBloqueante = consumoBloqueante[id] && consumoBloqueante[id] > cons.stock;

    if (cons.cantidad_volumen > cons.stock) {
      let mensajeError = `<li>${cons.nombre}: Stock ${cons.stock} ${cons.unidad}, Requiere ${cons.cantidad_volumen % 1 === 0 ? cons.cantidad_volumen : cons.cantidad_volumen.toFixed(2)} ${cons.unidad}</li>`;

      if (esBloqueante) {
        errorStock = true;
        htmlErrores += mensajeError;
        // Pintamos de rojo el producto si no nos alcanza el stock
        $(`.cantProdOrden`).filter(function () {
          let i = $(this).data('index');
          return productosOrden[i].id_producto == id;
        }).addClass('is-invalid');
      } else {
        htmlErroresNoBloqueantes += mensajeError;
      }

      // Pintamos los materiales en la tabla de servicios
      $(`.fila-material-consumo[data-id="${id}"]`).addClass('table-danger text-danger');
    }
  });

  $('.alertaStockOrden').remove();

  if (errorStock) {
    let htmlAlerta = `<div class="alert alert-danger p-2 mb-3 alertaStockOrden"><small><strong>¡Stock Insuficiente (No se puede registrar)!</strong><ul class="mb-0 ps-3 listaErroresStockOrden">${htmlErrores}</ul></small></div>`;
    $('#contenedorProductosOrden').before(htmlAlerta);
  }

  if (htmlErroresNoBloqueantes !== '') {
    let htmlAlertaAdv = `<div class="alert alert-danger p-2 mb-3 alertaStockOrden"><small><strong>Advertencia (Faltará stock al ejecutar servicios):</strong><ul class="mb-0 ps-3 listaErroresStockOrden">${htmlErroresNoBloqueantes}</ul></small></div>`;
    $('#contenedorServiciosOrden').before(htmlAlertaAdv);
  }

  // Generamos el resumen visual de consumo
  let htmlResumen = '';
  Object.keys(consumoPorProducto).forEach(id => {
    let cons = consumoPorProducto[id];
    let classTexto = cons.cantidad_volumen > cons.stock ? 'text-danger fw-bold' : 'text-success';
    htmlResumen += `<div class="d-flex justify-content-between mb-1 border-bottom border-secondary pb-1">
      <span>${cons.nombre}</span>
      <span class="${classTexto}">Se consumirán: ${cons.cantidad_volumen % 1 === 0 ? cons.cantidad_volumen : cons.cantidad_volumen.toFixed(2)} ${cons.unidad}</span>
    </div>`;
  });

  if (htmlResumen !== '') {
    $('#listaResumenVolumen').html(htmlResumen);
    $('#resumenVolumenOrden').removeClass('d-none');
  } else {
    $('#resumenVolumenOrden').addClass('d-none');
  }

  $('#subtotalProdOrden').text('$' + subProd.toFixed(2));
  $('#subtotalServOrden').text('$' + subServ.toFixed(2));
  $('#resumenProdOrden').text('$' + subProd.toFixed(2));
  $('#resumenServOrden').text('$' + subServ.toFixed(2));
  $('#resumenDeliveryOrden').text('$' + delivery.toFixed(2));
  $('#resumenTotalOrden').html(`$${total.toFixed(2)} <br><small class="text-muted fs-6">Bs ${totalBs.toFixed(2)} (IVA ${ivaActivo}%: $${montoIva.toFixed(2)})</small>`);
  $('#totalGeneralOrden').val(total.toFixed(2));
  $('#badgeProdOrden').text(productosOrden.length);
  $('#badgeServOrden').text(serviciosOrden.length);
  $('#btnGuardarOrden').prop('disabled', total <= 0 || errorStock || serviciosIncompletos);
}
function renderProductos() {
  let cont = $('#contenedorProductosOrden');
  if (productosOrden.length === 0) {
    cont.html(`<div class="fact-empty-state"><i class="fi fi-rs-box-open"></i><p>No hay productos agregados</p></div>`);
  } else {
    let html = `<table class="fact-items-table"><thead><tr>
      <th>Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th><th></th>
    </tr></thead><tbody>`;
    productosOrden.forEach((p, i) => {
      let sub = (p.cantidad * p.precio).toFixed(2);
      html += `<tr>
        <td><span class="fact-item-nombre">${p.nombre}</span></td>
        <td>$${p.precio.toFixed(2)}</td>
        <td><input type="number" class="fact-qty-input cantProdOrden" data-index="${i}" value="${p.cantidad}" min="1" step="1"></td>
        <td class="fw-bold text-primary">$${sub}</td>
        <td><button type="button" class="fact-btn-borrar quitarProdOrden" data-index="${i}">&times;</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    cont.html(html);
  }
  calcularTotales();
}
function renderServicios() {
  let cont = $('#contenedorServiciosOrden');
  if (serviciosOrden.length === 0) {
    cont.html(`<div class="fact-empty-state"><i class="fi fi-tr-room-service"></i><p>No hay servicios agregados</p></div>`);
  } else {
    let html = `<table class="fact-items-table"><thead><tr>
      <th>Servicio</th><th>Precio</th><th>Mapfre</th><th>Cant.</th><th>Subtotal</th><th></th>
    </tr></thead><tbody>`;
    serviciosOrden.forEach((s, i) => {
      let precio = s.es_mapfre ? s.precio_mapfre : s.precio;
      let sub = (s.cantidad * precio).toFixed(2);

      // Si es Mapfre y ya tiene un precio puesto, mostramos ese en verde y el original tachado
      let precioColHTML = s.es_mapfre && s.precio_mapfre > 0
        ? `<span class="text-success fw-bold">$${s.precio_mapfre.toFixed(2)}</span> <br><small class="text-muted text-decoration-line-through">$${s.precio.toFixed(2)}</small>`
        : `$${s.precio.toFixed(2)}`;

      let btnToggleMat = s.materiales && s.materiales.length > 0
        ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 btnToggleMatOrden" data-idx="${i}" title="Ver productos a descontar"><i class="fi fi-rs-plus-small"></i></button>`
        : '';

      let fechaHtml = `<input type="date" class="form-control form-control-sm fechaServOrden mt-1" data-index="${i}" value="${s.fecha_ejecucion || ''}" title="Fecha de Ejecución" required>`;
      let btnUbicacionHtml = s.id_ruta ?
        `<button type="button" class="btn btn-sm btn-success btnUbicacionServOrden mt-1 w-50" data-index="${i}" title="Ubicación guardada"><i class="fi fi-rs-marker me-1"></i>Ubicación OK</button>` :
        `<button type="button" class="btn btn-sm btn-outline-danger btnUbicacionServOrden mt-1 w-50" data-index="${i}" title="Falta Ubicación"><i class="fi fi-rs-marker me-1"></i>Ubicación</button>`;

      // El toggle Mapfre siempre está habilitado para que el usuario pueda activarlo
      // Cuando se activa, aparece un campito para escribir el precio que cubre Mapfre
      html += `<tr>
        <td>
          ${btnToggleMat}<span class="fact-item-nombre">${s.nombre}</span>
          ${fechaHtml}
        </td>
        <td>${precioColHTML}</td>
        <td>
          <div class="form-check form-switch" title="Activar precio Mapfre">
            <input class="form-check-input toggleMapfre" type="checkbox" data-index="${i}" ${s.es_mapfre ? 'checked' : ''}>
          </div>
          ${s.es_mapfre ? `<div class="input-group input-group-sm mt-1" style="max-width:120px;">
            <span class="input-group-text p-1">$</span>
            <input type="number" class="form-control form-control-sm precioMapfreOrden" data-index="${i}" value="${s.precio_mapfre > 0 ? s.precio_mapfre.toFixed(2) : ''}" min="0" step="0.01" placeholder="0.00">
          </div>` : ''}
        </td>
        <td>
          <input type="number" class="fact-qty-input cantServOrden" data-index="${i}" value="${s.cantidad}" min="1" step="1">
          ${btnUbicacionHtml}
        </td>
        <td class="fw-bold text-success">$${sub}</td>
        <td><button type="button" class="fact-btn-borrar quitarServOrden" data-index="${i}">&times;</button></td>
      </tr>`;

      // Sub-tabla de materiales: el "Total a descontar" se calcula multiplicando por la cantidad del servicio
      if (s.materiales && s.materiales.length > 0) {
        html += `<tr class="fact-materiales-row-Orden d-none" data-serv-idx="${i}">
          <td colspan="6" class="p-0 ps-3 pe-3 pb-2">
            <div class="bg-light rounded p-2 mt-1">
              <small class="text-muted fw-semibold d-block mb-1"><i class="fi fi-rs-box me-1"></i>Productos a descontar del stock:</small>
              <table class="table table-sm table-borderless mb-0">
                <thead><tr class="text-muted"><th style="font-size:.78rem">Producto</th><th style="font-size:.78rem">Cant/Unid (x1)</th><th style="font-size:.78rem">Total a descontar (x${s.cantidad})</th></tr></thead>
                <tbody>`;
        s.materiales.forEach(m => {
          let cantTotal = m.cantidad_requerida * s.cantidad;
          html += `<tr class="fila-material-consumo" data-id="${m.id_producto}">
            <td><small>${m.nombre}</small></td>
            <td><small>${m.cantidad_requerida} ${m.unidad}</small></td>
            <td><small class="fw-semibold cant-descontar-mat">${cantTotal % 1 === 0 ? cantTotal : cantTotal.toFixed(2)} ${m.unidad}</small></td>
          </tr>`;
        });
        html += `</tbody></table></div></td></tr>`;
      }
    });
    html += '</tbody></table>';
    cont.html(html);
  }
  calcularTotales();
}
async function abrirSelectorProductos() {
  let items = await cargarPresentaciones();
  if (!items.length) { Swal.fire('Info', 'No hay productos', 'info'); return; }

  let filas = items.map(p => {
    let id = p.id_presentacion_producto || p.id_producto;
    let idProducto = p.id_producto;
    let nombre = p.nombre_producto || p.nombre;
    if (p.nombre_presentacion) {
      nombre += ` (${p.nombre_presentacion})`;
    }
    let precio = parseFloat(p.precio_producto || p.precio || 0);
    let stock = parseFloat(p.stock_producto ?? 0);
    let stockMinimo = parseFloat(p.stock_minimo_producto ?? 0);
    let capacidad = parseFloat(p.cantidad_pmp || 1);
    let unidad = p.nombre_unidad_medida || 'Unidades';
    let presentacion = p.nombre_presentacion || '';

    // El stock mostrado es en volumen, calculamos para cuántas presentaciones alcanza
    let stockPresentaciones = Math.floor(stock / capacidad);

    // Vemos cómo andamos de inventario para este producto
    let esStockCritico = stock <= stockMinimo;
    let sinStock = stockPresentaciones <= 0;
    let clasesFila = esStockCritico ? 'table-danger' : '';
    let badgeStock = '';
    if (sinStock) {
      badgeStock = `<span class="badge bg-danger ms-1">Sin stock suficiente</span>`;
    } else if (esStockCritico) {
      badgeStock = `<span class="badge bg-warning text-dark ms-1"><i class="fi fi-rs-triangle-warning me-1"></i>Bajo</span>`;
    }

    return `<tr class="${clasesFila}">
      <td>
        ${nombre} <br>
        <small class="text-muted"><i class="fi fi-rs-box-alt me-1"></i>1 ${presentacion || 'Unidad'} = ${capacidad} ${unidad}</small>
      </td>
      <td>$${precio.toFixed(2)}</td>
      <td>
        <span data-bs-toggle="tooltip" title="Stock Total: ${stock} ${unidad}">${stockPresentaciones} ${presentacion || 'Unid.'}</span>
        ${badgeStock}
      </td>
      <td><button class="btn btn-sm text-white selProdOrden fact-purple-blue-grad"
        data-id="${id}" data-id_producto="${idProducto}" data-nombre="${nombre}" data-nombre_general="${p.nombre_producto || p.nombre}"
        data-precio="${precio}" data-stock="${stock}"
        data-capacidad="${capacidad}" data-unidad="${unidad}" data-presentacion="${presentacion}"
        ${sinStock ? 'disabled' : ''}>
        <i class="fi fi-rs-plus me-1"></i>${sinStock ? 'Agotado' : 'Agregar'}
      </button></td>
    </tr>`;
  }).join('');

  if ($.fn.DataTable.isDataTable('#dtSelProdOrden')) {
    $('#dtSelProdOrden').DataTable().destroy();
  }
  $('#dtSelProdOrden tbody').html(filas);

  let modalEl = document.getElementById('modalSelProdOrden');
  let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);

  // Le ponemos un fondito oscuro al modal principal para que resalte este nuevo
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  // Limpiamos y registramos los eventos una única vez para evitar duplicidad de callbacks
  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  $(modalEl).off('shown.bs.modal').on('shown.bs.modal', function () {
    $('#dtSelProdOrden').DataTable({
      paging: true,
      pageLength: 5,
      lengthChange: false,
      ordering: true,
      info: false,
      autoWidth: false,
      language: {
        search: 'Buscar:',
        zeroRecords: 'No se encontraron productos',
        paginate: { previous: '‹', next: '›' }
      },
      columnDefs: [
        { orderable: false, targets: 3 }
      ]
    });
  });

  modalInst.show();
}
async function abrirSelectorServicios() {
  let items = await cargarServicios();
  if (!items.length) { Swal.fire('Info', 'No hay servicios', 'info'); return; }

  let filas = items.map(s => {
    let id = s.id_servicio;
    let nombre = s.nombre_servicio || s.nombre;
    let precio = parseFloat(s.precio_servicio || s.precio || 0);
    let precioMapfre = parseFloat(s.precio_servicio_mapfre || 0);

    let mapfreBadge = precioMapfre > 0 ? `<br><small class="text-success">Mapfre: $${precioMapfre.toFixed(2)}</small>` : '';

    return `<tr>
      <td>${nombre}</td><td>$${precio.toFixed(2)} ${mapfreBadge}</td>
      <td><button class="btn btn-sm btn-success selServOrden fact-purple-blue-grad"
        data-id="${id}" data-nombre="${nombre}" data-precio="${precio}" data-precio_mapfre="${precioMapfre}">
        <i class="fi fi-rs-plus me-1"></i>Agregar
      </button></td>
    </tr>`;
  }).join('');

  if ($.fn.DataTable.isDataTable('#dtSelServOrden')) {
    $('#dtSelServOrden').DataTable().destroy();
  }
  $('#dtSelServOrden tbody').html(filas);

  let modalEl = document.getElementById('modalSelServOrden');
  let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);

  // Le ponemos un fondito oscuro al modal principal para que resalte este nuevo
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  // Limpiamos y registramos los eventos una única vez para evitar duplicidad de callbacks
  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  $(modalEl).off('shown.bs.modal').on('shown.bs.modal', function () {
    $('#dtSelServOrden').DataTable({
      paging: true,
      pageLength: 5,
      lengthChange: false,
      ordering: true,
      info: false,
      autoWidth: false,
      language: {
        search: 'Buscar:',
        zeroRecords: 'No se encontraron servicios',
        paginate: { previous: '‹', next: '›' }
      },
      columnDefs: [
        { orderable: false, targets: 2 }
      ]
    });
  });

  modalInst.show();
}
async function verDetalleOrden(idOrden) {
  let data = await pedirDatosAjax({
    modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
    datosPe: { accion: 'obtenerDetalle', id_orden_entrega_presupuesto: idOrden }
  });
  if (!data || !data.cabecera) {
    Swal.fire('Error', 'No se pudo obtener el detalle', 'error');
    return;
  }
  let c = data.cabecera;

  let estado = '';
  if (c.estado_num == 5) estado = '<span class="badge bg-danger">Anulada</span>';
  else if (c.estado_num == 1) estado = '<span class="badge bg-success">Procesada y Pagada</span>';
  else if (c.estado_num == 2) estado = '<span class="badge bg-warning text-dark">Procesada y sin Pago</span>';
  else if (c.estado_num == 3) estado = '<span class="badge bg-success"><i class="fi fi-rs-check-circle me-1"></i>Pagada y Despachada (Cancelada)</span>';
  else if (c.estado_num == 4) estado = '<span class="badge bg-info">Despachada y sin Pago</span>';
  else if (c.estado_num == 12) estado = '<span class="badge bg-info">Ejecutada y sin Pago</span>';
  else if (c.estado_num == 13) estado = '<span class="badge bg-success"><i class="fi fi-rs-check-circle me-1"></i>Pagada y Ejecutada</span>';
  else estado = `<span class="badge bg-secondary">${c.estado_dinamico || 'Activa'}</span>`;

  let prodHtml = '';
  let subProd = 0;
  if (data.productos && data.productos.length) {
    prodHtml = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Presentación</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
    data.productos.forEach(p => {
      let sub = p.cantidad_producto * p.precio_producto;
      subProd += sub;
      prodHtml += `<tr><td>${p.nombre_producto}</td><td>${p.nombre_presentacion}</td><td>${p.cantidad_producto}</td><td>$${parseFloat(p.precio_producto).toFixed(2)}</td><td>$${sub.toFixed(2)}</td></tr>`;
    });
    prodHtml += '</tbody></table>';
  } else {
    prodHtml = '<p class="text-muted">Sin productos</p>';
  }

  let servHtml = '';
  let subServ = 0;
  let resumenMateriales = {}; // Aquí vamos a ir sumando todos los materiales que usamos en los distintos servicios
  if (data.servicios && data.servicios.length) {
    servHtml = '<table class="table table-sm mb-0"><thead><tr><th>Servicio</th><th>Cant.</th><th>Precio</th><th>Mapfre</th><th>Subtotal</th></tr></thead><tbody>';
    data.servicios.forEach((s, idx) => {
      let precio = s.es_precio_mapfre == 1 ? s.precio_servicio_mapfre : s.precio_servicio;
      let sub = 0;
      let isCancelado = s.status == 4;
      if (!isCancelado) {
        sub = s.cantidad_servicio * precio;
        subServ += sub;
      }

      // Vamos sumando lo que gastamos para el total al final
      if (s.materiales && s.materiales.length) {
        s.materiales.forEach(m => {
          let key = m.id_producto;
          let cantUsada = parseFloat(m.cantidad_producto) * parseFloat(s.cantidad_servicio);
          if (!resumenMateriales[key]) {
            resumenMateriales[key] = {
              nombre: m.nombre_producto,
              unidad: m.nombre_unidad_medida || '',
              cantidad: 0
            };
          }
          resumenMateriales[key].cantidad += cantUsada;
        });
      }

      // Armamos la tablita de los materiales, pero la dejamos escondida hasta que le den al botón
      let matHtml = '';
      if (s.materiales && s.materiales.length) {
        matHtml = `<tr class="fact-materiales-row d-none" data-serv-idx="${idx}">
          <td colspan="5" class="p-0 ps-3 pe-3 pb-2">
            <div class="bg-light rounded p-2 mt-1">
              <small class="text-muted fw-semibold d-block mb-1"><i class="fi fi-rs-box me-1"></i>Productos utilizados:</small>
              <table class="table table-sm table-borderless mb-0">
                <thead><tr class="text-muted"><th style="font-size:.78rem">Producto</th><th style="font-size:.78rem">Cant/Unid (x1)</th><th style="font-size:.78rem">Total usado (x${s.cantidad_servicio})</th></tr></thead>
                <tbody>`;
        s.materiales.forEach(m => {
          let cantTotal = (parseFloat(m.cantidad_producto) * parseFloat(s.cantidad_servicio));
          matHtml += `<tr>
            <td><small>${m.nombre_producto}</small></td>
            <td><small>${parseFloat(m.cantidad_producto)} ${m.nombre_unidad_medida || ''}</small></td>
            <td><small class="fw-semibold">${cantTotal % 1 === 0 ? cantTotal : cantTotal.toFixed(2)} ${m.nombre_unidad_medida || ''}</small></td>
          </tr>`;
        });
        matHtml += '</tbody></table></div></td></tr>';
      }

      // Mostramos el precio de forma clara: si fue Mapfre, el original tachado y el Mapfre en verde
      let precioDetHTML = s.es_precio_mapfre == 1
        ? `<span class="text-success fw-bold">$${parseFloat(s.precio_servicio_mapfre).toFixed(2)}</span><br><small class="text-muted text-decoration-line-through">$${parseFloat(s.precio_servicio).toFixed(2)}</small>`
        : `$${parseFloat(s.precio_servicio).toFixed(2)}`;

      let mapfreBadge = s.es_precio_mapfre == 1
        ? `<span class="badge bg-success">Sí — $${parseFloat(s.precio_servicio_mapfre).toFixed(2)}</span>`
        : '<span class="badge bg-danger">No</span>';

      let mapButton = (s.coordenada_latitud && s.coordenada_longitud)
        ? `<button type="button" class="btn btn-sm btn-info text-white btnVerMapaServicioDetalle ms-2 py-0 px-2" 
            data-lat="${s.coordenada_latitud}" data-lng="${s.coordenada_longitud}" data-nombre="${s.nombre_servicio}" title="Ver ubicación de ejecución">
            <i class="fi fi-rs-map-marker"></i> Mapa</button>`
        : '';

      let statusBadge = '';
      if (s.status == 1) statusBadge = '<span class="badge bg-info ms-2">Pendiente</span>';
      else if (s.status == 2) statusBadge = '<span class="badge bg-success ms-2">Ejecutada</span>';
      else if (s.status == 4) statusBadge = '<span class="badge bg-danger ms-2">Cancelada</span>';

      let cssClassTachado = isCancelado ? 'text-decoration-line-through text-muted' : '';

      servHtml += `<tr class="${cssClassTachado}">
        <td>
          ${s.materiales && s.materiales.length ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 btnToggleMat" data-idx="${idx}" title="Ver productos"><i class="fi fi-rs-plus-small"></i></button>` : ''}
          ${s.nombre_servicio}
          ${statusBadge}
          ${mapButton}
        </td>
        <td>${s.cantidad_servicio}</td>
        <td>${precioDetHTML}</td>
        <td>${mapfreBadge}</td>
        <td>$${sub.toFixed(2)}</td>
      </tr>${matHtml}`;
    });
    servHtml += '</tbody></table>';

    // Al final de la lista de servicios, mostramos el gran total de materiales consumidos
    let clavesMat = Object.keys(resumenMateriales);
    if (clavesMat.length) {
      servHtml += `<div class="fact-resumen-materiales mt-2 p-2 rounded">
        <small class="fw-bold text-black d-block mb-1"><i class="fi fi-rs-resources me-1"></i>Total materiales consumidos:</small>
        <div class="d-flex flex-wrap gap-2">`;
      clavesMat.forEach(k => {
        let m = resumenMateriales[k];
        let cantDisplay = m.cantidad % 1 === 0 ? m.cantidad : m.cantidad.toFixed(2);
        servHtml += `<span class="badge bg-light text-dark border">${m.nombre} — <strong>${cantDisplay} ${m.unidad}</strong></span>`;
      });
      servHtml += '</div></div>';
    }
  } else {
    servHtml = '<p class="text-muted">Sin servicios</p>';
  }

  let delHtml = '';
  let costoDel = 0;
  let tieneCoordsDel = false;
  let delLat = 0, delLng = 0;
  if (data.delivery) {
    costoDel = parseFloat(data.delivery.costo_delivery_total || data.delivery.precio_ruta || 0);
    delLat = parseFloat(data.delivery.coordenada_latitud || 0);
    delLng = parseFloat(data.delivery.coordenada_longitud || 0);
    tieneCoordsDel = delLat !== 0 && delLng !== 0;
    delHtml = `<div class="row mb-2">
      <div class="col-md-4"><strong>Ruta:</strong> ${data.delivery.nombre_ruta}</div>
      <div class="col-md-4"><strong>Costo:</strong> $${costoDel.toFixed(2)}</div>
      <div class="col-md-4"><strong>Repartidor:</strong> ${data.delivery.REPARTIDOR || 'Sin asignar'}</div>
    </div>`;
    if (tieneCoordsDel) {
      delHtml += `<div id="mapaDetalleDel" class="rounded border mt-2" style="height: 250px; z-index: 1;"></div>`;
    }
  } else {
    delHtml = '<p class="text-muted">Sin delivery</p>';
  }

  let total = subProd + subServ + costoDel;
  let ivaPorcentaje = parseFloat(c.IVA) || 0;
  let montoIva = total * (ivaPorcentaje / 100);
  let totalConIva = total + montoIva;
  let totalBs = totalConIva * tasaBolivar;

  $('#contenidoDetalleOrden').html(`
    <div class="fact-detalle-seccion">
      <h6><i class="fi fi-rs-file-invoice-dollar me-2"></i>Información General</h6>
      <div class="row">
        <div class="col-md-3"><strong>Orden:</strong> ${c.id_orden_entrega_presupuesto}</div>
        <div class="col-md-3"><strong>Cliente:</strong> ${c.CLIENTE}</div>
        <div class="col-md-3"><strong>Fecha:</strong> ${cambiarFormatos(c.fecha_orden_entrega_presupuesto, 'fecha_hora')}</div>
        <div class="col-md-3"><strong>Estado:</strong> ${estado}</div>
      </div>
    </div>
    <div class="fact-detalle-seccion fact-seccion-productos">
      <h6><i class="fi fi-rs-box me-2"></i>Productos</h6>${prodHtml}
    </div>
    <div class="fact-detalle-seccion fact-seccion-servicios">
      <h6><i class="fi fi-rs-cogs me-2"></i>Servicios</h6>${servHtml}
    </div>
    <div class="fact-detalle-seccion fact-seccion-delivery">
      <h6><i class="fi fi-rs-truck-side me-2"></i>Delivery</h6>${delHtml}
    </div>
    <div class="fact-totales-panel">
      <div class="fact-total-row"><span>Subtotal Productos</span><span>$${subProd.toFixed(2)}</span></div>
      <div class="fact-total-row"><span>Subtotal Servicios</span><span>$${subServ.toFixed(2)}</span></div>
      ${costoDel > 0 ? `<div class="fact-total-row"><span>Delivery</span><span>$${costoDel.toFixed(2)}</span></div>` : ''}
      <div class="fact-total-row fact-total-grande">
        <span>TOTAL GENERAL</span>
        <span class="text-end">$${totalConIva.toFixed(2)} <br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${totalBs.toFixed(2)} (IVA ${ivaPorcentaje}%: $${montoIva.toFixed(2)})</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-2 px-2 py-1">
        <span class="text-success"><i class="fi fi-rs-money me-1"></i>Abonado</span>
        <span class="text-success text-end">$${parseFloat(c.total_pagado || 0).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(parseFloat(c.total_pagado || 0) * tasaBolivar).toFixed(2)}</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-1 px-2 py-1">
        <span class="text-danger fw-bold"><i class="fi fi-rs-exclamation me-1"></i>Restante</span>
        <span class="text-danger fw-bold text-end">$${((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva) * tasaBolivar).toFixed(2)}</small></span>
      </div>
    </div>
  `);

  $('#btnAnularOrdenModal').data('id', c.id_orden_entrega_presupuesto);
  if (c.status != 1) $('#btnAnularOrdenModal').hide();
  else $('#btnAnularOrdenModal').show();

  let botonesHtml = '';
  // Si la Orden no está pagada por completo, dejamos que puedan meterle un pago
  if (c.status == 1 || c.status == 3 || c.status == 12) {
    let cantRestante = (c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva;
    if (cantRestante > 0.01) {
      botonesHtml += `<button type="button" class="btn btn-success btnAbrirPagoDesdeDetalle" data-id="${c.id_orden_entrega_presupuesto}"><i class="fi fi-rs-credit-card me-1"></i>Añadir Pago</button> `;
    }
  }
  // Obviamente, solo mostramos "Despachar" si lleva delivery y todavía no ha salido
  if (data.delivery && c.estado_num != 3 && c.estado_num != 4 && c.estado_num != 5) {
    botonesHtml += `<button type="button" class="btn btn-info text-white btnDespacharOrden" data-id="${c.id_orden_entrega_presupuesto}"><i class="fi fi-rs-truck-side me-1"></i>Despachar</button> `;
  }
  $('#botonesExtraDetalle').html(botonesHtml);

  let modalDetalle = new bootstrap.Modal('.modalDetallesOrden');
  modalDetalle.show();

  // Si la Orden tiene coordenadas guardadas, le dibujamos un mapita chiquito para que vean a dónde fue
  if (tieneCoordsDel) {
    $('.modalDetallesOrden').off('shown.bs.modal.mapaDetalle').on('shown.bs.modal.mapaDetalle', function () {
      let contenedor = L.DomUtil.get('mapaDetalleDel');
      if (contenedor && contenedor._leaflet_id) return; // ya renderizado
      let mapaDet = L.map('mapaDetalleDel').setView([delLat, delLng], 15);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
      }).addTo(mapaDet);
      L.marker([delLat, delLng]).addTo(mapaDet).bindPopup('Ubicación de entrega').openPopup();
      const iconoJL = L.divIcon({ className: 'iconoHamburguesa' });
      L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJL }).addTo(mapaDet).bindPopup('J. LACRUZ C.A.');
      setTimeout(() => mapaDet.invalidateSize(), 200);
    });
  }
}
function resetFormOrden() {
  productosOrden = [];
  serviciosOrden = [];
  cachePresentaciones = null;
  cacheServicios = null;
  cacheRutas = null;
  // Limpiar campo de cliente (ahora es readonly, lo vaciamos directamente)
  $('#inputCedulaClienteOrden').val('');
  $('#nombreClienteOrden').val('');
  // Deshabilitar botón guardar hasta que se seleccione un cliente
  $('#btnGuardarOrden').prop('disabled', true);
  renderProductos();
  renderServicios();
  $('#chkDeliveryOrden').prop('checked', false);
  $('#seccionDeliveryOrden').addClass('d-none');
  $('#rowDeliveryResumen').hide();
  $('#costoDeliveryOrden').val('0.00');
  $('#badgeDelOrden').text('No');
  destruirMapaDelivery();
  $('#latDeliveryOrden, #lngDeliveryOrden, #idRutaDeliveryOrden').val('');
  $('#direccionDeliveryOrden, #rutaAsignadaDeliveryOrden').val('');
  $('#distanciaDeliveryOrden').val('0');
  calcularTotales();
  // Mostrar fecha actual
  mostrarFechaActual();
}
//#endregion [FUNCIONES DEL MODULO] FIN

//#region [EVENTOS] COMIENZO

// Inicialización DataTable
$(document).on('DOMContentLoaded', async function () {
  mostrarFechaActual();
  driverAyuda('ordenesEntregasPresupuestos', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Nueva Orden',
          description: 'Haz clic aquí para crear una nueva Orden. Podrás agregar productos, servicios y delivery.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Ordens',
          description: 'Aquí puedes ver todas las Ordens generadas, su estado y opciones para ver detalles, pagar o anular.',
          side: 'top'
        }
      },
      {
        element: '.botonVerOrden',
        popover: {
          title: 'Ver Detalles',
          description: 'Haz clic aquí para ver el detalle completo de la Orden, incluyendo productos, servicios y delivery.',
          side: 'left'
        }
      },
      {
        element: '.botonAnularOrden',
        popover: {
          title: 'Anular Orden',
          description: 'Anula la Orden y restaura el stock de los productos. Esta acción no se puede deshacer.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces el proceso de Ordención. Puedes crear Ordens con productos, servicios y delivery, y gestionar pagos.',
          side: 'top'
        }
      }
    ]
  });
  await cargarDatosFinancieros();
  await listarDataTable({
    encabezados: {
      'id_orden_entrega_presupuesto': 'N° Orden',
      'CLIENTE': 'CLIENTE',
      'fecha_orden_entrega_presupuesto': 'FECHA',
      'cant_productos': 'PRODUCTOS',
      'cant_servicios': 'SERVICIOS',
      'tiene_delivery': 'DELIVERY',
      'estado_dinamico': 'ESTADO',
    },
    informacionPe: {
      modulo: 'ordenesEntregasPresupuestos',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    },
    campoIdBtn: 'id_orden_entrega_presupuesto',
    infoTratoEspecial: {
      id_orden_entrega_presupuesto: (info) => `<span class="badge bg-light text-dark border fw-bold px-2">${info.valor}</span>`,
      fecha_orden_entrega_presupuesto: (info) => {
        if (!info.valor) return '-';
        return cambiarFormatos(info.valor, 'fecha_hora');
      },
      estado_dinamico: (info) => {
        let estadoNum = info.fila.estado_num;
        if (estadoNum == 5) return '<span class="badge bg-danger">Anulada</span>';
        if (estadoNum == 1) return '<span class="badge bg-success">Procesada y Pagada</span>';
        if (estadoNum == 2) return '<span class="badge bg-warning text-dark">Procesada y sin Pago</span>';
        if (estadoNum == 3) return '<span class="badge bg-success">Pagada y Despachada (Cancelada)</span>';
        if (estadoNum == 4) return '<span class="badge bg-info">Despachada y sin Pago</span>';
        return `<span class="badge bg-secondary">${info.valor}</span>`;
      },
      tiene_delivery: (info) => {
        return parseInt(info.valor) > 0
          ? '<span class="badge bg-info"><i class="fi fi-rs-truck-side me-1"></i>Sí</span>'
          : '<span class="badge bg-secondary">No</span>';
      },
    },
    botones: function (info) {
      let id = info.fila.id_orden_entrega_presupuesto;
      let btns = '<ul class="list-inline mb-0">';
      btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonVerOrden avtar avtar-xs btn-link-info"><i class="fi fi-rs-eye fs-3 iconoCentrado"></i></a></li>`;
      if (info.fila.status == 1) {
        btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonAnularOrden avtar avtar-xs btn-link-danger"><i class="fi fi-rs-ban fs-3 iconoCentrado"></i></a></li>`;
      }
      btns += '</ul>';
      return btns;
    }
  });

});

// Abrir modal registrar — resetear, mostrar fecha e inicializar DT de clientes
$('.modalRegistrar').on('show.bs.modal', function () {
  resetFormOrden();
  // Inicializamos el DataTable de clientes la primera vez que se abre el modal
  inicializarDtClientes();
});

// Botón Buscar Cliente: abre el modal de selección
$(document).off('click', '#btnBuscarClienteOrden').on('click', '#btnBuscarClienteOrden', function () {
  // Refrescar el DT para que siempre muestre datos actualizados
  if (dtClientesOrden) reiniciarDataTables(dtClientesOrden);
  $('#modalSelClienteOrden').modal('show');
});

// Botón Elegir del modal de clientes
$(document).off('click', '.btnElegirClienteOrden').on('click', '.btnElegirClienteOrden', function () {
  let id = $(this).data('id');
  let cliente = clientesSeleccionados[id];
  if (!cliente) return;

  $('#inputCedulaClienteOrden').val(cliente.rif_cedula_cliente);
  $('#nombreClienteOrden').val(cliente.razon_social_cliente || cliente.CLIENTE || '');
  $('#btnGuardarOrden').prop('disabled', false);

  $('#modalSelClienteOrden').modal('hide');
});

// Agregar producto
$(document).off('click', '#btnAgregarProductoOrden').on('click', '#btnAgregarProductoOrden', abrirSelectorProductos);

// Seleccionar producto del modal
$(document).off('click', '.selProdOrden').on('click', '.selProdOrden', function () {
  let idProducto = $(this).data('id');
  let existente = productosOrden.find(p => p.id_presentacion_producto == idProducto);

  if (existente) {
    existente.cantidad += 1;
  } else {
    productosOrden.push({
      id_presentacion_producto: idProducto,
      id_producto: $(this).data('id_producto'),
      nombre: $(this).data('nombre'),
      nombre_general: $(this).data('nombre_general'),
      precio: parseFloat($(this).data('precio')),
      cantidad: 1,
      stock: parseFloat($(this).data('stock')),
      capacidad: parseFloat($(this).data('capacidad') || 1),
      unidad: $(this).data('unidad'),
      presentacion: $(this).data('presentacion')
    });
  }
  renderProductos();
  $('#modalSelProdOrden').modal('hide');
});

// Cambiar cantidad producto
$(document).off('input', '.cantProdOrden').on('input', '.cantProdOrden', function () {
  let i = $(this).data('index');
  let val = parseInt($(this).val()) || 1;
  productosOrden[i].cantidad = val;
  calcularTotales();
});

// Quitar producto
$(document).off('click', '.quitarProdOrden').on('click', '.quitarProdOrden', function () {
  productosOrden.splice($(this).data('index'), 1);
  renderProductos();
});

// Agregar servicio
$(document).off('click', '#btnAgregarServicioOrden').on('click', '#btnAgregarServicioOrden', abrirSelectorServicios);

// Seleccionar servicio — si ya existe, solo sumamos +1 a la cantidad (como con productos)
$(document).off('click', '.selServOrden').on('click', '.selServOrden', async function () {
  let idServicio = $(this).data('id');
  let nombreServicio = $(this).data('nombre');
  let precioServicio = parseFloat($(this).data('precio'));

  // Si ya lo tenemos en la lista, solo le sumamos 1 a la cantidad y listo
  let existente = serviciosOrden.find(s => s.id_servicio == idServicio);
  if (existente) {
    existente.cantidad += 1;
    renderServicios();
    $('#modalSelServOrden').modal('hide');
    return;
  }

  let btn = $(this);
  let oldHtml = btn.html();
  btn.html('<i class="fi fi-rs-loading me-1 spinner-border spinner-border-sm"></i>Agregando').prop('disabled', true);

  let result = await pedirDatosAjax({
    modulo: 'servicios', noGuardarLocal: true,
    datosPe: { accion: 'seleccionarUno', id_servicio: idServicio }
  });

  btn.html(oldHtml).prop('disabled', false);

  let materiales = [];
  if (result && result.detallesExtra && result.detallesExtra.productos_servicio) {
    let presentaciones = await cargarPresentaciones();
    result.detallesExtra.productos_servicio.forEach(prod => {
      let pres = presentaciones.find(p => p.id_producto == prod.id_producto);
      if (pres) {
        let nombrePres = pres.nombre_producto || pres.nombre;
        if (pres.nombre_presentacion) nombrePres += ` (${pres.nombre_presentacion})`;
        materiales.push({
          id_producto: prod.id_producto,
          nombre: nombrePres,
          nombre_general: pres.nombre_producto || pres.nombre,
          unidad: pres.nombre_unidad_medida || '',
          cantidad_requerida: parseFloat(prod.cantidad_producto),
          stock: parseInt(pres.stock_producto ?? 0)
        });
      }
    });
  }

  // El precio Mapfre empieza en 0 porque el usuario lo ingresa manualmente al activar el toggle
  serviciosOrden.push({
    id_servicio: idServicio,
    nombre: nombreServicio,
    precio: precioServicio,
    cantidad: 1,
    es_mapfre: false,
    precio_mapfre: 0,
    materiales: materiales,
    fecha_ejecucion: '',
    latitud: '',
    longitud: '',
    id_ruta: '',
    direccion_texto: ''
  });

  renderServicios();
  $('#modalSelServOrden').modal('hide');
});

// Cambiar cantidad servicio — re-renderizamos todo para actualizar el encabezado (x{cantidad})
$(document).off('input', '.cantServOrden').on('input', '.cantServOrden', function () {
  let i = $(this).data('index');
  serviciosOrden[i].cantidad = parseInt($(this).val()) || 1;
  renderServicios();
});

// Toggle Mapfre
$(document).off('change', '.toggleMapfre').on('change', '.toggleMapfre', function () {
  let i = $(this).data('index');
  serviciosOrden[i].es_mapfre = $(this).is(':checked');
  renderServicios();
});

// Precio Mapfre
$(document).off('input', '.precioMapfreOrden').on('input', '.precioMapfreOrden', function () {
  let i = $(this).data('index');
  serviciosOrden[i].precio_mapfre = parseFloat($(this).val()) || 0;
  calcularTotales();
});

// Toggle materiales de servicio en detalle de Orden
$(document).off('click', '.btnToggleMat').on('click', '.btnToggleMat', function () {
  let idx = $(this).data('idx');
  let $row = $(`.fact-materiales-row[data-serv-idx="${idx}"]`);
  let $icon = $(this).find('i');
  $row.toggleClass('d-none');
  if ($row.hasClass('d-none')) {
    $icon.removeClass('fi-rs-minus-small').addClass('fi-rs-plus-small');
  } else {
    $icon.removeClass('fi-rs-plus-small').addClass('fi-rs-minus-small');
  }
});

// Toggle materiales de servicio en registro de Orden
$(document).off('click', '.btnToggleMatOrden').on('click', '.btnToggleMatOrden', function () {
  let idx = $(this).data('idx');
  let $row = $(`.fact-materiales-row-Orden[data-serv-idx="${idx}"]`);
  let $icon = $(this).find('i');
  $row.toggleClass('d-none');
  if ($row.hasClass('d-none')) {
    $icon.removeClass('fi-rs-minus-small').addClass('fi-rs-plus-small');
  } else {
    $icon.removeClass('fi-rs-plus-small').addClass('fi-rs-minus-small');
  }
});

// Quitar servicio
$(document).off('click', '.quitarServOrden').on('click', '.quitarServOrden', function () {
  serviciosOrden.splice($(this).data('index'), 1);
  renderServicios();
});

// Toggle delivery
$(document).off('change', '#chkDeliveryOrden').on('change', '#chkDeliveryOrden', async function () {
  if ($(this).is(':checked')) {
    $('#seccionDeliveryOrden').removeClass('d-none');
    $('#rowDeliveryResumen').show();
    $('#badgeDelOrden').text('Sí').removeClass('bg-secondary').addClass('bg-info');
    await cargarRepartidores();
    // Inicializar mapa tras un breve delay para que el DOM se renderice
    setTimeout(async () => {
      await inicializarMapaDelivery();
    }, 300);
  } else {
    $('#seccionDeliveryOrden').addClass('d-none');
    $('#rowDeliveryResumen').hide();
    $('#badgeDelOrden').text('No').removeClass('bg-info').addClass('bg-secondary');
    $('#costoDeliveryOrden').val('0.00');
    destruirMapaDelivery();
  }
  calcularTotales();
});

// Botón "Mi ubicación" del mapa delivery
$(document).off('click', '#btnMiUbicacionOrden').on('click', '#btnMiUbicacionOrden', async function () {
  if (!mapaDelivery) return;
  try {
    let ubicacion = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000 });
    });
    let lat = ubicacion.coords.latitude;
    let lng = ubicacion.coords.longitude;
    mapaDelivery.flyTo([lat, lng], 16, { animate: true, duration: 2 });
    if (marcadorDelivery) mapaDelivery.removeLayer(marcadorDelivery);
    marcadorDelivery = L.marker([lat, lng]).addTo(mapaDelivery);
    await actualizarDeliveryPorUbicacion({ lat, lng });
  } catch (e) {
    Swal.fire('Error', 'No se pudo obtener su ubicación', 'warning');
  }
});

// Enviar formulario manejado por el evento de #formOrden al final del archivo.

// Ver detalle
$(document).off('click', '.botonVerOrden').on('click', '.botonVerOrden', function (e) {
  e.preventDefault();
  verDetalleOrden($(this).attr('value'));
});

// Anular Orden (tabla)
$(document).off('click', '.botonAnularOrden').on('click', '.botonAnularOrden', async function (e) {
  e.preventDefault();
  let id = $(this).attr('value');
  let confirm = await Swal.fire({
    title: '¿Anular Orden?',
    text: `Se anulará la Orden ${id} y se restaurará el stock`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Sí, anular',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
      datosPe: { accion: 'anular', id_orden_entrega_presupuesto: id }
    });
    alertasAjax(resp);
    reiniciarDataTables();
  }
});

// Anular desde modal detalle
$(document).off('click', '#btnAnularOrdenModal').on('click', '#btnAnularOrdenModal', async function () {
  let id = $(this).data('id');
  let confirm = await Swal.fire({
    title: '¿Anular Orden?',
    text: `Se anulará la Orden ${id}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Sí, anular',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
      datosPe: { accion: 'anular', id_orden_entrega_presupuesto: id }
    });
    alertasAjax(resp);
    reiniciarDataTables();
    $('.modalDetallesOrden').modal('hide');
  }
});

// Validación en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'ordenesEntregasPresupuestos');
});

async function validarCedulaRepartidorOrden(cedula) {
  let input = $('#inputCedulaRepartidorOrden');
  let feedback = $('#feedbackRepartidorOrden');
  let hiddenInput = $('#selectRepartidorOrden');
  let icon = $('#iconRepartidorOrden');

  feedback.html('<span class="text-muted"><i class="fi fi-rs-loading me-1 spinner-border spinner-border-sm"></i>Buscando...</span>');
  icon.html('<i class="fi fi-rs-loading text-primary"></i>');

  let resultado = await pedirDatosAjax({
    modulo: 'repartidores',
    noGuardarLocal: true,
    datosPe: { accion: 'seleccionarUno', cedula_repartidor: cedula }
  });

  if (!resultado || resultado.icono === 'error' || Array.isArray(resultado)) {
    input.removeClass('is-valid').addClass('is-invalid').css({ 'border-color': '#dc3545', 'background-color': '#fffafa' });
    icon.html('<i class="fi fi-rs-cross-circle text-danger"></i>').addClass('border-danger');

    feedback.html(`
      <div class="text-danger mb-1 fw-medium">Repartidor no encontrado</div>
      <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" id="btnAbrirRegistroRepartidorOrden">
        <i class="fi fi-rs-user-add me-1"></i>Registrar ahora
      </button>
    `);
  } else {
    input.removeClass('is-invalid').addClass('is-valid').css({ 'border-color': '#198754', 'background-color': '#f8fff9' });
    icon.html('<i class="fi fi-rs-check-circle text-success"></i>').removeClass('border-danger').addClass('border-success');

    hiddenInput.val(resultado.cedula_repartidor);
    feedback.html(`<span class="text-success fw-medium"><i class="fi fi-rs-user-check me-1"></i>${resultado.nombre_repartidor} ${resultado.apellido_repartidor}</span>`);
  }
}

let timerRepartidor = null;

$(document).off('input', '#inputCedulaRepartidorOrden').on('input', '#inputCedulaRepartidorOrden', function () {
  // Formatear cédula: primera letra V, E, J, G, P seguida de números
  let val = $(this).val().toUpperCase().replace(/[^VEJGP0-9]/g, '');
  if (val.length > 0) {
    if (/^[0-9]/.test(val)) {
      val = 'V' + val; // Si empieza por número, asume V por defecto
    } else if (val.length > 1) {
      let letra = val.charAt(0);
      let numeros = val.substring(1).replace(/[^0-9]/g, '');
      val = letra + numeros;
    }
  }
  $(this).val(val);

  let cedula = val.trim();
  clearTimeout(timerRepartidor);

  $('#feedbackRepartidorOrden').html('');
  $('#selectRepartidorOrden').val('');
  $('#iconRepartidorOrden').html('<i class="fi fi-rs-motorcycle text-muted"></i>').removeClass('border-danger border-success');
  $(this).removeClass('is-valid is-invalid').css({ 'border-color': '', 'background-color': '' });

  // Solo buscamos si tiene al menos una letra y algunos números (ej: V1234)
  if (cedula.length < 5) return;

  timerRepartidor = setTimeout(() => {
    validarCedulaRepartidorOrden(cedula);
  }, 500);
});

// Modal para registrar repartidor
$(document).off('click', '#btnAbrirRegistroRepartidorOrden').on('click', '#btnAbrirRegistroRepartidorOrden', function () {
  let cedulaActual = $('#inputCedulaRepartidorOrden').val().trim();

  // Reseteamos el formulario
  let form = document.getElementById('formRegistroRepartidorOrden');
  if (form) {
    form.reset();
  }

  // Limpiamos estados de validación y feedback
  $('#formRegistroRepartidorOrden input').removeClass('is-valid is-invalid');
  $('#feedbackTelefonoRepartidorReg').html('');
  $('#btnGuardarRepartidorOrden').prop('disabled', true);

  // Seteamos la cédula actual
  $('#modalRegistroRepartidorOrden input[name="cedula_repartidor"]').val(cedulaActual);

  // Oscurecemos el modal de Orden para que resalte este
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  let modalEl = document.getElementById('modalRegistroRepartidorOrden');
  let modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  modal.show();
});

// Validación en tiempo real del formulario de registro de repartidor
function validarCamposRegistroRepartidor() {
  let nombreOk = $('#inputNombreRepartidorReg').hasClass('is-valid');
  let apellidoOk = $('#inputApellidoRepartidorReg').hasClass('is-valid');
  let telefonoOk = $('#inputTelefonoRepartidorReg').hasClass('is-valid');
  $('#btnGuardarRepartidorOrden').prop('disabled', !(nombreOk && apellidoOk && telefonoOk));
}

// Funciones de validación del formulario de registro de repartidores
function validarNombreRepartidor() {
  let el = $(this);
  el.val(el.val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''));
  let val = el.val().trim();
  if (val.length < 3) {
    el.removeClass('is-valid').addClass('is-invalid');
  } else {
    el.removeClass('is-invalid').addClass('is-valid');
  }
  validarCamposRegistroRepartidor();
}

function validarApellidoRepartidor() {
  let el = $(this);
  el.val(el.val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''));
  let val = el.val().trim();
  if (val.length < 3) {
    el.removeClass('is-valid').addClass('is-invalid');
  } else {
    el.removeClass('is-invalid').addClass('is-valid');
  }
  validarCamposRegistroRepartidor();
}

let timerTelefonoReg = null;
function validarTelefonoRepartidor() {
  let el = $(this);
  let val = el.val().replace(/\D/g, '');
  el.val(val); // Solo números
  let fb = $('#feedbackTelefonoRepartidorReg');
  clearTimeout(timerTelefonoReg);
  el.removeClass('is-valid is-invalid');
  fb.html('');

  if (val.length !== 11) {
    el.addClass('is-invalid');
    fb.html(`<small class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Debe tener 11 dígitos (${val.length}/11)</small>`);
    validarCamposRegistroRepartidor();
    return;
  }

  fb.html('<small class="text-muted"><i class="fi fi-rs-loading me-1"></i>Verificando...</small>');
  timerTelefonoReg = setTimeout(async () => {
    let resultado = await pedirDatosAjax({
      modulo: 'repartidores',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    });
    let existe = false;
    if (resultado) {
      let lista = Array.isArray(resultado) ? resultado : (resultado.data || []);
      existe = lista.some(r => r.telefono_repartidor === val);
    }
    if (existe) {
      $('#inputTelefonoRepartidorReg').removeClass('is-valid').addClass('is-invalid');
      fb.html('<small class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Este teléfono ya está registrado</small>');
    } else {
      $('#inputTelefonoRepartidorReg').removeClass('is-invalid').addClass('is-valid');
      fb.html('<small class="text-success"><i class="fi fi-rs-check-circle me-1"></i>Disponible</small>');
    }
    validarCamposRegistroRepartidor();
  }, 400);
}

// Delegaciones de eventos para validación en tiempo real del repartidor
$(document).off('input', '#inputNombreRepartidorReg').on('input', '#inputNombreRepartidorReg', validarNombreRepartidor);
$(document).off('input', '#inputApellidoRepartidorReg').on('input', '#inputApellidoRepartidorReg', validarApellidoRepartidor);
$(document).off('input', '#inputTelefonoRepartidorReg').on('input', '#inputTelefonoRepartidorReg', validarTelefonoRepartidor);

$(document).off('click', '#btnGuardarRepartidorOrden').on('click', '#btnGuardarRepartidorOrden', async function () {
  let form = document.getElementById('formRegistroRepartidorOrden');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  let btn = $(this);
  btn.prop('disabled', true).html('<i class="fi fi-rs-loading spinner-border spinner-border-sm me-1"></i>Guardando...');

  let formArray = $(form).serializeArray();
  let datosObj = { accion: 'registrar' };
  formArray.forEach(item => datosObj[item.name] = item.value);

  // El backend espera codigo_cedula_repartidor y cedula_repartidor separados
  let cedCompleta = datosObj.cedula_repartidor.toUpperCase();
  let regex = /^([VEJGP])(\d+)$/;
  let match = cedCompleta.match(regex);
  if (match) {
    datosObj.codigo_cedula_repartidor = match[1];
    datosObj.cedula_repartidor = match[2];
  } else {
    datosObj.codigo_cedula_repartidor = 'V';
  }

  // El backend espera prefijo_telefono_repartidor (4 dígitos) y telefono_repartidor (7 dígitos) separados
  let telefonoCompleto = (datosObj.telefono_repartidor || '').replace(/\D/g, '');
  if (telefonoCompleto.length === 11) {
    datosObj.prefijo_telefono_repartidor = telefonoCompleto.substring(0, 4);
    datosObj.telefono_repartidor = telefonoCompleto.substring(4);
  }

  let res = await pedirDatosAjax({
    modulo: 'repartidores',
    datosPe: datosObj
  });

  if (res && res.icono === 'success') {
    let cedulaRegistrada = $('#formRegistroRepartidorOrden input[name="cedula_repartidor"]').val().trim();
    $('#modalRegistroRepartidorOrden').modal('hide');
    Swal.fire({
      title: "Repartidor registrado",
      text: "Se ha registrado exitosamente.",
      icon: "success",
      confirmButtonText: "Continuar"
    }).then(() => {
      // Colocar la cédula en el input y validar automáticamente
      $('#inputCedulaRepartidorOrden').val(cedulaRegistrada);
      validarCedulaRepartidorOrden(cedulaRegistrada);
    });
  } else {
    btn.prop('disabled', false).html('Guardar');
    Swal.fire({
      title: res.titulo || "Error de registro",
      text: res.texto || "Ocurrió un error al intentar registrar el repartidor.",
      icon: res.icono || "error"
    });
  }
});

//#endregion [EVENTOS] FIN

//#region [LOGICA DE ESTADOS Y PAGOS]
// Date change for service
$(document).off('change', '.fechaServOrden').on('change', '.fechaServOrden', function () {
  let i = $(this).data('index');
  serviciosOrden[i].fecha_ejecucion = $(this).val();
  calcularTotales();
});

// Click map button for service
let mapaServicio = null;
let marcadorServicio = null;
let indiceServicioEditando = null;

$(document).off('click', '.btnUbicacionServOrden').on('click', '.btnUbicacionServOrden', function () {
  indiceServicioEditando = $(this).data('index');
  let s = serviciosOrden[indiceServicioEditando];

  $('#direccionServicioOrden').val(s.direccion_texto || '');
  $('#latServicioOrden').val(s.latitud || '');
  $('#lngServicioOrden').val(s.longitud || '');
  $('#idRutaServicioOrden').val(s.id_ruta || '');

  $('#modalUbicacionServicioOrden').modal('show');
});

// Al abrir el modal del servicio
$('#modalUbicacionServicioOrden').on('shown.bs.modal', function () {
  // Check if Delivery has coordinates to show copy button
  if ($('#chkDeliveryOrden').is(':checked') && $('#latDeliveryOrden').val() !== '') {
    $('#btnCopiarUbicacionDelivery').removeClass('d-none');
  } else {
    $('#btnCopiarUbicacionDelivery').addClass('d-none');
  }

  if (!mapaServicio) {
    mapaServicio = L.map('mapaUbicacionServicio').setView([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], 15);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapaServicio);

    // Marcador central (La empresa)
    const iconoJL = L.divIcon({ className: 'iconoHamburguesa' });
    L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJL })
      .addTo(mapaServicio)
      .bindPopup('J. LACRUZ C.A.')
      .openPopup();

    mapaServicio.on('click', async function (e) {
      if (marcadorServicio) {
        mapaServicio.removeLayer(marcadorServicio);
      }
      marcadorServicio = L.marker(e.latlng).addTo(mapaServicio);
      await actualizarUbicacionServicio(e.latlng);
    });
  } else {
    mapaServicio.invalidateSize();
  }

  let s = serviciosOrden[indiceServicioEditando];
  if (s && s.latitud && s.longitud) {
    let ll = L.latLng(s.latitud, s.longitud);
    if (marcadorServicio) mapaServicio.removeLayer(marcadorServicio);
    marcadorServicio = L.marker(ll).addTo(mapaServicio);
    mapaServicio.setView(ll, 15);
  }
});

async function actualizarUbicacionServicio(latlng) {
  $('#latServicioOrden').val(latlng.lat);
  $('#lngServicioOrden').val(latlng.lng);

  let distanciaKM = 0;
  try {
    let resp = await fetch(`https://api.tomtom.com/routing/1/calculateRoute/${CENTRO_JLACRUZ.lat},${CENTRO_JLACRUZ.lng}:${latlng.lat},${latlng.lng}/json?key=${TOMTOM_API_KEY}&travelMode=car`);
    let infoRuta = await resp.json();
    if (infoRuta.routes && infoRuta.routes[0]?.summary?.lengthInMeters) {
      distanciaKM = Math.ceil(infoRuta.routes[0].summary.lengthInMeters / 1000);
    } else throw new Error('Sin ruta');
  } catch (e) {
    let centroLL = L.latLng(CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng);
    let puntoLL = L.latLng(latlng.lat, latlng.lng);
    distanciaKM = Math.ceil(centroLL.distanceTo(puntoLL) / 1000);
  }
  $('#distanciaServicioOrden').val(distanciaKM);

  let rutas = await cargarRutas();
  let rutaEncontrada = rutas.find(r => {
    let min = parseFloat(r.minimo_km_ruta);
    let max = parseFloat(r.maximo_km_ruta);
    return distanciaKM >= min && distanciaKM <= max;
  });

  if (rutaEncontrada) {
    $('#rutaAsignadaServicioOrden').val(rutaEncontrada.nombre_ruta);
    $('#idRutaServicioOrden').val(rutaEncontrada.id_ruta);
  } else {
    $('#rutaAsignadaServicioOrden').val('Fuera de cobertura');
    $('#idRutaServicioOrden').val('');
  }

  try {
    let geoResp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`);
    let geoData = await geoResp.json();
    $('#direccionServicioOrden').val(geoData.display_name || `${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  } catch (e) {
    $('#direccionServicioOrden').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  }
}

$('#btnMiUbicacionServicio').click(function () {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(async pos => {
      let latlng = L.latLng(pos.coords.latitude, pos.coords.longitude);
      mapaServicio.setView(latlng, 15);
      if (marcadorServicio) mapaServicio.removeLayer(marcadorServicio);
      marcadorServicio = L.marker(latlng).addTo(mapaServicio);
      await actualizarUbicacionServicio(latlng);
    }, err => {
      Swal.fire('Error', 'No se pudo obtener su ubicación.', 'error');
    });
  } else {
    Swal.fire('Error', 'Su navegador no soporta geolocalización.', 'error');
  }
});

$('#btnConfirmarUbicacionServicio').click(function () {
  let idRuta = $('#idRutaServicioOrden').val();
  if (!idRuta) {
    Swal.fire('Atención', 'La ubicación seleccionada está fuera de cobertura. Intente con una más cercana.', 'warning');
    return;
  }

  let s = serviciosOrden[indiceServicioEditando];
  s.latitud = $('#latServicioOrden').val();
  s.longitud = $('#lngServicioOrden').val();
  s.id_ruta = idRuta;
  s.direccion_texto = $('#direccionServicioOrden').val();

  renderServicios();
  $('#modalUbicacionServicioOrden').modal('hide');
});

// Evento para copiar ubicación del delivery
$('#btnCopiarUbicacionDelivery').click(async function () {
  let lat = $('#latDeliveryOrden').val();
  let lng = $('#lngDeliveryOrden').val();
  if (lat && lng) {
    let latlng = L.latLng(lat, lng);
    mapaServicio.setView(latlng, 15);
    if (marcadorServicio) mapaServicio.removeLayer(marcadorServicio);
    marcadorServicio = L.marker(latlng).addTo(mapaServicio);
    await actualizarUbicacionServicio(latlng);
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Ubicación copiada del Delivery', showConfirmButton: false, timer: 1500 });
  }
});

// Evento para ver mapa del servicio en el modal de detalles
let mapaDetalleServicio = null;
let marcadorDetalleServicio = null;

$(document).off('click', '.btnVerMapaServicioDetalle').on('click', '.btnVerMapaServicioDetalle', function () {
  let lat = parseFloat($(this).data('lat'));
  let lng = parseFloat($(this).data('lng'));
  let nombre = $(this).data('nombre');

  // Escondemos momentáneamente el modal de detalles
  $('#modalDetallesOrden').modal('hide');

  $('#modalMapaDetalleServicio').off('shown.bs.modal').on('shown.bs.modal', function () {
    if (!mapaDetalleServicio) {
      mapaDetalleServicio = L.map('contenedorMapaDetalleServicio').setView([lat, lng], 15);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(mapaDetalleServicio);
      const iconoJL = L.divIcon({ className: 'iconoHamburguesa' });
      L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJL }).addTo(mapaDetalleServicio).bindPopup('J. LACRUZ C.A.');
    } else {
      mapaDetalleServicio.setView([lat, lng], 15);
      mapaDetalleServicio.invalidateSize();
    }

    if (marcadorDetalleServicio) mapaDetalleServicio.removeLayer(marcadorDetalleServicio);
    marcadorDetalleServicio = L.marker([lat, lng]).addTo(mapaDetalleServicio).bindPopup(`Servicio: ${nombre}`).openPopup();
  });

  // Al cerrar, volvemos a mostrar el modal de detalles
  $('#modalMapaDetalleServicio').off('hidden.bs.modal').on('hidden.bs.modal', function () {
    $('#modalDetallesOrden').modal('show');
  });

  $('#modalMapaDetalleServicio').modal('show');
});

$(document).off('click', '#btnGuardarOrden').on('click', '#btnGuardarOrden', function () {
  if (!$('#formOrden')[0].checkValidity()) {
    $('#formOrden')[0].reportValidity();
    return;
  }

  if ($('#chkDeliveryOrden').is(':checked')) {
    $('.btn-estado-orden[data-estado="3"]').show();
    $('.btn-estado-orden[data-estado="4"]').show();
  } else {
    $('.btn-estado-orden[data-estado="3"]').hide();
    $('.btn-estado-orden[data-estado="4"]').hide();
  }

  // Oscurecemos el modal de atrás para que se vea bien
  $('.modalRegistrar').addClass('fact-modal-dimmed');
  $('#modalEstadosOrden').modal('show');
});

// Si cierran el modal de estados sin elegir, restauramos el modal de atrás
$('#modalEstadosOrden').on('hidden.bs.modal', function () {
  $('.modalRegistrar').removeClass('fact-modal-dimmed');
});

$(document).off('click', '.btn-estado-orden').on('click', '.btn-estado-orden', async function () {
  let estado = $(this).data('estado');
  $('#estadoSeleccionadoOrden').val(estado);
  $('#modalEstadosOrden').modal('hide');
  $('.modalRegistrar').removeClass('fact-modal-dimmed');
  $('#formOrden').trigger('submit');
});

$(document).off('submit', '#formOrden').on('submit', '#formOrden', async function (e) {
  e.preventDefault();

  let cedula = $('#inputCedulaClienteOrden').val().trim();
  if (!cedula) {
    Swal.fire('Atención', 'Seleccione un cliente usando el botón "Buscar"', 'warning');
    return;
  }
  if (productosOrden.length === 0 && serviciosOrden.length === 0) {
    Swal.fire('Error', 'Agregue al menos un producto o servicio', 'warning');
    return;
  }


  // Validar que si el delivery está activado, tenga una ubicación seleccionada
  if ($('#chkDeliveryOrden').is(':checked') && !$('#idRutaDeliveryOrden').val()) {
    Swal.fire('Atención', 'Seleccione una ubicación en el mapa para asignar la ruta del Delivery.', 'warning');
    return;
  }

  // Validar que todos los servicios tengan su ubicación y fecha
  if (serviciosOrden.length > 0) {
    let servSinUbicacion = serviciosOrden.find(s => !s.id_ruta);
    let servSinFecha = serviciosOrden.find(s => !s.fecha_ejecucion);
    if (servSinUbicacion) {
      Swal.fire('Atención', `El servicio "${servSinUbicacion.nombre}" no tiene ubicación asignada. Haga clic en "Fija ubicación".`, 'warning');
      return;
    }
    if (servSinFecha) {
      Swal.fire('Atención', `El servicio "${servSinFecha.nombre}" no tiene fecha de ejecución.`, 'warning');
      return;
    }
  }

  let deliveryInfo = {};
  if ($('#chkDeliveryOrden').is(':checked') && $('#idRutaDeliveryOrden').val()) {
    let distanciaKM = $('#distanciaDeliveryOrden').val() || 1;
    deliveryInfo = {
      id_ruta: $('#idRutaDeliveryOrden').val(),
      cedula_repartidor: $('#selectRepartidorOrden').val() || null,
      latitud: $('#latDeliveryOrden').val() + '|' + distanciaKM,
      longitud: $('#lngDeliveryOrden').val()
    };
  }

  $('#btnGuardarOrden').html(`<i class="fi fi-rs-loading spinner-border spinner-border-sm me-1"></i>Guardando...`).prop('disabled', true);

  // Agregar inputs hidden con las matrices
  $(this).find('input[name="productos"], input[name="servicios"], input[name="delivery"], input[name="estadoSeleccionado"]').remove();
  $('<input>', { type: 'hidden', name: 'productos', value: JSON.stringify(productosOrden) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'servicios', value: JSON.stringify(serviciosOrden) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'delivery', value: JSON.stringify(deliveryInfo) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'estadoSeleccionado', value: $('#estadoSeleccionadoOrden').val() }).appendTo(this);

  let resp = await enviarFormulario({ formulario: this, modulo: 'ordenesEntregasPresupuestos' });

  $('#btnGuardarOrden').html(`<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar`).prop('disabled', false);

  if (resp && resp.icono === 'success') {
    $('.modalRegistrar').modal('hide');
    resetFormOrden();
    reiniciarDataTables();

    let estado = $('#estadoSeleccionadoOrden').val();
    if (estado == 1 || estado == 3) {
      abrirModalPagos(resp.id_orden_entrega_presupuesto);
    }
  }
});

async function abrirModalPagos(idOrden) {
  let [resMetodos, resMonedas, resBancos, resOrden] = await Promise.all([
    pedirDatosAjax({ modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true, datosPe: { accion: 'listarMetodosPago' } }),
    pedirDatosAjax({ modulo: 'monedas', noGuardarLocal: true, datosPe: { accion: 'listar' } }),
    pedirDatosAjax({ modulo: 'bancos', noGuardarLocal: true, datosPe: { accion: 'listar' } }),
    pedirDatosAjax({ modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true, datosPe: { accion: 'obtenerDetalle', id_orden_entrega_presupuesto: idOrden } })
  ]);

  if (!resOrden || !resOrden.cabecera) {
    Swal.fire('Error', 'No se pudo cargar la Orden', 'error');
    return;
  }

  let c = resOrden.cabecera;
  let totalOrden = parseFloat(c.total_orden || 0);
  let totalPagado = parseFloat(c.total_pagado || 0);
  let restante = (c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalOrden;

  let bsRate = 1;
  let monDolar = resMonedas.find(m => m.nombre_moneda.toUpperCase() === 'DÓLAR' || m.nombre_moneda.toUpperCase() === 'DOLAR');
  if (monDolar) bsRate = parseFloat(monDolar.valor_moneda);

  let metodosOpt = '<option value="">Seleccione...</option>' + resMetodos.filter(m => m.status == 1).map(m => `<option value="${m.id_metodo_pago}" data-moneda="${m.necesita_moneda}" data-emisor="${m.necesita_banco_emisor}" data-receptor="${m.necesita_banco_receptor}" data-ref="${m.necesita_referencia}">${m.nombre_metodo_pago}</option>`).join('');
  let monedasOpt = resMonedas.filter(m => m.status == 1).map(m => `<option value="${m.id_moneda}" data-valor="${m.valor_moneda}">${m.nombre_moneda}</option>`).join('');
  let bancosOpt = '<option value="">Seleccione...</option>' + (Array.isArray(resBancos) ? resBancos.filter(b => b.status == 1).map(b => `<option value="${b.id_banco}">${b.nombre_banco}</option>`).join('') : '');

  // Seteamos la información en el modal estático
  $('#modalPagosOrden #tituloModalPagosOrden').html(`<i class="fi fi-rs-credit-card me-2"></i>Detalles del Pago (Orden ${idOrden})`);
  $('#modalPagosOrden #pagoTotalPagar').text(`$${totalOrden.toFixed(2)}`);
  $('#modalPagosOrden #pagoCancelado').html(`$${totalPagado.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(totalPagado * bsRate).toFixed(2)}</small>`);
  $('#modalPagosOrden #pagoRestante').html(`$${restante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(restante * bsRate).toFixed(2)}</small>`);

  // Dejamos una sola fila de pago limpia y cargamos las opciones dinámicamente
  let firstRow = $('#modalPagosOrden #contenedorDetallesPago .fila-pago').first();
  firstRow.find('.btn-eliminar-pago').addClass('d-none');
  firstRow.find('.input-monto-pago').val('');
  firstRow.find('.input-referencia-pago').val('');
  firstRow.find('.sel-metodo-pago').html(metodosOpt).prop('selectedIndex', 0);
  firstRow.find('.sel-moneda-pago').html(monedasOpt).prop('selectedIndex', 0);
  firstRow.find('.sel-banco-emisor').html(bancosOpt).prop('selectedIndex', 0);
  firstRow.find('.sel-banco-receptor').html(bancosOpt).prop('selectedIndex', 0);
  firstRow.find('.col-moneda-pago, .col-banco-emisor, .col-banco-receptor, .col-referencia-pago').addClass('d-none');
  firstRow.find('.sel-metodo-pago').trigger('change');

  // Removemos cualquier otra fila de pago agregada previamente
  $('#modalPagosOrden #contenedorDetallesPago .fila-pago').not(':first').remove();

  let modalEl = document.getElementById('modalPagosOrden');
  let m = bootstrap.Modal.getOrCreateInstance(modalEl);
  m.show();

  let calcularRestanteModal = () => {
    let sumPagadoEnModal = 0;
    $('#modalPagosOrden .fila-pago').each(function () {
      let rawVal = $(this).find('.input-monto-pago').val() || '0';
      let valInput = parsearMonto(rawVal);
      let optMetodo = $(this).find('.sel-metodo-pago option:selected');
      let reqMoneda = optMetodo.data('moneda') == 1;

      let tasaMonedaSeleccionada = 1; // Por defecto Bolívares tiene valor 1

      if (reqMoneda) {
        let optMoneda = $(this).find('.sel-moneda-pago option:selected');
        if (optMoneda.val() !== '') {
          tasaMonedaSeleccionada = parseFloat(optMoneda.data('valor') || 1);
        }
      }

      // Formula universal: (monto * valor_de_su_moneda_en_bs) / valor_del_dolar_en_bs
      let montoEnDolares = (valInput * tasaMonedaSeleccionada) / bsRate;
      sumPagadoEnModal += montoEnDolares;
    });

    let nuevoRestante = restante - sumPagadoEnModal;
    let excede = nuevoRestante < -0.01;

    if (excede) {
      $('#pagoRestante').html(`<span class="text-danger">$${nuevoRestante.toFixed(2)}</span> <small class="text-danger fw-normal fs-6"> / Bs ${(nuevoRestante * bsRate).toFixed(2)} — ¡Excede el monto!</small>`);
      $('#btnConfirmarPago').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
    } else {
      $('#pagoRestante').html(`$${nuevoRestante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(nuevoRestante * bsRate).toFixed(2)}</small>`);
      $('#btnConfirmarPago').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    }
  };

  $(document).off('input', '#modalPagosOrden .input-monto-pago');
  $(document).off('change', '#modalPagosOrden .sel-moneda-pago');
  $(document).off('change', '#modalPagosOrden .sel-metodo-pago');
  $(document).off('click', '#btnAgregarOtroPago');
  $(document).off('click', '#modalPagosOrden .btn-eliminar-pago');
  $(document).off('click', '#btnConfirmarPago');

  $(document).on('input', '#modalPagosOrden .input-monto-pago', calcularRestanteModal);
  $(document).on('change', '#modalPagosOrden .sel-moneda-pago', calcularRestanteModal);

  $(document).on('change', '#modalPagosOrden .sel-metodo-pago', function () {
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
  });

  $(document).on('click', '#btnAgregarOtroPago', function () {
    let clone = $('#modalPagosOrden .fila-pago').first().clone();
    clone.find('.btn-eliminar-pago').removeClass('d-none');
    clone.find('.input-monto-pago').val('');
    clone.find('.sel-metodo-pago').prop('selectedIndex', 0);
    clone.find('.sel-moneda-pago').prop('selectedIndex', 0);
    clone.find('.sel-banco-emisor').prop('selectedIndex', 0);
    clone.find('.sel-banco-receptor').prop('selectedIndex', 0);
    clone.find('.input-referencia-pago').val('');
    clone.find('.col-moneda-pago, .col-banco-emisor, .col-banco-receptor, .col-referencia-pago').addClass('d-none');
    $('#modalPagosOrden #contenedorDetallesPago').append(clone);
  });

  $(document).on('click', '#modalPagosOrden .btn-eliminar-pago', function () {
    $(this).closest('.fila-pago').remove();
    calcularRestanteModal();
  });

  $(document).on('click', '#btnConfirmarPago', async function () {
    let pagosEnvio = [];
    let valido = true;
    $('#modalPagosOrden .fila-pago').each(function () {
      let optMetodo = $(this).find('.sel-metodo-pago option:selected');
      let reqMoneda = optMetodo.data('moneda') == 1;
      let reqEmisor = optMetodo.data('emisor') == 1;
      let reqReceptor = optMetodo.data('receptor') == 1;
      let reqRef = optMetodo.data('ref') == 1;

      let metodo = $(this).find('.sel-metodo-pago').val();
      let moneda = $(this).find('.sel-moneda-pago').val() || null;
      let bancoE = $(this).find('.sel-banco-emisor').val() || null;
      let bancoR = $(this).find('.sel-banco-receptor').val() || null;
      let ref = $(this).find('.input-referencia-pago').val() || null;
      let rawMonto = $(this).find('.input-monto-pago').val() || '0';
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
        pagosEnvio.push({
          id_metodo_pago: metodo,
          id_moneda: moneda,
          id_banco_emisor: bancoE,
          id_banco_receptor: bancoR,
          referencia_pago: ref,
          monto_pago: formatearMontoEnvio(monto)
        });
      }
    });

    if (!valido || pagosEnvio.length === 0) {
      Swal.fire('Atención', 'Campos faltantes o valor de moneda igual a 0', 'warning');
      return;
    }

    // Validar que la suma no exceda el restante
    let sumEnvio = 0;
    pagosEnvio.forEach(p => {
      let reqMoneda = true; // Por defecto lo tratamos como true para evitar nulos
      let optMetodo = $('#modalPagosOrden .fila-pago').eq(pagosEnvio.indexOf(p)).find('.sel-metodo-pago option:selected');
      if (optMetodo.length) reqMoneda = optMetodo.data('moneda') == 1;

      let tasaMonedaSeleccionada = 1;

      if (reqMoneda) {
        let optMoneda = $('#modalPagosOrden .fila-pago').eq(pagosEnvio.indexOf(p)).find('.sel-moneda-pago option:selected');
        if (optMoneda.val() !== '') {
          tasaMonedaSeleccionada = parseFloat(optMoneda.data('valor') || 1);
        }
      }

      let parsedMonto = parsearMonto(p.monto_pago);
      sumEnvio += (parsedMonto * tasaMonedaSeleccionada) / bsRate;
    });

    if (sumEnvio > restante + 0.01) {
      Swal.fire('Atención', 'El monto total de los pagos excede el restante de la Orden.', 'warning');
      return;
    }

    let fd = new FormData();
    fd.append('accion', 'registrarPago');
    fd.append('id_orden_entrega_presupuesto', idOrden);
    fd.append('pagos', JSON.stringify(pagosEnvio));

    let files = $('#inputComprobantesPago')[0].files;
    if (files.length > 3) {
      Swal.fire('Atención', 'Solo puedes subir un máximo de 3 comprobantes por Orden', 'warning');
      return;
    }
    for (let i = 0; i < files.length; i++) {
      let f = files[i];
      let ext = f.name.split('.').pop().toLowerCase();
      if (!['jpg', 'jpeg', 'png'].includes(ext)) {
        Swal.fire('Atención', 'El archivo ' + f.name + ' no tiene un formato válido (solo JPG y PNG)', 'warning');
        return;
      }
      fd.append('comprobantes[]', f);
    }

    let O = window.location.origin + new URL(import.meta.url).pathname.substring(0, new URL(import.meta.url).pathname.indexOf('/src/assets/')) + '/';

    let misHeaders = new Headers(encabezadosPeticiones);

    $('#spinnerCarga').removeClass('d-none');
    try {
      let req = await fetch(O + 'ordenesEntregasPresupuestos', {
        method: 'POST',
        headers: misHeaders,
        body: fd
      });
      let resp = await req.json();

      if (resp.icono === 'success') {
        m.hide();
        reiniciarDataTables();
        Swal.fire(resp.titulo, resp.texto, resp.icono);
      } else {
        Swal.fire(resp.titulo, resp.texto, resp.icono);
      }
    } catch (e) {
      console.error(e);
      Swal.fire('Error', 'Ocurrió un error al enviar los datos', 'error');
    } finally {
      $('#spinnerCarga').addClass('d-none');
    }
  });

  $(document).on('change', '#inputComprobantesPago', function () {
    if (this.files.length > 3) {
      Swal.fire('Atención', 'Solo puedes subir un máximo de 3 comprobantes', 'warning');
      this.value = '';
    }
  });
}

$(document).off('click', '.btnAbrirPagoDesdeDetalle').on('click', '.btnAbrirPagoDesdeDetalle', function () {
  let id = $(this).data('id');
  $('.modalDetallesOrden').modal('hide');
  setTimeout(() => abrirModalPagos(id), 400);
});

// Despachar Orden desde el detalle
$(document).off('click', '.btnDespacharOrden').on('click', '.btnDespacharOrden', async function () {
  let id = $(this).data('id');
  let confirm = await Swal.fire({
    title: '¿Despachar Orden?',
    html: `Se marcará la Orden <strong>${id}</strong> como despachada.<br><small class="text-muted">Si la Orden ya está pagada, pasará a "Pagada y Despachada". 
    De lo contrario, quedará como "Despachada y sin Pago".</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    confirmButtonText: '<i class="fi fi-rs-truck-side me-1"></i>Sí, despachar',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
      datosPe: { accion: 'despachar', id_orden_entrega_presupuesto: id }
    });
    if (resp && resp.icono === 'success') {
      $('.modalDetallesOrden').modal('hide');
      reiniciarDataTables();
    }
    Swal.fire(resp.titulo, resp.texto, resp.icono);
  }
});
//#endregion [LOGICA DE ESTADOS Y PAGOS] FIN

//Evento para validar en tiempo real y formatear moneda
$(document).off('input blur', '#modalPagosOrden input, #modalPagosOrden select')
$(document).on('input blur', '#modalPagosOrden input, #modalPagosOrden select', function () {
  validarEnTiempoReal(this, 'ordenesEntregasPresupuestos');
})
