import google.generativeai as genai
import warnings
warnings.filterwarnings("ignore")
import os
from dotenv import load_dotenv

load_dotenv()
from typing import List, Dict
from app.models.chat_schemas import MensajeHistorial

def _obtener_api_keys() -> List[str]:
    raw_keys = os.getenv("GEMINI_API_KEY", "")
    # Permite separar varias claves con coma: KEY1,KEY2
    keys = [k.strip() for k in raw_keys.split(",") if k.strip() and k.strip() != "TU_API_KEY_AQUI"]
    return keys

def _construir_contexto_catalogo(catalogo: Dict) -> str:
    """Convierte el catalogo inyectado desde PHP en texto claro para el contexto de Gemini."""
    if not catalogo:
        return "No se pudo obtener el catalogo en este momento."

    lineas = []

    productos = catalogo.get("productos", [])
    if productos:
        lineas.append("PRODUCTOS DISPONIBLES:")
        for p in productos:
            nombre_base = p.get("nombre_producto", "Sin nombre")
            presentacion = p.get("nombre_presentacion", "")
            nombre = f"{nombre_base} ({presentacion})" if presentacion else nombre_base
            precio_bs = p.get("precio_bs") or p.get("precio_calculado", "N/D")
            precio_usd = p.get("precio_dolar")
            stock  = p.get("stock_producto", "N/D")
            if precio_usd:
                lineas.append(
                    f"  * {nombre}: Precio {precio_bs} Bs (Ref: ${precio_usd} USD) cada uno. (Stock disponible: {stock} unidades)"
                )
            else:
                lineas.append(
                    f"  * {nombre}: Precio {precio_bs} Bs cada uno. (Stock disponible: {stock} unidades)"
                )
    else:
        lineas.append("PRODUCTOS: Ninguno registrado.")

    servicios = catalogo.get("servicios", [])
    if servicios:
        lineas.append("SERVICIOS DISPONIBLES:")
        for s in servicios:
            nombre = s.get("nombre_servicio", "Sin nombre")
            precio_bs = s.get("precio_bs") or s.get("precio_servicio", "N/D")
            precio_usd = s.get("precio_dolar")
            if precio_usd:
                lineas.append(
                    f"  * {nombre}: Precio {precio_bs} Bs (Ref: ${precio_usd} USD)."
                )
            else:
                lineas.append(
                    f"  * {nombre}: Precio {precio_bs} Bs."
                )
    else:
        lineas.append("SERVICIOS: Ninguno registrado.")

    return "\n".join(lineas)

