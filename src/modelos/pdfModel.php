<?php

namespace src\modelos;

use FPDF;
use DateTime;

class pdfModel extends FPDF {

  use traitModelo;
  private string $tituloEncabezado = '';
  private bool $header = true;
  private bool $footer = true;
  private array $datosExtraCabecera = [];
  public array|bool $dataNotaEntrega = false;

  public function __construct($instrucciones = null) {
    $this->header = $instrucciones['header'] ?? true;
    $this->footer = $instrucciones['footer'] ?? true;
    $this->dataNotaEntrega = $instrucciones['datosNotaEntrega'] ?? false;
    switch ($instrucciones['tamanoPagina'] ?? '') {
      case 'carta':
        $instrucciones['tamanoPagina'] = [216, 280];
        break;
      case 'oficio':
        $instrucciones['tamanoPagina'] = [215, 356];
        break;
      case 'factura55':
        $instrucciones['tamanoPagina'] = [55, 600];
        break;
      case 'factura80':
        $instrucciones['tamanoPagina'] = [80, 600];
        break;
      default:
        $instrucciones['tamanoPagina'] = [216, 280];
        break;
    }
    parent::__construct(
      $instrucciones['orientacion'] ?? 'P',
      $instrucciones['unidadMedida'] ?? 'mm',
      $instrucciones['tamanoPagina'] ?? 'A4'
    );
  }
  public function calcularLineas(int $ancho, string $textoFila) {
    $cw = &$this->CurrentFont['cw'];
    if ($ancho == 0) {
      $ancho = $this->w - $this->rMargin - $this->x;
    }
    $anchoMaximo = ($ancho - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $textoFila);
    $nb = strlen($s);

    if ($nb > 0 && $s[$nb - 1] == "\n") {
      $nb--;
    }

    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ') {
        $sep = $i;
      }
      if (!isset($cw[$c])) { // Manejo de caracteres no definidos en la fuente
        $l += 0;
      } else {
        $l += $cw[$c];
      }
      if ($l > $anchoMaximo) {
        if ($sep == -1) {
          if ($i == $j) {
            $i++;
          }
        } else {
          $i = $sep + 1;
        }
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else {
        $i++;
      }
    }
    return $nl;
  }
  function Header() {
    if ($this->header == true) {
      // Logo
      $this->Image($_SERVER['DOCUMENT_ROOT'] . '/proyecto-lacruz-j/src/assets/images/logo2.png', 10, 10, 17, 15);
      //Encabezado
      $fechaActual = new DateTime();
      $fechaActual = $fechaActual->format('d-m-Y');

      $this->SetFont('Arial', 'B', 12);
      $this->cell2(30, 10);
      $this->cell2(135, 10, $this->tituloEncabezado, 0, 0, 'C');
      $this->cell2(30, 10, 'EMITIDO EL: ' . $fechaActual, 0, 0, 'R');
      $this->Ln(20);

      if (count($this->datosExtraCabecera) > 0) {
        foreach ($this->datosExtraCabecera as $dato) {
          $dato = mb_convert_encoding($dato, 'ISO-8859-1', 'UTF-8');
          $this->cell2(0, 10, $dato, 0, 1, 'C');
        }
      }
    }
    if ($this->dataNotaEntrega) {
      // Cuadro exterior superior (Opcional, para delimitar la zona superior si se desea)
      // $this->Rect(10, 10, 190, 30);
      
      /*
      $this->Image($_SERVER['DOCUMENT_ROOT'] . '/proyecto-lacruz-j/src/assets/images/logo2.png', 10, 10, 28, 20);

      // Datos de la Empresa (Izquierda)
      $this->SetXY(42, 10);
      $this->SetFont('Arial', 'B', 19);
      $this->SetTextColor(14, 35, 125);
      $this->cell2(50, 5, 'J. LACRUZ C.A.', 0, 1, 'C');
      $this->SetX(42);
      $this->SetFont('Arial', 'B', 12);
      $this->cell2(50, 5, 'Multiservicios Generales', 0, 1, 'C');
      $this->SetX(42);
      $this->SetFont('Arial', 'B', 7);
      $this->cell2(50, 5, 'TELFS: +58 424-5085666 / 0414-5718890', 0, 1, 'C');
      $this->SetX(42);
      $this->cell2(50, 5, 'jlacruzca@gmail.com', 0, 1, 'C');

      //Direccion y Rif
      $this->SetFont('Arial', 'B', 8);
      $this->SetXY(155, 10);
      $this->cell2(40, 4, 'Carrera 2 Entre Calle 6 y 7', 0, 1, 'C');
      $this->SetX(155);
      $this->cell2(40, 4, 'Local Casa N914', 0, 1, 'C');
      $this->SetX(155);
      $this->cell2(40, 4, 'Nor. Local 3 Sector La mata', 0, 1, 'C');
      $this->SetX(155);
      $this->cell2(40, 4, 'Cabudare, Estado Lara', 0, 1, 'C');
      $this->SetX(155);
      $this->cell2(40, 4, 'Zona Postal 3001', 0, 1, 'C');
      $this->SetX(155);
      $this->SetFont('Arial', 'B', 10);
      $this->SetTextColor(10, 24, 82);
      $this->cell2(40, 4, 'RIF.: J-412192701', 0, 1, 'C');
      $this->SetTextColor(0);
      */

      $this->SetXY(10, 40);
      $this->SetFont('Arial', 'B', 8);
      $this->cell2(50, 6, 'LUGAR Y FECHA DE EMISIÓN', 1, 0, 'C');
      $this->cell2(10, 6, 'DIA', 1, 0, 'C');
      $this->cell2(10, 6, 'MES', 1, 0, 'C');
      $this->cell2(10, 6, 'AÑO', 1, 1, 'C');

      $fechaCompleta = explode(' ', $this->fechaHoraSel('fecha_hora_AM_PM', $this->dataNotaEntrega['fecha_orden']));
      $fechaArray = explode('-', $fechaCompleta[0]);

      $this->SetFont('Arial', '', 8);
      $this->cell2(50, 6, 'BARQUISIMETO', 1, 0, 'C');
      $this->cell2(10, 6, $fechaArray[0], 1, 0, 'C');
      $this->cell2(10, 6, $fechaArray[1], 1, 0, 'C');
      $this->cell2(10, 6, $fechaArray[2], 1, 1, 'C');

      $this->SetXY(155, 40);
      $this->SetFont('Arial', 'B', 9);
      $this->cell2(40, 5, 'N° ORDEN', 0, 1, 'C');
      $this->SetX(155);
      $this->SetFont('Arial', '', 9);
      $this->cell2(40, 5, $this->dataNotaEntrega['id_orden_entrega_presupuesto'], 0, 1, 'C');
    }
  }
  function Footer() {
    if ($this->footer) {
      // Posición: a 1,5 cm del final
      $this->SetY(-10);
      // Arial italic 8
      $this->SetFont('Arial', 'I', 10);
      // Número de página
      $pagina = mb_convert_encoding("Página", 'ISO-8859-1', 'UTF-8');
      $this->cell2(0, 10, $pagina . ' ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
  }
  public function crearPDF(array $datosPdf) {
    $procesarSecciones = function ($datosPdf, $nroSeccion = null) {

      $configColumnas = $datosPdf['configColumnas'];
      $infoBD = $datosPdf['infoBD'];
      $this->tituloEncabezado = $datosPdf['tituloReporte'] ?? '';
      $this->datosExtraCabecera = $datosPdf['datosExtCabecera'] ?? [];

      // Primera sección o sección única: inicializar el documento
      if ($nroSeccion === null || $nroSeccion == 0) {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 10);
      } else {
        // Secciones adicionales: agregar nueva página con separador
        $this->AddPage();
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        if (count($this->datosExtraCabecera) > 0) {
          foreach ($this->datosExtraCabecera as $dato) {
            $this->cell2(0, 10, $dato, 0, 1, 'C');
          }
        }
      }

      //Dibujamos los encabezados
      $this->SetFont("Arial", "B", "12");
      foreach ($configColumnas as $configInd) {
        $this->cell2($configInd[1], 10, $configInd[0], 1, 0, 'C');
      }
      $this->Ln();

      //Dibujamos las filas de datos
      $this->SetFont("Arial", "", "10");
      $alturaFila = 0;

      // Guardar la posición actual X y Y
      $x = $this->GetX();
      $y = $this->GetY();

      $clavesArreglo = array_keys($configColumnas);
      foreach ($infoBD as $fila) {
        $xActual = $x;

        // Calculamos el ancho de la fila
        $anchoCeldaMasGrande = 0;
        foreach ($clavesArreglo as $clave) {
          $anchoCeldaMasGrande = max($anchoCeldaMasGrande, $this->calcularLineas($configColumnas[$clave][1], $fila[$clave]));
        }
        $alturaFila = 5 * $anchoCeldaMasGrande;

        if ($y + $alturaFila > $this->PageBreakTrigger) {
          $this->AddPage();
          //Redibujamos los encabezados
          $this->SetFont("Arial", "B", "12");
          foreach ($configColumnas as $configInd) {
            $this->cell2($configInd[1], 10, $configInd[0], 1, 0, 'C');
          }
          $this->Ln();
          $this->SetFont("Arial", "", "10");

          $y = $this->GetY();
          $x = $this->GetX();
          $xActual = $x;
        }

        foreach ($clavesArreglo as $claveInd) {
          // Borde
          $this->Rect($xActual, $y, $configColumnas[$claveInd][1], $alturaFila);
          $this->SetXY($xActual, $y);
          $this->MultiCell($configColumnas[$claveInd][1], 5, $this->CSE($fila[$claveInd]), 0, 'C');
          $xActual += $configColumnas[$claveInd][1];
        }
        // Mover a la siguiente fila actualizando la posición Y
        $y += $alturaFila;
      }
    };

    if (isset($datosPdf[0])) {
      foreach ($datosPdf as $numeroArray => &$datoPdf) {
        $procesarSecciones($datoPdf, $numeroArray);
      }
    } else {
      $procesarSecciones($datosPdf);
    }
    return $this;
  }
  public function crearComanda(array $secciones) {
    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(2, 0, 2);
    $this->SetAutoPageBreak(true, 10);
    $this->SetFont("Arial", "B", "10");
    $this->cell2(0, 1, '', 0, 1);
    $encabezado = mb_convert_encoding('THE VIÑA FAST FOOD', 'ISO-8859-1', 'UTF-8');
    $this->cell2(0, 3, $encabezado, 0, 1, 'C');

    foreach ($secciones as $seccion) {

      $this->SetFont("Arial", "B", "8");
      $this->cell2(0, 5, '', 0, 1, '');
      $this->cell2(0, 5, '---------------------------------------------------------', 0, 1, 'C');

      $encabezado = mb_convert_encoding($seccion['tituloSeccion'], 'ISO-8859-1', 'UTF-8');
      $this->cell2(0, 3, $encabezado, 0, 1, 'C');

      $datosExtraArriba = isset($seccion['datosExtra']['arriba']) ? $seccion['datosExtra']['arriba'] : false;
      $datosExtraAbajo = isset($seccion['datosExtra']['abajo']) ? $seccion['datosExtra']['abajo'] : false;

      // DATOS EXTRA DE ARRIBA
      if ($datosExtraArriba) {
        $this->SetFont("Arial", "B", "8");
        $this->Ln();
        $CDA = 0;
        foreach ($datosExtraArriba['datosCeldas'] as $datoEx) {
          $this->cell2(0, 3, $datoEx, 0, 1, $datosExtraArriba['alineaciones'][$CDA]);
          $CDA++;
        }
      }
      foreach ($seccion['datosMedio'] as &$datoCo) {
        $anchoColumnas = $datoCo['anchoColumnas'];
        $encabezados = $datoCo['encabezados'];
        $datosCeldas = $datoCo['datosCeldas'];
        $tituloEncabezado = $datoCo['encabezadoPrin'];
        $alineaciones = $datoCo['alineaciones'];

        if (count($datosCeldas) == 0) {
          continue;
        }
        $this->SetY($this->GetY());
        $this->SetFont("Arial", "B", "8");
        $this->Ln();
        $this->cell2(0, 5, $tituloEncabezado, 0, 1, 'C');

        //Dibujamos los encabezados
        $c = 0;
        $this->SetFont("Arial", "B", "8");
        foreach ($encabezados as $encabezado) {
          $encabezado_convertido = mb_convert_encoding($encabezado, 'ISO-8859-1', 'UTF-8');
          $this->cell2($anchoColumnas[$c], 3, $encabezado_convertido, 0, 0, 'C');
          $c++;
        }
        $this->Ln();

        //Dibujamos las filas de datos
        $this->SetFont("Arial", "B", "8");

        // Guardar la posición actual X y Y
        $x = $this->GetX();
        $y = $this->GetY();

        // Dibujamos las celdas
        $clavesArreglo = array_keys($datosCeldas[0]);

        $datosCalculo = [];
        foreach ($datosCeldas as $fila) {
          $xActual = $x;

          //Calculamos el ancho de las filas para saber cual sera la mas ancha
          $ancho = 0;
          $anchoCeldaMasGrande = 0;
          foreach ($clavesArreglo as $clave) {

            if (!isset($anchoColumnas[$ancho])) {
              return [
                'fila' => $fila,
                'anchos' => $anchoColumnas,
                'posicionAncho' => $ancho,
                'claves' => $clavesArreglo,
                'datosCeldas' => $clavesArreglo,
              ];
            }
            $anchoCeldaMasGrande = max(
              $anchoCeldaMasGrande,
              $this->calcularLineas(
                $anchoColumnas[$ancho],
                $fila[$clave]
              )
            );
            $ancho++;
          }
          $alturaFila = 3 * $anchoCeldaMasGrande;

          //para cambiar de pagina en caso de faltar espacio
          if ($y + $alturaFila > $this->PageBreakTrigger) {
            $this->AddPage();
            $y = $this->GetY();
            $x = $this->GetX();
            $xActual = $x;
          }

          $ancho2 = 0;

          foreach ($clavesArreglo as $claveInd) {
            // Posición del cursor en la celda
            $this->SetXY($xActual, $y);
            // Imprimir el texto
            $textoFila = mb_convert_encoding($fila[$claveInd], 'ISO-8859-1', 'UTF-8');

            if ($anchoColumnas[$ancho2] * 2 < mb_strlen($textoFila)) {
              $this->SetFont("Arial", "B", "8");
            }
            $datosCalculo[] = [
              "Ancho" => $anchoColumnas[$ancho2],
              "texto" => $textoFila,
              "Nro caracteres" => mb_strlen($textoFila),
            ];
            $this->MultiCell($anchoColumnas[$ancho2], 3, $textoFila, 0, $alineaciones[$ancho2]);

            // Mover a la siguiente columna
            $xActual += $anchoColumnas[$ancho2];
            $ancho2++;
            $this->SetFont("Arial", "B", "8");
          }

          // Mover a la siguiente fila actualizando la posición Y
          $y += $alturaFila;
        }
        $this->SetY($this->GetY() + ($alturaFila ?? 0) - 6);
      }

      $this->cell2(0, 5, '', 0, 1);
      //DATOS EXTRA DE ABAJO
      if ($datosExtraAbajo) {
        $this->SetFont("Arial", "B", "8");
        $CDEA = 0;
        foreach ($datosExtraAbajo['datosCeldas'] as $datoExt) {
          $this->cell2(0, 3, $datoExt, 0, 1, $datosExtraAbajo['alineaciones'][$CDEA]);
          $CDEA++;
        }
      }
      if (isset($seccion['QR'])) {
        $imagenTemporalQR = $this->crearCodigoQR($seccion['QR']['dataQR'], $seccion['QR']['tamaño']);
        $tituloQR = mb_convert_encoding($seccion['QR']['tituloQR'], 'ISO-8859-1', 'UTF-8');
        $this->cell2(0, 5, '', 0, 1);
        $this->cell2(0, 3, $tituloQR, 0, 1, 'C');
        $this->Image(
          $imagenTemporalQR,
          $this->GetX() + 16,
          $this->GetY(),
          20,
          20,
          'PNG',
          $seccion['QR']['dataQR']
        );
        $this->SetY($this->GetY() + 15);

        if (file_exists($imagenTemporalQR)) {
          unlink($imagenTemporalQR);
        }
      }
    }
    return $this;
  }
  public function notaEntrega() {
    $this->SetMargins(10, 10, 10);
    $this->AddPage();

    $this->Rect(10, 57, 195, 178);
    $this->SetFont('Arial', '', 8);

    $itemPedido = $this->dataNotaEntrega ?: [];
    $cliente = $itemPedido['cliente'] ?? [];

    $this->SetXY(10, 57);
    $this->SetFont('Arial', 'B', 8);
    $this->cell2(165, 6, 'NOMBRE APELLIDO O RAZÓN SOCIAL: ' . $cliente['razon_social_cliente'], 1, 0,);
    $this->cell2(30, 6, 'R.I.F: ' . ($cliente['rif_cedula_cliente'] ?? ''), 1, 1, 'C');

    $this->cell2(195, 6, 'DOMICILIO FISCAL: ' . ($cliente['direccion_cliente'] ?? ''), 1, 1);

    $this->cell2(30, 4, 'TELÉFONO', 1, 0, 'C');
    $this->cell2(80, 4, 'ORDEN DE ENTREGA/GUÍA DE DESPACHO', 1, 0, 'C');
    $this->cell2(50, 4, 'CONDICIONES DE PAGO', 1, 0, 'C');
    $this->cell2(35, 4, 'VENCIMIENTO', 1, 1, 'C');

    $this->cell2(30, 4, $cliente['telefono_cliente'], 1, 0, 'C');
    $this->cell2(80, 4, 'N° ' . $this->dataNotaEntrega['id_orden_entrega_presupuesto'], 1, 0, 'C');
    $this->cell2(20, 4, 'CONTADO', 1, 0, 'C');
    $this->cell2(5, 4, 'X', 1, 0, 'C');
    $this->cell2(20, 4, 'CRÉDITO', 1, 0, 'C');
    $this->cell2(5, 4, '', 1, 0, 'C');
    $this->cell2(35, 4, '', 1, 1, 'C');

    $this->Ln();
    $fnBolivares = function ($valor) {
      $d = (float)$this->dataNotaEntrega['calculos']['dolar']['valor_fecha_moneda'];
      $bs = round((((float) $valor) * $d), 2);
      return $this->formatearStrings($bs, 'dinero');
    };

    //Encabezados
    $anchosE = array(30, 20, 75, 35, 35);
    $encabezados = array('COD.', 'CANT.', 'CONCEPTO O DESCRIPCIÓN DE LA VENTA',  'PRECIO UNIT.', 'MONTO');
    $this->SetFont('Arial', 'B', 8);
    for ($i = 0; $i < count($encabezados); $i++) {
      $this->cell2($anchosE[$i], 6,  $encabezados[$i], 1, 0, 'C');
    }
    $this->Ln();

    $this->SetFont('Arial', '', 7);
    foreach (($itemPedido['productos'] ?? []) as $producto) {
      $this->cell2(30, 5, $producto['id_presentacion_producto'], 0, 0, 'C');
      $this->cell2(20, 5, $this->formatearStrings($producto['cantidad_producto'], 'dinero'), 0, 0, 'C');
      $this->cell2(75, 5, $producto['nombre_producto'] . ' - ' . $producto['nombre_presentacion'], 0, 0, 'C');
      $this->cell2(35, 5, $fnBolivares($producto['precio_presentacion_factura']) . ' Bs', 0, 0, 'C');
      $this->cell2(35, 5, $fnBolivares($producto['subtotal_factura']) . ' Bs', 0, 1, 'R');
    }

    //Delivery
    $this->cell2(30, 5, '--------', 0, 0, 'C');
    $this->cell2(20, 5, '1,00', 0, 0, 'C');
    $this->cell2(75, 5, 'ENVÍO', 0, 0, 'C');
    $this->cell2(35, 5, $fnBolivares($this->dataNotaEntrega['calculos']['totalEnvio']) . ' Bs', 0, 0, 'C');
    $this->cell2(35, 5, $fnBolivares($this->dataNotaEntrega['calculos']['totalEnvio']) . ' Bs', 0, 1, 'R');

    $fechaCompleta = explode(' ', $this->fechaHoraSel('fecha_hora_AM_PM', $this->dataNotaEntrega['fecha_orden']));
    $fechaArray = explode('-', $fechaCompleta[0]);
    $hora = $fechaCompleta[1] . ' ' . $fechaCompleta[2];
    $fecha = implode('/', $fechaArray) . ' HORA: ' . $hora;

    $this->SetXY(10, 220);
    $this->SetFont('Arial', 'B', 7);
    $this->cell2(80, 5, '   ORDEN PEDIDO NÚMERO ' . $this->dataNotaEntrega['id_orden_entrega_presupuesto'], 0, 1);
    $this->cell2(80, 4, '   MONTO EQUIVALENTE EN DÓLARES ' . $this->dataNotaEntrega['calculos']['total_IVA'] . '$', 0, 1);
    $this->cell2(80, 4, '   TASA REFERENCIAL BCV AL ' . $fecha . '     ' . $this->dataNotaEntrega['calculos']['dolar']['valor_fecha_moneda'] . ' Bs', 0, 1);

    $this->SetXY(110, 220);
    $this->SetFont('Arial', 'B', 7.5);
    $this->cell2(50, 5, 'MONTO TOTAL BASE DISPONIBLE', 1, 0);
    $this->SetFont('Arial', '', 7.5);
    $this->cell2(45, 5, $fnBolivares($this->dataNotaEntrega['calculos']['total']) . ' Bs', 1, 1, 'R');

    $this->SetX(110);//
    $this->SetFont('Arial', 'B', 7.5);
    $this->cell2(50, 5, 'MONTO TOTAL IVA A PAGAR ' . $this->dataNotaEntrega['calculos']['porcentaje_IVA'] . '%', 1, 0);
    $this->SetFont('Arial', '', 7.5);
    $this->cell2(45, 5, $fnBolivares($this->dataNotaEntrega['calculos']['monto_IVA']) . ' Bs', 1, 1, 'R');
        //
    $this->SetX(110);//
    $this->SetFont('Arial', 'B', 7.5);
    $this->cell2(50, 5, 'MONTO TOTAL A PAGAR:', 1, 0);
    $this->SetFont('Arial', '', 7.5);
    $this->cell2(45, 5, $fnBolivares($this->dataNotaEntrega['calculos']['total_IVA']) . ' Bs', 1, 1, 'R');

    return $this;
  }
  public function CSE(string $string) {
    return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
  }
  public function cell2(int $ancho, int $alto = 0, string $texto = '', string|int $borde = 0, int $saltoLinea = 0, string $alineacion = '', $colorFondo = false, $hipervinculo = '') {
    $texto = mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    $this->Cell($ancho, $alto, $texto, $borde, $saltoLinea, $alineacion, $colorFondo, $hipervinculo);
  }
}
