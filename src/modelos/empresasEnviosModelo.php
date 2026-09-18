<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use src\modelos\bitacoraModelo;

class empresasEnviosModelo extends conexion {
  private string $idEmpresaEnvios = '';
  private string $nombreEmpresaEnvios = '';

  public function validarEmpresasEnvios(string $permiso, array &$info = [], array $requerido = []) {

    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('empresasEnvios', $permiso);
    if ($v) return $v;

    $v = $this->limpiarValidar($info, [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_empresa_envios' => [
          ...molId,
          "nombreAlerta" => "id de la empresa de envíos",
          "nombreBD" => "id_empresa_envios",
          "tablaBD" => "empresas_envios",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
        ],
        'nombre_empresa' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre de la empresa",
          "nombreBD" => "nombre_empresa",
          "tablaBD" => "empresas_envios",
          "debeSerUnicoBD" => true,
        ],
      ],
      'requerido' => $requerido,
    ]);
    if ($v) return $v;

    return false;
  }
  public function seleccionarEmpresasEnvios(array $info) {
    if (($info['id_empresa_envios'] ?? '') != '') {
      $v = $this->validarEmpresasEnvios('listar', $info, ['id_empresa_envios']);
      if ($v) return $v;
      $this->idEmpresaEnvios = $info['id_empresa_envios'];
    }
    return $this->seleccionarEmpresasEnviosP();
  }
  public function registrarEmpresasEnvios(array $info) {
    $v = $this->validarEmpresasEnvios('registrar', $info, ['nombre_empresa']);
    if ($v) return $v;
    $this->nombreEmpresaEnvios = $info['nombre_empresa'];
    return $this->registrarEmpresasEnviosP();
  }
  public function actualizarEmpresasEnvios(array $info) {
    $v = $this->validarEmpresasEnvios('actualizar', $info, ['id_empresa_envios', 'nombre_empresa']);
    if ($v) return $v;
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreEmpresaEnvios = $info['nombre_empresa'];
    return $this->actualizarEmpresasEnviosP();
  }
  public function eliminarEmpresasEnvios(array $info) {
    $v = $this->validarEmpresasEnvios('eliminar', $info, ['id_empresa_envios']);
    if ($v) return $v;
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    return $this->eliminarEmpresasEnviosP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarEmpresasEnviosP() {
    if ($this->idEmpresaEnvios == null || $this->idEmpresaEnvios == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_empresas_envios_todas',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'empresas_envios',
        'WHERE' => [
          "id_empresa_envios" => $this->idEmpresaEnvios,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Empresa de envíos no encontrada",
          "texto" => "La empresa de envíos que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      return $resultado->fetch(PDO::FETCH_ASSOC);
    }
  }
  private function registrarEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'empresas_envios',
      'datos' => [
        "nombre_empresa" => $this->nombreEmpresaEnvios,
      ]
    ]);
    if ($ultimoId == false || $ultimoId <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'empresasEnvios',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Empresa de envíos no registrada",
        "texto" => "La empresa de envíos no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'empresasEnvios',
      'accion' => 'Registrar',
      'resultado' => 'Éxito',
      'nuevo' => [
        "nombre_empresa" => $this->nombreEmpresaEnvios,
      ]
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'empresasEnvios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'empresasEnvios'
        ],
      ],
      'noCommit' => true
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
      "titulo" => "Empresa registrada",
      "texto" => "La empresa ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $datosViejos = $this->seleccionarEmpresasEnvios(['id_empresa_envios' => $this->idEmpresaEnvios]);
    $resultado = $this->actualizarDatos2([
      'tabla' => 'empresas_envios',
      'datos' => [
        "nombre_empresa" => $this->nombreEmpresaEnvios,
      ],
      "WHERE" => [
        "id_empresa_envios" => $this->idEmpresaEnvios,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'empresasEnvios',
        'accion' => 'actualizar Empresa de envíos con id: ' . $this->idEmpresaEnvios,
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio",
        "icono" => "warning",
      ];
    }

    $datosNuevos = $this->seleccionarEmpresasEnvios(['id_empresa_envios' => $this->idEmpresaEnvios]);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'empresasEnvios',
      'accion' => 'actualizar Empresa de envíos con id: ' . $this->idEmpresaEnvios,
      'resultado' => 'Éxito',
      'viejo' => $datosViejos,
      'nuevo' => $datosNuevos
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'empresasEnvios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'empresasEnvios'
        ],
      ],
      'noCommit' => true
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
      "titulo" => "Empresa de envíos actualizada",
      "texto" => "La empresa de envíos ha sido actualizada exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "empresas_envios",
      "WHERE" => [
        "id_empresa_envios" => $this->idEmpresaEnvios
      ]
    ]);
    if ($eliminarUsuario <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'empresasEnvios',
        'accion' => 'Eliminar Empresa de envíos con id: ' . $this->idEmpresaEnvios,
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Empresa de envíos no encontrada",
        "texto" => "La empresa de envíos no existe en la Base de Datos",
        "icono" => "error"
      ];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'empresasEnvios',
      'accion' => 'Eliminar Empresa de envíos con id: ' . $this->idEmpresaEnvios,
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'empresasEnvios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'empresasEnvios'
        ],
      ],
      'noCommit' => true
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
      "tipo" => "simple",
      "titulo" => "Empresa eliminada",
      "texto" => "La empresa ha sido eliminada con éxito",
      "icono" => "success"
    ];
  }
}
