<?php

namespace src\modelos;

use src\modelos\accesosModelo;
use src\modelos\bitacoraModelo;
use src\modelos\traitModelo;
use src\config\connect\conexion;
use src\modelos\mensajesWSModelo;
use PDO;

class preguntasSeguridadModelo extends conexion {
  use traitModelo;

  private string $idPregunta = '';
  private string $textoPregunta = '';

  public function validarPreguntasSeguridad(string|null $permiso, ?array &$info = null, ?array $requerido = null) {

    if ($permiso != null) {
      $objAcceso = new accesosModelo();
      $v = $objAcceso->validarPermisos('preguntas-seguridad', $permiso);
      if ($v) return $v;
    }

    if ($info === null) return false;
    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_pregunta' => [
          ...molIdSeguro,
          "nombreAlerta" => "id de la pregunta de seguridad",
          "nombreBD" => "id_pregunta",
          "tablaBD" => "preguntas_seguridad",
          'BD' => 'seguridad',
          "debeExistirBD" => true
        ],
        'texto_pregunta' => [
          ...molDescripcion,
          "nombreAlerta" => "texto de la pregunta",
          "nombreBD" => "texto_pregunta",
          "tablaBD" => "preguntas_seguridad",
          'BD' => 'seguridad',
          "debeSerUnicoBD" => true
        ],
      ],
      'requerido' => $requerido ?? []
    ];
    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }
  public function seleccionarPreguntasSeguridad(array $info) {
    $requerido = [];
    if (($info['id_pregunta'] ?? '') != "") $requerido[] = 'id_pregunta';
    $r = $this->validarPreguntasSeguridad(null, $info, $requerido);
    if (($info['id_pregunta'] ?? '') != "") $this->idPregunta = $info['id_pregunta'];
    if ($r) return $r;
    return $this->seleccionarPreguntasSeguridadP($info);
  }
  public function registrarPreguntasSeguridad(array $info) {
    $resultado = $this->validarPreguntasSeguridad('registrar', $info, ['texto_pregunta']);
    if ($resultado) return $resultado;

    [
      'texto_pregunta' => $this->textoPregunta
    ] = $info;

    return $this->registrarPreguntasSeguridadP();
  }
  public function actualizarPreguntasSeguridad(array $info) {
    $resultado = $this->validarPreguntasSeguridad(
      'actualizar',
      $info,
      ['id_pregunta', 'texto_pregunta']
    );
    if ($resultado) return $resultado;

    [
      'id_pregunta' => $this->idPregunta,
      'texto_pregunta' => $this->textoPregunta
    ] = $info;

    return $this->actualizarPreguntasSeguridadP();
  }
  public function eliminarPreguntasSeguridad(array $info) {
    $resultado = $this->validarPreguntasSeguridad('eliminar', $info, ['id_pregunta']);
    if ($resultado) return $resultado;

    $this->idPregunta = $info['id_pregunta'];
    return $this->eliminarPreguntasSeguridadP();
  }

  private function seleccionarPreguntasSeguridadP(array $info) {
    if ($this->idPregunta != '') {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'preguntas_seguridad',
        'BD' => 'seguridad',
        'WHERE' => [
          'id_pregunta' => $this->idPregunta
        ]
      ])->fetch();
    } else {
      switch (($info['tipoConsulta'] ?? '')) {
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'preguntas_seguridad',
            'BD' => 'seguridad',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'BD' => 'seguridad',
            'tabla' => 'preguntas_seguridad',
            'campos' => '*',
          ])->fetchAll();
      }
    }
  }
  private function registrarPreguntasSeguridadP() {
    $idPregunta = $this->generarCodSeg([
      'BD' => 'seguridad',
      'tablaBD' => 'preguntas_seguridad',
      'prefijo' => 'PREG',
      'campoID' => 'id_pregunta'
    ]);

    $ultimoId = $this->guardarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'preguntas_seguridad',
      'datos' =>  [
        'id_pregunta' => $idPregunta,
        "texto_pregunta" => $this->textoPregunta
      ]
    ]);
    $objBitacora = new bitacoraModelo();

    if ($ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'commit' => true,
        'modulo' => 'preguntas-seguridad',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'texto_pregunta' => $this->textoPregunta
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar",
        "icono" => "error"
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'bancos',
      'accion' => 'registrar',
      'resultado' => 'Éxito',
      'viejo' => [],
      'nuevo' => [
        'id_pregunta' => $ultimoId,
        'texto_pregunta' => $this->textoPregunta
      ]
    ]);

    $objetoNot = new mensajesWSModelo();
    $resultado= $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'preguntas-seguridad'],
        ['accion' => "actDT", 'modulo' => 'preguntas-seguridad'],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])){
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
      "titulo" => "Registro Exitoso",
      "texto" => "La pregunta de seguridad ha sido registrada",
      "icono" => "success"
    ];
  }
  private function actualizarPreguntasSeguridadP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'preguntas_seguridad',
      'BD' => 'seguridad',
      'WHERE' => ['id_pregunta' => $this->idPregunta]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->actualizarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'preguntas_seguridad',
      'datos' => [
        "texto_pregunta" => $this->textoPregunta
      ],
      'WHERE' => [
        "id_pregunta" => $this->idPregunta
      ]
    ]);

    if ($resultado <= 0 || $resultado == false) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'commit' => true,
        'modulo' => 'preguntas-seguridad',
        'accion' => 'actualizar',
        'resultado' => 'Fállido',
        'viejo' => $viejo,
        'nuevo' => [
          'id_pregunta' => $this->idPregunta,
          'texto_pregunta' => $this->textoPregunta
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No hubo cambios",
        "icono" => "info"
      ];
    }
    $objBitacora->registrarBitacora([
      'modulo' => 'preguntas-seguridad',
      'accion' => 'actualizar',
      'resultado' => 'Éxito',
      'viejo' => $viejo,
      'nuevo' => [
        'id_pregunta' => $this->idPregunta,
        'texto_pregunta' => $this->textoPregunta
      ]
    ]);

    $objetoNot = new mensajesWSModelo();
    $resultado= $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'preguntas-seguridad'],
        ['accion' => "actDT", 'modulo' => 'preguntas-seguridad'],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])){
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
      "titulo" => "Actualización Exitosa",
      "texto" => "El banco ha sido actualizado",
      "icono" => "success"
    ];
  }
  private function eliminarPreguntasSeguridadP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'preguntas_seguridad',
      'BD' => 'seguridad',
      'WHERE' => ['id_pregunta' => $this->idPregunta]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->eliminarDatos2([
      'BD' => 'seguridad',
      'tabla' => 'preguntas_seguridad',
      'WHERE' => [
        "id_pregunta" => $this->idPregunta
      ]
    ]);

    if ($resultado <= 0 || $resultado == false) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'commit' => true,
        'modulo' => 'preguntas-seguridad',
        'accion' => 'eliminar',
        'resultado' => 'Fállido',
        'viejo' => $viejo,
        'nuevo' => [],
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Eliminación Fallida",
        "texto" => "El banco no pudo ser eliminado",
        "icono" => "error"
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => '$this->rollback();',
      'accion' => 'eliminar',
      'resultado' => 'Éxito',
      'viejo' => $viejo,
      'nuevo' => []
    ]);

    $objetoNot = new mensajesWSModelo();
    $resultado= $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'preguntas-seguridad'],
        ['accion' => "actDT", 'modulo' => 'preguntas-seguridad'],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])){
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
      "titulo" => "Pregunta eliminada",
      "texto" => "La pregunta ha sido eliminada exitosamente, aunque aún podrá ser usada por aquellos usuarios que ya la habían elegido con anterioridad",
      "icono" => "success"
    ];
  }
}
