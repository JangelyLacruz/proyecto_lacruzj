-- ============================================
-- Backup: proyecto_lacruz + proyecto_lacruz_seguridad
-- Fecha: 2026-09-18 12:58:32
-- ============================================

SET FOREIGN_KEY_CHECKS=0;


-- ============================================
-- BASE DE DATOS: proyecto_lacruz
-- ============================================

CREATE DATABASE IF NOT EXISTS `proyecto_lacruz`;
USE `proyecto_lacruz`;

-- Tabla: bancos
DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=2878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `bancos` WRITE;
INSERT INTO `bancos` VALUES ('1', 'VENEZUELA', '0');
INSERT INTO `bancos` VALUES ('2', 'BANCO PROVINCIAL', '0');
INSERT INTO `bancos` VALUES ('93', 'bando de ejemplo', '0');
INSERT INTO `bancos` VALUES ('2849', 'PROVINCIAL', '0');
INSERT INTO `bancos` VALUES ('2850', 'MENCÁNTIL', '0');
INSERT INTO `bancos` VALUES ('2851', 'BANESCO', '0');
INSERT INTO `bancos` VALUES ('2852', 'BICENTENARIO', '0');
INSERT INTO `bancos` VALUES ('2853', '0102 - BANCO DE VENEZUELA', '1');
INSERT INTO `bancos` VALUES ('2854', '0104 - BANCO VENEZOLANO DE CRÉDITO', '1');
INSERT INTO `bancos` VALUES ('2855', '0105 - BANCO MERCÁNTIL', '1');
INSERT INTO `bancos` VALUES ('2856', '0108 - BBVA PROVINCIAL', '1');
INSERT INTO `bancos` VALUES ('2857', '0114 - BANCARIBE', '1');
INSERT INTO `bancos` VALUES ('2858', '0115 - BANCO EXTERIOR', '1');
INSERT INTO `bancos` VALUES ('2859', '0128 - BANCO CARONÍ', '1');
INSERT INTO `bancos` VALUES ('2860', '0134 - BANESCO', '1');
INSERT INTO `bancos` VALUES ('2861', '0137 - BANCO SOFITASA', '1');
INSERT INTO `bancos` VALUES ('2862', '0138 - BANCO PLAZA', '1');
INSERT INTO `bancos` VALUES ('2863', '0146 - BANGENTE', '1');
INSERT INTO `bancos` VALUES ('2864', '0151 - BANCO FONDO COMÚN', '1');
INSERT INTO `bancos` VALUES ('2865', '0156 - 100% BANCO', '1');
INSERT INTO `bancos` VALUES ('2866', '0157 - DELSUR BANCO UNIVERSAL', '1');
INSERT INTO `bancos` VALUES ('2867', '0163 - BANCO DEL TESORO', '1');
INSERT INTO `bancos` VALUES ('2868', '0168 - BANCRECER', '1');
INSERT INTO `bancos` VALUES ('2869', '0169 - R4 BANCO MICROFINANCIERO C.A.', '1');
INSERT INTO `bancos` VALUES ('2870', '0171 - BANCO ACTIVO', '1');
INSERT INTO `bancos` VALUES ('2871', '0172 - BANCAMIGA BANCO UNIVERSAL', '1');
INSERT INTO `bancos` VALUES ('2872', '0174 - BAMPLUS', '1');
INSERT INTO `bancos` VALUES ('2873', '0175 - BANCO DIGITAL DE LOS TRABAJADORES', '1');
INSERT INTO `bancos` VALUES ('2874', '0177 - BANFANB', '1');
INSERT INTO `bancos` VALUES ('2875', '0178 - N58 BANCO DIGITAL MICROFINANCIERO S.A.', '1');
INSERT INTO `bancos` VALUES ('2876', '0191 - BANCO NACIONAL DE CRÉDITO', '1');
INSERT INTO `bancos` VALUES ('2877', '0601 - INSTITUTO NACIONAL DE CRÉDITO POPULAR', '1');
UNLOCK TABLES;

