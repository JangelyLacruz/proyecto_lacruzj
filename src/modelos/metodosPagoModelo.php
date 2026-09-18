<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class metodosPagoModelo extends conexion {
  private int $idMetodoPago = 0;
  private string $nombreMetodoPago = '';
  private int $necesitaMoneda = 0;
  private int $necesitaBancoEmisor = 0;
  private int $necesitaBancoReceptor = 0;
  private int $necesitaReferencia = 0;
  private int $mostrarEcommerce = 0;

  public function validarMetodosPagos(string $permiso, array &$info = [], array $requerido = []) {

    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('metodos-pago', $permiso);
    if ($v) return $v;

    $clavesBooleanas = [
      'necesita_moneda',
      'mostrar_ecommerce',
      'necesita_referencia',
      'necesita_banco_receptor',
      'necesita_banco_emisor',
    ];
    foreach ($clavesBooleanas as $clave) {
      if (!isset($info[$clave])) {
        $info[$clave] = 0;
      }
    }

    $v = $this->limpiarValidar($info, [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_metodo_pago' => [
          ...molId,
          "nombreAlerta" => "ID",
          "nombreBD" => "id_metodo_pago",
          "tablaBD" => "metodos_pagos",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
        ],
        "nombre_metodo_pago" => [
          ...molNombreObj,
          "nombreBD" => "nombre_metodo_pago",
          "nombreAlerta" => "nombre",
          "tablaBD" => "metodos_pagos",
          "debeSerUnicoBD" => true
        ],
        "necesita_moneda" => [
          ...molBooleanoInt,
          "nombreAlerta" => "si necesita moneda",
        ],
        "necesita_banco_emisor" => [
          ...molBooleanoInt,
          "nombreAlerta" => "si necesita banco emisor",
        ],
        "necesita_banco_receptor" => [
          ...molBooleanoInt,
          "nombreAlerta" => "si necesita banco receptor",
        ],
        "necesita_referencia" => [
          ...molBooleanoInt,
          "nombreAlerta" => "si necesita referencia",
        ],
        "mostrar_ecommerce" => [
          ...molBooleanoInt,
          "nombreAlerta" => "si necesita referencia",
        ]
      ],
      'requerido' => $requerido,
    ]);
    if ($v) return $v;

    return false;
  }
  public function seleccionarMetodosPagos(array $info) {
    if (($info['id_metodo_pago'] ?? '') != '') {
      $v = $this->validarMetodosPagos('listar', $info, ['id_metodo_pago']);
      if ($v) return $v;
      $this->idMetodoPago = $info['id_metodo_pago'];
    }
    return $this->seleccionarMetodosPagosP($info);
  }
  public function registrarMetodosPagos(array $info) {
    $v = $this->validarMetodosPagos('registrar', $info, [
      'nombre_metodo_pago',
      'necesita_moneda',
      'necesita_banco_emisor',
      'necesita_banco_receptor',
      'necesita_referencia',
      'mostrar_ecommerce'
    ]);
    if ($v) return $v;

    $this->nombreMetodoPago = $info['nombre_metodo_pago'];
    $this->necesitaMoneda = $info['necesita_moneda'];
    $this->necesitaBancoEmisor = $info['necesita_banco_emisor'];
    $this->necesitaBancoReceptor = $info['necesita_banco_receptor'];;
    $this->necesitaReferencia = $info['necesita_referencia'];
    $this->mostrarEcommerce = $info['mostrar_ecommerce'];

    return $this->registrarMetodosPagosP();
  }
  public function actualizarMetodosPagos(array $info) {
    $v = $this->validarMetodosPagos('actualizar', $info, [
      'id_metodo_pago',
      'nombre_metodo_pago',
      'necesita_moneda',
      'necesita_banco_emisor',
      'necesita_banco_receptor',
      'necesita_referencia',
      'mostrar_ecommerce'
    ]);
    if ($v) return $v;

    $this->idMetodoPago = $info['id_metodo_pago'];
    $this->nombreMetodoPago = $info['nombre_metodo_pago'];
    $this->necesitaMoneda = $info['necesita_moneda'];
    $this->necesitaBancoEmisor = $info['necesita_banco_emisor'];
    $this->necesitaBancoReceptor = $info['necesita_banco_receptor'];
    $this->necesitaReferencia = $info['necesita_referencia'];
    $this->mostrarEcommerce = $info['mostrar_ecommerce'];

    return $this->actualizarMetodosPagosP();
  }
  public function eliminarMetodosPagos(array $info) {
    $v = $this->validarMetodosPagos('eliminar', $info, ['id_metodo_pago']);
    if ($v) return $v;

    $this->idMetodoPago = $info['id_metodo_pago'];
    return $this->eliminarMetodosPagosP();
  }

  private function seleccionarMetodosPagosP(array $info) {
    if ($this->idMetodoPago != 0 && $this->idMetodoPago != '') {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'metodos_pagos',
        'WHERE' => [
          'id_metodo_pago' => $this->idMetodoPago
        ],
      ])->fetch();
    } else {
      switch ($info['tipoConsulta'] ?? "") {
        case 'paraEcommerce':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
            'WHERE' => [
              'mostrar_ecommerce' => 1
            ],
          ])->fetchAll();
        case 'indexadosPorId':

          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
          ])->fetchAll();
      }
    }
  }
  private function registrarMetodosPagosP() {
    $objBitacora = new bitacoraModelo();
    $datosRegistrar = [
      "id_metodo_pago" => $this->idMetodoPago,
      "nombre_metodo_pago" => $this->nombreMetodoPago,
      "necesita_moneda" => $this->necesitaMoneda,
      "necesita_banco_emisor" => $this->necesitaBancoEmisor,
      "necesita_banco_receptor" => $this->necesitaBancoReceptor,
      "necesita_referencia" => $this->necesitaReferencia,
      "mostrar_ecommerce" => $this->mostrarEcommerce,
    ];
    $resultado = $this->guardarDatos2([
      'tabla' => 'metodos_pagos',
      'datos' => $datosRegistrar
    ]);
    if ($resultado <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'metodos-pago',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'metodos-pago',
      'accion' => 'registtrar',
      'resultado' => 'Éxito',
      'nuevo' => $datosRegistrar
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'metodos-pago'],
        ['accion' => "actDT", 'modulo' => 'metodos-pago'],
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
      "tipo" => "limpiarYcerrar",
      "titulo" => "Registro Exitoso",
      "texto" => "Método de pago registrado",
      "icono" => "success"
    ];
  }
  private function actualizarMetodosPagosP() {
    $datosViejos = $this->seleccionarMetodosPagos(['id_metodo_pago' => $this->idMetodoPago]);
    $resultado = $this->actualizarDatos2([
      'tabla' => 'metodos_pagos',
      'datos' => [
        "nombre_metodo_pago" => $this->nombreMetodoPago,
        "necesita_moneda" => $this->necesitaMoneda,
        "necesita_banco_emisor" => $this->necesitaBancoEmisor,
        "necesita_banco_receptor" => $this->necesitaBancoReceptor,
        "necesita_referencia" => $this->necesitaReferencia,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
      ],
      'WHERE' => ["id_metodo_pago" => $this->idMetodoPago]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($resultado == false || $resultado <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'metodos-pago',
        'accion' => 'Actualizar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizaron cambios en el registro",
        "icono" => "warning"
      ];
    }
    $datosNuevos = $this->seleccionarMetodosPagos(['id_metodo_pago' => $this->idMetodoPago]);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'metodos-pago',
      'accion' => 'Actualizar',
      'resultado' => 'Éxito',
      'viejo' => $datosViejos,
      'nuevo' => $datosNuevos,
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
          'modulo' => 'metodos-pago'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'metodos-pago'
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
      "tipo" => "limpiarYcerrar",
      "titulo" => "Actualización Exitosa",
      "texto" => "Método actualizado",
      "icono" => "success"
    ];
  }
  private function eliminarMetodosPagosP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => 'metodos_pagos',
      'WHERE' => [
        "id_metodo_pago" => $this->idMetodoPago
      ]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($resultado <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'metodos-pago',
        'accion' => 'Eliminar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo eliminar",
        "icono" => "error"
      ];
    }

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'metodos-pago',
      'accion' => 'Eliminar',
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $rb;

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'metodos-pago'],
        ['accion' => "actDT", 'modulo' => 'metodos-pago'],
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
      "tipo" => "simple",
      "titulo" => "Eliminado",
      "texto" => "El método ha sido desactivado",
      "icono" => "success"
    ];
  }
}
