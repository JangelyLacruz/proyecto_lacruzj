import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro, encabezados,
    ListarDataTable, cargarInputsActualizarQNR
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'roles'
    }
    await ListarDataTable(instruccionesLista);
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
