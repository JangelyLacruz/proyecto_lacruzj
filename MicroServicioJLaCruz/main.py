from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address
from slowapi.errors import RateLimitExceeded
from app.controllers.chat_controller import router as chat_router
import os
from pathlib import Path
from dotenv import load_dotenv

BASE_DIR = Path(__file__).resolve().parent
load_dotenv(dotenv_path=BASE_DIR / ".env")

# Leer el origen permitido del .env — sin valores quemados en el código
ORIGEN_PHP = os.getenv("URL_ORIGEN_PHP", "https://jlacruzca.com")
if not ORIGEN_PHP:
    ORIGEN_PHP = "https://jlacruzca.com"

# Rate Limiter: máximo 20 peticiones por minuto por IP del cliente
limitador = Limiter(key_func=get_remote_address)

app = FastAPI(
    title="MicroServicio J. LACRUZ C.A.",
    version="1.0",
    # Documentación pública desactivada para no exponer la API al exterior
    docs_url=None,
    redoc_url=None
)

# Registrar el manejador del Rate Limiter en la app
app.state.limiter = limitador
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)

# CORS restrictivo: solo acepta peticiones del servidor PHP configurado en .env
app.add_middleware(
    CORSMiddleware,
    allow_origins=[ORIGEN_PHP],
    allow_credentials=True,
    allow_methods=["POST"],
    allow_headers=["Content-Type", "X-Internal-Token"],
)

# Incluir las rutas del controlador
app.include_router(chat_router, prefix="/api")

@app.get("/")
def senal_de_vida():
    return {"mensaje": "Microservicio de ChatBot J. LACRUZ C.A. está en línea"}
