//#region [ IMPORTACIONES ] COMIENZO

import { initExportarDB } from './exportar.js';

//#endregion [ IMPORTACIONES ] FIN

//#region [ MANEJO DE LA CACHE ] COMIENZO
class cacheModelo {
  #sector;
  constructor(sector) {
    this.#sector = sector;
  }
  getItem(ruta) {
    let arrayBusqueda = ruta.split('/');
    let primeraClave = arrayBusqueda.shift();
    let cache = false;
    switch (this.#sector) {
      case 'ls':
        cache = localStorage.getItem(primeraClave);
        break;
      case 'ss':
        cache = sessionStorage.getItem(primeraClave);
        break;
      default:
        console.error('No se pudo inferir sobre el tipo de cache en disco');
        return false;
        break;
    }
    if (!cache) return false;
    cache = !cache.startsWith('{') ? cache : JSON.parse(cache);
    for (const claveAnidada of arrayBusqueda) {
      cache = cache[claveAnidada] ?? false;
      if (!cache) break;
    }
    return cache;
  }
  setItem(ruta, valor) {
    let arrayBusqueda = ruta.split('/');
    let nroClaves = arrayBusqueda.length;
    let primeraClave = arrayBusqueda.shift();
    let cache = false;
    if ((nroClaves) == 1) {
      if (typeof valor != 'string') valor = JSON.stringify(valor);
      switch (this.#sector) {
        case 'ls':
          cache = localStorage.setItem(primeraClave, valor);
          break;
        case 'ss':
          cache = sessionStorage.setItem(primeraClave, valor);
          break;
        default:
          console.error('No se pudo inferir sobre el tipo de cache en disco');
          return false;
          break;
      }
      return;
    }

    //verficar existencia del espacio de guardado
    switch (this.#sector) {
      case 'ls':
        cache = localStorage.getItem(primeraClave);
        break;
      case 'ss':
        cache = sessionStorage.getItem(primeraClave);
        break;
      default:
        console.error('No se pudo inferir sobre el tipo de cache en disco');
        return false;
        break;
    }
    cache = JSON.parse(cache);
    if (!cache || typeof cache == 'string') cache = {};
    let ref = cache;
    arrayBusqueda.forEach((clave, indice) => {
      if (!ref[clave]) ref[clave] = {};
      if (indice + 1 == arrayBusqueda.length) {
        ref[clave] = valor;
      } else {
        ref = ref[clave];
      }
    });
    cache = JSON.stringify(cache);

    switch (this.#sector) {
      case 'ls':
        cache = localStorage.setItem(primeraClave, cache);
        break;
      case 'ss':
        cache = sessionStorage.setItem(primeraClave, cache);
        break;
      default:
        console.error('No se pudo inferir sobre el tipo de cache en disco');
        return false;
        break;
    }
  }
  removeItem(ruta) {
    let arrayBusqueda = ruta.split('/');
    let nroClaves = arrayBusqueda.length;
    let primeraClave = arrayBusqueda.shift();

    if (nroClaves == 1) {
      switch (this.#sector) {
        case 'ls':
          localStorage.removeItem(primeraClave);
          break;
        case 'ss':
          sessionStorage.removeItem(primeraClave);
          break;
        default:
          console.error('No se pudo inferir sobre el tipo de cache en disco');
          return false;
          break;
      }
      return;
    }
    let cache;
    switch (this.#sector) {
      case 'ls':
        cache = localStorage.getItem(primeraClave);
        break;
      case 'ss':
        cache = sessionStorage.getItem(primeraClave);
        break;
      default:
        console.error('No se pudo inferir sobre el tipo de cache en disco');
        return false;
        break;
    }
    cache = JSON.parse(cache);
    let ref = cache;
    for (const [indice, clave] of arrayBusqueda.entries()) {
      if (!ref || !ref[clave]) return;
      if (indice + 1 == arrayBusqueda.length) delete ref[clave];
      else ref = ref[clave];
    }

    cache = JSON.stringify(cache);
    switch (this.#sector) {
      case 'ls':
        cache = localStorage.setItem(primeraClave, cache);
        break;
      case 'ss':
        cache = sessionStorage.setItem(primeraClave, cache);
        break;
      default:
        console.error('No se pudo inferir sobre el tipo de cache en disco');
        return false;
        break;
    }
    return true;
  }
}
export let objCacheLS = new cacheModelo('ls');
export let objCacheSS = new cacheModelo('ss');
//#endregion [ MANEJO DE LA CACHE ] FIN

//#region [ VARIABLES O CONSTANTES GLOBALES ] COMIENZO

export const modoDev = true;
export const rutaAbsoluta = window.location.origin + "/proyecto-lacruz-j/";
export const rutaFotos = rutaAbsoluta + "src/assets/fotosModulos/";
export let vista = $('.nombreVista').val();
export let instanciasDatatable = [];
export let variableDeError = '';
export let inputsActualizarNoRepetir = {};
export const tokenCSRF = $('meta[name="TOKEN_CSRF"]').attr('content');
export const encabezadosPeticiones = new Headers();
encabezadosPeticiones.append('X-TOKEN-CSRF', tokenCSRF);
encabezadosPeticiones.append('X-ES-AJAX', true);
export const coorJLACRUZ = [10.063276, -69.31708];
export let socket;
let peticionesEnVuelo = new Map();

//#region [Lenguajes] COMIENZO
export const españolDataTable = {
  "sProcessing": "Procesando...",
  "sLengthMenu": "Mostrar _MENU_ registros",
  "sZeroRecords": "No se encontraron resultados",
  "sEmptyTable": "Ningún dato disponible en esta tabla",
  "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
  "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
  "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
  "sSearch": "Buscar:",
  "sInfoThousands": ",",
  "sLoadingRecords": "Cargando...",
  "oPaginate": {
    "sFirst": "Primero",
    "sLast": "Último",
    "sNext": "Siguiente",
    "sPrevious": "Anterior"
  },
  "oAria": {
    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
  },
  "buttons": {
    "copy": "Copiar",
    "colvis": "Visibilidad"
  }
};
//#endregion [Lenguajes] FIN

//#region [ Expresiones Regulares ] COMIENZO

export const regexDescripcion = "^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9$\\(\\),.\\/!?# \\x3A\\x2D\\xBF\\x21]{3,255}$";
export const minRegexDescripcion = '3';
export const maxRegexDescripcion = '255';

//#endregion [ Expresiones Regulares ] FIN

//#endregion [ VARIABLES O CONSTANTES GLOBALES ] FIN

//#region [ VALIDACIONES ] COMIENZO
export function funcionAlertaError(input, texto) {
  if ($(input).closest('form').hasClass('login')) {
    return `
      <div class="mensajeError d-flex alert alert-danger alert-dismissible fade show mt-3">
        <i class="fi fi-rr-triangle-warning me-2"></i>
        ${texto}
      </div>
    `;
  } else {
    return `<div class="mensajeError text-danger small mt-1">${texto}</div>`;
  }
};
export function funcionMandarError(input, mensaje = false) {
  input = $(input);
  let mensajeHTML = funcionAlertaError(input, mensaje);
  let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');

  if (input.hasClass('validado') || !input.hasClass('error')) {
    input.removeClass('validado').addClass('error');
  }

  if (!mensaje) return;
  if (contenedorGI.find('.msjError').length > 0) {
    contenedorGI.find('.msjError').find('.mensajeError').remove();
    contenedorGI.find('.msjError').append(mensajeHTML)
  } else {
    contenedorGI.find('.mensajeError').remove();
    contenedorGI.append(mensajeHTML)
  }
}
export function funcionEliminaError(input) {
  input = $(input);
  input.addClass('validado').removeClass('error');
  let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
  contenedorGI.find('.mensajeError').remove();
}
export function reiniciarCampo(input) {
  input = $(input);
  input.removeClass('error validado');
  let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
  contenedorGI.find('.mensajeError').remove();
}
export async function validarEnTiempoReal(input, modulo, noFormatear) {

  input = $(input);
  let data = input.data();
  if (input.is(':disabled') || input.attr('type') == 'hidden') return;
  if (!noFormatear) {
    if (input.hasClass('dinero')) {
      formateoCampos(input, 'dinero')
    }
    if (input.hasClass('dineroPositivo')) {
      formateoCampos(input, 'dineroPositivo')
    }
  }

  let nameImput = input.attr('name')
  let valorIntroducido = input.val();
  let expresionRegular = RegExp(input.attr('pattern')) || false;
  let minimo = input.attr('minlength') || false;
  let maximo = input.attr('maxlength') || false;
  let minimoC = minimo;
  let maximoC = maximo;
  let requerido = input.attr('required') || false;
  let prefijo = false;

  if (data.prefijo) {
    let cuerpo = input.closest('[class^="col-"]').find(data.cuerpo);
    prefijo = input.closest('[class^="col-"]').find(data.prefijo);

    if (cuerpo.length == 0 || prefijo == 0) {
      cuerpo = input.closest('.form-group').find(data.cuerpo);
      prefijo = input.closest('.form-group').find(data.prefijo);
    }


    nameImput = cuerpo.attr('name');
    valorIntroducido = prefijo.val() + cuerpo.val();
    expresionRegular = RegExp(cuerpo.attr('pattern')) || false;
    minimoC = cuerpo.attr('minlengthC') || false;
    maximoC = cuerpo.attr('maxlengthC') || false;
    requerido = cuerpo.attr('required') || false;
    input = cuerpo;
  }

  let esValido = expresionRegular.test(valorIntroducido);

  //Validar si es requerido
  if (requerido && valorIntroducido == '' || (prefijo && prefijo.attr('required') && prefijo.val() == '')) {
    if (prefijo && prefijo.attr('required') && prefijo.val() == '') funcionMandarError(prefijo, 'Este campo es obligatorio!!!');
    if (requerido && valorIntroducido == '') funcionMandarError(input, 'Este campo es obligatorio!!!');
    return;
  } else {
    if (prefijo) funcionEliminaError(prefijo);
    funcionEliminaError(input);
  }

  if ((!requerido && valorIntroducido == '') || input.attr('readonly')) {
    input.removeClass('validado error');
    if (prefijo) prefijo.removeClass('validado error');
    return;
  }

  //Para validar el minimo del campo
  if (minimoC && valorIntroducido != '' && valorIntroducido.length < minimoC) {
    funcionMandarError(input, `El valor del campo debe ser mayor o igual a ${minimo || minimoC} caracteres`)
    return;
  } else {
    funcionEliminaError(input);
  }

  //Para validar el maximo del campo
  if (maximoC && valorIntroducido.length > maximoC) {
    funcionMandarError(input, `El valor del campo debe ser menor o igual a ${maximo || maximoC} caracteres`)
    return;
  } else {
    funcionEliminaError(input)
  }

  //Para validar la contrasena de confirmación
  if (input.attr('id') == 'contrasena2_usuario') {
    if ($('#contrasena1_usuario').val() != $('#contrasena2_usuario').val()) {
      funcionMandarError(input, 'El valor de ambas contraseña debe coincidir');
      return;
    } else {
      funcionEliminaError(input);
    }
  }

  //Para validar el formato del campo
  if (!esValido) {
    funcionMandarError(input, 'El valor del campo no es valido');
    return;
  } else {
    funcionEliminaError(input);
  }

  //Para validar campos que deben tener valores únicos
  if (input.hasClass('noRepetir') && valorIntroducido != '') {
    let proseguir = false;

    if (input.hasClass('formularioActualizar')) {
      if (
        inputsActualizarNoRepetir[nameImput] != valorIntroducido &&
        inputsActualizarNoRepetir[nameImput] != valorIntroducido.toUpperCase()
      ) {
        proseguir = true;
      }
    } else proseguir = true;
    if (proseguir != true) return;

    // Interacción con la BD
    let instruccionesPe = {
      'modulo': modulo,
      'datosPe': {
        'accion': 'listar',
      },
    }
    let registrosExistentes = await pedirDatosAjax(instruccionesPe);
    if (registrosExistentes?.icono == 'error') return alertasAjax(registrosExistentes);
    let mandaAlerta = false;
    for (let i = 0; i < registrosExistentes.length; i++) {
      if (
        registrosExistentes[i][`${nameImput}`] == valorIntroducido ||
        registrosExistentes[i][`${nameImput}`] == valorIntroducido.toUpperCase()
      ) {
        mandaAlerta = true;
        break;
      }
    }
    if (mandaAlerta) {
      funcionMandarError(input, 'El dato ingresado ya se encuentra registrado')
    } else {
      funcionEliminaError(input);
    }
  }

}
export async function validarTodosLosCampos(formulario, modulo) {

  let elementosForm = $(formulario).find('input, select, textarea');
  let promesas = [];
  for (let i = 0; i < elementosForm.length; i++) {
    promesas.push(validarEnTiempoReal($(elementosForm[i]), modulo, true));
  }
  await Promise.all(promesas);

  let hayUnoInvalido = false;
  elementosForm.each((indice, elemento) => {
    if ($(elemento).hasClass('error')) {
      hayUnoInvalido = true;
    }
  })

  if (hayUnoInvalido) {
    Swal.fire({
      icon: 'error',
      title: 'Hay campos inválidos',
      text: 'No se puede enviar el formulario con campos inválidos',
    })
    return true;
  } else {
    return false;
  }
}
export function cargarInputsActualizarQNR() {
  inputsActualizarNoRepetir = {};
  let inputsNR = $(this).find('.formularioActualizar.noRepetir');
  inputsNR.each((indice, input) => {
    input = $(input);
    let name = input.attr('name');
    let valor = input.val();
    let data = $(input).data();
    if (data.prefijo) {
      let cuerpo = input.closest('[class^="col-"]').find(data.cuerpo);
      let prefijo = input.closest('[class^="col-"]').find(data.prefijo);
      name = cuerpo.attr('name');
      valor = prefijo.val() + cuerpo.val();
    }
    inputsActualizarNoRepetir[name] = valor;
  });
}
export function mLength(objeto) {
  if (objeto) {
    if (objeto instanceof FormData) {
      return [...objeto.keys()].length;
    } else {
      return Object.keys(objeto).length
    }
  } else {
    return 0;
  }
}
export function identificarTipoDato(variable) {
  if (typeof variable == 'string' || typeof variable == 'number') return 'string';
  if (typeof variable === 'object' && variable !== null && 'jquery' in variable) return 'jquery';
  if (typeof variable === 'object' && variable !== null && variable.nodeType === 1) return 'html';
  return 'desconocido';
}
export function formateoCampos(elemento, formato) {

  let formatear = (e, f) => {
    switch (f) {
      case 'referencia':
        $(elemento).mask('000000', {
          reverse: true,
        });
        break;
      case 'dinero':
      case 'dineroDolar':
      case 'dineroBolivar':
      case 'dineroPositivo':

        let tipoElemento = identificarTipoDato(e);
        let valorActual = '';
        let el = false;
        let esInput = false;
        if (typeof e == 'string' || typeof e == 'number') {
          valorActual = e
        } else {
          el = $(e);
          esInput = el.is('input, textarea');
          valorActual = esInput ? el.val() : el.text();
        }

        let esNegativo = valorActual.toString().includes('-');
        let limpio = valorActual.toString().replace(/\D/g, "");
        if (limpio === "") limpio = "0";
        let numero = parseFloat(limpio) / 100;
        if (esNegativo && formato != 'dineroPositivo') numero = -Math.abs(numero)
        let nuevoValor = new Intl.NumberFormat('es-ES', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
          useGrouping: 'always'
        }).format(numero);

        if (formato === 'dineroDolar') nuevoValor += '$';
        if (formato === 'dineroBolivar') nuevoValor += ' Bs';

        if (tipoElemento == 'html' || tipoElemento == 'jquery') {
          esInput ? el.val(nuevoValor) : el.text(nuevoValor);
          if (esInput) {
            el[0].setSelectionRange(el.val().length, el.val().length);
          }
        }
        return nuevoValor;
        break;
      default:
        break;
    }
  }

  let tipoElemento = identificarTipoDato(elemento);
  let valoresFormateados = [];
  if (tipoElemento == 'html' || tipoElemento == 'jquery') {
    if ($(elemento).length > 1) {
      $(elemento).each((i, e) => {
        valoresFormateados.push(formatear(e, formato));
      })
    } else {
      valoresFormateados = formatear(elemento, formato);
    }
  } else {
    if (Array.isArray(elemento)) {
      elemento.forEach(e => {
        valoresFormateados.push(ormatear(e, formato));
      });
    } else {
      valoresFormateados = formatear(elemento, formato);
    }
  }

  return valoresFormateados;

}
//#endregion [ VALIDACIONES ] FIN

