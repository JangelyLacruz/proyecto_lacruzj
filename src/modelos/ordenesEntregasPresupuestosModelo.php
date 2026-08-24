<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\clientesModelo;
use src\modelos\productosModelo;
use src\modelos\rutasModelo;
use src\modelos\serviciosModelo; 
use src\modelos\repartidoresModelo; 
use src\modelos\pagosModelo; 
use PDO;
use Exception;

class ordenesEntregasPresupuestosModelo extends conexion {
  private string $idOrden = '';
  private string $cedulaUsuario = '';
  private string $rifCliente = '';
  private string $fechaOrden = '';
  private int $idCambioIva = 0;
  private int $status = 1;
  private array $productos = [];
  private array $servicios = [];
  private array $delivery = [];
  private array $pagos = [];

  // PUBLICOS

public function validarOrdenes(string $permiso, array &$info = [], array $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('ordenesEntregasPresupuestos', $permiso);
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_orden_entrega_presupuesto' => [
          ...molIdSeguro,
          "nombreAlerta" => "ID de la Orden",
          "nombreBD" => "id_orden_entrega_presupuesto",
          "tablaBD" => "ordenes_entregas_presupuestos",
          "debeExistirBD" => true,
        ],
        'rif_cedula_cliente' => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "Cliente",
          "nombreBD" => "rif_cedula_cliente",
          "tablaBD" => "clientes",
          "debeExistirBD" => true,
        ],
        'estadoSeleccionado' => [
          'tipo' => 'int',
          'minL' => 1,
          'maxL' => 4,
          'regex' => '^\d{1}$',
          'nombreAlerta' => "estado seleccionado"
        ],
      ],
      'requerido' => $requerido,
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;

    return false;
}
public function ListarOrdenes(array $info = []) {
    $v = $this->validarOrdenes('listar', $info);
    if ($v) return $v;
    return $this->ListarOrdenesP();
}
public function ObtenerOrden(array $info) {
    $v = $this->validarOrdenes('listar', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->ObtenerOrdenP();
}
public function ObtenerDetalleOrden(array $info) {
    $v = $this->validarOrdenes('listar', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->ObtenerDetalleOrdenP();
}
public function RegistrarOrden(array $info) {
    $v = $this->validarOrdenes('registrar', $info, ['rif_cedula_cliente']);
    if ($v) return $v;

    $prods = isset($info['productos']) && is_string($info['productos']) ? json_decode($info['productos'], true) : ($info['productos'] ?? []);
    $servs = isset($info['servicios']) && is_string($info['servicios']) ? json_decode($info['servicios'], true) : ($info['servicios'] ?? []);
    $deli  = isset($info['delivery'])  && is_string($info['delivery'])  ? json_decode($info['delivery'], true)  : ($info['delivery'] ?? []);

    if (empty($prods) && empty($servs)) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Sin artículos',
        'texto'  => 'Debe agregar al menos un producto o servicio',
        'icono'  => 'warning',
      ];
    }

    $this->rifCliente    = $info['rif_cedula_cliente'];
    $this->productos     = $prods;
    $this->servicios     = $servs;
    $this->delivery      = $deli;
    $this->cedulaUsuario = $_SESSION['cedula'] ?? '';
    $this->fechaOrden  = $this->FechaHora_Sel('fecha_hora_BD');

    $estadoSel = intval($info['estadoSeleccionado'] ?? 1);
    if ($estadoSel == 3 || $estadoSel == 4) {
      $this->status = 3;
    } else {
      $this->status = 1;
    }

    return $this->RegistrarOrdenP();
}
public function DespacharOrden(array $info) {
    $v = $this->validarOrdenes('despachar orden', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->DespacharOrdenP();
}
public function AnularOrden(array $info) {
    $v = $this->validarOrdenes('anular', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->AnularOrdenP();
}
public function ListarMetodosPago(array $info = []) {
    $v = $this->validarOrdenes('listar', $info);
    if ($v) return $v;
    return $this->ListarMetodosPagoP();
}
public function RegistrarPago(array $info) {
    $v = $this->validarOrdenes('agregar pago', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $pagos = isset($info['pagos']) && is_string($info['pagos']) ? json_decode($info['pagos'], true) : ($info['pagos'] ?? []);

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    $this->pagos   = $pagos;
    return $this->RegistrarPagoP();
}
public function ObtenerDetalleOrdenInterno(string $idOrden) {
    $this->idOrden = $idOrden;
    return $this->ObtenerDetalleOrdenP();
}

  // PRIVADOS

private function ListarOrdenesP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_orden_entrega_presupuesto,
        c.razon_social_cliente AS CLIENTE,
        f.rif_cedula_cliente,
        f.fecha_orden_entrega_presupuesto,
        f.status,
        (SELECT COUNT(*) FROM productos_ordenes_entregas_presupuestos pf WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status = 1) AS cant_productos,
        (SELECT COUNT(*) FROM servicios_ordenes_entregas_presupuestos sf WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status IN (1, 2)) AS cant_servicios,
        (SELECT COUNT(*) FROM deliveries d WHERE d.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND d.status = 1) AS tiene_delivery,
        ci.monto_cambio_iva,
        (SELECT COALESCE(SUM(pf.cantidad_producto * p.precio_producto), 0) 
         FROM productos_ordenes_entregas_presupuestos pf 
         JOIN presentaciones_productos pp ON pf.id_presentacion_producto=pp.id_presentacion_producto 
         JOIN productos p ON pp.id_producto = p.id_producto 
         WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status = 1) AS sub_prod,
        (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
         FROM servicios_ordenes_entregas_presupuestos sf 
         JOIN servicios s ON sf.id_servicio=s.id_servicio 
         WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status IN (1, 2)) AS sub_serv,
        (SELECT COALESCE(SUM(
           r.precio_ruta * 
           IF(lat.coordenada_latitud LIKE '%|%',
             CAST(SUBSTRING_INDEX(lat.coordenada_latitud, '|', -1) AS DECIMAL(10,2)),
             1
           )
         ), 0) 
         FROM deliveries d 
         JOIN direcciones dir ON d.id_direccion=dir.id_direccion 
         JOIN rutas r ON dir.id_ruta=r.id_ruta 
         LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion=lat.id_latitud_direccion
         WHERE d.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND d.status = 1) AS sub_del,
        (SELECT COALESCE(SUM(
           CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                THEN dp.monto_pago / COALESCE(
                  (SELECT cm.valor_moneda 
                   FROM cambios_monedas cm 
                   JOIN monedas m2 ON cm.id_moneda = m2.id_moneda 
                   WHERE m2.nombre_moneda IN ('DÓLAR', 'DOLAR') 
                     AND cm.fecha_cambio <= pa.fecha_pago 
                   ORDER BY cm.fecha_cambio DESC, cm.id_cambio_moneda DESC LIMIT 1),
                  (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR'))
                )
                ELSE dp.monto_pago END
         ), 0) 
         FROM pagos pa 
         JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
         JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
         WHERE pa.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1) AS total_pagado
      ",
      'tabla' => 'ordenes_entregas_presupuestos as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
        'cambios_iva as ci' => 'f.id_cambio_iva = ci.id_cambio_iva',
      ],
      'ORDER' => 'f.fecha_orden_entrega_presupuesto DESC',
    ]);

    $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filas as &$fila) {
      if ($fila['status'] == 2) {
        $fila['estado_dinamico'] = 'Anulada';
        $fila['estado_num'] = 5;
        continue;
      }

      $iva = floatval($fila['monto_cambio_iva']) / 100;
      $subTotal = floatval($fila['sub_prod']) + floatval($fila['sub_serv']) + floatval($fila['sub_del']);
      $totalOrden = $subTotal + ($subTotal * $iva);

      if ($fila['status'] == 10) {
        $fila['estado_dinamico'] = 'Procesada y Pagada';
        $fila['estado_num'] = 1;
        $pagado = $totalOrden; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } elseif ($fila['status'] == 11) {
        $fila['estado_dinamico'] = 'Pagada y Despachada';
        $fila['estado_num'] = 3;
        $pagado = $totalOrden; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } elseif ($fila['status'] == 13) {
        $fila['estado_dinamico'] = 'Pagada y Ejecutada';
        $fila['estado_num'] = 13;
        $pagado = $totalOrden; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } else {
        $pagado = floatval($fila['total_pagado']);
        $restante = round($totalOrden - $pagado, 2);

        if ($restante <= 0) {
          // Si se pagó todo de una vez
          if ($fila['status'] == 3) {
            $fila['estado_dinamico'] = 'Pagada y Despachada';
            $fila['estado_num'] = 3;
          } elseif ($fila['status'] == 12) {
            $fila['estado_dinamico'] = 'Pagada y Ejecutada';
            $fila['estado_num'] = 13;
          } else {
            $fila['estado_dinamico'] = 'Procesada y Pagada';
            $fila['estado_num'] = 1;
          }
        } else {
          // Si todavía deben algo
          if ($fila['status'] == 3) {
            $fila['estado_dinamico'] = 'Despachada y sin Pago';
            $fila['estado_num'] = 4;
          } elseif ($fila['status'] == 12) {
            $fila['estado_dinamico'] = 'Ejecutada y sin Pago';
            $fila['estado_num'] = 12;
          } else {
            $fila['estado_dinamico'] = 'Procesada y sin Pago';
            $fila['estado_num'] = 2;
          }
        }
      }
      $fila['total_orden'] = $totalOrden;
      $fila['total_pagado'] = $pagado;
    }
    return $filas;
}
private function ObtenerOrdenP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_orden_entrega_presupuesto, f.rif_cedula_cliente,
        f.fecha_orden_entrega_presupuesto, f.status,
        c.razon_social_cliente AS CLIENTE
      ",
      'tabla'  => 'ordenes_entregas_presupuestos as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
      ],
      'WHERE' => ['f.id_orden_entrega_presupuesto' => $this->idOrden],
      'eliminadosYVigentes' => true,
    ]);

    if ($resultado->rowCount() <= 0) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Orden no encontrada',
        'texto'  => 'La orden no existe en el sistema',
        'icono'  => 'error',
      ];
    }

    return $resultado->fetch(PDO::FETCH_ASSOC);
}
private function ObtenerDetalleOrdenP() {
    // Primero buscamos los datos principales de la orden
    $stmtCab = $this->conectar()->prepare("
      SELECT f.id_orden_entrega_presupuesto, f.fecha_orden_entrega_presupuesto, f.status,
             c.razon_social_cliente AS CLIENTE,
             f.rif_cedula_cliente,
             ci.monto_cambio_iva AS IVA,
             (SELECT COALESCE(SUM(
                CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                     THEN dp.monto_pago / COALESCE(
                       (SELECT cm.valor_moneda 
                        FROM cambios_monedas cm 
                        JOIN monedas m2 ON cm.id_moneda = m2.id_moneda 
                        WHERE m2.nombre_moneda IN ('DÓLAR', 'DOLAR') 
                          AND cm.fecha_cambio <= pa.fecha_pago 
                        ORDER BY cm.fecha_cambio DESC, cm.id_cambio_moneda DESC LIMIT 1),
                       (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR'))
                     )
                     ELSE dp.monto_pago END
              ), 0) 
              FROM pagos pa 
              JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
              JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
              WHERE pa.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1) AS total_pagado
      FROM ordenes_entregas_presupuestos f
      JOIN clientes c ON f.rif_cedula_cliente = c.rif_cedula_cliente
      JOIN cambios_iva ci ON f.id_cambio_iva = ci.id_cambio_iva
      WHERE f.id_orden_entrega_presupuesto = :id
    ");
    $stmtCab->execute([':id' => $this->idOrden]);
    $cabecera = $stmtCab->fetch(PDO::FETCH_ASSOC);

    if (!$cabecera) return [];

    // Luego buscamos qué productos se vendieron
    $stmtProd = $this->conectar()->prepare("
      SELECT pf.id_producto_factura, pf.cantidad_producto,
             p.nombre_producto, p.precio_producto,
             pr.nombre_presentacion,
             pp.id_presentacion_producto
      FROM productos_ordenes_entregas_presupuestos pf
      JOIN presentaciones_productos pp
        ON pf.id_presentacion_producto = pp.id_presentacion_producto
      JOIN productos p ON pp.id_producto = p.id_producto
      JOIN presentaciones pr ON pp.id_presentacion = pr.id_presentacion
      WHERE pf.id_orden_entrega_presupuesto = :id AND pf.status = 1
    ");
    $stmtProd->execute([':id' => $this->idOrden]);
    $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

    // También los servicios prestados (datos pivote de la orden)
    $stmtServ = $this->conectar()->prepare("
      SELECT sf.id_servicio_factura, sf.id_servicio, sf.cantidad_servicio, sf.status,
             sf.es_precio_mapfre, sf.precio_servicio_mapfre,
             sf.id_direccion,
             lat.coordenada_latitud, lon.coordenada_longitud
      FROM servicios_ordenes_entregas_presupuestos sf
      LEFT JOIN direcciones dir ON sf.id_direccion = dir.id_direccion
      LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion = lat.id_latitud_direccion
      LEFT JOIN longitudes_direcciones lon ON dir.id_longitud_direccion = lon.id_longitud_direccion
      WHERE sf.id_orden_entrega_presupuesto = :id AND sf.status != 0
    ");
    $stmtServ->execute([':id' => $this->idOrden]);
    $serviciosPivote = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

    // Enriquecemos cada servicio con sus datos base y materiales usando serviciosModelo
    $objServicios = new serviciosModelo();
    $servicios = [];
    foreach ($serviciosPivote as $sp) {
      $infoServ = $objServicios->seleccionarServicios([
        'id_servicio' => $sp['id_servicio'],
        'isInterno' => true
      ]);
      $sp['nombre_servicio'] = $infoServ['nombre_servicio'] ?? '';
      $sp['precio_servicio'] = $infoServ['precio_servicio'] ?? 0;
      // Materiales del servicio
      $sp['materiales'] = $infoServ['detallesExtra']['productos_servicio'] ?? [];
      $servicios[] = $sp;
    }

    // Y por último vemos si tiene algún viaje de delivery asignado
    $stmtDel = $this->conectar()->prepare("
      SELECT d.id_delivery, d.cedula_repartidor, d.id_direccion,
             dir.id_ruta,
             lat.coordenada_latitud, lon.coordenada_longitud
      FROM deliveries d
      JOIN direcciones dir ON d.id_direccion = dir.id_direccion
      LEFT JOIN latitudes_direcciones lat
        ON dir.id_latitud_direccion = lat.id_latitud_direccion
      LEFT JOIN longitudes_direcciones lon
        ON dir.id_longitud_direccion = lon.id_longitud_direccion
      WHERE d.id_orden_entrega_presupuesto = :id AND d.status = 1
    ");
    $stmtDel->execute([':id' => $this->idOrden]);
    $delivery = $stmtDel->fetch(PDO::FETCH_ASSOC);

    // Enriquecemos el delivery con datos de ruta y repartidor usando sus modelos
    if ($delivery) {
      $objRutas = new rutasModelo();
      $infoRuta = $objRutas->seleccionarRutas([
        'id_ruta' => $delivery['id_ruta'],
        'isInterno' => true
      ]);
      $delivery['nombre_ruta'] = $infoRuta['nombre_ruta'] ?? '';
      $delivery['precio_ruta'] = $infoRuta['precio_ruta'] ?? 0;

      if (!empty($delivery['cedula_repartidor'])) {
        $objRepartidores = new repartidoresModelo();
        $infoRep = $objRepartidores->seleccionarRepartidores([
          'cedula_repartidor' => $delivery['cedula_repartidor'],
          'isInterno' => true
        ]);
        $delivery['REPARTIDOR'] = is_array($infoRep) && isset($infoRep['nombre_repartidor'])
          ? $infoRep['nombre_repartidor'] . ' ' . ($infoRep['apellido_repartidor'] ?? '')
          : '';
      } else {
        $delivery['REPARTIDOR'] = '';
      }
    }

    // Acomodamos cómo se va a ver el estado de la orden basándonos en si ya pagaron o no
    if ($cabecera['status'] == 2) {
      $cabecera['estado_dinamico'] = 'Anulada';
      $cabecera['estado_num'] = 5;
    } else {
      $iva = floatval($cabecera['IVA']) / 100;

      $subProd = 0;
      foreach ($productos as $p) $subProd += ($p['cantidad_producto'] * $p['precio_producto']);

      $subServ = 0;
      foreach ($servicios as $s) {
        if ($s['status'] == 1 || $s['status'] == 2) {
          $precio = $s['es_precio_mapfre'] == 1 ? $s['precio_servicio_mapfre'] : $s['precio_servicio'];
          $subServ += ($s['cantidad_servicio'] * $precio);
        }
      }
      $subDel = 0;
      if ($delivery) {
        $precioRuta = floatval($delivery['precio_ruta']);
        $parts = explode('|', $delivery['coordenada_latitud']);
        if (count($parts) > 1) {
          $distancia = floatval($parts[1]);
          $subDel = $precioRuta * $distancia;
          $delivery['coordenada_latitud'] = $parts[0]; // Le quitamos la parte extra a las coordenadas para que el mapa no se vuelva loco
        } else {
          $subDel = $precioRuta;
        }
      }

      $subTotal = $subProd + $subServ + $subDel;
      $totalOrden = $subTotal + ($subTotal * $iva);

      if ($cabecera['status'] == 10) {
        $cabecera['estado_dinamico'] = 'Procesada y Pagada';
        $cabecera['estado_num'] = 1;
        $pagado = $totalOrden;
        $restante = 0;
      } elseif ($cabecera['status'] == 11) {
        $cabecera['estado_dinamico'] = 'Pagada y Despachada';
        $cabecera['estado_num'] = 3;
        $pagado = $totalOrden;
        $restante = 0;
      } elseif ($cabecera['status'] == 13) {
        $cabecera['estado_dinamico'] = 'Pagada y Ejecutada';
        $cabecera['estado_num'] = 13;
        $pagado = $totalOrden;
        $restante = 0;
      } else {
        $pagado = floatval($cabecera['total_pagado']);
        $restante = round($totalOrden - $pagado, 2);

        if ($restante <= 0) {
          if ($cabecera['status'] == 3) {
            $cabecera['estado_dinamico'] = 'Pagada y Despachada';
            $cabecera['estado_num'] = 3;
          } elseif ($cabecera['status'] == 12) {
            $cabecera['estado_dinamico'] = 'Pagada y Ejecutada';
            $cabecera['estado_num'] = 13;
          } else {
            $cabecera['estado_dinamico'] = 'Procesada y Pagada';
            $cabecera['estado_num'] = 1;
          }
        } else {
          if ($cabecera['status'] == 3) {
            $cabecera['estado_dinamico'] = 'Despachada y sin Pago';
            $cabecera['estado_num'] = 4;
          } elseif ($cabecera['status'] == 12) {
            $cabecera['estado_dinamico'] = 'Ejecutada y sin Pago';
            $cabecera['estado_num'] = 12;
          } else {
            $cabecera['estado_dinamico'] = 'Procesada y sin Pago';
            $cabecera['estado_num'] = 2;
          }
        }
      }
      $cabecera['total_orden'] = $totalOrden;
      $cabecera['total_pagado'] = $pagado;
      $cabecera['restante'] = $restante;
    }

    // Guardamos cuánto costó de verdad el delivery sumando kilómetros
    if ($delivery) {
      $delivery['costo_delivery_total'] = $subDel ?? floatval($delivery['precio_ruta']);
    }

    return [
      'cabecera'  => $cabecera,
      'productos' => $productos,
      'servicios' => $servicios,
      'delivery'  => $delivery ?: null,
    ];
}
private function RegistrarOrdenP() {
    $objBitacora  = new bitacoraModelo();
    $cn = $this->conectar();

    try {
        // Buscamos IVA
        $this->idCambioIva = $this->obtenerIVAActualP();

        // Generar Codigo OEP
        $this->idOrden = $this->generarCodSeg([
            'tablaBD' => 'ordenes_entregas_presupuestos',
            'prefijo' => 'OEP',
            'campoID' => 'id_orden_entrega_presupuesto',
        ]);

        // Datos del cliente
        $objClientes = new clientesModelo;
        $infoCliente = $objClientes->seleccionarClientes(['rif_cedula_cliente' => $this->rifCliente]);
        $nombreCliente = is_array($infoCliente) ? ($infoCliente['razon_social_cliente'] ?? $this->rifCliente) : $this->rifCliente;

        // Guardamos los datos principales
        $idFact = $this->guardarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos',
            'datos' => [
                'id_orden_entrega_presupuesto' => $this->idOrden,
                'cedula_usuario'    => $this->cedulaUsuario,
                'id_cambio_iva'     => $this->idCambioIva,
                'rif_cedula_cliente' => $this->rifCliente,
                'fecha_orden_entrega_presupuesto' => $this->fechaOrden,
                'status'            => $this->status,
            ],
            'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden],
        ]);

        // Validamos si no se crea la orden
        if (!$idFact) {
            $objBitacora->registrarBitacora([
                'modulo'    => 'ordenesEntregasPresupuestos',
                'accion'    => 'registrar',
                'resultado' => 'Fallido',
                'viejo'     => [],
                'nuevo'     => [
                    'rif'       => $this->rifCliente,
                    'productos' => count($this->productos),
                    'servicios' => count($this->servicios),
                ]
            ]);

            $this->rollback();

            return [
                'tipo'   => 'simple',
                'titulo' => 'Error al Registrar',
                'texto'  => 'No se pudo registrar la orden',
                'icono'  => 'error',
            ];
        }

        // Instanciamos el modelo de productos para modificar el stock
        $objProductos = new productosModelo();

        //Guardamos uno por uno los productos y descontamos stock
        foreach ($this->productos as $p) {
            $idPresentacion = $p['id_presentacion_producto'] ?? '';
            $cantidad       = (float)($p['cantidad'] ?? 0);

            if (empty($idPresentacion) || $cantidad <= 0) continue;

            // Consultamos stock de la presentación
            $resultadoStock = $this->seleccionarDatos2([
                'campos'     => 'p.stock_producto, pp.id_producto, pre.cantidad_pmp',
                'tabla'      => 'presentaciones_productos AS pp',
                'datosJoins' => [
                    'productos p' => 'pp.id_producto = p.id_producto',
                    'presentaciones pre' => 'pp.id_presentacion = pre.id_presentacion'
                ],
                'WHERE'      => [
                    'pp.id_presentacion_producto' => $idPresentacion,
                    'pp.status'                   => 1
                ]
            ]);
            $datosProd = $resultadoStock->fetch(PDO::FETCH_ASSOC);

            // Validamos por si no encontramos una presentación
            if (!$datosProd) {
                $objBitacora->registrarBitacora([
                    'modulo'    => 'ordenesEntregasPresupuestos',
                    'accion'    => 'registrar',
                    'resultado' => 'Fallido',
                    'viejo'     => [],
                    'nuevo'     => [
                        'rif'       => $this->rifCliente,
                        'productos' => count($this->productos),
                        'servicios' => count($this->servicios),
                    ]
                ]);

                $this->rollback();

                return [
                    'tipo'   => 'simple',
                    'titulo' => 'Error al Registrar',
                    'texto'  => 'No se pudo registrar la orden',
                    'icono'  => 'error',
                ];
            }

            $capacidad = (float)($datosProd['cantidad_pmp'] ?? 1);
            $volumenRequerido = $cantidad * $capacidad;

            // Validamos si el stock es insuficiente
            if ($datosProd['stock_producto'] < $volumenRequerido) {
                $objBitacora->registrarBitacora([
                    'modulo'    => 'ordenesEntregasPresupuestos',
                    'accion'    => 'registrar',
                    'resultado' => 'Fallido',
                    'viejo'     => [],
                    'nuevo'     => [
                        'rif'       => $this->rifCliente,
                        'productos' => count($this->productos),
                        'servicios' => count($this->servicios),
                    ]
                ]);

                $this->rollback();

                return [
                    'tipo'   => 'simple',
                    'titulo' => 'Stock insuficiente',
                    'texto'  => 'No hay suficiente stock para completar la orden',
                    'icono'  => 'error',
                ];
            }

            // Insertar en productos_ordenes_entregas_presupuestos
            $this->guardarDatos2([
                'tabla' => 'productos_ordenes_entregas_presupuestos',
                'datos' => [
                    'id_orden_entrega_presupuesto' => $this->idOrden,
                    'id_presentacion_producto' => $idPresentacion,
                    'cantidad_producto'        => $cantidad,
                    'status'                   => 1
                ]
            ]);

            // Descontar stock 
            $resStock = $objProductos->modificarStock($datosProd['id_producto'], -$volumenRequerido, $cn);
            if ($resStock !== true) {
                $objBitacora->registrarBitacora([
                    'modulo'    => 'ordenesEntregasPresupuestos',
                    'accion'    => 'registrar',
                    'resultado' => 'Fallido',
                    'viejo'     => [],
                    'nuevo'     => [
                        'rif'       => $this->rifCliente,
                        'productos' => count($this->productos),
                        'servicios' => count($this->servicios),
                    ]
                ]);

                $this->rollback();

                return [
                    'tipo'   => 'simple',
                    'titulo' => 'Error al Registrar',
                    'texto'  => 'No se pudo registrar la orden',
                    'icono'  => 'error',
                ];
            }
        }

        // Guardamos los servicios
        foreach ($this->servicios as $s) {
            $idServicio    = $s['id_servicio']         ?? '';
            $cantidad      = (float)($s['cantidad']    ?? 1);
            $esMapfre      = (int)($s['es_mapfre']     ?? 0);
            $precioMapfre  = (float)($s['precio_mapfre'] ?? 0);

            // Datos de ubicación y fecha del servicio
            $fechaEjecucion = $s['fecha_ejecucion'] ?? date('Y-m-d H:i:s');
            $latitud        = $s['latitud'] ?? '';
            $longitud       = $s['longitud'] ?? '';
            $idRuta         = (int)($s['id_ruta'] ?? 0);

            if (empty($idServicio)) continue;

            // Si el servicio tiene coordenadas y ruta asignadas, creamos la dirección
            $idDireccion = null;
            if ($idRuta > 0 && $latitud !== '' && $longitud !== '') {
                $idLat = $this->guardarDatos2(['tabla' => 'latitudes_direcciones', 'datos' => ['coordenada_latitud' => $latitud, 'status' => 1]]);
                $idLng = $this->guardarDatos2(['tabla' => 'longitudes_direcciones', 'datos' => ['coordenada_longitud' => $longitud, 'status' => 1]]);

                $idDireccion = $this->guardarDatos2([
                    'tabla' => 'direcciones',
                    'datos' => [
                        'id_latitud_direccion' => $idLat,
                        'id_longitud_direccion' => $idLng,
                        'id_ruta' => $idRuta,
                        'status' => 1
                    ]
                ]);
            }

            // Insertar en servicios_ordenes_entregas_presupuestos
            $this->guardarDatos2([
                'tabla' => 'servicios_ordenes_entregas_presupuestos',
                'datos' => [
                    'id_orden_entrega_presupuesto' => $this->idOrden,
                    'id_servicio'                  => $idServicio,
                    'id_direccion'                 => $idDireccion,
                    'fecha_ejecucion'              => $fechaEjecucion,
                    'cantidad_servicio'            => $cantidad,
                    'es_precio_mapfre'             => $esMapfre,
                    'precio_servicio_mapfre'       => $esMapfre ? $precioMapfre : 0,
                    'status'                       => 1
                ]
            ]);
        }

        // Revisamos si además pidieron delivery
        if (!empty($this->delivery) && !empty($this->delivery['id_ruta'])) {
            $idRuta = (int)($this->delivery['id_ruta'] ?? 0);
            $latitud = $this->delivery['latitud'] ?? '';
            $longitud = $this->delivery['longitud'] ?? '';

            if ($idRuta > 0 && $latitud !== '' && $longitud !== '') {
                $idLatitud = $this->guardarDatos2([
                    'tabla' => 'latitudes_direcciones',
                    'datos' => ['coordenada_latitud' => $latitud, 'status' => 1]
                ]);

                $idLongitud = $this->guardarDatos2([
                    'tabla' => 'longitudes_direcciones',
                    'datos' => ['coordenada_longitud' => $longitud, 'status' => 1]
                ]);

                $idDireccion = $this->guardarDatos2([
                    'tabla' => 'direcciones',
                    'datos' => [
                        'id_latitud_direccion'  => $idLatitud,
                        'id_longitud_direccion' => $idLongitud,
                        'id_ruta'               => $idRuta,
                        'status'                => 1
                    ]
                ]);

                $idDelivery = $this->generarCodSeg([
                    'tablaBD' => 'deliveries',
                    'prefijo' => 'DLVR',
                    'campoID' => 'id_delivery',
                ]);

                $cedulaRepartidor = $this->delivery['cedula_repartidor'] ?? null;

                $this->guardarDatos2([
                    'tabla' => 'deliveries',
                    'datos' => [
                        'id_delivery'                  => $idDelivery,
                        'id_orden_entrega_presupuesto' => $this->idOrden,
                        'id_direccion'      => $idDireccion,
                        'cedula_repartidor' => $cedulaRepartidor,
                        'status'            => 1
                    ]
                ]);
            }
        }

        // Registro en bitácora con datos nuevos
        $objBitacora->registrarBitacora([
            'modulo'    => 'ordenesEntregasPresupuestos',
            'accion'    => 'registrar',
            'resultado' => 'Éxito',
            'viejo'     => [],
            'nuevo'     => [
                'id_orden' => $this->idOrden,
                'cliente'  => $nombreCliente,
                'rif'      => $this->rifCliente,
                'productos' => count($this->productos),
                'servicios' => count($this->servicios),
            ]
        ]);

        $this->commit();

        $objetoNot = new mensajesWSModelo();
        $objetoNot->enviarMensajesWS([
            "receptor" => [
                'tipo' => 'rol',
                'rol' => 'ADMINISTRADOR'
            ],
            'cuerpo' => [
                ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
                ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos'],
                [
                    'accion' => 'alertar',
                    'alerta' => [
                        'tipo' => 'simple',
                        'titulo' => 'Nueva Orden',
                        'texto' => "Se ha registrado la orden {$this->idOrden} para el cliente {$nombreCliente}",
                        'icono' => 'info',
                        'notifier' => true,
                        'tiempo' => 3000
                    ]
                ],
            ],
            'noCommit' => true
        ]);

        return [
            'tipo'   => 'limpiar',
            'titulo' => 'Orden Registrada',
            'texto'  => "Orden {$this->idOrden} registrada correctamente.",
            'icono'  => 'success',
            'id_orden_entrega_presupuesto' => $this->idOrden,
        ];

    } catch (Exception) {
        
        $this->rollback();

        $objBitacora->registrarBitacora([
            'modulo'    => 'ordenesEntregasPresupuestos',
            'accion'    => 'registrar',
            'resultado' => 'Fallido',
            'viejo'     => [],
            'nuevo'     => [
                'rif'       => $this->rifCliente,
                'productos' => count($this->productos),
                'servicios' => count($this->servicios),
            ]
        ]);

        return [
            'tipo'   => 'simple',
            'titulo' => 'Error al Registrar',
            'texto'  => 'No se pudo registrar la orden',
            'icono'  => 'error',
        ];
    }
}
private function DespacharOrdenP() {
    $objBitacora = new bitacoraModelo();
    
    $resultado = $this->seleccionarDatos2([
      'campos' => 'status',
      'tabla'  => 'ordenes_entregas_presupuestos',
      'WHERE'  => ['id_orden_entrega_presupuesto' => $this->idOrden]
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'La orden no existe', 'icono' => 'error'];
    }

    $statusActual = intval($fila['status']);

    // Verificamos que tenga un estado válido antes de cambiar a despachada
    if ($statusActual == 2) {
      return ['tipo' => 'simple', 'titulo' => 'No permitido', 'texto' => 'No se puede despachar una orden anulada', 'icono' => 'warning'];
    }
    if ($statusActual == 3 || $statusActual == 11) {
      return ['tipo' => 'simple', 'titulo' => 'Ya despachada', 'texto' => 'Esta orden ya fue despachada anteriormente', 'icono' => 'info'];
    }

    // Si no estaba pagada completa, pasa a despachada sin pagar
    // Pero si ya habían pagado todo, la marcamos como despachada y pagada
    $nuevoStatus = ($statusActual == 10) ? 11 : 3;

    try {
      $this->actualizarDatos2([
        'tabla' => 'ordenes_entregas_presupuestos',
        'datos' => ['status' => $nuevoStatus],
        'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden]
      ]);

      $estadoTexto = ($nuevoStatus == 11) ? 'Pagada y Despachada' : 'Despachada y sin Pago';
      
      $objBitacora->registrarBitacora([
        'modulo'    => 'ordenesEntregasPresupuestos',
        'accion'    => 'despachar',
        'resultado' => 'Éxito',
        'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusActual],
        'nuevo'     => ['id_orden' => $this->idOrden, 'status' => $nuevoStatus]
      ]);

      $this->commit();

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
          ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Orden Despachada',
              'texto' => "La orden {$this->idOrden} fue despachada.",
              'icono' => 'info',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ]
        ],
        'noCommit' => true
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Orden Despachada',
        'texto'  => "La orden {$this->idOrden} fue marcada como \"{$estadoTexto}\".",
        'icono'  => 'success',
      ];
    } catch (\Exception) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo'    => 'ordenesEntregasPresupuestos',
        'accion'    => 'despachar',
        'resultado' => 'Fallido',
        'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusActual],
        'nuevo'     => ['id_orden' => $this->idOrden, 'status' => $nuevoStatus]
      ]);
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Despachar',
        'texto'  => 'No se pudo despachar la orden',
        'icono'  => 'error',
      ];
    }
}
private function AnularOrdenP() {
    $objBitacora  = new bitacoraModelo();
    $objProductos = new productosModelo();
    $cn = $this->conectar();

    try {
        // No podemos anular algo que ya está anulado
        $stmt = $cn->prepare(
            "SELECT status FROM ordenes_entregas_presupuestos WHERE id_orden_entrega_presupuesto = :id"
        );
        $stmt->execute([':id' => $this->idOrden]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fila || $fila['status'] == 2) {
            return [
                'tipo'   => 'simple',
                'titulo' => 'Orden ya anulada',
                'texto'  => 'Esta orden ya fue anulada anteriormente',
                'icono'  => 'warning',
            ];
        }

        $statusAnterior = $fila['status'];

        // Le cambiamos el estado al número 2, que es anulada
        $resultado = $this->actualizarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos',
            'datos' => ['status' => 2],
            'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden],
        ]);

        // Validamos si no se pudo anular la orden
        if (!$resultado || $resultado <= 0) {
            $objBitacora->registrarBitacora([
                'modulo'    => 'ordenesEntregasPresupuestos',
                'accion'    => 'anular',
                'resultado' => 'Fallido',
                'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusAnterior],
                'nuevo'     => []
            ]);

            $this->rollback();

            return [
                'tipo'   => 'simple',
                'titulo' => 'Error al Anular',
                'texto'  => 'No se pudo anular la orden',
                'icono'  => 'error',
            ];
        }

        // Devolvemos al inventario todos los productos usando modelo POO
        $stmtProd = $cn->prepare("
            SELECT pf.cantidad_producto, pp.id_producto, pre.cantidad_pmp
            FROM productos_ordenes_entregas_presupuestos pf
            JOIN presentaciones_productos pp ON pf.id_presentacion_producto = pp.id_presentacion_producto
            JOIN presentaciones pre ON pp.id_presentacion = pre.id_presentacion
            WHERE pf.id_orden_entrega_presupuesto = :id AND pf.status = 1
        ");
        $stmtProd->execute([':id' => $this->idOrden]);
        $prods = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prods as $p) {
            $cantidadRestaurar = $p['cantidad_producto'] * ($p['cantidad_pmp'] ?? 1);
            $resStock = $objProductos->modificarStock($p['id_producto'], $cantidadRestaurar, $cn);

            // VALIDACIÓN: si falla modificar stock de productos
            if ($resStock !== true) {
                $objBitacora->registrarBitacora([
                    'modulo'    => 'ordenesEntregasPresupuestos',
                    'accion'    => 'anular',
                    'resultado' => 'Fallido',
                    'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusAnterior],
                    'nuevo'     => []
                ]);

                $this->rollback();

                return [
                    'tipo'   => 'simple',
                    'titulo' => 'Error al Anular',
                    'texto'  => 'No se pudo anular la orden',
                    'icono'  => 'error',
                ];
            }
        }

        
        // SOLO restauramos stock de las OS que fueron ejecutadas (status = 2) ya que son las únicas que descontaron
        $stmtServ = $cn->prepare("
            SELECT sf.id_servicio, sf.cantidad_servicio
            FROM servicios_ordenes_entregas_presupuestos sf
            WHERE sf.id_orden_entrega_presupuesto = :id AND sf.status = 2
        ");
        $stmtServ->execute([':id' => $this->idOrden]);
        $servs = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

        $objServicios = new serviciosModelo();
        foreach ($servs as $s) {
            $infoServ = $objServicios->seleccionarServicios([
                'id_servicio' => $s['id_servicio'],
                'isInterno' => true
            ]);
            $mps = $infoServ['detallesExtra']['productos_servicio'] ?? [];

            foreach ($mps as $mp) {
                $cantidadRestaurar = $mp['cantidad_producto'] * $s['cantidad_servicio'];
                $resStock = $objProductos->modificarStock($mp['id_producto'], $cantidadRestaurar, $cn);

                // VALIDACIÓN: si falla modificar stock de materiales de servicio
                if ($resStock !== true) {
                    $objBitacora->registrarBitacora([
                        'modulo'    => 'ordenesEntregasPresupuestos',
                        'accion'    => 'anular',
                        'resultado' => 'Fallido',
                        'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusAnterior],
                        'nuevo'     => []
                    ]);

                    $this->rollback();

                    return [
                        'tipo'   => 'simple',
                        'titulo' => 'Error al Anular',
                        'texto'  => 'No se pudo anular la orden',
                        'icono'  => 'error',
                    ];
                }
            }
        }

        // Cancelar todas las OS asociadas que estuvieran activas
        $this->actualizarDatos2([
            'tabla' => 'servicios_ordenes_entregas_presupuestos',
            'datos' => ['status' => 4],
            'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden, 'status' => 1]
        ]);
        // Cancelar todas las OS asociadas que estuvieran ejecutadas
        $this->actualizarDatos2([
            'tabla' => 'servicios_ordenes_entregas_presupuestos',
            'datos' => ['status' => 4],
            'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden, 'status' => 2]
        ]);

        // Guardar en bitácora con viejo y nuevo
        $objBitacora->registrarBitacora([
            'modulo'    => 'ordenesEntregasPresupuestos',
            'accion'    => 'anular',
            'resultado' => 'Éxito',
            'viejo'     => ['id_orden' => $this->idOrden, 'status' => $statusAnterior],
            'nuevo'     => ['id_orden' => $this->idOrden, 'status' => 2]
        ]);

        $this->commit();

        $objetoNot = new mensajesWSModelo();
        $objetoNot->enviarMensajesWS([
            "receptor" => [
                'tipo' => 'rol',
                'rol' => 'ADMINISTRADOR'
            ],
            'cuerpo' => [
                ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
                ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos'],
                [
                    'accion' => 'alertar',
                    'alerta' => [
                        'tipo' => 'simple',
                        'titulo' => 'Orden Anulada',
                        'texto' => "La orden {$this->idOrden} fue anulada.",
                        'icono' => 'warning',
                        'notifier' => true,
                        'tiempo' => 3000
                    ]
                ]
            ],
            'noCommit' => true
        ]);

        return [
            'tipo'   => 'simple',
            'titulo' => 'Orden Anulada',
            'texto'  => "La orden {$this->idOrden} fue anulada y el stock restaurado.",
            'icono'  => 'success',
        ];

    } catch (Exception) {
        
        $this->rollback();

        $objBitacora->registrarBitacora([
            'modulo'    => 'ordenesEntregasPresupuestos',
            'accion'    => 'anular',
            'resultado' => 'Fallido',
            'viejo'     => ['id_orden' => $this->idOrden],
            'nuevo'     => []
        ]);

        return [
            'tipo'   => 'simple',
            'titulo' => 'Error al Anular',
            'texto'  => 'No se pudo anular la orden',
            'icono'  => 'error',
        ];
    }
}
private function ListarMetodosPagoP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla'  => 'metodos_pagos',
      'WHERE'  => ['status' => 1]
    ]);
    return $resultado->fetchAll(PDO::FETCH_ASSOC);
}
private function obtenerIVAActualP(): int {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_cambio_iva',
      'tabla'  => 'cambios_iva',
      'WHERE'  => ['status' => 1],
      'ORDER'  => 'id_cambio_iva DESC',
      'LIMIT'  => 1
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);
    return $fila ? (int)$fila['id_cambio_iva'] : 1;

}

private function RegistrarPagoP() {
    if (empty($this->pagos)) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'Debe ingresar al menos un detalle de pago.', 'icono' => 'warning'];
    }

    $objPagos = new pagosModelo();
    $info = [
      'id_orden_entrega_presupuesto' => $this->idOrden,
      'pagos' => is_array($this->pagos) ? json_encode($this->pagos) : $this->pagos,
      'noCommit' => false
    ];
    
    // Si la orden también trae comprobantes en $_FILES, pagosModelo lo maneja
    $resPago = $objPagos->registrarPago($info);

    if (isset($resPago['tipo']) && $resPago['tipo'] == 'simple' && isset($resPago['icono']) && $resPago['icono'] != 'success') {
       return $resPago;
    }

    return ['tipo' => 'limpiarYcerrar', 'titulo' => 'Pago Registrado', 'texto' => 'El pago se ha registrado exitosamente.', 'icono' => 'success'];
}

}

