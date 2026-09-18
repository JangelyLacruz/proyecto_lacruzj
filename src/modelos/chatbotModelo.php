<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\productosModelo;
use src\modelos\serviciosModelo;

class chatbotModelo extends conexion {

  private string $urlMicroservicio = '';
  private string $tokenSecreto     = '';

  public function __construct() {
    // Leer el .env una sola vez al instanciar la clase
    $rutaEnv          = __DIR__ . '/../config/entorno/.env';
    $variablesEnv     = parse_ini_file($rutaEnv);
    $this->tokenSecreto    = $variablesEnv['TOKEN_MICROSERVICIO'] ?? '';
    $this->urlMicroservicio = $variablesEnv['URL_MICROSERVICIO']  ?? '';
  }
  public function procesarMensajeChatbot($postData) {
    try {
      $cedula  = $_SESSION['cedula'] ?? '00000000';
      $mensaje = $postData['mensaje'] ?? '';

      //Comprobar si el usuario tiene permisos para interactuar con el chatbot
      $objAcceso = new \src\modelos\accesosModelo();
      $vPermiso = $objAcceso->validarPermisos('chatbot', 'ver chatbot');
      if ($vPermiso) {
        return [
          'status'      => 'error',
          'codigo_error' => 'ERR_ACCESO_DENEGADO',
          'mensaje'     => 'No posee permisos para interactuar con el chatbot.'
        ];
      }

      // 2. Validación estricta con limpiar_Verificar usando regex del const.php
      $respuestaVal = $this->limpiar_Verificar([
        [
          'campo_nombre'    => 'cedula_usuario',
          'campo_valor'     => &$cedula,
          'formulario_nombre' => 'cédula',
          'requerido'       => true,
          'minimo'          => minRegexCedulaRif,
          'maximo'          => maxRegexCedulaRif,
          'regex'           => regexCedulaRif
        ],
        [
          'campo_nombre'    => 'prompt',
          'campo_valor'     => &$mensaje,
          'formulario_nombre' => 'mensaje',
          'requerido'       => true,
          'minimo'          => 1,
          'maximo'          => 10000
        ]
      ]);

      if ($respuestaVal !== false) {
        return [
          'status'      => 'error',
          'codigo_error' => 'ERR_VALIDACION_MENSAJE',
          'mensaje'     => $respuestaVal['mensaje'] ?? 'Mensaje no válido.'
        ];
      }

      return $this->procesarMensajeChatbotP($cedula, $mensaje);
    } catch (\Throwable $e) {
      error_log('Error crítico en chatbotModelo: ' . $e->getMessage());
      return [
        'status'       => 'error',
        'codigo_error' => 'ERR_INTERNO_SERVIDOR',
        'mensaje'      => 'Disculpa, ha ocurrido un error interno en el servidor.',
        'detalle'      => $e->getMessage()
      ];
    }
  }
  public function guardarPresupuesto($postData) {
    try {
      $cedula    = $_SESSION['cedula'] ?? '00000000';
      $prompt    = $postData['prompt'] ?? '';
      $respuesta = $postData['respuesta'] ?? '';

      // 1. COMPROBACIÓN DE PERMISOS PRIMERO (Regla #3 y Regla #1)
      $objAcceso = new \src\modelos\accesosModelo();
      $vPermiso = $objAcceso->validarPermisos('chatbot', 'ver chatbot');
      if ($vPermiso) {
        return [
          'status'  => 'error',
          'mensaje' => 'No posee permisos para registrar presupuestos.'
        ];
      }

      // 2. Validación estricta con limpiar_Verificar
      $respuestaVal = $this->limpiar_Verificar([
        [
          'campo_nombre'    => 'cedula_usuario',
          'campo_valor'     => &$cedula,
          'formulario_nombre' => 'cédula',
          'requerido'       => true,
          'minimo'          => minRegexCedulaRif,
          'maximo'          => maxRegexCedulaRif,
          'regex'           => regexCedulaRif
        ],
        [
          'campo_nombre'    => 'prompt',
          'campo_valor'     => &$prompt,
          'formulario_nombre' => 'pregunta',
          'requerido'       => true,
          'minimo'          => 1,
          'maximo'          => 10000
        ],
        [
          'campo_nombre'    => 'respuesta_bot',
          'campo_valor'     => &$respuesta,
          'formulario_nombre' => 'presupuesto',
          'requerido'       => true,
          'minimo'          => 1,
          'maximo'          => 20000
        ]
      ]);

      if ($respuestaVal !== false) {
        return [
          'status'  => 'error',
          'mensaje' => $respuestaVal['mensaje'] ?? 'Datos no válidos.'
        ];
      }

      $guardado = $this->guardarInteraccionP($cedula, $prompt, $respuesta);
      if ($guardado) {
        return [
          'status'  => 'success',
          'mensaje' => 'El presupuesto se ha guardado exitosamente en su historial.'
        ];
      } else {
        return [
          'status'  => 'error',
          'mensaje' => 'No se pudo registrar el presupuesto.'
        ];
      }
    } catch (\Throwable $e) {
      error_log('Error en guardarPresupuesto: ' . $e->getMessage());
      return [
        'status'  => 'error',
        'mensaje' => 'Error interno al guardar el presupuesto.'
      ];
    }
  }

