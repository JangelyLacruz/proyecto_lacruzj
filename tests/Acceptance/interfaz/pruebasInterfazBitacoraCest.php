<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazBitacoraCest {

  public function verificarElementosBitacora(AcceptanceTester $I): void {
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

    $I->amOnPage('/bitacora');
    $I->seeInCurrentUrl('/bitacora');
    $I->waitForElementVisible('.dt-search input', 15);
    $I->wait(2); 
    $elementosBase = [
      '.dt-search input',
      '.tabla-ajax',
      '.modalVerCambiosBitacora',
      '#contenedorCambiosBitacora',
      '.modalVerCambiosBitacora .btn-close'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }
    $I->fillField('.dt-search input', 'LOGIN');
    $I->seeInField('.dt-search input', 'LOGIN');

    $I->executeJS("$('.tabla-ajax').DataTable().search('').draw();");
    $I->wait(2);
    $I->executeJS("
        let btn = document.querySelector('.btnVerCambiosBitacora');
        if(btn) btn.click();
    ");

    $I->waitForElementVisible('.modalVerCambiosBitacora', 10);
    $I->seeElementInDOM('#contenedorCambiosBitacora');

    $I->click('.modalVerCambiosBitacora .btn-close');
    $I->waitForElementNotVisible('.modalVerCambiosBitacora', 10);
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