//#region [ LISTAR CON DATATABLE ] COMIENZO
export async function listarDataTable(instrucciones) {
  const permisos = await pedirDatosAjax({
    modulo: 'accesos',
    datosPe: {
      accion: 'listarPorRol'
    }
  });
  if (permisos?.icono == 'error') return alertasAjax(permisos);

  let {
    selectorTabla = '.tabla-ajax',
    encabezados = null,
    informacionPe = false,
    botones = null,
    camposFuera = [],
    campoIdBtn = false,
    infoTratoEspecial = {},
    infoRenderizar = false,
    datosFooter = {},
    visualizacionResponsiveColumnas = {},
  } = instrucciones;

  let {
    modulo = false
  } = informacionPe
  let botonesCRUD = function (info) {
    let id = info['fila'][campoIdBtn];
    let boton = '';
    boton += '<ul class="list-inline me-auto mb-0">';
    if (modulo && permisos[modulo]) {
      if (permisos[modulo].includes('actualizar')) {
        boton += `
          <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
            <a href="#" value="${id}"  class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
            <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
            </a>
          </li>
        `;
      }
      if (permisos[modulo].includes('eliminar')) {
        boton += `
          <li value="${id}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
              <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
              <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
              </a>
          </li>
        `;
      }
    }
    boton += '</ul>';
    return boton;
  };
  let botonesAccion;
  if (!botones) {
    botonesAccion = null
  } else if (botones == 'CRUD') {
    botonesAccion = await botonesCRUD;
  } else if (botones) {
    botonesAccion = botones;
  }

  // Destruye cualquier instancia existente de DataTables en la tabla para evitar conflictos
  if ($.fn.DataTable.isDataTable(selectorTabla)) {
    $(selectorTabla).DataTable().destroy();
    $(selectorTabla).empty();
    $(selectorTabla).html('<thead></thead><tbody></tbody><tfoot></tfoot>');
  }

  let datos = infoRenderizar ? infoRenderizar : await pedirDatosAjax(informacionPe);
  if (datos?.icono == 'error') return alertasAjax(datos);

  const arregloColumnas = [];
  const dynamicColumnDefs = [];
  let targetsCount = 1;
  let textoEncabezados;
  let vistasPermitidas = [
    'usuarios', 'pedidos', 'rutas', 'empresasEnvios', 'metodos-pago', 'productos',
    'accesos', 'repartidores', 'clientes', 'reportes', 'proveedores', 'modulos',
    'permisos', 'inventario', 'producciones', 'cambiosIva', 'cambios-monedas',
    'bitacora', 'ordenesServicios', 'compras', 'categoriasProductos', 'unidadesMedidas',
    'roles', 'bancos', 'preguntas_seguridad'
  ];
  let laVistaPasa = vistasPermitidas.includes($('.nombreVista').val());

  // Intenta parsear los datos si vienen como un JSON de tipo string
  if (typeof datos === 'string') {
    try {
      datos = await JSON.parse(datos);
    } catch (e) {
      datos = []; // Si falla el parseo, tratamos como un arreglo vacío
    }
  }

  // para construir el objeto con los nombres de los campos que vienen en los datos del servidor
  let keysParaLasColumnas = [];
  if (encabezados) {
    keysParaLasColumnas = Object.keys(encabezados);
  } else if (datos.length >= 1) {
    keysParaLasColumnas = Object.keys(datos[0]);
  } else {
    console.error('No se pudieron encontrar encabezados para las columnas');
    return;
  }

  keysParaLasColumnas.forEach((key) => {
    if (!camposFuera.includes(key)) {
      let transformar = (key) => {
        return (key.charAt(0).toUpperCase() + key.slice(1)).replace(/_/g, ' ');
      }

      textoEncabezados = encabezados ? (encabezados[key] ?? transformar(key)) : transformar(key);
      if (infoTratoEspecial[key]) {
        arregloColumnas.push({
          data: key,
          title: textoEncabezados,
          render: function (valor, type, fila) {
            return infoTratoEspecial[key]({
              valor,
              fila,
            });
          }
        });
      } else {
        arregloColumnas.push({
          data: key,
          title: textoEncabezados
        });
      }

      // Añade la definición de la prioridad 1>n, a menor el numero más rango
      let prioridadColumna = laVistaPasa ? visualizacionResponsiveColumnas[key] : 1;
      let definicionColumna = {
        targets: [targetsCount],
        className: 'dt-center alineado_vertical',
      };
      if (Number.isInteger(prioridadColumna)) {
        definicionColumna.responsivePriority = prioridadColumna;
      }
      dynamicColumnDefs.push(definicionColumna);
      targetsCount++;
    }
  });

  if (arregloColumnas.length === 0) {
    arregloColumnas.push({ data: null, title: 'No hay datos disponibles' });
    dynamicColumnDefs.push({ targets: [0], className: 'tabla dt-center' });
    targetsCount = 1; // Aseguramos que targetsCount esté correcto para la siguiente columna (acciones)
  }
  if (botonesAccion != null) {
    arregloColumnas.push({
      data: null,
      title: 'ACCIONES',
      render: function (data, type, row) {
        let info = {
          permisos,
          'fila': row,
        }
        return botonesAccion(info);
      }
    });
    dynamicColumnDefs.push({
      orderable: false,
      className: 'acciones dt-center alineado_vertical',
      targets: [targetsCount],
      responsivePriority: 1
    });
  }

  // Inicializa DataTables con la configuración construida
  return new Promise((resolve) => {
    const dataTableInstance = $(selectorTabla).DataTable({
      responsive: {
        details: {
          type: 'column',
          target: 0
        }
      },
      ajax: function (data, callback, settings) {
        pedirDatosAjax(informacionPe)
          .then(losDatos => {
            if (losDatos?.icono == 'error') return alertasAjax(losDatos);
            if (informacionPe.funcClass) losDatos = informacionPe.funcClass(losDatos);
            let datosFiltrados = losDatos.map((dato) => {
              Object.keys(dato).forEach(clave => {
                if (camposFuera.includes(clave)) delete dato[clave];
              });
              return dato;
            });
            callback({ data: datosFiltrados });
          })
          .catch(err => {
            if (err?.icono == 'error') alertasAjax(err);
            console.error(err);
            resolve();
          });
      },
      order: [[0, 'desc']],
      autoWidth: true,
      columns: [
        {
          data: null,
          title: '',
          defaultContent: '',
          orderable: false
        },
        ...arregloColumnas
      ],
      columnDefs: [
        ...dynamicColumnDefs,
        {
          className: 'dtr-control dt-center alineado_vertical',
          orderable: false,
          targets: 0
        }
      ],
      language: españolDataTable,
      drawCallback: function () {
        iniciarTooltips(this.api().table().container());
      },
      initComplete: function (settings, json) {
        setTimeout(() => {
          resolve(dataTableInstance);
        }, 0);
      },
      footerCallback: function (row, data, start, end, display) {
        if (datosFooter != {}) {
          const api = this.api();
          // const elementoTfoot = $(api.table().node()).find('tfoot')[0];
          let encabezadosHTML = '';
          let datosHTML = '';
          for (const [encabezado, config] of Object.entries(datosFooter)) {
            let dato = config[0];
            let ancho = config[1];
            encabezadosHTML += `
              <th colspan="${ancho}" class="text-center bg-light-success border border-success">
                ${encabezado}
              </th>
            `;
            datosHTML += `
              <td colspan="${ancho}" class="text-center bg-light-success border border-success">
                ${dato}
              </td>
            `;
          }
          const footerHtml = `
            <tfoot>
              <tr>${encabezadosHTML}</tr>
              <tr>${datosHTML}</tr>
            </tfoot>
          `;
          let tfoot = $(`${footerHtml}`)
          if (this.find('tr').length) {
            this.find('tfoot').remove()
            this.append(footerHtml)
          }
        }
      },
    });
    instanciasDatatable.push(dataTableInstance);
  });
};
export function cambiarFormatos(cadena, tipo) {
  if (tipo == "fecha_hora") {
    const fechaObj = new Date(cadena);
    // Obtener los componentes de la fecha
    const dia = String(fechaObj.getDate()).padStart(2, '0');
    const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
    const ano = fechaObj.getFullYear();

    // Pasamos la hora a formato AM/PM
    let horas = fechaObj.getHours();
    const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
    const ampm = horas >= 12 ? 'PM' : 'AM'; // Determinar si es AM o PM

    // Convertimos las horas de formato de 24h a 12h
    horas = horas % 12;
    horas = horas ? horas : 12; // Si horas es 0, significa 12 AM
    const horasFormateadas = String(horas).padStart(2, '0');

    //Unimos todo
    const fechaFormateada = `${dia}-${mes}-${ano} ${horasFormateadas}:${minutos} ${ampm}`;
    cadena = fechaFormateada;
  } if (tipo == "fecha") {
    const fechaObj = new Date(cadena);
    // Obtener los componentes de la fecha
    const dia = String(fechaObj.getDate()).padStart(2, '0');
    const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
    const ano = fechaObj.getFullYear();

    //Unimos todo
    const fechaFormateada = `${dia}-${mes}-${ano}`;
    cadena = fechaFormateada;
  }
  return cadena;
}
export function reiniciarDataTables() {
  if (instanciasDatatable.length > 0) {
    instanciasDatatable.forEach(instancia => {
      instancia.ajax.reload(null, false);
    });
  }
}
//#endregion [ LISTAR CON DATATABLE ] FIN

//#region [ ENVIAR FORMULARIOS CON AJAX ] COMIENZO

