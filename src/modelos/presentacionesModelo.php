<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;
use PDO;

class presentacionesModelo extends conexion {
  private string $idPresentacion = '';
  private string  $idUnidadMedida = '';
  private string  $nombrePresentacion = '';
  private float $cantidadPMP = 0;

  // PÚBLICOS  
  public function validarPresentaciones(string $permiso, array $instruccionesVal) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('presentaciones', $permiso);
    if ($v) return $v;

    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "id de la presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
        'id_unidad_medida' => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "id de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        'nombre_presentacion' => [
          "campo_nombre" => "nombre_presentacion",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "nombre de la presentación",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "presentaciones",
          "debeSerUnico" => true,
        ],
        'cantidad_pmp' => [
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "cantidad del producto o materia prima",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarPresentaciones(array $info) {
    if (($info['id_presentacion'] ?? '') != '') {
      $resultado = $this->validarPresentaciones('listar', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_presentacion',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idPresentacion = $info['id_presentacion'];
    }
    return $this->seleccionarPresentacionesP($info);
  }
  public function registrarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones('registrar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_presentacion',
        'cantidad_pmp',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombrePresentacion = $info['nombre_presentacion'];
    $this->cantidadPMP = $info['cantidad_pmp'];

    return $this->registrarPresentacionesP();
  }
  public function actualizarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones('actualizar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion',
        'id_unidad_medida',
        'nombre_presentacion',
        'cantidad_pmp',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idPresentacion = $info['id_presentacion'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombrePresentacion = $info['nombre_presentacion'];
    $this->cantidadPMP = $info['cantidad_pmp'];

    return $this->actualizarPresentacionesP();
  }
  public function eliminarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones('eliminar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idPresentacion = $info['id_presentacion'];
    return $this->eliminarPresentacionesP();
  }

  // PRIVADOS 
  private function seleccionarPresentacionesP(array $info) {
    if ($this->idPresentacion == null || $this->idPresentacion == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '
              pr.id_presentacion, pr.nombre_presentacion, pr.cantidad_pmp, 
              um.nombre_unidad_medida
            ',
            'tabla' => 'presentaciones as pr',
            'datosJoins' => [
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida',
            ]
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '
              pr.id_presentacion, pr.nombre_presentacion, pr.cantidad_pmp, 
              um.id_unidad_medida,um.nombre_unidad_medida
            ',
            'tabla' => 'presentaciones as pr',
            'datosJoins' => [
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida',
            ]
          ])->fetchAll();
      }
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'presentaciones',
        'WHERE' => [
          "id_presentacion" => $this->idPresentacion,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Presentacion no encontrada",
          "texto" => "La presentación que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      return $resultado->fetch();
    }
  }
  private function registrarPresentacionesP() {
    $objBitacora = new bitacoraModelo();
    $objWS = new mensajesWSModelo();

    try {
      $idGen = $this->generarCodSeg([
        'tablaBD' => 'presentaciones',
        'prefijo' => 'PRES',
        'campoID' => 'id_presentacion'
      ]);

      $ultimoId = $this->guardarDatos2([
        'tabla' => 'presentaciones',
        'datos' => [
          "id_presentacion"      => $idGen,
          "id_unidad_medida"     => $this->idUnidadMedida,
          "nombre_presentacion"  => $this->nombrePresentacion,
          "cantidad_pmp"         => $this->cantidadPMP,
        ],
      ]);

      // Validamos si no se pudo registrar la presentación
      if ($ultimoId === false) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'presentaciones',
          'accion'    => 'registrar',
          'resultado' => 'Fallido',
          'viejo'     => [],
          'nuevo'     => [
            "id_unidad_medida"    => $this->idUnidadMedida,
            "nombre_presentacion" => $this->nombrePresentacion,
            "cantidad_pmp"        => $this->cantidadPMP,
          ]
        ]);

        $this->rollback();

        return [
          "tipo"   => "simple",
          "titulo" => "Error al registrar",
          "texto"  => "No se ha podido registrar la presentación",
          "icono"  => "error",
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'registrar',
        'resultado' => 'Éxito',
        'viejo'     => [],
        'nuevo'     => [
          "id_presentacion"      => $idGen,
          "id_unidad_medida"     => $this->idUnidadMedida,
          "nombre_presentacion"  => $this->nombrePresentacion,
          "cantidad_pmp"         => $this->cantidadPMP,
        ]
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          ['accion' => 'borrarDataModuloSS', 'modulo' => 'presentaciones'],
          ['accion' => 'actDT', 'modulo' => 'presentaciones'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Presentación registrada',
              'texto'    => "Se ha registrado la presentación {$this->nombrePresentacion}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ]
          ],
        ],
      ]);

      return [
        "tipo"   => "limpiarYcerrar",
        "titulo" => "Presentación registrada",
        "texto"  => "La presentación ha sido registrada exitosamente",
        "icono"  => "success",
      ];
    } catch (\Throwable) {

      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'registrar',
        'resultado' => 'Fallido',
        'viejo'     => [],
        'nuevo'     => [
          "id_unidad_medida"    => $this->idUnidadMedida,
          "nombre_presentacion" => $this->nombrePresentacion,
          "cantidad_pmp"        => $this->cantidadPMP,
        ]
      ]);

      return [
        "tipo"   => "simple",
        "titulo" => "Error al registrar",
        "texto"  => "No se ha podido registrar la presentación",
        "icono"  => "error",
      ];
    }
  }
  private function actualizarPresentacionesP() {
    $objBitacora = new bitacoraModelo();
    $objWS = new mensajesWSModelo();

    try {
      $estadoViejo = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla'  => 'presentaciones',
        'WHERE'  => [
          "id_presentacion" => $this->idPresentacion,
        ]
      ])->fetch();

      // Validamos si no existe la presentación
      if (!$estadoViejo) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'presentaciones',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => [],
          'nuevo'     => [
            "id_unidad_medida"    => $this->idUnidadMedida,
            "nombre_presentacion" => $this->nombrePresentacion,
            "cantidad_pmp"        => $this->cantidadPMP,
          ]
        ]);

        $this->rollback();

        return [
          "tipo"   => "simple",
          "titulo" => "Error al actualizar",
          "texto"  => "No se ha podido actualizar la presentación",
          "icono"  => "error",
        ];
      }

      $resultado = $this->actualizarDatos2([
        "tabla" => "presentaciones",
        "datos" => [
          "id_unidad_medida"    => $this->idUnidadMedida,
          "nombre_presentacion" => $this->nombrePresentacion,
          "cantidad_pmp"        => $this->cantidadPMP,
        ],
        "WHERE" => [
          "id_presentacion" => $this->idPresentacion,
        ]
      ]);

      // Validamos si no se realizó ningún cambio
      if ($resultado === false || $resultado <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'presentaciones',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => (array)$estadoViejo,
          'nuevo'     => [
            "id_unidad_medida"    => $this->idUnidadMedida,
            "nombre_presentacion" => $this->nombrePresentacion,
            "cantidad_pmp"        => $this->cantidadPMP,
          ]
        ]);

        $this->rollback();

        return [
          "tipo"   => "simple",
          "titulo" => "Error al actualizar",
          "texto"  => "No se ha podido actualizar la presentación",
          "icono"  => "error",
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'actualizar',
        'resultado' => 'Éxito',
        'viejo'     => (array)$estadoViejo,
        'nuevo'     => [
          "id_unidad_medida"    => $this->idUnidadMedida,
          "nombre_presentacion" => $this->nombrePresentacion,
          "cantidad_pmp"        => $this->cantidadPMP,
        ]
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          ['accion' => 'borrarDataModuloSS', 'modulo' => 'presentaciones'],
          ['accion' => 'actDT', 'modulo' => 'presentaciones'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Presentación actualizada',
              'texto'    => "Se ha actualizado la presentación {$this->nombrePresentacion}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ]
          ],
        ],
      ]);

      return [
        "tipo"   => "limpiarYcerrar",
        "titulo" => "Presentación actualizada",
        "texto"  => "La presentación ha sido actualizada exitosamente",
        "icono"  => "success",
      ];
    } catch (\Throwable) {

      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'actualizar',
        'resultado' => 'Fallido',
        'viejo'     => isset($estadoViejo) && $estadoViejo ? (array)$estadoViejo : ['id_presentacion' => $this->idPresentacion],
        'nuevo'     => [
          "id_unidad_medida"    => $this->idUnidadMedida,
          "nombre_presentacion" => $this->nombrePresentacion,
          "cantidad_pmp"        => $this->cantidadPMP,
        ]
      ]);

      return [
        "tipo"   => "simple",
        "titulo" => "Error al actualizar",
        "texto"  => 'No se ha podido actualizar la presentación',
        "icono"  => "error",
      ];
    }
  }
  private function eliminarPresentacionesP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();
    $estadoViejo = null;

    try {
      $estadoViejo = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla'  => 'presentaciones',
        'WHERE'  => [
          'id_presentacion' => $this->idPresentacion,
        ],
      ])->fetch();

      // Validamos si existe la presentación
      if (!$estadoViejo) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'presentaciones',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => [
            'id_presentacion' => $this->idPresentacion,
          ],
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La presentación no existe en la Base de Datos',
          'icono'  => 'error',
        ];
      }

      $resultado = $this->eliminarDatos2([
        'tabla' => 'presentaciones',
        'WHERE' => [
          'id_presentacion' => $this->idPresentacion,
        ],
      ]);

      // Validamos si se eliminó correctamente
      if ($resultado === false || $resultado !== 1) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'presentaciones',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => (array) $estadoViejo,
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La presentación no ha sido eliminada con éxito',
          'icono'  => 'error',
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'eliminar',
        'resultado' => 'Éxito',
        'viejo'     => (array) $estadoViejo,
        'nuevo'     => [],
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol'  => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          [
            'accion' => 'borrarDataModuloSS',
            'modulo' => 'presentaciones',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'presentaciones',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Presentación eliminada',
              'texto'    => 'La presentación ha sido eliminada del sistema',
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Presentación eliminada',
        'texto'  => 'La presentación ha sido eliminada con éxito',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'presentaciones',
        'accion'    => 'eliminar',
        'resultado' => 'Fallido',
        'viejo'     => $estadoViejo
          ? (array) $estadoViejo
          : [
            'id_presentacion' => $this->idPresentacion,
          ],
        'nuevo'     => [],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al eliminar',
        'texto'  => 'No se ha podido eliminar la presentación',
        'icono'  => 'error',
      ];
    }
  }
}
