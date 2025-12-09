//#region [VARIABLES O CONSTANTES GLOBALES] COMIENZO
export const rutaAbsoluta = window.location.origin + "/proyecto-lacruz-j/";
export let esteFormulario;
export let vista = $('.nombreVista').val();
export let instanciasDatatable = [];
export let encabezados = {};
export let modulo = '';
export let variableDeError = '';
export let inputsActualizarNoRepetir = {};

//#region [Lista de encabezados según la vista] COMIENZO
export let Camposfuera = ['status'];
switch (vista) {
    case 'id_venta':
        encabezados = {
            'productos': {
                "nombre_producto": "PRODUCTO",
                "precio_producto": "PRECIO",
                "cantidad_producto": "CANTIDAD",
                "detalle_producto": "DETALLES",
                "sub_total": "SUBTOTAL",
            },
            'promociones': {
                "promocion": "PROMOCIÓN",
                "producto": "PRODUCTO",
                "cantidad": "CANTIDAD",
                "detalles": "DETALLES",
                "pllevar": "¿P/LLEVAR?",
            },
            'insumos': {
                "nombre_insumo": "INSUMO",
                "precio_unitario_insumo": "PRECIO",
                "cantidad_insumo": "CANTIDAD",
                "sub_total": "SUBTOTAL",
            },
            'pagos': {
                "nombre_metodo_pago": "MÉTODO DE PAGO",
                "nombre_moneda": "MONEDA",
                "monto_pago": "MONTO",
                "banco_emisor": "BANCO EMISOR",
                "referencia_pago": "REF",
                "banco_receptor": "BANCO RECEPTOR",
            },
            'listaGeneral': {
                "id_venta": "ID",
                "CLIENTE": "CLIENTE",
                "MONTO": "MONTO",
                "FECHA": "FECHA",
                "estado": "¿DESPACHADA?",
            }
        }
        modulo = 'ventas';
        Camposfuera = [];
        break;
    case 'cedula_cliente':
        encabezados = {
            //Clientes
            "cedula_cliente": "CÉDULA",
            "nombre_cliente": "NOMBRE",
            "apellido_cliente": "APELLIDO",
            "telefono_cliente": "TELÉFONO",
        }
        modulo = 'clientes'
        break;
    case 'id_cambio_iva':
        encabezados = {
            //Cambios
            "id_cambio_iva": "ID",
            "monto_cambio_iva": "PORCENTAJE (%)",
            "fecha_cambio_iva": "FECHA",
        }
        modulo = 'cambios'
        break;
    case 'id_insumo':
        encabezados = {
            //para los insumos
            "id_insumo": "ID",
            "nombre_insumo": "NOMBRE",
            "precio_unitario_insumo": "PRECIO UNITARIO",
            "stock_insumo": "STOCK",
        }
        modulo = 'insumos'
        break;
    case 'id_metodo_pago':
        encabezados = {
            //para los insumos
            "id_metodo_pago": "ID",
            "nombre_metodo_pago": "NOMBRE",
            "necesita_moneda": "¿NECESITA MONEDA?",
        }
        modulo = 'metodos-pago'
        break;
    case 'id_moneda':
        encabezados = {
            "id_moneda": "ID",
            "nombre_moneda": "NOMBRE",
            "simbolo_moneda": "SÍMBOLO",
            "valor_moneda": "VALOR (Bs)",
        }
        modulo = 'monedas'
        break;
    case 'id_cambio_moneda':
        encabezados = {
            "id_cambio_moneda": "ID",
            "nombre_moneda": "MONEDA",
            "valor_cambio": "VALOR (Bs)",
            "fecha_cambio": "FECHA",
        }
        modulo = 'monedas'
        break;
    case 'cedula_usuario':
    case 'registrar_usuario':
    case 'olvidarCon_1':
    case 'olvidarCon_2':
        encabezados = {
            //Usuarios
            "cedula_usuario": "CÉDULA",
            "nombre_usuario": "NOMBRE",
            "apellido_usuario": "APELLIDO",
            "correo_usuario": "CORREO",
            "telefono_usuario": "TELÉFONO",
            "nombre_rol": "ROL",
            "usuario_usuario": "USUARIO",
        }
        modulo = 'usuarios'
        break;
    case 'id_rol':
        encabezados = {
            //Roles
            "id_rol": "ID",
            "nombre_rol": "NOMBRE",
        }
        modulo = 'roles'
        break;
    case 'id_bitacora':
        encabezados = {
            "id_bitacora": "ID",
            "usuario": "USUARIO",
            "nombre_modulo": "MÓDULO",
            "nombre_accion": "ACCIÓN",
            "nombre_so": "S.O",
            "fecha_bitacora": "FECHA",
            "descripcion": "DET",
            "resultado_accion_bitacora": "RESULT",
        }
        modulo = 'bitacora'
        break;
    case 'id_producto':
        encabezados = {
            "id_producto": "ID",
            "nombre_categoria": "CATEGORÍA",
            "nombre_producto": "NOMBRE",
            "precio_producto": "PRECIO",
            "descripcion_producto": "DESCRIPCIÓN",
            "necesita_contornos": "¿NECESITA CONTORNOS?",
            "necesita_rellenos": "¿NECESITA RELLENOS?",
        }
        modulo = 'productos'
        break;
    default:
        break;
}
//#endregion [Lista de encabezados según la vista] FIN

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

