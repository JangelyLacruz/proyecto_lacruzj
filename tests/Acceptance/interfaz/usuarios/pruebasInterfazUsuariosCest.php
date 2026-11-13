<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasUsuariosCest {
  public function verificarElementosLandingPage(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->seeInCurrentUrl('/');
    $I->verificoLaCargaDeLaImagen('.nav-logo-img');
    $elementosInteractivos = [
      '.nav-logo[href="#inicio"]',
      '.nav-links a[href="#inicio"]',
      '.nav-links a[href="#quienes"]',
      '.nav-links a[href="#servicios"]',
      '.nav-links a[href="#catalogo"]',
      '.nav-links a[href="#clientes"]',
      '.nav-links a[href="#contacto"]',
      '.nav-phone-btn[href^="tel:"]',
      '#btnAbrirCarritoNav',
      '#navBtnLogin',
      '#hamburgerBtn',
      '#mobileMenu a[href="#inicio"]',
      '#mobileMenu a[href="#quienes"]',
      '#mobileMenu a[href="#servicios"]',
      '#mobileMenu a[href="#catalogo"]',
      '#mobileMenu a[href="#clientes"]',
      '#mobileMenu a[href="#contacto"]',
      '#mobileUserGuest a.mobile-login-link',
      '#carouselTrack',
      '#carouselDots',
      '#buscadorCatalogoPagina',
      '#catalogoGridWrapper',
      '#catalogoVacioState',
      '.catalogo [data-bs-target="#cartOffcanvas"]',
      '.ecommerce-banner a[href="usuarios/login"]',
      '.ecommerce-banner a[href="#contacto"]',
      '#formContactoPagina',
      '#contactoNombre',
      '#contactoTelefono',
      '#contactoMensaje',
      '#formContactoPagina .enviarContactoWA',
      '.contacto a[href^="https://api.whatsapp.com/"]',
      '#mapaContacto',
      '.contacto a[href^="https://maps.app.goo.gl/"]',
      '#chatbot-toggle-btn',
      '#chatbot-close-btn',
      '#chatbot-form',
      '#chatbot-input',
      '#chatbot-form button[type="submit"]',
      '#chatbot-suggestions .btnSugerenciaChatBot',
      '#cartOffcanvas',
      '#cartOffcanvas [data-bs-dismiss="offcanvas"]',
      '#cartOffcanvas .btnTipoPago[data-tipo_pago="bs"]',
      '#cartOffcanvas .btnTipoPago[data-tipo_pago="usd"]',
      '#btnCartLogin',
      '#btnCartProcesar',
      '#modalCheckoutPedido',
      '#formCheckoutPedido',
      '#optDelivery',
      '#optRetiro',
      '#checkoutDireccion',
      '#formCheckoutPedido input[name="metodo_pago"]',
      '#checkoutObservaciones',
      '#btnConfirmarPedidoWeb',
      '#btnMisPedidosNav',
      '#modalMisPedidos',
    ];
    foreach ($elementosInteractivos as $selector) {
      $I->seeElementInDOM($selector);
    }
    $I->scroleoHastaElElemento('#buscadorCatalogoPagina');
    $I->llenoElCampo('#buscadorCatalogoPagina', 'Cloro');
    $I->cliqueoElCampo('#btnAbrirCarritoNav');
    $I->cliqueoElCampo('#cartOffcanvas [data-bs-dismiss="offcanvas"]');
    $I->abroElModal('#chatbot-toggle-btn','#chatbot-window');
    $I->seeElement('#chatbot-suggestions .btnSugerenciaChatBot');
    $I->llenoElCampo('#chatbot-input', 'Consulta de prueba');
    $I->cierroElModal('#chatbot-close-btn', '#chatbot-window');
  }
  /**
   * @depends verificarElementosLandingPage
   */
  public function verificarBotonesYFormulariosDelModuloUsuarios(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->setCookie('TEMP', 'true');
    $I->amOnPage('/usuarios/login');

    $I->seeElement('form.formularioAjax.login');
    $I->llenoElCampo('input[name="usuario_usuario"]', 'Ander123');
    $I->llenoElCampo('input[name="contrasena1_usuario"]', 'David01*');
    $I->cliqueoElCampo('form.formularioAjax.login button[type="submit"]');
    $I->confirmoLaAlertaSweetAlert();
    $I->waitForJS(
      "return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');",
      30
    );
    $cookieSesion = $I->grabCookie('JLACRUZ');
    if (empty($cookieSesion)) {
      $I->fail('Fallo crítico: No se generó la cookie de sesión JLACRUZ tras enviar el formulario.');
    }

    $I->amOnPage('/usuarios');
    $I->seeInCurrentUrl('/usuarios');
    $sufijo = bin2hex(random_bytes(3));
    $cedula = (string) (70000000 + random_int(0, 9999999));

    $I->abroElModal('button[data-bs-target=".modalRegistrar"]','.modalRegistrar');

    $I->seleccionoLaOpcion('.modalRegistrar select[name="codigo_cedula_usuario"]', 'V');
    $I->llenoElCampo('.modalRegistrar input[name="cedula_usuario"]', $cedula);
    $I->llenoElCampo('.modalRegistrar input[name="nombre_usuario"]', 'PRUEBA');
    $I->llenoElCampo('.modalRegistrar input[name="apellido_usuario"]', 'AUTOMATIZADA');
    $I->seleccionoLaOpcion('.modalRegistrar select[name="prefijo_telefono_usuario"]', '0424');
    $I->llenoElCampo('.modalRegistrar input[name="telefono_usuario"]', '555' . random_int(1000, 9999));
    $I->llenoElCampo('.modalRegistrar input[name="correo_usuario"]', 'qa-' . $sufijo . '@example.com');
    $I->llenoElCampo('.modalRegistrar input[name="usuario_usuario"]', 'qa' . $sufijo . '12');

    $I->waitForJS(
      "return document.querySelector('.modalRegistrar select[name=\"id_rol\"] option:nth-child(2)') !== null;",
      30
    );
    $rolRegistro = $I->executeJS(
      "return document.querySelector('.modalRegistrar select[name=\"id_rol\"] option:nth-child(2)').value;"
    );
    $I->assertTrue($rolRegistro !== '', 'El formulario de registro debe cargar roles seleccionables.');
    $I->seleccionoLaOpcion('.modalRegistrar select[name="id_rol"]', $rolRegistro);
    $I->abjuntoElArchivoAlCampoHTML('.modalRegistrar input[name="foto_usuario"]', 'src/assets/images/logo.png');
    $I->llenoElCampo('.modalRegistrar input[name="direccion_usuario"]', 'Direccion de prueba automatizada');
    $I->llenoElCampo('.modalRegistrar input[name="contrasena1_usuario"]', 'Prueba1234');
    $I->llenoElCampo('.modalRegistrar input[name="contrasena2_usuario"]', 'Prueba1234');

    $I->verificoBotonDeMostrarContrasena(
      '.modalRegistrar .btnMostrarOcultarContrasena',
      '.modalRegistrar input[name="contrasena1_usuario"]'
    );
    $I->verificoBotonDeMostrarContrasena(
      '.modalRegistrar .grupoConfirmarContrasena .btnMostrarOcultarContrasena',
      '.modalRegistrar input[name="contrasena2_usuario"]'
    );
    $I->abroElModal('.modalRegistrar button[data-bs-target=".modalPreguntasSeguridad"]','.modalPreguntasSeguridad');
    $I->waitForJS(
      "return document.querySelectorAll('.modalPreguntasSeguridad .selectPregunta option').length > 1;",
      30
    );
    $opcionesPregunta = $I->executeJS(
      "return Array.from(document.querySelector('.modalPreguntasSeguridad .selectPregunta').options)
        .map(option => option.value)
        .filter(value => value !== '');"
    );
    $I->assertTrue(count($opcionesPregunta) >= 6, 'El registro debe ofrecer seis preguntas de seguridad distintas.');
    for ($indicePregunta = 0; $indicePregunta < 6; $indicePregunta++) {
      $nombrePregunta = 'preguntas_respuestas-' . $indicePregunta . '-id_pregunta';
      $valorPregunta = $opcionesPregunta[$indicePregunta];
      $I->seleccionoLaOpcion(
        '.modalPreguntasSeguridad select[name="' . $nombrePregunta . '"]',
        ['value' => $valorPregunta]
      );
      $I->llenoElCampo(
        '.modalPreguntasSeguridad input[name="preguntas_respuestas-' . $indicePregunta . '-respuesta"]',
        'Respuesta de prueba ' . ($indicePregunta + 1)
      );
    }
    $I->cliqueoElCampo('.modalPreguntasSeguridad button[type="submit"]');
    $I->canceloLaAlertaSweetAlert();
    $I->abroElModal('.modalPreguntasSeguridad button[data-bs-target=".modalDatosUsuarios"]','.modalDatosUsuarios');
    $I->cierroElModal('.modalDatosUsuarios.modalRegistrar button[data-bs-dismiss="modal"]','.modalDatosUsuarios.modalRegistrar .modal-dialog.modal-xl');
  }
  public function _afterClass(AcceptanceTester $I): void {
    $I->resetCookie('TEMP');
  }
}
