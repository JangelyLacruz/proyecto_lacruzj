//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR,
  extraerDatosAjax, pedirDatosAjax, obtenerSiguienteIndice,
  validarEnTiempoReal, formateoCampos, rutaFotos,
  alertasAjax,
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
async function renderizarPresentaciones() {
  let presentacionesBD = await pedirDatosAjax({
    modulo: "presentaciones",
    datosPe: { accion: "listar" },
  });
  if (presentacionesBD?.icono == 'error') return alertasAjax(presentacionesBD);

  let html = "";

  for (let i = 0; i < presentacionesBD.length; i++) {
    let {
      id_presentacion,
      nombre_presentacion,
      cantidad_pmp,
      nombre_unidad_medida
    } = presentacionesBD[i]
    html += `
      <div class="filaPresentacion col-lg-4 mb-3">
        <div class="form-check card-presentacion p-3 border rounded">
          <input 
            name="presentaciones-${i}-id_presentacion"
            class="checkbox-presentacion d-none" 
            type="checkbox" 
            value="${id_presentacion}"
          >
          <input 
            name="presentaciones-${i}-mostrar_ecommerce"
            class="checkbox-MEC d-none" 
            type="checkbox" 
            value="1"
          >
          <input 
            class="inputFotoPresentacion d-none"
            type="file" name="foto_presentacion_${id_presentacion}"
            accept="image/*"
          >
          <div class="form-check-label w-100">
            <div class="d-flex justify-content-between align-items-center">
              <strong>${nombre_presentacion}</strong>
              <span class="badge bg-info">${cantidad_pmp} ${nombre_unidad_medida}</span>
              <div class="botoneraMEI d-none">
                <button type="button" class="btn btn-primary btn-sm btnFotoPresentacion">
                  <i class="fi fi-rs-camera p-1"></i>
                </button>
                <button type="button" class="btn btn-secondary btn-sm btnMEPresentacion">
                  <i class="fi fi-rs-marketplace-store p-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }
  let htmlAct = $(html)
  htmlAct.find('.btnFotoPresentacion').remove();
  $('.modalRegistrar').find('form').find(".contenedor-presentaciones").empty().append(html);
  $('.modalActualizar').find('form').find(".contenedor-presentaciones").empty().append(htmlAct);
}
async function crearFilaMateriaPrima(materiaPrima = null) {

  let modal = $(this).closest('.modal');
  let codigoUniCoFila = obtenerSiguienteIndice(
    modal,
    "select",
    "materias_primas",
    "id_materia_prima"
  );
  let idMateriaPrima = materiaPrima ? materiaPrima["id_materia_prima"] : "";
  let cantidad = materiaPrima ? materiaPrima["cantidad_materia_prima"] : "";
  let plantillaFila = $($('.plantillaFilaMP').html().replaceAll('[COD-FILA]', codigoUniCoFila));
  let selectMateria = plantillaFila.find('.select-materia-prima');
  plantillaFila.find('.input-cantidad-materia').val(cantidad);
  await extraerDatosAjax({
    modulosPeticion: ["materiasPrimas"],
    accionesPeticion: [{ accion: "listar" }],
    tipoElemento: ["select"],
    elementosDestino: [selectMateria],
    datosInsertar: [
      {
        value: "id_materia_prima",
        texto: "nombre_materia_prima",
        textoDefault: "Seleccione una materia prima",
        opcionSeleccionada: idMateriaPrima,
      }
    ],
  });
  modal.find('.cuerpoTablaMateriasPrimas').append(plantillaFila);
}
async function calcularCostosMateriasPrimas(modal) {

  //Precio BCV producto
  let precioDivisas = $(modal).find('[name="precio_producto"]').val()
  precioDivisas = precioDivisas.replaceAll('.', '').replaceAll(',', '.')
  precioDivisas = parseFloat(precioDivisas);
  let precioDolar = parseFloat($('.precioDolar').val());
  let precioBCV = (isNaN(precioDivisas) ? 0 : precioDivisas) * precioDolar;
  let inputPrecioBCV = modal.find('.precioProductoBCV');
  inputPrecioBCV.val(precioBCV.toFixed(2));
  formateoCampos(inputPrecioBCV, 'dineroBolivar');

  //Costo materias primas
  const tbody = modal.find(".cuerpoTablaMateriasPrimas");
  let totalCosto = 0;

  for (let i = 0; i < tbody.find("tr").length; i++) {
    let fila = $(tbody.find("tr")[i]);
    const materiaId = fila.find(".select-materia-prima").val();
    let cantidad = fila.find(".input-cantidad-materia").val().replaceAll('.', '').replaceAll(',', '.')
    cantidad = parseFloat(cantidad) || 0;

    let materiaPrimaBD = await pedirDatosAjax({
      modulo: "materiasPrimas",
      datosPe: {
        'accion': "seleccionarUno",
        'id_materia_prima': materiaId
      },
    });
    if (materiaPrimaBD?.icono == 'error') return alertasAjax(materiaPrimaBD);

    if (materiaId && materiaPrimaBD) {
      const costoUnitario = parseFloat(materiaPrimaBD['precio_materia_prima']) || 0;
      const subtotal = costoUnitario * cantidad;
      fila.find(".costo-unitario").text(`${costoUnitario.toFixed(2)}$`);
      fila.find(".subtotal").text(`${subtotal.toFixed(2)}$`);
      totalCosto += parseFloat(subtotal);
    }
  }
  modal.find("#totalCostoMaterias").text(`${totalCosto.toFixed(2)} $`);
}
async function inicializarModalProducto(modal) {
  try {
    let idProducto = modal.attr("id_producto");
    let productoBD = await pedirDatosAjax({
      modulo: "productos",
      datosPe: {
        'accion': "seleccionarUno",
        'id_producto': idProducto
      },
    });
    if (productoBD?.icono == 'error') {
      await alertasAjax(productoBD);
      return;
    }

    let {
      necesitan_materias_primas,
      detallesExtra = {}
    } = productoBD
    let {
      presentaciones = false,
      materias_primas = false
    } = detallesExtra

    //Seleccionamos las presentaciones del producto
    const contenedorPresentaciones = modal.find(".contenedor-presentaciones");
    modal.find(".btn-deseleccionar-todas").trigger('click');
    presentaciones.forEach(pres => {
      let card = contenedorPresentaciones
        .find(`.checkbox-presentacion[value="${pres.id_presentacion}"]`)
        .closest('.card-presentacion');
      if (card.length > 0) {
        habilitarDeshabilitarPresentacion.call(card, 'habilitar');
        if (pres.mostrar_ecommerce == 1) {
          card.find('.btnMEPresentacion').trigger('click')
        }
      }
    });

    //Luego cargamos las materias primas
    let cuerpoMPHTML = modal.find(".cuerpoTablaMateriasPrimas");
    cuerpoMPHTML.empty();

    modal.find(".campos-fabricado").hide();
    if (necesitan_materias_primas == 1) {
      modal.find(".campos-fabricado").show();
      for (let i = 0; i < materias_primas.length; i++) {
        await crearFilaMateriaPrima.call(cuerpoMPHTML, materias_primas[i]);
      }
    }
    await calcularCostosMateriasPrimas(modal);
  } catch (error) {
    modal.find(".contenedor-presentaciones").html(`
      <div class="col-12">
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Error al cargar datos: ${error.message}
        </div>
      </div>
    `);
  }
}
function renderizarDashboard() {
  let datosTabla = $('.tabla-ajax').DataTable().rows().data().toArray();
  let total = datosTabla.length;
  let criticos = datosTabla.filter(p => parseFloat(p.stock_producto) <= parseFloat(p.stock_minimo_producto)).length;
  let valorDivisas = datosTabla.reduce((acc, p) => acc + (parseFloat(p.precio_producto) * parseFloat(p.stock_producto)), 0);

  let dashboardHTML = `
    <div class="row mb-4" id="metricasDashboard">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-primary bg-opacity-10 rounded-circle"><i class="fi fi-rr-box text-primary fs-3"></i></div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Productos Totales
              </h6>
              <h3 class="mb-0 fw-bold totalProdDashboard">
                ${total}
              </h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                <i class="fi fi-rr-money text-success fs-3"></i>
              </div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Valor Inventario ($)
              </h6>
              <h3 class="valorTotalInventario mb-0 fw-bold">
                ${formateoCampos(valorDivisas,'dineroDolar')}
              </h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-danger bg-opacity-10 rounded-circle">
                <i class="fi fi-rr-triangle-warning text-danger fs-3 pulse-animation"></i>
              </div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Stock Crítico
              </h6>
              <h3 class="nroProdStockCriticos mb-0 fw-bold text-danger">${criticos}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
  let dashboard = $('#metricasDashboard');
  if (dashboard.length == 0) {
    $(dashboardHTML).insertBefore($('.tabla-ajax').closest('.card'));
  } else {
    dashboard.find('.totalProdDashboard').text(total)
    dashboard.find('.valorTotalInventario').text(`${formateoCampos(valorDivisas,'dineroDolar')}`)
    dashboard.find('.nroProdStockCriticos').text(criticos)
  }

}
function habilitarDeshabilitarPresentacion(cambio = null) {
  let card = $(this);
  let checkBox = card.find('.checkbox-presentacion')
  if (cambio) {
    if (cambio == 'habilitar') {
      checkBox.prop('checked', false);
      card.removeClass('bg-light border-primary')
    } else {
      checkBox.prop('checked', true);
      card.addClass('bg-light border-primary')
    }
  }
  card.toggleClass("bg-light border-primary");
  card.find('.inputFotoPresentacion').val('')
  checkBox.prop("checked", function (i, val) {
    if (val) {
      card.find('.botoneraMEI').addClass('d-none')
      card.find('.btnMEPresentacion').removeClass('btn-success').addClass('btn-secondary')
      card.find('.checkbox-MEC').prop('checked', false)
    } else {
      card.find('.botoneraMEI').removeClass('d-none')
    }
    return !val;
  });
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO
$(document).on("DOMContentLoaded", async function () {
  registrarTutorial();
  
  await new Promise(resolve => setTimeout(resolve, 500));

  await listarDataTable({
    encabezados: {
      "id_producto": "ID",
      "nombre_producto": "NOMBRE",
      "nombre_categoria_producto": "CATEGORÍA",
      "precio_producto": "PRECIO",
      "stock_producto": "STOCK",
      "nombre_unidad_medida": "UNIDAD DE MEDIDA",
    },
    informacionPe: {
      modulo: "productos",
      datosPe: { accion: "listar" },
    },
    botones: (info) => {
      let { permisos, fila } = info;
      let btn = ``;
      if (permisos.productos.includes('ver detalles de los productos')) {
        btn += `
          <li 
            class="list-inline-item align-bottom" 
            data-bs-toggle="tooltip" 
            data-bs-placement="top" 
            title="Ver presentaciones del producto"
          >
            <a 
              href="#" 
              value="${fila.id_producto}"
              class="btnVer avtar avtar-xs btn-link-success btn-pc-default"
              data-bs-toggle="modal" 
              data-bs-target=".modalVerPresentaciones"
            >
              <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
            </a>
          </li>
        `;
      }
      if (permisos.productos.includes('actualizar')) {
        btn += `
          <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
              <a href="#" value="${fila.id_producto}" class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
              <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
              </a>
          </li>
        `;
      }
      if (permisos.productos.includes('eliminar')) {
        btn += `
          <li value="${fila.id_producto}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
              <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
              <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
              </a>
          </li>
        `;
      }
      return `<ul class="list-inline me-auto mb-0">${btn}</ul>`;
    },
    infoTratoEspecial: {
      precio_producto: (info) => {
        return `<strong class="valor">${formateoCampos(info.valor,'dineroDolar')}</strong>`;
      },
      stock_producto: (info) => {
        const stockActual = parseFloat(info.valor);
        const stockMinimo = parseFloat(info.fila?.stock_minimo_producto ?? 0);
        const clase = stockActual <= stockMinimo ? 'danger' : 'success';
        const stockFormateado = stockActual.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return `<span class="badge bg-${clase} px-2 py-1" style="font-size:.85rem;">${stockFormateado}</span>`;
      },
    }
  });

  // Cargar categorías y dibujarlas manualmente para incluir el data-fabricado
  let categorias = await pedirDatosAjax({
    modulo: "categoriasProductos",
    datosPe: { accion: "listar" }
  });
  if (categorias?.icono == 'error') return alertasAjax(categorias);

  let htmlCat = '<option value="">Seleccione una categoría</option>';
  if (categorias && Array.isArray(categorias)) {
    categorias.forEach(cat => {
      htmlCat += `<option value="${cat.id_categoria_producto}" data-fabricado="${cat.necesitan_materias_primas}">${cat.nombre_categoria_producto}</option>`;
    });
  }
  $('.selectCategoriaProducto').html(htmlCat);

  await extraerDatosAjax({
    modulosPeticion: ["unidadesMedidas"],
    accionesPeticion: [{ accion: "listar" }],
    tipoElemento: ["select"],
    elementosDestino: [$(".selectUnidadMedida")],
    datosInsertar: [
      {
        value: "id_unidad_medida",
        texto: "nombre_unidad_medida",
        textoDefault: "Seleccione una unidad",
      }
    ],
  });
  renderizarPresentaciones();
  renderizarDashboard();

  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'productos') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

function registrarTutorial() {
  driverAyuda('productos', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Producto',
          description: 'Haz clic aquí para agregar un nuevo producto al sistema. Podrás definir su categoría, precio, stock y presentaciones.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '#metricasDashboard',
        popover: {
          title: 'Dashboard de Productos',
          description: 'Aquí puedes ver métricas importantes: total de productos, valor del inventario y productos con stock crítico.',
          side: 'top'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Productos',
          description: 'Tabla con todos los productos registrados. Puedes ver su stock, precio y categoría.',
          side: 'top'
        }
      },
      {
        element: '.btnVer',
        popover: {
          title: 'Ver Presentaciones',
          description: 'Haz clic aquí para ver las presentaciones del producto, incluyendo fotos y configuración de tienda online.',
          side: 'left'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Producto',
          description: 'Modifica los datos del producto, sus presentaciones o materias primas.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Producto',
          description: 'Elimina el producto del sistema. Esta acción afectará facturas, pedidos y producciones asociadas.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de productos. Puedes crear productos de fabricación (con materias primas) o de reventa.',
          side: 'top'
        }
      }
    ]
  });
}

