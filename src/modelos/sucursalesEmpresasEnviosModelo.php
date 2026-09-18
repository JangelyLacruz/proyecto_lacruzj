<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\rutasModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;

class sucursalesEmpresasEnviosModelo extends conexion {

  private int $idSucursal = 0;
  private int $idEmpresaEnvios = 0;
  private string $latitudSucursal = '';
  private string $longitudSucursal = '';
  private string $nombreSucursal = '';

// PÚBLICOS 

  public function validarSucursalesEmpresasEnvios(string $permiso, array $instruccionesVal) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('sucursalesEmpresasEnvios', $permiso);
    if ($r) return $r;

    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_sucursal_empresa_envios" => [
          "campo_nombre" => "id_sucursal_empresa_envios",
          "campo_valor" => &$valor,
          "formulario_nombre" => "sucursal de empresas de envíos",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "sucursales_empresas_envios",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "id_empresa_envios" => [
          "campo_nombre" => "id_empresa_envios",
          "campo_valor" => &$valor,
          "formulario_nombre" => "empresa de envíos",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "empresas_envios",
          "debeExistir" => true,
        ],
        "nombre_sucursal_empresa" => [
          "campo_nombre" => "nombre_sucursal_empresa",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la sucursal",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "sucursales_empresas_envios",
          "debeSerUnico" => true,
        ],
        "latitud_sucursal" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "latitud de la dirección",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
        "longitud_sucursal" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "longitud de la dirección",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $campos[] =  $funcionAsignadora($campo, $infoVal[$valorForm]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarSucursalesEmpresasEnvios($info = NULL) {
    if (($info['id_sucursal_empresa_envios'] ?? '') != "") {
      $resultado = $this->validarSucursalesEmpresasEnvios('listar', [
        'infoVal' => &$info,
        'camposVal' => [
          'id_sucursal_empresa_envios',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idSucursal = $info['id_sucursal_empresa_envios'];
    }
    return $this->seleccionarSucursalesEmpresasEnviosP();
  }
  public function registrarSucursalesEmpresasEnvios(array $info) {
    $resultado = $this->validarSucursalesEmpresasEnvios('registraRRR', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_empresa_envios',
        'nombre_sucursal_empresa',
        'latitud_sucursal',
        'longitud_sucursal',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreSucursal = $info['nombre_sucursal_empresa'];
    $this->latitudSucursal = $info['latitud_sucursal'];
    $this->longitudSucursal = $info['longitud_sucursal'];

    return $this->registrarSucursalesEmpresasEnviosP();
  }
  public function actualizarSucursalesEmpresasEnvios(array $info) {

    $latVacia = empty($info['latitud_sucursal'] ?? '');
    $lonVacia = empty($info['longitud_sucursal'] ?? '');

    if ($latVacia || $lonVacia) {
      $datosActuales = $this->seleccionarDatos2([
        'campos' => 'latd.coordenada_latitud, lond.coordenada_longitud',
        'tabla' => 'sucursales_empresas_envios as seen',
        'WHERE' => ['seen.id_sucursal_empresa_envios' => $info['id_sucursal_empresa_envios'] ?? ''],
        'datosJoins' => [
          'direcciones as di' => 'seen.id_direccion = di.id_direccion',
          'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
          'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
        ]
      ])->fetch(\PDO::FETCH_ASSOC);

      if ($datosActuales) {
        if ($latVacia) $info['latitud_sucursal'] = $datosActuales['coordenada_latitud'];
        if ($lonVacia) $info['longitud_sucursal'] = $datosActuales['coordenada_longitud'];
      }
    }

    $resultado = $this->validarSucursalesEmpresasEnvios('actualizar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_sucursal_empresa_envios',
        'id_empresa_envios',
        'nombre_sucursal_empresa',
        'latitud_sucursal',
        'longitud_sucursal',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idSucursal = $info['id_sucursal_empresa_envios'];
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreSucursal = $info['nombre_sucursal_empresa'];
    $this->latitudSucursal = $info['latitud_sucursal'];
    $this->longitudSucursal = $info['longitud_sucursal'];

    return $this->actualizarSucursalesEmpresasEnviosP();
  }
  public function eliminarSucursalesEmpresasEnvios(array $info) {
    $resultado = $this->validarSucursalesEmpresasEnvios('eliminar', [
      'infoVal' => &$info,
      'camposVal' => [
        'id_sucursal_empresa_envios',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idSucursal = $info['id_sucursal_empresa_envios'];
    return $this->eliminarSucursalesEmpresasEnviosP();
  }

  // PRIVADOS 
  private function seleccionarSucursalesEmpresasEnviosP() {
    if ($this->idSucursal == null || $this->idSucursal == "") {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'sucursales_empresas_envios AS se',
        'datosJoins' => [
          'empresas_envios AS ee' => 'se.id_empresa_envios = ee.id_empresa_envios'
        ]
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          seen.id_sucursal_empresa_envios,
          seen.id_empresa_envios, seen.nombre_sucursal_empresa,
          latd.coordenada_latitud, lond.coordenada_longitud
        ',
        'tabla' => 'sucursales_empresas_envios as seen',
        'WHERE' => [
          "seen.id_sucursal_empresa_envios" => $this->idSucursal,
        ],
        'datosJoins' => [
          'direcciones as di' => 'seen.id_direccion = di.id_direccion',
          'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
          'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
        ]
      ])->fetch();
    }
  }
  private function registrarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();

    $datosNuevo = [
      'id_empresa_envios'       => $this->idEmpresaEnvios,
      'id_direccion'            => null,
      'nombre_sucursal_empresa' => $this->nombreSucursal,
    ];

    try {
      $idLatitud = $this->VEYSNEC([
        'tabla'  => 'latitudes_direcciones',
        'campos' => 'id_latitud_direccion',
        'WHERE'  => [
          'coordenada_latitud' => $this->latitudSucursal,
        ],
      ]);

      $idLongitud = $this->VEYSNEC([
        'tabla'  => 'longitudes_direcciones',
        'campos' => 'id_longitud_direccion',
        'WHERE'  => [
          'coordenada_longitud' => $this->longitudSucursal,
        ],
      ]);

      $nroKm = $this->calcularKmEntreCoordenadas([
        'lat1' => $this->latitudSucursal,
        'lon1' => $this->longitudSucursal,
        'lat2' => coorJLACRUZ['latitud'],
        'lon2' => coorJLACRUZ['longitud'],
      ]);

      $objRutas = new rutasModelo();

      $rutaBD = $objRutas->seleccionarRutas([
        'km_recorrido' => $nroKm,
        'tipoConsulta' => 'porKm',
        'isInterno'    => true,
      ]);

      // Validamos si existe una ruta para la distancia calculada
      if (isset($rutaBD['tipo']) || !isset($rutaBD['id_ruta'])) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'registrar',
          'resultado' => 'Fallido',
          'viejo'     => [],
          'nuevo'     => $datosNuevo,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al registrar',
          'texto'  => 'No hay rutas estipuladas para esa distancia',
          'icono'  => 'error',
        ];
      }

      $idDireccion = $this->VEYSNEC([
        'tabla'  => 'direcciones',
        'campos' => 'id_direccion',
        'WHERE'  => [
          'id_latitud_direccion'  => $idLatitud,
          'id_longitud_direccion' => $idLongitud,
          'id_ruta'               => $rutaBD['id_ruta'],
        ],
      ]);

      $datosNuevo['id_direccion'] = $idDireccion;

      $ultimoId = $this->guardarDatos2([
        'tabla' => 'sucursales_empresas_envios',
        'datos' => [
          'id_empresa_envios'       => $this->idEmpresaEnvios,
          'id_direccion'            => $idDireccion,
          'nombre_sucursal_empresa' => $this->nombreSucursal,
        ],
      ]);

      // Validamos si la sucursal fue registrada correctamente
      if ($ultimoId === false || $ultimoId <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'registrar',
          'resultado' => 'Fallido',
          'viejo'     => [],
          'nuevo'     => $datosNuevo,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al registrar',
          'texto'  => 'La sucursal no ha sido registrada exitosamente',
          'icono'  => 'error',
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'registrar',
        'resultado' => 'Éxito',
        'viejo'     => [],
        'nuevo'     => $this->seleccionarSucursalesEmpresasEnvios([
          'id_sucursal_empresa_envios' => $ultimoId,
        ]),
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol'  => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          [
            'accion' => 'borrarDataModuloSS',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Nueva Sucursal de Envíos',
              'texto'    => "Se ha registrado la sucursal {$this->nombreSucursal}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'limpiarYcerrar',
        'titulo' => 'Sucursal registrada',
        'texto'  => 'La sucursal ha sido registrada exitosamente',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'registrar',
        'resultado' => 'Fallido',
        'viejo'     => [],
        'nuevo'     => $datosNuevo,
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al registrar',
        'texto'  => 'No se ha podido registrar la sucursal',
        'icono'  => 'error',
      ];
    }
  }
  private function actualizarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();

    $datosAntes = null;

    $datosNuevos = [
      'id_empresa_envios'       => $this->idEmpresaEnvios,
      'id_direccion'            => null,
      'nombre_sucursal_empresa' => $this->nombreSucursal,
    ];

    try {
      $datosAntes = $this->seleccionarSucursalesEmpresasEnvios([
        'id_sucursal_empresa_envios' => $this->idSucursal,
      ]);

      // Validamos si existe la sucursal
      if (!$datosAntes) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => [
            'id_sucursal_empresa_envios' => $this->idSucursal,
          ],
          'nuevo'     => $datosNuevos,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al actualizar',
          'texto'  => 'La sucursal no existe en el sistema',
          'icono'  => 'error',
        ];
      }

      $idLatitud = $this->VEYSNEC([
        'tabla'  => 'latitudes_direcciones',
        'campos' => 'id_latitud_direccion',
        'WHERE'  => [
          'coordenada_latitud' => $this->latitudSucursal,
        ],
      ]);

      $idLongitud = $this->VEYSNEC([
        'tabla'  => 'longitudes_direcciones',
        'campos' => 'id_longitud_direccion',
        'WHERE'  => [
          'coordenada_longitud' => $this->longitudSucursal,
        ],
      ]);

      $nroKm = $this->calcularKmEntreCoordenadas([
        'lat1' => $this->latitudSucursal,
        'lon1' => $this->longitudSucursal,
        'lat2' => coorJLACRUZ['latitud'],
        'lon2' => coorJLACRUZ['longitud'],
      ]);

      $objRutas = new rutasModelo();

      $rutaBD = $objRutas->seleccionarRutas([
        'km_recorrido' => $nroKm,
        'tipoConsulta' => 'porKm',
        'isInterno'    => true,
      ]);

      // Validamos si existe una ruta para esa ubicación
      if (isset($rutaBD['tipo']) || !isset($rutaBD['id_ruta'])) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => $datosAntes,
          'nuevo'     => $datosNuevos,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al actualizar',
          'texto'  => 'No hay rutas estipuladas para esa ubicación',
          'icono'  => 'error',
        ];
      }

      $idDireccion = $this->VEYSNEC([
        'tabla'  => 'direcciones',
        'campos' => 'id_direccion',
        'WHERE'  => [
          'id_latitud_direccion'  => $idLatitud,
          'id_longitud_direccion' => $idLongitud,
          'id_ruta'               => $rutaBD['id_ruta'],
        ],
      ]);

      $datosNuevos['id_direccion'] = $idDireccion;

      $resultado = $this->actualizarDatos2([
        'tabla' => 'sucursales_empresas_envios',
        'datos' => $datosNuevos,
        'WHERE' => [
          'id_sucursal_empresa_envios' => $this->idSucursal,
        ],
      ]);

      // Validamos si la sucursal fue actualizada
      if ($resultado === false || $resultado <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'actualizar',
          'resultado' => 'Fallido',
          'viejo'     => $datosAntes,
          'nuevo'     => $datosNuevos,
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al actualizar',
          'texto'  => 'La sucursal no ha sido actualizada',
          'icono'  => 'error',
        ];
      }

