<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\serviciosModelo;
use src\modelos\productosModelo;
use src\modelos\accesosModelo;
use src\modelos\ordenesEntregasPresupuestosModelo;
use PDO;

class ordenesServiciosModelo extends conexion {
  private string $idOrdenServicio = '';
  private int $nuevoStatus = 0;
  private string $nuevaFechaEjecucion = '';
  private array $productosAsociados = [];

  public function validarOrdenesServicios(string $permiso, array &$infoVal, array $camposVal) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('ordenesServicios', $permiso);
    if ($r) return $r;

    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_servicio_factura' => [
          "campo_nombre" => "id_servicio_factura",
          "campo_valor" => &$valor,
          "formulario_nombre" => "ID de la orden de servicio",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "servicios_ordenes_entregas_presupuestos",
          "debeExistir" => true,
        ],
        'status' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "estado de la orden",
          "requerido" => true,
          "minimo" => minRegexStatus,
          "maximo" => maxRegexStatus,
          "expresion_re" => regexStatus,
        ],
        'fecha_ejecucion' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "fecha de ejecución",
          "requerido" => false,
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
  
  public function listarOrdenesServicios(array $info) {
    if (($info['id_servicio_factura'] ?? '') != "") {
      $resultado = $this->validarOrdenesServicios('ver', $info, ['id_servicio_factura']);
      if ($resultado) return $resultado;
      $this->idOrdenServicio = $info['id_servicio_factura'];
    }
    return $this->listarOrdenesServiciosP($info);
  }
  
  public function actualizarOrdenesServicio(array $info) {
    $resultado = $this->validarOrdenesServicios('actualizar', $info, ['id_servicio_factura', 'status']);
    if ($resultado) return $resultado;

    $this->idOrdenServicio = $info['id_servicio_factura'];
    $this->nuevoStatus = (int)$info['status'];
    $this->nuevaFechaEjecucion = $info['fecha_ejecucion'] ?? '';

    return $this->actualizarOrdenesServicioP();
  }

  private function listarOrdenesServiciosP(array $info) {
    if ($this->idOrdenServicio != '') {
      return $this->seleccionarOrdenesServiciosP();
    }

    $ordenes = $this->seleccionarDatos2([
      'campos' => '
                sf.id_servicio_factura,
                sf.fecha_ejecucion,
                sf.cantidad_servicio,
                sf.status as status_orden,
                sf.es_precio_mapfre,
                sf.precio_servicio_mapfre,
                s.nombre_servicio,
                s.precio_servicio,
                s.id_servicio,
                s.foto_servicio,
                fa.id_orden_entrega_presupuesto,
                fa.fecha_orden_entrega_presupuesto,
                fa.status as status_factura,
                cl.rif_cedula_cliente,
                cl.razon_social_cliente,
                cl.telefono_cliente,
                cl.correo_cliente,
                cl.direccion_cliente,
                di.id_direccion,
                di.id_ruta,
                latd.coordenada_latitud,
                lond.coordenada_longitud,
                ru.nombre_ruta,
                CASE 
                    WHEN sf.es_precio_mapfre = 1 THEN sf.precio_servicio_mapfre
                    ELSE s.precio_servicio
                END as precio_real
            ',
      'tabla' => 'servicios_ordenes_entregas_presupuestos as sf',
      'datosJoins' => [
        'servicios as s' => 'sf.id_servicio = s.id_servicio',
        'ordenes_entregas_presupuestos as fa' => 'sf.id_orden_entrega_presupuesto = fa.id_orden_entrega_presupuesto',
        'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente',
        'direcciones as di' => 'sf.id_direccion = di.id_direccion',
        'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
        'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
        'rutas as ru' => 'di.id_ruta = ru.id_ruta',
      ],
      'WHERE' => [
        'fa.status' => ['!=' => 0]
      ],
      'ORDER' => 'sf.fecha_ejecucion ASC'
    ])->fetchAll();

    $fechaActual = date('Y-m-d');

    foreach ($ordenes as &$orden) {
      $fechaEjecucion = date('Y-m-d', strtotime($orden['fecha_ejecucion']));

      if ($orden['status_orden'] == 1 && $fechaEjecucion < $fechaActual) {
        $orden['status_orden'] = 3;
        $orden['esta_retrasado'] = true;
      } else {
        $orden['esta_retrasado'] = false;
      }

      $orden['url_direccion'] = "https://maps.google.com/?q={$orden['coordenada_latitud']},{$orden['coordenada_longitud']}";
    }
    unset($orden);

    return $ordenes;
  }

  private function seleccionarOrdenesServiciosP() {
    $orden = $this->seleccionarDatos2([
      'campos' => '
        sf.id_servicio_factura,
        sf.fecha_ejecucion,
        sf.cantidad_servicio,
        sf.status as status_orden,
        sf.es_precio_mapfre,
        sf.precio_servicio_mapfre,
        s.nombre_servicio,
        s.precio_servicio,
        s.id_servicio,
        s.foto_servicio,
        s.mostrar_ecommerce,
        fa.id_orden_entrega_presupuesto,
        fa.fecha_orden_entrega_presupuesto,
        fa.status as status_factura,
        cl.rif_cedula_cliente,
        cl.razon_social_cliente,
        cl.telefono_cliente,
        cl.correo_cliente,
        cl.direccion_cliente,
        di.id_direccion,
        di.id_ruta,
        latd.coordenada_latitud,
        lond.coordenada_longitud,
        ru.nombre_ruta,
        ru.precio_ruta,
        ru.minimo_km_ruta,
        ru.maximo_km_ruta
      ',
      'tabla' => 'servicios_ordenes_entregas_presupuestos as sf',
      'datosJoins' => [
        'servicios as s' => 'sf.id_servicio = s.id_servicio',
        'ordenes_entregas_presupuestos as fa' => 'sf.id_orden_entrega_presupuesto = fa.id_orden_entrega_presupuesto',
        'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente',
        'direcciones as di' => 'sf.id_direccion = di.id_direccion',
        'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
        'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
        'rutas as ru' => 'di.id_ruta = ru.id_ruta',
      ],
      'WHERE' => [
        'sf.id_servicio_factura' => $this->idOrdenServicio,
        'fa.status' => ['!=' => 0]
      ]
    ])->fetch();

    if (!$orden) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Orden no encontrada',
        'texto' => 'No se encontró la orden de servicio solicitada o la factura asociada está anulada',
        'icono' => 'error'
      ];
    }

    $precioUnitario = $orden['es_precio_mapfre'] == 1
      ? (float)$orden['precio_servicio_mapfre']
      : (float)$orden['precio_servicio'];

    $orden['precio_unitario_real'] = $precioUnitario;
    $orden['subtotal_real'] = $precioUnitario * (float)$orden['cantidad_servicio'];

    $orden['url_direccion'] = "https://maps.google.com/?q={$orden['coordenada_latitud']},{$orden['coordenada_longitud']}";

    $objServicios = new serviciosModelo();
    $servicioCatalogo = $objServicios->seleccionarServicios(['id_servicio' => $orden['id_servicio']]);

    if (isset($servicioCatalogo['detallesExtra']['productos_servicio'])) {
      $productosRequeridos = $servicioCatalogo['detallesExtra']['productos_servicio'];
      $objProductos = new productosModelo();
      foreach ($productosRequeridos as &$prodReq) {
        $productoCompleto = $objProductos->seleccionarProductos(['id_producto' => $prodReq['id_producto']]);
        if (is_array($productoCompleto) && !isset($productoCompleto['icono'])) {
          $prodReq = array_merge($prodReq, $productoCompleto);
        }
      }
      unset($prodReq);
      $this->productosAsociados = $productosRequeridos;
    } else {
      $this->productosAsociados = [];
    }

    $fechaActual = date('Y-m-d');
    $fechaEjecucion = date('Y-m-d', strtotime($orden['fecha_ejecucion']));

    if ($orden['status_orden'] == 1 && $fechaEjecucion < $fechaActual) {
      $orden['status_orden'] = 3;
      $orden['esta_retrasado'] = true;
    } else {
      $orden['esta_retrasado'] = false;
    }

    return $orden;
  }

  private function actualizarOrdenesServicioP() {
    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesServicios',
        'accion' => 'Actualizar con id ' . $this->idOrdenServicio,
        'resultado' => 'Fallido',
        'commit' => true,
      ]);
    };
    
    $ordenAntes = $this->listarOrdenesServicios(['id_servicio_factura' => $this->idOrdenServicio]);
    if (isset($ordenAntes['icono']) && $ordenAntes['icono'] == 'error') {
      return $ordenAntes;
    }

    $statusAnterior = (int)$ordenAntes['status_orden'];
    $cantidadServicio = (float)$ordenAntes['cantidad_servicio'];

    $transicionesPermitidas = [
      1 => [1, 2, 4],
      2 => [],
      3 => [1, 2, 4],
      4 => [],
    ];

    if (!in_array($this->nuevoStatus, $transicionesPermitidas[$statusAnterior] ?? [])) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Transición no permitida',
        'texto' => 'No se puede cambiar el estado de la orden de esta manera',
        'icono' => 'warning'
      ];
    }

    $permitirCambioFecha = ($this->nuevoStatus == 1);

    if ($this->nuevaFechaEjecucion != '' && !$permitirCambioFecha) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Acción no permitida',
        'texto' => 'No se puede cambiar la fecha de una orden que se está cancelando o ejecutando',
        'icono' => 'warning'
      ];
    }

    $datosActualizar = ['status' => $this->nuevoStatus];
    if ($permitirCambioFecha && !empty($this->nuevaFechaEjecucion)) {
      $datosActualizar['fecha_ejecucion'] = $this->nuevaFechaEjecucion;
    }

    if ($this->nuevoStatus == 2 && $statusAnterior != 2) {
      foreach ($this->productosAsociados as $producto) {
        $cantidadADescontar = $producto['cantidad_producto'] * $cantidadServicio;
        $stockActual = $this->seleccionarDatos2([
          'campos' => 'stock_producto',
          'tabla' => 'productos',
          'WHERE' => ['id_producto' => $producto['id_producto']]
        ])->fetch(PDO::FETCH_COLUMN);

        if ($stockActual < $cantidadADescontar) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Stock insuficiente',
            'texto' => 'No hay suficiente stock de ' . ($producto['nombre_producto'] ?? 'un material') . ' para ejecutar este servicio. Stock actual: ' . $stockActual . ', Requerido: ' . $cantidadADescontar,
            'icono' => 'error'
          ];
        }

        $resultado = $this->actualizarDatos2([
          'tabla' => 'productos',
          'datos' => ['stock_producto' => $stockActual - $cantidadADescontar],
          'WHERE' => ['id_producto' => $producto['id_producto']]
        ]);

        if ($resultado === false || $resultado <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error al descontar stock',
            'texto' => 'No se pudo descontar el stock del producto',
            'icono' => 'error'
          ];
        }
      }
    }

    $resultado = $this->actualizarDatos2([
      'tabla' => 'servicios_ordenes_entregas_presupuestos',
      'datos' => $datosActualizar,
      'WHERE' => ['id_servicio_factura' => $this->idOrdenServicio]
    ]);

    if ($resultado === false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al actualizar',
        'texto' => 'No se pudo actualizar la orden',
        'icono' => 'error'
      ];
    }
    
    $objOep = new ordenesEntregasPresupuestosModelo();
    $detalleOep = $objOep->ObtenerDetalleOrdenInterno($ordenAntes['id_orden_entrega_presupuesto']);
    
    if (isset($detalleOep['cabecera'])) {
      $restante = floatval($detalleOep['cabecera']['restante'] ?? 0);
      
      if ($this->nuevoStatus == 4 && $restante < -0.01) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Pago excede nuevo total',
          'texto' => 'No se puede cancelar el servicio porque los pagos ya registrados en la OEP excederían el nuevo total a pagar. Ajuste o anule los pagos primero.',
          'icono' => 'error'
        ];
      }

      $tienePendientes = false;
      $tieneEjecutados = false;
      if (isset($detalleOep['servicios'])) {
        foreach ($detalleOep['servicios'] as $srv) {
          if ($srv['status'] == 1) $tienePendientes = true;
          if ($srv['status'] == 2) $tieneEjecutados = true;
        }
      }
      
      if (!$tienePendientes && $tieneEjecutados) {
        $statusOep = (int)$detalleOep['cabecera']['status'];
        if ($statusOep == 1 || $statusOep == 10) {
          $nuevoStatusOep = ($restante <= 0) ? 13 : 12; 
          $this->actualizarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos',
            'datos' => ['status' => $nuevoStatusOep],
            'WHERE' => ['id_orden_entrega_presupuesto' => $ordenAntes['id_orden_entrega_presupuesto']]
          ]);
        }
      }
    
      if (!$tienePendientes && !$tieneEjecutados) {
        $statusOep = (int)$detalleOep['cabecera']['status'];
        if ($statusOep == 1 || $statusOep == 10) {
          $this->actualizarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos',
            'datos' => ['status' => 2], 
            'WHERE' => ['id_orden_entrega_presupuesto' => $ordenAntes['id_orden_entrega_presupuesto']]
          ]);
        }
      }
    }

    $ordenDespues = $this->listarOrdenesServicios(['id_servicio_factura' => $this->idOrdenServicio]);

    $objBitacora->registrarBitacora([
      'modulo' => 'ordenesServicios',
      'accion' => 'Actualizar con id ' . $this->idOrdenServicio,
      'resultado' => 'Éxito',
      'viejo' => $ordenAntes,
      'nuevo' => $ordenDespues
    ]);
    
    $estadosTexto = [1 => 'Pendiente', 2 => 'Ejecutada', 3 => 'Retrasada', 4 => 'Cancelada'];

    $objMensajesWS = new mensajesWSModelo();
    $objMensajesWS->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'rol',
        'rol' => 'ADMINISTRADOR'
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'ordenesServicios'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Orden de servicio',
            'texto' => 'La orden #' . $this->idOrdenServicio . ' ha sido ' . ($estadosTexto[$this->nuevoStatus] ?? 'actualizada'),
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
        [
          'accion' => "actDT",
          'modulo' => 'ordenesServicios'
        ],
      ],
      'noCommit' => true
    ]);
    $this->commit();

    $mensajeExito = '';
    if ($this->nuevoStatus == 4) {
      $mensajeExito = 'La orden ha sido cancelada exitosamente y el stock ha sido devuelto.';
    } elseif ($this->nuevoStatus == 2) {
      $mensajeExito = 'La orden ha sido marcada como ejecutada.';
    } else {
      $mensajeExito = 'La orden ha sido actualizada exitosamente';
      if (!empty($this->nuevaFechaEjecucion)) {
        $mensajeExito .= ' y la fecha ha sido reprogramada.';
      }
    }

    return [
      'tipo' => 'limpiarYcerrar',
      'titulo' => 'Orden actualizada',
      'texto' => $mensajeExito,
      'icono' => 'success'
    ];
  }
}