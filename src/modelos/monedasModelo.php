<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;

class monedasModelo extends conexion
{

    private $idMoneda;
    private $nombreMoneda;
    private $simboloMoneda;
    private $valorMoneda;

    /*Métodos para tomas datos de las views y asignarlos a los atributos*/
    public function seleccionarMonedas($id = null)
    {
        $this->idMoneda = $id;
        if ($this->idMoneda != "") {
            $campos = [
                [
                    "campo_nombre" => "id_moneda",
                    "campo_valor" => $this->idMoneda,
                    "formulario_nombre" => "id",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "monedas",
                    "debeExistir" => true
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }

        return $this->seleccionarMonedasP();
    }
    public function seleccionarCambiosMonedas()
    {
        return $this->seleccionarCambiosMonedasP();
    }
    public function registrarMonedas($nombreMoneda, $simboloMoneda, $valorMoneda)
    {
        $this->nombreMoneda = $nombreMoneda;
        $this->valorMoneda = $valorMoneda;
        $this->simboloMoneda = $simboloMoneda;

        $campos = [
            [
                "campo_nombre" => "nombre_moneda",
                "campo_valor" => $this->nombreMoneda,
                "formulario_nombre" => "nombre",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "monedas",
                "debeSerUnico" => true,
            ],
            [
                "campo_valor" => $this->valorMoneda,
                "formulario_nombre" => "valor",
                "requerido" => true,
                "minimo" => minRegexPrecio,
                "maximo" => maxRegexPrecio,
                "expresion_re" => regexPrecio,
            ],
            [
                "campo_nombre" => "simbolo_moneda",
                "campo_valor" => $this->simboloMoneda,
                "formulario_nombre" => "símbolo",
                "requerido" => true,
                "minimo" => minRegexSimboloMoneda,
                "maximo" => maxRegexSimboloMoneda,
                "expresion_re" => regexSimboloMoneda,
                "tabla" => "monedas",
                "debeSerUnico" => true
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->registrarMonedasP();
        }
    }
    public function actualizarMonedas($tipoAct, $idMoneda, $valorMoneda, $nombreMoneda = null, $simboloMoneda = null)
    {
        $this->idMoneda = $idMoneda;
        $this->nombreMoneda = $nombreMoneda;
        $this->simboloMoneda = $simboloMoneda;
        $this->valorMoneda = $valorMoneda;

        $campos = [
            [
                "campo_nombre" => "id_moneda",
                "campo_valor" => $this->idMoneda,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "monedas",
                "debeExistir" => true
            ],
            [
                "campo_valor" => $this->valorMoneda,
                "formulario_nombre" => "valor",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexPrecio,
            ]
        ];

        if ($tipoAct == 'completa' && $nombreMoneda != '' && $simboloMoneda != '') {
            $campos[] = [
                "campo_nombre" => "nombre_moneda",
                "campo_valor" => $this->nombreMoneda,
                "formulario_nombre" => "nombre",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "monedas",
                "debeSerUnico" => true
            ];
            $campos[] = [
                "campo_nombre" => "simbolo_moneda",
                "campo_valor" => $this->simboloMoneda,
                "formulario_nombre" => "símbolo",
                "requerido" => true,
                "minimo" => minRegexSimboloMoneda,
                "maximo" => maxRegexSimboloMoneda,
                "expresion_re" => regexSimboloMoneda,
                "tabla" => "monedas",
                "debeSerUnico" => true
            ];
        }

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarMonedasP($tipoAct);
        }
    }
    public function eliminarMonedas($idMoneda)
    {
        $this->idMoneda = $idMoneda;

        $campos = [
            [
                "campo_nombre" => "id_moneda",
                "campo_valor" => $this->idMoneda,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" =>regexId,
                "tabla" => "monedas",
                "debeExistir" => true
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarMonedasP();
        }
    }

    /*Métodos privados para interactuar con la base de datos*/
    private function seleccionarMonedasP()
    {

        if ($this->idMoneda == null || $this->idMoneda == "") {

            ;
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'monedas',
                'ORDER' => 'nombre_moneda'
            ];
            $datos = $this->seleccionarDatos($instruccionesBD);/*la ejecutamos*/
            $datos = $datos->fetchAll(PDO::FETCH_ASSOC); /*Creamos el arrays de tipo asociativo*/
            return $datos; /*Devolvemos*/
        } else {

            /*Hacemos la consulta */
            ;
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'monedas',
                'WHERE' => [
                    [
                        'condicion_campo' => 'id_moneda',
                        'condicion_marcador' => ':Id',
                        'condicion_valor' => $this->idMoneda,
                        'comparacion' => '=',
                    ]
                ],
                'ORDER' => 'nombre_moneda'
            ];
            $datos = $this->seleccionarDatos($instruccionesBD);

            /*Verificamos que el moneda seleccionado exista */
            if ($datos->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Moneda no encontrada",
                    "texto" => "La moneda que ha intentado actualizar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $datos = $datos->fetch(PDO::FETCH_ASSOC);/*Hacemos el arrays */
            }
            return $datos;
        }
    }
    private function seleccionarCambiosMonedasP()
    {

        $instruccionesBD = [
            'campos' => 'cm.id_cambio_moneda, mo.nombre_moneda, cm.valor_moneda, cm.fecha_cambio',
            'tabla' => 'cambios_monedas AS cm',
            'PEL' => 'cm',
            'datosJoins' => [
                [
                    "TablaDestino" => "monedas AS mo",
                    "conexionLo" => "cm.id_moneda = mo.id_moneda"
                ]
            ]
        ];
        $datos = $this->seleccionarDatos($instruccionesBD);
        $datos = $datos->fetchAll();
        return $datos;
    }
    private function registrarMonedasP()
    {
        $datos_registro_monedas = [
            [
                "campo_nombre" => "nombre_moneda",
                "campo_marcador" => ":nombre",
                "campo_valor" => $this->nombreMoneda,
                "ponerEnMayusculas"=>true
            ],
            [
                "campo_nombre" => "simbolo_moneda",
                "campo_marcador" => ":simbolo",
                "campo_valor" => $this->simboloMoneda
            ],
            [
                "campo_nombre" => "valor_moneda",
                "campo_marcador" => ":valor",
                "campo_valor" => $this->valorMoneda,
                "comaPunto"=>true,
            ],
        ];
        $ultimoId = $this->guardarDatos('monedas', $datos_registro_monedas);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Moneda registrada",
                "texto" => "La moneda ha sido registrada exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Moneda no registrada",
                "texto" => "La moneda no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarMonedasP($tipoAct)
    {

        $datosMoneda = $this->seleccionarMonedas($this->idMoneda);
        $valorActualMo = $datosMoneda['valor_moneda'];

        $instruccionesBD = [
            "tabla" => "monedas",
            "datos" => [
                [
                    "campo_nombre" => "valor_moneda",
                    "campo_marcador" => ":precio",
                    "campo_valor" => $this->valorMoneda,
                    "comaPunto"=>true,
                ],
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_moneda",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idMoneda,
                    "comparacion" => "="
                ]
            ]
        ];
        if ($tipoAct == 'completa') {
            $instruccionesBD['datos'][] = [
                "campo_nombre" => "nombre_moneda",
                "campo_marcador" => ":nombre",
                "campo_valor" => $this->nombreMoneda,
                "ponerEnMayusculas"=>true
            ];
            $instruccionesBD['datos'][] = [
                "campo_nombre" => "simbolo_moneda",
                "campo_marcador" => ":simbolo",
                "campo_valor" => $this->simboloMoneda
            ];
        }

        $resultado = $this->actualizarDatos($instruccionesBD);
        if ($resultado == false || $resultado <= 0) {
            $alertaError = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en la moneda",
                "icono" => "warning",
            ];
        } else {
            $alertaExito = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Moneda actualizada",
                "texto" => "La moneda ha sido actualizado exitosamente",
                "icono" => "success",
            ];
        }
        if (isset($alertaError)) {
            $this->rollback();
            return $alertaError;
            exit();
        }

