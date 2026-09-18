<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;

class rutasModelo extends conexion {
  private string $idRuta = '';
  private string $nombreRuta = '';
  private string $precioRuta = '';
  private string $minimoKmRuta = '';
  private string $maximoKmRuta = '';
  private string $kmRecorrido = '';

  public function validarRutas(string $permiso, array &$info = [], array $requerido = []) {
    if (isset($permiso)) {
      $objAcceso = new accesosModelo();
      $r = $objAcceso->validarPermisos('rutas', $permiso);
      if ($r) return $r;
    }
    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        "id_ruta" => [
          ...molId,
          "nombreBD" => "id_ruta",
          "tablaBD" => "rutas",
          "nombreAlerta" => "id de la ruta",
          "debeExistirBD" => true,
          "debeSerUnicoBD" => true
        ],
        "nombre_ruta" => [
          ...molNombreObj,
          "nombreAlerta" => "nombre de la ruta",
          "nombreBD" => "nombre_ruta",
          "tablaBD" => "rutas",
          "debeSerUnico" => true,
        ],
        "precio_ruta" => [
          ...molPrecioFormateado,
          "nombreAlerta" => "precio de la ruta",
        ],
        "minimo_km_ruta" => [
          ...molPrecioFormateado,
          "nombreAlerta" => "mínimo de km de la ruta",
        ],
        "maximo_km_ruta" => [
          ...molPrecioFormateado,
          "nombreAlerta" => "máximo de km de la ruta",
        ],
        "km_recorrido" => [
          ...molPrecio,
          "nombreAlerta" => "la cantidad de km recorridos",
        ],
      ],
      'requerido' => $requerido,
    ];
    if (isset($info)) {
      $r = $this->limpiarValidar($info, $esquema);
      if ($r) return $r;
    }
    return false;
  }
  public function seleccionarRutas(array $info) {
    $resultado = $this->validarRutas('listar', $info);
    if ($resultado) return $resultado;
    $this->idRuta = $info['id_ruta'] ?? '';
    $this->kmRecorrido = $info['km_recorrido'] ?? '';
    return $this->seleccionarRutasP($info);
  }
  public function registrarRutas(array $info) {
    $r = $this->validarRutas('registrar', $info, [
      'nombre_ruta',
      'precio_ruta',
      'minimo_km_ruta',
      'maximo_km_ruta'
    ]);
    if ($r) return $r;

    $this->nombreRuta = $info['nombre_ruta'];
    $this->precioRuta = $info['precio_ruta'];
    $this->minimoKmRuta = $info['minimo_km_ruta'];
    $this->maximoKmRuta = $info['maximo_km_ruta'];
    return $this->registrarRutasP();
  }
  public function actualizarRutas(array $info) {
    $resultado = $this->validarRutas('actualizar', $info, [
      'id_ruta',
      'nombre_ruta',
      'precio_ruta',
      'minimo_km_ruta',
      'maximo_km_ruta'
    ]);
    if ($resultado) return $resultado;

    $this->idRuta = $info['id_ruta'];
    $this->nombreRuta = $info['nombre_ruta'];
    $this->precioRuta = $info['precio_ruta'];
    $this->minimoKmRuta = $info['minimo_km_ruta'];
    $this->maximoKmRuta = $info['maximo_km_ruta'];

    return $this->actualizarRutasP();
  }
  public function eliminarRutas(array $info) {
    $r = $this->validarRutas('eliminar', $info, ['id_ruta']);
    if ($r) return $r;

    $this->idRuta = $info['id_ruta'];
    return $this->eliminarRutasP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ] --//
  private function seleccionarRutasP(array $info) {
    if ($this->idRuta == null || $this->idRuta == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'porKm':
          $this->kmRecorrido = ceil($this->kmRecorrido);
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
            'WHERE' => [
              'minimo_km_ruta' => '<= ' . $this->kmRecorrido,
              'maximo_km_ruta' => '>= ' . $this->kmRecorrido,
            ],
            'ORDER' => 'maximo_km_ruta DESC'
          ])->fetch();
          if (!isset($resultado['precio_ruta'])) {
            return  [
              'tipo' => 'simple',
              'titulo' => 'Sin rutas disponibles',
              'texto' => 'No hay rutas estipuladas para esa direccion',
              'icono' => 'error'
            ];
          }
          return $resultado;
        case 'porCoordenadas':
          $infoDireccion = $this->calcularKmPorCarretera($info['coordenadas']);
          if (isset($infoDireccion['icono'])) return $infoDireccion;
          return [
            'km_recorrido' => $infoDireccion['km_recorrido'],
            'nombre_direccion' => $infoDireccion['nombre_direccion'],
          ];
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
          ])->fetchAll();
      }
    } else {
      switch ($info['tipoConsulta'] ?? '') {
        case 'porFecha':
          return $this->seleccionarDatos2([
            'campos' => '
              ru.id_ruta,ru.nombre_ruta,ru.minimo_km_ruta,ru.maximo_km_ruta,
              (
                SELECT pr.precio_ruta FROM precios_rutas as pr
                WHERE pr.id_ruta = ru.id_ruta && fecha_cambio <= "' . $info['fecha'] . '"
                ORDER BY pr.id_precio_ruta DESC
                LIMIT 1
              ) as precio_ruta_fecha
            ',
            'tabla' => 'rutas as ru',
            'WHERE' => [
              'id_ruta' => $this->idRuta,
            ],
          ])->fetch();
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
            'WHERE' => [
              "id_ruta" => $this->idRuta,
            ]
          ])->fetch();
      }
    }
  }
  private function registrarRutasP() {
    $objBitacora = new bitacoraModelo();
    $alertaError = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'rutas',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Ruta no registrada",
        "texto" => "La ruta no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    };
    $datosGuardar = [
      "nombre_ruta" => $this->nombreRuta,
      "precio_ruta" => $this->precioRuta,
      "minimo_km_ruta" => $this->minimoKmRuta,
      "maximo_km_ruta" => $this->maximoKmRuta,
    ];
    $resultado = $this->guardarDatos2([
      'tabla' => 'rutas',
      'datos' => $datosGuardar
    ]);
    if ($resultado == false || $resultado <= 0) return $alertaError();

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'rutas',
      'accion' => 'Registrar',
      'resultado' => 'Éxito',
      'nuevo' => $datosGuardar,
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
          'modulo' => 'rutas'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'rutas'
        ],
      ],
      'noCommit' => true
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $this->commit();
    return   [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Ruta registrada",
      "texto" => "La ruta ha sido registrado exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarRutasP() {
    $datosActuales = $this->seleccionarRutas(['id_ruta' => $this->idRuta]);
    $objBitacora = new bitacoraModelo();
    $alertaError = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'rutas',
        'accion' => 'Actualizar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la ruta",
        "icono" => "warning",
      ];
    };
    $resultado = $this->actualizarDatos2([
      "tabla" => "rutas",
      "datos" => [
        "nombre_ruta" => $this->nombreRuta,
        "precio_ruta" => $this->precioRuta,
        "minimo_km_ruta" => $this->minimoKmRuta,
        "maximo_km_ruta" => $this->maximoKmRuta,
      ],
      "WHERE" => [
        "id_ruta" => $this->idRuta,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) return $alertaError();

    $datosDespues = $this->seleccionarRutas(['id_ruta' => $this->idRuta]);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'rutas',
      'accion' => 'Actualizar',
      'resultado' => 'Éxito',
      'viejo' => $datosActuales,
      'nuevo' => $datosDespues
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
          'modulo' => 'rutas'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'rutas'
        ],
      ],
      'noCommit' => true
    ]);
    if (isset($r['error'])){
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de socket',
        'texto' => 'Error al comunicar el cambio al resto de los usuarios del sistema',
        'icono' => 'error'
      ];
    }

    $this->commit();
    return  [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Ruta actualizada",
      "texto" => "La ruta ha sido actualizada exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarRutasP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "rutas",
      'WHERE' => [
        "id_ruta" => $this->idRuta
      ]
    ]);
    if ($eliminarUsuario <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'rutas',
        'accion' => 'Eliminar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Ruta no encontrado",
        "texto" => "La ruta no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'rutas',
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
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'rutas'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'rutas'
        ],
      ],
      'noCommit' => true
    ]);
    if (isset($r['error'])){
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
      "titulo" => "Ruta eliminada",
      "texto" => "La ruta ha sido eliminada con éxito",
      "icono" => "success"
    ];
  }
}
