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
let _modalRegistrarDesdePresupuesto = false;
let _idPresupuestoAProcesar = null; // ID del presupuesto que se está convirtiendo a orden real
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
  let subProd = productosOrden.reduce((s, p) => {
    let subBruto = p.cantidad * p.precio;
    let desc = parseFloat(p.descuento || 0);
    if (isNaN(desc) || desc < 0) desc = 0;
    if (desc > 100) desc = 100;
    let subNeto = subBruto * (1 - (desc / 100));
    return s + subNeto;
  }, 0);
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

  let montoIvaProd = subProd * (ivaActivo / 100);
  let subProdBs = subProd * tasaBolivar;
  let ivaProdBs = montoIvaProd * tasaBolivar;

  let montoIvaServ = subServ * (ivaActivo / 100);
  let subServBs = subServ * tasaBolivar;
  let ivaServBs = montoIvaServ * tasaBolivar;

  let montoIvaDel = delivery * (ivaActivo / 100);
  let delBs = delivery * tasaBolivar;
  let ivaDelBs = montoIvaDel * tasaBolivar;

  $('#subtotalProdOrden').html(`$${subProd.toFixed(2)} <span class="fw-normal text-muted">/ Bs ${subProdBs.toFixed(2)}</span> <span class="ms-1 text-primary">| IVA (${ivaActivo}%): $${montoIvaProd.toFixed(2)} (Bs ${ivaProdBs.toFixed(2)})</span>`);
  $('#subtotalServOrden').html(`$${subServ.toFixed(2)} <span class="fw-normal text-muted">/ Bs ${subServBs.toFixed(2)}</span> <span class="ms-1 text-primary">| IVA (${ivaActivo}%): $${montoIvaServ.toFixed(2)} (Bs ${ivaServBs.toFixed(2)})</span>`);
  $('#resumenProdOrden').html(`$${subProd.toFixed(2)} <span class="text-muted fs-6">/ Bs ${subProdBs.toFixed(2)}</span><small class="text-muted d-block">IVA (${ivaActivo}%): $${montoIvaProd.toFixed(2)} / Bs ${ivaProdBs.toFixed(2)}</small>`);
  $('#resumenServOrden').html(`$${subServ.toFixed(2)} <span class="text-muted fs-6">/ Bs ${subServBs.toFixed(2)}</span><small class="text-muted d-block">IVA (${ivaActivo}%): $${montoIvaServ.toFixed(2)} / Bs ${ivaServBs.toFixed(2)}</small>`);
  if (delivery > 0) {
    $('#resumenDeliveryOrden').html(`$${delivery.toFixed(2)} <span class="text-muted fs-6">/ Bs ${delBs.toFixed(2)}</span><small class="text-muted d-block">IVA (${ivaActivo}%): $${montoIvaDel.toFixed(2)} / Bs ${ivaDelBs.toFixed(2)}</small>`);
  } else {
    $('#resumenDeliveryOrden').text('$0.00');
  }
  $('#resumenTotalOrden').html(`$${total.toFixed(2)} <br><small class="text-muted fs-6">Bs ${totalBs.toFixed(2)} (IVA Total ${ivaActivo}%: $${montoIva.toFixed(2)} / Bs ${(montoIva * tasaBolivar).toFixed(2)})</small>`);
  $('#totalGeneralOrden').val(total.toFixed(2));
  $('#badgeProdOrden').text(productosOrden.length);
  $('#badgeServOrden').text(serviciosOrden.length);
  let esPresupuesto = $('#chkPresupuestoOrden').is(':checked');
  // Si el contenedor del check está oculto, es porque venimos del modo "Procesar Presupuesto"
  let esModoProcesamientoPresupuesto = $('#contenedorPresupuestoCheck').hasClass('d-none');
  // Los servicios incompletos (sin fecha o sin ubicación) SIEMPRE bloquean, incluso en presupuesto
  // Solo el errorStock se omite en modo presupuesto (el stock se descuenta cuando se procese)
  let permitirGuardar = esPresupuesto || esModoProcesamientoPresupuesto;
  $('#btnGuardarOrden').prop('disabled', total <= 0 || serviciosIncompletos || (!permitirGuardar && errorStock));
  // Cambiar texto del botón según modo
  if (esPresupuesto) {
    $('#btnGuardarOrden').html('<i class="fi fi-rs-document me-1"></i>Guardar como Presupuesto').css('background', 'linear-gradient(135deg, #7c3aed, #a855f7)');
  } else if (esModoProcesamientoPresupuesto) {
    $('#btnGuardarOrden').html('<i class="fi fi-rs-recycle me-1"></i>Procesar y Guardar').css('background', 'linear-gradient(135deg, #7c3aed, #a855f7)');
  } else {
    $('#btnGuardarOrden').html('<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar').css('background', 'linear-gradient(135deg, #4e54c8, #8f94fb)');
  }
}
function renderProductos() {
  let cont = $('#contenedorProductosOrden');
  if (productosOrden.length === 0) {
    cont.html(`<div class="fact-empty-state"><i class="fi fi-rs-box-open"></i><p>No hay productos agregados</p></div>`);
  } else {
    let html = `<table class="fact-items-table"><thead><tr>
      <th>Producto</th>
      <th>Precio</th>
      <th>Cant.</th>
      <th style="min-width: 220px;">Descuento</th>
      <th>Subtotal</th>
      <th></th>
    </tr></thead><tbody>`;
    productosOrden.forEach((p, i) => {
      let subBruto = p.cantidad * p.precio;
      let subBrutoBs = subBruto * tasaBolivar;

      let descPorc = parseFloat(p.descuento || 0);
      if (isNaN(descPorc) || descPorc < 0) descPorc = 0;
      if (descPorc > 100) descPorc = 100;
      p.descuento = descPorc;

      let tipoDesc = p.tipo_descuento || 'ninguno';
      let monedaDesc = p.moneda_descuento || '$';

      let montoDescUsd = subBruto * (descPorc / 100);
      let montoDescBs = montoDescUsd * tasaBolivar;
      let subNeto = subBruto - montoDescUsd;
      let subNetoBs = subNeto * tasaBolivar;

      let maxMonto = monedaDesc === 'Bs' ? subBrutoBs : subBruto;
      let valMonto = '';
      if (tipoDesc === 'monto') {
        if (p.monto_descuento_valor !== undefined && p.monto_descuento_valor !== null && p.monto_descuento_valor > 0) {
          valMonto = p.monto_descuento_valor;
        } else if (descPorc > 0) {
          valMonto = monedaDesc === 'Bs' ? montoDescBs.toFixed(2) : montoDescUsd.toFixed(2);
        }
      }

      let subtotalHtml = '';
      if (descPorc > 0) {
        subtotalHtml = `<td class="fw-bold text-primary col-subtotal-prod" data-index="${i}">
          <span class="fs-6 text-success">$${subNeto.toFixed(2)}</span>
          <small class="text-muted text-decoration-line-through d-block fw-normal" style="font-size:0.75rem;">$${subBruto.toFixed(2)}</small>
          <small class="text-muted fw-normal">Bs ${subNetoBs.toFixed(2)}</small>
        </td>`;
      } else {
        subtotalHtml = `<td class="fw-bold text-primary col-subtotal-prod" data-index="${i}">
          $${subBruto.toFixed(2)} <br><small class="text-muted fw-normal">Bs ${subBrutoBs.toFixed(2)}</small>
        </td>`;
      }

      html += `<tr class="fila-prod-orden" data-index="${i}">
        <td><span class="fact-item-nombre">${p.nombre}</span></td>
        <td>$${p.precio.toFixed(2)}</td>
        <td><input type="number" class="fact-qty-input cantProdOrden" data-index="${i}" value="${p.cantidad}" min="1" step="1"></td>
        <td>
          <div class="fact-desc-box p-1 rounded border bg-light" style="max-width: 250px;">
            <!-- Interruptores para las 2 opciones (nunca ambas) -->
            <div class="d-flex align-items-center justify-content-between mb-1 px-1">
              <div class="form-check form-switch m-0" title="Descuento por Porcentaje (%)">
                <input class="form-check-input switchDescPorc" type="checkbox" id="swDescPorc_${i}" data-index="${i}" ${tipoDesc === 'porcentaje' ? 'checked' : ''}>
                <label class="form-check-label fw-semibold" for="swDescPorc_${i}" style="font-size:0.75rem; cursor:pointer;">% Porc.</label>
              </div>
              <div class="form-check form-switch m-0" title="Descuento por Monto directo ($ o Bs)">
                <input class="form-check-input switchDescMonto" type="checkbox" id="swDescMonto_${i}" data-index="${i}" ${tipoDesc === 'monto' ? 'checked' : ''}>
                <label class="form-check-label fw-semibold" for="swDescMonto_${i}" style="font-size:0.75rem; cursor:pointer;">Monto</label>
              </div>
            </div>

            <!-- Opcion 1: Porcentaje -->
            <div class="contDescPorc ${tipoDesc === 'porcentaje' ? '' : 'd-none'}" id="contDescPorc_${i}">
              <div class="input-group input-group-sm">
                <input type="number" class="form-control form-control-sm text-end inputDescPorc" data-index="${i}" value="${descPorc > 0 ? descPorc : ''}" min="0" max="100" step="any" placeholder="0" ${tipoDesc === 'porcentaje' ? '' : 'disabled'}>
                <span class="input-group-text py-0 px-2 fw-bold text-muted">%</span>
              </div>
              <div class="desc-info-porc text-end mt-1" id="descInfoPorc_${i}" style="font-size:0.72rem;">
                ${descPorc > 0 ? `<span class="text-danger fw-semibold">-$${montoDescUsd.toFixed(2)}</span> <span class="text-muted">/ Bs ${montoDescBs.toFixed(2)}</span>` : '<span class="text-muted">Máx 100%</span>'}
              </div>
            </div>

            <!-- Opcion 2: Monto en $ o en Bs -->
            <div class="contDescMonto ${tipoDesc === 'monto' ? '' : 'd-none'}" id="contDescMonto_${i}">
              <div class="input-group input-group-sm">
                <button class="btn btn-outline-secondary dropdown-toggle btnMonedaDesc py-0 px-2 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:0.75rem;" id="btnMonedaDesc_${i}">
                  ${monedaDesc === 'Bs' ? 'Bs' : '$'}
                </button>
                <ul class="dropdown-menu dropdown-menu-sm shadow-sm py-1" style="min-width:70px;">
                  <li><a class="dropdown-item py-1 small selMonedaDesc" href="#" data-index="${i}" data-moneda="$">$ (USD)</a></li>
                  <li><a class="dropdown-item py-1 small selMonedaDesc" href="#" data-index="${i}" data-moneda="Bs">Bs (VES)</a></li>
                </ul>
                <input type="number" class="form-control form-control-sm text-end inputDescMonto" data-index="${i}" value="${valMonto}" min="0" step="any" placeholder="0.00" ${tipoDesc === 'monto' ? '' : 'disabled'}>
              </div>
              <div class="desc-info-monto text-end mt-1" id="descInfoMonto_${i}" style="font-size:0.72rem;">
                ${descPorc > 0 ? `<span class="text-primary fw-semibold">${descPorc.toFixed(2)}%</span> <span class="text-danger">(-$${montoDescUsd.toFixed(2)})</span>` : `<span class="text-muted">Máx: ${monedaDesc === 'Bs' ? 'Bs ' + maxMonto.toFixed(2) : '$' + maxMonto.toFixed(2)}</span>`}
              </div>
            </div>

            <!-- Estado sin descuento -->
            <div class="contDescNinguno text-center py-1 ${tipoDesc === 'porcentaje' || tipoDesc === 'monto' ? 'd-none' : ''}" id="contDescNinguno_${i}">
              <span class="text-muted" style="font-size:0.75rem;"><i class="fi fi-rs-cross-circle me-1"></i>Sin descuento</span>
            </div>
          </div>
        </td>
        ${subtotalHtml}
        <td><button type="button" class="fact-btn-borrar quitarProdOrden" data-index="${i}">&times;</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    cont.html(html);
  }
  calcularTotales();
}

function actualizarFilaDescuentoYTotales(idx, reRender = false) {
  if (reRender) {
    renderProductos();
    return;
  }
  let p = productosOrden[idx];
  if (!p) return;

  let subBruto = p.cantidad * p.precio;
  let subBrutoBs = subBruto * tasaBolivar;
  let descPorc = parseFloat(p.descuento || 0);
  if (isNaN(descPorc) || descPorc < 0) descPorc = 0;
  if (descPorc > 100) descPorc = 100;
  p.descuento = descPorc;

  let montoDescUsd = subBruto * (descPorc / 100);
  let montoDescBs = montoDescUsd * tasaBolivar;
  let subNeto = subBruto - montoDescUsd;
  let subNetoBs = subNeto * tasaBolivar;

  let monedaDesc = p.moneda_descuento || '$';
  let maxMonto = monedaDesc === 'Bs' ? subBrutoBs : subBruto;

  if (descPorc > 0) {
    $(`#descInfoPorc_${idx}`).html(`<span class="text-danger fw-semibold">-$${montoDescUsd.toFixed(2)}</span> <span class="text-muted">/ Bs ${montoDescBs.toFixed(2)}</span>`);
    $(`#descInfoMonto_${idx}`).html(`<span class="text-primary fw-semibold">${descPorc.toFixed(2)}%</span> <span class="text-danger">(-$${montoDescUsd.toFixed(2)})</span>`);
  } else {
    $(`#descInfoPorc_${idx}`).html('<span class="text-muted">Máx 100%</span>');
    $(`#descInfoMonto_${idx}`).html(`<span class="text-muted">Máx: ${monedaDesc === 'Bs' ? 'Bs ' + maxMonto.toFixed(2) : '$' + maxMonto.toFixed(2)}</span>`);
  }

  let subtotalTd = $(`.col-subtotal-prod[data-index="${idx}"]`);
  if (descPorc > 0) {
    subtotalTd.html(`
      <span class="fs-6 text-success">$${subNeto.toFixed(2)}</span>
      <small class="text-muted text-decoration-line-through d-block fw-normal" style="font-size:0.75rem;">$${subBruto.toFixed(2)}</small>
      <small class="text-muted fw-normal">Bs ${subNetoBs.toFixed(2)}</small>
    `);
  } else {
    subtotalTd.html(`$${subBruto.toFixed(2)} <br><small class="text-muted fw-normal">Bs ${subBrutoBs.toFixed(2)}</small>`);
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
      let subBs = (sub * tasaBolivar).toFixed(2);

      // Si es Mapfre y ya tiene un precio puesto, mostramos ese en verde y el original tachado
      let precioColHTML = s.es_mapfre && s.precio_mapfre > 0
        ? `<span class="text-success fw-bold">$${s.precio_mapfre.toFixed(2)}</span> <br><small class="text-muted text-decoration-line-through">$${s.precio.toFixed(2)}</small>`
        : `$${s.precio.toFixed(2)}`;

      let btnToggleMat = s.materiales && s.materiales.length > 0
        ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 btnToggleMatOrden" data-idx="${i}" title="Ver productos a descontar"><i class="fi fi-rs-plus-small"></i></button>`
        : '';

      // La fecha siempre es obligatoria (presupuesto o no), ya que es parte esencial de la cotización
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
            <input type="number" class="form-control form-control-sm precioMapfreOrden" data-index="${i}" value="${s.precio_mapfre > 0 ? s.precio_mapfre.toFixed(2) : ''}" min="0" step="any" placeholder="0.00">
          </div>` : ''}
        </td>
        <td>
          <input type="number" class="fact-qty-input cantServOrden" data-index="${i}" value="${s.cantidad}" min="1" step="1">
          ${btnUbicacionHtml}
        </td>
        <td class="fw-bold text-success">$${sub} <br><small class="text-muted fw-normal">Bs ${subBs}</small></td>
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

  let esPresupuesto = $('#chkPresupuestoOrden').is(':checked');

  // Agrupamos los ítems por id_producto para consolidar en 1 fila por producto
  let productosMap = {};
  items.forEach(p => {
    let idProd = p.id_producto;
    if (!productosMap[idProd]) {
      productosMap[idProd] = {
        id_producto: idProd,
        nombre: p.nombre_producto || p.nombre,
        stock: parseFloat(p.stock_producto ?? 0),
        stockMinimo: parseFloat(p.stock_minimo_producto ?? 0),
        unidad: p.nombre_unidad_medida || 'Unidades',
        presentaciones: []
      };
    }
    let capacidad = parseFloat(p.cantidad_pmp || 1);
    let precioBase = parseFloat(p.precio_producto || p.precio || 0);
    let precioPresentacion = precioBase * capacidad;
    productosMap[idProd].presentaciones.push({
      id_presentacion_producto: p.id_presentacion_producto,
      nombre_presentacion: p.nombre_presentacion || 'Unidad',
      capacidad: capacidad,
      precio_base: precioBase,
      precio: precioPresentacion
    });
  });

  let filas = Object.values(productosMap).map(p => {
    let stock = p.stock;
    let stockMinimo = p.stockMinimo;
    let unidad = p.unidad;
    let sinStock = stock <= 0;
    let esStockCritico = stock <= stockMinimo;
    let clasesFila = (!esPresupuesto && esStockCritico) ? 'table-danger' : '';
    let badgeStock = '';
    if (sinStock) {
      badgeStock = `<span class="badge bg-danger ms-1">Sin stock</span>`;
    } else if (esStockCritico) {
      badgeStock = `<span class="badge bg-warning text-dark ms-1"><i class="fi fi-rs-triangle-warning me-1"></i>Bajo</span>`;
    }

    // Selector de las presentaciones registradas para este producto
    let presOptions = p.presentaciones.map(pres => {
      return `<option value="${pres.id_presentacion_producto}" data-precio="${pres.precio}" data-capacidad="${pres.capacidad}" data-presentacion="${pres.nombre_presentacion}">
        ${pres.nombre_presentacion} (${pres.capacidad} ${unidad}) - $${pres.precio.toFixed(2)}
      </option>`;
    }).join('');

    let primerPres = p.presentaciones[0] || { id_presentacion_producto: '', precio: 0, capacidad: 1, nombre_presentacion: '' };
    let precioInicial = primerPres.precio;

    // En modo Presupuesto nunca se deshabilitan los botones de Agregar
    let btnDisabled = (!esPresupuesto && sinStock) ? 'disabled' : '';
    let btnTexto = (!esPresupuesto && sinStock) ? 'Agotado' : 'Agregar';

    return `<tr class="${clasesFila}">
      <td>
        <strong>${p.nombre}</strong>
      </td>
      <td>
        <span class="fw-semibold">${stock % 1 === 0 ? stock : stock.toFixed(2)} ${unidad}</span>
        ${badgeStock}
      </td>
      <td>
        <select class="form-select form-select-sm sel-pres-prod" style="min-width: 175px;">
          ${presOptions}
        </select>
      </td>
      <td class="col-precio-prod">
        <span class="precio-prod-val text-success fw-bold">$${precioInicial.toFixed(2)}</span>
      </td>
      <td>
        <button type="button" class="btn btn-sm text-white selProdOrden fact-purple-blue-grad"
          data-id_producto="${p.id_producto}"
          data-nombre_general="${p.nombre}"
          data-stock="${stock}"
          data-unidad="${unidad}"
          ${btnDisabled}>
          <i class="fi fi-rs-plus me-1"></i>${btnTexto}
        </button>
      </td>
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
        paginate: { previous: '<', next: '>' }
      },
      columnDefs: [
        { orderable: false, targets: [2, 4] }
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
  else if (c.estado_num == 20) estado = '<span class="badge" style="background: linear-gradient(135deg,#7c3aed,#a855f7);"><i class="fi fi-rs-document me-1"></i>Presupuesto</span>';
  else estado = `<span class="badge bg-secondary">${c.estado_dinamico || 'Activa'}</span>`;

  let tasaDetalle = parseFloat(c.tasa_cambio || c.valor_moneda || tasaBolivar) || tasaBolivar;
  let prodHtml = '';
  let subProd = 0;
  if (data.productos && data.productos.length) {
    prodHtml = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Presentación</th><th>Cant.</th><th>Precio</th><th>Descuento</th><th>Subtotal</th></tr></thead><tbody>';
    data.productos.forEach(p => {
      let capacidad = parseFloat(p.cantidad_pmp || 1);
      let precioPres = parseFloat(p.precio_presentacion !== undefined ? p.precio_presentacion : (parseFloat(p.precio_producto) * capacidad));
      let desc = parseFloat(p.descuento_producto || 0);
      let subSinDesc = parseFloat(p.cantidad_producto) * precioPres;
      let montoDesc = subSinDesc * (desc / 100);
      let sub = subSinDesc - montoDesc;
      subProd += sub;
      let subBs = sub * tasaDetalle;
      let precioBs = precioPres * tasaDetalle;
      let descBadge = desc > 0 
        ? `<span class="badge bg-success">${desc.toFixed(2)}% (-$${montoDesc.toFixed(2)})</span>` 
        : '<span class="badge bg-light text-dark border">0%</span>';
      prodHtml += `<tr>
        <td>${p.nombre_producto}</td>
        <td>${p.nombre_presentacion}</td>
        <td>${p.cantidad_producto}</td>
        <td>$${precioPres.toFixed(2)} <small class="text-muted d-block">Bs ${precioBs.toFixed(2)}</small></td>
        <td>${descBadge}</td>
        <td>$${sub.toFixed(2)} <small class="text-muted d-block">Bs ${subBs.toFixed(2)}</small></td>
      </tr>`;
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

      let subBs = sub * tasaDetalle;
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
        <td>$${sub.toFixed(2)} <small class="text-muted d-block">Bs ${subBs.toFixed(2)}</small></td>
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
      <div class="col-md-4"><strong>Costo:</strong> $${costoDel.toFixed(2)} <small class="text-muted">/ Bs ${(costoDel * tasaDetalle).toFixed(2)}</small></div>
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
  let totalBs = totalConIva * tasaDetalle;

  let montoIvaProdDet = subProd * (ivaPorcentaje / 100);
  let subProdBsDet = subProd * tasaDetalle;
  let ivaProdBsDet = montoIvaProdDet * tasaDetalle;

  let montoIvaServDet = subServ * (ivaPorcentaje / 100);
  let subServBsDet = subServ * tasaDetalle;
  let ivaServBsDet = montoIvaServDet * tasaDetalle;

  let montoIvaDelDet = costoDel * (ivaPorcentaje / 100);
  let delBsDet = costoDel * tasaDetalle;
  let ivaDelBsDet = montoIvaDelDet * tasaDetalle;

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
      <div class="fact-total-row">
        <span>Subtotal Productos</span>
        <span class="text-end">$${subProd.toFixed(2)} <span class="text-muted fs-6">/ Bs ${subProdBsDet.toFixed(2)}</span><br><small class="text-muted" style="font-weight: 500;">IVA (${ivaPorcentaje}%): $${montoIvaProdDet.toFixed(2)} / Bs ${ivaProdBsDet.toFixed(2)}</small></span>
      </div>
      <div class="fact-total-row">
        <span>Subtotal Servicios</span>
        <span class="text-end">$${subServ.toFixed(2)} <span class="text-muted fs-6">/ Bs ${subServBsDet.toFixed(2)}</span><br><small class="text-muted" style="font-weight: 500;">IVA (${ivaPorcentaje}%): $${montoIvaServDet.toFixed(2)} / Bs ${ivaServBsDet.toFixed(2)}</small></span>
      </div>
      ${costoDel > 0 ? `<div class="fact-total-row">
        <span>Delivery</span>
        <span class="text-end">$${costoDel.toFixed(2)} <span class="text-muted fs-6">/ Bs ${delBsDet.toFixed(2)}</span><br><small class="text-muted" style="font-weight: 500;">IVA (${ivaPorcentaje}%): $${montoIvaDelDet.toFixed(2)} / Bs ${ivaDelBsDet.toFixed(2)}</small></span>
      </div>` : ''}
      <div class="fact-total-row fact-total-grande">
        <span>TOTAL GENERAL</span>
        <span class="text-end">$${totalConIva.toFixed(2)} <br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${totalBs.toFixed(2)} (IVA Total ${ivaPorcentaje}%: $${montoIva.toFixed(2)} / Bs ${(montoIva * tasaDetalle).toFixed(2)})</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-2 px-2 py-1">
        <span class="text-success"><i class="fi fi-rs-money me-1"></i>Abonado</span>
        <span class="text-success text-end">$${parseFloat(c.total_pagado || 0).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(parseFloat(c.total_pagado || 0) * tasaDetalle).toFixed(2)}</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-1 px-2 py-1">
        <span class="text-danger fw-bold"><i class="fi fi-rs-exclamation me-1"></i>Restante</span>
        <span class="text-danger fw-bold text-end">$${((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva) * tasaDetalle).toFixed(2)}</small></span>
      </div>
    </div>
  `);

  $('#btnAnularOrdenModal').data('id', c.id_orden_entrega_presupuesto);
  if (c.status != 1) $('#btnAnularOrdenModal').hide();
  else $('#btnAnularOrdenModal').show();

  let botonesHtml = '';
  // Botón Procesar Presupuesto desde detalle
  if (c.status == 20) {
    botonesHtml += `<button type="button" class="btn text-white me-1 btnProcesarPresupuestoDetalle" 
      data-id="${c.id_orden_entrega_presupuesto}"
      style="background: linear-gradient(135deg,#7c3aed,#a855f7);">
      <i class="fi fi-rs-recycle me-1"></i>Procesar Presupuesto</button> `;
  }
  // Si la Orden no está pagada por completo, dejamos que puedan meterle un pago
  if (c.status == 1 || c.status == 3 || c.status == 12) {
    let cantRestante = (c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva;
    if (cantRestante > 0.01) {
      botonesHtml += `<button type="button" class="btn btn-success btnAbrirPagoDesdeDetalle" data-id="${c.id_orden_entrega_presupuesto}"><i class="fi fi-rs-credit-card me-1"></i>Añadir Pago</button> `;
    }
  }
  // Solo mostramos "Despachar" si lleva delivery, no es presupuesto y todavía no ha salido
  if (data.delivery && c.estado_num != 3 && c.estado_num != 4 && c.estado_num != 5 && c.estado_num != 20) {
    botonesHtml += `<button type="button" class="btn btn-info text-white btnDespacharOrden" data-id="${c.id_orden_entrega_presupuesto}"><i class="fi fi-rs-truck-side me-1"></i>Despachar</button> `;
  }

  // Botón Planilla de Retención IVA (Solo para órdenes pendientes de pago o ya pagadas; nunca en presupuestos ni anuladas)
  if (c.status != 20 && c.status != 2 && c.status != 0 && c.estado_num != 20 && c.estado_num != 5) {
    botonesHtml += `<button type="button" class="btn btn-outline-primary btnAbrirModalRetencion" data-id="${c.id_orden_entrega_presupuesto}"><i class="fi fi-rs-document-signed me-1"></i>Retención IVA</button> `;
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
  $('#btnGuardarOrden').prop('disabled', true)
    .html('<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar')
    .css('background', 'linear-gradient(135deg, #4e54c8, #8f94fb)');
  // Restaurar el checkbox de presupuesto
  $('#chkPresupuestoOrden').prop('checked', false);
  $('#contenedorPresupuestoCheck').removeClass('d-none');
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
  // Limpiar el ID de presupuesto en proceso al resetear
  _idPresupuestoAProcesar = null;
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
        if (estadoNum == 20) return '<span class="badge" style="background: linear-gradient(135deg,#7c3aed,#a855f7);"><i class="fi fi-rs-document me-1"></i>Presupuesto</span>';
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
      btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonImprimirPlanilla avtar avtar-xs btn-link-secondary" title="Imprimir Planilla"><i class="fi fi-rs-print fs-3 iconoCentrado"></i></a></li>`;
      if (info.fila.estado_num == 20) {
        btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonProcesarPresupuesto avtar avtar-xs" style="color:#7c3aed;"><i class="fi fi-rs-recycle fs-3 iconoCentrado" title="Procesar Presupuesto"></i></a></li>`;
      }
      if (info.fila.status == 1) {
        btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonAnularOrden avtar avtar-xs btn-link-danger"><i class="fi fi-rs-ban fs-3 iconoCentrado"></i></a></li>`;
      }
      btns += '</ul>';
      return btns;
    }
  });

});

// Abrir modal registrar — resetear, mostrar fecha e inicializar DT de clientes
// Si viene desde un "Procesar Presupuesto", la bandera _modalRegistrarDesdePresupuesto evita el reset
$('.modalRegistrar').on('show.bs.modal', function () {
  if (_modalRegistrarDesdePresupuesto) {
    // Solo inicializamos el DT de clientes; los datos ya fueron cargados por cargarPresupuestoEnModal
    _modalRegistrarDesdePresupuesto = false;
    inicializarDtClientes();
    return;
  }
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

// Botón para registrar un cliente no encontrado desde el modal de búsqueda de clientes
$(document).off('click', '#btnNuevoClienteDesdeOrden').on('click', '#btnNuevoClienteDesdeOrden', function () {
  let form = $('#formRegistrarClienteRapido');
  form[0].reset();
  form.find('.is-invalid, .error').removeClass('is-invalid error');
  form.find('.mensajeError, .invalid-feedback').remove();
  let modalRegCli = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRegistrarClienteOrden'));
  modalRegCli.show();
});

// Envío del formulario de registro rápido de cliente
$(document).off('submit', '#formRegistrarClienteRapido').on('submit', '#formRegistrarClienteRapido', async function (e) {
  e.preventDefault();
  let codRif = $('#regCliCodigoRif').val().trim();
  let numRif = $('#regCliRifCedula').val().trim();
  let razonSocial = $('#regCliRazonSocial').val().trim();
  let prefijoTel = $('#regCliPrefijoTelefono').val();
  let telefono = $('#regCliTelefono').val().trim();
  let correo = $('#regCliCorreo').val().trim();
  let direccion = $('#regCliDireccion').val().trim();

  if (!numRif || !razonSocial || !direccion) {
    Swal.fire('Campos requeridos', 'Por favor complete la cédula/RIF, razón social y dirección.', 'warning');
    return;
  }

  if (telefono && !prefijoTel) {
    Swal.fire('Prefijo requerido', 'Por favor seleccione el código de área / prefijo del teléfono.', 'warning');
    $('#regCliPrefijoTelefono').focus();
    return;
  }

  if (prefijoTel && !telefono) {
    Swal.fire('Teléfono incompleto', 'Por favor ingrese los 7 dígitos del número de teléfono o seleccione S/N.', 'warning');
    $('#regCliTelefono').focus();
    return;
  }

  if (telefono && telefono.length !== 7) {
    Swal.fire('Teléfono inválido', 'El número de teléfono debe tener 7 dígitos (sin incluir el prefijo).', 'warning');
    $('#regCliTelefono').focus();
    return;
  }

  if ($(this).find('.error').length > 0) {
    Swal.fire('Campos inválidos', 'Por favor corrija los datos marcados en rojo antes de guardar.', 'warning');
    return;
  }

  let cedulaCompleta = `${codRif}${numRif}`;
  let telefonoCompleto = prefijoTel ? `${prefijoTel}${telefono}` : telefono;

  let btn = $('#btnGuardarClienteRapido');
  btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

  try {
    let resp = await pedirDatosAjax({
      modulo: 'clientes',
      noGuardarLocal: true,
      datosPe: {
        accion: 'registrar',
        codigo_rif_cedula_cliente: codRif,
        rif_cedula_cliente: numRif,
        razon_social_cliente: razonSocial,
        prefijo_telefono_cliente: prefijoTel,
        telefono_cliente: telefono,
        correo_cliente: correo,
        direccion_cliente: direccion
      }
    });

    if (resp && resp.icono === 'success') {
      let modalRegCli = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarClienteOrden'));
      if (modalRegCli) modalRegCli.hide();

      // Guardamos la información en el caché local de clientes
      clientesSeleccionados[cedulaCompleta] = {
        rif_cedula_cliente: cedulaCompleta,
        razon_social_cliente: razonSocial,
        CLIENTE: razonSocial,
        telefono_cliente: telefonoCompleto,
        correo_cliente: correo
      };

      // Asignamos inmediatamente al formulario de la orden
      $('#inputCedulaClienteOrden').val(cedulaCompleta);
      $('#nombreClienteOrden').val(razonSocial);
      $('#btnGuardarOrden').prop('disabled', false);

      // Refrescamos la tabla de clientes si existe
      if (dtClientesOrden) reiniciarDataTables(dtClientesOrden);

      // Cerramos el modal de selección de clientes
      $('#modalSelClienteOrden').modal('hide');

      Swal.fire({
        icon: 'success',
        title: 'Cliente registrado',
        text: `El cliente ${razonSocial} (${cedulaCompleta}) ha sido registrado y seleccionado exitosamente.`
      });
    } else {
      Swal.fire({
        icon: resp.icono || 'error',
        title: resp.titulo || 'Error',
        text: resp.texto || 'No se pudo registrar el cliente'
      });
    }
  } catch (err) {
    Swal.fire('Error', 'Ocurrió un error al registrar el cliente', 'error');
  } finally {
    btn.prop('disabled', false).html('<i class="fi fi-rs-disk me-1"></i> Guardar y Seleccionar');
  }
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

// Cambio de presentación en el selector de productos
$(document).off('change', '#dtSelProdOrden .sel-pres-prod').on('change', '#dtSelProdOrden .sel-pres-prod', function () {
  let opt = $(this).find('option:selected');
  let precio = parseFloat(opt.data('precio') || 0);
  let fila = $(this).closest('tr');
  fila.find('.col-precio-prod .precio-prod-val').text(`$${precio.toFixed(2)}`);
});

// Agregar producto
$(document).off('click', '#btnAgregarProductoOrden').on('click', '#btnAgregarProductoOrden', abrirSelectorProductos);

// Seleccionar producto del modal con la presentación elegida
$(document).off('click', '.selProdOrden').on('click', '.selProdOrden', function () {
  let fila = $(this).closest('tr');
  let selPres = fila.find('.sel-pres-prod');
  let optPres = selPres.find('option:selected');
  let idPresentacionProducto = optPres.val();
  let precio = parseFloat(optPres.data('precio') || 0);
  let capacidad = parseFloat(optPres.data('capacidad') || 1);
  let presentacion = optPres.data('presentacion') || '';
  let idProducto = $(this).data('id_producto');
  let nombreGeneral = $(this).data('nombre_general');
  let stock = parseFloat($(this).data('stock') || 0);
  let unidad = $(this).data('unidad');
  let nombreCompleto = `${nombreGeneral} (${presentacion})`;

  let existente = productosOrden.find(p => p.id_presentacion_producto == idPresentacionProducto);

  if (existente) {
    existente.cantidad += 1;
    if (existente.tipo_descuento === 'monto') {
      let subBruto = existente.cantidad * existente.precio;
      let maxPermitido = existente.moneda_descuento === 'Bs' ? (subBruto * tasaBolivar) : subBruto;
      if (existente.monto_descuento_valor > maxPermitido) {
        existente.monto_descuento_valor = maxPermitido;
      }
      let porcentaje = maxPermitido > 0 ? (existente.monto_descuento_valor / maxPermitido) * 100 : 0;
      existente.descuento = parseFloat(porcentaje.toFixed(2));
    }
  } else {
    productosOrden.push({
      id_presentacion_producto: idPresentacionProducto,
      id_producto: idProducto,
      nombre: nombreCompleto,
      nombre_general: nombreGeneral,
      precio: precio,
      cantidad: 1,
      stock: stock,
      capacidad: capacidad,
      unidad: unidad,
      presentacion: presentacion,
      descuento: 0,
      tipo_descuento: 'ninguno',
      moneda_descuento: '$',
      monto_descuento_valor: 0
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

  let p = productosOrden[i];
  if (p.tipo_descuento === 'monto') {
    let subBruto = p.cantidad * p.precio;
    let maxPermitido = p.moneda_descuento === 'Bs' ? (subBruto * tasaBolivar) : subBruto;
    if (p.monto_descuento_valor > maxPermitido) {
      p.monto_descuento_valor = maxPermitido;
      $(`.inputDescMonto[data-index="${i}"]`).val(maxPermitido.toFixed(2));
    }
    let porcentaje = maxPermitido > 0 ? (p.monto_descuento_valor / maxPermitido) * 100 : 0;
    p.descuento = parseFloat(porcentaje.toFixed(2));
  }
  actualizarFilaDescuentoYTotales(i, false);
});

// Switch Descuento Porcentaje
$(document).off('change', '.switchDescPorc').on('change', '.switchDescPorc', function () {
  let idx = $(this).data('index');
  let isChecked = $(this).is(':checked');

  // Borrar lo que se colocó y regresar a 0 para no tener conflictos
  productosOrden[idx].descuento = 0;
  productosOrden[idx].monto_descuento_valor = 0;
  $(`#contDescPorc_${idx} input`).val('');
  $(`#contDescMonto_${idx} input`).val('');

  if (isChecked) {
    productosOrden[idx].tipo_descuento = 'porcentaje';
    $(`#swDescMonto_${idx}`).prop('checked', false);
    $(`#contDescMonto_${idx}`).addClass('d-none');
    $(`#contDescMonto_${idx} input`).prop('disabled', true);
    $(`#contDescPorc_${idx}`).removeClass('d-none');
    $(`#contDescPorc_${idx} input`).prop('disabled', false).focus();
    $(`#contDescNinguno_${idx}`).addClass('d-none');
  } else {
    productosOrden[idx].tipo_descuento = 'ninguno';
    $(`#contDescPorc_${idx}`).addClass('d-none');
    $(`#contDescPorc_${idx} input`).prop('disabled', true);
    $(`#contDescMonto_${idx} input`).prop('disabled', true);
    $(`#contDescNinguno_${idx}`).removeClass('d-none');
  }
  actualizarFilaDescuentoYTotales(idx);
});

// Switch Descuento Monto
$(document).off('change', '.switchDescMonto').on('change', '.switchDescMonto', function () {
  let idx = $(this).data('index');
  let isChecked = $(this).is(':checked');

  // Borrar lo que se colocó y regresar a 0 para no tener conflictos
  productosOrden[idx].descuento = 0;
  productosOrden[idx].monto_descuento_valor = 0;
  $(`#contDescPorc_${idx} input`).val('');
  $(`#contDescMonto_${idx} input`).val('');

  if (isChecked) {
    productosOrden[idx].tipo_descuento = 'monto';
    if (!productosOrden[idx].moneda_descuento) productosOrden[idx].moneda_descuento = '$';
    $(`#swDescPorc_${idx}`).prop('checked', false);
    $(`#contDescPorc_${idx}`).addClass('d-none');
    $(`#contDescPorc_${idx} input`).prop('disabled', true);
    $(`#contDescMonto_${idx}`).removeClass('d-none');
    $(`#contDescMonto_${idx} input`).prop('disabled', false).focus();
    $(`#contDescNinguno_${idx}`).addClass('d-none');
  } else {
    productosOrden[idx].tipo_descuento = 'ninguno';
    $(`#contDescMonto_${idx}`).addClass('d-none');
    $(`#contDescMonto_${idx} input`).prop('disabled', true);
    $(`#contDescPorc_${idx} input`).prop('disabled', true);
    $(`#contDescNinguno_${idx}`).removeClass('d-none');
  }
  actualizarFilaDescuentoYTotales(idx);
});

// Seleccionar moneda en descuento ($ o Bs)
$(document).off('click', '.selMonedaDesc').on('click', '.selMonedaDesc', function (e) {
  e.preventDefault();
  let idx = $(this).data('index');
  let moneda = $(this).data('moneda');
  productosOrden[idx].moneda_descuento = moneda;
  // Al cambiar moneda también se borra y regresa a 0 para evitar conflictos
  productosOrden[idx].descuento = 0;
  productosOrden[idx].monto_descuento_valor = 0;
  renderProductos();
  $(`#contDescMonto_${idx} input`).focus();
});

// Input de Descuento Porcentaje
$(document).off('input', '.inputDescPorc').on('input', '.inputDescPorc', function () {
  let idx = $(this).data('index');
  let rawVal = $(this).val();
  if (rawVal === '' || rawVal === null) {
    productosOrden[idx].descuento = 0;
    productosOrden[idx].monto_descuento_valor = 0;
    actualizarFilaDescuentoYTotales(idx, false);
    return;
  }
  let val = parseFloat(rawVal);
  if (isNaN(val) || val < 0) {
    val = 0;
  }
  if (val > 100) {
    val = 100;
    $(this).val(100);
  }
  productosOrden[idx].descuento = val;
  let p = productosOrden[idx];
  let subBruto = p.cantidad * p.precio;
  let montoUsd = subBruto * (val / 100);
  p.monto_descuento_valor = p.moneda_descuento === 'Bs' ? parseFloat((montoUsd * tasaBolivar).toFixed(2)) : parseFloat(montoUsd.toFixed(2));
  actualizarFilaDescuentoYTotales(idx, false);
});

// Input de Descuento Monto
$(document).off('input', '.inputDescMonto').on('input', '.inputDescMonto', function () {
  let idx = $(this).data('index');
  let rawVal = $(this).val();
  if (rawVal === '' || rawVal === null) {
    productosOrden[idx].descuento = 0;
    productosOrden[idx].monto_descuento_valor = 0;
    actualizarFilaDescuentoYTotales(idx, false);
    return;
  }
  let val = parseFloat(rawVal);
  let p = productosOrden[idx];
  let subBruto = p.cantidad * p.precio;
  let maxPermitido = p.moneda_descuento === 'Bs' ? (subBruto * tasaBolivar) : subBruto;

  if (isNaN(val) || val < 0) {
    val = 0;
  }
  if (val > maxPermitido) {
    val = maxPermitido;
    $(this).val(val.toFixed(2));
  }
  p.monto_descuento_valor = val;

  let porcentaje = 0;
  if (maxPermitido > 0) {
    porcentaje = (val / maxPermitido) * 100;
  }
  if (porcentaje > 100) porcentaje = 100;
  p.descuento = parseFloat(porcentaje.toFixed(2));
  actualizarFilaDescuentoYTotales(idx, false);
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

// Toggle del switch Presupuesto — recalcula totales para actualizar botón y texto
$(document).off('change', '#chkPresupuestoOrden').on('change', '#chkPresupuestoOrden', function () {
  let isChecked = $(this).is(':checked');
  if (!isChecked) {
    // Al desmarcar presupuesto, verificar si hay productos agregados que no poseen stock
    let productosSinStock = [];
    let consumoPorProducto = {};
    productosOrden.forEach(p => {
      let id = p.id_producto;
      if (!consumoPorProducto[id]) {
        consumoPorProducto[id] = { nombre: p.nombre_general || p.nombre, vol: 0, stock: p.stock, unidad: p.unidad };
      }
      consumoPorProducto[id].vol += (p.cantidad * p.capacidad);
    });
    Object.keys(consumoPorProducto).forEach(id => {
      let c = consumoPorProducto[id];
      if (c.vol > c.stock) {
        productosSinStock.push(`<b>${c.nombre}</b>: Stock actual ${c.stock} ${c.unidad}, requiere ${c.vol % 1 === 0 ? c.vol : c.vol.toFixed(2)} ${c.unidad}`);
      }
    });

    if (productosSinStock.length > 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Productos sin Stock Suficiente',
        html: `Al cambiar a Orden de Entrega se valida el inventario disponible.<br>Los siguientes productos no poseen stock suficiente:<br><ul class="text-start mt-2"><li>${productosSinStock.join('</li><li>')}</li></ul>`,
        confirmButtonColor: '#3085d6'
      });
    }
  }
  calcularTotales();
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

// Procesar Presupuesto (abrir modal de registro precargado)
$(document).off('click', '.botonProcesarPresupuesto').on('click', '.botonProcesarPresupuesto', async function (e) {
  e.preventDefault();
  let id = $(this).attr('value');

  let confirmResult = await Swal.fire({
    title: '¿Procesar Presupuesto?',
    html: `¿Estás seguro de procesar el presupuesto <strong>${id}</strong>?<br>
           <small class="text-muted">Se abrirá el formulario con los datos del presupuesto para que puedas verificar el stock y guardarlo como orden real.</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#7c3aed',
    confirmButtonText: '<i class="fi fi-rs-recycle me-1"></i>Sí, procesar',
    cancelButtonText: 'Cancelar'
  });

  if (confirmResult.isConfirmed) {
    await cargarPresupuestoEnModal(id);
  }
});

// Carga los datos de un presupuesto en el modal de registro para procesarlo como orden real
async function cargarPresupuestoEnModal(idOrden) {
  Swal.fire({ title: 'Cargando presupuesto...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

  let [data, presentaciones] = await Promise.all([
    pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
      datosPe: { accion: 'obtenerDetalle', id_orden_entrega_presupuesto: idOrden }
    }),
    cargarPresentaciones()
  ]);

  Swal.close();

  if (!data || !data.cabecera) {
    Swal.fire('Error', 'No se pudo cargar el presupuesto', 'error');
    return;
  }

  let c = data.cabecera;

  // Reiniciar el formulario limpio
  resetFormOrden();

  // Prellenar cliente
  $('#inputCedulaClienteOrden').val(c.rif_cedula_cliente);
  $('#nombreClienteOrden').val(c.CLIENTE || '');

  // Reconstruir productosOrden desde el caché de presentaciones
  productosOrden = [];
  if (data.productos && data.productos.length) {
    data.productos.forEach(p => {
      let pres = presentaciones.find(pp => String(pp.id_presentacion_producto) === String(p.id_presentacion_producto));
      if (pres) {
        let nombreCompleto = pres.nombre_producto || '';
        if (pres.nombre_presentacion) nombreCompleto += ` (${pres.nombre_presentacion})`;
        let capacidad = parseFloat(pres.cantidad_pmp || 1);
        let precioBase = parseFloat(pres.precio_producto || p.precio_producto || 0);
        let precioPres = precioBase * capacidad;
        let desc = parseFloat(p.descuento_producto || 0);
        productosOrden.push({
          id_producto_factura: p.id_producto_factura || null,
          id_presentacion_producto: p.id_presentacion_producto,
          id_producto: pres.id_producto,
          nombre: nombreCompleto,
          nombre_general: pres.nombre_producto || '',
          precio: precioPres,
          cantidad: parseInt(p.cantidad_producto),
          stock: parseFloat(pres.stock_producto ?? 0),
          capacidad: capacidad,
          unidad: pres.nombre_unidad_medida || 'Unidades',
          presentacion: pres.nombre_presentacion || '',
          descuento: desc,
          tipo_descuento: desc > 0 ? 'porcentaje' : 'ninguno',
          moneda_descuento: '$',
          monto_descuento_valor: 0
        });
      }
    });
  }

  // Reconstruir serviciosOrden
  serviciosOrden = [];
  if (data.servicios && data.servicios.length) {
    data.servicios.forEach(s => {
      if (s.status == 4) return; // skip cancelados
      let materiales = [];
      if (s.materiales && s.materiales.length) {
        s.materiales.forEach(m => {
          let presM = presentaciones.find(pp => String(pp.id_producto) === String(m.id_producto));
          materiales.push({
            id_producto: m.id_producto,
            nombre: m.nombre_producto || '',
            nombre_general: m.nombre_producto || '',
            unidad: m.nombre_unidad_medida || '',
            cantidad_requerida: parseFloat(m.cantidad_producto),
            stock: presM ? parseFloat(presM.stock_producto ?? 0) : 0
          });
        });
      }
      serviciosOrden.push({
        id_servicio: s.id_servicio,
        nombre: s.nombre_servicio || '',
        precio: parseFloat(s.precio_servicio || 0),
        cantidad: parseInt(s.cantidad_servicio || 1),
        es_mapfre: s.es_precio_mapfre == 1,
        precio_mapfre: parseFloat(s.precio_servicio_mapfre || 0),
        materiales: materiales,
        fecha_ejecucion: s.fecha_ejecucion ? s.fecha_ejecucion.substring(0, 10) : '',
        latitud: s.coordenada_latitud || '',
        longitud: s.coordenada_longitud || '',
        id_ruta: s.id_ruta || '',
        direccion_texto: ''
      });
    });
  }

  // Ocultar el switch de Presupuesto (no tendría sentido al procesar uno existente)
  $('#chkPresupuestoOrden').prop('checked', false);

  // Habilitar el botón guardar (ya tenemos cliente)
  $('#btnGuardarOrden').prop('disabled', false);

  // Renderizar todo
  renderProductos();
  renderServicios();

  // Ocultar el checkbox de Presupuesto DESPUÉS de renderizar (para que calcularTotales no lo muestre de nuevo)
  $('#contenedorPresupuestoCheck').addClass('d-none');

  // Notificar si tenía delivery para que el usuario lo reconfigure
  if (data.delivery) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'info',
      title: 'Este presupuesto tenía Delivery. Actívalo y configura la ubicación si lo necesitas.',
      showConfirmButton: false,
      timer: 5000
    });
  }

  // Abrir el modal de registro (con la bandera activa para no resetear el form)
  _idPresupuestoAProcesar = idOrden; // Guardamos el ID para que el submit llame a procesarPresupuesto
  _modalRegistrarDesdePresupuesto = true;
  let modal = bootstrap.Modal.getOrCreateInstance(document.querySelector('.modalRegistrar'));
  modal.show();
}

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

// Procesar Presupuesto desde el modal de detalle
$(document).off('click', '.btnProcesarPresupuestoDetalle').on('click', '.btnProcesarPresupuestoDetalle', async function () {
  let id = $(this).data('id');
  let confirmResult = await Swal.fire({
    title: '¿Procesar Presupuesto?',
    html: `¿Estás seguro de procesar el presupuesto <strong>${id}</strong>?<br>
           <small class="text-muted">Se abrirá el formulario con los datos del presupuesto para verificar el stock y guardarlo como orden real.</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#7c3aed',
    confirmButtonText: '<i class="fi fi-rs-recycle me-1"></i>Sí, procesar',
    cancelButtonText: 'Cancelar'
  });
  if (confirmResult.isConfirmed) {
    $('.modalDetallesOrden').modal('hide');
    setTimeout(() => cargarPresupuestoEnModal(id), 400);
  }
});

// Validación en tiempo real
$(document).off('input change', '.validar input, .validar select, .validar textarea');
$(document).on('input change', '.validar input, .validar select, .validar textarea', function () {
  let form = $(this).closest('form');
  let modulo = 'ordenesEntregasPresupuestos';
  if (form.attr('id') === 'formRegistrarClienteRapido') {
    modulo = 'clientes';
  }
  validarEnTiempoReal(this, modulo);
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
  // Solo se aceptan dígitos; la letra viene del select
  let val = $(this).val().replace(/\D/g, '');
  $(this).val(val);

  let letra = $('#selectCodigoRepartidorOrden').val() || 'V';
  let cedula = letra + val;

  clearTimeout(timerRepartidor);

  $('#feedbackRepartidorOrden').html('');
  $('#selectRepartidorOrden').val('');
  $('#iconRepartidorOrden').html('<i class="fi fi-rs-motorcycle text-muted"></i>').removeClass('border-danger border-success');
  $(this).removeClass('is-valid is-invalid').css({ 'border-color': '', 'background-color': '' });

  // Solo buscamos si tiene al menos 4 dígitos
  if (val.length < 4) return;

  timerRepartidor = setTimeout(() => {
    validarCedulaRepartidorOrden(cedula);
  }, 500);
});

// También disparar búsqueda al cambiar el select de prefijo de cédula
$(document).off('change', '#selectCodigoRepartidorOrden').on('change', '#selectCodigoRepartidorOrden', function () {
  let val = $('#inputCedulaRepartidorOrden').val().trim();
  if (val.length < 4) return;
  let letra = $(this).val() || 'V';
  let cedula = letra + val;
  clearTimeout(timerRepartidor);
  $('#feedbackRepartidorOrden').html('');
  $('#selectRepartidorOrden').val('');
  $('#iconRepartidorOrden').html('<i class="fi fi-rs-motorcycle text-muted"></i>').removeClass('border-danger border-success');
  $('#inputCedulaRepartidorOrden').removeClass('is-valid is-invalid').css({ 'border-color': '', 'background-color': '' });
  timerRepartidor = setTimeout(() => {
    validarCedulaRepartidorOrden(cedula);
  }, 500);
});

// Modal para registrar repartidor
$(document).off('click', '#btnAbrirRegistroRepartidorOrden').on('click', '#btnAbrirRegistroRepartidorOrden', function () {
  // Cedula completa = prefijo del select + valor del input
  let letraCed = $('#selectCodigoRepartidorOrden').val() || 'V';
  let cedulaActual = letraCed + $('#inputCedulaRepartidorOrden').val().trim();

  // Reseteamos el formulario
  let form = document.getElementById('formRegistroRepartidorOrden');
  if (form) {
    form.reset();
  }

  // Limpiamos estados de validación y feedback
  $('#formRegistroRepartidorOrden input').removeClass('is-valid is-invalid');
  $('#feedbackTelefonoRepartidorReg').html('');
  $('#btnGuardarRepartidorOrden').prop('disabled', true);

  // Seteamos la cédula en el campo oculto del modal de registro
  $('#modalRegistroRepartidorOrden input[name="cedula_repartidor"]').val(cedulaActual);
  // Seteamos también el código en el hidden
  $('#modalRegistroRepartidorOrden input[name="codigo_rif_cedula_repartidor"]').val(letraCed);

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

  if (val.length !== 7) {
    el.addClass('is-invalid');
    fb.html(`<small class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Debe tener 7 dígitos (${val.length}/7)</small>`);
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
  // El prefijo viene del select y el cuerpo del input
  let prefijo = $('#selectPrefijoTelefonoRepartidorReg').val() || '';
  let cuerpoTel = (datosObj.telefono_repartidor || '').replace(/\D/g, '');
  datosObj.prefijo_telefono_repartidor = prefijo;
  datosObj.telefono_repartidor = cuerpoTel;

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
      // Separar letra y dígitos para restaurar en el input de búsqueda
      let matchCed = cedulaRegistrada.match(/^([A-Za-z])(\d+)$/);
      if (matchCed) {
        $('#selectCodigoRepartidorOrden').val(matchCed[1].toUpperCase());
        $('#inputCedulaRepartidorOrden').val(matchCed[2]);
        validarCedulaRepartidorOrden(cedulaRegistrada);
      } else {
        $('#inputCedulaRepartidorOrden').val(cedulaRegistrada);
        validarCedulaRepartidorOrden(cedulaRegistrada);
      }
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

  // Si es presupuesto, enviamos directo sin pasar por el modal de estados
  if ($('#chkPresupuestoOrden').is(':checked')) {
    $('#estadoSeleccionadoOrden').val(1); // valor de relleno, no se usa en backend para presupuesto
    $('#formOrden').trigger('submit');
    return;
  }

  // Si venimos de "Procesar Presupuesto" (contenedor oculto), también enviamos directo
  // porque el backend lo guardará como orden real (es_presupuesto=0)
  if ($('#contenedorPresupuestoCheck').hasClass('d-none')) {
    $('#estadoSeleccionadoOrden').val(1);
    // Mostrar modal de estados igual que una orden normal para elegir cómo registrarla
    if ($('#chkDeliveryOrden').is(':checked')) {
      $('.btn-estado-orden[data-estado="3"]').show();
      $('.btn-estado-orden[data-estado="4"]').show();
    } else {
      $('.btn-estado-orden[data-estado="3"]').hide();
      $('.btn-estado-orden[data-estado="4"]').hide();
    }
    $('.modalRegistrar').addClass('fact-modal-dimmed');
    $('#modalEstadosOrden').modal('show');
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

  for (let p of productosOrden) {
    let d = parseFloat(p.descuento || 0);
    if (isNaN(d) || d < 0 || d > 100) {
      Swal.fire('Error', `El descuento para el producto "${p.nombre}" debe estar entre 0% y 100%`, 'warning');
      return;
    }
  }

  let esPresupuesto = $('#chkPresupuestoOrden').is(':checked');

  // Siempre validamos fecha y ubicación de servicios, tanto para OEP normal como para Presupuesto
  // El presupuesto es una cotización real: necesita fecha y lugar planeados
  if (serviciosOrden.length > 0) {
    let servSinUbicacion = serviciosOrden.find(s => !s.id_ruta);
    let servSinFecha = serviciosOrden.find(s => !s.fecha_ejecucion);
    if (servSinUbicacion) {
      Swal.fire('Atención', `El servicio "${servSinUbicacion.nombre}" no tiene ubicación asignada. Haga clic en "Ubicación".`, 'warning');
      return;
    }
    if (servSinFecha) {
      Swal.fire('Atención', `El servicio "${servSinFecha.nombre}" no tiene fecha de ejecución.`, 'warning');
      return;
    }
  }

  // Validación de delivery (solo para OEP real, en presupuesto no aplica)
  if (!esPresupuesto) {
    if ($('#chkDeliveryOrden').is(':checked') && !$('#idRutaDeliveryOrden').val()) {
      Swal.fire('Atención', 'Seleccione una ubicación en el mapa para asignar la ruta del Delivery.', 'warning');
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

  // === PROCESAR PRESUPUESTO EXISTENTE ===
  // Si venimos del flujo "Procesar Presupuesto", llamamos a la acción específica
  // en lugar de registrar, para que el presupuesto mantenga el mismo Nº de Orden.
  if (_idPresupuestoAProcesar !== null) {
    let idPresupuesto = _idPresupuestoAProcesar;
    let estadoSel = $('#estadoSeleccionadoOrden').val();

    let resp = await pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos', noGuardarLocal: true,
      datosPe: {
        accion: 'procesarPresupuesto',
        id_orden_entrega_presupuesto: idPresupuesto,
        estadoSeleccionado: estadoSel,
        productos: JSON.stringify(productosOrden),
        servicios: JSON.stringify(serviciosOrden),
        delivery: JSON.stringify(deliveryInfo),
        rif_cedula_cliente: $('#inputCedulaClienteOrden').val().trim()
      }
    });

    $('#btnGuardarOrden').html(`<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar`).prop('disabled', false);

    if (resp && resp.icono === 'success') {
      _idPresupuestoAProcesar = null;
      $('.modalRegistrar').modal('hide');
      resetFormOrden();
      reiniciarDataTables();
      // Abrir modal de pagos si el estado requiere pago inmediato (1 = procesada y pagada, 3 = pagada y despachada)
      if (estadoSel == 1 || estadoSel == 3) {
        abrirModalPagos(resp.id_orden_entrega_presupuesto);
      } else {
        Swal.fire({
          title: resp.titulo,
          html: `La orden <strong>${resp.id_orden_entrega_presupuesto}</strong> fue procesada exitosamente.`,
          icon: resp.icono,
          confirmButtonColor: '#7c3aed'
        });
      }
    } else {
      alertasAjax(resp);
    }
    return;
  }
  // === FIN PROCESAR PRESUPUESTO ===

  // Agregar inputs hidden con las matrices
  $(this).find('input[name="productos"], input[name="servicios"], input[name="delivery"], input[name="estadoSeleccionado"], input[name="es_presupuesto"]').remove();
  $('<input>', { type: 'hidden', name: 'productos', value: JSON.stringify(productosOrden) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'servicios', value: JSON.stringify(serviciosOrden) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'delivery', value: JSON.stringify(deliveryInfo) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'estadoSeleccionado', value: $('#estadoSeleccionadoOrden').val() }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'es_presupuesto', value: esPresupuesto ? '1' : '0' }).appendTo(this);

  let resp = await enviarFormulario({ formulario: this, modulo: 'ordenesEntregasPresupuestos' });

  $('#btnGuardarOrden').html(`<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar`).prop('disabled', false);

  if (resp && resp.icono === 'success') {
    $('.modalRegistrar').modal('hide');
    resetFormOrden();
    reiniciarDataTables();

    // Solo abrimos el modal de pagos si NO es un presupuesto
    if (!esPresupuesto) {
      let estado = $('#estadoSeleccionadoOrden').val();
      if (estado == 1 || estado == 3) {
        abrirModalPagos(resp.id_orden_entrega_presupuesto);
      }
    } else {
      Swal.fire({
        title: 'Presupuesto Guardado',
        html: `El presupuesto <strong>${resp.id_orden_entrega_presupuesto}</strong> ha sido guardado. Puedes procesarlo desde la lista cuando el cliente confirme.`,
        icon: 'success',
        confirmButtonColor: '#7c3aed'
      });
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
  let monedasOpt = resMonedas.filter(m => m.status == 1).map(m => `<option value="${m.id_moneda}" data-valor="${m.valor_moneda}" data-simbolo="${m.simbolo_moneda || ''}" data-nombre="${m.nombre_moneda}">${m.nombre_moneda}</option>`).join('');
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
  firstRow.find('.span-simbolo-moneda').text('$');
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
    let ultimaMonedaSeleccionada = null;

    $('#modalPagosOrden .fila-pago').each(function () {
      let rawVal = $(this).find('.input-monto-pago').val() || '0';
      let valInput = parsearMonto(rawVal);
      let optMetodo = $(this).find('.sel-metodo-pago option:selected');
      let reqMoneda = optMetodo.data('moneda') == 1;

      let tasaMonedaSeleccionada = 1; // Por defecto Bolívares tiene valor 1
      let simboloMonedaFila = '$';

      if (reqMoneda) {
        let optMoneda = $(this).find('.sel-moneda-pago option:selected');
        if (optMoneda.val() !== '') {
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
      let montoEnDolares = (valInput * tasaMonedaSeleccionada) / bsRate;
      sumPagadoEnModal += montoEnDolares;
    });

    let nuevoRestante = restante - sumPagadoEnModal;
    let excede = nuevoRestante < -0.01;
    let restanteBs = nuevoRestante * bsRate;

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

    $('#pagoRestante').html(htmlRestante);

    if (excede) {
      $('#btnConfirmarPago').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary fact-pagos-grad');
    } else {
      $('#btnConfirmarPago').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary fact-pagos-grad');
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

    calcularRestanteModal();
  });

  $(document).on('click', '#btnAgregarOtroPago', function () {
    let clone = $('#modalPagosOrden .fila-pago').first().clone();
    clone.find('.btn-eliminar-pago').removeClass('d-none');
    clone.find('.input-monto-pago').val('');
    clone.find('.span-simbolo-moneda').text('$');
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
      let moneda = reqMoneda ? ($(this).find('.sel-moneda-pago').val() || null) : null;
      let bancoE = reqEmisor ? ($(this).find('.sel-banco-emisor').val() || null) : null;
      let bancoR = reqReceptor ? ($(this).find('.sel-banco-receptor').val() || null) : null;
      let ref = reqRef ? ($(this).find('.input-referencia-pago').val() || null) : null;
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
      Swal.fire('Atención', 'Campos faltantes o valor de monto igual a 0', 'warning');
      return;
    }

    // Validar que la suma no exceda el restante
    let sumEnvio = 0;
    pagosEnvio.forEach((p, idx) => {
      let optMetodo = $('#modalPagosOrden .fila-pago').eq(idx).find('.sel-metodo-pago option:selected');
      let reqMoneda = optMetodo.length ? optMetodo.data('moneda') == 1 : false;

      let tasaMonedaSeleccionada = 1;

      if (reqMoneda) {
        let optMoneda = $('#modalPagosOrden .fila-pago').eq(idx).find('.sel-moneda-pago option:selected');
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
      let req = await fetch('?modulo=ordenesEntregasPresupuestos', {
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

// Evento para imprimir por POST (maneja tanto JSON de error como PDF exitoso)
$('body').on('click', '.botonImprimirPlanilla', function (e) {
  e.preventDefault();
  let id = $(this).attr('value');
  let formData = new FormData();
  formData.append('accion', 'imprimirPlanilla');
  formData.append('id_orden_entrega_presupuesto', id);

  fetch('?modulo=ordenesEntregasPresupuestos', {
    method: 'POST',
    body: formData,
    headers: {
      'X-TOKEN-CSRF': $('meta[name="TOKEN_CSRF"]').attr("content")
    }
  })
  .then(async response => {
    // Clonamos la respuesta para poder leerla dos veces si es necesario
    const clonada = response.clone();
    try {
      // Intentamos parsear como JSON primero (errores de permiso, validaciones, etc.)
      const res = await response.json();
      if (res && res.tipo) {
        if (typeof alertas === "function") {
          alertas(res);
        } else {
          Swal.fire({
            icon: res.icono || 'error',
            title: res.titulo || 'Error',
            text: res.texto || 'Ocurrió un error inesperado'
          });
        }
        return;
      }
    } catch (e) {
      // No es JSON válido, entonces es un PDF
    }
    // Si llegamos aquí, la respuesta es el PDF binario
    let blob = await clonada.blob();
    let url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');
    setTimeout(() => window.URL.revokeObjectURL(url), 1000);
  })
  .catch(error => {
    console.error("Error al imprimir:", error);
  });
});

//#region [LOGICA DE PLANILLA RETENCION IVA]
let _datosRetencionActual = null;

// Abrir modal de Retención IVA desde el Detalle de la Orden
$(document).off('click', '.btnAbrirModalRetencion').on('click', '.btnAbrirModalRetencion', async function (e) {
  e.preventDefault();
  let id = $(this).data('id') || $(this).attr('data-id');
  if (!id) return;

  Swal.fire({
    title: 'Consultando datos...',
    text: 'Cargando información para la retención de IVA',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    let resp = await pedirDatosAjax({
      modulo: 'ordenesEntregasPresupuestos',
      noGuardarLocal: true,
      datosPe: {
        accion: 'obtenerDatosRetencion',
        id_orden_entrega_presupuesto: id
      }
    });

    Swal.close();

    if (!resp || (resp.tipo === 'simple' && resp.icono === 'error')) {
      Swal.fire(resp.titulo || 'Error', resp.texto || 'No se pudieron obtener los datos de la orden', resp.icono || 'error');
      return;
    }

    _datosRetencionActual = resp;

    // Rellenar formulario del modal
    $('#retIdOrden').val(resp.id_orden_entrega_presupuesto);
    $('#retOrdenBadge').text(resp.id_orden_entrega_presupuesto);
    $('#retClienteNombre').text(resp.cliente || 'Consumidor Final');
    $('#retClienteRif').text(resp.rif_cedula_cliente || '-');

    $('#inputRetNroFactura').val(resp.nro_factura || '');
    $('#inputRetNroControl').val(resp.nro_control || '');
    $('#inputRetNroComprobante').val(resp.nro_comprobante || '');
    $('#inputRetFecha').val(resp.fecha_sugerida || new Date().toISOString().slice(0, 10));
    $('#selectRetPorcentaje').val('75');
    $('#inputRetTasaCambio').val(parseFloat(resp.tasa_bolivar || 1).toFixed(4));

    // Recalcular montos en pantalla
    actualizarCalculosModalRetencion();

    let modalRet = new bootstrap.Modal(document.getElementById('modalRetencionIVA'));
    modalRet.show();

  } catch (err) {
    Swal.close();
    console.error("Error al cargar retención:", err);
    Swal.fire('Error', 'Ocurrió un error al consultar la información de retención', 'error');
  }
});

// Función para actualizar en vivo el resumen de montos en Bs del modal
function actualizarCalculosModalRetencion() {
  if (!_datosRetencionActual) return;

  let tasa = parseFloat($('#inputRetTasaCambio').val()) || parseFloat(_datosRetencionActual.tasa_bolivar) || 1;
  let porcRet = parseFloat($('#selectRetPorcentaje').val()) || 75;
  let alicuota = parseFloat(_datosRetencionActual.alicuota_iva) || 16;

  let baseDolares = parseFloat(_datosRetencionActual.base_imponible_dolares) || 0;
  let ivaDolares = parseFloat(_datosRetencionActual.iva_dolares) || 0;

  let baseBs = baseDolares * tasa;
  let ivaBs = ivaDolares * tasa;
  let totalBs = baseBs + ivaBs;
  let ivaRetenidoBs = ivaBs * (porcRet / 100);
  let montoAPagarBs = totalBs - ivaRetenidoBs;

  const fnFmt = (n) => 'Bs ' + n.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  $('#resRetBaseImponible').text(fnFmt(baseBs));
  $('#resRetAlicuota').text(alicuota);
  $('#resRetMontoIva').text(fnFmt(ivaBs));
  $('#resRetPorcBadge').text(porcRet);
  $('#resRetMontoRetenido').text(fnFmt(ivaRetenidoBs));
  $('#resRetMontoAPagar').text(fnFmt(montoAPagarBs));
}

// Eventos para recalcular montos al cambiar tasa o porcentaje
$(document).on('input change', '#inputRetTasaCambio, #selectRetPorcentaje', function () {
  actualizarCalculosModalRetencion();
});

// Generar Retención IVA (Descarga de archivo Excel y persistencia en BD)
$(document).off('click', '#btnGenerarRetencionIVA').on('click', '#btnGenerarRetencionIVA', async function (e) {
  e.preventDefault();

  let idOrden = $('#retIdOrden').val().trim();
  let nroFactura = $('#inputRetNroFactura').val().trim();
  let nroControl = $('#inputRetNroControl').val().trim();
  let nroComprobante = $('#inputRetNroComprobante').val().trim();
  let fecha = $('#inputRetFecha').val().trim();
  let porcRet = $('#selectRetPorcentaje').val();
  let tasaCambio = $('#inputRetTasaCambio').val();

  if (!nroFactura) {
    Swal.fire('Campo Requerido', 'Por favor ingresa el Número de Factura (máx. 7 caracteres).', 'warning');
    $('#inputRetNroFactura').focus();
    return;
  }
  if (nroFactura.length > 7) {
    Swal.fire('Límite Excedido', 'El Número de Factura no puede superar 7 caracteres.', 'warning');
    $('#inputRetNroFactura').focus();
    return;
  }

  if (!nroControl) {
    Swal.fire('Campo Requerido', 'Por favor ingresa el Número de Control de la Factura (máx. 10 caracteres).', 'warning');
    $('#inputRetNroControl').focus();
    return;
  }
  if (nroControl.length > 10) {
    Swal.fire('Límite Excedido', 'El Número de Control no puede superar 10 caracteres.', 'warning');
    $('#inputRetNroControl').focus();
    return;
  }

  if (!nroComprobante) {
    Swal.fire('Campo Requerido', 'Por favor ingresa el Número de Comprobante (14 dígitos).', 'warning');
    $('#inputRetNroComprobante').focus();
    return;
  }
  if (nroComprobante.length > 14) {
    Swal.fire('Límite Excedido', 'El Número de Comprobante no puede superar 14 dígitos.', 'warning');
    $('#inputRetNroComprobante').focus();
    return;
  }

  if (!fecha) {
    Swal.fire('Campo Requerido', 'Por favor selecciona la fecha del comprobante.', 'warning');
    $('#inputRetFecha').focus();
    return;
  }

  let formData = new FormData();
  formData.append('accion', 'generarRetencionIVA');
  formData.append('id_orden_entrega_presupuesto', idOrden);
  formData.append('nro_factura', nroFactura);
  formData.append('nro_control', nroControl);
  formData.append('nro_comprobante', nroComprobante);
  formData.append('fecha_comprobante', fecha);
  formData.append('porcentaje_retencion', porcRet);
  formData.append('tasa_cambio', tasaCambio);

  Swal.fire({
    title: 'Generando comprobante...',
    text: 'Guardando datos y generando la planilla Excel',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    let response = await fetch('?modulo=ordenesEntregasPresupuestos', {
      method: 'POST',
      body: formData,
      headers: {
        'X-TOKEN-CSRF': $('meta[name="TOKEN_CSRF"]').attr("content")
      }
    });

    const clonada = response.clone();
    try {
      const resJson = await response.json();
      if (resJson && resJson.tipo) {
        Swal.fire({
          icon: resJson.icono || 'error',
          title: resJson.titulo || 'Aviso',
          text: resJson.texto || 'No se pudo generar la retención'
        });
        return;
      }
    } catch (errJson) {
      // Es el archivo binario Excel
    }

    let blob = await clonada.blob();
    let url = window.URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = `Comprobante_Retencion_IVA_${nroComprobante}.xlsx`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => window.URL.revokeObjectURL(url), 1000);

    Swal.fire({
      icon: 'success',
      title: '¡Retención Generada!',
      text: 'El comprobante de retención IVA en Excel ha sido generado y descargado correctamente.',
      timer: 3000
    });

    let modalEl = document.getElementById('modalRetencionIVA');
    let modalInst = bootstrap.Modal.getInstance(modalEl);
    if (modalInst) modalInst.hide();

  } catch (err) {
    console.error("Error al generar retención:", err);
    Swal.fire('Error', 'Ocurrió un error inesperado al generar el archivo Excel.', 'error');
  }
});
//#endregion [LOGICA DE PLANILLA RETENCION IVA]

