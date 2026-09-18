<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\categoriasProductosModelo;
use src\modelos\mensajesWSModelo;
use PDO;

class productosModelo extends conexion {
  private string $idProducto = '';
  private string $idPresentacionProd = '';
  private string $idUnidadMedida = '';
  private string $idCategoria = '';
  private string $nombreProducto = '';
  private float $precioProducto = 0;
  private int $stockProducto = 0;
  private int $stockMinimoProducto = 0;
  private array $presentaciones = [];
  private array $materiasPrimas = [];
  private array $fotoPresentacion = [];

  public function validarProductos(string $permiso, array &$info = [], array $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('productos', $permiso);
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'foto_presentacion' => [
          ...molFotoInd,
          'nombreAlerta' => 'foto de la presentación'
        ],
        'id_producto' => [
          ...molIdSeguro,
          "nombreAlerta" => "id del producto",
          "nombreBD" => "id_producto",
          "tablaBD" => "productos",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
        ],
        'id_unidad_medida' => [
          ...molId,
          "nombreAlerta" => "unidades de medida",
          "nombreBD" => "id_unidad_medida",
          "tablaBD" => "unidades_medidas",
          "debeExistirBD" => true,
        ],
        'id_categoria_producto' => [
          ...molId,
          "nombreAlerta" => "categoria",
          "nombreBD" => "id_categoria_producto",
          "tablaBD" => "categorias_productos",
          "debeExistirBD" => true,
        ],
        'nombre_producto' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre del producto",
          "nombreBD" => "nombre_producto",
          "tablaBD" => "productos",
          "debeSerUnicoBD" => true,
        ],
        'precio_producto' => [
          ...molPrecioFormateado,
          "nombreAlerta" => "precio en divisas",
        ],
        'stock_producto' => [
          ...molPrecioFormateado,
          "nombreAlerta" => "stock del producto",
        ],
        'stock_minimo_producto' => [
          ...molPrecioFormateado,
          "nombreAlerta" => "stock mínimo del producto",
        ],
        'presentaciones' => [
          'tipo' => 'array',
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'id_presentacion' => [
                ...molIdSeguro,
                "nombreAlerta" => "id de la presentación",
                "nombreBD" => "id_presentacion",
                "tablaBD" => "presentaciones",
                "debeExistirBD" => true,
              ],
              'mostrar_ecommerce' => [
                ...molBooleanoInt,
                "nombreAlerta" => "si se debe o no mostrar en el ecommerce",
              ],
            ],
            'requerido' => ['id_presentacion']
          ],
          'minItems' => 1,
          'nombreAlerta' => 'presentaciones del producto',
        ],
        'materias_primas' => [
          'tipo' => 'array',
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'id_materia_prima' => [
                ...molIdSeguro,
                "nombreAlerta" => "id de la materia prima",
                "nombreBD" => "id_materia_prima",
                "tablaBD" => "materias_primas",
                "debeExistirBD" => true,
              ],
              'cantidad_materia_prima' => [
                ...molPrecioFormateado,
                "nombreAlerta" => "cantidad de la materia prima",
              ],
            ],
            'requerido' => ['id_materia_prima', 'cantidad_materia_prima']
          ],
          'nombreAlerta' => 'las materias primas del producto'
        ],
        'id_presentacion_producto' => [
          ...molIdSeguro,
          "nombreAlerta" => "id de la presentación del producto",
          "nombreBD" => "id_presentacion_producto",
          "tablaBD" => "presentaciones_productos",
          "debeExistirBD" => true,
        ],
      ],
      'requerido' => $requerido,
    ];
    if (isset($info['id_categoria_producto']) && $info['id_categoria_producto'] != '') {
      $objCategorias = new categoriasProductosModelo();
      $categoriaBD = $objCategorias->seleccionarCategorias(['id_categoria_producto' => $info['id_categoria_producto']]);
      if ($categoriaBD['necesitan_materias_primas'] == 1) {
        $esquema['requerido'][] = 'materias_primas';
        $esquema['propiedades']['materias_primas']['minItems'] = 1;
      }
    }
    if (isset($info['presentaciones'])) $info['presentaciones'] = array_values($info['presentaciones']);
    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;

    return false;
  }
  public function modificarStock(string $id_producto, float $cantidad, $conexionTransaction = null) {
    try {
      $cn = $conexionTransaction ?? $this->conectar();
      $stmt = $cn->prepare("UPDATE productos SET stock_producto = stock_producto + :cant WHERE id_producto = :id");
      $stmt->execute([
        ':cant' => $cantidad,
        ':id' => $id_producto
      ]);
      return true;
    } catch (\Throwable $th) {
      return $th->getMessage();
    }
  }
  public function seleccionarProductos(array $info) {
    if (($info['tipoConsulta'] ?? '') == 'presentaciones') {
      $v = $this->validarProductos('ver detalles de los productos', $info);
    } else {
      $v = $this->validarProductos('listar', $info);
    }

    if ($v) return $v;
    $this->idProducto = $info['id_producto'] ?? '';
    $this->idPresentacionProd = $info['id_presentacion_producto'] ?? '';
    return $this->seleccionarProductosP($info);
  }
  public function obtenerParaChatbot() {
    return $this->obtenerParaChatbotP();
  }
  public function registrarProductos(array $info) {
    $v = $this->validarProductos('registrar', $info, [
      'id_unidad_medida',
      'id_categoria_producto',
      'nombre_producto',
      'precio_producto',
      'stock_producto',
      'stock_minimo_producto',
      'presentaciones',
    ]);
    if ($v !== false) return $v;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->idCategoria = $info['id_categoria_producto'];
    $this->nombreProducto = $info['nombre_producto'];
    $this->precioProducto = $info['precio_producto'];
    $this->stockProducto = $info['stock_producto'];
    $this->stockMinimoProducto = $info['stock_minimo_producto'];
    $this->presentaciones = $info['presentaciones'];
    $this->materiasPrimas = $info['materias_primas'] ?? [];

    foreach ($this->presentaciones as &$pre) {
      if (isset($info['foto_presentacion_' . $pre['id_presentacion']])) {
        $pre['foto_presentacion'] = $info['foto_presentacion_' . $pre['id_presentacion']];
      }
    }
    unset($pre);
    return $this->registrarProductosP();
  }
  public function actualizarProductos(array $info) {
    $v = $this->validarProductos('actualizar', $info, [
      'id_producto',
      'id_unidad_medida',
      'id_categoria_producto',
      'nombre_producto',
      'precio_producto',
      'stock_producto',
      'stock_minimo_producto',
      'presentaciones',
    ]);
    if ($v !== false) return $v;

    $this->idProducto = $info['id_producto'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->idCategoria = $info['id_categoria_producto'];
    $this->nombreProducto = $info['nombre_producto'];
    $this->precioProducto = $info['precio_producto'];
    $this->stockProducto = $info['stock_producto'];
    $this->stockMinimoProducto = $info['stock_minimo_producto'];
    $this->presentaciones = $info['presentaciones'];
    $this->materiasPrimas = $info['materias_primas'] ?? [];

    foreach ($this->presentaciones as &$pre) {
      if (isset($info['foto_presentacion_' . $pre['id_presentacion']])) {
        $pre['foto_presentacion'] = $info['foto_presentacion_' . $pre['id_presentacion']];
      }
    }
    unset($pre);
    return $this->actualizarProductosP();
  }
  public function eliminarProductos(array $info) {
    $v = $this->validarProductos('eliminar', $info, ['id_producto']);
    if ($v !== false) return $v;

    $this->idProducto = $info['id_producto'];
    return $this->eliminarProductosP();
  }
  public function actualizarFotPreProd(array $info) {
    $v = $this->validarProductos('actualizar', $info, [
      'id_presentacion_producto',
      'foto_presentacion',
    ]);
    if ($v !== false) return $v;
    $this->idPresentacionProd = $info['id_presentacion_producto'];
    $this->fotoPresentacion = $info['foto_presentacion'];
    return $this->actualizarFotPreProdP();
  }
  public function eliminarFotPreProd(array $info) {
    $v = $this->validarProductos('actualizar', $info, ['id_presentacion_producto']);
    if ($v !== false) return $v;
    $this->idPresentacionProd = $info['id_presentacion_producto'];
    return $this->eliminarFotPreProdP();
  }

  private function seleccionarProductosP(array $info) {
    if ($this->idProducto == null || $this->idProducto == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'ecommerce':
          return $this->seleccionarDatos2([
            'campos' => '
              *,
              ROUND((
                (
                  (
                    SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1
                  )*p.precio_producto
                )*pre.cantidad_pmp
              ),2) as precio_bs,
              ROUND((
                p.precio_producto*pre.cantidad_pmp
              ),2) as precio_dolar
            ',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
              "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
              "presentaciones as pre" => "pp.id_presentacion = pre.id_presentacion",
            ],
            'WHERE' => [
              'pp.mostrar_ecommerce' => 1,
              'pp.status' => 1,
            ]
          ])->fetchAll();
        case 'presentacionExp':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'presentaciones_productos as prpr',
            'datosJoins' => [
              "presentaciones as pr" => "prpr.id_presentacion = pr.id_presentacion",
            ],
            'WHERE' => [
              'prpr.id_presentacion_producto' => $this->idPresentacionProd
            ]
          ])->fetch();
        case 'productosFactura':
          return $this->seleccionarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos as f',
            'campos' => '*,
              ROUND(( 
                SELECT prpr.precio_producto 
                FROM precios_productos as prpr 
                WHERE prpr.id_producto = pr.id_producto AND prpr.fecha_cambio <= f.fecha_orden_entrega_presupuesto 
                ORDER BY prpr.id_precio_producto DESC 
                LIMIT 1 
              ),2) AS precio_producto_factura, 
              (pre.cantidad_pmp * pf.cantidad_producto) as cantidad_bruta, 
              ( 
                SELECT (prpr.precio_producto*pre.cantidad_pmp)  
                FROM precios_productos as prpr 
                WHERE prpr.id_producto = pr.id_producto AND prpr.fecha_cambio <= f.fecha_orden_entrega_presupuesto 
                ORDER BY prpr.id_precio_producto DESC
                LIMIT 1 
              ) as precio_presentacion_factura
            ',
            'datosJoins' => [
              'productos_ordenes_entregas_presupuestos as pf' => 'f.id_orden_entrega_presupuesto = pf.id_orden_entrega_presupuesto',
              'presentaciones_productos as pp' => 'pf.id_presentacion_producto = pp.id_presentacion_producto',
              'productos as pr' => 'pp.id_producto = pr.id_producto',
              'presentaciones as pre' => 'pp.id_presentacion = pre.id_presentacion',
            ],
            'WHERE' => [
              'pf.id_orden_entrega_presupuesto' => $info['id_factura'],
            ],
          ])->fetchAll();
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
            ]
          ])->fetchAll(PDO::FETCH_UNIQUE);
        case 'todasLasPresentaciones':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
              "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
              "presentaciones as pre" => "pp.id_presentacion = pre.id_presentacion",
            ],
            'WHERE' => [
              'pp.status' => 1,
            ]
          ])->fetchAll();
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
            ]
          ])->fetchAll();
      }
    } else {
      $producto = [];
      switch ($info['tipoConsulta'] ?? '') {
        case 'presentaciones':
          $producto = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
              "presentaciones as pre" => "pp.id_presentacion = pre.id_presentacion",
            ],
            'WHERE' => [
              'p.id_producto' => $this->idProducto
            ]
          ])->fetchAll();
          break;
        default:
          //Dato generales  
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as pr',
            'WHERE' => [
              "id_producto" => $this->idProducto,
            ],
            'datosJoins' => [
              'categorias_productos as cp' => 'pr.id_categoria_producto = cp.id_categoria_producto',
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida'
            ]
          ]);
          if ($resultado->rowCount() <= 0) {
            return [
              "tipo" => "simple",
              "titulo" => "Producto no encontrado",
              "texto" => "El producto no se encuentra.",
              "icono" => "error"
            ];
          }
          $producto = $resultado->fetch();

          //Presentaciones
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'presentaciones_productos',
            'WHERE' => [
              "id_producto" => $this->idProducto,
            ]
          ]);
          $presentaciones = $resultado->fetchAll();

          //Materias primas
          $resultado = $this->seleccionarDatos2([
            'campos' => 'id_materia_prima_producto, id_materia_prima, cantidad_materia_prima',
            'tabla' => 'materias_primas_productos',
            'WHERE' => [
              "id_producto" => $this->idProducto
            ]
          ]);
          $materiasPrimas = $resultado->fetchAll();
          $producto['detallesExtra'] = [
            'presentaciones' => $presentaciones,
            'materias_primas' => $materiasPrimas
          ];
      }
      return $producto;
    }
  }
  private function obtenerParaChatbotP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        p.nombre_producto, 
        pre.nombre_presentacion, 
        (p.precio_producto * pre.cantidad_pmp) as precio_calculado, 
        p.stock_producto
      ",
      'tabla' => 'productos AS p',
      'datosJoins' => [
        'presentaciones_productos AS pp' => 'p.id_producto = pp.id_producto',
        'presentaciones AS pre' => 'pp.id_presentacion = pre.id_presentacion'
      ],
      'WHERE' => ['p.status' => 1]
    ]);
    return ($resultado && $resultado->rowCount() > 0) ? $resultado->fetchAll(\PDO::FETCH_ASSOC) : [];
  }
  private function registrarProductosP() {
    $objBitacora = new bitacoraModelo();
    $arrayImg = [];
    $funcionError = function () use ($objBitacora, $arrayImg) {
      $this->rollback();
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'productos',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($arrayImg != []) {
        foreach ($arrayImg as $nombreImagen) {
          $this->Imagenes_Eli2('presentaciones_productos', $nombreImagen);
        }
      }
      if ($rb) return $rb;
    };
    $idProducto = $this->generarCodSeg([
      'tablaBD' => 'productos',
      'prefijo' => 'PROD',
      'campoID' => 'id_producto'
    ]);
    // Registro de los metadatos
    $resultado = $this->guardarDatos2([
      'tabla' => 'productos',
      'datos' => [
        'id_producto' => $idProducto,
        "id_unidad_medida" => $this->idUnidadMedida,
        "id_categoria_producto" => $this->idCategoria,
        "nombre_producto" => $this->nombreProducto,
        "precio_producto" => $this->precioProducto,
        "stock_producto" => $this->stockProducto,
        "stock_minimo_producto" => $this->stockMinimoProducto,
      ],
    ]);
    if ($resultado == false || $resultado <= 0) {
      $funcionError();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No registrado en BD',
        'icono' => 'error'
      ];
    }

    //Presentaciones
    foreach ($this->presentaciones as $pre) {
      //Imagen
      $nombreImagen = false;
      if (isset($pre['foto_presentacion']) && $pre['foto_presentacion'] != '') {
        $arrayImg[] = $nombreImagen = $this->Imagenes_Reg(
          'presentaciones_productos',
          $pre['foto_presentacion'],
          'presentaciones_productos'
        );
      }
      $idPresentacion = $this->generarCodSeg([
        'tablaBD' => 'presentaciones_productos',
        'prefijo' => 'PRPR',
        'campoID' => 'id_presentacion_producto'
      ]);

      $idPre = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_presentacion_producto" => $idPresentacion,
          "id_producto" => $idProducto,
          "id_presentacion" => $pre['id_presentacion'],
          "mostrar_ecommerce" => $pre['mostrar_ecommerce'] ?? 0,
          "foto_presentacion" => $nombreImagen ?? '',
        ]
      ]);
      if ($idPre == false || $idPre <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se pudo registrar las presentaciones del producto',
          'icono' => 'error'
        ];
      }
    }

    //Materias primas
    $objCategorias = new categoriasProductosModelo();
    $categoriaBD = $objCategorias->seleccionarCategorias(['id_categoria_producto' => $this->idCategoria]);
    if ($categoriaBD['necesitan_materias_primas'] == 1) {
      $this->materiasPrimas = $this->indexarArrays([
        'indice' => 'id_materia_prima',
        'camposSumar' => 'cantidad_materia_prima',
        'array' => $this->materiasPrimas,
      ]);
      foreach ($this->materiasPrimas as $idMP => $cantidadMP) {
        $idMp = $this->guardarDatos2([
          'tabla' => 'materias_primas_productos',
          'datos' => [
            "id_producto" => $idProducto,
            "id_materia_prima" => $idMP,
            "cantidad_materia_prima" => $cantidadMP,
          ]
        ]);
        if ($idMp == false || $idMp <= 0) {
          $funcionError();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se pudieron registrar las materias primas del producto',
            'icono' => 'error'
          ];
        }
      }
    }


    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'productos',
      'accion' => 'Registrar',
      'resultado' => 'Éxito',
      'nuevo' => $this->seleccionarProductos(['id_producto' => $idProducto])
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $resultado = $objNot->enviarMensajesWS([
      [
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['productos' => ['ver']]
        ],
        'cuerpo' => [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Productos',
            'texto' => "Se ha registrado un nuevo producto",
            'icono' => 'info',
            'notifier' => true,
          ]
        ], 
        'noCommit' => true
      ],
      [
        "receptor" => [
          'tipo' => 'todos',
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'productos'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'productos'
          ],
        ],
        'noCommit' => true
      ]
    ]);
    if (isset($r['error'])) {
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
      "titulo" => "Producto registrado",
      "texto" => "Exitoso",
      "icono" => "success"
    ];
  }
  private function actualizarProductosP() {
    $PRD = 0;
    $MPR = 0;
    $PRE = 0;
    $objBitacora = new bitacoraModelo();
    $imagenesPresentaciones = [];
    $datosViejos = $this->seleccionarProductos(['id_producto' => $this->idProducto]);

    $funcionError = function () use ($objBitacora, $imagenesPresentaciones) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'productos',
        'accion' => 'Actualizar',
        'resultado' => 'Éxito',
        'commit' => true
      ]);
      if ($imagenesPresentaciones != []) {
        foreach ($imagenesPresentaciones as $i) {
          $this->Imagenes_Eli2('presentaciones_productos', $i);
        }
      }
    };

    $productoActual = $this->seleccionarProductos(['id_producto' => $this->idProducto]);

    //Datos generales
    $resultado = $this->actualizarDatos2([
      "tabla" => "productos",
      "datos" => [
        "id_unidad_medida" => $this->idUnidadMedida,
        "id_categoria_producto" => $this->idCategoria,
        "nombre_producto" => $this->nombreProducto,
        "precio_producto" => $this->precioProducto,
        "stock_producto" => $this->stockProducto,
        "stock_minimo_producto" => $this->stockMinimoProducto,
      ],
      "WHERE" => [
        "id_producto" => $this->idProducto,
      ]
    ]);
    if ($resultado != false && $resultado > 0) $PRD++;

    //Materias primas
    $this->materiasPrimas = $this->indexarArrays([
      'indice' => 'id_materia_prima',
      'camposSumar' => 'cantidad_materia_prima',
      'array' => $this->materiasPrimas,
    ]);
    if (($productoActual['detallesExtra']['materias_primas'] ?? []) != []) {
      $MPR += $resultado = $this->eliminarDatos2([
        'tabla' => "materias_primas_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Materias primas anteriores no eliminadas',
          'texto' => 'No se pudo actualizar el producto',
          'icono' => 'error',
        ];
      }
    }
    foreach ($this->materiasPrimas as $id => $cantidad) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'materias_primas_productos',
        'datos' => [
          "id_producto" => $this->idProducto,
          "id_materia_prima" => $id,
          "cantidad_materia_prima" => $cantidad,
        ]
      ]);
      if ($resultado != false && $resultado > 0) $MPR++;
    }

    //Presentaciones
    if (isset($productoActual['detallesExtra']['presentaciones'])) {
      $PRE += $resultado = $this->eliminarDatos2([
        'tabla' => "presentaciones_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Presentaciones anteriores no eliminadas',
          'texto' => 'No se pudo actualizar el producto',
          'icono' => 'error',
        ];
      }
      foreach ($productoActual['detallesExtra']['presentaciones'] as $pre) {
        if ($pre['foto_presentacion'] != '') {
          $this->Imagenes_Eli2('presentaciones_productos', $pre['foto_presentacion']);
        }
      }
    }

    foreach ($this->presentaciones as $pres) {
      $foto = '';

      if (isset($pres['foto_presentacion'])) {
        $imagenesPresentaciones[] = $foto = $this->Imagenes_Reg('presentaciones_productos', $pres['foto_presentacion'], 'presentaciones_productos');
      }
      $id_presentacion_producto = $this->generarCodSeg([
        'tablaBD' => 'presentaciones_productos',
        'prefijo' => 'PRPR',
        'campoID' => 'id_presentacion_producto'
      ]);
      $resultado = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_presentacion_producto" => $id_presentacion_producto,
          "id_producto" => $this->idProducto,
          "id_presentacion" => $pres['id_presentacion'],
          "mostrar_ecommerce" => $pres['mostrar_ecommerce'] ?? 0,
          "foto_presentacion" => $foto,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
      } else {
        $PRE++;
      }
    }

    if ($PRD == 0 && $PRE == 0 && $MPR == 0) {
      $funcionError();
      return [
        'icono' => 'warning',
        'titulo' => 'Sin Modificaciones',
        'texto' => 'No se detectaron cambios',
        'tipo' => 'simple'
      ];
    }

    $datosNuevos = $this->seleccionarProductos(['id_producto' => $this->idProducto]);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'productos',
      'accion' => 'Actualizar',
      'resultado' => 'Éxito',
      'viejo' => $datosViejos,
      'nuevo' => $datosNuevos
    ]);
    if ($rb) {
      $funcionError();
      return $rb;
    }

    $objNot = new mensajesWSModelo();
    $resultado = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'productos'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'productos'
        ],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])) {
      $funcionError();
      return $resultado;
    };

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Producto actualizado",
      "texto" => "Exitoso",
      "icono" => "success"
    ];
  }
  private function eliminarProductosP() {
    $objBitacora = new bitacoraModelo();
    $funcionError = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'productos',
        'accion' => 'Eliminar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
    };

    $productoActual = $this->seleccionarProductos([
      'id_producto' => $this->idProducto,
    ]);

    //Presentaciones
    $resultado = $this->eliminarDatos2([
      'tabla' => "presentaciones_productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando las presentaciones del producto',
        'icono' => 'error',
      ];
    }

    //Materias Primas
    if (count($productoActual['detallesExtra']['materias_primas']) > 0) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "materias_primas_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error eliminando las materias primas asociadas al producto',
          'icono' => 'error',
        ];
      }
    }

    //Historial de precios
    $resultado = $this->eliminarDatos2([
      'tabla' => "precios_productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el historial de precios del producto',
        'icono' => 'error',
      ];
    }

    //El producto
    $resultado = $this->eliminarDatos2([
      'tabla' => "productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el producto',
        'icono' => 'error',
      ];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'productos',
      'accion' => 'Eliminar',
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $rb;

    // Fotos de las presentaciones
    foreach ($productoActual['detallesExtra']['presentaciones'] as $presentacion) {
      $this->Imagenes_Eli2('presetaciones_productos', $presentacion['foto_presentacion']);
    }

    $objNot = new mensajesWSModelo();
    $resultado = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'productos'],
        ['accion' => "actDT", 'modulo' => 'productos'],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])) {
      $funcionError();
      return $resultado;
    };

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Eliminado",
      "texto" => "El producto ha sido eliminado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarFotPreProdP() {
    $fotoVieja = $this->seleccionarDatos2([
      'campos' => 'foto_presentacion',
      'tabla' => 'presentaciones_productos',
      'WHERE' => [
        'id_presentacion_producto' => $this->idPresentacionProd
      ]
    ])->fetch(PDO::FETCH_COLUMN);
    $resultado = $this->Imagenes_Act([
      'subCarpeta' => 'presentaciones_productos',
      'imagen' => $this->fotoPresentacion,
      'tablaBD' => 'presentaciones_productos',
      'nombreCampoFoto' => 'foto_presentacion',
      'nombreCampoId' => 'id_presentacion_producto',
      'valorId' => $this->idPresentacionProd,
    ]);
    if ($resultado['icono'] != 'success') return $resultado;

    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'presentaciones_productos',
      'accion' => 'Actualizar foto de la presentacion (' . $this->idPresentacionProd . ')',
      'resultado' => 'Éxito',
      'viejo' => ['foto_presentacion' => $fotoVieja],
      'nuevo' => ['foto_presentacion' => $resultado['nuevaImagen']],
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'productos'],
        ['accion' => "actDT", 'modulo' => 'productos'],
      ],
      'noCommit' => true
    ]);
    if (isset($r['error'])) return $r;

    $this->commit();
    return $resultado;
  }
  private function eliminarFotPreProdP() {
    $resultado = $this->Imagenes_Eli([
      'subCarpeta' => 'presentaciones_productos',
      'tablaBD' => 'presentaciones_productos',
      'nombreCampoFoto' => 'foto_presentacion',
      'nombreCampoId' => 'id_presentacion_producto',
      'valorId' => $this->idPresentacionProd,
    ]);
    if (($resultado['icono'] ?? 'error') == 'error') return $resultado;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'productos'],
        ['accion' => "actDT", 'modulo' => 'productos'],
      ],
      'noCommit' => true
    ]);
    if (isset($resultado['error'])) {
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
}
