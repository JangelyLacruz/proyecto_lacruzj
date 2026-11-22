<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasAceptacionPermisosCest {
  
  public function verificarFlujoCompletoPermisos(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->setCookie('TEMP', 'true'); 
    
    $I->amOnPage('/usuarios/login');

    $I->seeElement('form.formularioAjax.login');
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');
    $I->click('form.formularioAjax.login button[type="submit"]');
    
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForJS(
      "return window.location.href.includes('/reportes');", 30);

    $I->amOnPage('/permisos');
    $I->seeInCurrentUrl('/permisos');

    $sufijo = strtoupper(bin2hex(random_bytes(3)));
    $nombrePermiso = 'QA_PERMISO_' . $sufijo;

    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);
    
    $I->fillField('.modalRegistrar input[name="nombre_permiso"]', $nombrePermiso);
    $I->click('.modalRegistrar button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(1); 
    $this->forzarCierreModales($I);
    
    // ==========================================

    $nombrePermisoEditado = $nombrePermiso . '_MOD';

    $I->waitForElementVisible('.dt-search input', 10); 
    $I->fillField('.dt-search input', $nombrePermiso);
    $I->waitForText($nombrePermiso, 10); 

    // Clic en editar
    $I->click('.btn-link-success', "//tr[td[contains(text(), '$nombrePermiso')]]");
    
    $I->waitForElementVisible('.modalActualizar', 10);
    $I->fillField('.modalActualizar input[name="nombre_permiso"]', $nombrePermisoEditado);
    $I->click('.modalActualizar button[type="submit"]');

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(1);

    $this->forzarCierreModales($I);

    // ==========================================
    $I->waitForElementVisible('.dt-search input', 10);
    $I->executeJS("document.querySelector('.dt-search input').value = '';");
    $I->fillField('.dt-search input', $nombrePermisoEditado);  
    $I->waitForText($nombrePermisoEditado, 10);

    // Clic en eliminar
    $I->click('.btn-link-danger', "//tr[td[contains(text(), '$nombrePermisoEditado')]]");

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');

    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(1);
  
    $this->forzarCierreModales($I);

    // ==========================================

    $I->waitForElementVisible('.dt-search input', 10);
    $I->executeJS("document.querySelector('.dt-search input').value = '';");
    $I->fillField('.dt-search input', $nombrePermisoEditado);
    
    $I->waitForText('No se encontraron resultados', 10); 
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

        let modales = document.querySelectorAll('.modalRegistrar, .modalActualizar');
        modales.forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
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