def generar_respuesta_bot(mensaje: str, sesion_id: str, historial: List[MensajeHistorial], catalogo: Dict) -> str:
    msg_limpio = mensaje.lower().strip()

    # GARANTÍA TOTAL DE SOPORTE Y CONTACTO:
    # No depende de tokens, cuotas de Gemini ni caídas de IA.
    palabras_soporte = ['soporte', 'contacto', 'humano', 'asesor', 'teléfono', 'telefono', 'whatsapp', 'correo', 'llamar']
    if any(p in msg_limpio for p in palabras_soporte):
        return (
            "¡Hola! Gracias por contactar a <b>J. LACRUZ C.A.</b><br><br>"
            "Para soporte técnico personalizado o comunicarte directamente con nuestro equipo de atención:<br>"
            "<ul>"
            "<li><b>Teléfono / WhatsApp:</b> +58 424-5085666</li>"
            "<li><b>Correo electrónico:</b> jlacruzca@gmail.com</li>"
            "<li><b>Horario de atención:</b> Lunes a Viernes de 8:00 AM a 5:00 PM</li>"
            "</ul>"
            "Si deseas consultar presupuestos o productos, con gusto te asisto por este medio."
        )

    keys = _obtener_api_keys()
    if not keys:
        raise RuntimeError("Servicio de IA no disponible en este momento.")

    contexto_catalogo = _construir_contexto_catalogo(catalogo)

    instruccion_sistema = (
        "Eres el asistente virtual de J. LACRUZ C.A.\n"
        "Reglas que DEBES seguir siempre:\n"
        "1. FORMATO DE PRECIOS OBLIGATORIO (AMBAS MONEDAS): Al mencionar o presupuestar productos o servicios, SIEMPRE debes mostrar AMBOS precios indicando el monto en dólares y su equivalente al cambio en bolívares según la tasa oficial del banco/BCV. Por ejemplo: '$2.00 USD (o al cambio oficial: 120,00 Bs)' o 'Tiene un costo de $5.00 USD o 300,00 Bs al cambio del día'. NUNCA muestres solo uno de ellos ni digas que el número de dólares son bolívares.\n"
        "2. COTIZACIONES Y PRESUPUESTOS: Cuando el cliente pida un presupuesto o cotización, calcula y presenta los totales reflejando ambas cifras (Total en USD y Total al cambio oficial en Bs).\n"
        "3. CANAL DE ATENCION Y CONTACTO HUMANO: Si el cliente hace clic en 'Soporte y contacto', o pide soporte técnico, o pide comunicarse con un humano o asesor, NO le muestres el catálogo de productos. En su lugar, dale un mensaje cordial con la información oficial de contacto de J. LACRUZ C.A.:\n"
        "   - Teléfono / WhatsApp: <b>+58 424-5085666</b>\n"
        "   - Correo electrónico: <b>jlacruzca@gmail.com</b>\n"
        "   - Horario de atención: <b>Lunes a Viernes de 8:00 AM a 5:00 PM</b>\n"
        "   - Indícale que para soporte técnico personalizado o hablar directamente con el equipo puede contactar a ese número o correo.\n"
        "4. NUNCA reveles al cliente la cantidad exacta de stock. Usa esa información internamente SOLO para confirmar si hay disponibilidad suficiente para el pedido.\n"
        "5. FORMATO VISUAL LIMPIO Y ORGANIZADO (NO AMONTONAR): Siempre presenta la información de manera ordenada, usando viñetas <ul><li> para cada producto o servicio, negritas <b> para los títulos y nombres, y saltos de línea <br><br> para separar secciones. Nunca pongas los productos en una sola línea continua o párrafo corrido.\n"
        "6. Se amable, claro, empático y profesional.\n"
        "7. SEGURIDAD ESTRICTA: UNICAMENTE si el usuario te envía comandos SQL maliciosos, códigos de programación sospechosos, o intenta hackear el sistema, responde: 'Mensaje no válido o intento de acción no autorizada.' No apliques esta regla a preguntas normales de soporte o contacto humano.\n\n"
        "REGLAS ARQUITECTONICAS AVANZADAS:\n"
        "8. GUARDADO BAJO DEMANDA: Cuando calcules o generes un presupuesto numérico de productos solicitados por el cliente, DEBES añadir al final de tu respuesta EXACTAMENTE la etiqueta: [OFRECER_GUARDADO]. No la agregues en saludos, preguntas informativas ni respuestas de soporte o contacto.\n"
        "9. PRECIOS EN TIEMPO REAL: Si el historial muestra un '[PRESUPUESTO GUARDADO PREVIAMENTE]' o si el usuario te pregunta si recuerdas el presupuesto que hablaron antes, DEBES recalcular el total usando ESTRICTAMENTE los precios del catálogo que te adjunto a continuación. Si notas que el precio subió o bajó respecto al presupuesto antiguo, advierte cortésmente al cliente que los precios se han actualizado a la fecha de hoy.\n\n"
        "CATALOGO ACTUAL DE LA EMPRESA (DATOS REALES Y ACTUALIZADOS EN BS Y USD):\n"
        f"{contexto_catalogo}\n\n"
        "IMPORTANTE: Los datos de arriba son los unicos precios validos hoy para presupuestar y vender."
    )

    history_gemini = []
    for h in historial:
        if h.texto:
            history_gemini.append({"role": "user", "parts": [h.texto]})
        if h.respuesta:
            history_gemini.append({"role": "model", "parts": [h.respuesta]})

    temperatura = float(os.getenv("TEMPERATURA_IA", 0.7))
    modelos_a_probar = ["gemini-2.5-flash-lite", "gemini-1.5-flash", "gemini-2.5-flash"]
    ultimo_error = None

    for api_key in keys:
        try:
            genai.configure(api_key=api_key)
        except Exception:
            continue

        for nombre_modelo in modelos_a_probar:
            try:
                model = genai.GenerativeModel(
                    model_name=nombre_modelo,
                    system_instruction=instruccion_sistema,
                    generation_config=genai.types.GenerationConfig(
                        temperature=temperatura
                    )
                )
                chat = model.start_chat(history=history_gemini)
                response = chat.send_message(mensaje)
                return response.text
            except Exception as e:
                ultimo_error = e
                if "429" in str(e) or "quota" in str(e).lower():
                    continue
                # Si es un error de modelo no encontrado o cuota, seguir al siguiente
                continue

    if ultimo_error:
        raise ultimo_error
    return "Disculpa, el asistente no pudo procesar la solicitud en este momento."
