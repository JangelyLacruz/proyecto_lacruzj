-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-09-2026 a las 01:39:51
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_estado_pedido` (IN `sp_id_pedido` VARCHAR(255), IN `sp_estado` INT)   BEGIN 
	START TRANSACTION; 
    -- Devolver el stock de los productos del pedido 
		IF(sp_estado=6) THEN 
		BEGIN 
        	DECLARE v_cantidad_bruta decimal(10,2);
            DECLARE v_stock_actual_producto decimal(10,2);
            DECLARE v_id_producto VARCHAR(30);
            DECLARE fin_consulta INT default 0;
            DECLARE productosPedido CURSOR FOR 
            SELECT (pres.cantidad_pmp * posp.cantidad_producto) as cantidad_bruta, prod.stock_producto,prod.id_producto 
            FROM productos_ordenes_entregas_presupuestos as posp 
            INNER JOIN presentaciones_productos as pp ON posp.id_presentacion_producto = pp.id_presentacion_producto 
            INNER JOIN presentaciones as pres ON pp.id_presentacion = pres.id_presentacion 
            INNER JOIN productos as prod ON pp.id_producto = prod.id_producto 
            WHERE posp.id_orden_entrega_presupuesto = sp_id_pedido;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin_consulta = 1; 
            
            OPEN productosPedido;
            mi_ciclo: LOOP 
            	FETCH productosPedido INTO v_cantidad_bruta, v_stock_actual_producto, v_id_producto; 
            	IF fin_consulta = 1 THEN 
                	LEAVE mi_ciclo; 
                END IF; 
            	-- Actualizamos cada stock 
            	UPDATE productos SET stock_producto = v_stock_actual_producto+v_cantidad_bruta WHERE id_producto=v_id_producto; 
                IF(ROW_COUNT()<=0) THEN 
            		ROLLBACK; 
            		SIGNAL SQLSTATE '45000'
            		SET MESSAGE_TEXT = 'No se logró actualizar el stock de uno de los productos'; 
            	END IF;
                
                -- Borramos los detalles de productos
                UPDATE productos_ordenes_entregas_presupuestos set status =0 
                WHERE id_orden_entrega_presupuesto = sp_id_pedido;
                IF(ROW_COUNT()<=0) THEN 
            		ROLLBACK; 
            		SIGNAL SQLSTATE '45000'
            		SET MESSAGE_TEXT = 'No se logró actualizar el stock de uno de los productos'; 
            	END IF;
            END LOOP mi_ciclo; 
            CLOSE productosPedido; 
        END; 
            
       -- Borrar los detalles del pago 
       UPDATE pagos SET status = 0 
       WHERE id_orden_entrega_presupuesto = sp_id_pedido; 
       	IF(ROW_COUNT()<=0) THEN 
       		ROLLBACK; 
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se borraron los detalles del pago'; 
       	END IF; 
	END IF; 
   	-- Actualizamos en status del pedido 
	UPDATE ordenes_entregas_presupuestos SET status = sp_estado 
	WHERE id_orden_entrega_presupuesto = sp_id_pedido; 
	IF(ROW_COUNT()<=0) THEN 
    	ROLLBACK; 
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se pudo actualizar el estado del pedido'; 
    END IF; 
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_materia_prima` (IN `id_materia_prima` VARCHAR(20), IN `precio_materia_prima` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_materias_primas (id_materia_prima, precio_materia_prima) 
    VALUES (id_materia_prima, precio_materia_prima);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_moneda` (IN `id_moneda` INT, IN `nuevo_valor_moneda` DECIMAL(20,2))   BEGIN
	INSERT INTO cambios_monedas(id_moneda,valor_moneda)
    VALUES(id_moneda, nuevo_valor_moneda);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_producto` (IN `p_id_producto` VARCHAR(20), IN `p_precio` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_productos (id_producto, precio_producto) VALUES (p_id_producto, p_precio);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_ruta` (IN `id_ruta` INT, IN `precio_ruta` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_rutas (id_ruta, precio_ruta) VALUES (id_ruta, precio_ruta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_servicio` (IN `id_servicio` VARCHAR(20), IN `precio_servicio` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_servicios (id_servicio, precio_servicio) VALUES (id_servicio, precio_servicio);
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_obtener_iva_actual` () RETURNS DECIMAL(20,2) READS SQL DATA BEGIN
    DECLARE v_iva decimal(20,2);
    SELECT monto_cambio_iva INTO v_iva 
    FROM cambios_iva 
    ORDER BY id_cambio_iva DESC
    LIMIT 1;
    RETURN v_iva;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_precio_con_iva` (`precio` DECIMAL(10,2)) RETURNS DECIMAL(20,2)  BEGIN

DECLARE iva_actual DECIMAL(20,2);
DECLARE precio_con_iva DECIMAL(20,2);
SELECT fn_obtener_iva_actual() INTO iva_actual;
set precio_con_iva = (precio+((precio*iva_actual)/100));
RETURN precio_con_iva;

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_sacar_raiz_cuadra` (`numero` DECIMAL(10,2)) RETURNS DECIMAL(10,2)  BEGIN
	RETURN SQRT(numero);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_sumar_dos_numeros` (`numero1` DECIMAL(10,2), `numero2` DECIMAL(10,2)) RETURNS DECIMAL(10,2)  BEGIN
	RETURN numero1+ numero2;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos`
--

CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bancos`
--

INSERT INTO `bancos` (`id_banco`, `nombre_banco`, `status`) VALUES
(1, 'VENEZUELA', 0),
(2, 'BANCO PROVINCIAL', 0),
(93, 'bando de ejemplo', 0),
(2849, 'PROVINCIAL', 0),
(2850, 'MENCÁNTIL', 0),
(2851, 'BANESCO', 0),
(2852, 'BICENTENARIO', 0),
(2853, '0102 - BANCO DE VENEZUELA', 1),
(2854, '0104 - BANCO VENEZOLANO DE CRÉDITO', 1),
(2855, '0105 - BANCO MERCÁNTIL', 1),
(2856, '0108 - BBVA PROVINCIAL', 1),
(2857, '0114 - BANCARIBE', 1),
(2858, '0115 - BANCO EXTERIOR', 1),
(2859, '0128 - BANCO CARONÍ', 1),
(2860, '0134 - BANESCO', 1),
(2861, '0137 - BANCO SOFITASA', 1),
(2862, '0138 - BANCO PLAZA', 1),
(2863, '0146 - BANGENTE', 1),
(2864, '0151 - BANCO FONDO COMÚN', 1),
(2865, '0156 - 100% BANCO', 1),
(2866, '0157 - DELSUR BANCO UNIVERSAL', 1),
(2867, '0163 - BANCO DEL TESORO', 1),
(2868, '0168 - BANCRECER', 1),
(2869, '0169 - R4 BANCO MICROFINANCIERO C.A.', 1),
(2870, '0171 - BANCO ACTIVO', 1),
(2871, '0172 - BANCAMIGA BANCO UNIVERSAL', 1),
(2872, '0174 - BAMPLUS', 1),
(2873, '0175 - BANCO DIGITAL DE LOS TRABAJADORES', 1),
(2874, '0177 - BANFANB', 1),
(2875, '0178 - N58 BANCO DIGITAL MICROFINANCIERO S.A.', 1),
(2876, '0191 - BANCO NACIONAL DE CRÉDITO', 1),
(2877, '0601 - INSTITUTO NACIONAL DE CRÉDITO POPULAR', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_detalles_pagos`
--

CREATE TABLE `bancos_detalles_pagos` (
  `id_banco_detalle_pago` int(11) NOT NULL,
  `id_detalle_pago` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `es_emisor` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_iva`
--

CREATE TABLE `cambios_iva` (
  `id_cambio_iva` int(11) NOT NULL,
  `monto_cambio_iva` decimal(20,2) NOT NULL,
  `fecha_cambio_iva` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_iva`
--

INSERT INTO `cambios_iva` (`id_cambio_iva`, `monto_cambio_iva`, `fecha_cambio_iva`, `status`) VALUES
(1, 12.00, '2025-12-05 18:15:15', 1),
(129, 12.00, '2026-04-24 15:34:11', 1),
(130, 12.00, '2026-04-24 15:34:17', 1),
(131, 12.00, '2026-04-24 15:36:14', 1),
(132, 12.00, '2026-04-24 15:36:50', 1),
(133, 12.00, '2026-04-24 15:36:54', 1),
(134, 12.00, '2026-04-24 15:41:00', 1),
(135, 24.00, '2026-04-24 15:46:47', 1),
(136, 12.00, '2026-04-24 15:49:02', 1),
(137, 122.00, '2026-05-05 20:28:20', 1),
(138, 16.00, '2026-05-17 10:12:05', 1),
(161, 77.00, '2026-05-25 11:55:35', 1),
(162, 15.00, '2026-05-25 12:00:18', 1),
(163, 19.00, '2026-05-25 12:05:19', 1),
(164, 78.00, '2026-05-25 12:06:32', 1),
(165, 15.00, '2026-05-25 12:07:45', 1),
(166, 15.00, '2026-05-25 12:18:15', 1),
(168, 12.00, '2026-05-25 12:48:10', 1),
(178, 23.00, '2026-05-25 13:22:39', 1),
(179, 12.00, '2026-05-25 13:23:25', 1),
(180, 15.00, '2026-05-25 13:24:59', 1),
(181, 23.00, '2026-05-25 13:30:57', 1),
(182, 78.00, '2026-05-25 13:32:03', 1),
(183, 89.00, '2026-05-25 13:32:20', 1),
(186, 34.00, '2026-05-25 13:44:22', 1),
(187, 12.00, '2026-05-25 13:49:17', 1),
(188, 15.00, '2026-05-25 13:49:48', 1),
(189, 16.00, '2026-05-25 19:41:09', 1),
(190, 18.00, '2026-05-25 19:42:29', 1),
(191, 20.00, '2026-05-26 20:46:10', 1),
(192, 22.00, '2026-05-26 20:47:31', 1),
(195, 24.00, '2026-05-26 20:49:45', 1),
(197, 16.00, '2026-05-29 08:10:36', 1),
(198, 20.00, '2026-05-29 08:17:38', 1),
(199, 16.00, '2026-05-29 08:18:08', 1),
(200, 23.00, '2026-05-29 08:19:05', 1),
(201, 24.00, '2026-05-29 08:20:54', 1),
(234, 1.00, '2026-06-09 00:56:53', 1),
(235, 1250000000000.00, '2026-06-13 16:27:13', 1),
(236, 1250000000000.00, '2026-06-13 16:27:13', 1),
(237, 1250.00, '2026-06-13 16:28:00', 1),
(238, 1250.00, '2026-06-13 16:28:00', 1),
(239, 1250.50, '2026-06-13 16:28:06', 1),
(240, 1250.50, '2026-06-13 16:28:06', 1),
(250, 77.00, '2026-06-13 16:47:54', 1),
(251, 77.00, '2026-06-13 16:48:00', 1),
(252, 87.00, '2026-06-13 16:48:18', 1),
(253, 16.00, '2026-06-13 19:30:36', 1),
(254, 78.00, '2026-06-20 22:10:21', 1),
(256, 16.00, '2026-06-27 13:41:17', 1),
(257, 22.00, '2026-07-02 22:10:39', 1),
(258, 16.00, '2026-07-09 13:28:30', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_monedas`
--

CREATE TABLE `cambios_monedas` (
  `id_cambio_moneda` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_monedas`
--

INSERT INTO `cambios_monedas` (`id_cambio_moneda`, `id_moneda`, `valor_moneda`, `fecha_cambio`, `status`) VALUES
(1, 1, 17.00, '2025-12-06 20:12:19', 1),
(2, 2, 2.00, '2025-12-06 20:13:37', 1),
(3, 3, 3.00, '2025-12-06 20:13:48', 1),
(4, 1, 257.93, '2025-12-06 20:14:15', 1),
(5, 1, 250.00, '2025-12-11 18:35:07', 1),
(6, 3, 250.00, '2025-12-11 19:04:46', 1),
(7, 2, 1.00, '2025-12-14 16:30:34', 1),
(8, 1, 23.00, '2026-02-07 20:44:34', 1),
(9, 1, 12.00, '2026-02-09 20:46:09', 1),
(10, 1, 250.00, '2026-02-09 20:46:55', 1),
(11, 1, 279.00, '2026-03-30 10:22:47', 1),
(12, 2, 22.00, '2026-03-30 10:22:56', 1),
(13, 3, 500.00, '2026-03-30 10:23:05', 1),
(14, 1, 12.00, '2026-03-30 10:29:39', 1),
(15, 3, 1234.00, '2026-03-30 10:29:58', 1),
(20, 1, 1200.00, '2026-05-06 10:25:10', 1),
(21, 3, 1200.00, '2026-05-06 10:25:20', 1),
(22, 1, 49338.00, '2026-05-06 16:29:26', 1),
(23, 1, 496.83, '2026-05-06 18:29:34', 1),
(24, 1, 2500.00, '2026-05-06 18:43:11', 1),
(25, 2, 120.00, '2026-05-06 18:44:28', 1),
(26, 1, 250.01, '2026-05-06 18:44:42', 1),
(27, 1, 500.00, '2026-05-06 18:44:53', 1),
(28, 2, 1.00, '2026-05-17 08:25:25', 1),
(29, 3, 600.00, '2026-05-17 08:26:06', 1),
(30, 1, 700.00, '2026-05-17 15:41:04', 1),
(31, 2, 1000.00, '2026-05-26 20:32:32', 1),
(32, 1, 600.00, '2026-05-28 14:01:03', 1),
(33, 8, 700.00, '2026-05-28 14:01:59', 1),
(57, 8, 0.12, '2026-05-30 20:05:20', 1),
(88, 2, 1.00, '2026-06-03 15:24:41', 1),
(89, 1, 750.00, '2026-06-04 08:31:28', 1),
(105, 1, 567.00, '2026-06-08 21:19:55', 1),
(133, 4, 1250000000000.00, '2026-06-13 16:27:13', 1),
(134, 4, 1250.00, '2026-06-13 16:28:00', 1),
(135, 4, 1250.50, '2026-06-13 16:28:06', 1),
(139, 1, 623.02, '2026-06-27 18:38:28', 1),
(140, 37, 0.12, '2026-07-02 21:03:54', 1),
(141, 37, 1.20, '2026-07-02 21:17:36', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_categoria_producto` varchar(50) NOT NULL,
  `necesitan_materias_primas` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id_categoria_producto`, `nombre_categoria_producto`, `necesitan_materias_primas`, `status`) VALUES
(1, 'FABRICADOS', 1, 1),
(2, 'NO FABRICADOS', 0, 1),
(3, 'INSUMOS', 1, 1),
(20, 'mmmm', 0, 0),
(21, 'NOMBRE', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `razon_social_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(150) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`rif_cedula_cliente`, `razon_social_cliente`, `telefono_cliente`, `correo_cliente`, `direccion_cliente`, `status`) VALUES
('', 'ANDEROSN FREITEZ', '04161234567', 'andersonfreitezN@gmail.com', 'ANDERSON', 0),
('30485684', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 0),
('E25252525', 'ANDEROSN FREITE', '04169484647', 'andersonfreite5z6@gmail.com', '5454', 0),
('E30485684', 'ANDEROSN FREIT', '04169484645', 'andersonfreitez6@gmail.co', 'SANARE', 0),
('G200000589', 'Círculo Militar de la Fuerza Armada', '', '', 'Av. Morancon Av. Los Abogados Barquisimeto Edo. Lara', 1),
('G200101350', 'Hospital Militar Dr. José Angel Alamo', '', 'oficinarecaudadolara25@gmail.com', 'Av. Principal El Ujano Edf. Hospital Militar Dr. José Angel Almo Barquisimeto Edo Lara', 1),
('J001241345', 'Compañía Anónima Nacional Teléfonos de Venezuela (CANTV)', '02125007373', '', 'Final Av. Libertador Edificio CANTV Caracas Venezuela ', 1),
('J001673920', 'Hospitalar C.A.', '04241941573', 'contigo@hospitalarve.com', 'Vda. Francisco de Miranda Edif. Centro Seguros La Paz Piso 7 Local 0-71, 0-73, 0-75, S-71 Urb. Boleita Caracas ( Petare) Miranda Zona Postal 1070', 1),
('J070003448', 'Cervecería Regional C.A', '04129628546', 'andrea.linares@cerveceriaregional.com', 'Av. 17 Los Haticos Local N 112-13 Maracaibo Edo. Zulia', 1),
('J116545646', 'dadasdasda', '04261231231', 'carloshurtado30e15@gmail.com', 'adasdadasdasdadasdasd', 0),
('J310729425', 'Condominio Centro Comercial Profesional Rosancar', '04128479199', 'rosancarbarquisimeto@gmail.com', 'Calle 20 Esq. Carrera 31 C.C Rosancar Nivel 30-97 Local PB Sector Centro Barquisimeto Edo. Lara', 1),
('J31317343', 'Carlos Hurtado', '04164532184', 'carloshurtado30e15@gmail.com', 'Valles de uribana, tamaca', 0),
('J314964291', 'Mbzoluciones C.A', '04222547862', 'mbzolucioles@gmail.com', 'Calle 26 entre Carreras 16 y 17 Edif. Torre Ejecutiva piso 4 oficina 45 Barquisimeto Edo Lara', 0),
('J54686423', 'asdadasdasdasd', '04243453453', 'carlosdsd30e15@gmail.com', 'dfsdfsfsdfsdff', 0),
('V12345666', 'Anderson Freitez', '04169484640', 'andersonfreitez61@gmail.com', 'SANARE', 0),
('V123456669', 'Anderson Freitez', '04169484678', 'andersonfreitekz6@gmail.com', 'SANARE', 0),
('V12345668', 'Anderson Freitez', '04169484648', 'andersonfreit9z6@gmail.com', 'SANARE', 0),
('V12345669', 'Anderson Freitez', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 0),
('V1234567', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'BARQUISIMETO', 0),
('V304856111', 'Anderson Freitei', '04169484647', 'andersonfreitez68@gmail.com', 'BARQUISIMETO', 0),
('V30485631', 'Ander Mendoza', '04160484640', 'andersonfreitez63@gmail.com', 'El Molino', 0),
('V30485680', 'Anderson Freitez', '04169484655', 'andersonfreitez76@gmail.com', 'SANARE', 0),
('V30485681', 'Anderson Freitez', '04169484643', 'andersonfreitez66@gmail.com', 'BARQUISIMETO', 0),
('V30485682', 'NADA', '04141234567', 'andersonfreitez6@gmail.co', 'SANARE', 0),
('V30485683', 'Anderson Freitezm', '04169484648', 'andersonfreitez68@gmail.com', 'BARQUISIMETO', 0),
('V30485684', 'Anderson Freitez', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 0),
('V304856845', '54114', '04169484646', 'andersonfreitez66@gmail.com', 'hhhh', 0),
('V30485686', 'Anderson Freitez', '04169999999', 'andersonfreitez6999@gmail.com', 'SANARE', 0),
('V30485688', 'Anderson Freitez', '04169484640', 'andersonfremitez6@gmail.com', 'BARQUISIMETO', 0),
('V30485689', 'ANDEROSN FREITEZ', '04169484648', 'andersonfreitez6@gmail.con', 'David', 0),
('V30485694', 'Anderson Freitez', '04169484699', 'andersonfreitez996@gmail.com', 'BARQUISIMETO', 0),
('V33333331', 'ANDEROSN FREITEZaa', '04161234569', 'andersonfreitez6@gmail.coj', 'ANDERSON', 0),
('V33333332', 'ANDEROSN FREITEZ', '04161234567', 'andersonfreitez12@gmail.com', 'Sanare', 0),
('V33333333', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'BARQUISIMETO', 0),
('V333333333', 'ANDEROSN FREITEZG', '04161234567', 'andersonfreitez16@gmail.com', 'SANARE', 0),
('V333333338', 'ANDEROSN FREITEj', '04161234560', 'andersonfreitez06@gmail.com', 'hhh', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` varchar(20) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `rif_proveedor`, `cedula_usuario`, `fecha_compra`, `status`) VALUES
('10', '30485684', '30485684', '2026-02-18 21:04:00', 0),
('11', '30485684', '30485684', '2026-05-18 10:00:00', 0),
('12', '30485684', '30485684', '2026-05-19 16:27:00', 0),
('13', '30485684', '30485684', '2026-05-30 10:13:00', 0),
('14', '30485684', '30485684', '2026-05-30 10:33:00', 0),
('15', '30485684', '30485684', '2026-05-30 18:08:00', 0),
('16', '30485684', '30485684', '2026-05-30 18:08:00', 0),
('17', '30485684', '30485684', '2026-06-07 14:06:00', 0),
('18', '30485684', '30485684', '2026-06-07 14:07:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantes_pagos`
--

CREATE TABLE `comprobantes_pagos` (
  `id_comprobante_pago` int(11) NOT NULL,
  `id_pago` varchar(20) NOT NULL,
  `path_comprobante` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comprobantes_pagos`
--

INSERT INTO `comprobantes_pagos` (`id_comprobante_pago`, `id_pago`, `path_comprobante`, `status`) VALUES
(143, 'PAG-26179-00001-82', 'comprobantes_pagos_2026_06_29_18_09_11_18.jpg', 1),
(144, 'PAG-26179-00002-96', 'comprobantes_pagos_2026_06_29_18_13_42_10.jpg', 1),
(145, 'PAG-26180-00001-26', 'comprobantes_pagos_2026_06_30_16_36_36_81.jpg', 1),
(146, 'PAG-26181-00001-57', 'comprobantes_pagos_2026_07_01_11_24_44_33.jpg', 1),
(147, 'PAG-26183-00001-58', 'comprobantes_pagos_2026_07_03_06_44_09_24.jpg', 1),
(148, 'PAG-26183-00002-83', 'comprobantes_pagos_2026_07_03_06_55_02_9.jpg', 1),
(149, 'PAG-26190-00001-58', 'comprobantes_pagos_2026_07_10_00_47_24_22.jpg', 1),
(150, 'PAG-26221-00001-44', 'comprobantes_pagos_2026_08_10_17_44_30_56.jpg', 1),
(151, 'PAG-26227-00001-57', 'comprobantes_pagos_2026_08_16_19_00_02_21.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_ordenes_entregas_presupuestos`
--

CREATE TABLE `control_ordenes_entregas_presupuestos` (
  `rif_cliente` varchar(20) NOT NULL,
  `numero_control_factura` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `control_ordenes_entregas_presupuestos`
--

INSERT INTO `control_ordenes_entregas_presupuestos` (`rif_cliente`, `numero_control_factura`, `status`) VALUES
('J001241345', 'GTC5834467', 1),
('J001673920', 'DFG3453454', 1),
('J070003448', 'zfd3458644', 1),
('J310729425', '2312ASDAD5', 1),
('J31317343', 'ZCT2345563', 1),
('J314964291', '5676SFS234', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deliveries`
--

CREATE TABLE `deliveries` (
  `id_delivery` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cedula_repartidor` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deliveries`
--

INSERT INTO `deliveries` (`id_delivery`, `id_orden_entrega_presupuesto`, `id_direccion`, `cedula_repartidor`, `status`) VALUES
('DELI-26179-00001-53', 'FACT-26179-00001-24', 156, 'V12344567', 1),
('DELI-26179-00002-63', 'FACT-26179-00002-67', 157, 'V12344567', 1),
('DELI-26180-00001-89', 'FACT-26180-00001-62', 158, 'V12344567', 1),
('DELI-26181-00001-81', 'FACT-26181-00001-30', 159, 'V12344567', 1),
('DELI-26183-00001-31', 'FACT-26183-00001-30', 160, 'V12344567', 1),
('DELI-26183-00002-07', 'FACT-26183-00002-26', 161, 'V12344567', 1),
('DELI-26190-00001-38', 'FACT-26190-00001-44', 162, 'V12344567', 1),
('DELI-26221-00001-04', 'FACT-26221-00001-60', 163, 'V12344567', 1),
('DELI-26227-00001-53', 'FACT-26227-00001-55', 164, 'V30485654', 1),
('DELI-26259-00001-98', 'OEP-26258-00002-85', 166, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pagos`
--

CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL,
  `id_pago` varchar(20) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `monto_pago` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_pagos`
--

INSERT INTO `detalles_pagos` (`id_detalle_pago`, `id_pago`, `id_metodo_pago`, `id_moneda`, `monto_pago`, `status`) VALUES
(264, 'PAG-26179-00001-82', 3, 1, 113.00, 1),
(265, 'PAG-26179-00002-96', 3, 1, 113.00, 1),
(266, 'PAG-26180-00001-26', 3, 1, 529.00, 1),
(267, 'PAG-26181-00001-57', 3, 1, 321.00, 1),
(268, 'PAG-26183-00001-58', 3, 1, 337.00, 1),
(269, 'PAG-26183-00002-83', 3, 1, 337.00, 1),
(270, 'PAG-26190-00001-58', 3, 1, 320.16, 1),
(271, 'PAG-26221-00001-44', 3, 1, 304.00, 1),
(272, 'PAG-26227-00001-57', 3, 1, 113.00, 1),
(273, 'PAG-26258-00001-00', 6, 1, 1.16, 1),
(274, 'PAG-26259-00001-57', 6, 1, 29.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL,
  `id_latitud_direccion` int(11) NOT NULL,
  `id_longitud_direccion` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id_direccion`, `id_latitud_direccion`, `id_longitud_direccion`, `id_ruta`, `status`) VALUES
(28, 58, 54, 6, 1),
(29, 59, 55, 6, 1),
(30, 60, 56, 6, 1),
(31, 61, 57, 6, 1),
(32, 62, 58, 2, 1),
(33, 63, 59, 6, 1),
(49, 79, 75, 6, 1),
(50, 80, 76, 6, 1),
(51, 81, 77, 6, 1),
(52, 81, 77, 11, 1),
(53, 82, 78, 11, 1),
(54, 83, 79, 11, 1),
(55, 84, 80, 11, 1),
(56, 85, 81, 11, 1),
(59, 88, 84, 11, 1),
(60, 89, 85, 11, 1),
(61, 90, 86, 10, 1),
(62, 91, 87, 10, 1),
(63, 92, 88, 10, 1),
(64, 93, 89, 10, 1),
(65, 94, 90, 10, 1),
(66, 95, 91, 10, 1),
(77, 106, 102, 11, 1),
(78, 107, 103, 11, 1),
(79, 108, 104, 11, 1),
(80, 109, 105, 11, 1),
(81, 110, 106, 11, 1),
(82, 111, 107, 11, 1),
(83, 112, 108, 11, 1),
(84, 113, 109, 11, 1),
(85, 114, 110, 11, 1),
(86, 115, 111, 11, 1),
(124, 153, 149, 11, 1),
(125, 154, 150, 11, 1),
(131, 160, 156, 11, 1),
(141, 170, 166, 11, 1),
(142, 171, 167, 11, 1),
(143, 172, 168, 11, 1),
(144, 173, 169, 11, 1),
(145, 174, 170, 11, 1),
(146, 175, 171, 11, 1),
(149, 178, 174, 11, 1),
(150, 179, 175, 11, 1),
(151, 180, 169, 11, 1),
(153, 182, 177, 11, 1),
(154, 183, 178, 11, 1),
(155, 184, 179, 11, 1),
(156, 185, 180, 11, 1),
(157, 186, 181, 11, 1),
(158, 187, 182, 11, 1),
(159, 188, 183, 11, 1),
(160, 189, 184, 11, 1),
(161, 190, 185, 11, 1),
(162, 191, 186, 11, 1),
(163, 192, 187, 11, 1),
(164, 193, 188, 11, 1),
(165, 194, 189, 9, 1),
(166, 195, 190, 11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_envios`
--

CREATE TABLE `empresas_envios` (
  `id_empresa_envios` int(11) NOT NULL,
  `nombre_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas_envios`
--

INSERT INTO `empresas_envios` (`id_empresa_envios`, `nombre_empresa`, `status`) VALUES
(1, 'ZOOMO', 0),
(2, 'ZOOM', 0),
(3, 'ZOOMh', 0),
(4, 'ZOOM', 0),
(5, 'ALIBABAB', 0),
(6, 'ZOOM', 1),
(7, 'ZOOM 2', 0),
(34, 'ZOOM2', 0),
(35, 'ZOOMMM', 0),
(36, 'mmm9', 0),
(37, 'ZOOMM', 0),
(38, 'ZOOMmm.', 0),
(39, 'ZOOMmm', 0),
(40, 'ZOOMs', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios_terceros`
--

CREATE TABLE `envios_terceros` (
  `id_envio_tercero` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `precio_envio_tercero` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `latitudes_direcciones`
--

CREATE TABLE `latitudes_direcciones` (
  `id_latitud_direccion` int(11) NOT NULL,
  `coordenada_latitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `latitudes_direcciones`
--

INSERT INTO `latitudes_direcciones` (`id_latitud_direccion`, `coordenada_latitud`, `status`) VALUES
(58, '10.0626331', 1),
(59, '10.0630566', 1),
(60, '10.0632884', 1),
(61, '10.072417', 1),
(62, '10.0670697', 1),
(63, '10.039655', 1),
(79, '10.053758704512', 1),
(80, '9.8599964012514', 1),
(81, '10.123456', 1),
(82, '9.8595170293252', 1),
(83, '9.8600828102787', 1),
(84, '9.8600797557029', 1),
(85, '9.8603294672703', 1),
(88, '9.8616122673031', 1),
(89, '9.8616276041428', 1),
(90, '10.051815346816', 1),
(91, '10.050653', 1),
(92, '9.8613761802134', 1),
(93, '9.8600626405522', 1),
(94, '10.050305879849', 1),
(95, '10.05010893544', 1),
(106, '9.8601550931395', 1),
(107, '9.859754768276', 1),
(108, '9.8599933013011', 1),
(109, '9.8599169544674', 1),
(110, '9.8615897462498', 1),
(111, '9.8616255287098', 1),
(112, '9.8603268948328', 1),
(113, '9.8603829908308', 1),
(114, '9.860111242047', 1),
(115, '9.8612437338729', 1),
(153, '9.8604001121099', 1),
(154, '9.8597670526368', 1),
(160, '9.8597788105131', 1),
(170, '9.861874', 1),
(171, '9.861672', 1),
(172, '9.8597561143133', 1),
(173, '9.8600273814353', 1),
(174, '9.8600766058849', 1),
(175, '9.8592958653846', 1),
(178, '9.8618713140662', 1),
(179, '9.8594610351324', 1),
(180, '9.8600262893916', 1),
(182, '9.8618126247782', 1),
(183, '9.8618387108127', 1),
(184, '9.8603384904674', 1),
(185, '9.8597834819881', 1),
(186, '9.8601496311066', 1),
(187, '9.861755', 1),
(188, '9.8597431994487', 1),
(189, '9.8600491537911', 1),
(190, '9.8598981990669', 1),
(191, '9.8597769944842', 1),
(192, '9.93103', 1),
(193, '9.8618556465183', 1),
(194, '10.065163756959667', 1),
(195, '10.209999999999987', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `longitudes_direcciones`
--

CREATE TABLE `longitudes_direcciones` (
  `id_longitud_direccion` int(11) NOT NULL,
  `coordenada_longitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `longitudes_direcciones`
--

INSERT INTO `longitudes_direcciones` (`id_longitud_direccion`, `coordenada_longitud`, `status`) VALUES
(54, '-69.3654583', 1),
(55, '-69.3644222', 1),
(56, '-69.3648094', 1),
(57, '-69.3757301', 1),
(58, '-69.3441849', 1),
(59, '-69.4294816', 1),
(75, '-69.273433685303', 1),
(76, '-69.611978291735', 1),
(77, '10.123456', 1),
(78, '-69.611947939329', 1),
(79, '-69.611985050712', 1),
(80, '-69.611983887064', 1),
(81, '-69.612001773886', 1),
(84, '-69.612089384389', 1),
(85, '-69.612089481661', 1),
(86, '-69.366774559021', 1),
(87, '-69.362676', 1),
(88, '-69.612072884695', 1),
(89, '-69.611984815715', 1),
(90, '-69.360347986221', 1),
(91, '-69.326858521672', 1),
(102, '-69.61199178066', 1),
(103, '-69.611963467553', 1),
(104, '-69.61198011811', 1),
(105, '-69.611974940021', 1),
(106, '-69.612088084289', 1),
(107, '-69.61208912813', 1),
(108, '-69.61200079391', 1),
(109, '-69.612006685027', 1),
(110, '-69.611986751037', 1),
(111, '-69.612064389881', 1),
(149, '-69.612005278293', 1),
(150, '-69.611963985342', 1),
(156, '-69.611965316644', 1),
(166, '-69.612083', 1),
(167, '-69.61203', 1),
(168, '-69.611963134899', 1),
(169, '-69.611978425897', 1),
(170, '-69.611981333779', 1),
(171, '-69.611937', 1),
(174, '-69.612083222138', 1),
(175, '-69.611946478615', 1),
(177, '-69.612078044944', 1),
(178, '-69.612080150969', 1),
(179, '-69.611995786211', 1),
(180, '-69.611964647621', 1),
(181, '-69.611986523216', 1),
(182, '-69.612045', 1),
(183, '-69.611962408635', 1),
(184, '-69.611980430118', 1),
(185, '-69.611971249216', 1),
(186, '-69.611963692348', 1),
(187, '-69.621948', 1),
(188, '-69.612069311619', 1),
(189, '-69.32130575180055', 1),
(190, '-69.3', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas`
--

CREATE TABLE `materias_primas` (
  `id_materia_prima` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` decimal(20,2) NOT NULL,
  `stock_minimo_materia_prima` decimal(20,2) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas`
--

INSERT INTO `materias_primas` (`id_materia_prima`, `id_unidad_medida`, `nombre_materia_prima`, `stock_materia_prima`, `stock_minimo_materia_prima`, `precio_materia_prima`, `status`) VALUES
('MATE-26123-00001-66', 2, 'HIPOCLORITO', 100.00, 10.00, 50.00, 1),
('MATE-26149-00001-45', 1, 'HIPOCLORITOm', 10.00, 8.00, 10.00, 0),
('MATE-26149-00002-90', 1, 'HIPOCLORITOSS', 222.00, 222.00, 111.00, 0),
('MATE-26157-00001-95', 2, 'HIPOCLORITOm', 101.00, 1.00, 1.00, 1);

--
-- Disparadores `materias_primas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_materia_prima_act` AFTER UPDATE ON `materias_primas` FOR EACH ROW BEGIN
IF NEW.PRECIO_MATERIA_PRIMA <> OLD.PRECIO_MATERIA_PRIMA THEN 
	CALL sp_guardar_precio_materia_prima(
    	NEW.id_materia_prima,
        NEW.precio_materia_prima
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_materia_prima_reg` AFTER INSERT ON `materias_primas` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_materia_prima(
    	NEW.id_materia_prima,
        NEW.precio_materia_prima
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_compras`
--

CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_compras`
--

INSERT INTO `materias_primas_compras` (`id_materia_prima_compra`, `id_materia_prima`, `id_compra`, `cantidad_materia_prima`, `status`) VALUES
(1, 'MATE-26123-00001-66', '10', 2.00, 0),
(2, 'MATE-26123-00001-66', '10', 5.00, 0),
(10, 'MATE-26123-00001-66', '12', 12.00, 0),
(11, 'MATE-26123-00001-66', '12', 13.00, 0),
(12, 'MATE-26123-00001-66', '13', 20.00, 0),
(14, 'MATE-26123-00001-66', '14', 10.00, 0),
(15, 'MATE-26123-00001-66', '15', 1.00, 0),
(16, 'MATE-26123-00001-66', '16', 1.00, 0),
(17, 'MATE-26123-00001-66', '17', 2.00, 0),
(18, 'MATE-26123-00001-66', '18', 10.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_producciones`
--

CREATE TABLE `materias_primas_producciones` (
  `id_materia_prima_produccion` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_productos`
--

CREATE TABLE `materias_primas_productos` (
  `id_materia_prima_producto` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_productos`
--

INSERT INTO `materias_primas_productos` (`id_materia_prima_producto`, `id_materia_prima`, `id_producto`, `cantidad_materia_prima`, `status`) VALUES
(187, 'MATE-26123-00001-66', 'PROD-26183-00001-56', 10.00, 0),
(200, 'MATE-26123-00001-66', 'PROD-26150-00001-39', 0.10, 0),
(202, 'MATE-26123-00001-66', 'PROD-26222-00001-83', 2.00, 0),
(203, 'MATE-26123-00001-66', 'PROD-26222-00002-19', 10.00, 1),
(204, 'MATE-26123-00001-66', 'PROD-26222-00003-10', 10.00, 0),
(206, 'MATE-26123-00001-66', 'PROD-26222-00006-10', 0.01, 0),
(214, 'MATE-26123-00001-66', 'PROD-26222-00004-43', 1.00, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pagos`
--

CREATE TABLE `metodos_pagos` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo_pago` varchar(50) NOT NULL,
  `necesita_moneda` tinyint(1) NOT NULL,
  `necesita_banco_emisor` tinyint(1) NOT NULL,
  `necesita_banco_receptor` tinyint(1) NOT NULL,
  `necesita_referencia` tinyint(1) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pagos`
--

INSERT INTO `metodos_pagos` (`id_metodo_pago`, `nombre_metodo_pago`, `necesita_moneda`, `necesita_banco_emisor`, `necesita_banco_receptor`, `necesita_referencia`, `mostrar_ecommerce`, `status`) VALUES
(1, 'TRANSFERENCIA', 0, 1, 1, 1, 1, 1),
(2, 'PAGO MÓVIL', 0, 0, 1, 1, 1, 1),
(3, 'ZELLE', 1, 0, 0, 1, 1, 1),
(4, 'ZINLI', 1, 0, 0, 1, 0, 0),
(5, 'BINANCE', 1, 1, 1, 1, 1, 1),
(6, 'EFECTIVO', 1, 0, 0, 0, 0, 1),
(12, 'TRANSFERENCI', 1, 1, 1, 1, 0, 0),
(13, 'ZELLEL', 1, 1, 1, 1, 0, 0),
(17, 'TOPOmm', 1, 1, 1, 1, 1, 0),
(18, 'TOPOm', 1, 0, 0, 0, 1, 0),
(51, 'OTRO', 0, 0, 0, 0, 0, 0),
(52, 'EFECTIVOM', 1, 1, 1, 1, 1, 0),
(53, 'ZINLI', 1, 0, 0, 0, 0, 0),
(54, 'EFECTIVOmm', 0, 0, 0, 0, 0, 0),
(55, 'EFECTIVOmm', 0, 0, 0, 0, 0, 0),
(56, 'EFECTIVOs', 1, 1, 1, 1, 1, 0),
(57, 'EFECTIVOb', 1, 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(11) NOT NULL,
  `nombre_moneda` varchar(20) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre_moneda`, `simbolo_moneda`, `valor_moneda`, `status`) VALUES
(1, 'DÓLAR', '$', 623.02, 1),
(2, 'BÓLIVAR', 'BS', 1.00, 1),
(3, 'EURO', '€', 600.00, 1),
(4, 'YUAN', '¥', 1250.50, 1),
(8, 'VALOR', 'U', 0.12, 0),
(37, 'YUAN2', 'YN', 1.20, 0);

--
-- Disparadores `monedas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_moneda_act` AFTER UPDATE ON `monedas` FOR EACH ROW BEGIN
IF NEW.VALOR_MONEDA <> OLD.VALOR_MONEDA THEN
CALL sp_guardar_precio_moneda(
	NEW.id_moneda,
    NEW.valor_moneda
);
END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_moneda_reg` AFTER INSERT ON `monedas` FOR EACH ROW BEGIN
CALL sp_guardar_precio_moneda(
	NEW.id_moneda,
    NEW.valor_moneda
);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_anomalos_materias_primas`
--

CREATE TABLE `movimientos_anomalos_materias_primas` (
  `id_movimiento_anomalo_materia_prima` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_anomalos_materias_primas`
--

INSERT INTO `movimientos_anomalos_materias_primas` (`id_movimiento_anomalo_materia_prima`, `id_materia_prima`, `cantidad_movimiento`, `tipo_movimiento`, `motivo_movimiento`, `fecha_movimiento`, `status`) VALUES
(1, 'MATE-26123-00001-66', 5, 1, 'llego', '2026-05-23 12:49:34', 1),
(2, 'MATE-26123-00001-66', 10, 1, 'asd', '2026-05-23 13:54:10', 1),
(3, 'MATE-26157-00001-95', 100, 1, 'jaja', '2026-06-20 22:01:28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_anomalos_productos`
--

CREATE TABLE `movimientos_anomalos_productos` (
  `id_movimiento_anomalo_producto` int(11) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_entregas_presupuestos`
--

CREATE TABLE `ordenes_entregas_presupuestos` (
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `fecha_orden_entrega_presupuesto` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes_entregas_presupuestos`
--

INSERT INTO `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`, `cedula_usuario`, `id_cambio_iva`, `rif_cedula_cliente`, `fecha_orden_entrega_presupuesto`, `status`) VALUES
('FACT-26179-00001-24', 'V30485684', 256, 'V30485684', '2026-06-29 18:09:11', 8),
('FACT-26179-00002-67', 'V30485684', 256, 'V30485684', '2026-06-29 18:13:42', 8),
('FACT-26180-00001-62', 'V30485684', 256, 'V30485684', '2026-06-30 16:36:35', 8),
('FACT-26181-00001-30', 'V30485684', 256, 'V30485684', '2026-07-01 11:24:44', 8),
('FACT-26183-00001-30', 'V30485688', 257, 'V30485688', '2026-07-03 06:44:09', 8),
('FACT-26183-00002-26', 'V30485688', 257, 'V30485688', '2026-07-03 06:55:02', 8),
('FACT-26190-00001-44', 'V30485684', 258, 'V30485684', '2026-07-10 00:47:24', 7),
('FACT-26221-00001-60', 'V30485684', 258, 'V30485684', '2026-08-10 17:44:30', 8),
('FACT-26227-00001-55', 'V30485684', 258, 'V30485631', '2026-08-16 19:00:02', 8),
('OEP-26258-00001-71', 'V30485684', 258, 'J310729425', '2026-09-16 23:16:37', 10),
('OEP-26258-00002-85', 'V30485684', 258, 'J314964291', '2026-09-16 23:24:24', 10),
('OEP-26259-00001-32', 'V30485684', 258, 'J310729425', '2026-09-17 10:08:40', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_orden_entrega_presupuesto`, `fecha_pago`, `status`) VALUES
('PAG-26179-00001-82', 'FACT-26179-00001-24', '2026-06-29 00:00:00', 1),
('PAG-26179-00002-96', 'FACT-26179-00002-67', '2026-06-29 00:00:00', 1),
('PAG-26180-00001-26', 'FACT-26180-00001-62', '2026-06-30 00:00:00', 1),
('PAG-26181-00001-57', 'FACT-26181-00001-30', '2026-07-01 00:00:00', 1),
('PAG-26183-00001-58', 'FACT-26183-00001-30', '2026-07-03 00:00:00', 1),
('PAG-26183-00002-83', 'FACT-26183-00002-26', '2026-07-03 00:00:00', 1),
('PAG-26190-00001-58', 'FACT-26190-00001-44', '2026-07-10 00:00:00', 1),
('PAG-26221-00001-44', 'FACT-26221-00001-60', '2026-08-10 00:00:00', 1),
('PAG-26227-00001-57', 'FACT-26227-00001-55', '2026-08-16 00:00:00', 1),
('PAG-26258-00001-00', 'OEP-26258-00001-71', '2026-09-16 23:17:04', 1),
('PAG-26259-00001-57', 'OEP-26258-00002-85', '2026-09-17 11:38:05', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_materias_primas`
--

CREATE TABLE `precios_materias_primas` (
  `id_precio_materia_prima` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_materias_primas`
--

INSERT INTO `precios_materias_primas` (`id_precio_materia_prima`, `id_materia_prima`, `precio_materia_prima`, `fecha_cambio`, `status`) VALUES
(1, 'MATE-26123-00001-66', 10.00, '2026-05-27 18:28:29', 1),
(2, 'MATE-26123-00001-66', 50.00, '2026-05-28 14:29:54', 1),
(4, 'MATE-26149-00001-45', 10.00, '2026-05-30 11:21:55', 1),
(5, 'MATE-26149-00002-90', 111.00, '2026-05-30 18:44:44', 1),
(46, 'MATE-26157-00001-95', 1.00, '2026-06-07 13:17:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_productos`
--

CREATE TABLE `precios_productos` (
  `id_precio_producto` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_productos`
--

INSERT INTO `precios_productos` (`id_precio_producto`, `id_producto`, `precio_producto`, `fecha_cambio`, `status`) VALUES
(42, 'PROD-26150-00001-39', 1.00, '2026-05-31 12:51:12', 0),
(63, 'PROD-26150-00001-39', 2.00, '2026-06-04 10:44:02', 0),
(65, 'PROD-26150-00001-39', 3.00, '2026-06-04 11:04:31', 0),
(74, 'PROD-26150-00001-39', 1.00, '2026-06-08 21:20:19', 0),
(86, 'PROD-26150-00001-39', 100.00, '2026-06-21 18:26:52', 0),
(87, 'PROD-26150-00001-39', 1.00, '2026-06-21 18:27:09', 0),
(89, 'PROD-26150-00001-39', 100.00, '2026-06-29 18:43:02', 0),
(90, 'PROD-26150-00001-39', 10000.00, '2026-06-29 18:43:11', 0),
(91, 'PROD-26150-00001-39', 1.00, '2026-06-30 15:53:05', 0),
(92, 'PROD-26150-00001-39', 10.00, '2026-07-03 07:47:04', 0),
(93, 'PROD-26150-00001-39', 100.00, '2026-07-03 07:47:27', 0),
(94, 'PROD-26183-00001-56', 1.00, '2026-07-03 07:49:40', 0),
(95, 'PROD-26150-00001-39', 1.00, '2026-07-03 11:12:43', 0),
(96, 'PROD-26222-00001-83', 1.00, '2026-08-12 00:22:42', 0),
(97, 'PROD-26222-00002-19', 1.00, '2026-08-12 00:37:05', 1),
(98, 'PROD-26222-00003-10', 1.00, '2026-08-12 00:37:59', 0),
(101, 'PROD-26222-00004-43', 1.00, '2026-08-12 00:48:20', 0),
(102, 'PROD-26222-00005-98', 1.00, '2026-08-12 00:48:53', 0),
(103, 'PROD-26222-00006-10', 10.00, '2026-08-12 00:50:58', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_rutas`
--

CREATE TABLE `precios_rutas` (
  `id_precio_ruta` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_rutas`
--

INSERT INTO `precios_rutas` (`id_precio_ruta`, `id_ruta`, `precio_ruta`, `fecha_cambio`, `status`) VALUES
(4, 9, 1.00, '2026-05-24 15:58:00', 1),
(5, 10, 2.00, '2026-05-24 15:58:22', 1),
(6, 11, 3.00, '2026-05-24 15:58:36', 1),
(7, 9, 1200.00, '2026-05-27 18:29:44', 1),
(43, 11, 5.00, '2026-06-04 10:04:22', 1),
(52, 11, 3.00, '2026-06-08 21:21:37', 1),
(53, 9, 0.50, '2026-06-08 21:21:46', 1),
(54, 10, 1.00, '2026-06-08 21:21:54', 1),
(55, 11, 2.00, '2026-06-08 21:22:00', 1),
(76, 71, 30.00, '2026-06-14 14:38:18', 1),
(77, 71, 3.00, '2026-06-14 14:38:26', 1),
(78, 71, 0.30, '2026-06-14 14:38:33', 1),
(79, 71, 30.00, '2026-06-14 14:39:44', 1),
(80, 71, 3000.00, '2026-06-14 14:39:59', 1),
(81, 71, 300.00, '2026-06-14 14:41:56', 1),
(82, 71, 30000.00, '2026-06-14 14:47:51', 1),
(83, 71, 3000.00, '2026-06-14 14:51:00', 1),
(84, 71, 3.00, '2026-06-14 14:52:57', 1),
(85, 72, 3.00, '2026-06-14 14:54:22', 1),
(86, 73, 10.00, '2026-06-24 18:37:18', 1),
(87, 73, 1000.00, '2026-06-24 18:37:26', 1),
(88, 73, 100.00, '2026-06-24 18:37:56', 1),
(89, 74, 1.00, '2026-06-30 16:28:05', 1),
(90, 75, 2.00, '2026-07-03 03:30:38', 1),
(91, 11, 20.00, '2026-07-03 06:59:53', 1),
(92, 11, 2.00, '2026-07-03 07:00:13', 1),
(93, 76, 100.00, '2026-07-03 07:00:37', 1),
(94, 77, 2.00, '2026-08-11 05:55:28', 1),
(95, 77, 0.20, '2026-08-11 05:55:38', 1),
(96, 78, 2.00, '2026-08-11 23:15:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_servicios`
--

CREATE TABLE `precios_servicios` (
  `id_precio_servicio` int(11) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_servicios`
--

INSERT INTO `precios_servicios` (`id_precio_servicio`, `id_servicio`, `precio_servicio`, `fecha_cambio`, `status`) VALUES
(2, 'SERV-26125-00001-08', 22.00, '2026-05-28 14:34:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id_presentacion` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id_presentacion`, `id_unidad_medida`, `nombre_presentacion`, `cantidad_pmp`, `status`) VALUES
('PRES-26123-00001-28', 2, 'LITRO', 1.00, 1),
('PRES-26159-00001-42', 2, 'BIDÓN', 20.00, 1),
('PRES-26159-00002-78', 2, 'PIPA', 200.00, 1),
('PRES-26159-00003-24', 2, 'GALÓN', 4.00, 1),
('PRES-26177-00001-19', 2, 'CAJA - 1L', 12.00, 1),
('PRES-26177-00002-40', 2, 'CAJA - GALÓN', 12.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones_materias_primas`
--

CREATE TABLE `presentaciones_materias_primas` (
  `id_materia_prima_presentacion` int(11) NOT NULL,
  `id_presentacion` varchar(20) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones_materias_primas`
--

INSERT INTO `presentaciones_materias_primas` (`id_materia_prima_presentacion`, `id_presentacion`, `id_materia_prima`, `status`) VALUES
(11, 'PRES-26123-00001-28', 'MATE-26123-00001-66', 1),
(17, 'PRES-26123-00001-28', 'MATE-26149-00002-90', 0),
(58, 'PRES-26123-00001-28', 'MATE-26149-00001-45', 0),
(59, 'PRES-26123-00001-28', 'MATE-26157-00001-95', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones_productos`
--

CREATE TABLE `presentaciones_productos` (
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_presentacion` varchar(20) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_presentacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones_productos`
--

INSERT INTO `presentaciones_productos` (`id_presentacion_producto`, `id_producto`, `id_presentacion`, `mostrar_ecommerce`, `foto_presentacion`, `status`) VALUES
('PRPR-26183-00007-41', 'PROD-26183-00001-56', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26183-00008-04', 'PROD-26183-00001-56', 'PRES-26159-00001-42', 1, '', 0),
('PRPR-26183-00009-24', 'PROD-26183-00001-56', 'PRES-26159-00002-78', 0, '', 0),
('PRPR-26183-00010-84', 'PROD-26183-00001-56', 'PRES-26159-00003-24', 1, '', 0),
('PRPR-26183-00011-58', 'PROD-26183-00001-56', 'PRES-26177-00001-19', 1, '', 0),
('PRPR-26183-00012-24', 'PROD-26183-00001-56', 'PRES-26177-00002-40', 0, '', 0),
('PRPR-26183-00013-65', 'PROD-26150-00001-39', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26183-00014-72', 'PROD-26150-00001-39', 'PRES-26159-00001-42', 1, '', 0),
('PRPR-26183-00015-86', 'PROD-26150-00001-39', 'PRES-26159-00002-78', 1, '', 0),
('PRPR-26183-00016-39', 'PROD-26150-00001-39', 'PRES-26159-00003-24', 1, '', 0),
('PRPR-26183-00017-07', 'PROD-26150-00001-39', 'PRES-26177-00001-19', 1, '', 0),
('PRPR-26183-00018-91', 'PROD-26150-00001-39', 'PRES-26177-00002-40', 1, '', 0),
('PRPR-26222-00001-40', 'PROD-26222-00001-83', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26222-00002-79', 'PROD-26222-00001-83', 'PRES-26159-00001-42', 1, '', 0),
('PRPR-26222-00003-78', 'PROD-26222-00001-83', 'PRES-26159-00002-78', 1, '', 0),
('PRPR-26222-00004-81', 'PROD-26222-00001-83', 'PRES-26159-00003-24', 0, '', 0),
('PRPR-26222-00005-06', 'PROD-26222-00001-83', 'PRES-26177-00001-19', 0, '', 0),
('PRPR-26222-00006-01', 'PROD-26222-00001-83', 'PRES-26177-00002-40', 0, '', 0),
('PRPR-26222-00007-32', 'PROD-26222-00002-19', 'PRES-26123-00001-28', 1, '', 1),
('PRPR-26222-00008-22', 'PROD-26222-00002-19', 'PRES-26159-00001-42', 1, '', 1),
('PRPR-26222-00009-69', 'PROD-26222-00002-19', 'PRES-26159-00002-78', 1, '', 1),
('PRPR-26222-00010-25', 'PROD-26222-00002-19', 'PRES-26159-00003-24', 1, '', 1),
('PRPR-26222-00011-11', 'PROD-26222-00002-19', 'PRES-26177-00001-19', 1, '', 1),
('PRPR-26222-00012-62', 'PROD-26222-00002-19', 'PRES-26177-00002-40', 1, '', 1),
('PRPR-26222-00013-45', 'PROD-26222-00003-10', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26222-00014-75', 'PROD-26222-00003-10', 'PRES-26159-00001-42', 0, '', 0),
('PRPR-26222-00015-48', 'PROD-26222-00003-10', 'PRES-26159-00002-78', 0, '', 0),
('PRPR-26222-00016-38', 'PROD-26222-00003-10', 'PRES-26159-00003-24', 0, '', 0),
('PRPR-26222-00017-82', 'PROD-26222-00003-10', 'PRES-26177-00001-19', 0, '', 0),
('PRPR-26222-00018-55', 'PROD-26222-00003-10', 'PRES-26177-00002-40', 0, '', 0),
('PRPR-26222-00025-15', 'PROD-26222-00005-98', 'PRES-26123-00001-28', 1, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00026-98', 'PROD-26222-00005-98', 'PRES-26159-00001-42', 0, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00027-61', 'PROD-26222-00005-98', 'PRES-26159-00002-78', 1, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00028-84', 'PROD-26222-00005-98', 'PRES-26159-00003-24', 0, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00029-56', 'PROD-26222-00005-98', 'PRES-26177-00001-19', 1, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00030-45', 'PROD-26222-00005-98', 'PRES-26177-00002-40', 0, 'presentaciones_productos_2026_08_11_12_48_53_82.png', 0),
('PRPR-26222-00031-33', 'PROD-26222-00006-10', 'PRES-26123-00001-28', 0, '', 0),
('PRPR-26222-00032-91', 'PROD-26222-00006-10', 'PRES-26159-00001-42', 0, 'presentaciones_productos_2026-08-11_12_51_14.png?v=2026-08-11_12_51_14', 0),
('PRPR-26222-00033-85', 'PROD-26222-00006-10', 'PRES-26159-00002-78', 0, '', 0),
('PRPR-26222-00034-70', 'PROD-26222-00006-10', 'PRES-26159-00003-24', 0, '', 0),
('PRPR-26222-00035-71', 'PROD-26222-00006-10', 'PRES-26177-00001-19', 0, '', 0),
('PRPR-26222-00036-95', 'PROD-26222-00006-10', 'PRES-26177-00002-40', 0, '', 0),
('PRPR-26222-00037-00', 'PROD-26222-00004-43', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26222-00038-12', 'PROD-26222-00004-43', 'PRES-26159-00002-78', 0, '', 0),
('PRPR-26222-00039-70', 'PROD-26222-00004-43', 'PRES-26159-00003-24', 1, 'presentaciones_productos_2026-08-11_13_57_40.png?v=2026-08-11_13_57_40', 0),
('PRPR-26222-00040-06', 'PROD-26222-00004-43', 'PRES-26177-00001-19', 1, '', 0),
('PRPR-26222-00041-20', 'PROD-26222-00004-43', 'PRES-26177-00002-40', 0, 'presentaciones_productos_2026-08-11_13_57_32.png?v=2026-08-11_13_57_32', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producciones`
--

CREATE TABLE `producciones` (
  `id_produccion` varchar(20) NOT NULL,
  `fecha_produccion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producciones`
--

INSERT INTO `producciones` (`id_produccion`, `fecha_produccion`, `status`) VALUES
('PROD-26125-00001-76', '2026-05-06 10:36:22', 1),
('PROD-26137-00001-39', '2026-05-18 09:22:25', 1),
('PROD-26149-00001-99', '2026-05-30 13:29:38', 1),
('PROD-26150-00001-73', '2026-05-31 12:56:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `stock_producto` decimal(20,2) NOT NULL,
  `stock_minimo_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_unidad_medida`, `id_categoria_producto`, `nombre_producto`, `precio_producto`, `stock_producto`, `stock_minimo_producto`, `status`) VALUES
('PROD-26150-00001-39', 2, 1, 'CLORO', 1.00, 600.00, 20.00, 0),
('PROD-26183-00001-56', 2, 1, 'CLORO2', 1.00, 100.00, 10.00, 0),
('PROD-26222-00001-83', 2, 1, 'CLORO2', 1.00, 100.00, 5.00, 0),
('PROD-26222-00002-19', 2, 1, 'CLORO', 1.00, 83.00, 5.00, 1),
('PROD-26222-00003-10', 2, 1, 'CLORO2', 1.00, 1.00, 5.00, 0),
('PROD-26222-00004-43', 2, 1, 'CLOROf', 1.00, 1.00, 5.00, 0),
('PROD-26222-00005-98', 2, 2, 'CLOROm', 1.00, 1.00, 5.00, 0),
('PROD-26222-00006-10', 2, 1, 'CLOROs', 10.00, 1.00, 5.00, 0);

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_producto_act` AFTER UPDATE ON `productos` FOR EACH ROW BEGIN
IF NEW.precio_producto <> OLD.precio_producto THEN
	CALL sp_guardar_precio_producto(
    	NEW.id_producto,
        NEW.precio_producto
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_producto_reg` AFTER INSERT ON `productos` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_producto(
    	NEW.id_producto,
        NEW.precio_producto
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_ordenes_entregas_presupuestos`
--

CREATE TABLE `productos_ordenes_entregas_presupuestos` (
  `id_producto_factura` int(11) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_ordenes_entregas_presupuestos`
--

INSERT INTO `productos_ordenes_entregas_presupuestos` (`id_producto_factura`, `id_orden_entrega_presupuesto`, `id_presentacion_producto`, `cantidad_producto`, `status`) VALUES
(327, 'FACT-26190-00001-44', 'PRPR-26183-00015-86', 1.00, 1),
(328, 'FACT-26221-00001-60', 'PRPR-26183-00015-86', 1.00, 1),
(329, 'FACT-26227-00001-55', 'PRPR-26222-00007-32', 1.00, 1),
(330, 'OEP-26258-00001-71', 'PRPR-26222-00012-62', 1.00, 1),
(331, 'OEP-26258-00002-85', 'PRPR-26222-00010-25', 1.00, 0),
(332, 'OEP-26259-00001-32', 'PRPR-26222-00011-11', 1.00, 1),
(333, 'OEP-26258-00002-85', 'PRPR-26222-00010-25', 1.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_producciones`
--

CREATE TABLE `productos_producciones` (
  `id_producto_produccion` int(11) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_producida` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_producciones`
--

INSERT INTO `productos_producciones` (`id_producto_produccion`, `id_produccion`, `id_producto`, `cantidad_producida`, `status`) VALUES
(105, 'PROD-26150-00001-73', 'PROD-26150-00001-39', 10.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_servicios`
--

CREATE TABLE `productos_servicios` (
  `id_producto_servicio` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `rif_proveedor` varchar(20) NOT NULL,
  `razon_social_proveedor` varchar(150) NOT NULL,
  `telefono_proveedor` varchar(11) NOT NULL,
  `correo_proveedor` varchar(150) NOT NULL,
  `direccion_proveedor` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`rif_proveedor`, `razon_social_proveedor`, `telefono_proveedor`, `correo_proveedor`, `direccion_proveedor`, `status`) VALUES
('123456789', 'ANDERSO', '04169484640', 'andersonfreitez60@gmail.com', 'HHHHBHBH', 0),
('1234567890', 'ANDERSONM', '04169484641', 'andersonfreitez6@gmail.com', 'DIRECCION', 0),
('30485684', 'ANDERSON', '04169484649', 'ANDERSONFREITEZ@GMAIL.COM', 'SANANA', 0),
('J085185330', 'Arcosan', '02512302345', 'Arcosanbarquisimeto@gamil.com', 'Calle 30 entre carreras 23 y 24 Barquisimeto Edo Lara.', 1),
('J12345611', 'ANDERSON9K', '04169484699', 'andersonfreitez6@gmail.co', 'kmkmkm', 0),
('J12345678', 'ANDERS', '04169484688', 'andersonfreitez9@gmail.com', 'eejejej', 0),
('J299682950', 'Mango Center', '', 'cajaprincipalcabudare@gmail.com', 'Av. Nectario Maria C.C Hipermercado Multimall Nivel Local del 02 al 08 Sector Tarabana Cabudare Edo. Lara', 1),
('J30485684', 'ANDERSON9', '04169484645', 'andersonfreitez61@gmail.co', 'jxjnjnj', 0),
('J4001234561', 'Comercializadora López 2018 C.A', '04245874877', 'Inversioneslopez@gmail.com', 'Carrera 24 entre calles 31 y 32. Barquisimeto Edo. Lara', 1),
('J4002345761', 'Ferretería Hermano Hernández', '04141347890', 'Ferreteriahernandez2020@Gmail.com', 'Calle 4 entre Av. La Mata y Av. 2 Cabudare Edo. Lara.', 1),
('J400602971', 'Químicos Alfonso', '', 'Adm.qalfonso@gmail.com', 'Calle 32 entre carreras 24 y 25 Barquisimeto Edo. Lara', 1),
('V1234567899', 'ANDERSONNN', '04169484647', 'andersonfreitez677@gmail.com', 'SANARE', 0),
('V30485684', 'ANDERSONNFA', '04169484648', 'andersonfreitez6@gmail.com', 'SANARESS', 0),
('V30485685', 'ANDERSON', '04169484640', 'andersonfreitez69@gmail.com', 'SANARE', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencias_detalles_pagos`
--

CREATE TABLE `referencias_detalles_pagos` (
  `id_referencia_detalle_pago` int(11) NOT NULL,
  `id_detalle_pago` int(11) NOT NULL,
  `referencia_pago` int(6) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `referencias_detalles_pagos`
--

INSERT INTO `referencias_detalles_pagos` (`id_referencia_detalle_pago`, `id_detalle_pago`, `referencia_pago`, `status`) VALUES
(213, 264, 123456, 1),
(214, 265, 123456, 1),
(215, 266, 123456, 1),
(216, 267, 123456, 1),
(217, 268, 123456, 1),
(218, 269, 123456, 1),
(219, 270, 123456, 1),
(220, 271, 123456, 1),
(221, 272, 123456, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidores`
--

CREATE TABLE `repartidores` (
  `cedula_repartidor` varchar(20) NOT NULL,
  `nombre_repartidor` varchar(50) NOT NULL,
  `apellido_repartidor` varchar(50) NOT NULL,
  `telefono_repartidor` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `repartidores`
--

INSERT INTO `repartidores` (`cedula_repartidor`, `nombre_repartidor`, `apellido_repartidor`, `telefono_repartidor`, `status`) VALUES
('V12344567', 'JOSE', 'JIMENEZ', '04161234567', 1),
('V30485654', 'ANDER', 'FREITEZ', '04169784646', 1),
('V30485683', 'ANDERSON', 'FREITEZ', '04169484648', 0),
('V30485684', 'ANDERSONn', 'FREITEZ', '04169484649', 1),
('V304856849', 'ANDERSON', 'FREITEZ', '04169484640', 0),
('V30485685', 'ANDERSON', 'FREITEZ', '04169484647', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(50) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `minimo_km_ruta` decimal(20,2) NOT NULL,
  `maximo_km_ruta` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_ruta`, `nombre_ruta`, `precio_ruta`, `minimo_km_ruta`, `maximo_km_ruta`, `status`) VALUES
(1, 'CERCANO', 1.50, 0.00, 5.00, 0),
(2, 'LEJANO', 2.50, 6.00, 10.00, 0),
(6, 'TERCERO', 3.50, 11.00, 100.00, 0),
(9, 'CERCANO', 0.50, 0.00, 5.00, 1),
(10, 'LEJANO', 1.00, 6.00, 10.00, 1),
(11, 'TERCERO', 2.00, 11.00, 100.00, 1),
(71, 'OTRA MAS', 3.00, 100.00, 1000.00, 0),
(72, 'OTRA MAS1', 3.00, 1.00, 100.00, 0),
(73, 'A DISTANCIA', 100.00, 100.00, 100.00, 0),
(74, 'UNO', 1.00, 10.00, 1.00, 0),
(75, 'otram', 2.00, 100.00, 100.00, 0),
(76, 'ANDER RUTA', 100.00, 100.00, 1000.00, 0),
(77, 'OTRA MAS', 0.20, 100.00, 100.00, 0),
(78, 'OTRA MAS', 2.00, 11.00, 100.00, 0);

--
-- Disparadores `rutas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_ruta_reg` AFTER INSERT ON `rutas` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_ruta(
    	NEW.id_ruta,
        NEW.precio_ruta
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_registrar_precio_ruta_act` AFTER UPDATE ON `rutas` FOR EACH ROW BEGIN
	IF NEW.precio_ruta <> OLD.precio_ruta THEN
	CALL sp_guardar_precio_ruta(
    	NEW.id_ruta,
        NEW.precio_ruta
    );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_servicio` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `id_unidad_medida`, `nombre_servicio`, `precio_servicio`, `mostrar_ecommerce`, `foto_servicio`, `status`) VALUES
('SERV-26125-00001-08', 2, 'FUMIGACION', 22.00, 0, '', 1);

--
-- Disparadores `servicios`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_servicio_act` AFTER UPDATE ON `servicios` FOR EACH ROW BEGIN
IF NEW.precio_servicio <> OLD.precio_servicio THEN
	CALL sp_guardar_precio_servicio(
    	NEW.id_servicio,
        NEW.precio_servicio
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_servicio_reg` AFTER INSERT ON `servicios` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_servicio(
    	NEW.id_servicio,
        NEW.precio_servicio
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_ordenes_entregas_presupuestos`
--

CREATE TABLE `servicios_ordenes_entregas_presupuestos` (
  `id_servicio_factura` int(11) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cantidad_servicio` decimal(20,2) NOT NULL,
  `fecha_ejecucion` datetime NOT NULL,
  `es_precio_mapfre` tinyint(1) NOT NULL,
  `precio_servicio_mapfre` decimal(20,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_ordenes_entregas_presupuestos`
--

INSERT INTO `servicios_ordenes_entregas_presupuestos` (`id_servicio_factura`, `id_orden_entrega_presupuesto`, `id_servicio`, `id_direccion`, `cantidad_servicio`, `fecha_ejecucion`, `es_precio_mapfre`, `precio_servicio_mapfre`, `status`) VALUES
(2, 'OEP-26258-00002-85', 'SERV-26125-00001-08', 165, 1.00, '2026-10-03 00:00:00', 0, 0.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales_empresas_envios`
--

CREATE TABLE `sucursales_empresas_envios` (
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `id_empresa_envios` int(11) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `nombre_sucursal_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales_empresas_envios`
--

INSERT INTO `sucursales_empresas_envios` (`id_sucursal_empresa_envios`, `id_empresa_envios`, `id_direccion`, `nombre_sucursal_empresa`, `status`) VALUES
(3, 6, 28, 'METROPOLIS', 0),
(4, 6, 28, 'METROPOLIS', 0),
(5, 6, 28, 'METROPOLISA', 0),
(6, 6, 28, 'METROPOLIS', 0),
(7, 6, 30, 'BRM', 1),
(8, 6, 31, 'AV INDUSTRIAS', 1),
(9, 6, 32, 'LARAPLACE', 1),
(10, 6, 33, 'AEROPUERTO JACINTO LARA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medidas`
--

CREATE TABLE `unidades_medidas` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_unidad_medida` varchar(50) NOT NULL,
  `simbolo_unidad_medida` varchar(3) NOT NULL,
  `equivalencia_ub` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_medidas`
--

INSERT INTO `unidades_medidas` (`id_unidad_medida`, `nombre_unidad_medida`, `simbolo_unidad_medida`, `equivalencia_ub`, `status`) VALUES
(1, 'KILO(S)', 'KG', 1000.00, 1),
(2, 'LITRO(S)', 'L', 1000.00, 1),
(3, 'MILLA', 'ML', 1000000.00, 0),
(4, 'MILL', 'ML', 1000000.00, 0),
(5, 'MILLA', 'M', 1000000.00, 0),
(6, 'MILLM', 'MIL', 1000000.00, 0),
(7, 'MILLA', 'ML', 2.00, 0),
(8, 'MILLAS', 'MLA', 1000000.00, 0),
(9, 'METRO CUADRADO', 'M2', 1000.00, 1),
(10, 'METROM', 'M', 1000.00, 0),
(11, 'METRO CUADRADOm', 'M2m', 1000.00, 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cambios_iva_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cambios_iva_todos` (
`id_cambio_iva` int(11)
,`monto_cambio_iva` decimal(20,2)
,`fecha_cambio_iva` datetime
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cambios_monedas_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cambios_monedas_todos` (
`id_cambio_moneda` int(11)
,`nombre_moneda` varchar(20)
,`valor_moneda` decimal(20,2)
,`fecha_cambio` datetime
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_categorias_productos_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_categorias_productos_todas` (
`id_categoria_producto` int(11)
,`nombre_categoria_producto` varchar(50)
,`necesitan_materias_primas` tinyint(1)
,`status` tinyint(1)
,`cantidad_productos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_clientes_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_clientes_todos` (
`rif_cedula_cliente` varchar(20)
,`razon_social_cliente` varchar(100)
,`telefono_cliente` varchar(11)
,`correo_cliente` varchar(150)
,`direccion_cliente` varchar(255)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_empresas_envios_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_empresas_envios_todas` (
`id_empresa_envios` int(11)
,`nombre_empresa` varchar(50)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_repartidores_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_repartidores_todos` (
`cedula_repartidor` varchar(20)
,`nombre_repartidor` varchar(50)
,`apellido_repartidor` varchar(50)
,`telefono_repartidor` varchar(11)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_unidades_medidas_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_unidades_medidas_todas` (
`id_unidad_medida` int(11)
,`nombre_unidad_medida` varchar(50)
,`simbolo_unidad_medida` varchar(3)
,`equivalencia_ub` decimal(20,2)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cambios_iva_todos`
--
DROP TABLE IF EXISTS `v_cambios_iva_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cambios_iva_todos`  AS SELECT `cambios_iva`.`id_cambio_iva` AS `id_cambio_iva`, `cambios_iva`.`monto_cambio_iva` AS `monto_cambio_iva`, `cambios_iva`.`fecha_cambio_iva` AS `fecha_cambio_iva`, `cambios_iva`.`status` AS `status` FROM `cambios_iva` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cambios_monedas_todos`
--
DROP TABLE IF EXISTS `v_cambios_monedas_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cambios_monedas_todos`  AS SELECT `cm`.`id_cambio_moneda` AS `id_cambio_moneda`, `mo`.`nombre_moneda` AS `nombre_moneda`, `cm`.`valor_moneda` AS `valor_moneda`, `cm`.`fecha_cambio` AS `fecha_cambio`, `cm`.`status` AS `status` FROM (`cambios_monedas` `cm` join `monedas` `mo` on(`cm`.`id_moneda` = `mo`.`id_moneda`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_categorias_productos_todas`
--
DROP TABLE IF EXISTS `v_categorias_productos_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_categorias_productos_todas`  AS SELECT `c`.`id_categoria_producto` AS `id_categoria_producto`, `c`.`nombre_categoria_producto` AS `nombre_categoria_producto`, `c`.`necesitan_materias_primas` AS `necesitan_materias_primas`, `c`.`status` AS `status`, count(`p`.`id_producto`) AS `cantidad_productos` FROM (`categorias_productos` `c` left join `productos` `p` on(`c`.`id_categoria_producto` = `p`.`id_categoria_producto` and `p`.`status` = 1)) GROUP BY `c`.`id_categoria_producto` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_clientes_todos`
--
DROP TABLE IF EXISTS `v_clientes_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clientes_todos`  AS SELECT `clientes`.`rif_cedula_cliente` AS `rif_cedula_cliente`, `clientes`.`razon_social_cliente` AS `razon_social_cliente`, `clientes`.`telefono_cliente` AS `telefono_cliente`, `clientes`.`correo_cliente` AS `correo_cliente`, `clientes`.`direccion_cliente` AS `direccion_cliente`, `clientes`.`status` AS `status` FROM `clientes` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_empresas_envios_todas`
--
DROP TABLE IF EXISTS `v_empresas_envios_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_empresas_envios_todas`  AS SELECT `empresas_envios`.`id_empresa_envios` AS `id_empresa_envios`, `empresas_envios`.`nombre_empresa` AS `nombre_empresa`, `empresas_envios`.`status` AS `status` FROM `empresas_envios` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_repartidores_todos`
--
DROP TABLE IF EXISTS `v_repartidores_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_repartidores_todos`  AS SELECT `repartidores`.`cedula_repartidor` AS `cedula_repartidor`, `repartidores`.`nombre_repartidor` AS `nombre_repartidor`, `repartidores`.`apellido_repartidor` AS `apellido_repartidor`, `repartidores`.`telefono_repartidor` AS `telefono_repartidor`, `repartidores`.`status` AS `status` FROM `repartidores` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_unidades_medidas_todas`
--
DROP TABLE IF EXISTS `v_unidades_medidas_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_unidades_medidas_todas`  AS SELECT `unidades_medidas`.`id_unidad_medida` AS `id_unidad_medida`, `unidades_medidas`.`nombre_unidad_medida` AS `nombre_unidad_medida`, `unidades_medidas`.`simbolo_unidad_medida` AS `simbolo_unidad_medida`, `unidades_medidas`.`equivalencia_ub` AS `equivalencia_ub`, `unidades_medidas`.`status` AS `status` FROM `unidades_medidas` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indices de la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  ADD PRIMARY KEY (`id_banco_detalle_pago`),
  ADD KEY `id_detalle_pago_bancos_detalles_pagos_fk` (`id_detalle_pago`),
  ADD KEY `id_banco_bancos_detalles_pagos_fk` (`id_banco`);

--
-- Indices de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  ADD PRIMARY KEY (`id_cambio_iva`),
  ADD KEY `fecha_cambio_iva_indice` (`fecha_cambio_iva`);

--
-- Indices de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD PRIMARY KEY (`id_cambio_moneda`),
  ADD KEY `id_moneda_cambios_monedas_fk` (`id_moneda`),
  ADD KEY `fecha_cambio_indice` (`fecha_cambio`) USING BTREE;

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id_categoria_producto`);

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
  ADD KEY `rif_proveedor_compras_fk` (`rif_proveedor`),
  ADD KEY `fecha_compra_indice` (`fecha_compra`) USING BTREE;

--
-- Indices de la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  ADD PRIMARY KEY (`id_comprobante_pago`),
  ADD KEY `id_pago_comprobantes_pagos_fk` (`id_pago`);

--
-- Indices de la tabla `control_ordenes_entregas_presupuestos`
--
ALTER TABLE `control_ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`rif_cliente`);

--
-- Indices de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id_delivery`),
  ADD KEY `id_direccion_deliveries_fk` (`id_direccion`),
  ADD KEY `cedula_repartidor_deliveries_fk` (`cedula_repartidor`),
  ADD KEY `id_orden_entrega_presupuesto_delivery_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD PRIMARY KEY (`id_detalle_pago`),
  ADD KEY `id_pago_detalles_pagos_fk` (`id_pago`),
  ADD KEY `id_metodo_pago_detalles_pagos_fk` (`id_metodo_pago`),
  ADD KEY `id_moneda_detalles_pagos_fk` (`id_moneda`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_ruta_direcciones_fk` (`id_ruta`),
  ADD KEY `id_latitud_direccion_direcciones_fk` (`id_latitud_direccion`),
  ADD KEY `id_longitud_direccion_direcciones_fk` (`id_longitud_direccion`);

--
-- Indices de la tabla `empresas_envios`
--
ALTER TABLE `empresas_envios`
  ADD PRIMARY KEY (`id_empresa_envios`);

--
-- Indices de la tabla `envios_terceros`
--
ALTER TABLE `envios_terceros`
  ADD PRIMARY KEY (`id_envio_tercero`),
  ADD KEY `id_sucursal_empresa_envios_envios_terceros_fk` (`id_sucursal_empresa_envios`),
  ADD KEY `id_orden_entrega_presupuesto_envios_terceros_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `latitudes_direcciones`
--
ALTER TABLE `latitudes_direcciones`
  ADD PRIMARY KEY (`id_latitud_direccion`);

--
-- Indices de la tabla `longitudes_direcciones`
--
ALTER TABLE `longitudes_direcciones`
  ADD PRIMARY KEY (`id_longitud_direccion`);

--
-- Indices de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD PRIMARY KEY (`id_materia_prima`),
  ADD KEY `id_unidad_medida_materias_primas_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD PRIMARY KEY (`id_materia_prima_compra`),
  ADD KEY `id_materia_prima_materias_primas_compras_fk` (`id_materia_prima`),
  ADD KEY `id_compra_materias_primas_compras_fk` (`id_compra`);

--
-- Indices de la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  ADD PRIMARY KEY (`id_materia_prima_produccion`),
  ADD KEY `id_materia_prima_materias_primas_producciones_fk` (`id_materia_prima`),
  ADD KEY `id_produccion_materias_primas_producciones_fk` (`id_produccion`);

--
-- Indices de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD PRIMARY KEY (`id_materia_prima_producto`),
  ADD KEY `id_materia_prima_materias_primas_productos_fk` (`id_materia_prima`),
  ADD KEY `id_producto_materias_primas_productos_fk` (`id_producto`);

--
-- Indices de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  ADD PRIMARY KEY (`id_metodo_pago`);

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indices de la tabla `movimientos_anomalos_materias_primas`
--
ALTER TABLE `movimientos_anomalos_materias_primas`
  ADD PRIMARY KEY (`id_movimiento_anomalo_materia_prima`),
  ADD KEY `id_materia_prima_movimientos_anomalos_materias_primas_fk` (`id_materia_prima`);

--
-- Indices de la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  ADD PRIMARY KEY (`id_movimiento_anomalo_producto`),
  ADD KEY `id_presentacion_producto_movimientos_anomalos_productos_fk` (`id_presentacion_producto`);

--
-- Indices de la tabla `ordenes_entregas_presupuestos`
--
ALTER TABLE `ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_orden_entrega_presupuesto`),
  ADD KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  ADD KEY `id_cambio_iva_venta` (`id_cambio_iva`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta_pago_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  ADD PRIMARY KEY (`id_precio_materia_prima`),
  ADD KEY `id_materia_prima_precios_materias_primas_fk` (`id_materia_prima`);

--
-- Indices de la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  ADD PRIMARY KEY (`id_precio_producto`),
  ADD KEY `id_producto_precios_productos_fk` (`id_producto`);

--
-- Indices de la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  ADD PRIMARY KEY (`id_precio_ruta`),
  ADD KEY `id_ruta_precios_rutas_fk` (`id_ruta`);

--
-- Indices de la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  ADD PRIMARY KEY (`id_precio_servicio`),
  ADD KEY `id_servicio_precios_servicios_fk` (`id_servicio`);

--
-- Indices de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD PRIMARY KEY (`id_presentacion`),
  ADD KEY `id_unidad_medida_presentaciones_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  ADD PRIMARY KEY (`id_materia_prima_presentacion`),
  ADD KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  ADD KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`);

--
-- Indices de la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  ADD PRIMARY KEY (`id_presentacion_producto`),
  ADD KEY `id_presentacion_productos_presentaciones_fk` (`id_presentacion`),
  ADD KEY `id_producto_productos_presentaciones_fk` (`id_producto`);

--
-- Indices de la tabla `producciones`
--
ALTER TABLE `producciones`
  ADD PRIMARY KEY (`id_produccion`),
  ADD KEY `fecha_produccion_indice` (`fecha_produccion`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_unidad_medida_productos_fk` (`id_unidad_medida`),
  ADD KEY `id_categoria_producto_productos_fk` (`id_categoria_producto`);

--
-- Indices de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD PRIMARY KEY (`id_producto_compra`),
  ADD KEY `id_producto_productos_compras_fk` (`id_presentacion_producto`),
  ADD KEY `id_compra_productos_compras_fk` (`id_compra`);

--
-- Indices de la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_producto_factura`),
  ADD KEY `id_presentacion_producto_poep_fk` (`id_presentacion_producto`),
  ADD KEY `id_orden_entrega_presupuesto_poep_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD PRIMARY KEY (`id_producto_produccion`),
  ADD KEY `id_produccion_productos_producciones_fk` (`id_produccion`),
  ADD KEY `id_producto_productos_producciones_fk` (`id_producto`);

--
-- Indices de la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD PRIMARY KEY (`id_producto_servicio`),
  ADD KEY `id_producto_productos_soep_fk` (`id_producto`),
  ADD KEY `id_servicio_factura_psoep_fk` (`id_servicio`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`rif_proveedor`);

--
-- Indices de la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  ADD PRIMARY KEY (`id_referencia_detalle_pago`),
  ADD KEY `id_detalle_pago_referencias_detalles_pagos_fk` (`id_detalle_pago`);

--
-- Indices de la tabla `repartidores`
--
ALTER TABLE `repartidores`
  ADD PRIMARY KEY (`cedula_repartidor`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`id_ruta`),
  ADD KEY `minimo_km_ruta_indice` (`minimo_km_ruta`),
  ADD KEY `maximo_km_ruta_indice` (`maximo_km_ruta`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_servicio_factura`),
  ADD KEY `id_orden_entrega_presupuesto_soep_fk` (`id_orden_entrega_presupuesto`),
  ADD KEY `id_servicio_soep_fk` (`id_servicio`),
  ADD KEY `id_direccion_soep_fk` (`id_direccion`);

--
-- Indices de la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  ADD PRIMARY KEY (`id_sucursal_empresa_envios`),
  ADD KEY `id_empresa_envios_sucursales_empresas_envios_fk` (`id_empresa_envios`),
  ADD KEY `id_direccion_sucursales_empresas_envios_fk` (`id_direccion`);

--
-- Indices de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2878;

--
-- AUTO_INCREMENT de la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  MODIFY `id_banco_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  MODIFY `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  MODIFY `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id_categoria_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  MODIFY `id_comprobante_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  MODIFY `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT de la tabla `empresas_envios`
--
ALTER TABLE `empresas_envios`
  MODIFY `id_empresa_envios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `latitudes_direcciones`
--
ALTER TABLE `latitudes_direcciones`
  MODIFY `id_latitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT de la tabla `longitudes_direcciones`
--
ALTER TABLE `longitudes_direcciones`
  MODIFY `id_longitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  MODIFY `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  MODIFY `id_materia_prima_produccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  MODIFY `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `movimientos_anomalos_materias_primas`
--
ALTER TABLE `movimientos_anomalos_materias_primas`
  MODIFY `id_movimiento_anomalo_materia_prima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  MODIFY `id_movimiento_anomalo_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  MODIFY `id_precio_materia_prima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  MODIFY `id_precio_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  MODIFY `id_precio_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  MODIFY `id_precio_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  MODIFY `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  MODIFY `id_producto_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  MODIFY `id_producto_produccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT de la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  MODIFY `id_producto_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  MODIFY `id_referencia_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  MODIFY `id_servicio_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  MODIFY `id_sucursal_empresa_envios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  ADD CONSTRAINT `id_banco_bancos_detalles_pagos_fk` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_detalle_pago_bancos_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD CONSTRAINT `id_moneda_cambios_monedas_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `rif_proveedor_compras_fk` FOREIGN KEY (`rif_proveedor`) REFERENCES `proveedores` (`rif_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  ADD CONSTRAINT `id_pago_comprobantes_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `control_ordenes_entregas_presupuestos`
--
ALTER TABLE `control_ordenes_entregas_presupuestos`
  ADD CONSTRAINT `control_ordenes_entregas_presupuestos_ibfk_1` FOREIGN KEY (`rif_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `cedula_repartidor_deliveries_fk` FOREIGN KEY (`cedula_repartidor`) REFERENCES `repartidores` (`cedula_repartidor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_direccion_deliveries_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_orden_entrega_presupuesto_delivery_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD CONSTRAINT `id_metodo_pago_detalles_pagos_fk` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pagos` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_moneda_detalles_pagos_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pago_detalles_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `id_latitud_direccion_direcciones_fk` FOREIGN KEY (`id_latitud_direccion`) REFERENCES `latitudes_direcciones` (`id_latitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_longitud_direccion_direcciones_fk` FOREIGN KEY (`id_longitud_direccion`) REFERENCES `longitudes_direcciones` (`id_longitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_ruta_direcciones_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `envios_terceros`
--
ALTER TABLE `envios_terceros`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_envios_terceros_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_sucursal_empresa_envios_envios_terceros_fk` FOREIGN KEY (`id_sucursal_empresa_envios`) REFERENCES `sucursales_empresas_envios` (`id_sucursal_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD CONSTRAINT `id_unidad_medida_materias_primas_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD CONSTRAINT `id_compra_materias_primas_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_materia_prima_materias_primas_compras_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  ADD CONSTRAINT `id_materia_prima_materias_primas_producciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_produccion_materias_primas_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD CONSTRAINT `id_materia_prima_materias_primas_productos_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_materias_primas_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_anomalos_materias_primas`
--
ALTER TABLE `movimientos_anomalos_materias_primas`
  ADD CONSTRAINT `id_materia_prima_movimientos_anomalos_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  ADD CONSTRAINT `id_presentacion_producto_movimientos_anomalos_productos_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ordenes_entregas_presupuestos`
--
ALTER TABLE `ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_pagos_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  ADD CONSTRAINT `id_materia_prima_precios_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  ADD CONSTRAINT `id_producto_precios_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  ADD CONSTRAINT `id_ruta_precios_rutas_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  ADD CONSTRAINT `id_servicio_precios_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD CONSTRAINT `id_unidad_medida_presentaciones_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  ADD CONSTRAINT `id_materia_prima_materias_primas_presentaciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_materias_primas_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  ADD CONSTRAINT `id_presentacion_productos_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_presentaciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `id_categoria_producto_productos_fk` FOREIGN KEY (`id_categoria_producto`) REFERENCES `categorias_productos` (`id_categoria_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_unidad_medida_productos_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD CONSTRAINT `id_compra_productos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_producto_productos_compras_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_poep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_producto_poep_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD CONSTRAINT `id_produccion_productos_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_producciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD CONSTRAINT `id_producto_productos_soep_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_serivicio_productos_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  ADD CONSTRAINT `id_detalle_pago_referencias_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `id_unidad_medida_servicios_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_direccion_soep_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_orden_entrega_presupuesto_soep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_soep_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  ADD CONSTRAINT `id_direccion_sucursales_empresas_envios_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_empresa_envios_sucursales_empresas_envios_fk` FOREIGN KEY (`id_empresa_envios`) REFERENCES `empresas_envios` (`id_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
