<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use Exception;

class exportarBDModelo extends conexion {
  private string $bases = '';
  public function exportar(string $BD) {

    $this->bases = $BD;
    if (!isset($_SESSION['cedula'])) {
      http_response_code(403);
      header('Content-Type: application/json');
      echo json_encode(['icono' => 'error', 'titulo' => 'Acceso denegado']);
      exit;
    }
    $rol = strtolower($_SESSION['rol'] ?? '');

    if ($rol != 1) {
      http_response_code(403);
      header('Content-Type: application/json');
      echo json_encode([
        'icono'  => 'error',
        'titulo' => 'Acceso denegado',
        'texto'  => 'No tienes permisos para exportar la base de datos.'
      ]);
      exit;
    }
    $timestamp = date('Y-m-d_H-i-s');
    $fileName = "backup_lacruz_{$timestamp}.sql";
    $filePath = __DIR__ . '/exports/' . $fileName;
    if (!is_dir(dirname($filePath))) {
      mkdir(dirname($filePath), 0755, true);
    }
    $dump = "-- ============================================\n";
    $dump .= "-- Backup: proyecto_lacruz + proyecto_lacruz_seguridad\n";
    $dump .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
    $dump .= "-- ============================================\n\n";
    $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    $bd = ($this->bases === 'proyecto_lacruz_seguridad') ? 'seguridad' : null;
    $pdo = $this->conectar($bd);
    $dump .= $this->volcarBase($pdo, $this->bases);
    file_put_contents($filePath, $dump);
    $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

    return $filePath;
  }
  private function volcarBase($pdo, string $database): string {
    $dump = "\n-- ============================================\n";
    $dump .= "-- BASE DE DATOS: {$database}\n";
    $dump .= "-- ============================================\n\n";
    $dump .= "CREATE DATABASE IF NOT EXISTS `{$database}`;\n";
    $dump .= "USE `{$database}`;\n\n";

    try {
      $pdo->exec("USE `{$database}`");
    } catch (Exception $e) {
      $dump .= "-- ERROR: No se pudo usar la BD {$database}: " . $e->getMessage() . "\n";
      return $dump;
    }

    try {
      $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
      $dump .= "-- ERROR al listar tablas: " . $e->getMessage() . "\n";
      return $dump;
    }

    foreach ($tablas as $tabla) {
      $dump .= $this->volcarTabla($pdo, $tabla);
    }

    return $dump;
  }
  private function volcarTabla($pdo, string $tabla): string {
    $dump = "-- Tabla: {$tabla}\n";
    $dump .= "DROP TABLE IF EXISTS `{$tabla}`;\n";

    try {
      $stmt = $pdo->query("SHOW CREATE TABLE `{$tabla}`");
      $create = $stmt->fetch(PDO::FETCH_ASSOC);
      $dump .= $create['Create Table'] . ";\n\n";
    } catch (Exception $e) {
      $dump .= "-- ERROR CREATE TABLE: " . $e->getMessage() . "\n\n";
      return $dump;
    }

    try {
      $filas = $pdo->query("SELECT * FROM `{$tabla}`");
    } catch (Exception $e) {
      $dump .= "-- ERROR SELECT: " . $e->getMessage() . "\n\n";
      return $dump;
    }

    if ($filas->rowCount() > 0) {
      $dump .= "LOCK TABLES `{$tabla}` WRITE;\n";

      while ($fila = $filas->fetch(PDO::FETCH_ASSOC)) {
        $valores = array_map(function ($v) {
          if (is_null($v)) return "NULL";
          return "'" . addslashes($v) . "'";
        }, $fila);
        $dump .= "INSERT INTO `{$tabla}` VALUES (" . implode(", ", $valores) . ");\n";
      }

      $dump .= "UNLOCK TABLES;\n\n";
    }

    return $dump;
  }
}
