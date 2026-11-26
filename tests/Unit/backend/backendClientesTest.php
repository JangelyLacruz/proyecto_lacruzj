<?php

namespace Tests\Unit\backend;

use Codeception\Test\Unit;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class backendClientesTest extends Unit
{
   private string $raizProyecto;
    private string $rutaControlador;
    private string $rifPrueba = 'V99112233';

    protected function _before(): void
    {
        $this->raizProyecto = dirname(__DIR__, 3);
        $this->rutaControlador = $this->raizProyecto . '/src/controladores/clientesControlador.php';
        $this->limpiarClientePrueba($this->rifPrueba);
    }

    protected function _after(): void
    {
        $this->limpiarClientePrueba($this->rifPrueba);
    }

    public function testControladorClientesRechazaSinSesion(): void
    {
        $res = $this->ejecutarPostControlador([
            'accion' => 'listar',
        ], []);

        $this->assertEquals(0, $res['exitCode'], $res['stderr']);
        $this->assertSame('', trim($res['stdout']));
    }

    public function testControladorClientesAccionNoReconocida(): void
    {
        $res = $this->ejecutarPostControlador([
            'accion' => 'accion_inexistente_xyz',
        ], [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ]);

        $this->assertEquals(
            0,
            $res['exitCode'],
            'Error en subproceso al probar acción no reconocida. Stderr: ' . $res['stderr']
        );

        $data = json_decode($res['stdout'], true);
        $this->assertIsArray($data, 'Salida recibida: ' . $res['stdout']);
        $this->assertEquals('error', $data['icono'] ?? null);
        $this->assertEquals('Acción no reconocida', $data['titulo'] ?? null);
    }

    public function testControladorClientesFlujoCrudCompleto(): void
    {
        $sesion = [
            'cedula' => 'V30485684',
            'id_rol' => 1,
            'rol'    => 1,
        ];

        $listar = $this->ejecutarPostControlador([
            'accion' => 'listar',
        ], $sesion);

        $this->assertEquals(0, $listar['exitCode'], $listar['stderr']);
        $listaData = json_decode($listar['stdout'], true);
        $this->assertIsArray($listaData, 'Salida listar: ' . $listar['stdout']);

        $registro = $this->ejecutarPostControlador([
            'accion'               => 'registrar',
            'rif_cedula_cliente'   => $this->rifPrueba,
            'razon_social_cliente' => 'Cliente Backend Test',
            'telefono_cliente'     => '04129911223',
            'correo_cliente'       => 'cliente.backend@ejemplo.com',
            'direccion_cliente'    => 'Av. Principal Backend',
        ], $sesion);

        $this->assertEquals(0, $registro['exitCode'], $registro['stderr']);
        $regData = json_decode($registro['stdout'], true);
        $this->assertIsArray($regData, 'Salida registrar: ' . $registro['stdout']);
        $this->assertEquals('success', $regData['icono'] ?? null, 'Respuesta registrar: ' . $registro['stdout']);

        $uno = $this->ejecutarPostControlador([
            'accion'             => 'seleccionarUno',
            'rif_cedula_cliente' => $this->rifPrueba,
        ], $sesion);

        $this->assertEquals(0, $uno['exitCode'], $uno['stderr']);
        $unoData = json_decode($uno['stdout'], true);
        $this->assertIsArray($unoData, 'Salida seleccionarUno: ' . $uno['stdout']);
        $this->assertNotEmpty($unoData, 'No se encontró el cliente registrado en seleccionarUno.');

        $actualizar = $this->ejecutarPostControlador([
            'accion'               => 'actualizar',
            'rif_cedula_cliente'   => $this->rifPrueba,
            'razon_social_cliente' => 'Cliente Backend Actualizado',
            'telefono_cliente'     => '04129911224',
            'correo_cliente'       => 'cliente.actualizado@ejemplo.com',
            'direccion_cliente'    => 'Av. Actualizada Backend',
        ], $sesion);

        $this->assertEquals(0, $actualizar['exitCode'], $actualizar['stderr']);
        $actData = json_decode($actualizar['stdout'], true);
        $this->assertIsArray($actData, 'Salida actualizar: ' . $actualizar['stdout']);
        $this->assertEquals('success', $actData['icono'] ?? null, 'Respuesta actualizar: ' . $actualizar['stdout']);

        $eliminar = $this->ejecutarPostControlador([
            'accion'             => 'eliminar',
            'rif_cedula_cliente' => $this->rifPrueba,
        ], $sesion);

        $this->assertEquals(0, $eliminar['exitCode'], $eliminar['stderr']);
        $elimData = json_decode($eliminar['stdout'], true);
        $this->assertIsArray($elimData, 'Salida eliminar: ' . $eliminar['stdout']);
        $this->assertEquals('success', $elimData['icono'] ?? null, 'Respuesta eliminar: ' . $eliminar['stdout']);
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

    private function limpiarClientePrueba(string $rifCedula): void
    {
        $dbPruebas = DB_NAME . '_pruebas';
        $pdo = new \PDO(
            "mysql:host=" . DB_SERVER . ";dbname={$dbPruebas};charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE rif_cedula_cliente = :rif");
        $stmt->execute([':rif' => $rifCedula]);
    }
}