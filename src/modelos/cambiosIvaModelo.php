<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class cambiosIvaModelo extends conexion
{
    private $idCambio;
    private $montoCambioIva;

    public function seleccionarCambiosIva($id=null)
    {
        $this->idCambio = $id;

        if ($this->idCambio != null && $this->idCambio != "") {
            //Arrays para las validaciones
            $campos = [
                [
                    "campo_nombre" => 'id_cambio_iva',
                    "campo_valor" => $this->idCambio,
                    "formulario_nombre" => "id del cambio",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => 'cambios_iva',
                    "debeExistir" => true
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->seleccionarCambiosIvaP();
            }
        } else {
            return $this->seleccionarCambiosIvaP();
        }
    }
    public function registrarCambiosIva($monto)
    {
        try {
            $this->montoCambioIva = $monto;
            $campos = [
                [
                    "campo_valor" => $this->montoCambioIva,
                    "formulario_nombre" => "monto del IVA",
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
                return $this->registrarCambiosIvaP();
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception("Error al registrar el rol en la base de datos: " . $e->getMessage());
        }
    }

    //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
    private function seleccionarCambiosIvaP()
    {
        if ($this->idCambio == null || $this->idCambio == "") {
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'cambios_iva',
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $roles = $resultado->fetchAll(PDO::FETCH_ASSOC);
            return $roles;
        } else {

            /*Hacemos la consulta */;
            
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'cambios_iva',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_cambio",
                        "condicion_marcador" => ":ID",
                        "condicion_valor" => $this->idCambio,
                        "comparacion" => "="
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Valor del IVA no encontrado",
                    "texto" => "El valor que ha intentado buscar no se encuentra en la base de datos",
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
    private function registrarCambiosIvaP()
    {
        $datos_registro_cambio_iva = [
            [
                "campo_nombre" => "monto_cambio_iva",
                "campo_marcador" => ":monto",
                "campo_valor" => $this->montoCambioIva,
            ],
            [
                "campo_nombre" => "fecha_cambio_iva",
                "campo_marcador" => ":fecha",
                "campo_valor" => $this->FechaHora_Sel('fecha_hora_BD'),
            ]
        ];

        $ultimoId = $this->guardarDatos('cambios_iva', $datos_registro_cambio_iva);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Valor Actualizado",
                "texto" => "El valor del IVA ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Valor no actualizado",
                "texto" => "El valor del IVA no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
}
