<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\ReportesModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionReportesTest extends Unit
{
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

    public function testReporteVentasValidaFechasFaltantesYRangoInvalido(): void
    {
        $modelo = new reportesModelo();

        $sinFechas = $modelo->reporteVentas([]);
        $this->assertIsArray($sinFechas);
        $this->assertEquals('error', $sinFechas['icono'] ?? null);

        $rangoInvertido = $modelo->reporteVentas([
            'fecha_desde' => '2026-11-20',
            'fecha_hasta' => '2026-11-10',
        ]);
        $this->assertIsArray($rangoInvertido);
        $this->assertEquals('error', $rangoInvertido['icono'] ?? null);
    }

    public function testReporteVentasSinRegistrosEnRangoRetornaWarning(): void
    {
        $modelo = new reportesModelo();

        $resultado = $modelo->reporteVentas([
            'fecha_desde'   => '1900-01-01',
            'fecha_hasta'   => '1900-01-02',
            'tipo_producto' => 'todos',
        ]);

        $this->assertIsArray($resultado);
        $this->assertEquals('warning', $resultado['icono'] ?? null);
    }

    public function testReporteComprasValidaFechasYSinRegistrosRetornaWarning(): void
    {
        $modelo = new reportesModelo();

        $sinFechas = $modelo->reporteCompras([]);
        $this->assertIsArray($sinFechas);
        $this->assertEquals('error', $sinFechas['icono'] ?? null);

        $sinDatos = $modelo->reporteCompras([
            'fecha_desde' => '1900-01-01',
            'fecha_hasta' => '1900-01-02',
            'tipo_item'   => 'todos',
        ]);
        $this->assertIsArray($sinDatos);
        $this->assertEquals('warning', $sinDatos['icono'] ?? null);
    }

    public function testReporteCierreValidaFechaYSinRegistrosRetornaWarning(): void
    {
        $modelo = new reportesModelo();

        $sinFecha = $modelo->reporteCierre([]);
        $this->assertIsArray($sinFecha);
        $this->assertEquals('error', $sinFecha['icono'] ?? null);

        $sinDatos = $modelo->reporteCierre([
            'fecha_cierre' => '1900-01-01',
        ]);
        $this->assertIsArray($sinDatos);
        $this->assertEquals('warning', $sinDatos['icono'] ?? null);
    }
}