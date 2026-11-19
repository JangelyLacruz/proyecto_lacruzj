<?php
declare(strict_types=1);
namespace Tests\Acceptance\aceptacion;
use Tests\Support\AcceptanceTester;

final class pruebasAceptacionClienteCest {
  
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
  

  public function verificarFlujoCompletoCliente(AcceptanceTester $I): void {
    // 1. Iniciar sesión
    $this->login($I);

    // 2. Ir a clientes y abrir modal
    $I->amOnPage('/clientes');
    $I->seeInCurrentUrl('/clientes');
    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);
    
    $sufijo = strtoupper(bin2hex(random_bytes(3)));
    $abecedario = array_merge(
        range('A', 'Z'),
        ['Á','É','Í','Ó','Ú','Ü','Ñ','á','é','í','ó','ú','ü','ñ']
    );
    $letras = '';
    for ($i = 0; $i < 8; $i++) {
        $letras .= $abecedario[array_rand($abecedario)];
    }

    $nueva_razon_social_cliente = $letras . ' Cía., S.L. ' . rand(100, 999) . ' (' . rand(1, 99) . '/7)';

    $operadoras = ['0416', '0426', '0424', '0414', '0412', '0422', '0212', '0251', '0241', '0257'];
    
    $nuevo_correo_cliente = 'cliente_' . $sufijo . '@example.com';

    $iniciosDireccion = ['Calle', 'Avenida', 'Urbanización', 'Sector', 'Edificio', 'Res.', 'Carrera', 'Vereda'];
    $nueva_direccion_cliente = $iniciosDireccion[array_rand($iniciosDireccion)]
        . ' ' . rand(1, 200) . ' #' . rand(1, 99) . '-' . rand(10, 99);
    
    // 1. Rif/Cedula 
    $codigosRif = ['V','E','J','G','C','P'];
    $letraRif = $codigosRif[array_rand($codigosRif)];
    $numeroRif = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    
    $I->selectOption('.modalRegistrar select[name="codigo_rif_cedula_cliente"]', $letraRif);
    $I->fillField('.modalRegistrar input[name="rif_cedula_cliente"]', $numeroRif);

    // 2. Razón social
    $I->fillField('.modalRegistrar input[name="razon_social_cliente"]', $nueva_razon_social_cliente);

