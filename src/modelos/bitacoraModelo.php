<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class bitacoraModelo extends conexion {
  private string $moduloBitacora = '';
  private string $accionBitacora = '';
  private string $resultadoBitacora = '';
  private string $ipDispositivo = '';
  private ?array $cambiosEfectuados = null;
  private int $commit = 0;

  public function seleccionarBitacora() {
    return $this->seleccionarBitacoraP();
  }
  public function registrarBitacora(array $instrucciones) {

    $this->moduloBitacora = $instrucciones['modulo'];
    $this->accionBitacora = $instrucciones['accion'];
    $this->resultadoBitacora = $instrucciones['resultado'];
    $this->commit = $instrucciones['commit'] ?? false;
    $nuevo = $instrucciones['nuevo'] ?? [];
    $viejo = $instrucciones['viejo'] ?? [];
    $this->ipDispositivo = $_SERVER['HTTP_X_IP_DISPOSITIVO'] ?? '0.0.0.0';

    $this->cambiosEfectuados = [];
    if ($viejo != [] || $nuevo != []) {
      $this->cambiosEfectuados = $this->sacarDiferenciaBitacora($nuevo, $viejo);
    }

    $respuesta = $this->limpiar_Verificar([
      [
        "campo_valor" => $this->moduloBitacora,
        "formulario_nombre" => "nombre del modulo",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => $this->accionBitacora,
        "formulario_nombre" => "nombre de la accion",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => $this->resultadoBitacora,
        "formulario_nombre" => "resultado",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => $this->ipDispositivo,
        "formulario_nombre" => "IP del dispositivo",
        "requerido" => false,
        "minimo" => 5,
        "maximo" => 25,
      ]
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarBitacoraP();
  }
  private function seleccionarBitacoraP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        b.id_bitacora, 
        u.nombre_usuario, 
        u.apellido_usuario,
        m.nombre_modulo as modulo_bitacora,
        b.accion, 
        b.fecha_bitacora, 
        b.resultado_bitacora, 
        b.ip_dispositivo, 
        b.cambios_efectuados
      ',
      'tabla' => 'bitacora as b',
      'BD' => 'seguridad',
      'datosJoins' => [
        "usuarios as u" => "b.cedula_usuario = u.cedula_usuario",
        "modulos as m" => "b.id_modulo = m.id_modulo"
      ],
      'ORDER' => 'b.id_bitacora DESC'
    ]);

    $Bitacora = $resultado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($Bitacora as &$item) {
      if (!empty($item['cambios_efectuados'])) {
        $item['cambios_efectuados'] = json_decode($item['cambios_efectuados'], true);
      }
    }
    unset($item);

    return $Bitacora;
  }
  private function registrarBitacoraP() {
    $idModulo = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_modulo' => $this->moduloBitacora
      ]
    ]);

    $datosBitacora = [
      "cedula_usuario" => $_SESSION['cedula'],
      "id_modulo" => $idModulo,
      "resultado_bitacora" => $this->resultadoBitacora,
      "accion" => $this->accionBitacora,
      "ip_dispositivo" => $this->ipDispositivo,
      "fecha_bitacora" => $this->fechaHoraSel('fecha_hora_BD'),
    ];

    if ($this->cambiosEfectuados !== null && !empty($this->cambiosEfectuados)) {
      $datosBitacora["cambios_efectuados"] = json_encode($this->cambiosEfectuados, JSON_UNESCAPED_UNICODE);
    }

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'bitacora',
      'BD' => 'seguridad',
      'datos' => $datosBitacora,
    ]);

    if ($ultimoId !== false && $ultimoId > 0) {
      if ($this->commit) $this->commit();
      return false;
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Bitacora no registrada",
        "texto" => "La bitacora no se registro correctamente",
        "icono" => "error"
      ];
      return $alerta;
    }
  }
}
