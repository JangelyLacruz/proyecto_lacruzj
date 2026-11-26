<?php

declare(strict_types=1);

namespace Tests\Unit\backend;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

final class backendPermisosTest extends Unit
{
    private const BASE_URI = 'http://localhost/proyecto-lacruz-j/';

    private static ?Client $client = null;
    private static string $csrfToken = '';

    protected function _before(): void
    {
        if (self::$client !== null) {
            return;
        }

        $cookies = new CookieJar();$cookies->setCookie(new SetCookie([
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

        $resLogin =$client->post('usuarios/login', [
            'headers'     => $this->headersAjax(),
            'form_params' => [
                'accion'              => 'iniciarSesion',
                'usuario_usuario'     => 'Ander123',
                'contrasena1_usuario' => 'David01*',
            ],
        ]);

        $this->assertEquals(200,$resLogin->getStatusCode(), 'Falló HTTP del login.');
        $jsonLogin = json_decode((string)$resLogin->getBody(), true);

        $loginValido = ($jsonLogin['tipo'] ?? null) === 'redireccionar';
        if (!$loginValido) {
            $loginValido = ($jsonLogin['icono'] ?? null) === 'success';
        }
        $this->assertTrue($loginValido,
            'Falló el inicio de sesión: ' . json_encode($jsonLogin, JSON_UNESCAPED_UNICODE)
        );

        $htmlVista = (string)$client->get('permisos')->getBody();
        preg_match('/<meta[^>]*name=["\']TOKEN_CSRF["\'][^>]*content=["\']([^"\']+)["\']/i', $htmlVista,$m);
        $this->assertNotEmpty($m[1] ?? '', 'No se pudo extraer el TOKEN_CSRF.');

        self::$client =$client;
        self::$csrfToken =$m[1];
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

    private function postAjax(string $ruta, array $campos, int$statusEsperado = 200): array
    {
        $res = self::$client->post($ruta, [
            'headers'     => $this->headersAjax(),
            'form_params' => $campos,
        ]);

        $status =$res->getStatusCode();
        $body = (string)$res->getBody();

        $this->assertStringNotContainsString('Fatal error', $body);$this->assertStringNotContainsString('Parse error', $body);$this->assertStringNotContainsString('Uncaught', $body);$this->assertEquals($statusEsperado,$status, "Esperaba HTTP {$statusEsperado}, recibió {$status}. Body: " . substr($body, 0, 300));

        $json = json_decode($body, true);
        $this->assertIsArray($json, 'Respuesta no es JSON válido: ' . substr($body, 0, 300));

        return $json;
    }

    public function testGetVistaPermisosAutorizado(): void
    {
        $res = self::$client->get('permisos');
        $html = (string)$res->getBody();

        $this->assertEquals(200,$res->getStatusCode());
        $this->assertStringNotContainsString('Fatal error',$html);
        $this->assertStringNotContainsString('Warning:',$html);
        $this->assertStringContainsString('TOKEN_CSRF',$html);
    }

    public function testListarPermisos(): void
    {
        $json =$this->postAjax('permisos', ['accion' => 'listar']);

        $this->assertNotEmpty($json);
        $this->assertArrayHasKey('id_permiso',$json[0]);
        $this->assertArrayHasKey('nombre_permiso',$json[0]);
    }

    public function testRegistrarNombreMenorAlMinimo(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'         => 'registrar',
            'nombre_permiso' => 'AB',
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Campo de nombre del permiso muy corto',$json['titulo'] ?? null);
    }

    public function testRegistrarNombreMayorAlMaximo(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'         => 'registrar',
            'nombre_permiso' => str_repeat('A', 51),
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Campo de nombre del permiso muy largo',$json['titulo'] ?? null);
    }

    public function testRegistrarNombreVacio(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'         => 'registrar',
            'nombre_permiso' => '',
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Campo de nombre del permiso obligatorio',$json['titulo'] ?? null);
    }

    public function testRegistrarNombreConCaracteresInvalidos(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'         => 'registrar',
            'nombre_permiso' => 'Permiso@Invalido*{};',
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Formato de nombre del permiso inválido', $json['titulo'] ?? null);
    }

    public function testSeleccionarUnoIdInexistente(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'     => 'seleccionarUno',
            'id_permiso' => '999999999',
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Dato no encontrado',$json['titulo'] ?? null);
    }

    public function testSeleccionarUnoIdFormatoInvalido(): void
    {
        $json =$this->postAjax('permisos', [
            'accion'     => 'seleccionarUno',
            'id_permiso' => 'abc',
        ], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
        $this->assertEquals('Formato de id del permiso inválido', $json['titulo'] ?? null);
    }

    public function testAccionNoReconocida(): void
    {
        $json =$this->postAjax('permisos', ['accion' => 'accionInexistenteQA'], 400);

        $this->assertEquals('error',$json['icono'] ?? null);
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

        $res =$clienteAnonimo->post('permisos', [
            'headers'     => ['X-ES-AJAX' => 'true'],
            'form_params' => ['accion' => 'listar'],
        ]);

        $json = json_decode((string) $res->getBody(), true);$this->assertFalse(is_array($json) && isset($json[0]['id_permiso']));
    }

    public function testFlujoCompletoCrudPermisos(): void
    {
        $sufijo = rand(1000, 9999);$nombreOriginal = "QA Guzzle {$sufijo}";
        $nombreEditado  = "QA Guzzle Edit {$sufijo}";
        $idPermiso = null;
        $eliminado = false;

        try {
            $jsonReg =$this->postAjax('permisos', [
                'accion'         => 'registrar',
                'nombre_permiso' => $nombreOriginal,
            ]);
            $this->assertEquals('success',$jsonReg['icono'] ?? null);
            $this->assertEquals('Permiso registrado',$jsonReg['titulo'] ?? null);

            $jsonDup =$this->postAjax('permisos', [
                'accion'         => 'registrar',
                'nombre_permiso' => $nombreOriginal,
            ], 400);
            $this->assertEquals('error',$jsonDup['icono'] ?? null);
            $this->assertEquals('Valor de nombre del permiso duplicado',$jsonDup['titulo'] ?? null);

            $lista =$this->postAjax('permisos', ['accion' => 'listar']);
            $encontrados = array_values(array_filter($lista,
                fn(array $p): bool => ($p['nombre_permiso'] ?? '') ===$nombreOriginal
            ));
            $this->assertNotEmpty($encontrados, 'El permiso registrado no apareció en listar.');
            $idPermiso = (string)$encontrados[0]['id_permiso'];

            $jsonUno =$this->postAjax('permisos', [
                'accion'     => 'seleccionarUno',
                'id_permiso' => $idPermiso,
            ]);
            $this->assertEquals($idPermiso, (string) ($jsonUno['id_permiso'] ?? ''));$this->assertEquals($nombreOriginal,$jsonUno['nombre_permiso'] ?? null);

            $jsonAct =$this->postAjax('permisos', [
                'accion'         => 'actualizar',
                'id_permiso'     => $idPermiso,
                'nombre_permiso' => $nombreEditado,
            ]);
            $this->assertEquals('success',$jsonAct['icono'] ?? null);
            $this->assertEquals('Permiso actualizado',$jsonAct['titulo'] ?? null);

            $jsonEli =$this->postAjax('permisos', [
                'accion'     => 'eliminar',
                'id_permiso' => $idPermiso,
            ]);
            $this->assertEquals('success',$jsonEli['icono'] ?? null);
            $this->assertEquals('Permiso eliminado',$jsonEli['titulo'] ?? null);
            $eliminado = true;

            $jsonEli2 =$this->postAjax('permisos', [
                'accion'     => 'eliminar',
                'id_permiso' => $idPermiso,
            ], 400);
            $this->assertEquals('error',$jsonEli2['icono'] ?? null);
        } finally {
            if ($idPermiso !== null && !$eliminado) {
                self::$client->post('permisos', [
                    'headers'     => $this->headersAjax(),
                    'form_params' => ['accion' => 'eliminar', 'id_permiso' => $idPermiso],
                ]);
            }
        }
    }
}