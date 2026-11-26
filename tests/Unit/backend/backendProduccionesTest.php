<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendProduccionesTest extends Unit
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

        $htmlVista = (string) $client->get('producciones')->getBody();
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
        $this->assertEquals(
            $statusEsperado,
            $status,
            "Esperaba HTTP {$statusEsperado}, recibió {$status}. Body: " . substr($body, 0, 300)
        );

        $json = json_decode($body, true);
        $this->assertIsArray($json, 'Respuesta no es JSON válido: ' . substr($body, 0, 300));

        return $json;
    }

    private function obtenerIdProduccionValida(): string
    {
        $lista = $this->postAjax('producciones', ['accion' => 'listar']);
        $this->assertNotEmpty($lista, 'No existen producciones registradas en la BD de pruebas.');
        return (string) $lista[0]['id_produccion'];
    }

    public function testGetVistaProduccionesAutorizado(): void
    {
        $res = self::$client->get('producciones');
        $html = (string) $res->getBody();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error', $html);
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringContainsString('TOKEN_CSRF', $html);
    }

    public function testListarProducciones(): void
    {
        $json = $this->postAjax('producciones', ['accion' => 'listar']);

        $this->assertNotEmpty($json);
        $this->assertArrayHasKey('id_produccion', $json[0]);
        $this->assertArrayHasKey('fecha_produccion', $json[0]);
    }

    public function testSeleccionarUnaProduccionValida(): void
    {
        $idProduccion = $this->obtenerIdProduccionValida();
        $json = $this->postAjax('producciones', [
            'accion'        => 'seleccionarUno',
            'id_produccion' => $idProduccion,
        ]);

        $this->assertEquals($idProduccion, $json['id_produccion'] ?? null);
        $this->assertArrayHasKey('detalles', $json);
        $this->assertIsArray($json['detalles']);
    }

    public function testSeleccionarUnaProduccionInexistente(): void
    {
        $json = $this->postAjax('producciones', [
            'accion'        => 'seleccionarUno',
            'id_produccion' => 'PROD-99999-99999-99',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testSeleccionarUnaProduccionFormatoInvalido(): void
    {
        $json = $this->postAjax('producciones', [
            'accion'        => 'seleccionarUno',
            'id_produccion' => 'ID_INVALIDO',
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testRegistrarProduccionSinProductos(): void
    {
        $json = $this->postAjax('producciones', [
            'accion'    => 'registrar',
            'productos' => json_encode([]),
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testRegistrarProduccionCantidadCero(): void
    {
        $json = $this->postAjax('producciones', [
            'accion'    => 'registrar',
            'productos' => json_encode([
                [
                    'id_producto'        => 'PROD-26150-00001-39',
                    'cantidad_producida' => '0',
                ],
            ]),
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testRegistrarProduccionProductoInexistente(): void
    {
        $json = $this->postAjax('producciones', [
            'accion'    => 'registrar',
            'productos' => json_encode([
                [
                    'id_producto'        => 'PROD-99999-99999-99',
                    'cantidad_producida' => '1',
                ],
            ]),
        ], 400);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testAccionNoReconocida(): void
    {
        $json = $this->postAjax('producciones', ['accion' => 'accionInexistenteQA'], 400);

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

        $res = $clienteAnonimo->post('producciones', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => ['accion' => 'listar'],
        ]);

        $json = json_decode((string) $res->getBody(), true);
        $this->assertFalse(is_array($json) && isset($json[0]['id_produccion']));
    }

    public function testRegistrarYActualizarProduccion(): void
    {
        $idProducto = 'PROD-26150-00001-39';

        $idExistente = $this->obtenerIdProduccionValida();
        $prodExistente = $this->postAjax('producciones', [
            'accion'        => 'seleccionarUno',
            'id_produccion' => $idExistente,
        ]);
        if (!empty($prodExistente['detalles'][0]['id_producto'])) {
            $idProducto = (string) $prodExistente['detalles'][0]['id_producto'];
        }

        // 1. Registrar producción con 1 unidad
        $jsonReg = $this->postAjax('producciones', [
            'accion'    => 'registrar',
            'productos' => json_encode([
                [
                    'id_producto'        => $idProducto,
                    'cantidad_producida' => '1',
                ],
            ]),
        ]);

        $this->assertEquals('success', $jsonReg['icono'] ?? null);
        $this->assertEquals('Éxito en el registro', $jsonReg['titulo'] ?? null);

        preg_match('/(PROD-\d{5}-\d{5}-\d{2})/', $jsonReg['texto'] ?? '', $m);
        $this->assertNotEmpty($m[1] ?? '', 'No se pudo extraer el ID de la producción registrada.');
        $idNueva = $m[1];

        // 2. Verificar detalle registrado
        $jsonUno = $this->postAjax('producciones', [
            'accion'        => 'seleccionarUno',
            'id_produccion' => $idNueva,
        ]);
        $this->assertEquals($idNueva, $jsonUno['id_produccion'] ?? null);
        $this->assertNotEmpty($jsonUno['detalles']);

        // 3. Actualizar la producción recién creada
        $jsonAct = $this->postAjax('producciones', [
            'accion'        => 'actualizar',
            'id_produccion' => $idNueva,
            'productos'     => json_encode([
                [
                    'id_producto'        => $idProducto,
                    'cantidad_producida' => '1',
                ],
            ]),
        ]);

        $this->assertEquals('success', $jsonAct['icono'] ?? null);
        $this->assertEquals('Éxito en la actualización', $jsonAct['titulo'] ?? null);
    }
}