//#endregion [VARIABLES O CONSTANTES GLOBALES] FIN

//#region [ VALIDACIONES ] COMIENZO
async function validarEnTiempoReal(input) {

    input = $(input);
    let nameImput = input.attr('name')
    let valorIntroducido = input.val();
    let minimo = input.attr('minlength') || false;
    let maximo = input.attr('maxlength') || false;
    let expresionRegular = RegExp(input.attr('pattern')) || false;
    let requerido = input.attr('required') || false;
    let esValido = expresionRegular.test(valorIntroducido);

    let funcionAlertaError = (texto) => {
        return `
            <div class="mensajeError text-danger small mt-1">${texto}</div>
        `;
    };
    if ($(input).closest('form').hasClass('login')) {
        funcionAlertaError = (texto) => {
            return `
            <div class="mensajeError d-flex alert alert-danger alert-dismissible fade show mt-3">
                <i class="fi fi-rr-triangle-warning me-2"></i>
                ${texto}
            </div>
        `;
        }
    }
    let funcionMandarError = (mensaje) => {
        let mensajeHTML = funcionAlertaError(mensaje);
        let contenedorGI = input.closest('[class^="col-"]');
        input.removeClass('validado').addClass('error');
        if (contenedorGI.find('.msjError').length > 0) {
            contenedorGI.find('.msjError').find('.mensajeError').remove();
            contenedorGI.find('.msjError').append(mensajeHTML)
        } else {
            contenedorGI.find('.mensajeError').remove();
            contenedorGI.append(mensajeHTML)
        }
    }
    let funcionEliminaError = () => {
        input.addClass('validado').removeClass('error');
        input.closest('[class^="col-"]').find('.mensajeError').remove();
    }

    if (requerido && valorIntroducido == '') {
        funcionMandarError('Este campo es obligatorio!!!');
        return;
    } else {
        funcionEliminaError();
    }

    //Para validar el minimo del campo
    if (minimo && valorIntroducido.length < minimo) {
        if (!requerido && valorIntroducido == '') {
            return;
        }
        funcionMandarError(`El valor del campo debe ser mayor o igual a ${minimo} caracteres`)
        return;
    } else {
        funcionEliminaError();
    }

    //Para validar el maximo del campo
    if (maximo && valorIntroducido.length > maximo) {
        if (!requerido && valorIntroducido == '') {
            return;
        }
        funcionMandarError(`El valor del campo debe ser menor o igual a ${maximo} caracteres`)
        return;
    } else {
        funcionEliminaError()
    }

    //Para validar la contrasena de confirmación
    if (input.attr('id') == 'contrasena2_usuario') {
        if (!requerido && valorIntroducido == '') {
            return;
        }
        if ($('#contrasena1_usuario').val() != $('#contrasena2_usuario').val()) {
            funcionMandarError('El valor de ambas contraseña debe coincidir');
            return;
        } else {
            funcionEliminaError();
        }
    }

    //Para validar el formato del campo
    if (!esValido) {
        if (!requerido && valorIntroducido == '') {
            return;
        }
        funcionMandarError('El valor del campo no es valido');
        return;
    } else {
        funcionEliminaError();
    }

    //Para validar campos que deben tener valores únicos
    if (input.hasClass('noRepetir')) {
        let proseguir = false;
        
        if (input.hasClass('formularioActualizar')) {
            if (
                inputsActualizarNoRepetir[nameImput] != valorIntroducido &&
                inputsActualizarNoRepetir[nameImput] != valorIntroducido.toUpperCase()
            ) {
                proseguir = true;
            }
        } else {
            proseguir = true;
        }
        if (proseguir != true) {
            return;
        }
        let instruccionesPe = {
            'modulo': modulo,
            'datosPe': {
                'accion': 'listar'
            },
        }

        let registrosExistentes = await pedirDatosAjax(instruccionesPe);
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
            funcionMandarError('El dato ingresado ya se encuentra registrado')
        } else {
            funcionEliminaError();
        }
    }
}
function validarTodosLosCampos() {

    let inputs = $(this).find('input')
    inputs.each((indice, input) => {
        validarEnTiempoReal(input);
    })

    let hayUnoInvalido = false;
    inputs.each((indice, input) => {
        if ($(input).hasClass('error')) {
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
        inputsActualizarNoRepetir[$(input).attr('name')] = $(input).val();
    });
}
//#endregion [ VALIDACIONES ] FIN

//#region [ LISTAR CON DATATABLE ] COMIENZO

//#region [Funcion para definir los botones] COMIENZO
let permisos = ''; let botonesAccion = null;
export async function btnLista(modulo) {

    botonesAccion = null;
    // if (permisos == '') {
    //     let instruccionesPe = {
    //         'modulo': 'permisos',
    //         'noGuardarLocal': true,
    //         'datosPe': {
    //             'accion': 'listarPorRol'
    //         },
    //     }
    //     permisos = await pedirDatosAjax(instruccionesPe);
    // }

    switch (vista) {
        case 'cedula_usuario':
        case 'cedula_cliente':
        case 'id_metodo_pago':
        case 'id_moneda':
        case 'id_presentacion':
        case 'id_rol':
            // if(
            //     permisos[modulo].includes('actualizar') ||
            //     permisos[modulo].includes('eliminar')
            // ){
            botonesAccion = (idRegistro) => {
                let boton = '';
                boton += `<ul class="list-inline me-auto mb-0">`;
                // if (permisos[modulo].includes('actualizar')) {
                boton += `
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
                            <a href="#" value="${idRegistro}"  class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
                            <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                // }
                // if (permisos[modulo].includes('eliminar')) {
                boton += `
                        <li value="${idRegistro}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                            <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
                            <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                // }
                boton += `</ul>`;

                return boton;
            };
            // }
            break;
        case 'id_promocion':
            if (
                permisos['promociones'].includes('ver detalles de promociones') ||
                permisos['promociones'].includes('actualizar') ||
                permisos['promociones'].includes('eliminar')
            ) {
                botonesAccion = (idRegistro) => {
                    let boton = '';
                    boton += `<ul class="list-inline me-auto mb-0">`;
                    if (permisos['promociones'].includes('ver detalles de promociones')) {
                        boton += `
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver detalles">
                            <a href="#" value="${idRegistro}" class="botonVer avtar avtar-xs btn-link-primary btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalDetalles">
                                <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                    }
                    if (permisos[modulo].includes('actualizar')) {
                        boton += `
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
                            <a href="#" value="${idRegistro}"  class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
                                <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                    }
                    if (permisos[modulo].includes('eliminar')) {
                        boton += `
                        <li value="${idRegistro}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                            <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
                                <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                    }
                    boton += `</ul>`;
                    return boton;
                }
            }
            break;
        case 'id_venta':
            if (
                permisos['ventas'].includes('ver') ||
                permisos['ventas'].includes('eliminar') ||
                permisos['ventas'].includes('actualizar')
            ) {
                botonesAccion = (idRegistro, selectorTabla = null) => {
                    let boton = '';

                    boton += `<ul class="list-inline me-auto mb-0">`;
                    if (permisos['ventas'].includes('actualizar') && selectorTabla == '.listaVentasSinPago') {
                        boton += `
                        <li id_venta="${idRegistro}" class="botonAggPago list-inline-item align-bottom" data-bs-toggle="tooltip" title="Agregar Pago">
                            <a href="#" class=" avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalAggPago">
                                <i class="fi fi-bs-expense fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                    }
                    if (permisos['reportes'].includes('imprimir comandas') && vista == 'id_venta') {
                        boton += `
                        <li class="botonImprimir list-inline-item align-bottom" value="${idRegistro}" data-bs-toggle="tooltip" title="Imprimir Comanda">
                            <a href="#"  class="avtar avtar-xs btn-link-primary btn-pc-default">
                                <i class="fi fi-rr-receipt fs-3 iconoCentrado"></i>
                            </a>
                        </li>
                    `;
                    }
                    if (permisos['ventas'].includes('ver detalles de las ventas')) {
                        boton += `
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver detalles">
                            <a href="#" value="${idRegistro}" class="botonVer avtar avtar-xs btn-link-primary btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalDetalles">
                                <i class="fi fi-rr-eye fs-3 iconoCentrado"></i>
                            </a>
                        </li>`;
                    }
                    if (permisos['ventas'].includes('eliminar')) {
                        boton += `
                        <li value="${idRegistro}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                            <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
                            <i class="fi fi-trash fs-3"></i>
                            </a>
                        </li>`;
                    }
                    boton += `</ul>`;

                    return boton;
                };
            }
            break;
    }
    return botonesAccion;
}
//#endregion [Funcion para definir los botones] FIN

//#region [Lista de los datos más generales] COMIENZO
export async function ListarDataTable(instrucciones) {

    let selector = instrucciones['selectorTabla'] ? instrucciones['selectorTabla'] : '.tabla-ajax';
    let CEP = instrucciones['credencialesEP'] ? instrucciones['credencialesEP'] : { 'accion': 'listar' };
    let encabezados = instrucciones['encabezados'];
    let modulo = instrucciones['modulo'];
    let botonesAccion = await btnLista(modulo);

    console.log(selector);
    // Destruye cualquier instancia existente de DataTables en la tabla para evitar conflictos
    if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
    }
    let instruccionesPe = {
        'modulo': modulo,
        'datosPe': CEP
    };
    let data = await pedirDatosAjax(instruccionesPe);
    let datos = data;
    let arregloColumnas = [];
    let dynamicColumnDefs = [];
    let targetsCount = 0;
    let textoEncabezados;

    // Intenta parsear los datos si vienen como un JSON de tipo string
    if (typeof datos === 'string') {
        try {
            datos = await JSON.parse(datos);
        } catch (e) {
            console.error("Error al parsear el JSON:", e);
            datos = []; // Si falla el parseo, tratamos como un arreglo vacío
        }
    }

    //para construir el objeto con los nombres de los campos que vienen en los datos del servidor
    let keysParaLasColumnas = [];
    if (datos.length >= 1) {
        keysParaLasColumnas = Object.keys(datos[0]);
    } else if (typeof encabezados !== 'undefined' && Object.keys(encabezados).length > 0) {
        if (vista == 'id_venta') {
            if (selector == '.listaVentasSinPago') {
                keysParaLasColumnas = ['id_venta', 'CLIENTE', 'FECHA', 'DEUDA'];
            } else {
                keysParaLasColumnas = Object.keys(encabezados['listaGeneral']);
            }
        } else {
            keysParaLasColumnas = Object.keys(encabezados);
        }
    } else {
        console.warn("No hay datos iniciales ni 'encabezados' predefinidos. La tabla podría no mostrar las columnas correctamente hasta que haya datos.");
    }

    //Recorremos el arreglo
    keysParaLasColumnas.forEach((key) => {
        //excluimos los campos que no deseemos mostrar al usuario
        if (!Camposfuera.includes(key)) {

            //Hacemos el arreglo con los títulos que llevaran las columnas
            textoEncabezados = encabezados[key] || (key.charAt(0).toUpperCase() + key.slice(1)).replace(/_/g, ' ');

            if (encabezados['listaGeneral']) {
                if (encabezados['listaGeneral'][key]) {
                    textoEncabezados = encabezados['listaGeneral'][key];
                }
            }

            switch (key) {
                case 'valor_moneda':// Lógica para campos de valor monetario
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados,
                        render: function (data, type, row) {
                            return `${data} bs`;
                        }
                    });
                    break;
                case 'precio_producto': // Lógica para campos de valor monetario $$
                case 'descuento_promocion':
                case 'precio_ruta':
                case 'precio_unitario_insumo':
                case 'DEUDA':
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados,
                        render: function (data, type, row) {
                            return `${data}$`;
                        }
                    });
                    break;
                case 'fecha_inicio_promocion': // Lógica para campos de fecha
                case 'fecha_fin_promocion':
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados,
                        render: function (data, type, row) {
                            let cadenaFormateada = cambiarFormatos(data, "fecha");
                            return `${cadenaFormateada}`;
                        }
                    });
                    break;
                case 'fecha_bitacora':
                case 'fecha_cambio_iva':
                case 'fecha_cambio':
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados,
                        render: function (data, type, row) {
                            let cadenaFormateada = cambiarFormatos(data, "fecha_hora");
                            return `${cadenaFormateada}`;
                        }
                    });
                    break;
                case 'necesita_moneda': // Lógica para campos de booleanos
                case 'necesita_banco_emisor':
                case 'necesita_banco_receptor':
                case 'necesita_referencia':
                case 'necesita_contornos':
                case 'necesita_rellenos':
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados,
                        render: function (data, type, row) {
                            if (data == 1) { return 'SI'; } else { return 'NO'; }
                        }
                    });
                    break;
                case 'estado':
                    if (vista == 'id_venta') {
                        arregloColumnas.push({
                            data: key,
                            title: '¿DESPACHADA?',
                            render: function (data, type, row) {

                                let checked = '';
                                if (data == 2) {
                                    checked = 'checked';
                                }
                                return `
                                        <div class="d-flex justify-content-center form-check form-switch custom-switch-v1 mb-0">
                                            <input ${checked} id_venta="${row[vista]}" type="checkbox" class="marcar_venta_despachada form-check-input input-primary">
                                        </div>
                                    `;
                            }
                        });
                    }
                    break;
                default:// Lógica para el resto de campos
                    arregloColumnas.push({
                        data: key,
                        title: textoEncabezados
                    });
                    break;
            }
            // Añade la definición de clase
            dynamicColumnDefs.push({ targets: [targetsCount], className: 'dt-body-center dt-head-center' });
            targetsCount++;
        }
    });
    if (arregloColumnas.length === 0) {
        arregloColumnas.push({ data: null, title: 'No hay datos disponibles' });
        dynamicColumnDefs.push({ targets: [0], className: 'tabla' });
        targetsCount = 1; // Aseguramos que targetsCount esté correcto para la siguiente columna (acciones)
    }
    if (botonesAccion != null) {
        arregloColumnas.push({
            data: null, // Esta columna no mapea directamente a un campo de datos
            title: 'ACCIONES',
            render: function (data, type, row) {
                const idRegistro = row[vista]; // Usamos la clave primaria para obtener el ID
                return botonesAccion(idRegistro, selector); // Genera los botones con el ID del registro
            }
        });
        dynamicColumnDefs.push({ orderable: false, className: 'acciones dt-body-center dt-head-center', targets: [targetsCount] });
    }

    // Inicializa DataTables con la configuración construida
    let dataTableInstance = await $(selector).DataTable({
        ajax: {
            url: rutaAbsoluta + modulo,
            method: 'POST',
            data: CEP,
            dataSrc: '' // Indica que los datos están directamente en la raíz de la respuesta JSON (un array)
        },
        order: [[0, "desc"]],
        columns: arregloColumnas, // Columnas ya definidas
        autoWidth: false, // Deshabilita el auto-ajuste de ancho de columna
        columnDefs: dynamicColumnDefs, // Definiciones de columna adicionales (clases, ordenamiento)
        language: españolDataTable,
        initComplete: function (settings, json) {
            instanciasDatatable.push(dataTableInstance);
        }
    });
};
//#endregion [Lista de los datos más generales] FIN

