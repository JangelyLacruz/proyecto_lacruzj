<?php

namespace src\config\connect;

use src\modelos\traitModelo;
use PDO;
use PDOException;
use Exception;
use Throwable;
use src\modelos\cacheModelo;

class conexion {
  use traitModelo;

  private string $servidorDB = DB_SERVER;
  private string $nombreDB = DB_NAME;
  private string $usuarioDB = DB_USER;
  private string $contrasenaDB = DB_PASS;
  protected static ?PDO $conexion = null;
  protected static ?PDO $conexion2 = null;

  public function __destruct() {
    if (self::$conexion instanceof PDO && !self::$conexion->inTransaction()) {
      self::$conexion = null;
    }
    if (self::$conexion2 instanceof PDO && !self::$conexion2->inTransaction()) {
      self::$conexion2 = null;
    }
  }
  protected function conectar($BD = null) {
    return $this->conectarP($BD);
  }
  protected function commit() {
    return $this->commitP();
  }
  protected function rollback() {
    return $this->rollbackP();
  }
  protected function seleccionarDatos2(array $instrucciones) {
    return $this->seleccionarDatosP2($instrucciones);
  }
  protected function guardarDatos2(array $instrucciones) {
    return $this->guardarDatosP2($instrucciones);
  }
  protected function actualizarDatos2(array $instrucciones) {
    return $this->actualizarDatosP2($instrucciones);
  }
  protected function eliminarDatos2(array $instrucciones) {
    return $this->eliminarDatosP2($instrucciones);
  }
  protected function procesosAlmacenados(array $instrucciones) {
    return $this->procesosAlmacenadosP($instrucciones);
  }

