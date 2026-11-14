<?php

declare(strict_types=1);

namespace Tests\Acceptance\aceptacion;

use Tests\Support\AcceptanceTester;

final class pruebasAceptacionInventarioCest {

  public function verificarFlujoCompletoInventario(AcceptanceTester $I): void {

    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->setCookie('TEMP', 'true');

    $I->amOnPage('/usuarios/login');
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');
    $I->click('form.formularioAjax.login button[type="submit"]');

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForJS(
      "return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');",30);

    $I->amOnPage('/inventario');
    $I->seeInCurrentUrl('/inventario');
    $I->waitForElementVisible('#tablaProductos', 15);
    $I->wait(2); 

    $I->waitForElementVisible('.botonVerES', 15);
    $I->click('.botonVerES');

    $I->waitForElementVisible('.modalVerMovimientosProducto', 10);
    $I->wait(2);
    $I->click('.modalVerMovimientosProducto button[data-bs-dismiss="modal"]');
    $I->waitForElementNotVisible('.modalVerMovimientosProducto', 10);
    $this->forzarCierreModales($I);
    $I->waitForElementVisible('.botonRegistrarCarga', 10);
    $I->click('.botonRegistrarCarga');

    $I->waitForElementVisible('.modalRegistrarAnomaliaProducto', 10);

    $I->waitForJS("
      let sel = document.querySelector('.modalRegistrarAnomaliaProducto select.selectPresentacionAnomalia');
      return sel && sel.options.length > 1;
    ", 15);

    $I->executeJS("
      let sel = document.querySelector('.modalRegistrarAnomaliaProducto select.selectPresentacionAnomalia');
      let opc = Array.from(sel.options).find(o => o.value !== '');
      if (opc) {
        sel.value = opc.value;
        sel.dispatchEvent(new Event('change', { bubbles: true }));
      }
    ");

    $I->selectOption('.modalRegistrarAnomaliaProducto select[name="tipo_movimiento"]', '1'); // Carga (+)
    $I->fillField('.modalRegistrarAnomaliaProducto input[name="cantidad_movimiento"]', '5');
    $I->fillField('.modalRegistrarAnomaliaProducto textarea[name="motivo_movimiento"]', 'Prueba automatizada Codeception de reposición anómala');

    $I->click('.modalRegistrarAnomaliaProducto button[type="submit"]');

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');

    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 15);
    $I->wait(1);
    $this->forzarCierreModales($I);
    $I->executeJS("
      let tabTrigger = document.querySelector('#tab-materiasPrimas');
      let tabInstance = bootstrap.Tab.getOrCreateInstance(tabTrigger);
      tabInstance.show();
    ");

    $I->waitForElementVisible('#tablaMateriasPrimas', 10);
    $I->waitForJS("
      return document.querySelectorAll('#tablaMateriasPrimas tbody tr').length > 0;
    ", 15);

    $I->seeElement('#tablaMateriasPrimas');
  }

  private function forzarCierreModales(AcceptanceTester $I): void {
    $I->executeJS("
      let swal = document.querySelector('.swal2-container');
      if (swal) swal.remove();
      document.body.classList.remove('swal2-shown', 'swal2-height-auto');
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
      let modales = document.querySelectorAll('.modalRegistrarAnomaliaProducto, .modalRegistrarAnomaliaMateriaPrima, .modalVerMovimientosProducto, .modalVerMovimientosMateriaPrima, .modalFiltrosReporte');
      modales.forEach(modal => { modal.classList.remove('show'); modal.style.display = 'none'; });
    ");
  }

  public function _after(AcceptanceTester $I): void {
    $I->executeJS("
      let swal = document.querySelector('.swal2-container');
      if (swal) swal.remove();
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open', 'swal2-shown', 'swal2-height-auto');
      let btnLogout = document.querySelector('.btnCerrarSession');
      if (btnLogout) btnLogout.click();
    ");
    $I->wait(1);
    $I->executeJS("
      let btnConfirm = document.querySelector('.swal2-confirm.swal2-styled');
      if (btnConfirm) btnConfirm.click();
    ");
    $I->wait(1);
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->executeJS("window.sessionStorage.clear(); window.localStorage.clear();");
  }
}