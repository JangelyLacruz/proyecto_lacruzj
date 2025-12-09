<?php

use src\modelos\permisosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    ob_clean();

    // $modeloPermisos = new permisosModel();
    // $resultado = $modeloPermisos->Permisos_Val('permisos', $accion);
    // if (isset($resultado['tipo'])) {
    //     echo json_encode($resultado);
    //     exit();
    // }

    $accion = isset($_POST["accion"]) ? $_POST["accion"] : '';
    $idRol = isset($_POST['id_rol']) ? $_POST['id_rol'] : '';
    $idModulo = isset($_POST['id_modulo']) ? $_POST['id_modulo'] : "";
    $idPermiso = isset($_POST['id_permiso']) ? $_POST['id_permiso'] : "";
    $cambio = isset($_POST['cambio']) ? $_POST['cambio'] : "";

    $modeloPermisos = new permisosModelo();

    switch ($accion) {
        case "listar":
            $resultado = $modeloPermisos->listarPermisos($idRol);
            echo json_encode($resultado);
            exit();
        case "listarPorRol":
            $resultado = $modeloPermisos->SeleccionarPermisosPorRol();
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $modeloPermisos->ActualizarPermisos($idRol, $idModulo, $idPermiso, $cambio);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} else {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/permisos/permisos.php";
}