  private function conectarP($BD = null) {
    $esPrueba = isset($_COOKIE['TEMP']) || isset($_ENV['MODO_TESTEO']);
    if ($BD == 'seguridad') {
      $this->nombreDB = $esPrueba ? DB_NAME . '_seguridad_pruebas' : DB_NAME . '_seguridad';
      $conexionElegida = self::$conexion2;
    } else {
      $this->nombreDB = $esPrueba ? DB_NAME . '_pruebas' : DB_NAME;
      $conexionElegida = self::$conexion;
    }

    if ($conexionElegida instanceof PDO) {
        return $conexionElegida;
    }

    try {
      $conexionElegida = new PDO(
        "mysql:host=" . $this->servidorDB . ";dbname=" . $this->nombreDB,
        $this->usuarioDB,
        $this->contrasenaDB,
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
      $conexionElegida->exec("SET CHARACTER SET utf8");
    } catch (PDOException $e) {
      $_SESSION['codigoRequest'] = 500;
      throw new PDOException(
        "Error al establecer la conexión con la base de datos (" . $this->nombreDB . "): " . $e->getMessage(), 
        (int)$e->getCode()
      );
    }
    
    if (!$conexionElegida->inTransaction()) {
      try {
        $conexionElegida->beginTransaction();
      } catch (PDOException $e) {
        throw new PDOException("Error al iniciar la transacción: " . $e->getMessage(),(int)$e->getCode());
      }
    }

    if ($BD == 'seguridad') {
        self::$conexion2 = $conexionElegida;
    } else {
        self::$conexion = $conexionElegida;
    }

    return $conexionElegida;
  }
  
  private function commitP() {
    if (
      self::$conexion instanceof PDO &&
      self::$conexion->inTransaction()
    ) {
      try {
        self::$conexion->commit();
      } catch (PDOException $e) {
        throw new PDOException(
          "Error al confirmar la transacción (COMMIT): " . $e->getMessage(), (int)$e->getCode());
      }
    }

    if (
      self::$conexion2 instanceof PDO &&
      self::$conexion2->inTransaction()
    ) {
      try {
        self::$conexion2->commit();
      } catch (PDOException $e) {
        throw new PDOException("Error al confirmar la transacción (COMMIT): " . $e->getMessage(),
        (int)$e->getCode());
      }
    }
  }
  
  private function rollbackP() {
    if (self::$conexion instanceof PDO && self::$conexion->inTransaction()) {
      try {
        self::$conexion->rollBack();
      } catch (PDOException $e) {
        throw new PDOException("Error al revertir la transacción (ROLLBACK): " . $e->getMessage(), (int)$e->getCode());
      }
    }
    if (self::$conexion2 instanceof PDO && self::$conexion2->inTransaction()) {
      try {
        self::$conexion2->rollBack();
      } catch (PDOException $e) {
        throw new PDOException("Error al revertir la transacción (ROLLBACK): " . $e->getMessage(), (int)$e->getCode());
      }
    }
  }
  
  private function seleccionarDatosP2(array $instrucciones) {
    // $objCache = new cacheModelo();
    try {
      $tabla = trim($instrucciones['tabla']);
      /* $cacheTabla = $objCache->getItem($tabla) ?? [];
      $claveCacheInd = $instrucciones;
      unset($claveCacheInd['fnDevolver']);
      $cache = $cacheTabla[json_encode($claveCacheInd)] ?? false;
      if ($cache) return $cache; */

      $conexion = $this->conectar(($instrucciones['BD'] ?? NULL));
      $campos = $instrucciones['campos'];

      $datosJoins = $instrucciones['datosJoins'] ?? false;
      $registrosEli = $instrucciones['registrosEli'] ?? false;
      $eliminadosYVigentes = $instrucciones['eliminadosYVigentes'] ?? false;
      $WHERE = $instrucciones['WHERE'] ?? false;
      $GROUP = $instrucciones['GROUP'] ?? false;
      $HAVING = $instrucciones['HAVING'] ?? false;
      $ORDER = $instrucciones['ORDER'] ?? false;
      $LIMIT = $instrucciones['LIMIT'] ?? false;
      $FOR_UPDATE = $instrucciones['FOR_UPDATE'] ?? false;
      $patronAS_captura = '/\b(as|AS)\b\s*(.*)/i';
      $PEL = '';
      $marcadoresWHERE = [];
      $marcadoresHAVING = [];

      if (preg_match($patronAS_captura, $tabla, $matches)) {
        $PEL = trim($matches[2]) . '.';
      }
      //LOS CAMPOS Y LA TABLA
      $consulta = 'SELECT ' . $campos . ' FROM ' . $tabla . ' ';

      //INNER JOINS        
      if ($datosJoins) {
        foreach ($datosJoins as $tablaDestino => &$conexionLo) {
          $patron = '/^\b(LEFT|RIGHT|FULL|OUTER|CROSS|NATURAL)\b/i';
          $tipoJoin = 'INNER';
          if (preg_match($patron, $tablaDestino, $matches)) {
            $tipoJoin = $matches[0] ?? 'INNER';
            $palabras = ['INNER', 'LEFT', "RIGHT", "FULL", "OUTER", "CROSS", "NATURAL"];
            foreach ($palabras as $palabra) {
              $tablaDestino = str_ireplace($palabra, "", $tablaDestino);
            }
          }
          $consulta .= " " . $tipoJoin . " JOIN " . $tablaDestino . " ON " . $conexionLo;
        }
      }

      //CONDICIÓN DE ELIMINADO LÓGICO
      $consulta .= ' WHERE ' . $PEL . 'status ';
      if ($registrosEli) {
        $consulta .= '= 0 ';
      } elseif ($eliminadosYVigentes == true) {
        $consulta .= '> -1 ';
      } else {
        $consulta .= '!= 0 ';
      }

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);

        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };

      if ($WHERE) {
        $marcadoresWHERE = [];
        foreach ($WHERE as $claveW1 => &$valorW1) {
          if (!is_array($valorW1)) {
            $patron_operador = '/^(<=|>=|<>|<|>|!==|===|!=)/';
            $operadorLo = '=';
            if (preg_match($patron_operador, $valorW1, $matches)) {
              $operadorLo = $matches[0];
              $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
              foreach ($palabras as $palabra) {
                $valorW1 = str_ireplace($palabra, "", $valorW1);
              }
            }

            $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
            $consulta .= ' AND ' . $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
          } else {
            foreach ($valorW1 as $operadorW1 => $valoresW1) {
              if (!is_array($valoresW1)) {
                $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                $consulta .= ' AND ' . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
              } else {
                $CV = 0;
                $operadorW1 = trim($operadorW1);
                foreach ($valoresW1 as $v) {
                  if ($operadorW1 == '=' && $CV > 0) {
                    $UNION = ' OR ';
                  } else {
                    $UNION = ' AND ';
                  }
                  $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                  $consulta .= $UNION . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
                  $CV++;
                }
              }
            }
          }
        }
        unset($valorW1);
      }
      if ($HAVING) {
        $marcadoresHAVING = [];
        $consulta .= ' HAVING ';
        $ch = 0;
        foreach ($HAVING as $claveH1 => &$valorH1) {
          if ($ch > 0) {
            $consulta .= ' AND ';
          }

          if (!is_array($valorH1)) {
            $patron_operador = '/^(<=|>=|<>|<|>|!=)/';
            $operadorLo = '=';
            if (preg_match($patron_operador, $valorH1, $matches)) {
              $operadorLo = $matches[0];
              $palabras = ["<=", ">=", "<>", "<", ">", "!="];
              foreach ($palabras as $palabra) {
                $valorH1 = str_ireplace($palabra, "", $valorH1);
              }
            }
            $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
            $consulta .= $claveH1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
          } else {
            foreach ($valorH1 as $operadorH1 => $valoresH1) {
              if (is_array($valoresH1)) {
                foreach ($valoresH1 as $valor) {
                  $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
                  if (is_array($claveH1) || is_array($operadorH1) || is_array($marcador)) {
                    return ['aqui10'];
                  }
                  $consulta .= ' AND ' . $claveH1 . ' ' . $operadorH1 . ' ' . $marcador . ' ';
                }
              } else {
                $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
                $consulta .= ' AND ' . $claveH1 . ' ' . $operadorH1 . ' ' . $marcador . ' ';
              }
            }
          }
          $ch++;
        }
        unset($valorH1);
      }
      if ($GROUP) {
        $consulta .= " GROUP BY " . $GROUP;
      }
      if ($ORDER) {
        $consulta .= " ORDER BY " . $ORDER;
      }
      if ($LIMIT) {
        $consulta .= " LIMIT " . $LIMIT;
      }
      if ($FOR_UPDATE) {
        if (!is_array($FOR_UPDATE)) {
          $FOR_UPDATE = [$FOR_UPDATE];
        }
        for ($i = 0; $i < count($FOR_UPDATE); $i++) {
          if ($i == 0) {
            if ($datosJoins) {
              $consulta .= " FOR UPDATE OF " . $FOR_UPDATE[$i];
            } else {
              $consulta .= " FOR UPDATE";
            }
          } else {
            $consulta .= ", " . $FOR_UPDATE[$i];
          }
        };
      }

      $consulta = preg_replace('/\s+/', ' ', $consulta);
      $consulta = $conexion->prepare($consulta);
      if ($WHERE) {
        foreach ($WHERE as $claveW2 => $valorW2) {
          $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
          if (!is_array($valorW2)) {
            $marcador = array_shift($marcadoresWHERE[$claveW2]);
            $consulta->bindValue($marcador, $valorW2);
          } else {
            foreach ($valorW2 as $operadorW2 => $valoresW2) {
              if (is_array($valoresW2)) {
                foreach ($valoresW2 as $valorInd) {
                  $marcador = array_shift($marcadoresWHERE[$claveW2]);
                  $consulta->bindValue($marcador, $valorInd);
                }
              } else {
                $marcador = array_shift($marcadoresWHERE[$claveW2]);
                $consulta->bindValue($marcador, $valoresW2);
              }
            }
          }
        }
      }
      if ($HAVING) {
        foreach ($HAVING as $claveH2 => $valorH2) {
          if (!is_array($valorH2)) {
            $claveH2 = $this->limpiarCadena($claveH2, 'antiFuncionesSQL');
            $marcador = array_shift($marcadoresHAVING[$claveH2]);
            $consulta->bindValue($marcador, $valorH2);
          } else {
            foreach ($valorH2 as $operadorH2 => $valoresH2) {
              if (is_array($valoresH2)) {
                foreach ($valoresH2 as $valorInd) {
                  $marcador = array_shift($marcadoresHAVING[$claveH2]);
                  $consulta->bindValue($marcador, $valorInd);
                }
              } else {
                $marcador = array_shift($marcadoresHAVING[$claveH2]);
                $consulta->bindValue($marcador, $valoresH2);
              }
            }
          }
        }
      }
      $consulta->execute();
      return $consulta;
    } catch (\Throwable $th) {
      $this->rollback();
      // $objCache->removeItem($instrucciones['tabla']);
      if ($this->imgTrans != []) {
        $this->Imagenes_Eli2($this->imgTrans['subCarpeta'], $this->imgTrans['imagenes']);
      }
      $error = [
        'titulo' => 'Error en la selección',
        'linea' => (int)$th->getLine(),
        'código de error' => (int)$th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones,
        'consulta' => $consulta ?? 'Sin consulta',
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  
  private function guardarDatosP2(array $instrucciones) {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'datos' => $datos,
      ] = $instrucciones;
      $WHERE =  $instrucciones['WHERE'] ?? $instrucciones['camposReciclado'] ?? false;
      if (empty($datos) || !is_array($datos)) {
        throw new \Exception("El parámetro 'datos' está vacío o no es un array en guardarDatosP.");
      }
      if (empty($tabla) || $tabla == '') {
        throw new \Exception("El parámetro 'tabla' está vacío dentro del metodo de guardarDatosP");
      }

      $esActualizacion = false;
      if ($WHERE != false) {
        $instruccionesBD = [
          'campos' => '*',
          'registrosEli' => true,
          'tabla' => $tabla,
          'BD' => ($instrucciones['BD'] ?? NULL),
          'WHERE' => $WHERE
        ];

        $resultado = $this->seleccionarDatosP2($instruccionesBD);
        if ($resultado->rowCount() > 0) {
          $esActualizacion = true;
        }
      }
      if ($esActualizacion) {
        $instrucciones['reciclaje'] = true;
        return $this->actualizarDatos2($instrucciones);
      } else {
        $query = "INSERT INTO $tabla ";

        $C = 0;
        $stringCampos = '';
        $stringMarcadores = '';

        foreach ($datos as $clave => $valor) {
          if ($C >= 1) {
            $stringCampos .= ', ';
            $stringMarcadores .= ', ';
          }
          $stringCampos .= $clave;
          $marcador = ':' . str_ireplace('.', "_", $clave);
          $stringMarcadores .= $marcador;
          $C++;
        }
        $query .= '(' . $stringCampos . ') VALUES (' . $stringMarcadores . ')';
        $sql = $conexion->prepare($query);

        foreach ($datos as $clave => $valor) {
          $marcador = ':' . str_ireplace('.', "_", $clave);
          $sql->bindValue($marcador, $valor);
        }
        $sql->execute();
        $nroColumnasAfetadas = 0;
        if ($conexion->lastInsertId() > 0) {
          $nroColumnasAfetadas = $conexion->lastInsertId();
        } else {
          $nroColumnasAfetadas = $sql->rowCount();
        }

        if ($nroColumnasAfetadas >= 1) {
          $objCache = new cacheModelo();
          $objCache->removeItem($tabla);
        }
        return $nroColumnasAfetadas;
      }
    } catch (\Throwable $th) {
      $this->rollback();
      if ($this->imgTrans != []) {
        $this->Imagenes_Eli2($this->imgTrans['subCarpeta'], $this->imgTrans['imagenes']);
      }
      $error = [
        'titulo' => 'Error en la inserción',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones,
        'imagenesTransaccion' => $this->imgTrans
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  
  private function actualizarDatosP2(array $instrucciones) {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'datos' => $datos,
      ] = $instrucciones;
      $WHERE = $instrucciones['camposReciclado'] ?? $instrucciones['WHERE'];
      $reciclaje = $instrucciones['reciclaje'] ?? false;

      //comenzamos la consulta SQL
      $consulta = "UPDATE $tabla SET ";

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);
        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };
      $marcadoresConsulta = [];

      $C = 0;
      foreach ($datos as $campoClave => $campoValor) {
        if ($C >= 1) {
          $consulta .= ",";
        }
        $marcador = $guardarMarcador($marcadoresConsulta, $campoClave);
        $consulta .= $campoClave . " = " . $marcador;
        $C++;
      }

      if ($reciclaje) {
        $consulta .= ', status = 1';
      }
      $consulta .= " WHERE ";

      $c2 = 0;
      foreach ($WHERE as $claveW1 => &$valorW1) {
        if ($c2 != 0) {
          $consulta .= ' AND ';
        }
        if (!is_array($valorW1)) {
          $patron_operador = '/^(<=|>=|<>|<|>|!==|===|!=)/';
          $operadorLo = '=';
          if (preg_match($patron_operador, $valorW1, $matches)) {
            $operadorLo = $matches[0];
            $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
            foreach ($palabras as $palabra) {
              $valorW1 = str_ireplace($palabra, "", $valorW1);
            }
          }
          $marcador = $guardarMarcador($marcadoresConsulta, $claveW1);
          $consulta .= $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
        } else {
          foreach ($valorW1 as $operadorW1 => $valoresW1) {
            if (!is_array($valoresW1)) {
              $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
              $consulta .= ' AND ' . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
            } else {
              $CV = 0;
              $operadorW1 = trim($operadorW1);
              foreach ($valoresW1 as $v) {
                if ($operadorW1 == '=' && $CV > 0) {
                  $UNION = ' OR ';
                } else {
                  $UNION = ' AND ';
                }
                $marcador = $guardarMarcador($marcadoresConsulta, $claveW1);
                $consulta .= $UNION . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
                $CV++;
              }
            }
          }
        }
        $c2++;
      }
      unset($valorW1);
      $sql = $conexion->prepare($consulta);
      foreach ($datos as $campoClave2 => $valorCampo2) {
        $marcador = array_shift($marcadoresConsulta[$campoClave2]);
        $sql->bindValue($marcador, $valorCampo2);
      }
      foreach ($WHERE as $claveW2 => $valorW2) {
        $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
        if (!is_array($valorW2)) {
          $marcador = array_shift($marcadoresConsulta[$claveW2]);
          $sql->bindValue($marcador, $valorW2);
        } else {
          foreach ($valorW2 as $operadorW2 => $valoresW2) {
            if (is_array($valoresW2)) {
              foreach ($valoresW2 as $valorInd) {
                $marcador = array_shift($marcadoresConsulta[$claveW2]);
                $sql->bindValue($marcador, $valorInd);
              }
            } else {
              $marcador = array_shift($marcadoresConsulta[$claveW2]);
              $sql->bindValue($marcador, $valoresW2);
            }
          }
        }
      }
      $sql->execute();
      $objCache = new cacheModelo();
      $objCache->removeItem($tabla);
      return $sql->rowCount();
    } catch (\Throwable $th) {
      $this->rollback();
      if ($this->imgTrans != []) {
        $this->Imagenes_Eli2($this->imgTrans['subCarpeta'], $this->imgTrans['imagenes']);
      }
      $error = [
        'titulo' => 'Error en la actualización',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'consulta' => $consulta ?? 'SIN CONSULTA',
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones,
        'imagenesTransaccion' => $this->imgTrans
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  
  private function eliminarDatosP2(array $instrucciones) {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'WHERE' => $WHERE,
      ] = $instrucciones;
      $permanentemente = $instrucciones['fisico'] ?? false;

      $consulta = '';
      if ($permanentemente == true) {
        $consulta .= "DELETE FROM $tabla";
      } else {
        $consulta .= "UPDATE $tabla SET status = 0";
      }
      $consulta .= " WHERE ";

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);

        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };

