<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\ModulosModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'clientes',
            'id_acceso'                  => 1,
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

class unitariaModulosTest extends Unit
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

    public function testSeleccionarTodosLosModulos(): void
    {
        $modulosFake = [
            ['id_modulo' => 1, 'nombre_modulo' => 'clientes'],
            ['id_modulo' => 2, 'nombre_modulo' => 'productos'],
        ];
        $stmtStub = $this->crearStmtStub($modulosFake, 2);

        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos'    => Expected::once(false),
            'seleccionarDatos2' => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarModulos([]);

        $this->assertCount(2, $resultado);
        $this->assertEquals('clientes', $resultado[0]['nombre_modulo']);
    }

    public function testSeleccionarModuloPorIdNoEncontrado(): void
    {
        $stmtVacio = $this->crearStmtStub([], 0);

        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos'    => Expected::once(false),
            'seleccionarDatos2' => Expected::once($stmtVacio),
        ], $this);

        $resultado = $modelo->seleccionarModulos(['id_modulo' => 999]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Módulo no encontrado', $resultado['titulo']);
    }

    public function testRegistrarModuloExitoso(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos' => Expected::once(false),
            'guardarDatos2'  => Expected::once(10),
        ], $this);

        $resultado = $modelo->registrarModulos([
            'nombre_modulo' => 'proveedores',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Módulo registrado', $resultado['titulo']);
        $this->assertEquals('limpiarYcerrar', $resultado['tipo']);
    }

    public function testRegistrarModuloFallaEnGuardado(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos' => Expected::once(false),
            'guardarDatos2'  => Expected::once(0),
        ], $this);

        $resultado = $modelo->registrarModulos([
            'nombre_modulo' => 'modulo_error',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Módulo no registrado', $resultado['titulo']);
    }

    public function testActualizarModuloSinCambios(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos'    => Expected::once(false),
            'seleccionarModulos' => ['id_modulo' => 3, 'nombre_modulo' => 'clientes'],
            'actualizarDatos2'  => Expected::once(0),
        ], $this);

        $resultado = $modelo->actualizarModulos([
            'id_modulo'     => 3,
            'nombre_modulo' => 'clientes',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin cambios realizados', $resultado['titulo']);
    }

    public function testActualizarModuloExitoso(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos'    => Expected::once(false),
            'seleccionarModulos' => ['id_modulo' => 3, 'nombre_modulo' => 'clientes_viejo'],
            'actualizarDatos2'  => Expected::once(1),
            'commit'           => Expected::once(),
        ], $this);

        $resultado = $modelo->actualizarModulos([
            'id_modulo'     => 3,
            'nombre_modulo' => 'clientes_nuevo',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Módulo actualizado', $resultado['titulo']);
    }

    public function testEliminarModuloExitoso(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos' => Expected::once(false),
            'eliminarDatos2' => Expected::once(1),
        ], $this);

        $resultado = $modelo->eliminarModulos([
            'id_modulo' => 5,
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Módulo eliminado', $resultado['titulo']);
    }

    public function testEliminarModuloNoEncontrado(): void
    {
        /** @var modulosModelo $modelo */
        $modelo = Stub::make(modulosModelo::class, [
            'validarModulos' => Expected::once(false),
            'eliminarDatos2' => Expected::once(0),
        ], $this);

        $resultado = $modelo->eliminarModulos([
            'id_modulo' => 999,
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Módulo no encontrado', $resultado['titulo']);
    }
}