<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;
use src\modelos\pdfModel;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use PDO;

class comprasModelo extends conexion {
  private string|null $id_compra = '';
  private string $rif_proveedor = '';
  private string $fecha_compra = '';
  private array $detalles = [];

  // Metodos publicos
  public function validarCompras(string $permiso, ?array $instruccionesVal = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('compras', $permiso);
    if ($v) return $v;

    if ($instruccionesVal === null) return false;

    [
      'infoVal'   => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $arrayValidaciones = [
      "id_compra" => [
        "campo_nombre"      => "id_compra",
        "formulario_nombre" => "ID de Compra",
        "requerido"         => true,
        "minimo"            => minRegexId,
        "maximo"            => maxRegexId,
        "expresion_re"      => regexId,
        "tabla"             => "compras",
        "debeExistir"       => true,
      ],
      "rif_proveedor" => [
        "campo_nombre"      => "rif_proveedor",
        "formulario_nombre" => "RIF del Proveedor",
        "requerido"         => true,
        "minimo"            => minRegexCedulaRifLetra,
        "maximo"            => maxRegexCedulaRifLetra,
        "expresion_re"      => regexCedulaRifLetra,
      ],
      "fecha_compra" => [
        "campo_nombre"      => "fecha_compra",
        "formulario_nombre" => "Fecha de Compra",
        "requerido"         => true,
      ],
      "cantidad_item" => [
        "campo_nombre"      => "cantidad",
        "formulario_nombre" => "Cantidad del artículo",
        "requerido"         => true,
        "minimo"            => minRegexCantidadItem,
        "maximo"            => maxRegexCantidadItem,
        "expresion_re"      => regexCantidadItem,
        "comaPunto"         => true,
        "noCero"            => true,
      ]
    ];

    $totalValidaciones = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $validacion = [];
      switch ($campo) {
        case 'id_compra':
          $validacion = $arrayValidaciones['id_compra'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
        case 'rif_proveedor':
          $validacion = $arrayValidaciones['rif_proveedor'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          $validacion['tabla']       = 'proveedores';
          $validacion['debeExistir'] = true;
          break;
        case 'fecha_compra':
          $validacion = $arrayValidaciones['fecha_compra'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
        case 'cantidad_item':
          $validacion = $arrayValidaciones['cantidad_item'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
      }
      if (!empty($validacion)) {
        $totalValidaciones[] = $validacion;
      }
    }
    return $this->limpiar_Verificar($totalValidaciones);
  }
  public function seleccionarCompra(array $info = []) {
    $this->id_compra = $info['id_compra'] ?? null;

    // Si hay ID validamos que exista y validamos permisos de ver
    if (!empty($this->id_compra)) {
      $infoVal = ['id_compra' => &$this->id_compra];
      $alerta = $this->validarCompras('ver', [
        'infoVal'   => &$infoVal,
        'camposVal' => ['id_compra']
      ]);
      if ($alerta !== false) return $alerta;
    } else {
      $alerta = $this->validarCompras('ver');
      if ($alerta !== false) return $alerta;
    }

    return $this->seleccionarCompraP();
  }
  public function registrarCompra(array $info) {
    $rif_proveedor = $info['rif_proveedor'] ?? '';
    $fecha_compra  = $info['fecha_compra']  ?? '';
    $detalles = [];
    if (!empty($info['detalles'])) {
      $decoded = json_decode($info['detalles'], true);
      if (is_array($decoded)) {
        $detalles = $decoded;
      }
    } elseif (!empty($info['detalle_id']) && is_array($info['detalle_id'])) {
      foreach ($info['detalle_id'] as $index => $id) {
        $detalles[] = [
          'proveedorId' => $info['detalle_proveedorId'][$index] ?? '',
          'tipo'        => $info['detalle_tipo'][$index] ?? '',
          'id'          => $id,
          'cantidad'    => $info['detalle_cantidad'][$index] ?? 1,
          'precio'      => $info['detalle_precio'][$index] ?? 0
        ];
      }
    }

    // Normalizar fecha para MySQL
    $fecha_compra = str_replace('T', ' ', $fecha_compra);
    if (strlen($fecha_compra) === 16) $fecha_compra .= ':00';

    // Si el RIF viene vacio lo sacamos del detalle
    if (empty($rif_proveedor) && !empty($detalles)) {
      $rif_proveedor = $detalles[0]['proveedorId'] ?? '';
    }

    // Validar cabecera y permisos de registrar
    $infoVal = [
      'rif_proveedor' => &$rif_proveedor,
      'fecha_compra'  => &$fecha_compra,
    ];
    $alerta = $this->validarCompras('registrar', [
      'infoVal'   => &$infoVal,
      'camposVal' => ['rif_proveedor', 'fecha_compra']
    ]);
    if ($alerta !== false) return $alerta;

    // Validar que haya al menos un ítem
    if (empty($detalles)) {
      return [
        "tipo"   => "simple",
        "titulo" => "Sin artículos",
        "texto"  => "Debe agregar al menos un artículo a la compra",
        "icono"  => "warning"
      ];
    }

    // Validar tipo, cantidad y normalizar precio de cada ítem
    foreach ($detalles as &$det) {
      if (!in_array($det['tipo'] ?? '', ['producto', 'materia_prima'])) {
        return [
          "tipo"   => "simple",
          "titulo" => "Tipo de artículo inválido",
          "texto"  => "Solo se permiten productos o materias primas",
          "icono"  => "error"
        ];
      }
      $infoVal = ['cantidad' => &$det['cantidad']];
      $alerta = $this->validarCompras('registrar', [
        'infoVal'   => &$infoVal,
        'camposVal' => ['cantidad_item' => 'cantidad']
      ]);
      if ($alerta !== false) return $alerta;

      $precio = (float) str_replace(',', '.', (string)($det['precio'] ?? $det['costo_compra'] ?? $det['precio_compra'] ?? 0));
      $det['precio'] = max(0, $precio);
    }
    unset($det);

    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra  = $fecha_compra;
    $this->detalles      = $detalles;

    return $this->registrarCompraP();
  }
  public function actualizarCompra(array $info) {
    $id_compra     = $info['id_compra']     ?? '';
    $rif_proveedor = $info['rif_proveedor'] ?? '';
    $fecha_compra  = $info['fecha_compra']  ?? '';
    $detalles = [];
    if (!empty($info['detalles'])) {
      $decoded = json_decode($info['detalles'], true);
      if (is_array($decoded)) {
        $detalles = $decoded;
      }
    } elseif (!empty($info['detalle_id']) && is_array($info['detalle_id'])) {
      foreach ($info['detalle_id'] as $index => $id) {
        $detalles[] = [
          'proveedorId' => $info['detalle_proveedorId'][$index] ?? '',
          'tipo'        => $info['detalle_tipo'][$index] ?? '',
          'id'          => $id,
          'cantidad'    => $info['detalle_cantidad'][$index] ?? 1,
          'precio'      => $info['detalle_precio'][$index] ?? 0
        ];
      }
    }

    // Normalizar fecha
    $fecha_compra = str_replace('T', ' ', $fecha_compra);
    if (strlen($fecha_compra) === 16) $fecha_compra .= ':00';

    // Validar cabecera y permisos de actualizar
    $infoVal = [
      'id_compra'     => &$id_compra,
      'rif_proveedor' => &$rif_proveedor,
      'fecha_compra'  => &$fecha_compra,
    ];
    $alerta = $this->validarCompras('actualizar', [
      'infoVal'   => &$infoVal,
      'camposVal' => [
        'id_compra',
        'rif_proveedor',
        'fecha_compra'
      ]
    ]);
    if ($alerta !== false) return $alerta;

    // Validar que la compra no esté ya recepcionada
    $compraExistente = $this->seleccionarCompra(['id_compra' => $id_compra]);
    if (!empty($compraExistente) && isset($compraExistente[0]['status']) && (int)$compraExistente[0]['status'] === 2) {
      return [
        "tipo"   => "simple",
        "titulo" => "Compra recepcionada",
        "texto"  => "Esta compra ya fue marcada como Recibida y no puede ser modificada.",
        "icono"  => "warning"
      ];
    }

    // Validar ítems
    if (empty($detalles)) {
      return [
        "tipo"   => "simple",
        "titulo" => "Sin artículos",
        "texto"  => "Debe incluir al menos un artículo",
        "icono"  => "warning"
      ];
    }

    foreach ($detalles as &$det) {
      if (!in_array($det['tipo'] ?? '', ['producto', 'materia_prima'])) {
        return [
          "tipo"   => "simple",
          "titulo" => "Tipo de artículo inválido",
          "texto"  => "Solo se permiten productos o materias primas",
          "icono"  => "error"
        ];
      }
      $infoVal = ['cantidad' => &$det['cantidad']];
      $alerta = $this->validarCompras('actualizar', [
        'infoVal'   => &$infoVal,
        'camposVal' => ['cantidad_item' => 'cantidad']
      ]);
      if ($alerta !== false) return $alerta;

      $precio = (float) str_replace(',', '.', (string)($det['precio'] ?? $det['costo_compra'] ?? $det['precio_compra'] ?? 0));
      $det['precio'] = max(0, $precio);
    }
    unset($det);

    $this->id_compra     = $id_compra;
    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra  = $fecha_compra;
    $this->detalles      = $detalles;
    return $this->actualizarCompraP();
  }
  public function eliminarCompra(array $info) {
    $id_compra = $info['id_compra'] ?? '';

    // Validar que el ID existe y permisos de eliminar
    $infoVal = ['id_compra' => &$id_compra];
    $alerta = $this->validarCompras('eliminar', [
      'infoVal'   => &$infoVal,
      'camposVal' => ['id_compra']
    ]);
    if ($alerta !== false) return $alerta;

    $this->id_compra = $id_compra;
    return $this->eliminarCompraP();
  }
  public function listarProductosParaCompra(): array {
    $v = $this->validarCompras('ver');
    if ($v) return $v;
    $sql = "
      SELECT
        pp.id_presentacion_producto,
        CONCAT(p.nombre_producto, ' (', pre.nombre_presentacion, ')') AS nombre_producto,
        COALESCE(um_pre.id_unidad_medida, um_prod.id_unidad_medida) AS id_unidad_medida,
        COALESCE(um_pre.nombre_unidad_medida, um_prod.nombre_unidad_medida) AS nombre_unidad_medida,
        COALESCE(um_pre.simbolo_unidad_medida, um_prod.simbolo_unidad_medida) AS simbolo_unidad_medida
      FROM presentaciones_productos pp
      INNER JOIN productos p   ON pp.id_producto   = p.id_producto
      INNER JOIN presentaciones pre ON pp.id_presentacion = pre.id_presentacion
      LEFT  JOIN unidades_medidas um_pre ON pre.id_unidad_medida = um_pre.id_unidad_medida
      LEFT  JOIN unidades_medidas um_prod ON p.id_unidad_medida = um_prod.id_unidad_medida
      WHERE p.status = 1 AND pp.status = 1 AND pre.status = 1
      ORDER BY p.nombre_producto, pre.nombre_presentacion
    ";
    $stmt = $this->conectar()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function cambiarEstadoRecepcion(array $info) {
    $id_compra = $info['id_compra'] ?? '';
    $infoVal = ['id_compra' => &$id_compra];
    $alerta = $this->validarCompras('actualizar', [
      'infoVal'   => &$infoVal,
      'camposVal' => ['id_compra']
    ]);
    if ($alerta !== false) return $alerta;

    $this->id_compra = $id_compra;
    return $this->cambiarEstadoRecepcionP();
  }
  public function imprimirOrdenCompra(array $info) {
    $id_compra = $info['id_compra'] ?? '';
    $infoVal = ['id_compra' => &$id_compra];
    $alerta = $this->validarCompras('ver', [
      'infoVal'   => &$infoVal,
      'camposVal' => ['id_compra']
    ]);
    if ($alerta !== false) return $alerta;

    $this->id_compra = $id_compra;
    return $this->imprimirOrdenCompraP();
  }
  public function imprimirPlanillaRetencion(array $info) {
    $id_compra = $info['id_compra'] ?? '';
    $infoVal = ['id_compra' => &$id_compra];
    $alerta = $this->validarCompras('ver', [
      'infoVal'   => &$infoVal,
      'camposVal' => ['id_compra']
    ]);
    if ($alerta !== false) return $alerta;

    $this->id_compra = $id_compra;
    return $this->imprimirPlanillaRetencionP();
  }
  public function obtenerTasaDolar(): array {
    $v = $this->validarCompras('ver');
    if ($v) return $v;
    $sql = "SELECT valor_moneda, simbolo_moneda FROM monedas WHERE (id_moneda = 1 OR simbolo_moneda = '$' OR nombre_moneda LIKE '%DOLAR%' OR nombre_moneda LIKE '%DÓLAR%') AND status = 1 ORDER BY id_moneda ASC LIMIT 1";
    $stmt = $this->conectar()->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $tasa = $row ? (float) $row['valor_moneda'] : 1.0;
    return ['tasa' => $tasa > 0 ? $tasa : 1.0, 'simbolo' => '$'];
  }

  // Metodos privados (BD)
  private function seleccionarCompraP() {
    // Listado general si no hay ID
    if (empty($this->id_compra)) {
      $sql = "
        SELECT
          c.id_compra,
          c.fecha_compra,
          c.rif_proveedor,
          c.status,
          CASE 
            WHEN c.status = 2 THEN 'Recibido'
            ELSE 'Pendiente por recibir'
          END AS estado_compra,
          COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
          (
            (
              SELECT COUNT(*) 
              FROM productos_compras pc
              WHERE pc.id_compra = c.id_compra
            )+(
              SELECT COUNT(*) 
              FROM materias_primas_compras mpc
              WHERE mpc.id_compra = c.id_compra
            )
          ) AS total_articulos
        FROM compras c
        LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
        WHERE c.status != 0
        ORDER BY CAST(c.id_compra AS UNSIGNED) DESC
      ";
      $stmt = $this->conectar()->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Detalle completo por ID
    $sql = "
            SELECT
                c.id_compra, c.fecha_compra, c.rif_proveedor, c.status,
                CASE 
                  WHEN c.status = 2 THEN 'Recibido'
                  ELSE 'Pendiente por recibir'
                END AS estado_compra,
                COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
                'producto' AS TIPO,
                CONCAT(prod.nombre_producto, ' (', pre.nombre_presentacion, ')') AS ARTICULO,
                pp.id_presentacion_producto AS id_item,
                pp.id_producto,
                pre.cantidad_pmp,
                det.cantidad_producto AS cantidad_raw,
                CONCAT(det.cantidad_producto, ' ',
                    COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                COALESCE(det.precio_compra, 0.00) AS precio_unitario,
                ROUND(det.cantidad_producto * COALESCE(det.precio_compra, 0.00), 2) AS subtotal,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            INNER JOIN productos_compras det ON c.id_compra = det.id_compra
            INNER JOIN presentaciones_productos pp ON det.id_presentacion_producto = pp.id_presentacion_producto
            INNER JOIN presentaciones pre ON pp.id_presentacion = pre.id_presentacion
            INNER JOIN productos prod ON pp.id_producto = prod.id_producto
            LEFT JOIN unidades_medidas um ON COALESCE(pre.id_unidad_medida, prod.id_unidad_medida) = um.id_unidad_medida
            WHERE c.status != 0 AND c.id_compra = :id1

          UNION ALL

            SELECT
                c.id_compra, c.fecha_compra, c.rif_proveedor, c.status,
                CASE 
                  WHEN c.status = 2 THEN 'Recibido'
                  ELSE 'Pendiente por recibir'
                END AS estado_compra,
                COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
                'materia_prima' AS TIPO,
                mp.nombre_materia_prima AS ARTICULO,
                det.id_materia_prima AS id_item,
                det.id_materia_prima AS id_producto,
                1 AS cantidad_pmp,
                det.cantidad_materia_prima AS cantidad_raw,
                CONCAT(det.cantidad_materia_prima, ' ',
                    COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                COALESCE(det.costo_compra, 0.00) AS precio_unitario,
                ROUND(det.cantidad_materia_prima * COALESCE(det.costo_compra, 0.00), 2) AS subtotal,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            INNER JOIN materias_primas_compras det ON c.id_compra = det.id_compra
            INNER JOIN materias_primas mp ON det.id_materia_prima = mp.id_materia_prima
            LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida
            WHERE c.status != 0 AND c.id_compra = :id2

          ORDER BY TIPO DESC
        ";
    $stmt = $this->conectar()->prepare($sql);
    $stmt->bindValue(':id1', (string) $this->id_compra, PDO::PARAM_STR);
    $stmt->bindValue(':id2', (string) $this->id_compra, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  private function registrarCompraP() {

    $bitacora = new bitacoraModelo();
    $objProductos = new productosModelo();
    $objMateriasPrimas = new materiasPrimasModelo();
    $cn = $this->conectar();
    try {
      // $cn->beginTransaction();

      // 1. Generar ID de compra (Manual pero robusto)
      $stMax = $cn->query('SELECT MAX(CAST(id_compra AS UNSIGNED)) FROM compras');
      $maxId = $stMax->fetchColumn();
      $id_compra = (string) (($maxId ?: 0) + 1);

      // 2. Insertar cabecera
      $stInsert = $cn->prepare(
        'INSERT INTO compras (id_compra, rif_proveedor, cedula_usuario, fecha_compra, status)
                 VALUES (:id, :rif, :cedula, :fecha, 1)'
      );
      $stInsert->execute([
        ':id'          => $id_compra,
        ':rif'         => $this->rif_proveedor,
        ':cedula'      => $_SESSION['cedula'] ?? '12345678',
        ':fecha'       => $this->fecha_compra,
      ]);

      // 3. Agrupar items duplicados sumando cantidades
      $itemsAgrupados = [];
      foreach ($this->detalles as $det) {
        $clave = $det['tipo'] . '_' . $det['id'];
        if (!isset($itemsAgrupados[$clave])) {
          $itemsAgrupados[$clave] = $det;
        } else {
          $itemsAgrupados[$clave]['cantidad'] += (float)$det['cantidad'];
          if (!empty($det['precio'])) {
            $itemsAgrupados[$clave]['precio'] = (float)$det['precio'];
          }
        }
      }

      // 4. Insertar detalles (quedan en status 1 sin sumar al inventario aun)
      foreach ($itemsAgrupados as $item) {
        $tipo    = $item['tipo'];
        $id_item = $item['id'];
        $cant    = (float) $item['cantidad'];
        $precio  = (float) ($item['precio'] ?? 0);

        if ($tipo === 'producto') {
          $cn->prepare(
            'INSERT INTO productos_compras (id_compra, id_presentacion_producto, cantidad_producto, precio_compra, status)
            VALUES (:compra, :item, :cant, :precio, 1)'
          )->execute([
            ':compra' => $id_compra,
            ':item'   => $id_item,
            ':cant'   => $cant,
            ':precio' => $precio
          ]);
        } elseif ($tipo === 'materia_prima') {
          $cn->prepare(
            'INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, costo_compra, status)
                         VALUES (:compra, :item, :cant, :precio, 1)'
          )->execute([
            ':compra' => $id_compra,
            ':item'   => $id_item,
            ':cant'   => $cant,
            ':precio' => $precio
          ]);
        }
      }

      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'registrar',
        'resultado' => 'Éxito',
        'viejo' => [],
        'nuevo' => [
          'id_compra' => $id_compra,
          'rif_proveedor' => $this->rif_proveedor,
          'fecha_compra' => $this->fecha_compra,
          'detalles' => $this->detalles
        ]
      ]);
      $this->commit();

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['compras' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'compras'],
          ['accion' => "actDT", 'modulo' => 'compras'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Compras',
              'texto' => "Se ha registrado la compra #$id_compra",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      return [
        'tipo'   => 'limpiar',
        'titulo' => 'Compra registrada',
        'texto'  => 'La compra ha sido registrada exitosamente',
        'icono'  => 'success'
      ];
    } catch (\Throwable $th) {
      $this->rollback();
      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'rif_proveedor' => $this->rif_proveedor,
          'fecha_compra' => $this->fecha_compra,
          'detalles' => $this->detalles
        ]
      ]);
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error de base de datos',
        'texto'  => 'Ocurrió un error al registrar la compra: ' . $th->getMessage(),
        'icono'  => 'error'
      ];
    }
  }
  private function actualizarCompraP() {
    $bitacora = new bitacoraModelo();
    $objProductos = new productosModelo();
    $objMateriasPrimas = new materiasPrimasModelo();
    try {
      $cn = $this->conectar();
      // $cn->beginTransaction();

      // 1. Obtener datos anteriores para bitácora
      $anteriores = $this->seleccionarCompra(['id_compra' => $this->id_compra]);
      $viejo = [
        'id_compra' => $this->id_compra,
        'rif_proveedor' => is_array($anteriores) && isset($anteriores[0]['rif_proveedor']) ? $anteriores[0]['rif_proveedor'] : '',
        'fecha_compra' => is_array($anteriores) && isset($anteriores[0]['fecha_compra']) ? $anteriores[0]['fecha_compra'] : '',
        'detalles' => is_array($anteriores) ? array_map(function ($item) {
          return [
            'tipo' => $item['TIPO'] ?? '',
            'id' => $item['id_item'] ?? '',
            'cantidad' => $item['cantidad_raw'] ?? 0
          ];
        }, $anteriores) : []
      ];

      // 2. Eliminar detalles anteriores
      $cn->prepare('DELETE FROM productos_compras WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('DELETE FROM materias_primas_compras WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);

      // 3. Actualizar cabecera
      $cn->prepare(
        'UPDATE compras SET rif_proveedor = :rif, fecha_compra = :fecha WHERE id_compra = :id'
      )->execute([
        ':rif'         => $this->rif_proveedor,
        ':fecha'       => $this->fecha_compra,
        ':id'          => $this->id_compra,
      ]);

      // 4. Insertar nuevos ítems (en status 1 sin sumar al inventario aún)
      foreach ($this->detalles as $det) {
        $tipo    = $det['tipo']     ?? '';
        $id_item = (string) ($det['id'] ?? '');
        $cant    = (float)  ($det['cantidad'] ?? 0);
        $precio  = (float)  ($det['precio'] ?? 0);

        if (empty($id_item) || $cant <= 0) continue;

        if ($tipo === 'producto') {
          $cn->prepare(
            'INSERT INTO productos_compras (id_compra, id_presentacion_producto, cantidad_producto, precio_compra, status)
                         VALUES (:compra, :item, :cant, :precio, 1)'
          )->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cant, ':precio' => $precio]);
        } elseif ($tipo === 'materia_prima') {
          $cn->prepare(
            'INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, costo_compra, status)
                         VALUES (:compra, :item, :cant, :precio, 1)'
          )->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cant, ':precio' => $precio]);
        }
      }

      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'actualizar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => [
          'id_compra' => $this->id_compra,
          'rif_proveedor' => $this->rif_proveedor,
          'fecha_compra' => $this->fecha_compra,
          'detalles' => $this->detalles
        ]
      ]);
      $this->commit();

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['compras' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'compras'],
          ['accion' => "actDT", 'modulo' => 'compras'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Compras',
              'texto' => "Se ha actualizado la compra #{$this->id_compra}",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      return [
        "tipo"   => "limpiarYcerrar",
        "titulo" => "Compra Actualizada",
        "texto"  => "La compra fue modificada correctamente",
        "icono"  => "success"
      ];
    } catch (\Throwable $e) {
      $this->rollback();
      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'actualizar',
        'resultado' => 'Fallido',
        'viejo' => isset($viejo) ? $viejo : [],
        'nuevo' => [
          'id_compra' => $this->id_compra,
          'rif_proveedor' => $this->rif_proveedor,
          'fecha_compra' => $this->fecha_compra,
          'detalles' => $this->detalles
        ]
      ]);
      return [
        "tipo"   => "simple",
        "titulo" => "Error",
        "texto"  => "Error al actualizar la compra: " . $e->getMessage(),
        "icono"  => "error"
      ];
    }
  }
  private function eliminarCompraP() {
    $bitacora = new bitacoraModelo();
    $objProductos = new productosModelo();
    $objMateriasPrimas = new materiasPrimasModelo();
    try {
      $cn = $this->conectar();
      // $cn->beginTransaction();

      // 1. Revertir stock de PRODUCTOS y MATERIAS PRIMAS (usando los modelos POO) solo si estaba recepcionada
      $anteriores = $this->seleccionarCompra(['id_compra' => $this->id_compra]);
      $statusActual = !empty($anteriores) && isset($anteriores[0]['status']) ? (int)$anteriores[0]['status'] : 1;

      $viejo = [
        'id_compra' => $this->id_compra,
        'rif_proveedor' => is_array($anteriores) && isset($anteriores[0]['rif_proveedor']) ? $anteriores[0]['rif_proveedor'] : '',
        'fecha_compra' => is_array($anteriores) && isset($anteriores[0]['fecha_compra']) ? $anteriores[0]['fecha_compra'] : '',
        'detalles' => is_array($anteriores) ? array_map(function ($item) {
          return [
            'tipo' => $item['TIPO'] ?? '',
            'id' => $item['id_item'] ?? '',
            'cantidad' => $item['cantidad_raw'] ?? 0
          ];
        }, $anteriores) : []
      ];

      if ($statusActual === 2) {
        foreach ($anteriores as $item) {
          if ($item['TIPO'] === 'producto') {
            $idProdReal = $item['id_producto'] ?? $item['id_item'];
            $factorConv = (float) ($item['cantidad_pmp'] ?? 1);
            $cantBase   = (float) $item['cantidad_raw'] * $factorConv;

            $resProd = $objProductos->modificarStock($idProdReal, -$cantBase, $cn);
            if ($resProd !== true) {
              $msg = is_array($resProd) ? ($resProd['texto'] ?? $resProd['titulo'] ?? 'Error al modificar stock de producto') : (string) $resProd;
              throw new \Exception($msg);
            }
          } elseif ($item['TIPO'] === 'materia_prima') {
            $resMp = $objMateriasPrimas->modificarStock($item['id_item'], -$item['cantidad_raw'], $cn);
            if ($resMp !== true) {
              $msg = is_array($resMp) ? ($resMp['texto'] ?? $resMp['titulo'] ?? 'Error al modificar stock de materia prima') : (string) $resMp;
              throw new \Exception($msg);
            }
          }
        }
      }

      // 3. Soft delete en cascada
      $cn->prepare('UPDATE compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('UPDATE productos_compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('UPDATE materias_primas_compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);

      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'eliminar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => []
      ]);
      $this->commit();

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['compras' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'compras'],
          ['accion' => "actDT", 'modulo' => 'compras'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Compras',
              'texto' => "Se ha anulado la compra #{$this->id_compra}",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      return [
        "tipo"   => "recargar",
        "titulo" => "Compra eliminada",
        "texto"  => "La compra #{$this->id_compra} fue eliminada exitosamente.",
        "icono"  => "success"
      ];
    } catch (\Throwable $e) {
      $this->rollback();
      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'eliminar',
        'resultado' => 'Fallido',
        'viejo' => isset($viejo) ? $viejo : ['id_compra' => $this->id_compra],
        'nuevo' => []
      ]);
      return [
        "tipo"   => "simple",
        "titulo" => "Error",
        "texto"  => "Error al eliminar la compra: " . $e->getMessage(),
        "icono"  => "error"
      ];
    }
  }
  private function cambiarEstadoRecepcionP() {
    $bitacora = new bitacoraModelo();
    $objProductos = new productosModelo();
    $objMateriasPrimas = new materiasPrimasModelo();

    try {
      $cn = $this->conectar();
      $anteriores = $this->seleccionarCompra(['id_compra' => $this->id_compra]);
      if (empty($anteriores)) {
        return [
          'tipo'   => 'simple',
          'titulo' => 'No encontrada',
          'texto'  => 'La orden de compra no fue encontrada.',
          'icono'  => 'error'
        ];
      }

      $statusActual = (int)($anteriores[0]['status'] ?? 0);
      if ($statusActual === 2) {
        return [
          'tipo'   => 'simple',
          'titulo' => 'Ya recepcionada',
          'texto'  => 'Esta compra ya fue marcada como Recibida anteriormente.',
          'icono'  => 'info'
        ];
      }
      if ($statusActual === 0) {
        return [
          'tipo'   => 'simple',
          'titulo' => 'Compra anulada',
          'texto'  => 'No se puede recepcionar una compra anulada.',
          'icono'  => 'error'
        ];
      }

      // Sumar al stock de productos y materias primas al declarar la recepción
      foreach ($anteriores as $item) {
        $cant = (float)($item['cantidad_raw'] ?? 0);
        if ($item['TIPO'] === 'producto') {
          $idProdReal = $item['id_producto'] ?? $item['id_item'];
          $factorConv = (float)($item['cantidad_pmp'] ?? 1);
          $cantBase   = $cant * $factorConv;

          $resProd = $objProductos->modificarStock($idProdReal, $cantBase, $cn);
          if ($resProd !== true) {
            $msg = is_array($resProd) ? ($resProd['texto'] ?? $resProd['titulo'] ?? 'Error al modificar stock de producto') : (string) $resProd;
            throw new \Exception($msg);
          }
        } elseif ($item['TIPO'] === 'materia_prima') {
          $resMp = $objMateriasPrimas->modificarStock($item['id_item'], $cant, $cn);
          if ($resMp !== true) {
            $msg = is_array($resMp) ? ($resMp['texto'] ?? $resMp['titulo'] ?? 'Error al modificar stock de materia prima') : (string) $resMp;
            throw new \Exception($msg);
          }
        }
      }

      // Actualizar status de la compra a 2 (Recibido)
      $cn->prepare('UPDATE compras SET status = 2 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);

      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'Recepcionar compra (' . $this->id_compra . ')',
        'resultado' => 'Éxito',
        'viejo' => [
          'id_compra' => $this->id_compra,
          'status' => 1
        ],
        'nuevo' => [
          'id_compra' => $this->id_compra,
          'status' => 2
        ]
      ]);
      $this->commit();

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['compras' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'compras'],
          ['accion' => "borrarDataModuloSS", 'modulo' => 'inventario'],
          ['accion' => "actDT", 'modulo' => 'compras'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Compras',
              'texto' => "La compra #{$this->id_compra} ha sido recepcionada e ingresada al inventario.",
              'icono' => 'success',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      return [
        "tipo"   => "recargar",
        "titulo" => "Mercancía recibida",
        "texto"  => "La compra #{$this->id_compra} fue declarada como recibida y su mercancía ingresó al inventario exitosamente.",
        "icono"  => "success"
      ];
    } catch (\Throwable $e) {
      $this->rollback();
      $bitacora->registrarBitacora([
        'modulo' => 'compras',
        'accion' => 'Recepcionar compra (' . $this->id_compra . ')',
        'resultado' => 'Fallido',
        'viejo' => ['id_compra' => $this->id_compra, 'status' => 1],
        'nuevo' => []
      ]);
      return [
        "tipo"   => "simple",
        "titulo" => "Error",
        "texto"  => "Error al recepcionar la compra: " . $e->getMessage(),
        "icono"  => "error"
      ];
    }
  }
  private function imprimirOrdenCompraP() {
    $detalles = $this->seleccionarCompra(['id_compra' => $this->id_compra]);
    if (empty($detalles)) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'No encontrada',
        'texto'  => 'La orden de compra solicitada no existe.',
        'icono'  => 'warning'
      ];
    }

    $proveedor = $detalles[0]['PROVEEDOR'] ?? 'No especificado';
    $rif = $detalles[0]['rif_proveedor'] ?? '';
    $fecha = $detalles[0]['fecha_compra'] ?? '';
    $estado = $detalles[0]['estado_compra'] ?? 'Pendiente por recibir';

    $infoCeldas = [];
    $idx = 1;
    foreach ($detalles as $det) {
      $infoCeldas[] = [
        'nro'      => (string) $idx++,
        'tipo'     => ($det['TIPO'] === 'producto') ? 'Producto' : 'Materia Prima',
        'articulo' => $det['ARTICULO'] ?? '',
        'cantidad' => $det['cantidad'] ?? ''
      ];
    }

    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('ORDEN DE COMPRA #' . $this->id_compra);

    $bitacora = new bitacoraModelo();
    $bitacora->registrarBitacora([
      'modulo' => 'compras',
      'accion' => 'Imprimir orden de compra (' . $this->id_compra . ')',
      'resultado' => 'Éxito',
    ]);

    return $objetoPDF->crearPDF([
      'tituloReporte' => 'ORDEN DE COMPRA N° ' . $this->id_compra,
      'datosExtCabecera' => [
        'PROVEEDOR: ' . $proveedor . ' (RIF: ' . $rif . ')',
        'FECHA: ' . $fecha . '    |    ESTADO: ' . mb_strtoupper($estado, 'UTF-8')
      ],
      'configColumnas' => [
        'nro'      => ['N°', 15],
        'tipo'     => ['TIPO', 40],
        'articulo' => ['ARTÍCULO', 95],
        'cantidad' => ['CANTIDAD', 40]
      ],
      'infoBD' => $infoCeldas
    ]);
  }
  private function imprimirPlanillaRetencionP() {
    $detalles = $this->seleccionarCompra(['id_compra' => $this->id_compra]);
    if (empty($detalles)) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'No encontrada',
        'texto'  => 'La compra solicitada no existe.',
        'icono'  => 'warning'
      ];
    }

    $cn = $this->conectar();
    $stCab = $cn->prepare("
      SELECT c.id_compra, c.fecha_compra, c.rif_proveedor,
             p.razon_social_proveedor, p.direccion_proveedor, p.telefono_proveedor
      FROM compras c
      LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
      WHERE c.id_compra = :id
    ");
    $stCab->execute([':id' => $this->id_compra]);
    $compraInfo = $stCab->fetch(PDO::FETCH_ASSOC);

    $rifProveedor = $compraInfo['rif_proveedor'] ?? ($detalles[0]['rif_proveedor'] ?? '');
    $razonProveedor = $compraInfo['razon_social_proveedor'] ?? ($detalles[0]['PROVEEDOR'] ?? 'NO ESPECIFICADO');
    $nroFactura = str_pad((string)$this->id_compra, 10, '0', STR_PAD_LEFT);
    $nroControl = 'NA-' . str_pad((string)$this->id_compra, 8, '0', STR_PAD_LEFT);
    $fechaFactura = !empty($compraInfo['fecha_compra']) ? date('d/m/Y', strtotime($compraInfo['fecha_compra'])) : date('d/m/Y');
    $fechaEmision = !empty($compraInfo['fecha_compra']) ? substr($compraInfo['fecha_compra'], 0, 10) : date('Y-m-d');

    // Cálculos de retención SENIAT
    $baseImponible = 0.0;
    foreach ($detalles as $det) {
      $cant = (float) ($det['cantidad_raw'] ?? 0);
      $precio = (float) ($det['precio_unitario'] ?? 0);
      $baseImponible += ($cant * $precio);
    }
    $baseImponible = round($baseImponible, 2);
    $alicuota = 16.00;
    $impuestoIVA = round($baseImponible * ($alicuota / 100), 2);
    $porcentajeRetencion = 75.00;
    $ivaRetenido = round($impuestoIVA * ($porcentajeRetencion / 100), 2);
    $totalCompra = round($baseImponible + $impuestoIVA, 2);
    $montoPagar = round($totalCompra - $ivaRetenido, 2);

    // Obtener o registrar en ordenes_retenciones_compras
    $periodoFiscal = date('Y/m', strtotime($fechaEmision));
    $prefijo = date('Ym', strtotime($fechaEmision));
    $nroComprobante = $prefijo . str_pad((string)$this->id_compra, 8, '0', STR_PAD_LEFT);

    try {
      $stRet = $cn->prepare("SELECT * FROM ordenes_retenciones_compras WHERE id_compra = :id AND status = 1 LIMIT 1");
      $stRet->execute([':id' => $this->id_compra]);
      $retExistente = $stRet->fetch(PDO::FETCH_ASSOC);

      if (empty($retExistente)) {
        $idRetencion = 'RET-' . str_pad((string)$this->id_compra, 5, '0', STR_PAD_LEFT);
        $stIns = $cn->prepare("
          INSERT INTO ordenes_retenciones_compras (
            id_retencion_compra, id_compra, status
          ) VALUES (
            :id_ret, :id_c, 1
          )
        ");
        $stIns->execute([
          ':id_ret' => $idRetencion,
          ':id_c'   => $this->id_compra,
        ]);
      }
    } catch (\Throwable $e) {
      // Ignorar si ya está registrada o restricción
    }

    $bitacora = new bitacoraModelo();
    $bitacora->registrarBitacora([
      'modulo' => 'compras',
      'accion' => 'Imprimir planilla de retención (' . $this->id_compra . ')',
      'resultado' => 'Éxito',
    ]);

    // Crear PDF apaisado tipo SENIAT
    $pdf = new pdfModel([
      'tamanoPagina' => 'carta',
      'orientacion'  => 'L',
      'header'       => false,
      'footer'       => false
    ]);

    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage('L');

    // 1. TEXTO LEGAL SUPERIOR IZQUIERDA
    $pdf->SetXY(10, 10);
    $pdf->SetFont('Arial', '', 6.5);
    $textoLegal = '(Ley del I.V.A. - Art. 11 " Serán responsables del pago del Impuesto en calidad de agentes de retención, los comprobantes o adquirientes de determinados bienes muebles y los receptores de ciertos servicios, a quienes la administración Tributaria designe como tal")';
    $pdf->MultiCell(150, 3.2, $pdf->CSE($textoLegal), 0, 'L');

    // 2. N° DE COMPROBANTE
    $pdf->SetXY(165, 8);
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->cell2(62, 5, 'N° DE COMPROBANTE', 1, 2, 'C');
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->cell2(62, 7, $nroComprobante, 1, 0, 'C');

    // 3. FECHA Y PERIODO FISCAL
    $pdf->SetXY(230, 8);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell2(40, 4.5, 'FECHA', 1, 2, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell2(40, 5.5, $fechaFactura, 1, 2, 'C');

    $pdf->SetXY(230, 24);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell2(40, 4.5, 'PERIODO FISCAL', 1, 2, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell2(40, 5.5, $periodoFiscal, 1, 0, 'C');

    // 4. IDENTIFICACION FISCAL (CUADROS)
    // Fila 1: Agente de Retención
    $pdf->SetXY(10, 24);
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->cell2(105, 4, $pdf->CSE('RAZON SOCIAL DEL AGENTE DE RETENCION'), 1, 0, 'L');
    $pdf->SetX(118);
    $pdf->cell2(109, 4, $pdf->CSE('REGISTRO DE INFORMACION FISCAL DEL AGENTE DE RETENCION'), 1, 1, 'L');

    $pdf->SetXY(10, 28);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->cell2(105, 6, 'J.LACRUZ C.A.', 1, 0, 'C');
    $pdf->SetX(118);
    $pdf->cell2(109, 6, 'RIF J-41219270-1', 1, 1, 'C');

    // Fila 2: Dirección Fiscal del Agente
    $pdf->SetXY(10, 36);
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->cell2(217, 3.5, $pdf->CSE('DIRECCION FISCAL DEL AGENTE DE RETENCION:'), 'LTR', 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell2(217, 5.5, $pdf->CSE('CALLE SAN RAFAEL CON AV. 1 Y 2 LA MATA CABUDARE LOCAL SN. MUNICIPIO PALAVECINO EDO.LARA'), 'LBR', 1, 'L');

    // Fila 3: Sujeto Retenido (Proveedor)
    $pdf->SetXY(10, 47);
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->cell2(105, 4, $pdf->CSE('NOMBRE Y RAZON SOCIAL DE SUJETO RETENIDO'), 1, 0, 'L');
    $pdf->SetX(118);
    $pdf->cell2(109, 4, $pdf->CSE('REGISTRO DE INFORMACION FISCAL DEL SUJETO RETENIDO (R.I.F)'), 1, 1, 'L');

    $pdf->SetXY(10, 51);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->cell2(105, 6, $pdf->CSE($razonProveedor), 1, 0, 'C');
    $pdf->SetX(118);
    $pdf->cell2(109, 6, $pdf->CSE($rifProveedor), 1, 1, 'C');

    // 5. TABLA PRINCIPAL
    $yTabla = 62;
    $cols = [
      ['key' => 'oper',          'w' => 10, 'h1' => 'Oper.',             'h2' => 'Nº',            'align' => 'C'],
      ['key' => 'fecha_fac',     'w' => 18, 'h1' => 'Fecha de',          'h2' => 'Factura',       'align' => 'C'],
      ['key' => 'nro_fac',       'w' => 22, 'h1' => 'Numero de',         'h2' => 'Factura',       'align' => 'C'],
      ['key' => 'nro_ctrl',      'w' => 22, 'h1' => 'Nº. Control',       'h2' => 'de Factura',    'align' => 'C'],
      ['key' => 'nd',            'w' => 14, 'h1' => 'Número',            'h2' => 'ND',            'align' => 'C'],
      ['key' => 'nc',            'w' => 14, 'h1' => 'Número',            'h2' => 'NC',            'align' => 'C'],
      ['key' => 'tipo_tr',       'w' => 14, 'h1' => 'Tipo de',           'h2' => 'Transacc.',     'align' => 'C'],
      ['key' => 'fac_afec',      'w' => 18, 'h1' => 'Nº Factura',        'h2' => 'Afectada',      'align' => 'C'],
      ['key' => 'total_con_iva', 'w' => 25, 'h1' => 'Total Compras',     'h2' => 'Incluyendo IVA', 'align' => 'R'],
      ['key' => 'sin_credito',   'w' => 21, 'h1' => 'Compras sin',       'h2' => 'Derecho Créd.', 'align' => 'R'],
      ['key' => 'base_imp',      'w' => 22, 'h1' => 'Base',              'h2' => 'Imponible',     'align' => 'R'],
      ['key' => 'alicuota',      'w' => 13, 'h1' => '%',                 'h2' => 'Alicuota',      'align' => 'C'],
      ['key' => 'impuesto_iva',  'w' => 22, 'h1' => 'Impuesto',          'h2' => 'IVA',           'align' => 'R'],
      ['key' => 'iva_retenido',  'w' => 25, 'h1' => 'IVA',               'h2' => 'Retenido',      'align' => 'R'],
    ];

    $xInicio = 10;
    $xSuper = $xInicio;
    for ($i = 0; $i < 10; $i++) {
      $xSuper += $cols[$i]['w'];
    }
    $wSuper = $cols[10]['w'] + $cols[11]['w'] + $cols[12]['w'];

    // Super-encabezado "COMPRAS INTERNAS O IMPORTACIONES"
    $pdf->SetXY($xSuper, $yTabla);
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->cell2($wSuper, 4.5, $pdf->CSE('COMPRAS INTERNAS O IMPORTACIONES'), 1, 0, 'C');

    // Encabezados
    $xPos = $xInicio;
    foreach ($cols as $idx => $c) {
      if ($idx >= 10 && $idx <= 12) {
        $pdf->SetXY($xPos, $yTabla + 4.5);
        $pdf->cell2($c['w'], 4, $pdf->CSE($c['h1']), 'LTR', 2, 'C');
        $pdf->cell2($c['w'], 4.5, $pdf->CSE($c['h2']), 'LBR', 0, 'C');
      } else {
        $pdf->SetXY($xPos, $yTabla);
        $pdf->cell2($c['w'], 6.5, $pdf->CSE($c['h1']), 'LTR', 2, 'C');
        $pdf->cell2($c['w'], 6.5, $pdf->CSE($c['h2']), 'LBR', 0, 'C');
      }
      $xPos += $c['w'];
    }

    // Fila de datos
    $yFila = $yTabla + 13;
    $pdf->SetFont('Arial', '', 7);

    $datosFila = [
      'oper'          => '01',
      'fecha_fac'     => $fechaFactura,
      'nro_fac'       => $nroFactura,
      'nro_ctrl'      => $nroControl,
      'nd'            => '',
      'nc'            => '',
      'tipo_tr'       => '01',
      'fac_afec'      => '',
      'total_con_iva' => number_format($totalCompra, 2, ',', '.'),
      'sin_credito'   => '0,00',
      'base_imp'      => number_format($baseImponible, 2, ',', '.'),
      'alicuota'      => number_format($alicuota, 0),
      'impuesto_iva'  => number_format($impuestoIVA, 2, ',', '.'),
      'iva_retenido'  => number_format($ivaRetenido, 2, ',', '.'),
    ];

    $pdf->SetXY($xInicio, $yFila);
    foreach ($cols as $c) {
      $pdf->cell2($c['w'], 6.5, $pdf->CSE($datosFila[$c['key']] ?? ''), 1, 0, $c['align']);
    }
    $yFila += 6.5;

    // Filas vacías decorativas
    for ($f = 0; $f < 2; $f++) {
      $pdf->SetXY($xInicio, $yFila);
      foreach ($cols as $c) {
        $pdf->cell2($c['w'], 6, '', 1, 0, 'C');
      }
      $yFila += 6;
    }

    // FILA DE TOTALES
    $pdf->SetXY($xInicio, $yFila);
    $pdf->SetFont('Arial', 'B', 7);

    $wTotalesIzq = 0;
    for ($i = 0; $i < 8; $i++) $wTotalesIzq += $cols[$i]['w'];

    $pdf->cell2($wTotalesIzq, 6.5, 'TOTALES', 1, 0, 'C');
    $pdf->cell2($cols[8]['w'], 6.5, number_format($totalCompra, 2, ',', '.'), 1, 0, 'R');
    $pdf->cell2($cols[9]['w'], 6.5, '0,00', 1, 0, 'R');
    $pdf->cell2($cols[10]['w'], 6.5, number_format($baseImponible, 2, ',', '.'), 1, 0, 'R');
    $pdf->cell2($cols[11]['w'], 6.5, '', 1, 0, 'C');
    $pdf->cell2($cols[12]['w'], 6.5, number_format($impuestoIVA, 2, ',', '.'), 1, 0, 'R');
    $pdf->cell2($cols[13]['w'], 6.5, number_format($ivaRetenido, 2, ',', '.'), 1, 1, 'R');

    $yFila += 6.5;

    // MONTO A PAGAR
    $pdf->SetXY($xInicio, $yFila + 2);
    $pdf->SetFont('Arial', 'BI', 8);
    $pdf->SetX($xSuper - 20);
    $pdf->cell2($wSuper + 20, 6, $pdf->CSE('MONTO A PAGAR.......................................'), 0, 0, 'R');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell2($cols[13]['w'], 6, number_format($montoPagar, 2, ',', '.'), 'B', 1, 'R');

    // FIRMAS Y SELLOS
    $yFirmas = $yFila + 22;

    $rutaLogo = 'src/assets/images/logo2.png';
    if (!file_exists($rutaLogo)) {
      $rutaLogo = 'src/assets/images/logo.png';
    }
    if (file_exists($rutaLogo)) {
      $pdf->Image($rutaLogo, 22, $yFirmas - 16, 18, 14);
    }
    $pdf->SetXY(42, $yFirmas - 14);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell2(60, 4, 'J. LACRUZ C.A.', 0, 1, 'L');
    $pdf->SetX(42);
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->cell2(60, 3.5, 'Multiservicios Generales', 0, 1, 'L');
    $pdf->SetX(42);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell2(60, 3.5, 'Rif.: J-412192701', 0, 1, 'L');

    $pdf->SetXY(15, $yFirmas + 2);
    $pdf->SetFont('Arial', '', 7);
    $pdf->cell2(95, 4, '________________________________________________', 0, 2, 'C');
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->cell2(95, 4, $pdf->CSE('FIRMA Y SELLO DEL AGENTE DE RETENCION'), 0, 0, 'C');

    $pdf->SetXY(160, $yFirmas + 2);
    $pdf->SetFont('Arial', '', 7);
    $pdf->cell2(95, 4, '________________________________________________', 0, 2, 'C');
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->cell2(95, 4, $pdf->CSE('RECIBIDO POR'), 0, 0, 'C');

    return $pdf;
  }
}
