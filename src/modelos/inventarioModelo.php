<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\pdfModel;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;
use PDO;

class inventarioModelo extends conexion {
  private string $idProducto = '';
  private string $idMateriaPrima = '';
  private string $idPresentacion = '';
  private float $cantidadMovimiento = 0;
  private int $tipoMovimiento = 0;
  private string $motivoMovimiento = '';
  private string $tipo = '';
  private string $tipoItem = '';
  private array $info = [];

  public function validarInventario(string $permiso, array $instruccionesVal) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('inventario', $permiso);
    if ($r) return $r;

    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $arrayValidaciones = [
      "id_producto" => [
        "campo_nombre" => "id_producto",
        "formulario_nombre" => "id del producto",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "expresion_re" => regexIdSeguro,
        "tabla" => "productos",
        "debeExistir" => true,
      ],
      "id_materia_prima" => [
        "campo_nombre" => "id_materia_prima",
        "formulario_nombre" => "id de la materia prima",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "expresion_re" => regexIdSeguro,
        "tabla" => "materias_primas",
        "debeExistir" => true,
      ],
      "id_presentacion_producto" => [
        "campo_nombre" => "id_presentacion_producto",
        "formulario_nombre" => "presentación del producto",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "tabla" => "presentaciones_productos",
        "debeExistir" => true,
      ],
      "cantidad_movimiento" => [
        "campo_nombre" => "cantidad_movimiento",
        "formulario_nombre" => "cantidad del movimiento",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "comaPunto" => true,
      ],
      "tipo_movimiento" => [
        "campo_nombre" => "tipo_movimiento",
        "formulario_nombre" => "tipo del movimiento",
        "requerido" => true,
        "minimo" => minRegexValorBoleano,
        "maximo" => maxRegexValorBoleano,
        "expresion_re" => regexValorBoleano,
      ],
      "motivo_movimiento" => [
        "campo_nombre" => "motivo_movimiento",
        "formulario_nombre" => "motivo del movimiento",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      "fecha_desde" => [
        "campo_nombre" => "fecha_desde",
        "formulario_nombre" => "fecha desde",
        "requerido" => true,
      ],
      "fecha_hasta" => [
        "campo_nombre" => "fecha_hasta",
        "formulario_nombre" => "fecha hasta",
        "requerido" => true,
      ],
    ];

    $totalValidaciones = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $validacion = $arrayValidaciones[$campo];
      $validacion['campo_valor'] = &$infoVal[$campo];
      $totalValidaciones[] = $validacion;
    }
    return $this->limpiar_Verificar($totalValidaciones);
  }
  public function registrarMovimientos(array $info) {
    $this->tipoItem = $info['tipo_item'] ?? '';

    if ($this->tipoItem === 'materia_prima') {
      $resultado = $this->validarInventario('registrar cargas o descargas de materias primas', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_materia_prima',
          'cantidad_movimiento',
          'tipo_movimiento',
          'motivo_movimiento'
        ]
      ]);
    } else {
      $resultado = $this->validarInventario('registrar cargas o descargas de productos', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_presentacion_producto',
          'cantidad_movimiento',
          'tipo_movimiento',
          'motivo_movimiento'
        ],
      ]);
    }

    if ($resultado) return $resultado;

    $this->cantidadMovimiento = (float) $info['cantidad_movimiento'];
    $this->tipoMovimiento = (int) $info['tipo_movimiento'];
    $this->motivoMovimiento = $info['motivo_movimiento'];

    if ($this->tipoItem === 'materia_prima') {
      $this->idMateriaPrima = $info['id_materia_prima'];
      return $this->registrarMovimientosMateriasPrimasP();
    } else {
      $this->idPresentacion = $info['id_presentacion_producto'];
      // Validar materias primas en CARGAS (tipo_movimiento == 1) - se necesita materia prima para producir
      if ($this->tipoMovimiento == 1) {
        $validacionMP = $this->validarStockMateriasPrimas();
        if ($validacionMP !== true) {
          return $validacionMP;
        }
      }
      return $this->registrarMovimientosProductosP();
    }
  }
  public function verEntradasSalidas(array $info) {
    $this->tipo = $info['tipo'] ?? '';

    if ($this->tipo === 'productos') {
      $resultado = $this->validarInventario('ver historial de e/s de los productos', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_producto',
        ]
      ]);
      if ($resultado) return $resultado;
      $this->idProducto = $info['id_producto'];
      return $this->verMovimientosProductosP();
    } else if ($this->tipo === 'materiasPrimas') {
      $resultado = $this->validarInventario('ver historial de e/s de las materias primas', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_materia_prima',
        ]
      ]);
      if ($resultado) return $resultado;
      $this->idMateriaPrima = $info['id_materia_prima'];
      return $this->verMovimientosMateriasPrimasP();
    }

    return [
      "tipo" => "simple",
      "titulo" => "Error",
      "texto" => "Tipo no reconocido. Debe ser 'productos' o 'materiasPrimas'",
      "icono" => "error"
    ];
  }
  public function reporteProductos(array $info) {
    $resultado = $this->validarInventario('ver historial de e/s de los productos', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_producto',
        'fecha_desde',
        'fecha_hasta'
      ]
    ]);
    if ($resultado) return $resultado;

    $this->info = $info;
    $this->info['id_producto'] = $info['id_producto'] ?? null;

    $this->info['fecha_desde'] = $this->fechaHoraSel("fecha_BD", date('d/m/Y', strtotime($info['fecha_desde'])));
    $this->info['fecha_hasta'] = $this->fechaHoraSel("fecha_BD", date('d/m/Y', strtotime($info['fecha_hasta'])));

    if ($this->info['fecha_desde'] > $this->info['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteProductosP();
  }
  public function reporteMateriasPrimas(array $info) {
    $resultado = $this->validarInventario('ver historial de e/s de las materias primas', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_materia_prima',
        'fecha_desde',
        'fecha_hasta'
      ]
    ]);
    if ($resultado) return $resultado;

    $this->info = $info;
    $this->info['id_materia_prima'] = $info['id_materia_prima'] ?? null;

    $this->info['fecha_desde'] = $this->fechaHoraSel("fecha_BD", date('d/m/Y', strtotime($info['fecha_desde'])));
    $this->info['fecha_hasta'] = $this->fechaHoraSel("fecha_BD", date('d/m/Y', strtotime($info['fecha_hasta'])));

    if ($this->info['fecha_desde'] > $this->info['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteMateriasPrimasP();
  }

  //  METODOS PRIVADOS
  private function validarStockMateriasPrimas() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_producto',
      'tabla' => 'presentaciones_productos',
      'WHERE' => [
        "id_presentacion_producto" => $this->idPresentacion,
      ]
    ]);
    if ($resultado->rowCount() <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Presentación no encontrada",
        "icono" => "error"
      ];
    }

    $idProducto = $resultado->fetch(PDO::FETCH_COLUMN);

    $resultado = $this->seleccionarDatos2([
      'campos' => 'mpp.cantidad_materia_prima, mp.nombre_materia_prima, mp.stock_materia_prima, mp.id_materia_prima',
      'tabla' => 'materias_primas_productos as mpp',
      'PEL' => 'mpp',
      'datosJoins' => [
        "materias_primas as mp" => "mpp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => [
        "mpp.id_producto" => $idProducto,
        "mpp.status" => 1
      ]
    ]);
    $materiasPrimas = $resultado->fetchAll();

    if (count($materiasPrimas) == 0) return true;

    $presentacionInfo = $this->seleccionarDatos2([
      'campos' => 'p.cantidad_pmp',
      'tabla' => 'presentaciones_productos as pp',
      'datosJoins' => [
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion"
      ],
      'WHERE' => [
        "pp.id_presentacion_producto" => $this->idPresentacion,
      ]
    ])->fetch();

    $cantidadPMP = $presentacionInfo['cantidad_pmp'] ?? 1;

    foreach ($materiasPrimas as $mp) {
      $cantidadNecesaria = $this->cantidadMovimiento * ($mp['cantidad_materia_prima'] * $cantidadPMP);

      if ($mp['stock_materia_prima'] < $cantidadNecesaria) {
        return [
          "tipo" => "simple",
          "titulo" => "Stock de materia prima insuficiente",
          "texto" => "No hay suficiente stock de la materia prima: " . $mp['nombre_materia_prima'] .
            " (Stock: " . $mp['stock_materia_prima'] . ", Necesario: " . $cantidadNecesaria . ")",
          "icono" => "error"
        ];
      }
    }
    return true;
  }
  private function registrarMovimientosProductosP() {
    $objBitacora = new bitacoraModelo();

    $resultado = $this->seleccionarDatos2([
      'campos' => '
        pp.id_producto, pp.id_presentacion_producto, pr.id_categoria_producto, 
        pr.nombre_producto, pr.stock_producto, pr.stock_minimo_producto,
        p.nombre_presentacion, p.cantidad_pmp
      ',
      'tabla' => 'presentaciones_productos as pp',
      'datosJoins' => [
        "productos as pr" => "pp.id_producto = pr.id_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion"
      ],
      'WHERE' => [
        "pp.id_presentacion_producto" => $this->idPresentacion,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Producto',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Presentación no encontrada",
        "texto" => "La presentación seleccionada no existe",
        "icono" => "error"
      ];
    }

    $info = $resultado->fetch(PDO::FETCH_ASSOC);
    $idProducto = $info['id_producto'];
    $nombreProducto = $info['nombre_producto'];
    $nombrePresentacion = $info['nombre_presentacion'];
    $stockActual = $info['stock_producto'];
    $cantidadPMP = $info['cantidad_pmp'];

    $nuevoStock = ($this->tipoMovimiento == 1) ? $stockActual + $this->cantidadMovimiento : $stockActual - $this->cantidadMovimiento;

    if ($this->tipoMovimiento == 0 && $nuevoStock < 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Producto',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Stock insuficiente",
        "texto" => "No hay suficiente stock del producto",
        "icono" => "error"
      ];
    }

    // Registrar movimiento
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'movimientos_anomalos_productos',
      'datos' => [
        "id_presentacion_producto" => $this->idPresentacion,
        "cantidad_movimiento" => $this->cantidadMovimiento,
        "tipo_movimiento" => $this->tipoMovimiento,
        "motivo_movimiento" => $this->motivoMovimiento,
        "fecha_movimiento" => $this->fechaHoraSel('fecha_hora_BD'),
      ]
    ]);

    if ($ultimoId === false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Producto',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Movimiento no registrado",
        "texto" => "El movimiento no ha podido ser registrado",
        "icono" => "error"
      ];
    }

    $materiasPrimas = $this->seleccionarDatos2([
      'campos' => 'mpp.id_materia_prima, mpp.cantidad_materia_prima',
      'tabla' => 'materias_primas_productos as mpp',
      'WHERE' => [
        "mpp.id_producto" => $idProducto,
        "mpp.status" => 1
      ]
    ])->fetchAll();

    foreach ($materiasPrimas as $mp) {
      $cantidadAjuste = $this->cantidadMovimiento * ($mp['cantidad_materia_prima'] * $cantidadPMP);

      $stockMP = $this->seleccionarDatos2([
        'campos' => 'stock_materia_prima',
        'tabla' => 'materias_primas',
        'WHERE' => [
          'id_materia_prima' => $mp['id_materia_prima']
        ]
      ])->fetch(PDO::FETCH_COLUMN);

      $nuevoStockMP = ($this->tipoMovimiento == 1)
        ? $stockMP - $cantidadAjuste
        : $stockMP + $cantidadAjuste;

      $this->actualizarDatos2([
        'tabla' => 'materias_primas',
        'datos' => [
          'stock_materia_prima' => $nuevoStockMP
        ],
        'WHERE' => [
          'id_materia_prima' => $mp['id_materia_prima']
        ]
      ]);
    }
    // Actualizar stock del producto
    $this->actualizarDatos2([
      'tabla' => 'productos',
      'datos' => ['stock_producto' => $nuevoStock],
      'WHERE' => ['id_producto' => $idProducto]
    ]);

    $movimientoRegistrado = $this->seleccionarDatos2([
      'campos' => '
          map.id_movimiento_anomalo_producto,
          p.nombre_presentacion,
          map.cantidad_movimiento,
          map.tipo_movimiento,
          map.motivo_movimiento,
          map.fecha_movimiento,
          pr.nombre_producto
        ',
      'tabla' => 'movimientos_anomalos_productos as map',
      'datosJoins' => [
        "presentaciones_productos as pp" => "map.id_presentacion_producto = pp.id_presentacion_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion",
        "productos as pr" => "pp.id_producto = pr.id_producto"
      ],
      'WHERE' => [
        "map.id_movimiento_anomalo_producto" => $ultimoId
      ]
    ])->fetch();

    $objBitacora->registrarBitacora([
      'modulo' => 'inventario',
      'accion' => 'Registrar Anomalia de Producto',
      'resultado' => 'Éxito',
      'nuevo' => $movimientoRegistrado
    ]);

    $objetoNot = new mensajesWSModelo();
    $tipoTexto = $this->tipoMovimiento == 1 ? 'CARGA' : 'DESCARGA';
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todosSinExcepcion',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'inventario'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'inventario'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Movimiento de inventario',
            'texto' => 'Se ha registrado un movimiento de ' . $tipoTexto . ' de ' . $this->cantidadMovimiento . ' unidades del producto "' . $nombreProducto . '" (' . $nombrePresentacion . ')',
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
      ],
      'noCommit' => true
    ]);

    $this->commit();

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Movimiento registrado",
      "texto" => "Movimiento registrado correctamente",
      "icono" => "success"
    ];
  }
  private function registrarMovimientosMateriasPrimasP() {
    $objBitacora = new bitacoraModelo();

    $resultado = $this->seleccionarDatos2([
      'campos' => 'stock_materia_prima, stock_minimo_materia_prima, nombre_materia_prima',
      'tabla' => 'materias_primas',
      'WHERE' => [
        "id_materia_prima" => $this->idMateriaPrima,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Materia Prima',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Materia prima no encontrada",
        "texto" => "La materia prima seleccionada no existe",
        "icono" => "error"
      ];
    }

    $materiaPrima = $resultado->fetch();
    $stockActual = $materiaPrima['stock_materia_prima'];
    $nombreMP = $materiaPrima['nombre_materia_prima'];

    $nuevoStock = ($this->tipoMovimiento == 1) ? $stockActual + $this->cantidadMovimiento : $stockActual - $this->cantidadMovimiento;

    if ($this->tipoMovimiento == 0 && $nuevoStock < 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Materia Prima',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Stock insuficiente",
        "texto" => "No hay suficiente stock de la materia prima",
        "icono" => "error"
      ];
    }

    // Registrar movimiento
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'movimientos_anomalos_materias_primas',
      'datos' => [
        "id_materia_prima" => $this->idMateriaPrima,
        "cantidad_movimiento" => $this->cantidadMovimiento,
        "tipo_movimiento" => $this->tipoMovimiento,
        "motivo_movimiento" => $this->motivoMovimiento,
        "fecha_movimiento" => $this->fechaHoraSel('fecha_hora_BD'),
      ]
    ]);

    if ($ultimoId === false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'inventario',
        'accion' => 'Registrar Anomalia de Materia Prima',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Movimiento no registrado",
        "texto" => "El movimiento no ha podido ser registrado",
        "icono" => "error"
      ];
    }

    // Actualizar stock de la materia prima
    $this->actualizarDatos2([
      'tabla' => 'materias_primas',
      'datos' => ['stock_materia_prima' => $nuevoStock],
      'WHERE' => ['id_materia_prima' => $this->idMateriaPrima]
    ]);

    $movimientoRegistrado = $this->seleccionarDatos2([
      'campos' => '
          mamp.id_movimiento_anomalo_materia_prima,
          mp.nombre_materia_prima,
          mamp.cantidad_movimiento,
          mamp.tipo_movimiento,
          mamp.motivo_movimiento,
          mamp.fecha_movimiento
        ',
      'tabla' => 'movimientos_anomalos_materias_primas as mamp',
      'datosJoins' => [
        "materias_primas as mp" => "mamp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => [
        "mamp.id_movimiento_anomalo_materia_prima" => $ultimoId
      ]
    ])->fetch();

    $objBitacora->registrarBitacora([
      'modulo' => 'inventario',
      'accion' => 'Registrar Anomalia de Materia Prima',
      'resultado' => 'Éxito',
      'nuevo' => $movimientoRegistrado
    ]);

    $objetoNot = new mensajesWSModelo();
    $tipoTexto = $this->tipoMovimiento == 1 ? 'CARGA' : 'DESCARGA';
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todosSinExcepcion',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'inventario'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'inventario'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Movimiento de inventario',
            'texto' => 'Se ha registrado un movimiento de ' . $tipoTexto . ' de ' . $this->cantidadMovimiento . ' unidades de la materia prima "' . $nombreMP . '"',
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
      ],
      'noCommit' => true
    ]);
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Movimiento registrado",
      "texto" => "Movimiento registrado correctamente",
      "icono" => "success"
    ];
  }
  private function verMovimientosProductosP() {
    return $this->seleccionarDatos2([
      'campos' => 'map.id_movimiento_anomalo_producto, p.nombre_presentacion, map.cantidad_movimiento, map.tipo_movimiento, map.motivo_movimiento, map.fecha_movimiento, pr.id_producto, pr.nombre_producto',
      'tabla' => 'movimientos_anomalos_productos as map',
      'PEL' => 'map',
      'datosJoins' => [
        "presentaciones_productos as pp" => "map.id_presentacion_producto = pp.id_presentacion_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion",
        "productos as pr" => "pp.id_producto = pr.id_producto"
      ],
      'WHERE' => [
        "pp.id_producto" => $this->idProducto,
      ],
      'ORDER' => 'map.id_movimiento_anomalo_producto DESC'
    ])->fetchAll();
  }
  private function verMovimientosMateriasPrimasP() {
    return $this->seleccionarDatos2([
      'campos' => '
        mamp.id_movimiento_anomalo_materia_prima, 
        mp.nombre_materia_prima, mamp.cantidad_movimiento,
        mamp.tipo_movimiento, mamp.motivo_movimiento, 
        mamp.fecha_movimiento
      ',
      'tabla' => 'movimientos_anomalos_materias_primas as mamp',
      'datosJoins' => [
        "materias_primas as mp" => "mamp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => [
        "mp.id_materia_prima" => $this->idMateriaPrima,
      ],
      'ORDER' => 'mamp.id_movimiento_anomalo_materia_prima DESC'
    ])->fetchAll();
  }
  private function reporteProductosP() {
    $conexion = $this->conectar();
    
    $sql = "
        SELECT 
            map.id_movimiento_anomalo_producto, 
            p.nombre_presentacion,
            map.cantidad_movimiento,
            map.tipo_movimiento,
            map.motivo_movimiento,
            map.fecha_movimiento,
            pr.nombre_producto
        FROM movimientos_anomalos_productos as map
        INNER JOIN presentaciones_productos as pp ON map.id_presentacion_producto = pp.id_presentacion_producto
        INNER JOIN presentaciones as p ON pp.id_presentacion = p.id_presentacion
        INNER JOIN productos as pr ON pp.id_producto = pr.id_producto
        WHERE map.status = 1
        AND DATE(map.fecha_movimiento) >= :fecha_desde
        AND DATE(map.fecha_movimiento) <= :fecha_hasta
    ";
    
    $params = [
        ':fecha_desde' => $this->info['fecha_desde'],
        ':fecha_hasta' => $this->info['fecha_hasta']
    ];
    
    if (!empty($this->info['id_producto'])) {
        $sql .= " AND pp.id_producto = :id_producto";
        $params[':id_producto'] = $this->info['id_producto'];
    }
    
    $sql .= " ORDER BY map.fecha_movimiento ASC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $infoCeldas = $stmt->fetchAll();

    if (empty($infoCeldas)) {
        return [
            'tipo' => 'simple',
            'titulo' => 'Sin registros',
            'texto' => 'No hay movimientos en el rango de fechas seleccionado',
            'icono' => 'warning',
        ];
    }

    foreach ($infoCeldas as &$fila) {
        $fila['fecha_movimiento'] = $this->fechaHoraSel('fecha_hora_AM_PM', $fila['fecha_movimiento']);
        $fila['tipo_movimiento'] = $fila['tipo_movimiento'] == 1 ? 'CARGA' : 'DESCARGA';
    }
    unset($fila);

    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MOVIMIENTOS');

    return $objetoPDF->crearPDF([
        "tituloReporte" => "REPORTE DE MOVIMIENTOS",
        "datosExtCabecera" => [
            "PRODUCTOS Desde: " . date('d-m-Y', strtotime($this->info['fecha_desde'])) . " Hasta: " . date('d-m-Y', strtotime($this->info['fecha_hasta']))
        ],
        "configColumnas" => [
            'id_movimiento_anomalo_producto' => ['ID', 10],
            'nombre_producto' => ['PRODUCTO', 35],
            'nombre_presentacion' => ['PRESENTACION', 35],
            'tipo_movimiento' => ['TIPO', 25],
            'cantidad_movimiento' => ['CANT', 20],
            'motivo_movimiento' => ['MOTIVO', 30],
            'fecha_movimiento' => ['FECHA', 35],
        ],
        "infoBD" => $infoCeldas,
    ]);
  }
  private function reporteMateriasPrimasP() {
    $conexion = $this->conectar();

    $sql = "
        SELECT 
            mamp.id_movimiento_anomalo_materia_prima,
            mp.nombre_materia_prima,
            mamp.cantidad_movimiento,
            mamp.tipo_movimiento,
            mamp.motivo_movimiento,
            mamp.fecha_movimiento
        FROM movimientos_anomalos_materias_primas as mamp
        INNER JOIN materias_primas as mp ON mamp.id_materia_prima = mp.id_materia_prima
        WHERE mamp.status = 1
        AND DATE(mamp.fecha_movimiento) >= :fecha_desde
        AND DATE(mamp.fecha_movimiento) <= :fecha_hasta
    ";
    
    $params = [
        ':fecha_desde' => $this->info['fecha_desde'],
        ':fecha_hasta' => $this->info['fecha_hasta']
    ];
    
    if (!empty($this->info['id_materia_prima'])) {
        $sql .= " AND mp.id_materia_prima = :id_materia_prima";
        $params[':id_materia_prima'] = $this->info['id_materia_prima'];
    }
    
    $sql .= " ORDER BY mamp.fecha_movimiento ASC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $infoCeldas = $stmt->fetchAll();

    if (empty($infoCeldas)) {
        return [
            'tipo' => 'simple',
            'titulo' => 'Sin registros',
            'texto' => 'No hay movimientos en el rango de fechas seleccionado',
            'icono' => 'warning',
        ];
    }

    foreach ($infoCeldas as &$fila) {
        $fila['fecha_movimiento'] = $this->fechaHoraSel('fecha_hora_AM_PM', $fila['fecha_movimiento']);
        $fila['tipo_movimiento'] = $fila['tipo_movimiento'] == 1 ? 'CARGA' : 'DESCARGA';
    }
    unset($fila);

    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MOVIMIENTOS ');

    return $objetoPDF->crearPDF([
        "tituloReporte" => "REPORTE DE MOVIMIENTOS",
        "datosExtCabecera" => [
            "MATERIAS PRIMAS Desde: " . date('d-m-Y', strtotime($this->info['fecha_desde'])) . " Hasta: " . date('d-m-Y', strtotime($this->info['fecha_hasta']))
        ],
        "configColumnas" => [
            'id_movimiento_anomalo_materia_prima' => ['ID', 15],
            'nombre_materia_prima' => ['MATERIA PRIMA', 40],
            'tipo_movimiento' => ['TIPO', 25],
            'cantidad_movimiento' => ['CANT', 25],
            'motivo_movimiento' => ['MOTIVO', 45],
            'fecha_movimiento' => ['FECHA', 40],
        ],
        "infoBD" => $infoCeldas,
    ]);
  }
}