$(document).off('click', '.btnVer');
$(document).on('click', '.btnVer', async function (e) {
  e.preventDefault();
  await listarDataTable({
    selectorTabla: '.tabla-presentaciones',
    encabezados: {
      "id_presentacion_producto": "ID PRESENTACIÓN",
      "nombre_presentacion": "NOMBRE",
      "foto_presentacion": "FOTO",
      "mostrar_ecommerce": "TIENDA ONLINE",
    },
    informacionPe: {
      modulo: "productos",
      datosPe: {
        id_producto: $(this).attr('value'),
        accion: "seleccionarUno",
        tipoConsulta: "presentaciones",
      },
    },
    infoTratoEspecial: {
      nombre_presentacion: (info) => {
        return `${info.valor} - ${info.fila.cantidad_pmp} ${info.fila.nombre_unidad_medida}`
      },
      mostrar_ecommerce: (info) => {
        return info.valor == 1 ? 'VISIBLE' : 'OCULTO';
      },
      foto_presentacion: (info) => {
        let foto = info.valor != '' ? info.valor : 'productoDefault.png';
        return `
          <img  
            src="${rutaFotos}presentaciones_productos/${foto}"
            class="estiloFotoRegistro fotoRegistro shadow-sm"
            data-modulo="productos"
            data-tabla_bd="presentaciones_productos"
            data-campo_id="id_presentacion_producto"
            data-valor_id="${info.fila.id_presentacion_producto}"
            data-campo_foto="foto_presentacion"
            data-accion_act="actualizarFoto"
            data-accion_eli="eliminarFoto"
            data-label_foto="Actualizar Foto de la Presentacion"
            data-texto_alerta="La foto de la presentación volverá a la configuración predeterminada"
            data-foto_default="productoDefault.png"
            imgRespaldo="${rutaFotos}presentaciones_productos/productoDefault.png"
          >`;
      }
    }
  });
});

