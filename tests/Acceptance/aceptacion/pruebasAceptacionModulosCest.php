<?php
declare(strict_types=1);
namespace Tests\Acceptance\aceptacion;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionModulosCest {
  
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
     * UAT-019: Registro de Módulo (RNF-6.1)
     */
    public function verificarRegistroModulo(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/modulos');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        // El módulo solo lleva un nombre único
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $nombreModulo = 'Modulo Test ' . $sufijo;

        $I->fillField('.modalRegistrar input[name="nombre_modulo"]', $nombreModulo);
        $I->click('.modalRegistrar button[type="submit"]');

        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Módulo registrado', 10, '.swal2-title');
        $I->see('El módulo ha sido registrado exitosamente');
        $I->makeScreenshot('UAT019_Exito_Registro_Modulo');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Verificar en la BD de SEGURIDAD
        $I->seeInDatabase('proyecto_lacruz_seguridad_pruebas.modulos', [
            'nombre_modulo' => $nombreModulo,
            'status'        => 1
        ]);
    }

    /**
     * UAT-020: Validar campos vacíos
     */
    public function verificarCamposObligatoriosVaciosModulo(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/modulos');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        // Guardar sin llenar nada
        $I->click('.modalRegistrar button[type="submit"]');

        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Hay campos inválidos', 10, '.swal2-title');
        $I->makeScreenshot('UAT020_Fallo_Campos_Vacios_Modulo');
        $I->click('.swal2-confirm');
        $I->wait(1);
    }

    /**
     * UAT-021: Listar módulos (solo vigentes, status != 0)
     */
    public function verificarListadoSoloVigentesModulos(AcceptanceTester $I): void
    {
        $this->login($I);

        // 1. Sembrar los datos directamente en la BD de SEGURIDAD
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $nombreActivo = 'Modulo Activo ' . $sufijo;
        $nombreInactivo = 'Modulo Inactivo ' . $sufijo;

        // Insertamos un módulo activo (status = 1)
        $I->haveInDatabase('proyecto_lacruz_seguridad_pruebas.modulos', [
            'nombre_modulo' => $nombreActivo,
            'status'        => 1
        ]);

        // Insertamos un módulo inactivo (status = 0)
        $I->haveInDatabase('proyecto_lacruz_seguridad_pruebas.modulos', [
            'nombre_modulo' => $nombreInactivo,
            'status'        => 0
        ]);

        // 2. Ir a la vista de módulos para ver el DataTable
        $I->amOnPage('/modulos');
        $I->waitForElementVisible('.tabla-ajax', 10);
        $I->makeScreenshot('UAT021_Listado_Modulos_Vigentes');
        $I->wait(2); // Esperar a que el AJAX cargue la tabla

        // 3. LA PRUEBA DE FUEGO: 
        // Verificar que el módulo ACTIVO aparece en la tabla
        $I->see($nombreActivo, '.tabla-ajax');

        // Verificar que el módulo INACTIVO NO aparece en la tabla
        $I->dontSee($nombreInactivo, '.tabla-ajax');
    }

    /**
     * UAT-022: Consultar Módulo
     */
    public function verificarConsultaModulo(AcceptanceTester $I): void
    {
        $this->login($I);

        // Crear
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $nombreOriginal = 'Modulo Consulta ' . $sufijo;
        
        $I->amOnPage('/modulos');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        $I->fillField('.modalRegistrar input[name="nombre_modulo"]', $nombreOriginal);
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Módulo registrado', 10, '.swal2-title');
        $I->makeScreenshot('UAT022_Exito_Registro_Modulo_Consulta');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        $idModulo = $I->grabFromDatabase('proyecto_lacruz_seguridad_pruebas.modulos', 'id_modulo', ['nombre_modulo' => $nombreOriginal]);

        // Consultar (Editar)
        $I->amOnPage('/modulos');
        $I->waitForElementClickable("a.botonEditar[value='$idModulo']", 15);
        $I->click("a.botonEditar[value='$idModulo']");
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2);