      $datosDespues = $this->seleccionarSucursalesEmpresasEnvios([
        'id_sucursal_empresa_envios' => $this->idSucursal,
      ]);

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'actualizar',
        'resultado' => 'Éxito',
        'viejo'     => $datosAntes,
        'nuevo'     => $datosDespues,
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol'  => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          [
            'accion' => 'borrarDataModuloSS',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Sucursal actualizada',
              'texto'    => "Se ha actualizado la sucursal {$this->nombreSucursal}",
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'limpiarYcerrar',
        'titulo' => 'Sucursal actualizada',
        'texto'  => 'La sucursal ha sido actualizada exitosamente',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'actualizar',
        'resultado' => 'Fallido',
        'viejo'     => $datosAntes ?: [
          'id_sucursal_empresa_envios' => $this->idSucursal,
        ],
        'nuevo'     => $datosNuevos,
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al actualizar',
        'texto'  => 'No se ha podido actualizar la sucursal',
        'icono'  => 'error',
      ];
    }
  }
  private function eliminarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $objWS       = new mensajesWSModelo();

    $datosAntes = null;

    try {
      $datosAntes = $this->seleccionarSucursalesEmpresasEnvios([
        'id_sucursal_empresa_envios' => $this->idSucursal,
      ]);

      // Validamos si existe la sucursal
      if (!$datosAntes) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => [
            'id_sucursal_empresa_envios' => $this->idSucursal,
          ],
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La sucursal no existe en la Base de Datos',
          'icono'  => 'error',
        ];
      }

      $resultado = $this->eliminarDatos2([
        'tabla' => 'sucursales_empresas_envios',
        'WHERE' => [
          'id_sucursal_empresa_envios' => $this->idSucursal,
        ],
      ]);

      // Validamos si la sucursal fue eliminada correctamente
      if ($resultado === false || $resultado <= 0) {
        $objBitacora->registrarBitacora([
          'modulo'    => 'sucursalesEmpresasEnvios',
          'accion'    => 'eliminar',
          'resultado' => 'Fallido',
          'viejo'     => $datosAntes,
          'nuevo'     => [],
        ]);

        $this->rollback();

        return [
          'tipo'   => 'simple',
          'titulo' => 'Error al eliminar',
          'texto'  => 'La sucursal no ha podido ser eliminada',
          'icono'  => 'error',
        ];
      }

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'eliminar',
        'resultado' => 'Éxito',
        'viejo'     => $datosAntes,
        'nuevo'     => [],
      ]);

      $this->commit();

      $objWS->enviarMensajesWS([
        'noCommit' => true,
        'receptor' => [
          'tipo' => 'rol',
          'rol'  => 'ADMINISTRADOR',
        ],
        'cuerpo' => [
          [
            'accion' => 'borrarDataModuloSS',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'actDT',
            'modulo' => 'sucursalesEmpresasEnvios',
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo'     => 'simple',
              'titulo'   => 'Sucursal eliminada',
              'texto'    => 'La sucursal ha sido eliminada del sistema',
              'icono'    => 'info',
              'notifier' => true,
              'tiempo'   => 3000,
            ],
          ],
        ],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Sucursal eliminada',
        'texto'  => 'La sucursal ha sido eliminada con éxito',
        'icono'  => 'success',
      ];
    } catch (\Throwable) {
      $this->rollback();

      $objBitacora->registrarBitacora([
        'modulo'    => 'sucursalesEmpresasEnvios',
        'accion'    => 'eliminar',
        'resultado' => 'Fallido',
        'viejo'     => $datosAntes ?: [
          'id_sucursal_empresa_envios' => $this->idSucursal,
        ],
        'nuevo'     => [],
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al eliminar',
        'texto'  => 'No se ha podido eliminar la sucursal',
        'icono'  => 'error',
      ];
    }
  }
}
