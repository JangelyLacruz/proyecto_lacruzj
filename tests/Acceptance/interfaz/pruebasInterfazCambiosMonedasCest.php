<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazCambiosMonedasCest {

  public function verificarElementosCambiosMonedas(AcceptanceTester $I): void {
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

    $I->amOnPage('/monedas/cambios-monedas');
    $I->seeInCurrentUrl('/monedas/cambios-monedas');

    $elementosBase = [
      'button[data-bs-target=".modalRegistrar"]',
      '.tabla-ajax',
      '.modalRegistrar',
      '.modalRegistrar select.selectMonedas',
      '.modalRegistrar input[name="valor_moneda"]',
      '.modalRegistrar button[type="submit"]',
      '.modalRegistrar button[data-bs-dismiss="modal"]'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }

    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);
    $I->waitForJS("return document.querySelectorAll('.selectMonedas option').length > 1;", 15);
    $opcionMoneda = $I->executeJS("return document.querySelector('.selectMonedas option:not([value=\"\"])').value;");
    $I->selectOption('.selectMonedas', ['value' => $opcionMoneda]);
    $I->fillField('.modalRegistrar input[name="valor_moneda"]', '60.50');
    $I->seeInField('.modalRegistrar input[name="valor_moneda"]', '60,50');

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