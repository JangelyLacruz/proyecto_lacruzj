-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-12-2025 a las 01:42:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_lacruz`
--
CREATE DATABASE IF NOT EXISTS `proyecto_lacruz` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `proyecto_lacruz`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `nombre_accion` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `fecha_bitacora` datetime NOT NULL,
  `resultado_accion_bitacora` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_iva`
--

CREATE TABLE `cambios_iva` (
  `id_cambio_iva` int(11) NOT NULL,
  `monto_cambio_iva` float NOT NULL,
  `fecha_cambio_iva` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_iva`
--

INSERT INTO `cambios_iva` (`id_cambio_iva`, `monto_cambio_iva`, `fecha_cambio_iva`, `status`) VALUES
(1, 12, '2025-12-05 18:15:15', 1),
(2, 13, '2025-12-05 18:17:41', 1),
(3, 13, '2025-12-05 18:17:55', 1),
(4, 14, '2025-12-05 18:18:02', 1),
(5, 12, '2025-12-06 15:56:00', 1),
(6, 12.01, '2025-12-06 15:56:12', 1),
(7, 12, '2025-12-06 15:56:22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_monedas`
--

CREATE TABLE `cambios_monedas` (
  `id_cambio_moneda` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `valor_moneda` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_monedas`
--

INSERT INTO `cambios_monedas` (`id_cambio_moneda`, `id_moneda`, `valor_moneda`, `fecha_cambio`, `status`) VALUES
(1, 1, 17, '2025-12-06 20:12:19', 1),
(2, 5, 2, '2025-12-06 20:13:37', 1),
(3, 2, 3, '2025-12-06 20:13:48', 1),
(4, 1, 257.93, '2025-12-06 20:14:15', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `razon_social_cliente` varchar(50) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(150) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pagos`
--

CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `monto_pago` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id_insumo` int(11) NOT NULL,
  `nombre_insumo` varchar(50) NOT NULL,
  `costo_insumo` float NOT NULL,
  `stock_insumo` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos_compras`
--

CREATE TABLE `insumos_compras` (
  `id_insumo_compra` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad_insumo` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos_ventas`
--

CREATE TABLE `insumos_ventas` (
  `id_insumo_venta` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad_insumo` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas`
--

CREATE TABLE `materias_primas` (
  `id_materia_prima` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` int(15) NOT NULL,
  `costo_materia_prima` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_compras`
--

CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `cantidad_materia_prima` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_presentaciones`
--

CREATE TABLE `materias_primas_presentaciones` (
  `id_materia_prima_presentacion` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_productos`
--

CREATE TABLE `materias_primas_productos` (
  `id_materia_prima_producto` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_materia_prima` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pagos`
--

CREATE TABLE `metodos_pagos` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo_pago` varchar(50) NOT NULL,
  `necesita_moneda` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pagos`
--

INSERT INTO `metodos_pagos` (`id_metodo_pago`, `nombre_metodo_pago`, `necesita_moneda`, `status`) VALUES
(1, 'EFECTIVO', 1, 1),
(2, 'TRANSFERENCIA', 0, 1),
(3, 'PAGO MÓVIL', 0, 1),
(4, 'ZELLE', 1, 0),
(5, 'BINANCE', 1, 0),
(6, 'BINANCE', 1, 1),
(7, 'ZELLE', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nombre_modulo` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nombre_modulo`, `status`) VALUES
(1, 'clientes', 1),
(2, 'presupuesto', 1),
(3, 'ventas', 1),
(4, 'productos', 1),
(5, 'servicios', 1),
(6, 'materias_primas', 1),
(7, 'proveedores', 1),
(8, 'compras', 1),
(9, 'usuarios', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(11) NOT NULL,
  `nombre_moneda` varchar(20) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `valor_moneda` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre_moneda`, `simbolo_moneda`, `valor_moneda`, `status`) VALUES
(1, 'DÓLAR', '$', 257.93, 1),
(2, 'EURO', '€', 3, 1),
(3, 'YUAN', '¥', 12, 1),
(4, 'AGUACATE', '$O', 1, 0),
(5, 'BÓLIVAR', 'BS', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `titulo_notificacion` varchar(30) NOT NULL,
  `tipo_notificacion` varchar(30) NOT NULL,
  `tiempo_notificacion` int(10) NOT NULL,
  `icono_notificacion` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_usuarios`
--

CREATE TABLE `notificaciones_usuarios` (
  `id_notificacion_usuario` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `fecha_creacion_notificacion` datetime NOT NULL,
  `mensaje_notificacion` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `status`) VALUES
(1, 'ver', 1),
(2, 'listar', 1),
(3, 'registrar', 1),
(4, 'actualizar', 1),
(5, 'eliminar', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id_presentacion` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `costo_producto_detal` float NOT NULL,
  `costo_producto_mayor` float NOT NULL,
  `stock_producto` int(15) NOT NULL,
  `producto_es_fabricado` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_producto` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_presentaciones`
--

CREATE TABLE `productos_presentaciones` (
  `id_producto_presentacion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_servicios_ventas`
--

CREATE TABLE `productos_servicios_ventas` (
  `id_producto_servicio_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_servicio_venta` int(11) NOT NULL,
  `cantidad_producto` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_ventas`
--

CREATE TABLE `productos_ventas` (
  `id_producto_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `cantidad_producto` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provedores`
--

CREATE TABLE `provedores` (
  `rif_proveedor` varchar(20) NOT NULL,
  `razon_social_proveedor` varchar(150) NOT NULL,
  `telefono_proveedor` varchar(11) NOT NULL,
  `correo_proveedor` varchar(150) NOT NULL,
  `direccion_proveedor` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `status`) VALUES
(1, 'ADMINISTRADOR', 1),
(2, 'OFICINISTA', 1),
(5, 'CAJERO', 0),
(6, 'CAJEROTA', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `costo_servicio` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_ventas`
--

CREATE TABLE `servicios_ventas` (
  `id_servicios_ventas` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `cantidad_servicio` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_unidad_medida` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `usuario_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `telefono_usuario` varchar(11) NOT NULL,
  `correo_usuario` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `id_rol`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `contrasena_usuario`, `telefono_usuario`, `correo_usuario`, `status`) VALUES
(1234567, 1, 'ANDERS', 'DAVID', 'ander1234', '$2y$10$tnkCgC.SA4fIWZ1jyJ1Nc.at2Ke5PF0PpZbGwmnBxjFZAeAltJjMW', '12345678901', 'ander2@gmail.com', 1),
(12345612, 1, 'ANDER', 'DAVID', 'ander1223', '$2y$10$PfF6nDUwP8glGtheVMQSWeLB9KljVn/hR1tyAbbgWbJMgHF5ystXi', '12345678901', 'ander3@gmail.com', 0),
(12345678, 1, 'ANDER', 'DAVID', 'David1234', '$2y$10$XkJLaTMns3kXJSxlSrpJ.uqTjP.gzLwLGdL22zeHLRjbxIW6MLGVS', '12345678901', 'ander1@gmail.com', 0),
(30485684, 2, 'ANDERSON', 'FREITEZ', 'Ander123', '$2y$10$3xuW0Z34n9oScdoEDKoo1.OWgDGnch8iJQn2zbeI/Ci0PRP9qCQke', '04169484649', 'andersonfreitez6@gmail.com', 1),
(123456789, 1, 'ANDERSON', 'DAVID', 'ander1234', '$2y$10$2LkOTyTXAcml2JuDnEZPXethdOI1EsrT7iQEGX/ql2hPxQ4GrPs6m', '12345678901', 'ander2@gmail.com', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `id_rol_acceso_fk` (`id_rol`),
  ADD KEY `id_permiso_acceso_fk` (`id_permiso`),
  ADD KEY `id_modulo_acceso_fk` (`id_modulo`);

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `cedula_usuario_bitacora_fk` (`cedula_usuario`),
  ADD KEY `id_modulo_bitacora_fk` (`id_modulo`),
  ADD KEY `id_accion_bitacora_fk` (`id_accion`);

--
-- Indices de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  ADD PRIMARY KEY (`id_cambio_iva`);

--
-- Indices de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD PRIMARY KEY (`id_cambio_moneda`),
  ADD KEY `id_moneda_cambio_fk` (`id_moneda`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`rif_cedula_cliente`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `rif_proveedor_compras_fk` (`rif_proveedor`);

--
-- Indices de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD PRIMARY KEY (`id_detalle_pago`),
  ADD KEY `id_pago_detalle_pago_fk` (`id_pago`),
  ADD KEY `id_moneda_detalle_pago_fk` (`id_moneda`),
  ADD KEY `id_metodo_pago_detalle_pago_fk` (`id_metodo_pago`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  ADD PRIMARY KEY (`id_insumo_compra`),
  ADD KEY `id_compra_insumos_compras_fk` (`id_compra`),
  ADD KEY `id_insumo_insumos_compras_fk` (`id_insumo`);

--
-- Indices de la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  ADD PRIMARY KEY (`id_insumo_venta`),
  ADD KEY `id_venta_insumos_ventas_fk` (`id_venta`),
  ADD KEY `id_insumo_insumos_ventas_fk` (`id_insumo`);

--
-- Indices de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD PRIMARY KEY (`id_materia_prima`);

--
-- Indices de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD PRIMARY KEY (`id_materia_prima_compra`),
  ADD KEY `id_compra_materias_primas_compras_fk` (`id_compra`),
  ADD KEY `id_materia_prima_materias_primas_compras_fk` (`id_materia_prima`);

--
-- Indices de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  ADD PRIMARY KEY (`id_materia_prima_presentacion`),
  ADD KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  ADD KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`);

--
-- Indices de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD PRIMARY KEY (`id_materia_prima_producto`),
  ADD KEY `id_materia_primas_materias_primas_productos_fk` (`id_materia_prima`),
  ADD KEY `id_producto_materias_primas_productos_fk` (`id_producto`);

--
-- Indices de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  ADD PRIMARY KEY (`id_metodo_pago`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  ADD PRIMARY KEY (`id_notificacion_usuario`),
  ADD KEY `cedula_usuario_notificacion_fk` (`cedula_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta_pago_fk` (`id_venta`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD PRIMARY KEY (`id_presentacion`),
  ADD KEY `id_unidad_medida_presentaciones_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD PRIMARY KEY (`id_producto_compra`),
  ADD KEY `id_compra_productos_compras_fk` (`id_compra`),
  ADD KEY `id_producto_productos_compras_fk` (`id_producto`);

--
-- Indices de la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  ADD PRIMARY KEY (`id_producto_presentacion`),
  ADD KEY `id_producto_presentacion_fk` (`id_producto`),
  ADD KEY `id_presentacion_productos_presentacion_fk` (`id_presentacion`);

--
-- Indices de la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  ADD PRIMARY KEY (`id_producto_servicio_venta`),
  ADD KEY `id_producto_productos_servicios_ventas_fk` (`id_producto`),
  ADD KEY `id_servicio_venta_productos_servicios_ventas_fk` (`id_servicio_venta`);

--
-- Indices de la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  ADD PRIMARY KEY (`id_producto_venta`),
  ADD KEY `id_venta_productos_ventas_fk` (`id_venta`),
  ADD KEY `id_producto_productos_ventas_fk` (`id_producto`);

--
-- Indices de la tabla `provedores`
--
ALTER TABLE `provedores`
  ADD PRIMARY KEY (`rif_proveedor`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  ADD PRIMARY KEY (`id_servicios_ventas`),
  ADD KEY `id_venta_servicios_ventas_fk` (`id_venta`),
  ADD KEY `id_servicio_servicios_ventas_fk` (`id_servicio`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `id_rol_usuario_fk` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  ADD KEY `id_cambio_iva_venta` (`id_cambio_iva`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  MODIFY `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  MODIFY `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  MODIFY `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  MODIFY `id_insumo_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  MODIFY `id_insumo_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  MODIFY `id_materia_prima` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  MODIFY `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  MODIFY `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  MODIFY `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  MODIFY `id_notificacion_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  MODIFY `id_presentacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  MODIFY `id_producto_presentacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  MODIFY `id_producto_servicio_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  MODIFY `id_servicios_ventas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `id_modulo_acceso_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_permiso_acceso_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_rol_acceso_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `cedula_usuario_bitacora_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_accion_bitacora_fk` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_bitacora_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD CONSTRAINT `id_moneda_cambio_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `rif_proveedor_compras_fk` FOREIGN KEY (`rif_proveedor`) REFERENCES `provedores` (`rif_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD CONSTRAINT `id_metodo_pago_detalle_pago_fk` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pagos` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_moneda_detalle_pago_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pago_detalle_pago_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  ADD CONSTRAINT `id_compra_insumos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_insumo_insumos_compras_fk` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`id_insumo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  ADD CONSTRAINT `id_insumo_insumos_ventas_fk` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`id_insumo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_insumos_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD CONSTRAINT `id_compra_materias_primas_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_materia_prima_materias_primas_compras_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  ADD CONSTRAINT `id_materia_prima_materias_primas_presentaciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_materias_primas_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD CONSTRAINT `id_materia_primas_materias_primas_productos_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_materias_primas_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  ADD CONSTRAINT `cedula_usuario_notificacion_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_notificacion_notificacion_fk` FOREIGN KEY (`id_notificacion_usuario`) REFERENCES `notificaciones` (`id_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_venta_pago_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD CONSTRAINT `id_unidad_medida_presentaciones_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD CONSTRAINT `id_compra_productos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_compras_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  ADD CONSTRAINT `id_presentacion_productos_presentacion_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_presentacion_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  ADD CONSTRAINT `id_producto_productos_servicios_ventas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_venta_productos_servicios_ventas_fk` FOREIGN KEY (`id_servicio_venta`) REFERENCES `servicios_ventas` (`id_servicios_ventas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  ADD CONSTRAINT `id_producto_productos_ventas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_productos_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `id_unidad_medida_servicios_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  ADD CONSTRAINT `id_servicio_servicios_ventas_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_servicios_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuario_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