export function convertirHTMLJSON(instrucciones) {

  let {
    elemento,
    camposFuera = null,
    camposFoto = null,
    tipoCuerpo = null
  } = instrucciones
  elemento = $(elemento);

  let cuerpoPeticion = new FormData();
  let elementos = elemento.find('select, input, textarea')
  let datosTransformados = {};

  elementos.each((i, elemento) => {
    elemento = $(elemento);
    let name = elemento.attr('name')
    let type = elemento.attr('type')

    let llaves = name.split('-');
    let valor = elemento.val()
    let referencia = datosTransformados;

    //Depurar campos 
    if (!name || elemento.attr('disabled')) {
      return true;
    }
    if (camposFuera) {
      let coincide = false
      camposFuera.forEach(campoF => {
        if (name.startsWith(campoF) || name == campoF) {
          coincide = true;
        }
      });
      if (coincide) return true;
    }
    if (type == 'checkbox' && !elemento.is(':checked')) return true;

    //Campos de tipo FILE
    if (camposFoto) {
      let esUnCampoFoto = false;
      llaves.forEach(llave => {
        if (camposFoto.includes(llave)) {
          esUnCampoFoto = true;
        }
      });
      if (esUnCampoFoto) return true;
    }
    for (let i = 0; i < llaves.length; i++) {
      const llave = llaves[i];
      if (!referencia[llave]) {
        referencia[llave] = {}
      }
      if (i == llaves.length - 1) {
        referencia[llave] = valor
      } else {
        referencia = referencia[llave]
      }
    }
  });
  let huboUnCampoFoto = false;

  if (camposFoto) {
    camposFoto.forEach(campoFoto => {
      if (elemento.find(`input[name="${campoFoto}"]`).length > 0) {
        huboUnCampoFoto = true;
        const campoHTML = elemento.find('input[name="' + campoFoto + '"]');
        if (
          !campoHTML.attr('multiple') &&
          campoHTML.val() != undefined &&
          campoHTML.val() != '' &&
          campoHTML.val() != [] &&
          campoHTML.val() != null
        ) {
          cuerpoPeticion.append(campoFoto, campoHTML[0].files[0]);
        } else {
          let imagenes = campoHTML[0].files;
          for (let i = 0; i < imagenes.length; i++) {
            const imagen = imagenes[i];
            cuerpoPeticion.append(campoFoto + '[]', imagen);
          }
        }
      }
    });
  }
  if (huboUnCampoFoto) {
    if (mLength(datosTransformados) > 0) {
      cuerpoPeticion.append('metadatos', JSON.stringify(datosTransformados));
    }
  } else if (tipoCuerpo == 'JSONPuro') {
    cuerpoPeticion = datosTransformados;
  } else {
    cuerpoPeticion = JSON.stringify(datosTransformados);
  }
  return cuerpoPeticion;

}
export async function enviarFormulario(instrucciones) {
  let {
    formulario,
    url = null,
    modulo,
    tipoCuerpo = 'formData',
    camposFuera = false,
  } = instrucciones

  formulario = $(formulario);
  const resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Quieres realizar la acción solicitada',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });

  if (resultado.isConfirmed) {
    if (formulario.hasClass('validar')) {
      let validacion = await validarTodosLosCampos(formulario[0], modulo);
      if (validacion != false) {
        return alertasAjax(validacion);
      }
    }

    let cuerpoPeticion = new FormData();

    let VEELCF = function (nameValidar) {
      let existe = false;
      if (camposFuera) {
        camposFuera.forEach(campoF => {
          if (nameValidar.startsWith(campoF)) {
            existe = true
          }
        });
      }
      return existe;
    }

    if (tipoCuerpo == 'formData') {
      let elementos = formulario.find('select, input:not([type="file"]), textarea');
      let elementosFile = formulario.find('input[type="file"]')
      // Metadatos
      elementos.each((c, e) => {
        e = $(e);
        let name = e.attr('name');
        let type = e.attr('type');
        if (!name || e.attr('disabled')) return true;
        if (type == 'checkbox' && !e.is(':checked')) return true;
        let value = e.val();
        if (!VEELCF(name)) cuerpoPeticion.append(name, value);
      });
      // Files
      if (elementosFile.length > 0) {
        elementosFile.each((i, input) => {
          const campoHTML = $(input);
          let nameImagen = campoHTML.attr('name');
          if (!nameImagen) return true;
          if (VEELCF(nameImagen)) return true;
          if (
            !campoHTML.attr('multiple') &&
            campoHTML.val() != undefined &&
            campoHTML.val() != '' &&
            campoHTML.val() != [] &&
            campoHTML.val() != null
          ) {
            cuerpoPeticion.append(nameImagen, campoHTML[0].files[0]);
          } else if (campoHTML[0]?.files.length > 0) {
            let imagenes = campoHTML[0].files;
            for (let i = 0; i < imagenes.length; i++) {
              const imagen = imagenes[i];
              cuerpoPeticion.append(nameImagen + '[]', imagen);
            }
          }
        })
      }
    } else if (tipoCuerpo == 'JSON') {
      let elementos = formulario.find('select, input:not([type="file"]), textarea');
      let elementosFile = formulario.find('input[type="file"]')
      let datosTransformados = {};

      // Metadatos
      elementos.each((i, elemento) => {
        elemento = $(elemento);
        let name = elemento.attr('name')
        if (VEELCF(name)) return true;
        if (!name || elemento.attr('disabled')) return true;
        let type = elemento.attr('type')
        let llaves = name.split('-');
        let valor = elemento.val()
        let referencia = datosTransformados;

        if (type == 'checkbox' && !elemento.is(':checked')) return true;
        for (let i = 0; i < llaves.length; i++) {
          const llave = llaves[i];
          if (!referencia[llave]) {
            referencia[llave] = {}
          }
          if (i == llaves.length - 1) {
            referencia[llave] = valor
          } else {
            referencia = referencia[llave]
          }
        }
      });

      // Files
      let huboUnCampoFoto = false;
      if (elementosFile.length > 0) {
        elementosFile.each((i, input) => {
          const campoHTML = $(input);
          let nameImagen = campoHTML.attr('name');
          if (nameImagen) {
            if (VEELCF(nameImagen)) return true;
            if (
              !campoHTML.attr('multiple') &&
              campoHTML.val() != undefined &&
              campoHTML.val() != '' &&
              campoHTML.val() != [] &&
              campoHTML.val() != null
            ) {
              huboUnCampoFoto = true;
              cuerpoPeticion.append(nameImagen, campoHTML[0].files[0]);
            } else if (input.files.length > 0) {
              let imagenes = campoHTML[0].files;
              for (let i = 0; i < imagenes.length; i++) {
                const imagen = imagenes[i];
                huboUnCampoFoto = true;
                cuerpoPeticion.append(nameImagen + '[]', imagen);
              }
            }
          }
        })
      }

      if (huboUnCampoFoto) {
        cuerpoPeticion.append('metadatos', JSON.stringify(datosTransformados));
      } else {
        cuerpoPeticion = JSON.stringify(datosTransformados);
      }
    }

    const config = {
      method: 'POST',
      headers: encabezadosPeticiones,
      mode: 'cors',
      cache: 'no-cache',
      body: cuerpoPeticion
    };

    let respuesta;
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      let direccion = url ?? rutaAbsoluta + modulo;
      respuesta = await fetch(direccion, config);
    } catch (error) {
      console.error(error)
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }

    const contentType = respuesta.headers.get('Content-Type');

    // Si es una respuesta JSON
    if (contentType.includes('application/json') || contentType.includes('text/html')) {
      const respuestaJSON = await respuesta.json();
      if ((respuestaJSON.icono ?? '') == 'success') {
        reiniciarDataModuloSS(modulo);
        if (modulo == 'monedas' || modulo == 'cambios') {
          reiniciarDataModuloSS(['monedas', 'cambios']);
        }
        reiniciarDataTables();
      }
      respuestaJSON.formulario = formulario
      await alertasAjax(respuestaJSON);
      return respuestaJSON;
    } else if (contentType.includes('application/pdf')) {
      const pdfBlob = await respuesta.blob();
      const urlPDF = URL.createObjectURL(pdfBlob);
      window.open(urlPDF, '_blank');
    } else {
      console.error('Tipo de contenido no reconocido!!!');
    }
  }
}
export function obtenerSiguienteIndice(elementoContenedor, etiqueta, name) {
  const elementosTotales = $(elementoContenedor).find(etiqueta + `[name^="${name}"`);
  let existentes = {};
  elementosTotales.each((i, elemento) => {
    let name = elemento.name;
    let arrayName = name.split('-');
    if (!existentes[arrayName[1]]) {
      existentes[arrayName[1]] = true;
    }
  });
  let i = 0;
  while (existentes[i]) {
    i++;
  }
  return i;
}

//#endregion [ENVIAR FORMULARIOS CON AJAX] FIN

//#region [ ALERTAS AJAX ] COMIENZO
export async function alertasAjax(alerta) {
  let resultado = '';

  let {
    tipo,
    icono,
    titulo,
    texto,
    formulario = null,
  } = alerta

  if (alerta.notifier == true) {
    notifier.show(alerta.titulo, alerta.texto, alerta.icono, rutaAbsoluta + `/src/assets/images/${alerta.icono}Icono.png`, alerta.tiempo ?? 0);
    return;
  }
  switch (tipo) {
    case 'simple':
      resultado = await Swal.fire({
        icon: alerta.icono,
        title: alerta.titulo,
        text: alerta.texto,
        confirmButtonText: 'Aceptar'
      });
      break;
    case 'recargar':
      resultado = await Swal.fire({
        icon: icono,
        title: titulo,
        text: texto,
        confirmButtonText: 'Aceptar'
      });
      if (resultado.isConfirmed) {
        window.location.reload();
      }
      break;
    case 'limpiar':
      resultado = await Swal.fire({
        icon: icono,
        title: titulo,
        text: texto,
        confirmButtonText: 'Aceptar'
      });
      if (formulario) {
        $(formulario)[0].reset();
        $(formulario).find('input,select,textarea').removeClass('error').each((i, elemento) => {
          reiniciarCampo(elemento);
        });
      }
      break;
    case 'limpiarYcerrar':
      resultado = await Swal.fire({
        icon: icono,
        title: titulo,
        text: texto,
        confirmButtonText: 'Aceptar'
      });
      if (formulario) {
        $(formulario)[0].reset();
        $(formulario).find('input,select,textarea').removeClass('error').each((i, elemento) => {
          reiniciarCampo(elemento);
        });
        const botonCerrar = $(formulario).closest('.modal').find('.btn-close');
        botonCerrar.trigger('click');
      }
      break;
    case 'redireccionar':
      window.location.href = alerta.url;
      break;
    case 'alertarYredireccionar':
      resultado = await Swal.fire({
        icon: alerta.icono,
        title: alerta.titulo,
        text: alerta.texto,
        showConfirmButton: false,
        timer: 2000
      });
      setTimeout(() => {
        window.location.href = alerta.url;
      }, 2000);
      break;
    case 'preguntar':
      resultado = await Swal.fire({
        title: alerta.titulo,
        text: alerta.texto,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
      });
      break;
    default:
      break;
  }
  return resultado;
}
//#endregion [ALERTAS AJAX] FIN

//#region [ PARA ELIMINAR REGISTROS ] COMIENZO
export async function eliminarRegistro(instrucciones) {
  let {
    campoId,
    modulo,
    boton
  } = instrucciones

  const resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Estás seguro de eliminar el registro?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });
  if (resultado.isConfirmed) {
    let respuesta;
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      respuesta = await pedirDatosAjax({
        noGuardarLocal: true,
        modulo,
        datosPe: {
          accion: 'eliminar',
          [campoId]: $(boton).attr('value')
        }
      });
      if (respuesta?.icono == 'error') return alertasAjax(respuesta);
    } catch (error) {
      console.error(error)
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }

    // Para actualizar los listados
    if (respuesta.icono == 'success') {
      reiniciarDataModuloSS(modulo);
      reiniciarDataTables();
    }
    let resultadoAlerta = await alertasAjax(respuesta);
    return {
      resultadoAlerta,
      respuestaBack: respuesta
    }
  }
}

//#endregion [ PARA ELIMINAR REGISTROS ] FIN

//#region [ PARA OBTENER DATOS A ACTUALIZAR ] COMIENZO
export async function obtenerDatosRegistro(instrucciones) {
  let {
    datosPe = { accion: 'listar' },
    boton,
    campoId,
    modulo,
  } = instrucciones

  const claseModalObj = $(boton).attr('data-bs-target');
  const claseFormulario = $(boton).attr('claseFormulario') ?? '.formularioAjax';
  let formulario = $(claseModalObj).find(claseFormulario);
  const idRegistro = $(boton).attr('value');
  const respuesta = await pedirDatosAjax({
    modulo,
    datosPe,
    funcionBusqueda: (r) => {
      if (!Array.isArray(r)) r = [r];
      return r.find(ri => ri[campoId] == idRegistro)
    }
  });

  console.log(respuesta);
  if (respuesta?.icono == 'error') {
    await alertasAjax(respuesta);
    return;
  };
  const datosNoAgrupados = respuesta?.datosNoAgrupados ?? respuesta;

  const inputs = formulario.find('select,input,textarea');
  inputs.each((indice, input) => {
    let tipoCampo = $(input).attr('type');
    const nombreCampo = input.name;
    if (tipoCampo == 'checkbox') {
      $(input).prop('checked', (datosNoAgrupados[nombreCampo] == 1 || datosNoAgrupados[nombreCampo] == true));
    } else if (tipoCampo != 'file') {
      if (Object.prototype.hasOwnProperty.call(datosNoAgrupados, nombreCampo)) {
        input.value = datosNoAgrupados[nombreCampo]; // Le Asignamos el valor al input
      }
    }
  });

  const datosInhab = inputs.filter('.inha');
  datosInhab.each((indice, input) => {
    $(input).prop('disabled', true);
  });
  return respuesta;
}
//#endregion [ PARA OBTENER DATOS A ACTUALIZAR ] FIN

//#region [ PARA HACER PETICIONES AJAX ] COMIENZO
export async function pedirDatosAjax(instrucciones) {
  const caching = false;

  let {
    datosPe = {},
    url = false,
    metodo = 'POST',
    modulo = false,
    JSONstring = false,
    noJSON = false,
    noGuardarLocal = false,
    funcionBusqueda = false
  } = instrucciones;
  let accion = datosPe?.accion;
  let buscarDatos = true;
  let cache = false
  let respuesta = '';
  let idPeticion = 'cachingModulos/' + modulo + '/' + JSON.stringify(datosPe)

  if (caching) {
    cache = objCacheLS.getItem(idPeticion);
    if (cache) {
      respuesta = cache;
      buscarDatos = false;
    }
  }
  if (buscarDatos) {
    if (peticionesEnVuelo.has(idPeticion)) {
      respuesta = await peticionesEnVuelo.get(idPeticion);
    } else {
      let promesaRed = (async () => {
        let formData = new FormData();
        if (metodo.toUpperCase() != 'GET') {
          if (JSONstring) {
            formData = JSON.stringify(datosPe);
          } else {
            for (const [clave, valor] of Object.entries(datosPe)) {
              formData.append(clave, valor);
            }
          }
        }

        let rutaCompleta = url ? url : rutaAbsoluta + modulo;

        let config = {
          method: metodo.toUpperCase(),
          headers: encabezadosPeticiones,
          mode: 'cors',
          body: formData
        };
        if (metodo == 'GET') delete (config.body);

        let respuestaFetch = await fetch(rutaCompleta, config);
        const contentType = respuestaFetch.headers.get('Content-Type') ?? '';
        if (contentType.includes('application/pdf')) {
          const pdfBlob = await respuestaFetch.blob();
          const urlPDF = URL.createObjectURL(pdfBlob);
          window.open(urlPDF, '_blank');
          return false;
        }

        if (!noJSON) {
          const textoRespuesta = await respuestaFetch.text();
          if (!textoRespuesta.trim()) return {};
          try {
            return JSON.parse(textoRespuesta);
          } catch (error) {
            console.error('Respuesta no válida del servidor', error, textoRespuesta);
            return {
              icono: 'error',
              titulo: 'Error del servidor',
              texto: 'La respuesta del servidor no pudo interpretarse.',
              detalle: textoRespuesta
            };
          }
        }

        return await respuestaFetch.text();
      })()
      peticionesEnVuelo.set(idPeticion, promesaRed);

      try {
        respuesta = await promesaRed;
        // Guardamos en la cache
        if (
          respuesta && !url && !noGuardarLocal && buscarDatos && caching && !respuesta.icono &&
          !respuesta.Rastro && !respuesta['código de error']
        ) {
          objCacheLS.setItem(idPeticion, respuesta)
        }
      } catch (error) {
        console.error('Ocurrió un error: ', error)
      } finally {
        peticionesEnVuelo.delete(idPeticion);
      }
    }
  }

  //Aplicamos filtro
  if (funcionBusqueda) {
    if (!Array.isArray(respuesta)) respuesta = [respuesta];
    respuesta = await funcionBusqueda(respuesta);
  }

  return respuesta;
}
export function reiniciarDataModuloSS(modulo) {

  let modulosVinculadosPedidos = [
    'productos', 'rutas', 'clientes', 'bancos', 'cambiosIva', 'categoriasProductos',
    'metodos-pago', 'monedas', 'presentaciones', 'servicios', 'repartidores', 'usuarios',
    'empresasEnvios'
  ];
  let borradas = 0;
  if (Array.isArray(modulo)) {
    modulo.forEach(m => {
      if (objCacheLS.removeItem('cachingModulos/' + m)) borradas++;
      if (modulosVinculadosPedidos.includes(m)) {
        objCacheLS.removeItem('cachingModulos/pedidos');
        borradas++;
      }
    });
  } else {
    if (objCacheLS.removeItem('cachingModulos/' + modulo)) borradas++;
    if (modulosVinculadosPedidos.includes(modulo)) {
      objCacheLS.removeItem('cachingModulos/pedidos');
      borradas++;
    }
  }
  objCacheLS.removeItem('cachingModulos/bitacora');
  return borradas;
}
//#endregion [ PARA HACER PETICIONES AJAX ] FIN