    // 3. Teléfono 
    $operadoras = ['0416', '0426', '0424', '0414', '0412', '0422', '0212', '0251', '0241', '0257'];
    $operadora = $operadoras[array_rand($operadoras)];
    $numeroTelefono = str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);

    $I->selectOption('.modalRegistrar select[name="prefijo_telefono_cliente"]', $operadora);
    $I->fillField('.modalRegistrar input[name="telefono_cliente"]', $numeroTelefono);

    // 4. Correo
    $I->fillField('.modalRegistrar input[name="correo_cliente"]', $nuevo_correo_cliente);

    // 5. Dirección 
    $I->fillField('.modalRegistrar textarea[name="direccion_cliente"]', $nueva_direccion_cliente);
   
   $I->click('.modalRegistrar button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->see('¿Estás seguro?', '.swal2-title');
    $I->click('.swal2-confirm');
    $I->waitForText('Cliente registrado', 10, '.swal2-title');
    $I->see('El cliente ha sido registrado exitosamente');
    $I->makeScreenshot('exito_registro_cliente');
    $I->click('.swal2-confirm');
    $I->wait(2);
    $I->seeInDatabase('clientes', [
        'rif_cedula_cliente'   => $letraRif . $numeroRif,
        //'razon_social_cliente' => $nueva_razon_social_cliente,
        'status'               => 1,
    ]);
    $guardada = $I->grabFromDatabase('clientes', 'razon_social_cliente', ['rif_cedula_cliente' => $letraRif . $numeroRif]);
    //$I->assertSame($nueva_razon_social_cliente, $guardada, 'La razón social se guardó corrupta (encoding roto)');
    $I->assertStringContainsString('Cía., S.L.', $guardada, 'La razón social perdió los acentos o el formato');
    }
  
    /**
     * UAT-002: Validar que no se pueda guardar un cliente con campos vacíos
     */
    public function verificarCamposObligatoriosVacios(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Ir a clientes y abrir el modal
        $I->amOnPage('/clientes');
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
        
        // Cerrar la alerta de error
        $I->click('.swal2-confirm');
        $I->wait(1);

        // 7. LA PRUEBA DE FUEGO: Verificar que NO se guardó ningún cliente en la BD
        $I->seeNumRecords(0, 'clientes');
    }

    /**
   * Función auxiliar para no repetir el código de crear cliente
   */
  private function crearClienteRapido(AcceptanceTester $I): string
  {
      $I->amOnPage('/clientes');
      $I->click('button[data-bs-target=".modalRegistrar"]');
      $I->waitForElementVisible('.modalRegistrar', 10);

      $letraRif = ['V','E','J'][array_rand(['V','E','J'])];
      $numeroRif = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
      $rifCompleto = $letraRif . $numeroRif;

      $I->selectOption('.modalRegistrar select[name="codigo_rif_cedula_cliente"]', $letraRif);
      $I->fillField('.modalRegistrar input[name="rif_cedula_cliente"]', $numeroRif);
      $I->fillField('.modalRegistrar input[name="razon_social_cliente"]', 'Cliente Para Anular ' . rand(100, 999));
      $I->selectOption('.modalRegistrar select[name="prefijo_telefono_cliente"]', '0414');
      $I->fillField('.modalRegistrar input[name="telefono_cliente"]', '1234567');
      $I->fillField('.modalRegistrar input[name="correo_cliente"]', 'anular_' . rand(1000, 9999) . '@example.com');
      $I->fillField('.modalRegistrar textarea[name="direccion_cliente"]', 'Calle Falsa 123');
      
      $I->click('.modalRegistrar button[type="submit"]');
      
      $I->waitForElementVisible('.swal2-confirm', 10);
      $I->click('.swal2-confirm');
      $I->waitForText('Cliente registrado', 10, '.swal2-title');
      $I->click('.swal2-confirm');
      $I->wait(2); 

      return $rifCompleto;
  }

    /**
     * UAT-003: El listado muestra solo clientes vigentes (status != 0)
     */
    public function verificarListadoSoloVigentes(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Crear un cliente usando la función auxiliar
        $rif = $this->crearClienteRapido($I);

        // 3. Verificar que el cliente APARECE en la tabla
        $I->amOnPage('/clientes');
        $I->waitForElementVisible('.tabla-ajax', 10);
        $I->wait(2); // Esperar a que el AJAX cargue la tabla
        $I->see($rif, '.tabla-ajax'); // El RIF debe estar visible en la tabla

        // 4. Anular (eliminar lógicamente) el cliente
        $I->click(['css' => ".botonEliminar[value='$rif'] a"]);
        
        // Confirmar el SweetAlert de eliminación
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro de eliminar el registro?', '.swal2-html-container');
        $I->click('.swal2-confirm');

        // Esperar el mensaje de éxito y cerrarlo
        $I->waitForText('Cliente eliminado', 10, '.swal2-title');
        $I->makeScreenshot('UAT003_Exito_Solo_Vigentes');
        $I->click('.swal2-confirm');
        
        // 5. EL TEST DE LA INTERFAZ: Esperar que la tabla se recargue y verificar que el RIF YA NO ESTÁ
        $I->wait(3); // El sistema recarga la tabla vía AJAX (reiniciarDataTables)
        $I->dontSee($rif, '.tabla-ajax'); // El RIF NO debe estar en la tabla

        // 6. LA PRUEBA DE FUEGO (BD): Comprobar que sigue en la base de datos pero inactivo
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente' => $rif,
            'status'             => 0,
        ]);
    }
  
     /**
     * UAT-004: Al consultar un cliente, se muestran sus datos exactos (RF-1.2.1)
     */
    public function verificarConsultaCliente(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Crear un cliente con datos exactos para la prueba
        $I->amOnPage('/clientes');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $letraRif = 'J';
        $numeroRif = '99887766';
        $rifCompleto = $letraRif . $numeroRif;
        $razonExacta = 'Consulta Exacta Cía., S.L. 123 (1/7)';
        $prefijoExacto = '0414';
        $telefonoExacto = '7654321';
        $correoExacto = 'consulta_exacta@example.com';
        $direccionExacta = 'Avenida Consulta Exacta 123';

        $I->selectOption('.modalRegistrar select[name="codigo_rif_cedula_cliente"]', $letraRif);
        $I->fillField('.modalRegistrar input[name="rif_cedula_cliente"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_cliente"]', $razonExacta);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_cliente"]', $prefijoExacto);
        $I->fillField('.modalRegistrar input[name="telefono_cliente"]', $telefonoExacto);
        $I->fillField('.modalRegistrar input[name="correo_cliente"]', $correoExacto);
        $I->fillField('.modalRegistrar textarea[name="direccion_cliente"]', $direccionExacta);
        
        $I->click('.modalRegistrar button[type="submit"]');
        
        // Aceptar confirmación y esperar éxito
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Cliente registrado', 10, '.swal2-title');
        $I->makeScreenshot('UAT004_Exito_Consulta_Cliente');
        $I->click('.swal2-confirm');
        
        // ESPERAR a que el SweetAlert desaparezca por completo para que no bloquee el clic
        $I->waitForElementNotVisible('.swal2-container', 10);

        // 3. Ir a la tabla y hacer clic en el botón de Editar/Consultar
        $I->amOnPage('/clientes');
        
        // El selector correcto: Es directamente la etiqueta <a> la que tiene la clase y el value
        $selectorEditar = "a.botonEditar[value='$rifCompleto']";
        
        // Esperar hasta 15 segundos a que el DataTable (AJAX) cargue la fila con el botón
        $I->waitForElementClickable($selectorEditar, 15);
        $I->click($selectorEditar);

        // 4. Esperar que se abra el modal de Actualizar
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2); // Esperar que el JS llene los inputs

        // 5. VALIDACIÓN: Comprobar que cada campo tiene exactamente los datos guardados
        $I->seeInField('.modalActualizar input[name="rif_cedula_cliente"]', $rifCompleto);
        $I->seeInField('.modalActualizar input[name="razon_social_cliente"]', $razonExacta);
        $I->seeInField('.modalActualizar select[name="prefijo_telefono_cliente"]', $prefijoExacto);
        $I->seeInField('.modalActualizar input[name="telefono_cliente"]', $telefonoExacto);
        $I->seeInField('.modalActualizar input[name="correo_cliente"]', $correoExacto);
        $I->seeInField('.modalActualizar textarea[name="direccion_cliente"]', $direccionExacta);

        // Cerrar el modal
        $I->executeJS("document.querySelector('.modalActualizar .btn-close').click();");
        $I->wait(1);
    }
    /**
     * UAT-005: Modificar un cliente actualiza la BD y conserva el RIF (RF-1.2.2)
     */
    public function verificarActualizacionCliente(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión
        $this->login($I);

        // 2. Crear un cliente base
        $I->amOnPage('/clientes');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $letraRif = 'V';
        $numeroRif = '11223344';
        $rifCompleto = $letraRif . $numeroRif;
        $razonOriginal = 'Cliente Original Cía.';
        
        $I->selectOption('.modalRegistrar select[name="codigo_rif_cedula_cliente"]', $letraRif);
        $I->fillField('.modalRegistrar input[name="rif_cedula_cliente"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_cliente"]', $razonOriginal);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_cliente"]', '0414');
        $I->fillField('.modalRegistrar input[name="telefono_cliente"]', '1234567');
        $I->fillField('.modalRegistrar input[name="correo_cliente"]', 'actualizar_' . rand(100,999) . '@example.com');
        $I->fillField('.modalRegistrar textarea[name="direccion_cliente"]', 'Direccion Original 123');
        
        $I->click('.modalRegistrar button[type="submit"]');
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Cliente registrado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);

        // 3. Buscar el cliente en la tabla y abrir el modal de edición
        $I->amOnPage('/clientes');
        $selectorEditar = "a.botonEditar[value='$rifCompleto']";
        $I->waitForElementClickable($selectorEditar, 15);
        $I->click($selectorEditar);
        $I->waitForElementVisible('.modalActualizar', 10);
        $I->wait(2); // Esperar que el AJAX llene el formulario

        // 4. Modificar la razón social y la dirección
        $razonModificada = 'Cliente Modificado Cía.';
        $direccionModificada = 'Nueva Direccion 456';

        $I->fillField('.modalActualizar input[name="razon_social_cliente"]', $razonModificada);
        $I->fillField('.modalActualizar textarea[name="direccion_cliente"]', $direccionModificada);
        
        // 5. Hacer clic en Guardar Cambios
        $I->click('.modalActualizar button[type="submit"]');
        
        // 6. Aceptar la confirmación y esperar el mensaje de éxito
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->click('.swal2-confirm');
        
        $I->waitForText('Cliente actualizado', 10, '.swal2-title');
        $I->see('El cliente ha sido actualizado exitosamente');
        $I->makeScreenshot('UAT005_Exito_Actualizacion');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2); // Esperar recarga de tabla

        // 7. LA PRUEBA DE FUEGO (BD): Comprobar que los datos cambiaron en MySQL
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente'   => $rifCompleto,
            'razon_social_cliente' => $razonModificada,
            'direccion_cliente'    => $direccionModificada,
            'status'               => 1, // Sigue vigente
        ]);

        // 8. Confirmar que el dato viejo ya no está en la BD
        $I->dontSeeInDatabase('clientes', [
            'rif_cedula_cliente'   => $rifCompleto,
            'razon_social_cliente' => $razonOriginal,
        ]);
    }

    /**
     * UAT-006: Anular un cliente cambia status = 0, pero no borra la fila de la BD (RF-1.2.3)
     */
    public function verificarAnulacionCliente(AcceptanceTester $I): void
    {
        // 1. Iniciar sesión y crear un cliente
        $this->login($I);
        $rif = $this->crearClienteRapido($I);

        // 2. Verificar que el cliente está vigente (status = 1)
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente' => $rif,
            'status'             => 1,
        ]);

        // 3. Ir al listado
        $I->amOnPage('/clientes');
        $I->waitForElementVisible('.tabla-ajax', 10);
        
        // El selector correcto: La clase .botonEliminar y el value están en el <li>
        $selectorEliminar = "li.botonEliminar[value='$rif'] a";
        
        // Esperar a que el AJAX cargue la tabla y el botón sea clickeable
        $I->waitForElementClickable($selectorEliminar, 15);
        $I->click($selectorEliminar);

        // 4. Confirmar el SweetAlert de eliminación
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->see('¿Estás seguro de eliminar el registro?', '.swal2-html-container');
        $I->click('.swal2-confirm');

        // 5. Verificar el mensaje de éxito
        $I->waitForText('Cliente eliminado', 10, '.swal2-title');
        
        // Evidencia para el acta
        $I->makeScreenshot('UAT006_Exito_Anulacion');
        
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2); // Dar tiempo a que la tabla se actualice

        // 6. PRUEBA DE FUEGO: Comprobar que en la BD NO se borró la fila, solo cambió el status
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente' => $rif,
            'status'             => 0, 
        ]);

        // 7. Verificación extra: Asegurarnos de que hay exactamente 1 registro con ese RIF
        $I->seeNumRecords(1, 'clientes', ['rif_cedula_cliente' => $rif]);
    }

     /**
     * UAT-025: Registrar un cliente con el RIF de uno ya anulado debe reciclar (actualizar) el registro
     */
    public function verificarReciclajeRifAnulado(AcceptanceTester $I): void
    {
        $this->login($I);

        // 1. Crear un cliente rápido y obtener su RIF
        $rif = $this->crearClienteRapido($I);

        // 2. Anular ese cliente (Borrado Lógico)
        $I->amOnPage('/clientes');
        $I->waitForElementClickable("li.botonEliminar[value='$rif'] a", 15);
        $I->click("li.botonEliminar[value='$rif'] a");
        $I->waitForElementVisible('.swal2-popup', 10);
        $I->click('.swal2-confirm');
        $I->waitForText('Cliente eliminado', 10, '.swal2-title');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Verificar que quedó anulado en la BD
        $I->seeInDatabase('clientes', ['rif_cedula_cliente' => $rif, 'status' => 0]);

        // 3. Intentar crear un NUEVO cliente usando el MISMO RIF (con datos nuevos)
        $I->amOnPage('/clientes');
        $I->click('button[data-bs-target=".modalRegistrar"]');
        $I->waitForElementVisible('.modalRegistrar', 10);

        $letraRif = substr($rif, 0, 1);
        $numeroRif = substr($rif, 1);
        
        $nuevaRazon = 'Cliente Revivido Cía.';

        $I->selectOption('.modalRegistrar select[name="codigo_rif_cedula_cliente"]', $letraRif);
        $I->fillField('.modalRegistrar input[name="rif_cedula_cliente"]', $numeroRif);
        $I->fillField('.modalRegistrar input[name="razon_social_cliente"]', $nuevaRazon);
        $I->selectOption('.modalRegistrar select[name="prefijo_telefono_cliente"]', '0414');
        $I->fillField('.modalRegistrar input[name="telefono_cliente"]', '9876543');
        $I->fillField('.modalRegistrar input[name="correo_cliente"]', 'revivido_' . rand(100, 999) . '@example.com');
        $I->fillField('.modalRegistrar textarea[name="direccion_cliente"]', 'Calle Revivido 123');
        
        $I->click('.modalRegistrar button[type="submit"]');

        // ==========================================
        // 4. LA PRUEBA DE FUEGO
        // ==========================================
        // El sistema debe mostrar el mensaje de éxito (porque lo recicló y actualizó)
        $I->waitForElementVisible('.swal2-confirm', 10);
        $I->see('¿Estás seguro?', '.swal2-title');
        $I->click('.swal2-confirm');

        $I->waitForText('Cliente registrado', 10, '.swal2-title');
        $I->makeScreenshot('UAT025_Exito_Reciclaje_RIF');
        $I->click('.swal2-confirm');
        $I->waitForElementNotVisible('.swal2-container', 10);
        $I->wait(2);

        // Verificar en la BD:
        // 1. Solo debe haber 1 registro con ese RIF (no hay duplicados)
        $I->seeNumRecords(1, 'clientes', ['rif_cedula_cliente' => $rif]);

        // 2. El registro debe estar vigente otra vez (status = 1)
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente' => $rif,
            'status'             => 1
        ]);

        // 3. Los datos deben haberse actualizado con los nuevos
        $I->seeInDatabase('clientes', [
            'rif_cedula_cliente'   => $rif,
            'razon_social_cliente' => $nuevaRazon
        ]);
    }
    
  public function _after(AcceptanceTester $I): void {
    $I->makeScreenshot('final_test025_' . uniqid());
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