<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\RepartidoresModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionRepartidoresTest extends Unit
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
        $modelo = new repartidoresModelo();
        $modelo->eliminarRepartidores([
            'rif_cedula_repartidor' => self::RIF_PRUEBA,
        ]);
    }

    public function testListarRepartidoresIntegracion(): void
{
    $objRepartidor = new repartidoresModelo();
    $listado = $objRepartidor->seleccionarRepartidores(['accion' => 'listar']);

    $this->assertIsArray($listado);
    $this->assertArrayNotHasKey(
        'icono',
        $listado,
        'Error al listar repartidores: ' . json_encode($listado, JSON_UNESCAPED_UNICODE)
    );
}

     public function testRegistrarRepartidorSinPermisoRetornaAccionNoAutorizada(): void
{
    $_SESSION['id_rol'] = 999;
    $_SESSION['rol'] = 999;

    $objrepartidor = new repartidoresModelo();
    $resultado = $objrepartidor->registrarRepartidores([
        'rif_cedula_repartidor'   => 'V88776655',
        'razon_social_repartidor' => 'Repartidor Sin Permiso C.A.',
        'telefono_repartidor'     => '04148877665',
        'correo_repartidor'       => 'sinpermiso@ejemplo.com',
        'direccion_repartidor'    => 'Av. Prueba',
    ]);

    $this->assertIsArray($resultado);
    $this->assertEquals('error', $resultado['icono'] ?? null);
    $this->assertEquals('Acción no autorizada', $resultado['titulo'] ?? null);
}

    public function testRegistrarConsultarYActualizarRepartidorIntegracion(): void
    {
        $cedulaPrueba = 'V99887766';
        $telefonoPrueba = '04129988776';

        $_SESSION['cedula'] = 'V30485684';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;

        $this->asegurarPermisosRepartidorRol(1, [
            'listar',
            'registrar',
            'ver detalles de los repartidores',
            'actualizar',
            'eliminar',
        ]);
        $this->resetearConexionesEstaticas();

        $objRepartidor = new repartidoresModelo();

        $objRepartidor->eliminarRepartidores([
            'cedula_repartidor' => $cedulaPrueba,
        ]);

        $resReg = $objRepartidor->registrarRepartidores([
            'cedula_repartidor'   => $cedulaPrueba,
            'nombre_repartidor'   => 'Luis',
            'apellido_repartidor' => 'Mendoza',
            'telefono_repartidor' => $telefonoPrueba,
        ]);

        $this->assertIsArray($resReg);
        $this->assertEquals(
            'success',
            $resReg['icono'] ?? null,
            'Error al registrar repartidor: ' . json_encode($resReg, JSON_UNESCAPED_UNICODE)
        );

        $consultado = $objRepartidor->seleccionarRepartidores([
            'cedula_repartidor' => $cedulaPrueba,
        ]);

        $this->assertIsArray($consultado);
        $this->assertEquals(
            $cedulaPrueba,
            $consultado['cedula_repartidor'] ?? null,
            'Respuesta al consultar: ' . json_encode($consultado, JSON_UNESCAPED_UNICODE)
        );
        $this->assertEquals('Luis', $consultado['nombre_repartidor'] ?? null);
        $this->assertEquals('Mendoza', $consultado['apellido_repartidor'] ?? null);

        $resAct = $objRepartidor->actualizarRepartidores([
            'cedula_repartidor'   => $cedulaPrueba,
            'nombre_repartidor'   => 'Carlos',
            'apellido_repartidor' => 'Freitez',
            'telefono_repartidor' => $telefonoPrueba,
        ]);

        $this->assertIsArray($resAct);
        $this->assertEquals(
            'success',
            $resAct['icono'] ?? null,
            'Error al actualizar repartidor: ' . json_encode($resAct, JSON_UNESCAPED_UNICODE)
        );

        $objRepartidor->eliminarRepartidores([
            'cedula_repartidor' => $cedulaPrueba,
        ]);
    }

    private function resetearConexionesEstaticas(): void
    {
        $ref = new ReflectionClass(conexion::class);
        foreach ($ref->getStaticProperties() as $propName => $_) {
            $prop = $ref->getProperty($propName);
            $prop->setAccessible(true);
            $prop->setValue(null, null);
        }
    }

    private function asegurarPermisosRepartidorRol(int $idRol, array $permisos): void
    {
        $dbSeg = DB_NAME . '_seguridad_pruebas';
        $pdo = new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname=$dbSeg;charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );

        $idModulo = $pdo->query("SELECT id_modulo FROM modulos WHERE nombre_modulo = 'repartidores' LIMIT 1")->fetchColumn();
        if (!$idModulo) {
            return;
        }

        foreach ($permisos as $nombrePermiso) {
            $stmtPerm = $pdo->prepare("SELECT id_permiso FROM permisos WHERE nombre_permiso = :perm LIMIT 1");
            $stmtPerm->execute([':perm' => $nombrePermiso]);
            $idPermiso = $stmtPerm->fetchColumn();

            if (!$idPermiso) {
                $stmtInsPerm = $pdo->prepare("INSERT INTO permisos (nombre_permiso, status) VALUES (:perm, 1)");
                $stmtInsPerm->execute([':perm' => $nombrePermiso]);
                $idPermiso = $pdo->lastInsertId();
            } else {
                $pdo->prepare("UPDATE permisos SET status = 1 WHERE id_permiso = :id")
                    ->execute([':id' => (int) $idPermiso]);
            }

            if (!$idPermiso) {
                continue;
            }

            $stmtCheck = $pdo->prepare(
                "SELECT id_acceso FROM accesos WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm LIMIT 1"
            );
            $stmtCheck->execute([
                ':rol'  => $idRol,
                ':mod'  => (int) $idModulo,
                ':perm' => (int) $idPermiso,
            ]);
            $idAcceso = $stmtCheck->fetchColumn();

            if ($idAcceso) {
                $pdo->prepare("UPDATE accesos SET status = 1 WHERE id_acceso = :id")
                    ->execute([':id' => (int) $idAcceso]);
            } else {
                $pdo->prepare(
                    "INSERT INTO accesos (id_rol, id_modulo, id_permiso, status) VALUES (:rol, :mod, :perm, 1)"
                )->execute([
                    ':rol'  => $idRol,
                    ':mod'  => (int) $idModulo,
                    ':perm' => (int) $idPermiso,
                ]);
            }
        }
    }

    public function testFronteraRegistrarRepartidorConDatosInvalidosRechaza(): void
    {
        $objRepartidor = new repartidoresModelo();

        $resInvalido = $objRepartidor->registrarRepartidores([
            'rif_cedula_repartidor'     => '',
            'razon_social_repartidor'   => '',
            'telefono_repartidor'       => '',
            'correo_repartidor'         => 'correo_no_valido',
            'direccion_repartidor'      => '',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resInvalido);
        $this->assertNotEquals('success', $resInvalido['icono'] ?? null);
    }
}