        //Comenzamos la segunda transacción [Insertar el cambio en el historial]
        if ($valorActualMo != $this->valorMoneda) {
            $datos_registro_cambio = [
                [
                    "campo_nombre" => "id_moneda",
                    "campo_marcador" => ":moneda",
                    "campo_valor" => $this->idMoneda
                ],
                [
                    "campo_nombre" => "valor_moneda",
                    "campo_marcador" => ":valor",
                    "campo_valor" => $this->valorMoneda,
                    "comaPunto"=>true,
                ],
                [
                    "campo_nombre" => "fecha_cambio",
                    "campo_marcador" => ":fecha",
                    "campo_valor" => $this->FechaHora_Sel('fecha_hora_BD')
                ],
            ];
            $resultado = $this->guardarDatos('cambios_monedas', $datos_registro_cambio);
            if ($resultado !== false && $resultado > 0) {
                $alertaExito = [
                    "tipo" => "limpiar",
                    "titulo" => "Valor actualizado",
                    "texto" => "El valor de la moneda ha sido actualizado exitosamente",
                    "icono" => "success",
                ];
            } else {
                $alertaError = [
                    "tipo" => "simple",
                    "titulo" => "Valor no actualizado",
                    "texto" => "El valor de la moneda no se ha podido actualizar",
                    "icono" => "error",
                ];
            }
        }

        if (isset($alertaError)) {
            $this->rollback();
            return $alertaError;
            exit();
        } else {
            $this->commit();
            return $alertaExito;
            exit();
        }
    }
    private function eliminarMonedasP()
    {
        $eliminarUsuario = $this->eliminarDatos("monedas", "id_moneda", $this->idMoneda);
        if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Moneda eliminada",
                "texto" => "La moneda ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Moneda no encontrada",
                "texto" => "La moneda no existe en la Base de Datos",
                "icono" => "error"
            ];
        }
        return $alerta;
    }
}
