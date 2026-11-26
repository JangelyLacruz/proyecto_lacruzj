<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\accesosModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'id_acceso'                  => 1,
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'clientes',
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

class unitariaAccesosTest extends Unit
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

    public function testValidarPermisosAutorizado(): void
    {
        $filasPermisos = [
            ['nombre_rol' => 'Admin', 'nombre_modulo' => 'clientes', 'nombre_permiso' => 'registrar'],
            ['nombre_rol' => 'Admin', 'nombre_modulo' => 'clientes', 'nombre_permiso' => 'listar'],
        ];
        $stmtStub = $this->crearStmtStub($filasPermisos, count($filasPermisos));

        /** @var accesosModelo $modelo */
        $modelo = Stub::make(accesosModelo::class, [
            'seleccionarPermisosPorRol' => [
                'clientes' => ['registrar', 'listar'],
            ],
            'seleccionarDatos2' => $stmtStub,
        ], $this);

        $resultado = $modelo->validarPermisos('clientes', 'registrar');

        $this->assertFalse($resultado);
    }

    public function testValidarPermisosNoAutorizado(): void
    {
        /** @var accesosModelo $modelo */
        $modelo = Stub::make(accesosModelo::class, [
            'seleccionarPermisosPorRol' => [
                'clientes' => ['listar'],
            ],
        ], $this);

        $resultado = $modelo->validarPermisos('clientes', 'eliminar');

        $this->assertIsArray($resultado);
        $this->assertSame('error', $resultado['icono']);
        $this->assertSame('Acción no autorizada', $resultado['titulo']);
    }

    public function testSeleccionarPermisosPorRolAgrupaPorModulo(): void
    {
        $filas = [
            ['nombre_rol' => 'Admin', 'nombre_modulo' => 'clientes', 'nombre_permiso' => 'listar'],
            ['nombre_rol' => 'Admin', 'nombre_modulo' => 'clientes', 'nombre_permiso' => 'registrar'],
            ['nombre_rol' => 'Admin', 'nombre_modulo' => 'productos', 'nombre_permiso' => 'ver'],
        ];
        $stmtStub = $this->crearStmtStub($filas, count($filas));

        /** @var accesosModelo $modelo */
        $modelo = Stub::make(accesosModelo::class, [
            'validarAccesos'    => Expected::once(false),
            'seleccionarDatos2' => Expected::once($stmtStub),
        ], $this);

        $permisos = $modelo->seleccionarPermisosPorRol(1);

        $this->assertArrayHasKey('clientes', $permisos);
        $this->assertSame(['listar', 'registrar'], $permisos['clientes']);
        $this->assertSame(['ver'], $permisos['productos']);
    }

    public function testActualizarPermisosExitoso(): void
    {
        /** @var accesosModelo $modelo */
        $modelo = Stub::make(accesosModelo::class, [
            'validarAccesos' => Expected::once(false),
            'VEYSNEC'        => Expected::once(['status' => 0, 'id_acceso' => 12]),
            'actualizarDatos2' => Expected::once(1),
            'commit'         => Expected::once(),
            'rollback'       => Expected::never(),
        ], $this);

        $resultado = $modelo->actualizarPermisos(1, 2, 3, 1);

        $this->assertSame('success', $resultado['icono']);
        $this->assertSame('Acceso actualizado', $resultado['titulo']);
    }

    public function testActualizarPermisosFallaHaceRollback(): void
    {
        /** @var accesosModelo $modelo */
        $modelo = Stub::make(accesosModelo::class, [
            'validarAccesos' => Expected::once(false),
            'VEYSNEC'        => Expected::once(['status' => 0, 'id_acceso' => 12]),
            'actualizarDatos2' => Expected::once(0),
            'rollback'       => Expected::once(),
            'commit'         => Expected::never(),
        ], $this);

        $resultado = $modelo->actualizarPermisos(1, 2, 3, 1);

        $this->assertSame('error', $resultado['icono']);
        $this->assertSame('Acceso no actualizado', $resultado['titulo']);
    }
}