//#region [Función para cambiar el formato de los campos] COMIENZO
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
    }
    if (tipo == "fecha") {
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
//#endregion [Función para cambiar el formato de los campos] FIN

//#endregion [ LISTAR CON DATATABLE ] FIN

//#region [ENVIAR FORMULARIOS CON AJAX] COMIENZO
export async function enviarFormulario() {

    esteFormulario = $(this);
    let resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Quieres realizar la acción solicitada",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    })

    if (resultado.isConfirmed) {

        let hayUnCampoInvalido = validarTodosLosCampos.call(this);
        if (hayUnCampoInvalido) {
            return;
        }

        let metodo = $(this).attr("method");
        let action = $(this).attr("action");
        let data = new FormData(this);
        let encabezados = new Headers();

        let config = {
            method: metodo,
            headers: encabezados,
            mode: 'cors',
            cache: 'no-cache',
            body: data
        };

        let respuesta = await fetch(action, config)
        let contentType = respuesta.headers.get("Content-Type");

        // Si es una respuesta JSON
        if (contentType.includes("application/json") || contentType.includes("text/html")) {
            const respuestaJSON = await respuesta.json();

            //Para actualizar los listados
            if (instanciasDatatable) {
                if (instanciasDatatable.length > 0) {
                    instanciasDatatable.forEach(instancia => {
                        instancia.ajax.reload(null, false);
                    });
                }
            }

            if (respuestaJSON['icono'] == 'success') {
                reiniciarSS(modulo)
            }
            return alertas_ajax(respuestaJSON);

            // Si es un PDF
        } else if (contentType.includes("application/pdf")) {
            const pdfBlob = await respuesta.blob();
            const urlPDF = URL.createObjectURL(pdfBlob);
            window.open(urlPDF, '_blank')
        } else {
            console.warn("Tipo de respuesta no esperado:", contentType);
        }
    }
}
//#endregion [ENVIAR FORMULARIOS CON AJAX] FIN

//#region [ALERTAS AJAX] COMIENZO
export async function alertas_ajax(alerta) {
    let resultado = '';
    switch (alerta.tipo) {
        case "simple":
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            });
            break;
        case "recargar":
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            })
            if (resultado.isConfirmed) {
                location.reload();
            }
            break;
        case "limpiar":
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            })
            if (esteFormulario) {
                esteFormulario[0].reset();
                $(esteFormulario).find('select, input').removeClass('validado');
            }
            break;
        case "limpiarYcerrar":
            await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            })
            if (esteFormulario) {
                esteFormulario[0].reset();
                let botonCerrar = $(esteFormulario).closest('.modal.fade').find('.btn-close');
                botonCerrar.trigger('click');
            }
            break;
        case "reiniciarForm":
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            })
            if (resultado.isConfirmed || resultado.isDismissed) {

                esteFormulario = $(esteFormulario);
                let botonCerrar = esteFormulario.closest('.modalActualizar').find('.btn-close');
                if (botonCerrar.length == 0) { botonCerrar = $($('.modalActualizar2').find('.btn-close')) }
                let botonCerrar2 = esteFormulario.find('.btn-close').last();

                if (alerta.formulario == 'actualizar') {
                    botonCerrar.trigger('click');
                } else if (alerta.formulario == 'registrar') {

                    if (vista == 'id_venta') { botonCerrar2.trigger('click'); }
                    esteFormulario.empty();
                    esteFormulario.append(HTMLFormReg);

                    //Para cargar las opciones de nuevo
                    if (vista == 'id_promocion') {
                        await precargaPromociones()
                    } else if (vista == 'id_venta') {
                        await precargaVentas()
                    }
                }
            }
            break;
        case "redireccionar":
            window.location.href = alerta['url']
            break;
        case "alertarYredireccionar":
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => {
                window.location.href = alerta['url']
            }, 2000);
            break;
        case "actualizarFoto":
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            })
            if (resultado.isConfirmed || resultado.isDismissed) {
                actualizarImagenPorIdRegistro(
                    alerta['nombreClase'],
                    alerta['idRegistroBuscado'],
                    alerta['nuevaRutaImagen']
                );
                if (alerta['reiniciarPreview']) {
                    $('.etiquetaImagenPrev ').attr('src', rutaAbsoluta + "app/assets/fotos/default.png")
                }
            }
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
            })
            return resultado;
            break;
        default:
            break;
    }
}
//#endregion [ALERTAS AJAX] FIN

