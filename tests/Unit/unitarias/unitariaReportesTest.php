<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\ReportesModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'id_servicio'                => 1,
            'nombre_servicio'            => 'Servicio Prueb',
            'precio_servicio'            => '100.00',
            'id_producto'                => 1,
            'nombre_producto'            => 'Producto Prueb',
            'precio_producto'            => '50.00',
            'stock_producto'             => 10,
            'nombre_categoria_producto'  => 'General',
            'id_materia_prima'           => 1,
            'nombre_materia_prima'       => 'Materia Prima Prueba',
            'stock_materia_prima'        => 25,
            'precio_materia_prima'       => '15.00',
            'simbolo_unidad_medida'      => 'kg',
            'id_acceso'                  => 1,
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'reportes',
            'id_permiso'                 => 1,
            'nombre_permiso'             => 'listar',
            'nombre_rol'                 => 'Administrador',
            'id_icono_notificacion'      => 1,
            'id_tipo_notificacion'       => 2,
            'id_notificacion'            => 1,
            'id_accion_resagada_usuario' => 1,
            'id_bitacora'                => 1,
            'id_rol'                     => 1,
            'cedula_usuario'             => 'V15265756',
            'status'                     => 1,
        ];

        public function offsetExists(mixed $offset): bool { return true; }
        public function offsetGet(mixed $offset): mixed { return $this->datos[$offset] ?? 1; }
        public function offsetSet(mixed $offset, mixed $value): void { $this->datos[$offset] = $value; }
        public function offsetUnset(mixed $offset): void { unset($this->datos[$offset]); }
    }
}

class unitariaReportesTest extends Unit
{
    protected function _before(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['cedula'] = 'V15265756';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->inyectarPdoFakeEnConexion();
    }

    protected function _after(): void
    {
        $ref = new ReflectionClass(conexion::class);
        foreach (['conexion', 'conexion2'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $prop = $ref->getProperty($propName);
                $prop->setAccessible(true);
                $prop->setValue(null, null);
            }
        }
    }

    private function inyectarPdoFakeEnConexion(): void
    {
        $filaUniversal = new FilaFakeBD();

        $stmtFake = $this->createMock(PDOStatement::class);
        $stmtFake->method('execute')->willReturn(true);
        $stmtFake->method('rowCount')->willReturn(1);
        $stmtFake->method('fetch')->willReturnCallback(
            fn($mode = PDO::FETCH_DEFAULT) => $mode === PDO::FETCH_COLUMN ? '1' : $filaUniversal
        );
        $stmtFake->method('fetchAll')->willReturnCallback(
            fn($mode = PDO::FETCH_DEFAULT) => $mode === PDO::FETCH_COLUMN ? ['V15265756'] : []
        );

        $pdoFake = $this->createMock(PDO::class);
        $pdoFake->method('prepare')->willReturn($stmtFake);
        $pdoFake->method('inTransaction')->willReturn(true);
        $pdoFake->method('commit')->willReturn(true);
        $pdoFake->method('rollBack')->willReturn(true);
        $pdoFake->method('lastInsertId')->willReturn('999');

        $ref = new ReflectionClass(conexion::class);
        foreach (['conexion', 'conexion2'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $prop = $ref->getProperty($propName);
                $prop->setAccessible(true);
                $prop->setValue(null, $pdoFake);
            }
        }
    }

    private function crearStmtStub(array $datosRetorno = [], int $rowCount = 0): PDOStatement
    {
        $filaUnica = isset($datosRetorno[0]) && is_array($datosRetorno[0]) ? $datosRetorno[0] : $datosRetorno;
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('rowCount')->willReturn($rowCount);
        $stmt->method('fetchAll')->willReturn($datosRetorno);
        $stmt->method('fetch')->willReturn($filaUnica);
        return $stmt;
    }

    public function testReporteVentasSinFechasRetornaError(): void
    {
        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [], $this);

        $resultado = $modelo->reporteVentas([]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Sin fecha específicada', $resultado['titulo']);
    }

    public function testReporteVentasFechaDesdeMayorQueHastaRetornaError(): void
    {
        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [], $this);

        $resultado = $modelo->reporteVentas([
            'fecha_desde' => '20/11/2026',
            'fecha_hasta' => '10/11/2026',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Fecha de inicio mayor a la fecha de fin', $resultado['titulo']);
    }

    public function testReporteVentasSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->reporteVentas([
            'fecha_desde' => '2026-11-01',
            'fecha_hasta' => '2026-11-19',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin registros existentes', $resultado['titulo']);
    }

    public function testReporteComprasSinFechasRetornaError(): void
    {
        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [], $this);

        $resultado = $modelo->reporteCompras([
            'fecha_desde' => '2026-11-01',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Sin fecha específicada', $resultado['titulo']);
    }

   public function testReporteComprasSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::exactly(2, $stmtVacio),
        ], $this);

        $resultado = $modelo->reporteCompras([
            'fecha_desde' => '2026-11-01',
            'fecha_hasta' => '2026-11-19',
            'tipo_item'   => 'todos',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin registros existentes', $resultado['titulo']);
    }

    public function testReporteCierreSinFechaRetornaError(): void
    {
        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [], $this);

        $resultado = $modelo->reporteCierre([]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Sin fecha específicada', $resultado['titulo']);
    }

    public function testReporteCierreSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->reporteCierre([
            'fecha_cierre' => '2026-11-19',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin registros existentes', $resultado['titulo']);
    }

    public function testReporteServiciosSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->reporteServicios();

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin registros existentes', $resultado['titulo']);
    }

    public function testReporteProductosSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->reporteProductos();

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin regisros existentes', $resultado['titulo']);
    }

    public function testReporteMateriaPrimaSinRegistrosRetornaWarning(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var reportesModelo $modelo */
        $modelo = Stub::make(reportesModelo::class, [
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->reporteMateriaPrima();

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin regisros existentes', $resultado['titulo']);
    }
}