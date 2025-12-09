import {
    ListarDataTable, enviarFormulario, extraerDatosAjax, encabezados 
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'monedas',
        'credencialesEP':{
            'accion':'listarCambios'
        }
    }
    await ListarDataTable(instruccionesLista);

    let instrucciones = {
        'modulosPeticion': ['monedas'],
        'accionesPeticion': [{ 'accion': 'listar' }],
        'tipoElemento': ['select'],
        'elementosDestino': [$('.selectMonedas')],
        'datosInsertar': [
            {
                'value': 'id_moneda',
                'texto': 'nombre_moneda',
                'textoDefault': 'Seleccione una moneda'
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
