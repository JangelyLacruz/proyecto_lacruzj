<?php

namespace Tests\Unit;

use ArrayAccess;
use Codeception\Test\Unit;
use Codeception\Stub;
use Codeception\Stub\Expected;
use src\modelos\exportarBDModelo;
use src\config\connect\conexion;
use PDO;
use PDOStatement;
use ReflectionClass;

require_once dirname(__DIR__, 3) . '/src/config/const.php';

if (!class_exists(__NAMESPACE__ . '\FilaFakeBD')) {
    class FilaFakeBD implements ArrayAccess
    {
        private array $datos = [
            'id_acceso'                  => 1,
            'id_modulo'                  => 1,
            'nombre_modulo'              => 'clientes',
            'id_permiso'                 => 1,
            'nombre_permiso'             => 'listar',
            'nombre_rol'                 => 'Administrador',
            'rif_cedula_cliente'         => 'V12345678',
            'razon_social_cliente'       => 'Cliente Prueba C.A.',
            'telefono_cliente'           => '04141234567',
            'correo_cliente'             => 'prueba@ejemplo.com',
            'direccion_cliente'          => 'Av. Principal',
            'id_icono_notificacion'      => 1,
            'id_tipo_notificacion'       => 2,
            'id_notificacion'            => 1,
            'id_accion_resagada_usuario' => 1,
            'id_bitacora'                => 1,
            'id_rol'                     => 1,
            'cedula_usuario'             => 'V15265756',
            'status'                     => 1,
        ];

        public function offsetExists(mixed $offset): bool { return true; }
        public function offsetGet(mixed $offset): mixed { return $this->datos[$offset] ?? 1; }
        public function offsetSet(mixed $offset, mixed $value): void { $this->datos[$offset] = $value; }
        public function offsetUnset(mixed $offset): void { unset($this->datos[$offset]); }
    }
}

class unitariaExportarBDTest extends Unit
{
    private array $archivosCreados = [];

    protected function _before(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['cedula'] = 'V15265756';
        $_SESSION['id_rol'] = 1;
        $_SESSION['rol'] = 1;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->inyectarPdoFakeEnConexion();
    }

    protected function _after(): void
    {
        foreach ($this->archivosCreados as $ruta) {
            if (is_file($ruta)) {
                @unlink($ruta);
            }
        }
        $this->archivosCreados = [];

        $ref = new ReflectionClass(conexion::class);
        foreach (['conexion', 'conexion2'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $prop = $ref->getProperty($propName);
                $prop->setAccessible(true);
                $prop->setValue(null, null);
            }
        }
    }

    private function crearPdoParaExportar(array $tablas, array $filasPorTabla): PDO
    {
        $stmtShowTables = $this->createMock(PDOStatement::class);
        $stmtShowTables->method('fetchAll')->willReturn($tablas);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('exec')->willReturn(0);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($stmtShowTables, $filasPorTabla) {
            if (str_contains($sql, 'SHOW TABLES')) {
                return $stmtShowTables;
            }

            if (preg_match('/SHOW CREATE TABLE `([^`]+)`/', $sql, $m)) {
                $tabla = $m[1];
                $stmtCreate = $this->createMock(PDOStatement::class);
                $stmtCreate->method('fetch')->willReturn([
                    'Table' => $tabla,
                    'Create Table' => "CREATE TABLE `{$tabla}` (`id` int NOT NULL)",
                ]);
                return $stmtCreate;
            }

            if (preg_match('/SELECT \* FROM `([^`]+)`/', $sql, $m)) {
                $tabla = $m[1];
                $filas = $filasPorTabla[$tabla] ?? [];
                $indice = 0;

                $stmtSelect = $this->createMock(PDOStatement::class);
                $stmtSelect->method('rowCount')->willReturn(count($filas));
                $stmtSelect->method('fetch')->willReturnCallback(function () use ($filas, &$indice) {
                    if ($indice >= count($filas)) {
                        return false;
                    }
                    return $filas[$indice++];
                });
                return $stmtSelect;
            }

            return $this->createMock(PDOStatement::class);
        });

