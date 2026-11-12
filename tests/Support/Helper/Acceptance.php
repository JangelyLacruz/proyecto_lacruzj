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
  public function abjuntoElArchivoAlCampoHTML(string $selector, string $rutaRelativaArchivo): void {
    $cleanPath = ltrim($rutaRelativaArchivo, '/\\');
    $rutaLimpia = \ROOT_PATH . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cleanPath);
    $rutaCompleta = realpath($rutaLimpia);
    if ($rutaCompleta === false || !file_exists($rutaCompleta)) {
      $this->fail("El archivo no existe en el repositorio (revisa mayúsculas o .gitignore): {$rutaLimpia}");
    }
    $dataDir = \ROOT_PATH . '/tests/Support/Data';
    if (!is_dir($dataDir)) mkdir($dataDir, 0775, true);
    $codeceptionPath = '../../../' . str_replace('\\', '/', $cleanPath);
    /** @var \Codeception\Module\WebDriver $webDriver */
    $webDriver = $this->getModule('WebDriver');
    $webDriver->attachFile($selector, $codeceptionPath);
  }
}
