<?php
    require_once 'vendor/autoload.php';
    require_once 'src/config/const.php';
    session_name(APP_SESSION_NAME); session_start();
    require_once 'src/config/inc/head.php';
    use src\controladores\frontController;
    $controlador = new frontController;
    require_once 'src/config/inc/script.php';
?>