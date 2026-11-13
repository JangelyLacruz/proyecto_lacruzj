<?php

namespace Tests\Support\Helper;

// ¡Aquí está la clave!: Debe extender de \Codeception\Module
class Acceptance extends \Codeception\Module {
  private function getWebDriver(): \Codeception\Module\WebDriver {
    /** @var \Codeception\Module\WebDriver $webdriver */
    $webdriver = $this->getModule('WebDriver');
    return $webdriver;
  }
  public function verificoLaCargaDeLaImagen(string $selectorCss): void {
    $I = $this->getWebDriver();
    $I->seeElement($selectorCss);
    $js = sprintf("
      const img = document.querySelector('%s');
      return img && img.complete && img.naturalWidth > 0;
    ", addslashes($selectorCss));
    $cargo = $I->executeJS($js);
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
    $I = $this->getWebDriver();
    $I->attachFile($selector, $codeceptionPath);
  }
  public function cliqueoElCampo(string $selector, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $I->waitForElement($selector, $timeout);
    $I->waitForElementVisible($selector, $timeout);
    $I->waitForElementClickable($selector, $timeout);
    $I->click($selector);
  }
  public function llenoElCampo(string $selector, string $valor, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $I->waitForElement($selector, $timeout);
    $this->scroleoHastaElElemento($selector, $timeout);
    $I->waitForElementVisible($selector, $timeout);
    $I->waitForElementClickable($selector, $timeout);
    $I->fillField($selector, $valor);
    $I->seeInField($selector, $valor);
  }
  public function seleccionoLaOpcion(string $selector, string|array $opcion, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $I->waitForElement($selector, $timeout);
    $I->waitForElementVisible($selector, $timeout);
    $I->selectOption($selector, $opcion);
  }
  public function abroElModal(string $selectorBotonAbrir, string $selectorModalQueSeDebeAbrir, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $this->cliqueoElCampo($selectorBotonAbrir, $timeout);
    $I->waitForElementVisible($selectorModalQueSeDebeAbrir, $timeout);
  }
  public function cierroElModal(string $selectorBotonCerrar, string $selectorModalContenedor, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $this->cliqueoElCampo($selectorBotonCerrar, $timeout);
    $I->waitForElementNotVisible($selectorModalContenedor, $timeout);
  }
  public function confirmoLaAlertaSweetAlert(int $timeout = 20): void {
    $this->cliqueoElCampo('.swal2-confirm.swal2-styled', $timeout);
    $this->getWebDriver()->waitForElementNotVisible('.swal2-container', $timeout);
  }
  public function canceloLaAlertaSweetAlert(int $timeout = 20): void {
    $this->cliqueoElCampo('.swal2-cancel.swal2-styled', $timeout);
    $this->getWebDriver()->waitForElementNotVisible('.swal2-container', $timeout);
  }
  public function veoElTexto(string $texto, ?string $selector = null, int $timeout = 20): void {
    $I = $this->getWebDriver();

    if ($selector !== null) {
      $I->waitForElementVisible($selector, $timeout);
    }
    $I->waitForText($texto, $timeout, $selector);
  }
  public function verificoBotonDeMostrarContrasena(string $selectorBotonCambio, string $selectorInputQueCambia, string $estadoInicial = 'contrasena'): void {
    $I = $this->getWebDriver();
    $tipo = $estadoInicial == 'contrasena' ? 'text' : 'password';
    $contrario = $estadoInicial == 'contrasena' ? 'password' : 'text';
    $this->cliqueoElCampo($selectorBotonCambio);
    $I->seeElement($selectorInputQueCambia . '[type="' . $tipo . '"]');
    $this->cliqueoElCampo($selectorBotonCambio);
    $I->seeElement($selectorInputQueCambia . '[type="' . $contrario . '"]');
  }
  public function scroleoHastaElElemento(string $selector, int $timeout = 20): void {
    $I = $this->getWebDriver();
    $I->waitForElement($selector, $timeout);
    $tieneScroll = $I->executeJS("
      const el = document.querySelector('{$selector}');
      if (!el) return false;

      let padre = el.parentElement;
      while (padre) {
        const estilo = window.getComputedStyle(padre);
        const puedeScrollear = (estilo.overflowY === 'auto' || estilo.overflowY === 'scroll');
        if (puedeScrollear && padre.scrollHeight > padre.clientHeight) {
            return true; // El modal o contenedor interno tiene scroll
        }
        padre = padre.parentElement;
      }

      // Si ningún contenedor padre scrollea, verificamos la página principal (body/html)
      return document.documentElement.scrollHeight > window.innerHeight;
    ");
    if ($tieneScroll) {
      $I->executeJS("
        document.querySelector('{$selector}')
        .scrollIntoView({ behavior: 'instant', block: 'center' });
      ");
    }
    $I->waitForElementVisible($selector, $timeout);
  }
}