      $marcadoresWHERE = [];
      $c = 0;
      foreach ($WHERE as $claveW1 => &$valorW1) {
        if ($c > 0) {
          $consulta .= ' AND ';
        }

        if (!is_array($valorW1)) {
          $patron_operador = '/\b(<=|>=|<>|<|>|=|!==|===|!=|==)\b/';
          $operadorLo = '=';
          if (preg_match($patron_operador, $valorW1, $matches)) {
            $operadorLo = $matches[0];
            $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
            foreach ($palabras as $palabra) {
              $valorW1 = str_ireplace($palabra, "", $valorW1);
            }
          }

          $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
          $consulta .= $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
        } else {
          foreach ($valorW1 as $operadorW1 => $valoresW1) {
            if (!is_array($valoresW1)) {
              $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
              $consulta .= $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
            } else {
              // $operadoresQueUsanORPorElMedio=['='];
              $operadoresQueUsanANDPorElMedio = ['!='];

              foreach ($valoresW1 as $cv => $v) {
                if ($cv > 0) {
                  $opMedio = in_array($operadorW1, $operadoresQueUsanANDPorElMedio) ? 'AND' : 'OR';
                  $consulta .= $opMedio . ' ';
                }
                $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                $consulta .= $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
              }
            }
          }
        }
        $c++;
      }
      unset($valorW1);

