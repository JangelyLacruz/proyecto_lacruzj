<?php

namespace src\modelos;
use PDO;
use src\modelos\rolesModelo;
use src\config\connect\conexion;

class permisosModelo extends conexion
{
    private $idPermiso;
    private $idRol;
    private $idModulo;
    private $cambio;
    private $moduloVal;
    private $permisoVal;

    public function listarPermisos($idRol)
    {

        $this->idRol = $idRol;
        if ($this->idRol != '') {
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
                    "debeExistir" => true
                ]
            ];
            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        } else {
            $modeloRoles = new rolesModelo();
            $roles = $modeloRoles->seleccionarRoles();
            $primerRol = $roles[0]['id_rol'];
            $this->idRol = $primerRol;
        }
        return $this->listarPermisosP();
    }
    public function SeleccionarPermisosPorRol()
    {
        $this->idRol = $_SESSION['rol'];

        if ($this->idRol != "") {
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
                    "debeExistir" => true
                ]
            ];
            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }
        return $this->SeleccionarPermisosPorRolP();
    }
    public function ActualizarPermisos($rol, $modulo, $permiso, $cambio)
    {

        $this->idRol = $rol;
        $this->idModulo = $modulo;
        $this->idPermiso = $permiso;
        $this->cambio = $cambio;

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
                "debeExistir" => true
            ],
            [
                "campo_nombre" => "id_modulo",
                "campo_valor" => $this->idModulo,
                "formulario_nombre" => "id del módulo",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "modulos",
                "debeExistir" => true
            ],
            [
                "campo_nombre" => "id_permiso",
                "campo_valor" => $this->idPermiso,
                "formulario_nombre" => "id del permiso",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "permisos",
                "debeExistir" => true
            ],
            [
                "campo_valor" => $this->cambio,
                "formulario_nombre" => "cambio del permiso",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        }

        return $this->ActualizarPermisosP();
    }
    public function Permisos_Val($modulo, $permiso)
    {
        $this->moduloVal= $modulo;

        switch ($permiso) {
            case 'listar':
            case 'seleccionarUno':
            case 'seleccionarDeuda':
            case 'listarPorRol':
            case 'listarPorCategoria':
            case 'listarDetalles':
            case 'listarDespachadas':
            case 'listarSinPago':
            case 'cerrarSesion':
                $this->permisoVal='listar';
                break;
            case 'actualizarFoto':
            case 'eliminarFoto':
            case 'cambiarEstado':
            case 'registrarPago':
            case 'cambiarTemaInterfaz':
                $this->permisoVal='actualizar';
                break;
            case 'registrarToken':
                $this->permisoVal='registrar';
                break;
            case 'consultaDashboard':
                $this->moduloVal='dashboard';
                $this->permisoVal='ver dashboard';
                break;
            case 'reporte_ventas':
            case 'resumen_ventas':
                $this->permisoVal='imprimir reportes de ventas';
                break;
            case 'reporte_productos':
                $this->permisoVal='imprimir reportes de productos';
                break;
            case 'comanda_venta':
                $this->permisoVal='imprimir comandas';
                break;
            default:
                $this->permisoVal= $permiso;
                break;
        }
        switch ($modulo) {
            case 'bitacora':
                $this->permisoVal='ver bitácora';
            break;
            default:
                
            break;
        }

        $campos = [
            [
                "campo_valor" => $this->moduloVal,
                "formulario_nombre" => "modulo a validar",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
            ],
            [
                "campo_valor" => $this->permisoVal,
                "formulario_nombre" => "permiso a validar",
                "requerido" => true,
                "minimo" => minRegexDescripcion,
                "maximo" => maxRegexDescripcion,
                "expresion_re" => regexDescripcion,
            ],
        ];
        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        }
        return $this->ValidarPermisos();

    }

    private function listarPermisosP()
    {
        if ($this->idRol == '') {
            $this->idRol = 1;
        }
        //Obtenemos todos los modulos
        $instruccionesBD = [
            'campos' => '*',
            'tabla' => 'modulos',
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        if ($resultado->rowCount() > 0) {
            $modulosTotales = $resultado->fetchAll(PDO::FETCH_ASSOC);
        }

        //Todos los permisos generales
        $instruccionesBD = [
            'campos' => '*',
            'tabla' => 'permisos',
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        if ($resultado->rowCount() > 0) {
            $permisosTotales = $resultado->fetchAll(PDO::FETCH_ASSOC);
        }

        //separamos los permisos generales de los especiales
        $permisosGenerales = [];
        $permisosEspeciales = [];
        foreach ($permisosTotales as $permiso) {
            if(
                $permiso['nombre_permiso'] == 'ver' ||
                $permiso['nombre_permiso'] == 'listar' ||
                $permiso['nombre_permiso'] == 'registrar' ||
                $permiso['nombre_permiso'] == 'actualizar' ||
                $permiso['nombre_permiso'] == 'eliminar'
            ){
                $permisosGenerales[] = $permiso;
            }else{
                $permisosEspeciales[] = $permiso;
            }
        }

        //permisos que tiene el rol
        $instruccionesBD = [
            'campos' => 'id_permiso, id_modulo',
            'tabla' => 'accesos',
            'WHERE' => [
                [
                    "condicion_campo" => "id_rol",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idRol,
                    "comparacion" => "="
                ]
            ],
            'ORDER' => 'id_modulo ASC'
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        if ($resultado->rowCount() > 0) {
            $permisosRol = $resultado->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $permisosRol = [];
        }

        /*Para agrupar los permisos del rol por modulo*/
        $idModulo = 0;
        $nuevosArrPerRol = [];
        foreach ($permisosRol as $permiso) {
            if ($idModulo !== $permiso['id_modulo']) {
                $nuevosArrPerRol[$permiso['id_modulo']][] = $permiso['id_permiso'];
            } else {
                $idModulo = $permiso['id_modulo'];
                $nuevosArrPerRol[$permiso['id_modulo']] = $permiso['id_permiso'];
            }
        }

        /*Función para armar la estructura completa de la permisologia
        con los permisos generales*/
        foreach ($modulosTotales as $modulo) {
            $idModulo = $modulo['id_modulo'];
            $permisosAsignados = $nuevosArrPerRol[$idModulo] ?? [];

            $permisosDelModulo = [];
            foreach ($permisosGenerales as $permiso) {
                $permisosDelModulo[] = [
                    "id" => $permiso['id_permiso'],
                    "nombre" => $permiso['nombre_permiso'],
                    // Verifica si el id_permiso está en los permisos asignados
                    "activo" => in_array($permiso['id_permiso'], $permisosAsignados)
                ];
            }

            if (
                $modulo['nombre_modulo'] != 'dashboard' &&
                $modulo['nombre_modulo'] != 'reportes' &&
                $modulo['nombre_modulo'] != 'bitacora' &&
                $modulo['nombre_modulo'] != 'cambios' &&
                $modulo['nombre_modulo'] != 'imagenes'
            ) {
                $totalidadPermisos[] = [
                    "modulo" => [
                        "id" => $idModulo,
                        "nombre" => $modulo['nombre_modulo']
                    ],
                    "permisos" => $permisosDelModulo
                ];
            }
        }

        /*Función para armar la estructura completa de la permisologia
        con los permisos especiales*/
        $respaldoPerEsp = [
            'dashboard' => ['ver dashboard'],
            'cambios' => ['ver historial de cambio', 'actualizar cambio de divisas'],
            'ventas' => ['ver detalles de las ventas','ver ventas despachadas', 'ver ventas sin cancelar'],
            'reportes' => [
                'imprimir reportes de ventas',
                'imprimir reportes de productos',
                'imprimir comandas',
            ],
            'usuarios' => [
                'asignar roles a usuarios',
                'ver el precio del dólar',
                'ver notificaciones',
                'ver modal de ayuda'
            ],
            'promociones' => ['ver detalles de promociones'],
            'bitacora' => ['ver bitácora'],
            'imagenes' => ['transformar imagenes']
        ];

        $NTPE = 0;
        foreach ($respaldoPerEsp as $modulo => $permisos) {
            $NTPE += count($permisos);
        }

        $instruccionesBD = [
            'campos' => '
                ac.id_permiso, pe.nombre_permiso, ac.id_modulo,
                mo.nombre_modulo,ac.estado
            ',
            'tabla' => 'accesos as ac',
            'PEL' => 'ac',
            'eliminadosYVigentes' => true,
            'datosJoins' => [
                [
                    'TablaDestino' => 'permisos as pe',
                    'conexionLo' => 'ac.id_permiso = pe.id_permiso',
                ],
                [
                    'TablaDestino' => 'modulos as mo',
                    'conexionLo' => 'ac.id_modulo = mo.id_modulo',
                ],
            ],
            'WHERE' => [
                [
                    "condicion_campo" => "ac.id_rol",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idRol,
                    "comparacion" => "="
                ],
                [
                    "condicion_campo" => "ac.id_permiso",
                    "condicion_marcador" => ":idPermiso1",
                    "condicion_valor" => 1,
                    "comparacion" => "!="
                ],
                [
                    "condicion_campo" => "ac.id_permiso",
                    "condicion_marcador" => ":idPermiso2",
                    "condicion_valor" => 2,
                    "comparacion" => "!="
                ],
                [
                    "condicion_campo" => "ac.id_permiso",
                    "condicion_marcador" => ":idPermiso3",
                    "condicion_valor" => 3,
                    "comparacion" => "!="
                ],
                [
                    "condicion_campo" => "ac.id_permiso",
                    "condicion_marcador" => ":idPermiso4",
                    "condicion_valor" => 4,
                    "comparacion" => "!="
                ],
                [
                    "condicion_campo" => "ac.id_permiso",
                    "condicion_marcador" => ":idPermiso5",
                    "condicion_valor" => 5,
                    "comparacion" => "!="
                ],
            ],
            'ORDER' => 'id_modulo ASC'
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        $permisosEspRol = $resultado->fetchAll(PDO::FETCH_ASSOC);


        $CR = 0;
        $huboRespaldado = false;
        //comprobar que estén los permisos asi sea eliminados lógicamente
        if (count($permisosEspRol) < $NTPE) {
            foreach ($respaldoPerEsp as $modulo => $permisos) {

                //CONSULTAMOS EL ID DEL MODULO
                $instruccionesBD = [
                    'campos' => 'id_modulo',
                    'tabla' => 'modulos',
                    'WHERE' => [
                        [
                            "condicion_campo" => "nombre_modulo",
                            "condicion_marcador" => ":nombre",
                            "condicion_valor" => $modulo,
                            "comparacion" => "="
                        ],
                    ],
                    'LIMIT' => 1
                ];
                $resultado = $this->seleccionarDatos($instruccionesBD);
                //SI EXISTE LO TOMAMOS Y SI NO LO CREAMOS
                if ($resultado->rowCount() == 1) {
                    $idModulo = $resultado->fetch(PDO::FETCH_COLUMN);
                } else {
                    $datos_registro_modulo = [
                        [
                            "campo_nombre" => "nombre_modulo",
                            "campo_marcador" => ":nombre",
                            "campo_valor" => $modulo
                        ],
                        [
                            "campo_nombre" => "estado",
                            "campo_marcador" => ":estado",
                            "campo_valor" => "1"
                        ],
                    ];
                    $ultimoId = $this->guardarDatos('modulos', $datos_registro_modulo);
                    if ($ultimoId !== false && $ultimoId > 0) {
                        $idModulo = $ultimoId;
                    }
                }

                //CONSULTAMOS EL ID DE LOS PERMISOS 
                foreach ($permisos as $permiso) {
                    $instruccionesBD = [
                        'campos' => 'id_permiso',
                        'tabla' => 'permisos',
                        'WHERE' => [
                            [
                                "condicion_campo" => "nombre_permiso",
                                "condicion_marcador" => ":nombre",
                                "condicion_valor" => $permiso,
                                "comparacion" => "="
                            ],
                        ],
                        'LIMIT' => 1
                    ];
                    $resultado = $this->seleccionarDatos($instruccionesBD);
                    //SI EXISTE LO TOMAMOS Y SI NO LO CREAMOS
                    if ($resultado->rowCount() == 1) {
                        $idPermiso = $resultado->fetch(PDO::FETCH_COLUMN);
                    } else {
                        $datos_registro_permiso = [
                            [
                                "campo_nombre" => "nombre_permiso",
                                "campo_marcador" => ":nombre",
                                "campo_valor" => $permiso
                            ],
                            [
                                "campo_nombre" => "estado",
                                "campo_marcador" => ":estado",
                                "campo_valor" => "1"
                            ],
                        ];
                        $ultimoId = $this->guardarDatos('permisos', $datos_registro_permiso);
                        if ($ultimoId !== false && $ultimoId > 0) {
                            $idPermiso = $ultimoId;
                        }
                    }

                    /*AHORA VERIFICAMOS QUE EL PERMISO ESTE O 
                    NO REGISTRADO AL ROL EN LOS ACCESOS*/

                    $instruccionesBD = [
                        'campos' => 'id_acceso',
                        'tabla' => 'accesos',
                        'eliminadosYVigentes' => true,
                        'WHERE' => [
                            [
                                "condicion_campo" => "id_modulo",
                                "condicion_marcador" => ":modulo",
                                "condicion_valor" => $idModulo,
                                "comparacion" => "="
                            ],
                            [
                                "condicion_campo" => "id_permiso",
                                "condicion_marcador" => ":permiso",
                                "condicion_valor" => $idPermiso,
                                "comparacion" => "="
                            ],
                            [
                                "condicion_campo" => "id_rol",
                                "condicion_marcador" => ":rol",
                                "condicion_valor" => $this->idRol,
                                "comparacion" => "="
                            ],
                        ],
                    ];
                    $resultado = $this->seleccionarDatos($instruccionesBD);

                    $datos = [
                        'id_rol' => $this->idRol,
                        'id_modulo' => $idModulo,
                        'id_permiso' => $idPermiso,
                        'resultado Consulta' => $resultado->rowCount(),
                    ];
                    //return $datos;

                    if ($resultado->rowCount() == 0) {
                        $datos_registro_acceso = [
                            [
                                "campo_nombre" => "id_permiso",
                                "campo_marcador" => ":permiso",
                                "campo_valor" => $idPermiso
                            ],
                            [
                                "campo_nombre" => "id_modulo",
                                "campo_marcador" => ":modulo",
                                "campo_valor" => $idModulo
                            ],
                            [
                                "campo_nombre" => "id_rol",
                                "campo_marcador" => ":rol",
                                "campo_valor" => $this->idRol
                            ],
                            [
                                "campo_nombre" => "estado",
                                "campo_marcador" => ":estado",
                                "campo_valor" => "0"
                            ],
                        ];
                        $ultimoId = $this->guardarDatos('accesos', $datos_registro_acceso);
                        if ($ultimoId !== false && $ultimoId > 0) {
                            $this->commit();
                            $CR++;
                        } else {
                            $alerta = [
                                'tipo' => 'simple',
                                'icono' => 'error',
                                'texto' => 'hubo un error al guardar el acceso',
                                'titulo' => 'No se guardo el acceso',
                            ];
                            $this->rollback();
                            return $alerta;
                        }
                    }
                }
            }
            $huboRespaldado = true;
        }

        $permisosConRespaldo = false;
        if ($huboRespaldado) {
            $instruccionesBD = [
                'campos' => '
                    ac.id_permiso, pe.nombre_permiso, ac.id_modulo,
                    mo.nombre_modulo, ac.estado
                ',
                'tabla' => 'accesos as ac',
                'PEL' => 'ac',
                'eliminadosYVigentes' => true,
                'datosJoins' => [
                    [
                        'TablaDestino' => 'permisos as pe',
                        'conexionLo' => 'ac.id_permiso = pe.id_permiso',
                    ],
                    [
                        'TablaDestino' => 'modulos as mo',
                        'conexionLo' => 'ac.id_modulo = mo.id_modulo',
                    ],
                ],
                'WHERE' => [
                    [
                        "condicion_campo" => "ac.id_rol",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $this->idRol,
                        "comparacion" => "="
                    ],
                    [
                        "condicion_campo" => "ac.id_permiso",
                        "condicion_marcador" => ":idPermiso1",
                        "condicion_valor" => 1,
                        "comparacion" => "!="
                    ],
                    [
                        "condicion_campo" => "ac.id_permiso",
                        "condicion_marcador" => ":idPermiso2",
                        "condicion_valor" => 2,
                        "comparacion" => "!="
                    ],
                    [
                        "condicion_campo" => "ac.id_permiso",
                        "condicion_marcador" => ":idPermiso3",
                        "condicion_valor" => 3,
                        "comparacion" => "!="
                    ],
                    [
                        "condicion_campo" => "ac.id_permiso",
                        "condicion_marcador" => ":idPermiso4",
                        "condicion_valor" => 4,
                        "comparacion" => "!="
                    ],
                ],
                'ORDER' => 'id_modulo ASC'
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $permisosConRespaldo = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $permisosEspRol = $permisosConRespaldo;
        }

        // return [
        //     'hubo respaldo' => $huboRespaldado,
        //     'contador de registros de accesos' => $CR,
        //     'permisos del especiales del rol' => $permisosEspRol,
        //     'permisos del especiales del rol con respaldado' => $permisosConRespaldo,
        // ];

        $permisos = [
            'generales' => $totalidadPermisos,
            'especiales' => $permisosEspRol
        ];
        return $permisos;
    }
    private function SeleccionarPermisosPorRolP()
    {
        //Obtenemos los permisos totales del rol en todos los modulos
        $instruccionesBD = [
            'campos' => 'ro.nombre_rol, mo.nombre_modulo, pe.nombre_permiso',
            'tabla' => 'accesos as ac',
            'PEL' => 'ac',
            'datosJoins' => [
                [
                    "TablaDestino" => 'roles as ro',
                    "conexionLo" => 'ac.id_rol = ro.id_rol'
                ],
                [
                    "TablaDestino" => 'permisos as pe',
                    "conexionLo" => 'ac.id_permiso = pe.id_permiso'
                ],
                [
                    "TablaDestino" => 'modulos as mo',
                    "conexionLo" => 'ac.id_modulo = mo.id_modulo'
                ]
            ],
            'WHERE' => [
                [
                    'condicion_campo' => 'ro.id_rol',
                    'condicion_valor' => $this->idRol,
                    'condicion_marcador' => ':id_rol',
                    'comparacion' => '=',
                ]
            ],
            'ORDER' => 'mo.nombre_modulo asc'
        ];


        $resultado = $this->seleccionarDatos($instruccionesBD);

        if ($resultado->rowCount() == 0) {
            $permisosRol = [];
            $ArrayPermisos = [];
        } else {
            $permisosRol = $resultado->fetchAll(PDO::FETCH_ASSOC);

            //Construimos la estructura sintetizada
            $ArrayPermisos = [];
            $nombreModulo = '';
            foreach ($permisosRol as $permiso) {
                if ($permiso['nombre_modulo'] != $nombreModulo) {
                    $ArrayPermisos[$permiso['nombre_modulo']] = [$permiso['nombre_permiso']];
                    $nombreModulo = $permiso['nombre_modulo'];
                } else {
                    $ArrayPermisos[$permiso['nombre_modulo']][] = $permiso['nombre_permiso'];
                }
            }
        }
        return $ArrayPermisos;
    }
    private function ActualizarPermisosP()
    {
        $instruccionesBD = [
            'campos' => '*',
            'tabla' => 'accesos',
            'eliminadosYVigentes' => true,
            'WHERE' => [
                [
                    "condicion_campo" => "id_rol",
                    "condicion_marcador" => ":rol",
                    "condicion_valor" => $this->idRol,
                    "comparacion" => "="
                ],
                [
                    "condicion_campo" => "id_modulo",
                    "condicion_marcador" => ":modulo",
                    "condicion_valor" => $this->idModulo,
                    "comparacion" => "="
                ],
                [
                    "condicion_campo" => "id_permiso",
                    "condicion_marcador" => ":permiso",
                    "condicion_valor" => $this->idPermiso,
                    "comparacion" => "="
                ],
            ],
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        if ($resultado->rowCount() <= 0) {
            $existePermiso = false;
        } else {
            $existePermiso = true;
            $registroAcceso = $resultado->fetch(PDO::FETCH_ASSOC);
            $idAcceso = $registroAcceso['id_acceso'];
        }
        //return $existePermiso;

        if (!$existePermiso) {
            $datos_registro_acceso = [
                [
                    "campo_nombre" => "id_rol",
                    "campo_marcador" => ":rol",
                    "campo_valor" => $this->idRol
                ],
                [
                    "campo_nombre" => "id_modulo",
                    "campo_marcador" => ":modulo",
                    "campo_valor" => $this->idModulo
                ],
                [
                    "campo_nombre" => "id_permiso",
                    "campo_marcador" => ":permiso",
                    "campo_valor" => $this->idPermiso
                ],
            ];
            $ultimoId = $this->guardarDatos('accesos', $datos_registro_acceso);
            if ($ultimoId !== false && $ultimoId > 0) {
                $alerta = [
                    "tipo" => "limpiar",
                    "titulo" => "Acceso registrado",
                    "texto" => "El acceso ha sido registrado exitosamente",
                    "icono" => "success",
                ];
                $this->commit();
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Acceso no registrado",
                    "texto" => "El acceso no ha sido registrado exitosamente",
                    "icono" => "error",
                ];
                $this->rollback();
            }
            return $alerta;
        } else {

            if ($this->cambio == 0) {
                $eliminarUsuario = $this->eliminarDatos("accesos", "id_acceso", $idAcceso);
                if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */
                    $alertaExito = [
                        "tipo" => "simple",
                        "titulo" => "Acceso actualizado",
                        "texto" => "El acceso ha sido deshabilitado correctamente",
                        "icono" => "success"
                    ];
                } else {
                    $alertaError = [
                        "tipo" => "simple",
                        "titulo" => "Acceso no deshabilitado",
                        "texto" => "El acceso no ha podido ser deshabilitado",
                        "icono" => "error"
                    ];
                }
            } else {
                $instruccionesBD = [
                    "tabla" => "accesos",
                    "datos" => [
                        [
                            "campo_nombre" => "estado",
                            "campo_marcador" => ":estado",
                            "campo_valor" => $this->cambio
                        ]
                    ],
                    "condiciones" => [
                        [
                            "condicion_campo" => "id_acceso",
                            "condicion_marcador" => ":id_acceso",
                            "condicion_valor" => $idAcceso,
                            "comparacion" => "="
                        ]
                    ]
                ];
                $resultado = $this->actualizarDatos($instruccionesBD);
                if ($resultado != 0) {
                    $alertaExito = [
                        "tipo" => "simple",
                        "titulo" => "Permiso actualizado",
                        "texto" => "El permiso ha sido actualizado exitosamente",
                        "icono" => "success",
                    ];
                } else {
                    $alertaError = [
                        "tipo" => "simple",
                        "titulo" => "Permiso no actualizado",
                        "texto" => "El permiso no ha sido actualizado",
                        "icono" => "error",
                    ];
                }
            }

            if (isset($alertaError)) {
                $this->rollback();
                return $alertaError;
            } else {
                $this->commit();
                return $alertaExito;
            }
        }
    }
    private function ValidarPermisos(){
        $permisos= $this->SeleccionarPermisosPorRol();
        if(!isset($permisos[$this->moduloVal])){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Acción no autorizada",
                "texto" => "No posee permisos para realizar la acción solicitada",
                "icono" => "error"
            ];
            return $alerta;
            exit();
        }
        
        if(in_array($this->permisoVal, $permisos[$this->moduloVal])) {
            return false;
        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Acción no autorizada",
                "texto" => "No posee permisos para realizar la acción solicitada",
                "icono" => "error",
                'permisos totales'=>$permisos,
                "modulo"=>$this->moduloVal,
                "permisos del modulo"=>$permisos[$this->moduloVal],
                "permiso recibido"=>$this->permisoVal
            ];
            return $alerta;
        }
    }
}