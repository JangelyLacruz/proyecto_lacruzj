<?php

namespace src\modelos;

use src\config\connect\conexion;

class clientesModelo extends conexion {

  private string $rifCedulaCliente = '';
  private string $razonSocialCliente = '';
  private string $telefonoCliente = '';
  private string $correoCliente = '';
  private string $direccionCliente = '';
  private string $numeroControlFactura = '';

  public function validarClientes(string|null $permiso, array &$info, $requerido = []) {
    if ($permiso) {
      $objAcceso = new accesosModelo();
      $v = $objAcceso->validarPermisos('clientes', $permiso);
      if ($v) return $v;
    }

    if (isset($info['numero_control_factura']) && is_string($info['numero_control_factura'])) {
      $info['numero_control_factura'] = mb_strtoupper(mb_substr(trim($info['numero_control_factura']), 0, 10));
    }

    $esquemaClientes = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'rif_cedula_cliente' => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "rif o cédula del cliente",
          "nombreBD" => "rif_cedula_cliente",
          "tablaBD" => "clientes",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
        ],
        'telefono_cliente' => [
          ...molTelefono,
          "nombreAlerta" => "teléfono del cliente",
          "nombreBD" => "telefono_cliente",
          "tablaBD" => "clientes",
          "requerido" => true,
          "debeSerUnicoBD" => true,
        ],
        'razon_social_cliente' => [
          ...molDescripcion,
          "nombreAlerta" => "razón social del cliente",
          "nombreBD" => "razon_social_cliente",
          "tablaBD" => "clientes",
        ],
        'numero_control_factura' => [
          ...molNumControlFactura,
          "nombreAlerta" => "número de control de factura",
          "nombreBD" => "numero_control_factura",
          "tablaBD" => 'control_ordenes_entregas_presupuestos',
],
        'correo_cliente' => [
          ...molCorreo,
          "nombreAlerta" => "correo electrónico del cliente",
          "nombreBD" => "correo_cliente",
          "tablaBD" => 'clientes',
          "debeSerUnicoBD" => true,
        ],
        'direccion_cliente' => [
          ...molDescripcion,
          "nombreAlerta" => "dirección del cliente",
          "nombreBD" => "direccion_cliente",
          "tablaBD" => 'clientes',
        ],
      ],
      'requerido' => $requerido
    ];
    // if (isset($info['cedula_exis'])) $esquemaClientes['propiedades']['rif_cedula_cliente']['debeExistirBD'] = true;
    if (isset($info['prefijo_telefono_cliente'])) {
      $info['telefono_cliente'] = $info['prefijo_telefono_cliente'] . $info['telefono_cliente'];
    }
    if (isset($info['codigo_rif_cedula_cliente'])) {
      $info['rif_cedula_cliente'] = $info['codigo_rif_cedula_cliente'] . $info['rif_cedula_cliente'];
    }
    if (isset($info['esR'])) {
      unset($esquemaClientes['propiedades']['rif_cedula_cliente']['debeExistirBD']);
      unset($info['esR']);
    }
    $v = $this->limpiarValidar($info, $esquemaClientes);
    if ($v) return $v;

    return false;
  }
  public function seleccionarClientes(array $info) {
    if (isset($info['vieneDelModuloUsuarios'])) {
      $v = $this->validarClientes(null, $info);
    } elseif (($info['rif_cedula_cliente'] ?? '') != '') {
      $info['cedula_exis'] = true;
      $v = $this->validarClientes('ver detalles de los clientes', $info);
    } else {
      $v = $this->validarClientes('listar', $info);
    }

    if ($v) return $v;
    $this->rifCedulaCliente = $info['rif_cedula_cliente'] ?? '';

    return $this->seleccionarClientesP();
  }
  public function registrarClientes(array $info) {
    $info['esR'] = true;
    $permiso = isset($info['vieneDelModuloUsuarios']) ? '' : 'registrar';
    $this->numeroControlFactura = $info['numero_control_factura'] ?? '';

    $camposAValidar = [
      'rif_cedula_cliente',
      'razon_social_cliente',
      'direccion_cliente'
    ];

    if (trim($this->numeroControlFactura) !== '') {
      $camposAValidar[] = 'numero_control_factura';
    }
    $v = $this->validarClientes($permiso, $info, $camposAValidar); 

    if ($v !== false) return $v;

    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    $this->razonSocialCliente = $info['razon_social_cliente'];
    $this->telefonoCliente = $info['telefono_cliente'];
    $this->correoCliente = $info['correo_cliente'];
    $this->direccionCliente = $info['direccion_cliente'];

    return $this->registrarClientesP($info);
  }
  public function actualizarClientes(array $info) {
    $this->numeroControlFactura = $info['numero_control_factura'] ?? '';
    $camposAValidar = [
      'rif_cedula_cliente',
      'razon_social_cliente',
      'direccion_cliente'
    ];

    if (trim($this->numeroControlFactura) !== '') {
      $camposAValidar[] = 'numero_control_factura';
    }

    $v = $this->validarClientes('actualizar', $info, $camposAValidar); 
    if ($v) return $v;

    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    $this->razonSocialCliente = $info['razon_social_cliente'];
    $this->telefonoCliente = $info['telefono_cliente'];
    $this->correoCliente = $info['correo_cliente'];
    $this->direccionCliente = $info['direccion_cliente'];

    return $this->actualizarClientesP($info);
  }
  public function eliminarClientes(array $info) {
    $v = $this->validarClientes('eliminar', $info, [
      'rif_cedula_cliente',
    ]);
    if ($v) return $v;
    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    return $this->eliminarClientesP($info);
  }

  private function seleccionarClientesP() {
    if ($this->rifCedulaCliente == null || $this->rifCedulaCliente == "") {
      return $this->seleccionarDatos2([
        'campos' => '
         c.rif_cedula_cliente, 
         c.razon_social_cliente, 
         c.telefono_cliente, 
         c.correo_cliente, 
         c.direccion_cliente, 
        ctrl.numero_control_factura
        ',
        'tabla' => 'clientes AS c 
            LEFT JOIN control_ordenes_entregas_presupuestos AS ctrl 
            ON c.rif_cedula_cliente = ctrl.rif_cliente',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
          'campos' => '
            clientes.rif_cedula_cliente, 
            clientes.razon_social_cliente, 
            clientes.telefono_cliente, 
            clientes.correo_cliente, 
            clientes.direccion_cliente, 
            control_ordenes_entregas_presupuestos.numero_control_factura
          ',
          'tabla' => 'clientes INNER JOIN control_ordenes_entregas_presupuestos ON clientes.rif_cedula_cliente = control_ordenes_entregas_presupuestos.rif_cliente',

      /*  'campos' => '
          rif_cedula_cliente, razon_social_cliente,
          telefono_cliente, correo_cliente, direccion_cliente, numero_control_factura
        ',
        'tabla' => 'v_clientes_todos',    */

        'WHERE' => [
          "rif_cedula_cliente" => $this->rifCedulaCliente,
        ]
      ])->fetch();
    }
  }

  private function registrarClientesP(array $info) {

    $ultimoID = $this->guardarDatos2([
      "tabla" => 'clientes',
      'datos' => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
        "razon_social_cliente" => $this->razonSocialCliente,
        "telefono_cliente" => $this->telefonoCliente,
        "correo_cliente" => $this->correoCliente,
        "direccion_cliente" => $this->direccionCliente
      ],
      'WHERE' => [
        'rif_cedula_cliente' => $this->rifCedulaCliente
      ]
    ]);

    if ($ultimoID === false || $ultimoID <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Cliente no registrado",
        "texto" => "El cliente no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }

    if (!empty($this->numeroControlFactura)) {
      $this->guardarDatos2([
        "tabla" => 'control_ordenes_entregas_presupuestos',
        'datos' => [
          "rif_cliente" => $this->rifCedulaCliente,
          "numero_control_factura" => $this->numeroControlFactura
        ],
        'WHERE' => [
          'rif_cliente' => $this->rifCedulaCliente
        ]
      ]);
    }

    if (!isset($info['sinCommit'])) $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Cliente registrado",
      "texto" => "El cliente ha sido registrado exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarClientesP(array $info) {
    $resultado = $this->actualizarDatos2([
      "tabla" => "clientes",
      "datos" => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
        "razon_social_cliente" => $this->razonSocialCliente,
        "telefono_cliente" => $this->telefonoCliente,
        "correo_cliente" => $this->correoCliente,
        "direccion_cliente" => $this->direccionCliente
      ],
      "WHERE" => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
      ]
    ]);

    $resultadoControl = false;
    if (!empty($this->numeroControlFactura)) {
      // Verificar si ya existe un registro para este RIF en la tabla de control
      $existeControl = $this->seleccionarDatos2([
        'campos' => 'rif_cliente',
        'tabla' => 'control_ordenes_entregas_presupuestos',
        'WHERE' => [
          "rif_cliente" => $this->rifCedulaCliente,
        ]
      ])->fetch();

      if ($existeControl) {
        // Si existe, actualizar
        $resControl = $this->actualizarDatos2([
          "tabla" => "control_ordenes_entregas_presupuestos",
          "datos" => [
            "numero_control_factura" => $this->numeroControlFactura
          ],
          "WHERE" => [
            "rif_cliente" => $this->rifCedulaCliente
          ]
        ]);
        if ($resControl !== false && $resControl > 0) $resultadoControl = true;
      } else {
        // Si no existe previo, registrarlo mediante guardarDatos2
        $resControl = $this->guardarDatos2([
          "tabla" => "control_ordenes_entregas_presupuestos",
          "datos" => [
            "rif_cliente" => $this->rifCedulaCliente,
            "numero_control_factura" => $this->numeroControlFactura
          ],
          "WHERE" => [
            "rif_cliente" => $this->rifCedulaCliente
          ]
        ]);
        if ($resControl !== false && $resControl > 0) $resultadoControl = true;
      }
    }

    if (($resultado == false || $resultado <= 0) && !$resultadoControl) {
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se han realizado cambios en el registro",
        "icono" => "warning",
      ];
    }
    if (!isset($info['sinCommit'])) $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Cliente actualizado",
      "texto" => "El cliente ha sido actualizado exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarClientesP(array $info) {
    $eliminarCliente = $this->eliminarDatos2([
      'tabla' => 'clientes',
      'WHERE' => [
        'rif_cedula_cliente' => $this->rifCedulaCliente
      ]
    ]);

    if ($eliminarCliente <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Cliente no encontrado",
        "texto" => "El cliente no existe en la base de datos",
        "icono" => "error",
      ];
    }

    if (!isset($info['sinCommit'])) $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Cliente eliminado",
      "texto" => "El cliente ha sido eliminado exitosamente",
      "icono" => "success",
    ];
  }
}