        $I->seeInField('.modalActualizar input[name="nombre_modulo"]', $nombreOriginal);
        
        $I->executeJS("document.querySelector('.modalActualizar .btn-close').click();");
        $I->wait(1);
    }

    /**
     * UAT-023: Actualizar Módulo
     */
    public function verificarActualizacionModulo(AcceptanceTester $I): void
    {
        $this->login($I);

        // Crear
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $nombreOriginal = 'Modulo Actualizar ' . $sufijo;
        
        $I->amOnPage('/modulos');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        $I->fillField('.modalRegistrar input[name="nombre_modulo"]', $nombreOriginal);
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Módulo registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        $idModulo = $I->grabFromDatabase('proyecto_lacruz_seguridad_pruebas.modulos', 'id_modulo', ['nombre_modulo' => $nombreOriginal]);

        // Editar
        $I->amOnPage('/modulos');
        $I->waitForElementClickable("a.botonEditar[value='$idModulo']", 15);
        $I->click("a.botonEditar[value='$idModulo']");
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2);

        $nombreModificado = 'Modulo Modificado ' . $sufijo;
        $I->fillField('.modalActualizar input[name="nombre_modulo"]', $nombreModificado);
        
        $I->click('.modalActualizar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Módulo actualizado', 10, '.swal2-title');
        $I->makeScreenshot('UAT023_Exito_Actualizacion_Modulo');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Verificar en BD
        $I->seeInDatabase('proyecto_lacruz_seguridad_pruebas.modulos', [
            'id_modulo'     => $idModulo,
            'nombre_modulo' => $nombreModificado,
            'status'        => 1
        ]);
    }

    /**
     * UAT-024: Anular un módulo (Borrado Lógico)
     */
    public function verificarAnulacionModulo(AcceptanceTester $I): void
    {
        $this->login($I);

        // ==========================================
        // PASO 1: CREAR EL MÓDULO BASE
        // ==========================================
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $nombreModulo = 'Modulo Para Anular ' . $sufijo;
        
        $I->amOnPage('/modulos');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        $I->fillField('.modalRegistrar input[name="nombre_modulo"]', $nombreModulo);
        $I->click('.modalRegistrar button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Módulo registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Obtener el ID del módulo desde la BD de seguridad para usarlo en la tabla
        $idModulo = $I->grabFromDatabase('proyecto_lacruz_seguridad_pruebas.modulos', 'id_modulo', ['nombre_modulo' => $nombreModulo]);

        // ==========================================
        // PASO 2: ANULAR EL MÓDULO
        // ==========================================
        $I->amOnPage('/modulos');
        $I->waitForElementClickable("li.botonEliminar[value='$idModulo'] a", 15);
        $I->click("li.botonEliminar[value='$idModulo'] a");

        // Confirmar el SweetAlert de eliminación
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro de eliminar el registro?', '.swal2-html-container');
        $I->click('.swal2-confirm');

        // Esperar mensaje de éxito y tomar foto de evidencia
        $I->waitForText('Módulo eliminado', 10, '.swal2-title');
        $I->see('El módulo ha sido eliminado con éxito');
        $I->makeScreenshot('UAT024_Exito_Anulacion_Modulo');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // ==========================================
        // PASO 3: VERIFICAR BORRADO LÓGICO EN BD SEGURIDAD
        // ==========================================
        // Comprobamos que el módulo sigue existiendo pero con status = 0
        $I->seeInDatabase('proyecto_lacruz_seguridad_pruebas.modulos', [
            'id_modulo' => $idModulo,
            'status'    => 0
        ]);
        
        // Confirmamos que la fila NO se borró físicamente
        $I->seeNumRecords(1, 'proyecto_lacruz_seguridad_pruebas.modulos', ['id_modulo' => $idModulo]);
    }

    public function _after(AcceptanceTester $I): void {
        $I->makeScreenshot('final_modulos024_' . uniqid());
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