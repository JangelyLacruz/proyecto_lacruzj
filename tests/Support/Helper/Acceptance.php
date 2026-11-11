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
  public function abjuntoElArchivoAlCampoHTML(string $selectorCss, string $rutaRelativaArchivo): void {
    $cleanPath = ltrim($rutaRelativaArchivo, '/\\');
    $fullPath = \ROOT_PATH . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cleanPath);
    if (!file_exists($fullPath)) $this->fail("El archivo a adjuntar no existe en la ruta: {$fullPath}");
    $codeceptionPath = '../../../' . str_replace('\\', '/', $cleanPath);
    /** @var \Codeception\Module\WebDriver $webDriver */
    $webDriver = $this->getModule('WebDriver');
    $webDriver->attachFile($selectorCss, $codeceptionPath);
  }
}
