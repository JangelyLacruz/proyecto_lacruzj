<?php

use src\modelos\rolesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = isset($_POST['id_rol']) ? $_POST['id_rol'] : "";
    $nombre = isset($_POST['nombre_rol']) ? $_POST['nombre_rol'] : "";
    
    $objeto = new rolesModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarRoles();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarRoles($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarRoles($nombre);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarRoles($id, $nombre);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarRoles($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/roles/roles.php";
}
