//#region IMPORTACIONES Y VARIABLES GLOBALES
// El chatbot usa fetch() nativo (no pedir_datos_ajax()) porque su flujo
// es distinto al de los módulos CRUD: no usa DataTables ni SessionStorage.
// La variable cajaMensajes se inicializa dentro del document.ready() para
// garantizar que el DOM esté listo antes de intentar seleccionarla.
//#endregion

//#region FUNCIONES PROPIAS DEL MÓDULO

// Escapa caracteres HTML peligrosos para prevenir ataques XSS.
// CRÍTICO: Todo texto del usuario debe pasar por aquí antes de ir al DOM.
function escaparHtml(texto) {
    const mapa = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
        '/': '&#x2F;'
    };
    return String(texto).replace(/[&<>"'/]/g, (c) => mapa[c]);
}

// Sanitiza la respuesta del bot permitiendo solo etiquetas HTML seguras.
// Esto permite que Gemini use <b>, <br>, <ul>, <li> pero bloquea <script>.
function sanitizarRespuestaBot(texto) {
    if (!texto) return '';

    let lineas = texto.split('\n');
    let lineasProcesadas = [];
    let enLista = false;

    for (let i = 0; i < lineas.length; i++) {
        let linea = lineas[i].trim();

        // Convertir negritas markdown **texto**
        linea = linea.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Detectar si es un elemento de lista que empieza con * o -
        let matchLista = linea.match(/^[*-]\s+(.+)$/);
        if (matchLista) {
            if (!enLista) {
                lineasProcesadas.push('<ul class="ps-3 my-2" style="list-style-type: disc;">');
                enLista = true;
            }
            lineasProcesadas.push('<li class="mb-1">' + matchLista[1] + '</li>');
        } else {
            if (enLista) {
                lineasProcesadas.push('</ul>');
                enLista = false;
            }
            if (linea === '') {
                lineasProcesadas.push('<br>');
            } else {
                lineasProcesadas.push('<div>' + linea + '</div>');
            }
        }
    }
    if (enLista) {
        lineasProcesadas.push('</ul>');
    }

    let procesado = lineasProcesadas.join('');

    // Escapar etiquetas peligrosas pero preservar las permitidas
    let seguro = escaparHtml(procesado);
    seguro = seguro
        .replace(/&lt;b&gt;/g, '<b>').replace(/&lt;&#x2F;b&gt;/g, '</b>')
        .replace(/&lt;br&gt;/g, '<br>')
        .replace(/&lt;ul class=&quot;ps-3 my-2&quot; style=&quot;list-style-type: disc;&quot;&gt;/g, '<ul class="ps-3 my-2" style="list-style-type: disc;">')
        .replace(/&lt;ul&gt;/g, '<ul class="ps-3 my-2" style="list-style-type: disc;">').replace(/&lt;&#x2F;ul&gt;/g, '</ul>')
        .replace(/&lt;li class=&quot;mb-1&quot;&gt;/g, '<li class="mb-1">')
        .replace(/&lt;li&gt;/g, '<li class="mb-1">').replace(/&lt;&#x2F;li&gt;/g, '</li>')
        .replace(/&lt;div&gt;/g, '<div class="mb-1">').replace(/&lt;&#x2F;div&gt;/g, '</div>')
        .replace(/&lt;strong&gt;/g, '<strong>').replace(/&lt;&#x2F;strong&gt;/g, '</strong>');

    return seguro;
}

function obtenerHora() {
    return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function agregarBurbujaUsuario(cajaMensajes, texto) {
    // escaparHtml() protege contra XSS: el usuario no puede inyectar HTML
    // justify-content-end alinea la burbuja a la derecha
    let html = `
        <div class="d-flex mb-3 justify-content-end">
            <div class="user-msg-bubble bg-primary text-white p-3 shadow-sm">
                ${escaparHtml(texto)}
                <div class="text-end mt-1" style="font-size: 0.65rem; color: rgba(255,255,255,0.7);">${obtenerHora()}</div>
            </div>
        </div>
    `;
    cajaMensajes.append(html);
    cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);
}

function agregarBurbujaBot(cajaMensajes, texto, isError = false, promptOriginal = '') {
    let clases    = isError ? 'bg-danger text-white border' : 'bg-white border rounded text-dark';
    let textColor = isError ? 'rgba(255,255,255,0.7)' : 'var(--bs-gray-600)';

    let ofrecerGuardado = false;
    if (!isError && texto.includes('[OFRECER_GUARDADO]')) {
        texto = texto.replace('[OFRECER_GUARDADO]', '').trim();
        ofrecerGuardado = true;
    }

    let textoSanitizado = isError ? escaparHtml(texto) : sanitizarRespuestaBot(texto);

    let htmlBotonGuardar = '';
    if (ofrecerGuardado && promptOriginal) {
        let promptCodificado = encodeURIComponent(promptOriginal);
        let respuestaCodificada = encodeURIComponent(texto); // Guardar respuesta limpia sin formatear
        
        htmlBotonGuardar = `
            <div class="mt-1 mb-2 text-start">
                <button type="button" class="btn btn-sm btn-outline-primary btn-guardar-presupuesto shadow-sm"
                        data-prompt="${promptCodificado}"
                        data-respuesta="${respuestaCodificada}"
                        style="font-size: 0.75rem; border-radius: 20px; padding: 4px 12px;">
                    <i class="fi fi-rr-disk"></i> Presione aquí para guardar este presupuesto
                </button>
            </div>
        `;
    }

    let html = `
        <div class="d-flex mb-3 flex-column align-items-start">
            <div class="bot-msg-bubble ${clases} p-3 shadow-sm" style="max-width: 85%;">
                ${textoSanitizado}
                <div class="text-end mt-1" style="font-size: 0.65rem; color: ${textColor};">${obtenerHora()}</div>
            </div>
            ${htmlBotonGuardar}
        </div>
    `;
    cajaMensajes.append(html);
    cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);
}

function mostrarPensando(cajaMensajes) {
    let id  = 'typing-' + Date.now();
    let html = `
        <div class="d-flex mb-3" id="${id}">
            <div class="bot-msg-bubble bg-white border p-3 shadow-sm text-muted">
                <i class="fi fi-rr-menu-dots"></i> Pensando...
            </div>
        </div>
    `;
    cajaMensajes.append(html);
    cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);
    return id;
}

function quitarPensando(id) {
    $('#' + id).remove();
}
//#endregion

//#region DELEGACIÓN DE EVENTOS
$(document).ready(function() {

    // Se declara aquí para garantizar que el DOM ya cargó
    const cajaMensajes = $('#chatbot-messages');

    // Mostrar ventana del chat
    $(document).off('click', '#chatbot-toggle-btn').on('click', '#chatbot-toggle-btn', function() {
        $('#chatbot-window').removeClass('d-none');
        $(this).addClass('d-none');
    });

    // Cerrar ventana del chat
    $(document).off('click', '#chatbot-close-btn').on('click', '#chatbot-close-btn', function() {
        $('#chatbot-window').addClass('d-none');
        $('#chatbot-toggle-btn').removeClass('d-none');
    });

    // Botones de sugerencias rápidas
    $(document).off('click', '#chatbot-suggestions button').on('click', '#chatbot-suggestions button', function() {
        let texto = $(this).text();
        $('#chatbot-input').val(texto);
        $('#chatbot-form').submit();
    });

    // Envío del formulario (Fetch + Async/Await)
    $(document).off('submit', '#chatbot-form').on('submit', '#chatbot-form', async function(e) {
        e.preventDefault();

        let inputElement  = $('#chatbot-input');
        let botonEnviar   = $(this).find('button[type="submit"]');
        let textoMensaje  = inputElement.val().trim();

        if (textoMensaje === '') return;

        // Mostrar mensaje del usuario y bloquear input mientras espera
        agregarBurbujaUsuario(cajaMensajes, textoMensaje);
        inputElement.val('').prop('disabled', true);
        botonEnviar.prop('disabled', true);

        let idTyping = mostrarPensando(cajaMensajes);

        try {
            let formData = new FormData();
            formData.append('accion', 'enviarMensaje');
            formData.append('mensaje', textoMensaje);

            // Obtener el token CSRF del meta tag
            const tokenCsrf = $('meta[name="TOKEN_CSRF"]').attr('content') || '';

            const respuestaHttp = await fetch('?views=chatbot', {
                method: 'POST',
                headers: {
                    'X-TOKEN-CSRF': tokenCsrf
                },
                body: formData
            });

            if (!respuestaHttp.ok) {
                throw new Error('Error HTTP ' + respuestaHttp.status);
            }

            const respuestaJson = await respuestaHttp.json();
            quitarPensando(idTyping);

            if (respuestaJson && respuestaJson.status === 'error') {
                agregarBurbujaBot(cajaMensajes, respuestaJson.mensaje, true);
            } else if (respuestaJson && respuestaJson.respuesta) {
                agregarBurbujaBot(cajaMensajes, respuestaJson.respuesta, false, textoMensaje);
            } else {
                agregarBurbujaBot(cajaMensajes, 'Error: Formato de respuesta desconocido.', true);
            }

        } catch (error) {
            quitarPensando(idTyping);
            // Sin console.error para no exponer trazas técnicas en el navegador
            agregarBurbujaBot(cajaMensajes, 'Disculpa, ocurrió un error de conexión. Intenta enviar tu mensaje de nuevo.', true);
        } finally {
            // Reactivar input independientemente del resultado
            inputElement.prop('disabled', false).focus();
            botonEnviar.prop('disabled', false);
        }
    });

    // Event listener para el botón "Guardar presupuesto"
    $(document).off('click', '.btn-guardar-presupuesto').on('click', '.btn-guardar-presupuesto', async function() {
        const boton = $(this);
        const promptOriginal = decodeURIComponent(boton.data('prompt'));
        const respuestaIA = decodeURIComponent(boton.data('respuesta'));

        boton.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1" role="status"></i> Guardando...');

        try {
            let formData = new FormData();
            formData.append('accion', 'guardarPresupuesto');
            formData.append('prompt', promptOriginal);
            formData.append('respuesta', respuestaIA);

            // Obtener el token CSRF del meta tag
            const tokenCsrf = $('meta[name="TOKEN_CSRF"]').attr('content') || '';

            const respuestaHttp = await fetch('?views=chatbot', {
                method: 'POST',
                headers: {
                    'X-TOKEN-CSRF': tokenCsrf
                },
                body: formData
            });

            if (!respuestaHttp.ok) {
                throw new Error('Error de conexión en el servidor.');
            }

            const respuestaJson = await respuestaHttp.json();

            if (respuestaJson && respuestaJson.status === 'success') {
                boton.removeClass('btn-outline-primary')
                     .addClass('btn-success text-white')
                     .prop('disabled', true)
                     .html('<i class="fi fi-rr-check"></i> ¡Presupuesto guardado!');
                
                // Mostrar alerta dulce (SweetAlert) del sistema
                if (typeof k === 'function') {
                    k({
                        tipo: 'simple',
                        icono: 'success',
                        titulo: '¡Guardado!',
                        texto: respuestaJson.mensaje
                    });
                }
            } else {
                throw new Error(respuestaJson.mensaje || 'Error al intentar guardar.');
            }

        } catch (error) {
            boton.prop('disabled', false)
                 .removeClass('btn-success')
                 .addClass('btn-outline-danger')
                 .html('<i class="fi fi-rr-exclamation"></i> Reintentar guardado');
            
            if (typeof k === 'function') {
                k({
                    tipo: 'simple',
                    icono: 'error',
                    titulo: 'Error',
                    texto: error.message
                });
            }
        }
    });
});
//#endregion