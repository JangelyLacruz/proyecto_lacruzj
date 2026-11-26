<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\ProveedoresModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionProveedoresTest extends Unit
{
    private const RIF_PRUEBA = 'V15265756';

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

        $_SESSION['cedula'] = 'V15265756';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_IP_DISPOSITIVO'] = '127.0.0.1';
    }

    protected function _after(): void
    {
        $modelo = new ProveedoresModelo();
        $modelo->eliminarProveedores([
            'rif_cedula_proveedor' => self::RIF_PRUEBA,
        ]);
    }

    public function testListarProveedoresIntegracion(): void
{
    $objProveedor = new ProveedoresModelo();
    $listado = $objProveedor->seleccionarProveedores([
        'razon_social_proveedor' => 'Demo',
    ]);

    $this->assertIsArray($listado);
    $this->assertArrayNotHasKey(
        'icono',
        $listado,
        'Error al listar proveedores: ' . json_encode($listado, JSON_UNESCAPED_UNICODE)
    );
}

    public function testRegistrarProveedorSinPermisoRetornaAccionNoAutorizada(): void
{
    $_SESSION['id_rol'] = 999;
    $_SESSION['rol'] = 999;

    $objProveedor = new proveedoresModelo();
    $resultado = $objProveedor->registrarProveedores([
        'rif_cedula_proveedor'   => 'V88776655',
        'razon_social_proveedor' => 'Proveedor Sin Permiso C.A.',
        'telefono_proveedor'     => '04148877665',
        'correo_proveedor'       => 'sinpermiso@ejemplo.com',
        'direccion_proveedor'    => 'Av. Prueba',
    ]);

    $this->assertIsArray($resultado);
    $this->assertEquals('error', $resultado['icono'] ?? null);
    $this->assertEquals('Acción no autorizada', $resultado['titulo'] ?? null);
}

    public function testRegistrarConsultarYActualizarProveedorIntegracion(): void
    {
        $_SESSION['cedula'] = 'V15265756';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;

        $this->asegurarPermisosProveedorRol(1, [
        'registrar',
        'ver detalles de los proveedores',
        'actualizar',
        ]);
        $this->resetearConexionesEstaticas();

        $objProveedor = new proveedoresModelo();

        $objProveedor->eliminarProveedores(['rif_proveedor' => self::RIF_PRUEBA]);

        $resReg = $objProveedor->registrarProveedores([
            'rif_proveedor'          => self::RIF_PRUEBA,
            'razon_social_proveedor' => 'Proveedor Integracion C.A.',
            'telefono_proveedor'     => '04149988776',
            'correo_proveedor'       => 'integracion.proveedor@ejemplo.com',
            'direccion_proveedor'    => 'Av. Principal Integracion',
        ]);

        $this->assertIsArray($resReg);
        $this->assertEquals(
            'success',
            $resReg['icono'] ?? null,
            'Error al registrar proveedor: ' . json_encode($resReg, JSON_UNESCAPED_UNICODE)
        );

        $consultado = $objProveedor->seleccionarProveedores([
            'rif_proveedor' => self::RIF_PRUEBA,
        ]);

        $this->assertIsArray($consultado);
        $this->assertEquals(
            self::RIF_PRUEBA,
            $consultado['rif_proveedor'] ?? null,
            'Respuesta al consultar: ' . json_encode($consultado, JSON_UNESCAPED_UNICODE)
        );
        $this->assertEquals('Proveedor Integracion C.A.', $consultado['razon_social_proveedor'] ?? null);

        $resAct = $objProveedor->actualizarProveedores([
            'rif_proveedor'          => self::RIF_PRUEBA,
            'razon_social_proveedor' => 'Proveedor Integracion Editado C.A.',
            'telefono_proveedor'     => '04149988776',
            'correo_proveedor'       => 'integracion.proveedor@ejemplo.com',
            'direccion_proveedor'    => 'Calle Nueva Integracion',
        ]);

        $this->assertIsArray($resAct);
        $this->assertEquals(
            'success',
            $resAct['icono'] ?? null,
            'Error al actualizar proveedor: ' . json_encode($resAct, JSON_UNESCAPED_UNICODE)
        );
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

    private function asegurarPermisosProveedorRol(int $idRol, array $permisos): void
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

        $idModulo = $pdo->query("SELECT id_modulo FROM modulos WHERE nombre_modulo = 'proveedores' LIMIT 1")->fetchColumn();
        if (!$idModulo) {
            return;
        }

        foreach ($permisos as $nombrePermiso) {
            $stmtPerm = $pdo->prepare("SELECT id_permiso FROM permisos WHERE nombre_permiso = :perm LIMIT 1");
            $stmtPerm->execute([':perm' => $nombrePermiso]);
            $idPermiso = $stmtPerm->fetchColumn();

            // Si el permiso no existe en la tabla `permisos` de pruebas, lo creamos
            if (!$idPermiso) {
                $stmtInsPerm = $pdo->prepare(
                    "INSERT INTO permisos (nombre_permiso, status) VALUES (:perm, 1)"
                );
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

    public function testFronteraRegistrarProveedorConDatosInvalidosRechaza(): void
    {
        $objProveedor = new ProveedoresModelo();

        $resInvalido = $objProveedor->registrarProveedores([
            'rif_cedula_proveedor'     => '',
            'razon_social_proveedor'   => '',
            'telefono_proveedor'       => '',
            'correo_proveedor'         => 'correo_no_valido',
            'direccion_proveedor'      => '',
            'vieneDelModuloUsuarios' => true,
        ]);

        $this->assertIsArray($resInvalido);
        $this->assertNotEquals('success', $resInvalido['icono'] ?? null);
    }

    
}