$(document).off("click", ".inputFotoPresentacion");
$(document).on("click", ".inputFotoPresentacion", function (e) {
  e.stopPropagation();
})

$(document).off("click", ".btnFotoPresentacion");
$(document).on("click", ".btnFotoPresentacion", function (e) {
  e.stopPropagation();
  let card = $(this).closest('.filaPresentacion');
  card.find('.inputFotoPresentacion').trigger('click')
});

$(document).off("click", ".btnMEPresentacion");
$(document).on("click", ".btnMEPresentacion", function (e) {
  e.stopPropagation();
  let card = $(this).closest('.filaPresentacion');
  let checkBox = card.find('.checkbox-MEC')
  if ($(this).hasClass('btn-success')) {
    checkBox.prop('checked', false);
    $(this).removeClass('btn-success').addClass('btn-secondary')
  } else {
    checkBox.prop('checked', true);
    $(this).addClass('btn-success').removeClass('btn-secondary')
  }
});

$(document).off("click", "#btnAgregarMateriaPrima");
$(document).on("click", "#btnAgregarMateriaPrima", function () {
  crearFilaMateriaPrima.call(this);
});

$(document).off("click", ".btn-eliminar-materia");
$(document).on("click", ".btn-eliminar-materia", function () {
  let modal = $(this).closest(".modal");
  $(this).closest("tr").remove();
  calcularCostosMateriasPrimas(modal);
});

