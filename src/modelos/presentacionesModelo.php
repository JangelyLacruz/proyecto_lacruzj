<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class presentacionesModelo extends conexion
{
    private $idPresentacion;
    private $idUnidadMedida;
    private $nombrePresentacion;
    private $cantidadPMP;

    public function seleccionarPresentaciones($id = null)
    {
        $this->idPresentacion = $id;

        if ($this->idPresentacion != null && $this->idPresentacion != "") {
            //Arrays para las validaciones
            $campos = [
                [
                    "campo_nombre" => 'id_presentacion',
                    "campo_valor" => $this->idPresentacion,
                    "formulario_nombre" => "id de la presentación",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => 'presentaciones',
                    "debeExistir" => true
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }
        return $this->seleccionarPresentacionesP();
    }
    public function registrarPresentaciones($idUnidadMedida, $nombre, $cantidadPMP)
    {
        try {
            $this->idUnidadMedida = $idUnidadMedida;
            $this->nombrePresentacion = $nombre;
            $this->cantidadPMP = $cantidadPMP;

            $campos = [
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_valor" => $this->idUnidadMedida,
                    "formulario_nombre" => "id de la unidad de medida",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "unidades_medidas",
                    "debeExistir" => true,
                ],
                [
                    "campo_nombre" => "nombre_presentacion",
                    "campo_valor" => $this->nombrePresentacion,
                    "formulario_nombre" => "nombre de la presentación",
                    "requerido" => true,
                    "minimo" => minRegexNombrePer,
                    "maximo" => maxRegexNombrePer,
                    "expresion_re" => regexNombrePer,
                    "tabla" => "presentaciones",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_valor" => $this->nombrePresentacion,
                    "formulario_nombre" => "cantidad del producto o materia prima",
                    "requerido" => true,
                    "minimo" => minRegexPrecio,
                    "maximo" => maxRegexPrecio,
                    "expresion_re" => regexPrecio,
                ],
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->registrarPresentacionesP();
            }
        } catch (PDOException $e) {
            throw new Exception("Error al registrar la presentación en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarPresentaciones($id, $idUnidadMedida, $nombre, $cantidadPMP)
    {
        $this->idPresentacion = $id;
        $this->idUnidadMedida = $idUnidadMedida;
        $this->nombrePresentacion = $nombre;
        $this->cantidadPMP = $cantidadPMP;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "id_materia_prima",
                "campo_valor" => $this->idPresentacion,
                "formulario_nombre" => "id de la presentación",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "presentaciones",
                "debeExistir" => true,
            ],
            [
                "campo_nombre" => "id_unidad_medida",
                "campo_valor" => $this->idUnidadMedida,
                "formulario_nombre" => "id de la unidad de medida",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "unidades_medidas",
                "debeExistir" => true,
            ],
            [
                "campo_nombre" => "nombre_presentacion",
                "campo_valor" => $this->nombrePresentacion,
                "formulario_nombre" => "nombre de la presentación",
                "requerido" => true,
                "minimo" => minRegexNombrePer,
                "maximo" => maxRegexNombrePer,
                "expresion_re" => regexNombrePer,
                "tabla" => "presentaciones",
                "debeSerUnico" => true,
            ],
            [
                "campo_valor" => $this->cantidadPMP,
                "formulario_nombre" => "cantidad del producto o materia prima",
                "requerido" => true,
                "minimo" => minRegexPrecio,
                "maximo" => maxRegexPrecio,
                "expresion_re" => regexPrecio,
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarPresentacionesP();
        }
    }
    public function eliminarPresentaciones($id)
    {
        $this->idPresentacion = $id;

        //Arrays para las validaciones
        $campos = [
            [
                "campo_nombre" => "id_rol",
                "campo_valor" => $this->idPresentacion,
                "formulario_nombre" => "id del rol",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "debeExistir" => true,
                "tabla" => "presentaciones"
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarPresentacionesP();
        }
    }

    //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
    private function seleccionarPresentacionesP()
    {
        if ($this->idPresentacion == null || $this->idPresentacion == "") {
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'presentaciones',
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $Presentaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
            return $Presentaciones;
        } else {

                /*Hacemos la consulta */;
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'presentaciones',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_presentacion",
                        "condicion_marcador" => ":ID",
                        "condicion_valor" => $this->idPresentacion,
                        "comparacion" => "="
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Presentacion no encontrada",
                    "texto" => "La presentación que ha intentado buscar no se encuentra en la base de datos",
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
    private function registrarPresentacionesP()
    {
        $datos_registro_Presentaciones = [
            [
                "campo_nombre" => "id_unidad_medida",
                "campo_marcador" => ":unidadMedida",
                "campo_valor" => $this->idUnidadMedida,
            ],
            [
                "campo_nombre" => "nombre_presentacion",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $this->nombrePresentacion,
                "ponerEnMayusculas" => true
            ],
            [
                "campo_nombre" => "cantidad_pmp",
                "campo_marcador" => ":cantidadPmp",
                "campo_valor" => $this->cantidadPMP,
            ],
        ];

        $ultimoId = $this->guardarDatos('presentaciones', $datos_registro_Presentaciones);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Presentación registrada",
                "texto" => "La presentación ha sido registrada exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Presentación no registrada",
                "texto" => "La presentación no ha sido registrada exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarPresentacionesP()
    {

        $instruccionesBD = [
            "tabla" => "presentaciones",
            "datos" => [
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_marcador" => ":unidadMedida",
                    "campo_valor" => $this->idUnidadMedida,
                ],
                [
                    "campo_nombre" => "nombre_presentacion",
                    "campo_marcador" => ":Nombre",
                    "campo_valor" => $this->nombrePresentacion,
                    "ponerEnMayusculas" => true
                ],
                [
                    "campo_nombre" => "cantidad_pmp",
                    "campo_marcador" => ":cantidadPmp",
                    "campo_valor" => $this->cantidadPMP,
                ],
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_presentacion",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idPresentacion,
                    "comparacion" => "="
                ]
            ]
        ];
        $resultado = $this->actualizarDatos($instruccionesBD);

        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en la presentación",
                "icono" => "warning",
            ];
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Presentación actualizada",
                "texto" => "La presentación ha sido actualizada exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarPresentacionesP()
    {
        $resultado = $this->eliminarDatos("presentaciones", "id_presentacion", $this->idPresentacion);
        if ($resultado->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Presentación eliminada",
                "texto" => "La presentación ha sido eliminada con éxito",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Presentación no encontrada",
                "texto" => "La presentación no existe en la Base de Datos",
                "icono" => "error"
            ];
        }
        return $alerta;
    }
}
