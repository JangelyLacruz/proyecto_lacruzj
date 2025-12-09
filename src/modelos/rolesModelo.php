<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class rolesModelo extends conexion
{
    private $idRol;
    private $nombreRol;

    public function seleccionarRoles($id = null)
    {
        $this->idRol = $id;

        if ($this->idRol != null && $this->idRol != "") {
            //Arrays para las validaciones
            $campos = [
                [
                    "campo_nombre" => 'id_rol',
                    "campo_valor" => $this->idRol,
                    "formulario_nombre" => "id del rol",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => 'roles',
                    "debeExistir" => true
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->seleccionarRolesP();
            }
        } else {
            return $this->seleccionarRolesP();
        }
    }
    public function registrarRoles($nombre)
    {
        try {
            $this->nombreRol = $nombre;
            $campos = [
                [
                    "campo_nombre" => "nombre_rol",
                    "campo_valor" => $this->nombreRol,
                    "formulario_nombre" => "nombre del rol",
                    "requerido" => true,
                    "minimo" => minRegexNombrePer,
                    "maximo" => maxRegexNombrePer,
                    "expresion_re" => regexNombrePer,
                    "tabla" => "roles",
                    "debeSerUnico" => true,
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->registrarRolesP();
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception("Error al registrar el rol en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarRoles($id, $nombre)
    {
        $this->idRol = $id;
        $this->nombreRol = $nombre;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "id_rol",
                "campo_valor" => $this->idRol,
                "formulario_nombre" => "id del rol",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "roles",
                "debeExistir" => true,
                "debeSerUnico" => true
            ],
            [
                "campo_nombre" => "nombre_rol",
                "campo_valor" => $this->nombreRol,
                "formulario_nombre" => "nombre del rol",
                "requerido" => true,
                "minimo" => minRegexNombrePer,
                "maximo" => maxRegexNombrePer,
                "expresion_re" => regexNombrePer,
                "tabla" => "roles",
                "debeSerUnico" => true
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarRolesP();
        }
    }
    public function eliminarRoles($id)
    {
        /*Limpiar Inyección de SQL */
        $this->idRol = $id;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "id_rol",
                "campo_valor" => $this->idRol,
                "formulario_nombre" => "id del rol",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "debeExistir" => true,
                "camposDiferentes" => 1,
                "tabla" => "roles"
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarRolesP();
        }
    }

    //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
    private function seleccionarRolesP()
    {
        if ($this->idRol == null || $this->idRol == "") {
            $instruccionesBD = [
                'campos' => 'id_rol, nombre_rol',
                'tabla' => 'roles',
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $roles = $resultado->fetchAll(PDO::FETCH_ASSOC);
            return $roles;
        } else {

            /*Hacemos la consulta */;
            
            $instruccionesBD = [
                'campos' => 'id_rol, nombre_rol',
                'tabla' => 'roles',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_rol",
                        "condicion_marcador" => ":ID",
                        "condicion_valor" => $this->idRol,
                        "comparacion" => "="
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Rol no encontrado",
                    "texto" => "El rol que ha intentado buscar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $rol = $resultado->fetch(PDO::FETCH_ASSOC);
            }
            return $rol;
        }
    }
    private function registrarRolesP()
    {
        $datos_registro_roles = [
            [
                "campo_nombre" => "nombre_rol",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $this->nombreRol,
                "ponerEnMayusculas" => true
            ],
        ];

        $ultimoId = $this->guardarDatos('roles', $datos_registro_roles);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Rol registrado",
                "texto" => "El rol ha sido registrado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Rol no registrado",
                "texto" => "El rol no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarRolesP()
    {

        $instruccionesBD = [
            "tabla" => "roles",
            "datos" => [
                [
                    "campo_nombre" => "id_rol",
                    "campo_marcador" => ":id",
                    "campo_valor" => $this->idRol
                ],
                [
                    "campo_nombre" => "nombre_rol",
                    "campo_marcador" => ":Nombre",
                    "campo_valor" => $this->nombreRol,
                    "ponerEnMayusculas" => true
                ]
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_rol",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idRol,
                    "comparacion" => "="
                ]
            ]
        ];
        $resultado = $this->actualizarDatos($instruccionesBD);

        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en el rol",
                "icono" => "warning",
            ];
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Rol actualizado",
                "texto" => "El rol ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarRolesP()
    {
        $eliminarUsuario = $this->eliminarDatos("roles", "id_rol", $this->idRol);
        if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Rol eliminado",
                "texto" => "El rol ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Rol no encontrado",
                "texto" => "El rol no existe en la Base de Datos",
                "icono" => "error"
            ];
        }
        return $alerta;
    }
}
