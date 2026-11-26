<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendBitacoraTest extends Unit
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

        $htmlVista = (string) $client->get('bitacora')->getBody();
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

    public function testGetVistaBitacoraAutorizado(): void
    {
        $res = self::$client->get('bitacora');
        $html = (string) $res->getBody();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error', $html);
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringContainsString('TOKEN_CSRF', $html);
    }

    public function testListarBitacoraEstructuraValida(): void
    {
        $json = $this->postAjax('bitacora', ['accion' => 'listar']);

        $this->assertNotEmpty($json);
        $registro = $json[0];

        $this->assertArrayHasKey('id_bitacora', $registro);
        $this->assertArrayHasKey('nombre_usuario', $registro);
        $this->assertArrayHasKey('apellido_usuario', $registro);
        $this->assertArrayHasKey('modulo_bitacora', $registro);
        $this->assertArrayHasKey('accion', $registro);
        $this->assertArrayHasKey('fecha_bitacora', $registro);
        $this->assertArrayHasKey('resultado_bitacora', $registro);
        $this->assertArrayHasKey('ip_dispositivo', $registro);
        $this->assertArrayHasKey('cambios_efectuados', $registro);
    }

    public function testListarBitacoraOrdenDescendente(): void
    {
        $json = $this->postAjax('bitacora', ['accion' => 'listar']);

        $this->assertGreaterThanOrEqual(2, count($json));
        $this->assertGreaterThan((int) $json[1]['id_bitacora'], (int) $json[0]['id_bitacora']);
    }

    public function testCambiosEfectuadosDecodificadosComoArreglo(): void
    {
        $json = $this->postAjax('bitacora', ['accion' => 'listar']);

        foreach ($json as $item) {
            if (!empty($item['cambios_efectuados'])) {
                $this->assertIsArray(
                    $item['cambios_efectuados'],
                    'cambios_efectuados debe llegar decodificado como arreglo.'
                );
                return;
            }
        }

        $this->assertNotEmpty($json);
    }

    public function testRechazarAccionNoReconocidaOInmutable(): void
    {
        $json = $this->postAjax('bitacora', ['accion' => 'eliminar'], 400);

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

        $res = $clienteAnonimo->post('bitacora', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => ['accion' => 'listar'],
        ]);

        $json = json_decode((string) $res->getBody(), true);
        $this->assertFalse(is_array($json) && isset($json[0]['id_bitacora']));
    }
}