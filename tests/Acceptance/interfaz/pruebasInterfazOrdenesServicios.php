<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazOrdenesServiciosCest {

  public function verificarElementosOrdenesServicios(AcceptanceTester $I): void {
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

    $I->amOnPage('/ordenesServicios');
    $I->seeInCurrentUrl('/ordenesServicios');
    $I->waitForElementVisible('.dt-search input', 15);

    $elementosBase = [
      '.tabla-ajax',
      '.modalDetallesOrden',
      '#tab-info-orden',
      '#tab-cliente-orden',
      '#tab-ubicacion-orden',
      '.modalGestionarOrden',
      '.modalGestionarOrden select.selectNuevoStatus',
      '.modalGestionarOrden input.nueva_fecha_ejecucion'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }
    $I->waitForElementVisible('.btnVerOrdenServicio', 15);
    $I->click('.btnVerOrdenServicio');
    $I->waitForElementVisible('.modalDetallesOrden', 10);
    $I->executeJS("bootstrap.Tab.getOrCreateInstance(document.querySelector('#tab-cliente-orden')).show();");
    $I->waitForElementVisible('.modalDetallesOrden .rif_cedula_cliente', 5);

    $I->executeJS("bootstrap.Tab.getOrCreateInstance(document.querySelector('#tab-ubicacion-orden')).show();");
    $I->waitForElementVisible('.modalDetallesOrden .url_direccion_btn', 5);

    $I->click('.modalDetallesOrden .btn-close');
    $I->waitForElementNotVisible('.modalDetallesOrden', 10);

    $hayBotonGestion = (bool) $I->executeJS("return document.querySelectorAll('.btnGestionarOrden').length > 0;");

    if ($hayBotonGestion) {
      $I->click('.btnGestionarOrden');
      $I->waitForElementVisible('.modalGestionarOrden', 10);

      $I->executeJS("
        let sel = document.querySelector('.modalGestionarOrden select.selectNuevoStatus');
        sel.value = '1';
        sel.dispatchEvent(new Event('change', { bubbles: true }));
      ");
      $I->waitForElementVisible('.modalGestionarOrden .nueva_fecha_ejecucion', 5);

      $fechaPrueba = date('Y-m-d');
      $I->fillField('.modalGestionarOrden .nueva_fecha_ejecucion', $fechaPrueba);
      $I->seeInField('.modalGestionarOrden .nueva_fecha_ejecucion', $fechaPrueba);

      $I->click('.modalGestionarOrden button[data-bs-dismiss="modal"]');
      $I->waitForElementNotVisible('.modalGestionarOrden', 10);
    }
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