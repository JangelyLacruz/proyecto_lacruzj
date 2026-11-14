<?php
declare(strict_types=1);
namespace Tests\Acceptance\humo;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionCambiosIvaCest {
  
  public function verificarActualizarIva(AcceptanceTester $I): void {
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
      "return window.location.href.includes('/reportes');", 30);
    $I->amOnPage('/cambiosIva');
    $I->seeInCurrentUrl('/cambiosIva');
    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);
    $nuevoIva = rand(10, 30) . '.' . rand(10, 99);
    $I->fillField('.modalRegistrar input[name="monto_cambio_iva"]', $nuevoIva);
    $I->click('.modalRegistrar button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(2);
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