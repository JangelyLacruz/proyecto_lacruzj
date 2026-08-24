//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR,
  extraerDatosAjax, pedirDatosAjax, obtenerSiguienteIndice,
  validarEnTiempoReal, formateoCampos, rutaFotos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

// Cache de productos para no pedirlos mil veces al servidor
let cacheProductosServ = null;

function registrarTutorial() {
  driverAyuda('servicios', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Servicio',
          description: 'Haz clic aquí para agregar un nuevo servicio al sistema. Los servicios pueden ser ofrecidos a clientes y consumen productos del inventario.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Servicios',
          description: 'Aquí puedes ver todos los servicios registrados, su precio y si están visibles en la tienda online.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Servicio',
          description: 'Modifica los datos del servicio, su precio o los productos que consume.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Servicio',
          description: 'Elimina el servicio del sistema. Ten cuidado porque puede afectar facturas y pedidos asociados.',
          side: 'left'
        }
      },
      {
        element: '.btnAbrirSelectorProdServ',
        popover: {
          title: 'Agregar Producto al Servicio',
          description: 'Haz clic aquí para seleccionar productos del inventario que serán consumidos al realizar este servicio.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.dataTables_filter input',
        popover: {
          title: 'Buscador de Servicios',
          description: 'Puedes buscar servicios por nombre, ID o cualquier otro campo de la tabla.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de servicios. Recuerda que cada servicio puede consumir productos del inventario automáticamente.',
          side: 'top'
        }
      }
    ]
  });
}

