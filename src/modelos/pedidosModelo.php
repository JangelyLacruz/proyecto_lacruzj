<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\metodosPagoModelo;
use src\modelos\presentacionesModelo;
use src\modelos\rutasModelo;
use src\modelos\cambiosIvaModelo;
use src\modelos\pdfModel;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;

class pedidosModelo extends conexion {
  private string $idPedido = '';
  private array $productosPedido = [];
  private array $deliveryPedido = [];
  private array $pagosPedido = [];
  private array $comprobantesPagos = [];
  private int $statusPedido = 0;

  public function validarPedidos(string $permiso, ?array &$info, ?array $requerido) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('pedidos', $permiso);
    if ($r) return $r;
    $esquemaPedidos = [
      "tipo" => 'arrayA',
      'propiedades' => [
        'delivery' => [
          'tipo' => 'arrayA',
          'nombreAlerta' => 'al delivery',
          'propiedades' => [
            'latitud' => [
              'tipo' => 'float',
              'nombreAlerta' => 'latitud de la ubicación',
              "minL" => minRegexCoordenadas,
              "maxL" => maxRegexCoordenadas,
              "regex" => regexCoordenadas,
            ],
            'longitud' => [
              'tipo' => 'float',
              'nombreAlerta' => 'longitud de la ubicación',
              "minL" => minRegexCoordenadas,
              "maxL" => maxRegexCoordenadas,
              "regex" => regexCoordenadas,
            ],
          ],
          'requerido' => ['latitud', 'longitud']
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
              'id_presentacion' => [
                'tipo' => 'string',
                "nombreAlerta" => "id de la presentación",
                "minL" => minRegexIdSeguro,
                "maxL" => maxRegexIdSeguro,
                "regex" => regexIdSeguro,
                "tablaBD" => "presentaciones",
                "nombreBD" => "id_presentacion",
                "debeExistirBD" => true,
              ],
              'id_presentacion_producto' => [
                'tipo' => 'string',
                "nombreAlerta" => "id de la presentación del producto",
                "minL" => minRegexIdSeguro,
                "maxL" => maxRegexIdSeguro,
                "regex" => regexIdSeguro,
                "tablaBD" => "presentaciones_productos",
                "nombreBD" => "id_presentacion_producto",
                "debeExistirBD" => true,
              ],
              'cantidad' => [
                'tipo' => 'int',
                "nombreAlerta" => "cantidad del producto",
                "minL" => minRegexCantidadItem,
                "maxL" => maxRegexCantidadItem,
                "regex" => regexCantidadItem,
              ],
            ],
            'requerido' => [
              'id_producto',
              'id_presentacion',
              'cantidad',
            ]
          ],
          'minItems' => 1
        ],
        'pagos' => [
          'tipo' => 'array',
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'monto_pago' => [
                'tipo' => 'string',
                "nombreAlerta" => "monto del pago",
                "minL" => minRegexPrecio,
                "maxL" => maxRegexPrecio,
                "regex" => regexPrecio,
                "cFloat" => true,
              ],
              'id_moneda' => [
                'tipo' => 'string',
                "nombreBD" => "id_moneda",
                "nombreAlerta" => "id de la moneda",
                "minL" => minRegexId,
                "maxL" => maxRegexId,
                "regex" => regexId,
                "tablaBD" => "monedas",
                "debeExistirBD" => true,
              ],
              'id_metodo_pago' => [
                'tipo' => 'string',
                "nombreBD" => "id_metodo_pago",
                "nombreAlerta" => "id del método de pago",
                "minL" => minRegexId,
                "maxL" => maxRegexId,
                "regex" => regexId,
                "tablaBD" => "metodos_pagos",
                "debeExistirBD" => true,
              ],
              'referencia_pago' => [
                'tipo' => 'string',
                "nombreAlerta" => "referencia del pago",
                "minL" => minRegexCantidadItem,
                "maxL" => maxRegexCantidadItem,
                "regex" => regexCantidadItem,
                'funcionVal' => function ($valor, $contexto) {
                  $MPBD = [];
                  return $contexto;
                  if (!isset($cto['cache'][$cto['padre']['id_metodo_pago']])) {
                    $objMP = new metodosPagoModelo();
                    $cto['cache'][$cto['padre']['id_metodo_pago']] =
                      $MPBD = $objMP->seleccionarMetodosPagos(['id_metodo_pago' => $$cto['cache']['metodos_pagos'][$cto['padre']['id_metodo_pago']]]);
                  } else {
                    $MPBD = $cto['cache'][$cto['padre']['id_metodo_pago']];
                  }
                  if ($MPBD['necesita_referencia'] == 1) return true;
                  return false;
                },
              ],
              'id_banco_emisor' => [
                'tipo' => 'string',
                "nombreBD" => "id_banco",
                "nombreAlerta" => "banco emisor",
                "minL" => minRegexId,
                "maxL" => maxRegexId,
                "regex" => regexId,
                "tablaBD" => "bancos",
                "debeExistirBD" => true,
              ],
              'id_banco_receptor' => [
                'tipo' => 'string',
                "nombreBD" => "id_banco",
                "nombreAlerta" => "banco receptor",
                "minL" => minRegexId,
                "maxL" => maxRegexId,
                "regex" => regexId,
                "tablaBD" => "bancos",
                "debeExistirBD" => true,
              ],
            ],
            'requerido' => ['monto_pago', 'id_moneda', 'id_metodo_pago']
          ],
          'minItems' => 1,
          'nombreAlerta' => 'detalles del pago'
        ],
        'comprobantes_pago' => [
          'tipo' => 'archivo',
          'extensiones' => ['jpg', 'png', 'jpeg', 'webp'],
          'maximoMb' => 5120,
          'minItems' => 1,
          'nombreAlerta' => 'comprobantes del pago',
        ],
        'cedula_repartidor' => [
          'tipo' => 'string',
          "nombreBD" => "cedula_repartidor",
          "nombreAlerta" => "cédula del repartidor",
          "minL" => minRegexCedulaRifLetra,
          "maxL" => maxRegexCedulaRifLetra,
          "regex" => regexCedulaRifLetra,
          "tablaBD" => "repartidores",
          "debeExistirBD" => true,
        ],
        'status_pedido' => [
          'tipo' => 'string',
          "nombreAlerta" => "status del pedido",
          "minL" => minRegexStatus,
          "maxL" => maxRegexStatus,
          "regex" => regexStatus,
        ],
        'id_pedido' => [
          'tipo' => 'string',
          "minL" => minRegexIdSeguro,
          "maxL" => maxRegexIdSeguro,
          "regex" => regexIdSeguro,
          "nombreAlerta" => "id del pedido",
          "nombreBD" => "id_orden_entrega_presupuesto",
          "tablaBD" => "ordenes_entregas_presupuestos",
          "debeExistirBD" => true,
          "debeSerUnicoBD" => true,
        ],
      ],
      'requerido' => $requerido
    ];
    $r = $this->limpiarValidar($info, $esquemaPedidos);
    if ($r) return $r;
    return false;
  }
  public function listarPedidos(array $info) {
    $requerido = [];
    if (($info['id_pedido'] ?? '') != "") $requerido[] = 'id_pedido';
    $r = $this->validarPedidos('listar', $info, $requerido);
    if (($info['id_pedido'] ?? '') != "") $this->idPedido = $info['id_pedido'];
    if ($r) return $r;
    return $this->listarPedidosP($info);
  }
  public function registrarPedidos(array $info) {
    $resultado = $this->validarPedidos('registrar', $info, [
      'comprobantes_pago',
      'productos',
      'pagos',
      'delivery'
    ]);
    if ($resultado) return $resultado;
    [
      'productos' => $this->productosPedido,
      'pagos' => $this->pagosPedido,
      'delivery' => $this->deliveryPedido,
      'comprobantes_pago' => $this->comprobantesPagos,
    ] = $info;
    return $this->registrarPedidosP();
  }
  public function asignarRepartidoresPedidos(array $info) {
    $resultado = $this->validarPedidos('asignar repartidores a pedidos', $info, [
      'id_delivery',
      'id_pedido',
      'cedula_repartidor',
    ]);
    if ($resultado) return $resultado;

    [
      'id_delivery' => $this->deliveryPedido['id_delivery'],
      'id_pedido' => $this->idPedido,
      'cedula_repartidor' => $this->deliveryPedido['cedula_repartidor'],
    ] = $info;
    return $this->asignarRepartidoresPedidosP();
  }
  public function actualizarPedidos(array $info) {
    $r = $this->validarPedidos('cambiar estado de los pedidos', $info, [
      'id_pedido',
      'status_pedido',
    ]);
    if ($r) return $r;
    [
      'id_pedido' => $this->idPedido,
      'status_pedido' => $this->statusPedido,
    ] = $info;

    return $this->actualizarPedidoP();
  }
  public function imprimirPedidos(array $info) {
    $r = $this->validarPedidos('imprimir pedidos', $info, [
      'id_pedido',
    ]);
    if ($r) return $r;
    $this->idPedido = $info['id_pedido'];
    return $this->imprimirPedidosP();
  }

  private function listarPedidosP(array $info) {
    if ($this->idPedido != '') {

      //Generales
      $datosGenerales = $this->seleccionarDatos2([
        'campos' => '*, fa.status as status_pedido,fa.cedula_usuario,fa.fecha_orden_entrega_presupuesto as fecha_orden',
        'tabla' => 'ordenes_entregas_presupuestos as fa',
        'datosJoins' => [
          'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente',
          'cambios_iva as ci' => 'fa.id_cambio_iva = ci.id_cambio_iva',
        ],
        'WHERE' => [
          'fa.id_orden_entrega_presupuesto' => $this->idPedido,
        ]
      ])->fetch();

      // IVA
      $IVA = $datosGenerales['monto_cambio_iva'];

      //Usuario - Vendedor
      $vendedor = false;
      if ($datosGenerales['cedula_usuario'] != '') {
        $objUsuario = new usuariosModelo();
        $vendedor = $objUsuario->seleccionarUsuarios(['cedula_usuario' => $datosGenerales['cedula_usuario']]);
        unset($objUsuario);
      }

      // Las divisas de esa fecha
      $objMonedas = new monedasModelo();
      $monedas = $objMonedas->seleccionarMonedas([
        'tipoConsulta' => 'divisasPorFecha',
        'fecha' => $datosGenerales['fecha_orden_entrega_presupuesto']
      ]);
      unset($objMonedas);

      // Si es delivery
      $medioEnvio = $this->seleccionarDatos2([
        'tabla' => 'deliveries as de',
        'campos' => '*',
        'datosJoins' => [
          'LEFT repartidores as re' => 'de.cedula_repartidor = re.cedula_repartidor',
        ],
        'WHERE' => [
          'de.id_orden_entrega_presupuesto' => $this->idPedido
        ]
      ])->fetch();
      $medioEnvio['tipo_medio_envio'] = 'delivery';

      //Si es de envio de terceros
      if (($medioEnvio['id_delivery'] ?? []) == []) {
        $medioEnvio = $this->seleccionarDatos2([
          'tabla' => 'envios_terceros as et',
          'campos' => '*',
          'datosJoins' => [
            'sucursales_empresas_envios as see' => 'et.id_sucursal_empresa_envios = see.id_sucursal_empresa_envios',
            'empresas_envios as ee' => 'see.id_empresa_envios = ee.id_empresa_envios',
          ],
          'WHERE' => [
            'et.id_orden_entrega_presupuesto' => $this->idPedido
          ]
        ])->fetch();
        $medioEnvio['tipo_medio_envio'] = 'deTercero';
      }

      //Dirección
      $medioEnvio += $this->seleccionarDatos2([
        'tabla' => 'direcciones as di',
        'campos' => '*',
        'datosJoins' => [
          'rutas as ru' => 'di.id_ruta = ru.id_ruta',
          'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
          'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
        ],
        'WHERE' => [
          'di.id_direccion' => $medioEnvio['id_direccion']
        ]
      ])->fetch();
      $medioEnvio['url_direccion'] = "https://maps.google.com/?q={$medioEnvio['coordenada_latitud']},{$medioEnvio['coordenada_longitud']}";

      // La ruta del delivery
      $totalEnvio = 0;
      if ($medioEnvio['tipo_medio_envio'] == 'delivery') {
        $objRutas = new rutasModelo();
        $rutaFecha = $objRutas->seleccionarRutas([
          'tipoConsulta' => 'porFecha',
          'id_ruta' => $medioEnvio['id_ruta'],
          'fecha' => $datosGenerales['fecha_orden_entrega_presupuesto']
        ]);
        unset($objRutas);
        if (isset($rutaFecha['icono'])) return $rutaFecha;

        $medioEnvio['precio_ruta_factura'] = $rutaFecha['precio_ruta_fecha'];
        $infoRuta = $this->calcularKmPorCarretera([
          'partida' => [
            'latitud' => coorJLACRUZ['latitud'],
            'longitud' => coorJLACRUZ['longitud'],
          ],
          'llegada' => [
            'latitud' => $medioEnvio['coordenada_latitud'],
            'longitud' => $medioEnvio['coordenada_longitud'],
          ]
        ]);
        if (isset($_COOKIE['TEMP']) || isset($_ENV['MODO_TESTEO'])) $infoRuta['km_recorrido'] = 48;
        if (isset($infoRuta['icono'])) return $infoRuta;

        $medioEnvio['km_recorrido'] = $infoRuta['km_recorrido'];
        $totalEnvio = $medioEnvio['precio_ruta_factura'] * $infoRuta['km_recorrido'];
      } else {
        $totalEnvio = $medioEnvio['precio_envio_tercero'];
      }

      // Productos
      $objProductos = new productosModelo();
      $productos = $objProductos->seleccionarProductos([
        'tipoConsulta' => 'productosFactura',
        'id_factura' => $this->idPedido,
      ]);
      unset($objProductos);

      $totalProductos = 0;
      $totalDescuento = 0;
      foreach ($productos as &$prodInd) {
        $subtotal = $prodInd['cantidad_bruta'] * $prodInd['precio_producto_factura'];
        if ($prodInd['cantidad_bruta'] >= 20) {
          $subtotal -= $subtotal * 0.10;
          $prodInd['descuento'] = $subtotal * 0.10;
          $totalDescuento += $subtotal * 0.10;
          $prodInd['precioSinDescuento'] = $subtotal;
          $prodInd['precio_presentacion_factura'] -= (0.10 * $prodInd['precio_presentacion_factura']);
        } else {
          $prodInd['descuento'] = 0;
          $prodInd['precioSinDescuento'] = $subtotal;
        }
        $prodInd['subtotal_factura'] = $subtotal;
        $totalProductos += (float)$subtotal;
      }
      unset($prodInd);

      // Detalles - pagos
      $detallesPagos = $this->seleccionarDatos2([
        'tabla' => 'pagos as p',
        'campos' => '
          p.id_pago, dp.id_detalle_pago, dp.id_metodo_pago,
          dp.id_moneda,dp.monto_pago, rdp.referencia_pago
        ',
        'datosJoins' => [
          'detalles_pagos as dp' => 'p.id_pago = dp.id_pago',
          'LEFT ' . 'referencias_detalles_pagos as rdp' => 'dp.id_detalle_pago = rdp.id_detalle_pago',
        ],
        'WHERE' => [
          'id_orden_entrega_presupuesto' => $this->idPedido
        ],
      ])->fetchAll();

      $idsPagos = $this->indexarArrays([
        "indice" => 'id_pago',
        'camposAgrupar' => 'id_pago',
        'indicesNumericos' => true,
        'array' => $detallesPagos,
      ]);

      $objMetodosPagos = new metodosPagoModelo();
      $metodosPagos = $objMetodosPagos->seleccionarMetodosPagos([
        'tipoConsulta' => 'indexadosPorId',
      ]);
      unset($objMetodosPagos);

      $objBancos = new bancosModelo();
      $bancos = $objBancos->seleccionarBancos([
        'tipoConsulta' => 'indexadosPorId'
      ]);
      unset($objBancos);

      $totalPagos = 0;
      foreach ($detallesPagos as &$detalle) {
        //Pasamos a dolar
        $valorMoneda = $monedas[$detalle['id_moneda']]['valor_fecha_moneda'];
        $subTotal = $valorMoneda * $detalle['monto_pago'];
        $totalPagos += $subTotalDolar = $subTotal / $monedas[1]['valor_fecha_moneda'];
        $detalle['equivalencia_fecha_factura'] = $subTotalDolar;
        $detalle['nombre_metodo_pago'] = $metodosPagos[$detalle['id_metodo_pago']]['nombre_metodo_pago'];
        $detalle['nombre_moneda'] = $monedas[$detalle['id_moneda']]['nombre_moneda'];

        //Bancos del pago
        $bancosDetalles = $this->seleccionarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'campos' => '*',
          'WHERE' => [
            'id_detalle_pago' => $detalle['id_detalle_pago']
          ]
        ])->fetchAll();

        //Bancos
        $detalle['nombre_banco_emisor'] = 'N/A';
        $detalle['nombre_banco_receptor'] = 'N/A';
        $detalle['referencia_pago'] = $detalle['referencia_pago'] ? $detalle['referencia_pago'] : 'N/A';
        if (!empty($bancosDetalles)) {
          foreach ($bancosDetalles as $bancoD) {
            if ($bancoD['es_emisor'] == 1) {
              $detalle['id_banco_emisor'] = $bancoD['id_banco'];
              $detalle['nombre_banco_emisor'] = $bancos[$bancoD['id_banco']]['nombre_banco'];
            } else {
              $detalle['id_banco_receptor'] = $bancoD['id_banco'];
              $detalle['nombre_banco_receptor'] = $bancos[$bancoD['id_banco']]['nombre_banco'];
            }
          }
        }
      }
      unset($detalle);

      //Captures del pago
      $capturesPagos = [];
      foreach ($idsPagos as $idPago) {
        $capturesPagos += $this->seleccionarDatos2([
          'tabla' => 'comprobantes_pagos',
          'campos' => 'path_comprobante',
          'WHERE' => [
            'id_pago' => $idPago
          ]
        ])->fetchAll(PDO::FETCH_COLUMN);
      }
      $cliente = $this->intersArray($datosGenerales, [
        'rif_cedula_cliente',
        'telefono_cliente',
        'razon_social_cliente',
        'direccion_cliente',
        'correo_cliente',
      ]);
      $datosFactura = $this->intersArray($datosGenerales, [
        'fecha_orden',
        'status_pedido',
        'id_orden_entrega_presupuesto',
      ]);
      $cargos = ($totalProductos + $totalEnvio) + (($totalProductos + $totalEnvio) * ($IVA / 100));
      return $datosFactura += [
        'vendedor' => $vendedor,
        'cliente' => $cliente,
        'productos' => $productos,
        'pagos' => $detallesPagos,
        'capturesPagos' => $capturesPagos,
        'medioEnvio' => $medioEnvio,
        'calculos' => [
          'dolar' => $monedas[1],
          'porcentaje_IVA' => $IVA,
          'totalProductos' => round($totalProductos, 2),
          'totalEnvio' => round($totalEnvio, 2),
          'total' => round(($totalProductos + $totalEnvio), 2),
          'total_IVA' => round(($totalProductos + $totalEnvio) + (($totalProductos + $totalEnvio) * ($IVA / 100)), 2),
          'monto_IVA' => round((($totalProductos + $totalEnvio) * ($IVA / 100)), 2),
          'totalPagos' => round($totalPagos, 2),
          'totalGeneral' => round(($totalPagos - $cargos), 2),
          'totalDescuento' => round($totalDescuento, 2),
        ]
      ];
    } else {
      if (($info['tipoConsulta'] ?? '') == 'clienteInicioSesion') {
        return $this->seleccionarDatos2([
          'campos' => '
            cl.rif_cedula_cliente, cl.razon_social_cliente,
            fa.id_orden_entrega_presupuesto, fa.fecha_orden_entrega_presupuesto, fa.status as status_pedido
          ',
          'tabla' => 'ordenes_entregas_presupuestos as fa',
          'datosJoins' => [
            'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente'
          ],
          'WHERE' => [
            'cl.rif_cedula_cliente' => $_SESSION['cedula']
          ]
        ])->fetchAll();
      } else {
        $pedidos = $this->seleccionarDatos2([
          'campos' => '
            cl.rif_cedula_cliente, cl.razon_social_cliente,
            fa.id_orden_entrega_presupuesto, fa.fecha_orden_entrega_presupuesto, fa.status as status_pedido
          ',
          'tabla' => 'ordenes_entregas_presupuestos as fa',
          'datosJoins' => [
            'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente'
          ],
        ])->fetchAll();
        $mapPedidos = [
          'porConfirmar' => [],
          'confirmados' => [],
          'rechazados' => [],
          'entregados' => [],
        ];
        foreach ($pedidos as $pedido) {
          switch ($pedido['status_pedido']) {
            case 5: //pendiente
              $mapPedidos['porConfirmar'][] = $pedido;
              break;
            case 6: //rechazados
              $mapPedidos['rechazados'][] = $pedido;
              break;
            case 7: //confirmados
              $mapPedidos['confirmados'][] = $pedido;
              break;
            case 8: //entregados
              $mapPedidos['entregados'][] = $pedido;
              break;
            default:
              # code...
              break;
          }
        }
        return $mapPedidos;
      }
    }
  }
  private function registrarPedidosP() {
    $objBitacora = new bitacoraModelo();
    $comprobantesPagos = [];
    $error = function () use ($objBitacora, $comprobantesPagos) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'pedidos',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($comprobantesPagos != []) {
        $this->Imagenes_Eli2('comprobantes_pagos', $comprobantesPagos);
      }
    };

    // #region VALIDACIONES DE LOGICA DE NEGOCIO
    $totalPagar = 0;
    $totalPagado = 0;

    //Pagos
    $objMon = new monedasModelo();
    $monedasBD = $objMon->seleccionarMonedas([
      'tipoConsulta' => 'indexadosPorId'
    ]);
    unset($objMon);
    $dolar = $monedasBD[1];
    foreach ($this->pagosPedido as $pago) {
      $totalPagado += $pago['monto_pago'] * $monedasBD[$pago['id_moneda']]['valor_moneda'];
    };

    //Productos
    $objProd = new productosModelo();
    $productosBD = $objProd->seleccionarProductos([
      'tipoConsulta' => 'indexadosPorId'
    ]);
    unset($objProd);

    //Presentaciones
    $objPresentacion = new presentacionesModelo();
    $presentacionesBD = $objPresentacion->seleccionarPresentaciones([
      'tipoConsulta' => 'indexadosPorId'
    ]);
    unset($objPresentacion);

    $descuentoStocks = [];
    foreach ($this->productosPedido as $producto) {
      $productoBD = $productosBD[$producto['id_producto']];
      $presentacionBD = $presentacionesBD[$producto['id_presentacion']];
      $cantidadBruta = $producto['cantidad'] * $presentacionBD['cantidad_pmp'];
      $subtotalBase = $producto['cantidad'] * $productoBD['precio_producto'];
      $subTotalDescuento = $subtotalBase;
      if ($cantidadBruta >= 20) {
        $subTotalDescuento = $subtotalBase - ($subtotalBase * 0.1);
      }
      $totalPagar += $subTotalDescuento;

      // Validamos que el stock disponible sea suficiente
      if ($cantidadBruta > $productoBD['stock_producto']) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Cantidad no disponible',
          'texto' => '
            Actualmente no contamos con esa cantidad de producto, 
            por favor expecifique una cantidad menor',
          'icono' => 'warning',
        ];
      }
      $descuentoStocks[$producto['id_producto']] = [
        'cantidadDescontar' => $cantidadBruta,
        'cantidadActual' => $productoBD['stock_producto']
      ];
    }

    // Delivery
    $infoRuta = $this->calcularKmPorCarretera([
      'partida' => [
        'latitud' => coorJLACRUZ['latitud'],
        'longitud' => coorJLACRUZ['longitud'],
      ],
      'llegada' => [
        'latitud' => $this->deliveryPedido['latitud'],
        'longitud' => $this->deliveryPedido['longitud'],
      ]
    ]);

    if (isset($_COOKIE['TEMP']) || isset($_ENV['MODO_TESTEO'])) $distanciaKM = 48;
    if (isset($infoRuta['icono'])) {
      $error();
      return $infoRuta;
    }

    $objRutas = new rutasModelo();
    $rutaBD = $objRutas->seleccionarRutas([
      'tipoConsulta' => 'porKm',
      'km_recorrido' => $infoRuta['km_recorrido']
    ]);
    unset($objRutas);
    if (isset($rutaBD['icono'])) return $rutaBD;
    $totalPagar += ($rutaBD['precio_ruta'] * $infoRuta['km_recorrido']) * $dolar['valor_moneda'];

    //Validamos que haya pagado completo
    if ($totalPagado < $totalPagar) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Pago Incompleto',
        'texto' => 'No puede registrar un pedido sin cancelar la totalidad del mismo',
        'icono' => 'warning',
      ];
    }

    // #endregion VALIDACIONES DE LOGICA DE NEGOCIO

    // #region TRANSACCIÓN

    // Cambio IVA
    $objIva = new cambiosIvaModelo();
    $cambioIva = $objIva->seleccionarCambiosIva([
      'tipoConsulta' => 'ivaActual'
    ]);
    unset($objIva);

    // Pedido
    $idPedido = $this->generarCodSeg([
      'tablaBD' => 'ordenes_entregas_presupuestos',
      'prefijo' => 'FACT',
      'campoID' => 'id_orden_entrega_presupuesto'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'id_orden_entrega_presupuesto' => $idPedido,
        'rif_cedula_cliente' => $_SESSION['cedula'],
        'id_cambio_iva' => $cambioIva['id_cambio_iva'],
        'fecha_orden_entrega_presupuesto' => $this->FechaHora_Sel('fecha_hora_BD'),
        'status' => 5,
      ],
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ha ocurrido un error registrando el pedido',
        'icono' => 'error',
      ];
    }

    // Productos
    foreach ($this->productosPedido as $producto) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_ordenes_entregas_presupuestos',
        'datos' => [
          'id_presentacion_producto' => $producto['id_presentacion_producto'],
          'id_orden_entrega_presupuesto' => $idPedido,
          'cantidad_producto' => $producto['cantidad'],
        ]
      ]);
      if ($idDetalle == false || $idDetalle <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error registrando los productos del pedido',
          'icono' => 'error',
        ];
      }
    }
    foreach ($descuentoStocks as $idProducto => $cantidades) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => $cantidades['cantidadActual'] - $cantidades['cantidadDescontar']
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error actualizando el stock de los productos del pedido',
          'icono' => 'error',
        ];
      }
    }

    // Pago
    $idPago = $this->generarCodSeg([
      'tablaBD' => 'pagos',
      'prefijo' => 'PAG',
      'campoID' => 'id_pago'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'pagos',
      'datos' => [
        'id_pago' => $idPago,
        'id_orden_entrega_presupuesto' => $idPedido,
        'fecha_pago' => $this->FechaHora_Sel('Fecha_Actual_BD'),
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al registrar el pago',
        'texto' => 'Ha ocurrido un error registrando el Pago',
        'icono' => 'error',
      ];
    }

    // Detalles - pagos
    $this->pagosPedido = $this->indexarArrays([
      'indice' => [
        'id_metodo_pago',
        'id_moneda',
        'id_banco_emisor',
        'id_banco_receptor',
        'referencia_pago'
      ],
      'camposAgrupar' => [
        'id_metodo_pago',
        'id_moneda',
        'id_banco_emisor',
        'id_banco_receptor',
        'referencia_pago'
      ],
      'camposSumar' => ['monto_pago'],
      'array' => $this->pagosPedido,
    ]);

    foreach ($this->pagosPedido as $pago) {
      $idDetallePago = $this->guardarDatos2([
        'tabla' => 'detalles_pagos',
        'datos' => [
          'id_pago' => $idPago,
          'id_metodo_pago' => $pago['id_metodo_pago'],
          'id_moneda' => $pago['id_moneda'],
          'monto_pago' => $pago['monto_pago'],
        ]
      ]);
      if ($idDetallePago == false || $idDetallePago <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error registrando los detalles del pago',
          'icono' => 'error',
        ];
      }
      if (($pago['id_banco_emisor'] ?? '') != '') {
        $idDetalleBancoEmisor = $this->guardarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'id_banco' => $pago['id_banco_emisor'],
            'es_emisor' => 1,
          ]
        ]);
        if ($idDetalleBancoEmisor == false || $idDetalleBancoEmisor <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando el banco emisor del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
      if (($pago['id_banco_receptor'] ?? '') != '') {
        $idDetalleBancoReceptor = $this->guardarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'id_banco' => $pago['id_banco_receptor'],
            'es_emisor' => 0,
          ]
        ]);
        if ($idDetalleBancoReceptor == false || $idDetalleBancoReceptor <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando el banco receptor del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
      if (($pago['referencia_pago'] ?? '') != '') {
        $idDetalleReferencia = $this->guardarDatos2([
          'tabla' => 'referencias_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'referencia_pago' => $pago['referencia_pago'],
          ]
        ]);
        if ($idDetalleReferencia == false || $idDetalleReferencia <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando la referencia del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
    }

    //Comprobantes del pago
    $comprobantesPagos = $this->Imagenes_Reg('comprobantes_pagos', $this->comprobantesPagos, 'comprobantes_pagos');
    $this->imgTrans = [
      'subCarpeta' => 'comprobantes_pagos',
      'imagenes' => $comprobantesPagos
    ];
    if (count($comprobantesPagos) != count(($this->comprobantesPagos['name'] ?? []))) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de guardado',
        'texto' => 'No se han podido guardar todos los comprobantes del pago!!! ' . count($comprobantesPagos),
        'icono' => 'error',
      ];
    }
    foreach ($comprobantesPagos as $comprobante) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'comprobantes_pagos',
        'datos' => [
          'id_pago' => $idPago,
          'path_comprobante' => $comprobante,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error al registrar los comprobantes del pago',
          'texto' => 'Ha ocurrido un error registrando los comprobantes del pago',
          'icono' => 'error',
        ];
      }
    }

    // Latitud
    $idLatitud = $this->VEYSNEC([
      'tabla' => 'latitudes_direcciones',
      'campos' => 'id_latitud_direccion',
      'WHERE' => [
        'coordenada_latitud' => $this->deliveryPedido['latitud'],
      ]
    ]);

    // Longitud
    $idLongitud = $this->VEYSNEC([
      'tabla' => 'longitudes_direcciones',
      'campos' => 'id_longitud_direccion',
      'WHERE' => [
        'coordenada_longitud' => $this->deliveryPedido['longitud'],
      ]
    ]);

    // Direccion - Ruta
    $idDireccion = $this->VEYSNEC([
      'tabla' => 'direcciones',
      'campos' => 'id_direccion',
      'WHERE' => [
        'id_ruta' => $rutaBD['id_ruta'],
        'id_latitud_direccion' => $idLatitud,
        'id_longitud_direccion' => $idLongitud,
      ]
    ]);

    // Delivery
    $idDelivery = $this->generarCodSeg([
      'tablaBD' => 'deliveries',
      'prefijo' => 'DELI',
      'campoID' => 'id_delivery'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'deliveries',
      'datos' => [
        'id_delivery' => $idDelivery,
        'id_orden_entrega_presupuesto' => $idPedido,
        'id_direccion' => $idDireccion,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ha ocurrido un error registrando el pedido',
        'icono' => 'error',
      ];
    }

    $objNot = new mensajesWSModelo();
    $resultado = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'porPermisos',
        'permisos' => [
          'pedidos' => 'ver pedidos de los clientes',
        ]
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'pedidos'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Pedido nuevo',
            'texto' => 'Acaba de llegar un pedido nuevo',
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
        [
          'accion' => "actDT",
          'modulo' => 'pedidos'
        ],
      ],
      'noCommit' => true
    ]);
    unset($objNot);
    if (isset($resultado['error']) && !isset($_COOKIE['TEMP']) && !isset($_ENV['MODO_TESTEO'])) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'pedidos',
      'accion' => 'registrar',
      'resultado' => 'Éxito',
      'viejo' => [],
      'nuevo' => [
        'productos' => $this->productosPedido,
        'pagos' => $this->pagosPedido,
        'delivery' => $this->deliveryPedido,
        'comprobantes_pago' => $comprobantesPagos,
      ]
    ]);
    unset($objBitacora);
    if (($rb['icono'] ?? '') == 'error') return $rb;

    $this->commit();
    return [
      'tipo' => 'simple',
      'titulo' => 'Pedido Registrado',
      'texto' => 'El pedido ha sido registrado con exito!!!',
      'icono' => 'success',
    ];
    // #endregion TRANSACCIÓN
  }
  private function asignarRepartidoresPedidosP() {

    $dataActualPedido = $this->listarPedidos(['id_pedido' => $this->idPedido]);
    $statusViejo = $dataActualPedido['status_pedido'];

    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'pedidos',
        'accion' => 'Asignar Repartidor al pedido (' . $this->idPedido . ')',
        'resultado' => 'Fallido',
        'commit' => true,
      ]);
    };

    //Delivery
    $resultado = $this->actualizarDatos2([
      'tabla' => 'deliveries',
      'datos' => [
        'cedula_repartidor' => $this->deliveryPedido['cedula_repartidor']
      ],
      'WHERE' => [
        'id_delivery' => $this->deliveryPedido['id_delivery']
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        'tipo' => 'simple',
        'texto' => 'No se ha podido asignar el repartidor al delivery',
        'titulo' => 'Asiganción fallida',
        'icono' => 'error'
      ];
    }

    //status
    $resultado = $this->actualizarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'status' => 7,
        'cedula_usuario' => $_SESSION['cedula'],
      ],
      'WHERE' => [
        'id_orden_entrega_presupuesto' => $this->idPedido
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        'tipo' => 'simple',
        'texto' => 'No se ha podido actualizar el estado del pedido',
        'titulo' => 'Asiganción fallida',
        'icono' => 'error'
      ];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'pedidos',
      'accion' => 'Asignar Repartidor al pedido (' . $this->idPedido . ')',
      'resultado' => 'Éxito',
      'viejo' => [
        'status_pedido' => $statusViejo,
      ],
      'nuevo' => [
        'status_pedido' => 7,
        'cedula_repartidor' => $this->deliveryPedido['cedula_repartidor'],
        'cedula_usuario' => $_SESSION['cedula'],
      ]
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'pedidos'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'pedidos'
        ],
      ],
      'noCommit' => true
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
      'tipo' => 'limpiarYcerrar',
      'texto' => 'Repartidor asignado correctamente',
      'titulo' => 'Asiganción Exitosa',
      'icono' => 'success'
    ];
  }
  private function actualizarPedidoP() {
    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'pedidos',
        'accion' => 'Actualizar pedido (' . $this->idPedido . ')',
        'resultado' => 'Fallido',
        'commit' => true,
      ]);
    };

    $dataActualPedido = $this->listarPedidos(['id_pedido' => $this->idPedido]);
    if (!isset($dataActualPedido['id_orden_entrega_presupuesto'])) return $dataActualPedido;

    $resultado = $this->procesosAlmacenados([
      'sp' => 'sp_cambiar_estado_pedido',
      'parametros' => [
        'sp_id_pedido' => $this->idPedido,
        'sp_estado' => $this->statusPedido,
      ]
    ]);
    if (!$resultado['exito']) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Fallo la actualización',
        'texto' => 'No se pudo actualizar el estado del pedido',
        'icono' => 'error'
      ];
    }

    $nuevo = [
      'status_pedido' => $this->statusPedido
    ];
    $viejo = [
      'status_pedido' =>  $dataActualPedido['status_pedido'],
    ];
    if ($this->statusPedido == 6) {
      $nuevo['productos'] = [];
      $viejo['productos'] = $dataActualPedido['productos'];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'pedidos',
      'accion' => 'Actualizar pedido (' . $this->idPedido . ')',
      'resultado' => 'Éxito',
      'viejo' => $viejo,
      'nuevo' => $nuevo
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'pedidos'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'pedidos'
        ],
      ],
      'noCommit' => true
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
      'tipo' => 'simple',
      'titulo' => 'Actualización exitosa',
      'texto' => 'Se actualizó correctamente el estado del pedido',
      'icono' => 'success'
    ];
  }
  private function imprimirPedidosP() {
    $datosPedido = $this->listarPedidos(['id_pedido' => $this->idPedido]);
    if (isset($datosPedido['icono'])) return $datosPedido;

    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'pedidos',
      'accion' => 'Imprimir pedido (' . $this->idPedido . ')',
      'resultado' => 'Éxito',
      'commit' => true,
    ]);
    if ($rb) return $rb;

    $objReportes = new pdfModel([
      'datosNotaEntrega' => $datosPedido,
      'header' => false,
      'footer' => false,
    ]);
    return $objReportes->notaEntrega();
  }
}

/*
  Estados del Pedido
  5: Pendiente de confirmar
  6: Rechazado
  7: Confirmado
  8: Entregado
*/