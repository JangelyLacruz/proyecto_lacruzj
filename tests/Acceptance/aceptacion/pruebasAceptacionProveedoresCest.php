<?php
declare(strict_types=1);
namespace Tests\Acceptance\aceptacion;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionProveedoresCest {
  
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
     * UAT-007: Registro de Proveedor 
     */
    public function verificarFlujoCompletoProveedores(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Ir a proveedores y abrir modal
        $I->amOnPage('/proveedores');
        $I->seeInCurrentUrl('/proveedores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        // 3. Generar datos aleatorios
        $sufijo = strtoupper(bin2hex(random_bytes(3)));
        $razonSocial = 'Proveedor ' . $sufijo . ' C.A.';
        $numeroRif = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $rifCompleto = 'J' . $numeroRif; //  J para habilitar el Nro Control
        $nroControl = '00' . rand(10000, 99999);
        $prefijo = '0414';
        $telefono = str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        $correo = 'proveedor_' . $sufijo . '@example.com';
        $direccion = 'Avenida Industrial ' . rand(1, 100);

        // 4. Llenar el formulario
        $I->selectOption('.modalRegistrar select[name="codigo_rif_proveedor"]', 'J');
        
        // El JS habilita el campo de Nro Control al seleccionar 'J'
        $I->waitForElementVisible('.modalRegistrar .contenedorNumControl', 5);
        $I->waitForElementClickable('.modalRegistrar input[name="nro_control_factura"]', 5);
        
        $I->fillField('.modalRegistrar input[name="rif_proveedor"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_proveedor"]', $razonSocial);
        $I->fillField('.modalRegistrar input[name="nro_control_factura"]', $nroControl);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_proveedor"]', $prefijo);
        $I->fillField('.modalRegistrar input[name="telefono_proveedor"]', $telefono);
        $I->fillField('.modalRegistrar input[name="correo_proveedor"]', $correo);
        $I->fillField('.modalRegistrar textarea[name="direccion_proveedor"]', $direccion);

        // 5. Guardar y aceptar SweetAlerts
        $I->click('.modalRegistrar button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Proveedor registrado', 10, '.swal2-title');
        $I->see('El proveedor ha sido registrado exitosamente');
        $I->makeScreenshot('UAT007_Proveedor_Registrado');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // 6. PRUEBA DE FUEGO: Verificar en la tabla principal `proveedores`
        $I->seeInDatabase('proveedores', [
            'rif_proveedor'         => $rifCompleto,
            'razon_social_proveedor'=> $razonSocial,
            'status'                => 1,
        ]);

        // 7. PRUEBA DE FUEGO 2: Verificar que el Nro de Control se guardó en la tabla `nros_control_retenciones`
        $I->seeInDatabase('nros_control_retenciones', [
            'rif_proveedor'      => $rifCompleto,
            'nro_control_factura'=> $nroControl,
        ]);

        // 8. Verificar Encoding (UTF-8)
        $guardada = $I->grabFromDatabase('proveedores', 'razon_social_proveedor', ['rif_proveedor' => $rifCompleto]);
        $I->assertStringContainsString('C.A.', $guardada);
    }

    /**
     * UAT-009: Al consultar un proveedor, se muestran sus datos exactos (RF-11.4.2.1)
     */
    public function verificarConsultaProveedor(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión y crear un proveedor con RIF "J" (para probar el Nº Control)
        $this->login($I);
        
        $I->amOnPage('/proveedores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $letraRif = 'J';
        $numeroRif = '77665544';
        $rifCompleto = $letraRif . $numeroRif;
        $razonExacta = 'Consulta Proveedor C.A.';
        $nroControlExacto = '0012345';
        $prefijoExacto = '0424';
        $telefonoExacto = '9876543';
        $correoExacto = 'consulta_prov@example.com';
        $direccionExacta = 'Avenida Consulta Proveedor 456';

        $I->selectOption('.modalRegistrar select[name="codigo_rif_proveedor"]', $letraRif);
        $I->waitForElementClickable('.modalRegistrar input[name="nro_control_factura"]', 5);
        
        $I->fillField('.modalRegistrar input[name="rif_proveedor"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_proveedor"]', $razonExacta);
        $I->fillField('.modalRegistrar input[name="nro_control_factura"]', $nroControlExacto);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_proveedor"]', $prefijoExacto);
        $I->fillField('.modalRegistrar input[name="telefono_proveedor"]', $telefonoExacto);
        $I->fillField('.modalRegistrar input[name="correo_proveedor"]', $correoExacto);
        $I->fillField('.modalRegistrar textarea[name="direccion_proveedor"]', $direccionExacta);
        
        $I->click('.modalRegistrar button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Proveedor registrado', 10, '.swal2-title');
        $I->makeScreenshot('UAT009_Exito_Consulta_Proveedor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // 2. Ir a la tabla y hacer clic en el botón de Editar (Consultar)
        $I->amOnPage('/proveedores');
        $I->waitForElementVisible('.tabla-ajax', 10);
        
        $selectorEditar = "a.botonEditar[value='$rifCompleto']";
        $I->waitForElementClickable($selectorEditar, 15);
        $I->click($selectorEditar);

        // 3. Esperar que se abra el modal de Actualizar
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2); // Dar tiempo al JS para inyectar los datos vía AJAX

        // 4. VALIDACIÓN: Comprobar que todos los campos tienen exactamente los datos guardados
        $I->seeInField('.modalActualizar input[name="rif_proveedor"]', $rifCompleto);
        $I->seeInField('.modalActualizar input[name="razon_social_proveedor"]', $razonExacta);
        $I->seeOptionIsSelected('.modalActualizar select[name="prefijo_telefono_proveedor"]', $prefijoExacto);
        $I->seeInField('.modalActualizar input[name="telefono_proveedor"]', $telefonoExacto);
        $I->seeInField('.modalActualizar input[name="correo_proveedor"]', $correoExacto);
        $I->seeInField('.modalActualizar textarea[name="direccion_proveedor"]', $direccionExacta);

        // 5. VALIDACIÓN ESPECIAL: El campo de control debe estar visible y con el valor correcto
        $I->seeElement('.modalActualizar .contenedorNumControl'); // El contenedor debe estar visible
        $I->seeInField('.modalActualizar input[name="nro_control_factura"]', $nroControlExacto); // Y tener el número

        // Cerrar el modal de forma segura
        $I->executeJS("document.querySelector('.modalActualizar .btn-close').click();");
        $I->wait(1);
    }

    /**
     * UAT-008: Validar que no se pueda guardar un proveedor con campos vacíos
     */
    public function verificarCamposObligatoriosVaciosProveedor(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Ir a proveedores y abrir el modal
        $I->amOnPage('/proveedores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        // 3. Hacer clic en Guardar SIN LLENAR NADA
        $I->click('.modalRegistrar button[type="submit"]');

        // 4. El sistema primero muestra la confirmación de "¿Estás seguro?"
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        
        // 5. Aceptamos la confirmación
        $I->click('.swal2-confirm');

        // 6. AHORA el sistema valida y muestra el error (con un pequeño delay por el AJAX)
        $I->waitForText('Hay campos inválidos', 10, '.swal2-title');
        $I->see('No se puede enviar el formulario con campos inválidos');
        $I->makeScreenshot('UAT008_Campos_Obligatorios_Vacios_Proveedor');
        
        // 7. Cerrar la alerta de error
        $I->click('.swal2-confirm');
        $I->wait(1);

        // 8. LA PRUEBA DE FUEGO: Verificar que NO se guardó ningún proveedor en la BD
        $I->dontSeeInDatabase('proveedores', [
            'razon_social_proveedor' => '', // Si dejó pasar el vacío, sería un error grave
        ]);
    }

     /**
     * Función auxiliar para crear un proveedor rápido y retornar su RIF
     */
    private function crearProveedorRapido(AcceptanceTester $I): string
    {
        $I->amOnPage('/proveedores');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $letraRif = 'V'; // Usamos V para no requerir Nro de Control
        $numeroRif = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $rifCompleto = $letraRif . $numeroRif;

        $I->selectOption('.modalRegistrar select[name="codigo_rif_proveedor"]', $letraRif);
        $I->fillField('.modalRegistrar input[name="rif_proveedor"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_proveedor"]', 'Proveedor Test ' . rand(100, 999) . ' C.A.');
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_proveedor"]', '0414');
        $I->fillField('.modalRegistrar input[name="telefono_proveedor"]', str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT));
        $I->fillField('.modalRegistrar input[name="correo_proveedor"]', 'prov_' . rand(1000, 9999) . '@example.com');
        $I->fillField('.modalRegistrar textarea[name="direccion_proveedor"]', 'Calle Falsa 123');
        
        $I->click('.modalRegistrar button[type="submit"]');
        
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Proveedor registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2); 

        return $rifCompleto;
    }

    /**
     * UAT-010: Listar proveedores (solo vigentes, status != 0)
     */
    public function verificarListadoSoloVigentesProveedores(AcceptanceTester $I): void
    {
        $this->login($I);
        $rif = $this->crearProveedorRapido($I);

        // 1. Verificar que el proveedor APARECE en la tabla
        $I->amOnPage('/proveedores');
        $I->waitForElementVisible('.tabla-ajax', 10);
        $I->wait(2);
        $I->see($rif, '.tabla-ajax'); // Debe estar visible

        // 2. Anular el proveedor
        $I->click(['css' => "li.botonEliminar[value='$rif'] a"]);
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Proveedor eliminado', 10, '.swal2-title');
        $I->makeScreenshot('UAT010_Listado_Solo_Vigentes_Proveedor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(3); // Esperar que la tabla AJAX se recargue

        // 3. Verificar que el proveedor YA NO ESTÁ en la tabla
        $I->dontSee($rif, '.tabla-ajax');

        // 4. Verificar en BD que sigue existiendo pero inactivo
        $I->seeInDatabase('proveedores', [
            'rif_proveedor' => $rif,
            'status'        => 0,
        ]);
    }

    /**
     * UAT-011: Actualizar proveedor (verificar el UPDATE en BD)
     */
    public function verificarActualizacionProveedor(AcceptanceTester $I): void
    {
        $this->login($I);
        $rif = $this->crearProveedorRapido($I);

        // 1. Abrir modal de edición
        $I->amOnPage('/proveedores');
        $selectorEditar = "a.botonEditar[value='$rif']";
        $I->waitForElementClickable($selectorEditar, 15);
        $I->click($selectorEditar);
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2);

        // 2. Modificar datos
        $razonModificada = 'Proveedor Modificado ' . rand(100, 999) . ' C.A.';
        $direccionModificada = 'Nueva Direccion ' . rand(100, 999);

        $I->fillField('.modalActualizar input[name="razon_social_proveedor"]', $razonModificada);
        $I->fillField('.modalActualizar textarea[name="direccion_proveedor"]', $direccionModificada);
        
        // 3. Guardar cambios
        $I->click('.modalActualizar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Proveedor actualizado', 10, '.swal2-title');
        $I->makeScreenshot('UAT011_Exito_Actualizacion_Proveedor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // 4. Verificar en BD que el UPDATE se aplicó
        $I->seeInDatabase('proveedores', [
            'rif_proveedor'          => $rif,
            'razon_social_proveedor' => $razonModificada,
            'direccion_proveedor'    => $direccionModificada,
            'status'                 => 1
        ]);
    }

    /**
     * UAT-012: Anular proveedor (verificar borrado lógico status = 0)
     */
    public function verificarAnulacionProveedor(AcceptanceTester $I): void
    {
        $this->login($I);
        $rif = $this->crearProveedorRapido($I);

        // 1. Ir a la tabla y anular
        $I->amOnPage('/proveedores');
        $selectorEliminar = "li.botonEliminar[value='$rif'] a";
        $I->waitForElementClickable($selectorEliminar, 15);
        $I->click($selectorEliminar);

        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro de eliminar el registro?', '.swal2-html-container');
        $I->click('.swal2-confirm');
        
        // 2. Esperar mensaje de éxito
        $I->waitForText('Proveedor eliminado', 10, '.swal2-title');
        $I->makeScreenshot('UAT012_Exito_Anulacion_Proveedor');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // 3. LA PRUEBA DE FUEGO (Borrado Lógico)
        $I->seeInDatabase('proveedores', [
            'rif_proveedor' => $rif,
            'status'        => 0 // Cambió a 0
        ]);

        // 4. Confirmar que la fila NO se borró físicamente
        $I->seeNumRecords(1, 'proveedores', ['rif_proveedor' => $rif]);
    }

public function _after(AcceptanceTester $I): void {
    // 1. Foto de seguridad al finalizar el test
    $I->makeScreenshot('final_proveedores012_' . uniqid());
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