$(document).off("change", ".select-materia-prima");
$(document).on("change", ".select-materia-prima", function () {
  let modal = $(this).closest(".modal");
  calcularCostosMateriasPrimas(modal);
});

$(document).off("click", ".card-presentacion");
$(document).on("click", ".card-presentacion", function (e) {
  habilitarDeshabilitarPresentacion.call(this);
});

$(document).off("click", ".btn-seleccionar-todas");
$(document).on("click", ".btn-seleccionar-todas", function (e) {
  let modal = $(this).closest(".modal").find(".card-presentacion").each((i, card) => {
    habilitarDeshabilitarPresentacion.call(card, 'habilitar');
  })
});

$(document).off("click", ".btn-deseleccionar-todas");
$(document).on("click", ".btn-deseleccionar-todas", function (e) {
  let modal = $(this).closest(".modal").find(".card-presentacion").each((i, card) => {
    habilitarDeshabilitarPresentacion.call(card, 'deshabilitar');
  })
});

// Cambiar si es fabricado de acuerdo a la categoria seleccionada
$(document).off("change", ".selectCategoria");
$(document).on("change", ".selectCategoria", function () {
  const opcionSeleccionada = $(this).find('option:selected');
  const esFabricado = opcionSeleccionada.attr('data-fabricado') == "1";
  const modal = $(this).closest(".modal");

  if (esFabricado) {
    modal.find(".campos-fabricado").show();
  } else {
    modal.find(".campos-fabricado").hide();
    modal.find(".cuerpoTablaMateriasPrimas").empty();
  }
});

