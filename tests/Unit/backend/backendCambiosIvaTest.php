<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendCambiosIvaTest extends Unit
{
    private const BASE_URI = 'http://localhost/proyecto-lacruz-j/';

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

        $htmlVista = (string) $client->get('cambiosIva')->getBody();
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

    private function postAjax(string $ruta, array $campos, int $statusEsperado = 200): array
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
        $this->assertEquals($statusEsperado, $status, "Esperaba HTTP {$statusEsperado}, recibió {$status}. Body: " . substr($body, 0, 300));

        $json = json_decode($body, true);
        $this->assertIsArray($json, 'Respuesta no es JSON válido: ' . substr($body, 0, 300));

        return $json;
    }

    public function testGetVistaCambiosIvaAutorizado(): void
    {
        $res = self::$client->get('cambiosIva');
        $html = (string) $res->getBody();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error', $html);
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringContainsString('TOKEN_CSRF', $html);
    }

    public function testListarCambiosIva(): void
    {
        $json = $this->postAjax('cambiosIva', ['accion' => 'listar']);

        $this->assertNotEmpty($json);
        $this->assertArrayHasKey('monto_cambio_iva', $json[0]);
    }

    public function testConsultarIvaActual(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'       => 'listar',
            'tipoConsulta' => 'ivaActual',
        ]);

        $this->assertArrayHasKey('id_cambio_iva', $json);
        $this->assertArrayHasKey('monto_cambio_iva', $json);
    }

    public function testConsultarCambioIvaPorIdInexistente(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'        => 'listar',
            'id_cambio_iva' => '999999999',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Dato no encontrado', $json['titulo'] ?? null);
    }

    public function testActualizarIvaVacio(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'           => 'actualizar',
            'monto_cambio_iva' => '',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testActualizarIvaIgualACero(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'           => 'actualizar',
            'monto_cambio_iva' => '0',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testActualizarIvaSuperiorACien(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'           => 'actualizar',
            'monto_cambio_iva' => '101',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Porcentaje superior al 100%', $json['titulo'] ?? null);
    }

    public function testActualizarIvaFormatoInvalido(): void
    {
        $json = $this->postAjax('cambiosIva', [
            'accion'           => 'actualizar',
            'monto_cambio_iva' => 'iva_invalido',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testAccionNoReconocida(): void
    {
        $json = $this->postAjax('cambiosIva', ['accion' => 'accionInexistenteQA'], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
        $this->assertEquals('Acción no reconocida', $json['titulo'] ?? null);
    }

    public function testBloqueoPostSinSesionActiva(): void
    {
        $clienteAnonimo = new Client([
            'base_uri'        => self::BASE_URI,
            'http_errors'     => false,
            'allow_redirects' => false,
            'timeout'         => 10,
        ]);

        $res = $clienteAnonimo->post('cambiosIva', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => ['accion' => 'listar'],
        ]);

        $json = json_decode((string) $res->getBody(), true);
        $this->assertFalse(is_array($json) && isset($json[0]['monto_cambio_iva']));
    }

    public function testActualizarIvaValidoYVerificarActual(): void
    {
        $ivaPrevio = $this->postAjax('cambiosIva', [
            'accion'       => 'listar',
            'tipoConsulta' => 'ivaActual',
        ]);
        $montoOriginal = (float) ($ivaPrevio['monto_cambio_iva'] ?? 16);
        $montoPrueba = (abs($montoOriginal - 16.25) < 0.01) ? '16.50' : '16.25';

        try {
            $jsonAct = $this->postAjax('cambiosIva', [
                'accion'           => 'actualizar',
                'monto_cambio_iva' => $montoPrueba,
            ]);

            $this->assertEquals('success', $jsonAct['icono'] ?? null);
            $this->assertEquals('Valor Actualizado', $jsonAct['titulo'] ?? null);

            $ivaNuevo = $this->postAjax('cambiosIva', [
                'accion'       => 'listar',
                'tipoConsulta' => 'ivaActual',
            ]);
            $this->assertEquals((float) $montoPrueba, (float) ($ivaNuevo['monto_cambio_iva'] ?? 0));
        } finally {
            if ($montoOriginal > 0 && $montoOriginal <= 100) {
                self::$client->post('cambiosIva', [
                    'headers'     => $this->headersAjax(),
                    'form_params' => [
                        'accion'           => 'actualizar',
                        'monto_cambio_iva' => number_format($montoOriginal, 2, '.', ''),
                    ],
                ]);
            }
        }
    }
}