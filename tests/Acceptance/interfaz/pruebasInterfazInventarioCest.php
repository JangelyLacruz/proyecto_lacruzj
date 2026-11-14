<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazInventarioCest {

  public function verificarElementosInventario(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->setCookie('TEMP', 'true');

    $I->amOnPage('/usuarios/login');
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');
    $I->click('form.formularioAjax.login button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForJS(
      "return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard') || window.location.href.includes('/home');",
      30
    );
    $I->amOnPage('/inventario');
    $I->seeInCurrentUrl('/inventario');

    $elementosBase = [
      '#tab-productos',
      '#tab-materiasPrimas',
      '#tablaProductos',
      '#tablaMateriasPrimas',
      '#graficaStockProductos',
      '#graficaStockMateriasPrimas',
      '.modalRegistrarAnomaliaProducto',
      '.modalRegistrarAnomaliaMateriaPrima',
      '.modalVerMovimientosProducto',
      '.modalVerMovimientosMateriaPrima',
      '.modalFiltrosReporte',
      '#formFiltrosReporte input[name="fecha_desde"]',
      '#formFiltrosReporte input[name="fecha_hasta"]'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }

    $I->waitForElementVisible('.botonImprimirES', 15);
    $I->click('.botonImprimirES');
    $I->waitForElementVisible('.modalFiltrosReporte', 10);

    $fechaDesde = date('Y-m-01');
    $fechaHasta = date('Y-m-d');
    $I->executeJS("
        document.querySelector('#formFiltrosReporte input[name=\"fecha_desde\"]').value = '$fechaDesde';
        document.querySelector('#formFiltrosReporte input[name=\"fecha_hasta\"]').value = '$fechaHasta';
    ");
    $I->seeInField('#formFiltrosReporte input[name="fecha_desde"]', $fechaDesde);
    $I->seeInField('#formFiltrosReporte input[name="fecha_hasta"]', $fechaHasta);

    $I->click('.modalFiltrosReporte button[data-bs-dismiss="modal"]');
    $I->waitForElementNotVisible('.modalFiltrosReporte', 10);
    $I->waitForElementVisible('.botonRegistrarCarga', 10);
    $I->click('.botonRegistrarCarga');
    $I->waitForElementVisible('.modalRegistrarAnomaliaProducto', 10);

    $I->selectOption('.modalRegistrarAnomaliaProducto select[name="tipo_movimiento"]', '1');
    $I->fillField('.modalRegistrarAnomaliaProducto input[name="cantidad_movimiento"]', '10');
    $I->fillField('.modalRegistrarAnomaliaProducto textarea[name="motivo_movimiento"]', 'Verificacion de interfaz');
    $I->seeInField('.modalRegistrarAnomaliaProducto input[name="cantidad_movimiento"]', '10');

    $I->click('.modalRegistrarAnomaliaProducto button[data-bs-dismiss="modal"]');
    $I->waitForElementNotVisible('.modalRegistrarAnomaliaProducto', 10);
    $I->executeJS("
      let tabTrigger = document.querySelector('#tab-materiasPrimas');
      let tabInstance = bootstrap.Tab.getOrCreateInstance(tabTrigger);
      tabInstance.show();
    ");
    $I->waitForElementVisible('#tablaMateriasPrimas', 10);
    $I->waitForJS("return document.querySelectorAll('#tablaMateriasPrimas tbody tr').length > 0;", 15);
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