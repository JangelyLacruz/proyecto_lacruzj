<?php

use src\modelos\unidadesMedidasModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = isset($_POST['id_unidad_medida']) ? $_POST['id_unidad_medida'] : "";
    $nombre = isset($_POST['nombre_unidad_medida']) ? $_POST['nombre_unidad_medida'] : "";
    
    $objeto = new unidadesMedidasModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarUnidadesMedidas();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarUnidadesMedidas($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarUnidadesMedidas($nombre);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarUnidadesMedidas($id, $nombre);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarUnidadesMedidas($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/unidadesMedidas/unidadesMedidas.php";
}
