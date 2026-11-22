<?php

declare(strict_types=1);

namespace Tests\Acceptance\humo;

use Tests\Support\AcceptanceTester;

final class pruebasInterfazUsuariosCest {
  public function verificarRegistrarUnUsuario(AcceptanceTester $I): void {
    $I->amOnPage('/');
    $I->resetCookie('JLACRUZ');
    $I->resetCookie('TEMP');
    $I->setCookie('TEMP', 'true');
    $I->amOnPage('/usuarios/login');

    $I->llenoElCampo('input[name="usuario_usuario"]', 'Ander123');
    $I->llenoElCampo('input[name="contrasena1_usuario"]', 'David01*');
    $I->cliqueoElCampo('form.formularioAjax.login button[type="submit"]');
    $I->confirmoLaAlertaSweetAlert();
    $I->waitForJS(
      "return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');",
      30
    );
    $I->amOnPage('/usuarios');
    $I->seeInCurrentUrl('/usuarios');

    $sufijo = bin2hex(random_bytes(3));
    $cedula = (string) (70000000 + random_int(0, 9999999));

    $I->abroElModal('button[data-bs-target=".modalRegistrar"]', '.modalRegistrar');
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
    $I->seleccionoLaOpcion('.modalRegistrar select[name="id_rol"]', ['value' => $rolRegistro]);
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
    $I->confirmoLaAlertaSweetAlert();
  }
  public function _afterClass(AcceptanceTester $I): void {
    $I->resetCookie('TEMP');
  }
}
