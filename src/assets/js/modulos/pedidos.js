//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, listarItemPanelCarritoPedido, rutaFotos, cambiarFormatos,
  pedirDatosAjax, extraerDatosAjax, enviarFormulario, alertasAjax, reiniciarDataTables,
  formateoCampos, mostrarOcultarSpinnerCarga, objCacheSS, reiniciarDataModuloSS
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] FIN
function aggProductoPedido() {
  let htmlActual = $(this).addClass('checked').html();
  $(this).html(`<i class="fi fi-rr-check me-2"></i>Agregado`);
  setTimeout(() => {
    $(this).html(htmlActual);
    $(this).removeClass('checked');
  }, 200);

  let { tipo_item, id_presentacion_producto } = $(this).data('info');
  let item = objCacheSS.getItem('itemsCarritoPedido/' + tipo_item + '/' + id_presentacion_producto);
  if (item) item.cantidad++;
  else {
    item = {
      ...$(this).data('info'),
      cantidad: 1
    }
  }
  objCacheSS.setItem('itemsCarritoPedido/' + tipo_item + '/' + id_presentacion_producto, item);
  listarItemPanelCarritoPedido();
}
async function cambiarEstadosPedido() {
  let infoCambio = $(this).data('info');

  let {
    titulo_alerta,
    texto_alerta,
    estado,
    id_pedido
  } = infoCambio

  let resultado = await alertasAjax({
    'tipo': 'preguntar',
    titulo: titulo_alerta,
    texto: texto_alerta
  })

  if (resultado.isConfirmed) {
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      let resultado = await pedirDatosAjax({
        noGuardarLocal: true,
        modulo: 'pedidos',
        datosPe: {
          accion: 'cambiarEstado',
          id_pedido,
          status_pedido: estado
        }
      });
      if (resultado?.icono == 'success') {
        reiniciarDataModuloSS('pedidos');
        reiniciarDataTables();
      }
      alertasAjax(resultado);
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }
  }

}
async function verDetallesPedido() {
  try {
    mostrarOcultarSpinnerCarga('mostrar');
    let modalD = $('.modalDetallesPedido');
    let pedido = await pedirDatosAjax({
      'modulo': 'pedidos',
      'datosPe': {
        'accion': 'seleccionarUno',
        'id_pedido': $(this).attr('value')
      }
    });
    if (pedido?.icono == 'error') return alertasAjax(pedido);

    let {
      calculos,
      cliente,
      status,
      vendedor,
      medioEnvio,
      capturesPagos
    } = pedido
    let {
      totalProductos,
      total_IVA,
      totalPagos,
      porcentaje_IVA,
      totalEnvio,
      dolar,
      monto_IVA
    } = calculos;

    let promesas = [];

    //Productos
    promesas.push(listarDataTable({
      selectorTabla: '.tablaProductosPedido',
      encabezados: {
        "id_presentacion_producto": "CÓDIGO",
        "nombre_producto": "PRODUCTO",
        "cantidad_producto": 'CANTIDAD',
        "precio_presentacion_factura": 'PRECIO UNITARIO',
        "cantidad_pmp": 'SUBTOTAL'
      },
      informacionPe: {
        funcClass: (datos) => {
          return datos.productos
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'seleccionarUno',
          'id_pedido': $(this).attr('value')
        }
      },
      infoTratoEspecial: {
        nombre_producto: (info) => {
          let foto = info.fila.foto_presentacion != '' ? info.fila.foto_presentacion : 'productoDefault.png';
          return `
            <div class="d-flex fila_producto">
              <div class="imagen_producto align-items-center justify-content-center text-muted">
                <img src="${rutaFotos}presentaciones_productos/${foto}" class=" estiloFotoRegistro">
              </div>
              <div class="ms-3 text-start fs-7">
                <div class="mb-0 fw-bold text-dark">${info.fila.nombre_producto}</div>
                <small class="text-muted">${info.fila.nombre_presentacion}</small>
              </div>
            </div>
          `;
        },
        precio_presentacion_factura: (info) => {
          let precioBS = (parseFloat(dolar.valor_fecha_moneda) * parseFloat(info.valor));
          return `
            <div class="py-3 text-end listarItemsPedido">
              <div class="precio_usd">${parseFloat(info.valor).toFixed(2)}$</div>
              <div class="precio_bs">${precioBS.toFixed(2)} Bs</div>
            </div>
          `;
        },
        cantidad_pmp: (info) => {
          let {
            subtotal_factura
          } = info.fila;
          let precioBS = dolar.valor_fecha_moneda * subtotal_factura;
          return `
            <div class="py-3 text-end listarItemsPedido">
              <div class="precio_usd">${subtotal_factura.toFixed(2)}$</div>
              <div class="precio_bs">${precioBS.toFixed(2)} Bs</div>
            </div>
          `;
        },
        cantidad_producto: (info) => {
          return `<span class="cantidadProducto">${info.valor}</span>`;
        }
      },
      datosFooter: {
        'Monto total de los productos': [`<span class="totalProductosPedido">${totalProductos.toFixed(2)}$</span>`, 5],
      }
    }));

    //Pagos
    let tituloExcedente = totalPagos - total_IVA > 0 ? 'Sobrante' : 'Faltante';
    promesas.push(listarDataTable({
      selectorTabla: '.tablaDetallesPagosPedido',
      encabezados: {
        nombre_metodo_pago: 'MÉTODO DE PAGO',
        monto_pago: 'MONTO',
        nombre_moneda: 'MONEDA',
        equivalencia_fecha_factura: 'EQUIVALENCIA($)',
        nombre_banco_emisor: 'B. EMISOR',
        nombre_banco_receptor: 'B. EMISOR',
        referencia_pago: 'REFERENCIA'
      },
      informacionPe: {
        funcClass: (datos) => {
          return datos.pagos
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'seleccionarUno',
          'id_pedido': $(this).attr('value')
        }
      },
      infoTratoEspecial: {
        equivalencia_fecha_factura: (info) => {
          return `<span class="equivalenciaSubTotalPago">${info.valor.toFixed(2)}$</span>`
        }
      },
      datosFooter: {
        'Total Productos': [`<span class="totalDolar">${totalProductos.toFixed(2)}</span>`, 1],
        'Total Envío': [`<span class="totalDolar">${totalEnvio.toFixed(2)}</span>`, 1],
        [`Monto IVA (${porcentaje_IVA}%)`]: [`<span class="totalDolar">${monto_IVA.toFixed(2)}</span>`, 2],
        'Total General': [`<span class="totalDolar">${total_IVA.toFixed(2)}</span>`, 1],
        'Total Cancelado': [`<span class="totalDolar">${totalPagos.toFixed(2)}</span>`, 1],
        [tituloExcedente]: [`<span class="totalDolar">${(totalPagos - total_IVA).toFixed(2)}</span>`, 1],
      }
    }));

    // Los comprobantes del pago
    let comprobantesHTML = ``;

    capturesPagos.forEach((capture, nroCap) => {
      let collapsed = `collapsed`;
      let aria_expanded = "false"
      let show = ""
      console.log({ capture, nroCap })
      if (nroCap == 0) {
        collapsed = ``
        aria_expanded = "true"
        show = "show"
      }
      comprobantesHTML += `
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading${nroCap}">
          <button class="accordion-button ${collapsed}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${nroCap}" aria-expanded="${aria_expanded}" aria-controls="collapse${nroCap}">
            Comprobante del pago #${nroCap + 1}
          </button>
        </h2>
        <div id="collapse${nroCap}" class="accordion-collapse collapse ${show}" aria-labelledby="heading${nroCap}" data-bs-parent="#acordionCaptures">
          <div class="accordion-body">
            <img src="${rutaFotos}comprobantes_pagos/${capture}" class="capturePagos">
          </div>
        </div>
      </div>
    `;
    });
    modalD.find('#acordionCaptures').empty().append(comprobantesHTML);

    //Vendedor-cliente-delivery
    let infoRestante = {
      ...cliente, ...vendedor, ...medioEnvio, ...calculos
    }


    //Insercion de datos
    let valoresAparte = [
      'foto_usuario', 'url_direccion',
    ];
    let clavesDineroDolar = [
      'precio_ruta_factura', 'totalEnvio'
    ];
    for (const [clave, valor] of Object.entries(infoRestante)) {
      if (valoresAparte.includes(clave)) continue;
      if (clavesDineroDolar.includes(clave)) modalD.find(`.tap_${clave}`).text(parseFloat(valor).toFixed(2) + '$');
      else modalD.find(`.tap_${clave}`).text(valor);
    }
    let foto = infoRestante.foto_usuario ? infoRestante.foto_usuario : 'perfilDefaultUsuario.png'
    foto = rutaFotos + 'usuarios/' + foto;
    modalD.find('.tap_foto_usuario').attr('src', foto);
    modalD.find('.tap_url_direccion').attr('href', infoRestante.url_direccion);

    //Logica del vendedor
    const permisos = await pedirDatosAjax({
      modulo: 'accesos',
      datosPe: {
        accion: 'listarPorRol'
      }
    });
    if (vendedor && permisos.pedidos.includes('ver pedidos de los clientes')) {
      modalD.find('#tapVendedorPedido').removeClass('d-none');
      modalD.find('.btnTapVendedor').removeClass('d-none');
    } else {
      modalD.find('.btnTapVendedor').addClass('d-none');
      modalD.find('#tapVendedorPedido').addClass('d-none');
    }

    $('.idPedido').val($(this).attr('value'));

    // Logica del delivery/envio de tercero
    if (!permisos.pedidos.includes('ver pedidos de los clientes')) {
      modalD.find('#seccionAsignarRepartidor').addClass('d-none');
      modalD.find('#seccionDatosRepartidor').addClass('d-none');
      modalD.find('#seccionSucursalEmpresaEnvios').addClass('d-none');
      modalD.find('.btnTapCliente').addClass('d-none');
    } else if (medioEnvio['tipo_medio_envio'] == 'delivery') {
      $('.idDeliveryPedido').val(medioEnvio['id_delivery']);
      modalD.find('.btnTapMedioEnvio').find('button').text('Delivery');
      modalD.find('.tituloMedioEnvio').text('DELIVERY');
      modalD.find('#seccionSucursalEmpresaEnvios').addClass('d-none');

      if (!medioEnvio['cedula_repartidor']) {
        modalD.find('#seccionAsignarRepartidor').removeClass('d-none');
        modalD.find('#seccionDatosRepartidor').addClass('d-none');
      } else {
        modalD.find('#seccionAsignarRepartidor').addClass('d-none');
        modalD.find('#seccionDatosRepartidor').removeClass('d-none');
      }
    } else {
      modalD.find('.btnTapMedioEnvio').find('button').text('Envío');
      modalD.find('.tituloMedioEnvio').text('ENVÍO DE TERCERO');
      modalD.find('#seccionAsignarRepartidor').addClass('d-none');
      modalD.find('#seccionDatosRepartidor').addClass('d-none');
      modalD.find('#seccionSucursalEmpresaEnvios').removeClass('d-none');
    }

    await Promise.all(promesas);

    //Formateos cantidades
    let preciosBs = $(modalD).find('.precio_bs');
    let preciosUsd = $(modalD).find(`
      .precio_usd, 
      .totalProductosPedido,
      .tap_precio_ruta_factura,
      .tap_totalEnvio,
      .equivalenciaSubTotalPago,
      .totalDolar
    `);
    let dinero = $(modalD).find('.cantidadProducto');

    formateoCampos(dinero, 'dinero');
    formateoCampos(preciosBs, 'dineroBolivar');
    formateoCampos(preciosUsd, 'dineroDolar');


    modalD.find('.btnTapProductos').trigger('click');

  } finally {
    mostrarOcultarSpinnerCarga('ocultar')
  }
}
async function mostrarUOcultarTaps() {
  const permisos = await pedirDatosAjax({
    modulo: 'accesos',
    datosPe: {
      accion: 'listarPorRol'
    }
  });

  let padre = $('.botonesTaps')
  let botonesClientes = padre.find(`
    .btnTapCatalogoProductos, 
    .btnTapPedidosRealizados
  `)
  let botonesVendedor = padre.find(`
    .btnTapPedidosPendientes, 
    .btnTapPedidosRechazados, 
    .btnTapPedidosConfirmados, 
    .btnTapPedidosEntregados`
  );
  let cuerposTapClientes = $('.contenidoTaps').find(`
    #tapCatalogoProductos, 
    #tapPedidosRealizados
  `)
  let cuerposTapVendedor = $('.contenidoTaps').find(`
    #tapPedidosPendientes, 
    #tapPedidosRechazados, 
    #tapPedidosConfirmados, 
    #tapPedidosEntregados
  `)

  if (permisos.pedidos.includes('ver pedidos propios')) {
    botonesClientes.removeClass('d-none')
    cuerposTapClientes.removeClass('d-none')
  }
  if (permisos.pedidos.includes('ver pedidos de los clientes')) {
    botonesVendedor.removeClass('d-none')
    cuerposTapVendedor.removeClass('d-none')
    padre.find('.btnTapPedidosPendientes').find('button').trigger('click')
  }
}
async function imprimirPedido() {
  let resultado = await alertasAjax({
    'tipo': 'preguntar',
    'titulo': 'Imprimir pedido',
    'texto': '¿Desea imprimir el pedido?',
  });

  if (resultado.isConfirmed) {
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      let resultado = await pedirDatosAjax({
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'imprimirPedido',
          'id_pedido': $(this).attr('value')
        }
      });
      if (resultado?.icono == 'error') return alertasAjax(resultado);
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }

  }
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO

