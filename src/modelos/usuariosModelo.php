<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class usuariosModelo extends conexion
{
    private $cedulaUsuario;
    private $rolUsuario;
    private $nombreUsuario;
    private $apellidoUsuario;
    private $usuarioUsuario;
    private $contrasena1Usuario;
    private $contrasena2Usuario;
    private $telefonoUsuario;
    private $correoUsuario;

    public function seleccionarUsuarios($cedula = null)
    {
        $this->cedulaUsuario = $cedula;

        if ($this->cedulaUsuario != null && $this->cedulaUsuario != "") {
            //Arrays para las validaciones
            $campos = [
                [
                    "campo_valor" => $this->cedulaUsuario,
                    "formulario_nombre" => "cédula",
                    "requerido" => true,
                    "minimo" => minRegexCedula,
                    "maximo" => maxRegexCedula,
                    "expresion_re" => regexCedula,
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->seleccionarUsuariosP();
            }
        } else {
            return $this->seleccionarUsuariosP();
        }
    }
    public function registrarUsuarios($cedula, $id_rol, $nombre, $apellido, $telefono, $correo, $usuario, $contrasena1, $contrasena2)
    {
        try {
            $this->cedulaUsuario = $cedula;
            $this->rolUsuario = $id_rol;
            $this->nombreUsuario = $nombre;
            $this->apellidoUsuario = $apellido;
            $this->usuarioUsuario = $usuario;
            $this->contrasena1Usuario = $contrasena1;
            $this->contrasena2Usuario = $contrasena2;
            $this->telefonoUsuario = $telefono;
            $this->correoUsuario = $correo;

            //Arrays para las validaciones
            $campos = [
                [
                    "campo_nombre" => "cedula_usuario",
                    "campo_valor" => $this->cedulaUsuario,
                    "formulario_nombre" => "cédula",
                    "requerido" => true,
                    "minimo" => minRegexCedula,
                    "maximo" => maxRegexCedula,
                    "expresion_re" => regexCedula,
                    "tabla" => "usuarios",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_nombre" => "id_rol",
                    "campo_valor" => $this->rolUsuario,
                    "formulario_nombre" => "rol del usuario",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "roles",
                    "debeExistir" => true,
                ],
                [
                    "campo_valor" => $this->nombreUsuario,
                    "formulario_nombre" => "nombre",
                    "requerido" => true,
                    "minimo" => minRegexNombrePer,
                    "maximo" => maxRegexNombrePer,
                    "expresion_re" => regexNombrePer,
                ],
                [
                    "campo_valor" => $this->apellidoUsuario,
                    "formulario_nombre" => "apellido",
                    "requerido" => true,
                    "minimo" => minRegexNombrePer,
                    "maximo" => maxRegexNombrePer,
                    "expresion_re" => regexNombrePer,
                ],
                [
                    "campo_nombre" => "correo_usuario",
                    "campo_valor" => $this->correoUsuario,
                    "formulario_nombre" => "correo",
                    "requerido" => true,
                    "minimo" => minRegexCorreo,
                    "maximo" => maxRegexCorreo,
                    "expresion_re" => regexCorreo,
                    "tabla" => "usuarios",
                    "debeSerUnico" => true
                ],
                [
                    "campo_valor" => $this->telefonoUsuario,
                    "formulario_nombre" => "teléfono",
                    "requerido" => true,
                    "minimo" => minRegexTelefono,
                    "maximo" => maxRegexTelefono,
                    "expresion_re" => regexTelefono,
                ],
                [
                    "campo_nombre" => "usuario_usuario",
                    "campo_valor" => $this->usuarioUsuario,
                    "formulario_nombre" => "nombre de usuario",
                    "requerido" => true,
                    "minimo" => minRegexUsuario,
                    "maximo" => maxRegexUsuario,
                    "expresion_re" => regexUsuario,
                    "tabla" => "usuarios",
                    "debeSerUnico" => true
                ],
                [
                    "campo_valor" => $this->contrasena1Usuario,
                    "formulario_nombre" => "contraseña",
                    "requerido" => true,
                    "minimo" => minRegexContrasena,
                    "maximo" => maxRegexContrasena,
                    "expresion_re" => regexContrasena,
                    "camposIguales" => $this->contrasena2Usuario,
                ],
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                //Usamos este metodo para procesar e incriptar la contraseña
                $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
                return $this->registrarUsuariosP();
            }
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar(): " . $e->getMessage());
            throw new Exception("Error al registrar el usuario en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarUsuarios($cedula, $nombre, $apellido, $correo, $telefono, $rol, $usuario, $contrasena1, $contrasena2)
    {
        $this->cedulaUsuario = $cedula;
        $this->nombreUsuario = $nombre;
        $this->apellidoUsuario = $apellido;
        $this->correoUsuario = $correo;
        $this->telefonoUsuario = $telefono;
        $this->rolUsuario = $rol;
        $this->usuarioUsuario = $usuario;
        $this->contrasena1Usuario = $contrasena1;
        $this->contrasena2Usuario = $contrasena2;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "cedula_usuario",
                "campo_valor" => $this->cedulaUsuario,
                "formulario_nombre" => "cédula",
                "requerido" => true,
                "minimo" => minRegexCedula,
                "maximo" => maxRegexCedula,
                "expresion_re" => regexCedula,
                "tabla" => "usuarios",
                "debeExistir" => true,
                "debeSerUnico" => true
            ],
            [
                "campo_valor" => $this->nombreUsuario,
                "formulario_nombre" => "nombre",
                "requerido" => true,
                "minimo" => minRegexNombrePer,
                "maximo" => maxRegexNombrePer,
                "expresion_re" => regexNombrePer
            ],
            [
                "campo_valor" => $this->apellidoUsuario,
                "formulario_nombre" => "apellido",
                "requerido" => true,
                "minimo" => minRegexNombrePer,
                "maximo" => maxRegexNombrePer,
                "expresion_re" => regexNombrePer
            ],
            [
                "campo_nombre" => "correo_usuario",
                "campo_valor" => $this->correoUsuario,
                "formulario_nombre" => "correo",
                "requerido" => true,
                "minimo" => minRegexCorreo,
                "maximo" => maxRegexCorreo,
                "expresion_re" => regexCorreo,
                "tabla" => "usuarios",
                "debeSerUnico" => true
            ],
            [
                "campo_valor" => $this->telefonoUsuario,
                "formulario_nombre" => "teléfono",
                "requerido" => true,
                "minimo" => minRegexTelefono,
                "maximo" => maxRegexTelefono,
                "expresion_re" => regexTelefono
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_valor" => $this->usuarioUsuario,
                "formulario_nombre" => "nombre de usuario",
                "requerido" => true,
                "minimo" => minRegexUsuario,
                "maximo" => maxRegexUsuario,
                "expresion_re" => regexUsuario,
                "tabla" => "usuarios",
                "debeSerUnico" => true
            ],
            [
                "campo_valor" => $this->contrasena1Usuario,
                "formulario_nombre" => "contraseña",
                "minimo" => minRegexContrasena,
                "maximo" => maxRegexContrasena,
                "expresion_re" => regexContrasena,
                "camposIguales" => $this->contrasena2Usuario
            ],
            [
                "campo_nombre" => "id_rol",
                "campo_valor" => $this->rolUsuario,
                "formulario_nombre" => "rol",
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "roles",
                "debeExistir" => true
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            //Usamos este metodo para procesar e incriptar la contraseña
            if ($this->contrasena1Usuario != '') {
                $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
            }
            return $this->actualizarUsuariosP();
        }
    }
    public function eliminarUsuarios($cedula)
    {
        /*Limpiar Inyección de SQL */
        $this->cedulaUsuario = $cedula;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "cedula_usuario",
                "campo_valor" => $this->cedulaUsuario,
                "formulario_nombre" => "cédula",
                "requerido" => true,
                "minimo" => minRegexCedula,
                "maximo" => maxRegexCedula,
                "expresion_re" => regexCedula,
                "debeExistir" => true,
                "camposDiferentes" => 30485684,
                "tabla" => "usuarios"
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarUsuariosP();
        }
    }
    public function iniciarSesionUsuarios($usuario, $contrasena)
    {

        $this->usuarioUsuario = $usuario;
        $this->contrasena1Usuario = $contrasena;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "usuario_usuario",
                "campo_valor" => $this->usuarioUsuario,
                "formulario_nombre" => "nombre de usuario",
                "requerido" => true,
                "minimo" => minRegexUsuario,
                "maximo" => maxRegexUsuario,
                "expresion_re" => regexUsuario,
                "tabla" => "usuarios",
                "debeExistir" => true
            ],
            [
                "campo_valor" => $this->contrasena1Usuario,
                "formulario_nombre" => "contraseña",
                "requerido" => true,
                "minimo" => minRegexContrasena,
                "maximo" => maxRegexContrasena,
                "expresion_re" => regexContrasena,
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->iniciarSesionUsuariosP();
        }
    }
    public function cerrarSesionUsuarios()
    {
        return $this->cerrarSesionUsuariosP();
    }

    //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
    private function seleccionarUsuariosP()
    {
        if ($this->cedulaUsuario == null || $this->cedulaUsuario == "") {
            //campos específicos para la consulta
            $instruccionesBD = [
                'campos' => '
                        u.cedula_usuario, ro.nombre_rol, u.nombre_usuario,
                        u.apellido_usuario, u.telefono_usuario, u.correo_usuario,
                        u.usuario_usuario
                    ',
                'tabla' => 'usuarios AS u',
                'PEL' => 'u',
                'datosJoins' => [
                    [
                        "TablaDestino" => "roles AS ro",
                        "conexionLo" => "u.id_rol = ro.id_rol"
                    ]
                ],
                'WHERE' => [
                    [
                        "condicion_campo" => "cedula_usuario",
                        "condicion_marcador" => ":ID",
                        "condicion_valor" => 30485684,
                        "comparacion" => "!="
                    ],
                    [
                        "condicion_campo" => "cedula_usuario",
                        "condicion_marcador" => ":ID2",
                        "condicion_valor" => $_SESSION['cedula'],
                        "comparacion" => "!="
                    ],
                ],
            ];
            $datos = $this->seleccionarDatos($instruccionesBD);
            $datos = $datos->fetchAll(PDO::FETCH_ASSOC);
            return $datos; /*Devolvemos*/
        } else {

            /*Hacemos la consulta */;
            $instruccionesBD = [
                'campos' => '
                    cedula_usuario, nombre_usuario,
                    apellido_usuario, telefono_usuario, correo_usuario,
                    usuario_usuario, id_rol
                ',
                'tabla' => 'usuarios',
                'WHERE' => [
                    [
                        "condicion_campo" => "cedula_usuario",
                        "condicion_marcador" => ":ID",
                        "condicion_valor" => $this->cedulaUsuario,
                        "comparacion" => "="
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);

            /*Verificamos que el Usuario seleccionado exista */
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Usuario no encontrado",
                    "texto" => "El usuario que ha intentado actualizar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
            }
            return $usuario;
        }
    }
    private function registrarUsuariosP()
    {
        $datos_registro_usuarios = [
            [
                "campo_nombre" => "cedula_usuario",
                "campo_marcador" => ":cedula",
                "campo_valor" => $this->cedulaUsuario
            ],
            [
                "campo_nombre" => "nombre_usuario",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $this->nombreUsuario,
                "ponerEnMayusculas" => true
            ],
            [
                "campo_nombre" => "apellido_usuario",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $this->apellidoUsuario,
                "ponerEnMayusculas" => true
            ],
            [
                "campo_nombre" => "correo_usuario",
                "campo_marcador" => ":Correo",
                "campo_valor" => $this->correoUsuario
            ],
            [
                "campo_nombre" => "telefono_usuario",
                "campo_marcador" => ":Telefono",
                "campo_valor" => $this->telefonoUsuario
            ],
            [
                "campo_nombre" => "id_rol",
                "campo_marcador" => ":Rol",
                "campo_valor" => $this->rolUsuario
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $this->usuarioUsuario
            ],
            [
                "campo_nombre" => "contrasena_usuario",
                "campo_marcador" => ":Contrasena",
                "campo_valor" => $this->contrasena1Usuario
            ],
        ];
        $condicion = [
            "condicion_campo" => "cedula_usuario",
            "condicion_marcador" => ":cedula",
            "condicion_valor" => $this->cedulaUsuario
        ];

        $resultado = $this->guardarDatos('usuarios', $datos_registro_usuarios, $condicion);
        if ($resultado == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Usuario registrado",
                "texto" => "El usuario ha sido registrado exitosamente",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Usuario no registrado",
                "texto" => "El usuario no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarUsuariosP()
    {

        $instruccionesBD = [
            "campos" => "contrasena_usuario, id_rol",
            "tabla" => "usuarios",
            'WHERE' => [
                [
                    "condicion_campo" => "cedula_usuario",
                    "condicion_marcador" => ":cedula",
                    "condicion_valor" => $this->cedulaUsuario,
                    "comparacion" => "="
                ]
            ]
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        $usuariosExistente = $resultado->fetch(PDO::FETCH_ASSOC);

        if ($this->contrasena1Usuario == "") {
            $this->contrasena1Usuario = $usuariosExistente['contrasena_usuario'];
        };
        if ($this->rolUsuario == '') {
            $this->rolUsuario = $usuariosExistente['id_rol'];
        }

        $instruccionesBD = [
            "tabla" => "usuarios",
            "datos" => [
                [
                    "campo_nombre" => "cedula_usuario",
                    "campo_marcador" => ":cedula",
                    "campo_valor" => $this->cedulaUsuario
                ],
                [
                    "campo_nombre" => "nombre_usuario",
                    "campo_marcador" => ":Nombre",
                    "campo_valor" => $this->nombreUsuario,
                    "ponerEnMayusculas" => true
                ],
                [
                    "campo_nombre" => "apellido_usuario",
                    "campo_marcador" => ":Apellido",
                    "campo_valor" => $this->apellidoUsuario,
                    "ponerEnMayusculas" => true
                ],
                [
                    "campo_nombre" => "correo_usuario",
                    "campo_marcador" => ":Correo",
                    "campo_valor" => $this->correoUsuario
                ],
                [
                    "campo_nombre" => "telefono_usuario",
                    "campo_marcador" => ":Telefono",
                    "campo_valor" => $this->telefonoUsuario
                ],
                [
                    "campo_nombre" => "id_rol",
                    "campo_marcador" => ":Rol",
                    "campo_valor" => $this->rolUsuario
                ],
                [
                    "campo_nombre" => "usuario_usuario",
                    "campo_marcador" => ":Usuario",
                    "campo_valor" => $this->usuarioUsuario
                ],
                [
                    "campo_nombre" => "contrasena_usuario",
                    "campo_marcador" => ":Contrasena",
                    "campo_valor" => $this->contrasena1Usuario
                ]
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "cedula_usuario",
                    "condicion_marcador" => ":cedula",
                    "condicion_valor" => $this->cedulaUsuario,
                    "comparacion" => "="
                ]
            ]
        ];

        $resultado = $this->actualizarDatos($instruccionesBD);

        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en el usuario",
                "icono" => "warning",
            ];
        } else {
            if ($this->cedulaUsuario == $_SESSION['cedula']) {
                $_SESSION['nombre'] = $this->nombreUsuario;
                $_SESSION['apellido'] = $this->apellidoUsuario;
                $_SESSION['telefono'] = $this->telefonoUsuario;
                $_SESSION['usuario'] = $this->usuarioUsuario;
                $_SESSION['rol'] = $this->rolUsuario;
            }
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Usuario actualizado",
                "texto" => "El usuario ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarUsuariosP()
    {

        /*hacemos la consulta */
        $instruccionesBD = [
            'campos' => '*',
            'tabla' => 'usuarios',
            'WHERE' => [
                [
                    "condicion_campo" => "cedula_usuario",
                    "condicion_marcador" => ":ID",
                    "condicion_valor" => $this->cedulaUsuario,
                    "comparacion" => "="
                ]
            ]
        ];
        $datos = $this->seleccionarDatos($instruccionesBD);
        /*verificamos que el usuario seleccionado exista */
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Usuario no encontrado",
                "texto" => "El usuario que ha intentado eliminar no se encuentra en la base de datos",
                "icono" => "error"
            ];
            return $alerta;
            exit();
        } else {
            $datos = $datos->fetch();/*hacemos el arrays */
        }

        $eliminarUsuario = $this->eliminarDatos("usuarios", "cedula_usuario", $this->cedulaUsuario);
        if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Usuario eliminado",
                "texto" => "El usuario de " . $datos['nombre_usuario'] . " " . $datos['apellido_usuario'] . " ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Usuario no encontrado",
                "texto" => "El usuario no existe en la Base de Datos",
                "icono" => "error"
            ];
        }
        return $alerta;
    }
    private function iniciarSesionUsuariosP()
    {
        $campos = [
            [
                "validarLogin" => [
                    "usuario" => $this->usuarioUsuario,
                    "contrasena" => $this->contrasena1Usuario,
                ]
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            $urlRedireccion = 'usuarios/';
            //Alerta para el redireccionamiento
            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL . $urlRedireccion
            ];
            return $alerta;
            exit();
        }
    }
    private function cerrarSesionUsuariosP()
    {
        session_destroy(); /*Destruimos la sesión y por ende todas las variables de SESSION*/

        //Alerta para el redireccionamiento
        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . "usuarios/login"
        ];
        return $alerta;
        exit();
    }
}
