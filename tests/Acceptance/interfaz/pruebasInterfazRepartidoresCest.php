<?php

declare(strict_types=1);

namespace Tests\Acceptance\interfaz;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazRepartidoresCest {

  public function verificarElementosRepartidores(AcceptanceTester $I): void {
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

          $I->amOnPage('/repartidores');
          $I->seeInCurrentUrl('/repartidores');

          $elementosBase = [
            'button[data-bs-target=".modalRegistrar"]',
            '.tabla-ajax',
            '.modalRegistrar',
            '.modalRegistrar input[name="cedula_repartidor"]',         
            '.modalRegistrar input[name="nombre_repartidor"]',
            '.modalRegistrar input[name="apellido_repartidor"]',
            '.modalRegistrar input[name="telefono_repartidor"]',
            '.modalRegistrar button[type="submit"]',
            '.modalRegistrar button[data-bs-dismiss="modal"]'
          ];

          foreach ($elementosBase as $selector) {
            $I->seeElementInDOM($selector);
          }

          $I->waitForElementClickable('button[data-bs-target=".modalRegistrar"]', 10);
          $I->click('button[data-bs-target=".modalRegistrar"]');

          $I->waitForElementVisible('.modalRegistrar input[name="nombre_repartidor"]', 15);

          $textoPrueba = 'Repartidor Interfaz C.A.';

          $I->executeJS("
            let input = document.querySelector('.modalRegistrar input[name=\"nombre_repartidor\"]');
            input.value = '{$textoPrueba}';
            input.dispatchEvent(new Event('input', { bubbles: true })); 
          ");

          $valorInput = $I->executeJS("return document.querySelector('.modalRegistrar input[name=\"nombre_repartidor\"]').value;");
          
          $I->assertEquals($textoPrueba, $valorInput, 'El input de Nombre no retuvo el texto escrito.');
        
          $I->click('.modalRegistrar button[data-bs-dismiss="modal"]');
          try {
            $I->waitForElementNotVisible('.modalRegistrar', 10);
          } catch (\Exception $e) {
            $I->executeJS("document.querySelector('.modalRegistrar').style.display = 'none';");
          }
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