//#region [PARA ELIMINAR REGISTROS] COMIENZO
export async function eliminarRegistro() {
    let botonEliminar = this;

    let resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Estás seguro de eliminar el registro?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    })
    if (resultado.isConfirmed) {

        let idRegistro = botonEliminar.value;
        let nombreCampo = vista;

        let instruccionesPe = {
            'noGuardarLocal': true,
            'modulo': 'SPI',
            'nombreId': nombreCampo,
            'datosPe': {
                'accion': 'eliminar',
                [nombreCampo]: [idRegistro]
            }
        }
        let respuesta = await pedirDatosAjax(instruccionesPe);

        //Para actualizar los listados
        if (instanciasDatatable.length > 0) {
            instanciasDatatable.forEach(instancia => {
                instancia.ajax.reload(null, false);
            });
        }

        if (respuesta['icono'] == 'success') {
            reiniciarSS(modulo)
        }

        return alertas_ajax(respuesta);
    }
}
//#endregion [PARA ELIMINAR REGISTROS] FIN

//#region [PARA OBTENER DATOS A ACTUALIZAR] COMIENZO
export async function obtenerDatosRegistro() {

    //Para obtener el accion especifico del formulario a que se imprime mediante el click del usuario
    let claseObjetivo = $(this).attr('data-bs-target')//
    let formulario = $(claseObjetivo).find('.formularioAjax');

    if (formulario.length == 0) {
        formulario = $('.formularioAjax.actualizar')
    }

    //Para poder imprimir los datos del usuario en el modal de editar mi perfil 
    let nombreCampo = $(this).hasClass('valueHeader') ? 'cedula_usuario' : vista;
    let idRegistro = $(this).attr('value');

    let instruccionesPe = {
        'modulo': 'SPI',//
        'nombreId': nombreCampo,
        'datosPe': {
            'accion': 'seleccionarUno',
            [nombreCampo]: idRegistro
        }
    }
    let respuesta = await pedirDatosAjax(instruccionesPe);
    let datosNoAgrupados = respuesta['datosNoAgrupados'] ? respuesta['datosNoAgrupados'] : respuesta;

    formulario.find('input, select').removeClass('validado error').closest('[class^="col-"]').find('.mensajeError').remove()
    let inputs = formulario.find(".formularioActualizar");
    inputs.each((indice, input) => {
        const nombreCampo = input.name; // Obtener el atributo "name"
        if (datosNoAgrupados.hasOwnProperty(nombreCampo)) {
            input.value = datosNoAgrupados[nombreCampo]; // Le Asignamos el valor al input
        }
    });

    //Deshabilitamos los datos que se deban
    let datosInhab = inputs.filter('.inha')
    datosInhab.each((indice, input) => {
        $(input).prop('disabled', true)
    })
}
//#endregion [PARA OBTENER DATOS A ACTUALIZAR] FIN