//#region [ PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML ] COMIENZO
export async function extraerDatosAjax(instrucciones) {
  variableDeError = '';
  const {
    modulosPeticion: modulos,
    accionesPeticion: acciones,
    tipoElemento: tipos,
    elementosDestino: destinos,
    datosInsertar: datosInsercion,
    funcionBusqueda = []
  } = instrucciones;

  for (let i = 0; i < modulos.length; i++) {
    const modulo = modulos[i];
    const tipoElemento = tipos[i];
    let elementoDestino = $(destinos[i]);
    let funcionBusquedaInd = funcionBusqueda[i] ?? false
    if (Array.isArray(destinos[i]) || destinos[i].length > 1) {
      let elementosDom = $.map(destinos[i], function (objJq) {
        objJq = $(objJq);
        return objJq.toArray();
      });
      elementoDestino = $(elementosDom);
    }
    const datosInsertar = datosInsercion[i];
    const accion = acciones[i];

    const instruccionesPe = {
      modulo,
      datosPe: {},
      funcionBusqueda: funcionBusquedaInd
    };
    for (const [clave, valor] of Object.entries(accion)) {
      instruccionesPe.datosPe[clave] = valor;
    }
    let datosRecibidos = await pedirDatosAjax(instruccionesPe);
    if (datosRecibidos.tipo) {
      if (tipoElemento == 'select') {
        elementoDestino.empty();
        elementoDestino.append('<option value="">Sin Registros</option>');
        continue;
      } else {
        variableDeError = { error: 'sin registros' };
        continue;
      }
    } else {
      variableDeError = { exito: 'Con registros' };
    }
    if (tipoElemento == 'select') {
      // #region LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEM
      let elementoObtClases = elementoDestino[0][0] ?? elementoDestino[0];

      if ($(elementoObtClases).is('option')) {
        elementoObtClases = elementoDestino[0]
      }
      elementoObtClases = $(elementoObtClases);

      if (elementoDestino.length > 1) {
        elementoObtClases = $(elementoDestino[0]);
      }
      const clasesDelSelect = elementoObtClases.attr('class');
      if (!clasesDelSelect) {
        return;
      }
      const arregloDeClases = clasesDelSelect.split(' ');
      const clasesForma = clasesDelSelect.replace(/\s/g, '.');

      // para obtener todos los id's seleccionados hasta el momento
      let registrosSeleccionados = [];
      if (arregloDeClases.includes('OQNPR')) {
        let selectsTotales;
        if (Array.isArray(elementoDestino)) {
          selectsTotales = elementoDestino[0].closest('.contenedorDetalles').find('.' + clasesForma);
        } else {
          selectsTotales = elementoDestino.closest('.contenedorDetalles').find('.' + clasesForma);
        }

        registrosSeleccionados = selectsTotales.map(function () {
          if ($(this).val() != '') {
            return $(this).val();
          }
          return false;
        }).get(); // el .get() transforma el arrays de jquery a DOM
      }
      // #endregion LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEN

      let funcionRenderizarOpciones = (elemento, datosInsertar, registrosDB) => {

        elemento.empty();
        if (datosInsertar.textoDefault) {
          elemento.append(`<option value="">${datosInsertar.textoDefault}</option>`);
        }
        registrosDB.forEach(registroBD => {
          const idRegistroActual = String(registroBD[datosInsertar.value]);
          if (!registrosSeleccionados.includes(idRegistroActual)) {
            let textoOpcion = '';
            if (Array.isArray(datosInsertar.texto)) {
              for (let j = 0; j < datosInsertar.texto.length; j++) {
                if (j == 0) {
                  textoOpcion += registroBD[datosInsertar.texto[j]];
                } else {
                  textoOpcion += ' ' + registroBD[datosInsertar.texto[j]];
                }
              }
            } else {
              textoOpcion = registroBD[datosInsertar.texto];
            }
            elemento.append(
              $('<option>', {
                value: registroBD[datosInsertar.value],
                text: textoOpcion,
                selected: registroBD[datosInsertar.value] == datosInsertar.opcionSeleccionada
              })
            );
          }
        });
      }
      if (Array.isArray(elementoDestino)) {
        array.forEach(elementoInd => {
          funcionRenderizarOpciones(elementoInd, datosInsertar, datosRecibidos)
        });
      } else {
        funcionRenderizarOpciones(elementoDestino, datosInsertar, datosRecibidos)
      }

      // Para validar si quedan o no opciones para mostrar
      let totalOptions = '';
      if (Array.isArray(elementoDestino)) {
        totalOptions = elementoDestino[0].find('option').length;
      } else {
        totalOptions = elementoDestino.find('option').length;
      }
      if (totalOptions == 1) {
        if (Array.isArray(elementoDestino)) {
          elementoDestino.forEach(elemento => {
            elemento.empty();
            elemento.append('<option class="texto-rojo">Sin más opciones</option>');
          });
        } else {
          elementoDestino.empty();
          elementoDestino.append('<option class="texto-rojo">Sin más opciones</option>');
        }
      }
    } else if (tipoElemento == 'input') {
      if (Array.isArray(datosInsertar)) {
        for (let k = 0; k < elementoDestino.length; k++) {
          let elemento = $(elementoDestino[k]);
          elemento.val(datosRecibidos[datosInsertar[k]]);
          elemento.removeClass('error');
          elemento.closest('.form-group').find('.error-message').remove();
        }
      } else {
        elementoDestino.val(datosRecibidos[datosInsertar]);
        elementoDestino.removeClass('error');
        elementoDestino.closest('.form-group').find('.error-message').remove();
      }
    }
  };
}
//#endregion [ PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML ] FIN

//#region [ WEBSOCKETS ] COMIENZO  

// #region [ CONFIGURACIONES Y FUNCIONES GENERALES ] COMIENZO  
export async function initServidorWS() {
  try {
    socket = io(
      // 'https://apithevinanode-production.up.railway.app/',
      'https://api-the-vina-node.onrender.com/',
      // 'http://localhost:1235/',
      {
        reconnection: false,
        auth: {
          datosUsuario: {
            cedula: $('.cedulaIS').val(),
            rol: $('.rolIS').val(),
          }
        }
      }
    )

    let intervaloPollingSinc = false;
    let intervaloReconexion = false;
    let nroIntentosMax = 10;
    let intentos = 0;
    let activarPollings = () => {
      if (!intervaloPollingSinc) {
        intervaloPollingSinc = setInterval(() => {
          procesarAccionesResagadas()
        }, 1000 * 60 * 5)//50 minutos 
      }
      if (!intervaloReconexion) {
        intervaloReconexion = setInterval(() => {
          if (intentos < nroIntentosMax) {
            try {
              socket.connect();
            } catch (error) {

            }
            intentos++;
          }
        }, 1000 * 60 * 100)//100 minutos 
      }
    }
    let desactivarPollings = () => {
      if (intervaloPollingSinc) {
        clearInterval(Number(intervaloPollingSinc));
        intervaloPollingSinc = false;
      }
      if (intervaloReconexion) {
        clearInterval(Number(intervaloReconexion));
        intervaloReconexion = false;
      }
    }

    socket.on('mensajeServidor', (msj) => {
      procesarMensajeWS(msj);
    });
    socket.on('connect', () => {
      intentos = 0;
      desactivarPollings();
    });
    socket.on('disconnect', () => {
      activarPollings();
    });
    socket.on('connect_error', (error) => {
      activarPollings();
    });
  } catch (error) {
  }
}
export async function procesarMensajeWS(instruccionesMsj) {
  if (typeof instruccionesMsj == 'string') {
    instruccionesMsj = JSON.parse(instruccionesMsj);
  }
  let EARBD = async (msj) => { //Eliminar Accion Resagada en la BD
    await pedirDatosAjax({
      'noGuardarLocal': true,
      'JSONstring': true,
      'modulo': 'mensajesWS',
      'datosPe': {
        'accion': "eliminarAccionResagada",
        'AccionMsj': msj
      }
    })
  }
  let procesarMsj = async (msj) => {
    let {
      accion,
      modulo = false,
      alerta = false,
    } = msj
    switch (accion) {
      case "actDT":
        if (modulo == $('.nombreVista').val()) {
          reiniciarDataModuloSS(modulo);
          reiniciarDataTables();
          await EARBD(msj);
        }
        break;
      case "actPrecioDolar":
        if (!msj.precioDolar) {
          cargarPrecioDolar();
        } else {
          $('.tipoDeDolarPrecio').empty();
          $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">Precio del BCV</a>`);
          $('.contenedorPrecioDolar').find('.precio_dolar').text(parseFloat(msj.precioDolar).toFixed(2));
          let infoDolarWS = {
            'precio': msj.precioDolar,
            'tipoPrecio': 'Precio del BCV'
          }
          objCacheSS.setItem('infoDolarWS', infoDolarWS);
        }
        await EARBD(msj)
        break;
      case "alertar":
        reiniciarDataModuloSS('mensajesWS')
        listarNotificaciones();
        alertasAjax(alerta);
        break;
      case "borrarDataModuloSS":
        reiniciarDataModuloSS(modulo);
        await EARBD(msj);
        break;
      default:
        console.error('Acción no reconocida');
        break;
    }
  }

  if (Array.isArray(instruccionesMsj)) {
    instruccionesMsj.forEach(msjInd => {
      procesarMsj(msjInd);
    });
  } else {
    procesarMsj(instruccionesMsj);
  }
}
// #endregion [ CONFIGURACIONES Y FUNCIONES GENERALES ] FIN  

// #region [NOTIFICACIONES DEL SISTEMA] COMIENZO
export async function listarNotificaciones() {
  const notificaciones = await pedirDatosAjax({
    modulo: 'mensajesWS',
    datosPe: {
      accion: 'listarNotificaciones'
    }
  });
  if (notificaciones?.icono == 'error') return alertasAjax(notificaciones);
  let notificacionHTML = '';
  let notificacionesNoLeidas = 0;

  if (!notificaciones.icono) {
    $('.btnETLN').show()
    $('.btnMTLNCL').addClass('link-primary').removeClass('text-muted').attr('href', '#');
    notificaciones.forEach(notificacion => {

      let {
        tipo_notificacion,
        fecha_creacion_notificacion,
        status,
        texto_notificacion,
        titulo_notificacion,
        icono_notificacion,
      } = notificacion

      let fecha = cambiarFormatos(fecha_creacion_notificacion, 'fecha_hora');
      fecha = fecha.split(' ');
      const dia = fecha[0];
      const hora = fecha[1] + ' ' + fecha[2];

      let bgNotificacion = '';
      switch (tipo_notificacion) {
        case 'success':
          bgNotificacion = 'bg-light-success';
          break;
        case 'error':
          bgNotificacion = 'bg-light-danger';
          break;
        default:
          break;
      }
      let bgNoLeida = '';
      if (status == 1) {
        notificacionesNoLeidas++;
        bgNoLeida = 'style="background-color: #4d88ff45;"';
      }

      notificacionHTML += `
        <li ${bgNoLeida}>
            <a href="#" class="d-flex align-items-center">
                <div class="img me-3 ${bgNotificacion}">
                    <img src="${rutaAbsoluta}src/assets/images/${icono_notificacion}Icono.png" alt="Image" class="img-fluid">
                </div>
                <div class="text w-100">
                    <span class="float-end text-muted">${hora}</span>
                <strong>${titulo_notificacion}</strong>
                <p class="p-0 m-0">${texto_notificacion}</p>
                <p class="p-0 m-0">${dia}</p>
                </div>
            </a>
        </li>
      `;
    });
    $('.contenedorNotificaciones').empty().append(notificacionHTML);
    if (notificacionesNoLeidas == 0) {
      $('.nroNotNoLeidas').hide();
      $('.btnMTLNCL').hide()
    } else {
      $('.nroNotNoLeidas').show().text(notificacionesNoLeidas);
      $('.btnMTLNCL').show()
    }
  } else {
    $('.btnETLN').hide()
    $('.btnMTLNCL').removeClass('link-primary').addClass('text-muted').removeAttr('href');
    $('.nroNotNoLeidas').hide();
    $('.contenedorNotificaciones').empty().append(`<h3 class="title text-center ms-1 fs-5">SIN NOTIFICACIONES</h3>`);
  }
}
export async function marcarNotificacionesComoLeidas() {
  let resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Seguro de marcar todas las notificaciones como leídas?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });

  if (resultado.isConfirmed) {
    resultado = await pedirDatosAjax({
      noGuardarLocal: true,
      modulo: 'mensajesWS',
      datosPe: {
        accion: 'marcarTodasNotComoLeidas'
      }
    });
    if (resultado.icono == 'success') {
      reiniciarDataModuloSS('mensajesWS');
      listarNotificaciones();
    }
    alertasAjax(resultado);
  }
}
export async function vaciarBuzonNotificaciones() {
  let resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Seguro que desea eliminar todas las notificaciones?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });
  if (resultado.isConfirmed) {
    resultado = await pedirDatosAjax({
      noGuardarLocal: true,
      modulo: 'mensajesWS',
      datosPe: {
        accion: 'eliminarTodasNot'
      }
    });
    if (resultado?.icono == 'success') {
      reiniciarDataModuloSS('mensajesWS')
      listarNotificaciones();
    }
    alertasAjax(resultado);
  }
}
// #endregion [NOTIFICACIONES DEL SISTEMA] FIN

