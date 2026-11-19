<?php
declare(strict_types=1);
namespace Tests\Acceptance\aceptacion;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionAccesosCest {
  
    private function login(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->resetCookie('JLACRUZ');
        $I->setCookie('TEMP', 'true'); 
        
        $I->amOnPage('/usuarios/login');
        $I->seeElement('form.formularioAjax.login');
        $I->fillField('input[name="usuario_usuario"]', 'jangely23');
        $I->fillField('input[name="contrasena1_usuario"]', '12345678');
        $I->click('form.formularioAjax.login button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
        $I->click('.swal2-confirm.swal2-styled');
        $I->waitForJS("return window.location.href.includes('/reportes');", 30);
    }

    /**
     * UAT-026: Listar permisos del sistema
     */
    public function verificarListarPermisos(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/accesos');
        
        // 1. Verificar que cargan los roles a la izquierda
        $I->waitForElementVisible('.opcionesRoles', 10);
        $I->seeElement('.opcionesRoles a.active');

        // 2. Verificar que carga el acordeón de módulos a la derecha
        $I->waitForElementVisible('.containerPermEspe .accordion-button', 10);

        // 3. Hacer clic en "Expandir" para que los checkboxes se hagan visibles
        $I->click('.btnExpandirContraerPermisos');
        $I->wait(1); // Dar medio segundo a la animación de Bootstrap

        // 4. Ahora sí, verificar que los checkboxes están visibles en la pantalla
        $I->seeElement('.permiso_checkbox');

        $I->makeScreenshot('UAT026_Listar_Accesos');
    }

    /**
     * UAT-027: Habilitar un permiso (Módulo de BANCOS)
     */
    public function verificarHabilitarPermiso(AcceptanceTester $I): void
    {
        $this->login($I);
        $I->amOnPage('/accesos');
        $I->waitForElementVisible('.opcionesRoles', 10);
        
        $idRol = $I->grabAttributeFrom('.opcionesRoles', 'idRol');
        $I->click(['css' => "li.opcionesRoles[idRol='$idRol'] a"]);
        $I->waitForElementVisible('.containerPermEspe .accordion-button', 10);
        $I->wait(2);

        // Script JS: Busca el acordeón del módulo 'BANCOS' 
        $datosCheckbox = $I->executeJS("
            let acordeones = document.querySelectorAll('.accordion-item');
            for (let item of acordeones) {
                if (item.innerText.toUpperCase().includes('BANCOS')) {
                    item.querySelector('.accordion-button').click(); // Abre el acordeón
                    let body = item.querySelector('.accordion-body');
                    if(!body) continue;
                    let b = body.querySelector('.permiso_checkbox');
                    if (b) {
                        if (b.checked) b.click(); // Si ya estaba prendido, lo apagamos
                        return [b.getAttribute('idmodulo'), b.getAttribute('idpermiso')];
                    }
                }
            }
            return null;
        ");

        if ($datosCheckbox) {
            $I->wait(2); // Esperar que procese el apagado 
            
            $I->executeJS("
                let acordeones = document.querySelectorAll('.accordion-item');
                for (let item of acordeones) {
                    if (item.innerText.toUpperCase().includes('BANCOS')) {
                        let b = item.querySelector('.permiso_checkbox');
                        if (b && !b.checked) b.click();
                    }
                }
            ");
            $I->wait(2); 

            $idModulo = $datosCheckbox[0];
            $idPermiso = $datosCheckbox[1];

            // Validar que en BD está activo (1)
            $I->seeInDatabase('proyecto_lacruz_seguridad_pruebas.accesos', [
                'id_rol'     => $idRol,
                'id_modulo'  => $idModulo,
                'id_permiso' => $idPermiso,
                'status'     => 1
            ]);
            $I->makeScreenshot('UAT027_Habilitar_Permiso');
        }
    }

    /**
     * UAT-028: Deshabilitar un permiso (Target: Módulo de BANCOS)
     */
    public function verificarDeshabilitarPermiso(AcceptanceTester $I): void
    {
        $this->login($I);
        $I->amOnPage('/accesos');
        $I->waitForElementVisible('.opcionesRoles', 10);
        
        $idRol = $I->grabAttributeFrom('.opcionesRoles', 'idRol');
        $I->click(['css' => "li.opcionesRoles[idRol='$idRol'] a"]);
        $I->waitForElementVisible('.containerPermEspe .accordion-button', 10);
        $I->wait(2);

        // Script JS: Busca el acordeón del módulo 'BANCOS' y apaga su primer switch
        $datosCheckbox = $I->executeJS("
            let acordeones = document.querySelectorAll('.accordion-item');
            for (let item of acordeones) {
                if (item.innerText.toUpperCase().includes('BANCOS')) {
                    item.querySelector('.accordion-button').click();
                    let body = item.querySelector('.accordion-body');
                    if(!body) continue;
                    let b = body.querySelector('.permiso_checkbox');
                    if (b) {
                        if (!b.checked) b.click(); // Si ya estaba apagado, lo encendemos
                        return [b.getAttribute('idmodulo'), b.getAttribute('idpermiso')];
                    }
                }
            }
            return null;
        ");

        if ($datosCheckbox) {
            $I->wait(2); // Esperar que procese el encendido 
            
            $I->executeJS("
                let acordeones = document.querySelectorAll('.accordion-item');
                for (let item of acordeones) {
                    if (item.innerText.toUpperCase().includes('BANCOS')) {
                        let b = item.querySelector('.permiso_checkbox');
                        if (b && b.checked) b.click();
                    }
                }
            ");
            $I->wait(2);

            $idModulo = $datosCheckbox[0];
            $idPermiso = $datosCheckbox[1];

            // Validar que en BD está inactivo (0)
            $I->seeInDatabase('proyecto_lacruz_seguridad_pruebas.accesos', [
                'id_rol'     => $idRol,
                'id_modulo'  => $idModulo,
                'id_permiso' => $idPermiso,
                'status'     => 0
            ]);
            $I->makeScreenshot('UAT028_Deshabilitar_Permiso');
        }
    }

    public function _after(AcceptanceTester $I): void {
        $I->makeScreenshot('final_accesos0271_' . uniqid());
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