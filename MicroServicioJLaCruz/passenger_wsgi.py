import sys
import os
import json
import logging
from pathlib import Path
from dotenv import load_dotenv

# Obtener ruta absoluta del directorio del microservicio
BASE_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(BASE_DIR))

# Cargar variables de entorno desde su ruta absoluta
load_dotenv(dotenv_path=BASE_DIR / ".env")

TOKEN_SECRETO = os.getenv("TOKEN_MICROSERVICIO")
ORIGEN_PHP = os.getenv("URL_ORIGEN_PHP", "https://jlacruzca.com")

try:
    from app.services.gemini_chatbot import generar_respuesta_bot
    from app.models.chat_schemas import MensajeHistorial
    startup_error = None
except Exception as e:
    import traceback
    startup_error = traceback.format_exc()

def application(environ, start_response):
    path = environ.get('PATH_INFO', '')
    method = environ.get('REQUEST_METHOD', 'GET').upper()

    # Si hubo error al importar librerías o configurar, devolverlo como JSON para diagnóstico
    if startup_error:
        start_response('500 Internal Server Error', [('Content-Type', 'application/json; charset=utf-8')])
        return [json.dumps({"status": "error", "mensaje": "Error en el arranque de Python", "detalle": startup_error}).encode('utf-8')]

    # Cabeceras estándar de respuesta con soporte CORS
    headers = [
        ('Content-Type', 'application/json; charset=utf-8'),
        ('Access-Control-Allow-Origin', ORIGEN_PHP),
        ('Access-Control-Allow-Methods', 'POST, GET, OPTIONS'),
        ('Access-Control-Allow-Headers', 'Content-Type, X-Internal-Token'),
    ]

    # 0. Soporte preflight CORS
    if method == 'OPTIONS':
        start_response('204 No Content', headers)
        return [b'']

    # 1. Endpoint de Señal de Vida (Ping / Health Check)
    # Acepta /, /chatbot-api, /chatbot-api/
    clean_path = path.rstrip('/')
    if clean_path in ('', '/chatbot-api', '/api'):
        start_response('200 OK', headers)
        return [json.dumps({"mensaje": "Microservicio de ChatBot J. LACRUZ C.A. está en línea"}).encode('utf-8')]

    # 2. Endpoint de Chat (/chat o /api/chat)
    if clean_path.endswith('/chat') or clean_path.endswith('/api/chat'):
        if method != 'POST':
            start_response('405 Method Not Allowed', headers)
            return [json.dumps({"status": "error", "mensaje": "Método no permitido"}).encode('utf-8')]

        # Validar Token Secreto Interno
        token_recibido = environ.get('HTTP_X_INTERNAL_TOKEN', '')
        if not TOKEN_SECRETO or token_recibido != TOKEN_SECRETO:
            start_response('403 Forbidden', headers)
            return [json.dumps({"status": "error", "mensaje": "No autorizado"}).encode('utf-8')]

        # Leer cuerpo de forma segura sin bloquear Passenger
        try:
            content_length = int(environ.get('CONTENT_LENGTH', 0))
        except (ValueError, TypeError):
            content_length = 0

        if content_length <= 0:
            start_response('400 Bad Request', headers)
            return [json.dumps({"status": "error", "mensaje": "Cuerpo de petición vacío"}).encode('utf-8')]

        cuerpo_bytes = environ['wsgi.input'].read(content_length)
        try:
            datos = json.loads(cuerpo_bytes.decode('utf-8'))
        except Exception:
            start_response('400 Bad Request', headers)
            return [json.dumps({"status": "error", "mensaje": "JSON inválido"}).encode('utf-8')]

        mensaje = datos.get('mensaje', '')
        sesion_id = datos.get('sesion_id', '')
        historial_raw = datos.get('historial', [])
        catalogo = datos.get('catalogo', {})

        # Estructurar historial seguro
        historial = []
        for h in historial_raw:
            try:
                historial.append(MensajeHistorial(**h))
            except Exception:
                pass

        try:
            respuesta = generar_respuesta_bot(
                mensaje=mensaje,
                sesion_id=sesion_id,
                historial=historial,
                catalogo=catalogo
            )
            start_response('200 OK', headers)
            return [json.dumps({"status": "success", "respuesta": respuesta}).encode('utf-8')]
        except Exception as e:
            logging.error("Error en gemini: %s", str(e))
            msg = f"El asistente está ocupado y no puede atenderte en estos momentos. [Detalle técnico: {str(e)}]"
            start_response('200 OK', headers)
            return [json.dumps({"status": "success", "respuesta": msg}).encode('utf-8')]

    # 3. Ruta no encontrada
    start_response('404 Not Found', headers)
    return [json.dumps({"status": "error", "mensaje": f"Ruta no encontrada: {path}"}).encode('utf-8')]
