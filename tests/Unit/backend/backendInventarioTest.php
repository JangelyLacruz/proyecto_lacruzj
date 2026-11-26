<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendInventarioTest extends Unit
{
    private const BASE_URI = 'http://localhost/proyecto-lacruz-j/';
    private const ID_PRODUCTO = 'PROD-26299-00002-67';
    private const ID_PRESENTACION = 'PRPR-26299-00002-08';
    private const ID_MATERIA_PRIMA = 'MATE-26123-00001-66';

    private static ?Client $client = null;
    private static string $csrfToken = '';

    protected function _before(): void
    {
        if (self::$client !== null) {
            return;
        }

        $cookies = new CookieJar();
        $cookies->setCookie(new SetCookie([
            'Name'   => 'TEMP',
            'Value'  => 'true',
            'Domain' => 'localhost',
            'Path'   => '/',
        ]));

        $client = new Client([
            'base_uri'        => self::BASE_URI,
            'cookies'         => $cookies,
            'http_errors'     => false,
            'connect_timeout' => 5,
            'timeout'         => 30,
        ]);

        $client->get('usuarios/login');

        $resLogin = $client->post('usuarios/login', [
            'headers'     => $this->headersAjax(),
            'form_params' => [
                'accion'              => 'iniciarSesion',
                'usuario_usuario'     => 'Ander123',
                'contrasena1_usuario' => 'David01*',
            ],
        ]);

        $this->assertEquals(200, $resLogin->getStatusCode(), 'Falló HTTP del login.');
        $jsonLogin = json_decode((string) $resLogin->getBody(), true);

        $loginValido = ($jsonLogin['tipo'] ?? null) === 'redireccionar';
        if (!$loginValido) {
            $loginValido = ($jsonLogin['icono'] ?? null) === 'success';
        }
        $this->assertTrue(
            $loginValido,
            'Falló el inicio de sesión: ' . json_encode($jsonLogin, JSON_UNESCAPED_UNICODE)
        );

        $htmlVista = (string) $client->get('inventario')->getBody();
        preg_match('/<meta[^>]*name=["\']TOKEN_CSRF["\'][^>]*content=["\']([^"\']+)["\']/i', $htmlVista, $m);
        $this->assertNotEmpty($m[1] ?? '', 'No se pudo extraer el TOKEN_CSRF.');

        self::$client = $client;
        self::$csrfToken = $m[1];
    }

    private function headersAjax(): array
    {
        $headers = [
            'X-ES-AJAX'        => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IP-DISPOSITIVO' => '127.0.0.1',
            'Accept'           => 'application/json, text/plain, */*',
        ];

        if (self::$csrfToken !== '') {
            $headers['X-TOKEN-CSRF'] = self::$csrfToken;
        }

        return $headers;
    }

    private function postAjax(string $ruta, array $campos, array $statusesPermitidos = [200]): array
    {
        $res = self::$client->post($ruta, [
            'headers'     => $this->headersAjax(),
            'form_params' => $campos,
        ]);

        $status = $res->getStatusCode();
        $body = (string) $res->getBody();

        $this->assertStringNotContainsString('Fatal error', $body);
        $this->assertStringNotContainsString('Parse error', $body);
        $this->assertStringNotContainsString('Uncaught', $body);
        $this->assertTrue(
            in_array($status, $statusesPermitidos, true),
            'Esperaba HTTP ' . implode('/', $statusesPermitidos) . ", recibió {$status}. Body: " . substr($body, 0, 300)
        );

        $json = json_decode($body, true);
        $this->assertIsArray($json, 'Respuesta no es JSON válido: ' . substr($body, 0, 300));

        return $json;
    }

    public function testGetVistaInventarioAutorizado(): void
    {
        $res = self::$client->get('inventario');
        $html = (string) $res->getBody();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error', $html);
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringContainsString('TOKEN_CSRF', $html);
    }

    public function testVerEntradasSalidasProductos(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'      => 'verEntradasSalidas',
            'tipo'        => 'productos',
            'id_producto' => self::ID_PRODUCTO,
        ]);

        $this->assertArrayNotHasKey('icono', $json);
    }

    public function testVerEntradasSalidasMateriasPrimas(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'           => 'verEntradasSalidas',
            'tipo'             => 'materiasPrimas',
            'id_materia_prima' => self::ID_MATERIA_PRIMA,
        ]);

        $this->assertArrayNotHasKey('icono', $json);
    }

    public function testVerEntradasSalidasTipoNoReconocido(): void
    {
        $json = $this->postAjax('inventario', [
            'accion' => 'verEntradasSalidas',
            'tipo'   => 'invalido',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertStringContainsString('Tipo no reconocido', $json['texto'] ?? '');
    }

    public function testReporteProductosFechaInicioMayorAFin(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'      => 'reporteProductos',
            'id_producto' => self::ID_PRODUCTO,
            'fecha_desde' => '2026-12-31',
            'fecha_hasta' => '2026-01-01',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Fecha de inicio mayor a la fecha de fin', $json['titulo'] ?? null);
    }

    public function testReporteProductosRangoSinRegistros(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'      => 'reporteProductos',
            'id_producto' => self::ID_PRODUCTO,
            'fecha_desde' => '2020-01-01',
            'fecha_hasta' => '2020-01-02',
        ], [200, 400]);

        $this->assertEquals('warning', $json['icono'] ?? null);
        $this->assertEquals('Sin registros', $json['titulo'] ?? null);
    }

    public function testReporteMateriasPrimasFechaInicioMayorAFin(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'           => 'reporteMateriasPrimas',
            'id_materia_prima' => self::ID_MATERIA_PRIMA,
            'fecha_desde'      => '2026-12-31',
            'fecha_hasta'      => '2026-01-01',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Fecha de inicio mayor a la fecha de fin', $json['titulo'] ?? null);
    }

    public function testReporteMateriasPrimasRangoSinRegistros(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'           => 'reporteMateriasPrimas',
            'id_materia_prima' => self::ID_MATERIA_PRIMA,
            'fecha_desde'      => '2020-01-01',
            'fecha_hasta'      => '2020-01-02',
        ], [200, 400]);

        $this->assertEquals('warning', $json['icono'] ?? null);
        $this->assertEquals('Sin registros', $json['titulo'] ?? null);
    }

    public function testRegistrarMovimientoProductoCantidadCero(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'                   => 'registrarMovimientosProductos',
            'id_presentacion_producto' => self::ID_PRESENTACION,
            'cantidad_movimiento'      => '0',
            'tipo_movimiento'          => '1',
            'motivo_movimiento'        => 'Prueba cantidad cero',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testRegistrarMovimientoMateriaPrimaStockInsuficiente(): void
    {
        $json = $this->postAjax('inventario', [
            'accion'              => 'registrarMovimientosMateriasPrimas',
            'id_materia_prima'    => self::ID_MATERIA_PRIMA,
            'cantidad_movimiento' => '999999',
            'tipo_movimiento'     => '0',
            'motivo_movimiento'   => 'Prueba descarga excesiva',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Stock insuficiente', $json['titulo'] ?? null);
    }

    public function testAccionNoReconocida(): void
    {
        $json = $this->postAjax('inventario', ['accion' => 'accionInexistenteQA'], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Accion no reconocida', $json['titulo'] ?? null);
    }

    public function testBloqueoPostSinSesionActiva(): void
    {
        $clienteAnonimo = new Client([
            'base_uri'        => self::BASE_URI,
            'http_errors'     => false,
            'allow_redirects' => false,
            'timeout'         => 10,
        ]);

        $res = $clienteAnonimo->post('inventario', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => [
                'accion'      => 'verEntradasSalidas',
                'tipo'        => 'productos',
                'id_producto' => self::ID_PRODUCTO,
            ],
        ]);

        $json = json_decode((string) $res->getBody(), true);
        $this->assertFalse(is_array($json) && isset($json[0]['id_movimiento_anomalo_producto']));
    }

    public function testFlujoCargaYDescargaCompensatoriaProducto(): void
    {
        $cargado = false;

        try {
            $jsonCarga = $this->postAjax('inventario', [
                'accion'                   => 'registrarMovimientosProductos',
                'id_presentacion_producto' => self::ID_PRESENTACION,
                'cantidad_movimiento'      => '1',
                'tipo_movimiento'          => '1',
                'motivo_movimiento'        => 'Carga QA Guzzle producto',
            ]);

            $this->assertEquals('success', $jsonCarga['icono'] ?? null);
            $this->assertEquals('Movimiento registrado', $jsonCarga['titulo'] ?? null);
            $cargado = true;

            $jsonDescarga = $this->postAjax('inventario', [
                'accion'                   => 'registrarMovimientosProductos',
                'id_presentacion_producto' => self::ID_PRESENTACION,
                'cantidad_movimiento'      => '1',
                'tipo_movimiento'          => '0',
                'motivo_movimiento'        => 'Descarga QA Guzzle producto',
            ]);

            $this->assertEquals('success', $jsonDescarga['icono'] ?? null);
            $this->assertEquals('Movimiento registrado', $jsonDescarga['titulo'] ?? null);
            $cargado = false;
        } finally {
            if ($cargado) {
                self::$client->post('inventario', [
                    'headers'     => $this->headersAjax(),
                    'form_params' => [
                        'accion'                   => 'registrarMovimientosProductos',
                        'id_presentacion_producto' => self::ID_PRESENTACION,
                        'cantidad_movimiento'      => '1',
                        'tipo_movimiento'          => '0',
                        'motivo_movimiento'        => 'Reversion QA Guzzle producto',
                    ],
                ]);
            }
        }
    }

    public function testFlujoCargaYDescargaCompensatoriaMateriaPrima(): void
    {
        $cargado = false;

        try {
            $jsonCarga = $this->postAjax('inventario', [
                'accion'              => 'registrarMovimientosMateriasPrimas',
                'id_materia_prima'    => self::ID_MATERIA_PRIMA,
                'cantidad_movimiento' => '1',
                'tipo_movimiento'     => '1',
                'motivo_movimiento'   => 'Carga QA Guzzle materia prima',
            ]);

            $this->assertEquals('success', $jsonCarga['icono'] ?? null);
            $this->assertEquals('Movimiento registrado', $jsonCarga['titulo'] ?? null);
            $cargado = true;

            $jsonDescarga = $this->postAjax('inventario', [
                'accion'              => 'registrarMovimientosMateriasPrimas',
                'id_materia_prima'    => self::ID_MATERIA_PRIMA,
                'cantidad_movimiento' => '1',
                'tipo_movimiento'     => '0',
                'motivo_movimiento'   => 'Descarga QA Guzzle materia prima',
            ]);

            $this->assertEquals('success', $jsonDescarga['icono'] ?? null);
            $this->assertEquals('Movimiento registrado', $jsonDescarga['titulo'] ?? null);
            $cargado = false;
        } finally {
            if ($cargado) {
                self::$client->post('inventario', [
                    'headers'     => $this->headersAjax(),
                    'form_params' => [
                        'accion'              => 'registrarMovimientosMateriasPrimas',
                        'id_materia_prima'    => self::ID_MATERIA_PRIMA,
                        'cantidad_movimiento' => '1',
                        'tipo_movimiento'     => '0',
                        'motivo_movimiento'   => 'Reversion QA Guzzle materia prima',
                    ],
                ]);
            }
        }
    }
}