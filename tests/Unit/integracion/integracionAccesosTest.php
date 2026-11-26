<?php

namespace Tests\Unit\integracion;

use Codeception\Test\Unit;
use src\modelos\accesosModelo;
use src\config\connect\conexion;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class integracionAccesosTest extends Unit
{
    private int $idRol = 1;
    private int $idModulo = 1;
    private int $idPermiso = 1;
    private string $nombreModulo = 'clientes';
    private string $nombrePermiso = 'ver';

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

        $pdo = $this->getConexionSeguridad();

        $idRol = $pdo->query("SELECT id_rol FROM roles WHERE id_rol != 1 ORDER BY id_rol ASC LIMIT 1")->fetchColumn();
        if (!$idRol) {
            $idRol = $pdo->query("SELECT id_rol FROM roles ORDER BY id_rol ASC LIMIT 1")->fetchColumn();
        }
        $this->idRol = $idRol ? (int) $idRol : 0;

        $modRow = $pdo->query("SELECT id_modulo, nombre_modulo FROM modulos ORDER BY id_modulo ASC LIMIT 1")->fetch();
        $this->idModulo = $modRow ? (int) $modRow['id_modulo'] : 0;
        $this->nombreModulo = $modRow ? (string) $modRow['nombre_modulo'] : 'clientes';

        $permRow = $pdo->query("SELECT id_permiso, nombre_permiso FROM permisos ORDER BY id_permiso ASC LIMIT 1")->fetch();
        $this->idPermiso = $permRow ? (int) $permRow['id_permiso'] : 0;
        $this->nombrePermiso = $permRow ? (string) $permRow['nombre_permiso'] : 'ver';

        if ($this->idRol <= 0 || $this->idModulo <= 0 || $this->idPermiso <= 0) {
            $this->fail('Faltan registros base en roles, modulos o permisos dentro de proyecto_lacruz_seguridad_pruebas.');
        }

        $stmtCheck = $pdo->prepare(
            "SELECT id_acceso FROM accesos WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm LIMIT 1"
        );
        $stmtCheck->execute([
            ':rol'  => $this->idRol,
            ':mod'  => $this->idModulo,
            ':perm' => $this->idPermiso,
        ]);

        if (!$stmtCheck->fetchColumn()) {
            $stmtInsert = $pdo->prepare(
                "INSERT INTO accesos (id_rol, id_modulo, id_permiso, status) VALUES (:rol, :mod, :perm, 0)"
            );
            $stmtInsert->execute([
                ':rol'  => $this->idRol,
                ':mod'  => $this->idModulo,
                ':perm' => $this->idPermiso,
            ]);
        }

        $idModAccesos = $pdo->query("SELECT id_modulo FROM modulos WHERE nombre_modulo = 'accesos' LIMIT 1")->fetchColumn();
        $idPermAct = $pdo->query("SELECT id_permiso FROM permisos WHERE nombre_permiso = 'actualizar permisos' LIMIT 1")->fetchColumn();

        if ($idModAccesos && $idPermAct) {
            $stmtPermAdmin = $pdo->prepare(
                "SELECT id_acceso FROM accesos WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm LIMIT 1"
            );
            $stmtPermAdmin->execute([
                ':rol'  => 1,
                ':mod'  => (int) $idModAccesos,
                ':perm' => (int) $idPermAct,
            ]);
            $idAccesoAdmin = $stmtPermAdmin->fetchColumn();

            if ($idAccesoAdmin) {
                $pdo->prepare("UPDATE accesos SET status = 1 WHERE id_acceso = :id")
                    ->execute([':id' => (int) $idAccesoAdmin]);
            } else {
                $pdo->prepare(
                    "INSERT INTO accesos (id_rol, id_modulo, id_permiso, status) VALUES (:rol, :mod, :perm, 1)"
                )->execute([
                    ':rol'  => 1,
                    ':mod'  => (int) $idModAccesos,
                    ':perm' => (int) $idPermAct,
                ]);
            }
        }
    }

    protected function _after(): void
    {
        $pdo = $this->getConexionSeguridad();
        $stmt = $pdo->prepare(
            "UPDATE accesos SET status = 0 WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm"
        );
        $stmt->execute([
            ':rol'  => $this->idRol,
            ':mod'  => $this->idModulo,
            ':perm' => $this->idPermiso,
        ]);
    }

    public function testActualizarPermisoEnciendeEnBD(): void
    {
        $modelo = new accesosModelo();

        $modelo->actualizarPermisos($this->idRol, $this->idModulo, $this->idPermiso, 0);

        $resultado = $modelo->actualizarPermisos($this->idRol, $this->idModulo, $this->idPermiso, 1);

        $permisosActuales = $modelo->seleccionarPermisosPorRol(1);
        $this->assertEquals(
            'success',
            $resultado['icono'] ?? null,
            json_encode([
                'resultado'      => $resultado,
                'session_rol'    => $_SESSION['rol'] ?? null,
                'permisos_rol_1' => $permisosActuales,
            ])
        );
        $this->assertEquals('Acceso actualizado', $resultado['titulo'] ?? null);

        $conexion = $this->getConexionSeguridad();
        $stmt = $conexion->prepare(
            "SELECT status FROM accesos WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm LIMIT 1"
        );
        $stmt->execute([
            ':rol'  => $this->idRol,
            ':mod'  => $this->idModulo,
            ':perm' => $this->idPermiso,
        ]);
        $status = $stmt->fetchColumn();

        $this->assertEquals(1, (int) $status, 'El permiso no se guardó como activo en la base de datos.');
    }

    public function testActualizarPermisoApagaEnBD(): void
    {
        $modelo = new accesosModelo();

        $modelo->actualizarPermisos($this->idRol, $this->idModulo, $this->idPermiso, 1);

        $resultado = $modelo->actualizarPermisos($this->idRol, $this->idModulo, $this->idPermiso, 0);

        $this->assertEquals('success', $resultado['icono'] ?? null, $resultado['texto'] ?? 'Error al apagar permiso');

        $conexion = $this->getConexionSeguridad();
        $stmt = $conexion->prepare(
            "SELECT status FROM accesos WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm LIMIT 1"
        );
        $stmt->execute([
            ':rol'  => $this->idRol,
            ':mod'  => $this->idModulo,
            ':perm' => $this->idPermiso,
        ]);
        $status = $stmt->fetchColumn();

        $this->assertEquals(0, (int) $status, 'El permiso no se guardó como inactivo en la base de datos.');
    }

    public function testValidarPermisoApagadoDevuelveError(): void
    {
        $modelo = new accesosModelo();

        $modelo->actualizarPermisos($this->idRol, $this->idModulo, $this->idPermiso, 0);

        $_SESSION['id_rol'] = $this->idRol;
        $_SESSION['rol'] = $this->idRol;

        $resultado = $modelo->validarPermisos($this->nombreModulo, $this->nombrePermiso);

        $this->assertIsArray($resultado);
        $this->assertEquals('error', $resultado['icono'] ?? null);
        $this->assertEquals('Acción no autorizada', $resultado['titulo'] ?? null);
    }

    private function getConexionSeguridad(): \PDO
    {
        $db = DB_NAME . '_seguridad_pruebas';
        return new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname=$db;charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }
}