<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class iniciarSesionSmokeCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->setCookie('TEMP', 'true');
    }

    public function verificarFlujoDeInicioDeSesion(AcceptanceTester $I)
    {
        $I->amOnPage('/usuarios/login');

        // $I->seeResponseCodeIs(200);
        // $I->seeElement('form.formularioAjax.login');
        // $I->seeElement('input[name="usuario_usuario"]');
        // $I->seeElement('input[name="contrasena1_usuario"]');
        // $I->seeElement('button[type="submit"]');

        $I->fillField('input[name="usuario_usuario"]', 'Ander123'); 
        $I->fillField('input[name="contrasena1_usuario"]', 'David01*');

        $I->click('button[type="submit"]');

        $I->amOnPage('/reportes');
        $I->seeResponseCodeIs(200);
    }
}
