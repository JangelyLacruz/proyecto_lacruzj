<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\traitModelo;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;
use PDO;

class reportesEstadisticosModelo extends conexion {
  use traitModelo;

  public function validarReportesEstadisticos(string $permiso, ?array &$info = null, ?array $requerido = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('reportesEstadisticos', $permiso);
    if ($v) return $v;

    if ($info === null) return false;

    if (isset($info['fecha_inicio']) && $info['fecha_inicio'] === '') {
      unset($info['fecha_inicio']);
    }
    if (isset($info['fecha_fin']) && $info['fecha_fin'] === '') {
      unset($info['fecha_fin']);
    }

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'rango' => [
          ...molNombreObj,
          'nombreAlerta' => 'rango de fechas'
        ],
        'fecha_inicio' => [
          'tipo' => 'string',
          'regex' => '^\d{4}-\d{2}-\d{2}$',
          'nombreAlerta' => 'fecha de inicio'
        ],
        'fecha_fin' => [
          'tipo' => 'string',
          'regex' => '^\d{4}-\d{2}-\d{2}$',
          'nombreAlerta' => 'fecha de fin'
        ]
      ],
      'requerido' => $requerido ?? []
    ];
    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }

  public function obtenerDatosDashboard(array $datos) {
    $v = $this->validarReportesEstadisticos('ver reportes estadísticos', $datos);
    if ($v) return $v;

    // Sanitización y armado de filtros
    $filtros = [
      'ventas' => "AND o.fecha_orden_entrega_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
      'compras' => "AND c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
      'produccion' => "AND p.fecha_produccion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
    ];

    if (isset($datos['rango'])) {
      if ($datos['rango'] === 'ultimos_3_meses') {
        $filtros['ventas'] = "AND o.fecha_orden_entrega_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        $filtros['compras'] = "AND c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        $filtros['produccion'] = "AND p.fecha_produccion >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
      } elseif ($datos['rango'] === 'personalizado' && !empty($datos['fecha_inicio']) && !empty($datos['fecha_fin'])) {
        $inicio = $datos['fecha_inicio'];
        $fin = $datos['fecha_fin'] . ' 23:59:59';
        $filtros['ventas'] = "AND o.fecha_orden_entrega_presupuesto BETWEEN '$inicio' AND '$fin'";
        $filtros['compras'] = "AND c.fecha_compra BETWEEN '$inicio' AND '$fin'";
        $filtros['produccion'] = "AND p.fecha_produccion BETWEEN '$inicio' AND '$fin'";
      }
    }

    return $this->obtenerDatosDashboardP($filtros);
  }

  private function obtenerDatosDashboardP(array $filtros) {
    $this->conectar();

    try {
      $formatChartData = function ($data, $labelKey, $valueKey) {
        if (!$data || empty($data)) return ['labels' => [], 'data' => []];
        $labels = [];
        $values = [];
        foreach ($data as $row) {
          $labels[] = $row[$labelKey];
          $values[] = $row[$valueKey];
        }
        return ['labels' => $labels, 'data' => $values];
      };

      $fVentas = $filtros['ventas'];
      $fCompras = $filtros['compras'];
      $fProd = $filtros['produccion'];

      // KPIs
      $stmt = self::$conexion->prepare("SELECT COUNT(rif_cedula_cliente) FROM clientes");
      $stmt->execute();
      $nuevosClientes = $stmt->fetchColumn() ?: 0;

      $stmt = self::$conexion->prepare("SELECT COUNT(id_produccion) FROM producciones p WHERE p.status = 1 $fProd");
      $stmt->execute();
      $producciones = $stmt->fetchColumn() ?: 0;

      $stmt = self::$conexion->prepare("SELECT COUNT(id_orden_entrega_presupuesto) FROM ordenes_entregas_presupuestos WHERE status = 5");
      $stmt->execute();
      $pedidosPendientes = $stmt->fetchColumn() ?: 0;
      $kpis = ['nuevosClientes' => $nuevosClientes, 'producciones' => $producciones, 'pedidosPendientes' => $pedidosPendientes];

      // Top Productos
      $stmt = self::$conexion->prepare("SELECT pr.nombre_producto as nombre, SUM(pof.cantidad_producto) as total_vendido FROM productos_ordenes_entregas_presupuestos pof INNER JOIN ordenes_entregas_presupuestos o ON pof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto INNER JOIN presentaciones_productos pp ON pof.id_presentacion_producto = pp.id_presentacion_producto INNER JOIN productos pr ON pp.id_producto = pr.id_producto WHERE o.status != 2 AND pof.status = 1 $fVentas GROUP BY pr.id_producto ORDER BY total_vendido DESC LIMIT 5");
      $stmt->execute();
      $topProductos = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre', 'total_vendido');

      // Top Servicios
      $stmt = self::$conexion->prepare("SELECT s.nombre_servicio as nombre, COUNT(sof.id_servicio_factura) as total_vendido FROM servicios_ordenes_entregas_presupuestos sof INNER JOIN ordenes_entregas_presupuestos o ON sof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto INNER JOIN servicios s ON sof.id_servicio = s.id_servicio WHERE o.status != 2 AND sof.status = 1 $fVentas GROUP BY s.id_servicio ORDER BY total_vendido DESC LIMIT 5");
      $stmt->execute();
      $topServicios = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre', 'total_vendido');

      // Top Clientes
      $stmt = self::$conexion->prepare("SELECT c.razon_social_cliente as nombre, COUNT(o.id_orden_entrega_presupuesto) as total_pedidos FROM ordenes_entregas_presupuestos o INNER JOIN clientes c ON o.rif_cedula_cliente = c.rif_cedula_cliente WHERE o.status != 2 $fVentas GROUP BY c.rif_cedula_cliente ORDER BY total_pedidos DESC LIMIT 5");
      $stmt->execute();
      $topClientes = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre', 'total_pedidos');

      // Ingresos vs Egresos Mensuales (Línea de tiempo de los últimos 6 meses)
      $stmt = self::$conexion->prepare("SELECT DATE_FORMAT(o.fecha_orden_entrega_presupuesto, '%Y-%m') as mes, COUNT(o.id_orden_entrega_presupuesto) as ingresos_cant FROM ordenes_entregas_presupuestos o WHERE o.status != 0 AND o.status != 2 GROUP BY mes ORDER BY mes ASC");
      $stmt->execute();
      $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt = self::$conexion->prepare("SELECT DATE_FORMAT(c.fecha_compra, '%Y-%m') as mes, COUNT(c.id_compra) as egresos_cant FROM compras c WHERE c.status != 0 AND c.status != 2 GROUP BY mes ORDER BY mes ASC");
      $stmt->execute();
      $egresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Generar los últimos 6 meses continuos para garantizar el trazado de la línea
      $meses = [];
      for ($i = 5; $i >= 0; $i--) {
        $meses[] = date('Y-m', strtotime("-$i months"));
      }

      // Si hay meses en los datos más antiguos o diferentes, incluirlos
      foreach (array_merge(array_column($ingresos, 'mes'), array_column($egresos, 'mes')) as $m) {
        if (!empty($m) && !in_array($m, $meses)) {
          $meses[] = $m;
        }
      }
      sort($meses);

      $ingresosData = [];
      $egresosData = [];
      foreach ($meses as $mes) {
        $keyIn = array_search($mes, array_column($ingresos, 'mes'));
        $ingresosData[] = $keyIn !== false ? (int)$ingresos[$keyIn]['ingresos_cant'] : 0;
        $keyEg = array_search($mes, array_column($egresos, 'mes'));
        $egresosData[] = $keyEg !== false ? (int)$egresos[$keyEg]['egresos_cant'] : 0;
      }
      $ingresosEgresos = ['fechas' => $meses, 'ingresos' => $ingresosData, 'egresos' => $egresosData];

      // Productos Vs Servicios
      $stmt1 = self::$conexion->prepare("SELECT COUNT(pof.id_producto_factura) as cant FROM productos_ordenes_entregas_presupuestos pof INNER JOIN ordenes_entregas_presupuestos o ON pof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto WHERE o.status != 2 AND pof.status = 1 $fVentas");
      $stmt1->execute();
      $p = $stmt1->fetchColumn() ?: 0;
      $stmt2 = self::$conexion->prepare("SELECT COUNT(sof.id_servicio_factura) as cant FROM servicios_ordenes_entregas_presupuestos sof INNER JOIN ordenes_entregas_presupuestos o ON sof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto WHERE o.status != 2 AND sof.status = 1 $fVentas");
      $stmt2->execute();
      $s = $stmt2->fetchColumn() ?: 0;
      $productosVsServicios = ['productos' => $p, 'servicios' => $s, 'vacio' => ($p == 0 && $s == 0)];

      // Ventas por Día de la Semana
      $dias = ['Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'];
      $dataMap = array_fill_keys(array_values($dias), 0);
      $stmt = self::$conexion->prepare("SELECT DAYNAME(o.fecha_orden_entrega_presupuesto) as dia_en, COUNT(o.id_orden_entrega_presupuesto) as total FROM ordenes_entregas_presupuestos o WHERE o.status != 2 $fVentas GROUP BY dia_en");
      $stmt->execute();
      foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($dias[$row['dia_en']])) $dataMap[$dias[$row['dia_en']]] = (int)$row['total'];
      }
      $ventasPorDia = ['labels' => array_keys($dataMap), 'data' => array_values($dataMap)];

      // Top Proveedores
      $stmt = self::$conexion->prepare("SELECT pr.razon_social_proveedor as nombre, COUNT(c.id_compra) as total_compras FROM compras c INNER JOIN proveedores pr ON c.rif_proveedor = pr.rif_proveedor WHERE c.status = 1 $fCompras GROUP BY pr.rif_proveedor ORDER BY total_compras DESC LIMIT 5");
      $stmt->execute();
      $topProveedores = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre', 'total_compras');

      // Historial Produccion
      $stmt = self::$conexion->prepare("SELECT DATE_FORMAT(p.fecha_produccion, '%Y-%m-%d') as fecha, SUM(pp.cantidad_producida) as cantidad FROM producciones p INNER JOIN productos_producciones pp ON p.id_produccion = pp.id_produccion WHERE p.status = 1 $fProd GROUP BY fecha ORDER BY fecha ASC LIMIT 30");
      $stmt->execute();
      $historialProduccion = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'fecha', 'cantidad');

      // Cuentas por Cobrar
      $stmt = self::$conexion->prepare("SELECT COUNT(id_orden_entrega_presupuesto) FROM ordenes_entregas_presupuestos WHERE status IN (1, 10)");
      $stmt->execute();
      $pendientes = $stmt->fetchColumn() ?: 0;
      $stmt = self::$conexion->prepare("SELECT COUNT(id_orden_entrega_presupuesto) FROM ordenes_entregas_presupuestos WHERE status IN (3, 11)");
      $stmt->execute();
      $pagadas = $stmt->fetchColumn() ?: 0;
      $cuentasPorCobrar = ['pendientes' => $pendientes, 'pagadas' => $pagadas, 'vacio' => ($pendientes == 0 && $pagadas == 0)];

      // Top Materias Primas
      $stmt = self::$conexion->prepare("SELECT mp.nombre_materia_prima as nombre, SUM(mpc.cantidad_materia_prima) as cantidad FROM materias_primas_compras mpc INNER JOIN compras c ON mpc.id_compra = c.id_compra INNER JOIN materias_primas mp ON mpc.id_materia_prima = mp.id_materia_prima WHERE c.status = 1 $fCompras GROUP BY mp.id_materia_prima ORDER BY cantidad DESC LIMIT 5");
      $stmt->execute();
      $consumoMateriasPrimas = $formatChartData($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre', 'cantidad');

      // Actividad Reciente
      $stmt = self::$conexion->prepare("(SELECT 'Venta' as tipo, id_orden_entrega_presupuesto as id, c.razon_social_cliente as referencia, fecha_orden_entrega_presupuesto as fecha FROM ordenes_entregas_presupuestos o INNER JOIN clientes c ON o.rif_cedula_cliente = c.rif_cedula_cliente WHERE o.status != 2 ORDER BY fecha DESC LIMIT 3) UNION ALL (SELECT 'Compra' as tipo, id_compra as id, pr.razon_social_proveedor as referencia, fecha_compra as fecha FROM compras co INNER JOIN proveedores pr ON co.rif_proveedor = pr.rif_proveedor WHERE co.status = 1 ORDER BY fecha DESC LIMIT 3) ORDER BY fecha DESC LIMIT 5");
      $stmt->execute();
      $actividadReciente = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

      // Consolidar Resultado Final
      $resultado = [
        'tipo' => 'datos',
        'datos' => [
          'topProductos' => $topProductos,
          'topServicios' => $topServicios,
          'topClientes' => $topClientes,
          'ingresosEgresos' => $ingresosEgresos,
          'productosVsServicios' => $productosVsServicios,
          'ventasPorDia' => $ventasPorDia,
          'topProveedores' => $topProveedores,
          'historialProduccion' => $historialProduccion,
          'cuentasPorCobrar' => $cuentasPorCobrar,
          'consumoMateriasPrimas' => $consumoMateriasPrimas,
          'actividadReciente' => $actividadReciente,
          'kpis' => $kpis
        ]
      ];

      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'reportesEstadisticos',
        'accion' => 'Consultar datos de reportes estadísticos',
        'resultado' => 'Éxito'
      ]);

      return $resultado;
    } catch (\Throwable $th) {
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'reportesEstadisticos',
        'accion' => 'Consultar datos de reportes estadísticos',
        'resultado' => 'Fracaso'
      ]);

      return [
        'tipo' => 'simple',
        'titulo' => 'Error al cargar estadísticas',
        'texto' => 'Hubo un problema al procesar los datos: ' . $th->getMessage(),
        'icono' => 'error'
      ];
    }
  }
}