// #region [ACCIONES RESAGADAS] COMIENZO
export async function procesarAccionesResagadas() {
  let acciones = await pedirDatosAjax({
    'noGuardarLocal': true,
    'modulo': 'mensajesWS',
    'datosPe': {
      'accion': 'listarAccionesResagadas'
    }
  });
  if (acciones?.icono == 'error') return alertasAjax(acciones);
  const accionesMapeadas = acciones.map(
    (
      {
        accion_resagada: accion,
        nombre_modulo: modulo
      }
    ) => (
      {
        accion,
        modulo
      }
    )
  );
  procesarMensajeWS(accionesMapeadas)
}
// #endregion [ACCIONES RESAGADAS] FIN

// #endregion [WEBSOCKETS] FIN

//#region [ E-COMMERCE ] COMIENZO

async function initEcommerce() {
  aggDetallePagoPedido();
  await extraerDatosAjax({
    modulosPeticion: ['metodos-pago', 'monedas', 'bancos', 'bancos', 'cambiosIva'],
    accionesPeticion: [
      { accion: 'listar', tipoConsulta: 'paraEcommerce' },
      { accion: 'listar' },
      { accion: 'listar' },
      { accion: 'listar' },
      { accion: 'listar', tipoConsulta: 'ivaActual' },
    ],
    tipoElemento: ['select', 'select', 'select', 'select', 'input'],
    elementosDestino: [
      $('.selectMetodoPagoPedido'),
      $('.selectMonedaPagoPedido'),
      $('.selectBancoEmisorPagoPedido'),
      $('.selectBancoReceptorPagoPedido'),
      $('.porcentajeIVA'),
    ],
    datosInsertar: [
      {
        'texto': 'nombre_metodo_pago',
        'value': 'id_metodo_pago',
        'textoDefault': 'Seleccione un método de pago'
      },
      {
        'texto': 'nombre_moneda',
        'value': 'id_moneda',
        'textoDefault': 'Seleccione una moneda'
      },
      {
        'texto': 'nombre_banco',
        'value': 'id_banco',
        'textoDefault': 'Seleccione un banco'
      },
      {
        'texto': 'nombre_banco',
        'value': 'id_banco',
        'textoDefault': 'Seleccione un banco'
      },
      'monto_cambio_iva'
    ],
  });
  listarItemPanelCarritoPedido();

}
export async function listarItemPanelCarritoPedido() {
  let itemCarrito = objCacheSS.getItem('itemsCarritoPedido');
  $('.depositoDetallesPedido').empty()
  if (itemCarrito?.productos) {
    let { productos, servicios } = itemCarrito;
    for (const info of Object.values(productos)) {
      let contenedorDetalles = $('.panelCotizacionPedido').find('.depositoDetallesPedido')
      let molde = $('.panelCotizacionPedido').find('.moldeItemPedido').clone();
      let {
        cantidad,
        nombre_producto,
        nombre_presentacion,
        foto_presentacion
      } = info

      molde.data({
        ...info,
      })

      let foto = foto_presentacion != '' ? foto_presentacion : 'productoDefault.png';
      molde.find('.nombreItem').text(nombre_producto + ' - ' + nombre_presentacion);
      molde.find('.imagenItemPedido').attr('src', rutaFotos + 'presentaciones_productos/' + foto);
      molde.find('.cantidadItemCarrito').text(info.cantidad)
      molde.removeClass('d-none moldeItemPedido').addClass('itemPedido');
      contenedorDetalles.append(molde);
    }
  }
  await recalcularTotalesPedido();
}
function sumarRestarItemCarritoPedido() {
  let detalle = $(this).closest('.itemPedido');
  let { id_producto, tipo_item, id_presentacion_producto } = detalle.data();
  const cantidadItem = $(this).siblings(".cantidadItemCarrito");
  let cantidadActual = parseInt(cantidadItem.text() || 0);
  if ($(this).hasClass('btnSumItemPedido')) {
    cantidadActual++;
  } else if ($(this).hasClass('btnResItemPedido') && cantidadActual > 1) {
    cantidadActual--;
  }
  cantidadItem.text(cantidadActual);
  objCacheSS.setItem('itemsCarritoPedido/' + tipo_item + '/' + id_presentacion_producto + '/cantidad', cantidadActual);
  recalcularTotalesPedido();
}
async function recalcularTotalesPedido() {
  let precioDolar = parseFloat($('.precioDolar').val());
  let IVA = parseFloat($('.porcentajeIVA').val()) / 100;
  let totalBase = 0;
  let totalDescuento = 0;
  let tipoMoneda = $('.panelCotizacionPedido').find('.btnTipoPago.active').data('tipo_pago');

  let signoMoneda = '';
  if (tipoMoneda == 'bs') {
    signoMoneda = ' Bs';
  } else {
    signoMoneda = '$';
  }
  $('.panelCotizacionPedido').find('.signoPrecio').text(signoMoneda);
  $('#modalPagoUbicacion').find('.signoPrecio').text(signoMoneda);
  $('#modalPagoDetalles').find('.signoPrecio').text(signoMoneda);

  let totalItems = 0;

  let items = objCacheSS.getItem('itemsCarritoPedido');
  $('.depositoDetallesPedido').find(".itemPedido").each(function () {

    const detalle = $(this);
    let {
      id_producto,
      id_presentacion_producto,
      precio_producto,
      cantidad_pmp
    } = detalle.data();

    let cantidad = parseInt(items.productos[id_presentacion_producto].cantidad);
    totalItems += cantidad;

    let precioBase = parseFloat(precio_producto);
    if (tipoMoneda == 'bs') {
      precioBase *= precioDolar
    }
    detalle.find('.precioBaseItem').text(precioBase.toFixed(2));
    detalle.find('.precioMayorItem').text((precioBase - (precioBase * 0.1)).toFixed(2));


    let cantidadBruta = cantidad * cantidad_pmp;
    let subtotalBase = cantidadBruta * precioBase;
    let subTotalDescuento = subtotalBase;


    if (cantidadBruta >= 20) {
      detalle.find('.subTotalBase').removeClass('d-none');
      subTotalDescuento = (subtotalBase - (subtotalBase * 0.1)).toFixed(2);
    } else {
      detalle.find('.subTotalBase').addClass('d-none');
    }

    detalle.find('.cantidadSubTotalBase').text(subtotalBase);
    detalle.find('.cantidadSubTotalDescuento').text(subTotalDescuento);

    // CALCULO
    totalBase += parseFloat(subtotalBase);
    totalDescuento += parseFloat(subTotalDescuento)
  });

  if (totalItems == 0) {
    $('.carritoVacio').removeClass('d-none');
    $('.nroItemsPedido').addClass('d-none')
  } else {
    $('.carritoVacio').addClass('d-none');
    $('.nroItemsPedido').removeClass('d-none').text(totalItems)
  }

  $(".totalesPedido").find('.cantidadTotalBase').text(totalBase.toFixed(2));
  $(".totalesPedido").find('.cantidadTotalDescuento').text(totalDescuento.toFixed(2));

  let montoDescuentoMayor = parseFloat((totalBase - totalDescuento).toFixed(2));
  if (montoDescuentoMayor <= 0) {
    $('.descuentoPedidoPorMayor').addClass('d-none');
    $('.totalBase').addClass('d-none').removeClass('d-flex');
    $(".cantidadDescuentoPedidoPorMayor").text("0.00");
  } else {
    $('.descuentoPedidoPorMayor').removeClass('d-none');
    $('.totalBase').removeClass('d-none').addClass('d-flex');
    $(".cantidadDescuentoPedidoPorMayor").text(montoDescuentoMayor);
  }

  //Total delivery
  let kmR = $('#modalPagoUbicacion').find('#inputKilometrosTotales').val();
  let distanciaKM = Math.ceil(parseFloat(kmR != '' ? kmR : 0));
  let ruta = await pedirDatosAjax({
    'modulo': 'rutas',
    'datosPe': {
      'accion': 'listar',
    },
    funcionBusqueda: (rutas) => {
      return rutas.find(r => (r.minimo_km_ruta <= distanciaKM && r.maximo_km_ruta >= distanciaKM))
    }
  })
  if (!ruta) {
    return alertasAjax({
      'tipo': 'simple',
      'titulo': 'No hay rutas disponibles',
      'texto': 'Actualmente no existen rutas estipuladas para esa ubicacion',
      'icono': 'warning'
    })
  }
  let precioKm = parseFloat(ruta.precio_ruta);

  if (tipoMoneda == 'bs') precioKm *= parseFloat(precioDolar);
  const subtotalEnvio = parseFloat((distanciaKM * precioKm));
  $('#inputPrecioPorKm').val(precioKm.toFixed(2));
  $('#inputSubtotalEnvio').val(subtotalEnvio.toFixed(2));

  // Calculo pagos
  let totalCancelado = 0;
  let monedas = await pedirDatosAjax({
    'modulo': 'monedas',
    'datosPe': {
      'accion': 'listar'
    }
  });
  if (monedas?.icono == 'error') return alertasAjax(monedas);

  // Formateo de campos
  let detallesPago = $('#modalPagoDetalles').find('.detalles_pago');
  let detalles = detallesPago.toArray();
  let promesas = detalles.map(async (e) => {
    let idMoneda = $(e).find('.selectMonedaPagoPedido').val();
    let montoPago = $(e).find('.inputMontoPagoPedido').val().replaceAll('.', '').replaceAll(',', '.');
    if (idMoneda != '' && montoPago != '0.00' && montoPago != '') {
      let monedaBD = monedas.find(M => (M.id_moneda == idMoneda));
      return parseFloat(monedaBD.valor_moneda * parseFloat(montoPago));
    }
    return 0;
  });

  detallesPago.each((i, e) => {
    formateoCampos($(e).find('.inputMontoPagoPedido'), 'dinero');
  });

  let resultados = await Promise.all(promesas);
  totalCancelado = resultados.reduce((sum, val) => sum + val, 0);

  if (tipoMoneda == 'usd') totalCancelado /= precioDolar;

  //Mandar totales al modal 2 del pago
  let totalGeneralIva = (totalDescuento + subtotalEnvio) + ((totalDescuento + subtotalEnvio) * IVA);
  let restante = (totalCancelado - totalGeneralIva);

  let modalPagoDeta = $('#modalPagoDetalles');
  modalPagoDeta.find('.totalItemsPedido').text(totalDescuento.toFixed(2));
  modalPagoDeta.find('.totalDeliveryPedido').text(subtotalEnvio.toFixed(2));
  modalPagoDeta.find('.sumaTotalPedido').text(totalGeneralIva.toFixed(2))
  modalPagoDeta.find('.canceladoTotalPedido').text(totalCancelado.toFixed(2))
  modalPagoDeta.find('.restanteTotalPedido').text(restante.toFixed(2))
  modalPagoDeta.find('.signoPrecio').text(signoMoneda);

  //Formateo
  let elementosFormatear = $('.panelCotizacionPedido').find(`
    .precioBaseItem,
    .precioMayorItem,
    .cantidadSubTotalBase,
    .cantidadSubTotalDescuento,
    .cantidadTotalBase,
    .cantidadTotalDescuento,
    .cantidadDescuentoPedidoPorMayor
  `).each(function () {
    $(this).text(parseFloat($(this).text()).toFixed(2))
    formateoCampos($(this), 'dinero');
  });
  elementosFormatear = modalPagoDeta.find(`
    .totalItemsPedido,
    .totalDeliveryPedido,
    .sumaTotalPedido,
    .canceladoTotalPedido,
    .restanteTotalPedido
  `)
  formateoCampos(elementosFormatear, 'dinero');
  formateoCampos($('#inputPrecioPorKm, #inputSubtotalEnvio'), 'dinero')
}
function cambiarMonedaCalculoPedido() {
  $(this).siblings().removeClass("active btn-dark text-white").addClass("btn-outline-secondary bg-light border-0 text-dark");
  $(this).addClass("active btn-dark text-white").removeClass("btn-outline-secondary bg-light border-0 text-dark");
  let monedaSel = $(this).data('tipo_pago');
  let contenedor = '';
  if ($(this).closest('.botonera').data('nro_btn') == '1') {
    contenedor = $('#modalPagoDetalles');
  } else {
    contenedor = $('.panelCotizacionPedido')
  }
  let botones = contenedor.find('.btnTipoPago');
  botones.removeClass("active btn-dark text-white").addClass("btn-outline-secondary bg-light border-0 text-dark");
  let botonElegido = botones.filter(`[data-tipo_pago="${monedaSel}"]`)
  botonElegido.addClass("active btn-dark text-white").removeClass("btn-outline-secondary bg-light border-0 text-dark");

  if (monedaSel == 'usd') {
    $('#modalPagoDetalles').find('.infoPagoBCV').addClass('d-none');
  } else {
    $('#modalPagoDetalles').find('.infoPagoBCV').removeClass('d-none');
  }

  recalcularTotalesPedido();
}
function eliminarItemPedido() {
  let detalle = $(this).closest('.itemPedido');
  let { id_producto, tipo_item, id_presentacion_producto } = detalle.data();
  objCacheSS.removeItem('itemsCarritoPedido/' + tipo_item + '/' + id_presentacion_producto);
  detalle.remove();
  recalcularTotalesPedido();
}
function obtenerUbicacion() {
  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });
};
let instanciaMapa = '';
async function cargarMapaPedido(e) {
  e.preventDefault();

  let productos = objCacheSS.getItem('itemsCarritoPedido/productos');
  if (mLength(productos) == 0) {
    await alertasAjax({
      'tipo': 'simple',
      'titulo': 'Sin productos',
      'texto': 'No has agregado productos al carrito todavía',
      icono: 'warning'
    });
    return;
  }

  const offcanvasCarrito = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
  if (offcanvasCarrito) offcanvasCarrito.hide();
  setTimeout(() => {
    $('#modalPagoUbicacion').modal('show');
  }, 200);

  try {

    const contenedorMapa = L.DomUtil.get('contenedorMapaPedido');
    if (contenedorMapa && contenedorMapa._leaflet_id) {
      if (instanciaMapa) {
        instanciaMapa.remove();
        instanciaMapa = null;
      }
    }
    if (contenedorMapa) {
      const resultado = await navigator.permissions.query({ name: 'geolocation' });
      const funcionDevolverDelMapa = async () => {
        const alerta = {
          tipo: 'simple',
          icono: 'warning',
          titulo: 'Permiso denegado!',
          texto: 'No se puede mostrar el mapa sin la autorización del uso del GPS del dispositivo'
        };
        $('#modalPagoUbicacion').find('.btn-close').trigger('click');
        await alertasAjax(alerta);
      };

      if (resultado.state === 'prompt') {
        const respuesta = await alertasAjax({
          tipo: 'preguntar',
          titulo: 'Usar Ubicación actual',
          texto: '¿Esta de acuerdo en que se use su ubicación actual para el pedido?'
        });
        if (!respuesta.isConfirmed) {
          return funcionDevolverDelMapa();
        }
      } else if (resultado.state === 'denied') {
        return funcionDevolverDelMapa();
      }

      let ubicacion = '';
      try {
        ubicacion = await obtenerUbicacion();
      } catch (error) {
        if (error.code == 1) {
          await funcionDevolverDelMapa();
          return;
        }
        return;
      }
      const latitud = ubicacion.coords.latitude;
      const longitud = ubicacion.coords.longitude;

      // ubicación por default (ubicacion de la persona)
      const mapa = L.map('contenedorMapaPedido').setView([latitud, longitud], 15);
      instanciaMapa = mapa;
      let marcadorActual = L.marker([latitud, longitud]).addTo(mapa).bindPopup('Ubicación del envío');
      var posicion = marcadorActual.getLatLng();
      actualizarDetallesEnvio({ 'lat': posicion.lat, 'lng': posicion.lng });

      // Ubicación de JLACRUZ
      const iconoJLACRUZ = L.divIcon({ className: 'iconoHamburguesa' });
      const marcadorJLACRUZ = L.marker([10.063276, -69.31708], { icon: iconoJLACRUZ }).addTo(mapa).bindPopup('JLACRUZ');

      // Capa de visualización      
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="#">OpenStreetMap</a> contributors'
      }).addTo(mapa);

      // Evento del click
      mapa.on('click', function clickEnMapa(e) {
        if (marcadorActual) {
          mapa.removeLayer(marcadorActual);
        }
        marcadorActual = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mapa);
        mapa.panTo([e.latlng.lat, e.latlng.lng]);
        actualizarDetallesEnvio(e.latlng);
      });

      $(document).on('click', '.btnSelUbiActualPedido', async function (e) {
        const ubicacion = await obtenerUbicacion();
        const latitud = ubicacion.coords.latitude;
        const longitud = ubicacion.coords.longitude;
        const latlng = L.latLng(latitud, longitud);

        mapa.flyTo([latitud, longitud], 16, {
          animate: true,
          duration: 2
        });

        if (marcadorActual) {
          mapa.removeLayer(marcadorActual);
        }
        marcadorActual = L.marker([latitud, longitud]).addTo(mapa);
        actualizarDetallesEnvio(latlng);
      });
    }
  } catch (e) {
    console.error(e)
  }
}
async function actualizarDetallesEnvio(latlng) {
  objCacheSS.setItem('itemsCarritoPedido/delivery', {
    'latitud': latlng.lat,
    'longitud': latlng.lng,
  })
  let infoRuta = await pedirDatosAjax({
    JSONstring: true,
    modulo: 'rutas',
    datosPe: {
      accion: 'listar',
      tipoConsulta: 'porCoordenadas',
      coordenadas: {
        partida: {
          latitud: coorJLACRUZ[0],
          longitud: coorJLACRUZ[1]
        },
        llegada: {
          latitud: latlng.lat,
          longitud: latlng.lng
        },
      }
    }
  });
  if (infoRuta?.icono == 'error') return alertasAjax(infoRuta);
  $('#inputKilometrosTotales').val(infoRuta.km_recorrido);
  recalcularTotalesPedido();

  if (infoRuta.nombre_direccion) {
    $('#inputDireccionEnvio').val(infoRuta.nombre_direccion);
  } else {
    $('#inputDireccionEnvio').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  }
}
function aggDetallePagoPedido() {
  const detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  const htmlMolde = $('#modalPagoDetalles').find('.plantillaDetallePago').html();
  const stringMolde = htmlMolde.replace(/\[INDICES\]/g, detallesT.length);
  const molde = $(`<div class="detalles_pago">${stringMolde}</div>`);
  molde.find('.nroDetalle').text(detallesT.length + 1);
  molde.find('.btnEliminarDetallePago').removeClass('d-none');
  molde.removeClass('plantillaDetallePago d-none').addClass('detalles_pago');
  $('#contenedorDetallesPago').append(molde);
  actualizarBotonesEliminar();
  recalcularTotalesPedido();
}
function eliDetallePagoPedido() {
  let detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  if (detallesT.length <= 1) return;
  $(this).closest('.detalles_pago').remove();
  detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  detallesT.each((i, detalle) => {
    detalle = $(detalle)
    detalle.find('.nroDetalle').text(i + 1);
    detalle.find('.selectMetodoPagoPedido').attr('name', `pagos-${i}-id_metodo_pago`)
    detalle.find('.inputMontoPagoPedido').attr('name', `pagos-${i}-monto_pago`)
    detalle.find('.selectMonedaPagoPedido').attr('name', `pagos-${i}-id_moneda`)
    detalle.find('.inputReferenciaPagoPedido').attr('name', `pagos-${i}-referencia_pago`)
    detalle.find('.selectBancoEmisorPagoPedido').attr('name', `pagos-${i}-id_banco_emisor`)
    detalle.find('.selectBancoReceptorPagoPedido').attr('name', `pagos-${i}-id_banco_receptor`)
  });
  actualizarBotonesEliminar();
  recalcularTotalesPedido();
}
function actualizarBotonesEliminar() {
  const total = $('#contenedorDetallesPago').find('.detalles_pago').length;
  if (total <= 1) {
    $('.btnEliminarDetallePago').addClass('d-none');
  } else {
    $('.btnEliminarDetallePago').removeClass('d-none');
  }
}
function mostrarPreviewComprobantes() {
  const preview = $('#modalPagoDetalles').find('#pagoComprobantesPreview');
  preview.empty();

  let archivos = Array.from(this.files);
  archivos.forEach(file => {
    const nombreFoto = $(`
      <div class="pago-file-chip">
        <i class="fi fi-rr-picture"></i>
        <span>${file.name}</span>
      </div>
    `);
    preview.append(nombreFoto);
  });

  let drop = $('#modalPagoDetalles').find('.areaComprobantesPago').find('.msjDropCapturesPago');
  if (archivos.length > 0) {
    drop.addClass('d-none');
  } else {
    drop.removeClass('d-none');
  }
}
async function eliminarPedido(sinAlerta = false) {
  let resultado;
  if (!sinAlerta) {
    resultado = await Swal.fire({
      title: '¿Eliminar pedido?',
      text: 'Esta acción vaciará todos los ítems de tu pedido actual.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#c82333',
      cancelButtonColor: '#4e54c8',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    });
  }
  if ((resultado && resultado.isConfirmed) || sinAlerta) {
    objCacheSS.removeItem('itemsCarritoPedido');
    await reiniciarDataModuloSS('pedidos');
    reiniciarDataTables();
    listarItemPanelCarritoPedido();
    $('#modalPagoDetalles').modal('hide');
    $('#modalPagoDetalles').find('#contenedorDetallesPago').empty();
    aggDetallePagoPedido();
    $('#modalPagoDetalles').find('#inputComprobantes').val('').trigger('change')
    if (!sinAlerta) {
      Swal.fire({
        icon: 'success',
        title: 'Pedido eliminado',
        timer: 1500,
        showConfirmButton: false
      });
    }
  }
}
async function mostrarUOcultarCamposDetallesPagoPedido() {

  let idMetodoPagoSel = $(this).val()

  let detalle = $(this).closest('.detalles_pago');
  if (idMetodoPagoSel == '') {
    detalle.find(`
      .inputReferenciaPagoPedido,
      .selectBancoEmisorPagoPedido,
      .selectBancoReceptorPagoPedido
    `).each((i, e) => {
      $(e).prop('disabled', true).closest('[class*="col-lg-"]').addClass('d-none')
    });

    detalle.find('.selectMonedaPagoPedido')
      .closest('[class*="col-lg-"]').addClass('d-none')

    detalle.find('.selectMetodoPagoPedido')
      .closest('[class*="col-lg-"]')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');

    detalle.find('.inputMontoPagoPedido')
      .closest('[class*="col-lg-"]')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');

    return;
  }
  let metodoPagoBD = await pedirDatosAjax({
    'modulo': 'metodos-pago',
    'datosPe': {
      'accion': 'listar',
    },
    funcionBusqueda: (mps) => {
      return mps.find(mp => (mp.id_metodo_pago == idMetodoPagoSel))
    }
  })
  if (metodoPagoBD?.icono == 'error') return alertasAjax(metodoPagoBD);
  let totalCampos = 0;

  //Moneda
  if (metodoPagoBD.necesita_moneda == 1) {
    detalle.find('.selectMetodoPagoPedido')
      .closest('.col-lg-6')
      .removeClass('col-lg-6')
      .addClass('col-lg-4');
    detalle.find('.inputMontoPagoPedido')
      .closest('.col-lg-6')
      .removeClass('col-lg-6')
      .addClass('col-lg-4');
    detalle.find('.selectMonedaPagoPedido')
      .val('')
      .closest('.col-lg-4')
      .removeClass('d-none');
  } else {
    detalle.find('.selectMetodoPagoPedido')
      .closest('.col-lg-4')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');
    detalle.find('.inputMontoPagoPedido')
      .closest('.col-lg-4')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');
    detalle.find('.selectMonedaPagoPedido')
      .val(2)
      .closest('.col-lg-4')
      .addClass('d-none');
  }

  // Referencia
  if (metodoPagoBD.necesita_referencia == 1) {
    totalCampos++;
    detalle
      .find('.inputReferenciaPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.inputReferenciaPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  //Banco emisor
  if (metodoPagoBD.necesita_banco_emisor == 1) {
    totalCampos++;
    detalle
      .find('.selectBancoEmisorPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.selectBancoEmisorPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  // Banco Receptor
  if (metodoPagoBD.necesita_banco_receptor == 1) {
    totalCampos++;
    detalle
      .find('.selectBancoReceptorPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.selectBancoReceptorPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  let largoCSF = 12 / (totalCampos != 0 ? totalCampos : 3);

  let CSF = detalle.find('.inputReferenciaPagoPedido, .selectBancoEmisorPagoPedido, .selectBancoReceptorPagoPedido')
  CSF.each((i, elemento) => {
    let grupocCampo = $(elemento).closest('[class*="col-lg"]');
    grupocCampo.attr('class', function (i, c) {
      let clase = c.replace(/(^|\s)col-lg-\S+/g, '');
      return c.replace(/(^|\s)col-lg-\S+/g, '');
    });
    grupocCampo.addClass(`col-lg-${largoCSF}`)
  });
  recalcularTotalesPedido();
}
async function enviarPedido() {
  let respuesta = await alertasAjax({
    'tipo': 'preguntar',
    'titulo': 'Confirmar pedido',
    'texto': '¿Desea confirmar y registrar su pedido?'
  })
  if (respuesta.isConfirmed) {

    let hayCamposInvalidos = await validarTodosLosCampos($(this).closest('.modal').find('#contenedorDetallesPago'), 'pedidos');
    if (hayCamposInvalidos) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Campos Inválidos',
        'texto': 'El formulario del pago tiene campos inválidos, verifique e intente de nuevo',
        'icono': 'warning'
      })
    }

    let pagos = await convertirHTMLJSON({
      elemento: $('#modalPagoDetalles #contenedorDetallesPago'),
    })
    pagos = await JSON.parse(pagos);
    let comprobantesPago = await convertirHTMLJSON({
      elemento: $('#modalPagoDetalles .areaComprobantesPago'),
      camposFoto: ['comprobantes_pago']
    })
    let items = objCacheSS.getItem('itemsCarritoPedido');
    if (mLength(items.productos) <= 0) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Sin items en el carrito',
        'texto': 'No puedes enviar un pedido sin articulos',
        'icono': 'warning'
      });
    }
    let productosFil = [];
    for (const [clave, valor] of Object.entries(items.productos)) {
      productosFil.push({
        'id_producto': valor.id_producto,
        'id_presentacion': valor.id_presentacion,
        'id_presentacion_producto': valor.id_presentacion_producto,
        'cantidad': valor.cantidad,
      });
    }
    items.productos = productosFil;

    if (!(comprobantesPago instanceof FormData) || mLength(comprobantesPago) <= 0) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Sin comprobantes de pago',
        'texto': 'Debe agg al menos un comprobante de pago',
        'icono': 'warning'
      });
    }

    let todaInfo = {
      ...items,
      ...pagos,
      ...{
        'accion': 'registrar'
      }
    };
    todaInfo.productos = Object.values(items.productos);
    comprobantesPago.append('metadatos', JSON.stringify(todaInfo));
    const config = {
      method: 'POST',
      headers: encabezadosPeticiones,
      mode: 'cors',
      cache: 'no-cache',
      body: comprobantesPago
    };
    let respuesta;
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      respuesta = await (await fetch(rutaAbsoluta + 'pedidos', config)).json();
    } catch (error) {
      console.error(error)
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }

    if (respuesta.icono == 'success') eliminarPedido(true);
    return alertasAjax(respuesta);
  }
}
async function regresarOffCanvas() {
  $(this).closest('.modal').find('.btn-close').trigger('click');
  (new bootstrap.Offcanvas($('.panelCotizacionPedido'))).show();
}

