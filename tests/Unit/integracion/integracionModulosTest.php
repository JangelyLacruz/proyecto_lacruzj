<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\ModulosModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionModulosTest extends Unit
{
   private const NOMBRE_MODULO_PRUEBA = 'Modulo Integracion Test';
    private const NOMBRE_MODULO_EDITADO = 'Modulo Integracion Editado';
    private ?int $idModuloCreado = null;

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
        if ($this->idModuloCreado !== null && $this->idModuloCreado > 0) {
            $modelo = new modulosModelo();
            $modelo->eliminarModulos([
                'id_modulo' => $this->idModuloCreado,
            ]);
            $this->idModuloCreado = null;
        }
    }

    public function testListarModulosIntegracion(): void
    {
        $objModulo = new modulosModelo();
        $listado = $objModulo->seleccionarModulos(['accion' => 'listar']);

        $this->assertIsArray($listado);
        $this->assertArrayNotHasKey(
            'icono',
            $listado,
            'Error al listar modulos: ' . json_encode($listado, JSON_UNESCAPED_UNICODE)
        );
    }

    public function testRegistrarConsultarYActualizarModuloIntegracion(): void
    {
        $objModulo = new modulosModelo();

        $resReg = $objModulo->registrarModulos([
            'nombre_modulo'          => self::NOMBRE_MODULO_PRUEBA,
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resReg);
        $this->assertEquals(
            'success',
            $resReg['icono'] ?? null,
            'Error al registrar modulo: ' . json_encode($resReg, JSON_UNESCAPED_UNICODE)
        );

        $listado = $objModulo->seleccionarModulos(['accion' => 'listar']);
        $this->assertIsArray($listado);

        $encontrado = null;
        foreach ($listado as $fila) {
            if (is_array($fila) && ($fila['nombre_modulo'] ?? null) === self::NOMBRE_MODULO_PRUEBA) {
                $encontrado = $fila;
                break;
            }
        }

        $this->assertNotNull($encontrado, 'No se encontró el módulo registrado en el listado.');
        $this->idModuloCreado = (int) ($encontrado['id_modulo'] ?? 0);
        $this->assertGreaterThan(0, $this->idModuloCreado);

        $consultado = $objModulo->seleccionarModulos([
            'id_modulo' => $this->idModuloCreado,
        ]);

        $this->assertIsArray($consultado);
        $this->assertEquals($this->idModuloCreado, (int) ($consultado['id_modulo'] ?? 0));
        $this->assertEquals(self::NOMBRE_MODULO_PRUEBA, $consultado['nombre_modulo'] ?? null);

        $resAct = $objModulo->actualizarModulos([
            'id_modulo'     => $this->idModuloCreado,
            'nombre_modulo' => self::NOMBRE_MODULO_EDITADO,
        ]);

        $this->assertIsArray($resAct);
        $this->assertEquals(
            'success',
            $resAct['icono'] ?? null,
            'Error al actualizar modulo: ' . json_encode($resAct, JSON_UNESCAPED_UNICODE)
        );
    }

    public function testFronteraRegistrarModuloConDatosInvalidosRechaza(): void
    {
        $objModulo = new modulosModelo();

        $resInvalido = $objModulo->registrarModulos([
            'id_modulo'              => '',
            'nombre_modulo'          => '',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resInvalido);
        $this->assertNotEquals('success', $resInvalido['icono'] ?? null);
    }
}