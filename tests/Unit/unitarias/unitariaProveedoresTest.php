<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\ProveedoresModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'rif_proveedor'              => 'J12345678',
            'razon_social_proveedor'     => 'Proveedor Demo C.A.',
            'telefono_proveedor'         => '04141234567',
            'correo_proveedor'           => 'proveedor@ejemplo.com',
            'direccion_proveedor'        => 'Av. Principal',
            'nro_control_factura'        => '00-123456',
            'id_acceso'                  => 1,
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'proveedores',
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

class unitariaProveedoresTest extends Unit
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

    public function testRegistrarProveedorExitosoConNumeroControl(): void
    {
        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'guardarDatos2'      => Expected::exactly(2, 10),
            'commit'             => Expected::once(),
        ], $this);

        $resultado = $modelo->registrarProveedores([
            'codigo_rif_proveedor'     => 'J',
            'rif_proveedor'            => '12345678',
            'razon_social_proveedor'   => 'Suministros Prueba C.A.',
            'nro_control_factura'      => '00-001234',
            'prefijo_telefono_proveedor' => '0414',
            'telefono_proveedor'       => '1234567',
            'correo_proveedor'         => 'suministros@ejemplo.com',
            'direccion_proveedor'      => 'Av. Principal',
            'vieneDelModuloUsuarios'   => true,
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Proveedor registrado', $resultado['titulo']);
        $this->assertEquals('limpiarYcerrar', $resultado['tipo']);
    }

    public function testRegistrarProveedorExitosoSinNumeroControl(): void
    {
        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'guardarDatos2'      => Expected::once(5),
            'commit'             => Expected::once(),
        ], $this);

        $resultado = $modelo->registrarProveedores([
            'rif_proveedor'          => 'V11223344',
            'razon_social_proveedor' => 'Proveedor Sin Control',
            'telefono_proveedor'     => '04241112233',
            'correo_proveedor'       => 'sincontrol@ejemplo.com',
            'direccion_proveedor'    => 'Centro',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Proveedor registrado', $resultado['titulo']);
    }

    public function testSeleccionarTodosLosProveedores(): void
    {
        $proveedoresFake = [
            [
                'rif_proveedor'          => 'J12345678',
                'razon_social_proveedor' => 'Proveedor Uno C.A.',
                'telefono_proveedor'     => '04141111111',
                'correo_proveedor'       => 'uno@ejemplo.com',
                'direccion_proveedor'    => 'Calle 1',
                'nro_control_factura'    => '00-0001',
            ],
            [
                'rif_proveedor'          => 'J87654321',
                'razon_social_proveedor' => 'Proveedor Dos S.A.',
                'telefono_proveedor'     => '04242222222',
                'correo_proveedor'       => 'dos@ejemplo.com',
                'direccion_proveedor'    => 'Calle 2',
                'nro_control_factura'    => null,
            ],
        ];
        $stmtStub = $this->crearStmtStub($proveedoresFake, 2);

        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'seleccionarDatos2'  => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarProveedores([]);

        $this->assertCount(2, $resultado);
        $this->assertEquals('J12345678', $resultado[0]['rif_proveedor']);
    }

    public function testSeleccionarUnProveedorPorRif(): void
    {
        $proveedorFake = [
            'rif_proveedor'          => 'J99887766',
            'razon_social_proveedor' => 'Proveedor Específico C.A.',
            'telefono_proveedor'     => '04127654321',
            'correo_proveedor'       => 'especifico@ejemplo.com',
            'direccion_proveedor'    => 'Av. Bolívar',
            'nro_control_factura'    => '00-0099',
        ];
        $stmtStub = $this->crearStmtStub($proveedorFake, 1);

        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'seleccionarDatos2'  => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarProveedores([
            'rif_proveedor' => 'J99887766',
        ]);

        $this->assertEquals('J99887766', $resultado['rif_proveedor']);
        $this->assertEquals('00-0099', $resultado['nro_control_factura']);
    }

    public function testActualizarProveedorExitoso(): void
    {
        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'actualizarDatos2'   => Expected::once(1),
            'commit'             => Expected::once(),
        ], $this);

        $resultado = $modelo->actualizarProveedores([
            'rif_proveedor'          => 'J12345678',
            'razon_social_proveedor' => 'Proveedor Actualizado C.A.',
            'telefono_proveedor'     => '04141234567',
            'correo_proveedor'       => 'actualizado@ejemplo.com',
            'direccion_proveedor'    => 'Nueva Dirección',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Proveedor actualizado', $resultado['titulo']);
    }

    public function testActualizarProveedorSinCambios(): void
    {
        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'actualizarDatos2'   => Expected::once(0),
            'commit'             => Expected::never(),
        ], $this);

        $resultado = $modelo->actualizarProveedores([
            'rif_proveedor'          => 'J12345678',
            'razon_social_proveedor' => 'Mismo Nombre',
            'telefono_proveedor'     => '04141234567',
            'correo_proveedor'       => 'mismo@ejemplo.com',
            'direccion_proveedor'    => 'Misma Dirección',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Proveedor no actualizado', $resultado['titulo']);
    }

    public function testEliminarProveedorExitoso(): void
    {
        /** @var proveedoresModelo $modelo */
        $modelo = Stub::make(proveedoresModelo::class, [
            'validarProveedores' => Expected::once(false),
            'eliminarDatos2'     => Expected::once(1),
            'commit'             => Expected::once(),
        ], $this);

        $resultado = $modelo->eliminarProveedores([
            'rif_proveedor' => 'J12345678',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Proveedor eliminado', $resultado['titulo']);
    }
}