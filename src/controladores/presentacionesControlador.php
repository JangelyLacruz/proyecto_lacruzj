<?php

use src\modelos\presentacionesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = isset($_POST['id_presentacion']) ? $_POST['id_presentacion'] : "";
    $idUnidadMedida = isset($_POST['id_unidad_medida']) ? $_POST['id_unidad_medida'] : "";
    $nombre = isset($_POST['nombre_presentacion']) ? $_POST['nombre_presentacion'] : "";
    $cantidadPmp = isset($_POST['cantidad_pmp']) ? $_POST['cantidad_pmp'] : "";
    
    $objeto = new presentacionesModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarPresentaciones();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarPresentaciones($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarPresentaciones($idUnidadMedida, $nombre, $cantidadPmp);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarPresentaciones($id,$idUnidadMedida, $nombre, $cantidadPmp);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarPresentaciones($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/presentaciones/presentaciones.php";
}
