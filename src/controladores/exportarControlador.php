<?php

use src\modelos\exportarBDModelo;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  switch ($_POST['accion']) {
    case 'exportar':
      ob_clean();
      $exportar = new exportarBDModelo();
      $exportar->DECORE($exportar->exportar($_POST['BD']));
  }

  exit();
}
