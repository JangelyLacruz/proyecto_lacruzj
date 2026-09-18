<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\clientesModelo;
use src\modelos\accesosModelo;
use src\modelos\correosModelo;
use PDO;
use Datetime;

class usuariosModelo extends conexion {
  private string $cedulaUsuario = '';
  private int $rolUsuario = 0;
  private string $nombreUsuario = '';
  private string $apellidoUsuario = '';
  private string $usuarioUsuario = '';
  private string $contrasena1Usuario = '';
  private string $telefonoUsuario = '';
  private string $correoUsuario = '';
  private string $direccionUsuario = '';
  private array $fotoUsuario = [];
  private array $preguntasSeguridadUsuario = [];
  private string $codigoRecContrasenaUsuario = '';

  public function validarUsuarios(string|null $permiso, null|array  &$info = null, null|array $requerido = null) {
    if (isset($permiso)) {
      $objAcceso = new accesosModelo();
      $v = $objAcceso->validarPermisos('usuarios', $permiso);
      if ($v) return $v;
    }

    //Concatenar los strings
    if (isset($info['prefijo_telefono_usuario'])) {
      $info['telefono_usuario'] = $info['prefijo_telefono_usuario'] . $info['telefono_usuario'];
    }
    if (isset($info['codigo_cedula_usuario'])) {
      $info['cedula_usuario'] = $info['codigo_cedula_usuario'] . $info['cedula_usuario'];
    }
    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        "apellido_usuario" => [
          ...molNombrePer,
          "nombreAlerta" => "apellido",
        ],
        "cedula_usuario" => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "cédula",
          "nombreBD" => "cedula_usuario",
          "tablaBD" => "usuarios",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
          "BD" => 'seguridad',
        ],
        'codigo_recuperacion' => [
          ...molDescripcion,
          'nombreAlerta' => 'codigo de recuperación'
        ],
        "contrasena1_usuario" => [
          ...molContrasena,
          "nombreAlerta" => "contraseña",
        ],
        "contrasena2_usuario" => [
          ...molContrasena,
          "nombreAlerta" => "contraseña de confirmación",
        ],
        "correo_usuario" => [
          ...molCorreo,
          "nombreBD" => "correo_usuario",
          "nombreAlerta" => "correo",
          "tablaBD" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnicoBD" => true
        ],
        "direccion_usuario" => [
          ...molDescripcion,
          "nombreAlerta" => "dirección",
        ],
        "foto_usuario" => [
          ...molFotoInd,
          "nombreAlerta" => "foto de pérfil",
        ],
        "hashContrasena" => [
          ...molDescripcion,
          'nombreAlerta' => 'Token de seguridad'
        ],
        "id_rol" => [
          ...molId,
          "nombreAlerta" => "rol",
          "nombreBD" => "id_rol",
          "tabla" => "roles",
          "BD" => 'seguridad',
          "debeExistir" => true
        ],
        "nombre_usuario" => [
          ...molNombrePer,
          "nombreAlerta" => "nombre",
        ],
        'preguntas_respuestas' => [
          'tipo' => 'array',
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'id_pregunta' => [
                ...molIdSeguro,
                "nombreAlerta" => "id de la pregunta",
                "nombreBD" => "id_pregunta",
                "tabla" => "preguntas_seguridad",
                "BD" => 'seguridad',
                "debeExistir" => true
              ],
              'respuesta' => [
                ...molDescripcion,
                'nombreAlerta' => 'respuesta de la pregunta'
              ]
            ],
            'requerido' => [
              'id_pregunta',
              'respuesta'
            ]
          ],
          'nroItems' => 6,
          'nombreAlerta' => 'preguntas de seguridad'
        ],
        "tipo_metodo" => [
          ...molId,
          "nombreAlerta" => "método de recuperación",
        ],
        "telefono_usuario" => [
          ...molTelefono,
          "nombreBD" => 'telefono_usuario',
          "nombreAlerta" => "teléfono",
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true,
        ],
        "usuario_usuario" => [
          ...molUsuario,
          "nombreBD" => "usuario_usuario",
          "nombreAlerta" => "nombre de usuario",
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true,
        ],

      ],
      'requerido' => $requerido
    ];

    if (isset($info) && isset($requerido)) {
      if (!isset($info['accion'])) $info['accion'] = 'cualquiera';
      if ($info['accion'] == 'eliminar') {
        $esquema['propiedades']['cedula_usuario']['diferenteA'] = $this->seleccionarDatos2([
          'campos' => 'u.cedula_usuario',
          'tabla' => 'usuarios as u',
          'BD' => 'seguridad',
          'datosJoins' => [
            'roles as ro' => 'u.id_rol = ro.id_rol'
          ],
          'WHERE' => [
            'ro.nombre_rol' => 'SUPER USUARIO'
          ]
        ])->fetch(PDO::FETCH_COLUMN);
      }
      if ($info['accion'] == 'iniciarSesion') {
        unset($esquema['propiedades']['usuario_usuario']['debeSerUnicoBD']);
      }
      if ($info['accion'] == 'registrar') {
        unset($esquema['propiedades']['cedula_usuario']['debeExistirBD']);
        $esquema['propiedades']['contrasena1_usuario']['deberSerIgual'] = $info['contrasena2_usuario'] ?? '';
        $esquema['propiedades']['contrasena2_usuario']['deberSerIgual'] = $info['contrasena1_usuario'] ?? '';
      }

      //Para verificar que si se quiere act la contraseña esta sea igual
      if (
        $info['accion'] == 'actualizar' && (
          $info['contrasena1_usuario'] ?? '' != '' ||
          $info['contrasena2_usuario'] ?? '' != '')
      ) {
        $esquema['propiedades']['contrasena1_usuario']['deberSerIgual'] = $info['contrasena2_usuario'] ?? '';
        $esquema['propiedades']['contrasena2_usuario']['deberSerIgual'] = $info['contrasena1_usuario'] ?? '';
      }

      //Para asegurarnos que el metodo de verificacion exista
      if (($info['accion'] ?? '') == 'validarMetodoRecContrasena') {
        if (($info['tipo_metodo'] ?? '') == 'preguntasSeguridad') {
          array_push($esquema['requerido'], 'preguntas_respuestas');
        }
        if (($info['tipo_metodo'] ?? '') == 'codigo') {
          array_push($esquema['requerido'], 'codigo_recuperacion');
        }
        $esquema['propiedades']['preguntas_respuestas']['nroItems'] = '2';
      }

      $v = $this->limpiarValidar($info, $esquema);
      if ($v) return $v;
    }

    return false;
  }
  public function seleccionarUsuarios(array $info) {
    if (($info['cedula_usuario'] ?? '') != '') {
      $r = $this->validarUsuarios(null, $info, ['cedula_usuario']);
      if ($r) return $r;
      $this->cedulaUsuario = $info['cedula_usuario'];
    } else {
      $r = $this->validarUsuarios(null);
      if ($r) return $r;
    }
    return $this->seleccionarUsuariosP($info);
  }
  public function registrarUsuarios(array $info) {
    $r = $this->validarUsuarios(null, $info, [
      'apellido_usuario',
      'cedula_usuario',
      'contrasena1_usuario',
      'contrasena2_usuario',
      'correo_usuario',
      'id_rol',
      'nombre_usuario',
      'telefono_usuario',
      'usuario_usuario',
      'preguntas_respuestas'
    ]);
    if ($r) return $r;
    [
      'apellido_usuario' => $this->apellidoUsuario,
      'cedula_usuario' => $this->cedulaUsuario,
      'contrasena1_usuario' => $this->contrasena1Usuario,
      'id_rol' => $this->rolUsuario,
      'nombre_usuario' => $this->nombreUsuario,
      'telefono_usuario' => $this->telefonoUsuario,
      'correo_usuario' => $this->correoUsuario,
      'usuario_usuario' => $this->usuarioUsuario,
      'direccion_usuario' => $this->direccionUsuario,
      'preguntas_respuestas' => $this->preguntasSeguridadUsuario,
    ] = $info;
    $this->fotoUsuario = $info['foto_usuario'] ?? [];
    $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
    return $this->registrarUsuariosP();
  }
  public function actualizarUsuarios(array $info) {
    $requerido = [
      'cedula_usuario' => 'cedula_usuario_act',
      'id_rol',
      'nombre_usuario',
      'apellido_usuario',
      'usuario_usuario',
      'telefono_usuario',
      'correo_usuario',
      'direccion_usuario',
    ];
    if (($info['contrasena1_usuario'] ?? '') != '' || ($info['contrasena2_usuario'] ?? '') != '') {
      array_push($requerido, 'contrasena1_usuario', 'contrasena2_usuario');
    }
    $r = $this->validarUsuarios('actualizar', $info, $requerido);
    if ($r) return $r;

    [
      'apellido_usuario' => $this->apellidoUsuario,
      'cedula_usuario' => $this->cedulaUsuario,
      'contrasena1_usuario' => $this->contrasena1Usuario,
      'correo_usuario' => $this->correoUsuario,
      'id_rol' => $this->rolUsuario,
      'nombre_usuario' => $this->nombreUsuario,
      'telefono_usuario' => $this->telefonoUsuario,
      'usuario_usuario' => $this->usuarioUsuario,
      'direccion_usuario' => $this->direccionUsuario,
    ] = $info;

    //Usamos este metodo para procesar e incriptar la contraseña
    if ($this->contrasena1Usuario != '') {
      $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
    }
    return $this->actualizarUsuariosP();
  }
  public function eliminarUsuarios(array $info) {
    $r = $this->validarUsuarios('eliminar', $info, ['cedula_usuario']);
    if ($r) return $r;

    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarUsuariosP();
  }
  public function actualizarFotosUsuarios(array $info) {
    $r = $this->validarUsuarios('actualizar', $info, ['cedula_usuario', 'foto_usuario']);
    if ($r) return $r;
    $this->cedulaUsuario = $info['cedula_usuario'];
    $this->fotoUsuario = $info['foto_usuario'];
    return $this->actualizarFotosUsuariosP();
  }
  public function eliminarFotosUsuarios(array $info) {
    $r = $this->validarUsuarios('actualizar', $info, ['cedula_usuario']);
    if ($r) return $r;
    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarFotosUsuariosP();
  }
  public function iniciarSesionUsuarios(array $info) {

    //Enviar verificacion interna al servidor de Google
    if (empty($info['g-recaptcha-response']) && !modoDev) {
      return [
        "tipo" => "simple",
        "titulo" => "Seguridad",
        "texto" => "Falta completar la validación del puzle de seguridad.",
        "icono" => "error"
      ];
    }
    $resultadoCaptcha = $this->hacerPeticionesAPIs([
      'url' => 'https://www.google.com/recaptcha/api/siteverify',
      'datosPe' => [
        'secret'   => '6LdSVPgsAAAAAFAQ6_8Z-y0Q_0s-Xy3XVLGtydmw',
        'response' => $info['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
      ]
    ]);
    if (($resultadoCaptcha['success'] ?? false) != true && !modoDev) {
      return [
        "tipo" => "simple",
        "titulo" => "Fallo de Seguridad",
        "texto" => "La verificación del captcha falló. Inténtelo nuevamente.",
        "icono" => "error"
      ];
    }

    $r = $this->validarUsuarios(null, $info, ['usuario_usuario', 'contrasena1_usuario']);
    if ($r) return $r;
    $this->usuarioUsuario = $info['usuario_usuario'];
    $this->contrasena1Usuario = $info['contrasena1_usuario'];
    return $this->iniciarSesionUsuariosP();
  }
  public function cerrarSesionUsuarios() {
    return $this->cerrarSesionUsuariosP();
  }
  public function validarTipoMetodoRecContrasenaUsuarios(array $info) {
    $r = $this->validarUsuarios(null, $info, ['cedula_usuario', 'tipo_metodo']);
    if ($r) return $r;
    $this->preguntasSeguridadUsuario = $info['preguntas_respuestas'] ?? [];
    $this->codigoRecContrasenaUsuario = $info['codigo_recuperacion'] ?? '';
    $this->cedulaUsuario = $info['cedula_usuario'] ?? '';
    return $this->validarTipoMetodoRecContrasenaUsuariosP($info);
  }
  public function restablecerContrasenaUsuario(array $info) {
    $r = $this->validarUsuarios(null, $info, [
      'cedula_usuario',
      'contrasena1_usuario',
      'contrasena2_usuario',
      'hashContrasena'
    ]);
    if ($r) return $r;
    $this->contrasena1Usuario = $info['contrasena1_usuario'] ?? [];
    $this->cedulaUsuario = $info['cedula_usuario'] ?? '';
    return $this->restablecerContrasenaUsuarioP($info);
  }
  public function solicitarCodigoRecContrasena(array $info) {
    $r = $this->validarUsuarios(null, $info, ['cedula_usuario', 'tipo_metodo']);
    if ($r) return $r;
    $this->cedulaUsuario = $info['cedula_usuario'] ?? [];
    return $this->solicitarCodigoRecContrasenaP($info);
  }
  public function programarCierreSesionUsuario(array $info) {
    $info['cedula_usuario'] = $_SESSION['cedula'];
    $v = $this->validarUsuarios(null, $info, ['cedula_usuario']);
    if ($v) return $v;

    $this->cedulaUsuario = $info['cedula_usuario'];

    return $this->programarCierreSesionUsuarioP();
  }
  public function validarVigenciaSesionUsuario() {
    $info = ['cedula_usuario' => $_SESSION['cedula']];
    $v = $this->validarUsuarios(null, $info, ['cedula_usuario']);
    if ($v) return $v;

    $this->cedulaUsuario = $info['cedula_usuario'];

    return $this->validarVigenciaSesionUsuarioP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ] --//
  private function seleccionarUsuariosP(array $info) {
    if ($this->cedulaUsuario == null || $this->cedulaUsuario == "") {
      return  $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_usuarios_todos',
        "BD" => 'seguridad',
        'WHERE' => [
          "cedula_usuario" => [
            "!=" => [30485684, $_SESSION['cedula'] ?? '']
          ]
        ],
      ])->fetchAll();
    } else {
      switch ($info['tipoConsulta'] ?? '') {
        case 'verificarExistencia':
          return $this->seleccionarDatos2([
            'campos' => 'cedula_usuario',
            'tabla' => 'usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
              "cedula_usuario" => $this->cedulaUsuario,
            ]
          ])->fetch();
        case 'solicitarPreguntasSeguridad':
          return $this->seleccionarDatos2([
            'campos' => 'ps.id_pregunta, ps.texto_pregunta',
            'tabla' => 'preguntas_seguridad_usuarios as psu',
            'BD' => 'seguridad',
            'datosJoins' => [
              'preguntas_seguridad as ps' => 'psu.id_pregunta = ps.id_pregunta'
            ],
            'WHERE' => [
              "psu.cedula_usuario" => $this->cedulaUsuario,
            ]
          ])->fetchAll();
        default:
          return $this->seleccionarDatos2([
            'campos' => '
              cedula_usuario, nombre_usuario,
              apellido_usuario, telefono_usuario, correo_usuario,
              usuario_usuario, id_rol,contrasena_usuario,direccion_usuario,
              foto_usuario
            ',
            'tabla' => 'usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
              "cedula_usuario" => $this->cedulaUsuario,
            ]
          ])->fetch();
      }
    }
  }
  private function registrarUsuariosP() {
    $fotoUsuario = '';
    $objBitacora = new bitacoraModelo();

    $error = function () use ($fotoUsuario, $objBitacora) {
      $this->rollback();
      if (isset($_SESSION['cedula'])) {
        $objBitacora->registrarBitacora([
          'modulo' => 'usuarios',
          'accion' => 'registrar usuario con la cedula/rif: ' . $this->cedulaUsuario,
          'resultado' => 'Fallido',
          'commit' => true
        ]);
      }
      if ($fotoUsuario != '') $this->Imagenes_Eli2('usuarios', $fotoUsuario);
    };
    if ($this->fotoUsuario != []) {
      $fotoUsuario = $this->Imagenes_Reg('usuarios', $this->fotoUsuario, 'usuarios');
    }
    $datoNuevos = [
      "cedula_usuario" => $this->cedulaUsuario,
      "nombre_usuario" => $this->nombreUsuario,
      "apellido_usuario" => $this->apellidoUsuario,
      "correo_usuario" => $this->correoUsuario,
      "telefono_usuario" => $this->telefonoUsuario,
      "id_rol" => $this->rolUsuario,
      "usuario_usuario" => $this->usuarioUsuario,
      "contrasena_usuario" => $this->contrasena1Usuario,
      "foto_usuario" => $fotoUsuario,
      "direccion_usuario" => $this->direccionUsuario,
    ];
    $resultado = $this->guardarDatos2([
      'datos' => $datoNuevos,
      'tabla' => 'usuarios',
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        "tipo" => "simple",
        "titulo" => "Usuario no registrado",
        "texto" => "El usuario no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }

    $clienteObj = new clientesModelo();
    $cliente = $clienteObj->seleccionarClientes([
      'rif_cedula_cliente' => $this->cedulaUsuario,
      "vieneDelModuloUsuarios" => true
    ]);

    // Preguntas
    foreach ($this->preguntasSeguridadUsuario as $pregunta) {
      $idPreguntasUsuario = $this->generarCodSeg([
        'tablaBD' => 'preguntas_seguridad_usuarios',
        'prefijo' => 'PRUS',
        'campoID' => 'id_pregunta_usuario',
        'BD' => 'seguridad',
      ]);
      $resultado = $this->guardarDatos2([
        'tabla' => 'preguntas_seguridad_usuarios',
        'datos' => [
          'id_pregunta_usuario' => $idPreguntasUsuario,
          'id_pregunta' => $pregunta['id_pregunta'],
          'cedula_usuario' => $this->cedulaUsuario,
          'respuesta_pregunta' => password_hash($pregunta['respuesta'], PASSWORD_BCRYPT, ["cost" => 10])
        ],
        'BD' => 'seguridad'
      ]);
      if ($resultado <= 0 || $resultado == false) {
        return [
          'tipo' => 'simple',
          'titulo' => "Pregunta no registrada",
          'texto' => 'Las preguntas de seguridad no pudieron ser registradas correctamente',
          'icono' => 'error',
        ];
      }
    }

    if (!isset($cliente['rif_cedula_cliente'])) {
      $resultado = $clienteObj->registrarClientes([
        "rif_cedula_cliente" => $this->cedulaUsuario,
        "razon_social_cliente" => $this->nombreUsuario . ' ' . $this->apellidoUsuario,
        "telefono_cliente" => $this->telefonoUsuario,
        "correo_cliente" => $this->correoUsuario,
        "direccion_cliente" => $this->direccionUsuario,
        "sinCommit" => true,
        "vieneDelModuloUsuarios" => true
      ]);
      if (($resultado['icono'] ?? '') != 'success') return $resultado;
    }

    $objNot = new mensajesWSModelo();
    $r= $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    if (isset($_SESSION['cedula'])) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'usuarios',
        'accion' => 'registrar usuario con la cédula/rif: ' . $this->cedulaUsuario,
        'resultado' => 'Éxito',
        'nuevo' => $datoNuevos,
      ]);
      if ($rb) return $rb;
    }

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Usuario registrado",
      "texto" => "El usuario ha sido registrado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarUsuariosP() {
    $datosActuales = $this->seleccionarUsuarios([
      "cedula_usuario" => $this->cedulaUsuario,
      'accion' => 'listar'
    ]);
    if ($this->contrasena1Usuario == "") $this->contrasena1Usuario = $datosActuales['contrasena_usuario'];
    if ($this->rolUsuario == '') $this->rolUsuario = $datosActuales['id_rol'];

    $datosAct = [
      "nombre_usuario" => $this->nombreUsuario,
      "apellido_usuario" => $this->apellidoUsuario,
      "telefono_usuario" => $this->telefonoUsuario,
      "id_rol" => $this->rolUsuario,
      "usuario_usuario" => $this->usuarioUsuario,
      "contrasena_usuario" => $this->contrasena1Usuario,
      "direccion_usuario" => $this->direccionUsuario,
      "correo_usuario" => $this->correoUsuario,
    ];
    $resultado = $this->actualizarDatos2([
      "datos" => $datosAct,
      "tabla" => "usuarios",
      'BD' => 'seguridad',
      "WHERE" => [
        "cedula_usuario" => $this->cedulaUsuario,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el usuario",
        "icono" => "warning",
      ];
    }

    $clienteObj = new clientesModelo();
    $cliente = $clienteObj->seleccionarClientes(['rif_cedula_cliente' => $this->cedulaUsuario]);
    if (isset($cliente['rif_cedula_cliente'])) {
      $clienteObj->actualizarClientes([
        "rif_cedula_cliente" => $this->cedulaUsuario,
        "razon_social_cliente" => $this->nombreUsuario . ' ' . $this->apellidoUsuario,
        "telefono_cliente" => $this->telefonoUsuario,
        "correo_cliente" => $this->correoUsuario,
        "direccion_cliente" => $this->direccionUsuario,
        "sinCommit" => true,
      ]);
    }

    if ($this->cedulaUsuario == $_SESSION['cedula']) {
      $_SESSION['nombre'] = $this->nombreUsuario;
      $_SESSION['apellido'] = $this->apellidoUsuario;
      $_SESSION['telefono'] = $this->telefonoUsuario;
      $_SESSION['usuario'] = $this->usuarioUsuario;
      $_SESSION['rol'] = $this->rolUsuario;
    }

    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Actualizar usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
      'viejo' => $datosActuales,
      'nuevo' => $datosAct
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Usuario actualizado",
      "texto" => "El usuario ha sido actualizado exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarUsuariosP() {
    $objBitacora = new bitacoraModelo();
    $error = function ($numeroFallo) use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'usuarios',
        'accion' => 'Eliminar usuario con la cedula/rif: ' . $this->cedulaUsuario,
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Usuario no eliminado",
        "texto" => "El usuario no pudo ser eliminado de la Base de Datos. Error: #000".$numeroFallo,
        "icono" => "error"
      ];
    };

    //Usuario
    $resultado = $this->eliminarDatos2([
      'tabla' => "usuarios",
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($resultado <= 0) return $error(1);

    //Preguntas
    $resultado = $this->eliminarDatos2([
      'tabla' => "preguntas_seguridad_usuarios",
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($resultado <= 0) return $error(2);

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Eliminar usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $error(3);

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])) return $error(4);

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Usuario eliminado",
      "texto" => "El usuario ha sido eliminado con éxito",
      "icono" => "success"
    ];
  }
  private function actualizarFotosUsuariosP() {
    $objBitacora = new bitacoraModelo();
    $usuariosActual = $this->seleccionarUsuarios([
      'cedula_usuario' => $this->cedulaUsuario,
      'accion' => 'listar'
    ]);

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Actualizar foto del usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
      'viejo' => ['fofo_usuario' => $usuariosActual['foto_usuario']],
      'nuevo' => ['foto_usuario' => $this->fotoUsuario],
    ]);
    if ($rb) return $rb;

    $resultado = $this->Imagenes_Act([
      'subCarpeta' => 'usuarios',
      'imagen' => $this->fotoUsuario,
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
    if ($resultado['icono'] != 'success') return $resultado;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $this->commit();
    return $resultado;
  }
  private function eliminarFotosUsuariosP() {
    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Eliminar foto del usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $rb;

    $resultado = $this->Imagenes_Eli([
      'subCarpeta' => 'usuarios',
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
    if ($resultado['icono'] != 'success') return $resultado;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $this->commit();
    return $resultado;
  }
  private function iniciarSesionUsuariosP() {
    $instruccionesConsultaCom = [
      'campos' => "
        us.cedula_usuario, us.nombre_usuario, us.apellido_usuario,
        ro.id_rol, ro.nombre_rol, us.usuario_usuario, us.contrasena_usuario,
        us.foto_usuario,us.ultimo_acceso_usuario, us.intentos_inicio_sesion_fallidos_usuario
      ",
      'tabla' =>  "usuarios as us",
      'BD' => 'seguridad',
      'datosJoins' => [
        'roles as ro' => 'us.id_rol = ro.id_rol'
      ],
      'WHERE' => [
        "us.usuario_usuario" => $this->usuarioUsuario,
      ],
    ];
    $datosUsAtuales = $this->seleccionarDatos2($instruccionesConsultaCom)->fetch();
    
    if($datosUsAtuales['intentos_inicio_sesion_fallidos_usuario'] >=3){
      return [
        'tipo'=>'simple',
        'icono'=>'error',
        'titulo'=>'Usuario Bloqueado',
        'texto'=>"
          Actualmente su usuario se encuentra bloqueado, por favor 
          desbloqueelo en la opción de [ ¿Olvidaste tu contraseña? ] 
          que se encuntre en la parte inferior
        "
      ];
    }
    

    if (!isset($datosUsAtuales['cedula_usuario'])) {
      return [
        "tipo" => "simple",
        "titulo" => "Usuario incorrecto",
        "texto" => "El usuario que ha introducido es incorrecto, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }
    if ($datosUsAtuales['intentos_inicio_sesion_fallidos_usuario'] >= 5) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Demasiados intentos',
        'texto' => 'Ha sobrepasado el limite de intentos permitidos para iniciar sesión',
        'icono' => 'error',
      ];
    }
    if (!password_verify($this->contrasena1Usuario, $datosUsAtuales['contrasena_usuario'])) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'usuarios',
        'BD' => 'seguridad',
        'datos' => [
          'intentos_inicio_sesion_fallidos_usuario' => ((int)$datosUsAtuales['intentos_inicio_sesion_fallidos_usuario'] + 1)
        ],
        'WHERE' => [
          'usuario_usuario' => $datosUsAtuales['usuario_usuario']
        ]
      ]);
      $this->commit();
      if ($resultado <= 0 || $resultado == false) {
        return [
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "No se pudo confirmar los intentos de inicio de sesión",
          "icono" => "error",
        ];
      }

      return [
        "tipo" => "simple",
        "titulo" => "Contraseña incorrecta",
        "texto" => "La contraseña que ha introducido es incorrecta, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }

    $ahora = new DateTime();
    $ahora->modify('+5 minutes'); //Al ahora le sumamos 5 minutos de validez de la sesion
    $ahoraBD = $ahora->format('Y-m-d H:i:s');
    $resultado = $this->actualizarDatos2([
      'tabla' => 'usuarios',
      'BD' => 'seguridad',
      'datos' => [
        'ultimo_acceso_usuario' => $ahoraBD,
        'intentos_inicio_sesion_fallidos_usuario' => 0,
      ],
      'WHERE' => [
        'usuario_usuario' => $datosUsAtuales['usuario_usuario']
      ],
    ]);
    if ($resultado == false) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al iniciar sesión',
        'texto' => 'No se pudo iniciar sesión',
        'icono' => 'error',
      ];
    }

    $_SESSION['cedula'] = $datosUsAtuales['cedula_usuario'];
    $_SESSION['nombre'] = $datosUsAtuales['nombre_usuario'];
    $_SESSION['apellido'] = $datosUsAtuales['apellido_usuario'];
    $_SESSION['usuario'] = $datosUsAtuales['usuario_usuario'];
    $_SESSION['rol'] = $datosUsAtuales['id_rol'];
    $_SESSION['nombreRol'] = $datosUsAtuales['nombre_rol'];
    $_SESSION['foto'] = $datosUsAtuales['foto_usuario'];
    $_SESSION['TOKEN_CSRF'] = bin2hex(random_bytes(32));
    $_SESSION['ultimo_inicio_sesion'] = $ahoraBD;

    $datosUsuarioDespues = $this->seleccionarDatos2($instruccionesConsultaCom)->fetch();
    $objBitacora = new bitacoraModelo();

    unset($datosUsAtuales['contrasena_usuario']);
    unset($datosUsuarioDespues['contrasena_usuario']);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Iniciar sesión',
      'resultado' => 'Éxito',
      'viejo' => $datosUsAtuales,
      'nuevo' => $datosUsuarioDespues
    ]);
    if ($rb) return $rb;
    $this->commit();
    return [
      "tipo" => "redireccionar",
      "url" => $this->redireccionarUsuario(true)
    ];
  }
  private function cerrarSesionUsuariosP() {
    session_destroy();
    return [
      "tipo" => "redireccionar",
      "url" => APP_URL . "usuarios/login",
      'icono' => 'error',
    ];
  }
  private function validarTipoMetodoRecContrasenaUsuariosP(array $info) {
    switch ($info['tipo_metodo'] ?? '') {
      case 3:
        foreach ($this->preguntasSeguridadUsuario as $pregunta) {
          $respuestaBD = $this->seleccionarDatos2([
            'BD' => 'seguridad',
            'tabla' => 'preguntas_seguridad_usuarios',
            'campos' => 'respuesta_pregunta',
            'WHERE' => [
              'id_pregunta' => $pregunta['id_pregunta']
            ]
          ])->fetch(PDO::FETCH_COLUMN);

          if (!password_verify($pregunta['respuesta'], $respuestaBD)) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Respuesta erroneas',
              'texto' => 'Las respuestas a las preguntas de seguridad no coinciden',
              'icono' => 'error',
            ];
          }
        }

        $codigo = $_SESSION['hashRecContrasena'] = base64_encode(random_bytes(16));

        $tokenActual = $this->seleccionarDatos2([
          'BD' => 'seguridad',
          'tabla' => 'tokens_usuarios',
          'campos' => 'id_token_usuario,token,vencimiento_token',
          'WHERE' => [
            'cedula_usuario' => $this->cedulaUsuario,
            'tipo_token' => 1,
          ]
        ])->fetch();

        $ahora = new DateTime();
        $vencimiento = new DateTime($tokenActual['vencimiento_token'] ?? null);
        $diferenciaMin = ($vencimiento->getTimestamp() - $ahora->getTimestamp()) / 60;

        if (empty($tokenActual) || $diferenciaMin <= 0) {
          if ($tokenActual != []) {
            $resultado = $this->eliminarDatos2([
              'fisico' => true,
              'BD' => 'seguridad',
              'tabla' => 'tokens_usuarios',
              'WHERE' => [
                'id_token_usuario' => $tokenActual['id_token_usuario'],
              ]
            ]);
            if ($resultado == false || $resultado <= 0) {
              return [
                'tipo' => 'simple',
                'titulo' => 'Token antiguo no eliminado',
                'texto' => 'El antiguo token no pudo ser eliminado correctamente',
                'icono' => 'error',
              ];
            }
          }
          $ahora->modify('+15 minutes'); //Al ahora le sumamos 15 minutos
          $idTokenUsuario = $this->generarCodSeg([
            'BD' => 'seguridad',
            'tablaBD' => 'tokens_usuarios',
            'prefijo' => 'TOKU',
            'campoID' => 'id_token_usuario',
          ]);
          $resultado = $this->guardarDatos2([
            'BD' => 'seguridad',
            'tabla' => 'tokens_usuarios',
            'datos' => [
              'id_token_usuario' => $idTokenUsuario,
              'cedula_usuario' => $this->cedulaUsuario,
              'tipo_token' => 1, //1 es para recuperar clave
              'token' => $codigo,
              'vencimiento_token' => $ahora->format('Y-m-d H:i:s')
            ],
            'WHERE' => [
              'cedula_usuario' => $this->cedulaUsuario
            ]
          ]);
          if ($resultado == false || $resultado <= 0) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Token no generado',
              'texto' => 'El token no pudo ser generado correctamente',
              'icono' => 'error',
            ];
          }
        } else {
          $codigo = $tokenActual['token'];
        }
        $this->commit();
        return [
          'tipo' => 'simple',
          'titulo' => 'Validado',
          'texto' => 'Confirmado',
          'icono' => 'success',
          'codigoRestauracion' => $codigo
        ];

      case 2:
      case 1:

        $codigoRecContrasena = $_SESSION['hashRecContrasena'] = base64_encode(random_bytes(16));
        $tokenActual = $this->seleccionarDatos2([
          'BD' => 'seguridad',
          'tabla' => 'tokens_usuarios',
          'campos' => 'id_token_usuario,token,vencimiento_token',
          'WHERE' => [
            'cedula_usuario' => $this->cedulaUsuario,
            'tipo_token' => 2,
          ]
        ])->fetch();

        $ahora = new DateTime();
        $vencimiento = new DateTime($tokenActual['vencimiento_token'] ?? null);
        $diferenciaMin = ($vencimiento->getTimestamp() - $ahora->getTimestamp()) / 60;

        if (empty($tokenActual) || $diferenciaMin <= 0) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Token vencido',
            'texto' => 'El código suministrado ha expirado, por favor solicita uno nuevo',
            'icono' => 'error',
          ];
        }

        if ($this->codigoRecContrasenaUsuario != $tokenActual['token']) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Código erroneo',
            'texto' => 'El código que ha introducido es incorrecto',
            'icono' => 'error',
          ];
        }

        //Se borra porque ya se usó
        $resultado = $this->eliminarDatos2([
          'BD' => 'seguridad',
          'tabla' => 'tokens_usuarios',
          'WHERE' => [
            'id_token_usuario' => $tokenActual['id_token_usuario'],
          ]
        ]);
        if ($resultado == false || $resultado <= 0) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Token no eliminado',
            'texto' => 'El token a usar no pudo ser eliminado',
            'icono' => 'error',
          ];
        }

        //Creamos el token de recuperacion de contraseña
        $ahora->modify('+15 minutes');
        $idTokenUsuario = $this->generarCodSeg([
          'BD' => 'seguridad',
          'tablaBD' => 'tokens_usuarios',
          'prefijo' => 'TOKU',
          'campoID' => 'id_token_usuario',
        ]);
        $resultado = $this->guardarDatos2([
          'BD' => 'seguridad',
          'tabla' => 'tokens_usuarios',
          'datos' => [
            'id_token_usuario' => $idTokenUsuario,
            'cedula_usuario' => $this->cedulaUsuario,
            'tipo_token' => 1, //1 es para recuperar clave
            'token' => $codigoRecContrasena,
            'vencimiento_token' => $ahora->format('Y-m-d H:i:s')
          ],
          'WHERE' => [
            'cedula_usuario' => $this->cedulaUsuario
          ]
        ]);
        if ($resultado == false || $resultado <= 0) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Token no generado',
            'texto' => 'El token no pudo ser generado correctamente',
            'icono' => 'error',
          ];
        }

        $this->commit();
        return [
          'tipo' => 'simple',
          'titulo' => 'Validado',
          'texto' => 'Confirmado',
          'icono' => 'success',
          'codigoRestauracion' => $codigoRecContrasena
        ];
      default:
        return [
          'tipo' => 'simple',
          'titulo' => 'Sin método de verificación',
          'texto' => 'No has seleccionado ningún método de verificación',
          'icono' => 'error',
        ];
        break;
    }
  }
  private function solicitarCodigoRecContrasenaP(array $info) {
    switch ($info['tipo_metodo'] ?? '') {
      case '1': //mensaje normal

        break;
      case '2': //mensaje por correo
        $datosUsuario = $this->seleccionarDatos2([
          'BD' => 'seguridad',
          'campos' => 'nombre_usuario, apellido_usuario, correo_usuario',
          'tabla' => 'usuarios',
          'WHERE' => [
            'cedula_usuario' => $this->cedulaUsuario
          ]
        ])->fetch();
        if ($datosUsuario == []) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Sin correo de destino',
            'texto' => 'No posee un correo para el envío del código para la recuperación del acceso',
            'icono' => 'error'
          ];
        }

        $tokenDeEnvio = '';
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nroTotalCaracteres = strlen($caracteres);
        for ($i = 0; $i < 6; $i++) {
          $tokenDeEnvio .= $caracteres[rand(0, $nroTotalCaracteres - 1)];
        }

        $tokenActual = $this->seleccionarDatos2([
          'BD' => 'seguridad',
          'tabla' => 'tokens_usuarios',
          'campos' => 'id_token_usuario,token,vencimiento_token',
          'WHERE' => [
            'cedula_usuario' => $this->cedulaUsuario,
            'tipo_token' => 2, //Token que se envia
          ]
        ])->fetch();

        $ahora = new DateTime();
        $vencimiento = new DateTime($tokenActual['vencimiento_token'] ?? null);
        $diferenciaMin = ($vencimiento->getTimestamp() - $ahora->getTimestamp()) / 60;

        if (empty($tokenActual) || $diferenciaMin <= 0) {
          if ($tokenActual != []) {
            $resultado = $this->eliminarDatos2([
              'fisico' => true,
              'BD' => 'seguridad',
              'tabla' => 'tokens_usuarios',
              'WHERE' => [
                'id_token_usuario' => $tokenActual['id_token_usuario'],
              ]
            ]);
            if ($resultado == false || $resultado <= 0) {
              return [
                'tipo' => 'simple',
                'titulo' => 'Token antiguo no eliminado',
                'texto' => 'El antiguo token no pudo ser eliminado correctamente',
                'icono' => 'error',
              ];
            }
          }
          $ahora->modify('+5 minutes'); //Al ahora le sumamos 5 minutos
          $idTokenUsuario = $this->generarCodSeg([
            'BD' => 'seguridad',
            'tablaBD' => 'tokens_usuarios',
            'prefijo' => 'TOKU',
            'campoID' => 'id_token_usuario',
          ]);
          $resultado = $this->guardarDatos2([
            'BD' => 'seguridad',
            'tabla' => 'tokens_usuarios',
            'datos' => [
              'id_token_usuario' => $idTokenUsuario,
              'cedula_usuario' => $this->cedulaUsuario,
              'tipo_token' => 2, //2 es para el que se envia al usuario
              'token' => $tokenDeEnvio,
              'vencimiento_token' => $ahora->format('Y-m-d H:i:s')
            ],
            'WHERE' => [
              'cedula_usuario' => $this->cedulaUsuario
            ]
          ]);
          if ($resultado == false || $resultado <= 0) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Token no generado',
              'texto' => 'El token no pudo ser generado correctamente',
              'icono' => 'error',
            ];
          }
        } else {
          $tokenDeEnvio = $tokenActual['token'];
        }

        $objCorreo = new correosModelo();
        $resultado = $objCorreo->enviarCorreos([
          'asunto_correo' => 'Correo de recuperación',
          'cuerpo_correo' => "
            Hola, <b>" . $datosUsuario['nombre_usuario'] . "</b> 
            nos da mucho gusto saludarte.  Tu código para la recuperación de tu 
            clave de inicio de sesión al sistema JLACRUZ es: " . $tokenDeEnvio . ". 
            No compartas este código con nadie.
          ",
          'destinatarios_correo' => [
            'nombre' => $datosUsuario['nombre_usuario'] . ' ' . $datosUsuario['apellido_usuario'],
            'correo' => $datosUsuario['correo_usuario']
          ],
          'esHTML' => true,
        ]);
        if (($resultado['icono'] ?? '') == 'error') return $resultado;
        $correoRecortado = substr($datosUsuario['correo_usuario'], 0, 4) . "******" . strstr($datosUsuario['correo_usuario'], '@');
        $this->commit();
        return [
          'tipo' => 'simple',
          'titulo' => 'Codigo enviado',
          'texto' => 'Si introdujo el correo y/o teléfono correctamente recibirá el código y deberá introducirlo a continuación',
          'icono' => 'success',
          'correo' => $correoRecortado,
        ];
    }
  }
  private function restablecerContrasenaUsuarioP($info) {
    $tokenActual = $this->seleccionarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'tokens_usuarios',
      'campos' => 'token, vencimiento_token',
      'WHERE' => [
        'tipo_token' => 1, //Para recuperar la contraseña
        'cedula_usuario' => $this->cedulaUsuario,
      ]
    ])->fetch();
    if (empty($tokenActual)) {
      return [
        'tipo' => 'simple',
        'titulo' => "Token expirado",
        'texto' => 'El token de seguridad ha expirado',
        'icono' => 'error'
      ];
    }

    $datetime = new Datetime($tokenActual['vencimiento_token']);
    $segundosVencimientoToken = $datetime->getTimestamp();
    $fechaActual = new Datetime();
    $segundosFechaActual = $fechaActual->getTimestamp();

    if (($segundosVencimientoToken - $segundosFechaActual) <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Token Vencido',
        'texto' => 'Tu token ha caducado, por favor, reanuda el proceso desde el principio',
        'icono' => 'error',
      ];
    }
    if ($this->codigoRecContrasenaUsuario == $tokenActual['token']) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Token erróneo',
        'texto' => 'El token que ha enviado no es válido',
        'icono' => 'error',
      ];
    }

    $resultado = $this->actualizarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'usuarios',
      'datos' => [
        'contrasena_usuario' => password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]),
        'intentos_inicio_sesion_fallidos_usuario' => 0,
      ],
      'WHERE' => [
        'cedula_usuario' => $this->cedulaUsuario
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Contraseña no actualizada',
        'texto' => 'La contraseña no ha sido actualizada',
        'icono' => 'error',
      ];
    }
    $resultado = $this->eliminarDatos2([
      'fisico' => true,
      'BD' => 'seguridad',
      'tabla' => 'tokens_usuarios',
      'WHERE' => [
        'tipo_token' => [
          '=' => [1, 2]
        ],
        'cedula_usuario' => $this->cedulaUsuario,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Token no eliminado',
        'texto' => 'El token no ha podido ser eliminado',
        'icono' => 'error',
      ];
    }

    $this->commit();
    return [
      'tipo' => 'simple',
      'titulo' => 'Contraseña actualizada',
      'texto' => 'La contraseña ha sido actualizada correctamente',
      'icono' => 'success'
    ];
  }
  private function programarCierreSesionUsuarioP() {
    $laSesionEstaActiva = $this->validarVigenciaSesionUsuario();
    if ($laSesionEstaActiva['icono'] != 'success') return $this->cerrarSesionUsuarios();

    $ahora = new DateTime();
    $ahora->modify('+5 minutes'); //Al ahora le sumamos 5 minutos
    $resultado = $this->actualizarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'usuarios',
      'datos' => [
        'ultimo_acceso_usuario' => $ahora->format('Y-m-d H:i:s')
      ],
      'WHERE' => [
        'cedula_usuario' => $this->cedulaUsuario
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Última hora de actividad no actualizada',
        'texto' => 'La última hora de actividad no fue actualizada correctamente',
        'icono' => 'error',
      ];
    }
    $this->commit();
    return [
      'tipo' => 'simple',
      'titulo' => 'Última hora de actividad actualizada',
      'texto' => 'La última hora de actividad fue actualizada correctamente',
      'icono' => 'success',
    ];
  }
  private function validarVigenciaSesionUsuarioP() {
    $fechaExpiracionSesion = $this->seleccionarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'usuarios',
      'campos' => 'ultimo_acceso_usuario',
      'WHERE' => [
        'cedula_usuario' => $this->cedulaUsuario
      ]
    ])->fetch(PDO::FETCH_COLUMN);

    $ahora = new DateTime();
    $expiracion = new DateTime($fechaExpiracionSesion);
    $diferenciaMin = ($expiracion->getTimestamp() - $ahora->getTimestamp()) / 60;

    if ($diferenciaMin <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => "Sesión expirada",
        'texto' => 'El tiempo de inicio de sesión ha expirado, por favor vuelva a entrar con sus credenciales',
        'icono' => 'error',
      ];
    }
    return [
      'tipo' => 'simple',
      'titulo' => "Sesión activa",
      'texto' => 'La sesión se encuentra vigente',
      'icono' => 'success',
    ];
  }
}
