<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;
use src\modelos\accesosModelo;

class mensajesWSModelo extends conexion {

  private int $idNotificacion = 0;
  private string $cedulaUsuario = '';
  private array $instruccionesNoti = [];
  private array $instruccionesAccion = [];

  /*Métodos para tomas datos de las views y asignarlos a los atributos*/
  public function buscarUsuariosReceptores(array $instrucciones) {
    $receptor = $instrucciones['tipo'];
    $cedula = $instrucciones['cedula'] ?? false;
    $rol = $instrucciones['rol'] ?? false;
    $permisos = $instrucciones['permisos'] ?? false;
    $receptoresAceptados = [
      'todosSinExcepcion',
      'todos',
      'rol',
      'porPermisos',
      'cedula'
    ];
    if (!in_array($receptor, $receptoresAceptados)) {
      return [
        'tipo' => '',
        'icono' => 'error',
        'titulo' => "Tipo de receptor no admitido",
        'texto' => 'El receptor que has estipulado en las instrucciones ',
      ];
    }
    $cedulasUsuarios = [];
    switch ($receptor) {
      case 'todosSinExcepcion':
        $cedulasUsuarios = $this->seleccionarDatos2([
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios as us',
          "BD" => "seguridad",
        ])->fetchAll(PDO::FETCH_COLUMN);
        break;
      case 'todos':
        $cedulasUsuarios = $this->seleccionarDatos2([
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios as us',
          "BD" => "seguridad",
          'WHERE' => [
            'cedula_usuario' => '!= ' . ($_SESSION['cedula']  ?? '')
          ],
        ])->fetchAll(PDO::FETCH_COLUMN);
        break;
      case 'rol':
        $cedulasUsuarios = $this->seleccionarDatos2([
          'campos' => 'us.cedula_usuario',
          'tabla' => 'usuarios as us',
          "BD" => "seguridad",
          'datosJoins' => [
            'roles as ro' => 'us.id_rol = ro.id_rol',
          ],
          'WHERE' => [
            'ro.nombre_rol' => $rol,
          ],
        ])->fetchAll(PDO::FETCH_COLUMN);
        break;
      case 'porPermisos':
        $rolesTotales = $this->seleccionarDatos2([
          'tabla' => 'roles',
          'campos' => 'id_rol',
          'BD' => 'seguridad'
        ])->fetchAll(PDO::FETCH_COLUMN);
        $objAccesos = new accesosModelo();
        $idsValidos = [];
        foreach ($rolesTotales as $idRol) {
          $permisosRol = $objAccesos->seleccionarPermisosPorRol($idRol);
          $tieneTodosLosPermisos = true;
          foreach ($permisos as $modulo => $permisosMo) {
            if (is_array($permisosMo)) {
              foreach ($permisosMo as $permisoMoInd) {
                if (!in_array($permisoMoInd, ($permisosRol[$modulo] ?? []))) $tieneTodosLosPermisos = false;
              }
            } else {
              if (!in_array($permisosMo, ($permisosRol[$modulo] ?? []))) $tieneTodosLosPermisos = false;
            }
          }
          if ($tieneTodosLosPermisos) $idsValidos[] = $idRol;
        }
        foreach ($idsValidos as $idRol) {
          $cedulasUsuarios += $this->seleccionarDatos2([
            'tabla' => 'usuarios',
            'campos' => 'cedula_usuario',
            'BD' => 'seguridad',
            'WHERE' => [
              'id_rol' => $idRol
            ]
          ])->fetchAll(PDO::FETCH_COLUMN);
        }
        break;
      case 'cedula':
        $cedulasUsuarios[] = $cedula;
        break;
      default:
        break;
    }
    return $cedulasUsuarios;
  }
  public function seleccionarNotificaciones($id = 0) {
    $this->idNotificacion = $id;
    $this->cedulaUsuario = $_SESSION['cedula'] ?? '';

    $campos = [[
      "campo_nombre" => "cedula_usuario",
      "campo_valor" => &$this->cedulaUsuario,
      "formulario_nombre" => "cédula",
      "requerido" => true,
      "minimo" => minRegexCedulaRifLetra,
      "maximo" => maxRegexCedulaRifLetra,
      "expresion_re" => regexCedulaRifLetra,
      "tabla" => "usuarios",
      "BD" => "seguridad",
      "debeExistir" => true,
    ]];
    if ($this->idNotificacion != 0) {
      $campos[] = [
        "campo_nombre" => "id_notificacion",
        "campo_valor" => &$this->idNotificacion,
        "formulario_nombre" => "id de la notificación",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "notificaciones",
        "BD" => "seguridad",
        "debeExistir" => true,
      ];
    }

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->seleccionarNotificacionesP();
    }
  }
  public function registrarNotificaciones(array $instruccionesNoti) {
    if (is_string($instruccionesNoti)) {
      $instruccionesNoti = json_decode($instruccionesNoti, true);
    }

    $this->instruccionesNoti = $instruccionesNoti;
    [
      "tipo" => &$tipo,
      "titulo" => &$titulo,
      "texto" => &$texto,
      "icono" => &$icono,
      "tiempo" => &$tiempo,
      "cedulasReceptores" => &$cedulasReceptores,
    ] = $this->instruccionesNoti;

    $campos = [
      [
        "campo_valor" => &$titulo,
        "formulario_nombre" => "título de la notificación",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$texto,
        "formulario_nombre" => "texto de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => &$icono,
        "formulario_nombre" => "icono de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion
      ],
      [
        "campo_valor" => &$tipo,
        "formulario_nombre" => "tipo de la notificación",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj
      ],
      [
        "campo_valor" => &$tiempo,
        "formulario_nombre" => "tiempo de la notificación",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
      ],
    ];
    foreach ($cedulasReceptores as &$cedula) {
      $campos[] = [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$cedula,
        "formulario_nombre" => "cedula",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "usuarios",
        "BD" => "seguridad",
        "debeExistir" => true
      ];
    }
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarNotificacionesP();
  }
  public function actualizarNotificaciones(string $cambio) {
    $campos = [
      [
        "campo_valor" => $cambio,
        "formulario_nombre" => "cambio de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarNotificacionesP($cambio);
    }
  }
  public function eliminarNotificaciones($id = null) {
    if ($id != null) {
      $this->idNotificacion = $id;
      $campos = [
        [
          "campo_nombre" => "id_notificacion",
          "campo_valor" => &$this->idNotificacion,
          "formulario_nombre" => "id de la notificación",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "notificaciones",
          "BD" => "seguridad",
          "debeExistir" => true,
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
    } else {
      $respuesta = false;
    }

    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarNotificacionesP();
    }
  }
  public function seleccionarAccionesResagadas() {
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$_SESSION['cedula'],
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "usuarios",
        "BD" => "seguridad",
        "debeExistir" => true
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->seleccionarAccionesResagadasP();
    }
  }
  public function registrarAccionesResagadas(array $datosAccion) {
    $this->instruccionesAccion = $datosAccion;
    [
      'accion' => &$accion,
      'modulo' => &$modulo,
      'cedulasReceptores' => &$cedulaReceptores,
    ] = $this->instruccionesAccion;

    $campos = [
      [
        "campo_valor" => &$accion,
        "formulario_nombre" => "accion resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$modulo,
        "formulario_nombre" => "modulo de la acción resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
    ];

    foreach ($cedulaReceptores as &$cedula) {
      $campos[] = [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$cedula,
        "formulario_nombre" => "cedula",
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "usuarios",
        "BD" => "seguridad",
        "debeExistir" => true
      ];
    }
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarAccionesResagadasP();
  }
  public function eliminarAccionesResagadas(array $AON) {
    $this->instruccionesAccion = $AON;
    $accion = $this->instruccionesAccion['accion'];
    $modulo = $this->instruccionesAccion['modulo'] ?? '';

    $campos = [
      [
        "campo_valor" => &$accion,
        "formulario_nombre" => "accion resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$modulo,
        "formulario_nombre" => "modulo de la acción resagada",
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarAccionesResagadasP();
    }
  }
  public function enviarMensajesWS(array $instruccionesMsj) {

    #region [REGISTRO DE LA ACCION O LA NOTIFICACIÓN EN LA BD]
    $procesarInstrucciones = function ($instruccion) {
      $registrarBD = function ($cuerpo, $receptor) {
        $cedulasReceptores = $this->buscarUsuariosReceptores($receptor);
        if ($cuerpo['accion'] == 'alertar') {
          return $this->registrarNotificaciones([
            'tipo' => $cuerpo['alerta']['tipo'],
            'titulo' => $cuerpo['alerta']['titulo'],
            'texto' => $cuerpo['alerta']['texto'],
            'icono' => $cuerpo['alerta']['icono'],
            'notifier' => $cuerpo['alerta']['notifier'],
            'tiempo' => $cuerpo['alerta']['tiempo'] ?? 0,
            'cedulasReceptores' => $cedulasReceptores,
          ]);
        } else {
          return $this->registrarAccionesResagadas([
            'accion' => $cuerpo['accion'],
            'modulo' => $cuerpo['modulo'],
            'cedulasReceptores' => $cedulasReceptores,
          ]);
        }
      };

      if(isset($instruccion['cuerpo']['accion'])){
        $resultado = $registrarBD($instruccion['cuerpo'], $instruccion['receptor']);
        if ($resultado != false) return $resultado;
      }elseif (count($instruccion['cuerpo']) > 1) {
        foreach ($instruccion['cuerpo'] as $cuerpoInd) {
          $resultado = $registrarBD($cuerpoInd, $instruccion['receptor']);
          if ($resultado != false) return $resultado;
        }
      } else {
        $resultado = $registrarBD($instruccion['cuerpo'], $instruccion['receptor']);
        if ($resultado != false) return $resultado;
      }
    };
    
    if (isset($instruccionesMsj['receptor'])) {
      $resultado = $procesarInstrucciones($instruccionesMsj);
      if ($resultado != false) return $resultado;
    } else {
      foreach ($instruccionesMsj as $instruccionInd) {
        $resultado = $procesarInstrucciones($instruccionInd);
        if ($resultado != false) return $resultado;
      }
    }
    #endregion [REGISTRO DE LA ACCION O LA NOTIFICACIÓN EN LA BD]

    #region [DIFUSIÓN POR EL WEBSOCKET]
    $emisor = [
      "emisor" => [
        "cedula" => $_SESSION['cedula'] ?? '',
        "rol" => $_SESSION['nombreRol'] ?? ''
      ]
    ];
    $estructuraEnvio = [];
    if (isset($instruccionesMsj['receptor'])) {
      $cedulasReceptores = $this->buscarUsuariosReceptores($instruccionesMsj['receptor']);
      $estructuraEnvio[] = $emisor + [
        'cuerpo' => $instruccionesMsj['cuerpo'],
        'cedulasReceptores' => $cedulasReceptores
      ];
    } else {
      foreach ($instruccionesMsj as $instruccion) {
        $cedulasReceptores = $this->buscarUsuariosReceptores($instruccion['receptor']);
        $estructuraEnvio[] = $emisor + [
          'cuerpo' => $instruccion['cuerpo'],
          'cedulasReceptores' => $cedulasReceptores
        ];
      }
    }

    $resultado = $this->hacerPeticionesAPIs([
      "url" =>
      // "https://apithevinanode-production.up.railway.app/api/enviar-mensajes-ws",
      "https://api-the-vina-node.onrender.com/api/enviar-mensajes-ws",
      // "http://localhost:1235/api/enviar-mensajes-ws",
      "metodo" => "POST",
      "datosPe" => $estructuraEnvio,
      "enviarComoJSON" => true
    ]);
    #endregion [DIFUSIÓN POR EL WEBSOCKET]

    if (!isset($resultado['error'])) {
      if (!isset($instruccionesMsj['noCommit'])) $this->commit();
      return ['resultado' => $resultado];
    } else {
      return ['error' => $resultado];
    }
  }

  private function seleccionarNotificacionesP() {
    if ($this->idNotificacion != null && $this->idNotificacion != "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          no.titulo_notificacion, no.texto_notificacion, 
          no.fecha_creacion_notificacion, no.tipo_notificacion
        ',
        'tabla' => 'notificaciones as no',
        "BD" => "seguridad",
        'WHERE' => [
          'no.id_notificacion' =>  $this->idNotificacion,
          'nu.cedula_usuario' =>  $this->cedulaUsuario,
          'nu.status' => ' != ' .  0,
        ],
      ]);

      $notificacion = $resultado->fetch(PDO::FETCH_ASSOC);
      return $notificacion;
    } else {

      $resultado = $this->seleccionarDatos2([
        'campos' => '
          no.id_notificacion,
          ino.path_icono_notificacion AS icono_notificacion,
          tn.nombre_tipo_notificacion AS tipo_notificacion,
          no.titulo_notificacion,
          no.texto_notificacion,
          no.fecha_creacion_notificacion, 
          no.status
        ',
        'tabla' => 'notificaciones as no',
        "BD" => "seguridad",
        'datosJoins' => [
          'iconos_notificaciones as ino' => 'no.id_icono_notificacion = ino.id_icono_notificacion',
          'tipos_notificaciones as tn' => 'no.id_tipo_notificacion = tn.id_tipo_notificacion',
        ],
        'WHERE' => [
          'no.cedula_usuario' => $this->cedulaUsuario,
          'no.status' => '!= ' . 0
        ],
        'ORDER' => 'no.fecha_creacion_notificacion DESC'
      ]);

      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Sin notificaciones",
          "texto" => "El usuario no posee notificaciones registradas en la Base de Datos",
          "icono" => "info"
        ];
        return $alerta;
        exit();
      } else {
        $notificaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
      }
      return $notificaciones;
    }
  }
  private function registrarNotificacionesP() {
    [
      "tipo" => &$tipo,
      "titulo" => &$titulo,
      "texto" => &$texto,
      "icono" => &$icono,
      "tiempo" => &$tiempo,
      "cedulasReceptores" => &$cedulasReceptores,
    ] = $this->instruccionesNoti;

    //Id icono
    $idIcono = $this->VEYSNEC([
      'campos' => 'id_icono_notificacion',
      'tabla' => 'iconos_notificaciones',
      "BD" => "seguridad",
      'WHERE' => [
        'path_icono_notificacion' => $icono,
      ],
    ]);
    // Id tipo notificacion
    $idTipoNotificacion = $this->VEYSNEC([
      'campos' => 'id_tipo_notificacion',
      'tabla' => 'tipos_notificaciones',
      "BD" => "seguridad",
      'WHERE' => [
        'nombre_tipo_notificacion' => $tipo,
      ],
    ]);

    // PROCESO DE ASOCIAR LA NOTIFICACION A EL O LOS USUARIOS
    $asociarCedulaANotificacion = function ($cedula, $infoNot) {
      $ultimoId = $this->guardarDatos2([
        'tabla' => 'notificaciones',
        "BD" => "seguridad",
        'datos' => [
          "cedula_usuario" => $cedula,
          'id_icono_notificacion' => $infoNot['id_icono_notificacion'],
          'id_tipo_notificacion' => $infoNot['id_tipo_notificacion'],
          'tiempo_notificacion' => $infoNot['tiempo_notificacion'],
          'titulo_notificacion' => $infoNot['titulo_notificacion'],
          'texto_notificacion' => $infoNot['texto_notificacion'],
          "fecha_creacion_notificacion" => $this->fechaHoraSel('fecha_hora_BD'),
        ]
      ]);
      if ($ultimoId == false || $ultimoId == 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Notificación no registrada",
          "texto" => "La notificación no ha sido registrada al usuario",
          "icono" => "error",
        ];
      }
      return false;
    };
    $infoNoti = [
      'id_icono_notificacion' => $idIcono,
      'id_tipo_notificacion' => $idTipoNotificacion,
      'tiempo_notificacion' => $tiempo,
      'titulo_notificacion' => $titulo,
      'texto_notificacion' => $texto,
    ];
    if (is_array($cedulasReceptores)) {
      foreach ($cedulasReceptores as $cedula) {
        $resultado = $asociarCedulaANotificacion($cedula, $infoNoti);
        if ($resultado != false) return $resultado;
      }
    } else {
      $resultado = $asociarCedulaANotificacion($cedulasReceptores, $infoNoti);
      if ($resultado != false) return $resultado;
    }
    return false;
  }
  private function actualizarNotificacionesP(string $cambio) {
    if ($cambio == 'marcarTodasComoLeidas') {

      $notSinMarcarComoLeidas = $this->seleccionarDatos2([
        "tabla" => "notificaciones",
        "BD" => "seguridad",
        'campos' => 'id_notificacion',
        'WHERE' => [
          'status' => [
            '!=' => [0, 2]
          ],
          "cedula_usuario" => $_SESSION['cedula'],
        ]
      ]);
      if ($notSinMarcarComoLeidas->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Sin notificaciones no leídas",
          "texto" => "No hay notificaciones sin leer",
          "icono" => "warning",
        ];
      }

      $resultado = $this->actualizarDatos2([
        "tabla" => "notificaciones",
        "BD" => "seguridad",
        "datos" => [
          "status" => 2
        ],
        "WHERE" => [
          "cedula_usuario" => $_SESSION['cedula'],
          "status" => '!= ' . 0,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Error al marcar como leídas",
          "texto" => "No se logró marcar las notificaciones como leídas",
          "icono" => "error",
        ];
      }
      $this->commit();
      return [
        "tipo" => "simple",
        "titulo" => "Listo",
        "texto" => "Notificaciones correctamente marcadas como leídas",
        "icono" => "success",
      ];
    }
  }
  private function eliminarNotificacionesP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => "notificaciones",
      "BD" => "seguridad",
      'WHERE' => [
        "cedula_usuario" => $_SESSION['cedula']
      ]
    ]);
    if ($resultado > 0) {

      $alerta = [
        "tipo" => "simple",
        "titulo" => "Notificaciones eliminadas",
        "texto" => "Las notificaciones del buzón han sido eliminadas del buzón",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Notificaciones no eliminadas",
        "texto" => "No se ha podido eliminar las notificaciones del buzón",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
  private function seleccionarAccionesResagadasP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        aru.accion_resagada, mo.nombre_modulo
      ',
      'tabla' => 'acciones_resagadas_usuarios as aru',
      "BD" => "seguridad",
      'datosJoins' => [
        'modulos as mo' => 'aru.id_modulo = mo.id_modulo',
      ],
      'WHERE' => [
        'aru.cedula_usuario' => $_SESSION['cedula']
      ]
    ]);

    $resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
  }
  private function registrarAccionesResagadasP() {
    [
      'accion' => $accion,
      'cedulasReceptores' => $cedulasReceptores,
    ] = $this->instruccionesAccion;
    $modulo = $this->instruccionesAccion['modulo'] ?? 'cualquiera';

    //EL MODULO 
    $idModulo = $this->VEYSNEC([
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      "BD" => "seguridad",
      'WHERE' => [
        'nombre_modulo' => $modulo
      ],
    ]);

    $asociarAccion = function ($cedula, $idModulo, $accion) {
      //Registramos la accion solo si ya no hay una vigente
      $idAccionResagada = $this->VEYSNEC([
        'campos' => 'id_accion_resagada_usuario',
        'tabla' => 'acciones_resagadas_usuarios',
        "BD" => "seguridad",
        'WHERE' => [
          'id_modulo' => $idModulo,
          'accion_resagada' => $accion,
          'cedula_usuario' => $cedula,
        ],
      ]);
      if ($idAccionResagada == false && $idAccionResagada == 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Acción no registrada",
          "texto" => "La acción no ha sido registrada al usuario",
          "icono" => "error",
        ];
      }
      return false;
    };
    if (is_array($cedulasReceptores)) {
      foreach ($cedulasReceptores as $cedula) {
        $resultado = $asociarAccion($cedula, $idModulo, $accion);
        if ($resultado != false) return $resultado;
      }
    } else {
      $resultado = $asociarAccion($cedulasReceptores, $idModulo, $accion);
      if ($resultado != false) return $resultado;
    }
    return false;
  }
  private function eliminarAccionesResagadasP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      "BD" => "seguridad",
      'WHERE' => [
        'nombre_modulo' => $this->instruccionesAccion['modulo'] ?? 'cualquiera'
      ]
    ]);
    $idModulo = $resultado->fetch(PDO::FETCH_COLUMN);
    $resultado = $this->eliminarDatos2([
      'tabla' => 'acciones_resagadas_usuarios',
      "BD" => "seguridad",
      'fisico' => true,
      'WHERE' => [
        'cedula_usuario' => $_SESSION['cedula'],
        'accion_resagada' => $this->instruccionesAccion['accion'],
        'id_modulo' => $idModulo,
      ]
    ]);
    if ($resultado <= 0) {
      return 'Ocurrió un error';
    }
    $this->commit();
    return 'Éxito';
  }
}