//#endregion [ E-COMMERCE ] COMIENZO

//#region [ DINAMISMO DEL HTML ] COMIENZO

function initSidebar() {

  //Evento para abrir el sidebar
  $(document).off('click', '.sidebarToggle')
  $(document).on('click', '.sidebarToggle', function (e) {
    $("body").toggleClass("sidebar-closed");
    $(".sidebar").toggleClass("show");
    $(".sidebarBackdrop").toggleClass("show");
  })

  //Evento para cerrar el sidebar
  $(document).off('click', '.sidebarBackdrop')
  $(document).on('click', '.sidebarBackdrop', function (e) {
    $(".sidebar").removeClass("show");
    $(this).removeClass("show");
    $("body").addClass("sidebar-closed");
  })

  $(document).off('click', '.sidebar-menu li')
  $(document).on('click', '.sidebar-menu li', async function (e) {
    e.preventDefault();
    e.stopPropagation();
    let a = $(this).find('a')
    let dirA = window.location.href.split('#')[0];
    let dirN = $(a).attr('href');
    if (dirN == dirA) return;
    let direccion = (dirN).split('/');
    let modulo = direccion[4];

    let respuesta = await fetch(rutaAbsoluta + modulo);
    respuesta = await respuesta.text();
    try {
      respuesta = await JSON.parse(respuesta);
    } catch (e) { }

    if (respuesta?.icono == 'error') {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Permiso no autorizado',
        'texto': 'No cuenta con los permisos necesarios para acceder a esta opción',
        'icono': 'error'
      });
    }
    cambiarEstadoLiSidebar.call(this);
    window.location.href = dirN;
  });

  cargarModuloSeleccionadoSidebar();
}
function cambiarEstadoLiSidebar() {

  if ($(this).hasClass('activa')) {
    return;
  } else {
    $(this).addClass('activa');
    $(this).closest('.sidebar-menu').find('li').not($(this)).removeClass('activa')
  }

  if ($(this).children('.aSubSidebar').length === 0) {
    let textoOpcionSeleccionada = $(this).find('span').text();
    objCacheSS.setItem('moduloSeleccionadoSidebar', textoOpcionSeleccionada);
    moverScrollSidebar($(this));
  }
}
function cargarModuloSeleccionadoSidebar() {
  let opcionSeleccionada = objCacheSS.getItem('moduloSeleccionadoSidebar');

  let opcionSel;
  if (!opcionSeleccionada || opcionSeleccionada === 'null') {
    let hrefSinHash = window.location.href.split('#')[0];
    if (hrefSinHash.endsWith('/')) hrefSinHash = hrefSinHash.slice(0, -1);
    opcionSel = $('.sidebar-menu').find(`a[href="${hrefSinHash}"]`);
  } else {
    opcionSel = $('.sidebar-menu li span').filter(function () {
      return $.trim($(this).text()) === $.trim(opcionSeleccionada);
    });
  }
  if (opcionSel.length == 0) return;

  let selectedLi = opcionSel.closest('li');
  selectedLi.addClass('activa');

  selectedLi.parents('.collapse').each(function () {
    let $collapse = $(this);
    $collapse.addClass('show').attr('aria-expanded', 'true');

    let targetId = $collapse.attr('id');
    if (targetId) {
      $(`[data-bs-target="#${targetId}"], [href="#${targetId}"]`).each(function () {
        $(this).removeClass('collapsed').addClass('active').attr('aria-expanded', 'true');
      });
    }
  });

  moverScrollSidebar(selectedLi);
}
function moverScrollSidebar($item) {
  let $sidebar = $('.sidebar');
  let $scrollContainer = $sidebar.filter(function () {
    return this.scrollHeight > this.clientHeight;
  });

  if (!$scrollContainer.length) {
    $scrollContainer = $('.sidebar-menu');
  }
  if (!$scrollContainer.length || !$item.length) {
    return;
  }

  let sidebarHeight = $scrollContainer.innerHeight();
  let itemOffset = $item.position().top;
  let itemHeight = $item.outerHeight(true);
  let currentScroll = $scrollContainer.scrollTop();
  let desiredScroll = currentScroll + itemOffset - Math.max((sidebarHeight - itemHeight) / 2, 0);

  if (desiredScroll < 0) {
    desiredScroll = 0;
  }

  let maxScroll = $scrollContainer.prop('scrollHeight') - sidebarHeight;
  if (desiredScroll > maxScroll) {
    desiredScroll = maxScroll;
  }

  if (Math.abs(desiredScroll - currentScroll) > 1) {
    $scrollContainer.animate({ scrollTop: desiredScroll }, 160);
  }
}
async function initNotificaciones() {
  $('.headerPrincipal').find('.custom-dropdown').on('show.bs.dropdown', function () {
    let that = $(this);
    setTimeout(function () {
      that.find('.dropdown-menu').addClass('active');
    }, 100);
  });
  $('.custom-dropdown').on('hide.bs.dropdown', function () {
    $(this).find('.dropdown-menu').removeClass('active');
  });
  await listarNotificaciones();
}
function eliminarAriaHidden() {
  $('[aria-hidden="true"]').removeAttr('aria-hidden');
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
        const target = $(mutation.target);
        if (target.attr('aria-hidden') === 'true') {
          // Lo removemos inmediatamente
          target.removeAttr('aria-hidden');
        }
      }
    });
  });
  observer.observe(document.body, {
    attributes: true,
    subtree: true,
    attributeFilter: ['aria-hidden']
  });
}
export function iniciarTooltips(contenedor = document) {
  if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;

  const elementos = contenedor.matches?.('[data-bs-toggle="tooltip"]')
    ? [contenedor]
    : contenedor.querySelectorAll('[data-bs-toggle="tooltip"]');

  elementos.forEach((elemento) => {
    if (!bootstrap.Tooltip.getInstance(elemento)) {
      new bootstrap.Tooltip(elemento);
    }
  });
}
export function mostrarOcultarSpinnerCarga(cambio) {
  let s = $('#spinnerCarga');
  cambio == 'mostrar' ? s.removeClass('d-none') : s.addClass('d-none')
};
export async function cerrarSession(sinAlerta = false) {
  let respuesta;
  if (!sinAlerta) {
    respuesta = await alertasAjax({
      'tipo': 'preguntar',
      'titulo': '¿Desea cerrar la sesión?',
      'texto': 'Si cierra la sesión, deberá iniciar sesión nuevamente con su usuario y contraseña para acceder al sistema',
    });
  }
  if (respuesta?.isConfirmed == true || sinAlerta) {
    respuesta = await pedirDatosAjax({
      'modulo': 'usuarios',
      'noGuardarLocal': true,
      'datosPe': {
        'accion': 'cerrarSesion'
      }
    });
  }
  sessionStorage.clear();
  await alertasAjax(respuesta);
}
function limpiarListenersSesion() {
  //Borramos por default temporizadores viejos
  let idTiempoAlertaCierreSesion = objCacheSS.getItem('usuario/idTiempoAlertaCierreSesion');
  let idTiempoCierreSesion = objCacheSS.getItem('usuario/idTiempoCierreSesion');
  let idIntervaloSondeoSesion = objCacheSS.getItem('usuario/idIntervaloSondeoSesion')

  if (idTiempoAlertaCierreSesion) clearTimeout(Number(idTiempoAlertaCierreSesion || 0));
  if (idTiempoCierreSesion) clearTimeout(Number(idTiempoCierreSesion || 0));
  if (idIntervaloSondeoSesion) clearInterval(Number(idIntervaloSondeoSesion || 0));
}
async function programarReiniciarCierreSesion() {

  //Borramos por default temporizadores viejos
  limpiarListenersSesion()

  //Iniciamos lo nuevo
  let idTiempoAlertaCierreSesion = setTimeout(async () => {
    $(".alertaCierreSesion").removeClass('d-none').animate({
      opacity: 1,
    }, 500);
  }, 1000 * 60 * 4);
  let idTiempoCierreSesion = setTimeout(async () => {
    let alerta = await cerrarSession(true);
    alertasAjax(alerta);
  }, 1000 * 60 * 5);
  let idIntervaloSondeoSesion = setInterval(async () => {
    let nroE = objCacheSS.getItem('usuario/nroEventosUsuario');
    if (nroE >= 1) {

      // Actualizamos en el backend de forma silenciosa y no evidente
      let resultado = await pedirDatosAjax({
        'modulo': 'usuarios',
        'datosPe': {
          'accion': 'refrescarSesion'
        }
      });
      if (resultado?.icono == 'error' || resultado?.tipo == 'redireccionar') return alertasAjax(resultado);
      objCacheSS.setItem('usuario/nroEventosUsuario', 0);

      programarReiniciarCierreSesion()
    }
  }, 1000 * 60 * 3);

  objCacheSS.setItem('usuario/idTiempoAlertaCierreSesion', idTiempoAlertaCierreSesion);
  objCacheSS.setItem('usuario/idTiempoCierreSesion', idTiempoCierreSesion);
  objCacheSS.setItem('usuario/idIntervaloSondeoSesion', idIntervaloSondeoSesion)
}
async function extenderSesion() {
  let nro = objCacheSS.getItem('usuario/nroEventosUsuario');
  if (nro <= 0) objCacheSS.setItem('usuario/nroEventosUsuario', 1);
}
function initEventosSesion() {
  objCacheSS.setItem('usuario/nroEventosUsuario', 0);
  programarReiniciarCierreSesion();
  let eventos = ['keydown', 'click', 'touchstart'];
  eventos.forEach(e => {
    $(document).off(e, extenderSesion);
    $(document).on(e, extenderSesion);
  });
}
function mostrarUOcultarContrasenaInput() {
  let input = $(this).closest('[class*="col-"]').find('input');
  if ($(this).hasClass('fi-rs-eye')) {
    $(this).removeClass('fi-rs-eye').addClass('fi-rs-crossed-eye')
    input.attr('type', 'text');
  } else {
    $(this).addClass('fi-rs-eye').removeClass('fi-rs-crossed-eye')
    input.attr('type', 'password');
  }
}

