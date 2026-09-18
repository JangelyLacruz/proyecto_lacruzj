<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;


class materiasPrimasModelo extends conexion {
  private string $idMateriaPrima = '';
  private string $idUnidadMedida = '';
  private string $nombreMateriaPrima = '';
  private float $precioMateriaPrima = 0;
  private int $stockMateriaPrima = 0;
  private int $stockMinimoMateriaPrima = 0;
  private array $presentaciones = [];

  // PÚBLICOS
  public function validarMateriasPrimas(string $permiso, array $instruccionesVal) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('materiasPrimas', $permiso);
    if ($r) return $r;

    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_materia_prima' => [
          "campo_nombre" => "id_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la materia prima",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "materias_primas",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'id_unidad_medida' => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        'nombre_materia_prima' => [
          "campo_nombre" => "nombre_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la materia prima",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "materias_primas",
          "debeSerUnico" => true,
        ],
        'precio_materia_prima' => [
          "campo_valor" => &$valor,
          'comaPunto' => true,
          "formulario_nombre" => "precio de la matería prima",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        'stock_materia_prima' => [
          "campo_nombre" => "stock_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'stock_minimo_materia_prima' => [
          "campo_nombre" => "stock_minimo_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock mínimo",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" => &$valor,
          "formulario_nombre" => "presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      switch ($campo) {
        case 'presentaciones':
          // El campo puede llegar como string JSON (desde input hidden) o como array ya decodificado
          if (is_string($infoVal['presentaciones'] ?? null)) {
            $infoVal['presentaciones'] = json_decode($infoVal['presentaciones'], true) ?? [];
          }
          if (empty($infoVal['presentaciones'])) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin presentaciones',
              'texto' => 'No has enviado las presentaciones de la materia prima',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['presentaciones'] as &$pre) {
            if (is_array($pre)) {
              $pre = $pre['id_presentacion'] ?? '';
            }
            $campos[] = $funcionAsignadora('id_presentacion', $pre);
          }
          unset($pre);
          break;

        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function modificarStock(string $id_materia_prima, float $cantidad, $conexionTransaction = null) {
    try {
      $cn = $conexionTransaction ?? $this->conectar();
      $stmt = $cn->prepare("UPDATE materias_primas SET stock_materia_prima = stock_materia_prima + :cant WHERE id_materia_prima = :id");
      $stmt->execute([
        ':cant' => $cantidad,
        ':id' => $id_materia_prima
      ]);
      return true;
    } catch (\Throwable) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error',
        'texto'  => 'Ha ocurrido un error',
        'icono'  => 'error',
      ];
    }
  }
  public function seleccionarMateriasPrimas($info = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('materiasPrimas', 'listar');
    if ($v) return $v;

    if (isset($info['id_materia_prima'])) {
      $resultado = $this->validarMateriasPrimas('listar', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_materia_prima',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idMateriaPrima = $info['id_materia_prima'];
    }
    return $this->seleccionarMateriasPrimasP();
  }
  public function registrarMateriasPrimas(array $info) {
    $resultado = $this->validarMateriasPrimas('registrar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_materia_prima',
        'precio_materia_prima',
        'stock_materia_prima',
        'stock_minimo_materia_prima',
        'presentaciones',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombreMateriaPrima = $info['nombre_materia_prima'];
    $this->precioMateriaPrima = $info['precio_materia_prima'];
    $this->stockMateriaPrima = $info['stock_materia_prima'];
    $this->stockMinimoMateriaPrima = $info['stock_minimo_materia_prima'];
    $this->presentaciones = $info['presentaciones'];

    return $this->registrarMateriasPrimasP();
  }
  public function actualizarMateriasPrimas(array $info) {
    $resultado = $this->validarMateriasPrimas('actualizar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_materia_prima',
        'id_unidad_medida',
        'nombre_materia_prima',
        'precio_materia_prima',
        'stock_materia_prima',
        'stock_minimo_materia_prima',
        'presentaciones',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idMateriaPrima = $info['id_materia_prima'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombreMateriaPrima = $info['nombre_materia_prima'];
    $this->precioMateriaPrima = $info['precio_materia_prima'];
    $this->stockMateriaPrima = $info['stock_materia_prima'];
    $this->stockMinimoMateriaPrima = $info['stock_minimo_materia_prima'];
    $this->presentaciones = $info['presentaciones'];

    return $this->actualizarMateriasPrimasP();
  }
  public function eliminarMateriasPrimas(array $info) {
    $resultado = $this->validarMateriasPrimas('eliminar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_materia_prima',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idMateriaPrima = $info['id_materia_prima'];
    return $this->eliminarMateriasPrimasP();
  }

  // PRIVADOS
  private function seleccionarMateriasPrimasP() {
    if ($this->idMateriaPrima == null || $this->idMateriaPrima == "") {
      $instruccionesBD = [
        'campos' => '
          mp.id_materia_prima, mp.nombre_materia_prima,
          um.nombre_unidad_medida, mp.stock_materia_prima, 
          mp.stock_minimo_materia_prima, 
          mp.precio_materia_prima,mp.id_unidad_medida
        ',
        'tabla' => 'materias_primas as mp',
        'datosJoins' => [
          "unidades_medidas as um" => "mp.id_unidad_medida = um.id_unidad_medida",
        ]
      ];
      return $this->seleccionarDatos2($instruccionesBD)->fetchAll();
    } else {
      //Datos generales
      $materiaPrima = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'materias_primas as mp',
        'WHERE' => [
          "id_materia_prima" => $this->idMateriaPrima,
        ],
        'datosJoins' => [
          'unidades_medidas as um' => 'mp.id_unidad_medida = um.id_unidad_medida'
        ]
      ])->fetch();

      //Presentaciones
      $presentaciones = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'presentaciones_materias_primas as prmp',
        'WHERE' => [
          "id_materia_prima" => $this->idMateriaPrima,
        ],
        'datosJoins' => [
          'presentaciones as pr' => 'prmp.id_presentacion = pr.id_presentacion'
        ]
      ])->fetchAll() ?? [];
      $materiaPrima['presentaciones'] = $presentaciones;
      return $materiaPrima;
    }
  }
  private function registrarMateriasPrimasP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();
    $idMateriaPrima = null;

    $datosMateriaPrima = [
      'id_unidad_medida'          => $this->idUnidadMedida,
      'nombre_materia_prima'      => $this->nombreMateriaPrima,
      'stock_materia_prima'       => $this->stockMateriaPrima,
      'stock_minimo_materia_prima' => $this->stockMinimoMateriaPrima,
      'precio_materia_prima'      => $this->precioMateriaPrima,
    ];

    try {
      $idMateriaPrima = $this->generarCodSeg([
        'tablaBD' => 'materias_primas',
        'prefijo' => 'MATE',
        'campoID' => 'id_materia_prima',
      ]);

      $ultimoId = $this->guardarDatos2([
        'tabla' => 'materias_primas',
        'datos' => [
          'id_materia_prima'          => $idMateriaPrima,
          'id_unidad_medida'          => $this->idUnidadMedida,
          'nombre_materia_prima'      => $this->nombreMateriaPrima,
          'stock_materia_prima'       => $this->stockMateriaPrima,
          'stock_minimo_materia_prima' => $this->stockMinimoMateriaPrima,
          'precio_materia_prima'      => $this->precioMateriaPrima,
        ],
      ]);

      // Validamos si la materia prima fue registrada
      if ($ultimoId === false || $ultimoId <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'materiasPrimas',
          'accion'    => 'registrar',
          'resultado' => 'Fallido',
          'viejo'     => [],
          'nuevo'     => $datosMateriaPrima,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al registrar',
          'texto'  => 'La materia prima no ha podido ser registrada',
          'icono'  => 'error',
        ];
      }

      // Registramos las presentaciones relacionadas
      if (!empty($this->presentaciones)) {
        foreach ($this->presentaciones as $idPresentacion) {
          if ($idPresentacion !== '') {
            $ultimoIdPresentacion = $this->guardarDatos2([
              'tabla' => 'presentaciones_materias_primas',
              'datos' => [
                'id_materia_prima' => $idMateriaPrima,
                'id_presentacion' => $idPresentacion,
              ],
            ]);

            // Validamos si la presentación fue relacionada correctamente
            if (
              $ultimoIdPresentacion === false
              || $ultimoIdPresentacion <= 0
            ) {
              $objBitacora->registrarBitacora([
                'modulo'    => 'materiasPrimas',
                'accion'    => 'registrar',
                'resultado' => 'Fallido',
                'viejo'     => [],
                'nuevo'     => array_merge(
                  $datosMateriaPrima,
                  [
                    'id_materia_prima' => $idMateriaPrima,
                    'id_presentacion'  => $idPresentacion,
                  ]
                ),
              ]);

              $this->rollback();

              return [
                'tipo'   => 'simple',
                'titulo' => 'Error al registrar',
                'texto'  => 'La presentación de la materia prima no ha podido ser registrada',
                'icono'  => 'error',
              ];
            }
          }
        }
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'registrar',
        'resultado' => 'Éxito',
        'viejo'     => [],
        'nuevo'     => $this->seleccionarMateriasPrimas([
          'id_materia_prima' => $idMateriaPrima,
        ]),
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
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Materia prima registrada',
              'texto'    => "Se ha registrado la materia prima {$this->nombreMateriaPrima}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'limpiarYcerrar',
        'titulo' => 'Materia prima registrada',
        'texto'  => 'La materia prima ha sido registrada exitosamente',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'registrar',
        'resultado' => 'Fallido',
        'viejo'     => [],
        'nuevo'     => array_merge(
          $datosMateriaPrima,
          $idMateriaPrima
            ? ['id_materia_prima' => $idMateriaPrima]
            : []
        ),
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al registrar',
        'texto'  => 'No se pudo registrar la materia prima',
        'icono'  => 'error',
      ];
    }
  }
  private function actualizarMateriasPrimasP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();
    $datosAntes = null;

    $datosNuevos = [
      'id_unidad_medida'           => $this->idUnidadMedida,
      'nombre_materia_prima'       => $this->nombreMateriaPrima,
      'stock_materia_prima'        => $this->stockMateriaPrima,
      'stock_minimo_materia_prima' => $this->stockMinimoMateriaPrima,
      'precio_materia_prima'       => $this->precioMateriaPrima,
    ];

    try {
      $MAT = 0;
      $PRE = 0;

      $datosAntes = $this->seleccionarMateriasPrimas([
        'id_materia_prima' => $this->idMateriaPrima,
      ]);

      // Validamos si existe la materia prima
      if (!$datosAntes) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'materiasPrimas',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => [
            'id_materia_prima' => $this->idMateriaPrima,
          ],
          'nuevo'     => $datosNuevos,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al actualizar',
          'texto'  => 'La materia prima no existe en el sistema',
          'icono'  => 'error',
        ];
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'materias_primas',
        'datos' => $datosNuevos,
        'WHERE' => [
          'id_materia_prima' => $this->idMateriaPrima,
        ],
      ]);

      if ($resultado !== false && $resultado > 0) {
        $MAT++;
      }

      // Eliminamos las presentaciones anteriores si existen
      if (!empty($datosAntes['presentaciones'])) {
        $resultado = $this->eliminarDatos2([
          'tabla' => 'presentaciones_materias_primas',
          'WHERE' => [
            'id_materia_prima' => $this->idMateriaPrima,
          ],
          'fisico' => true,
        ]);

        if ($resultado === false || $resultado <= 0) {
          $objBitacora->registrarBitacora([
            'modulo'    => 'materiasPrimas',
            'accion'    => 'actualizar',
            'resultado' => 'Fallido',
            'viejo'     => $datosAntes,
            'nuevo'     => $datosNuevos,
          ]);

          $this->rollback();

          return [
            'tipo'   => 'simple',
            'titulo' => 'Error al actualizar',
            'texto'  => 'Las presentaciones de la materia prima no han podido ser eliminadas',
            'icono'  => 'error',
          ];
        }

        $PRE += $resultado;
      }

      // Registramos las nuevas presentaciones
      if (!empty($this->presentaciones)) {
        foreach ($this->presentaciones as $idPresentacion) {
          if ($idPresentacion !== '') {
            $ultimoId = $this->guardarDatos2([
              'tabla' => 'presentaciones_materias_primas',
              'datos' => [
                'id_materia_prima' => $this->idMateriaPrima,
                'id_presentacion' => $idPresentacion,
              ],
            ]);

            if ($ultimoId === false || $ultimoId <= 0) {
              $objBitacora->registrarBitacora([
                'modulo'    => 'materiasPrimas',
                'accion'    => 'actualizar',
                'resultado' => 'Fallido',
                'viejo'     => $datosAntes,
                'nuevo'     => array_merge(
                  $datosNuevos,
                  [
                    'id_materia_prima' => $this->idMateriaPrima,
                    'id_presentacion'  => $idPresentacion,
                  ]
                ),
              ]);

              $this->rollback();

              return [
                'tipo'   => 'simple',
                'titulo' => 'Error al actualizar',
                'texto'  => 'La presentación de la materia prima no ha podido ser registrada',
                'icono'  => 'error',
              ];
            }

            $PRE++;
          }
        }
      }

      // Validamos si se realizó algún cambio
      if ($PRE === 0 && $MAT === 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'materiasPrimas',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => $datosAntes,
          'nuevo'     => $datosNuevos,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Aviso',
          'texto'  => 'La materia prima no ha sido actualizada',
          'icono'  => 'warning',
        ];
      }

      $datosDespues = $this->seleccionarMateriasPrimas([
        'id_materia_prima' => $this->idMateriaPrima,
      ]);

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'actualizar',
        'resultado' => 'Éxito',
        'viejo'     => $datosAntes,
        'nuevo'     => $datosDespues,
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
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Materia Prima actualizada',
              'texto'    => "Se ha actualizado la materia prima {$this->nombreMateriaPrima}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'limpiarYcerrar',
        'titulo' => 'Materia prima actualizada',
        'texto'  => 'La materia prima ha sido actualizada exitosamente',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'actualizar',
        'resultado' => 'Fallido',
        'viejo'     => $datosAntes ?: [
          'id_materia_prima' => $this->idMateriaPrima,
        ],
        'nuevo'     => $datosNuevos,
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al actualizar',
        'texto'  => 'No se pudo actualizar la materia prima',
        'icono'  => 'warning',
      ];
    }
  }
  private function eliminarMateriasPrimasP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();
    $datosAntes = null;

    try {
      $datosAntes = $this->seleccionarMateriasPrimas([
        'id_materia_prima' => $this->idMateriaPrima,
      ]);

      // Validamos si existe la materia prima
      if (!$datosAntes) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'materiasPrimas',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => [
            'id_materia_prima' => $this->idMateriaPrima,
          ],
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La materia prima no existe en el sistema',
          'icono'  => 'error',
        ];
      }

      // Eliminamos las presentaciones relacionadas
      if (!empty($datosAntes['presentaciones'])) {
        $resultado = $this->eliminarDatos2([
          'tabla' => 'presentaciones_materias_primas',
          'WHERE' => [
            'id_materia_prima' => $this->idMateriaPrima,
          ],
        ]);

        if ($resultado === false || $resultado <= 0) {
          $objBitacora->registrarBitacora([
            'modulo'    => 'materiasPrimas',
            'accion'    => 'eliminar',
            'resultado' => 'Fallido',
            'viejo'     => $datosAntes,
            'nuevo'     => [],
          ]);

          $this->rollback();

          return [
            'tipo'   => 'simple',
            'titulo' => 'Error al eliminar',
            'texto'  => 'Las presentaciones de la materia prima no han podido ser eliminadas',
            'icono'  => 'error',
          ];
        }
      }

      // Eliminamos la materia prima principal
      $resultado = $this->eliminarDatos2([
        'tabla' => 'materias_primas',
        'WHERE' => [
          'id_materia_prima' => $this->idMateriaPrima,
        ],
      ]);

      if ($resultado === false || $resultado <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'materiasPrimas',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => $datosAntes,
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La materia prima no ha podido ser eliminada',
          'icono'  => 'error',
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'eliminar',
        'resultado' => 'Éxito',
        'viejo'     => $datosAntes,
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
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'materiasPrimas',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Materia Prima eliminada',
              'texto'    => 'La materia prima ha sido eliminada del sistema',
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Materia prima eliminada',
        'texto'  => 'La materia prima ha sido eliminada con éxito',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'materiasPrimas',
        'accion'    => 'eliminar',
        'resultado' => 'Fallido',
        'viejo'     => $datosAntes ?: [
          'id_materia_prima' => $this->idMateriaPrima,
        ],
        'nuevo'     => [],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al eliminar',
        'texto'  => 'No se pudo eliminar la materia prima',
        'icono'  => 'error',
      ];
    }
  }
}
