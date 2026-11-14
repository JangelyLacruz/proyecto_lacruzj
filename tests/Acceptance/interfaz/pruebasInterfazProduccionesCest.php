<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazProduccionesCest {

  public function verificarElementosProducciones(AcceptanceTester $I): void {
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
    $I->amOnPage('/producciones');
    $I->seeInCurrentUrl('/producciones');

    $elementosBase = [
      'button[data-bs-target=".modalRegistrar"]',
      '#btnAgregarProducto',
      '#templateProductoFila',
      '.modalRegistrar',
      '.modalActualizar',
      '.modalConsultar',
      '#tbodyProductosConsulta'
    ];

    foreach ($elementosBase as $selector) {
      $I->seeElementInDOM($selector);
    }

    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);

    $I->executeJS("
      if (document.querySelectorAll('#contenedorProductos .fila-producto').length === 0) {
        document.querySelector('#btnAgregarProducto').click();
      }
    ");
    $I->waitForElementVisible('.modalRegistrar .fila-producto', 10);

    $I->click('#btnAgregarProducto');
    $I->waitForJS("return document.querySelectorAll('#contenedorProductos .fila-producto').length >= 2;", 10);
    $I->click('#contenedorProductos .fila-producto:last-child .btn-eliminar-fila');
    $I->wait(1);

    $I->waitForJS("return document.querySelectorAll('.modalRegistrar .fila-producto .selectProductos option').length > 1;", 15);
    $I->fillField('.modalRegistrar .fila-producto input.cantidad-producto', '25');
    $I->seeInField('.modalRegistrar .fila-producto input.cantidad-producto', '25');

    $I->click('.modalRegistrar button[data-bs-dismiss="modal"]');
    $I->waitForElementNotVisible('.modalRegistrar', 10);
    $I->waitForElementVisible('.dt-search input', 10);
    
    $I->executeJS("
        let btnConsulta = document.querySelector('.botonConsultar');
        if(btnConsulta) btnConsulta.click();
    ");

    $I->waitForElementVisible('.modalConsultar', 10);
    $I->seeElementInDOM('#tbodyProductosConsulta');
    $I->click('.modalConsultar .btn-close');
    $I->waitForElementNotVisible('.modalConsultar', 10);
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