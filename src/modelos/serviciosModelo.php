<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;
use src\modelos\productosModelo;
use PDO;


class serviciosModelo extends conexion {
  private string $idServicio = '';
  private string $idUnidadMedida = '';
  private string $nombreServicio = '';
  private float  $precioServicio = 0;
  private int $mostrarEcommerce = 0;
  private array $fotoServicio = [];
  private array  $productosServicio = [];

  // PÚBLICOS

  
  public function validarServicios(string $permiso, array &$info = [], array $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('servicios', $permiso);
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'foto_servicio' => [
          ...molFotoInd,
          'nombreAlerta' => 'foto del servicio'
        ],
        'id_servicio' => [
          ...molIdSeguro,
          "nombreAlerta" => "id del servicio",
          "nombreBD" => "id_servicio",
          "tablaBD" => "servicios",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
        ],
        'id_unidad_medida' => [
          ...molId,
          "nombreAlerta" => "unidad de medida",
          "nombreBD" => "id_unidad_medida",
          "tablaBD" => "unidades_medidas",
          "debeExistirBD" => true,
        ],
        'nombre_servicio' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre del servicio",
          "nombreBD" => "nombre_servicio",
          "tablaBD" => "servicios",
          "debeSerUnicoBD" => true,
        ],
        'precio_servicio' => [
          ...molPrecioFormateado,
          "nombreAlerta" => "precio del servicio",
        ],
        'mostrar_ecommerce' => [
          ...molBooleano,
          "nombreAlerta" => "mostrar en el ecommerce",
        ],
        'productos_servicio' => [
          'tipo' => 'array',
          'minItems' => 0,
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'id_producto' => [
                ...molIdSeguro,
                "nombreAlerta" => "id del producto",
                "nombreBD" => "id_producto",
                "tablaBD" => "productos",
                "debeExistirBD" => true,
              ],
              'cantidad_producto' => [
                ...molPrecioFormateado,
                "nombreAlerta" => "cantidad del producto",
              ],
            ],
            'requerido' => ['id_producto', 'cantidad_producto']
          ],
          'nombreAlerta' => 'productos del servicio'
        ]
      ],
      'requerido' => $requerido,
    ];

    // Reconstruir productos_servicio desde claves planas (cuando se envía como formData)
    // Formato: productos_servicio-{index}-{campo} => valor
    if (!isset($info['productos_servicio'])) {
      $productosTemp = [];
      foreach ($info as $clave => $valor) {
        if (strpos($clave, 'productos_servicio-') === 0) {
          $partes = explode('-', $clave, 3); // ['productos_servicio', '0', 'id_producto']
          if (count($partes) === 3) {
            $indice = $partes[1];
            $campo  = $partes[2];
            $productosTemp[$indice][$campo] = $valor;
          }
          unset($info[$clave]);
        }
      }
      ksort($productosTemp);
      $info['productos_servicio'] = array_values($productosTemp);
    } elseif (is_string($info['productos_servicio'])) {
      $info['productos_servicio'] = json_decode($info['productos_servicio'], true) ?: [];
    } elseif (is_array($info['productos_servicio'])) {
      $info['productos_servicio'] = array_values($info['productos_servicio']);
    }

    // Convertir mostrar_ecommerce de string a bool (el form envía "1" o "0")
    if (isset($info['mostrar_ecommerce'])) {
      $info['mostrar_ecommerce'] = (bool) $info['mostrar_ecommerce'];
    }

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;

    return false;
  }
  public function seleccionarServicios(array $info) {
    if (!isset($info['isInterno'])) {
      if (($info['id_servicio'] ?? '') != '') {
        $respuesta = $this->validarServicios('listar', $info, ['id_servicio']);
        if ($respuesta !== false) return $respuesta;
      } else {
        $respuesta = $this->validarServicios('listar', $info, []);
        if ($respuesta !== false) return $respuesta;
      }
    }
    $this->idServicio = $info['id_servicio'] ?? '';
    return $this->seleccionarServiciosP($info);
  }
  public function obtenerParaChatbot() {
    return $this->obtenerParaChatbotP();
  }
  public function registrarServicio(array $info) {
    $respuesta = $this->validarServicios('registrar', $info, [
      'id_unidad_medida',
      'nombre_servicio',
      'precio_servicio'
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idUnidadMedida    = $info['id_unidad_medida'];
    $this->nombreServicio    = $info['nombre_servicio'];
    $this->precioServicio    = $info['precio_servicio'];
    $this->mostrarEcommerce  = $info['mostrar_ecommerce'] ?? 0;
    $this->productosServicio = $info['productos_servicio'] ?? [];

    if (isset($info['foto_servicio']) && !empty($info['foto_servicio'])) {
      $this->fotoServicio = $info['foto_servicio'];
    }

    return $this->registrarServicioP();
  }
  public function actualizarServicio(array $info) {
    $respuesta = $this->validarServicios('actualizar', $info, [
      'id_servicio',
      'id_unidad_medida',
      'nombre_servicio',
      'precio_servicio'
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idServicio        = $info['id_servicio'];
    $this->idUnidadMedida    = $info['id_unidad_medida'];
    $this->nombreServicio    = $info['nombre_servicio'];
    $this->precioServicio    = $info['precio_servicio'];
    $this->mostrarEcommerce  = $info['mostrar_ecommerce'] ?? 0;
    $this->productosServicio = $info['productos_servicio'] ?? [];

    if (isset($info['foto_servicio']) && !empty($info['foto_servicio'])) {
      $this->fotoServicio = $info['foto_servicio'];
    }

    return $this->actualizarServicioP();
  }
  public function eliminarServicio(array $info) {
    $respuesta = $this->validarServicios('eliminar', $info, ['id_servicio']);
    if ($respuesta !== false) return $respuesta;

    $this->idServicio = $info['id_servicio'];
    return $this->eliminarServicioP();
  }
  public function actualizarFotoServicio(array $info) {
    $respuesta = $this->validarServicios('actualizar', $info, ['id_servicio', 'foto_servicio']);
    if ($respuesta !== false) return $respuesta;
    $this->idServicio = $info['id_servicio'];
    $this->fotoServicio = $info['foto_servicio'];
    return $this->actualizarFotoServicioP();
  }

  public function eliminarFotoServicio(array $info) {
    $respuesta = $this->validarServicios('actualizar', $info, ['id_servicio']);
    if ($respuesta !== false) return $respuesta;
    $this->idServicio = $info['id_servicio'];
    return $this->eliminarFotoServicioP();
  }

  // PRIVADOS

  private function obtenerParaChatbotP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'nombre_servicio, precio_servicio',
      'tabla' => 'servicios',
      'WHERE' => ['status' => 1]
    ]);
    return ($resultado && $resultado->rowCount() > 0) ? $resultado->fetchAll(\PDO::FETCH_ASSOC) : [];
  }
 
  private function seleccionarServiciosP(){
    if ($this->idServicio == null || $this->idServicio == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'servicios as s',
        'datosJoins' => [
          "unidades_medidas as um" => "s.id_unidad_medida = um.id_unidad_medida",
        ]
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      // Datos generales
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'servicios as s',
        'WHERE' => [
          "id_servicio" => $this->idServicio,
        ],
        'datosJoins' => [
          'unidades_medidas as um' => 's.id_unidad_medida = um.id_unidad_medida'
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Servicio no encontrado",
          "texto" => "El servicio no se encuentra.",
          "icono" => "error"
        ];
      }
      $servicio = $resultado->fetch(PDO::FETCH_ASSOC);

      // Productos del servicio con nombres en una sola consulta JOIN
      $resultado = $this->seleccionarDatos2([
        'campos' => 'ps.id_producto, ps.cantidad_producto, pr.nombre_producto, um.nombre_unidad_medida',
        'tabla' => 'productos_servicios as ps',
        'datosJoins' => [
          'productos as pr' => 'ps.id_producto = pr.id_producto',
          'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida',
        ],
        'WHERE' => [
          'ps.id_servicio' => $this->idServicio
        ]
      ]);
      $productosServicio = $resultado->fetchAll(PDO::FETCH_ASSOC);

      $servicio['detallesExtra'] = [
        'productos_servicio' => $productosServicio,
      ];
      return $servicio;
    }
  }
  private function registrarServicioP() {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora([
        'modulo' => 'servicios',
        'accion' => 'Registrar',
        'resultado' => 'fallido',
        'commit' => true
      ]);
    };

    $idServicio = $this->generarCodSeg([
      'tablaBD' => 'servicios',
      'prefijo' => 'SERV',
      'campoID' => 'id_servicio'
    ]);

    $objBit = new bitacoraModelo();

    // Foto
    $nombreImagen = '';
    if (!empty($this->fotoServicio)) {
      $nombreImagen = $this->Imagenes_Reg(
        'servicios',
        $this->fotoServicio,
        'servicios'
      );
    }

    $resultado = $this->guardarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'id_servicio' => $idServicio,
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_servicio" => $this->nombreServicio,
        "precio_servicio" => $this->precioServicio,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
        "foto_servicio" => $nombreImagen,
      ],
    ]);

    if ($resultado == false || $resultado <= 0) {
      $funcionError($objBit);
      if ($nombreImagen != '') {
        $this->Imagenes_Eli2('servicios', $nombreImagen);
      }
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo registrar el servicio',
        'icono' => 'error'
      ];
    }

    // Productos del servicio (materias primas)
    foreach ($this->productosServicio as $prod) {
      $idProd = $this->guardarDatos2([
        'tabla' => 'productos_servicios',
        'datos' => [
          "id_servicio" => $idServicio,
          "id_producto" => $prod['id_producto'],
          "cantidad_producto" => $prod['cantidad_producto'],
        ]
      ]);
      if ($idProd == false || $idProd <= 0) {
        $funcionError($objBit);
        if ($nombreImagen != '') {
          $this->Imagenes_Eli2('servicios', $nombreImagen);
        }
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se pudieron registrar los productos del servicio',
          'icono' => 'error'
        ];
      }
    }

    $datosNuevos = [
      'id_servicio' => $idServicio,
      'id_unidad_medida' => $this->idUnidadMedida,
      'nombre_servicio' => $this->nombreServicio,
      'precio_servicio' => $this->precioServicio,
      'mostrar_ecommerce' => $this->mostrarEcommerce,
      'productos_servicio' => $this->productosServicio,
    ];

    $objBit->registrarBitacora([
      'modulo' => 'servicios',
      'accion' => 'Registrar servicio: ' . $this->nombreServicio,
      'resultado' => 'Éxito',
      'nuevo' => $datosNuevos,
    ]);
    $this->commit();

    $objNot = new mensajesWSModelo();
    $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'rol',
        'rol' => 'ADMINISTRADOR'
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'servicios'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Nuevo Servicio',
            'texto' => "Se ha registrado el servicio {$this->nombreServicio}",
            'icono' => 'info',
            'notifier' => true,
            'tiempo' => 3000
          ]
        ],
        [
          'accion' => "actDT",
          'modulo' => 'servicios'
        ],
      ]
    ]);
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Servicio registrado",
      "texto" => "El servicio ha sido registrado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarServicioP() {
    $SRV = 0;
    $PRS = 0;

    $funcionError = function () {
      $bitacoraModelo = new bitacoraModelo();
      $this->rollback();
      $bitacoraModelo->registrarBitacora([
        'modulo' => 'servicios',
        'accion' => 'Actualizar',
        'resultado' => 'fallido',
        'commit' => true
      ]);
    };

    $servicioActual = $this->seleccionarServicios(['id_servicio' => $this->idServicio, 'isInterno' => true]);

    $datosGenerales = [
      "id_unidad_medida" => $this->idUnidadMedida,
      "nombre_servicio" => $this->nombreServicio,
      "precio_servicio" => $this->precioServicio,
      "mostrar_ecommerce" => $this->mostrarEcommerce,
    ];

    // Foto
    if (!empty($this->fotoServicio)) {
      $nombreImagen = $this->Imagenes_Reg(
        'servicios',
        $this->fotoServicio,
        'servicios'
      );
      $datosGenerales["foto_servicio"] = $nombreImagen;

      if (($servicioActual['foto_servicio'] ?? '') != '') {
        $this->Imagenes_Eli2('servicios', $servicioActual['foto_servicio']);
      }
    }

    // Datos generales
    $resultado = $this->actualizarDatos2([
      "tabla" => "servicios",
      "datos" => $datosGenerales,
      "WHERE" => [
        "id_servicio" => $this->idServicio,
      ]
    ]);
    if ($resultado != false && $resultado > 0) $SRV++;

    // Productos del servicio
    if (($servicioActual['detallesExtra']['productos_servicio'] ?? []) != []) {
      $PRS += $resultado = $this->eliminarDatos2([
        'tabla' => "productos_servicios",
        'WHERE' => [
          "id_servicio" => $this->idServicio
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Productos anteriores no eliminados',
          'texto' => 'No se pudo actualizar el servicio',
          'icono' => 'error',
        ];
      }
    }

    foreach ($this->productosServicio as $prod) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'productos_servicios',
        'datos' => [
          "id_servicio" => $this->idServicio,
          "id_producto" => $prod['id_producto'],
          "cantidad_producto" => $prod['cantidad_producto'],
        ]
      ]);
      if ($resultado != false && $resultado > 0) $PRS++;
    }

    if ($SRV == 0 && $PRS == 0) {
      $funcionError();
      return [
        'icono' => 'warning',
        'titulo' => 'Sin Modificaciones',
        'texto' => 'No se detectaron cambios',
        'tipo' => 'simple'
      ];
    }

    $datosNuevos = [
      'id_unidad_medida' => $this->idUnidadMedida,
      'nombre_servicio' => $this->nombreServicio,
      'precio_servicio' => $this->precioServicio,
      'mostrar_ecommerce' => $this->mostrarEcommerce,
      'productos_servicio' => $this->productosServicio,
    ];

    $productosServicioViejos = array_map(function($p) {
      return [
        'id_producto' => (string)$p['id_producto'],
        'cantidad_producto' => (string)$p['cantidad_producto']
      ];
    }, $servicioActual['detallesExtra']['productos_servicio'] ?? []);

    $datosViejos = [
      'id_unidad_medida' => $servicioActual['id_unidad_medida'] ?? '',
      'nombre_servicio' => $servicioActual['nombre_servicio'] ?? '',
      'precio_servicio' => $servicioActual['precio_servicio'] ?? 0,
      'mostrar_ecommerce' => $servicioActual['mostrar_ecommerce'] ?? 0,
      'productos_servicio' => $productosServicioViejos,
    ];

    $bitacoraModelo = new bitacoraModelo();
    $resultado = $bitacoraModelo->registrarBitacora([
      'modulo' => 'servicios',
      'accion' => 'Actualizar servicio: ' . $this->nombreServicio,
      'resultado' => 'Éxito',
      'viejo' => $datosViejos,
      'nuevo' => $datosNuevos,
    ]);
    if ($resultado) {
      $funcionError();
      return $resultado;
    }

    $this->commit();

    if ($PRS > 0) {
      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Servicio Actualizado',
              'texto' => "Se han actualizado las materias primas de un servicio.",
              'icono' => 'info',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ],
          [
            'accion' => "actDT",
            'modulo' => 'servicios'
          ],
        ]
      ]);
    } else {
      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'servicios'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'servicios'
          ],
        ]
      ]);
    }
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Servicio actualizado",
      "texto" => "El servicio ha sido actualizado exitosamente",
      "icono" => "success"
    ];
  }
  private function eliminarServicioP() {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora([
        'modulo' => 'servicios',
        'accion' => 'Eliminar',
        'resultado' => 'fallido',
        'commit' => true
      ]);
    };
    $objBi = new bitacoraModelo();

    $servicioActual = $this->seleccionarServicios([
      'id_servicio' => $this->idServicio,
      'isInterno' => true,
    ]);

    // Productos del servicio
    if (count($servicioActual['detallesExtra']['productos_servicio'] ?? []) > 0) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "productos_servicios",
        'WHERE' => [
          "id_servicio" => $this->idServicio
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $funcionError($objBi);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error eliminando los productos asociados al servicio',
          'icono' => 'error',
        ];
      }
    }

    // El servicio
    $resultado = $this->eliminarDatos2([
      'tabla' => "servicios",
      'WHERE' => [
        "id_servicio" => $this->idServicio
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el servicio',
        'icono' => 'error',
      ];
    }

    $datosViejos = [
      'id_servicio' => $this->idServicio,
      'nombre_servicio' => $servicioActual['nombre_servicio'] ?? '',
      'precio_servicio' => $servicioActual['precio_servicio'] ?? 0,
      'productos_servicio' => $servicioActual['detallesExtra']['productos_servicio'] ?? [],
    ];

    $resBit = $objBi->registrarBitacora([
      'modulo' => 'servicios',
      'accion' => 'Eliminar servicio: ' . ($servicioActual['nombre_servicio'] ?? $this->idServicio),
      'resultado' => 'Éxito',
      'viejo' => $datosViejos,
    ]);
    if ($resBit) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error registrando el evento en la bitácora',
        'icono' => 'error',
      ];
    }

    // Foto del servicio
    if (($servicioActual['foto_servicio'] ?? '') != '') {
      $this->Imagenes_Eli2('servicios', $servicioActual['foto_servicio']);
    }

    $this->commit();

    $objNot = new mensajesWSModelo();
    $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'rol',
        'rol' => 'ADMINISTRADOR'
      ],
      'cuerpo' => [
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Servicio Eliminado',
            'texto' => "Se ha eliminado un servicio.",
            'icono' => 'warning',
            'notifier' => true,
            'tiempo' => 3000
          ]
        ],
        [
          'accion' => "actDT",
          'modulo' => 'servicios'
        ],
      ]
    ]);
    return [
      "tipo" => "simple",
      "titulo" => "Servicio eliminado",
      "texto" => "El servicio ha sido eliminado con éxito",
      "icono" => "success"
    ];
  }
  private function actualizarFotoServicioP() {
    $nombreImagen = $this->Imagenes_Reg(
      'servicios',
      $this->fotoServicio,
      'servicios'
    );
    $resultado = $this->actualizarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'foto_servicio' => $nombreImagen,
      ],
      'WHERE' => [
        'id_servicio' => $this->idServicio,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $this->Imagenes_Eli2('servicios', $nombreImagen);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo actualizar la foto del servicio',
        'icono' => 'error',
      ];
    }
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Foto actualizada",
      "texto" => "La foto del servicio ha sido actualizada",
      "icono" => "success"
    ];
  }
  private function eliminarFotoServicioP() {
    $servicioActual = $this->seleccionarServicios(['id_servicio' => $this->idServicio]);
    if (($servicioActual['foto_servicio'] ?? '') != '') {
      $this->Imagenes_Eli2('servicios', $servicioActual['foto_servicio']);
    }
    $resultado = $this->actualizarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'foto_servicio' => '',
      ],
      'WHERE' => [
        'id_servicio' => $this->idServicio,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo eliminar la foto del servicio',
        'icono' => 'error',
      ];
    }
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Foto eliminada",
      "texto" => "La foto del servicio ha sido eliminada",
      "icono" => "success"
    ];
  }
}