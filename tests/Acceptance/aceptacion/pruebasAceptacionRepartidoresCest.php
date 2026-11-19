<?php
declare(strict_types=1);
namespace Tests\Acceptance\aceptacion;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionRepartidoresCest {
  
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
     * UAT-013: Registro de Repartidor (RF-11.7.1)
     */
    public function verificarFlujoCompletoRepartidor(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Ir a repartidores y abrir modal
        $I->amOnPage('/repartidores');
        $I->seeInCurrentUrl('/repartidores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        // 3. Generar datos aleatorios (SIN espacios ni números en el nombre/apellido)
        $letraCedula = ['V', 'E'][array_rand(['V', 'E'])];
        $numeroCedula = str_pad((string) rand(1000000, 99999999), 8, '0', STR_PAD_LEFT);
        $cedulaCompleta = $letraCedula . $numeroCedula;
        
        // Generar nombre y apellido solo con letras para pasar la validación regexNombrePer
        $nombre = 'Juan' . chr(rand(65, 90)) . chr(rand(65, 90)); 
        $apellido = 'Perez' . chr(rand(65, 90)); 
        
        $prefijo = '0414';
        $telefono = str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);

        // 4. Llenar el formulario
        $I->selectOption('.modalRegistrar select[name="codigo_cedula_repartidor"]', $letraCedula);
        $I->fillField('.modalRegistrar input[name="cedula_repartidor"]', $numeroCedula);
        $I->fillField('.modalRegistrar input[name="nombre_repartidor"]', $nombre);
        $I->fillField('.modalRegistrar input[name="apellido_repartidor"]', $apellido);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_repartidor"]', $prefijo);
        $I->fillField('.modalRegistrar input[name="telefono_repartidor"]', $telefono);

        // 5. Guardar y aceptar SweetAlerts
        $I->click('.modalRegistrar button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Repartidor registrado', 10, '.swal2-title');
        $I->see('El repartidor ha sido registrado exitosamente');
        
        //  FOTO DE EVIDENCIA PARA EL ACTA 
        $I->makeScreenshot('UAT013_Exito_Registro_Repartidor');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // 6. PRUEBA DE FUEGO: Verificar en la base de datos
        $I->seeInDatabase('repartidores', [
            'cedula_repartidor'   => $cedulaCompleta,
            'nombre_repartidor'   => $nombre,
            'apellido_repartidor' => $apellido,
            'telefono_repartidor' => $prefijo . $telefono,
            'status'              => 1,
        ]);
        
        // 7. Verificar que la cédula se guardó exacta (sin perder la letra)
        $guardada = $I->grabFromDatabase('repartidores', 'cedula_repartidor', ['nombre_repartidor' => $nombre]);
        $I->assertSame($cedulaCompleta, $guardada, 'La cédula se guardó corrupta o incompleta');
    }

     /**
     * UAT-014: Validar que no se pueda guardar un repartidor con campos vacíos
     */
    public function verificarCamposObligatoriosVaciosRepartidor(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/repartidores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $I->click('.modalRegistrar button[type="submit"]');

        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Hay campos inválidos', 10, '.swal2-title');
        $I->see('No se puede enviar el formulario con campos inválidos');
        $I->makeScreenshot('UAT014_Campos_Obligatorios_Vacios_Proveedor');
        
        $I->click('.swal2-confirm');
        $I->wait(1);

        $I->dontSeeInDatabase('repartidores', ['nombre_repartidor' => '']);
    }

