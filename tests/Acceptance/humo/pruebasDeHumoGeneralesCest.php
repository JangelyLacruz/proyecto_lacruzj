<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasDeHumoGeneralesCest {

  public function _before(AcceptanceTester $I): void {
    if ($I->loadSessionSnapshot('sesion_iniciada')) return;

    $I->amOnPage('/');
    $I->setCookie('TEMP', 'true');
    $I->amOnPage('/usuarios/login');

    // Verificamos que el formulario AJAX y sus campos existen en pantalla
    $I->seeElement('form.formularioAjax.login');
    $I->seeElement('input[name="usuario_usuario"]');
    $I->seeElement('input[name="contrasena1_usuario"]');

    // Llenamos las credenciales reales
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');

    $I->click('button[type="submit"]');
    $I->click('.swal2-confirm.swal2-styled');

    $I->waitForJS("return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');", 10);
    $I->saveSessionSnapshot('sesion_iniciada');
  }
  public function verificarRedireccionDelLoginAlDashboard(AcceptanceTester $I): void {
    $I->seeInCurrentUrl('/reportes');
    $I->dontSeeElement('form.formularioAjax.login');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaInicio(AcceptanceTester $I): void {
    $I->amOnPage('/home');
    $I->seeInCurrentUrl('/home');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
    $I->see('Clientes', '.card.step-card.h-100');
    $I->see('Inventario', '.card.step-card.h-100');
    $I->see('Órdenes de entrega', '.card.step-card.h-100');
  }
  public function verificarDisponibilidadRutaClientes(AcceptanceTester $I): void {
    $I->amOnPage('/clientes');
    $I->seeInCurrentUrl('/clientes');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaOrdenesEntrega(AcceptanceTester $I): void {
    $I->amOnPage('/ordenesEntregasPresupuestos');
    $I->seeInCurrentUrl('/ordenesEntregasPresupuestos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaPagos(AcceptanceTester $I): void {
    $I->amOnPage('/pagos');
    $I->seeInCurrentUrl('/pagos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaInventario(AcceptanceTester $I): void {
    $I->amOnPage('/inventario');
    $I->seeInCurrentUrl('/inventario');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaCompras(AcceptanceTester $I): void {
    $I->amOnPage('/compras');
    $I->seeInCurrentUrl('/compras');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaOrdenesServicios(AcceptanceTester $I): void {
    $I->amOnPage('/ordenesServicios');
    $I->seeInCurrentUrl('/ordenesServicios');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaProducciones(AcceptanceTester $I): void {
    $I->amOnPage('/producciones');
    $I->seeInCurrentUrl('/producciones');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaReportes(AcceptanceTester $I): void {
    $I->amOnPage('/reportes');
    $I->seeInCurrentUrl('/reportes');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaProductos(AcceptanceTester $I): void {
    $I->amOnPage('/productos');
    $I->seeInCurrentUrl('/productos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaMateriasPrimas(AcceptanceTester $I): void {
    $I->amOnPage('/materiasPrimas');
    $I->seeInCurrentUrl('/materiasPrimas');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaServicios(AcceptanceTester $I): void {
    $I->amOnPage('/servicios');
    $I->seeInCurrentUrl('/servicios');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaProveedores(AcceptanceTester $I): void {
    $I->amOnPage('/proveedores');
    $I->seeInCurrentUrl('/proveedores');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaPresentaciones(AcceptanceTester $I): void {
    $I->amOnPage('/presentaciones');
    $I->seeInCurrentUrl('/presentaciones');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaCambiosMonedas(AcceptanceTester $I): void {
    $I->amOnPage('/monedas/cambios-monedas');
    $I->seeInCurrentUrl('/monedas/cambios-monedas');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaRepartidores(AcceptanceTester $I): void {
    $I->amOnPage('/repartidores');
    $I->seeInCurrentUrl('/repartidores');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaCategoriasProductos(AcceptanceTester $I): void {
    $I->amOnPage('/categoriasProductos');
    $I->seeInCurrentUrl('/categoriasProductos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaUnidadesMedidas(AcceptanceTester $I): void {
    $I->amOnPage('/unidadesMedidas');
    $I->seeInCurrentUrl('/unidadesMedidas');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaMetodosPago(AcceptanceTester $I): void {
    $I->amOnPage('/metodos-pago');
    $I->seeInCurrentUrl('/metodos-pago');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaMonedas(AcceptanceTester $I): void {
    $I->amOnPage('/monedas');
    $I->seeInCurrentUrl('/monedas');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaCambiosIva(AcceptanceTester $I): void {
    $I->amOnPage('/cambiosIva');
    $I->seeInCurrentUrl('/cambiosIva');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaEmpresasEnvios(AcceptanceTester $I): void {
    $I->amOnPage('/empresasEnvios');
    $I->seeInCurrentUrl('/empresasEnvios');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaSucursalesEmpresasEnvios(AcceptanceTester $I): void {
    $I->amOnPage('/sucursalesEmpresasEnvios');
    $I->seeInCurrentUrl('/sucursalesEmpresasEnvios');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaBancos(AcceptanceTester $I): void {
    $I->amOnPage('/bancos');
    $I->seeInCurrentUrl('/bancos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaRutas(AcceptanceTester $I): void {
    $I->amOnPage('/rutas');
    $I->seeInCurrentUrl('/rutas');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaUsuarios(AcceptanceTester $I): void {
    $I->amOnPage('/usuarios');
    $I->seeInCurrentUrl('/usuarios');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaAccesos(AcceptanceTester $I): void {
    $I->amOnPage('/accesos');
    $I->seeInCurrentUrl('/accesos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaRoles(AcceptanceTester $I): void {
    $I->amOnPage('/roles');
    $I->seeInCurrentUrl('/roles');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaModulos(AcceptanceTester $I): void {
    $I->amOnPage('/modulos');
    $I->seeInCurrentUrl('/modulos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaPermisos(AcceptanceTester $I): void {
    $I->amOnPage('/permisos');
    $I->seeInCurrentUrl('/permisos');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaBitacora(AcceptanceTester $I): void {
    $I->amOnPage('/bitacora');
    $I->seeInCurrentUrl('/bitacora');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
  public function verificarDisponibilidadRutaPreguntasSeguridad(AcceptanceTester $I): void {
    $I->amOnPage('/preguntas-seguridad');
    $I->seeInCurrentUrl('/preguntas-seguridad');
    $I->dontSee('Fatal error');
    $I->dontSee('Warning');
    $I->wait(0.5);
  }
}
  