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

    $I->seeElement('form.formularioAjax.login');
    $I->fillField('input[name="usuario_usuario"]', 'Ander123');
    $I->fillField('input[name="contrasena1_usuario"]', 'David01*');
    $I->click('form.formularioAjax.login button[type="submit"]');
    $I->waitForElementVisible('.swal2-confirm.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForJS("return window.location.href.includes('/reportes') || window.location.href.includes('/dashboard');", 10);

    $I->amOnPage('/usuarios');
    $I->seeInCurrentUrl('/usuarios');

    $sufijo = bin2hex(random_bytes(3));
    $cedula = (string) (70000000 + random_int(0, 9999999));

    $I->click('button[data-bs-target=".modalRegistrar"]');
    $I->waitForElementVisible('.modalRegistrar', 10);

    $I->selectOption('.modalRegistrar select[name="codigo_cedula_usuario"]', 'V');
    $I->fillField('.modalRegistrar input[name="cedula_usuario"]', $cedula);
    $I->fillField('.modalRegistrar input[name="nombre_usuario"]', 'PRUEBA');
    $I->fillField('.modalRegistrar input[name="apellido_usuario"]', 'AUTOMATIZADA');
    $I->selectOption('.modalRegistrar select[name="prefijo_telefono_usuario"]', '0424');
    $I->fillField('.modalRegistrar input[name="telefono_usuario"]', '555' . random_int(1000, 9999));
    $I->fillField('.modalRegistrar input[name="correo_usuario"]', 'qa-' . $sufijo . '@example.com');
    $I->fillField('.modalRegistrar input[name="usuario_usuario"]', 'qa' . $sufijo . '12');

    $I->waitForJS(
      "return document.querySelector('.modalRegistrar select[name=\"id_rol\"] option:nth-child(2)') !== null;",
      10
    );
    $rolRegistro = $I->executeJS(
      "return document.querySelector('.modalRegistrar select[name=\"id_rol\"] option:nth-child(2)').value;"
    );
    $I->assertTrue($rolRegistro !== '', 'El formulario de registro debe cargar roles seleccionables.');
    $I->selectOption('.modalRegistrar select[name="id_rol"]', ['value' => $rolRegistro]);
    $I->attachFile('.modalRegistrar input[name="foto_usuario"]', '../../../src/assets/images/logo.png');
    $I->fillField('.modalRegistrar input[name="direccion_usuario"]', 'Direccion de prueba automatizada');
    $I->fillField('.modalRegistrar input[name="contrasena1_usuario"]', 'Prueba1234');
    $I->fillField('.modalRegistrar input[name="contrasena2_usuario"]', 'Prueba1234');

    $I->click('.modalRegistrar .btnMostrarOcultarContrasena');
    $I->seeElement('.modalRegistrar input[name="contrasena1_usuario"][type="text"]');
    $I->click('.modalRegistrar .btnMostrarOcultarContrasena');
    $I->seeElement('.modalRegistrar input[name="contrasena1_usuario"][type="password"]');

    $I->click('.modalRegistrar .grupoConfirmarContrasena .btnMostrarOcultarContrasena');
    $I->seeElement('.modalRegistrar input[name="contrasena2_usuario"][type="text"]');
    $I->click('.modalRegistrar .grupoConfirmarContrasena .btnMostrarOcultarContrasena');
    $I->seeElement('.modalRegistrar input[name="contrasena2_usuario"][type="password"]');

    $I->click('.modalRegistrar button[data-bs-target=".modalPreguntasSeguridad"]');
    $I->waitForElementVisible('.modalPreguntasSeguridad', 10);
    $I->waitForJS(
      "return document.querySelectorAll('.modalPreguntasSeguridad .selectPregunta option').length > 1;",
      10
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
      $I->selectOption(
        '.modalPreguntasSeguridad select[name="' . $nombrePregunta . '"]',
        ['value' => $valorPregunta]
      );
      $I->fillField(
        '.modalPreguntasSeguridad input[name="preguntas_respuestas-' . $indicePregunta . '-respuesta"]',
        'Respuesta de prueba ' . ($indicePregunta + 1)
      );
    }
    $I->click('.modalPreguntasSeguridad button[type="submit"]');
    $I->waitForElementVisible('.swal2-cancel.swal2-styled', 10);
    $I->click('.swal2-confirm.swal2-styled');
    $I->waitForElementVisible('.swal2-icon.swal2-success.swal2-icon-show', 10);
  }
}