$(document).on("DOMContentLoaded", async function () {
  mostrarUOcultarTaps();
  const permisos = await pedirDatosAjax({
    modulo: 'accesos',
    datosPe: {
      accion: 'listarPorRol'
    }
  });
  if (permisos?.icono == 'error') return alertasAjax(permisos);

  if (permisos.pedidos.includes('ver pedidos propios')) {
    let promesas = [];
    // Catalogo de productos
    promesas.push(listarDataTable({
      encabezados: {
        "id_presentacion_producto": "CÓDIGO",
        "nombre_producto": "PRODUCTO",
        "nombre_categoria_producto": 'CATEGORÍA',
        "precio_dolar": "PRECIO",
        "stock_producto": "STOCK",
      },
      informacionPe: {
        'modulo': 'productos',
        'datosPe': {
          'accion': 'listarEcommerce',
          'tipoConsulta': 'ecommerce'
        },
      },
      botones: (info) => {
        let boton = $(`
          <button class="btn btn-dark rounded-pill px-2 py-2 btnAggProdSerCarrito shadow-sm">
            <i class="fi fi-rr-shopping-cart-add me-1"></i>
            Añadir
          </button>
        `)
        info.fila.tipo_item = 'productos';
        boton.attr('data-info', JSON.stringify(info.fila))
        return boton.prop('outerHTML');
      },
      infoTratoEspecial: {
        nombre_producto: (info) => {
          let foto = info.fila.foto_presentacion != '' ? info.fila.foto_presentacion : 'productoDefault.png';
          return `
            <div class="d-flex fila_producto">
              <div class="imagen_producto align-items-center justify-content-center text-muted">
                <img src="${rutaFotos}presentaciones_productos/${foto}" class=" estiloFotoRegistro">
              </div>
              <div class="ms-3 text-start fs-7">
                <div class="mb-0 fw-bold text-dark">${info.fila.nombre_producto}</div>
                <small class="text-muted">${info.fila.nombre_presentacion}</small>
              </div>
            </div>
          `;
        },
        precio_dolar: (info) => {
          return `
            <div class="py-3 text-end listarItemsPedido">
              <div class="precio_usd">${info.valor}$</div>
              <div class="precio_bs">${info.fila.precio_bs} Bs</div>
            </div>
          `;
        },
        stock_producto: (info) => {
          return info.valor > 0 ?
            '<i class="fi fi-rr-check-circle text-success fs-5 title="Disponible""></i>' :
            '<i class="fi fi-rr-cross-circle text-danger fs-5" title="No disponiblek"></i>'
        },
      },
      visualizacionResponsiveColumnas: {
        "id_presentacion_producto": 1,
        "nombre_producto": 1,
        "precio_dolar": 2,
        "nombre_categoria_producto": 3,
        "stock_producto": 4,
      },
    }));
    // Del cliente que inicio sesion
    promesas.push(listarDataTable({
      selectorTabla: '.tablaPedidosRealizados',
      encabezados: {
        "id_orden_entrega_presupuesto": "COD",
        "fecha_orden_entrega_presupuesto": "FECHA",
        'status_pedido': 'ESTADO'
      },
      informacionPe: {
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'listar',
          'tipoConsulta': 'clienteInicioSesion'
        }
      },
      infoTratoEspecial: {
        fecha_orden_entrega_presupuesto: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
        status_pedido: (info) => {
          let texto = '';
          let color = '';
          switch (info.valor) {
            case 5:
              color = 'bg-primary';
              texto = 'Por Confimar';
              break;
            case 6:
              color = 'bg-danger'
              texto = 'Cancelado';
              break;
            case 7:
              color = 'bg-success'
              texto = 'Confirmado';
              break;
            case 8:
              color = 'bg-success'
              texto = 'Despachado';
              break;
            default:
              break;
          }
          return `<span class="badge ${color}">${texto}</span>`;
        }
      },
      botones: (info) => {
        let { permisos, fila } = info;
        let btn = ``;
        if (permisos.pedidos.includes('ver detalles de pedidos propios')) {
          btn += `
          <li 
            class="list-inline-item align-bottom" 
            data-bs-toggle="tooltip" 
            data-bs-placement="top" 
            title="Ver pedido"
          >
            <a 
              href="#" 
              value="${fila.id_orden_entrega_presupuesto}"
              class="btnVerPedido avtar avtar-xs btn-link-success btn-pc-default"
              data-bs-toggle="modal" 
              data-bs-target=".modalDetallesPedido"
            >
              <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
            </a>
          </li>
        `;
        }
        return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
      },
      visualizacionResponsiveColumnas: {
        "id_orden_entrega_presupuesto": 1,
        "fecha_orden_entrega_presupuesto": 1,
        'status_pedido': 2,
      },
    }));

    Promise.all(promesas).then(() => {
      let preciosDolares = $('#tapCatalogoProductos').find('.precio_usd');
      let preciosBs = $('#tapCatalogoProductos').find('.precio_bs');
      formateoCampos(preciosDolares, 'dineroDolar');
      formateoCampos(preciosBs, 'dineroBolivar');
    })
  }
  if (permisos.pedidos.includes('ver pedidos de los clientes')) {
    //Por confirmar
    listarDataTable({
      selectorTabla: '.tablaPedidosPendientes',
      encabezados: {
        "id_orden_entrega_presupuesto": "COD",
        "fecha_orden_entrega_presupuesto": "FECHA",
        "razon_social_cliente": "CLIENTE",
      },
      informacionPe: {
        funcClass: (datos) => {
          return datos.porConfirmar
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'listar',
        }
      },
      infoTratoEspecial: {
        fecha_orden_entrega_presupuesto: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
        razon_social_cliente: (info) => {
          return `${info.valor} - ${info.fila.rif_cedula_cliente}`;
        },
      },
      botones: (info) => {
        let { permisos, fila } = info;
        let btn = ``;
        if (permisos.pedidos.includes('ver detalles de pedidos propios')) {
          btn += `
            <li 
              class="list-inline-item align-bottom" 
              data-bs-toggle="tooltip" 
              data-bs-placement="top" 
              title="Ver pedido"
            >
              <a 
                href="#" 
                value="${fila.id_orden_entrega_presupuesto}"
                class="btnVerPedido avtar avtar-xs btn-link-success btn-pc-default"
                data-bs-toggle="modal" 
                data-bs-target=".modalDetallesPedido"
              >
                <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        if (permisos.pedidos.includes('cancelar pedidos')) {
          let configBtn = JSON.stringify({
            id_pedido: fila.id_orden_entrega_presupuesto,
            estado: 6,
            titulo_alerta: '¿Seguro que desea cancelar el pedido?',
            texto_alerta: 'Si cancela el pedido este será enviado a la lista de pedidos cancelados'
          })
          btn += `
            <li 
              value='${fila.id_orden_entrega_presupuesto}'
              class="btnCambiarEstadoPedido list-inline-item align-bottom"
              data-info='${configBtn}',
              data-bs-toggle="tooltip" 
              title="Cancelar"
            >
              <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
                <i class="fi fi-rs-ban fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
      },
      visualizacionResponsiveColumnas: {
        "id_orden_entrega_presupuesto": 1,
        "fecha_orden_entrega_presupuesto": 1,
        "razon_social_cliente": 2,
      },
    });

    //Rechazados
    listarDataTable({
      selectorTabla: '.tablaPedidosRechazados',
      encabezados: {
        "id_orden_entrega_presupuesto": "COD",
        "fecha_orden_entrega_presupuesto": "FECHA",
        "razon_social_cliente": "CLIENTE",
      },
      informacionPe: {
        'funcClass': (datos) => {
          return datos.rechazados
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'listar',
        }
      },
      infoTratoEspecial: {
        fecha_orden_entrega_presupuesto: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
        razon_social_cliente: (info) => {
          return `${info.valor} - ${info.fila.rif_cedula_cliente}`;
        },
      },
      botones: (info) => {
        let { permisos, fila } = info;
        let btn = ``;
        if (permisos.pedidos.includes('ver detalles de pedidos propios')) {
          btn += `
          <li 
            class="list-inline-item align-bottom" 
            data-bs-toggle="tooltip" 
            data-bs-placement="top" 
            title="Ver pedido"
          >
            <a 
              href="#" 
              value="${fila.id_orden_entrega_presupuesto}"
              class="btnVerPedido avtar avtar-xs btn-link-success btn-pc-default"
              data-bs-toggle="modal" 
              data-bs-target=".modalDetallesPedido"
            >
              <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
            </a>
          </li>
        `;
        }
        return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
      },
      visualizacionResponsiveColumnas: {
        "id_orden_entrega_presupuesto": 1,
        "fecha_orden_entrega_presupuesto": 1,
        "razon_social_cliente": 2,
      },
    });

    //Confirmados
    listarDataTable({
      selectorTabla: '.tablaPedidosConfirmados',
      encabezados: {
        "id_orden_entrega_presupuesto": "COD",
        "fecha_orden_entrega_presupuesto": "FECHA",
        "razon_social_cliente": "CLIENTE",
      },
      informacionPe: {
        'funcClass': (datos) => {
          return datos.confirmados
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'listar',
        }
      },
      infoTratoEspecial: {
        fecha_orden_entrega_presupuesto: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
        razon_social_cliente: (info) => {
          return `${info.valor} - ${info.fila.rif_cedula_cliente}`;
        },
      },
      botones: (info) => {
        let { permisos, fila } = info;
        let btn = ``;
        if (permisos.pedidos.includes('ver detalles de pedidos propios')) {
          btn += `
            <li 
              class="list-inline-item align-bottom" 
              data-bs-toggle="tooltip" 
              data-bs-placement="top" 
              title="Ver pedido"
            >
              <a 
                href="#" 
                value="${fila.id_orden_entrega_presupuesto}"
                class="btnVerPedido avtar avtar-xs btn-link-success btn-pc-default"
                data-bs-toggle="modal" 
                data-bs-target=".modalDetallesPedido"
              >
                <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        if (permisos.pedidos.includes('despachar pedidos')) {
          let configBtn = JSON.stringify({
            id_pedido: fila.id_orden_entrega_presupuesto,
            estado: 8,
            titulo_alerta: '¿Los productos del pedido han sido despachados?',
            texto_alerta: 'Haga click en el boton de "Aceptar" para confirmar que los producos han sido despachados'
          })
          btn += `
            <li 
              value='${fila.id_orden_entrega_presupuesto}'
              class="btnCambiarEstadoPedido list-inline-item align-bottom"
              data-info='${configBtn}',
              data-bs-toggle="tooltip" 
              title="Despachar"
            >
              <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
              <i class="fi fi-rr-person-dolly fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        if (permisos.pedidos.includes('imprimir pedidos')) {
          btn += `
            <li 
              value='${fila.id_orden_entrega_presupuesto}'
              class="btnImprimirPedido list-inline-item align-bottom"
              data-bs-toggle="tooltip" 
              title="Imprimir"
            >
              <a href="#" class="avtar avtar-xs btn-link-success btn-pc-default">
                <i class="fi fi-rr-print fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
      },
      visualizacionResponsiveColumnas: {
        "id_orden_entrega_presupuesto": 1,
        "fecha_orden_entrega_presupuesto": 1,
        "razon_social_cliente": 2,
      },
    });

    //Entregados
    listarDataTable({
      selectorTabla: '.tablaPedidosEntregados',
      encabezados: {
        "id_orden_entrega_presupuesto": "COD",
        "fecha_orden_entrega_presupuesto": "FECHA",
        "razon_social_cliente": "CLIENTE",
      },
      informacionPe: {
        'funcClass': (datos) => {
          return datos.entregados
        },
        'modulo': 'pedidos',
        'datosPe': {
          'accion': 'listar',
        }
      },
      infoTratoEspecial: {
        fecha_orden_entrega_presupuesto: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
        razon_social_cliente: (info) => {
          return `${info.valor} - ${info.fila.rif_cedula_cliente}`;
        },
      },
      botones: (info) => {
        let { permisos, fila } = info;
        let btn = ``;
        if (permisos.pedidos.includes('ver detalles de pedidos propios')) {
          btn += `
            <li 
              class="list-inline-item align-bottom" 
              data-bs-toggle="tooltip" 
              data-bs-placement="top" 
              title="Ver pedido"
            >
              <a 
                href="#" 
                value="${fila.id_orden_entrega_presupuesto}"
                class="btnVerPedido avtar avtar-xs btn-link-success btn-pc-default"
                data-bs-toggle="modal" 
                data-bs-target=".modalDetallesPedido"
              >
                <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        if (permisos.pedidos.includes('imprimir pedidos')) {
          btn += `
            <li 
              value='${fila.id_orden_entrega_presupuesto}'
              class="btnImprimirPedido list-inline-item align-bottom"
              data-bs-toggle="tooltip" 
              title="Imprimir"
            >
              <a href="#" class="avtar avtar-xs btn-link-success btn-pc-default">
                <i class="fi fi-rr-print fs-3 iconoCentrado"></i>
              </a>
            </li>
          `;
        }
        return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
      },
      visualizacionResponsiveColumnas: {
        "id_orden_entrega_presupuesto": 1,
        "fecha_orden_entrega_presupuesto": 1,
        "razon_social_cliente": 2,
      },
    });
  }
  extraerDatosAjax({
    'modulosPeticion': ['repartidores'],
    'accionesPeticion': [{ 'accion': 'listar' }],
    'tipoElemento': ['select'],
    'elementosDestino': [$('.selectRepartidor')],
    'datosInsertar': [
      {
        'value': 'cedula_repartidor',
        'texto': ['nombre_repartidor', 'apellido_repartidor'],
        'textoDefault': 'Seleccione un repartidor'
      }
    ]
  })
  driverAyuda('pedidos', {
    pasos: [
      {
        element: '.btnTapPedidosPendientes',
        popover: {
          title: 'Pedidos Pendientes',
          description: 'Como vendedor, aquí puedes ver los pedidos que esperan confirmación. Puedes aceptarlos o rechazarlos.',
          side: 'top'
        }
      },
      {
        element: '.btnTapPedidosRechazados',
        popover: {
          title: 'Pedidos Rechazados',
          description: 'Lista de pedidos que fueron cancelados o rechazados, ya sea por el cliente o por el vendedor.',
          side: 'top'
        }
      },
      {
        element: '.btnTapPedidosConfirmados',
        popover: {
          title: 'Pedidos Confirmados',
          description: 'Aquí se muestran los pedidos que ya han sido aceptados y están listos para ser despachados.',
          side: 'top'
        }
      },
      {
        element: '.btnTapPedidosEntregados',
        popover: {
          title: 'Pedidos Entregados',
          description: 'Historial de pedidos que ya han sido despachados y entregados al cliente.',
          side: 'top'
        }
      },
      {
        element: '.btnTapCatalogoProductos',
        popover: {
          title: 'Catálogo de Productos',
          description: 'Aquí puedes ver todos los productos disponibles para pedir. Haz clic en "Añadir" para agregar productos a tu carrito.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.btnTapPedidosRealizados',
        popover: {
          title: 'Mis Pedidos',
          description: 'Consulta el historial de tus pedidos realizados y su estado actual (Pendiente, Confirmado, Cancelado, Despachado).',
          side: 'top'
        }
      },
      {
        element: '.tabla-ajax, .tablaPedidosRealizados, .tablaPedidosPendientes, .tablaPedidosConfirmados, .tablaPedidosRechazados, .tablaPedidosEntregados',
        popover: {
          title: 'Listado de Datos',
          description: 'Tablas donde puedes ver productos, pedidos realizados o pedidos por gestionar. Puedes buscar y ordenar información.',
          side: 'top'
        }
      },
      {
        element: '.btnVerPedido',
        popover: {
          title: 'Ver Detalles del Pedido',
          description: 'Haz clic aquí para ver el detalle completo de tu pedido, incluyendo productos, pagos y estado del delivery.',
          side: 'left'
        }
      },
      {
        element: '.btnCambiarEstadoPedido',
        popover: {
          title: 'Gestionar Pedido',
          description: 'Desde aquí puedes cambiar el estado del pedido: Cancelar, Confirmar o Despachar según corresponda.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de pedidos. Puedes realizar pedidos como cliente o gestionarlos como vendedor en cada una de sus etapas.',
          side: 'top'
        }
      }
    ]
  });
});

//Imprimir pedido
$(document).off("click", ".btnImprimirPedido")
$(document).on("click", ".btnImprimirPedido", async function () {
  imprimirPedido.call(this);
})
//Cambiar estado pedido
$(document).off("click", ".btnCambiarEstadoPedido")
$(document).on("click", ".btnCambiarEstadoPedido", function () {
  cambiarEstadosPedido.call(this);
})

//Agg producto carrito
$(document).off("click", ".btnAggProdSerCarrito")
$(document).on("click", ".btnAggProdSerCarrito", function () {
  if (!$(this).hasClass('checked')) {
    aggProductoPedido.call(this);
  }
})

//Ver detalles de los pedidos
$(document).off("click", ".btnVerPedido")
$(document).on("click", ".btnVerPedido", async function () {
  verDetallesPedido.call(this)
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'pedidos'
  })
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN
