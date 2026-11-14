<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazCambiosIvaCest {

  public function verificarElementosCambiosIva(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->setCookie('TEMP', 'true');
    $I->amOnPage('/usuarios/login');
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');
    $I->click('form.formularioAjax.login button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->wait(3);

    $I->amOnPage('/cambiosIva');
    $I->seeInCurrentUrl('/cambiosIva');

    $elementosBase = [
      'button[data-bs-target=".modalRegistrar"]',
      '.tabla-ajax',
      '.modalRegistrar',
      '.modalRegistrar input[name="monto_cambio_iva"]',
      '.modalRegistrar button[type="submit"]',
      '.modalRegistrar button[data-bs-dismiss="modal"]'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }

    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);

    $I->fillField('.modalRegistrar input[name="monto_cambio_iva"]', '16.00');
    $valorInput = $I->executeJS("return document.querySelector('.modalRegistrar input[name=\"monto_cambio_iva\"]').value;");
    $I->assertTrue(in_array($valorInput, ['16.00', '16,00']), 'El input de IVA no tiene el formato correcto');

    $I->click('.modalRegistrar button[data-bs-dismiss="modal"]');
    $I->waitForElementNotVisible('.modalRegistrar', 10);
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