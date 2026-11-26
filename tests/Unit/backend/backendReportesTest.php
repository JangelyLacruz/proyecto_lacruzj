<?php

namespace Tests\Unit\backend;

use Codeception\Test\Unit;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

class backendReportesTest extends Unit
{
    private string $raizProyecto;
    private string $rutaControlador;
    private array $sesionAdmin = ['cedula' => 'V30485684', 'id_rol' => 1, 'rol' => 1];

    protected function _before(): void
    {
        $this->raizProyecto = dirname(__DIR__, 3);
        $this->rutaControlador = $this->raizProyecto . '/src/controladores/reportesControlador.php';
    }
   
    public function testControladorRechazaReporteInexistente(): void
    {
        $res = $this->ejecutarPostControlador(['reporte' => 'reporte_que_no_existe'], $this->sesionAdmin);
        $data = json_decode($res['stdout'], true);
        
        $this->assertIsArray($data);
        $this->assertEquals('error', $data['icono'] ?? null);
    }

    public function testReporteVentasSinFechasDevuelveError(): void
    {
        $res = $this->ejecutarPostControlador(['reporte' => 'reporteVentas'], $this->sesionAdmin);
        $data = json_decode($res['stdout'], true);
        
        $this->assertEquals('error', $data['icono'] ?? null);
        $this->assertEquals('Sin fecha específicada', $data['titulo'] ?? null);
    }

    public function testReporteVentasGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteVentas',
            'fecha_desde' => '01-01-2024',
            'fecha_hasta' => date('d-m-Y'),
            'tipo_producto' => 'todos'
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Ventas');
    }

    public function testReporteComprasGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteCompras',
            'fecha_desde' => '07-06-2026',
            'fecha_hasta' => date('d-m-Y'),
            'tipo_item' => 'todos'
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Compras');
    }

    public function testReporteCierreGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteCierre',
            'fecha_cierre' => date('29-06-2026')
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Cierre de Caja');
    }

    public function testReporteServiciosGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteServicios'
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Servicios');
    }
    
    public function testReporteProductosGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteProductos'
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Productos');
    }

    public function testReporteMateriasPrimasGeneraPDF(): void
    {
        $res = $this->ejecutarPostControlador([
            'reporte' => 'reporteMateriaPrima'
        ], $this->sesionAdmin);

        $this->assertPdfValido($res, 'Reporte de Materias Primas');
    }

    private function assertPdfValido(array $res, string $nombreReporte): void
    {
        $this->assertEquals(0, $res['exitCode'], "Error en $nombreReporte. Stderr: " . $res['stderr']);
        
        $inicioArchivo = substr($res['stdout'], 0, 5);
        $this->assertEquals('%PDF-', $inicioArchivo, "El archivo de $nombreReporte no es un PDF válido.");
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

        $cmd = [PHP_BINARY, '-r', $runnerCode, $payload];

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