        return $pdo;
    }

    private function inyectarPdoFakeEnConexion(): void
    {
        $filaUniversal = new FilaFakeBD();

        $stmtFake = $this->createMock(PDOStatement::class);
        $stmtFake->method('execute')->willReturn(true);
        $stmtFake->method('rowCount')->willReturn(1);
        $stmtFake->method('fetch')->willReturnCallback(
            fn($mode = PDO::FETCH_DEFAULT) => $mode === PDO::FETCH_COLUMN ? '1' : $filaUniversal
        );
        $stmtFake->method('fetchAll')->willReturnCallback(
            fn($mode = PDO::FETCH_DEFAULT) => $mode === PDO::FETCH_COLUMN ? ['V15265756'] : []
        );

        $pdoFake = $this->createMock(PDO::class);
        $pdoFake->method('prepare')->willReturn($stmtFake);
        $pdoFake->method('inTransaction')->willReturn(true);
        $pdoFake->method('commit')->willReturn(true);
        $pdoFake->method('rollBack')->willReturn(true);
        $pdoFake->method('lastInsertId')->willReturn('999');

        $ref = new ReflectionClass(conexion::class);
        foreach (['conexion', 'conexion2'] as $propName) {
            if ($ref->hasProperty($propName)) {
                $prop = $ref->getProperty($propName);
                $prop->setAccessible(true);
                $prop->setValue(null, $pdoFake);
            }
        }
    }

    public function testExportarBaseDatosPrincipalGeneraArchivoSql(): void
    {
        $pdoExport = $this->crearPdoParaExportar(
            ['clientes'],
            [
                'clientes' => [
                    ['id' => '1', 'nombre' => 'Cliente Uno'],
                ],
            ]
        );

        /** @var exportarBDModelo $modelo */
        $modelo = Stub::make(exportarBDModelo::class, [
            'conectar' => Expected::once($pdoExport),
        ], $this);

        $rutaWeb = $modelo->exportar('proyecto_lacruz');

        $this->assertStringStartsWith('src/modelos/exports/backup_lacruz_', $rutaWeb);
        $this->assertStringEndsWith('.sql', $rutaWeb);

        $rutaFisica = dirname(__DIR__, 3) . '/' . str_replace('/', DIRECTORY_SEPARATOR, $rutaWeb);
        $this->archivosCreados[] = $rutaFisica;

        $this->assertFileExists($rutaFisica);
        $contenido = file_get_contents($rutaFisica);
        $this->assertNotFalse($contenido);
        $this->assertStringContainsString('CREATE TABLE `clientes`', $contenido);
        $this->assertStringContainsString("INSERT INTO `clientes` VALUES ('1', 'Cliente Uno');", $contenido);
    }

    public function testExportarBaseDatosSeguridadUsaPrefijoCorrecto(): void
    {
        $pdoExport = $this->crearPdoParaExportar(['roles'], []);

        /** @var exportarBDModelo $modelo */
        $modelo = Stub::make(exportarBDModelo::class, [
            'conectar' => Expected::once($pdoExport),
        ], $this);

        $rutaWeb = $modelo->exportar(' proyecto_lacruz_seguridad ');

        $this->assertStringStartsWith('src/modelos/exports/backup_lacruz_seguridad_', $rutaWeb);
        $this->assertStringEndsWith('.sql', $rutaWeb);

        $rutaFisica = dirname(__DIR__, 3) . '/' . str_replace('/', DIRECTORY_SEPARATOR, $rutaWeb);
        $this->archivosCreados[] = $rutaFisica;

        $this->assertFileExists($rutaFisica);
        $contenido = file_get_contents($rutaFisica);
        $this->assertNotFalse($contenido);
        $this->assertStringContainsString('CREATE TABLE `roles`', $contenido);
        $this->assertStringNotContainsString('INSERT INTO `roles`', $contenido);
    }
}