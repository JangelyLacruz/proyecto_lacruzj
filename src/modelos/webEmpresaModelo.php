<?php

namespace src\modelos;

use PDO;
use DateTime;
use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;

class webEmpresaModelo extends conexion {

  // Atributos privados
  private string $cedulaCliente = '';
  private array $productosPedido = [];
  private array $serviciosPedido = [];
  private array $deliveryPedido = [];
  private array $pagoPedido = [];

  public function __construct() {
    $this->cedulaCliente = $_SESSION['cedula'] ?? '';
  }

  // #region [ MÉTODOS PÚBLICOS ]

  public function obtenerCatalogoProductos() {
    return $this->obtenerCatalogoProductosP();
  }

  public function obtenerCatalogoServicios() {
    return $this->obtenerCatalogoServiciosP();
  }

  public function obtenerMonedas() {
    return $this->obtenerMonedasP();
  }

  public function verificarSesionCliente() {
    return $this->verificarSesionClienteP();
  }

  public function registrarPedidoWeb(array $datos) {
    if (empty($_SESSION['cedula'])) {
      return [
        'status'  => 'error',
        'icono'   => 'warning',
        'titulo'  => 'Inicio de sesión requerido',
        'mensaje' => 'Debe iniciar sesión como cliente para procesar su pedido.'
      ];
    }

    $this->cedulaCliente = $_SESSION['cedula'];

    $prods = $datos['productos'] ?? [];
    if (is_string($prods)) {
      $prods = json_decode($prods, true) ?: [];
    }
    $this->productosPedido = is_array($prods) ? $prods : [];

    $servs = $datos['servicios'] ?? [];
    if (is_string($servs)) {
      $servs = json_decode($servs, true) ?: [];
    }
    $this->serviciosPedido = is_array($servs) ? $servs : [];

    $delivery = $datos['delivery'] ?? [];
    if (is_string($delivery)) {
      $delivery = json_decode($delivery, true) ?: [];
    }
    if (!empty($datos['direccion_entrega'])) {
      $delivery['direccion'] = $datos['direccion_entrega'];
      $delivery['tipo'] = $datos['tipo_entrega'] ?? 'delivery';
    }
    $this->deliveryPedido = is_array($delivery) ? $delivery : [];

    $pago = $datos['pago'] ?? [];
    if (is_string($pago)) {
      $pago = json_decode($pago, true) ?: [];
    }
    if (!empty($datos['metodo_pago'])) {
      $pago['metodo'] = $datos['metodo_pago'];
    }
    $this->pagoPedido = is_array($pago) ? $pago : [];

    if (empty($this->productosPedido) && empty($this->serviciosPedido)) {
      return [
        'status'  => 'error',
        'icono'   => 'error',
        'titulo'  => 'Carrito vacío',
        'mensaje' => 'Debe agregar al menos un producto o servicio al pedido.'
      ];
    }

    return $this->registrarPedidoWebP();
  }

  public function consultarPedidosCliente() {
    if (empty($_SESSION['cedula'])) {
      return [
        'status'  => 'error',
        'mensaje' => 'Sesión no iniciada'
      ];
    }
    return $this->consultarPedidosClienteP();
  }

  public function cerrarSesionWeb() {
    return $this->cerrarSesionWebP();
  }

  // #endregion [ MÉTODOS PÚBLICOS ]

  // #region [ MÉTODOS PRIVADOS (P) ]