//#region [PARA HACER PETICIONES AJAX] COMIENZO}
export async function pedirDatosAjax(instrucciones) {

    let modulosFuera = ['usuarios', 'ventas', 'bitacora', 'notificaciones', 'roles','metodos-pago', 'monedas','cambios-monedas','presentaciones','permisos'];
    let moduloPe = instrucciones['modulo'];
    let accionPe = instrucciones['datosPe']['accion'];
    let listaDeIds = {
        'ventas': 'id_venta',
        'clientes': 'cedula_cliente',
        'cambios': 'id_cambio',
        'productos': 'id_producto',
        'categorias': 'id_categoria',
        'promociones': 'id_promocion',
        'insumos': 'id_insumo',
        'metodos-pago': 'id_metodo_pago',
        'bancos': 'id_banco',
        'monedas': 'id_moneda',
        'usuarios': 'cedula_usuario',
        'roles': 'id_rol',
        'permisos': 'id_rol',
        'contornos': 'id_contorno',
        'rellenos': 'id_relleno',
        'repartidores': 'cedula_repartidor',
        'rutas': 'id_ruta',
        'bitacora': 'id_bitacora',
    }
    if (moduloPe == 'SPI') {
        for (const clave in listaDeIds) {
            if (Object.hasOwnProperty.call(listaDeIds, clave)) {
                if (listaDeIds[clave] == instrucciones['nombreId']) {
                    moduloPe = clave;
                    break;
                }
            }
        }
    }
    let datosModulo = sessionStorage.getItem(moduloPe) || false;
    let buscarDatos = true;
    let datosLocales = false;
    let idEnviado = instrucciones['datosPe'][listaDeIds[moduloPe]] || false;
    let idCategoriaEnviado = instrucciones['datosPe']['id_categoria'] || false;
    let registrarElEspacio = false;

    if (datosModulo) {
        datosModulo = await JSON.parse(datosModulo);
        if (datosModulo[accionPe]) {
            if (accionPe == 'seleccionarUno' || accionPe == 'listarDetalles') {
                if (datosModulo[accionPe][idEnviado]) {
                    datosLocales = datosModulo[accionPe][idEnviado];
                    buscarDatos = false;
                } else {
                    registrarElEspacio = true;
                }
            }
            else if (accionPe == 'listarPorCategoria') {
                if (datosModulo[accionPe][idCategoriaEnviado]) {
                    datosLocales = datosModulo[accionPe][idCategoriaEnviado];
                    buscarDatos = false;
                } else {
                    registrarElEspacio = true;
                }
            } else {
                datosLocales = datosModulo[accionPe];
                buscarDatos = false;
            }
        } else {
            registrarElEspacio = true;
        }
    } else {
        registrarElEspacio = true
    }
    if (registrarElEspacio) {
        if (!datosModulo) {
            datosModulo = {}
        }
        if (!datosModulo[accionPe]) {
            datosModulo[accionPe] = {}
        }
        if ((accionPe == 'seleccionarUno' || accionPe == 'listarDetalles') && !datosModulo[accionPe][idEnviado]) {
            datosModulo[accionPe][idEnviado] = {};
        } else if (accionPe == 'listarPorCategoria' && !datosModulo[accionPe][idCategoriaEnviado]) {
            datosModulo[accionPe][idCategoriaEnviado] = {};
        }
    }

    let respuesta = '';
    if (buscarDatos) {

        let headers = new Headers(); let formData = new FormData();
        for (let [clave, valor] of Object.entries(instrucciones['datosPe'])) {
            formData.append(clave, valor);
        }
        respuesta = await fetch(`${rutaAbsoluta}` + moduloPe, {
            method: 'POST',
            headers: headers,
            mode: 'cors',
            body: formData,
        })

        if (!instrucciones['noJSON']) {
            respuesta = await respuesta.json();


            if (
                !instrucciones['noGuardarLocal'] &&
                !(moduloPe == 'permisos' && accionPe == 'listar') &&
                !modulosFuera.includes(moduloPe)
            ) {
                if (typeof datosModulo == 'string') {
                    datosModulo = await JSON.parse(datosModulo)
                }

                if (accionPe == 'seleccionarUno' || accionPe == 'listarDetalles') {
                    datosModulo[accionPe][idEnviado] = respuesta;
                } else if (accionPe == 'listarPorCategoria') {
                    datosModulo[accionPe][idCategoriaEnviado] = respuesta;
                } else {
                    datosModulo[accionPe] = respuesta;
                }

                let datosStringGuardar = JSON.stringify(datosModulo);

                try {
                    sessionStorage.setItem(moduloPe, datosStringGuardar);
                    // let datoGu = sessionStorage.getItem(moduloPe)
                    // datoGu = await JSON.parse(datoGu);
                } catch (error) {
                    console.error('Ocurrió un error al guardar los datos de forma local. El error es: ', error)
                }
            }
        }
    } else {
        respuesta = datosLocales;
    }

    return respuesta;
}
export function reiniciarSS(modulo) {
    if (sessionStorage.getItem(modulo)) {
        sessionStorage.removeItem(modulo);
    }
}
//#endregion [PARA HACER PETICIONES AJAX] FIN

