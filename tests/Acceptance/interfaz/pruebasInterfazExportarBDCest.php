<?php

declare(strict_types=1);

namespace Tests\Acceptance\interfaz;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazExportarBDCest {

    protected function _before(): void
  {
      $patron = 'C:\xampp\htdocs\proyecto-lacruz-j\tests\_output\*.sql';
      $archivos = glob($patron);
      if ($archivos !== false) {
          foreach ($archivos as $archivo) {
              @unlink($archivo);
          }
      }
  }

  public function verificarElementosExportarBD(AcceptanceTester $I): void {
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

        $I->executeJS("
            const segundoBtn = document.querySelector('.swal2-container .swal2-confirm');
            if (segundoBtn) segundoBtn.click();
        ");

        $I->waitForElementNotVisible('.swal2-container', 10);

        $I->waitForElementClickable('#btnExportarBD', 10);
        $I->click('#btnExportarBD');
        $I->waitForElementVisible('#modalExportarBD', 10);

        $I->checkOption('.checkExportBD[value="proyecto_lacruz"]');

        $I->click('#btnConfirmarExportar');

        $I->waitForElementVisible('.swal2-popup', 15);
        $I->see('Exportación iniciada', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->wait(3); 

        $patronBusqueda = 'C:\xampp\htdocs\proyecto-lacruz-j\tests\_output\backup_lacruz_*.sql';
        $archivosDescargados = glob($patronBusqueda);

        $I->assertNotEmpty($archivosDescargados, 'El archivo SQL no se descargó en la carpeta _output.');
        
        $rutaArchivo = end($archivosDescargados);
        
        $tamano = filesize($rutaArchivo);
        $I->assertGreaterThan(0, $tamano, 'El archivo SQL se descargó pero está vacío.');
        
        $primerasLineas = file_get_contents($rutaArchivo, false, null, 0, 200);
        $I->assertStringContainsString('Backup: proyecto_lacruz', $primerasLineas, 'El archivo no parece ser un backup SQL válido.');
        
        $I->comment("¡Base de datos exportada con éxito! Archivo: " . basename($rutaArchivo) . " (" . $tamano . " bytes)");
      }

  public function _after(AcceptanceTester $I): void {
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
}