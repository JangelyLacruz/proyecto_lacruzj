<?php

namespace Tests\Unit\backend;

use Codeception\Test\Unit;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class backendModulosTest extends Unit
{
   private string $raizProyecto;
    private string $rutaControlador;

    protected function _before(): void
    {
        $this->raizProyecto = dirname(__DIR__, 3);
        $this->rutaControlador = $this->raizProyecto . '/src/controladores/modulosControlador.php';
    }

    protected function _after(): void
    {
        $this->limpiarModulosPrueba('Modulo Backend Test');
    }

    public function testControladorModulosRechazaSinSesion(): void
    {
        $res = $this->ejecutarPostControlador([
            'accion' => 'listar',
        ], []);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $this->assertSame('', trim($res['stdout']));
    }

    public function testControladorModulosAccionNoReconocida(): void
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

    public function testControladorModulosListarYRegistrar(): void
    {
        $this->limpiarModulosPrueba('Modulo Backend Test');

        $listar = $this->ejecutarPostControlador([
            'accion' => 'listar',
        ], [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ]);

        $this->assertEquals(0, $listar['exitCode'], $listar['stderr']);
        $listaData = json_decode($listar['stdout'], true);
        $this->assertIsArray($listaData, 'Salida listar: ' . $listar['stdout']);

        $registro = $this->ejecutarPostControlador([
            'accion'        => 'registrar',
            'nombre_modulo' => 'Modulo Backend Test',
        ], [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ]);

        $this->assertEquals(0, $registro['exitCode'], $registro['stderr']);
        $regData = json_decode($registro['stdout'], true);
        $this->assertIsArray($regData, 'Salida registrar: ' . $registro['stdout']);
        $this->assertEquals('success', $regData['icono'] ?? null, 'Respuesta: ' . $registro['stdout']);
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

    private function limpiarModulosPrueba(string $nombreModulo): void
    {
        $dbSeg = DB_NAME . '_seguridad_pruebas';
        $pdo = new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname={$dbSeg};charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
        $stmt = $pdo->prepare("DELETE FROM modulos WHERE nombre_modulo = :nombre");
        $stmt->execute([':nombre' => $nombreModulo]);
    }
}