<?php 
namespace src\modelos;
use PDO;
use PDOException;
use DateTime;
use DateTimeZone;
use DateInterval;

trait traitModelo {

    private $servidorDB = DB_SERVER;
    private $nombreDB = DB_NAME;
    private $userDB = DB_USER;
    private $passwordDB = DB_PASS;
    protected $conexion;

    public function limpiarCadena($cadena)
    {

        /*Arrays con palabras no admitidas */
        $palabras = ["<script>", "</script>", "<script src", "<script type=", "SELECT * FROM", "SELECT ", " SELECT ", "DELETE FROM", "INSERT INTO", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASES", "<?php", "?>", "--", "^", "<", ">", "==", "=", ";", "::"];

        $cadena = trim($cadena); /*Para borrar espacios en blanco*/
        $cadena = stripslashes($cadena); /*Eliminar los paréntesis */

        foreach ($palabras as $palabra) {
            $cadena = str_ireplace($palabra, "", $cadena); /*elimina la palabra expecificada */
        }

        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);

        return $cadena;
    }
    public function limpiar_Verificar($campos)
    {
        /*Ejemplo del uso 
            $campos=[
                [
                    //Para nombrar el datos
                    "campo_nombre"=>"usuario_nombre",//nombre BD
                    "campo_valor"=>"", //valor BD
                    "formulario_nombre"=>"fecha", //nombre para mostrar al usuario
                    //para validar el formato al final
                    "expresion_re"=>'',
                    "requerido"=>true,
                    "minimo"=>"1",
                    "maximo"=>"15",
                    //para cotejar datos que no pueden estar duplicados
                    "tabla"=>"",
                    "debeSerUnico"=>true,
                    //para verificar la existencia de un dato cuando se va a actualizar [para el ID]
                    "debeExistir",true,
                    //para verificar si dos campos son iguales [como para confirmaciones de contraseña y demás]
                    "camposIguales"=>"this->contrasena2",
                    "camposDiferentes"=>"30485684", //para cotejar si un dato es diferente a un valor específico
                    "validarLogin"=> //para validar el inicio de sesion
                        [
                            "usuario"=>$this->usuarioUsuario,
                            "contrasena"=> $this->contrasena1Usuario
                        ],
                    ]
                    "VANRA"=> //para validar que se esté introduciendo un valor distinto en la act
                        [
                            "atributo"=>"valor_moneda",
                            "valor"=> $this->valorMoneda
                        ],
                    ]
                ]
            ];
        */
        foreach ($campos as &$campo) {

            //Para evitar la inyección de SQL
            if (isset($campo['campo_valor'])) {
                $campo['campo_valor'] = $this->limpiarCadena($campo['campo_valor']);
            }

            //Para validar campos requeridos
            if (isset($campo['requerido'])) {
                if (!isset($campo['campo_valor']) || $campo['campo_valor'] == "") {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Campo de " . $campo['formulario_nombre'] . " obligatorio",
                        "texto" => 'No puedes enviar el formulario sin llenar el campo de ' . $campo['formulario_nombre'] . ', por favor verifique e intente de nuevo ',
                        "icono" => "error",
                    ];
                    return ($alerta);
                    exit();
                }
            }

            //Para validar el largo y minimo
            if (isset($campo['maximo'])) {
                if ($campo['campo_valor'] != "") {
                    if (mb_strlen($campo['campo_valor']) > $campo['maximo']) {
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Campo de " . $campo['formulario_nombre'] . " muy largo",
                            "texto" => "El campo de " . $campo['formulario_nombre'] . " no puede tener más de " . $campo['maximo'] . " carácteres de longitud: " . $campo['campo_valor'],
                            "icono" => "error",
                        ];
                        return ($alerta);
                        exit();
                    } elseif (mb_strlen($campo['campo_valor']) < $campo['minimo']) {
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Campo de " . $campo['formulario_nombre'] . " muy corto",
                            "texto" => "El campo de" . $campo['formulario_nombre'] . " no puede tener menos de " . $campo['minimo'] . " carácteres de longitud: " . $campo['campo_valor'],
                            "icono" => "error",
                        ];
                        return ($alerta);
                        exit();
                    }
                }
            }

            //Para validar el formato del campo con expresiones regulares
            if (isset($campo['expresion_re'])) {
                if ($campo['campo_valor'] != "") {
                    if (!preg_match("/" . $campo['expresion_re'] . "/", $campo['campo_valor'])) {
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Formato de " . $campo['formulario_nombre'] . " inválido",
                            "texto" => "El formato del campo " . $campo['formulario_nombre'] . " no es correcto, por favor verifique e intente de nuevo.",
                            "icono" => "error",
                        ];
                        return ($alerta);
                        exit();
                    }
                }
            }

            //Para verificar la existencia de un registro para su actualización [normalmente solo el ID del registro]
            if (isset($campo['debeExistir'])) {
                $instruccionesBD = [
                    'campos' => '*',
                    'tabla' =>  $campo['tabla'],
                    'WHERE' => [
                        [
                            'condicion_campo' => $campo['campo_nombre'],
                            'condicion_marcador' => ':Id',
                            'condicion_valor' => $campo['campo_valor'],
                            'comparacion' => '=',
                        ]
                    ]
                ];
                $registrosExistentes = $this->seleccionarDatos($instruccionesBD);

                if ($registrosExistentes->rowCount() == 0 && isset($campo['requerido'])) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Dato no encontrado",
                        "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " no se encuentra registrado dentro de la base de datos del sistema, por favor verifique e intente de nuevo: " . $campo['campo_valor'],
                        "icono" => "error",
                    ];
                    return ($alerta);
                    exit();
                } else {
                    $registrosExis = $registrosExistentes->fetch(PDO::FETCH_ASSOC);/*hacemos el arrays */
                }
            }

            //Para verificar que no haya mas registros con ese valor
            if (isset($campo['debeSerUnico'])) {
                $pedirDatosDeNuevo = true;
                if (isset($registrosExis)) {
                    if (isset($registrosExis[$campo['campo_nombre']])) {
                        $pedirDatosDeNuevo = false;
                    }
                }
                
                if ($pedirDatosDeNuevo) {
                    $instruccionesBD = [
                        'campos' => '*',
                        'tabla' =>  $campo['tabla'],
                        'WHERE' => [
                            [
                                'condicion_campo' => $campo['campo_nombre'],
                                'condicion_marcador' => ':Id',
                                'condicion_valor' => $campo['campo_valor'],
                                'comparacion' => '=',
                            ]
                        ]
                    ];
                    $resultado = $this->seleccionarDatos($instruccionesBD);
                    if ($resultado->rowCount() > 0) {
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Valor de " . $campo['formulario_nombre'] . " duplicado",
                            "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " ya se encuentra registrado y no se puede duplicar, por favor verifique e intente de nuevo",
                            "icono" => "error",
                        ];
                        return ($alerta);
                        exit();
                    } else {
                        $registrosExis = false;
                    }
                }

                if ($registrosExis != false) {
                    if ($registrosExis[$campo['campo_nombre']] != $campo['campo_valor']) {
                        $instruccionesBD = [
                            'campos' => $campo['campo_nombre'],
                            'tabla' =>  $campo['tabla'],
                            'WHERE' => [
                                [
                                    'condicion_campo' => $campo['campo_nombre'],
                                    'condicion_marcador' => ':Id',
                                    'condicion_valor' => $campo['campo_valor'],
                                    'comparacion' => '=',
                                ]
                            ]
                        ];
                        $checkRegistro = $this->seleccionarDatos($instruccionesBD);
                        if ($checkRegistro->rowCount() > 0) {
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Valor de " . $campo['formulario_nombre'] . " duplicado",
                                "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " ya se encuentra registrado y no se puede duplicar, por favor verifique e intente de nuevo",
                                "icono" => "error",
                            ];
                            return ($alerta);
                            exit();
                        }
                    }
                }
            }

            //Para validar si dos campos son iguales
            if (isset($campo['camposIguales'])) {
                if ($campo['campo_valor'] != $campo['camposIguales']) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Desigualdad de valores",
                        "texto" => "El valor de ambos campos de " . $campo['formulario_nombre'] . " deben ser iguales, verifique e intente nuevamente",
                        "icono" => "error",
                    ];
                    return ($alerta);
                    exit();
                }
            }

            //para evitar que un dato específico sea eliminado o alguna otra operación
            if (isset($campo['camposDiferentes'])) {
                if ($campo['campo_valor'] == $campo['camposDiferentes']) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "ERROR",
                        "texto" => "El valor de " . $campo['formulario_nombre'] . " no puede ser usado en esa transacción",
                        "icono" => "error",
                    ];
                    return ($alerta);
                    exit();
                }
            }

            //para validar el usuario y contraseña del login
            if (isset($campo['validarLogin'])) {
                $instruccionesBD = [
                    'campos' => "
                        us.cedula_usuario, us.nombre_usuario, us.apellido_usuario,
                        ro.id_rol, ro.nombre_rol, us.usuario_usuario, us.contrasena_usuario
                    ",
                    'tabla' =>  "usuarios as us",
                    'PEL'=>'us',
                    'datosJoins'=>[
                        [
                            'TablaDestino'=>'roles as ro',
                            'conexionLo'=>'us.id_rol = ro.id_rol'
                        ]
                    ],
                    'WHERE' => [
                        [
                            'condicion_campo' => "us.usuario_usuario",
                            'condicion_marcador' => ':usuario_usuario',
                            'condicion_valor' => $campo['validarLogin']['usuario'],
                            'comparacion' => '=',
                        ]
                    ]
                ];
                $check_usuario = $this->seleccionarDatos($instruccionesBD);

                if ($check_usuario->rowCount() == 1) {

                    $check_usuario = $check_usuario->fetch(); /*Para hacer un arrays con los datos que coincidieron en la BD */

                    if (
                        ($campo['validarLogin']['usuario'] != $check_usuario['usuario_usuario']) ||
                        (!password_verify($campo['validarLogin']['contrasena'], $check_usuario['contrasena_usuario']))
                    ) {
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Contraseña incorrecta",
                            "texto" => "La contraseña que ha introducido es incorrecta, por favor verifique e intente nuevamente",
                            "icono" => "error",
                        ];
                        return ($alerta);
                        exit();
                    } else {
                        /*Creamos las variables de sesión */
                        $_SESSION['cedula'] = $check_usuario['cedula_usuario'];
                        $_SESSION['nombre'] = $check_usuario['nombre_usuario'];
                        $_SESSION['apellido'] = $check_usuario['apellido_usuario'];
                        $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                        $_SESSION['rol'] = $check_usuario['id_rol'];
                        $_SESSION['nombreRol'] = $check_usuario['nombre_rol'];
                    }
                }
            }

            //Valor de Atributo No Repetible del registro a Actualizar
            if (isset($campo['VANRA'])) {

                $instruccionesBD = [
                    'campos' => "*",
                    'tabla' =>  $campo['tabla'],
                    'WHERE' => [
                        [
                            'condicion_campo' => $campo['campo_nombre'],
                            'condicion_marcador' => ':ID',
                            'condicion_valor' => $campo['campo_valor'],
                            'comparacion' => '=',
                        ]
                    ]
                ];
                $checkRegistro = $this->seleccionarDatos($instruccionesBD);
                //Para verificar que el valor no es igual al que ya posee
                $valorCheck = $checkRegistro->fetch(PDO::FETCH_ASSOC);
                if ($valorCheck[$campo['VANRA']['atributo']] == $campo['VANRA']['valor']) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Valor ya registrado",
                        "texto" => "El valor de " . $campo['formulario_nombre'] . " introducido es igual al actual, por favor verifique e intente de nuevo",
                        "icono" => "error",
                    ];
                    return ($alerta);
                    exit();
                }
            }
        }
        return false;
    }
    public function FechaHora_Sel($tipo, $fecha = null, $tiempo = null)
    {
        if ($tipo === 'Fecha_hora_foto') {
            $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas')); // Especifica la zona horaria de Venezuela
            $this->fecha = $this->fecha->format('Y-m-d H:i:s');
            $this->fecha = str_replace(' ', '_', $this->fecha);
            $this->fecha = str_replace(':', '_', $this->fecha);
        } elseif ($tipo === 'fecha_hora_BD') {

            $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
            $this->fecha = $this->fecha->format('Y-m-d H:i:s');
        } elseif ($tipo == 'fecha_BD') {
            $this->fecha = str_replace('-', '/', $this->fecha);
            $this->fecha = str_replace(':', '/', $this->fecha);
            $this->fecha = DateTime::createFromFormat('d-m-Y', $fecha);
            $this->fecha = $this->fecha->format('Y-m-d');
        } elseif ($tipo == 'Fecha_Actual_BD') {
            $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
            $this->fecha = $this->fecha->format('Y-m-d');
        } elseif ($tipo == 'Fecha_Hora_Actual') {
            $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
            $this->fecha = $this->fecha->format('d-m-Y h:i A');
        } elseif ($tipo == 'fecha_hora_AM_PM') {
            $timestamp = strtotime($fecha);
            $this->fecha = date('d-m-Y h:i A', $timestamp);
        } elseif ($tipo == 'tiempo_antes_BD') {
            $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
            $intervalo = new DateInterval('P' . $tiempo . 'D');
            $this->fecha = $this->fecha->sub($intervalo);
            $this->fecha = $this->fecha->format('Y-m-d');
        }elseif($tipo=='Fecha_Normal'){
            $this->fecha = DateTime::createFromFormat('Y-m-d', $fecha);
            $this->fecha = $this->fecha->format('d-m-Y');
        }
        return $this->fecha;
    }
    public function __destruct()
    {
        $this->conexion = null;
    }

    //Métodos protegidos para el encapsulamiento
    protected function conectar()
    {
        return $this->conectarP();
    }
    protected function commit()
    {
        return $this->commitP();
    }
    protected function rollback()
    {
        return $this->rollbackP();
    }
    protected function seleccionarDatos(array $datos)
    {
        return $this->seleccionarDatosP($datos);
    }
    protected function guardarDatos($tabla, $datos, $condicion = null)
    {

        foreach($datos as &$dato){
            //Pasar a mayúsculas
            if(isset($dato['ponerEnMayusculas'])){
                $dato['campo_valor']= mb_strtoupper($dato['campo_valor']);
            }

            //Pasar la coma a punto
            if(isset($dato['comaPunto'])){
                $dato['campo_valor']= str_ireplace(',','.',$dato['campo_valor']);
            }
        }

        return $this->guardarDatosP($tabla, $datos, $condicion);
    }
    protected function actualizarDatos($instrucciones)
    {

        foreach($instrucciones['datos'] as &$dato){
            //Pasar a mayúsculas
            if(isset($dato['ponerEnMayusculas'])){
                $dato['campo_valor']= mb_strtoupper($dato['campo_valor']);
            }

            //Pasar la coma a punto
            if(isset($dato['comaPunto'])){
                $dato['campo_valor']= str_ireplace(',','.',$dato['campo_valor']);
            }
        }

        return $this->actualizarDatosP($instrucciones);
    }
    protected function eliminarDatos($tabla, $campo, $id, $permanente = null)
    {
        return $this->eliminarDatosP($tabla, $campo, $id, $permanente);
    }

    /*Métodos privados para el manejo de datos con la BD*/
    protected function conectarP()
    {
        if ($this->conexion instanceof PDO && $this->conexion->inTransaction()) {
            return;
        }
        if ($this->conexion == null) {
            try {
                $this->conexion = new PDO(
                    "mysql:host=" . $this->servidorDB . ";dbname=" . $this->nombreDB,
                    $this->userDB,
                    $this->passwordDB,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        //PDO::ATTR_EMULATE_PREPARES => false, // Mejor seguridad y rendimiento
                    ]
                );
                $this->conexion->exec("SET CHARACTER SET utf8");
            } catch (PDOException $e) {
                // Para obtener el error
                throw new PDOException("Error al establecer la conexión con la base de datos: " . $e->getMessage(), " Código de error: " . $e->getCode());
            }
        }
        if (!$this->conexion->inTransaction()) {
            try {
                $this->conexion->beginTransaction();
            } catch (PDOException $e) {
                throw new PDOException("Error al iniciar la transacción: " . $e->getMessage(), " Este es el código: " . $e->getCode());
            }
        }
        return $this->conexion;
    }
    protected function commitP()
    {
        //verificamos que la conexión esté abierta y esté en una transacción
        if ($this->conexion instanceof PDO && $this->conexion->inTransaction()) {
            try {
                $this->conexion->commit();
            } catch (PDOException $e) {
                throw new PDOException("Error al confirmar la transacción (COMMIT): " . $e->getMessage(), (int)$e->getCode());
            }
        }
    }
    protected function rollbackP()
    {
        //aqui verificamos igual que en el commit
        if ($this->conexion instanceof PDO && $this->conexion->inTransaction()) {
            try {
                $this->conexion->rollBack();
            } catch (PDOException $e) {
                throw new PDOException("Error al revertir la transacción (ROLLBACK): " . $e->getMessage(), (int)$e->getCode());
            }
        }
    }
    private function seleccionarDatosP(array $datos)
    {
        $this->conectar();
        //LOS CAMPOS Y LA TABLA
        $consulta = 'SELECT ' . $datos['campos'] . ' FROM ' . $datos['tabla'] . ' ';

        //INNER JOINS
        if (isset($datos['datosJoins'])) {
            foreach ($datos['datosJoins'] as $join) {
                if (is_array($join) && isset($join["TablaDestino"]) && isset($join["conexionLo"])) {
                    if (isset($join['tipoJoin'])) {
                        $consulta .=
                            " " . $join['tipoJoin'] . " JOIN " . $join["TablaDestino"] .
                            " ON " . $join["conexionLo"];
                    } else {
                        $consulta .=
                            " INNER JOIN " . $join["TablaDestino"] .
                            " ON " . $join["conexionLo"];
                    }
                }
            }
        }
        //CONDICIÓN DE ELIMINADO LÓGICO
        if (isset($datos['registrosEli'])) {
            $consulta .= ' WHERE ';
            //Prefijo de Eliminado Lógico
            if (isset($datos['PEL'])) {
                $consulta .= '' . $datos['PEL'] . '.';
            }
            $consulta .= 'status = 0 ';
        } elseif (isset($datos['eliminadosYVigentes'])) {
            $consulta .= ' WHERE ';
            if (isset($datos['PEL'])) {
                $consulta .= '' . $datos['PEL'] . '.';
            }
            $consulta .= 'status > -1 ';
        } else {
            $consulta .= ' WHERE ';
            if (isset($datos['PEL'])) {
                $consulta .= '' . $datos['PEL'] . '.';
            }
            $consulta .= 'status != 0 ';
        }

        //CONDICIONES EXTRAS
        if (isset($datos['WHERE'])) {
            foreach ($datos['WHERE'] as $condicion) {
                $consulta .= 'AND ' . $condicion['condicion_campo'] . ' ';
                $consulta .= $condicion['comparacion'] . ' ';
                $consulta .= $condicion['condicion_marcador'] . ' ';
            }
        }
        //GROUP BY
        if (isset($datos['GROUP']) && !empty($datos['GROUP'])) {
            $consulta .= " GROUP BY " . $datos['GROUP'];
        }
        // HAVING
        if (isset($datos['HAVING'])) {
            $consulta .= ' HAVING ';
            $ch = 0;
            foreach ($datos['HAVING'] as $condicion) {
                if ($ch > 0) {
                    $consulta .= 'AND ';
                }
                $consulta .= $condicion['condicion_campo'] . ' ';
                $consulta .= $condicion['comparacion'] . ' ';
                $consulta .= $condicion['condicion_marcador'] . ' ';
                $ch++;
            }
        }
        //ORDER BY
        if (isset($datos['ORDER']) && !empty($datos['ORDER'])) {
            $consulta .= " ORDER BY " . $datos['ORDER'];
        }
        //LIMIT
        if (isset($datos['LIMIT']) && !empty($datos['LIMIT'])) {
            $consulta .= " LIMIT " . $datos['LIMIT'];
        }

        //PREPARACIÓN (ANTI-SQL-INYECTION)
        $consulta = $this->conexion->prepare($consulta);

        //HACEMOS EL BIND DE MARCADORES POR VALORES

        //return $datos['WHERE'];
        if (isset($datos['WHERE'])) {
            foreach ($datos['WHERE'] as $condicion) {
                $consulta->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
            }
        }
        if (isset($datos['HAVING'])) {
            foreach ($datos['HAVING'] as $condicion) {
                $consulta->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
            }
        }
        // return $consulta;
        $consulta->execute();
        return $consulta;
    }
    private function guardarDatosP($tabla, $datos, $condicion)
    {
        $this->conectar();
        if ($condicion != null) {
            /*Para verificar si el id, cedula, placa o cualquier código que se esté intentando ingresar ya se encuentra en la BD */

            $instruccionesBD = [
                'campos' => '*',
                'registrosEli' => true,
                'tabla' => $tabla,
                'WHERE' => [
                    [
                        'condicion_campo' => $condicion["condicion_campo"],
                        'condicion_marcador' => ':Id',
                        'condicion_valor' => $condicion["condicion_valor"],
                        'comparacion' => '=',
                    ]
                ]
            ];

            $registroExistente = $this->seleccionarDatos($instruccionesBD);
            if ($registroExistente->rowCount() > 0) {
                //comenzamos la consulta SQL
                $query = "UPDATE $tabla SET ";

                //recorremos el arrays con los campos de la misma
                $C = 0;
                foreach ($datos as $clave) {
                    
                    if ($C >= 1) {
                        $query .= ",";
                    }
                    $query .= $clave["campo_nombre"] . "=" .  $clave["campo_marcador"];
                    $C++;
                }

                $query.= ", status = 1";
                $query .= " WHERE " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];

                //la preparamos para evitar la inyeccion de sql
                $sql = $this->conexion->prepare($query);

                //recorremos el array con la condicion de la misma
                foreach ($datos as $clave) {
                    $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
                    $C++;
                }

                $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
                $sql->execute(); //ejecutamos la consulta

                return $sql->rowCount();
            } else {
                $query = "INSERT INTO $tabla (";

                $C = 0;
                foreach ($datos as $clave) {
                    if ($C >= 1) {
                        $query .= ", ";
                    }
                    $query .= $clave["campo_nombre"];
                    $C++;
                }

                $query .= ") VALUES (";

                $C = 0;
                foreach ($datos as $clave) {
                    if ($C >= 1) {
                        $query .= ", ";
                    }
                    $query .= $clave["campo_marcador"];
                    $C++;
                }

                $query .= " ) ";
                $sql = $this->conexion->prepare($query);

                foreach ($datos as $clave) {
                    $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
                }

                $sql->execute();

                //Porque el ID puede o no ser autoincremental
                if ($this->conexion->lastInsertId() > 0) {
                    return $this->conexion->lastInsertId();
                } else {
                    return $sql->rowCount();
                }
            }
        } else {
            $query = "INSERT INTO $tabla (";

            $C = 0;
            foreach ($datos as $clave) {
                if ($C >= 1) {
                    $query .= ", ";
                }
                $query .= $clave["campo_nombre"];
                $C++;
            }

            $query .= ") VALUES (";

            $C = 0;
            foreach ($datos as $clave) {
                if ($C >= 1) {
                    $query .= ", ";
                }
                $query .= $clave["campo_marcador"];
                $C++;
            }

            $query .= ")";

            /*conectar() retorna la conexión que preparamos con prepare para la consulta de inserción en la variable $query */
            $sql = $this->conexion->prepare($query);


            foreach ($datos as $clave) {
                $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
            }


            $sql->execute();
            return $this->conexion->lastInsertId();
        }
    }
    private function actualizarDatosP($instrucciones)
    {
        $this->conectar();
        //comenzamos la consulta SQL
        $query = "UPDATE " . $instrucciones['tabla'] . " SET ";
        //recorremos el arrays con los campos de la misma
        $C = 0;
        //return $instrucciones['datos'];

        foreach ($instrucciones['datos'] as $clave) {
            if ($C >= 1) {
                $query .= ", ";
            }
            $query .= $clave["campo_nombre"] . " = " .  $clave["campo_marcador"];
            $C++;
        }
        $query .= " WHERE ";

        $co = 0;
        $numeroCondi = count($instrucciones['condiciones']);
        foreach ($instrucciones['condiciones'] as $condicion) {

            $query .= $condicion["condicion_campo"] . ' ';
            $query .= $condicion['comparacion'] . ' ';
            $query .= $condicion["condicion_marcador"] . ' ';

            if ($numeroCondi > 1 && $co == 0) {
                $query .= " AND (";
            }

            $co++;

            if ($co > 1 && $numeroCondi > 2 && $numeroCondi > $co) {
                $query .= " OR ";
            }
        }
        if ($numeroCondi > 1) {
            $query .= " )";
        }

        //return $query;

        //la preparamos para evitar la inyeccion de sql
        $sql = $this->conexion->prepare($query);

        //recorremos el array con la condicion de la misma
        foreach ($instrucciones['datos'] as $dato) {
            $sql->bindParam($dato["campo_marcador"], $dato["campo_valor"]);
        }

        foreach ($instrucciones['condiciones'] as $condicion) {
            $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
        }

        $sql->execute(); //ejecutamos la consulta

        return $sql->rowCount();
    }
    private function eliminarDatosP($tabla, $campo, $id, $permanente)
    {
        $this->conectar();
        if ($permanente == true) {
            $sql = $this->conexion->prepare("DELETE FROM $tabla WHERE $campo = :id");
        } else {
            $sql = $this->conexion->prepare("UPDATE $tabla SET status = 0 WHERE $campo = :id");
        }
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }
}

?>