      $consulta = preg_replace('/\s+/', ' ', $consulta);
      $consulta = $conexion->prepare($consulta);

      foreach ($WHERE as $claveW2 => $valorW2) {
        $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
        if (!is_array($valorW2)) {
          $marcador = array_shift($marcadoresWHERE[$claveW2]);
          $consulta->bindValue($marcador, $valorW2);
        } else {
          foreach ($valorW2 as $operadorW2 => $valoresW2) {
            if (is_array($valoresW2)) {
              foreach ($valoresW2 as $valorInd) {
                $marcador = array_shift($marcadoresWHERE[$claveW2]);
                $consulta->bindValue($marcador, $valorInd);
              }
            } else {
              $marcador = array_shift($marcadoresWHERE[$claveW2]);
              $consulta->bindValue($marcador, $valoresW2);
            }
          }
        }
      }
      $consulta->execute();
      if ($consulta->rowCount() >= 1) {
        $objCache = new cacheModelo();
        $objCache->removeItem($tabla);
      }
      return $consulta->rowCount();
    } catch (\Throwable $th) {
      $this->rollback();
      if ($this->imgTrans != []) {
        $this->Imagenes_Eli2($this->imgTrans['subCarpeta'], $this->imgTrans['imagenes']);
      }
      $error = [
        'consulta' => $consulta ?? 'Sin consulta',
        'titulo' => 'Error en la eliminación',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones,
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  
  private function procesosAlmacenadosP(array $instrucciones) {
    $conexion = $this->conectar();
    $sql = 'CALL ' . $instrucciones['sp'] . '(';
    $c = 0;
    foreach ($instrucciones['parametros'] ?? [] as $p => $v) {
      $sql .= ':' . $p;
      $c++;
      if ($c < count($instrucciones['parametros'])) $sql .= ',';
    }
    $sql .= ')';
    $consulta = $conexion->prepare($sql);

    $parametrosExec = [];
    foreach ($instrucciones['parametros'] ?? [] as $p => $v) {
      $parametrosExec[':' . $p] = $v;
    }
    return [
      'exito' => $consulta->execute($parametrosExec),
      'retorno' => $consulta->fetchAll()
    ];
  }
}
class errorBD extends Exception {
  protected array $detalles;
  public function __construct(string $mensaje, array $detalles, string $codigo, Throwable $anterior) {
    parent::__construct($mensaje, $codigo, $anterior);
    $this->detalles = $detalles;
  }
  public function getDetalles() {
    return $this->detalles;
  }
}