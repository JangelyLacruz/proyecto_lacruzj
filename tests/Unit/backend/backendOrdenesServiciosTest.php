<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendOrdenesServiciosTest extends Unit
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

        $htmlVista = (string) $client->get('ordenesServicios')->getBody();
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

    private function obtenerListadoOrdenes(): array
    {
        $lista = $this->postAjax('ordenesServicios', ['accion' => 'listar']);
        $this->assertNotEmpty($lista, 'No existen órdenes de servicio registradas en la BD de pruebas.');
        return $lista;
    }

    public function testGetVistaOrdenesServiciosAutorizado(): void
    {
        $res = self::$client->get('ordenesServicios');
        $html = (string) $res->getBody();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error', $html);
        $this->assertStringNotContainsString('Warning:', $html);
        $this->assertStringContainsString('TOKEN_CSRF', $html);
    }

    public function testListarOrdenesServicios(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $orden = $lista[0];

        $this->assertArrayHasKey('id_servicio_factura', $orden);
        $this->assertArrayHasKey('status_orden', $orden);
        $this->assertArrayHasKey('nombre_servicio', $orden);
        $this->assertArrayHasKey('esta_retrasado', $orden);
        $this->assertArrayHasKey('url_direccion', $orden);
    }

    public function testConsultarOrdenServicioIndividualValida(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $idOrden = (string) $lista[0]['id_servicio_factura'];

        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'listar',
            'id_servicio_factura' => $idOrden,
        ]);

        $this->assertEquals($idOrden, (string) ($json['id_servicio_factura'] ?? ''));
        $this->assertArrayHasKey('precio_unitario_real', $json);
        $this->assertArrayHasKey('subtotal_real', $json);
    }

    public function testConsultarOrdenServicioIdInexistente(): void
    {
        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'listar',
            'id_servicio_factura' => '999999999',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testConsultarOrdenServicioIdFormatoInvalido(): void
    {
        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'listar',
            'id_servicio_factura' => 'abc',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testActualizarOrdenEstadoFormatoInvalido(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $idOrden = (string) $lista[0]['id_servicio_factura'];

        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'actualizar',
            'id_servicio_factura' => $idOrden,
            'status'              => '99',
        ], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testActualizarOrdenTransicionNoPermitida(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $idOrden = (string) $lista[0]['id_servicio_factura'];

        // El estado 9 nunca pertenece a las transiciones permitidas [1, 2, 4] ni a []
        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'actualizar',
            'id_servicio_factura' => $idOrden,
            'status'              => '9',
        ], [200, 400]);

        $this->assertEquals('warning', $json['icono'] ?? null);
        $this->assertEquals('Transición no permitida', $json['titulo'] ?? null);
    }

    public function testActualizarOrdenNoPermiteCambiarFechaAlEjecutar(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $ordenEditable = null;

        foreach ($lista as $item) {
            if (in_array((int) $item['status_orden'], [1, 3], true)) {
                $ordenEditable = $item;
                break;
            }
        }

        if ($ordenEditable === null) {
            $this->markTestSkipped('No hay órdenes en estado Pendiente (1) o Retrasada (3) para validar bloqueo de fecha.');
        }

        $json = $this->postAjax('ordenesServicios', [
            'accion'              => 'actualizar',
            'id_servicio_factura' => (string) $ordenEditable['id_servicio_factura'],
            'status'              => '2',
            'fecha_ejecucion'     => '2026-12-20',
        ], [200, 400]);

        $this->assertEquals('warning', $json['icono'] ?? null);
        $this->assertEquals('Acción no permitida', $json['titulo'] ?? null);
    }

    public function testAccionNoReconocida(): void
    {
        $json = $this->postAjax('ordenesServicios', ['accion' => 'accionInexistenteQA'], [400]);

        $this->assertEquals('error', $json['icono'] ?? null);
    }

    public function testBloqueoPostSinSesionActiva(): void
    {
        $clienteAnonimo = new Client([
            'base_uri'        => self::BASE_URI,
            'http_errors'     => false,
            'allow_redirects' => false,
            'timeout'         => 10,
        ]);

        $res = $clienteAnonimo->post('ordenesServicios', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => ['accion' => 'listar'],
        ]);

        $json = json_decode((string) $res->getBody(), true);
        $this->assertFalse(is_array($json) && isset($json[0]['id_servicio_factura']));
    }

    public function testReprogramarFechaOrdenPendienteYRestaurar(): void
    {
        $lista = $this->obtenerListadoOrdenes();
        $ordenEditable = null;

        foreach ($lista as $item) {
            if (in_array((int) $item['status_orden'], [1, 3], true)) {
                $ordenEditable = $item;
                break;
            }
        }

        if ($ordenEditable === null) {
            $this->markTestSkipped('No hay órdenes en estado Pendiente (1) o Retrasada (3) para reprogramar.');
        }

        $idOrden = (string) $ordenEditable['id_servicio_factura'];
        $fechaOriginal = date('Y-m-d', strtotime((string) $ordenEditable['fecha_ejecucion']));
        $fechaPrueba = ($fechaOriginal === '2026-12-28') ? '2026-12-29' : '2026-12-28';

        try {
            $jsonAct = $this->postAjax('ordenesServicios', [
                'accion'              => 'actualizar',
                'id_servicio_factura' => $idOrden,
                'status'              => '1',
                'fecha_ejecucion'     => $fechaPrueba,
            ]);

            $this->assertEquals('success', $jsonAct['icono'] ?? null);
            $this->assertEquals('Orden actualizada', $jsonAct['titulo'] ?? null);
        } finally {
            self::$client->post('ordenesServicios', [
                'headers'     => $this->headersAjax(),
                'form_params' => [
                    'accion'              => 'actualizar',
                    'id_servicio_factura' => $idOrden,
                    'status'              => '1',
                    'fecha_ejecucion'     => $fechaOriginal,
                ],
            ]);
        }
    }
}