async function cargarProductosParaSelector() {
  if (cacheProductosServ) return cacheProductosServ;
  let items = await pedirDatosAjax({
    modulo: 'productos', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  cacheProductosServ = Array.isArray(items) ? items : [];
  return cacheProductosServ;
}

function agregarFilaProductoServicio(modal, idProducto, nombreProducto, cantidad = '') {
  let cuerpo = modal.find('.cuerpoTablaProductosServicio');
  let codigoFila = obtenerSiguienteIndice(modal, "input", "productos_servicio");

  // Formateamos la cantidad igual que el precio (con coma decimal)
  let cantidadFormateada = '';
  if (cantidad !== '' && cantidad !== null && cantidad !== undefined) {
    let num = parseFloat(cantidad);
    if (!isNaN(num)) {
      cantidadFormateada = num.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
  }

  let html = `<tr data-id-producto="${idProducto}">
    <td>
      <input type="hidden" name="productos_servicio-${codigoFila}-id_producto" value="${idProducto}">
      <div class="serv-prod-nombre">
        <div class="serv-prod-icon"><i class="fi fi-rs-box"></i></div>
        <span class="fw-semibold">${nombreProducto}</span>
      </div>
    </td>
    <td>
      <input type="text" name="productos_servicio-${codigoFila}-cantidad_producto"
             class="form-control dinero input-cantidad-producto"
             placeholder="0,00" value="${cantidadFormateada}">
    </td>
    <td class="text-center">
      <button type="button" class="btn serv-btn-eliminar btn-eliminar-producto-serv">
        <i class="fi fi-rr-trash-check"></i>
      </button>
    </td>
  </tr>`;

  cuerpo.append(html);
  actualizarBadgeProductos(modal);
}

function actualizarBadgeProductos(modal) {
  let cant = modal.find('.cuerpoTablaProductosServicio tr').length;
  let badge = modal.find('.badgeCantProdServ');
  badge.text(cant === 1 ? '1 producto' : cant + ' productos');
}

async function abrirSelectorProductosServicio(modalPadre) {
  let items = await cargarProductosParaSelector();
  if (!items.length) { Swal.fire('Info', 'No hay productos registrados', 'info'); return; }

  let filas = items.map(p => {
    let id = p.id_producto;
    let nombre = p.nombre_producto;
    let unidad = p.nombre_unidad_medida || '';
    let stock = parseInt(p.stock_producto ?? 0);

    return `<tr>
      <td>${nombre}</td>
      <td>${unidad}</td>
      <td>${stock}</td>
      <td>
        <button class="btn btn-sm text-white selProdServicio"
          style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
          data-id="${id}" data-nombre="${nombre} (${unidad})">
          <i class="fi fi-rs-plus me-1"></i>Agregar
        </button>
      </td>
    </tr>`;
  }).join('');

  // Armamos el modal del selector
  $('#modalSelProdServicio').remove();
  $('body').append(`
    <div class="modal fade" id="modalSelProdServicio">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <h5 class="modal-title"><i class="fi fi-rs-box me-2"></i>Seleccionar Producto</h5>
            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <table class="table table-hover table-striped w-100" id="dtSelProdServicio">
              <thead>
                <tr><th>Producto</th><th>Unidad</th><th>Stock</th><th></th></tr>
              </thead>
              <tbody>${filas}</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  `);

  let modalEl = document.getElementById('modalSelProdServicio');
  let modalInst = new bootstrap.Modal(modalEl);

  // Guardamos referencia al modal padre para saber dónde meter la fila
  $(modalEl).data('modal-padre', modalPadre);

  // Oscurecemos el modal de atrás para que se vea bien
  modalPadre.addClass('fact-modal-dimmed');
  modalEl.addEventListener('hidden.bs.modal', () => {
    modalPadre.removeClass('fact-modal-dimmed');
  });

  modalEl.addEventListener('shown.bs.modal', function () {
    if (!$.fn.DataTable.isDataTable('#dtSelProdServicio')) {
      $('#dtSelProdServicio').DataTable({
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
        columnDefs: [{ orderable: false, targets: 3 }]
      });
    }
  }, { once: true });

  modalInst.show();
}

async function inicializarModalServicio(modal) {
  try {
    let idServicio = modal.attr("id_servicio");
    let servicioBD = await pedirDatosAjax({
      modulo: "servicios",
      noGuardarLocal: true,
      datosPe: {
        'accion': "seleccionarUno",
        'id_servicio': idServicio
      },
    });

    let { detallesExtra = {} } = servicioBD;
    let { productos_servicio = [] } = detallesExtra;

    // Vaciamos las filas anteriores y ponemos las del servicio actual
    let cuerpoHTML = modal.find(".cuerpoTablaProductosServicio");
    cuerpoHTML.empty();

    // Cargamos la lista de productos para tener los nombres
    let listaProductos = await cargarProductosParaSelector();

    for (let i = 0; i < productos_servicio.length; i++) {
      let prod = productos_servicio[i];
      let info = listaProductos.find(p => p.id_producto == prod.id_producto);
      let nombre = info ? `${info.nombre_producto} (${info.nombre_unidad_medida || ''})` : `Producto #${prod.id_producto}`;
      agregarFilaProductoServicio(modal, prod.id_producto, nombre, prod.cantidad_producto);
    }
  } catch (error) {
    console.error('Error al inicializar modal servicio:', error);
  }
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO
$(document).on("DOMContentLoaded", async function () {
  //  Registrar el tutorial PRIMERO
  registrarTutorial();
  
  // Esperar un poco para que el DOM esté completamente renderizado
  await new Promise(resolve => setTimeout(resolve, 500));

  await listarDataTable({
    encabezados: {
      "id_servicio": "ID",
      "nombre_servicio": "NOMBRE",
      "nombre_unidad_medida": "UNIDAD DE MEDIDA",
      "precio_servicio": "PRECIO ($)",
      "mostrar_ecommerce": "E-COMMERCE",
    },
    informacionPe: {
      modulo: "servicios",
      datosPe: { accion: "listar" },
    },
    campoIdBtn: 'id_servicio',
    botones: 'CRUD',
    infoTratoEspecial: {
      precio_servicio: (info) => {
        return `<strong>${parseFloat(info.valor).toFixed(2)}$</strong>`;
      },
      mostrar_ecommerce: (info) => {
        return info.valor == 1
          ? '<span class="badge bg-success">Visible</span>'
          : '<span class="badge bg-secondary">Oculto</span>';
      },
      foto_servicio: (info) => {
        let foto = info.valor != '' ? info.valor : 'servicioDefault.png';
        return `
          <img 
            src="${rutaFotos}servicios/${foto}"
            class="estiloFotoRegistro shadow-sm"
            data-modulo="servicios"
            data-tabla_bd="servicios"
            data-campo_id="id_servicio"
            data-valor_id="${info.fila.id_servicio}"
            data-campo_foto="foto_servicio"
            data-accion_act="actualizarFoto"
            data-accion_eli="eliminarFoto"
            data-label_foto="Actualizar Foto del Servicio"
            data-texto_alerta="La foto del servicio volverá a la configuración predeterminada"
            data-foto_default="servicioDefault.png"
          >`;
      }
    }
  });
  
  // Cargar unidades de medida en los selects
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
  
  // Verificar si hay un driver pendiente para servicios
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'servicios') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

// Abrir el DataTable de productos al hacer click en "Agregar Producto"
$(document).off("click", ".btnAbrirSelectorProdServ");
$(document).on("click", ".btnAbrirSelectorProdServ", function () {
  let modalPadre = $(this).closest('.modal');
  abrirSelectorProductosServicio(modalPadre);
});

// Cuando seleccionan un producto del DataTable, lo metemos en la tabla del servicio
$(document).off("click", ".selProdServicio");
$(document).on("click", ".selProdServicio", function () {
  let idProducto = $(this).data('id');
  let nombreProducto = $(this).data('nombre');
  let modalSelector = $('#modalSelProdServicio');
  let modalPadre = modalSelector.data('modal-padre');

  // Si el producto ya está en la tabla, no lo agregamos de nuevo
  let yaExiste = modalPadre.find(`.cuerpoTablaProductosServicio tr[data-id-producto="${idProducto}"]`).length > 0;
  if (yaExiste) {
    Swal.fire('Ya agregado', 'Este producto ya está en la lista', 'info');
    return;
  }

  agregarFilaProductoServicio(modalPadre, idProducto, nombreProducto);
  modalSelector.modal('hide');
});

// Eliminar fila de producto del servicio
$(document).off("click", ".btn-eliminar-producto-serv");
$(document).on("click", ".btn-eliminar-producto-serv", function () {
  let modal = $(this).closest('.modal');
  $(this).closest("tr").remove();
  actualizarBadgeProductos(modal);
});

// Envío de formularios (registrar / actualizar) con validación de al menos 1 producto
$(document).off("submit", ".formularioAjax");
$(document).on("submit", ".formularioAjax", async function (e) {
  e.preventDefault();

  // Validar que todos los productos tengan cantidad
  let faltaCantidad = false;
  $(this).find('.input-cantidad-producto').each(function () {
    let val = $(this).val().trim();
    // Parsear formato con coma (1.234,56) o punto (1234.56)
    let num = parseFloat(val.replace(/\./g, '').replace(',', '.'));
    if (!val || isNaN(num) || num <= 0) {
      faltaCantidad = true;
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });
  if (faltaCantidad) {
    Swal.fire('Cantidad inválida', 'Todos los productos deben tener una cantidad mayor a 0', 'warning');
    return;
  }

  let resultado = await enviarFormulario({
    convertirJSON: true,
    camposFoto: ['foto_servicio'],
    formulario: this,
    modulo: 'servicios'
  });
  if (resultado && resultado.icono && resultado.icono == 'success') {
    let cuerpoHTML = $(this).find(".cuerpoTablaProductosServicio");
    cuerpoHTML.empty();
    // Limpiamos también el cache para que se recarguen los servicios frescos
    cacheProductosServ = null;
  }
});

// Editar registro — cargar datos en el modal
$(document).off("click", ".botonEditar");
$(document).on("click", ".botonEditar", async function (e) {
  e.preventDefault();

  const idServicio = $(this).attr("value");
  const modalTarget = $(this).attr("data-bs-target");
  const modal = $(modalTarget);

  // Limpiar preview de foto anterior y reset del input
  modal.find('.previewFotoServicioActual').addClass('d-none');
  modal.find('.fotoServicioActualImg').attr('src', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');
  modal.find('.previewFotoServicio').addClass('d-none');
  modal.find('.previewFotoServicio img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');
  modal.find('.inputFotoServicio').val('');

  let datosServicio = await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_servicio',
    modulo: 'servicios',
  });

  // Mostrar foto actual si existe
  if (datosServicio && datosServicio.foto_servicio) {
    let fotoUrl = rutaFotos + 'servicios/' + datosServicio.foto_servicio;
    modal.find('.fotoServicioActualImg').attr('src', fotoUrl);
    modal.find('.previewFotoServicioActual').removeClass('d-none');
  }

  // Formatear los inputs de dinero que trae la base de datos para que no se vuelva decimal al actualizar
  modal.find('.dinero, .dineroDolar, .dineroBolivar').each(function () {
    let val = $(this).val();
    if (val !== '') {
      $(this).val(parseFloat(val).toFixed(2));
      let tipoMask = $(this).hasClass('dineroDolar') ? 'dineroDolar' : ($(this).hasClass('dineroBolivar') ? 'dineroBolivar' : 'dinero');
      formateoCampos($(this), tipoMask);
    }
  });

  modal.attr("id_servicio", idServicio);

  // Primero cargar los productos (asíncrono, sin que cargarInputsActualizarQNR los borre)
  await inicializarModalServicio(modal);

  // Luego actualizar los campos QNR — esto puede resetear inputs del form
  // pero NO toca la tabla de productos porque está fuera del form de inputs normales
  await cargarInputsActualizarQNR.call(modal.find("form"));
});

// Eliminar registro
$(document).off("click", ".botonEliminar");
$(document).on("click", ".botonEliminar", function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_servicio',
    modulo: 'servicios',
  });
});

// Formateo campos dinero (precio)
$(document).off("input", ".dinero");
$(document).on("input", ".dinero", function () {
  formateoCampos($(this), 'dinero');
});

// Preview de foto al seleccionar archivo
$(document).off('change', '.inputFotoServicio');
$(document).on('change', '.inputFotoServicio', function () {
  let modal = $(this).closest('.modal');
  let file = this.files && this.files[0];
  let previewBox = modal.find('.previewFotoServicio');
  if (file) {
    let reader = new FileReader();
    reader.onload = function(e) {
      previewBox.find('img').attr('src', e.target.result);
      previewBox.removeClass('d-none');
    }
    reader.readAsDataURL(file);
  } else {
    previewBox.find('img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');
    previewBox.addClass('d-none');
  }
});

// Validación en tiempo real
$(document).off('input', '.validar input, .validar select, .validar textarea');
$(document).on('input', '.validar input, .validar select, .validar textarea', function () {
  validarEnTiempoReal(this, 'servicios');
});
//#endregion [ DELEGACIÓN DE EVENTOS ] FIN