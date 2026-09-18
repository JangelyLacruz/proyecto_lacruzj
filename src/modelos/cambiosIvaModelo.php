<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;
use PDO;

class cambiosIvaModelo extends conexion {
  private int $idCambio = 0;
  private float $montoCambioIva = 0;

  public function validarCambiosIva(string $permiso, array $instruccionesVal) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('cambiosIva', $permiso);
    if ($r) return $r;

    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_cambio_iva' => [
          "campo_nombre" => 'id_cambio_iva',
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del cambio",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'cambios_iva',
          "debeExistir" => true
        ],
        'monto_cambio_iva' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "monto del IVA",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          "noCero" => true,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      if ($campo == 'monto_cambio_iva' && $infoVal[$campo] > 100) {
        return [
          'tipo' => 'simple',
          'titulo' => 'Porcentaje superior al 100%',
          'texto' => 'No puede estipular un porcentaje de más del 100%',
          'icono' => 'error',
        ];
      }
      $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarCambiosIva(array $info) {
    if (($info['id_cambio_iva'] ?? '') != "") {
      $resultado = $this->validarCambiosIva('ver',[
        'infoVal' => &$info,
        'camposVal' => ['id_cambio_iva'],
      ]);
      if ($resultado) return $resultado;
      $this->idCambio = $info['id_cambio_iva'];
    }
    return $this->seleccionarCambiosIvaP($info);
  }
  public function registrarCambiosIva(array $info) {
    $resultado = $this->validarCambiosIva('registrar', [
      'infoVal' => &$info,
      'camposVal' => ['monto_cambio_iva'],
    ]);

    if ($resultado) return $resultado;
    $this->montoCambioIva = $info['monto_cambio_iva'];
    return $this->registrarCambiosIvaP();
  }

  private function seleccionarCambiosIvaP(array $info) {
    if ($this->idCambio == null || $this->idCambio == "") {
      if (($info['tipoConsulta'] ?? '') == 'ivaActual') {
        return $this->seleccionarDatos2([
          'campos' => '*',
          'tabla' => 'cambios_iva',
          'ORDER' => 'id_cambio_iva DESC',
          'LIMIT' => 1
        ])->fetch();
      } else {
        return $this->seleccionarDatos2([
          'campos' => '*',
          'tabla' => 'v_cambios_iva_todos',
        ])->fetchAll();
      }
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'cambios_iva',
        'WHERE' => [
          "id_cambio_iva" => $this->idCambio,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Valor del IVA no encontrado",
          "texto" => "El valor que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
      } else {
        $rol = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $rol;
    }
  }
  private function registrarCambiosIvaP() {
    $objBitacora = new bitacoraModelo();

    $ivaAntes = $this->seleccionarCambiosIva([
      'tipoConsulta' => 'ivaActual'
    ]);
    if (!$ivaAntes) {
      $ivaAntes = ['monto_cambio_iva' => 0];
    }

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'cambios_iva',
      'datos' => [
        "monto_cambio_iva" => $this->montoCambioIva,
        "fecha_cambio_iva" => $this->fechaHoraSel('fecha_hora_BD'),
      ]
    ]);

    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'cambiosIva',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'commit' => true,
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Valor no actualizado",
        "texto" => "El valor del IVA no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    $ivaDespues = $this->seleccionarCambiosIva([
      'tipoConsulta' => 'ivaActual'
    ]);
    $objBitacora->registrarBitacora([
      'modulo' => 'cambiosIva', 
      'accion' => 'registrar', 
      'resultado' => 'Éxito', 
      'viejo' => $ivaAntes,
      'nuevo' => $ivaDespues
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS(
      [
        "receptor" => [
          'tipo' => 'permisos',
          'pemisos' => ['cambiosIva' => 'ver']
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'cambiosIva'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'cambiosIva'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'IVA actualizado',
              'texto' => 'El IVA ha sido actualizado de ' . $ivaAntes['monto_cambio_iva'] . '% a ' . $ivaDespues['monto_cambio_iva'] . '%',
              'icono' => 'info',
              'notifier' => true,
            ]
          ],
        ],
        'noCommit' => true
      ],
    );

    $this->commit();

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Valor Actualizado",
      "texto" => "El valor del IVA ha sido actualizado de manera exitosa",
      "icono" => "success",
    ];
  }
}
