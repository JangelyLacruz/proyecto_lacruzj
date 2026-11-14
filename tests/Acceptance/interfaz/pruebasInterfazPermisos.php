<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazPermisosCest {

  public function verificarElementosPermisos(AcceptanceTester $I): void {
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

    $I->amOnPage('/permisos');
    $I->seeInCurrentUrl('/permisos');
    $I->waitForElementVisible('.dt-search input', 15);
    $I->wait(2); 

    $elementosBase = [
      '.tabla-ajax',
      '.dt-search input',
      '.modalPermisos, .modalGestionarPermisos, .modalActualizar'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }

    $I->fillField('.dt-search input', 'ADMINISTRADOR');
    $I->seeInField('.dt-search input', 'ADMINISTRADOR');
    $I->fillField('.dt-search input', '');
    $I->waitForElementVisible('.botonEditar, .botonGestionar, .btn-link-primary, .btn-link-success', 15);
    
    $I->executeJS("
      let btn = document.querySelector('.tabla-ajax tbody tr .botonEditar, .tabla-ajax tbody tr .botonGestionar, .tabla-ajax tbody tr a.btn-pc-default');
      if (btn) btn.click();
    ");

    $modalSelector = '.modalPermisos, .modalGestionarPermisos, .modalActualizar';
    $I->waitForElementVisible($modalSelector, 10);

    $I->waitForJS("
      return document.querySelectorAll('.form-check-input, input[type=\"checkbox\"]').length > 0;
    ", 10);

    $I->executeJS("
      let primerCheck = document.querySelector('.modal.show input[type=\"checkbox\"]');
      if (primerCheck) {
        let estadoInicial = primerCheck.checked;
        primerCheck.click();
        primerCheck.checked = estadoInicial; // Restauramos inmediatamente
      }
    ");
    $I->executeJS("
      let modalAbierto = document.querySelector('.modal.show');
      if (modalAbierto) {
        let btnCerrar = modalAbierto.querySelector('.btn-close, button[data-bs-dismiss=\"modal\"]');
        if (btnCerrar) btnCerrar.click();
      }
    ");

    $I->wait(1);
    $this->forzarCierreModales($I);

    $I->seeInCurrentUrl('/permisos');
    $I->seeElement('.dt-search input');
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
      let modales = document.querySelectorAll('.modalPermisos, .modalGestionarPermisos, .modalActualizar');
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