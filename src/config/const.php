<?php
    /*Constantes de la BD*/
    const DB_SERVER= "localhost";
    const DB_NAME= "proyecto_lacruz";
    const DB_USER= "root";
    const DB_PASS= "";

    /*Constante de la APP */
    $PROTOCOLO='http://';
    if(
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ){
        $PROTOCOLO='https://';
    }
    define('APP_URL', $PROTOCOLO . $_SERVER['HTTP_HOST']."/proyecto-lacruz-j/");
    
    const APP_NAME = "MULTISERVICIOS JLACRUZ C.A.";
    const APP_SESSION_NAME = "JLACRUZ";
    date_default_timezone_set("America/Caracas");

    /*Expresiones regulares*/
    const regexId='^\d{1,9}$';
    const minRegexId='1';
    const maxRegexId='9';

    const regexCantidadItem='^\d{1,9}$';
    const minRegexCantidadItem='1';
    const maxRegexCantidadItem='9';

    const regexCedula='^\d{7,9}$';
    const minRegexCedula='7';
    const maxRegexCedula='9';

    const regexNombreObj='^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9$(),.\/\-!¡?¿% ]{3,50}$';
    const minRegexNombreObj='3';
    const maxRegexNombreObj='50';

    const regexNombrePer='^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ]{3,30}$';
    const minRegexNombrePer='3';
    const maxRegexNombrePer='50';

    const regexDescripcion='^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ0-9\$\(\)\,\.\/\-\!\¡\?\¿\:\-\= ]{3,255}$';
    const minRegexDescripcion='3';
    const maxRegexDescripcion='255';

    const regexTelefono='^\d{11}$';
    const minRegexTelefono='11';
    const maxRegexTelefono='11';

    const regexPrecio='^\d{1,10}(?:[.,]\d{1,10})?$';
    const minRegexPrecio='1';
    const maxRegexPrecio='10';

    const regexValorBoleano='^\d{1}$';
    const minRegexValorBoleano='1';
    const maxRegexValorBoleano='1';
    
    const regexSimboloMoneda='^[a-zA-Z0-9À-ÿ€¥$]{1,3}$';
    const minRegexSimboloMoneda='1';
    const maxRegexSimboloMoneda='3';

    const regexCorreo='^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$';
    const minRegexCorreo='10';
    const maxRegexCorreo='255';

    const regexUsuario='^(?=.*[0-9].*[0-9])(?=.{8,}).*$';
    const minRegexUsuario='8';
    const maxRegexUsuario='20';

    const regexContrasena='^(?=.*[0-9].*[0-9])(?=.{8,}).*$';
    const minRegexContrasena='8';
    const maxRegexContrasena='20';

    const regexToken='^[a-zA-ZáéíóúüÁÉÍÓÚñÑ0-9\-\_\.\:\;\!\/\%\$\=\&\s]{1,255}$';
    const minRegexToken='1';
    const maxRegexToken='255';

    const regexTipoToken='^[a-zA-ZáéíóúüÁÉÍÓÚñÑ0-9\-\_\.\:\;\!\/\%\$\=\&\s]{1,255}$';
    const minRegexTipoToken='1';
    const maxRegexTipoToken='255';

    const regexReferencia="^\d{4,10}$";
    const minRegexReferencia='4';
    const maxRegexReferencia='10';

?>