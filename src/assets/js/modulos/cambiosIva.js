import {
    ListarDataTable, enviarFormulario, encabezados
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'cambiosIva'
    }
    await ListarDataTable(instruccionesLista);
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario.call(this);
});