-- Tabla: bancos_detalles_pagos
DROP TABLE IF EXISTS `bancos_detalles_pagos`;
CREATE TABLE `bancos_detalles_pagos` (
  `id_banco_detalle_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_detalle_pago` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `es_emisor` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_banco_detalle_pago`),
  KEY `id_detalle_pago_bancos_detalles_pagos_fk` (`id_detalle_pago`),
  KEY `id_banco_bancos_detalles_pagos_fk` (`id_banco`),
  CONSTRAINT `id_banco_bancos_detalles_pagos_fk` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_detalle_pago_bancos_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `bancos_detalles_pagos` WRITE;
INSERT INTO `bancos_detalles_pagos` VALUES ('232', '273', '2865', '1', '1');
INSERT INTO `bancos_detalles_pagos` VALUES ('233', '273', '2868', '0', '1');
UNLOCK TABLES;

-- Tabla: cambios_iva
DROP TABLE IF EXISTS `cambios_iva`;
CREATE TABLE `cambios_iva` (
  `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT,
  `monto_cambio_iva` decimal(20,2) NOT NULL,
  `fecha_cambio_iva` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_cambio_iva`),
  KEY `fecha_cambio_iva_indice` (`fecha_cambio_iva`)
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `cambios_iva` WRITE;
INSERT INTO `cambios_iva` VALUES ('1', '12.00', '2025-12-05 18:15:15', '1');
INSERT INTO `cambios_iva` VALUES ('129', '12.00', '2026-04-24 15:34:11', '1');
INSERT INTO `cambios_iva` VALUES ('130', '12.00', '2026-04-24 15:34:17', '1');
INSERT INTO `cambios_iva` VALUES ('131', '12.00', '2026-04-24 15:36:14', '1');
INSERT INTO `cambios_iva` VALUES ('132', '12.00', '2026-04-24 15:36:50', '1');
INSERT INTO `cambios_iva` VALUES ('133', '12.00', '2026-04-24 15:36:54', '1');
INSERT INTO `cambios_iva` VALUES ('134', '12.00', '2026-04-24 15:41:00', '1');
INSERT INTO `cambios_iva` VALUES ('135', '24.00', '2026-04-24 15:46:47', '1');
INSERT INTO `cambios_iva` VALUES ('136', '12.00', '2026-04-24 15:49:02', '1');
INSERT INTO `cambios_iva` VALUES ('137', '122.00', '2026-05-05 20:28:20', '1');
INSERT INTO `cambios_iva` VALUES ('138', '16.00', '2026-05-17 10:12:05', '1');
INSERT INTO `cambios_iva` VALUES ('161', '77.00', '2026-05-25 11:55:35', '1');
INSERT INTO `cambios_iva` VALUES ('162', '15.00', '2026-05-25 12:00:18', '1');
INSERT INTO `cambios_iva` VALUES ('163', '19.00', '2026-05-25 12:05:19', '1');
INSERT INTO `cambios_iva` VALUES ('164', '78.00', '2026-05-25 12:06:32', '1');
INSERT INTO `cambios_iva` VALUES ('165', '15.00', '2026-05-25 12:07:45', '1');
INSERT INTO `cambios_iva` VALUES ('166', '15.00', '2026-05-25 12:18:15', '1');
INSERT INTO `cambios_iva` VALUES ('168', '12.00', '2026-05-25 12:48:10', '1');
INSERT INTO `cambios_iva` VALUES ('178', '23.00', '2026-05-25 13:22:39', '1');
INSERT INTO `cambios_iva` VALUES ('179', '12.00', '2026-05-25 13:23:25', '1');
INSERT INTO `cambios_iva` VALUES ('180', '15.00', '2026-05-25 13:24:59', '1');
INSERT INTO `cambios_iva` VALUES ('181', '23.00', '2026-05-25 13:30:57', '1');
INSERT INTO `cambios_iva` VALUES ('182', '78.00', '2026-05-25 13:32:03', '1');
INSERT INTO `cambios_iva` VALUES ('183', '89.00', '2026-05-25 13:32:20', '1');
INSERT INTO `cambios_iva` VALUES ('186', '34.00', '2026-05-25 13:44:22', '1');
INSERT INTO `cambios_iva` VALUES ('187', '12.00', '2026-05-25 13:49:17', '1');
INSERT INTO `cambios_iva` VALUES ('188', '15.00', '2026-05-25 13:49:48', '1');
INSERT INTO `cambios_iva` VALUES ('189', '16.00', '2026-05-25 19:41:09', '1');
INSERT INTO `cambios_iva` VALUES ('190', '18.00', '2026-05-25 19:42:29', '1');
INSERT INTO `cambios_iva` VALUES ('191', '20.00', '2026-05-26 20:46:10', '1');
INSERT INTO `cambios_iva` VALUES ('192', '22.00', '2026-05-26 20:47:31', '1');
INSERT INTO `cambios_iva` VALUES ('195', '24.00', '2026-05-26 20:49:45', '1');
INSERT INTO `cambios_iva` VALUES ('197', '16.00', '2026-05-29 08:10:36', '1');
INSERT INTO `cambios_iva` VALUES ('198', '20.00', '2026-05-29 08:17:38', '1');
INSERT INTO `cambios_iva` VALUES ('199', '16.00', '2026-05-29 08:18:08', '1');
INSERT INTO `cambios_iva` VALUES ('200', '23.00', '2026-05-29 08:19:05', '1');
INSERT INTO `cambios_iva` VALUES ('201', '24.00', '2026-05-29 08:20:54', '1');
INSERT INTO `cambios_iva` VALUES ('234', '1.00', '2026-06-09 00:56:53', '1');
INSERT INTO `cambios_iva` VALUES ('235', '1250000000000.00', '2026-06-13 16:27:13', '1');
INSERT INTO `cambios_iva` VALUES ('236', '1250000000000.00', '2026-06-13 16:27:13', '1');
INSERT INTO `cambios_iva` VALUES ('237', '1250.00', '2026-06-13 16:28:00', '1');
INSERT INTO `cambios_iva` VALUES ('238', '1250.00', '2026-06-13 16:28:00', '1');
INSERT INTO `cambios_iva` VALUES ('239', '1250.50', '2026-06-13 16:28:06', '1');
INSERT INTO `cambios_iva` VALUES ('240', '1250.50', '2026-06-13 16:28:06', '1');
INSERT INTO `cambios_iva` VALUES ('250', '77.00', '2026-06-13 16:47:54', '1');
INSERT INTO `cambios_iva` VALUES ('251', '77.00', '2026-06-13 16:48:00', '1');
INSERT INTO `cambios_iva` VALUES ('252', '87.00', '2026-06-13 16:48:18', '1');
INSERT INTO `cambios_iva` VALUES ('253', '16.00', '2026-06-13 19:30:36', '1');
INSERT INTO `cambios_iva` VALUES ('254', '78.00', '2026-06-20 22:10:21', '1');
INSERT INTO `cambios_iva` VALUES ('256', '16.00', '2026-06-27 13:41:17', '1');
INSERT INTO `cambios_iva` VALUES ('257', '22.00', '2026-07-02 22:10:39', '1');
INSERT INTO `cambios_iva` VALUES ('258', '16.00', '2026-07-09 13:28:30', '1');
UNLOCK TABLES;

-- Tabla: cambios_monedas
DROP TABLE IF EXISTS `cambios_monedas`;
CREATE TABLE `cambios_monedas` (
  `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT,
  `id_moneda` int(11) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_cambio_moneda`),
  KEY `id_moneda_cambios_monedas_fk` (`id_moneda`),
  KEY `fecha_cambio_indice` (`fecha_cambio`) USING BTREE,
  CONSTRAINT `id_moneda_cambios_monedas_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `cambios_monedas` WRITE;
INSERT INTO `cambios_monedas` VALUES ('1', '1', '17.00', '2025-12-06 20:12:19', '1');
INSERT INTO `cambios_monedas` VALUES ('2', '2', '2.00', '2025-12-06 20:13:37', '1');
INSERT INTO `cambios_monedas` VALUES ('3', '3', '3.00', '2025-12-06 20:13:48', '1');
INSERT INTO `cambios_monedas` VALUES ('4', '1', '257.93', '2025-12-06 20:14:15', '1');
INSERT INTO `cambios_monedas` VALUES ('5', '1', '250.00', '2025-12-11 18:35:07', '1');
INSERT INTO `cambios_monedas` VALUES ('6', '3', '250.00', '2025-12-11 19:04:46', '1');
INSERT INTO `cambios_monedas` VALUES ('7', '2', '1.00', '2025-12-14 16:30:34', '1');
INSERT INTO `cambios_monedas` VALUES ('8', '1', '23.00', '2026-02-07 20:44:34', '1');
INSERT INTO `cambios_monedas` VALUES ('9', '1', '12.00', '2026-02-09 20:46:09', '1');
INSERT INTO `cambios_monedas` VALUES ('10', '1', '250.00', '2026-02-09 20:46:55', '1');
INSERT INTO `cambios_monedas` VALUES ('11', '1', '279.00', '2026-03-30 10:22:47', '1');
INSERT INTO `cambios_monedas` VALUES ('12', '2', '22.00', '2026-03-30 10:22:56', '1');
INSERT INTO `cambios_monedas` VALUES ('13', '3', '500.00', '2026-03-30 10:23:05', '1');
INSERT INTO `cambios_monedas` VALUES ('14', '1', '12.00', '2026-03-30 10:29:39', '1');
INSERT INTO `cambios_monedas` VALUES ('15', '3', '1234.00', '2026-03-30 10:29:58', '1');
INSERT INTO `cambios_monedas` VALUES ('20', '1', '1200.00', '2026-05-06 10:25:10', '1');
INSERT INTO `cambios_monedas` VALUES ('21', '3', '1200.00', '2026-05-06 10:25:20', '1');
INSERT INTO `cambios_monedas` VALUES ('22', '1', '49338.00', '2026-05-06 16:29:26', '1');
INSERT INTO `cambios_monedas` VALUES ('23', '1', '496.83', '2026-05-06 18:29:34', '1');
INSERT INTO `cambios_monedas` VALUES ('24', '1', '2500.00', '2026-05-06 18:43:11', '1');
INSERT INTO `cambios_monedas` VALUES ('25', '2', '120.00', '2026-05-06 18:44:28', '1');
INSERT INTO `cambios_monedas` VALUES ('26', '1', '250.01', '2026-05-06 18:44:42', '1');
INSERT INTO `cambios_monedas` VALUES ('27', '1', '500.00', '2026-05-06 18:44:53', '1');
INSERT INTO `cambios_monedas` VALUES ('28', '2', '1.00', '2026-05-17 08:25:25', '1');
INSERT INTO `cambios_monedas` VALUES ('29', '3', '600.00', '2026-05-17 08:26:06', '1');
INSERT INTO `cambios_monedas` VALUES ('30', '1', '700.00', '2026-05-17 15:41:04', '1');
INSERT INTO `cambios_monedas` VALUES ('31', '2', '1000.00', '2026-05-26 20:32:32', '1');
INSERT INTO `cambios_monedas` VALUES ('32', '1', '600.00', '2026-05-28 14:01:03', '1');
INSERT INTO `cambios_monedas` VALUES ('33', '8', '700.00', '2026-05-28 14:01:59', '1');
INSERT INTO `cambios_monedas` VALUES ('57', '8', '0.12', '2026-05-30 20:05:20', '1');
INSERT INTO `cambios_monedas` VALUES ('88', '2', '1.00', '2026-06-03 15:24:41', '1');
INSERT INTO `cambios_monedas` VALUES ('89', '1', '750.00', '2026-06-04 08:31:28', '1');
INSERT INTO `cambios_monedas` VALUES ('105', '1', '567.00', '2026-06-08 21:19:55', '1');
INSERT INTO `cambios_monedas` VALUES ('133', '4', '1250000000000.00', '2026-06-13 16:27:13', '1');
INSERT INTO `cambios_monedas` VALUES ('134', '4', '1250.00', '2026-06-13 16:28:00', '1');
INSERT INTO `cambios_monedas` VALUES ('135', '4', '1250.50', '2026-06-13 16:28:06', '1');
INSERT INTO `cambios_monedas` VALUES ('139', '1', '623.02', '2026-06-27 18:38:28', '1');
INSERT INTO `cambios_monedas` VALUES ('140', '37', '0.12', '2026-07-02 21:03:54', '1');
INSERT INTO `cambios_monedas` VALUES ('141', '37', '1.20', '2026-07-02 21:17:36', '1');
INSERT INTO `cambios_monedas` VALUES ('142', '1', '772.54', '2026-08-18 01:25:30', '1');
UNLOCK TABLES;

-- Tabla: categorias_productos
DROP TABLE IF EXISTS `categorias_productos`;
CREATE TABLE `categorias_productos` (
  `id_categoria_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria_producto` varchar(50) NOT NULL,
  `necesitan_materias_primas` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_categoria_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `categorias_productos` WRITE;
INSERT INTO `categorias_productos` VALUES ('1', 'FABRICADOS', '1', '1');
INSERT INTO `categorias_productos` VALUES ('2', 'NO FABRICADOS', '0', '1');
INSERT INTO `categorias_productos` VALUES ('3', 'INSUMOS', '1', '1');
INSERT INTO `categorias_productos` VALUES ('20', 'mmmm', '0', '0');
INSERT INTO `categorias_productos` VALUES ('21', 'NOMBRE', '0', '0');
UNLOCK TABLES;

-- Tabla: clientes
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `razon_social_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(150) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`rif_cedula_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `clientes` WRITE;
INSERT INTO `clientes` VALUES ('', 'ANDEROSN FREITEZ', '04161234567', 'andersonfreitezN@gmail.com', 'ANDERSON', '0');
INSERT INTO `clientes` VALUES ('30485684', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('E25252525', 'ANDEROSN FREITE', '04169484647', 'andersonfreite5z6@gmail.com', '5454', '0');
INSERT INTO `clientes` VALUES ('E30485684', 'ANDEROSN FREIT', '04169484645', 'andersonfreitez6@gmail.co', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('G200000589', 'Círculo Militar de la Fuerza Armada', '', '', 'Av. Morancon Av. Los Abogados Barquisimeto Edo. Lara', '1');
INSERT INTO `clientes` VALUES ('G200101350', 'Hospital Militar Dr. José Angel Alamo', '', 'oficinarecaudadolara25@gmail.com', 'Av. Principal El Ujano Edf. Hospital Militar Dr. José Angel Almo Barquisimeto Edo Lara', '1');
INSERT INTO `clientes` VALUES ('J001241345', 'Compañía Anónima Nacional Teléfonos de Venezuela (CANTV)', '02125007373', '', 'Final Av. Libertador Edificio CANTV Caracas Venezuela ', '1');
INSERT INTO `clientes` VALUES ('J001673920', 'Hospitalar C.A.', '04241941573', 'contigo@hospitalarve.com', 'Vda. Francisco de Miranda Edif. Centro Seguros La Paz Piso 7 Local 0-71, 0-73, 0-75, S-71 Urb. Boleita Caracas ( Petare) Miranda Zona Postal 1070', '1');
INSERT INTO `clientes` VALUES ('J070003448', 'Cervecería Regional C.A', '04129628546', 'andrea.linares@cerveceriaregional.com', 'Av. 17 Los Haticos Local N 112-13 Maracaibo Edo. Zulia', '1');
INSERT INTO `clientes` VALUES ('J310729425', 'Condominio Centro Comercial Profesional Rosancar', '04128479199', 'rosancarbarquisimeto@gmail.com', 'Calle 20 Esq. Carrera 31 C.C Rosancar Nivel 30-97 Local PB Sector Centro Barquisimeto Edo. Lara', '1');
INSERT INTO `clientes` VALUES ('J314964291', 'Mbzoluciones C.A', '', 'mbzolucioles@gmail.com', 'Calle 26 entre Carreras 16 y 17 Edif. Torre Ejecutiva piso 4 oficina 45 Barquisimeto Edo Lara', '1');
INSERT INTO `clientes` VALUES ('V12345666', 'Anderson Freitezjj', '04169484640', 'andersonfreitez69@gmail.com', 'BARQUISIMETO', '1');
INSERT INTO `clientes` VALUES ('V123456669', 'Anderson Freitez', '04169484678', 'andersonfreitekz6@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V12345668', 'Anderson Freitez', '04169484648', 'andersonfreit9z6@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V12345669', 'Anderson Freitez', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V1234567', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V12345678', 'Ander Freitez', '04160000000', 'ander@gmail.com', 'QUIBOR', '1');
INSERT INTO `clientes` VALUES ('V304856111', 'Anderson Freitei', '04169484647', 'andersonfreitez68@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V30485631', 'Ander Mendoza', '04160484640', 'andersonfreitez63@gmail.com', 'El Molino', '0');
INSERT INTO `clientes` VALUES ('V30485680', 'Anderson Freitez', '04169484655', 'andersonfreitez76@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V30485681', 'Anderson Freitez', '04169484643', 'andersonfreitez66@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V30485682', 'NADA', '04141234567', 'andersonfreitez6@gmail.co', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V30485683', 'Anderson Freitezm', '04169484648', 'andersonfreitez68@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V30485684', 'Anderson Freitez', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V304856845', '54114', '04169484646', 'andersonfreitez66@gmail.com', 'hhhh', '0');
INSERT INTO `clientes` VALUES ('V30485686', 'Anderson Freitez', '04169999999', 'andersonfreitez6999@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V30485688', 'Anderson Freitez', '04169484647', 'andersonfreite9z6@gmail.com', 'BARQUISIMETO', '1');
INSERT INTO `clientes` VALUES ('V30485689', 'ANDEROSN FREITEZ', '04169484648', 'andersonfreitez6@gmail.con', 'David', '0');
INSERT INTO `clientes` VALUES ('V30485694', 'Anderson Freitez', '04169484699', 'andersonfreitez996@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V33333331', 'ANDEROSN FREITEZaa', '04161234569', 'andersonfreitez6@gmail.coj', 'ANDERSON', '0');
INSERT INTO `clientes` VALUES ('V33333332', 'ANDEROSN FREITEZ', '04161234567', 'andersonfreitez12@gmail.com', 'Sanare', '0');
INSERT INTO `clientes` VALUES ('V33333333', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'BARQUISIMETO', '0');
INSERT INTO `clientes` VALUES ('V333333333', 'ANDEROSN FREITEZG', '04161234567', 'andersonfreitez16@gmail.com', 'SANARE', '0');
INSERT INTO `clientes` VALUES ('V333333338', 'ANDEROSN FREITEj', '04161234560', 'andersonfreitez06@gmail.com', 'hhh', '0');
UNLOCK TABLES;

-- Tabla: compras
DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras` (
  `id_compra` varchar(20) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_compra`),
  KEY `rif_proveedor_compras_fk` (`rif_proveedor`),
  KEY `fecha_compra_indice` (`fecha_compra`) USING BTREE,
  CONSTRAINT `rif_proveedor_compras_fk` FOREIGN KEY (`rif_proveedor`) REFERENCES `proveedores` (`rif_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `compras` WRITE;
INSERT INTO `compras` VALUES ('10', '30485684', '30485684', '2026-02-18 21:04:00', '0');
INSERT INTO `compras` VALUES ('11', '30485684', '30485684', '2026-05-18 10:00:00', '0');
INSERT INTO `compras` VALUES ('12', '30485684', '30485684', '2026-05-19 16:27:00', '0');
INSERT INTO `compras` VALUES ('13', '30485684', '30485684', '2026-05-30 10:13:00', '0');
INSERT INTO `compras` VALUES ('14', '30485684', '30485684', '2026-05-30 10:33:00', '0');
INSERT INTO `compras` VALUES ('15', '30485684', '30485684', '2026-05-30 18:08:00', '0');
INSERT INTO `compras` VALUES ('16', '30485684', '30485684', '2026-05-30 18:08:00', '0');
INSERT INTO `compras` VALUES ('17', '30485684', '30485684', '2026-06-07 14:06:00', '0');
INSERT INTO `compras` VALUES ('18', '30485684', '30485684', '2026-06-07 14:07:00', '1');
UNLOCK TABLES;

-- Tabla: comprobantes_pagos
DROP TABLE IF EXISTS `comprobantes_pagos`;
CREATE TABLE `comprobantes_pagos` (
  `id_comprobante_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_pago` varchar(20) NOT NULL,
  `path_comprobante` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_comprobante_pago`),
  KEY `id_pago_comprobantes_pagos_fk` (`id_pago`),
  CONSTRAINT `id_pago_comprobantes_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `comprobantes_pagos` WRITE;
INSERT INTO `comprobantes_pagos` VALUES ('143', 'PAG-26179-00001-82', 'comprobantes_pagos_2026_06_29_18_09_11_18.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('144', 'PAG-26179-00002-96', 'comprobantes_pagos_2026_06_29_18_13_42_10.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('145', 'PAG-26180-00001-26', 'comprobantes_pagos_2026_06_30_16_36_36_81.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('146', 'PAG-26181-00001-57', 'comprobantes_pagos_2026_07_01_11_24_44_33.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('147', 'PAG-26183-00001-58', 'comprobantes_pagos_2026_07_03_06_44_09_24.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('148', 'PAG-26183-00002-83', 'comprobantes_pagos_2026_07_03_06_55_02_9.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('149', 'PAG-26190-00001-58', 'comprobantes_pagos_2026_07_10_00_47_24_22.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('150', 'PAG-26221-00001-44', 'comprobantes_pagos_2026_08_10_17_44_30_56.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('151', 'PAG-26227-00001-57', 'comprobantes_pagos_2026_08_16_19_00_02_21.jpg', '1');
INSERT INTO `comprobantes_pagos` VALUES ('152', 'PAG-26258-00001-25', 'comprobantes_pagos_2026_09_16_09_49_09_86.jpg', '1');
UNLOCK TABLES;

-- Tabla: deliveries
DROP TABLE IF EXISTS `deliveries`;
CREATE TABLE `deliveries` (
  `id_delivery` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cedula_repartidor` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_delivery`),
  KEY `id_direccion_deliveries_fk` (`id_direccion`),
  KEY `cedula_repartidor_deliveries_fk` (`cedula_repartidor`),
  KEY `id_orden_entrega_presupuesto_delivery_fk` (`id_orden_entrega_presupuesto`),
  CONSTRAINT `cedula_repartidor_deliveries_fk` FOREIGN KEY (`cedula_repartidor`) REFERENCES `repartidores` (`cedula_repartidor`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_direccion_deliveries_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_orden_entrega_presupuesto_delivery_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `deliveries` WRITE;
INSERT INTO `deliveries` VALUES ('DELI-26179-00001-53', 'FACT-26179-00001-24', '156', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26179-00002-63', 'FACT-26179-00002-67', '157', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26180-00001-89', 'FACT-26180-00001-62', '158', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26181-00001-81', 'FACT-26181-00001-30', '159', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26183-00001-31', 'FACT-26183-00001-30', '160', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26183-00002-07', 'FACT-26183-00002-26', '161', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26190-00001-38', 'FACT-26190-00001-44', '162', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26221-00001-04', 'FACT-26221-00001-60', '163', 'V12344567', '1');
INSERT INTO `deliveries` VALUES ('DELI-26227-00001-53', 'FACT-26227-00001-55', '164', 'V30485654', '1');
INSERT INTO `deliveries` VALUES ('DELI-26258-00001-66', 'FACT-26258-00001-89', '165', 'V12344567', '1');
UNLOCK TABLES;

-- Tabla: detalles_pagos
DROP TABLE IF EXISTS `detalles_pagos`;
CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_pago` varchar(20) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `monto_pago` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_detalle_pago`),
  KEY `id_pago_detalles_pagos_fk` (`id_pago`),
  KEY `id_metodo_pago_detalles_pagos_fk` (`id_metodo_pago`),
  KEY `id_moneda_detalles_pagos_fk` (`id_moneda`),
  CONSTRAINT `id_metodo_pago_detalles_pagos_fk` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pagos` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_moneda_detalles_pagos_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_pago_detalles_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `detalles_pagos` WRITE;
INSERT INTO `detalles_pagos` VALUES ('264', 'PAG-26179-00001-82', '3', '1', '113.00', '1');
INSERT INTO `detalles_pagos` VALUES ('265', 'PAG-26179-00002-96', '3', '1', '113.00', '1');
INSERT INTO `detalles_pagos` VALUES ('266', 'PAG-26180-00001-26', '3', '1', '529.00', '1');
INSERT INTO `detalles_pagos` VALUES ('267', 'PAG-26181-00001-57', '3', '1', '321.00', '1');
INSERT INTO `detalles_pagos` VALUES ('268', 'PAG-26183-00001-58', '3', '1', '337.00', '1');
INSERT INTO `detalles_pagos` VALUES ('269', 'PAG-26183-00002-83', '3', '1', '337.00', '1');
INSERT INTO `detalles_pagos` VALUES ('270', 'PAG-26190-00001-58', '3', '1', '320.16', '1');
INSERT INTO `detalles_pagos` VALUES ('271', 'PAG-26221-00001-44', '3', '1', '304.00', '1');
INSERT INTO `detalles_pagos` VALUES ('272', 'PAG-26227-00001-57', '3', '1', '113.00', '1');
INSERT INTO `detalles_pagos` VALUES ('273', 'PAG-26258-00001-25', '1', '2', '97000.00', '1');
UNLOCK TABLES;

-- Tabla: direcciones
DROP TABLE IF EXISTS `direcciones`;
CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_latitud_direccion` int(11) NOT NULL,
  `id_longitud_direccion` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_direccion`),
  KEY `id_ruta_direcciones_fk` (`id_ruta`),
  KEY `id_latitud_direccion_direcciones_fk` (`id_latitud_direccion`),
  KEY `id_longitud_direccion_direcciones_fk` (`id_longitud_direccion`),
  CONSTRAINT `id_latitud_direccion_direcciones_fk` FOREIGN KEY (`id_latitud_direccion`) REFERENCES `latitudes_direcciones` (`id_latitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_longitud_direccion_direcciones_fk` FOREIGN KEY (`id_longitud_direccion`) REFERENCES `longitudes_direcciones` (`id_longitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_ruta_direcciones_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `direcciones` WRITE;
INSERT INTO `direcciones` VALUES ('28', '58', '54', '6', '1');
INSERT INTO `direcciones` VALUES ('29', '59', '55', '6', '1');
INSERT INTO `direcciones` VALUES ('30', '60', '56', '6', '1');
INSERT INTO `direcciones` VALUES ('31', '61', '57', '6', '1');
INSERT INTO `direcciones` VALUES ('32', '62', '58', '2', '1');
INSERT INTO `direcciones` VALUES ('33', '63', '59', '6', '1');
INSERT INTO `direcciones` VALUES ('49', '79', '75', '6', '1');
INSERT INTO `direcciones` VALUES ('50', '80', '76', '6', '1');
INSERT INTO `direcciones` VALUES ('51', '81', '77', '6', '1');
INSERT INTO `direcciones` VALUES ('52', '81', '77', '11', '1');
INSERT INTO `direcciones` VALUES ('53', '82', '78', '11', '1');
INSERT INTO `direcciones` VALUES ('54', '83', '79', '11', '1');
INSERT INTO `direcciones` VALUES ('55', '84', '80', '11', '1');
INSERT INTO `direcciones` VALUES ('56', '85', '81', '11', '1');
INSERT INTO `direcciones` VALUES ('59', '88', '84', '11', '1');
INSERT INTO `direcciones` VALUES ('60', '89', '85', '11', '1');
INSERT INTO `direcciones` VALUES ('61', '90', '86', '10', '1');
INSERT INTO `direcciones` VALUES ('62', '91', '87', '10', '1');
INSERT INTO `direcciones` VALUES ('63', '92', '88', '10', '1');
INSERT INTO `direcciones` VALUES ('64', '93', '89', '10', '1');
INSERT INTO `direcciones` VALUES ('65', '94', '90', '10', '1');
INSERT INTO `direcciones` VALUES ('66', '95', '91', '10', '1');
INSERT INTO `direcciones` VALUES ('77', '106', '102', '11', '1');
INSERT INTO `direcciones` VALUES ('78', '107', '103', '11', '1');
INSERT INTO `direcciones` VALUES ('79', '108', '104', '11', '1');
INSERT INTO `direcciones` VALUES ('80', '109', '105', '11', '1');
INSERT INTO `direcciones` VALUES ('81', '110', '106', '11', '1');
INSERT INTO `direcciones` VALUES ('82', '111', '107', '11', '1');
INSERT INTO `direcciones` VALUES ('83', '112', '108', '11', '1');
INSERT INTO `direcciones` VALUES ('84', '113', '109', '11', '1');
INSERT INTO `direcciones` VALUES ('85', '114', '110', '11', '1');
INSERT INTO `direcciones` VALUES ('86', '115', '111', '11', '1');
INSERT INTO `direcciones` VALUES ('124', '153', '149', '11', '1');
INSERT INTO `direcciones` VALUES ('125', '154', '150', '11', '1');
INSERT INTO `direcciones` VALUES ('131', '160', '156', '11', '1');
INSERT INTO `direcciones` VALUES ('141', '170', '166', '11', '1');
INSERT INTO `direcciones` VALUES ('142', '171', '167', '11', '1');
INSERT INTO `direcciones` VALUES ('143', '172', '168', '11', '1');
INSERT INTO `direcciones` VALUES ('144', '173', '169', '11', '1');
INSERT INTO `direcciones` VALUES ('145', '174', '170', '11', '1');
INSERT INTO `direcciones` VALUES ('146', '175', '171', '11', '1');
INSERT INTO `direcciones` VALUES ('149', '178', '174', '11', '1');
INSERT INTO `direcciones` VALUES ('150', '179', '175', '11', '1');
INSERT INTO `direcciones` VALUES ('151', '180', '169', '11', '1');
INSERT INTO `direcciones` VALUES ('153', '182', '177', '11', '1');
INSERT INTO `direcciones` VALUES ('154', '183', '178', '11', '1');
INSERT INTO `direcciones` VALUES ('155', '184', '179', '11', '1');
INSERT INTO `direcciones` VALUES ('156', '185', '180', '11', '1');
INSERT INTO `direcciones` VALUES ('157', '186', '181', '11', '1');
INSERT INTO `direcciones` VALUES ('158', '187', '182', '11', '1');
INSERT INTO `direcciones` VALUES ('159', '188', '183', '11', '1');
INSERT INTO `direcciones` VALUES ('160', '189', '184', '11', '1');
INSERT INTO `direcciones` VALUES ('161', '190', '185', '11', '1');
INSERT INTO `direcciones` VALUES ('162', '191', '186', '11', '1');
INSERT INTO `direcciones` VALUES ('163', '192', '187', '11', '1');
INSERT INTO `direcciones` VALUES ('164', '193', '188', '11', '1');
INSERT INTO `direcciones` VALUES ('165', '194', '189', '11', '1');
UNLOCK TABLES;

-- Tabla: empresas_envios
DROP TABLE IF EXISTS `empresas_envios`;
CREATE TABLE `empresas_envios` (
  `id_empresa_envios` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_empresa_envios`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `empresas_envios` WRITE;
INSERT INTO `empresas_envios` VALUES ('1', 'ZOOMO', '0');
INSERT INTO `empresas_envios` VALUES ('2', 'ZOOM', '0');
INSERT INTO `empresas_envios` VALUES ('3', 'ZOOMh', '0');
INSERT INTO `empresas_envios` VALUES ('4', 'ZOOM', '0');
INSERT INTO `empresas_envios` VALUES ('5', 'ALIBABAB', '0');
INSERT INTO `empresas_envios` VALUES ('6', 'ZOOMim', '0');
INSERT INTO `empresas_envios` VALUES ('7', 'ZOOM 2', '0');
INSERT INTO `empresas_envios` VALUES ('34', 'ZOOM2', '0');
INSERT INTO `empresas_envios` VALUES ('35', 'ZOOMMM', '0');
INSERT INTO `empresas_envios` VALUES ('36', 'mmm9', '0');
INSERT INTO `empresas_envios` VALUES ('37', 'ZOOMM', '0');
INSERT INTO `empresas_envios` VALUES ('38', 'ZOOMmm.', '0');
INSERT INTO `empresas_envios` VALUES ('39', 'ZOOMmm', '0');
INSERT INTO `empresas_envios` VALUES ('40', 'ZOOMs', '0');
INSERT INTO `empresas_envios` VALUES ('41', 'ZOOMM', '0');
INSERT INTO `empresas_envios` VALUES ('42', 'ZOOM', '1');
UNLOCK TABLES;

-- Tabla: envios_terceros
DROP TABLE IF EXISTS `envios_terceros`;
CREATE TABLE `envios_terceros` (
  `id_envio_tercero` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `precio_envio_tercero` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_envio_tercero`),
  KEY `id_sucursal_empresa_envios_envios_terceros_fk` (`id_sucursal_empresa_envios`),
  KEY `id_orden_entrega_presupuesto_envios_terceros_fk` (`id_orden_entrega_presupuesto`),
  CONSTRAINT `id_orden_entrega_presupuesto_envios_terceros_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_sucursal_empresa_envios_envios_terceros_fk` FOREIGN KEY (`id_sucursal_empresa_envios`) REFERENCES `sucursales_empresas_envios` (`id_sucursal_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: latitudes_direcciones
DROP TABLE IF EXISTS `latitudes_direcciones`;
CREATE TABLE `latitudes_direcciones` (
  `id_latitud_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `coordenada_latitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_latitud_direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `latitudes_direcciones` WRITE;
INSERT INTO `latitudes_direcciones` VALUES ('58', '10.0626331', '1');
INSERT INTO `latitudes_direcciones` VALUES ('59', '10.0630566', '1');
INSERT INTO `latitudes_direcciones` VALUES ('60', '10.0632884', '1');
INSERT INTO `latitudes_direcciones` VALUES ('61', '10.072417', '1');
INSERT INTO `latitudes_direcciones` VALUES ('62', '10.0670697', '1');
INSERT INTO `latitudes_direcciones` VALUES ('63', '10.039655', '1');
INSERT INTO `latitudes_direcciones` VALUES ('79', '10.053758704512', '1');
INSERT INTO `latitudes_direcciones` VALUES ('80', '9.8599964012514', '1');
INSERT INTO `latitudes_direcciones` VALUES ('81', '10.123456', '1');
INSERT INTO `latitudes_direcciones` VALUES ('82', '9.8595170293252', '1');
INSERT INTO `latitudes_direcciones` VALUES ('83', '9.8600828102787', '1');
INSERT INTO `latitudes_direcciones` VALUES ('84', '9.8600797557029', '1');
INSERT INTO `latitudes_direcciones` VALUES ('85', '9.8603294672703', '1');
INSERT INTO `latitudes_direcciones` VALUES ('88', '9.8616122673031', '1');
INSERT INTO `latitudes_direcciones` VALUES ('89', '9.8616276041428', '1');
INSERT INTO `latitudes_direcciones` VALUES ('90', '10.051815346816', '1');
INSERT INTO `latitudes_direcciones` VALUES ('91', '10.050653', '1');
INSERT INTO `latitudes_direcciones` VALUES ('92', '9.8613761802134', '1');
INSERT INTO `latitudes_direcciones` VALUES ('93', '9.8600626405522', '1');
INSERT INTO `latitudes_direcciones` VALUES ('94', '10.050305879849', '1');
INSERT INTO `latitudes_direcciones` VALUES ('95', '10.05010893544', '1');
INSERT INTO `latitudes_direcciones` VALUES ('106', '9.8601550931395', '1');
INSERT INTO `latitudes_direcciones` VALUES ('107', '9.859754768276', '1');
INSERT INTO `latitudes_direcciones` VALUES ('108', '9.8599933013011', '1');
INSERT INTO `latitudes_direcciones` VALUES ('109', '9.8599169544674', '1');
INSERT INTO `latitudes_direcciones` VALUES ('110', '9.8615897462498', '1');
INSERT INTO `latitudes_direcciones` VALUES ('111', '9.8616255287098', '1');
INSERT INTO `latitudes_direcciones` VALUES ('112', '9.8603268948328', '1');
INSERT INTO `latitudes_direcciones` VALUES ('113', '9.8603829908308', '1');
INSERT INTO `latitudes_direcciones` VALUES ('114', '9.860111242047', '1');
INSERT INTO `latitudes_direcciones` VALUES ('115', '9.8612437338729', '1');
INSERT INTO `latitudes_direcciones` VALUES ('153', '9.8604001121099', '1');
INSERT INTO `latitudes_direcciones` VALUES ('154', '9.8597670526368', '1');
INSERT INTO `latitudes_direcciones` VALUES ('160', '9.8597788105131', '1');
INSERT INTO `latitudes_direcciones` VALUES ('170', '9.861874', '1');
INSERT INTO `latitudes_direcciones` VALUES ('171', '9.861672', '1');
INSERT INTO `latitudes_direcciones` VALUES ('172', '9.8597561143133', '1');
INSERT INTO `latitudes_direcciones` VALUES ('173', '9.8600273814353', '1');
INSERT INTO `latitudes_direcciones` VALUES ('174', '9.8600766058849', '1');
INSERT INTO `latitudes_direcciones` VALUES ('175', '9.8592958653846', '1');
INSERT INTO `latitudes_direcciones` VALUES ('178', '9.8618713140662', '1');
INSERT INTO `latitudes_direcciones` VALUES ('179', '9.8594610351324', '1');
INSERT INTO `latitudes_direcciones` VALUES ('180', '9.8600262893916', '1');
INSERT INTO `latitudes_direcciones` VALUES ('182', '9.8618126247782', '1');
INSERT INTO `latitudes_direcciones` VALUES ('183', '9.8618387108127', '1');
INSERT INTO `latitudes_direcciones` VALUES ('184', '9.8603384904674', '1');
INSERT INTO `latitudes_direcciones` VALUES ('185', '9.8597834819881', '1');
INSERT INTO `latitudes_direcciones` VALUES ('186', '9.8601496311066', '1');
INSERT INTO `latitudes_direcciones` VALUES ('187', '9.861755', '1');
INSERT INTO `latitudes_direcciones` VALUES ('188', '9.8597431994487', '1');
INSERT INTO `latitudes_direcciones` VALUES ('189', '9.8600491537911', '1');
INSERT INTO `latitudes_direcciones` VALUES ('190', '9.8598981990669', '1');
INSERT INTO `latitudes_direcciones` VALUES ('191', '9.8597769944842', '1');
INSERT INTO `latitudes_direcciones` VALUES ('192', '9.93103', '1');
INSERT INTO `latitudes_direcciones` VALUES ('193', '9.8618556465183', '1');
INSERT INTO `latitudes_direcciones` VALUES ('194', '9.86177', '1');
UNLOCK TABLES;

-- Tabla: longitudes_direcciones
DROP TABLE IF EXISTS `longitudes_direcciones`;
CREATE TABLE `longitudes_direcciones` (
  `id_longitud_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `coordenada_longitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_longitud_direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `longitudes_direcciones` WRITE;
INSERT INTO `longitudes_direcciones` VALUES ('54', '-69.3654583', '1');
INSERT INTO `longitudes_direcciones` VALUES ('55', '-69.3644222', '1');
INSERT INTO `longitudes_direcciones` VALUES ('56', '-69.3648094', '1');
INSERT INTO `longitudes_direcciones` VALUES ('57', '-69.3757301', '1');
INSERT INTO `longitudes_direcciones` VALUES ('58', '-69.3441849', '1');
INSERT INTO `longitudes_direcciones` VALUES ('59', '-69.4294816', '1');
INSERT INTO `longitudes_direcciones` VALUES ('75', '-69.273433685303', '1');
INSERT INTO `longitudes_direcciones` VALUES ('76', '-69.611978291735', '1');
INSERT INTO `longitudes_direcciones` VALUES ('77', '10.123456', '1');
INSERT INTO `longitudes_direcciones` VALUES ('78', '-69.611947939329', '1');
INSERT INTO `longitudes_direcciones` VALUES ('79', '-69.611985050712', '1');
INSERT INTO `longitudes_direcciones` VALUES ('80', '-69.611983887064', '1');
INSERT INTO `longitudes_direcciones` VALUES ('81', '-69.612001773886', '1');
INSERT INTO `longitudes_direcciones` VALUES ('84', '-69.612089384389', '1');
INSERT INTO `longitudes_direcciones` VALUES ('85', '-69.612089481661', '1');
INSERT INTO `longitudes_direcciones` VALUES ('86', '-69.366774559021', '1');
INSERT INTO `longitudes_direcciones` VALUES ('87', '-69.362676', '1');
INSERT INTO `longitudes_direcciones` VALUES ('88', '-69.612072884695', '1');
INSERT INTO `longitudes_direcciones` VALUES ('89', '-69.611984815715', '1');
INSERT INTO `longitudes_direcciones` VALUES ('90', '-69.360347986221', '1');
INSERT INTO `longitudes_direcciones` VALUES ('91', '-69.326858521672', '1');
INSERT INTO `longitudes_direcciones` VALUES ('102', '-69.61199178066', '1');
INSERT INTO `longitudes_direcciones` VALUES ('103', '-69.611963467553', '1');
INSERT INTO `longitudes_direcciones` VALUES ('104', '-69.61198011811', '1');
INSERT INTO `longitudes_direcciones` VALUES ('105', '-69.611974940021', '1');
INSERT INTO `longitudes_direcciones` VALUES ('106', '-69.612088084289', '1');
INSERT INTO `longitudes_direcciones` VALUES ('107', '-69.61208912813', '1');
INSERT INTO `longitudes_direcciones` VALUES ('108', '-69.61200079391', '1');
INSERT INTO `longitudes_direcciones` VALUES ('109', '-69.612006685027', '1');
INSERT INTO `longitudes_direcciones` VALUES ('110', '-69.611986751037', '1');
INSERT INTO `longitudes_direcciones` VALUES ('111', '-69.612064389881', '1');
INSERT INTO `longitudes_direcciones` VALUES ('149', '-69.612005278293', '1');
INSERT INTO `longitudes_direcciones` VALUES ('150', '-69.611963985342', '1');
INSERT INTO `longitudes_direcciones` VALUES ('156', '-69.611965316644', '1');
INSERT INTO `longitudes_direcciones` VALUES ('166', '-69.612083', '1');
INSERT INTO `longitudes_direcciones` VALUES ('167', '-69.61203', '1');
INSERT INTO `longitudes_direcciones` VALUES ('168', '-69.611963134899', '1');
INSERT INTO `longitudes_direcciones` VALUES ('169', '-69.611978425897', '1');
INSERT INTO `longitudes_direcciones` VALUES ('170', '-69.611981333779', '1');
INSERT INTO `longitudes_direcciones` VALUES ('171', '-69.611937', '1');
INSERT INTO `longitudes_direcciones` VALUES ('174', '-69.612083222138', '1');
INSERT INTO `longitudes_direcciones` VALUES ('175', '-69.611946478615', '1');
INSERT INTO `longitudes_direcciones` VALUES ('177', '-69.612078044944', '1');
INSERT INTO `longitudes_direcciones` VALUES ('178', '-69.612080150969', '1');
INSERT INTO `longitudes_direcciones` VALUES ('179', '-69.611995786211', '1');
INSERT INTO `longitudes_direcciones` VALUES ('180', '-69.611964647621', '1');
INSERT INTO `longitudes_direcciones` VALUES ('181', '-69.611986523216', '1');
INSERT INTO `longitudes_direcciones` VALUES ('182', '-69.612045', '1');
INSERT INTO `longitudes_direcciones` VALUES ('183', '-69.611962408635', '1');
INSERT INTO `longitudes_direcciones` VALUES ('184', '-69.611980430118', '1');
INSERT INTO `longitudes_direcciones` VALUES ('185', '-69.611971249216', '1');
INSERT INTO `longitudes_direcciones` VALUES ('186', '-69.611963692348', '1');
INSERT INTO `longitudes_direcciones` VALUES ('187', '-69.621948', '1');
INSERT INTO `longitudes_direcciones` VALUES ('188', '-69.612069311619', '1');
INSERT INTO `longitudes_direcciones` VALUES ('189', '-69.6120565', '1');
UNLOCK TABLES;

-- Tabla: materias_primas
DROP TABLE IF EXISTS `materias_primas`;
CREATE TABLE `materias_primas` (
  `id_materia_prima` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` decimal(20,2) NOT NULL,
  `stock_minimo_materia_prima` decimal(20,2) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia_prima`),
  KEY `id_unidad_medida_materias_primas_fk` (`id_unidad_medida`),
  CONSTRAINT `id_unidad_medida_materias_primas_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `materias_primas` WRITE;
INSERT INTO `materias_primas` VALUES ('MATE-26123-00001-66', '2', 'HIPOCLORITO', '100.00', '10.00', '50.00', '1');
INSERT INTO `materias_primas` VALUES ('MATE-26149-00001-45', '1', 'HIPOCLORITOm', '10.00', '8.00', '10.00', '0');
INSERT INTO `materias_primas` VALUES ('MATE-26149-00002-90', '1', 'HIPOCLORITOSS', '222.00', '222.00', '111.00', '0');
INSERT INTO `materias_primas` VALUES ('MATE-26157-00001-95', '2', 'HIPOCLORITOm', '101.00', '1.00', '1.00', '1');
UNLOCK TABLES;

-- Tabla: materias_primas_compras
DROP TABLE IF EXISTS `materias_primas_compras`;
CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia_prima_compra`),
  KEY `id_materia_prima_materias_primas_compras_fk` (`id_materia_prima`),
  KEY `id_compra_materias_primas_compras_fk` (`id_compra`),
  CONSTRAINT `id_compra_materias_primas_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_materia_prima_materias_primas_compras_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `materias_primas_compras` WRITE;
INSERT INTO `materias_primas_compras` VALUES ('1', 'MATE-26123-00001-66', '10', '2.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('2', 'MATE-26123-00001-66', '10', '5.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('10', 'MATE-26123-00001-66', '12', '12.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('11', 'MATE-26123-00001-66', '12', '13.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('12', 'MATE-26123-00001-66', '13', '20.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('14', 'MATE-26123-00001-66', '14', '10.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('15', 'MATE-26123-00001-66', '15', '1.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('16', 'MATE-26123-00001-66', '16', '1.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('17', 'MATE-26123-00001-66', '17', '2.00', '0');
INSERT INTO `materias_primas_compras` VALUES ('18', 'MATE-26123-00001-66', '18', '10.00', '1');
UNLOCK TABLES;

-- Tabla: materias_primas_producciones
DROP TABLE IF EXISTS `materias_primas_producciones`;
CREATE TABLE `materias_primas_producciones` (
  `id_materia_prima_produccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia_prima_produccion`),
  KEY `id_materia_prima_materias_primas_producciones_fk` (`id_materia_prima`),
  KEY `id_produccion_materias_primas_producciones_fk` (`id_produccion`),
  CONSTRAINT `id_materia_prima_materias_primas_producciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_produccion_materias_primas_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: materias_primas_productos
DROP TABLE IF EXISTS `materias_primas_productos`;
CREATE TABLE `materias_primas_productos` (
  `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia_prima_producto`),
  KEY `id_materia_prima_materias_primas_productos_fk` (`id_materia_prima`),
  KEY `id_producto_materias_primas_productos_fk` (`id_producto`),
  CONSTRAINT `id_materia_prima_materias_primas_productos_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_producto_materias_primas_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `materias_primas_productos` WRITE;
INSERT INTO `materias_primas_productos` VALUES ('187', 'MATE-26123-00001-66', 'PROD-26183-00001-56', '10.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('200', 'MATE-26123-00001-66', 'PROD-26150-00001-39', '0.10', '0');
INSERT INTO `materias_primas_productos` VALUES ('202', 'MATE-26123-00001-66', 'PROD-26222-00001-83', '2.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('203', 'MATE-26123-00001-66', 'PROD-26222-00002-19', '10.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('204', 'MATE-26123-00001-66', 'PROD-26222-00003-10', '10.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('206', 'MATE-26123-00001-66', 'PROD-26222-00006-10', '0.01', '0');
INSERT INTO `materias_primas_productos` VALUES ('214', 'MATE-26123-00001-66', 'PROD-26222-00004-43', '1.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('215', 'MATE-26123-00001-66', 'PROD-26259-00001-46', '0.01', '0');
INSERT INTO `materias_primas_productos` VALUES ('216', 'MATE-26123-00001-66', 'PROD-26259-00002-49', '0.10', '0');
INSERT INTO `materias_primas_productos` VALUES ('217', 'MATE-26123-00001-66', 'PROD-26259-00003-43', '1.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('219', 'MATE-26123-00001-66', 'PROD-26259-00004-17', '10.00', '0');
INSERT INTO `materias_primas_productos` VALUES ('222', 'MATE-26123-00001-66', 'PROD-26259-00006-02', '10.00', '1');
UNLOCK TABLES;

-- Tabla: metodos_pagos
DROP TABLE IF EXISTS `metodos_pagos`;
CREATE TABLE `metodos_pagos` (
  `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodo_pago` varchar(50) NOT NULL,
  `necesita_moneda` tinyint(1) NOT NULL,
  `necesita_banco_emisor` tinyint(1) NOT NULL,
  `necesita_banco_receptor` tinyint(1) NOT NULL,
  `necesita_referencia` tinyint(1) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `metodos_pagos` WRITE;
INSERT INTO `metodos_pagos` VALUES ('1', 'TRANSFERENCIA', '0', '1', '1', '1', '1', '1');
INSERT INTO `metodos_pagos` VALUES ('2', 'PAGO MÓVIL', '0', '0', '1', '1', '1', '1');
INSERT INTO `metodos_pagos` VALUES ('3', 'ZELLE', '1', '0', '0', '1', '1', '1');
INSERT INTO `metodos_pagos` VALUES ('4', 'ZINLI', '1', '0', '0', '1', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('5', 'BINANCE', '1', '1', '1', '1', '1', '1');
INSERT INTO `metodos_pagos` VALUES ('6', 'EFECTIVO', '1', '0', '0', '0', '0', '1');
INSERT INTO `metodos_pagos` VALUES ('12', 'TRANSFERENCI', '1', '1', '1', '1', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('13', 'ZELLEL', '1', '1', '1', '1', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('17', 'TOPOmm', '1', '1', '1', '1', '1', '0');
INSERT INTO `metodos_pagos` VALUES ('18', 'TOPOm', '1', '0', '0', '0', '1', '0');
INSERT INTO `metodos_pagos` VALUES ('51', 'OTRO', '0', '0', '0', '0', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('52', 'EFECTIVOM', '1', '1', '1', '1', '1', '0');
INSERT INTO `metodos_pagos` VALUES ('53', 'ZINLI', '1', '0', '0', '0', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('54', 'EFECTIVOmm', '0', '0', '0', '0', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('55', 'EFECTIVOmm', '0', '0', '0', '0', '0', '0');
INSERT INTO `metodos_pagos` VALUES ('56', 'EFECTIVOs', '1', '1', '1', '1', '1', '0');
INSERT INTO `metodos_pagos` VALUES ('57', 'EFECTIVOb', '1', '1', '1', '1', '1', '0');
INSERT INTO `metodos_pagos` VALUES ('59', 'mmm', '1', '0', '1', '1', '0', '0');
UNLOCK TABLES;

-- Tabla: monedas
DROP TABLE IF EXISTS `monedas`;
CREATE TABLE `monedas` (
  `id_moneda` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_moneda` varchar(20) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_moneda`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `monedas` WRITE;
INSERT INTO `monedas` VALUES ('1', 'DÓLAR', '$', '772.54', '1');
INSERT INTO `monedas` VALUES ('2', 'BÓLIVAR', 'BS', '1.00', '1');
INSERT INTO `monedas` VALUES ('3', 'EURO', '€', '600.00', '1');
INSERT INTO `monedas` VALUES ('4', 'YUAN', '¥', '1250.50', '1');
INSERT INTO `monedas` VALUES ('8', 'VALOR', 'U', '0.12', '0');
INSERT INTO `monedas` VALUES ('37', 'YUAN2', 'YN', '1.20', '0');
UNLOCK TABLES;

-- Tabla: movimientos_anomalos_materias_primas
DROP TABLE IF EXISTS `movimientos_anomalos_materias_primas`;
CREATE TABLE `movimientos_anomalos_materias_primas` (
  `id_movimiento_anomalo_materia_prima` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia_prima` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_movimiento_anomalo_materia_prima`),
  KEY `id_materia_prima_movimientos_anomalos_materias_primas_fk` (`id_materia_prima`),
  CONSTRAINT `id_materia_prima_movimientos_anomalos_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `movimientos_anomalos_materias_primas` WRITE;
INSERT INTO `movimientos_anomalos_materias_primas` VALUES ('1', 'MATE-26123-00001-66', '5', '1', 'llego', '2026-05-23 12:49:34', '1');
INSERT INTO `movimientos_anomalos_materias_primas` VALUES ('2', 'MATE-26123-00001-66', '10', '1', 'asd', '2026-05-23 13:54:10', '1');
INSERT INTO `movimientos_anomalos_materias_primas` VALUES ('3', 'MATE-26157-00001-95', '100', '1', 'jaja', '2026-06-20 22:01:28', '1');
UNLOCK TABLES;

-- Tabla: movimientos_anomalos_productos
DROP TABLE IF EXISTS `movimientos_anomalos_productos`;
CREATE TABLE `movimientos_anomalos_productos` (
  `id_movimiento_anomalo_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_movimiento_anomalo_producto`),
  KEY `id_presentacion_producto_movimientos_anomalos_productos_fk` (`id_presentacion_producto`),
  CONSTRAINT `id_presentacion_producto_movimientos_anomalos_productos_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: ordenes_entregas_presupuestos
DROP TABLE IF EXISTS `ordenes_entregas_presupuestos`;
CREATE TABLE `ordenes_entregas_presupuestos` (
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `fecha_orden_entrega_presupuesto` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_orden_entrega_presupuesto`),
  KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  KEY `id_cambio_iva_venta` (`id_cambio_iva`),
  CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `ordenes_entregas_presupuestos` WRITE;
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26179-00001-24', 'V30485684', '256', 'V30485684', '2026-06-29 18:09:11', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26179-00002-67', 'V30485684', '256', 'V30485684', '2026-06-29 18:13:42', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26180-00001-62', 'V30485684', '256', 'V30485684', '2026-06-30 16:36:35', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26181-00001-30', 'V30485684', '256', 'V30485684', '2026-07-01 11:24:44', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26183-00001-30', 'V30485688', '257', 'V30485688', '2026-07-03 06:44:09', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26183-00002-26', 'V30485688', '257', 'V30485688', '2026-07-03 06:55:02', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26190-00001-44', 'V30485684', '258', 'V30485684', '2026-07-10 00:47:24', '7');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26221-00001-60', 'V30485684', '258', 'V30485684', '2026-08-10 17:44:30', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26227-00001-55', 'V30485684', '258', 'V30485631', '2026-08-16 19:00:02', '8');
INSERT INTO `ordenes_entregas_presupuestos` VALUES ('FACT-26258-00001-89', 'V30485684', '258', 'V30485684', '2026-09-16 09:49:09', '8');
UNLOCK TABLES;

-- Tabla: pagos
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos` (
  `id_pago` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_pago`),
  KEY `id_venta_pago_fk` (`id_orden_entrega_presupuesto`),
  CONSTRAINT `id_orden_entrega_presupuesto_pagos_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `pagos` WRITE;
INSERT INTO `pagos` VALUES ('PAG-26179-00001-82', 'FACT-26179-00001-24', '2026-06-29 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26179-00002-96', 'FACT-26179-00002-67', '2026-06-29 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26180-00001-26', 'FACT-26180-00001-62', '2026-06-30 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26181-00001-57', 'FACT-26181-00001-30', '2026-07-01 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26183-00001-58', 'FACT-26183-00001-30', '2026-07-03 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26183-00002-83', 'FACT-26183-00002-26', '2026-07-03 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26190-00001-58', 'FACT-26190-00001-44', '2026-07-10 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26221-00001-44', 'FACT-26221-00001-60', '2026-08-10 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26227-00001-57', 'FACT-26227-00001-55', '2026-08-16 00:00:00', '1');
INSERT INTO `pagos` VALUES ('PAG-26258-00001-25', 'FACT-26258-00001-89', '2026-09-16 00:00:00', '1');
UNLOCK TABLES;

-- Tabla: precios_materias_primas
DROP TABLE IF EXISTS `precios_materias_primas`;
CREATE TABLE `precios_materias_primas` (
  `id_precio_materia_prima` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia_prima` varchar(20) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_precio_materia_prima`),
  KEY `id_materia_prima_precios_materias_primas_fk` (`id_materia_prima`),
  CONSTRAINT `id_materia_prima_precios_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `precios_materias_primas` WRITE;
INSERT INTO `precios_materias_primas` VALUES ('1', 'MATE-26123-00001-66', '10.00', '2026-05-27 18:28:29', '1');
INSERT INTO `precios_materias_primas` VALUES ('2', 'MATE-26123-00001-66', '50.00', '2026-05-28 14:29:54', '1');
INSERT INTO `precios_materias_primas` VALUES ('4', 'MATE-26149-00001-45', '10.00', '2026-05-30 11:21:55', '1');
INSERT INTO `precios_materias_primas` VALUES ('5', 'MATE-26149-00002-90', '111.00', '2026-05-30 18:44:44', '1');
INSERT INTO `precios_materias_primas` VALUES ('46', 'MATE-26157-00001-95', '1.00', '2026-06-07 13:17:51', '1');
UNLOCK TABLES;

-- Tabla: precios_productos
DROP TABLE IF EXISTS `precios_productos`;
CREATE TABLE `precios_productos` (
  `id_precio_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` varchar(20) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_precio_producto`),
  KEY `id_producto_precios_productos_fk` (`id_producto`),
  CONSTRAINT `id_producto_precios_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `precios_productos` WRITE;
INSERT INTO `precios_productos` VALUES ('42', 'PROD-26150-00001-39', '1.00', '2026-05-31 12:51:12', '0');
INSERT INTO `precios_productos` VALUES ('63', 'PROD-26150-00001-39', '2.00', '2026-06-04 10:44:02', '0');
INSERT INTO `precios_productos` VALUES ('65', 'PROD-26150-00001-39', '3.00', '2026-06-04 11:04:31', '0');
INSERT INTO `precios_productos` VALUES ('74', 'PROD-26150-00001-39', '1.00', '2026-06-08 21:20:19', '0');
INSERT INTO `precios_productos` VALUES ('86', 'PROD-26150-00001-39', '100.00', '2026-06-21 18:26:52', '0');
INSERT INTO `precios_productos` VALUES ('87', 'PROD-26150-00001-39', '1.00', '2026-06-21 18:27:09', '0');
INSERT INTO `precios_productos` VALUES ('89', 'PROD-26150-00001-39', '100.00', '2026-06-29 18:43:02', '0');
INSERT INTO `precios_productos` VALUES ('90', 'PROD-26150-00001-39', '10000.00', '2026-06-29 18:43:11', '0');
INSERT INTO `precios_productos` VALUES ('91', 'PROD-26150-00001-39', '1.00', '2026-06-30 15:53:05', '0');
INSERT INTO `precios_productos` VALUES ('92', 'PROD-26150-00001-39', '10.00', '2026-07-03 07:47:04', '0');
INSERT INTO `precios_productos` VALUES ('93', 'PROD-26150-00001-39', '100.00', '2026-07-03 07:47:27', '0');
INSERT INTO `precios_productos` VALUES ('94', 'PROD-26183-00001-56', '1.00', '2026-07-03 07:49:40', '0');
INSERT INTO `precios_productos` VALUES ('95', 'PROD-26150-00001-39', '1.00', '2026-07-03 11:12:43', '0');
INSERT INTO `precios_productos` VALUES ('96', 'PROD-26222-00001-83', '1.00', '2026-08-12 00:22:42', '0');
INSERT INTO `precios_productos` VALUES ('97', 'PROD-26222-00002-19', '1.00', '2026-08-12 00:37:05', '0');
INSERT INTO `precios_productos` VALUES ('98', 'PROD-26222-00003-10', '1.00', '2026-08-12 00:37:59', '0');
INSERT INTO `precios_productos` VALUES ('101', 'PROD-26222-00004-43', '1.00', '2026-08-12 00:48:20', '0');
INSERT INTO `precios_productos` VALUES ('102', 'PROD-26222-00005-98', '1.00', '2026-08-12 00:48:53', '0');
INSERT INTO `precios_productos` VALUES ('103', 'PROD-26222-00006-10', '10.00', '2026-08-12 00:50:58', '0');
INSERT INTO `precios_productos` VALUES ('104', 'PROD-26259-00001-46', '1.00', '2026-09-17 18:50:52', '0');
INSERT INTO `precios_productos` VALUES ('105', 'PROD-26259-00002-49', '1.00', '2026-09-17 18:58:08', '0');
INSERT INTO `precios_productos` VALUES ('106', 'PROD-26259-00003-43', '1.00', '2026-09-17 19:00:30', '0');
INSERT INTO `precios_productos` VALUES ('108', 'PROD-26259-00004-17', '1.00', '2026-09-17 19:05:05', '0');
INSERT INTO `precios_productos` VALUES ('109', 'PROD-26259-00005-07', '1.00', '2026-09-17 19:17:50', '0');
INSERT INTO `precios_productos` VALUES ('112', 'PROD-26259-00006-02', '10.00', '2026-09-17 19:50:39', '1');
UNLOCK TABLES;

-- Tabla: precios_rutas
DROP TABLE IF EXISTS `precios_rutas`;
CREATE TABLE `precios_rutas` (
  `id_precio_ruta` int(11) NOT NULL AUTO_INCREMENT,
  `id_ruta` int(11) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_precio_ruta`),
  KEY `id_ruta_precios_rutas_fk` (`id_ruta`),
  CONSTRAINT `id_ruta_precios_rutas_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `precios_rutas` WRITE;
INSERT INTO `precios_rutas` VALUES ('4', '9', '1.00', '2026-05-24 15:58:00', '1');
INSERT INTO `precios_rutas` VALUES ('5', '10', '2.00', '2026-05-24 15:58:22', '1');
INSERT INTO `precios_rutas` VALUES ('6', '11', '3.00', '2026-05-24 15:58:36', '1');
INSERT INTO `precios_rutas` VALUES ('7', '9', '1200.00', '2026-05-27 18:29:44', '1');
INSERT INTO `precios_rutas` VALUES ('43', '11', '5.00', '2026-06-04 10:04:22', '1');
INSERT INTO `precios_rutas` VALUES ('52', '11', '3.00', '2026-06-08 21:21:37', '1');
INSERT INTO `precios_rutas` VALUES ('53', '9', '0.50', '2026-06-08 21:21:46', '1');
INSERT INTO `precios_rutas` VALUES ('54', '10', '1.00', '2026-06-08 21:21:54', '1');
INSERT INTO `precios_rutas` VALUES ('55', '11', '2.00', '2026-06-08 21:22:00', '1');
INSERT INTO `precios_rutas` VALUES ('76', '71', '30.00', '2026-06-14 14:38:18', '1');
INSERT INTO `precios_rutas` VALUES ('77', '71', '3.00', '2026-06-14 14:38:26', '1');
INSERT INTO `precios_rutas` VALUES ('78', '71', '0.30', '2026-06-14 14:38:33', '1');
INSERT INTO `precios_rutas` VALUES ('79', '71', '30.00', '2026-06-14 14:39:44', '1');
INSERT INTO `precios_rutas` VALUES ('80', '71', '3000.00', '2026-06-14 14:39:59', '1');
INSERT INTO `precios_rutas` VALUES ('81', '71', '300.00', '2026-06-14 14:41:56', '1');
INSERT INTO `precios_rutas` VALUES ('82', '71', '30000.00', '2026-06-14 14:47:51', '1');
INSERT INTO `precios_rutas` VALUES ('83', '71', '3000.00', '2026-06-14 14:51:00', '1');
INSERT INTO `precios_rutas` VALUES ('84', '71', '3.00', '2026-06-14 14:52:57', '1');
INSERT INTO `precios_rutas` VALUES ('85', '72', '3.00', '2026-06-14 14:54:22', '1');
INSERT INTO `precios_rutas` VALUES ('86', '73', '10.00', '2026-06-24 18:37:18', '1');
INSERT INTO `precios_rutas` VALUES ('87', '73', '1000.00', '2026-06-24 18:37:26', '1');
INSERT INTO `precios_rutas` VALUES ('88', '73', '100.00', '2026-06-24 18:37:56', '1');
INSERT INTO `precios_rutas` VALUES ('89', '74', '1.00', '2026-06-30 16:28:05', '1');
INSERT INTO `precios_rutas` VALUES ('90', '75', '2.00', '2026-07-03 03:30:38', '1');
INSERT INTO `precios_rutas` VALUES ('91', '11', '20.00', '2026-07-03 06:59:53', '1');
INSERT INTO `precios_rutas` VALUES ('92', '11', '2.00', '2026-07-03 07:00:13', '1');
INSERT INTO `precios_rutas` VALUES ('93', '76', '100.00', '2026-07-03 07:00:37', '1');
INSERT INTO `precios_rutas` VALUES ('94', '77', '2.00', '2026-08-11 05:55:28', '1');
INSERT INTO `precios_rutas` VALUES ('95', '77', '0.20', '2026-08-11 05:55:38', '1');
INSERT INTO `precios_rutas` VALUES ('96', '78', '2.00', '2026-08-11 23:15:25', '1');
INSERT INTO `precios_rutas` VALUES ('97', '79', '2.00', '2026-09-17 17:53:33', '1');
UNLOCK TABLES;

-- Tabla: precios_servicios
DROP TABLE IF EXISTS `precios_servicios`;
CREATE TABLE `precios_servicios` (
  `id_precio_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `id_servicio` varchar(20) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_precio_servicio`),
  KEY `id_servicio_precios_servicios_fk` (`id_servicio`),
  CONSTRAINT `id_servicio_precios_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `precios_servicios` WRITE;
INSERT INTO `precios_servicios` VALUES ('2', 'SERV-26125-00001-08', '22.00', '2026-05-28 14:34:00', '1');
UNLOCK TABLES;

-- Tabla: presentaciones
DROP TABLE IF EXISTS `presentaciones`;
CREATE TABLE `presentaciones` (
  `id_presentacion` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_presentacion`),
  KEY `id_unidad_medida_presentaciones_fk` (`id_unidad_medida`),
  CONSTRAINT `id_unidad_medida_presentaciones_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `presentaciones` WRITE;
INSERT INTO `presentaciones` VALUES ('PRES-26123-00001-28', '2', 'LITRO', '1.00', '1');
INSERT INTO `presentaciones` VALUES ('PRES-26159-00001-42', '2', 'BIDÓN', '20.00', '1');
INSERT INTO `presentaciones` VALUES ('PRES-26159-00002-78', '2', 'PIPA', '200.00', '1');
INSERT INTO `presentaciones` VALUES ('PRES-26159-00003-24', '2', 'GALÓN', '4.00', '1');
INSERT INTO `presentaciones` VALUES ('PRES-26177-00001-19', '2', 'CAJA - 1L', '12.00', '1');
INSERT INTO `presentaciones` VALUES ('PRES-26177-00002-40', '2', 'CAJA - GALÓN', '12.00', '1');
UNLOCK TABLES;

-- Tabla: presentaciones_materias_primas
DROP TABLE IF EXISTS `presentaciones_materias_primas`;
CREATE TABLE `presentaciones_materias_primas` (
  `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_presentacion` varchar(20) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia_prima_presentacion`),
  KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`),
  CONSTRAINT `id_materia_prima_materias_primas_presentaciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_presentacion_materias_primas_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `presentaciones_materias_primas` WRITE;
INSERT INTO `presentaciones_materias_primas` VALUES ('11', 'PRES-26123-00001-28', 'MATE-26123-00001-66', '1');
INSERT INTO `presentaciones_materias_primas` VALUES ('17', 'PRES-26123-00001-28', 'MATE-26149-00002-90', '0');
INSERT INTO `presentaciones_materias_primas` VALUES ('58', 'PRES-26123-00001-28', 'MATE-26149-00001-45', '0');
INSERT INTO `presentaciones_materias_primas` VALUES ('59', 'PRES-26123-00001-28', 'MATE-26157-00001-95', '1');
UNLOCK TABLES;

-- Tabla: presentaciones_productos
DROP TABLE IF EXISTS `presentaciones_productos`;
CREATE TABLE `presentaciones_productos` (
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_presentacion` varchar(20) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_presentacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_presentacion_producto`),
  KEY `id_presentacion_productos_presentaciones_fk` (`id_presentacion`),
  KEY `id_producto_productos_presentaciones_fk` (`id_producto`),
  CONSTRAINT `id_presentacion_productos_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_producto_productos_presentaciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `presentaciones_productos` WRITE;
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00007-41', 'PROD-26183-00001-56', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00008-04', 'PROD-26183-00001-56', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00009-24', 'PROD-26183-00001-56', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00010-84', 'PROD-26183-00001-56', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00011-58', 'PROD-26183-00001-56', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00012-24', 'PROD-26183-00001-56', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00013-65', 'PROD-26150-00001-39', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00014-72', 'PROD-26150-00001-39', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00015-86', 'PROD-26150-00001-39', 'PRES-26159-00002-78', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00016-39', 'PROD-26150-00001-39', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00017-07', 'PROD-26150-00001-39', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26183-00018-91', 'PROD-26150-00001-39', 'PRES-26177-00002-40', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00001-40', 'PROD-26222-00001-83', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00002-79', 'PROD-26222-00001-83', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00003-78', 'PROD-26222-00001-83', 'PRES-26159-00002-78', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00004-81', 'PROD-26222-00001-83', 'PRES-26159-00003-24', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00005-06', 'PROD-26222-00001-83', 'PRES-26177-00001-19', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00006-01', 'PROD-26222-00001-83', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00007-32', 'PROD-26222-00002-19', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00008-22', 'PROD-26222-00002-19', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00009-69', 'PROD-26222-00002-19', 'PRES-26159-00002-78', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00010-25', 'PROD-26222-00002-19', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00011-11', 'PROD-26222-00002-19', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00012-62', 'PROD-26222-00002-19', 'PRES-26177-00002-40', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00013-45', 'PROD-26222-00003-10', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00014-75', 'PROD-26222-00003-10', 'PRES-26159-00001-42', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00015-48', 'PROD-26222-00003-10', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00016-38', 'PROD-26222-00003-10', 'PRES-26159-00003-24', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00017-82', 'PROD-26222-00003-10', 'PRES-26177-00001-19', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00018-55', 'PROD-26222-00003-10', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00025-15', 'PROD-26222-00005-98', 'PRES-26123-00001-28', '1', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00026-98', 'PROD-26222-00005-98', 'PRES-26159-00001-42', '0', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00027-61', 'PROD-26222-00005-98', 'PRES-26159-00002-78', '1', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00028-84', 'PROD-26222-00005-98', 'PRES-26159-00003-24', '0', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00029-56', 'PROD-26222-00005-98', 'PRES-26177-00001-19', '1', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00030-45', 'PROD-26222-00005-98', 'PRES-26177-00002-40', '0', 'presentaciones_productos_2026_08_11_12_48_53_82.png', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00031-33', 'PROD-26222-00006-10', 'PRES-26123-00001-28', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00032-91', 'PROD-26222-00006-10', 'PRES-26159-00001-42', '0', 'presentaciones_productos_2026-08-11_12_51_14.png?v=2026-08-11_12_51_14', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00033-85', 'PROD-26222-00006-10', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00034-70', 'PROD-26222-00006-10', 'PRES-26159-00003-24', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00035-71', 'PROD-26222-00006-10', 'PRES-26177-00001-19', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00036-95', 'PROD-26222-00006-10', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00037-00', 'PROD-26222-00004-43', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00038-12', 'PROD-26222-00004-43', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00039-70', 'PROD-26222-00004-43', 'PRES-26159-00003-24', '1', 'presentaciones_productos_2026-08-11_13_57_40.png?v=2026-08-11_13_57_40', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00040-06', 'PROD-26222-00004-43', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26222-00041-20', 'PROD-26222-00004-43', 'PRES-26177-00002-40', '0', 'presentaciones_productos_2026-08-11_13_57_32.png?v=2026-08-11_13_57_32', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00001-84', 'PROD-26259-00001-46', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00002-90', 'PROD-26259-00001-46', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00003-78', 'PROD-26259-00001-46', 'PRES-26159-00002-78', '1', 'presentaciones_productos_2026-09-17_18_51_04.jpg?v=2026-09-17_18_51_04', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00004-26', 'PROD-26259-00001-46', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00005-18', 'PROD-26259-00001-46', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00006-07', 'PROD-26259-00001-46', 'PRES-26177-00002-40', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00007-39', 'PROD-26259-00002-49', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00008-95', 'PROD-26259-00002-49', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00009-22', 'PROD-26259-00002-49', 'PRES-26159-00002-78', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00010-11', 'PROD-26259-00002-49', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00011-29', 'PROD-26259-00002-49', 'PRES-26177-00001-19', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00012-00', 'PROD-26259-00002-49', 'PRES-26177-00002-40', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00013-48', 'PROD-26259-00003-43', 'PRES-26123-00001-28', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00014-34', 'PROD-26259-00003-43', 'PRES-26159-00001-42', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00015-30', 'PROD-26259-00003-43', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00016-37', 'PROD-26259-00003-43', 'PRES-26159-00003-24', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00017-08', 'PROD-26259-00003-43', 'PRES-26177-00001-19', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00018-39', 'PROD-26259-00003-43', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00019-95', 'PROD-26259-00004-17', 'PRES-26123-00001-28', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00020-17', 'PROD-26259-00004-17', 'PRES-26159-00001-42', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00021-28', 'PROD-26259-00004-17', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00022-02', 'PROD-26259-00004-17', 'PRES-26159-00003-24', '1', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00023-33', 'PROD-26259-00004-17', 'PRES-26177-00001-19', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00024-91', 'PROD-26259-00004-17', 'PRES-26177-00002-40', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00025-48', 'PROD-26259-00005-07', 'PRES-26123-00001-28', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00026-17', 'PROD-26259-00005-07', 'PRES-26159-00001-42', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00027-01', 'PROD-26259-00005-07', 'PRES-26159-00002-78', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00028-25', 'PROD-26259-00005-07', 'PRES-26159-00003-24', '0', '', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00029-32', 'PROD-26259-00005-07', 'PRES-26177-00001-19', '0', 'presentaciones_productos_2026-09-17_19_21_59.jpg?v=2026-09-17_19_21_59', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00030-54', 'PROD-26259-00005-07', 'PRES-26177-00002-40', '0', 'presentaciones_productos_2026-09-17_19_19_27.jpg?v=2026-09-17_19_19_27', '0');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00031-92', 'PROD-26259-00006-02', 'PRES-26123-00001-28', '0', '', '1');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00032-58', 'PROD-26259-00006-02', 'PRES-26159-00001-42', '0', '', '1');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00033-94', 'PROD-26259-00006-02', 'PRES-26159-00002-78', '0', '', '1');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00034-20', 'PROD-26259-00006-02', 'PRES-26159-00003-24', '0', '', '1');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00035-24', 'PROD-26259-00006-02', 'PRES-26177-00001-19', '0', '', '1');
INSERT INTO `presentaciones_productos` VALUES ('PRPR-26259-00036-63', 'PROD-26259-00006-02', 'PRES-26177-00002-40', '0', '', '1');
UNLOCK TABLES;

-- Tabla: producciones
DROP TABLE IF EXISTS `producciones`;
CREATE TABLE `producciones` (
  `id_produccion` varchar(20) NOT NULL,
  `fecha_produccion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_produccion`),
  KEY `fecha_produccion_indice` (`fecha_produccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `producciones` WRITE;
INSERT INTO `producciones` VALUES ('PROD-26125-00001-76', '2026-05-06 10:36:22', '1');
INSERT INTO `producciones` VALUES ('PROD-26137-00001-39', '2026-05-18 09:22:25', '1');
INSERT INTO `producciones` VALUES ('PROD-26149-00001-99', '2026-05-30 13:29:38', '1');
INSERT INTO `producciones` VALUES ('PROD-26150-00001-73', '2026-05-31 12:56:16', '1');
UNLOCK TABLES;

-- Tabla: productos
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `stock_producto` decimal(20,2) NOT NULL,
  `stock_minimo_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_producto`),
  KEY `id_unidad_medida_productos_fk` (`id_unidad_medida`),
  KEY `id_categoria_producto_productos_fk` (`id_categoria_producto`),
  CONSTRAINT `id_categoria_producto_productos_fk` FOREIGN KEY (`id_categoria_producto`) REFERENCES `categorias_productos` (`id_categoria_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_unidad_medida_productos_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `productos` WRITE;
INSERT INTO `productos` VALUES ('PROD-26150-00001-39', '2', '1', 'CLORO', '1.00', '600.00', '20.00', '0');
INSERT INTO `productos` VALUES ('PROD-26183-00001-56', '2', '1', 'CLORO2', '1.00', '100.00', '10.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00001-83', '2', '1', 'CLORO2', '1.00', '100.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00002-19', '2', '1', 'CLORO', '1.00', '87.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00003-10', '2', '1', 'CLORO2', '1.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00004-43', '2', '1', 'CLOROf', '1.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00005-98', '2', '2', 'CLOROm', '1.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26222-00006-10', '2', '1', 'CLOROs', '10.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00001-46', '2', '1', 'HIPOCLORITO', '1.00', '10.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00002-49', '2', '1', 'CLORO', '1.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00003-43', '2', '1', 'CLORO', '1.00', '0.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00004-17', '2', '1', 'CLORO', '1.00', '100.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00005-07', '2', '2', 'CLORO', '1.00', '1.00', '5.00', '0');
INSERT INTO `productos` VALUES ('PROD-26259-00006-02', '2', '1', 'CLORO', '10.00', '1.00', '5.00', '1');
UNLOCK TABLES;

-- Tabla: productos_compras
DROP TABLE IF EXISTS `productos_compras`;
CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_producto_compra`),
  KEY `id_producto_productos_compras_fk` (`id_presentacion_producto`),
  KEY `id_compra_productos_compras_fk` (`id_compra`),
  CONSTRAINT `id_compra_productos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_presentacion_producto_productos_compras_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: productos_ordenes_entregas_presupuestos
DROP TABLE IF EXISTS `productos_ordenes_entregas_presupuestos`;
CREATE TABLE `productos_ordenes_entregas_presupuestos` (
  `id_producto_factura` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_producto_factura`),
  KEY `id_presentacion_producto_poep_fk` (`id_presentacion_producto`),
  KEY `id_orden_entrega_presupuesto_poep_fk` (`id_orden_entrega_presupuesto`),
  CONSTRAINT `id_orden_entrega_presupuesto_poep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_presentacion_producto_poep_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `productos_ordenes_entregas_presupuestos` WRITE;
INSERT INTO `productos_ordenes_entregas_presupuestos` VALUES ('327', 'FACT-26190-00001-44', 'PRPR-26183-00015-86', '1.00', '1');
INSERT INTO `productos_ordenes_entregas_presupuestos` VALUES ('328', 'FACT-26221-00001-60', 'PRPR-26183-00015-86', '1.00', '1');
INSERT INTO `productos_ordenes_entregas_presupuestos` VALUES ('329', 'FACT-26227-00001-55', 'PRPR-26222-00007-32', '1.00', '1');
INSERT INTO `productos_ordenes_entregas_presupuestos` VALUES ('330', 'FACT-26258-00001-89', 'PRPR-26222-00012-62', '1.00', '1');
UNLOCK TABLES;

-- Tabla: productos_producciones
DROP TABLE IF EXISTS `productos_producciones`;
CREATE TABLE `productos_producciones` (
  `id_producto_produccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_produccion` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_producida` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_producto_produccion`),
  KEY `id_produccion_productos_producciones_fk` (`id_produccion`),
  KEY `id_producto_productos_producciones_fk` (`id_producto`),
  CONSTRAINT `id_produccion_productos_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_producto_productos_producciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `productos_producciones` WRITE;
INSERT INTO `productos_producciones` VALUES ('105', 'PROD-26150-00001-73', 'PROD-26150-00001-39', '10.00', '1');
UNLOCK TABLES;

-- Tabla: productos_servicios
DROP TABLE IF EXISTS `productos_servicios`;
CREATE TABLE `productos_servicios` (
  `id_producto_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_producto_servicio`),
  KEY `id_producto_productos_soep_fk` (`id_producto`),
  KEY `id_servicio_factura_psoep_fk` (`id_servicio`),
  CONSTRAINT `id_producto_productos_soep_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_serivicio_productos_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: proveedores
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `rif_proveedor` varchar(20) NOT NULL,
  `razon_social_proveedor` varchar(150) NOT NULL,
  `telefono_proveedor` varchar(11) NOT NULL,
  `correo_proveedor` varchar(150) NOT NULL,
  `direccion_proveedor` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`rif_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `proveedores` WRITE;
INSERT INTO `proveedores` VALUES ('123456789', 'ANDERSO', '04169484640', 'andersonfreitez60@gmail.com', 'HHHHBHBH', '0');
INSERT INTO `proveedores` VALUES ('1234567890', 'ANDERSONM', '04169484641', 'andersonfreitez6@gmail.com', 'DIRECCION', '0');
INSERT INTO `proveedores` VALUES ('30485684', 'ANDERSON', '04169484649', 'ANDERSONFREITEZ@GMAIL.COM', 'SANANA', '0');
INSERT INTO `proveedores` VALUES ('J085185330', 'Arcosan', '02512302345', 'Arcosanbarquisimeto@gamil.com', 'Calle 30 entre carreras 23 y 24 Barquisimeto Edo Lara.', '1');
INSERT INTO `proveedores` VALUES ('J12345611', 'ANDERSON9K', '04169484699', 'andersonfreitez6@gmail.co', 'kmkmkm', '0');
INSERT INTO `proveedores` VALUES ('J12345678', 'ANDERS', '04169484688', 'andersonfreitez9@gmail.com', 'eejejej', '0');
INSERT INTO `proveedores` VALUES ('J299682950', 'Mango Center', '', 'cajaprincipalcabudare@gmail.com', 'Av. Nectario Maria C.C Hipermercado Multimall Nivel Local del 02 al 08 Sector Tarabana Cabudare Edo. Lara', '1');
INSERT INTO `proveedores` VALUES ('J30485684', 'ANDERSON9', '04169484645', 'andersonfreitez61@gmail.co', 'jxjnjnj', '0');
INSERT INTO `proveedores` VALUES ('J4001234561', 'Comercializadora López 2018 C.A', '04245874877', 'Inversioneslopez@gmail.com', 'Carrera 24 entre calles 31 y 32. Barquisimeto Edo. Lara', '1');
INSERT INTO `proveedores` VALUES ('J4002345761', 'Ferretería Hermano Hernández', '04141347890', 'Ferreteriahernandez2020@Gmail.com', 'Calle 4 entre Av. La Mata y Av. 2 Cabudare Edo. Lara.', '1');
INSERT INTO `proveedores` VALUES ('J400602971', 'Químicos Alfonso', '', 'Adm.qalfonso@gmail.com', 'Calle 32 entre carreras 24 y 25 Barquisimeto Edo. Lara', '1');
INSERT INTO `proveedores` VALUES ('V1234567899', 'ANDERSONNN', '04169484647', 'andersonfreitez677@gmail.com', 'SANARE', '0');
INSERT INTO `proveedores` VALUES ('V30485684', 'ANDERSONNFA', '04169484648', 'andersonfreitez6@gmail.com', 'SANARESS', '0');
INSERT INTO `proveedores` VALUES ('V30485685', 'ANDERSON', '04169484640', 'andersonfreitez69@gmail.com', 'SANARE', '0');
UNLOCK TABLES;

-- Tabla: referencias_detalles_pagos
DROP TABLE IF EXISTS `referencias_detalles_pagos`;
CREATE TABLE `referencias_detalles_pagos` (
  `id_referencia_detalle_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_detalle_pago` int(11) NOT NULL,
  `referencia_pago` int(6) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_referencia_detalle_pago`),
  KEY `id_detalle_pago_referencias_detalles_pagos_fk` (`id_detalle_pago`),
  CONSTRAINT `id_detalle_pago_referencias_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `referencias_detalles_pagos` WRITE;
INSERT INTO `referencias_detalles_pagos` VALUES ('213', '264', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('214', '265', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('215', '266', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('216', '267', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('217', '268', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('218', '269', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('219', '270', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('220', '271', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('221', '272', '123456', '1');
INSERT INTO `referencias_detalles_pagos` VALUES ('222', '273', '123456', '1');
UNLOCK TABLES;

-- Tabla: repartidores
DROP TABLE IF EXISTS `repartidores`;
CREATE TABLE `repartidores` (
  `cedula_repartidor` varchar(20) NOT NULL,
  `nombre_repartidor` varchar(50) NOT NULL,
  `apellido_repartidor` varchar(50) NOT NULL,
  `telefono_repartidor` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cedula_repartidor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `repartidores` WRITE;
INSERT INTO `repartidores` VALUES ('V12344567', 'JOSE', 'JIMENEZ', '04161234567', '1');
INSERT INTO `repartidores` VALUES ('V30485654', 'ANDER', 'FREITEZ', '04169784646', '1');
INSERT INTO `repartidores` VALUES ('V30485683', 'ANDERSON', 'FREITEZ', '04169484648', '0');
INSERT INTO `repartidores` VALUES ('V30485684', 'ANDERSONn', 'FREITEZ', '04169484649', '1');
INSERT INTO `repartidores` VALUES ('V304856849', 'ANDERSON', 'FREITEZ', '04169484640', '0');
INSERT INTO `repartidores` VALUES ('V30485685', 'ANDERSON', 'FREITEZ', '04169484647', '0');
UNLOCK TABLES;

-- Tabla: rutas
DROP TABLE IF EXISTS `rutas`;
CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ruta` varchar(50) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `minimo_km_ruta` decimal(20,2) NOT NULL,
  `maximo_km_ruta` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_ruta`),
  KEY `minimo_km_ruta_indice` (`minimo_km_ruta`),
  KEY `maximo_km_ruta_indice` (`maximo_km_ruta`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `rutas` WRITE;
INSERT INTO `rutas` VALUES ('1', 'CERCANO', '1.50', '0.00', '5.00', '0');
INSERT INTO `rutas` VALUES ('2', 'LEJANO', '2.50', '6.00', '10.00', '0');
INSERT INTO `rutas` VALUES ('6', 'TERCERO', '3.50', '11.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('9', 'CERCANO', '0.50', '0.00', '5.00', '1');
INSERT INTO `rutas` VALUES ('10', 'LEJANO', '1.00', '6.00', '10.00', '1');
INSERT INTO `rutas` VALUES ('11', 'TERCERO', '2.00', '11.00', '100.00', '1');
INSERT INTO `rutas` VALUES ('71', 'OTRA MAS', '3.00', '100.00', '1000.00', '0');
INSERT INTO `rutas` VALUES ('72', 'OTRA MAS1', '3.00', '1.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('73', 'A DISTANCIA', '100.00', '100.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('74', 'UNO', '1.00', '10.00', '1.00', '0');
INSERT INTO `rutas` VALUES ('75', 'otram', '2.00', '100.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('76', 'ANDER RUTA', '100.00', '100.00', '1000.00', '0');
INSERT INTO `rutas` VALUES ('77', 'OTRA MAS', '0.20', '100.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('78', 'OTRA MAS', '2.00', '11.00', '100.00', '0');
INSERT INTO `rutas` VALUES ('79', 'OTRA MAS', '2.00', '100.00', '100.00', '0');
UNLOCK TABLES;

-- Tabla: servicios
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `id_servicio` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_servicio` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_servicio`),
  KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`),
  CONSTRAINT `id_unidad_medida_servicios_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `servicios` WRITE;
INSERT INTO `servicios` VALUES ('SERV-26125-00001-08', '2', 'FUMIGACION', '22.00', '0', '', '1');
UNLOCK TABLES;

-- Tabla: servicios_ordenes_entregas_presupuestos
DROP TABLE IF EXISTS `servicios_ordenes_entregas_presupuestos`;
CREATE TABLE `servicios_ordenes_entregas_presupuestos` (
  `id_servicio_factura` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cantidad_servicio` decimal(20,2) NOT NULL,
  `fecha_ejecucion` datetime NOT NULL,
  `es_precio_mapfre` tinyint(1) NOT NULL,
  `precio_servicio_mapfre` decimal(20,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_servicio_factura`),
  KEY `id_orden_entrega_presupuesto_soep_fk` (`id_orden_entrega_presupuesto`),
  KEY `id_servicio_soep_fk` (`id_servicio`),
  KEY `id_direccion_soep_fk` (`id_direccion`),
  CONSTRAINT `id_direccion_soep_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_orden_entrega_presupuesto_soep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_servicio_soep_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: sucursales_empresas_envios
DROP TABLE IF EXISTS `sucursales_empresas_envios`;
CREATE TABLE `sucursales_empresas_envios` (
  `id_sucursal_empresa_envios` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa_envios` int(11) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `nombre_sucursal_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_sucursal_empresa_envios`),
  KEY `id_empresa_envios_sucursales_empresas_envios_fk` (`id_empresa_envios`),
  KEY `id_direccion_sucursales_empresas_envios_fk` (`id_direccion`),
  CONSTRAINT `id_direccion_sucursales_empresas_envios_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_empresa_envios_sucursales_empresas_envios_fk` FOREIGN KEY (`id_empresa_envios`) REFERENCES `empresas_envios` (`id_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `sucursales_empresas_envios` WRITE;
INSERT INTO `sucursales_empresas_envios` VALUES ('3', '6', '28', 'METROPOLIS', '0');
INSERT INTO `sucursales_empresas_envios` VALUES ('4', '6', '28', 'METROPOLIS', '0');
INSERT INTO `sucursales_empresas_envios` VALUES ('5', '6', '28', 'METROPOLISA', '0');
INSERT INTO `sucursales_empresas_envios` VALUES ('6', '6', '28', 'METROPOLIS', '0');
INSERT INTO `sucursales_empresas_envios` VALUES ('7', '6', '30', 'BRM', '1');
INSERT INTO `sucursales_empresas_envios` VALUES ('8', '6', '31', 'AV INDUSTRIAS', '1');
INSERT INTO `sucursales_empresas_envios` VALUES ('9', '6', '32', 'LARAPLACE', '1');
INSERT INTO `sucursales_empresas_envios` VALUES ('10', '6', '33', 'AEROPUERTO JACINTO LARA', '1');
UNLOCK TABLES;

-- Tabla: unidades_medidas
DROP TABLE IF EXISTS `unidades_medidas`;
CREATE TABLE `unidades_medidas` (
  `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_unidad_medida` varchar(50) NOT NULL,
  `simbolo_unidad_medida` varchar(3) NOT NULL,
  `equivalencia_ub` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_unidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `unidades_medidas` WRITE;
INSERT INTO `unidades_medidas` VALUES ('1', 'KILO(S)', 'KG', '1000.00', '1');
INSERT INTO `unidades_medidas` VALUES ('2', 'LITRO(S)', 'L', '1000.00', '1');
INSERT INTO `unidades_medidas` VALUES ('3', 'MILLA', 'ML', '1000000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('4', 'MILL', 'ML', '1000000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('5', 'MILLA', 'M', '1000000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('6', 'MILLM', 'MIL', '1000000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('7', 'MILLA', 'ML', '2.00', '0');
INSERT INTO `unidades_medidas` VALUES ('8', 'MILLAS', 'MLA', '1000000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('9', 'METRO CUADRADO', 'M2', '1000.00', '1');
INSERT INTO `unidades_medidas` VALUES ('10', 'METROM', 'M', '1000.00', '0');
INSERT INTO `unidades_medidas` VALUES ('11', 'METRO CUADRADOm', 'M2m', '1000.00', '0');
UNLOCK TABLES;

-- Tabla: v_cambios_iva_todos
DROP TABLE IF EXISTS `v_cambios_iva_todos`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_cambios_monedas_todos
DROP TABLE IF EXISTS `v_cambios_monedas_todos`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_categorias_productos_todas
DROP TABLE IF EXISTS `v_categorias_productos_todas`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_clientes_todos
DROP TABLE IF EXISTS `v_clientes_todos`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_empresas_envios_todas
DROP TABLE IF EXISTS `v_empresas_envios_todas`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_repartidores_todos
DROP TABLE IF EXISTS `v_repartidores_todos`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

-- Tabla: v_unidades_medidas_todas
DROP TABLE IF EXISTS `v_unidades_medidas_todas`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

