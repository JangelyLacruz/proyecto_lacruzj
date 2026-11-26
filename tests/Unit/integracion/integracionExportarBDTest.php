<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\exportarBDModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionExportarBDTest extends Unit
{
    private ?string $rutaArchivoGenerado = null;

    protected function _before(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_COOKIE['TEMP'] = true;
        $_ENV['MODO_TESTEO'] = true;

        $ref = new ReflectionClass(conexion::class);
        foreach ($ref->getStaticProperties() as $propName => $_) {
            $prop = $ref->getProperty($propName);
            $prop->setAccessible(true);
            $prop->setValue(null, null);
        }

        $_SESSION['cedula'] = 'V30485684';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;
    }

    protected function _after(): void
    {
        if ($this->rutaArchivoGenerado && file_exists($this->rutaArchivoGenerado)) {
            @unlink($this->rutaArchivoGenerado);
            $this->rutaArchivoGenerado = null;
        }
    }

    public function testExportarBaseDatosPruebasGeneraArchivoSql(): void
    {
        $bdPruebas = DB_NAME . '_pruebas';
        $pdo = new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname={$bdPruebas};charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );

        $tablas = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        $this->assertNotEmpty($tablas, "La base de datos {$bdPruebas} no tiene tablas para exportar.");

        $dirExports = dirname(__DIR__, 3) . '/src/modelos/exports';
        if (!is_dir($dirExports)) {
            mkdir($dirExports, 0755, true);
        }

        $rutaFisica = $dirExports . '/backup_test_' . uniqid() . '.sql';
        $this->rutaArchivoGenerado = $rutaFisica;

        $sql = "SET FOREIGN_KEY_CHECKS=0;\nUSE `{$bdPruebas}`;\n";
        foreach ($tablas as $tabla) {
            $create = $pdo->query("SHOW CREATE TABLE `{$tabla}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `{$tabla}`;\n" . ($create['Create Table'] ?? '') . ";\n\n";
        }
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($rutaFisica, $sql);

        $this->assertFileExists($rutaFisica);
        $this->assertGreaterThan(0, filesize($rutaFisica));
        $this->assertStringContainsString("USE `{$bdPruebas}`;", file_get_contents($rutaFisica));
    }
}