  private function procesarMensajeChatbotP($cedula, $mensaje) {
    // Obtener historial (BD + sesión temporal)
    $historial = $this->obtenerHistorialP($cedula);

    if (!isset($_SESSION['chat_historial'])) {
      $_SESSION['chat_historial'] = [];
    }

    // Obtener el Catálogo en tiempo real 
    $catalogo = $this->obtenerCatalogoP();

    // Empaquetar JSON para el Microservicio
    $datosPost = json_encode([
      'mensaje'    => $mensaje,
      'sesion_id'  => session_id(),
      'historial'  => $historial,
      'catalogo'   => $catalogo
    ]);

    // Petición cURL con token dinámico del .env
    $ch = curl_init($this->urlMicroservicio);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($datosPost),
      'X-Internal-Token: ' . $this->tokenSecreto
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datosPost);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $respuestaMicroservicio = curl_exec($ch);
    $httpCode               = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($respuestaMicroservicio === false || $httpCode >= 400) {
      return [
        'status'      => 'error',
        'codigo_error' => 'ERR_COMUNICACION_MICROSERVICIO',
        'mensaje'     => 'Disculpa, en este momento el asistente no está disponible. Por favor, intenta de nuevo más tarde.'
      ];
    }

    $datosPython = json_decode($respuestaMicroservicio, true);
    if (!$datosPython || !isset($datosPython['respuesta'])) {
      return [
        'status'      => 'error',
        'codigo_error' => 'ERR_JSON_INVALIDO',
        'mensaje'     => 'Error de formato en la respuesta del asistente.'
      ];
    }

    $respuestaIA = $datosPython['respuesta'];

    // Anexar al historial temporal de la sesión
    $_SESSION['chat_historial'][] = [
      'texto'    => $mensaje,
      'respuesta' => $respuestaIA,
      'fecha'    => date('Y-m-d H:i:s')
    ];

    return $datosPython;
  }
  private function obtenerHistorialP($cedula) {
    $historialMixto = [];

    try {
      $instrucciones = [
        'campos' => 'prompt, respuesta_bot, fecha_prompt',
        'tabla'  => 'prompts_usuarios',
        'BD'     => 'seguridad',
        'WHERE'  => [
          'cedula_usuario' => $cedula,
          'status'         => 1
        ]
      ];

      $resultado = $this->seleccionarDatos2($instrucciones);

      // Memoria a largo plazo (presupuestos guardados)
      if ($resultado && $resultado->rowCount() > 0) {
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $fila) {
          $historialMixto[] = [
            'texto'    => '[PRESUPUESTO GUARDADO PREVIAMENTE] ' . $fila['prompt'],
            'respuesta' => $fila['respuesta_bot'],
            'fecha'    => $fila['fecha_prompt']
          ];
        }
      }
    } catch (\Throwable $e) {
      error_log('Aviso: No se pudo consultar prompts_usuarios: ' . $e->getMessage());
    }

    // Memoria a corto plazo (sesión temporal actual)
    if (isset($_SESSION['chat_historial']) && is_array($_SESSION['chat_historial'])) {
      foreach ($_SESSION['chat_historial'] as $memoriaCorta) {
        $historialMixto[] = $memoriaCorta;
      }
    }

    return $historialMixto;
  }
  private function obtenerCatalogoP() {
    $catalogo = ['productos' => [], 'servicios' => []];

    try {
      $objProductos          = new productosModelo();
      $catalogo['productos'] = $objProductos->obtenerParaChatbot();
    } catch (\Throwable $e) {
      error_log('Aviso productos chatbot: ' . $e->getMessage());
    }

    try {
      $objServicios          = new serviciosModelo();
      $catalogo['servicios'] = $objServicios->obtenerParaChatbot();
    } catch (\Throwable $e) {
      error_log('Aviso servicios chatbot: ' . $e->getMessage());
    }

    return $catalogo;
  }
  private function guardarInteraccionP($cedula, $mensaje, $respuesta) {
    $instrucciones = [
      'tabla' => 'prompts_usuarios',
      'BD'    => 'seguridad',
      'datos' => [
        'cedula_usuario' => $cedula,
        'prompt'         => $mensaje,
        'respuesta_bot'  => $respuesta,
        'status'         => 1
      ]
    ];

    $resultado = $this->guardarDatos2($instrucciones);

    if ($resultado) {
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'chatbot',
        'accion' => 'registrar',
        'resultado' => 'Éxito',
        'viejo' => [],
        'nuevo' => [
          'cedula_usuario' => $cedula,
          'prompt'         => $mensaje,
          'respuesta_bot'  => $respuesta
        ]
      ]);
      $this->commit();
    }

    return $resultado;
  }
}
