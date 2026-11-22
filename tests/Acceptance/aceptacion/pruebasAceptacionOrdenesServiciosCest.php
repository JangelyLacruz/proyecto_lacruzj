<?php

declare(strict_types=1);

namespace Tests\Acceptance\aceptacion;

use Tests\Support\AcceptanceTester;

final class pruebasAceptacionOrdenesServiciosCest {

  public function verificarFlujoCompletoOrdenesServicios(AcceptanceTester $I): void {
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
      "return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');",
      30
    );

    $I->amOnPage('/ordenesServicios');
    $I->seeInCurrentUrl('/ordenesServicios');
    $I->waitForElementVisible('.dt-search input', 15);
    $I->wait(2);

    $I->waitForElementVisible('.btnVerOrdenServicio', 15);
    $I->click('.btnVerOrdenServicio');

    $I->waitForElementVisible('.modalDetallesOrden', 10);
    $I->waitForJS("return document.querySelector('.modalDetallesOrden .id_servicio_factura').innerText !== '-';", 10);

    $I->executeJS("
      let triggerEl = document.querySelector('#tab-cliente-orden');
      let tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
      tab.show();
    ");
    $I->waitForElementVisible('.modalDetallesOrden .rif_cedula_cliente', 10);

    $I->executeJS("
      let triggerEl = document.querySelector('#tab-ubicacion-orden');
      let tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
      tab.show();
    ");
    $I->waitForElementVisible('.modalDetallesOrden .url_direccion_btn', 10);

    $I->click('.modalDetallesOrden .btn-close');
    $I->wait(1);
    $this->forzarCierreModales($I);

    $hayBotonGestion = (bool) $I->executeJS("return document.querySelectorAll('.btnGestionarOrden').length > 0;");

    if ($hayBotonGestion) {
      $I->click('.btnGestionarOrden');
      $I->waitForElementVisible('.modalGestionarOrden', 10);

      $I->waitForJS("return document.querySelector('.modalGestionarOrden .info_id_orden').innerText !== '-';", 10);

      // Seleccionamos estado 1 (Pendiente) y asignamos por JS la fecha de mañana para evitar corrupción de input date
      $fechaManana = date('Y-m-d', strtotime('+1 day'));
      $I->executeJS("
        let sel = document.querySelector('.modalGestionarOrden select.selectNuevoStatus');
        sel.value = '1';
        sel.dispatchEvent(new Event('change', { bubbles: true }));

        let inputFecha = document.querySelector('.modalGestionarOrden .nueva_fecha_ejecucion');
        inputFecha.value = '$fechaManana';
        inputFecha.dispatchEvent(new Event('input', { bubbles: true }));
        inputFecha.dispatchEvent(new Event('change', { bubbles: true }));
      ");

      $I->wait(1);
      $I->click('.modalGestionarOrden button[type="submit"]');

      // Confirmar alerta "¿Actualizar orden?"
      $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
      $I->click('.swal2-confirm.swal2-styled');

      // Esperar a que finalice la petición AJAX y aparezca la alerta de respuesta del servidor
      $I->wait(1);
      $I->waitForElementVisible('.swal2-popup', 15);
      $I->wait(1);
      $this->forzarCierreModales($I);

      $I->waitForElementVisible('.dt-search input', 10);
    }
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
      let modales = document.querySelectorAll('.modalDetallesOrden, .modalGestionarOrden');
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