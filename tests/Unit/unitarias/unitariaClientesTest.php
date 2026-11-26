<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\clientesModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'rif_cedula_cliente'         => 'V12345678',
            'razon_social_cliente'       => 'Cliente Prueba C.A.',
            'telefono_cliente'           => '04141234567',
            'correo_cliente'             => 'prueba@ejemplo.com',
            'direccion_cliente'          => 'Av. Principal',
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

class unitariaClientesTest extends Unit
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

    public function testRegistrarClienteExitoso(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes' => Expected::once(false),
            'guardarDatos2'   => Expected::once(15),
            'commit'          => Expected::once(),
        ], $this);

        $resultado = $modelo->registrarClientes([
            'codigo_rif_cedula_cliente' => 'J',
            'rif_cedula_cliente'        => '12345678',
            'razon_social_cliente'      => 'Comercial Prueba C.A.',
            'prefijo_telefono_cliente'  => '0414',
            'telefono_cliente'          => '1234567',
            'correo_cliente'            => 'comercial@ejemplo.com',
            'direccion_cliente'         => 'Av. Lara',
            'vieneDelModuloUsuarios'    => true,
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Cliente registrado', $resultado['titulo']);
        $this->assertEquals('limpiarYcerrar', $resultado['tipo']);
    }

    public function testRegistrarClienteFallaEnGuardado(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes' => Expected::once(false),
            'guardarDatos2'   => Expected::once(0),
            'commit'          => Expected::never(),
        ], $this);

        $resultado = $modelo->registrarClientes([
            'rif_cedula_cliente'     => 'V99999999',
            'razon_social_cliente'   => 'Cliente Error',
            'telefono_cliente'       => '04241112233',
            'correo_cliente'         => 'error@ejemplo.com',
            'direccion_cliente'      => 'Centro',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Cliente no registrado', $resultado['titulo']);
    }

    public function testSeleccionarTodosLosClientes(): void
    {
        $clientesFake = [
            [
                'rif_cedula_cliente'   => 'V12345678',
                'razon_social_cliente' => 'Cliente Uno C.A.',
                'telefono_cliente'     => '04141111111',
                'correo_cliente'       => 'uno@ejemplo.com',
                'direccion_cliente'    => 'Calle 1',
            ],
            [
                'rif_cedula_cliente'   => 'J87654321',
                'razon_social_cliente' => 'Cliente Dos S.A.',
                'telefono_cliente'     => '04242222222',
                'correo_cliente'       => 'dos@ejemplo.com',
                'direccion_cliente'    => 'Calle 2',
            ],
        ];
        $stmtStub = $this->crearStmtStub($clientesFake, 2);

        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes'   => Expected::once(false),
            'seleccionarDatos2' => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarClientes([
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertCount(2, $resultado);
        $this->assertEquals('V12345678', $resultado[0]['rif_cedula_cliente']);
    }

    public function testSeleccionarUnClientePorRif(): void
    {
        $clienteFake = [
            'rif_cedula_cliente'   => 'J99887766',
            'razon_social_cliente' => 'Cliente Específico C.A.',
            'telefono_cliente'     => '04127654321',
            'correo_cliente'       => 'especifico@ejemplo.com',
            'direccion_cliente'    => 'Av. Bolívar',
        ];
        $stmtStub = $this->crearStmtStub($clienteFake, 1);

        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes'   => Expected::once(false),
            'seleccionarDatos2' => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarClientes([
            'rif_cedula_cliente'     => 'J99887766',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertEquals('J99887766', $resultado['rif_cedula_cliente']);
        $this->assertEquals('Cliente Específico C.A.', $resultado['razon_social_cliente']);
    }

    public function testActualizarClienteExitoso(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes'  => Expected::once(false),
            'actualizarDatos2' => Expected::once(1),
            'commit'           => Expected::once(),
        ], $this);

        $resultado = $modelo->actualizarClientes([
            'rif_cedula_cliente'   => 'V11223344',
            'razon_social_cliente' => 'Cliente Actualizado C.A.',
            'telefono_cliente'     => '04141234567',
            'correo_cliente'       => 'actualizado@ejemplo.com',
            'direccion_cliente'    => 'Nueva Dirección',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Cliente actualizado', $resultado['titulo']);
    }

    public function testActualizarClienteSinCambios(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes'  => Expected::once(false),
            'actualizarDatos2' => Expected::once(0),
            'commit'           => Expected::never(),
        ], $this);

        $resultado = $modelo->actualizarClientes([
            'rif_cedula_cliente'   => 'V11223344',
            'razon_social_cliente' => 'Mismo Nombre',
            'telefono_cliente'     => '04141234567',
            'correo_cliente'       => 'mismo@ejemplo.com',
            'direccion_cliente'    => 'Misma Dirección',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin cambios realizados', $resultado['titulo']);
    }

    public function testEliminarClienteExitoso(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes' => Expected::once(false),
            'eliminarDatos2'  => Expected::once(1),
            'commit'          => Expected::once(),
        ], $this);

        $resultado = $modelo->eliminarClientes([
            'rif_cedula_cliente' => 'V11223344',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Cliente eliminado', $resultado['titulo']);
    }

    public function testEliminarClienteNoEncontrado(): void
    {
        /** @var clientesModelo $modelo */
        $modelo = Stub::make(clientesModelo::class, [
            'validarClientes' => Expected::once(false),
            'eliminarDatos2'  => Expected::once(0),
            'commit'          => Expected::never(),
        ], $this);

        $resultado = $modelo->eliminarClientes([
            'rif_cedula_cliente' => 'V00000000',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Cliente no encontrado', $resultado['titulo']);
    }
}