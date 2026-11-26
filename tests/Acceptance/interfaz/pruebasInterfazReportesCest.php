<?php

declare(strict_types=1);

namespace Tests\Acceptance\interfaz;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazReportesCest {

  public function verificarElementosReportes(AcceptanceTester $I): void {
        $I->amOnPage('/');
        $I->resetCookie('JLACRUZ');
        $I->setCookie('TEMP', 'true');
        $I->amOnPage('/usuarios/login');
        $I->fillField('input[name="usuario_usuario"]', 'jangely23');
        $I->fillField('input[name="contrasena1_usuario"]', '12345678');
        $I->click('form.formularioAjax.login button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
        $I->click('.swal2-confirm.swal2-styled');
        $I->wait(3);

        $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
        $I->click('.swal2-confirm.swal2-styled');
        $I->waitForElementNotVisible('.swal2-container', 10);

        $I->amOnPage('/reportes');
        $I->waitForElementVisible('#mainContent', 10);
        $I->seeElementInDOM('input.nombreVista[value="reportes"]');

        $I->seeElement('#formReporteVentas');
        $I->seeElement('#formReporteCompras');
        $I->seeElement('#formCierreCaja');
        $I->seeElement('#formReporteServicios');
        $I->seeElement('#formReporteProductos');
        $I->seeElement('#formReporteMateriaPrima');

        $I->click('#formReporteVentas button[type="submit"]');
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('Debe seleccionar el intervalo de fechas (Desde y Hasta)', '.swal2-html-container');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);

        $I->click('#formCierreCaja button[type="submit"]');
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('Debe seleccionar una fecha de cierre', '.swal2-html-container');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->executeJS("
            document.querySelectorAll('.swal2-container').forEach(el => el.remove());
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open', 'swal2-shown', 'swal2-height-auto');
            const btnLogout = document.querySelector('.btnCerrarSession');
            if (btnLogout) btnLogout.click();
        ");
        $I->wait(1);

        $I->executeJS("
            const btnConfirm = document.querySelector('.swal2-confirm.swal2-styled');
            if (btnConfirm) btnConfirm.click();
        ");
        $I->wait(1);

        $I->resetCookie('JLACRUZ');
        $I->resetCookie('TEMP');
        $I->executeJS('window.sessionStorage.clear(); window.localStorage.clear();');
    }
}