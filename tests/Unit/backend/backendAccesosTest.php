<?php

namespace Tests\Unit\backend;

use Codeception\Test\Unit;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class backendAccesosTest extends Unit
{
   private string $raizProyecto;
    private string $rutaControlador;

    protected function _before(): void
    {
        $this->raizProyecto = dirname(__DIR__, 3);
        $this->rutaControlador = $this->raizProyecto . '/src/controladores/accesosControlador.php';
    }

    public function testControladorAccesosAccionNoReconocida(): void
    {
        $res = $this->ejecutarPostControlador([
            'accion' => 'accion_inexistente_xyz',
        ], [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ]);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $data = json_decode($res['stdout'], true);
        $this->assertIsArray($data, 'Salida recibida: ' . $res['stdout']);
        $this->assertEquals('error', $data['icono'] ?? null);
        $this->assertEquals('Acción no reconocida', $data['titulo'] ?? null);
    }

    public function testControladorAccesosListarPorRolYListar(): void
    {
        $sesion = [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ];

        $listarPorRol = $this->ejecutarPostControlador([
            'accion' => 'listarPorRol',
        ], $sesion);

        $this->assertEquals(0, $listarPorRol['exitCode'], $listarPorRol['stderr']);
        $rolesData = json_decode($listarPorRol['stdout'], true);
        $this->assertIsArray($rolesData, 'Salida listarPorRol: ' . $listarPorRol['stdout']);

        $listar = $this->ejecutarPostControlador([
            'accion' => 'listar',
            'id_rol' => 1,
        ], $sesion);

        $this->assertEquals(0, $listar['exitCode'], $listar['stderr']);
        $permisosData = json_decode($listar['stdout'], true);
        $this->assertIsArray($permisosData, 'Salida listar: ' . $listar['stdout']);
    }

    public function testControladorAccesosActualizarYRestaurarEstado(): void
    {
        $sesion = [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ];

        $pdo = $this->obtenerPdoSeguridadPruebas();
        $fila = $pdo->query("SELECT id_rol, id_modulo, id_permiso, status FROM accesos LIMIT 1")->fetch(\PDO::FETCH_ASSOC);

        if (!$fila) {
            $this->markTestSkipped('No hay registros en la tabla accesos de pruebas para probar actualizar.');
        }

        $estadoOriginal = (int) $fila['status'];
        $nuevoEstado = $estadoOriginal === 1 ? 0 : 1;

        try {
            $resAct = $this->ejecutarPostControlador([
                'accion'     => 'actualizar',
                'id_rol'     => $fila['id_rol'],
                'id_modulo'  => $fila['id_modulo'],
                'id_permiso' => $fila['id_permiso'],
                'cambio'     => $nuevoEstado,
            ], $sesion);

            $this->assertEquals(0, $resAct['exitCode'], $resAct['stderr']);
            $actData = json_decode($resAct['stdout'], true);
            $this->assertIsArray($actData, 'Salida actualizar: ' . $resAct['stdout']);
            $this->assertNotEquals('error', $actData['icono'] ?? null, 'Respuesta: ' . $resAct['stdout']);
        } finally {
            $stmt = $pdo->prepare(
                "UPDATE accesos SET status = :status WHERE id_rol = :rol AND id_modulo = :mod AND id_permiso = :perm"
            );
            $stmt->execute([
                ':status' => $estadoOriginal,
                ':rol'    => $fila['id_rol'],
                ':mod'    => $fila['id_modulo'],
                ':perm'   => $fila['id_permiso'],
            ]);
        }
    }

    private function ejecutarPostControlador(array $post, array $session): array
    {
        $payload = base64_encode(json_encode([
            'controlador' => $this->rutaControlador,
            'post'        => $post,
            'session'     => $session,
        ]));

        $runnerCode = <<<'PHP'
        chdir(getenv('TEST_PROJECT_ROOT'));
        $_ENV['MODO_TESTEO'] = '1';
        $_COOKIE['TEMP'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_IP_DISPOSITIVO'] = '127.0.0.1';

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $cfg = json_decode(base64_decode($argv[1]), true);
        $_SESSION = $cfg['session'] ?? [];
        $_POST = $cfg['post'] ?? [];
        $_REQUEST = $_POST;

        require_once getenv('TEST_PROJECT_ROOT') . '/vendor/autoload.php';
        require_once getenv('TEST_PROJECT_ROOT') . '/src/config/const.php';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
        require $cfg['controlador'];
        PHP;

        $cmd = [
            PHP_BINARY,
            '-r',
            $runnerCode,
            $payload,
        ];

        $rawEnv = array_merge($_ENV, $_SERVER, [
            'TEST_PROJECT_ROOT' => $this->raizProyecto,
        ]);

        $env = [];
        foreach ($rawEnv as $k => $v) {
            if (is_scalar($v) || $v === null) {
                $env[(string) $k] = (string) ($v ?? '');
            }
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $proc = proc_open($cmd, $descriptors, $pipes, $this->raizProyecto, $env);
        $this->assertIsResource($proc, 'No se pudo iniciar el subproceso PHP de prueba.');

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($proc);

        return [
            'exitCode' => $exitCode,
            'stdout'   => (string) $stdout,
            'stderr'   => (string) $stderr,
        ];
    }

    private function obtenerPdoSeguridadPruebas(): \PDO
    {
        $dbSeg = DB_NAME . '_seguridad_pruebas';
        return new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname={$dbSeg};charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}