    /**
     * UAT-015: Listar repartidores (solo vigentes, status != 0)
     */
    public function verificarListadoSoloVigentesRepartidores(AcceptanceTester $I): void
    {
        $this->login($I);
        
        // Creamos un repartidor rápido
        $letra = 'V';
        $numero = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $cedula = $letra . $numero;
        
        $I->amOnPage('/repartidores');
        $I->waitForElementClickable('button[data-bs-target=".modalRegistrar"]', 10);
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        $I->selectOption('.modalRegistrar select[name="codigo_cedula_repartidor"]', $letra);
        $I->fillField('.modalRegistrar input[name="cedula_repartidor"]', $numero);
        $I->fillField('.modalRegistrar input[name="nombre_repartidor"]', 'Luis');
        $I->fillField('.modalRegistrar input[name="apellido_repartidor"]', 'Gomez');
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_repartidor"]', '0414');
        $I->fillField('.modalRegistrar input[name="telefono_repartidor"]', '1234567');
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Repartidor registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Verificar que aparece en la tabla
        $I->amOnPage('/repartidores');
        $I->waitForElementVisible('.tabla-ajax', 10);
        $I->wait(2);
        $I->see($cedula, '.tabla-ajax');

        // Anular
        $I->click(['css' => "li.botonEliminar[value='$cedula'] a"]);
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Repartidor eliminado', 10, '.swal2-title');
        $I->makeScreenshot('UAT015_Listado_Solo_Vigentes_Repartidor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(3);

        // Ya no debe estar en la tabla, pero sí en BD (inactivo)
        $I->dontSee($cedula, '.tabla-ajax');
        $I->seeInDatabase('repartidores', ['cedula_repartidor' => $cedula, 'status' => 0]);
    }

    /**
     * UAT-016: Consultar un repartidor (mostrar datos exactos)
     */
    public function verificarConsultaRepartidor(AcceptanceTester $I): void
    {
        $this->login($I);

        // Creamos un repartidor con datos exactos
        $letra = 'E';
        $numero = '99887766';
        $cedulaCompleta = $letra . $numero;
        
        $I->amOnPage('/repartidores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        $I->selectOption('.modalRegistrar select[name="codigo_cedula_repartidor"]', $letra);
        $I->fillField('.modalRegistrar input[name="cedula_repartidor"]', $numero);
        $I->fillField('.modalRegistrar input[name="nombre_repartidor"]', 'Carlos');
        $I->fillField('.modalRegistrar input[name="apellido_repartidor"]', 'Mendoza');
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_repartidor"]', '0424');
        $I->fillField('.modalRegistrar input[name="telefono_repartidor"]', '7654321');
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Repartidor registrado', 10, '.swal2-title');
        $I->makeScreenshot('UAT016_Exito_Consulta_Repartidor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Abrir modal de editar
        $I->amOnPage('/repartidores');
        $selectorEditar = "a.botonEditar[value='$cedulaCompleta']";
        $I->waitForElementClickable($selectorEditar, 15);
        $I->click($selectorEditar);
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2);

        // Validar campos
        $I->seeInField('.modalActualizar input[name="nombre_repartidor"]', 'Carlos');
        $I->seeInField('.modalActualizar input[name="apellido_repartidor"]', 'Mendoza');
        $I->seeOptionIsSelected('.modalActualizar select[name="prefijo_telefono_repartidor"]', '0424');
        $I->seeInField('.modalActualizar input[name="telefono_repartidor"]', '7654321');
        
        $I->executeJS("document.querySelector('.modalActualizar .btn-close').click();");
        $I->wait(1);
    }

    /**
     * UAT-017: Actualizar un repartidor
     */
    public function verificarActualizacionRepartidor(AcceptanceTester $I): void
    {
        $this->login($I);

        // ==========================================
        // PASO 1: CREAR EL REPARTIDOR BASE
        // ==========================================
        $letra = 'V';
        $numero = '11223344';
        $cedulaCompleta = $letra . $numero;
        
        $I->amOnPage('/repartidores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        
        $I->selectOption('.modalRegistrar select[name="codigo_cedula_repartidor"]', $letra);
        $I->fillField('.modalRegistrar input[name="cedula_repartidor"]', $numero);
        $I->fillField('.modalRegistrar input[name="nombre_repartidor"]', 'Pedro');
        $I->fillField('.modalRegistrar input[name="apellido_repartidor"]', 'Perez');
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_repartidor"]', '0414');
        $I->fillField('.modalRegistrar input[name="telefono_repartidor"]', '1234567');
        
        // Guardar el REGISTRO
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Repartidor registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // ==========================================
        // PASO 2: EDITAR EL REPARTIDOR
        // ==========================================
        // Volver a cargar la tabla
        $I->amOnPage('/repartidores');
        
        // Buscar el botón de editar (lápiz) para ese RIF y hacer clic
        $I->waitForElementClickable("a.botonEditar[value='$cedulaCompleta']", 15);
        $I->click("a.botonEditar[value='$cedulaCompleta']");
        
        // Esperar que se abra el modal de ACTUALIZAR
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2); // Dar tiempo a que el JS cargue los datos

        // Modificar los datos
        $I->fillField('.modalActualizar input[name="nombre_repartidor"]', 'PedroModificado');
        $I->fillField('.modalActualizar input[name="apellido_repartidor"]', 'PerezModificado');
        
        // Guardar la ACTUALIZACIÓN
        $I->click('.modalActualizar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        
        // Esperar el mensaje de actualización
        $I->waitForText('Repartidor actualizado', 10, '.swal2-title');
        
        // Foto de evidencia
        $I->makeScreenshot('UAT017_Exito_Actualizacion_Repartidor1');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // ==========================================
        // PASO 3: VERIFICAR EN LA BASE DE DATOS
        // ==========================================
        $I->seeInDatabase('repartidores', [
            'cedula_repartidor' => $cedulaCompleta,
            'nombre_repartidor' => 'PedroModificado',
            'apellido_repartidor' => 'PerezModificado',
            'status' => 1
        ]);
    }

     /**
     * UAT-018: Anular un repartidor (Borrado Lógico)
     */
    public function verificarAnulacionRepartidor(AcceptanceTester $I): void
    {
        $this->login($I);

        // ==========================================
        // PASO 1: CREAR EL REPARTIDOR (Con datos únicos)
        // ==========================================
        $letra = 'V';
        // Generamos una cédula aleatoria de 8 dígitos
        $numero = str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $cedulaCompleta = $letra . $numero;
        
        // Generamos un teléfono aleatorio de 7 dígitos
        $telefono = str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        
        $I->amOnPage('/repartidores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);
        
        $I->selectOption('.modalRegistrar select[name="codigo_cedula_repartidor"]', $letra);
        $I->fillField('.modalRegistrar input[name="cedula_repartidor"]', $numero);
        $I->fillField('.modalRegistrar input[name="nombre_repartidor"]', 'Jose');
        $I->fillField('.modalRegistrar input[name="apellido_repartidor"]', 'Ramirez');
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_repartidor"]', '0416');
        $I->fillField('.modalRegistrar input[name="telefono_repartidor"]', $telefono);
        
        // Guardar el REGISTRO
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Repartidor registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // ==========================================
        // PASO 2: ANULAR EL REPARTIDOR
        // ==========================================
        // Buscar en la tabla la cédula recién creada y hacer clic en la papelera
        $I->amOnPage('/repartidores');
        $I->waitForElementClickable("li.botonEliminar[value='$cedulaCompleta'] a", 15);
        $I->click("li.botonEliminar[value='$cedulaCompleta'] a");
        
        // Confirmar el SweetAlert
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro de eliminar el registro?', '.swal2-html-container');
        $I->click('.swal2-confirm');
        
        // Esperar el mensaje de éxito y tomar foto
        $I->waitForText('Repartidor eliminado', 10, '.swal2-title');
        $I->makeScreenshot('UAT018_Exito_Anulacion_Repartidor');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // ==========================================
        // PASO 3: VERIFICAR BORRADO LÓGICO EN BD
        // ==========================================
        // Comprobamos que el repartidor sigue existiendo pero con status = 0
        $I->seeInDatabase('repartidores', [
            'cedula_repartidor' => $cedulaCompleta,
            'status' => 0
        ]);
        
        // Comprobamos que no se duplicó ni borró físicamente
        $I->seeNumRecords(1, 'repartidores', ['cedula_repartidor' => $cedulaCompleta]);
    }

public function _after(AcceptanceTester $I): void {
    // 1. Foto de seguridad al finalizar el test
    $I->makeScreenshot('final_repartidores018_' . uniqid());
    // 2. Limpieza de modales y cierre de sesión
    $I->executeJS("
      let swal = document.querySelector('.swal2-container');
      if (swal) swal.remove();
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open', 'swal2-shown', 'swal2-height-auto');
      let btnLogout = document.querySelector('.btnCerrarSession');
      if (btnLogout) btnLogout.click();
    ");
    $I->wait(1);
    // 3. Aceptar alerta de cerrar sesión
    $I->executeJS("
      let btnConfirm = document.querySelector('.swal2-confirm.swal2-styled');
      if (btnConfirm) btnConfirm.click();
    ");
    $I->wait(1);
    // 4. Limpiar cookies 
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->executeJS("window.sessionStorage.clear(); window.localStorage.clear();");
  }
}