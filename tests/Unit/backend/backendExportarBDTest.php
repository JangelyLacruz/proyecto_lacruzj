<?php

namespace Tests\Unit\backend;

use Codeception\Test\Unit;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class backendExportarBDTest extends Unit
{
    private string $raizProyecto;
    private string $rutaControlador;

    protected function _before(): void
    {
        $this->raizProyecto = dirname(__DIR__, 3);
        $this->rutaControlador = $this->raizProyecto . '/src/controladores/exportarControlador.php';
    }

    public function testControladorExportarBDMetodoNoPostNoResponde(): void
    {
        $res = $this->ejecutarPeticion([
            'method'  => 'GET',
            'post'    => [],
            'session' => [
                'cedula' => 'V30485684',
                'id_rol' => 1,
                'rol'    => 1,
            ],
        ]);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $this->assertSame('', trim($res['stdout']));
    }

    public function testControladorExportarBDSinAccionExitLimpio(): void
    {
        $res = $this->ejecutarPeticion([
            'method'  => 'POST',
            'post'    => [],
            'session' => [
                'cedula' => 'V30485684',
                'id_rol' => 1,
                'rol'    => 1,
            ],
        ]);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
    }

    public function testControladorExportarBDConBDInvalida(): void
    {
        $res = $this->ejecutarPeticion([
            'method'  => 'POST',
            'post'    => [
                'accion' => 'exportar',
                'BD'     => 'bd_inexistente_xyz_123',
            ],
            'session' => [
                'cedula' => 'V30485684',
                'id_rol' => 1,
                'rol'    => 1,
            ],
        ]);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $data = json_decode($res['stdout'], true);
        $this->assertIsArray($data, 'Salida recibida: ' . $res['stdout']);
        $this->assertArrayHasKey('icono', $data);
        $this->assertNotSame('', trim($res['stdout']));
    }

    public function testControladorExportarBDPruebas(): void
    {
        $bdPruebas = DB_NAME . '_pruebas';

        $res = $this->ejecutarPeticion([
            'method'  => 'POST',
            'post'    => [
                'accion' => 'exportar',
                'BD'     => $bdPruebas,
            ],
            'session' => [
                'cedula' => 'V30485684',
                'id_rol' => 1,
                'rol'    => 1,
            ],
        ]);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $data = json_decode($res['stdout'], true);
        $this->assertIsArray($data, 'Salida exportar BD pruebas: ' . $res['stdout']);
        $this->assertArrayHasKey('icono', $data);
    }

    private function ejecutarPeticion(array $cfgData): array
    {
        $payload = base64_encode(json_encode([
            'controlador' => $this->rutaControlador,
            'method'      => $cfgData['method'] ?? 'POST',
            'post'        => $cfgData['post'] ?? [],
            'session'     => $cfgData['session'] ?? [],
        ]));

        $runnerCode = <<<'PHP'
        chdir(getenv('TEST_PROJECT_ROOT'));
        $_ENV['MODO_TESTEO'] = '1';
        $_COOKIE['TEMP'] = '1';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_IP_DISPOSITIVO'] = '127.0.0.1';

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $cfg = json_decode(base64_decode($argv[1]), true);
        $_SERVER['REQUEST_METHOD'] = $cfg['method'] ?? 'POST';
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
}