<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;

class metodosPagoModelo extends conexion
{

    private $idMetodoDePago;
    private $nombreMetodoPago;
    private $necesitaMonedaMetodoPago;

    /*Métodos para tomas datos de las views y asignarlos a los atributos*/
    public function seleccionarMetodoPago($id = null)
    {
        $this->idMetodoDePago = $id;

        if (isset($this->idMetodoDePago)) {
            $campos = [
                [
                    "campo_nombre" => "id_metodo_pago",
                    "campo_valor" => $this->idMetodoDePago,
                    "formulario_nombre" => "id",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" =>maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "metodos_pagos",
                    "debeExistir" => true
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }
        return $this->seleccionarMetodoPagoP();
    }
    public function registrarMetodoPago($nombre, $necesitaMoneda)
    {
        $this->nombreMetodoPago = $nombre;
        $this->necesitaMonedaMetodoPago = $necesitaMoneda != '' ? $necesitaMoneda : 0;
        
        $campos = [
            [
                "campo_nombre" => "nombre_metodo_pago",
                "campo_valor" => $this->nombreMetodoPago,
                "formulario_nombre" => "nombre",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "metodos_pagos",
                "debeSerUnico" => true
            ],
            [
                "campo_valor" => $this->necesitaMonedaMetodoPago,
                "formulario_nombre" => "necesita moneda",
                "requerido" => true,
                "minimo" => minRegexValorBoleano,
                "maximo" => maxRegexValorBoleano,
                "expresion_re" => regexValorBoleano,
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->registrarMetodoPagoP();
        }
    }
    public function actualizarMetodoPago($id, $nombre, $necesitaMoneda)
    {

        $this->idMetodoDePago = $id;
        $this->nombreMetodoPago = $nombre;
        $this->necesitaMonedaMetodoPago = $necesitaMoneda != '' ? $necesitaMoneda : 0;
        
        $campos = [
            [
                "campo_nombre" => "id_metodo_pago",
                "campo_valor" => $this->idMetodoDePago,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "metodos_pagos",
                "debeExistir" => true
            ],
            [
                "campo_valor" => $this->necesitaMonedaMetodoPago,
                "formulario_nombre" => "necesita moneda",
                "requerido" => true,
                "minimo" => minRegexValorBoleano,
                "maximo" => maxRegexValorBoleano,
                "expresion_re" => regexValorBoleano,
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarMetodoPagoP();
        }
    }
    public function eliminarMetodoPago($id)
    {
        $this->idMetodoDePago = $id;

        $campos = [
            [
                "campo_nombre" => "id_metodo_pago",
                "campo_valor" => $this->idMetodoDePago,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "metodos_pagos",
                "debeExistir" => true
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarMetodoPagoP();
        }
    }

    /*Métodos privados para interactuar con la base de datos*/
    private function seleccionarMetodoPagoP()
    {

        if (!isset($this->idMetodoDePago)) {

            //return $this->idMetodoDePago;
        ;
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'metodos_pagos',
                'ORDER' => 'nombre_metodo_pago',
            ];
            $datos = $this->seleccionarDatos($instruccionesBD);/*la ejecutamos*/
            $metodosPago = $datos->fetchAll(PDO::FETCH_ASSOC); /*Creamos el arrays de tipo asociativo*/
            return $metodosPago; /*Devolvemos*/
        } else {
            /*Hacemos la consulta */
        ;
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'metodos_pagos',
                'WHERE' => [
                    [
                        'condicion_campo' => 'id_metodo_pago',
                        'condicion_marcador' => ':Id',
                        'condicion_valor' => $this->idMetodoDePago,
                        'comparacion' => '=',
                    ]
                ],
                'ORDER' => 'nombre_metodo_pago'
            ];
            $datos = $this->seleccionarDatos($instruccionesBD);

            if ($datos->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Metodo de pago no encontrado",
                    "texto" => "El método de pago que ha intentado actualizar no se encuentra en la base de datos",
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
    private function registrarMetodoPagoP()
    {
        $datos_registro_metodo_pago = [
            [
                "campo_nombre" => "nombre_metodo_pago",
                "campo_marcador" => ":nombre",
                "campo_valor" => $this->nombreMetodoPago,
                "ponerEnMayusculas"=>true
            ],
            [
                "campo_nombre" => "necesita_Moneda",
                "campo_marcador" => ":necesita_Moneda",
                "campo_valor" => $this->necesitaMonedaMetodoPago
            ],
        ];
        $ultimoId = $this->guardarDatos('metodos_pagos', $datos_registro_metodo_pago);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Metodo de pago registrado",
                "texto" => "El método de pago ha sido registrada exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Método de pago no registrado",
                "texto" => "El método de pago no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarMetodoPagoP()
    {
        $instruccionesBD = [
            "tabla" => "metodos_pagos",
            "datos" => [
                [
                    "campo_nombre" => "nombre_metodo_pago",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $this->nombreMetodoPago,
                    "ponerEnMayusculas"=>true
                ],
                [
                    "campo_nombre" => "necesita_moneda",
                    "campo_marcador" => ":necesita_moneda",
                    "campo_valor" => $this->necesitaMonedaMetodoPago
                ],
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_metodo_pago",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idMetodoDePago,
                    "comparacion" => "="
                ]
            ]
        ];
        $resultado = $this->actualizarDatos($instruccionesBD);

        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en el método de pago",
                "icono" => "warning",
            ];
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Método de pago actualizado",
                "texto" => "El método de pago ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarMetodoPagoP()
    {
        $eliminarCategoria = $this->eliminarDatos("metodos_pagos", "id_metodo_pago", $this->idMetodoDePago);
        if ($eliminarCategoria->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Método de pago eliminado",
                "texto" => "La método de pago ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Método de pago no encontrado",
                "texto" => "La método de pago no existe en la Base de Datos",
                "icono" => "error"
            ];
        }
        return $alerta;
    }
}

