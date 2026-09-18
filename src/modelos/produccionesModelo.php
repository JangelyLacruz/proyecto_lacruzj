<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;

class produccionesModelo extends conexion {
  private string $idProduccion = '';
  private array $productos = [];

  public function validarProducciones(string $permiso, ?array &$info, ?array $requerido) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('producciones', $permiso);
    if ($r) return $r;

    $esquemaProducciones = [
      "tipo" => 'arrayA',
      'propiedades' => [
        'id_produccion' => [
          'tipo' => 'string',
          "nombreAlerta" => "id de la producción",
          "minL" => minRegexIdSeguro,
          "maxL" => maxRegexIdSeguro,
          "regex" => regexIdSeguro,
          "nombreBD" => "id_produccion",
          "tablaBD" => "producciones",
          "debeExistirBD" => true,
          "debeSerUnicoBD" => true,
        ],
        'productos' => [
          'tipo' => 'array',
          'nombreAlerta' => 'productos',
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'id_producto' => [
                'tipo' => 'string',
                "nombreAlerta" => "id del producto",
                "minL" => minRegexIdSeguro,
                "maxL" => maxRegexIdSeguro,
                "regex" => regexIdSeguro,
                "nombreBD" => "id_producto",
                "tablaBD" => "productos",
                "debeExistirBD" => true,
              ],
              'cantidad_producida' => [
                'tipo' => 'string',
                "nombreAlerta" => "cantidad del producto",
                "minL" => minRegexCantidadItem,
                "maxL" => maxRegexCantidadItem,
                "regex" => regexCantidadItem,
                "cFloat" => true,
              ],
            ],
            'requerido' => [
              'id_producto',
              'cantidad_producida',
            ]
          ],
          'minItems' => 1
        ],
      ],
      'requerido' => $requerido
    ];

    $r = $this->limpiarValidar($info, $esquemaProducciones);
    if ($r) return $r;
    return false;
  }
  public function seleccionarProducciones(array $info) {
    $requerido = [];
    if (($info['id_produccion'] ?? '') != '') {
      $requerido[] = 'id_produccion';
    }
    $resultado = $this->validarProducciones('ver', $info, $requerido);
    if ($resultado) return $resultado;
    if (($info['id_produccion'] ?? '') != '') {
      $this->idProduccion = $info['id_produccion'];
    }
    return $this->seleccionarProduccionesP();
  }
  public function registrarProducciones(array $info) {
    if (isset($info['productos']) && is_string($info['productos'])) {
      $info['productos'] = json_decode($info['productos'], true);
    }

    $resultado = $this->validarProducciones('registrar', $info, ['productos']);
    if ($resultado) return $resultado;
    $this->productos = $info['productos'];
    return $this->registrarProduccionesP();
  }
  public function actualizarProducciones(array $info) {
    if (isset($info['productos']) && is_string($info['productos'])) {
      $info['productos'] = json_decode($info['productos'], true);
    }

    $resultado = $this->validarProducciones('actualizar', $info, ['id_produccion', 'productos']);
    if ($resultado) return $resultado;
    $this->idProduccion = $info['id_produccion'];
    $this->productos = $info['productos'];
    return $this->actualizarProduccionesP();
  }

  private function seleccionarProduccionesP() {
    if ($this->idProduccion == null || $this->idProduccion == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_produccion, fecha_produccion',
        'tabla' => 'producciones',
        'ORDER' => 'fecha_produccion DESC'
      ]);
      return $resultado->fetchAll();
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Producción no encontrada",
          "texto" => "La producción que ha intentado consultar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      $produccion = $resultado->fetch();

      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_producto, cantidad_producida',
        'tabla' => 'productos_producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ]);

      $produccion['detalles'] = $resultado->rowCount() > 0 ? $resultado->fetchAll() : [];
      return $produccion;
    }
  }
  private function registrarProduccionesP() {
    $objBitacora = new bitacoraModelo();

    $idProduccion = $this->generarCodSeg([
      'tablaBD' => 'producciones',
      'prefijo' => 'PROD',
      'campoID' => 'id_produccion'
    ]);

    $resultadoGuardar = $this->guardarDatos2([
      'tabla' => 'producciones',
      'datos' => [
        'id_produccion' => $idProduccion,
        'fecha_produccion' => $this->fechaHoraSel('fecha_hora_BD')
      ]
    ]);

    if ($resultadoGuardar === false || $resultadoGuardar <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'producciones',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error al crear la producción',
        'icono' => 'error'
      ];
    }

    $this->productos = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $this->productos,
    ]);

    foreach ($this->productos as $id => $cantidad) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $idProduccion,
          'id_producto' => $id,
          'cantidad_producida' => $cantidad,
        ]
      ]);

      if ($idDetalle == 0 || $idDetalle == false) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Registrar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $id
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        return $infoGeneralProducto;
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $cantidad)
        ],
        'WHERE' => [
          'id_producto' => $id
        ]
      ]);

      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Registrar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      // Descontar materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStock = ($stockMP - $cantidadTotalRestar);

        if ($nuevoStock < 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Registrar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se puede descontar una cantidad mayor de materia prima a la disponible dentro del stock de inventario',
            'icono' => 'error'
          ];
        }

        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStock
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);

        if ($resultado === false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Registrar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }
      }
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'producciones',
      'accion' => 'Registrar',
      'resultado' => 'Éxito',
      'nuevo' => $this->seleccionarProducciones([
        'id_produccion' => $idProduccion
      ])
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['producciones' => 'ver']
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'producciones'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'producciones'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Producción registrada',
            'texto' => 'El stock de algunos productos ha cambiado',
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
    ]);

    $this->commit();
    return [
      'tipo' => 'limpiarYcerrar',
      'icono' => 'success',
      'titulo' => 'Éxito en el registro',
      'texto' => 'Se registró correctamente la producción: ' . $idProduccion,
    ];
  }
  private function actualizarProduccionesP() {
    $objBitacora = new bitacoraModelo();

    // Obtener datos antes de la actualización
    $datosAntes = $this->seleccionarProducciones([
      'id_produccion' => $this->idProduccion
    ]);

    if (isset($datosAntes['tipo']) && $datosAntes['tipo'] == 'simple') {
      $this->rollback();
      return $datosAntes;
    }

    $detallesOriginales = $datosAntes['detalles'] ?? [];
    $detallesIndexados = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $detallesOriginales,
    ]);

    $this->productos = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $this->productos,
    ]);

    // Eliminar detalles antiguos
    $resultado = $this->eliminarDatos2([
      'tabla' => 'productos_producciones',
      'WHERE' => [
        'id_produccion' => $this->idProduccion
      ],
      'fisico' => true
    ]);

    // Restaurar stock de productos antiguos
    foreach ($detallesIndexados as $idProducto => $cantidadProducto) {
      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $idProducto
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        return $infoGeneralProducto;
      }

      if ($cantidadProducto > $infoGeneralProducto['stock_producto']) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se puede guardar la nueva cantidad de ' . $infoGeneralProducto['nombre_producto'] . ' porque esto dejaría un stock negativo del mismo',
          'icono' => 'error'
        ];
      }

      $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] - $cantidadProducto)
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);

      // Subir stock de materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];
      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalSumar = $cantidadProducto * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];
        $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => ($stockMP + $cantidadTotalSumar)
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
      }
    }

    // Insertar nuevos productos
    foreach ($this->productos as $idProducto => $cantidad) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'cantidad_producida' => $cantidad,
        ]
      ]);

      if ($idDetalle == 0 || $idDetalle === false) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $idProducto
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        return $infoGeneralProducto;
      }

      // Actualizar stock del producto
      $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $cantidad)
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);

      // Descontar materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];
      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStock = ($stockMP - $cantidadTotalRestar);

        if ($nuevoStock < 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Actualizar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se puede descontar una cantidad mayor de materia prima a la disponible dentro del stock de inventario',
            'icono' => 'error'
          ];
        }

        $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStock
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
      }
    }

    $datosDespues = $this->seleccionarProducciones([
      'id_produccion' => $this->idProduccion
    ]);

    $objBitacora->registrarBitacora([
      'modulo' => 'producciones',
      'accion' => 'Actualizar',
      'resultado' => 'Éxito',
      'viejo' => $datosAntes,
      'nuevo' => $datosDespues
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['producciones' => 'ver']
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'producciones'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'producciones'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Producción actualizada',
            'texto' => 'El stock de algunos productos ha cambiado',
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
    ]);

    $this->commit();
    return [
      'icono' => 'success',
      'titulo' => 'Éxito en la actualización',
      'texto' => 'Se actualizó correctamente la producción #' . $this->idProduccion,
      'tipo' => 'limpiarYcerrar'
    ];
  }
}