//#endregion [ DINAMISMO DEL HTML ] FIN

//#region [ CARGAR PRECIO DEL DÓLAR API ] COMIENZO
export async function cargarPrecioDolar() {
  try {
    let infoDolarWS = objCacheSS.getItem('infoDolarWS');
    if (!infoDolarWS) infoDolarWS = {}
    const tipoPrecio = infoDolarWS?.tipoPrecio;
    let precioDolar = '';
    let pedirPorAPI = false;

    if (tipoPrecio == 'Precio Local' || !tipoPrecio) {
      try {
        const dolar = await (await fetch(
          // 'https://apithevinanode-production.up.railway.app/api/precio-dolar-bcv',
          'https://api-the-vina-node.onrender.com/api/precio-dolar-bcv',
          // 'http://localhost:1235/api/precio-dolar-bcv'
        )).json();
        precioDolar = dolar.precioDolar.toFixed(2);
        infoDolarWS['precio'] = precioDolar.toString();
        infoDolarWS['tipoPrecio'] = 'Precio del BCV';
      } catch (error) {
        try {
          const dolar = await (await fetch('https://ve.dolarapi.com/v1/dolares/oficial')).json();
          precioDolar = dolar.promedio.toFixed(2);
          infoDolarWS['precio'] = precioDolar.toString();
          infoDolarWS['tipoPrecio'] = 'Precio del BCV';

        } catch (error) {

          const dolar = await pedirDatosAjax({
            modulo: 'monedas',
            datosPe: {
              accion: 'listar',
            },
            funcionBusqueda: (registros) => {
              return registros.find(dato => dato.id_moneda == 1)
            },
          });
          if (dolar?.icono == 'error') return alertasAjax(dolar);
          precioDolar = dolar.valor_moneda;
          infoDolarWS['precio'] = precioDolar;
          infoDolarWS['tipoPrecio'] = 'Precio Local';
        }
      }
    }

    if (infoDolarWS['tipoPrecio'] == 'Precio Local') {
      $('.tipoDeDolarPrecio').text(infoDolarWS['tipoPrecio']);
    } else {
      $('.tipoDeDolarPrecio').empty();
      $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">${infoDolarWS['tipoPrecio']}</a>`);
    }

    let precio = parseFloat(infoDolarWS['precio']).toFixed(2);
    infoDolarWS['precio'] = precio
    $('.precio_dolar').text(precio);
    objCacheSS.setItem('infoDolarWS', infoDolarWS)
  } catch (error) {
    console.error(error)
    $('.contenedorPrecioDolar').empty().text('ERROR AL CARGAR EL PRECIO DEL DÓLAR');
  }
}
async function initPrecioDolar() {
  let infoDolar = objCacheSS.getItem('infoDolarWS');
  if (!infoDolar) {
    await cargarPrecioDolar();
  } else {
    if (infoDolar['tipoPrecio'] == 'Precio Local') {
      $('.tipoDeDolarPrecio').text(infoDolar['tipoPrecio']);
    } else {
      $('.tipoDeDolarPrecio').empty();
      $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">${infoDolar['tipoPrecio']}</a>`);
    }

    $('.precio_dolar').text(parseFloat(infoDolar['precio']).toFixed(2));
  }
  await extraerDatosAjax({
    modulosPeticion: ['monedas'],
    accionesPeticion: [{ 'accion': 'listar' }],
    tipoElemento: ['input'],
    elementosDestino: [$('.precioDolar')],
    datosInsertar: ['valor_moneda'],
    funcionBusqueda: [
      function (registros) {
        return registros.find(dato => dato.id_moneda == 1)
      },
    ]
  })
}
// #endregion [CARGAR PRECIO DEL DÓLAR API] FIN

