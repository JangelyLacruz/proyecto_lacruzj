<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\pdfModel;

class reportesModelo extends conexion {

  private array $filtros = [];

  private function normalizarFecha(string $fecha): string {
    $fecha = trim($fecha);
    if (empty($fecha)) return '';

    // Formato DD-MM-YYYY o DD/MM/YYYY
    if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $fecha, $m)) {
      return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    }

    // Formato YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
      return $fecha;
    }

    $ts = strtotime($fecha);
    if ($ts !== false) {
      return date('Y-m-d', $ts);
    }
    return $fecha;
  }

  public function reporteVentas(array $filtros) {
    if (empty($filtros['fecha_desde']) || empty($filtros['fecha_hasta'])) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }
    $this->filtros['tipo_producto'] = $filtros['tipo_producto'] ?? 'todos';
    $this->filtros['fecha_desde']   = $this->normalizarFecha($filtros['fecha_desde']);
    $this->filtros['fecha_hasta']   = $this->normalizarFecha($filtros['fecha_hasta']);

    if ($this->filtros['fecha_desde'] > $this->filtros['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteVentasP();
  }

  public function reporteCompras(array $filtros) {
    if (empty($filtros['fecha_desde']) || empty($filtros['fecha_hasta'])) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }

    $this->filtros['tipo_item']   = $filtros['tipo_item'] ?? 'todos';
    $this->filtros['fecha_desde'] = $this->normalizarFecha($filtros['fecha_desde']);
    $this->filtros['fecha_hasta'] = $this->normalizarFecha($filtros['fecha_hasta']);

    if ($this->filtros['fecha_desde'] > $this->filtros['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteComprasP();
  }

  public function reporteCierre(array $filtros) {
    if (empty($filtros['fecha_cierre'])) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de cierre para el reporte'
      ];
    }

    $this->filtros['fecha_cierre'] = $this->normalizarFecha($filtros['fecha_cierre']);

    return $this->reporteCierreP();
  }
  public function reporteServicios() {
    $instruccionesDB = [
      'tabla' => 'servicios as s',
      'campos' => '
        s.id_servicio, s.nombre_servicio, s.precio_servicio
      ',
      'ORDER' => 's.nombre_servicio ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();

    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['precio_servicio'] .= ' Bs';
    }
    unset($fila);
    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE SERVICIOS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE SERVICIOS",
      "configColumnas" => [
        'id_servicio' => ['CÓDIGO', 60],
        'nombre_servicio' => ['SERVICIO', 80],
        'precio_servicio' => ['PRECIO', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  public function reporteProductos() {
    $instruccionesDB = [
      'tabla' => 'productos as p',
      'campos' => '
        p.id_producto, p.nombre_producto, p.precio_producto, p.stock_producto, cp.nombre_categoria_producto
      ',
      'datosJoins' => [
        'categorias_productos AS cp' => 'p.id_categoria_producto = cp.id_categoria_producto',
      ],
      'ORDER' => 'cp.id_categoria_producto, p.nombre_producto  ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin regisros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['precio_producto'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE PRODUCTOS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE PRODUCTOS",
      "configColumnas" => [
        'id_producto' => ['CÓDIGO', 60],
        'nombre_producto' => ['PRODUCTO', 40],
        'precio_producto' => ['PRECIO', 30],
        'stock_producto' => ['STOCK', 20],
        'nombre_categoria_producto' => ['CATEGORÍA', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  public function reporteMateriaPrima() {
    $instruccionesDB = [
      'tabla' => 'materias_primas as mp',
      'campos' => '
        mp.id_materia_prima, mp.nombre_materia_prima, mp.stock_materia_prima, mp.precio_materia_prima, um.simbolo_unidad_medida
      ',
      'datosJoins' => [
        'unidades_medidas AS um' => 'mp.id_unidad_medida = um.id_unidad_medida',
      ],
      'ORDER' => 'mp.nombre_materia_prima ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin regisros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['stock_materia_prima'] .= ' ' . $fila['simbolo_unidad_medida'];
      $fila['precio_materia_prima'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MATERIAS PRIMAS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE MATERIAS PRIMAS",
      "configColumnas" => [
        'id_materia_prima' => ['CÓDIGO', 60],
        'nombre_materia_prima' => ['MATERIA PRIMA', 60],
        'stock_materia_prima' => ['STOCK', 30],
        'precio_materia_prima' => ['PRECIO', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }

  private function reporteVentasP() {
    //$Filtro = "";
    $tipoProducto = $this->filtros['tipo_producto'] ?? 'todos';

    switch ($tipoProducto) {
        case 'productos':
            $textoFiltroTipo = 'Solo Productos';
            break;
        case 'servicios':
            $textoFiltroTipo = 'Solo Servicios';
            break;
        default:
            $textoFiltroTipo = 'Todos los Items';
            break;
    }

    $instruccionesDB = [
      'tabla' => 'ordenes_entregas_presupuestos as f',
      'campos' => "
            f.id_orden_entrega_presupuesto,
            f.fecha_orden_entrega_presupuesto,
            COALESCE(c.razon_social_cliente, f.rif_cedula_cliente) as rif_cedula_cliente,
            
            (SELECT COUNT(*) 
             FROM productos_ordenes_entregas_presupuestos pf 
             WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status != 0) AS cant_productos,
            
            (SELECT COUNT(*) 
             FROM servicios_ordenes_entregas_presupuestos sf 
             WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status != 0) AS cant_servicios,

            COALESCE((
              SELECT SUM(dp.monto_pago * m.valor_moneda)
              FROM pagos p
              LEFT JOIN detalles_pagos dp ON p.id_pago = dp.id_pago
              LEFT JOIN monedas m ON dp.id_moneda = m.id_moneda
              WHERE p.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND p.status != 0
            ), 0) AS total_venta
      ",
      'datosJoins' => [
        'LEFT clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
      ],
      'WHERE' => [
        'f.fecha_orden_entrega_presupuesto' => [
          '>=' => $this->filtros['fecha_desde'] . ' 00:00:00',
          '<=' => $this->filtros['fecha_hasta'] . ' 23:59:59',
        ]
      ],
      'ORDER' => 'f.fecha_orden_entrega_presupuesto DESC'
    ];

    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if (empty($infoCeldas) || empty($infoCeldas[0]['id_orden_entrega_presupuesto'])) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de ese intervalo de tiempo',
        'icono' => 'warning',
      ];
    }

    if ($tipoProducto === 'productos') {
        $infoCeldas = array_filter($infoCeldas, function($fila) {
            return $fila['cant_productos'] > 0 && $fila['cant_servicios'] == 0;
        });
    } elseif ($tipoProducto === 'servicios') {
        $infoCeldas = array_filter($infoCeldas, function($fila) {
            return $fila['cant_servicios'] > 0 && $fila['cant_productos'] == 0;
        });
    }

    // Acumulador para el total general
    $montoTotalVentas = 0;

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $montoTotalVentas += floatval($fila['total_venta']);
      $fila['fecha_orden_entrega_presupuesto'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_orden_entrega_presupuesto']);
      $fila['total_venta'] = number_format((float)$fila['total_venta'], 2, ',', '.') . ' Bs';
    }
    unset($fila);
    $infoCeldas = array_values($infoCeldas);

       $infoCeldas[] = [
      'id_orden_entrega_presupuesto' => '',
      'rif_cedula_cliente'           => '',
      'fecha_orden_entrega_presupuesto' => 'TOTAL DEL DÍA:',
      'total_venta'                  => number_format($montoTotalVentas, 2, ',', '.') . ' Bs'
    ];

    $fechaDesde = date('d/m/Y', strtotime($this->filtros['fecha_desde']));
    $fechaHasta = date('d/m/Y', strtotime($this->filtros['fecha_hasta']));

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE VENTAS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE VENTAS",
      "rangoFechas"    => "{$fechaDesde} al {$fechaHasta}",
      "filtroAplicado" => $textoFiltroTipo,
      "datosExtCabecera" => [
        "Rango de Fechas: {$fechaDesde} al {$fechaHasta}",
        "Filtro Aplicado: {$textoFiltroTipo}"
      ],
      "configColumnas" => [
        'id_orden_entrega_presupuesto' => ['NRO FACTURA', 40],
        'rif_cedula_cliente' => ['CLIENTE', 40],
        'fecha_orden_entrega_presupuesto' => ['FECHA', 50],
        'total_venta' => ['MONTO', 50],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  private function reporteComprasP() {
    $tipoCompras = $this->filtros['tipo_item'] ?? 'todos';

    $whereFechas = [
        'c.fecha_compra' => [
            '>=' => $this->filtros['fecha_desde'] . ' 00:00:00',
            '<=' => $this->filtros['fecha_hasta'] . ' 23:59:59',
        ]
    ];

    $resMP = [];
    $resProd = [];

    // CONSULTA MATERIAS PRIMAS 
    if (in_array($tipoCompras, ['todos', 'materias_primas'])) {
        $whereMP = $whereFechas;
        $whereMP['mpc.status'] = 1;
        $instruccionesMP = [
            'tabla' => 'compras as c',
            'campos' => '
                  c.id_compra,
                  c.fecha_compra,
                  prov.razon_social_proveedor,
                  mp.nombre_materia_prima AS descripcion,
                  COALESCE(mp.precio_materia_prima, 0) AS precio,
                  SUM(mpc.cantidad_materia_prima) AS cantidad,
                  SUM(mpc.cantidad_materia_prima * COALESCE(mp.precio_materia_prima, 0)) AS total_compra
            ',
            'datosJoins' => [
                 'LEFT materias_primas_compras as mpc' => 'c.id_compra = mpc.id_compra',
                 'LEFT materias_primas as mp'          => 'mpc.id_materia_prima = mp.id_materia_prima',
                 'LEFT proveedores as prov'            => 'c.rif_proveedor = prov.rif_proveedor',
            ],
            'WHERE' => $whereFechas,
            'GROUP BY' => 'c.id_compra, mpc.id_materia_prima, c.fecha_compra, prov.razon_social_proveedor, mp.nombre_materia_prima, mp.precio_materia_prima'
        ];
        $resMP = $this->seleccionarDatos2($instruccionesMP)->fetchAll() ?: [];
    }

    // CONSULTA PRODUCTOS / INSUMOS
    if (in_array($tipoCompras, ['todos', 'productos', 'insumos'])) {
        $whereProd = $whereFechas;

        // APLICAR FILTRO DE CATEGORÍA SEGÚN EL TIPO SELECCIONADO
    if ($tipoCompras === 'productos') {
        // Solo productos fabricados (1) y no fabricados (2)
        $whereProd['prod.id_categoria_producto'] = [
                '>=' => 1,
                '<=' => 2
            ];
    } elseif ($tipoCompras === 'insumos') {
        // Solo insumos (categoría 3)
        $whereProd ['prod.id_categoria_producto'] = 3;
    }
        $whereProd['pc.status'] = 1;
        
        $instruccionesProd = [
            'tabla' => 'compras as c',
            'campos' => '
                  c.id_compra,
                  c.fecha_compra,
                  COALESCE(prov.razon_social_proveedor, c.rif_proveedor) as razon_social_proveedor,
                  CONCAT(prod.nombre_producto, IF(p.nombre_presentacion IS NOT NULL AND p.nombre_presentacion != "", CONCAT(" (", p.nombre_presentacion, ")"), "")) AS descripcion,
                  COALESCE(prod.precio_producto, 0) AS precio,
                  SUM(pc.cantidad_producto) AS cantidad,
                  SUM(pc.cantidad_producto * COALESCE(prod.precio_producto, 0)) AS total_compra
            ',
            'datosJoins' => [
                 'LEFT productos_compras as pc'          => 'c.id_compra = pc.id_compra',
                 'LEFT presentaciones_productos as pres' => 'pc.id_presentacion_producto = pres.id_presentacion_producto',
                 'LEFT presentaciones as p'              => 'pres.id_presentacion = p.id_presentacion',
                 'LEFT productos as prod'                => 'pres.id_producto = prod.id_producto',
                 'LEFT proveedores as prov'              => 'c.rif_proveedor = prov.rif_proveedor',
            ],
            'WHERE' => $whereProd,
            'GROUP BY' => 'c.id_compra, pc.id_presentacion_producto, c.fecha_compra, prov.razon_social_proveedor, prod.nombre_producto, p.nombre_presentacion, prod.precio_producto'
        ];
    $resProd = $this->seleccionarDatos2($instruccionesProd)->fetchAll() ?: [];
}

    switch ($tipoCompras) {
        case 'materias_primas':
            $textoFiltroTipo = 'Materias Primas';
            $infoCeldas = $resMP;
            break;
        case 'productos':
            $textoFiltroTipo = 'Productos';
            $infoCeldas = $resProd;
            break;
        case 'insumos':
            $textoFiltroTipo = 'Insumos';
            $infoCeldas = $resProd;
            break;
        default:
            $textoFiltroTipo = 'Todos los Items';
            $infoCeldas = array_merge($resMP, $resProd);

            // Ordenar el arreglo combinado por fecha descendente
            usort($infoCeldas, function($a, $b) {
                return strtotime($b['fecha_compra']) - strtotime($a['fecha_compra']);
            });
            break;
    }

    if ($infoCeldas == [] || ($infoCeldas[0]['id_compra'] ?? null) == null) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de ese intervalo de tiempo',
        'icono' => 'warning',
      ];
    }   

    $montoTotalCompras = 0;

    foreach ($infoCeldas as &$fila) {
      $montoTotalCompras += floatval($fila['total_compra'] ?? 0);
      $precioUnitario = $fila['precio'] ?? 0;
      
      $fila['id_compra']              = (string) ($fila['id_compra'] ?? '');
      $fila['fecha_compra']           = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_compra'] ?? '');
      $fila['razon_social_proveedor'] = (string) ($fila['razon_social_proveedor'] ?? '');
      $fila['descripcion']            = (string) ($fila['descripcion'] ?? '');
      $fila['cantidad']               = (string) ($fila['cantidad'] ?? '0');
      $fila['precio']                 = number_format((float)$precioUnitario, 2, ',', '.') . ' Bs';
      $fila['total_compra']           = number_format((float)$fila['total_compra'], 2, ',', '.') . ' Bs';
    }
    unset($fila);
   
    // Fila del Total General
    $infoCeldas[] = [
        'id_compra'              => '',
        'fecha_compra'           => '',
        'razon_social_proveedor' => '',
        'descripcion'            => 'TOTAL COMPRAS:',
        'cantidad'               => '',
        'precio'                 => '',
        'total_compra'           => number_format($montoTotalCompras, 2, ',', '.') . ' Bs'
    ];

    $fechaDesde = date('d/m/Y', strtotime($this->filtros['fecha_desde']));
    $fechaHasta = date('d/m/Y', strtotime($this->filtros['fecha_hasta']));

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE COMPRAS');

    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE COMPRAS",
      "rangoFechas"   => "{$fechaDesde} al {$fechaHasta}",
      "filtroAplicado" => $textoFiltroTipo,
      "datosExtCabecera" => [
        "Rango de Fechas: {$fechaDesde} al {$fechaHasta}",
        "Filtro Aplicado: {$textoFiltroTipo}"
      ],
      "configColumnas" => [
        'id_compra' => ['CÓDIGO', 20],
        'fecha_compra' => ['FECHA', 25],
        'razon_social_proveedor' => ['PROVEEDOR', 35],
        'descripcion' => ['DESCRIPCIÓN', 40],
        'cantidad' => ['CANTIDAD', 25],
        'precio' => ['PRECIO', 25],
        'total_compra' => ['TOTAL', 30],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  private function reporteCierreP() {
    $instruccionesDB = [
        'tabla'  => 'pagos as p',
        'campos' => '
              p.id_orden_entrega_presupuesto,
              COALESCE(c.razon_social_cliente, c.rif_cedula_cliente, f.rif_cedula_cliente, "") as rif_cedula_cliente,
              p.fecha_pago,
              f.fecha_orden_entrega_presupuesto,
              COALESCE(mp.nombre_metodo_pago, "Efectivo") as nombre_metodo_pago,
              COALESCE(m.simbolo_moneda, "Bs") as simbolo_moneda,
              COALESCE(dp.monto_pago, 0) as monto_pago,
              (COALESCE(dp.monto_pago, 0) * COALESCE(m.valor_moneda, 1)) AS monto_pago_bs
        ',
        'datosJoins' => [
              'LEFT ordenes_entregas_presupuestos as f' => 'p.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto',
              'LEFT clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
              'LEFT detalles_pagos as dp' => 'p.id_pago = dp.id_pago',
              'LEFT metodos_pagos as mp' => 'dp.id_metodo_pago = mp.id_metodo_pago',
              'LEFT monedas as m' => 'dp.id_moneda = m.id_moneda',
      ],
      'WHERE' => [
        'p.fecha_pago' => [
            '>=' => $this->filtros['fecha_cierre'] . ' 00:00:00',
            '<=' => $this->filtros['fecha_cierre'] . ' 23:59:59',
        ]
      ],
      'ORDER' => 'p.fecha_pago DESC'
    ];

    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros en la fecha seleccionada',
        'icono' => 'warning',
      ];
    }

    // Acumulador para la sumatoria total
    $totalGeneralDia = 0;

    foreach ($infoCeldas as &$fila) {
      $totalGeneralDia += floatval($fila['monto_pago_bs']);  
      $fila['fecha_pago'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_pago']);

      // Formatear montos con su símbolo y decimales
      $montoOriginal = number_format($fila['monto_pago'], 2, ',', '.');
      $fila['monto_detalle'] = $fila['nombre_metodo_pago'] . ' (' . $fila['simbolo_moneda'] . ' ' . $montoOriginal . ')';
      
      // Monto a la moneda local
      $fila['monto_pago_bs'] = number_format($fila['monto_pago_bs'], 2, ',', '.') . ' Bs';

      // Limpiar los campos temporales 
      unset($fila['monto_pago'], $fila['simbolo_moneda'], $fila['nombre_metodo_pago']);
    }
    unset($fila);

    $infoCeldas[] = [
        'id_orden_entrega_presupuesto' => '',
        'rif_cedula_cliente'           => '',
        'fecha_pago'                   => 'TOTAL DEL DÍA:',
        'monto_detalle'                => '',
        'monto_pago_bs'                => number_format($totalGeneralDia, 2, ',', '.') . ' Bs'
    ];

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE CIERRE DE CAJA');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE CIERRE DE CAJA",
      "configColumnas" => [
        'id_orden_entrega_presupuesto' => ['NRO FACTURA', 40],
        'rif_cedula_cliente' => ['CLIENTE', 40],
        'fecha_pago' => ['FECHA', 40],
        'monto_detalle' => ['FORMA DE PAGO', 40],
        'monto_pago_bs' => ['MONTO TOTAL', 35],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
}
