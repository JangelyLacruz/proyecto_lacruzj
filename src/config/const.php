<?php

// #region [ CONSTANTES DE LA BD ]

const DB_SERVER = "localhost";
const DB_NAME = "proyecto_lacruz";
const DB_USER = "root";
const DB_PASS = "";
const DIR_FOTOS = "src/assets/fotosModulos/";

// #endregion [ CONSTANTES DE LA BD ]

// #region [ CONSTANTES DE LA APP ]

$PROTOCOLO = 'http://';
if (
  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ||
  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
  $PROTOCOLO = 'https://';
}
define('APP_URL', $PROTOCOLO . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/proyecto-lacruz-j/");
const modoDev = true;
const coorJLACRUZ = ['latitud' => 10.063276, 'longitud' => -69.31708];
const APP_NAME = "MULTISERVICIOS JLACRUZ C.A.";
const APP_SESSION_NAME = "JLACRUZ";
date_default_timezone_set("America/Caracas");

// #endregion [ CONSTANTES DE LA APP ]

// #region [ EXPRESIONES REGULARES - ATOMOS ]
const regexId = '^\d{1,9}$';
const minRegexId = '1';
const maxRegexId = '9';

const regexIdSeguro = '^[a-zA-Z]{2,4}-\d{5}-\d{5}-\d{2}$';
const minRegexIdSeguro = '17';
const maxRegexIdSeguro = '20';

const regexCantidadItem = '^\d{1,10}(?:[.,]\d{1,3})?$';
const minRegexCantidadItem = '1';
const maxRegexCantidadItem = '11';

const regexStatus = '^\d{1}$';
const minRegexStatus = '1';
const maxRegexStatus = '1';

const regexCedulaRif = '^\d{7,10}$';
const minRegexCedulaRif = '7';
const maxRegexCedulaRif = '10';

const regexCedulaRifLetra = '^[a-zA-Z]?\d{7,11}$';
const minRegexCedulaRifLetra = '7';
const maxRegexCedulaRifLetra = '11';

const regexNombreObj = '^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9\\$\\(\\)\\,\\.\\/\\!¡\\?%:=#<>\\s\\n\\r\\-\\_]{3,50}$';
const minRegexNombreObj = '3';
const maxRegexNombreObj = '50';

const regexNombrePer = '^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]{3,30}$';
const minRegexNombrePer = '3';
const maxRegexNombrePer = '50';

const regexDescripcion = '^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9\\$\\(\\)\\,\\.\\/\\!¡\\?¿%:=#<>\\s\\n\\r\\-\\_]{3,255}$';
const minRegexDescripcion = '3';
const maxRegexDescripcion = '255';

const regexCuerpoCorreo = '^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9\\$\\(\\)\\,\\.\\/\\!¡\\?%:=#<>\\s\\n\\r\\-\\_]{3,3000}$';
const minRegexCuerpoCorreo = '3';
const maxRegexCuerpoCorreo = '3000';

const regexTelefono = '^\d{11}$';
const minRegexTelefono = '11';
const maxRegexTelefono = '11';

const regexPrefijoTelefono = '^\d{4}$';
const minRegexPrefijoTelefono = '4';
const maxRegexPrefijoTelefono = '4';

const regexCuerpoTelefono = '^\d{7}$';
const minRegexCuerpoTelefono = '7';
const maxRegexCuerpoTelefono = '7';

const regexPrecio = '^\d{1,20}(?:[.,]\d{1,3})?$';
const minRegexPrecio = '1';
const maxRegexPrecio = '20';

const regexPrecioFront = '^\d{1,3}(\.\d{3})*(,\d{2})?$';
const minRegexPrecioFront = '1';
const maxRegexPrecioFront = '20';

const regexValorBoleano = '^\d{1}$';
const minRegexValorBoleano = '1';
const maxRegexValorBoleano = '1';

const regexSimboloMoneda = '^[a-zA-Z0-9À-ÿ€¥$]{1,3}$';
const minRegexSimboloMoneda = '1';
const maxRegexSimboloMoneda = '3';

const regexCorreo = '^[a-zA-Z0-9._%\+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,10}$';
const minRegexCorreo = '10';
const maxRegexCorreo = '255';

const regexUsuario = '^(?=.*[0-9].*[0-9])(?=.{8,}).*$';
const minRegexUsuario = '8';
const maxRegexUsuario = '20';

const regexContrasena = '^(?=.*[0-9].*[0-9])(?=.{8,}).*$';
const minRegexContrasena = '8';
const maxRegexContrasena = '20';

const regexEnteroGrande = '^\d{1,15}$';
const minRegexEnteroGrande = '1';
const maxRegexEnteroGrande = '15';

const regexCoordenadas = '^[+-]?\d+\.\d{1,20}$';
const minRegexCoordenadas = 0;
const maxRegexCoordenadas = 20;

const regexUrl = '^https?:\/\/[\w\-\.]+(?::\d+)?(?:\/[\w\-\.\/\?\%\&\=\#\:\,+\'\@\!]*)*$';
const minRegexUrl = '10';
const maxRegexUrl = '255';

const regexReferencia = '^\d{4,6}$';
const minRegexReferencia = '4';
const maxRegexReferencia = '6';

const regexNumControlFactura = '^[A-Z0-9\-]{0,10}$';
const minRegexNumControlFactura = '0';
const maxRegexNumControlFactura = '10';
// #endregion [ EXPRESIONES REGULARES - ATOMOS ]

// #region [ MOLÉCULAS - VALIDACIONES ]
define('molId', [
  'tipo' => 'int',
  "minL" => minRegexId,
  "maxL" => maxRegexId,
  "regex" => regexId
]);
define('molCantidadItem', [
  'tipo' => 'string',
  "minL" => minRegexCantidadItem,
  "maxL" => maxRegexCantidadItem,
  "regex" => regexCantidadItem,
  "cFloat" => true
]);
define('molIdSeguro', [
  'tipo' => 'string',
  "minL" => minRegexIdSeguro,
  "maxL" => maxRegexIdSeguro,
  "regex" => regexIdSeguro
]);
define('molCedulaRifLetra', [
  'tipo' => 'string',
  "minL" => minRegexCedulaRifLetra,
  "maxL" => maxRegexCedulaRifLetra,
  "regex" => regexCedulaRifLetra
]);
define('molNombrePer', [
  'tipo' => 'string',
  "minL" => minRegexNombrePer,
  "maxL" => maxRegexNombrePer,
  "regex" => regexNombrePer
]);
define('molNombreObj', [
  'tipo' => 'string',
  "minL" => minRegexNombreObj,
  "maxL" => maxRegexNombreObj,
  "regex" => regexNombreObj
]);
define('molContrasena', [
  'tipo' => 'string',
  "minL" => minRegexContrasena,
  "maxL" => maxRegexContrasena,
  "regex" => regexContrasena
]);
define('molCorreo', [
  'tipo' => 'string',
  "minL" => minRegexCorreo,
  "maxL" => maxRegexCorreo,
  "regex" => regexCorreo
]);
define('molDescripcion', [
  'tipo' => 'string',
  "minL" => minRegexDescripcion,
  "maxL" => maxRegexDescripcion,
  "regex" => regexDescripcion
]);
define('molTelefono', [
  'tipo' => 'string',
  "minL" => minRegexTelefono,
  "maxL" => maxRegexTelefono,
  "regex" => regexTelefono
]);
define('molUsuario', [
  'tipo' => 'string',
  "minL" => minRegexUsuario,
  "maxL" => maxRegexUsuario,
  "regex" => regexUsuario
]);
define('molPrecio', [
  'tipo' => ['string', 'int', 'float'],
  "minL" => minRegexPrecio,
  "maxL" => maxRegexPrecio,
  "regex" => regexPrecio,
]);
define('molPrecioFormateado', [
  'tipo' => 'string',
  "minL" => minRegexPrecio,
  "maxL" => maxRegexPrecio,
  "regex" => regexPrecio,
  "cFloat" => true
]);
define('molBooleano', [
  'tipo' => 'boolean',
]);
define('molBooleanoInt', [
  'tipo' => 'int',
  "minL" => minRegexValorBoleano,
  "maxL" => maxRegexValorBoleano,
  "regex" => regexValorBoleano,
]);

define('molCuerpoCorreo', [
  'tipo' => 'string',
  "minL" => minRegexCuerpoCorreo,
  "maxL" => maxRegexCuerpoCorreo,
  "regex" => regexCuerpoCorreo,
]);
define('molFoto', [
  'tipo' => 'archivo',
  'extensiones' => ['jpg', 'png', 'jpeg', 'webp'],
  'maximoMb' => 5,
  'minItems' => 1
]);
define('molFotoInd', [
  'tipo' => 'archivo',
  'extensiones' => ['jpg', 'png', 'jpeg', 'webp'],
  'maximoMb' => 5,
  'minItems' => 1,
  'maxItems' => 1
]);
define('molNumControlFactura', [
  'tipo' => 'string',
  "minL" => minRegexNumControlFactura,
  "maxL" => maxRegexNumControlFactura,
  "regex" => regexNumControlFactura
]);
// #endregion [ MOLÉCULAS - VALIDACIONES ]