//#region [PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML] COMIENZO
export async function extraerDatosAjax(instrucciones) {

    variableDeError = '';
    let modulos = instrucciones['modulosPeticion'];
    let acciones = instrucciones['accionesPeticion']
    let tipoElemento = instrucciones['tipoElemento']
    let elementosDestino = instrucciones['elementosDestino']
    let datosInsertar = instrucciones['datosInsertar']

    let c = 0;
    let modulo = '';
    let accion = '';
    let datosRecibidos = '';
    let numeroPet = 1;

    for (const [index, moduloInd] of modulos.entries()) {

        if (moduloInd != modulo || accion != acciones[c]) {
            modulo = moduloInd
            accion = acciones[c]

            let instruccionesPe = {
                'modulo': modulo,
                'datosPe': {}
            }
            for (const [clave, valor] of Object.entries(accion)) {
                instruccionesPe['datosPe'][clave] = valor;
            }
            datosRecibidos = await pedirDatosAjax(instruccionesPe)
        }
        if (datosRecibidos['tipo']) {
            if (tipoElemento[c] == 'select') {
                elementosDestino[c].empty();
                elementosDestino[c].append(`<option value="">Sin Registros</option>`);
                continue;
            } else {
                variableDeError = { 'error': 'sin registros' };
                continue;
            }
        } else {
            variableDeError = { 'exito': 'Con registros' };
        }
        if (tipoElemento[c] == 'select') {

            //#region LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEM
            let elementoObtClases = elementosDestino[c];
            if (Array.isArray(elementosDestino[c])) {
                elementoObtClases = elementosDestino[c][1]
            }

            let clasesDelSelect = elementoObtClases.attr('class')

            if (!clasesDelSelect) {
                return;
            }
            let arregloDeClases = clasesDelSelect.split(' ');
            let clasesForma = clasesDelSelect.replace(/\s/g, '.');

            //para obtener todos los id's seleccionados hasta el momento
            let registrosSeleccionados = [];
            if (arregloDeClases.includes('OQNPR')) {

                let selectsTotales;
                if (Array.isArray(elementosDestino[c])) {
                    selectsTotales = elementosDestino[c][0].closest('.contenedorDetalles').find('.' + clasesForma);
                } else {
                    selectsTotales = elementosDestino[c].closest('.contenedorDetalles').find('.' + clasesForma);
                }

                const selects = selectsTotales.map(function () {
                    if ($(this).val() != '') {
                        return $(this).val();
                    }
                }).get(); //el .get() transforma el arrays de jquery a DOM
                registrosSeleccionados = selects;
            }
            //#endregion LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEN

            if (Array.isArray(elementosDestino[c])) {

                elementosDestino[c].forEach(elemento => {
                    elemento.empty();
                    if (datosInsertar[c]['textoDefault']) {
                        elemento.append(`<option value="">${datosInsertar[c]['textoDefault']}</option>`);
                    }
                })
            } else {
                elementosDestino[c].empty();
                if (datosInsertar[c]['textoDefault']) {
                    elementosDestino[c].append(`<option value="">${datosInsertar[c]['textoDefault']}</option>`);
                }
            }

            datosRecibidos.forEach(registro => {
                const idRegistroActual = String(registro[datosInsertar[c]['value']]);
                if (!registrosSeleccionados.includes(idRegistroActual)) {
                    if (Array.isArray(elementosDestino[c])) {
                        elementosDestino[c].forEach(select => {
                            if (registro[datosInsertar[c]['value']] == datosInsertar[c]['opcionSeleccionada']) {
                                select.append(
                                    $('<option>', {
                                        value: registro[datosInsertar[c]['value']],   // Asignamos el value
                                        text: registro[datosInsertar[c]['texto']],
                                        selected: true
                                    })
                                );
                            } else {
                                select.append(
                                    $('<option>', {
                                        value: registro[datosInsertar[c]['value']],   // Asignamos el value
                                        text: registro[datosInsertar[c]['texto']] // Y el texto que tendrá dentro
                                    })
                                );
                            }
                        })
                    } else {
                        if (registro[datosInsertar[c]['value']] == datosInsertar[c]['opcionSeleccionada']) {
                            elementosDestino[c].append(
                                $('<option>', {
                                    value: registro[datosInsertar[c]['value']],
                                    text: registro[datosInsertar[c]['texto']],
                                    selected: true //para que esté previamente seleccionada
                                })
                            );
                        } else {
                            elementosDestino[c].append(
                                $('<option>', {
                                    value: registro[datosInsertar[c]['value']],   // Asignamos el value
                                    text: registro[datosInsertar[c]['texto']] // Y el texto que tendrá dentro
                                })
                            );
                        }
                    }
                }
            });

            //Para validar si quedan o no opciones para mostrar
            let totalOptions = '';
            if (Array.isArray(elementosDestino[c])) {
                totalOptions = elementosDestino[c][0].find('option').length;
            } else {
                totalOptions = elementosDestino[c].find('option').length;
            }

            if (totalOptions == 1) {
                elementosDestino[c].empty();
                elementosDestino[c].append('<option class="texto-rojo">Sin más opciones</option>');
            }
        } else if (tipoElemento[c] == 'input') {
            if (Array.isArray(elementosDestino[c])) {
                let c2 = 0;
                elementosDestino[c].forEach(elemento => {
                    elemento.val(datosRecibidos[datosInsertar[c][c2]])
                    c2++;
                    elemento.removeClass('error');
                    elemento.closest('.form-group').find('.error-message').remove()
                })
            } else {
                elementosDestino[c].val(datosRecibidos[datosInsertar[c]])
                elementosDestino[c].removeClass('error');
                elementosDestino[c].closest('.form-group').find('.error-message').remove()
            }
        }
        c++;
    };
}
//#endregion [PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO

//Evento para la precarga datos y eventos
$(document).on('DOMContentLoaded', async function (e) {
    const sidebar = document.querySelector('#sidebar');
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebar && sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('sidebar-active');
        });
    }
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this);
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN