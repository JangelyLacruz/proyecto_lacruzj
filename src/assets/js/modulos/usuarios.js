import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro, encabezados,
    ListarDataTable, extraerDatosAjax, cargarInputsActualizarQNR
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'usuarios'
    }
    await ListarDataTable(instruccionesLista);

    let instrucciones = {
        'modulosPeticion': ['roles'],
        'accionesPeticion': [{ 'accion': 'listar' }],
        'tipoElemento': ['select'],
        'elementosDestino': [$('.selectRoles')],
        'datosInsertar': [
            {
                'value': 'id_rol',
                'texto': 'nombre_rol',
                'textoDefault': 'Seleccione un rol'
            }
        ]
    }
    extraerDatosAjax(instrucciones)
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario.call(this);
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro.call(this);
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro.call(this);
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});
