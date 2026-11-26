<?php

declare(strict_types=1);

namespace Tests\Acceptance\interfaz;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazAccesosCest {

   protected function _before(): void
  {
      $db = DB_NAME . '_seguridad_pruebas';
      $pdo = new \PDO("mysql:host=" . DB_SERVER . ";dbname=$db;charset=utf8mb4", DB_USER, DB_PASS, [
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      ]);
      $pdo->exec("UPDATE accesos SET status = 1 WHERE id_rol = 1");
  }

  public function verificarElementosAccesos(AcceptanceTester $I): void {
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

        $I->amOnPage('/accesos');
        $I->seeInCurrentUrl('/accesos');

        $elementosBase = [
          '.selectRolesPermisos',          
          '.opcionesRoles',               
          '.contenedorPanel',              
          '.containerPermEspe',            
          '.btnExpandirContraerPermisos',  
          '.inputBusquedaPermisos',        
          '.paginadorPermisos'             
        ];

        foreach ($elementosBase as $selector) {
          $I->seeElementInDOM($selector);
        }
      }

  public function verificarInteraccionAcordeonYSwitches(AcceptanceTester $I): void {

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

        $I->amOnPage('/accesos');
        $I->seeInCurrentUrl('/accesos');
        
        $I->waitForElementVisible('.opcionesRoles', 10);
        
        $I->click('.btnExpandirContraerPermisos');
        
        $I->waitForElementVisible('.permiso_checkbox', 10);

        $estadoInicial = $I->executeJS("
            let switchBtn = document.querySelector('.accordion-collapse.show .permiso_checkbox');
            return switchBtn ? switchBtn.checked : null;
        ");

        if ($estadoInicial === null) {
            $I->comment('No se encontraron switches visibles en el acordeón para interactuar.');
            return;
        }

        $I->executeJS("
            let switchBtn = document.querySelector('.accordion-collapse.show .permiso_checkbox');
            switchBtn.click();
        ");

        $I->wait(1);

        $estadoFinal = $I->executeJS("
            let switchBtn = document.querySelector('.accordion-collapse.show .permiso_checkbox');
            return switchBtn ? switchBtn.checked : null;
        ");

        $I->assertNotEquals(
            $estadoInicial, 
            $estadoFinal, 
            'El switch visual no cambió de estado después de hacer clic.'
        );
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