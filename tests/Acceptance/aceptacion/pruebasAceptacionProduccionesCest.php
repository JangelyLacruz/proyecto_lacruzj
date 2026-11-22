<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasAceptacionProduccionesCest {
  
  public function verificarFlujoCompletoProducciones(AcceptanceTester $I): void {
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
      "return window.location.href.includes('/reportes');", 
      30
    );
    $I->amOnPage('/producciones');
    $I->seeInCurrentUrl('/producciones');

    $I->waitForElementVisible('button[data-bs-target=".modalRegistrar"]', 10);
    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);

    $I->executeJS("
      if (document.querySelectorAll('#contenedorProductos .fila-producto').length === 0) {
        document.querySelector('#btnAgregarProducto').click();
      }
    ");

    $I->waitForElementVisible('.modalRegistrar .fila-producto', 10);

    $I->waitForJS("
      let select = document.querySelector('.modalRegistrar .fila-producto .selectProductos');
      return select && select.options.length > 1;
    ", 15);

    $I->executeJS("
      let select = document.querySelector('.modalRegistrar .fila-producto .selectProductos');
      let opcionesValidas = Array.from(select.options).filter(o => o.value !== '');
      if (opcionesValidas.length > 0) {
        select.value = opcionesValidas[0].value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
      }
    ");

    $cantidadRegistro = strval(rand(10, 50));
    $I->fillField('.modalRegistrar .fila-producto input.cantidad-producto', $cantidadRegistro);

    $I->click('.modalRegistrar button[type="submit"]');

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(1);

    $this->forzarCierreModales($I);

    $I->reloadPage();
    $I->waitForElementVisible('.dt-search input', 15);
    $I->wait(3);

    $I->executeJS("$('.tabla-ajax').DataTable().order([1, 'desc']).draw();");
    $I->wait(2);

    $I->executeJS("
      let btn = document.querySelector('.botonConsultar');
      if (btn) btn.click();
    ");

    $I->waitForElementVisible('.modalConsultar', 10);
    $I->waitForElementNotVisible('#tbodyProductosConsulta .text-muted', 15);
    $I->see($cantidadRegistro, '#tbodyProductosConsulta');

    $I->click('.modalConsultar .btn-close');
    $I->wait(1);
    $this->forzarCierreModales($I);

    $I->waitForElementVisible('.dt-search input', 10);

    $I->executeJS("
      let btn = document.querySelector('.botonEditar');
      if (btn) btn.click();
    ");

    $I->waitForElementVisible('.modalActualizar', 10);
    $I->waitForElementVisible('.modalActualizar .fila-producto', 15);
    $I->waitForJS("return document.querySelector('.modalActualizar .fila-producto input.cantidad-producto').value !== '';", 10);

    $nuevaCantidad = strval(rand(60, 90));
    $I->fillField('.modalActualizar .fila-producto input.cantidad-producto', $nuevaCantidad);

    $I->click('.modalActualizar button[type="submit"]');

    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
    $I->wait(1);

    $this->forzarCierreModales($I);
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
      let modales = document.querySelectorAll('.modalRegistrar, .modalActualizar, .modalConsultar');
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