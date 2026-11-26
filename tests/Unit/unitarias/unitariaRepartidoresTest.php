<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\RepartidoresModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'cedula_repartidor'          => 'V18765432',
            'nombre_repartidor'          => 'Carlos',
            'apellido_repartidor'        => 'Mendoza',
            'telefono_repartidor'        => '04141234567',
            'rif_cedula_cliente'         => 'V12345678',
            'razon_social_cliente'       => 'Cliente Demo C.A.',
            'id_acceso'                  => 1,
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'repartidores',
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

class unitariaRepartidoresTest extends Unit
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

    public function testRegistrarRepartidorExitoso(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'guardarDatos2'       => Expected::once(12),
            'commit'              => Expected::once(),
        ], $this);

        $resultado = $modelo->registrarRepartidores([
            'codigo_cedula_repartidor'   => 'V',
            'cedula_repartidor'          => '18765432',
            'nombre_repartidor'          => 'Carlos',
            'apellido_repartidor'        => 'Mendoza',
            'prefijo_telefono_repartidor'=> '0414',
            'telefono_repartidor'        => '1234567',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Repartidor registrado', $resultado['titulo']);
        $this->assertEquals('limpiarYcerrar', $resultado['tipo']);
    }

    public function testRegistrarRepartidorFallaEnGuardado(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'guardarDatos2'       => Expected::once(0),
            'commit'              => Expected::never(),
        ], $this);

        $resultado = $modelo->registrarRepartidores([
            'cedula_repartidor'   => 'V99999999',
            'nombre_repartidor'   => 'Error',
            'apellido_repartidor' => 'Prueba',
            'telefono_repartidor' => '04241112233',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Repartidor no registrado', $resultado['titulo']);
    }

    public function testSeleccionarTodosLosRepartidores(): void
    {
        $repartidoresFake = [
            [
                'cedula_repartidor'   => 'V18765432',
                'nombre_repartidor'   => 'Pedro Alberto',
                'apellido_repartidor' => 'Mendoza',
                'telefono_repartidor' => '04141234567',
            ],
            [
                'cedula_repartidor'   => 'V20111222',
                'nombre_repartidor'   => 'Luis',
                'apellido_repartidor' => 'Pérez',
                'telefono_repartidor' => '04247654321',
            ],
        ];
        $stmtStub = $this->crearStmtStub($repartidoresFake, 2);

        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'seleccionarDatos2'   => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarRepartidores([]);

        $this->assertCount(2, $resultado);
        $this->assertEquals('V18765432', $resultado[0]['cedula_repartidor']);
    }

    public function testSeleccionarUnRepartidorPorCedula(): void
    {
        $repartidorFake = [
            'cedula_repartidor'   => 'V18765432',
            'nombre_repartidor'   => 'Pedro Alberto',
            'apellido_repartidor' => 'Mendoza',
            'telefono_repartidor' => '04141234567',
        ];
        $stmtStub = $this->crearStmtStub($repartidorFake, 1);

        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'seleccionarDatos2'   => Expected::once($stmtStub),
        ], $this);

        $resultado = $modelo->seleccionarRepartidores([
            'cedula_repartidor' => 'V18765432',
        ]);

        $this->assertEquals('V18765432', $resultado['cedula_repartidor']);
        $this->assertEquals('Pedro Alberto', $resultado['nombre_repartidor']);
    }

    public function testActualizarRepartidorExitoso(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'actualizarDatos2'    => Expected::once(1),
            'commit'              => Expected::once(),
        ], $this);

        $resultado = $modelo->actualizarRepartidores([
            'cedula_repartidor'   => 'V18765432',
            'nombre_repartidor'   => 'Pedro Alberto',
            'apellido_repartidor' => 'Mendoza',
            'telefono_repartidor' => '04141234567',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Repartidor actualizado', $resultado['titulo']);
    }

    public function testActualizarRepartidorSinCambios(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'actualizarDatos2'    => Expected::once(0),
            'commit'              => Expected::never(),
        ], $this);

        $resultado = $modelo->actualizarRepartidores([
            'cedula_repartidor'   => 'V18765432',
            'nombre_repartidor'   => 'Pedro Alberto',
            'apellido_repartidor' => 'Mendoza',
            'telefono_repartidor' => '04141234567',
        ]);

        $this->assertEquals('warning', $resultado['icono']);
        $this->assertEquals('Sin cambios realizados', $resultado['titulo']);
    }

    public function testEliminarRepartidorExitoso(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'eliminarDatos2'      => Expected::once(1),
            'commit'              => Expected::once(),
        ], $this);

        $resultado = $modelo->eliminarRepartidores([
            'cedula_repartidor' => 'V18765432',
        ]);

        $this->assertEquals('success', $resultado['icono']);
        $this->assertEquals('Repartidor eliminado', $resultado['titulo']);
    }

    public function testEliminarRepartidorNoEncontrado(): void
    {
        /** @var repartidoresModelo $modelo */
        $modelo = Stub::make(repartidoresModelo::class, [
            'validarRepartidores' => Expected::once(false),
            'eliminarDatos2'      => Expected::once(0),
            'commit'              => Expected::never(),
        ], $this);

        $resultado = $modelo->eliminarRepartidores([
            'cedula_repartidor' => 'V00000000',
        ]);

        $this->assertEquals('error', $resultado['icono']);
        $this->assertEquals('Repartidor no encontrado', $resultado['titulo']);
    }
}