  private function obtenerCatalogoProductosP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        p.id_producto,
        p.nombre_producto,
        p.stock_producto,
        p.precio_producto,
        cp.nombre_categoria_producto,
        um.nombre_unidad_medida,
        pp.id_presentacion_producto,
        pp.foto_presentacion,
        pre.nombre_presentacion,
        pre.cantidad_pmp,
        ROUND((
          (
            (SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1)
            * p.precio_producto
          ) * pre.cantidad_pmp
        ),2) as precio_bs,
        ROUND((p.precio_producto * pre.cantidad_pmp),2) as precio_dolar
      ',
      'tabla'      => 'productos as p',
      'datosJoins' => [
        "unidades_medidas as um"         => "p.id_unidad_medida = um.id_unidad_medida",
        "categorias_productos as cp"     => "p.id_categoria_producto = cp.id_categoria_producto",
        "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
        "presentaciones as pre"          => "pp.id_presentacion = pre.id_presentacion",
      ],
      'WHERE' => [
        'pp.status' => 1,
        'p.status'  => 1,
      ]
    ]);

    return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  private function obtenerCatalogoServiciosP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        s.id_servicio,
        s.nombre_servicio,
        s.precio_servicio,
        s.foto_servicio,
        s.mostrar_ecommerce,
        um.nombre_unidad_medida,
        ROUND((
          (SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1) * s.precio_servicio
        ), 2) as precio_bs,
        ROUND(s.precio_servicio, 2) as precio_dolar
      ',
      'tabla'      => 'servicios as s',
      'datosJoins' => [
        "unidades_medidas as um" => "s.id_unidad_medida = um.id_unidad_medida",
      ],
      'WHERE' => [
        's.status' => 1
      ]
    ]);

    return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  private function obtenerMonedasP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_moneda, nombre_moneda, simbolo_moneda, valor_moneda',
      'tabla'  => 'monedas',
      'ORDER'  => 'id_moneda ASC'
    ]);
    return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  private function verificarSesionClienteP() {
    if (!empty($_SESSION['cedula'])) {
      return [
        'autenticado' => true,
        'esCliente'   => ($_SESSION['rol'] ?? '') == 3,
        'cedula'      => $_SESSION['cedula'],
        'nombre'      => $_SESSION['nombre'] ?? '',
        'apellido'    => $_SESSION['apellido'] ?? '',
        'usuario'     => $_SESSION['usuario'] ?? '',
        'rol'         => $_SESSION['rol'] ?? '',
        'nombreRol'   => $_SESSION['nombreRol'] ?? ''
      ];
    }
    return [
      'autenticado' => false,
      'esCliente'   => false
    ];
  }

  private function registrarPedidoWebP() {
    $cedulaCliente = $this->cedulaCliente;

    // Obtener id_cambio_iva activo
    $ivaActual = $this->seleccionarDatos2([
      'campos' => 'id_cambio_iva',
      'tabla'  => 'cambios_iva',
      'WHERE'  => ['status' => 1],
      'LIMIT'  => 1
    ])->fetch(PDO::FETCH_ASSOC);

    $idCambioIva = $ivaActual['id_cambio_iva'] ?? 258;

    // Generar correlativo de pedido
    $ahora = new DateTime();
    $diaAno = $ahora->format('z') + 1;
    $anoDos = $ahora->format('y');
    $prefijo = sprintf("FACT-%s%03d", $anoDos, $diaAno);

    // Contar cuantos existen hoy para secuencia
    $cont = $this->seleccionarDatos2([
      'campos' => 'COUNT(*) as total',
      'tabla'  => 'ordenes_entregas_presupuestos',
      'WHERE'  => ['id_orden_entrega_presupuesto' => ['LIKE' => $prefijo . '%']]
    ])->fetch(PDO::FETCH_ASSOC);

    $secuencia = ($cont['total'] ?? 0) + 1;
    $aleatorio = rand(10, 99);
    $idOrden = sprintf("%s-%05d-%02d", $prefijo, $secuencia, $aleatorio);

    // 1. Insertar orden principal con estatus 5 (Pendiente por confirmar)
    $resOrden = $this->guardarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'id_orden_entrega_presupuesto'      => $idOrden,
        'cedula_usuario'                    => $cedulaCliente,
        'id_cambio_iva'                     => $idCambioIva,
        'rif_cedula_cliente'                => $cedulaCliente,
        'fecha_orden_entrega_presupuesto'   => date('Y-m-d H:i:s'),
        'status'                            => 5 // Pendiente por confirmar
      ]
    ]);

    if (!$resOrden) {
      $this->rollback();
      return [
        'status'  => 'error',
        'icono'   => 'error',
        'titulo'  => 'Error al registrar',
        'mensaje' => 'No se pudo crear la orden del pedido. Intente nuevamente.'
      ];
    }

    // 2. Insertar productos
    foreach ($this->productosPedido as $prod) {
      $idPresProd = $prod['id_presentacion_producto'] ?? null;
      $cant       = (int) ($prod['cantidad'] ?? 1);
      if ($idPresProd && $cant > 0) {
        $this->guardarDatos2([
          'tabla' => 'productos_ordenes_entregas_presupuestos',
          'datos' => [
            'id_orden_entrega_presupuesto' => $idOrden,
            'id_presentacion_producto'     => $idPresProd,
            'cantidad_producto'            => $cant,
            'status'                       => 1
          ]
        ]);
      }
    }

    // 3. Insertar servicios (usando direccion con ruta activa)
    $dirDefault = $this->seleccionarDatos2([
      'campos'     => 'di.id_direccion',
      'tabla'      => 'direcciones as di',
      'datosJoins' => [
        'rutas as ru' => 'di.id_ruta = ru.id_ruta'
      ],
      'WHERE'      => [
        'di.status' => 1,
        'ru.status' => 1
      ],
      'LIMIT'      => 1
    ])->fetch(PDO::FETCH_ASSOC);
    $idDireccionDefault = $dirDefault['id_direccion'] ?? 52;

    foreach ($this->serviciosPedido as $serv) {
      $idServ = $serv['id_servicio'] ?? null;
      $cantS  = (float) ($serv['cantidad'] ?? 1);
      if ($idServ && $cantS > 0) {
        $this->guardarDatos2([
          'tabla' => 'servicios_ordenes_entregas_presupuestos',
          'datos' => [
            'id_orden_entrega_presupuesto' => $idOrden,
            'id_servicio'                  => $idServ,
            'id_direccion'                 => $idDireccionDefault,
            'fecha_ejecucion'              => date('Y-m-d H:i:s'),
            'cantidad_servicio'            => $cantS,
            'es_precio_mapfre'             => 0,
            'precio_servicio_mapfre'       => 0,
            'status'                       => 1
          ]
        ]);
      }
    }

    // 4. Si solicitó delivery, registrar en tabla deliveries
    if (!empty($this->deliveryPedido['direccion']) && ($this->deliveryPedido['tipo'] ?? 'delivery') === 'delivery') {
      $idDelivery = sprintf("DELI-%s%03d-%05d-%02d", $anoDos, $diaAno, $secuencia, rand(10, 99));
      $this->guardarDatos2([
        'tabla' => 'deliveries',
        'datos' => [
          'id_delivery'                  => $idDelivery,
          'id_orden_entrega_presupuesto' => $idOrden,
          'id_direccion'                 => $idDireccionDefault,
          'cedula_repartidor'            => null,
          'status'                       => 1
        ]
      ]);
    }

    // 5. Registrar en Bitácora
    $objBitacora = new bitacoraModelo();
    $objBitacora->registrarBitacora([
      'modulo'    => 'pedidos',
      'accion'    => 'Registrar pedido cliente web: ' . $idOrden,
      'resultado' => 'Éxito',
      'viejo'     => [],
      'nuevo'     => [
        'id_pedido'      => $idOrden,
        'cedula_cliente' => $cedulaCliente,
        'productos'      => count($this->productosPedido),
        'servicios'      => count($this->serviciosPedido)
      ],
      'commit'    => true
    ]);

    // 6. Notificar a los administradores vía WebSocket
    try {
      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => ['tipo' => 'todos'],
        'cuerpo'   => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'pedidos'],
          ['accion' => "actDT", 'modulo' => 'pedidos'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Nuevo Pedido Web',
              'texto'    => 'Se ha recibido el pedido #' . $idOrden,
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 4000
            ]
          ]
        ],
        'noCommit' => true
      ]);
    } catch (\Throwable $e) {
      error_log("Aviso WS pedidos: " . $e->getMessage());
    }

    $this->commit();

    return [
      'status'    => 'success',
      'icono'     => 'success',
      'titulo'    => '¡Pedido Realizado!',
      'mensaje'   => 'Su pedido #' . $idOrden . ' ha sido recibido exitosamente. En breve nuestro equipo lo confirmará.',
      'id_pedido' => $idOrden
    ];
  }

  private function consultarPedidosClienteP() {
    $cedulaCliente = $this->cedulaCliente;

    $resultado = $this->seleccionarDatos2([
      'campos' => '
        fa.id_orden_entrega_presupuesto as id_pedido,
        fa.id_orden_entrega_presupuesto,
        fa.fecha_orden_entrega_presupuesto,
        fa.status as status_pedido,
        fa.status,
        de.id_delivery,
        de.cedula_repartidor,
        rep.nombre_repartidor as repartidor_nombre
      ',
      'tabla'      => 'ordenes_entregas_presupuestos as fa',
      'datosJoins' => [
        'LEFT deliveries as de'    => 'fa.id_orden_entrega_presupuesto = de.id_orden_entrega_presupuesto',
        'LEFT repartidores as rep' => 'de.cedula_repartidor = rep.cedula_repartidor'
      ],
      'WHERE' => [
        'fa.rif_cedula_cliente' => $cedulaCliente
      ],
      'ORDER' => 'fa.fecha_orden_entrega_presupuesto DESC'
    ]);

    if (!$resultado) return [];

    $pedidos = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Mapear estados amigables para el cliente y consultar detalles de productos
    foreach ($pedidos as &$p) {
      $idPedido = $p['id_pedido'];
      $statusNum = (int) $p['status_pedido'];
      $tieneRepartidor = !empty($p['cedula_repartidor']);

      switch ($statusNum) {
        case 5:
          $p['estado_texto'] = 'Pendiente por confirmar';
          $p['badge_color']  = 'warning text-dark';
          $p['progreso']     = 25;
          $p['descripcion_estado'] = 'Hemos recibido tu pedido y estamos verificando la disponibilidad.';
          break;
        case 7:
          if ($tieneRepartidor) {
            $p['estado_texto'] = 'En camino / Salió de la empresa';
            $p['badge_color']  = 'info text-dark';
            $p['progreso']     = 75;
            $p['descripcion_estado'] = 'Tu pedido ya salió de nuestras instalaciones y va en camino con el repartidor: ' . ($p['repartidor_nombre'] ?? 'Asignado') . '.';
          } else {
            $p['estado_texto'] = 'Confirmado / En preparación';
            $p['badge_color']  = 'primary text-white';
            $p['progreso']     = 50;
            $p['descripcion_estado'] = 'Tu pedido ha sido aprobado y el personal lo está empacando en almacén.';
          }
          break;
        case 8:
          $p['estado_texto'] = 'Entregado';
          $p['badge_color']  = 'success text-white';
          $p['progreso']     = 100;
          $p['descripcion_estado'] = 'El pedido fue entregado y completado satisfactoriamente.';
          break;
        case 6:
          $p['estado_texto'] = 'Cancelado';
          $p['badge_color']  = 'danger text-white';
          $p['progreso']     = 0;
          $p['descripcion_estado'] = 'El pedido no pudo ser procesado o fue cancelado.';
          break;
        default:
          $p['estado_texto'] = 'Procesando';
          $p['badge_color']  = 'secondary text-white';
          $p['progreso']     = 30;
          $p['descripcion_estado'] = 'El pedido se encuentra en gestión interna.';
          break;
      }

      // Consultar productos de la orden
      $resItems = $this->seleccionarDatos2([
        'campos' => '
          pop.id_producto_factura,
          pop.id_presentacion_producto,
          pop.cantidad_producto,
          p.nombre_producto,
          pre.nombre_presentacion,
          ROUND(p.precio_producto * pre.cantidad_pmp, 2) as precio_unitario_dolar,
          ROUND(
            ((SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1) * p.precio_producto) * pre.cantidad_pmp,
            2
          ) as precio_unitario_bs,
          ROUND((p.precio_producto * pre.cantidad_pmp) * pop.cantidad_producto, 2) as subtotal_dolar,
          ROUND(
            (((SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1) * p.precio_producto) * pre.cantidad_pmp) * pop.cantidad_producto,
            2
          ) as subtotal_bs,
          pp.foto_presentacion
        ',
        'tabla' => 'productos_ordenes_entregas_presupuestos as pop',
        'datosJoins' => [
          'presentaciones_productos as pp' => 'pop.id_presentacion_producto = pp.id_presentacion_producto',
          'productos as p'                => 'pp.id_producto = p.id_producto',
          'presentaciones as pre'         => 'pp.id_presentacion = pre.id_presentacion'
        ],
        'WHERE' => [
          'pop.id_orden_entrega_presupuesto' => $idPedido
        ]
      ]);
      $items = $resItems ? $resItems->fetchAll(PDO::FETCH_ASSOC) : [];

      // Consultar servicios de la orden (si los hubiese)
      $resServs = $this->seleccionarDatos2([
        'campos' => '
          sop.id_servicio_factura,
          sop.id_servicio,
          sop.cantidad_servicio,
          s.nombre_servicio,
          ROUND(s.precio_servicio, 2) as precio_unitario_dolar,
          ROUND((SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1) * s.precio_servicio, 2) as precio_unitario_bs,
          ROUND(s.precio_servicio * sop.cantidad_servicio, 2) as subtotal_dolar,
          ROUND(((SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1) * s.precio_servicio) * sop.cantidad_servicio, 2) as subtotal_bs,
          s.foto_servicio
        ',
        'tabla' => 'servicios_ordenes_entregas_presupuestos as sop',
        'datosJoins' => [
          'servicios as s' => 'sop.id_servicio = s.id_servicio'
        ],
        'WHERE' => [
          'sop.id_orden_entrega_presupuesto' => $idPedido
        ]
      ]);
      $servicios = $resServs ? $resServs->fetchAll(PDO::FETCH_ASSOC) : [];

      $totalDolar = 0;
      $totalBs = 0;
      foreach ($items as $it) {
        $totalDolar += (float) ($it['subtotal_dolar'] ?? 0);
        $totalBs += (float) ($it['subtotal_bs'] ?? 0);
      }
      foreach ($servicios as $sv) {
        $totalDolar += (float) ($sv['subtotal_dolar'] ?? 0);
        $totalBs += (float) ($sv['subtotal_bs'] ?? 0);
      }

      $p['items'] = $items;
      $p['servicios'] = $servicios;
      $p['total_dolar'] = number_format($totalDolar, 2, '.', '');
      $p['total_bs'] = number_format($totalBs, 2, '.', '');
      $p['total_items'] = count($items) + count($servicios);
    }
    unset($p);

    return $pedidos;
  }

  private function cerrarSesionWebP() {
    session_destroy();
    return [
      'status'  => 'success',
      'mensaje' => 'Sesión finalizada exitosamente',
      'url'     => APP_URL
    ];
  }

  // #endregion [ MÉTODOS PRIVADOS (P) ]
}