//#region [ IMAGENES REGISTROS ] COMIENZO
async function actualizarFotoRegistros() {
  let modal = $(this).closest('.modal')
  let datos = modal.data();
  const formulario = modal.find('.formularioActualizarFotoPerfil');
  const inputFoto = modal.find('#inputFotoPerfil');
  if (inputFoto[0].files.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Atención',
      text: 'Por favor, selecciona una nueva foto para actualizar.',
    });
    return;
  }

  const resultado = await enviarFormulario({
    formulario: formulario,
    modulo: datos.modulo,
  });
  if (resultado && resultado.icono === 'success') {
    const nuevaRuta = $('#previsualizacionFotoPerfilModal').attr('src');
    $(`img[data-tabla_bd="${datos.tabla_bd}"][data-campo_id="${datos.campo_id}"][data-valor_id="${datos.valor_id}"]`)
      .attr('src', nuevaRuta);
    modal.modal('hide');
    reiniciarDataModuloSS(datos.modulo)
  }
}
async function eliminarFotoRegistros() {
  let modal = $(this).closest('.modal');
  let datos = modal.data();

  let resultado = await alertasAjax({
    tipo: 'preguntar',
    titulo: '¿Estás seguro?',
    texto: datos.texto_alerta,
    icono: 'warning',
  });
  if (resultado.isConfirmed) {
    let resultado;
    try {
      mostrarOcultarSpinnerCarga('mostrar')
      resultado = await pedirDatosAjax({
        modulo: datos.modulo,
        datosPe: {
          accion: datos.accion_eli,
          [datos.campo_id]: datos.valor_id
        }
      });
    } finally {
      mostrarOcultarSpinnerCarga('ocultar')
    }
    if (resultado && resultado.icono === 'success') {
      const rutaDefault = rutaFotos + datos.tabla_bd + '/' + datos.foto_default;
      modal.find('#previsualizacionFotoPerfilModal').attr('src', rutaDefault);
      $(`img[data-tabla_bd="${datos.tabla_bd}"][data-campo_id="${datos.campo_id}"][data-valor_id="${datos.valor_id}"]`)
        .attr('src', rutaDefault);
      modal.find('#inputFotoPerfil').val('');
      modal.modal('hide');
      reiniciarDataModuloSS(datos.modulo)
    }
    alertasAjax(resultado);
  }
}
// #endregion [IMAGENES REGISTROS] FIN

//#region [ IP ] COMIENZO
async function obtenerIP() {
  let IP = objCacheSS.getItem('usuario/ip');
  let tiempoIp = objCacheSS.getItem('usuario/tiempo_ip');

  let intervaloBusqueda;
  $(window).on('online', () => {
    intervaloBusqueda = setInterval(async () => {
      try {
        let ipVigente = (await (await fetch('https://api.ipify.org?format=json')).json())?.ip;
        objCacheSS.setItem('usuario/ip', ipVigente);
        objCacheSS.setItem('usuario/tiempo_ip', Date.now());
      } catch (error) {
        console.error(error)
      }
    }, 1000 * 60 * 5);
  });
  $(window).on('offline', () => {
    clearInterval(Number(intervaloBusqueda));
  });

  //La primera vez al cargar el DOM
  if (!IP) {
    try {
      IP = ((await (await fetch('https://api.ipify.org?format=json')).json())?.ip);
      objCacheSS.setItem('usuario/ip', IP);
      objCacheSS.setItem('usuario/tiempo_ip', Date.now());
    } catch (error) {
      console.error(error);
      return false;
    }
  }

  //Cada 5 minutos
  if (tiempoIp && ((Date.now() - parseInt(tiempoIp)) / 60000) > 5) {
    try {
      IP = ((await (await fetch('https://api.ipify.org?format=json')).json())?.ip);
      objCacheSS.setItem('usuario/ip', IP);
      objCacheSS.setItem('usuario/tiempo_ip', Date.now());
    } catch (error) {
      console.error(error);
      return false;
    }
  }

  return IP;
}
//#endregion [ IP ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

//Evento para la precarga datos y eventos
$(document).on('DOMContentLoaded', async function (e) {
  initExportarDB();
  eliminarAriaHidden();
  iniciarTooltips();
  try {
    let ipDispositivo = await obtenerIP();
    encabezadosPeticiones.append('X-IP-DISPOSITIVO', ipDispositivo);
  } catch (error) {
    console.info('No se pudo conseguir la direccion IP del dispositivo')
  }

  let vistasFueraSe = ['login'];
  if (!vistasFueraSe.includes(vista)) {
    initSidebar();
    initNotificaciones();
    initServidorWS();
    extraerDatosAjax({
      modulosPeticion: ['roles'],
      accionesPeticion: [
        { accion: 'listar' },
      ],
      tipoElemento: ['select', 'input'],
      elementosDestino: [$('.selectRoles')],
      datosInsertar: [
        {
          texto: 'nombre_rol',
          value: 'id_rol',
          textoDefault: 'Seleccione una opción'
        },
      ]
    });
    await Promise.all([
      initPrecioDolar()
    ]);
    initEcommerce();
    if (!modoDev) initEventosSesion();
  }
});

//#region [ IMAGENES DE REGISTROS ] COMIENZO

// Sincronizar imagen con la del perfil
$(document).on('click', '.fotoRegistro', function () {
  let modal = $('#modalActualizarFotoPerfil');
  let datos = $(this).data();
  modal.modal('show');
  modal.find('#previsualizacionFotoPerfilModal').attr('src', $(this).attr('src'))
  modal.find('#inputFotoPerfil').val('').attr('name', datos.campo_foto);
  modal.find('#nombreCampoIdRegistroFoto').val(datos.valor_id).attr('name', datos.campo_id);
  modal.find('#modalActualizarFotoPerfilLabel').text(datos.label_foto ?? 'Actualizar imagen de registro');
  modal.find('.inputAccionActFoto').val(datos.accion_act);
  modal.data(datos);
});

// Vinculación foto - input FILE
$(document).on('click', '#btnDispararInputFile', function () {
  $('#inputFotoPerfil').trigger('click');
});

// Previsualización de la imagen seleccionada
$(document).on('change', '#inputFotoPerfil', function (e) {
  const archivo = e.target.files[0];
  if (archivo) {
    const reader = new FileReader();
    reader.onload = function (event) {
      $('#previsualizacionFotoPerfilModal').attr('src', event.target.result);
    };
    reader.readAsDataURL(archivo);
  }
});

// Actualizar foto de perfil
$(document).on('click', '.btnGuardarFotoPerfil', async function () {
  actualizarFotoRegistros.call(this);
});

// Eliminar foto de perfil
$(document).on('click', '.btnEliminarFotoPerfil', async function (e) {
  e.stopPropagation(); // Evitar que el clic se propague al contenedor que abre el input file
  eliminarFotoRegistros.call(this);
});

//#endregion [ IMAGENES DE REGISTROS ] FIN

//#region [ NOTIFICACIONES ] COMIENZO

//Eliminar todas las notificaciones
$(document).off('click', '.btnETLN')
$(document).on('click', '.btnETLN', function () {
  vaciarBuzonNotificaciones();
})

//Marcar todas las notificaciones como leidas
$(document).off('click', '.btnMTLNCL')
$(document).on('click', '.btnMTLNCL', function () {
  marcarNotificacionesComoLeidas();
})

//#endregion [ NOTIFICACIONES ] FIN

//#region [ E-COMMERCE ] COMIENZO

// Regregar offCanvas ecommerce 
$(document).off('click', '.btnAtrasOffCanvasPedido')
$(document).on("click", ".btnAtrasOffCanvasPedido", function () {
  regresarOffCanvas.call(this);
});

//Evento para validar en tiempo real
$(document).off('input blur', '#modalPagoDetalles input, #modalPagoDetalles select, #modalPagoDetalles textarea')
$(document).on('input blur', '#modalPagoDetalles input, #modalPagoDetalles select, #modalPagoDetalles textarea', function () {
  validarEnTiempoReal(this, 'pedidos');
})

//Evento para validar en tiempo real
$(document).off('input', '#modalPagoDetalles .inputReferenciaPagoPedido')
$(document).on('input', '#modalPagoDetalles .inputReferenciaPagoPedido', function () {
  formateoCampos($(this), 'referencia');
})

// Cambiar moneda de calculo
$(document).off('click', '.btnTipoPago')
$(document).on("click", ".btnTipoPago", function () {
  cambiarMonedaCalculoPedido.call(this);
});

//Aumentar o decrementar un item
$(document).off('click', '.btnSumItemPedido, .btnResItemPedido')
$(document).on("click", ".btnSumItemPedido, .btnResItemPedido", function () {
  sumarRestarItemCarritoPedido.call(this);
});

//Eliminar un item
$(document).off("click", ".btnEliItemPedido");
$(document).on("click", ".btnEliItemPedido", function () {
  eliminarItemPedido.call(this)
});

// cargar mapa leafteat
$(document).off('click', '.btnProcesarPedido');
$(document).on('click', '.btnProcesarPedido', function (e) {

  cargarMapaPedido.call(this, e);
});

//Agg detalle pago pedido
$(document).off('click', '.btnAggDetallePagoPedido');
$(document).on('click', '.btnAggDetallePagoPedido', function () {
  aggDetallePagoPedido();
});

// Eliminar detalle Pago Pedido
$(document).off('click', '.btnEliminarDetallePago');
$(document).on('click', '.btnEliminarDetallePago', function () {
  eliDetallePagoPedido.call($(this));
});

// Preview de comprobantes
$(document).off('change', '#modalPagoDetalles #inputComprobantes');
$(document).on('change', '#modalPagoDetalles #inputComprobantes', function () {
  mostrarPreviewComprobantes.call(this);
});

// Borrrar detalles del pedido
$(document).off('click', '#btnEliminarPedido');
$(document).on('click', '#btnEliminarPedido', async function () {
  eliminarPedido();
});

// Enviar pedido
$(document).off('click', '#btnConfirmarPedido');
$(document).on('click', '#btnConfirmarPedido', function () {
  enviarPedido.call(this);
});

// Sincronizar Leaflet con el ciclo de vida del Modal
$(document).on('shown.bs.modal', '#modalPagoUbicacion', function () {
  if (instanciaMapa) {
    setTimeout(() => {
      instanciaMapa.invalidateSize();
    }, 200);
  }
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('change', '.selectMetodoPagoPedido');
$(document).on('change', '.selectMetodoPagoPedido', function () {
  mostrarUOcultarCamposDetallesPagoPedido.call(this);
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('input', '.inputMontoPagoPedido');
$(document).on('input', '.inputMontoPagoPedido', function () {
  recalcularTotalesPedido();
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('change', '.selectMonedaPagoPedido');
$(document).on('change', '.selectMonedaPagoPedido', function () {
  recalcularTotalesPedido();
});

//#endregion [ E-COMMERCE ] FIN

//#region [ IMAGENES ] COMIENZO

// Escucha todos los errores de carga en la página
document.addEventListener('error', function (event) {
  if (event.target.tagName.toLowerCase() === 'img') {
    const imagenFallo = event.target;
    imagenFallo.onerror = null;
    imagenFallo.src = $(imagenFallo).attr('imgRespaldo');
  }
}, true);

//#endregion [ IMAGENES ] FIN

//#region [ SESION ] COMIENZO

// Para extender el tiempo de la sesión
$(document).off('click', '.btnExtenderSesion')
$(document).on('click', '.btnExtenderSesion', async function () {
  $(".alertaCierreSesion").animate({
    opacity: 0,
  }, 500, function () {
    $(this).addClass('d-none');
  });
  // Actualizamos en el backend de forma silenciosa y no evidente
  let resultado = await pedirDatosAjax({
    'modulo': 'usuarios',
    'datosPe': {
      'accion': 'refrescarSesion'
    }
  });
  if (resultado?.icono == 'error' || resultado?.tipo == 'redireccionar') return alertasAjax(resultado);
  objCacheSS.setItem('usuario/nroEventosUsuario', 0);
  programarReiniciarCierreSesion();
});
//#endregion [ SESION ] FIN

//#region [ DINAMISMO HTML ] COMIENZO

// Para desplegar las columnas ocultas en el DataTable 
$(document).off('click', '.btnDesplegarDT')
$(document).on('click', '.btnDesplegarDT', function () {
  let elementosOcultos = $(this).closest('.card-body').find('.row.dt-row').find('[class*="dt-CDT"]:hidden')
  if (elementosOcultos.length == 0) return;
  elementosOcultos.addClass('visualizarAjuro');
  $(this).addClass('d-none')
  $(this).closest('.card-body').find('.btnPlegarDT').removeClass('d-none');
});

// Para plegar las columnas que no caben dentro de la pantalla en el DataTable 
$(document).off('click', '.btnPlegarDT')
$(document).on('click', '.btnPlegarDT', function () {
  $(this).addClass('d-none')
  $(this).closest('.card-body').find('.btnDesplegarDT').removeClass('d-none');
  $(this).closest('.card-body').find('.row.dt-row').find('.visualizarAjuro').removeClass('visualizarAjuro')
});

$(document).off('click', '.btnMostrarOcultarContrasena');
$(document).on('click', '.btnMostrarOcultarContrasena', async function () {
  mostrarUOcultarContrasenaInput.call(this);
})

// Para escuchar cuando la pantalla cambie de tamaño
$(window).on('resize', function () {
  let ancho = $(window).width();

  if (ancho <= 1200) {
    $('.botoneraDT').each((i, botonera) => {
      let btnDesplegar = $(botonera).closest('.card').find('.btnDesplegarDT');
      let hayUnaOculta = false;
      let celdas = $(botonera).closest('.card').find('th, td');
      celdas.each((i, c) => {
        if ($(c).css('display') == 'none') {
          hayUnaOculta = true;
          return false;
        }
      })
      if (hayUnaOculta) {
        btnDesplegar.css('display', 'block');
      } else {
        btnDesplegar.css('display', 'none');
      }
    })
  } else {
    $('.botoneraDT').each((i, botonera) => {
      $(botonera).closest('.card').find('.btnDesplegarDT').css('display', 'none');
    })
  }
});

//#endregion [ DINAMISMO HTML ] FIN


//Cerrar sesión
$(document).off('click', '.btnCerrarSession')
$(document).on('click', '.btnCerrarSession', function () {
  cerrarSession();
})

//Formulario del perfil
$(document).off('submit', '.formularioPerfil')
$(document).on('submit', '.formularioPerfil', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'usuarios'
  });
})

//Plasmar los datos en el formulario de los usuarios
$(document).off('click', '.btnEditarPerfil')
$(document).on('click', '.btnEditarPerfil', function () {
  obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
    datosPe: {
      'accion': 'seleccionarUno',
      'cedula_usuario': $(this).attr('value'),
    }
  });
})

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN

