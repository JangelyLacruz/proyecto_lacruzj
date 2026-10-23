<?php

namespace Tests\Support\Helper;

// ¡Aquí está la clave!: Debe extender de \Codeception\Module
class Acceptance extends \Codeception\Module {
  public function verificoLaCargaDeLaImagen(string $selectorCss): void {
    /** @var \Codeception\Module\WebDriver $webdriver */
    $webdriver = $this->getModule('WebDriver');
    $webdriver->seeElement($selectorCss);
    $js = sprintf("
      const img = document.querySelector('%s');
      return img && img.complete && img.naturalWidth > 0;
    ", addslashes($selectorCss));
    $cargo = $webdriver->executeJS($js);
    $this->assertTrue($cargo, "La imagen con selector '{$selectorCss}' no cargó o está rota.");
  }
}
