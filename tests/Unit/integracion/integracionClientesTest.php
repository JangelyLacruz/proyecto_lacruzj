<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\ClientesModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionClientesTest extends Unit
{
    private const RIF_PRUEBA = 'V99887766';

    protected function _before(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_COOKIE['TEMP'] = true;
        $_ENV['MODO_TESTEO'] = true;

        $ref = new ReflectionClass(conexion::class);
        foreach (['conexion', 'conexion2'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $prop = $ref->getProperty($propName);
                $prop->setAccessible(true);
                $prop->setValue(null, null);
            }
        }

        $_SESSION['cedula'] = 'V30485684';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_IP_DISPOSITIVO'] = '127.0.0.1';
    }

    protected function _after(): void
    {
        $modelo = new clientesModelo();
        $modelo->eliminarClientes([
            'rif_cedula_cliente' => self::RIF_PRUEBA,
        ]);
    }

    public function testListarClientesIntegracion(): void
{
    $objCliente = new clientesModelo();
    $listado = $objCliente->seleccionarClientes(['accion' => 'listar']);

    $this->assertIsArray($listado);
    $this->assertArrayNotHasKey(
        'icono',
        $listado,
        'Error al listar clientes: ' . json_encode($listado, JSON_UNESCAPED_UNICODE)
    );
}

    public function testRegistrarConsultarYActualizarClienteIntegracion(): void
    {
        $objCliente = new clientesModelo();

        $resReg = $objCliente->registrarClientes([
            'rif_cedula_cliente'     => self::RIF_PRUEBA,
            'razon_social_cliente'   => 'Cliente Integracion C.A.',
            'telefono_cliente'       => '04149988776',
            'correo_cliente'         => 'integracion.cliente@ejemplo.com',
            'direccion_cliente'      => 'Av. Principal Integracion',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resReg);
        $this->assertEquals(
            'success',
            $resReg['icono'] ?? null,
            'Error al registrar cliente: ' . json_encode($resReg, JSON_UNESCAPED_UNICODE)
        );

        $consultado = $objCliente->seleccionarClientes([
            'rif_cedula_cliente' => self::RIF_PRUEBA,
        ]);

        $this->assertIsArray($consultado);
        $this->assertEquals(self::RIF_PRUEBA, $consultado['rif_cedula_cliente'] ?? null);
        $this->assertEquals('Cliente Integracion C.A.', $consultado['razon_social_cliente'] ?? null);

        $resAct = $objCliente->actualizarClientes([
            'rif_cedula_cliente'     => self::RIF_PRUEBA,
            'razon_social_cliente'   => 'Cliente Integracion Editado C.A.',
            'telefono_cliente'       => '04149988776',
            'correo_cliente'         => 'integracion.cliente@ejemplo.com',
            'direccion_cliente'      => 'Calle Nueva Integracion',
        ]);

        $this->assertIsArray($resAct);
        $this->assertEquals(
            'success',
            $resAct['icono'] ?? null,
            'Error al actualizar cliente: ' . json_encode($resAct, JSON_UNESCAPED_UNICODE)
        );
    }

    public function testFronteraRegistrarClienteConDatosInvalidosRechaza(): void
    {
        $objCliente = new clientesModelo();

        $resInvalido = $objCliente->registrarClientes([
            'rif_cedula_cliente'     => '',
            'razon_social_cliente'   => '',
            'telefono_cliente'       => '',
            'correo_cliente'         => 'correo_no_valido',
            'direccion_cliente'      => '',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resInvalido);
        $this->assertNotEquals('success', $resInvalido['icono'] ?? null);
    }
}