$(document).off("click", ".botonEditar");
$(document).on("click", ".botonEditar", async function (e) {
  e.preventDefault();

  const idProducto = $(this).attr("value");
  const modalTarget = $(this).attr("data-bs-target");
  const modal = $(modalTarget);

  let totalData = await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_producto',
    modulo: 'productos',
  });
  await cargarInputsActualizarQNR.call(modal.find("form"));
  modal.attr("id_producto", idProducto);
  await inicializarModalProducto(modal);
  modal.find('.dineroPositivo').trigger('input');
  calcularCostosMateriasPrimas(modal)
});

$(document).off("submit", ".formularioAjax");
$(document).on("submit", ".formularioAjax", async function (e) {
  e.preventDefault();
  let resultado = await enviarFormulario({
    tipoCuerpo: 'JSON',
    formulario: this,
    modulo: 'productos'
  })
  if (resultado && resultado.icono && resultado.icono == 'success') {
    renderizarDashboard();
    $(this).closest(".modal").find(".card-presentacion").each((i, card) => {
      habilitarDeshabilitarPresentacion.call(card, 'deshabilitar');
    });
    let cuerpoMPHTML = $(this).find(".cuerpoTablaMateriasPrimas");
    cuerpoMPHTML.empty();
    $(this).find(".campos-fabricado").hide();
  }
});

$(document).off("click", ".botonEliminar");
$(document).on("click", ".botonEliminar", async function (e) {
  e.preventDefault();
  let resultado = await eliminarRegistro({
    boton: this,
    campoId: 'id_producto',
    modulo: 'productos',
  });
  if (resultado?.respuestaBack?.icono == 'success') renderizarDashboard();
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'productos');
  if ($(this).hasClass('input-cantidad-materia') || $(this).attr('name') == 'precio_producto') {
    calcularCostosMateriasPrimas($(this).closest('.modal'));
  }
})
//#endregion [ DELEGACIÓN DE EVENTOS ] FIN