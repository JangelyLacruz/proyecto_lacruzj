-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-11-2026 a las 16:44:51
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
-- Base de datos: `proyecto_lacruz_seguridad_pruebas`
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
    	SELECT  (pres.cantidad_pmp * posp.cantidad_producto) as cantidad_bruta, prod.stock_producto,prod.id_producto
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
		END LOOP mi_ciclo;
        CLOSE productosPedido; 
        END;
        
        -- Borrar los detalles del pago
        UPDATE pagos SET status = 0 WHERE id_orden_entrega_presupuesto = sp_id_pedido;
        IF(ROW_COUNT()<=0) THEN
          	ROLLBACK;
            SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'No se borraron los detalles del pago';
       	END IF;
 	END IF;
    
    -- Actualizamos en status del pedido
    UPDATE pedidos SET status = sp_estado WHERE id_orden_entrega_presupuesto = sp_id_pedido;
    IF(ROW_COUNT()<=0) THEN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'No se pudo actualizar el estado del pedido';
    END IF;
    
    COMMIT;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id_acceso`, `id_rol`, `id_modulo`, `id_permiso`, `status`) VALUES
(813, 1, 251, 618, 1),
(814, 1, 251, 619, 1),
(815, 1, 251, 620, 1),
(816, 1, 251, 621, 1),
(817, 1, 251, 622, 1),
(818, 1, 18, 622, 1),
(819, 1, 18, 621, 1),
(820, 1, 18, 620, 1),
(821, 1, 18, 619, 1),
(822, 1, 18, 618, 1),
(823, 1, 249, 618, 1),
(824, 1, 249, 619, 1),
(825, 1, 249, 620, 1),
(826, 1, 249, 621, 1),
(827, 1, 249, 622, 1),
(828, 1, 1, 621, 1),
(829, 1, 1, 622, 1),
(830, 1, 1, 620, 1),
(831, 1, 1, 619, 1),
(832, 1, 1, 618, 1),
(833, 1, 8, 618, 1),
(834, 1, 8, 619, 1),
(835, 1, 8, 620, 1),
(836, 1, 8, 621, 1),
(837, 1, 8, 622, 1),
(838, 1, 250, 622, 1),
(839, 1, 250, 621, 1),
(840, 1, 250, 620, 1),
(841, 1, 250, 619, 1),
(842, 1, 250, 618, 1),
(843, 1, 25, 618, 1),
(844, 1, 25, 619, 1),
(845, 1, 25, 620, 1),
(846, 1, 25, 621, 1),
(847, 1, 25, 622, 1),
(848, 1, 24, 622, 1),
(849, 1, 24, 621, 1),
(850, 1, 24, 619, 1),
(851, 1, 24, 620, 1),
(852, 1, 24, 618, 1),
(853, 1, 19, 618, 1),
(854, 1, 23, 618, 1),
(855, 1, 23, 619, 1),
(856, 1, 19, 619, 1),
(857, 1, 19, 620, 1),
(858, 1, 23, 620, 1),
(859, 1, 23, 621, 1),
(860, 1, 19, 621, 1),
(861, 1, 19, 622, 1),
(862, 1, 23, 622, 1),
(863, 1, 248, 618, 1),
(864, 1, 248, 619, 1),
(865, 1, 248, 620, 1),
(866, 1, 248, 621, 1),
(867, 1, 248, 622, 1),
(868, 1, 28, 622, 1),
(869, 1, 28, 621, 1),
(870, 1, 28, 620, 1),
(871, 1, 28, 619, 1),
(872, 1, 28, 618, 1),
(873, 1, 21, 618, 1),
(874, 1, 21, 619, 1),
(875, 1, 21, 620, 1),
(876, 1, 21, 621, 1),
(877, 1, 21, 622, 1),
(878, 1, 246, 622, 1),
(879, 1, 246, 621, 1),
(880, 1, 246, 620, 1),
(881, 1, 246, 619, 1),
(882, 1, 246, 618, 1),
(883, 1, 4, 618, 1),
(884, 1, 4, 619, 1),
(885, 1, 4, 620, 1),
(886, 1, 4, 621, 1),
(887, 1, 4, 622, 1),
(888, 1, 15, 622, 1),
(889, 1, 15, 621, 1),
(890, 1, 15, 620, 1),
(891, 1, 15, 619, 1),
(892, 1, 15, 618, 1),
(893, 1, 7, 618, 1),
(894, 1, 7, 619, 1),
(895, 1, 7, 620, 1),
(896, 1, 7, 621, 1),
(897, 1, 7, 622, 1),
(898, 1, 252, 618, 1),
(899, 1, 17, 618, 1),
(900, 1, 247, 618, 1),
(901, 1, 247, 619, 1),
(902, 1, 17, 619, 1),
(903, 1, 252, 619, 1),
(904, 1, 252, 620, 1),
(905, 1, 17, 620, 1),
(906, 1, 247, 620, 1),
(907, 1, 247, 621, 1),
(908, 1, 17, 621, 1),
(909, 1, 252, 621, 1),
(910, 1, 252, 622, 1),
(911, 1, 17, 622, 1),
(912, 1, 247, 622, 1),
(913, 1, 5, 618, 1),
(914, 1, 22, 618, 1),
(915, 1, 9, 618, 1),
(916, 1, 3, 618, 1),
(917, 1, 3, 619, 1),
(918, 1, 9, 619, 1),
(919, 1, 22, 619, 1),
(920, 1, 5, 619, 1),
(921, 1, 5, 620, 1),
(922, 1, 22, 620, 1),
(923, 1, 9, 620, 1),
(924, 1, 3, 620, 1),
(925, 1, 3, 621, 1),
(926, 1, 9, 621, 1),
(927, 1, 22, 621, 1),
(928, 1, 5, 621, 1),
(929, 1, 5, 622, 1),
(930, 1, 22, 622, 1),
(931, 1, 9, 622, 1),
(932, 1, 3, 622, 1),
(933, 1, 257, 618, 1),
(934, 1, 257, 619, 1),
(935, 1, 257, 620, 1),
(936, 1, 257, 621, 1),
(937, 1, 257, 622, 1),
(938, 1, 258, 618, 1),
(939, 1, 258, 619, 1),
(940, 1, 258, 620, 1),
(941, 1, 258, 621, 1),
(942, 1, 258, 622, 1),
(943, 1, 29, 623, 1),
(944, 1, 10, 625, 1),
(945, 1, 18, 627, 1),
(946, 1, 3, 629, 1),
(947, 1, 3, 631, 1),
(948, 1, 3, 633, 1),
(949, 1, 11, 635, 1),
(950, 1, 11, 637, 1),
(951, 1, 9, 639, 1),
(952, 1, 9, 641, 1),
(953, 1, 253, 643, 1),
(954, 1, 13, 645, 1),
(955, 1, 15, 644, 1),
(956, 1, 9, 642, 1),
(957, 1, 9, 640, 1),
(958, 1, 9, 638, 1),
(959, 1, 11, 636, 1),
(960, 1, 11, 634, 1),
(961, 1, 3, 632, 1),
(962, 1, 3, 630, 1),
(963, 1, 3, 628, 1),
(964, 1, 18, 626, 1),
(965, 1, 10, 624, 1),
(966, 1, 259, 618, 1),
(967, 1, 260, 618, 1),
(968, 1, 260, 619, 1),
(969, 1, 259, 619, 1),
(970, 1, 259, 620, 1),
(971, 1, 260, 620, 1),
(972, 1, 260, 621, 1),
(973, 1, 259, 621, 1),
(974, 1, 259, 622, 1),
(975, 1, 260, 622, 1),
(976, 1, 261, 618, 1),
(977, 1, 261, 619, 1),
(978, 1, 261, 620, 1),
(979, 1, 261, 621, 1),
(980, 1, 261, 622, 1),
(981, 1, 262, 618, 1),
(982, 1, 262, 619, 1),
(983, 1, 262, 620, 1),
(984, 1, 262, 621, 1),
(985, 1, 262, 622, 1),
(986, 1, 29, 618, 1),
(987, 1, 29, 619, 1),
(988, 1, 29, 621, 1),
(989, 1, 29, 620, 1),
(990, 1, 29, 622, 1),
(991, 1, 248, 647, 1),
(992, 1, 248, 648, 1),
(993, 1, 248, 649, 1),
(994, 1, 263, 618, 1),
(995, 1, 263, 619, 1),
(996, 1, 263, 620, 1),
(997, 1, 263, 621, 1),
(998, 1, 263, 622, 1),
(999, 1, 248, 650, 1),
(1000, 1, 264, 618, 1),
(1001, 1, 264, 619, 1),
(1002, 1, 264, 620, 1),
(1003, 1, 264, 621, 1),
(1004, 1, 264, 622, 1),
(1005, 1, 248, 651, 1),
(1006, 1, 248, 652, 1),
(1007, 2, 18, 618, 1),
(1008, 3, 18, 618, 0),
(1009, 3, 9, 618, 0),
(1010, 3, 8, 618, 0),
(1011, 3, 1, 618, 0),
(1012, 3, 249, 618, 0),
(1013, 3, 264, 618, 0),
(1014, 3, 251, 618, 0),
(1015, 3, 261, 618, 0),
(1016, 3, 261, 619, 1),
(1017, 3, 251, 619, 1),
(1018, 3, 18, 619, 1),
(1019, 3, 264, 619, 1),
(1020, 3, 249, 619, 1),
(1021, 3, 1, 619, 1),
(1022, 3, 8, 619, 1),
(1023, 3, 29, 618, 0),
(1024, 3, 250, 618, 0),
(1025, 3, 250, 619, 1),
(1026, 1, 248, 653, 1),
(1027, 3, 9, 622, 1),
(1028, 1, 267, 618, 1),
(1029, 1, 267, 619, 1),
(1030, 1, 267, 620, 1),
(1031, 1, 267, 621, 1),
(1032, 1, 303, 618, 1),
(1033, 1, 266, 619, 1),
(1034, 1, 266, 618, 1),
(1035, 1, 266, 620, 1),
(1036, 1, 266, 621, 1),
(1037, 1, 253, 655, 1),
(1038, 1, 253, 657, 1),
(1039, 1, 253, 656, 1),
(1040, 1, 253, 654, 1),
(1041, 1, 261, 658, 1),
(1042, 1, 261, 659, 1),
(1043, 1, 261, 660, 1),
(1047, 1, 248, 662, 1),
(1048, 1, 4, 663, 1),
(1049, 1, 23, 664, 1),
(1050, 1, 261, 665, 1),
(1051, 1, 308, 618, 1),
(1052, 1, 308, 620, 1),
(1053, 1, 308, 619, 1),
(1054, 1, 308, 621, 1),
(1055, 1, 308, 622, 1),
(1061, 1, 307, 618, 1),
(1062, 1, 307, 619, 1),
(1063, 1, 307, 620, 1),
(1064, 1, 307, 621, 1),
(1065, 1, 307, 622, 1),
(1066, 1, 303, 619, 1),
(1067, 1, 303, 620, 1),
(1068, 1, 303, 621, 1),
(1069, 1, 303, 622, 1),
(1070, 1, 307, 666, 1),
(1071, 1, 307, 667, 1),
(1072, 1, 309, 618, 1),
(1073, 1, 309, 619, 1),
(1074, 1, 309, 670, 1),
(1075, 1, 309, 620, 1),
(1076, 1, 309, 621, 1),
(1077, 1, 309, 622, 1),
(1078, 1, 309, 671, 1),
(1079, 1, 309, 675, 1),
(1080, 1, 309, 672, 1),
(1081, 1, 305, 618, 1),
(1082, 1, 305, 619, 1),
(1083, 1, 305, 620, 1),
(1084, 1, 305, 621, 1),
(1085, 1, 305, 622, 1),
(1086, 1, 311, 620, 1),
(1087, 1, 311, 619, 1),
(1088, 1, 311, 618, 1),
(1089, 1, 311, 621, 1),
(1090, 1, 311, 622, 1),
(1091, 1, 311, 674, 1),
(1092, 1, 1, 676, 1),
(1093, 1, 252, 677, 1),
(1094, 2, 261, 665, 1),
(1095, 2, 251, 619, 1),
(1096, 2, 251, 620, 1),
(1097, 2, 251, 621, 1),
(1098, 2, 251, 622, 1),
(1099, 2, 10, 624, 1),
(1100, 2, 10, 625, 1),
(1101, 2, 18, 619, 1),
(1102, 2, 18, 620, 1),
(1103, 2, 18, 626, 1),
(1104, 2, 18, 621, 1),
(1105, 2, 18, 622, 1),
(1106, 2, 18, 627, 1),
(1107, 2, 264, 618, 1),
(1108, 2, 264, 619, 1),
(1109, 2, 264, 620, 1),
(1110, 2, 264, 621, 1),
(1111, 2, 264, 622, 1),
(1112, 2, 249, 618, 1),
(1113, 2, 249, 619, 1),
(1114, 2, 249, 620, 1),
(1115, 2, 249, 621, 1),
(1116, 2, 249, 622, 1),
(1117, 2, 307, 618, 1),
(1118, 2, 307, 619, 1),
(1119, 2, 307, 620, 1),
(1120, 2, 307, 621, 1),
(1121, 2, 307, 622, 1),
(1122, 2, 307, 667, 1),
(1123, 2, 307, 666, 1),
(1124, 2, 1, 618, 1),
(1125, 2, 1, 619, 1),
(1126, 2, 1, 620, 1),
(1127, 2, 1, 621, 1),
(1128, 2, 1, 622, 1),
(1129, 2, 1, 676, 1),
(1130, 2, 8, 618, 1),
(1131, 2, 8, 619, 1),
(1132, 2, 8, 620, 1),
(1133, 2, 8, 621, 1),
(1134, 2, 8, 622, 1),
(1135, 2, 29, 618, 1),
(1136, 2, 29, 619, 1),
(1137, 2, 29, 620, 1),
(1138, 2, 29, 621, 1),
(1139, 2, 29, 622, 1),
(1140, 2, 29, 623, 1),
(1141, 2, 250, 618, 1),
(1142, 2, 250, 619, 1),
(1143, 2, 250, 620, 1),
(1144, 2, 250, 621, 1),
(1145, 2, 250, 622, 1),
(1146, 2, 3, 618, 1),
(1147, 2, 3, 619, 1),
(1148, 2, 3, 620, 1),
(1149, 2, 3, 621, 1),
(1150, 2, 3, 622, 1),
(1151, 2, 266, 618, 1),
(1152, 2, 266, 619, 1),
(1153, 2, 266, 620, 1),
(1154, 2, 266, 621, 1),
(1155, 2, 266, 622, 1),
(1156, 2, 25, 618, 1),
(1157, 2, 25, 619, 1),
(1158, 2, 25, 620, 1),
(1159, 2, 25, 621, 1),
(1160, 2, 25, 622, 1),
(1161, 2, 253, 643, 1),
(1162, 2, 253, 655, 1),
(1163, 2, 253, 656, 1),
(1164, 2, 253, 654, 1),
(1165, 2, 253, 657, 1),
(1166, 2, 258, 619, 1),
(1167, 2, 258, 618, 1),
(1168, 2, 258, 620, 1),
(1169, 2, 258, 621, 1),
(1170, 2, 258, 622, 1),
(1171, 2, 24, 618, 1),
(1172, 2, 24, 619, 1),
(1173, 2, 24, 620, 1),
(1174, 2, 24, 621, 1),
(1175, 2, 24, 622, 1),
(1176, 2, 267, 618, 1),
(1177, 2, 267, 619, 1),
(1178, 2, 267, 620, 1),
(1179, 2, 267, 621, 1),
(1180, 2, 267, 622, 1),
(1181, 2, 19, 618, 1),
(1182, 2, 19, 619, 1),
(1183, 2, 19, 620, 1),
(1184, 2, 19, 621, 1),
(1185, 2, 19, 622, 1),
(1186, 2, 303, 618, 1),
(1187, 2, 303, 619, 1),
(1188, 2, 303, 620, 1),
(1189, 2, 303, 621, 1),
(1190, 2, 303, 622, 1),
(1191, 2, 262, 618, 0),
(1192, 2, 262, 619, 1),
(1193, 2, 262, 620, 0),
(1194, 2, 262, 621, 0),
(1195, 2, 262, 622, 0),
(1196, 2, 23, 618, 1),
(1197, 2, 23, 619, 1),
(1198, 2, 23, 620, 1),
(1199, 2, 23, 621, 1),
(1200, 2, 23, 622, 1),
(1201, 2, 23, 664, 1),
(1202, 2, 309, 618, 1),
(1203, 2, 309, 619, 1),
(1204, 2, 309, 620, 1),
(1205, 2, 309, 621, 1),
(1206, 2, 309, 622, 1),
(1207, 2, 309, 671, 1),
(1208, 2, 309, 675, 1),
(1209, 2, 309, 670, 1),
(1210, 2, 309, 672, 1),
(1211, 2, 260, 618, 1),
(1212, 2, 260, 619, 1),
(1213, 2, 260, 620, 1),
(1214, 2, 260, 621, 1),
(1215, 2, 260, 622, 1),
(1216, 2, 259, 618, 1),
(1217, 2, 259, 619, 1),
(1218, 2, 259, 620, 1),
(1219, 2, 259, 621, 1),
(1220, 2, 259, 622, 1),
(1221, 2, 248, 618, 1),
(1222, 2, 248, 619, 1),
(1223, 2, 248, 620, 1),
(1224, 2, 248, 621, 1),
(1225, 2, 248, 622, 1),
(1226, 2, 248, 648, 1),
(1227, 2, 248, 647, 1),
(1228, 2, 248, 650, 1),
(1229, 2, 248, 662, 1),
(1230, 2, 248, 649, 1),
(1231, 2, 248, 653, 1),
(1232, 2, 248, 652, 1),
(1233, 2, 248, 651, 1),
(1234, 2, 28, 618, 0),
(1235, 2, 28, 619, 1),
(1236, 2, 308, 619, 1),
(1237, 2, 21, 619, 1),
(1238, 2, 21, 618, 1),
(1239, 2, 21, 620, 1),
(1240, 2, 21, 621, 1),
(1241, 2, 21, 622, 1),
(1242, 2, 305, 618, 1),
(1243, 2, 305, 619, 1),
(1244, 2, 305, 620, 1),
(1245, 2, 305, 621, 1),
(1246, 2, 305, 622, 1),
(1247, 2, 246, 618, 1),
(1248, 2, 246, 619, 1),
(1249, 2, 246, 620, 1),
(1250, 2, 246, 621, 1),
(1251, 2, 246, 622, 1),
(1252, 2, 4, 618, 1),
(1253, 2, 4, 619, 1),
(1254, 2, 4, 620, 1),
(1255, 2, 4, 621, 1),
(1256, 2, 4, 622, 1),
(1257, 2, 4, 663, 1),
(1258, 2, 15, 618, 1),
(1259, 2, 15, 619, 1),
(1260, 2, 15, 620, 1),
(1261, 2, 15, 621, 1),
(1262, 2, 15, 622, 1),
(1263, 2, 7, 618, 1),
(1264, 2, 7, 619, 1),
(1265, 2, 7, 620, 1),
(1266, 2, 7, 621, 1),
(1267, 2, 7, 622, 1),
(1268, 2, 252, 618, 1),
(1269, 2, 252, 619, 1),
(1270, 2, 252, 620, 1),
(1271, 2, 252, 621, 1),
(1272, 2, 252, 622, 1),
(1273, 2, 252, 677, 1),
(1274, 2, 11, 634, 1),
(1275, 2, 11, 636, 1),
(1276, 2, 11, 635, 1),
(1277, 2, 11, 637, 1),
(1278, 2, 311, 618, 1),
(1279, 2, 311, 620, 1),
(1280, 2, 311, 619, 1),
(1281, 2, 311, 621, 1),
(1282, 2, 311, 622, 1),
(1283, 2, 311, 674, 1),
(1284, 2, 17, 619, 1),
(1285, 2, 247, 618, 1),
(1286, 2, 247, 619, 1),
(1287, 2, 247, 620, 1),
(1288, 2, 247, 621, 1),
(1289, 2, 247, 622, 1),
(1290, 2, 5, 618, 1),
(1291, 2, 5, 619, 1),
(1292, 2, 5, 620, 1),
(1293, 2, 5, 621, 1),
(1294, 2, 5, 622, 1),
(1295, 2, 257, 618, 1),
(1296, 2, 257, 619, 1),
(1297, 2, 257, 620, 1),
(1298, 2, 257, 621, 1),
(1299, 2, 257, 622, 1),
(1300, 2, 22, 619, 1),
(1301, 2, 22, 618, 1),
(1302, 2, 22, 620, 1),
(1303, 2, 22, 621, 1),
(1304, 2, 22, 622, 1),
(1305, 2, 9, 618, 1),
(1306, 2, 9, 619, 1),
(1307, 2, 9, 620, 1),
(1308, 2, 9, 621, 1),
(1309, 2, 9, 622, 1),
(1310, 2, 9, 638, 1),
(1311, 2, 9, 640, 1),
(1312, 2, 9, 639, 1),
(1313, 2, 9, 641, 1),
(1314, 2, 9, 642, 1),
(1315, 3, 261, 665, 1),
(1316, 3, 3, 619, 1),
(1317, 3, 266, 619, 1),
(1318, 3, 25, 619, 1),
(1319, 3, 258, 619, 1),
(1320, 3, 24, 619, 1),
(1321, 3, 267, 619, 1),
(1322, 3, 267, 620, 1),
(1323, 3, 19, 619, 1),
(1324, 3, 303, 619, 1),
(1325, 3, 262, 619, 1),
(1326, 3, 23, 619, 1),
(1327, 3, 309, 619, 1),
(1328, 3, 260, 619, 1),
(1329, 3, 259, 619, 1),
(1330, 3, 248, 619, 1),
(1331, 3, 28, 619, 1),
(1332, 3, 308, 619, 1),
(1333, 3, 21, 619, 1),
(1334, 3, 305, 619, 1),
(1335, 3, 246, 619, 1),
(1336, 3, 4, 619, 1),
(1337, 3, 15, 619, 1),
(1338, 3, 7, 619, 1),
(1339, 3, 252, 619, 1),
(1340, 3, 11, 635, 0),
(1341, 3, 311, 619, 0),
(1342, 3, 17, 619, 1),
(1343, 3, 247, 619, 1),
(1344, 3, 5, 619, 1),
(1345, 3, 257, 619, 1),
(1346, 3, 22, 619, 1),
(1347, 3, 9, 619, 1),
(1348, 3, 9, 621, 1),
(1349, 3, 9, 639, 1),
(1350, 3, 9, 641, 1),
(1351, 3, 9, 642, 1),
(1352, 3, 9, 640, 1),
(1353, 3, 248, 618, 1),
(1354, 3, 248, 620, 1),
(1355, 3, 248, 651, 1),
(1356, 3, 248, 647, 1),
(1357, 35, 251, 618, 1),
(1358, 35, 251, 619, 1),
(1359, 35, 253, 643, 1),
(1360, 35, 253, 654, 1),
(1361, 35, 1, 618, 1),
(1362, 35, 1, 619, 1),
(1363, 1, 309, 679, 1),
(1364, 1, 309, 679, 1),
(1365, 2, 309, 679, 1),
(1366, 2, 309, 679, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones_resagadas_usuarios`
--

CREATE TABLE `acciones_resagadas_usuarios` (
  `id_accion_resagada_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `accion_resagada` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones_resagadas_usuarios`
--

INSERT INTO `acciones_resagadas_usuarios` (`id_accion_resagada_usuario`, `id_modulo`, `cedula_usuario`, `accion_resagada`, `status`) VALUES
(1139, 9, 'V30485688', 'borrarDataModuloSS', 1),
(1140, 9, 'V30485688', 'actDT', 1),
(1141, 9, 'V30485681', 'borrarDataModuloSS', 1),
(1143, 9, 'V30485681', 'actDT', 1),
(1151, 248, 'V30485681', 'borrarDataModuloSS', 1),
(1153, 248, 'V30485681', 'actDT', 1),
(1159, 247, 'V30485681', 'borrarDataModuloSS', 1),
(1161, 247, 'V30485681', 'actDT', 1),
(1167, 250, 'V30485681', 'borrarDataModuloSS', 1),
(1169, 250, 'V30485681', 'actDT', 1),
(1191, 19, 'V30485681', 'borrarDataModuloSS', 1),
(1193, 19, 'V30485681', 'actDT', 1),
(1227, 4, 'V30485681', 'borrarDataModuloSS', 1),
(1229, 4, 'V30485681', 'actDT', 1),
(1285, 9, 'V30485631', 'actDT', 1),
(1289, 4, 'V30485631', 'actDT', 1),
(1301, 9, 'V30485631', 'borrarDataModuloSS', 1),
(1319, 9, 'V29910249', 'actDT', 1),
(1322, 248, 'V15265756', 'actDT', 1),
(1326, 9, 'V29910249', 'borrarDataModuloSS', 1),
(1341, 19, 'V29910249', 'borrarDataModuloSS', 1),
(1344, 19, 'V15265756', 'actDT', 1),
(1345, 19, 'V29910249', 'actDT', 1),
(1349, 253, 'V29910249', 'borrarDataModuloSS', 1),
(1351, 253, 'V11429340', 'actDT', 1),
(1352, 253, 'V15265756', 'actDT', 1),
(1353, 253, 'V29910249', 'actDT', 1),
(1354, 253, 'V30485684', 'actDT', 1),
(1356, 9, 'V15265757', 'borrarDataModuloSS', 1),
(1358, 9, 'V15265757', 'actDT', 1),
(1363, 9, 'V31317406', 'borrarDataModuloSS', 1),
(1364, 9, 'V31317406', 'actDT', 1),
(1367, 4, 'V15265757', 'borrarDataModuloSS', 1),
(1368, 4, 'V29910249', 'borrarDataModuloSS', 1),
(1370, 4, 'V31317406', 'borrarDataModuloSS', 1),
(1372, 4, 'V15265756', 'actDT', 1),
(1373, 4, 'V15265757', 'actDT', 1),
(1374, 4, 'V29910249', 'actDT', 1),
(1376, 4, 'V31317406', 'actDT', 1),
(1389, 9, 'V12345678', 'actDT', 1),
(1390, 4, 'V12345678', 'borrarDataModuloSS', 1),
(1392, 4, 'V12345678', 'actDT', 1),
(1423, 4, 'V30485684', 'actDT', 1),
(1461, 4, 'V15265756', 'borrarDataModuloSS', 1),
(1474, 19, 'V12345678', 'borrarDataModuloSS', 1),
(1475, 19, 'V15265756', 'borrarDataModuloSS', 1),
(1476, 19, 'V15265757', 'borrarDataModuloSS', 1),
(1478, 19, 'V31317406', 'borrarDataModuloSS', 1),
(1479, 19, 'V12345678', 'actDT', 1),
(1480, 19, 'V15265757', 'actDT', 1),
(1481, 19, 'V30485684', 'actDT', 1),
(1482, 19, 'V31317406', 'actDT', 1),
(1509, 253, 'V11429340', 'borrarDataModuloSS', 1),
(1510, 253, 'V12345678', 'borrarDataModuloSS', 1),
(1511, 253, 'V15265756', 'borrarDataModuloSS', 1),
(1512, 253, 'V15265757', 'borrarDataModuloSS', 1),
(1513, 253, 'V30485684', 'borrarDataModuloSS', 1),
(1514, 253, 'V31317406', 'borrarDataModuloSS', 1),
(1515, 253, 'V12345678', 'actDT', 1),
(1516, 253, 'V15265757', 'actDT', 1),
(1517, 253, 'V31317406', 'actDT', 1),
(1518, 9, 'V11429340', 'borrarDataModuloSS', 1),
(1519, 9, 'V12345678', 'borrarDataModuloSS', 1),
(1520, 9, 'V15265756', 'borrarDataModuloSS', 1),
(1522, 9, 'V72600792', 'borrarDataModuloSS', 1),
(1523, 9, 'V11429340', 'actDT', 1),
(1524, 9, 'V15265756', 'actDT', 1),
(1526, 9, 'V72600792', 'actDT', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `resultado_bitacora` varchar(50) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `ip_dispositivo` varchar(25) NOT NULL,
  `cambios_efectuados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fecha_bitacora` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(2247, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-02 19:16:47\"}}', '2026-08-02 19:16:47', 1),
(2248, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:20:56\"}}', '2026-08-03 15:20:56', 1),
(2249, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:37:44\"}}', '2026-08-03 15:37:44', 1),
(2250, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:26:26\"}}', '2026-08-03 16:26:26', 1),
(2251, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:34:09\"}}', '2026-08-03 16:34:09', 1),
(2252, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:44:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-03 16:44:58', 1),
(2253, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-06 14:34:11\"}}', '2026-08-06 14:34:11', 1),
(2254, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:27:54\"}}', '2026-08-07 11:27:54', 1),
(2255, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:45:45\"}}', '2026-08-07 11:45:45', 1),
(2256, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:48:07\"}}', '2026-08-07 11:48:07', 1),
(2257, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:50:21\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 11:50:21', 1),
(2258, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:52:24\"}}', '2026-08-07 11:52:24', 1),
(2259, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:59:48\"}}', '2026-08-07 11:59:48', 1),
(2260, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:02:20\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 12:02:20', 1),
(2261, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:04:32\"}}', '2026-08-07 12:04:32', 1),
(2262, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:11:47\"}}', '2026-08-07 12:11:47', 1),
(2263, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:29:25\"}}', '2026-08-07 12:29:25', 1),
(2264, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:57:18\"}}', '2026-08-07 12:57:19', 1),
(2265, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:17:54\"}}', '2026-08-07 13:17:54', 1),
(2266, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:20:00\"}}', '2026-08-07 13:20:00', 1),
(2267, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:24:42\"}}', '2026-08-07 14:24:42', 1),
(2268, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:31:05\"}}', '2026-08-07 14:31:05', 1),
(2269, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:34:53\"}}', '2026-08-07 14:34:53', 1),
(2270, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:36:19\"}}', '2026-08-07 14:36:19', 1),
(2271, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:39:01\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:39:01', 1),
(2272, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:40:56\"}}', '2026-08-07 14:40:56', 1),
(2273, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:44:05\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:44:05', 1),
(2274, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:53:46\"}}', '2026-08-07 14:48:46', 1),
(2275, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:57:49\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:52:49', 1),
(2276, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:01:59\"}}', '2026-08-07 14:56:59', 1),
(2277, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:07:59\"}}', '2026-08-07 15:02:59', 1),
(2278, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:24:04\"}}', '2026-08-07 15:19:04', 1),
(2279, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:29:35\"}}', '2026-08-07 15:24:35', 1),
(2280, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 11:44:29\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 11:39:29', 1),
(2281, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:13', 1),
(2282, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:55', 1),
(2283, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:59', 1),
(2284, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:13:55\"}}', '2026-08-08 12:08:55', 1),
(2285, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:30:09\"}}', '2026-08-08 12:25:09', 1),
(2286, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:31:23\"}}', '2026-08-08 13:26:23', 1),
(2287, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:42:01\"}}', '2026-08-08 13:37:01', 1),
(2288, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:53:41\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 13:48:41', 1),
(2289, 'V30485684', 28, 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pagos\"}}', '2026-08-08 13:49:57', 1),
(2290, 'V30485684', 28, 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pago\"}}', '2026-08-08 13:50:09', 1),
(2291, 'V30485684', 28, 'Éxito', 'registrar', '190.97.229.57', '{\"id_permiso\":{\"Registrado\":673},\"nombre_permiso\":{\"Registrado\":\"permisox\"},\"status\":{\"Registrado\":1}}', '2026-08-08 13:50:22', 1),
(2292, 'V30485684', 28, 'Éxito', 'Eliminar con id 673', '190.97.229.57', NULL, '2026-08-08 13:50:26', 1),
(2294, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 16:41:32\"}}', '2026-08-08 16:36:32', 1),
(2295, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 17:30:34\"}}', '2026-08-08 17:25:34', 1),
(2296, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:25:37\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:20:37', 1),
(2297, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:34:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:29:26', 1),
(2298, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:35:09\"}}', '2026-08-10 14:30:09', 1),
(2299, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:47:03\"}}', '2026-08-10 14:42:03', 1),
(2300, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 15:04:51\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:59:51', 1),
(2301, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:17:40\"}}', '2026-08-10 16:12:40', 1),
(2302, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:29:33\"}}', '2026-08-10 16:24:33', 1),
(2303, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfremitez6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1239\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$petVY5x\\/7OGMh1gk\\/PuBt.R2SxsI20QXItsUrfqIG.XrEk4aScQ5m\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:45:17', 1),
(2304, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:01:59\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 16:56:59', 1),
(2305, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V30485681', '181.208.252.213', '{\"cedula_usuario\":{\"Registrado\":\"V30485681\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitez66@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484643\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1236\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk\\/y0bWnjNde\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_58_55_14.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:58:57', 1),
(2306, 'V30485684', 9, 'Éxito', 'Actualizar usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"nombre_usuario\":{\"Modificado\":\"Andersoa\"},\"cedula_usuario\":{\"Eliminado\":\"V30485688\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:16', 1),
(2307, 'V30485684', 9, 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"foto_usuario\":{\"name\":{\"Registrado\":\"Imagen2.jpg\"},\"full_path\":{\"Registrado\":\"Imagen2.jpg\"},\"type\":{\"Registrado\":\"image\\/jpeg\"},\"tmp_name\":{\"Registrado\":\"C:\\\\xampp\\\\tmp\\\\phpCA63.tmp\"},\"error\":{\"Registrado\":0},\"size\":{\"Registrado\":134384}},\"fofo_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:26', 1),
(2308, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485688', '181.208.252.213', NULL, '2026-08-10 17:00:36', 1),
(2309, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:21:15\"}}', '2026-08-10 17:16:15', 1),
(2310, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:27:17\"}}', '2026-08-10 17:22:17', 1),
(2311, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:36:12\"}}', '2026-08-10 17:31:12', 1),
(2312, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:45:38\"}}', '2026-08-10 17:40:38', 1),
(2313, 'V30485684', 248, 'Éxito', 'registrar', '181.208.252.213', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26183-00015-86\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":304}}],\"delivery\":{\"latitud\":{\"Registrado\":9.93103},\"longitud\":{\"Registrado\":-69.621948}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_10_17_44_30_56.jpg\"]}]}', '2026-08-10 17:44:32', 1),
(2314, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-08-10 17:47:34', 1),
(2315, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":8}}', '2026-08-10 17:48:13', 1),
(2317, 'V30485684', 247, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"100\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-10 17:55:28', 1),
(2318, 'V30485684', 247, 'Éxito', 'Actualizar', '190.97.229.57', '{\"precio_ruta\":{\"Modificado\":\"0.20\"}}', '2026-08-10 17:55:38', 1),
(2319, 'V30485684', 247, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-10 17:55:45', 1),
(2320, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:09:37\"}}', '2026-08-10 18:04:37', 1),
(2321, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:22:58\"}}', '2026-08-10 18:17:58', 1),
(2322, 'V30485684', 250, 'Éxito', 'Registrar', '181.208.252.213', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMM\"}}', '2026-08-10 18:25:23', 1),
(2323, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:30:58\"}}', '2026-08-10 18:25:58', 1),
(2324, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:26:39', 1),
(2325, 'V30485684', 250, 'Éxito', 'actualizar Empresa de envíos con id: 38', '190.97.229.57', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMmm.\"}}', '2026-08-10 18:27:01', 1),
(2326, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:31:06', 1),
(2327, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 39', '190.97.229.57', NULL, '2026-08-10 18:31:10', 1),
(2328, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 38', '190.97.229.57', NULL, '2026-08-10 18:31:13', 1),
(2329, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 37', '190.97.229.57', NULL, '2026-08-10 18:31:17', 1),
(2330, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:19:22\"}}', '2026-08-11 11:14:22', 1),
(2331, 'V30485684', 247, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"11\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-11 11:15:25', 1),
(2332, 'V30485684', 247, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 11:15:41', 1),
(2333, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMs\"}}', '2026-08-11 11:15:59', 1),
(2334, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 40', '190.97.229.57', NULL, '2026-08-11 11:16:03', 1),
(2335, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:45:30\"}}', '2026-08-11 11:40:30', 1),
(2336, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:53:02\"}}', '2026-08-11 11:48:02', 1),
(2337, 'V30485684', 19, 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOm\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":1},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 11:50:12', 1),
(2338, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"nombre_metodo_pago\":{\"Modificado\":\"EFECTIVOmm\"}}', '2026-08-11 11:54:44', 1),
(2339, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:54:59', 1),
(2340, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:45', 1),
(2341, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:57', 1),
(2342, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:09:33\"}}', '2026-08-11 12:04:33', 1),
(2343, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 12:04:51', 1),
(2344, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_moneda\":{\"Modificado\":0},\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:07:35', 1),
(2345, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0}}', '2026-08-11 12:07:43', 1),
(2346, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:07:49', 1),
(2347, 'V30485684', 19, 'Éxito', 'registtrar', '181.208.252.213', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOs\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 12:08:10', 1),
(2348, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:09:39', 1),
(2349, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:09:52', 1),
(2350, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:18:14\"}}', '2026-08-11 12:13:14', 1),
(2351, 'V30485684', 19, 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOb\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-08-11 12:13:43', 1),
(2352, 'V30485684', 19, 'Éxito', 'Actualizar', '190.97.229.57', '{\"necesita_banco_emisor\":{\"Modificado\":1},\"necesita_banco_receptor\":{\"Modificado\":1},\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:15:24', 1),
(2353, 'V30485684', 19, 'Éxito', 'Actualizar', '190.97.229.57', '{\"mostrar_ecommerce\":{\"Modificado\":1}}', '2026-08-11 12:15:46', 1),
(2354, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":1}}', '2026-08-11 12:17:52', 1),
(2355, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:17:56', 1),
(2356, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:02', 1),
(2357, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:06', 1),
(2358, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:18:27', 1),
(2359, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0}}', '2026-08-11 12:18:54', 1),
(2360, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00001-86\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00002-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00003-67\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00004-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00005-00\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00006-27\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:22:42', 1),
(2361, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:32:46\"}}', '2026-08-11 12:27:46', 1),
(2362, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00006-27)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_26.jpg?v=2026-08-11_12_33_26\"}}', '2026-08-11 12:33:26', 1),
(2363, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00005-00)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_44.jpg?v=2026-08-11_12_33_44\"}}', '2026-08-11 12:33:44', 1),
(2364, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00001-40\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00003-78\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00004-81\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00005-06\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00006-01\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"2.00\"}}]}}', '2026-08-11 12:34:12', 1),
(2365, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:34:38', 1),
(2366, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:36:17', 1),
(2367, 'V30485684', 4, 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00009-69\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00010-25\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00011-11\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00012-62\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:05', 1),
(2368, 'V30485684', 4, 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00013-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00014-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00015-48\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00016-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00017-82\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00018-55\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:59', 1),
(2369, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:44:05\"}}', '2026-08-11 12:39:05', 1),
(2370, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROf\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00019-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00020-14\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00021-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00022-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00023-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00024-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:48:20', 1),
(2371, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLOROm\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00025-15\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00026-98\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00027-61\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00028-84\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00029-56\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00030-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-08-11 12:48:53', 1),
(2372, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROs\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00031-33\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_50_58_55.jpg\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00032-91\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00033-85\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00034-70\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00035-71\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00036-95\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-08-11 12:50:58', 1),
(2373, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00032-91)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_51_14.png?v=2026-08-11_12_51_14\"}}', '2026-08-11 12:51:14', 1),
(2374, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:52:04', 1),
(2375, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:53:54', 1),
(2376, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:10:17\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:05:17', 1),
(2377, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-23\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-74\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-91\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-84\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-33\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:05:38', 1),
(2378, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00042-33)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_05_54.png?v=2026-08-11_13_05_54\"}}', '2026-08-11 13:05:54', 1),
(2379, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-09\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-77\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-44\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-60\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:06:02', 1),
(2380, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-85\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-61\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-26\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-64\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-74\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-30\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:43', 1),
(2381, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-27\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-10\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-14\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-16\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-48\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-85\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:58', 1),
(2382, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:18:52\"}}', '2026-08-11 13:13:52', 1),
(2383, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:30:45\"}}', '2026-08-11 13:25:46', 1),
(2384, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-67\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-54\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-27\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-32\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:26:08', 1),
(2385, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:45:19\"}}', '2026-08-11 13:40:19', 1),
(2386, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:55:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:50:26', 1),
(2387, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-12\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-22\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-89\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-07\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-17\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":212}}]}}', '2026-08-11 13:56:49', 1),
(2388, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-68\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-34\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-45\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-23\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00041-07\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Eliminado\":1},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00042-17\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":213}}]}}', '2026-08-11 13:56:57', 1),
(2389, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-00\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-12\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-70\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-06\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00041-20\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":214}}]}}', '2026-08-11 13:57:05', 1),
(2390, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00041-20)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_32.png?v=2026-08-11_13_57_32\"}}', '2026-08-11 13:57:32', 1),
(2391, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00039-70)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_40.png?v=2026-08-11_13_57_40\"}}', '2026-08-11 13:57:40', 1),
(2392, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:14:01\"}}', '2026-08-11 14:09:01', 1),
(2393, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:15:35\"}}', '2026-08-11 14:10:35', 1),
(2394, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 15:28:51\"}}', '2026-08-11 15:23:51', 1),
(2395, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-12 11:05:48\"}}', '2026-08-12 11:00:48', 1),
(2396, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:03:33\"}}', '2026-08-14 10:58:33', 1),
(2397, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:04:40\"}}', '2026-08-14 10:59:40', 1),
(2398, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:07:14\"}}', '2026-08-14 11:02:14', 1),
(2399, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:18:46\"}}', '2026-08-14 11:13:47', 1),
(2400, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:25:05\"}}', '2026-08-14 11:20:05', 1),
(2401, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:08:33\"}}', '2026-08-14 12:03:33', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(2402, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:23:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 12:18:58', 1),
(2403, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:42:03\"}}', '2026-08-14 12:37:03', 1),
(2404, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:59:48\"}}', '2026-08-14 12:54:48', 1),
(2405, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:15:12\"}}', '2026-08-14 13:10:12', 1),
(2406, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:40:53\"}}', '2026-08-14 13:35:53', 1),
(2407, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:56:25\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 13:51:25', 1),
(2408, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 14:04:41\"}}', '2026-08-14 13:59:41', 1),
(2409, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 11:01:46\"}}', '2026-08-15 10:56:46', 1),
(2410, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:30:26\"}}', '2026-08-15 18:25:26', 1),
(2411, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:40:36\"}}', '2026-08-15 18:35:36', 1),
(2412, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:49:19\"}}', '2026-08-15 18:44:19', 1),
(2413, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:36:42\"}}', '2026-08-16 15:31:42', 1),
(2414, 'V30485684', 17, 'Éxito', 'eliminar', '190.97.229.57', '{\"id_rol\":{\"Eliminado\":2},\"nombre_rol\":{\"Eliminado\":\"OFICINISTA\"},\"status\":{\"Eliminado\":1}}', '2026-08-16 15:35:40', 1),
(2416, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:43:57\"}}', '2026-08-16 15:38:57', 1),
(2422, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:51:48\"}}', '2026-08-16 15:46:48', 1),
(2424, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:04:30\"}}', '2026-08-16 15:59:30', 1),
(2426, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:07:52\"}}', '2026-08-16 16:02:52', 1),
(2428, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485681', '181.208.252.213', NULL, '2026-08-16 16:03:15', 1),
(2429, 'V30485631', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:10:37\"}}', '2026-08-16 16:05:37', 1),
(2430, 'V30485684', 17, 'Éxito', 'actualizar', '190.97.229.57', '{\"nombre_rol\":{\"Modificado\":\"CLIENTE\"},\"status\":{\"Eliminado\":1}}', '2026-08-16 16:05:58', 1),
(2431, 'V30485631', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:19:39\"}}', '2026-08-16 16:14:39', 1),
(2432, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:28:37\"}}', '2026-08-16 16:23:37', 1),
(2434, 'V30485631', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:30:41\"}}', '2026-08-16 16:25:41', 1),
(2435, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 17:55:08\"}}', '2026-08-16 17:50:08', 1),
(2437, 'V30485631', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 17:55:27\"}}', '2026-08-16 17:50:27', 1),
(2438, 'V30485684', 4, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-16 18:57:00', 1),
(2439, 'V30485684', 4, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-16 18:57:05', 1),
(2440, 'V30485631', 248, 'Fallido', 'registrar', '190.97.229.57', NULL, '2026-08-16 18:59:25', 1),
(2441, 'V30485631', 248, 'Éxito', 'registrar', '190.97.229.57', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":113}}],\"delivery\":{\"latitud\":{\"Registrado\":9.861855646518277},\"longitud\":{\"Registrado\":-69.61206931161935}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_16_19_00_02_21.jpg\"]}]}', '2026-08-16 19:00:05', 1),
(2442, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26227-00001-55)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V30485654\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-08-16 19:25:14', 1),
(2443, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26227-00001-55)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":8}}', '2026-08-16 19:25:36', 1),
(2445, 'V30485684', 9, 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"foto_usuario\":{\"name\":{\"Registrado\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\"},\"full_path\":{\"Registrado\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\"},\"type\":{\"Registrado\":\"image\\/jpeg\"},\"tmp_name\":{\"Registrado\":\"C:\\\\xampp\\\\tmp\\\\phpD44.tmp\"},\"error\":{\"Registrado\":0},\"size\":{\"Registrado\":118154}},\"fofo_usuario\":{\"Eliminado\":\"\"}}', '2026-08-16 19:49:47', 1),
(2446, 'V30485684', 9, 'Éxito', 'Actualizar usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"apellido_usuario\":{\"Modificado\":\"Mendozam\"},\"cedula_usuario\":{\"Eliminado\":\"V30485631\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47\"}}', '2026-08-16 19:49:57', 1),
(2447, 'V30485684', 9, 'Éxito', 'Actualizar usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"apellido_usuario\":{\"Modificado\":\"Mendoza\"},\"cedula_usuario\":{\"Eliminado\":\"V30485631\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47\"}}', '2026-08-16 19:50:04', 1),
(2448, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitenz6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":2},\"usuario_usuario\":{\"Registrado\":\"Ander12399\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$OFi7gH2tMZled2NsBfYyI.NWAYlnlg3CUuQN\\/mEoH3phwkhCk5jDO\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_16_19_51_07_2.png\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-16 19:51:10', 1),
(2449, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485631', '190.97.229.57', NULL, '2026-08-16 19:51:25', 1),
(2450, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485688', '190.97.229.57', NULL, '2026-08-16 19:51:31', 1),
(2451, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 15:23:08\"}}', '2026-08-29 15:18:08', 1),
(2453, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V15265756', '190.97.229.62', '{\"cedula_usuario\":{\"Registrado\":\"V15265756\"},\"nombre_usuario\":{\"Registrado\":\"Jangely\"},\"apellido_usuario\":{\"Registrado\":\"Lacruz\"},\"correo_usuario\":{\"Registrado\":\"jangelylacruz1@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04267503595\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"jangely23\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$aIuVo59BETuuJt.0yPHkzeO2vwKAyBEZVM2ZOEuH8j2snfrTA6eIG\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_29_15_24_46_57.jpg\"},\"direccion_usuario\":{\"Registrado\":\"Barrio Jose Gregorio Hernandez\"}}', '2026-08-29 15:24:46', 1),
(2454, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 15:30:17\"}}', '2026-08-29 15:25:17', 1),
(2460, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 15:53:29\"}}', '2026-08-29 15:48:30', 1),
(2462, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 16:20:38\"}}', '2026-08-29 16:15:38', 1),
(2465, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.248.179.242', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 16:23:59\"}}', '2026-08-29 16:18:59', 1),
(2467, 'V30485684', 309, 'Éxito', 'registrar', '38.248.179.242', '{\"id_orden\":{\"Registrado\":\"OEP-26240-00001-31\"},\"cliente\":{\"Registrado\":\"Jangely Lacruz\"},\"rif\":{\"Registrado\":\"V15265756\"},\"productos\":{\"Registrado\":0},\"servicios\":{\"Registrado\":1}}', '2026-08-29 16:21:45', 1),
(2469, 'V30485684', 309, 'Éxito', 'anular', '38.248.179.242', '{\"status\":{\"Modificado\":2}}', '2026-08-29 16:22:42', 1),
(2473, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '0.0.0.0', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 18:34:04\"}}', '2026-08-29 18:29:04', 1),
(2474, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '0.0.0.0', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-29 18:46:38\"}}', '2026-08-29 18:41:38', 1),
(2475, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 10:24:36\"}}', '2026-08-30 10:19:37', 1),
(2483, 'V29910249', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 11:49:21\"}}', '2026-08-30 11:44:21', 1),
(2484, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 13:51:27\"}}', '2026-08-30 13:46:27', 1),
(2488, 'V29910249', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 13:53:16\"}}', '2026-08-30 13:48:16', 1),
(2489, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 15:19:53\"}}', '2026-08-30 15:14:53', 1),
(2495, 'V30485684', 248, 'Éxito', 'registrar', '190.97.229.57', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"cantidad\":{\"Registrado\":4}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"1\"},\"id_moneda\":{\"Registrado\":\"2\"},\"id_banco_emisor\":{\"Registrado\":\"2860\"},\"id_banco_receptor\":{\"Registrado\":\"2860\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":72270.32}}],\"delivery\":{\"latitud\":{\"Registrado\":9.8621335},\"longitud\":{\"Registrado\":-69.6131002}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_30_15_18_27_63.jpg\"]}]}', '2026-08-30 15:18:27', 1),
(2496, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 19:00:16\"}}', '2026-08-30 18:55:16', 1),
(2498, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 20:40:55\"}}', '2026-08-30 20:35:55', 1),
(2500, 'V15265756', 9, 'Éxito', 'registrar usuario con la cédula/rif: V11429340', '38.172.138.63', '{\"cedula_usuario\":{\"Registrado\":\"V11429340\"},\"nombre_usuario\":{\"Registrado\":\"Jorge\"},\"apellido_usuario\":{\"Registrado\":\"Quintero\"},\"correo_usuario\":{\"Registrado\":\"quinterojor72@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04141590801\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"quinterojor72\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$asuk5IDr5mFV03KptBKKtevs0cR3m4Q4RecDUyBVk2PSB.XaULCVy\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_30_20_46_32_40.jpg\"},\"direccion_usuario\":{\"Registrado\":\"Cabudare\"}}', '2026-08-30 20:46:33', 1),
(2502, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 21:11:16\"}}', '2026-08-30 21:06:16', 1),
(2504, 'V15265756', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V11429340', '38.172.138.63', NULL, '2026-08-30 21:07:52', 1),
(2505, 'V15265756', 9, 'Éxito', 'registrar usuario con la cédula/rif: V11429340', '38.172.138.63', '{\"cedula_usuario\":{\"Registrado\":\"V11429340\"},\"nombre_usuario\":{\"Registrado\":\"Jorge\"},\"apellido_usuario\":{\"Registrado\":\"Quintero\"},\"correo_usuario\":{\"Registrado\":\"quinterojor72@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04141590801\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"quinterojor72\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$HyV7lDW8Wcb.acXRPc2vI.SajlGrYFYnzp8PAlWNTUJvYGkpMK05m\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_30_21_27_21_39.jpg\"},\"direccion_usuario\":{\"Registrado\":\"Cabudare\"}}', '2026-08-30 21:27:21', 1),
(2507, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 21:34:01\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-30 21:29:01', 1),
(2510, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 21:37:50\"}}', '2026-08-30 21:32:50', 1),
(2514, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-30 22:29:58\"}}', '2026-08-30 22:24:58', 1),
(2517, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 06:46:11\"}}', '2026-08-31 06:41:11', 1),
(2522, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 07:04:29\"}}', '2026-08-31 06:59:29', 1),
(2524, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.206.103', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 09:55:07\"}}', '2026-08-31 09:50:08', 1),
(2529, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.194.33', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 12:15:27\"}}', '2026-08-31 12:10:27', 1),
(2533, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.194.33', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 12:36:31\"}}', '2026-08-31 12:31:31', 1),
(2535, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.194.33', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-31 12:38:04\"}}', '2026-08-31 12:33:04', 1),
(2537, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 06:25:09\"}}', '2026-09-01 06:20:09', 1),
(2540, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.74.176.137', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 17:56:03\"}}', '2026-09-01 17:51:03', 1),
(2542, 'V30485684', 23, 'Éxito', 'actualizar', '190.74.176.137', '{\"valor_moneda\":{\"Modificado\":\"798.33\"}}', '2026-09-01 17:52:44', 1),
(2543, 'V30485684', 309, 'Éxito', 'registrar', '190.74.176.137', '{\"id_orden\":{\"Registrado\":\"OEP-26243-00001-29\"},\"cliente\":{\"Registrado\":\"Yeison Carreño\"},\"rif\":{\"Registrado\":\"V29910249\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-01 17:54:49', 1),
(2544, 'V30485684', 23, 'Éxito', 'actualizar', '190.74.176.137', '{\"valor_moneda\":{\"Modificado\":\"850.00\"}}', '2026-09-01 18:00:29', 1),
(2545, 'V30485684', 309, 'Éxito', 'Actualizar OEP: OEP-26243-00001-29', '186.165.160.125', '{\"status\":{\"Modificado\":10}}', '2026-09-01 18:26:03', 1),
(2546, 'V30485684', 259, 'Éxito', 'Registrar Pago: PAG-26243-00001-63', '186.165.160.125', '{\"id_pago\":{\"Registrado\":\"PAG-26243-00001-63\"},\"id_orden\":{\"Registrado\":\"OEP-26243-00001-29\"}}', '2026-09-01 18:26:03', 1),
(2547, 'V30485684', 262, 'Éxito', 'eliminar módulo con id: 266', '186.165.160.125', NULL, '2026-09-01 18:26:49', 1),
(2548, 'V30485684', 19, 'Éxito', 'registtrar', '186.165.160.125', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"Nuevo Metodo de pago\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":1},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-09-01 18:35:55', 1),
(2549, 'V30485684', 19, 'Éxito', 'Eliminar', '186.165.160.125', NULL, '2026-09-01 18:36:28', 1),
(2550, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '159.26.100.221', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 18:47:03\"}}', '2026-09-01 18:42:03', 1),
(2553, 'V30485684', 309, 'Éxito', 'registrar', '186.165.160.125', '{\"id_orden\":{\"Registrado\":\"OEP-26243-00002-97\"},\"cliente\":{\"Registrado\":\"Jorge Quintero\"},\"rif\":{\"Registrado\":\"V11429340\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-01 18:50:03', 1),
(2554, 'V30485684', 309, 'Éxito', 'despachar', '186.165.160.125', '{\"status\":{\"Modificado\":3}}', '2026-09-01 18:50:59', 1),
(2556, 'V30485684', 309, 'Éxito', 'registrar', '186.165.160.125', '{\"id_orden\":{\"Registrado\":\"OEP-26243-00003-50\"},\"cliente\":{\"Registrado\":\"Condominio Centro Comercial Profesional Rosancar\"},\"rif\":{\"Registrado\":\"J310729425\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":1}}', '2026-09-01 18:53:17', 1),
(2557, 'V30485684', 260, 'Éxito', 'Actualizar con id 3', '186.165.160.125', '{\"status_orden\":{\"Modificado\":2},\"status_factura\":{\"Modificado\":12}}', '2026-09-01 18:53:48', 1),
(2558, 'V30485684', 309, 'Éxito', 'despachar', '186.165.160.125', '{\"status\":{\"Modificado\":3}}', '2026-09-01 18:54:04', 1),
(2559, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 18:59:27\"}}', '2026-09-01 18:54:27', 1),
(2562, 'V30485684', 309, 'Éxito', 'Actualizar OEP: OEP-26243-00003-50', '186.165.160.125', '{\"status\":{\"Modificado\":11}}', '2026-09-01 18:55:20', 1),
(2563, 'V30485684', 259, 'Éxito', 'Registrar Pago: PAG-26243-00002-63', '186.165.160.125', '{\"id_pago\":{\"Registrado\":\"PAG-26243-00002-63\"},\"id_orden\":{\"Registrado\":\"OEP-26243-00003-50\"}}', '2026-09-01 18:55:20', 1),
(2570, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.74.176.137', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 19:32:49\"}}', '2026-09-01 19:27:49', 1),
(2573, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.49', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 20:03:49\"}}', '2026-09-01 19:58:49', 1),
(2575, 'V30485684', 8, 'Éxito', 'registrar', '186.14.223.49', '{\"id_compra\":{\"Registrado\":\"19\"},\"rif_proveedor\":{\"Registrado\":\"J4002345761\"},\"fecha_compra\":{\"Registrado\":\"2026-09-01 19:59:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J4002345761\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"nombre\":{\"Registrado\":\"\\n                CLORO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-01 19:59:49', 1),
(2576, 'V30485684', 8, 'Éxito', 'registrar', '186.14.223.49', '{\"id_compra\":{\"Registrado\":\"20\"},\"rif_proveedor\":{\"Registrado\":\"J085185330\"},\"fecha_compra\":{\"Registrado\":\"2026-09-01 19:59:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"nombre\":{\"Registrado\":\"\\n                CLORO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"materia_prima\"},\"id\":{\"Registrado\":\"MATE-26123-00001-66\"},\"nombre\":{\"Registrado\":\"\\n                HIPOCLORITO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-01 20:00:05', 1),
(2578, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-01 23:44:15\"}}', '2026-09-01 23:39:16', 1),
(2580, 'V30485684', 253, 'Éxito', 'Registrar Anomalia de Materia Prima', '154.40.142.21', '{\"id_movimiento_anomalo_materia_prima\":{\"Registrado\":4},\"nombre_materia_prima\":{\"Registrado\":\"HIPOCLORITOm\"},\"cantidad_movimiento\":{\"Registrado\":2},\"tipo_movimiento\":{\"Registrado\":1},\"motivo_movimiento\":{\"Registrado\":\"prueba de carga\"},\"fecha_movimiento\":{\"Registrado\":\"2026-09-01 23:39:43\"}}', '2026-09-01 23:39:43', 1),
(2581, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.21', '{\"id_produccion\":{\"Registrado\":\"PROD-26243-00001-65\"},\"fecha_produccion\":{\"Registrado\":\"2026-09-01 23:40:14\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"cantidad_producida\":{\"Registrado\":\"2.00\"}}]}', '2026-09-01 23:40:14', 1),
(2582, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.21', '{\"id_permiso\":{\"Registrado\":678},\"nombre_permiso\":{\"Registrado\":\"prueba de permiso\"},\"status\":{\"Registrado\":1}}', '2026-09-01 23:42:32', 1),
(2583, 'V30485684', 28, 'Éxito', 'Eliminar con id 678', '154.40.142.21', NULL, '2026-09-01 23:42:37', 1),
(2584, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.192.79', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-02 09:01:01\"}}', '2026-09-02 08:56:01', 1),
(2587, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-02 19:52:27\"}}', '2026-09-02 19:47:27', 1),
(2592, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-02 21:41:59\"}}', '2026-09-02 21:36:59', 1),
(2595, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.207.90', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-03 14:50:25\"}}', '2026-09-03 14:45:25', 1),
(2597, 'V11429340', 17, 'Éxito', 'registrar', '186.165.207.90', '{\"id_rol\":{\"Registrado\":\"35\"},\"nombre_rol\":{\"Registrado\":\"Cajera\"}}', '2026-09-03 14:54:17', 1),
(2598, 'V11429340', 8, 'Éxito', 'registrar', '186.165.207.90', '{\"id_compra\":{\"Registrado\":\"21\"},\"rif_proveedor\":{\"Registrado\":\"J085185330\"},\"fecha_compra\":{\"Registrado\":\"2026-09-03 15:17:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"materia_prima\"},\"id\":{\"Registrado\":\"MATE-26123-00001-66\"},\"nombre\":{\"Registrado\":\"\\n                HIPOCLORITO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"nombre\":{\"Registrado\":\"\\n                CLORO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"materia_prima\"},\"id\":{\"Registrado\":\"MATE-26123-00001-66\"},\"nombre\":{\"Registrado\":\"\\n                HIPOCLORITO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-03 15:18:22', 1),
(2599, 'V11429340', 309, 'Éxito', 'registrar', '186.165.207.90', '{\"id_orden\":{\"Registrado\":\"OEP-26245-00001-13\"},\"cliente\":{\"Registrado\":\"Yeison Carreño\"},\"rif\":{\"Registrado\":\"V29910249\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":1}}', '2026-09-03 16:18:00', 1),
(2600, 'V11429340', 309, 'Éxito', 'Actualizar OEP: OEP-26245-00001-13', '186.165.207.90', '{\"status\":{\"Modificado\":10}}', '2026-09-03 16:20:41', 1),
(2601, 'V11429340', 259, 'Éxito', 'Registrar Pago: PAG-26245-00001-11', '186.165.207.90', '{\"id_pago\":{\"Registrado\":\"PAG-26245-00001-11\"},\"id_orden\":{\"Registrado\":\"OEP-26245-00001-13\"}}', '2026-09-03 16:20:41', 1),
(2602, 'V11429340', 309, 'Éxito', 'despachar', '186.165.207.90', '{\"status\":{\"Modificado\":11}}', '2026-09-03 16:21:55', 1),
(2603, 'V11429340', 309, 'Éxito', 'registrar', '186.165.207.90', '{\"id_orden\":{\"Registrado\":\"OEP-26245-00002-22\"},\"cliente\":{\"Registrado\":\"Jangely Lacruz\"},\"rif\":{\"Registrado\":\"V15265756\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-03 16:32:58', 1),
(2604, 'V11429340', 309, 'Éxito', 'anular', '186.165.207.90', '{\"status\":{\"Modificado\":2}}', '2026-09-03 16:33:36', 1),
(2605, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.165.140.173', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-04 08:52:35\"}}', '2026-09-04 08:47:36', 1),
(2609, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.165.140.173', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-04 09:34:08\"}}', '2026-09-04 09:29:08', 1),
(2613, 'V29910249', 9, 'Éxito', 'Iniciar sesión', '186.165.140.173', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-04 09:43:49\"}}', '2026-09-04 09:38:49', 1),
(2614, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.165.140.173', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-04 09:49:16\"}}', '2026-09-04 09:44:16', 1),
(2616, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.63', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-04 14:13:48\"}}', '2026-09-04 14:08:48', 1),
(2619, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-05 09:15:00\"}}', '2026-09-05 09:10:00', 1),
(2623, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.21', '{\"valor_moneda\":{\"Modificado\":\"807.39\"}}', '2026-09-05 09:55:39', 1),
(2631, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 13:42:52\"}}', '2026-09-06 13:37:52', 1),
(2633, 'V15265756', 9, 'Éxito', 'registrar usuario con la cédula/rif: V15265757', '181.208.252.199', '{\"cedula_usuario\":{\"Registrado\":\"V15265757\"},\"nombre_usuario\":{\"Registrado\":\"Jangely\"},\"apellido_usuario\":{\"Registrado\":\"Lacruz\"},\"correo_usuario\":{\"Registrado\":\"jangelylacruz@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04267503595\"},\"id_rol\":{\"Registrado\":3},\"usuario_usuario\":{\"Registrado\":\"jangely25\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$cSEjzsc.brRUe1PgioUgYOpzwWcsy7SSr.J6vCuCTsZ4BB1Y197VO\"},\"foto_usuario\":{\"Registrado\":\"\"},\"direccion_usuario\":{\"Registrado\":\"Jose Gregorio Hernandez\"}}', '2026-09-06 13:40:34', 1),
(2634, 'V15265757', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 13:46:10\"}}', '2026-09-06 13:41:10', 1),
(2635, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 13:48:26\"}}', '2026-09-06 13:43:26', 1),
(2637, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 14:14:03\"}}', '2026-09-06 14:09:03', 1),
(2639, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 14:22:30\"}}', '2026-09-06 14:17:30', 1),
(2644, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 14:45:37\"}}', '2026-09-06 14:40:37', 1),
(2646, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:23:52\"}}', '2026-09-06 19:18:52', 1),
(2648, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:44:05\"}}', '2026-09-06 19:39:05', 1),
(2651, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:56:34\"}}', '2026-09-06 19:51:34', 1),
(2657, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 20:04:24\"}}', '2026-09-06 19:59:24', 1),
(2662, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 20:51:11\"}}', '2026-09-06 20:46:11', 1),
(2664, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 23:13:23\"}}', '2026-09-06 23:08:23', 1),
(2667, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.69.75', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-07 15:13:31\"}}', '2026-09-07 15:08:31', 1),
(2669, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.165.205.135', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 09:43:37\"}}', '2026-09-08 09:38:37', 1),
(2671, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.237.186', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 09:43:51\"}}', '2026-09-08 09:38:51', 1),
(2674, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.237.186', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 09:46:30\"}}', '2026-09-08 09:41:30', 1),
(2677, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.200.236.162', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 10:07:00\"}}', '2026-09-08 10:02:00', 1),
(2684, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.59', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 11:35:19\"}}', '2026-09-08 11:30:19', 1),
(2686, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 15:40:00\"}}', '2026-09-08 15:35:00', 1),
(2690, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 17:10:47\"}}', '2026-09-08 17:05:47', 1),
(2693, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.200.145.81', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 20:02:56\"}}', '2026-09-08 19:57:56', 1),
(2695, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 21:02:13\"}}', '2026-09-08 20:57:13', 1),
(2698, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 21:42:18\"}}', '2026-09-08 21:37:18', 1),
(2700, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 21:42:32\"}}', '2026-09-08 21:37:32', 1),
(2703, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 22:35:56\"}}', '2026-09-08 22:30:56', 1),
(2704, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-08 22:35:58\"}}', '2026-09-08 22:30:58', 1),
(2714, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 10:33:57\"}}', '2026-09-09 10:28:57', 1),
(2717, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.200.236.162', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 11:50:00\"}}', '2026-09-09 11:45:00', 1),
(2720, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 14:03:54\"}}', '2026-09-09 13:58:54', 1),
(2724, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 16:34:49\"}}', '2026-09-09 16:29:49', 1),
(2730, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.153.66.250', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 19:28:54\"}}', '2026-09-09 19:23:54', 1),
(2732, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.60', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-09 19:29:53\"}}', '2026-09-09 19:24:53', 1),
(2739, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 06:57:17\"}}', '2026-09-10 06:52:17', 1),
(2741, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.213.248', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 07:47:48\"}}', '2026-09-10 07:42:48', 1),
(2743, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 20:57:07\"}}', '2026-09-10 20:52:07', 1),
(2746, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 20:59:26\"}}', '2026-09-10 20:54:26', 1),
(2748, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 21:30:47\"}}', '2026-09-10 21:25:47', 1),
(2750, 'V15265756', 9, 'Éxito', 'Actualizar usuario con la cedula/rif: V15265757', '190.97.229.62', '{\"contrasena_usuario\":{\"Modificado\":\"$2y$10$Etf1BEQCvlkxfAk0PrRxGeHxSJtHFghi8hF8VcMaOJMEVLtP7o2zC\"},\"cedula_usuario\":{\"Eliminado\":\"V15265757\"},\"foto_usuario\":{\"Eliminado\":\"\"}}', '2026-09-10 21:26:12', 1),
(2751, 'V15265757', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 21:31:37\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-09-10 21:26:37', 1),
(2752, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.41.22.5', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-11 13:39:48\"}}', '2026-09-11 13:34:48', 1),
(2754, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.165.159.254', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-11 13:46:04\"}}', '2026-09-11 13:41:04', 1),
(2756, 'V30485684', 8, 'Éxito', 'Recepcionar compra (18)', '186.165.159.254', '{\"status\":{\"Modificado\":2}}', '2026-09-11 13:41:40', 1),
(2757, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.137.177.132', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-11 13:56:37\"}}', '2026-09-11 13:51:37', 1),
(2759, 'V15265757', 9, 'Éxito', 'Iniciar sesión', '190.97.229.62', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-12 10:28:03\"}}', '2026-09-12 10:23:03', 1),
(2760, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.48.184', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-12 12:35:47\"}}', '2026-09-12 12:30:47', 1),
(2762, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-13 19:32:36\"}}', '2026-09-13 19:27:36', 1),
(2764, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 09:52:16\"}}', '2026-09-16 09:47:16', 1),
(2767, 'V31317406', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 10:07:19\"}}', '2026-09-16 10:02:19', 1),
(2768, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 18:41:50\"}}', '2026-09-16 18:36:50', 1),
(2770, 'V30485684', 8, 'Éxito', 'registrar', '186.14.223.54', '{\"id_compra\":{\"Registrado\":\"22\"},\"rif_proveedor\":{\"Registrado\":\"J085185330\"},\"fecha_compra\":{\"Registrado\":\"2026-09-16 19:17:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"nombre\":{\"Registrado\":\"\\n                CLORO\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-16 19:17:28', 1),
(2772, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 19:32:50\"}}', '2026-09-16 19:27:50', 1),
(2779, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 21:47:25\"}}', '2026-09-16 21:42:25', 1),
(2786, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 22:22:27\"}}', '2026-09-16 22:17:27', 1),
(2789, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 22:37:36\"}}', '2026-09-16 22:32:36', 1),
(2792, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 23:08:22\"}}', '2026-09-16 23:03:22', 1),
(2794, 'V30485684', 307, 'Éxito', 'registrar', '0.0.0.0', '{\"cedula_usuario\":{\"Registrado\":\"V30485684\"},\"prompt\":{\"Registrado\":\"quiero u¿fumigar mi casa y tambien me gustaria 2 litros de cloro\"},\"respuesta_bot\":{\"Registrado\":\"¡Claro que sí! Con gusto te preparo tu presupuesto.\\r\\n\\r\\nAquí tienes el detalle de tu pedido:\\r\\n\\r\\n*   <bFUMIGACION<\\/b>: 1 servicio. Costo: 17.762,58 Bs (Ref: $22.00 USD)\\r\\n*   <bCLORO (LITRO)<\\/b>: 2 unidades. Costo por unidad: 807,39 Bs (Ref: $1.00 USD). Total: 1.614,78 Bs (Ref: $2.00 USD)\\r\\n\\r\\n---\\r\\n\\r\\n<bTotal General<\\/b>: 19.377,36 Bs (Ref: $24.00 USD)\\r\\n\\r\\n¿Deseas realizar alguna modificación o agregar algo más a tu presupuesto?\"}}', '2026-09-16 23:04:44', 1),
(2795, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 23:15:14\"}}', '2026-09-16 23:10:14', 1),
(2797, 'V30485684', 8, 'Éxito', 'Recepcionar compra (21)', '186.14.223.54', '{\"status\":{\"Modificado\":2}}', '2026-09-17 00:05:19', 1),
(2798, 'V30485684', 8, 'Éxito', 'registrar', '186.14.223.54', '{\"id_compra\":{\"Registrado\":\"23\"},\"rif_proveedor\":{\"Registrado\":\"J299682950\"},\"fecha_compra\":{\"Registrado\":\"2026-09-17 00:05:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J299682950\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"nombre\":{\"Registrado\":\"\\n                CLORO (BIDÓN)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-17 00:05:31', 1),
(2799, 'V30485684', 8, 'Éxito', 'registrar', '186.14.223.49', '{\"id_compra\":{\"Registrado\":\"24\"},\"rif_proveedor\":{\"Registrado\":\"J085185330\"},\"fecha_compra\":{\"Registrado\":\"2026-09-17 00:27:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J085185330\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"nombre\":{\"Registrado\":\"\\n                CLORO (BIDÓN)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-09-17 00:27:55', 1),
(2800, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 19:13:24\"}}', '2026-09-17 19:08:24', 1),
(2802, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 20:23:20', 1),
(2803, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.54', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 21:18:24\"}}', '2026-09-17 21:13:24', 1),
(2805, 'V30485684', 8, 'Éxito', 'Recepcionar compra (22)', '186.14.223.54', '{\"status\":{\"Modificado\":2}}', '2026-09-17 21:13:46', 1),
(2806, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 22:27:40\"}}', '2026-09-17 22:22:40', 1),
(2814, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.14.223.49', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 22:35:30\"}}', '2026-09-17 22:30:30', 1),
(2831, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '143.255.85.61', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 22:54:33\"}}', '2026-09-17 22:49:33', 1),
(2838, 'V30485684', 5, 'Éxito', 'Actualizar servicio: FUMIGACION', '154.40.142.21', '{\"precio_servicio\":{\"Modificado\":30},\"mostrar_ecommerce\":{\"Modificado\":1},\"productos_servicio\":[]}', '2026-09-18 00:38:42', 1),
(2842, 'V30485684', 5, 'Éxito', 'Registrar servicio: Aseo y Limpieza', '154.40.142.21', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00001-67\"},\"id_unidad_medida\":{\"Registrado\":\"9\"},\"nombre_servicio\":{\"Registrado\":\"Aseo y Limpieza\"},\"precio_servicio\":{\"Registrado\":20},\"mostrar_ecommerce\":{\"Registrado\":0},\"productos_servicio\":[]}', '2026-09-18 00:49:36', 1),
(2843, 'V30485684', 5, 'Éxito', 'Actualizar servicio: ASEO Y LIMPIEZA', '154.40.142.21', '{\"nombre_servicio\":{\"Modificado\":\"ASEO Y LIMPIEZA\"},\"productos_servicio\":[]}', '2026-09-18 00:49:54', 1),
(2847, 'V30485684', 22, 'Éxito', 'registrar', '143.255.85.61', '{\"id_unidad_medida\":{\"Registrado\":\"37\"},\"nombre_unidad_medida\":{\"Registrado\":\"GRAMOS\"},\"simbolo_unidad_medida\":{\"Registrado\":\"GRA\"},\"equivalencia_ub\":{\"Registrado\":100}}', '2026-09-18 01:52:03', 1),
(2849, 'V30485684', 21, 'Éxito', 'registrar', '143.255.85.61', '{\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_unidad_medida\":{\"Registrado\":\"37\"},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":500}}', '2026-09-18 01:56:33', 1),
(2850, 'V30485684', 21, 'Éxito', 'registrar', '143.255.85.61', '{\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_unidad_medida\":{\"Registrado\":\"37\"},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":1000}}', '2026-09-18 01:57:29', 1),
(2851, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"precio_materia_prima\":{\"Modificado\":\"2.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":98}},{\"id_materia_prima_presentacion\":{\"Registrado\":99},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":100},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}}]}', '2026-09-18 01:58:30', 1),
(2852, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"id_unidad_medida\":{\"Modificado\":37},\"nombre_materia_prima\":{\"Modificado\":\"GENAPOL\"},\"stock_minimo_materia_prima\":{\"Modificado\":\"5.00\"},\"nombre_unidad_medida\":{\"Modificado\":\"GRAMOS\"},\"simbolo_unidad_medida\":{\"Modificado\":\"GRA\"},\"equivalencia_ub\":{\"Modificado\":\"100.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":101},\"id_presentacion\":{\"Modificado\":\"PRES-26260-00001-90\"},\"id_unidad_medida\":{\"Modificado\":37},\"nombre_presentacion\":{\"Modificado\":\"BOLSA\"},\"cantidad_pmp\":{\"Modificado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":102},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26157-00001-95\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 02:00:16', 1),
(2853, 'V30485684', 24, 'Éxito', 'registrar', '143.255.85.61', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00001-72\"},\"id_unidad_medida\":{\"Registrado\":1},\"nombre_materia_prima\":{\"Registrado\":\"SODA CAUSTICA\"},\"stock_materia_prima\":{\"Registrado\":\"30.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"5.00\"},\"precio_materia_prima\":{\"Registrado\":\"224.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KG\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":103},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00001-72\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 02:01:46', 1),
(2854, 'V30485684', 24, 'Éxito', 'registrar', '143.255.85.61', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"FORMOL\"},\"stock_materia_prima\":{\"Registrado\":\"400.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"50.00\"},\"precio_materia_prima\":{\"Registrado\":\"4.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":104},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":105},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":106},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":107},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}}]}', '2026-09-18 02:03:17', 1),
(2855, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"precio_materia_prima\":{\"Modificado\":\"2400.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":108}}]}', '2026-09-18 02:03:35', 1),
(2856, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"precio_materia_prima\":{\"Modificado\":\"24.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":109}}]}', '2026-09-18 02:03:55', 1),
(2857, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"precio_materia_prima\":{\"Modificado\":\"3.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":110}}]}', '2026-09-18 02:04:08', 1),
(2858, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":111}},{\"id_materia_prima_presentacion\":{\"Modificado\":112}}]}', '2026-09-18 02:05:24', 1),
(2859, 'V30485684', 24, 'Éxito', 'actualizar', '143.255.85.61', '{\"precio_materia_prima\":{\"Modificado\":\"4.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":113}},{\"id_materia_prima_presentacion\":{\"Modificado\":114}}]}', '2026-09-18 02:05:57', 1),
(2860, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 03:14:28\"}}', '2026-09-18 03:09:28', 1),
(2862, 'V30485684', 22, 'Éxito', 'actualizar', '154.40.142.21', '{\"simbolo_unidad_medida\":{\"Modificado\":\"GR\"},\"equivalencia_ub\":{\"Modificado\":1},\"status\":{\"Eliminado\":1}}', '2026-09-18 03:09:52', 1),
(2863, 'V30485684', 24, 'Éxito', 'actualizar', '154.40.142.21', '{\"precio_materia_prima\":{\"Modificado\":\"3.24\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":115}}]}', '2026-09-18 03:10:17', 1),
(2864, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '181.208.252.199', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 05:45:37\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-09-18 05:40:37', 1),
(2871, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '82.86.69.239', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 07:37:02\"}}', '2026-09-18 07:32:02', 1),
(2873, 'V30485684', 5, 'Éxito', 'Actualizar servicio: ASEO Y LIMPIEZA', '82.86.69.239', '{\"precio_servicio\":{\"Modificado\":0.3},\"productos_servicio\":[]}', '2026-09-18 07:32:52', 1),
(2874, 'V30485684', 5, 'Éxito', 'Actualizar servicio: FUMIGACION', '82.86.69.239', '{\"id_unidad_medida\":{\"Modificado\":\"9\"},\"productos_servicio\":[]}', '2026-09-18 07:33:07', 1),
(2875, 'V30485684', 5, 'Éxito', 'Registrar servicio: DESMALEZADO', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00002-16\"},\"id_unidad_medida\":{\"Registrado\":\"9\"},\"nombre_servicio\":{\"Registrado\":\"DESMALEZADO\"},\"precio_servicio\":{\"Registrado\":0.9},\"mostrar_ecommerce\":{\"Registrado\":0},\"productos_servicio\":[]}', '2026-09-18 07:34:34', 1),
(2876, 'V30485684', 22, 'Éxito', 'registrar', '82.86.69.239', '{\"id_unidad_medida\":{\"Registrado\":\"38\"},\"nombre_unidad_medida\":{\"Registrado\":\"METRO CUBICO\"},\"simbolo_unidad_medida\":{\"Registrado\":\"MC\"},\"equivalencia_ub\":{\"Registrado\":1000}}', '2026-09-18 07:36:07', 1),
(2877, 'V30485684', 5, 'Éxito', 'Actualizar servicio: FUMIGACION', '82.86.69.239', '{\"id_unidad_medida\":{\"Modificado\":\"38\"},\"precio_servicio\":{\"Modificado\":0.1},\"productos_servicio\":[]}', '2026-09-18 07:36:37', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(2878, 'V30485684', 22, 'Éxito', 'registrar', '82.86.69.239', '{\"id_unidad_medida\":{\"Registrado\":\"39\"},\"nombre_unidad_medida\":{\"Registrado\":\"KILOMETROS\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KM\"},\"equivalencia_ub\":{\"Registrado\":1000}}', '2026-09-18 07:37:34', 1),
(2879, 'V30485684', 5, 'Éxito', 'Registrar servicio: BOTE DE ESCOMBROS', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00003-42\"},\"id_unidad_medida\":{\"Registrado\":\"39\"},\"nombre_servicio\":{\"Registrado\":\"BOTE DE ESCOMBROS\"},\"precio_servicio\":{\"Registrado\":60},\"mostrar_ecommerce\":{\"Registrado\":0},\"productos_servicio\":[]}', '2026-09-18 07:38:21', 1),
(2880, 'V30485684', 5, 'Éxito', 'Registrar servicio: RECARGA DE BOTELLONES', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00004-63\"},\"id_unidad_medida\":{\"Registrado\":\"2\"},\"nombre_servicio\":{\"Registrado\":\"RECARGA DE BOTELLONES\"},\"precio_servicio\":{\"Registrado\":0.5},\"mostrar_ecommerce\":{\"Registrado\":1},\"productos_servicio\":[]}', '2026-09-18 07:38:53', 1),
(2881, 'V30485684', 5, 'Éxito', 'Actualizar servicio: BOTE DE ESCOMBROS', '82.86.69.239', '{\"mostrar_ecommerce\":{\"Modificado\":1},\"productos_servicio\":[]}', '2026-09-18 07:39:02', 1),
(2882, 'V30485684', 5, 'Éxito', 'Registrar servicio: CISTERNA DE AGUA', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00005-37\"},\"id_unidad_medida\":{\"Registrado\":\"2\"},\"nombre_servicio\":{\"Registrado\":\"CISTERNA DE AGUA\"},\"precio_servicio\":{\"Registrado\":60},\"mostrar_ecommerce\":{\"Registrado\":1},\"productos_servicio\":[]}', '2026-09-18 07:39:53', 1),
(2883, 'V30485684', 5, 'Éxito', 'Registrar servicio: PICO Y PODA', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00006-72\"},\"id_unidad_medida\":{\"Registrado\":\"9\"},\"nombre_servicio\":{\"Registrado\":\"PICO Y PODA\"},\"precio_servicio\":{\"Registrado\":10},\"mostrar_ecommerce\":{\"Registrado\":1},\"productos_servicio\":[]}', '2026-09-18 07:40:58', 1),
(2884, 'V30485684', 5, 'Éxito', 'Registrar servicio: ACARREO DE MUDANZAS', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00007-00\"},\"id_unidad_medida\":{\"Registrado\":\"39\"},\"nombre_servicio\":{\"Registrado\":\"ACARREO DE MUDANZAS\"},\"precio_servicio\":{\"Registrado\":30},\"mostrar_ecommerce\":{\"Registrado\":1},\"productos_servicio\":[]}', '2026-09-18 07:41:35', 1),
(2885, 'V30485684', 5, 'Éxito', 'Registrar servicio: MANTENIMIENTO DE SALAS DE BANO', '82.86.69.239', '{\"id_servicio\":{\"Registrado\":\"SERV-26260-00008-44\"},\"id_unidad_medida\":{\"Registrado\":\"9\"},\"nombre_servicio\":{\"Registrado\":\"MANTENIMIENTO DE SALAS DE BANO\"},\"precio_servicio\":{\"Registrado\":150},\"mostrar_ecommerce\":{\"Registrado\":0},\"productos_servicio\":[]}', '2026-09-18 07:45:15', 1),
(2886, 'V30485684', 5, 'Éxito', 'Actualizar servicio: MANTENIMIENTO DE SALAS DE BANO', '82.86.69.239', '{\"mostrar_ecommerce\":{\"Modificado\":1},\"productos_servicio\":[]}', '2026-09-18 07:45:28', 1),
(2887, 'V30485684', 24, 'Éxito', 'actualizar', '82.86.69.239', '{\"id_unidad_medida\":{\"Modificado\":1},\"precio_materia_prima\":{\"Modificado\":\"5.00\"},\"nombre_unidad_medida\":{\"Modificado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Modificado\":\"KG\"},\"equivalencia_ub\":{\"Modificado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":116}},{\"id_materia_prima_presentacion\":{\"Modificado\":117}}]}', '2026-09-18 08:01:12', 1),
(2888, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"SULFONICO\"},\"stock_materia_prima\":{\"Registrado\":\"150.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"3.50\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":118},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":119},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":120},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":121},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":122},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":123},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":124},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":125},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:03:46', 1),
(2889, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00004-60\"},\"id_unidad_medida\":{\"Registrado\":1},\"nombre_materia_prima\":{\"Registrado\":\"COLORANTE\"},\"stock_materia_prima\":{\"Registrado\":\"80.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"4.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KG\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":126},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00004-60\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":127},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00004-60\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:05:10', 1),
(2890, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"id_unidad_medida\":{\"Registrado\":1},\"nombre_materia_prima\":{\"Registrado\":\"SAL\"},\"stock_materia_prima\":{\"Registrado\":\"160.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"1.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KG\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":128},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":129},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":130},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":131},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":132},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":133},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":134},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":135},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:05:56', 1),
(2891, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"FRAGANCIA\"},\"stock_materia_prima\":{\"Registrado\":\"60.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"20.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":136},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":137},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":138},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":139},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":140},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":141},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":142},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":143},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:09:01', 1),
(2892, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"id_unidad_medida\":{\"Registrado\":1},\"nombre_materia_prima\":{\"Registrado\":\"GLICERINA\"},\"stock_materia_prima\":{\"Registrado\":\"140.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"10.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KG\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":144},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":145},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":146},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":147},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":148},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":149},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":150},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":151},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00007-39\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:11:08', 1),
(2893, 'V30485684', 24, 'Éxito', 'actualizar', '82.86.69.239', '{\"nombre_materia_prima\":{\"Modificado\":\"SODA CAUTICA\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":152}}]}', '2026-09-18 08:11:29', 1),
(2894, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"DODIGEN\"},\"stock_materia_prima\":{\"Registrado\":\"80.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"8.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":153},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":154},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":155},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":156},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":157},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":158},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":159},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":160},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:13:25', 1),
(2895, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"NONL\"},\"stock_materia_prima\":{\"Registrado\":\"100.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"2.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":161},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":162},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":163},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":164},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":165},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":166},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":167},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":168},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:14:36', 1),
(2896, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_materia_prima\":{\"Registrado\":\"ALCOHOL\"},\"stock_materia_prima\":{\"Registrado\":\"100.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"3.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":169},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":170},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":171},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":172},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":173},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":174},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":175},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":176},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:15:55', 1),
(2897, 'V30485684', 24, 'Éxito', 'registrar', '82.86.69.239', '{\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"id_unidad_medida\":{\"Registrado\":1},\"nombre_materia_prima\":{\"Registrado\":\"CUMARINA\"},\"stock_materia_prima\":{\"Registrado\":\"60.00\"},\"stock_minimo_materia_prima\":{\"Registrado\":\"20.00\"},\"precio_materia_prima\":{\"Registrado\":\"20.00\"},\"status\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"KILO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"KG\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Registrado\":177},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_pmp\":{\"Registrado\":\"1.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":178},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"BIDÓN\"},\"cantidad_pmp\":{\"Registrado\":\"20.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":179},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"PIPA\"},\"cantidad_pmp\":{\"Registrado\":\"200.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":180},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"4.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":181},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - 1L\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":182},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":2},\"nombre_presentacion\":{\"Registrado\":\"CAJA - GALÓN\"},\"cantidad_pmp\":{\"Registrado\":\"12.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":183},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"BOLSA\"},\"cantidad_pmp\":{\"Registrado\":\"500.00\"}},{\"id_materia_prima_presentacion\":{\"Registrado\":184},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"status\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":37},\"nombre_presentacion\":{\"Registrado\":\"EMPAQUE\"},\"cantidad_pmp\":{\"Registrado\":\"1000.00\"}}]}', '2026-09-18 08:17:18', 1),
(2899, 'V30485684', 4, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"JABON MULTIUSO\"},\"precio_producto\":{\"Registrado\":\"1.50\"},\"stock_producto\":{\"Registrado\":\"400.00\"},\"stock_minimo_producto\":{\"Registrado\":\"50.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00001-52\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00002-26\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00003-29\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00004-31\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00005-55\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00006-10\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":215},\"id_materia_prima\":{\"Registrado\":\"MATE-26157-00001-95\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.40\"}},{\"id_materia_prima_producto\":{\"Registrado\":216},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.40\"}},{\"id_materia_prima_producto\":{\"Registrado\":217},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00001-72\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.10\"}},{\"id_materia_prima_producto\":{\"Registrado\":218},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.26\"}},{\"id_materia_prima_producto\":{\"Registrado\":219},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.20\"}},{\"id_materia_prima_producto\":{\"Registrado\":220},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-09-18 08:22:24', 1),
(2900, 'V30485684', 4, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"BRISOL\"},\"precio_producto\":{\"Registrado\":\"1.50\"},\"stock_producto\":{\"Registrado\":\"320.00\"},\"stock_minimo_producto\":{\"Registrado\":\"40.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00007-69\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00008-78\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00009-36\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00010-56\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00011-22\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00012-68\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":221},\"id_materia_prima\":{\"Registrado\":\"MATE-26157-00001-95\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":222},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00003-28\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":223},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00001-72\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}},{\"id_materia_prima_producto\":{\"Registrado\":224},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00004-60\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Registrado\":225},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00005-48\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Registrado\":226},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-09-18 08:25:48', 1),
(2901, 'V30485684', 4, 'Éxito', 'Actualizar', '82.86.69.239', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00013-44\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00014-65\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00015-14\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00016-70\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00017-59\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00018-51\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":227},\"cantidad_materia_prima\":{\"Modificado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Modificado\":228},\"cantidad_materia_prima\":{\"Modificado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Modificado\":229},\"cantidad_materia_prima\":{\"Modificado\":\"0.01\"}},{\"id_materia_prima_producto\":{\"Modificado\":230},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":231},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":232}}]}}', '2026-09-18 08:26:19', 1),
(2902, 'V30485684', 4, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"DESINFECTANTE\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"350.00\"},\"stock_minimo_producto\":{\"Registrado\":\"50.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00019-22\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":233},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00008-36\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.03\"}},{\"id_materia_prima_producto\":{\"Registrado\":234},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00009-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":235},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00002-67\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":236},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00010-22\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":237},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00011-99\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.04\"}},{\"id_materia_prima_producto\":{\"Registrado\":238},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00006-14\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.06\"}},{\"id_materia_prima_producto\":{\"Registrado\":239},\"id_materia_prima\":{\"Registrado\":\"MATE-26260-00004-60\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.05\"}}]}}', '2026-09-18 08:29:43', 1),
(2903, 'V30485684', 4, 'Éxito', 'Actualizar', '82.86.69.239', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00019-90\"}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00020-90\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00021-77\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00022-33\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00023-94\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00024-60\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":240}},{\"id_materia_prima_producto\":{\"Modificado\":241}},{\"id_materia_prima_producto\":{\"Modificado\":242}},{\"id_materia_prima_producto\":{\"Modificado\":243}},{\"id_materia_prima_producto\":{\"Modificado\":244}},{\"id_materia_prima_producto\":{\"Modificado\":245}},{\"id_materia_prima_producto\":{\"Modificado\":246}}]}}', '2026-09-18 08:29:57', 1),
(2904, 'V30485684', 4, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CERA\"},\"precio_producto\":{\"Registrado\":\"2.00\"},\"stock_producto\":{\"Registrado\":\"150.00\"},\"stock_minimo_producto\":{\"Registrado\":\"30.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00025-12\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00026-16\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00027-18\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00028-67\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00029-96\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00030-66\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-09-18 08:30:57', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(2905, 'V30485684', 4, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LIMPIA POCETA\"},\"precio_producto\":{\"Registrado\":\"2.00\"},\"stock_producto\":{\"Registrado\":\"140.00\"},\"stock_minimo_producto\":{\"Registrado\":\"40.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00031-83\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00032-67\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00033-35\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00034-33\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00035-04\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00036-45\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00005-44\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-09-18 08:32:03', 1),
(2906, 'V30485684', 24, 'Éxito', 'actualizar', '82.86.69.239', '{\"precio_materia_prima\":{\"Modificado\":\"5.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":185}},{\"id_materia_prima_presentacion\":{\"Modificado\":186}},{\"id_materia_prima_presentacion\":{\"Modificado\":187}},{\"id_materia_prima_presentacion\":{\"Modificado\":188}},{\"id_materia_prima_presentacion\":{\"Modificado\":189}},{\"id_materia_prima_presentacion\":{\"Modificado\":190}},{\"id_materia_prima_presentacion\":{\"Modificado\":191}},{\"id_materia_prima_presentacion\":{\"Modificado\":192}}]}', '2026-09-18 08:33:03', 1),
(2907, 'V30485684', 24, 'Éxito', 'actualizar', '82.86.69.239', '{\"precio_materia_prima\":{\"Modificado\":\"8.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":193}},{\"id_materia_prima_presentacion\":{\"Modificado\":194}},{\"id_materia_prima_presentacion\":{\"Modificado\":195}},{\"id_materia_prima_presentacion\":{\"Modificado\":196}},{\"id_materia_prima_presentacion\":{\"Modificado\":197}},{\"id_materia_prima_presentacion\":{\"Modificado\":198}},{\"id_materia_prima_presentacion\":{\"Modificado\":199}},{\"id_materia_prima_presentacion\":{\"Modificado\":200}}]}', '2026-09-18 08:33:57', 1),
(2908, 'V30485684', 24, 'Éxito', 'actualizar', '82.86.69.239', '{\"precio_materia_prima\":{\"Modificado\":\"7.00\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":201}},{\"id_materia_prima_presentacion\":{\"Modificado\":202}},{\"id_materia_prima_presentacion\":{\"Modificado\":203}},{\"id_materia_prima_presentacion\":{\"Modificado\":204}},{\"id_materia_prima_presentacion\":{\"Modificado\":205}},{\"id_materia_prima_presentacion\":{\"Modificado\":206}},{\"id_materia_prima_presentacion\":{\"Modificado\":207}},{\"id_materia_prima_presentacion\":{\"Modificado\":208}}]}', '2026-09-18 08:34:10', 1),
(2909, 'V30485684', 4, 'Éxito', 'Actualizar', '82.86.69.239', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00037-32\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00038-90\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00039-07\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00040-18\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00041-65\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26260-00042-81\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":247},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":248},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":249},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":250},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":251},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":252},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}},{\"id_materia_prima_producto\":{\"Modificado\":253},\"cantidad_materia_prima\":{\"Modificado\":\"0.02\"}}]}}', '2026-09-18 08:34:59', 1),
(2910, 'V30485684', 246, 'Éxito', 'Registrar', '82.86.69.239', '{\"id_produccion\":{\"Registrado\":\"PROD-26260-00001-90\"},\"fecha_produccion\":{\"Registrado\":\"2026-09-18 08:37:52\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"10.00\"}},{\"id_producto\":{\"Registrado\":\"PROD-26260-00002-67\"},\"cantidad_producida\":{\"Registrado\":\"5.00\"}},{\"id_producto\":{\"Registrado\":\"PROD-26260-00003-35\"},\"cantidad_producida\":{\"Registrado\":\"7.00\"}}]}', '2026-09-18 08:37:52', 1),
(2911, 'V30485684', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"Ver productos\"},\"respuesta\":{\"Registrado\":\"¡Claro! Aquí tienes el listado completo de nuestros productos disponibles:\\n\\n<ul>\\n    <li><b>BRISOL (LITRO)<\\/b>: 1.211,09 Bs (Ref: $1.50 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (LITRO)<\\/b>: 1.211,09 Bs (Ref: $1.50 USD) cada uno.<\\/li>\\n    <li><b>CERA (LITRO)<\\/b>: 1.614,78 Bs (Ref: $2.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (LITRO)<\\/b>: 1.614,78 Bs (Ref: $2.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (LITRO)<\\/b>: 807,39 Bs (Ref: $1.00 USD) cada uno.<\\/li>\\n    <li><b>BRISOL (BIDÓN)<\\/b>: 24.221,70 Bs (Ref: $30.00 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (BIDÓN)<\\/b>: 24.221,70 Bs (Ref: $30.00 USD) cada uno.<\\/li>\\n    <li><b>CERA (BIDÓN)<\\/b>: 32.295,60 Bs (Ref: $40.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (BIDÓN)<\\/b>: 32.295,60 Bs (Ref: $40.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (BIDÓN)<\\/b>: 16.147,80 Bs (Ref: $20.00 USD) cada uno.<\\/li>\\n    <li><b>BRISOL (PIPA)<\\/b>: 242.217,00 Bs (Ref: $300.00 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (PIPA)<\\/b>: 242.217,00 Bs (Ref: $300.00 USD) cada uno.<\\/li>\\n    <li><b>CERA (PIPA)<\\/b>: 322.956,00 Bs (Ref: $400.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (PIPA)<\\/b>: 322.956,00 Bs (Ref: $400.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (PIPA)<\\/b>: 161.478,00 Bs (Ref: $200.00 USD) cada uno.<\\/li>\\n    <li><b>BRISOL (GALÓN)<\\/b>: 4.844,34 Bs (Ref: $6.00 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (GALÓN)<\\/b>: 4.844,34 Bs (Ref: $6.00 USD) cada uno.<\\/li>\\n    <li><b>CERA (GALÓN)<\\/b>: 6.459,12 Bs (Ref: $8.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (GALÓN)<\\/b>: 6.459,12 Bs (Ref: $8.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (GALÓN)<\\/b>: 3.229,56 Bs (Ref: $4.00 USD) cada uno.<\\/li>\\n    <li><b>BRISOL (CAJA - 1L)<\\/b>: 14.533,02 Bs (Ref: $18.00 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (CAJA - 1L)<\\/b>: 14.533,02 Bs (Ref: $18.00 USD) cada uno.<\\/li>\\n    <li><b>CERA (CAJA - 1L)<\\/b>: 19.377,36 Bs (Ref: $24.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (CAJA - 1L)<\\/b>: 19.377,36 Bs (Ref: $24.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (CAJA - 1L)<\\/b>: 9.688,68 Bs (Ref: $12.00 USD) cada uno.<\\/li>\\n    <li><b>BRISOL (CAJA - GALÓN)<\\/b>: 14.533,02 Bs (Ref: $18.00 USD) cada uno.<\\/li>\\n    <li><b>JABON MULTIUSO (CAJA - GALÓN)<\\/b>: 14.533,02 Bs (Ref: $18.00 USD) cada uno.<\\/li>\\n    <li><b>CERA (CAJA - GALÓN)<\\/b>: 19.377,36 Bs (Ref: $24.00 USD) cada uno.<\\/li>\\n    <li><b>LIMPIA POCETA (CAJA - GALÓN)<\\/b>: 19.377,36 Bs (Ref: $24.00 USD) cada uno.<\\/li>\\n    <li><b>DESINFECTANTE (CAJA - GALÓN)<\\/b>: 9.688,68 Bs (Ref: $12.00 USD) cada uno.<\\/li>\\n<\\/ul>\\n\\n¿Te gustaría agregar alguno de estos productos a tu pedido o tienes alguna otra consulta?\"}}', '2026-09-18 09:28:34', 1),
(2912, 'V30485684', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"quiero uqe me hagas un presupuesto para 100 listros de brisol\"},\"respuesta\":{\"Registrado\":\"¡Claro que sí! Con gusto te preparo el presupuesto para 100 litros de Brisol.\\n\\nAquí tienes el detalle:\\n\\n*   <b>BRISOL (LITRO)<\\/b>: 100 unidades.\\n    *   Costo por unidad: 1.211,09 Bs (Ref: $1.50 USD)\\n    *   Costo total: 121.109,00 Bs (Ref: $150.00 USD)\\n\\n<br><br>\\n\\n<b>Total General<\\/b>: 121.109,00 Bs (Ref: $150.00 USD)\\n\\n¿Deseas realizar alguna modificación o agregar algo más a tu presupuesto?\\n[OFRECER_GUARDADO]\"}}', '2026-09-18 09:29:08', 1),
(2913, 'V30485684', 307, 'Éxito', 'registrar', '0.0.0.0', '{\"cedula_usuario\":{\"Registrado\":\"V30485684\"},\"prompt\":{\"Registrado\":\"quiero uqe me hagas un presupuesto para 100 listros de brisol\"},\"respuesta_bot\":{\"Registrado\":\"¡Claro que sí! Con gusto te preparo el presupuesto para 100 litros de Brisol.\\r\\n\\r\\nAquí tienes el detalle:\\r\\n\\r\\n*   <bBRISOL (LITRO)<\\/b>: 100 unidades.\\r\\n    *   Costo por unidad: 1.211,09 Bs (Ref: $1.50 USD)\\r\\n    *   Costo total: 121.109,00 Bs (Ref: $150.00 USD)\\r\\n\\r\\n<br><br>\\r\\n\\r\\n<bTotal General<\\/b>: 121.109,00 Bs (Ref: $150.00 USD)\\r\\n\\r\\n¿Deseas realizar alguna modificación o agregar algo más a tu presupuesto?\"}}', '2026-09-18 09:29:12', 1),
(2914, 'V30485684', 24, 'Éxito', 'actualizar', '186.167.219.206', '{\"id_unidad_medida\":{\"Modificado\":2},\"stock_materia_prima\":{\"Modificado\":\"59.00\"},\"nombre_unidad_medida\":{\"Modificado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Modificado\":\"L\"},\"presentaciones\":[{\"id_materia_prima_presentacion\":{\"Modificado\":209}},{\"id_materia_prima_presentacion\":{\"Modificado\":210}},{\"id_materia_prima_presentacion\":{\"Modificado\":211}},{\"id_materia_prima_presentacion\":{\"Modificado\":212}},{\"id_materia_prima_presentacion\":{\"Modificado\":213}},{\"id_materia_prima_presentacion\":{\"Modificado\":214}},{\"id_materia_prima_presentacion\":{\"Modificado\":215}},{\"id_materia_prima_presentacion\":{\"Modificado\":216}}]}', '2026-09-18 09:40:16', 1),
(2915, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:41:11', 1),
(2916, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:42:06', 1),
(2917, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:42:28', 1),
(2918, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:42:47', 1),
(2919, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:49:05', 1),
(2920, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 09:50:34', 1),
(2921, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 10:05:38', 1),
(2924, 'V30485684', 309, 'Fallido', 'registrar', '186.167.219.206', '{\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-09-18 10:11:31', 1),
(2927, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '186.167.219.206', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 10:19:42\"}}', '2026-09-18 10:14:42', 1),
(2929, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.219.206', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 10:22:32\"}}', '2026-09-18 10:17:32', 1),
(2935, 'V30485684', 246, 'Éxito', 'Registrar', '186.167.219.206', '{\"id_produccion\":{\"Registrado\":\"PROD-26260-00002-70\"},\"fecha_produccion\":{\"Registrado\":\"2026-09-18 10:23:50\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"10.00\"}}]}', '2026-09-18 10:23:50', 1),
(2936, 'V30485684', 23, 'Éxito', 'actualizar', '186.167.219.206', '{\"valor_moneda\":{\"Modificado\":\"849.00\"}}', '2026-09-18 10:26:23', 1),
(2937, 'V30485684', 4, 'Éxito', 'Registrar', '186.167.219.206', '{\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00043-15\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00044-10\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00045-39\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00046-39\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00047-40\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00048-82\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00049-34\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00001-90\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26260-00050-82\"},\"id_producto\":{\"Registrado\":\"PROD-26260-00006-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26260-00002-08\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-09-18 10:26:56', 1),
(2938, 'V12345678', 9, 'Éxito', 'Iniciar sesión', '186.167.219.206', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 10:42:20\"}}', '2026-09-18 10:37:20', 1),
(2940, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.163.59', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-22 09:26:06\"}}', '2026-09-22 09:21:06', 1),
(2942, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.125.157', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-22 17:16:48\"}}', '2026-09-22 17:11:48', 1),
(2945, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.200.236.162', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-27 18:47:25\"}}', '2026-09-27 18:42:25', 1),
(2947, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.159.48.40', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-29 14:25:50\"}}', '2026-09-29 14:20:50', 1),
(2949, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.125.222', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-29 15:29:02\"}}', '2026-09-29 15:24:03', 1),
(2951, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.125.222', '{\"id_producto\":{\"Registrado\":\"PROD-26271-00001-84\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"JABON ANTIBACTERIAL\"},\"precio_producto\":{\"Registrado\":\"3.00\"},\"stock_producto\":{\"Registrado\":\"0.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26271-00001-91\"},\"id_producto\":{\"Registrado\":\"PROD-26271-00001-84\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":254},\"id_materia_prima\":{\"Registrado\":\"MATE-26157-00001-95\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-09-29 15:31:19', 1),
(2952, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.125.222', '{\"stock_producto\":{\"Modificado\":\"50.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00001-46\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":255}}]}}', '2026-09-29 15:32:04', 1),
(2953, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.125.222', '{\"precio_producto\":{\"Modificado\":\"0.90\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00002-52\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00003-51\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00004-88\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00005-18\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00006-67\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00007-18\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00008-74\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00009-18\"}}],\"materias_primas\":[]}}', '2026-09-29 15:32:36', 1),
(2954, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.125.222', '{\"precio_producto\":{\"Modificado\":\"4.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00010-29\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00011-80\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00012-55\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00013-31\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00014-00\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26271-00015-92\"}}],\"materias_primas\":[]}}', '2026-09-29 15:33:46', 1),
(2955, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.125.222', '{\"id_producto\":{\"Registrado\":\"PROD-26271-00002-85\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"DESENGRASANTE\"},\"precio_producto\":{\"Registrado\":\"4.00\"},\"stock_producto\":{\"Registrado\":\"40.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26271-00016-62\"},\"id_producto\":{\"Registrado\":\"PROD-26271-00002-85\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":256},\"id_materia_prima\":{\"Registrado\":\"MATE-26157-00001-95\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-09-29 15:36:07', 1),
(2957, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.159.48.40', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-29 15:46:18\"}}', '2026-09-29 15:41:18', 1),
(2963, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.159.48.40', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-29 17:37:31\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-09-29 17:32:31', 1),
(2965, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.159.48.40', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-29 20:57:37\"}}', '2026-09-29 20:52:37', 1),
(2967, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.188.165', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-01 15:31:44\"}}', '2026-10-01 15:26:44', 1),
(2969, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.188.165', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-02 16:26:14\"}}', '2026-10-02 16:21:14', 1),
(2971, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.63.128', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-05 16:46:59\"}}', '2026-10-05 16:41:59', 1),
(2975, 'V11429340', 8, 'Éxito', 'registrar', '161.140.63.128', '{\"id_compra\":{\"Registrado\":\"25\"},\"rif_proveedor\":{\"Registrado\":\"J314964291\"},\"fecha_compra\":{\"Registrado\":\"2026-10-05 16:44:00\"},\"detalles\":[{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00011-22\"},\"nombre\":{\"Registrado\":\"\\n                BRISOL (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26271-00014-00\"},\"nombre\":{\"Registrado\":\"\\n                CERA (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26271-00011-80\"},\"nombre\":{\"Registrado\":\"\\n                CERA (BIDÓN)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26271-00006-67\"},\"nombre\":{\"Registrado\":\"\\n                CLORO (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00041-65\"},\"nombre\":{\"Registrado\":\"\\n                DESINFECTANTE (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00041-65\"},\"nombre\":{\"Registrado\":\"\\n                DESINFECTANTE (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00017-59\"},\"nombre\":{\"Registrado\":\"\\n                JABON MULTIUSO (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00032-67\"},\"nombre\":{\"Registrado\":\"\\n                LIMPIA POCETA (BIDÓN)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}},{\"proveedorId\":{\"Registrado\":\"J314964291\"},\"tipo\":{\"Registrado\":\"producto\"},\"id\":{\"Registrado\":\"PRPR-26260-00035-04\"},\"nombre\":{\"Registrado\":\"\\n                LIMPIA POCETA (CAJA - 1L)\\n            \"},\"cantidad\":{\"Registrado\":1},\"id_unidad_medida\":{\"Registrado\":\"2\"}}]}', '2026-10-05 16:53:41', 1),
(2977, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.46', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-07 09:11:27\"}}', '2026-10-07 09:06:27', 1),
(2979, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.157.129', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-08 10:32:00\"}}', '2026-10-08 10:27:00', 1),
(2982, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.87.241', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-09 09:42:05\"}}', '2026-10-09 09:37:05', 1),
(2984, 'V11429340', 309, 'Fallido', 'registrar', '161.140.87.241', '{\"rif\":{\"Registrado\":\"J310729425\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-09 09:53:25', 1),
(2992, 'V11429340', 309, 'Fallido', 'registrar', '161.140.87.241', '{\"rif\":{\"Registrado\":\"J310729425\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-09 11:37:50', 1),
(2993, 'V11429340', 309, 'Fallido', 'registrar', '161.140.87.241', '{\"rif\":{\"Registrado\":\"J310729425\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-09 11:38:37', 1),
(2994, 'V11429340', 309, 'Fallido', 'registrar', '161.140.87.241', '{\"rif\":{\"Registrado\":\"J310729425\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-09 11:40:45', 1),
(2995, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.228.106', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-09 12:25:25\"}}', '2026-10-09 12:20:25', 1),
(2997, 'V30485684', 22, 'Éxito', 'registrar', '186.167.228.106', '{\"id_unidad_medida\":{\"Registrado\":\"40\"},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":1}}', '2026-10-09 12:21:35', 1),
(2998, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.58.55', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-09 16:56:30\"}}', '2026-10-09 16:51:30', 1),
(3000, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-09 17:05:07\"}}', '2026-10-09 17:00:07', 1),
(3002, 'V30485684', 21, 'Éxito', 'registrar', '154.40.142.21', '{\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"id_unidad_medida\":{\"Registrado\":\"40\"},\"nombre_presentacion\":{\"Registrado\":\"UNIDAD\"},\"cantidad_pmp\":{\"Registrado\":1}}', '2026-10-09 17:01:15', 1),
(3003, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.46', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-11 18:21:15\"}}', '2026-10-11 18:16:15', 1),
(3007, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '82.86.166.15', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-11 18:31:38\"}}', '2026-10-11 18:26:38', 1),
(3009, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '82.86.166.15', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-11 18:40:34\"}}', '2026-10-11 18:35:34', 1),
(3012, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00001-53\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopa Nro..32\"},\"precio_producto\":{\"Registrado\":\"12.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00001-66\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00001-53\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:41:09', 1),
(3014, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00002-33\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopa Nro 34\"},\"precio_producto\":{\"Registrado\":\"14.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00002-59\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00002-33\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:43:56', 1),
(3015, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00003-39\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopa sencilla\"},\"precio_producto\":{\"Registrado\":\"8.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00003-87\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00003-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:44:42', 1),
(3016, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00004-71\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Pañitos Amarillos Marca\"},\"precio_producto\":{\"Registrado\":\"0.50\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00004-95\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00004-71\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:45:30', 1),
(3017, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00005-56\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Pañitos color.\"},\"precio_producto\":{\"Registrado\":\"0.50\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00005-30\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00005-56\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:46:13', 1),
(3018, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00006-00\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Aragan sencillo\"},\"precio_producto\":{\"Registrado\":\"5.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00006-35\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00006-00\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:47:01', 1),
(3019, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00007-51\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Aragan Marca\"},\"precio_producto\":{\"Registrado\":\"5.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00007-41\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00007-51\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:47:42', 1),
(3020, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00008-73\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Papel higiénico Bulto 4 rollos Marca\"},\"precio_producto\":{\"Registrado\":\"5.70\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00008-86\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00008-73\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:48:28', 1),
(3021, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00009-58\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Ciclon\"},\"precio_producto\":{\"Registrado\":\"6.07\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00009-89\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00009-58\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:49:09', 1),
(3022, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00010-03\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Esponjas verdes\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00010-74\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00010-03\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:49:54', 1),
(3023, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00011-79\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo tipo planchita\"},\"precio_producto\":{\"Registrado\":\"2.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00011-75\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00011-79\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:50:28', 1),
(3024, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00012-51\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo de.barrer cerda gruesa.Marca\"},\"precio_producto\":{\"Registrado\":\"5.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00012-81\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00012-51\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:51:11', 1),
(3025, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00013-86\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillos de Barrer cerdas gruesas Marca\"},\"precio_producto\":{\"Registrado\":\"4.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00013-37\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00013-86\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:52:06', 1),
(3026, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00014-31\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo de barrer cerdas Finas\"},\"precio_producto\":{\"Registrado\":\"6.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00014-33\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00014-31\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:52:44', 1),
(3027, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00015-45\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolas negras 200 Lts\"},\"precio_producto\":{\"Registrado\":\"0.07\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00015-22\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00015-45\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:54:29', 1),
(3028, 'V11429340', 4, 'Éxito', 'Registrar', '38.172.138.46', '{\"id_producto\":{\"Registrado\":\"PROD-26283-00016-59\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas Negras 100 Lts\"},\"precio_producto\":{\"Registrado\":\"0.70\"},\"stock_producto\":{\"Registrado\":\"0.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26283-00016-43\"},\"id_producto\":{\"Registrado\":\"PROD-26283-00016-59\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-11 18:55:38', 1),
(3031, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.46', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-12 03:36:37\"}}', '2026-10-12 03:31:37', 1),
(3035, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.46', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-12 04:26:31\"}}', '2026-10-12 04:21:31', 1),
(3040, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.91.206', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-12 15:04:45\"}}', '2026-10-12 14:59:45', 1),
(3059, 'V11429340', 262, 'Éxito', 'eliminar módulo con id: 262', '161.140.91.206', NULL, '2026-10-12 15:18:03', 1),
(3062, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.216.208', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 08:53:34\"}}', '2026-10-13 08:48:34', 1),
(3064, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.41.22.0', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 09:44:24\"}}', '2026-10-13 09:39:24', 1),
(3066, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 11:40:02\"}}', '2026-10-13 11:35:02', 1),
(3068, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.41.22.3', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 11:48:26\"}}', '2026-10-13 11:43:26', 1),
(3070, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 12:05:49\"}}', '2026-10-13 12:00:49', 1),
(3072, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.117.187', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 12:18:33\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-10-13 12:13:33', 1),
(3074, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.200.236.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-13 21:39:57\"}}', '2026-10-13 21:34:57', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(3078, 'V30485684', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"Ver productos\"},\"respuesta\":{\"Registrado\":\"¡Hola! Con gusto te muestro nuestro catálogo de productos.\\n\\nAquí tienes la lista de lo que ofrecemos:\\n\\n<br>\\n\\n**PRODUCTOS DISPONIBLES:**\\n\\n<ul>\\n    <li><b>BRISOL (LITRO)<\\/b>: 1.211,09 Bs (Ref: $1.50 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (LITRO)<\\/b>: 1.211,09 Bs (Ref: $1.50 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (LITRO)<\\/b>: 1.614,78 Bs (Ref: $2.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (LITRO)<\\/b>: 807,39 Bs (Ref: $1.00 USD)<\\/li>\\n    <li><b>JABON ANTIBACTERIAL (LITRO)<\\/b>: 2.422,17 Bs (Ref: $3.00 USD)<\\/li>\\n    <li><b>CLORO (LITRO)<\\/b>: 726,65 Bs (Ref: $0.90 USD)<\\/li>\\n    <li><b>CERA (LITRO)<\\/b>: 3.229,56 Bs (Ref: $4.00 USD)<\\/li>\\n    <li><b>DESENGRASANTE (LITRO)<\\/b>: 3.229,56 Bs (Ref: $4.00 USD)<\\/li>\\n    <li><b>BRISOL (BIDÓN)<\\/b>: 24.221,70 Bs (Ref: $30.00 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (BIDÓN)<\\/b>: 24.221,70 Bs (Ref: $30.00 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (BIDÓN)<\\/b>: 32.295,60 Bs (Ref: $40.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (BIDÓN)<\\/b>: 16.147,80 Bs (Ref: $20.00 USD)<\\/li>\\n    <li><b>CLORO (BIDÓN)<\\/b>: 14.533,02 Bs (Ref: $18.00 USD)<\\/li>\\n    <li><b>CERA (BIDÓN)<\\/b>: 64.591,20 Bs (Ref: $80.00 USD)<\\/li>\\n    <li><b>BRISOL (PIPA)<\\/b>: 242.217,00 Bs (Ref: $300.00 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (PIPA)<\\/b>: 242.217,00 Bs (Ref: $300.00 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (PIPA)<\\/b>: 322.956,00 Bs (Ref: $400.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (PIPA)<\\/b>: 161.478,00 Bs (Ref: $200.00 USD)<\\/li>\\n    <li><b>CLORO (PIPA)<\\/b>: 145.330,20 Bs (Ref: $180.00 USD)<\\/li>\\n    <li><b>CERA (PIPA)<\\/b>: 645.912,00 Bs (Ref: $800.00 USD)<\\/li>\\n    <li><b>BRISOL (GALÓN)<\\/b>: 4.844,34 Bs (Ref: $6.00 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (GALÓN)<\\/b>: 4.844,34 Bs (Ref: $6.00 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (GALÓN)<\\/b>: 6.459,12 Bs (Ref: $8.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (GALÓN)<\\/b>: 3.229,56 Bs (Ref: $4.00 USD)<\\/li>\\n    <li><b>CLORO (GALÓN)<\\/b>: 2.906,60 Bs (Ref: $3.60 USD)<\\/li>\\n    <li><b>CERA (GALÓN)<\\/b>: 12.918,24 Bs (Ref: $16.00 USD)<\\/li>\\n    <li><b>BRISOL (CAJA - 1L)<\\/b>: 12.110,85 Bs (Ref: $18.00 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (CAJA - 1L)<\\/b>: 12.110,85 Bs (Ref: $18.00 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (CAJA - 1L)<\\/b>: 16.147,80 Bs (Ref: $24.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (CAJA - 1L)<\\/b>: 8.073,90 Bs (Ref: $12.00 USD)<\\/li>\\n    <li><b>CLORO (CAJA - 1L)<\\/b>: 7.323,15 Bs (Ref: $10.80 USD)<\\/li>\\n    <li><b>CERA (CAJA - 1L)<\\/b>: 32.295,60 Bs (Ref: $48.00 USD)<\\/li>\\n    <li><b>BRISOL (CAJA - GALÓN)<\\/b>: 12.110,85 Bs (Ref: $18.00 USD)<\\/li>\\n    <li><b>JABON MULTIUSO (CAJA - GALÓN)<\\/b>: 12.110,85 Bs (Ref: $18.00 USD)<\\/li>\\n    <li><b>LIMPIA POCETA (CAJA - GALÓN)<\\/b>: 16.147,80 Bs (Ref: $24.00 USD)<\\/li>\\n    <li><b>DESINFECTANTE (CAJA - GALÓN)<\\/b>: 8.073,90 Bs (Ref: $12.00 USD)<\\/li>\\n    <li><b>CLORO (CAJA - GALÓN)<\\/b>: 7.323,15 Bs (Ref: $10.80 USD)<\\/li>\\n    <li><b>CERA (CAJA - GALÓN)<\\/b>: 32.295,60 Bs (Ref: $48.00 USD)<\\/li>\\n    <li><b>CLORO (BOLSA)<\\/b>: 305.643,00 Bs (Ref: $450.00 USD)<\\/li>\\n    <li><b>CLORO (EMPAQUE)<\\/b>: 611.286,00 Bs (Ref: $900.00 USD)<\\/li>\\n    <li><b>Mopa Nro..32 (UNIDAD)<\\/b>: 8.073,90 Bs (Ref: $12.00 USD)<\\/li>\\n    <li><b>Mopa Nro 34 (UNIDAD)<\\/b>: 9.508,92 Bs (Ref: $14.00 USD)<\\/li>\\n    <li><b>Mopa sencilla (UNIDAD)<\\/b>: 6.459,12 Bs (Ref: $8.00 USD)<\\/li>\\n    <li><b>Pañitos Amarillos Marca (UNIDAD)<\\/b>: 424,50 Bs (Ref: $0.50 USD)<\\/li>\\n    <li><b>Pañitos color. (UNIDAD)<\\/b>: 424,50 Bs (Ref: $0.50 USD)<\\/li>\\n    <li><b>Aragan sencillo (UNIDAD)<\\/b>: 4.245,00 Bs (Ref: $5.00 USD)<\\/li>\\n    <li><b>Aragan Marca (UNIDAD)<\\/b>: 4.245,00 Bs (Ref: $5.00 USD)<\\/li>\\n    <li><b>Papel higiénico Bulto 4 rollos Marca (UNIDAD)<\\/b>: 4.780,08 Bs (Ref: $5.70 USD)<\\/li>\\n    <li><b>Ciclon (UNIDAD)<\\/b>: 5.085,96 Bs (Ref: $6.07 USD)<\\/li>\\n    <li><b>Esponjas verdes (UNIDAD)<\\/b>: 807,39 Bs (Ref: $1.00 USD)<\\/li>\\n    <li><b>Cepillo tipo planchita (UNIDAD)<\\/b>: 1.614,78 Bs (Ref: $2.00 USD)<\\/li>\\n    <li><b>Cepillo de.barrer cerda gruesa.Marca (UNIDAD)<\\/b>: 4.245,00 Bs (Ref: $5.00 USD)<\\/li>\\n    <li><b>Cepillos de Barrer cerdas gruesas Marca (UNIDAD)<\\/b>: 3.229,56 Bs (Ref: $4.00 USD)<\\/li>\\n    <li><b>Cepillo de barrer cerdas Finas (UNIDAD)<\\/b>: 4.844,34 Bs (Ref: $6.00 USD)<\\/li>\\n    <li><b>Bolas negras 200 Lts (UNIDAD)<\\/b>: 59,43 Bs (Ref: $0.07 USD)<\\/li>\\n    <li><b>Bolsas Negras 100 Lts (UNIDAD)<\\/b>: 594,30 Bs (Ref: $0.70 USD)<\\/li>\\n<\\/ul>\\n\\n<br>\\n\\n**SERVICIOS DISPONIBLES:**\\n\\n<ul>\\n    <li><b>FUMIGACION<\\/b>: 80,74 Bs (Ref: $0.10 USD)<\\/li>\\n    <li><b>ASEO Y LIMPIEZA<\\/b>: 242,17 Bs (Ref: $0.30 USD)<\\/li>\\n    <li><b>DESMALEZADO<\\/b>: 726,65 Bs (Ref: $0.90 USD)<\\/li>\\n    <li><b>BOTE DE ESCOMBROS<\\/b>: 48.443,40 Bs (Ref: $60.00 USD)<\\/li>\\n    <li><b>RECARGA DE BOTELLONES<\\/b>: 424,50 Bs (Ref: $0.50 USD)<\\/li>\\n    <li><b>CISTERNA DE AGUA<\\/b>: 48.443,40 Bs (Ref: $60.00 USD)<\\/li>\\n    <li><b>PICO Y PODA<\\/b>: 8.073,90 Bs (Ref: $10.00 USD)<\\/li>\\n    <li><b>ACARREO DE MUDANZAS<\\/b>: 24.221,70 Bs (Ref: $30.00 USD)<\\/li>\\n    <li><b>MANTENIMIENTO DE SALAS DE BANO<\\/b>: 121.108,50 Bs (Ref: $150.00 USD)<\\/li>\\n<\\/ul>\\n\\n<br>\\n\\nSi deseas un presupuesto o tienes alguna pregunta sobre algún producto o servicio, no dudes en consultarme.\"}}', '2026-10-13 21:36:30', 1),
(3079, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.178.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 09:20:03\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-10-14 09:15:03', 1),
(3081, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.248.176.33', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 12:03:03\"}}', '2026-10-14 11:58:03', 1),
(3084, 'V30485684', 28, 'Éxito', 'registrar', '38.248.176.33', '{\"id_permiso\":{\"Registrado\":679},\"nombre_permiso\":{\"Registrado\":\"imprimir\"},\"status\":{\"Registrado\":1}}', '2026-10-14 12:02:49', 1),
(3085, 'V30485684', 309, 'Fallido', 'registrar', '38.137.177.195', '{\"rif\":{\"Registrado\":\"V30123456\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-14 12:05:01', 1),
(3086, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.137.177.195', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 12:28:59\"}}', '2026-10-14 12:23:59', 1),
(3088, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.137.177.195', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 14:25:58\"}}', '2026-10-14 14:20:58', 1),
(3090, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '15.204.43.249', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 20:26:13\"}}', '2026-10-14 20:21:13', 1),
(3093, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '15.204.43.249', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 22:33:22\"}}', '2026-10-14 22:28:22', 1),
(3095, 'V30485684', 309, 'Éxito', 'registrar', '15.204.43.249', '{\"id_orden\":{\"Registrado\":\"OEP-26286-00001-76\"},\"cliente\":{\"Registrado\":\"Carlos Hurtado\"},\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-14 22:36:05', 1),
(3096, 'V30485684', 309, 'Éxito', 'Actualizar OEP: OEP-26286-00001-76', '15.204.43.249', '{\"status\":{\"Modificado\":10}}', '2026-10-14 22:42:58', 1),
(3097, 'V30485684', 259, 'Éxito', 'Registrar Pago: PAG-26286-00001-55', '15.204.43.249', '{\"id_pago\":{\"Registrado\":\"PAG-26286-00001-55\"},\"id_orden\":{\"Registrado\":\"OEP-26286-00001-76\"}}', '2026-10-14 22:42:58', 1),
(3098, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '146.70.147.99', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-14 23:31:21\"}}', '2026-10-14 23:26:22', 1),
(3100, 'V30485684', 309, 'Éxito', 'registrar', '146.70.147.99', '{\"id_orden\":{\"Registrado\":\"OEP-26286-00002-01\"},\"cliente\":{\"Registrado\":\"Makro\"},\"rif\":{\"Registrado\":\"V30123456\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-14 23:48:06', 1),
(3101, 'V30485684', 309, 'Éxito', 'procesarPresupuesto', '146.70.147.99', '{\"status\":{\"Modificado\":1},\"productos\":{\"Registrado\":1}}', '2026-10-14 23:48:28', 1),
(3102, 'V30485684', 309, 'Éxito', 'anular', '146.70.147.99', '{\"status\":{\"Modificado\":2}}', '2026-10-14 23:48:54', 1),
(3104, 'V30485684', 309, 'Éxito', 'registrar', '146.70.147.99', '{\"id_orden\":{\"Registrado\":\"OEP-26286-00003-91\"},\"cliente\":{\"Registrado\":\"NETUNO1\"},\"rif\":{\"Registrado\":\"V30123123\"},\"productos\":{\"Registrado\":0},\"servicios\":{\"Registrado\":1}}', '2026-10-14 23:51:27', 1),
(3105, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '15.204.43.249', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-15 00:01:22\"}}', '2026-10-14 23:56:22', 1),
(3108, 'V30485684', 309, 'Éxito', 'Actualizar OEP: OEP-26286-00003-91', '15.204.43.249', '{\"status\":{\"Modificado\":10}}', '2026-10-15 00:21:55', 1),
(3109, 'V30485684', 259, 'Éxito', 'Registrar Pago: PAG-26287-00001-04', '15.204.43.249', '{\"id_pago\":{\"Registrado\":\"PAG-26287-00001-04\"},\"id_orden\":{\"Registrado\":\"OEP-26286-00003-91\"}}', '2026-10-15 00:21:55', 1),
(3110, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.182.241', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-15 16:46:06\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-10-15 16:41:06', 1),
(3112, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-15 20:31:25\"}}', '2026-10-15 20:26:25', 1),
(3114, 'V30485684', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"Pedir presupuesto\"},\"respuesta\":{\"Registrado\":\"¡Excelente! Para poder generar tu presupuesto, por favor, indícame qué productos o servicios necesitas. Puedes detallarme las cantidades y los nombres de los artículos o servicios.\\n\\nPor ejemplo, podrías decirme:\\n*   \\\"Necesito 2 litros de Jabón Multiuso y 1 servicio de Fumigación.\\\"\\n*   \\\"Me gustaría un presupuesto para 5 bidones de Desinfectante y 1 cisterna de agua.\\\"\\n\\n¡Estoy a tu disposición para armar tu presupuesto!\"}}', '2026-10-15 20:26:58', 1),
(3115, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.153.22', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-16 18:24:53\"}}', '2026-10-16 18:19:53', 1),
(3117, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.153.22', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-16 18:32:27\"}}', '2026-10-16 18:27:27', 1),
(3119, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.69.133', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-19 17:19:52\"}}', '2026-10-19 17:14:52', 1),
(3121, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.167.232.21', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-21 09:34:05\"}}', '2026-10-21 09:29:05', 1),
(3124, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00001-52\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Escobillon c\\/extension\"},\"precio_producto\":{\"Registrado\":\"8.50\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00001-88\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00001-52\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:33:20', 1),
(3125, 'V11429340', 4, 'Éxito', 'Actualizar', '186.167.232.21', '{\"precio_producto\":{\"Modificado\":\"10.20\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26293-00001-04\"}}],\"materias_primas\":[]}}', '2026-10-21 10:41:00', 1),
(3126, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00002-47\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo de Lavar\"},\"precio_producto\":{\"Registrado\":\"3.42\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00002-62\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00002-47\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:43:42', 1),
(3127, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00003-68\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Esponjas Limpia Sol\"},\"precio_producto\":{\"Registrado\":\"0.95\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00003-19\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00003-68\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:47:25', 1),
(3128, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00004-99\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Esponjas de Alambre\"},\"precio_producto\":{\"Registrado\":\"0.60\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00004-75\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00004-99\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:51:10', 1),
(3130, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00005-15\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Aragan Pequeño Doble Goma\"},\"precio_producto\":{\"Registrado\":\"6.96\"},\"stock_producto\":{\"Registrado\":\"6.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00005-75\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00005-15\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:53:59', 1),
(3131, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00006-93\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Plagatox\"},\"precio_producto\":{\"Registrado\":\"7.80\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00006-99\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00006-93\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:55:12', 1),
(3132, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00007-17\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Rastrillo Pequeño\"},\"precio_producto\":{\"Registrado\":\"3.60\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00007-33\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00007-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:56:27', 1),
(3133, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00008-69\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Palas\"},\"precio_producto\":{\"Registrado\":\"4.56\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00008-06\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00008-69\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:57:24', 1),
(3134, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00009-22\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillos de Barrer Dalia\"},\"precio_producto\":{\"Registrado\":\"4.80\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00009-07\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00009-22\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 10:59:50', 1),
(3135, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00010-95\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo de Barrer Garbi\"},\"precio_producto\":{\"Registrado\":\"4.56\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00010-41\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00010-95\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:01:33', 1),
(3136, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00011-32\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo de Barrer Rustico\"},\"precio_producto\":{\"Registrado\":\"8.16\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00011-65\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00011-32\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:02:42', 1),
(3137, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00012-32\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Chupones de Poceta\"},\"precio_producto\":{\"Registrado\":\"6.48\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00012-52\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00012-32\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:09:26', 1),
(3138, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00013-71\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Ambientadores Glade\"},\"precio_producto\":{\"Registrado\":\"7.68\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00013-57\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00013-71\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:10:35', 1),
(3139, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00014-08\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Pares de Guantes\"},\"precio_producto\":{\"Registrado\":\"7.20\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00014-11\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00014-08\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:11:36', 1),
(3140, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00015-65\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Carro de Baura\"},\"precio_producto\":{\"Registrado\":\"102.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00015-24\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00015-65\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:13:07', 1),
(3141, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00016-19\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Tobo de Exprimir Mopa\"},\"precio_producto\":{\"Registrado\":\"100.80\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00016-43\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00016-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:18:21', 1),
(3142, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00017-42\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Papeleras\"},\"precio_producto\":{\"Registrado\":\"20.40\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00017-76\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00017-42\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:19:24', 1),
(3143, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00018-27\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Pañitos Amarillos\"},\"precio_producto\":{\"Registrado\":\"1.50\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00018-68\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00018-27\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:22:17', 1),
(3144, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00019-67\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Coletos\"},\"precio_producto\":{\"Registrado\":\"2.40\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00019-90\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00019-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:24:42', 1),
(3145, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00020-08\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillo Limpia Poceta\"},\"precio_producto\":{\"Registrado\":\"5.40\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00020-30\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00020-08\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:32:36', 1),
(3146, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00021-02\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Limpia Vidrios Pequeño\"},\"precio_producto\":{\"Registrado\":\"11.40\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00021-41\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00021-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:33:47', 1),
(3147, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00022-32\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Haragan Metalico Doble Goma\"},\"precio_producto\":{\"Registrado\":\"10.80\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00022-72\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00022-32\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:36:04', 1),
(3148, 'V11429340', 4, 'Éxito', 'Actualizar', '186.167.232.21', '{\"nombre_producto\":{\"Modificado\":\"Haragan Pequeño Doble Goma\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26293-00023-66\"}}],\"materias_primas\":[]}}', '2026-10-21 11:36:26', 1),
(3149, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00023-67\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Papel Higienico Sutil\"},\"precio_producto\":{\"Registrado\":\"6.00\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00024-80\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00023-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:38:45', 1),
(3150, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00024-03\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas Papeleras Chillonas 30Lts\"},\"precio_producto\":{\"Registrado\":\"0.07\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00025-85\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00024-03\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:41:26', 1),
(3151, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00025-11\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas Papeleras Chillonas 60Lts\"},\"precio_producto\":{\"Registrado\":\"0.01\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00026-30\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00025-11\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:49:12', 1),
(3153, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00026-25\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopas #42\"},\"precio_producto\":{\"Registrado\":\"6.96\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00027-21\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00026-25\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:54:46', 1),
(3154, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00027-32\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopas #36\"},\"precio_producto\":{\"Registrado\":\"6.48\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00028-34\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00027-32\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:55:41', 1),
(3155, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00028-67\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Mopas #660\"},\"precio_producto\":{\"Registrado\":\"8.16\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00029-90\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00028-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 11:57:02', 1),
(3156, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00029-59\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Cepillos de Pocetas S\\/Taza\"},\"precio_producto\":{\"Registrado\":\"4.80\"},\"stock_producto\":{\"Registrado\":\"2.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00030-64\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00029-59\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:03:15', 1),
(3157, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00030-00\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Coletos Grandes\"},\"precio_producto\":{\"Registrado\":\"3.36\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00031-82\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00030-00\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:04:58', 1),
(3158, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00031-98\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas de Basura 120Lts\"},\"precio_producto\":{\"Registrado\":\"0.17\"},\"stock_producto\":{\"Registrado\":\"50.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00032-43\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00031-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:28:30', 1),
(3159, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00032-66\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas de Basura 150Lts\"},\"precio_producto\":{\"Registrado\":\"0.23\"},\"stock_producto\":{\"Registrado\":\"0.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00033-49\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00032-66\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:29:28', 1),
(3160, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00033-73\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Bolsas de Basura 200Lts\"},\"precio_producto\":{\"Registrado\":\"0.46\"},\"stock_producto\":{\"Registrado\":\"25.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00034-73\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00033-73\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:30:32', 1),
(3161, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00034-13\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Glade Rep Automatico\"},\"precio_producto\":{\"Registrado\":\"7.80\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00035-19\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00034-13\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:31:33', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(3162, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00035-45\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Jabon Dalan\"},\"precio_producto\":{\"Registrado\":\"3.00\"},\"stock_producto\":{\"Registrado\":\"3.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00036-17\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00035-45\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:32:24', 1),
(3163, 'V11429340', 4, 'Éxito', 'Actualizar', '186.167.232.21', '{\"nombre_producto\":{\"Modificado\":\"Jabon Dalan Liquido\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26293-00036-49\"}}],\"materias_primas\":[]}}', '2026-10-21 12:32:38', 1),
(3165, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00036-29\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Jabon Moncler Liquido\"},\"precio_producto\":{\"Registrado\":\"3.60\"},\"stock_producto\":{\"Registrado\":\"3.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00037-97\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00036-29\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:33:56', 1),
(3166, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00037-76\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Guantes Eterna\"},\"precio_producto\":{\"Registrado\":\"5.40\"},\"stock_producto\":{\"Registrado\":\"4.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00038-01\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00037-76\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:34:58', 1),
(3167, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00038-32\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Embudos\"},\"precio_producto\":{\"Registrado\":\"3.84\"},\"stock_producto\":{\"Registrado\":\"3.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00039-46\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00038-32\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:36:32', 1),
(3168, 'V11429340', 4, 'Éxito', 'Registrar', '186.167.232.21', '{\"id_producto\":{\"Registrado\":\"PROD-26293-00039-67\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Papel Higienico 9\"},\"precio_producto\":{\"Registrado\":\"3.76\"},\"stock_producto\":{\"Registrado\":\"4.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26293-00040-73\"},\"id_producto\":{\"Registrado\":\"PROD-26293-00039-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-21 12:42:34', 1),
(3170, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '38.159.48.40', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-21 15:19:39\"}}', '2026-10-21 15:14:39', 1),
(3173, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '186.167.49.58', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-22 11:26:51\"}}', '2026-10-22 11:21:52', 1),
(3176, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '186.167.49.58', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-22 12:31:19\"}}', '2026-10-22 12:26:19', 1),
(3178, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '149.102.242.100', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-22 19:59:30\"}}', '2026-10-22 19:54:30', 1),
(3180, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.131.70', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 09:31:55\"}}', '2026-10-24 09:26:55', 1),
(3182, 'V11429340', 23, 'Éxito', 'actualizar', '186.165.210.42', '{\"valor_moneda\":{\"Modificado\":\"886.23\"}}', '2026-10-24 09:51:02', 1),
(3183, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.210.42', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00001-53\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LIMPIA POCETA\\/LIMPIA CERAMICA\"},\"precio_producto\":{\"Registrado\":\"2.60\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00001-31\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00001-53\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 11:04:48', 1),
(3184, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"nombre_producto\":{\"Modificado\":\"Limpia Poceta\\/Limpia Ceramica 1Lts\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00001-88\"}}],\"materias_primas\":[]}}', '2026-10-24 11:06:35', 1),
(3185, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.210.42', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00002-67\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Tapa Boca KN95\"},\"precio_producto\":{\"Registrado\":\"0.50\"},\"stock_producto\":{\"Registrado\":\"273.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00002-78\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 11:10:37', 1),
(3186, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"nombre_producto\":{\"Modificado\":\"Limpia Poceta\\/Limpia Ceramica 1Lts Marca CLIC\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00003-36\"}}],\"materias_primas\":[]}}', '2026-10-24 11:12:10', 1),
(3187, 'V11429340', 309, 'Éxito', 'registrar', '186.165.210.42', '{\"id_orden\":{\"Registrado\":\"OEP-26296-00001-31\"},\"cliente\":{\"Registrado\":\"Jorge Quintero\"},\"rif\":{\"Registrado\":\"V11429340\"},\"productos\":{\"Registrado\":3},\"servicios\":{\"Registrado\":0}}', '2026-10-24 11:15:16', 1),
(3188, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"precio_producto\":{\"Modificado\":\"1.30\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00004-44\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00005-22\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00006-89\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00007-69\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00008-95\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00009-36\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":257}},{\"id_materia_prima_producto\":{\"Modificado\":258}},{\"id_materia_prima_producto\":{\"Modificado\":259}},{\"id_materia_prima_producto\":{\"Modificado\":260}},{\"id_materia_prima_producto\":{\"Modificado\":261}},{\"id_materia_prima_producto\":{\"Modificado\":262}}]}}', '2026-10-24 11:23:35', 1),
(3189, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"precio_producto\":{\"Modificado\":\"0.80\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00010-01\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00011-54\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00012-54\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00013-21\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00014-84\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00015-90\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00016-08\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00017-01\"}}],\"materias_primas\":[]}}', '2026-10-24 11:24:03', 1),
(3190, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"precio_producto\":{\"Modificado\":\"3.70\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00018-92\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00019-02\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00020-85\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00021-84\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00022-53\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00023-06\"}}],\"materias_primas\":[]}}', '2026-10-24 11:25:23', 1),
(3191, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.210.42', '{\"nombre_producto\":{\"Modificado\":\"CERA MARCA CLICK\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00018-52\"}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00019-02\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00020-85\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00021-84\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00022-53\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00023-06\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00004-75\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}}],\"materias_primas\":[]}}', '2026-10-24 11:27:24', 1),
(3192, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.210.42', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00003-65\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LAVAPLATOS MARCA J.LACRUZ\"},\"precio_producto\":{\"Registrado\":\"1.50\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00019-45\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00003-65\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 11:44:53', 1),
(3193, 'V11429340', 309, 'Éxito', 'anular', '186.165.210.42', '{\"status\":{\"Modificado\":2}}', '2026-10-24 11:47:49', 1),
(3194, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.210.42', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00004-20\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LAVAPLATOS GALON MARCA J.LACRUZ\"},\"precio_producto\":{\"Registrado\":\"5.20\"},\"stock_producto\":{\"Registrado\":\"15.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00020-65\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00004-20\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 11:52:20', 1),
(3195, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.210.42', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00005-97\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"JABON MULTIUSO GALON MARCA J.LACRUZ\"},\"precio_producto\":{\"Registrado\":\"5.20\"},\"stock_producto\":{\"Registrado\":\"20.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00021-18\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00005-97\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 12:00:37', 1),
(3196, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.248.178.187', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 12:21:32\"}}', '2026-10-24 12:16:32', 1),
(3198, 'V30485684', 309, 'Éxito', 'registrar', '38.248.178.187', '{\"id_orden\":{\"Registrado\":\"OEP-26296-00002-85\"},\"cliente\":{\"Registrado\":\"Carlos Hurtado\"},\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-24 12:17:35', 1),
(3199, 'V30485684', 309, 'Éxito', 'procesarPresupuesto', '38.248.178.187', '{\"status\":{\"Modificado\":1},\"productos\":{\"Registrado\":2}}', '2026-10-24 12:18:50', 1),
(3200, 'V30485684', 309, 'Éxito', 'anular', '38.248.178.187', '{\"status\":{\"Modificado\":2}}', '2026-10-24 12:18:55', 1),
(3201, 'V11429340', 309, 'Éxito', 'registrar', '186.165.210.42', '{\"id_orden\":{\"Registrado\":\"OEP-26296-00003-31\"},\"cliente\":{\"Registrado\":\"Jorge Quintero\"},\"rif\":{\"Registrado\":\"V11429340\"},\"productos\":{\"Registrado\":5},\"servicios\":{\"Registrado\":0}}', '2026-10-24 12:19:55', 1),
(3202, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.127.220.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 14:09:41\"}}', '2026-10-24 14:04:42', 1),
(3205, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.248.178.187', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 14:37:58\"}}', '2026-10-24 14:32:58', 1),
(3207, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.73.7', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 15:04:56\"}}', '2026-10-24 14:59:56', 1),
(3209, 'V11429340', 309, 'Éxito', 'Actualizar OEP: OEP-26296-00003-31', '161.140.73.7', '{\"status\":{\"Modificado\":10}}', '2026-10-24 15:06:40', 1),
(3210, 'V11429340', 259, 'Éxito', 'Registrar Pago: PAG-26296-00001-73', '161.140.73.7', '{\"id_pago\":{\"Registrado\":\"PAG-26296-00001-73\"},\"id_orden\":{\"Registrado\":\"OEP-26296-00003-31\"}}', '2026-10-24 15:06:40', 1),
(3211, 'V11429340', 309, 'Éxito', 'registrar', '161.140.73.7', '{\"id_orden\":{\"Registrado\":\"OEP-26296-00004-43\"},\"cliente\":{\"Registrado\":\"Jorge Quintero\"},\"rif\":{\"Registrado\":\"V11429340\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-24 15:08:11', 1),
(3212, 'V11429340', 309, 'Éxito', 'anular', '161.140.73.7', '{\"status\":{\"Modificado\":2}}', '2026-10-24 15:08:33', 1),
(3213, 'V11429340', 19, 'Éxito', 'registtrar', '161.140.120.27', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"PUNTO DE VENTA\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-10-24 15:10:34', 1),
(3214, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.248.178.187', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-24 15:17:15\"}}', '2026-10-24 15:12:15', 1),
(3216, 'V11429340', 19, 'Éxito', 'Actualizar', '161.140.120.27', '{\"necesita_moneda\":{\"Modificado\":0}}', '2026-10-24 15:13:58', 1),
(3217, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00006-81\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"JABON LAVA PLATOS MARCA CLICK\"},\"precio_producto\":{\"Registrado\":\"2.70\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00022-24\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00006-81\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 15:48:15', 1),
(3218, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00007-00\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"DESINECTANTE MARCA CLIP 1LT\"},\"precio_producto\":{\"Registrado\":\"1.50\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00023-15\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00007-00\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 15:50:33', 1),
(3219, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00008-06\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"SUAVIZANTE MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"2.60\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00024-25\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00008-06\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 15:56:42', 1),
(3220, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00009-57\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"JABON MULTIUSO MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"2.50\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00025-60\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00009-57\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 15:59:10', 1),
(3221, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00010-61\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"DESENGRASANTE MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"2.60\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00026-58\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00010-61\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 16:00:25', 1),
(3222, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00011-13\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"PIPAS 200 LTS\"},\"precio_producto\":{\"Registrado\":\"50.00\"},\"stock_producto\":{\"Registrado\":\"20.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00027-05\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00011-13\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 16:01:21', 1),
(3223, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00012-86\"},\"id_unidad_medida\":{\"Registrado\":40},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"BOLSAS DE BASURA 20 LTS PAQ 20UNI\"},\"precio_producto\":{\"Registrado\":\"3.00\"},\"stock_producto\":{\"Registrado\":\"80.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"UNIDAD\"},\"simbolo_unidad_medida\":{\"Registrado\":\"UND\"},\"equivalencia_ub\":{\"Registrado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00028-26\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00012-86\"},\"id_presentacion\":{\"Registrado\":\"PRES-26281-00001-92\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 16:11:26', 1),
(3224, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00013-06\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"SUAVIZANTE GRANEL MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"3.50\"},\"stock_producto\":{\"Registrado\":\"20.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00029-94\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00013-06\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 16:59:51', 1),
(3225, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00014-30\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CERA GRANEL MARCA CLICK 1LT\"},\"precio_producto\":{\"Registrado\":\"2.80\"},\"stock_producto\":{\"Registrado\":\"20.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00030-99\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00014-30\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 17:01:19', 1),
(3226, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00015-57\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LIMPIA CERAMICA GRANEL MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"3.00\"},\"stock_producto\":{\"Registrado\":\"20.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00031-20\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00015-57\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 17:02:02', 1),
(3227, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00016-13\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"LIMPIA CERAMICA GRANEL 4LTS MARCA CLIC\"},\"precio_producto\":{\"Registrado\":\"11.20\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00032-49\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00016-13\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00033-19\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00016-13\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 17:03:13', 1),
(3228, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00017-25\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CERA GRANEL GALON 4LTS MARCA CLIC\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00034-12\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00017-25\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 17:04:00', 1),
(3229, 'V11429340', 4, 'Éxito', 'Registrar', '161.140.120.27', '{\"id_producto\":{\"Registrado\":\"PROD-26296-00018-84\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"SUAVIZANTE GRANEL GALON 4LTS MARCA CLIC\"},\"precio_producto\":{\"Registrado\":\"13.20\"},\"stock_producto\":{\"Registrado\":\"5.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26296-00035-78\"},\"id_producto\":{\"Registrado\":\"PROD-26296-00018-84\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-24 17:04:29', 1),
(3230, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.120.27', '{\"precio_producto\":{\"Modificado\":\"4.50\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00036-95\"}}],\"materias_primas\":[]}}', '2026-10-24 17:04:58', 1),
(3231, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.120.27', '{\"nombre_producto\":{\"Modificado\":\"LIMPIA POCETA\\/LIMPIA CERAMICA 1LT MARCA CLIC\"},\"precio_producto\":{\"Modificado\":\"4.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26296-00037-66\"},\"id_presentacion\":{\"Modificado\":\"PRES-26123-00001-28\"}}],\"materias_primas\":[]}}', '2026-10-24 17:06:06', 1),
(3232, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.44.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-26 15:49:24\"}}', '2026-10-26 15:44:24', 1),
(3234, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.44.23', '{\"nombre_producto\":{\"Modificado\":\"DESINFECTANTE MARCA CLIP 1LT\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26298-00001-73\"}}],\"materias_primas\":[]}}', '2026-10-26 15:45:25', 1),
(3236, 'V11429340', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"DAME PRESUPUESTO DE 5 PIPAS DE JABON\"},\"respuesta\":{\"Registrado\":\"¡Claro que sí! Con gusto le preparo el presupuesto para 5 pipas de jabón.\\n\\nAquí tiene el detalle:\\n\\n*   <b>5 x PIPAS DE JABON MULTIUSO<\\/b>:\\n    *   Precio por unidad: $260.00 USD (o 230,419.80 Bs al cambio oficial)\\n    *   Total: $1,300.00 USD (o 1,152,099.00 Bs al cambio oficial)\\n\\n**Total del Presupuesto:**\\n\\n*   <b>$1,300.00 USD<\\/b>\\n*   <b>1,152,099.00 Bs<\\/b> (al cambio oficial del día)\\n\\nSi desea realizar la compra o tiene alguna otra consulta, no dude en preguntar.\\n\\n[OFRECER_GUARDADO]\"}}', '2026-10-26 15:55:13', 1),
(3237, 'V11429340', 307, 'Éxito', 'consultar', '0.0.0.0', '{\"mensaje\":{\"Registrado\":\"DAME PRESUPUESTO DE 5 LITROS DE JABON MULTIUSO MARCA CLIC\"},\"respuesta\":{\"Registrado\":\"¡Con gusto le preparo su presupuesto!\\n\\nAquí tiene el detalle para 5 litros de Jabón Multiuso Marca Clic:\\n\\n*   <b>5 x JABON MULTIUSO MARCA CLIC 1LT (LITRO)<\\/b>:\\n    *   Precio por unidad: $2.50 USD (o 2,215.58 Bs al cambio oficial)\\n    *   Total: $12.50 USD (o 11,077.90 Bs al cambio oficial)\\n\\n**Total del Presupuesto:**\\n\\n*   <b>$12.50 USD<\\/b>\\n*   <b>11,077.90 Bs<\\/b> (al cambio oficial del día)\\n\\n¿Desea agregar algo más a su pedido o tiene alguna otra consulta?\\n\\n[OFRECER_GUARDADO]\"}}', '2026-10-26 15:57:29', 1),
(3238, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.79.12', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-26 17:35:25\"}}', '2026-10-26 17:30:25', 1),
(3240, 'V11429340', 4, 'Éxito', 'Actualizar', '161.140.79.12', '{\"nombre_producto\":{\"Modificado\":\"Cepillo de.barrer cerda gruesa.Marca Amazonas\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26298-00002-51\"}}],\"materias_primas\":[]}}', '2026-10-26 17:32:56', 1),
(3241, 'V11429340', 309, 'Éxito', 'registrar', '161.140.79.12', '{\"id_orden\":{\"Registrado\":\"OEP-26298-00001-48\"},\"cliente\":{\"Registrado\":\"Ana Gonzalez\"},\"rif\":{\"Registrado\":\"V17505531\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-26 17:44:56', 1),
(3242, 'V11429340', 309, 'Éxito', 'Actualizar OEP: OEP-26298-00001-48', '161.140.79.12', '{\"status\":{\"Modificado\":10}}', '2026-10-26 17:45:29', 1),
(3243, 'V11429340', 259, 'Éxito', 'Registrar Pago: PAG-26298-00001-60', '161.140.79.12', '{\"id_pago\":{\"Registrado\":\"PAG-26298-00001-60\"},\"id_orden\":{\"Registrado\":\"OEP-26298-00001-48\"}}', '2026-10-26 17:45:29', 1),
(3245, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.184.207', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-27 09:32:42\"}}', '2026-10-27 09:27:42', 1),
(3247, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.184.207', '{\"id_producto\":{\"Registrado\":\"PROD-26299-00001-54\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLORO MARCA CLIC 1LT\"},\"precio_producto\":{\"Registrado\":\"1.20\"},\"stock_producto\":{\"Registrado\":\"12.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26299-00001-29\"},\"id_producto\":{\"Registrado\":\"PROD-26299-00001-54\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-27 09:47:49', 1),
(3248, 'V11429340', 4, 'Éxito', 'Registrar', '186.165.184.207', '{\"id_producto\":{\"Registrado\":\"PROD-26299-00002-67\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CERA J.LACRUZ 1LT\"},\"precio_producto\":{\"Registrado\":\"3.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26299-00002-08\"},\"id_producto\":{\"Registrado\":\"PROD-26299-00002-67\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-27 09:53:15', 1),
(3249, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.184.207', '{\"nombre_producto\":{\"Modificado\":\"DESENGRASANTE J.LACRUZ\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26299-00003-63\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":263}}]}}', '2026-10-27 11:05:45', 1),
(3250, 'V11429340', 4, 'Éxito', 'Actualizar', '186.165.184.207', '{\"nombre_producto\":{\"Modificado\":\"JABON MULTIUSO 1LT J.LACRUZ\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26299-00004-61\"}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00005-22\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00006-89\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00007-69\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00008-95\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26296-00009-36\"},\"id_producto\":{\"Eliminado\":\"PROD-26260-00001-35\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":264}},{\"id_materia_prima_producto\":{\"Modificado\":265}},{\"id_materia_prima_producto\":{\"Modificado\":266}},{\"id_materia_prima_producto\":{\"Modificado\":267}},{\"id_materia_prima_producto\":{\"Modificado\":268}},{\"id_materia_prima_producto\":{\"Modificado\":269}}]}}', '2026-10-27 11:08:48', 1),
(3251, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.140.234', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-28 08:52:11\"}}', '2026-10-28 08:47:11', 1),
(3253, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.74.250.108', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-28 13:04:42\"}}', '2026-10-28 12:59:42', 1),
(3256, 'V30485684', 4, 'Éxito', 'Registrar', '38.74.250.108', '{\"id_producto\":{\"Registrado\":\"PROD-26300-00001-23\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"Producto de prueba Omar\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26300-00001-59\"},\"id_producto\":{\"Registrado\":\"PROD-26300-00001-23\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-10-28 13:13:51', 1),
(3257, 'V30485684', 309, 'Éxito', 'registrar', '38.74.250.108', '{\"id_orden\":{\"Registrado\":\"OEP-26300-00001-63\"},\"cliente\":{\"Registrado\":\"Carlos Hurtado\"},\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-10-28 13:14:38', 1),
(3258, 'V30485684', 309, 'Éxito', 'procesarPresupuesto', '38.74.250.108', '{\"status\":{\"Modificado\":1},\"productos\":{\"Registrado\":1}}', '2026-10-28 13:14:56', 1),
(3259, 'V30485684', 309, 'Éxito', 'anular', '38.74.250.108', '{\"status\":{\"Modificado\":2}}', '2026-10-28 13:15:00', 1),
(3260, 'V30485684', 4, 'Éxito', 'Eliminar', '38.74.250.108', NULL, '2026-10-28 13:15:23', 1),
(3261, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-28 15:08:27\"}}', '2026-10-28 15:03:27', 1),
(3263, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.74.250.108', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-28 15:09:19\"}}', '2026-10-28 15:04:19', 1),
(3265, 'V11429340', 4, 'Éxito', 'Actualizar', '38.172.138.66', '{\"nombre_producto\":{\"Modificado\":\"Haragan Limpia Vidrios Pequeño\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26300-00002-78\"}}],\"materias_primas\":[]}}', '2026-10-28 16:56:05', 1),
(3266, 'V11429340', 309, 'Éxito', 'registrar', '38.172.138.66', '{\"id_orden\":{\"Registrado\":\"OEP-26300-00002-61\"},\"cliente\":{\"Registrado\":\"Jorge Quintero\"},\"rif\":{\"Registrado\":\"V11429340\"},\"productos\":{\"Registrado\":2},\"servicios\":{\"Registrado\":0}}', '2026-10-28 17:08:09', 1),
(3267, 'V11429340', 309, 'Éxito', 'Actualizar OEP: OEP-26300-00002-61', '38.172.138.66', '{\"status\":{\"Modificado\":10}}', '2026-10-28 17:16:55', 1),
(3268, 'V11429340', 259, 'Éxito', 'Registrar Pago: PAG-26300-00001-99', '38.172.138.66', '{\"id_pago\":{\"Registrado\":\"PAG-26300-00001-99\"},\"id_orden\":{\"Registrado\":\"OEP-26300-00002-61\"}}', '2026-10-28 17:16:55', 1),
(3269, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-29 08:48:35\"}}', '2026-10-29 08:43:35', 1),
(3271, 'V11429340', 23, 'Éxito', 'actualizar', '38.172.138.66', '{\"valor_moneda\":{\"Modificado\":\"888.89\"}}', '2026-10-29 11:48:50', 1),
(3272, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-29 15:54:12\"}}', '2026-10-29 15:49:12', 1),
(3274, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '161.140.122.157', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-29 16:04:56\"}}', '2026-10-29 15:59:56', 1),
(3277, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-30 10:27:53\"}}', '2026-10-30 10:22:53', 1),
(3279, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '186.165.200.7', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-10-31 11:41:59\"}}', '2026-10-31 11:36:59', 1),
(3281, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.200.236.95', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-01 21:25:56\"}}', '2026-11-01 21:20:56', 1),
(3282, 'V30485684', 309, 'Éxito', 'registrar', '154.200.236.95', '{\"id_orden\":{\"Registrado\":\"OEP-26304-00001-88\"},\"cliente\":{\"Registrado\":\"Carlos Hurtado\"},\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-11-01 21:23:01', 1),
(3283, 'V30485684', 309, 'Éxito', 'Actualizar OEP: OEP-26304-00001-88', '154.200.236.95', '{\"status\":{\"Modificado\":10}}', '2026-11-01 21:24:39', 1),
(3284, 'V30485684', 259, 'Éxito', 'Registrar Pago: PAG-26304-00001-11', '154.200.236.95', '{\"id_pago\":{\"Registrado\":\"PAG-26304-00001-11\"},\"id_orden\":{\"Registrado\":\"OEP-26304-00001-88\"}}', '2026-11-01 21:24:39', 1),
(3285, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '38.74.250.137', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-02 08:45:26\"}}', '2026-11-02 08:40:26', 1),
(3287, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-02 16:02:04\"}}', '2026-11-02 15:57:05', 1),
(3289, 'V15265756', 9, 'Éxito', 'Iniciar sesión', '190.97.229.47', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-03 20:12:36\"}}', '2026-11-03 20:07:36', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(3292, 'V11429340', 9, 'Éxito', 'Iniciar sesión', '38.172.138.66', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-05 17:09:34\"}}', '2026-11-05 17:04:34', 1),
(3295, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.178.16', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-06 16:03:30\"}}', '2026-11-06 15:58:30', 1),
(3297, 'V30485684', 309, 'Éxito', 'registrar', '181.208.178.16', '{\"id_orden\":{\"Registrado\":\"OEP-26309-00001-70\"},\"cliente\":{\"Registrado\":\"Carlos Hurtado\"},\"rif\":{\"Registrado\":\"V31317406\"},\"productos\":{\"Registrado\":1},\"servicios\":{\"Registrado\":0}}', '2026-11-06 16:10:44', 1),
(3298, 'V30485684', 309, 'Éxito', 'procesarPresupuesto', '181.208.178.16', '{\"status\":{\"Modificado\":1},\"productos\":{\"Registrado\":1}}', '2026-11-06 16:11:22', 1),
(3299, 'V30485684', 309, 'Éxito', 'anular', '181.208.178.16', '{\"status\":{\"Modificado\":2}}', '2026-11-06 16:11:30', 1),
(3300, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.17', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-07 14:04:17\"}}', '2026-11-07 13:59:17', 1),
(3302, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.28', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 13:23:29\"}}', '2026-11-08 13:18:30', 1),
(3304, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.28', '{\"id_permiso\":{\"Registrado\":680},\"nombre_permiso\":{\"Registrado\":\"QA_PERMISO_D1D42B\"},\"status\":{\"Registrado\":1}}', '2026-11-08 13:18:35', 1),
(3305, 'V30485684', 28, 'Éxito', 'Actualizar con id 680', '154.40.142.28', '{\"nombre_permiso\":{\"Modificado\":\"QA_PERMISO_D1D42B_MOD\"}}', '2026-11-08 13:19:00', 1),
(3306, 'V30485684', 28, 'Éxito', 'Eliminar con id 680', '154.40.142.28', NULL, '2026-11-08 13:19:12', 1),
(3307, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.28', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 13:25:50\"}}', '2026-11-08 13:20:50', 1),
(3309, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.28', '{\"id_permiso\":{\"Registrado\":681},\"nombre_permiso\":{\"Registrado\":\"QA_PERMISO_2C2EE9\"},\"status\":{\"Registrado\":1}}', '2026-11-08 13:20:57', 1),
(3310, 'V30485684', 28, 'Éxito', 'Actualizar con id 681', '154.40.142.28', '{\"nombre_permiso\":{\"Modificado\":\"QA_PERMISO_2C2EE9_MOD\"}}', '2026-11-08 13:21:09', 1),
(3311, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.28', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 13:33:14\"}}', '2026-11-08 13:28:14', 1),
(3313, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.28', '{\"id_permiso\":{\"Registrado\":682},\"nombre_permiso\":{\"Registrado\":\"QA_PERMISO_395F4E\"},\"status\":{\"Registrado\":1}}', '2026-11-08 13:28:20', 1),
(3314, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.28', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 13:46:32\"}}', '2026-11-08 13:41:33', 1),
(3316, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.28', '{\"id_permiso\":{\"Registrado\":683},\"nombre_permiso\":{\"Registrado\":\"QA_PERMISO_238E44\"},\"status\":{\"Registrado\":1}}', '2026-11-08 13:41:42', 1),
(3317, 'V30485684', 28, 'Éxito', 'Actualizar con id 683', '154.40.142.28', '{\"nombre_permiso\":{\"Modificado\":\"QA_PERMISO_238E44_MOD\"}}', '2026-11-08 13:41:50', 1),
(3318, 'V30485684', 28, 'Éxito', 'Eliminar con id 683', '154.40.142.28', NULL, '2026-11-08 13:41:55', 1),
(3319, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.20', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 14:57:12\"}}', '2026-11-08 14:52:13', 1),
(3321, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.20', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:02:05\"}}', '2026-11-08 14:57:06', 1),
(3323, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.20', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:03:10\"}}', '2026-11-08 14:58:10', 1),
(3325, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:10:49\"}}', '2026-11-08 15:05:50', 1),
(3327, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:12:09\"}}', '2026-11-08 15:07:09', 1),
(3329, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.23', '{\"id_cambio_iva\":{\"Modificado\":259},\"monto_cambio_iva\":{\"Modificado\":\"25.00\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 15:07:13\"}}', '2026-11-08 15:07:13', 1),
(3330, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:17:39\"}}', '2026-11-08 15:12:39', 1),
(3332, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.23', '{\"id_cambio_iva\":{\"Modificado\":260},\"monto_cambio_iva\":{\"Modificado\":\"30.00\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 15:12:47\"}}', '2026-11-08 15:12:47', 1),
(3333, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:21:06\"}}', '2026-11-08 15:16:07', 1),
(3335, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.23', '{\"id_cambio_iva\":{\"Modificado\":261},\"monto_cambio_iva\":{\"Modificado\":\"21.97\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 15:16:10\"}}', '2026-11-08 15:16:10', 1),
(3336, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:25:11\"}}', '2026-11-08 15:20:11', 1),
(3338, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.23', '{\"id_cambio_iva\":{\"Modificado\":262},\"monto_cambio_iva\":{\"Modificado\":\"18.41\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 15:20:16\"}}', '2026-11-08 15:20:16', 1),
(3339, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:29:54\"}}', '2026-11-08 15:24:54', 1),
(3341, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.23', '{\"id_cambio_iva\":{\"Modificado\":263},\"monto_cambio_iva\":{\"Modificado\":\"15.42\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 15:24:58\"}}', '2026-11-08 15:24:58', 1),
(3342, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:32:29\"}}', '2026-11-08 15:27:29', 1),
(3344, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"53.00\"}}', '2026-11-08 15:27:36', 1),
(3345, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:41:24\"}}', '2026-11-08 15:36:25', 1),
(3347, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"61.36\"}}', '2026-11-08 15:36:31', 1),
(3348, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:43:54\"}}', '2026-11-08 15:38:54', 1),
(3350, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"83.00\"}}', '2026-11-08 15:38:58', 1),
(3351, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:45:06\"}}', '2026-11-08 15:40:06', 1),
(3353, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"68.00\"}}', '2026-11-08 15:40:19', 1),
(3354, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:48:11\"}}', '2026-11-08 15:43:12', 1),
(3356, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"98.00\"}}', '2026-11-08 15:43:19', 1),
(3357, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:49:21\"}}', '2026-11-08 15:44:21', 1),
(3359, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"69.00\"}}', '2026-11-08 15:44:26', 1),
(3360, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:51:21\"}}', '2026-11-08 15:46:21', 1),
(3362, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"51.00\"}}', '2026-11-08 15:46:26', 1),
(3363, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 15:52:34\"}}', '2026-11-08 15:47:35', 1),
(3365, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"75.00\"}}', '2026-11-08 15:47:41', 1),
(3366, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.23', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:00:53\"}}', '2026-11-08 15:55:54', 1),
(3368, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.23', '{\"valor_moneda\":{\"Modificado\":\"56.00\"}}', '2026-11-08 15:56:02', 1),
(3369, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:09:57\"}}', '2026-11-08 16:04:57', 1),
(3371, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.24', '{\"valor_moneda\":{\"Modificado\":\"95.00\"}}', '2026-11-08 16:05:05', 1),
(3372, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:12:09\"}}', '2026-11-08 16:07:11', 1),
(3374, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.24', '{\"valor_moneda\":{\"Modificado\":\"81.00\"}}', '2026-11-08 16:07:19', 1),
(3375, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:15:04\"}}', '2026-11-08 16:10:04', 1),
(3377, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:16:24\"}}', '2026-11-08 16:11:24', 1),
(3379, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:17:09\"}}', '2026-11-08 16:12:10', 1),
(3381, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00001-76\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:12:19\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"155.00\"}}]}', '2026-11-08 16:12:19', 1),
(3382, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:20:38\"}}', '2026-11-08 16:15:38', 1),
(3384, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00002-72\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:15:43\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"15.00\"}}]}', '2026-11-08 16:15:44', 1),
(3385, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:24:10\"}}', '2026-11-08 16:19:11', 1),
(3387, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:25:16\"}}', '2026-11-08 16:20:17', 1),
(3389, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:26:14\"}}', '2026-11-08 16:21:14', 1),
(3391, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00003-54\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:21:20\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"27.00\"}}]}', '2026-11-08 16:21:20', 1),
(3392, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:30:04\"}}', '2026-11-08 16:25:04', 1),
(3394, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:31:58\"}}', '2026-11-08 16:26:58', 1),
(3396, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00004-76\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:27:03\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"42.00\"}}]}', '2026-11-08 16:27:03', 1),
(3397, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:35:29\"}}', '2026-11-08 16:30:29', 1),
(3399, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00005-77\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:30:47\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"877.00\"}}]}', '2026-11-08 16:30:47', 1),
(3400, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 16:38:34\"}}', '2026-11-08 16:33:34', 1),
(3402, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00006-81\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 16:33:40\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"41.00\"}}]}', '2026-11-08 16:33:41', 1),
(3403, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:02:05\"}}', '2026-11-08 18:57:05', 1),
(3405, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00007-71\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 18:57:12\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"27.00\"}}]}', '2026-11-08 18:57:12', 1),
(3406, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:05:35\"}}', '2026-11-08 19:00:35', 1),
(3408, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.24', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00008-74\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 19:00:40\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"43.00\"}}]}', '2026-11-08 19:00:40', 1),
(3409, 'V30485684', 246, 'Éxito', 'Actualizar', '154.40.142.24', '{\"detalles\":[{\"cantidad_producida\":{\"Modificado\":\"90.00\"}}]}', '2026-11-08 19:00:51', 1),
(3410, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:13:49\"}}', '2026-11-08 19:08:49', 1),
(3412, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:16:42\"}}', '2026-11-08 19:11:42', 1),
(3414, 'V30485684', 260, 'Éxito', 'Actualizar con id 5', '154.40.142.29', '{\"fecha_ejecucion\":{\"Modificado\":\"0000-00-00 00:00:00\"}}', '2026-11-08 19:11:54', 1),
(3415, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:17:31\"}}', '2026-11-08 19:12:31', 1),
(3417, 'V30485684', 253, 'Éxito', 'Registrar Anomalia de Producto', '154.40.142.29', '{\"id_movimiento_anomalo_producto\":{\"Registrado\":57},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_movimiento\":{\"Registrado\":5},\"tipo_movimiento\":{\"Registrado\":1},\"motivo_movimiento\":{\"Registrado\":\"Prueba automatizada Codeception de reposición anóm\"},\"fecha_movimiento\":{\"Registrado\":\"2026-11-08 19:12:40\"},\"nombre_producto\":{\"Registrado\":\"CERA J.LACRUZ 1LT\"}}', '2026-11-08 19:12:41', 1),
(3418, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:19:28\"}}', '2026-11-08 19:14:29', 1),
(3420, 'V30485684', 253, 'Éxito', 'Registrar Anomalia de Producto', '154.40.142.29', '{\"id_movimiento_anomalo_producto\":{\"Registrado\":58},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_movimiento\":{\"Registrado\":5},\"tipo_movimiento\":{\"Registrado\":1},\"motivo_movimiento\":{\"Registrado\":\"Prueba automatizada Codeception de reposición anóm\"},\"fecha_movimiento\":{\"Registrado\":\"2026-11-08 19:14:46\"},\"nombre_producto\":{\"Registrado\":\"CERA J.LACRUZ 1LT\"}}', '2026-11-08 19:14:47', 1),
(3421, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:24:16\"}}', '2026-11-08 19:19:17', 1),
(3423, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:24:29\"}}', '2026-11-08 19:19:29', 1),
(3425, 'V30485684', 18, 'Éxito', 'registrar', '154.40.142.29', '{\"id_cambio_iva\":{\"Modificado\":264},\"monto_cambio_iva\":{\"Modificado\":\"26.80\"},\"fecha_cambio_iva\":{\"Modificado\":\"2026-11-08 19:19:34\"}}', '2026-11-08 19:19:34', 1),
(3426, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:24:42\"}}', '2026-11-08 19:19:42', 1),
(3428, 'V30485684', 23, 'Éxito', 'actualizar', '154.40.142.29', '{\"valor_moneda\":{\"Modificado\":\"74.00\"}}', '2026-11-08 19:19:47', 1),
(3429, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:24:53\"}}', '2026-11-08 19:19:53', 1),
(3431, 'V30485684', 253, 'Éxito', 'Registrar Anomalia de Producto', '154.40.142.29', '{\"id_movimiento_anomalo_producto\":{\"Registrado\":59},\"nombre_presentacion\":{\"Registrado\":\"LITRO\"},\"cantidad_movimiento\":{\"Registrado\":5},\"tipo_movimiento\":{\"Registrado\":1},\"motivo_movimiento\":{\"Registrado\":\"Prueba automatizada Codeception de reposición anóm\"},\"fecha_movimiento\":{\"Registrado\":\"2026-11-08 19:20:02\"},\"nombre_producto\":{\"Registrado\":\"CERA J.LACRUZ 1LT\"}}', '2026-11-08 19:20:02', 1),
(3432, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:25:08\"}}', '2026-11-08 19:20:08', 1),
(3434, 'V30485684', 260, 'Éxito', 'Actualizar con id 4', '154.40.142.29', '{\"fecha_ejecucion\":{\"Modificado\":\"0000-00-00 00:00:00\"}}', '2026-11-08 19:20:16', 1),
(3435, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:25:25\"}}', '2026-11-08 19:20:25', 1),
(3437, 'V30485684', 28, 'Éxito', 'registrar', '154.40.142.29', '{\"id_permiso\":{\"Registrado\":684},\"nombre_permiso\":{\"Registrado\":\"QA_PERMISO_A80B85\"},\"status\":{\"Registrado\":1}}', '2026-11-08 19:20:29', 1),
(3438, 'V30485684', 28, 'Éxito', 'Actualizar con id 684', '154.40.142.29', '{\"nombre_permiso\":{\"Modificado\":\"QA_PERMISO_A80B85_MOD\"}}', '2026-11-08 19:20:32', 1),
(3439, 'V30485684', 28, 'Éxito', 'Eliminar con id 684', '154.40.142.29', NULL, '2026-11-08 19:20:36', 1),
(3440, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:25:43\"}}', '2026-11-08 19:20:44', 1),
(3442, 'V30485684', 246, 'Éxito', 'Registrar', '154.40.142.29', '{\"id_produccion\":{\"Registrado\":\"PROD-26311-00009-05\"},\"fecha_produccion\":{\"Registrado\":\"2026-11-08 19:20:48\"},\"status\":{\"Registrado\":1},\"detalles\":[{\"id_producto\":{\"Registrado\":\"PROD-26260-00001-35\"},\"cantidad_producida\":{\"Registrado\":\"22.00\"}}]}', '2026-11-08 19:20:48', 1),
(3443, 'V30485684', 246, 'Éxito', 'Actualizar', '154.40.142.29', '{\"detalles\":[{\"cantidad_producida\":{\"Modificado\":\"78.00\"}}]}', '2026-11-08 19:20:58', 1),
(3444, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.29', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 19:26:04\"}}', '2026-11-08 19:21:04', 1),
(3446, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V72600792', '154.40.142.29', '{\"cedula_usuario\":{\"Registrado\":\"V72600792\"},\"nombre_usuario\":{\"Registrado\":\"PRUEBA\"},\"apellido_usuario\":{\"Registrado\":\"AUTOMATIZADA\"},\"correo_usuario\":{\"Registrado\":\"qa-604142@example.com\"},\"telefono_usuario\":{\"Registrado\":\"04245551607\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"qa60414212\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$U61x.siKLpw8LvBzs.HMpOU56wpXb.60sPfdgL.6Mchf5uQ5Z\\/D0a\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_11_08_19_21_17_76.png\"},\"direccion_usuario\":{\"Registrado\":\"Direccion de prueba automatizada\"}}', '2026-11-08 19:21:20', 1),
(3447, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:12:37\"}}', '2026-11-08 20:07:38', 1),
(3449, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:19:03\"}}', '2026-11-08 20:14:03', 1),
(3451, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:19:14\"}}', '2026-11-08 20:14:14', 1),
(3453, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:19:39\"}}', '2026-11-08 20:14:39', 1),
(3455, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:19:47\"}}', '2026-11-08 20:14:47', 1),
(3457, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:19:54\"}}', '2026-11-08 20:14:54', 1),
(3459, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:20:07\"}}', '2026-11-08 20:15:07', 1),
(3461, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:28:18\"}}', '2026-11-08 20:23:19', 1),
(3463, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:28:32\"}}', '2026-11-08 20:23:33', 1),
(3465, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:29:47\"}}', '2026-11-08 20:24:47', 1),
(3467, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:30:33\"}}', '2026-11-08 20:25:34', 1),
(3469, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:38:14\"}}', '2026-11-08 20:33:14', 1),
(3471, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:38:26\"}}', '2026-11-08 20:33:26', 1),
(3473, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:38:40\"}}', '2026-11-08 20:33:41', 1),
(3475, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:38:50\"}}', '2026-11-08 20:33:50', 1),
(3477, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:39:02\"}}', '2026-11-08 20:34:02', 1),
(3479, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:39:20\"}}', '2026-11-08 20:34:20', 1),
(3481, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:40:11\"}}', '2026-11-08 20:35:11', 1),
(3483, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:40:24\"}}', '2026-11-08 20:35:24', 1),
(3485, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:40:38\"}}', '2026-11-08 20:35:38', 1),
(3487, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:40:48\"}}', '2026-11-08 20:35:48', 1),
(3489, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:40:56\"}}', '2026-11-08 20:35:56', 1),
(3491, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '154.40.142.24', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-11-08 20:41:11\"}}', '2026-11-08 20:36:11', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iconos_notificaciones`
--

CREATE TABLE `iconos_notificaciones` (
  `id_icono_notificacion` int(11) NOT NULL,
  `path_icono_notificacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `iconos_notificaciones`
--

INSERT INTO `iconos_notificaciones` (`id_icono_notificacion`, `path_icono_notificacion`, `status`) VALUES
(1, 'info', 1),
(3, 'success', 1),
(4, 'warning', 1);

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
(3, 'facturacion', 1),
(4, 'productos', 1),
(5, 'servicios', 1),
(7, 'proveedores', 1),
(8, 'compras', 1),
(9, 'usuarios', 1),
(10, 'cambios', 1),
(11, 'reportes', 1),
(13, 'bitacora', 1),
(15, 'promociones', 1),
(16, 'imagenes', 1),
(17, 'roles', 1),
(18, 'cambiosIva', 1),
(19, 'metodos-pago', 1),
(21, 'presentaciones', 1),
(22, 'unidadesMedidas', 1),
(23, 'monedas', 1),
(24, 'materiasPrimas', 1),
(25, 'insumos', 1),
(28, 'permisos', 1),
(29, 'dashboard', 1),
(246, 'producciones', 1),
(247, 'rutas', 1),
(248, 'pedidos', 1),
(249, 'categoriasProductos', 1),
(250, 'empresasEnvios', 1),
(251, 'bancos', 1),
(252, 'repartidores', 1),
(253, 'inventario', 1),
(257, 'sucursalesEmpresasEnvios', 1),
(258, 'Materias Primas', 1),
(259, 'pagos', 1),
(260, 'ordenesServicios', 1),
(261, 'accesos', 1),
(262, 'modulos', 1),
(263, 'ventas', 0),
(264, 'Categorias de Productos', 1),
(266, 'facturas', 0),
(267, 'mensajesWS', 1),
(303, 'metodos-pagos', 1),
(305, 'presentaciones_productos', 1),
(306, 'mamm', 0),
(307, 'chatbot', 1),
(308, 'preguntas-seguridad', 1),
(309, 'ordenesEntregasPresupuestos', 1),
(311, 'reportesEstadisticos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_icono_notificacion` int(11) NOT NULL,
  `id_tipo_notificacion` int(11) NOT NULL,
  `tiempo_notificacion` int(11) NOT NULL,
  `titulo_notificacion` varchar(30) NOT NULL,
  `texto_notificacion` varchar(255) NOT NULL,
  `fecha_creacion_notificacion` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `cedula_usuario`, `id_icono_notificacion`, `id_tipo_notificacion`, `tiempo_notificacion`, `titulo_notificacion`, `texto_notificacion`, `fecha_creacion_notificacion`, `status`) VALUES
(398, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:49:57', 0),
(399, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:50:09', 0),
(400, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido registrado en el sistema.', '2026-08-08 13:50:22', 0),
(401, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido eliminado del sistema.', '2026-08-08 13:50:26', 0),
(402, 'V30485681', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', 1),
(403, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', 0),
(404, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-16 19:00:03', 0),
(405, 'V15265756', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-30 15:18:27', 1),
(406, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-30 15:18:27', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `status`) VALUES
(618, 'ver', 1),
(619, 'listar', 1),
(620, 'registrar', 1),
(621, 'actualizar', 1),
(622, 'eliminar', 1),
(623, 'ver dashboard', 1),
(624, 'ver historial de cambio', 1),
(625, 'actualizar cambio de divisas', 1),
(626, 'ver historial de cambio del iva', 1),
(627, 'actualizar cambio del iva', 1),
(628, 'ver detalles de las ventas', 1),
(629, 'ver ventas despachadas', 1),
(630, 'ver ventas sin cancelar', 1),
(631, 'ver pedidos en espera', 1),
(632, 'ver pedidos rechazados', 1),
(633, 'gestionar ventas', 1),
(634, 'ver reportes', 1),
(635, 'imprimir reportes de ventas', 1),
(636, 'imprimir reportes de productos', 1),
(637, 'imprimir comandas', 1),
(638, 'asignar roles a usuarios', 1),
(639, 'ver el precio del dólar', 1),
(640, 'ver notificaciones', 1),
(641, 'ver modal de ayuda', 1),
(642, 'ver carrito de compra', 1),
(643, 'ver inventario', 1),
(644, 'ver detalles de promociones', 1),
(645, 'ver bitácora', 1),
(646, 'PERMISOSN', 0),
(647, 'ver detalles de pedidos propios', 1),
(648, 'cambiar estado de los pedidos', 1),
(649, 'cancelar pedidos', 1),
(650, 'despachar pedidos', 1),
(651, 'ver pedidos propios', 1),
(652, 'ver pedidos de los clientes', 1),
(653, 'imprimir pedidos', 1),
(654, 'registrar cargas o descargas de productos', 1),
(655, 'ver historial de e/s de los productos', 1),
(656, 'registrar cargas o descargas de materias primas', 1),
(657, 'ver historial de e/s de las materias primas', 1),
(658, 'ver permisos generales', 1),
(659, 'ver permisos especiales', 1),
(660, 'actualizar permisos', 1),
(662, 'asignar repartidores a pedidos', 1),
(663, 'ver detalles de los productos', 1),
(664, 'ver historial de cambio de las divisas', 1),
(665, 'listar permisos', 1),
(666, 'ver chatbot', 1),
(667, 'enviar mensaje chatbot', 1),
(668, 'imprimir reportes de anomalias de productos', 1),
(669, 'imprimir reportes de anomalias de materias primas', 1),
(670, 'anular', 1),
(671, 'despachar orden', 1),
(672, 'agregar pago', 1),
(673, 'permisox', 0),
(674, 'ver reportes estadísticos', 1),
(675, 'agregar cliente', 1),
(676, 'ver detalles de los clientes', 1),
(677, 'ver detalles de los repartidores', 1),
(678, 'prueba de permiso', 0),
(679, 'imprimir', 1),
(680, 'QA_PERMISO_D1D42B_MOD', 0),
(681, 'QA_PERMISO_2C2EE9_MOD', 1),
(682, 'QA_PERMISO_395F4E', 1),
(683, 'QA_PERMISO_238E44_MOD', 0),
(684, 'QA_PERMISO_A80B85_MOD', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_seguridad`
--

CREATE TABLE `preguntas_seguridad` (
  `id_pregunta` varchar(20) NOT NULL,
  `texto_pregunta` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas_seguridad`
--

INSERT INTO `preguntas_seguridad` (`id_pregunta`, `texto_pregunta`, `status`) VALUES
('PREG-26210-00001-26', '¿Cuál es tu comida favorita?', 1),
('PREG-26210-00002-21', '¿En qué calle vivías cuando tenías diez años?', 1),
('PREG-26210-00003-77', '¿Cómo se llamaba tu primera mascota?', 1),
('PREG-26210-00004-61', '¿Cuál es el segundo nombre de tu abuelo materno?', 1),
('PREG-26210-00005-63', '¿Cómo se llamaba tu primer maestro de escuela?', 1),
('PREG-26210-00006-60', '¿Cuál era tu apodo favorito durante la infancia?', 1),
('PREG-26210-00007-27', '¿Cuál fue el primer modelo de auto que tuviste?', 1),
('PREG-26210-00008-61', '¿A qué ciudad viajaste en tu primer vuelo en avión?', 1),
('PREG-26210-00009-75', '¿Cuál es el título de tu libro favorito de la infancia?', 1),
('PREG-26210-00010-46', '¿Cuál fue el primer concierto de música al que asististe?', 1),
('PREG-26210-00011-43', '¿En qué hotel te hospedaste durante tus vacaciones favoritas?', 1),
('PREG-26210-00012-08', '¿Cómo se llamaba la primera empresa donde trabajaste?', 1),
('PREG-26210-00013-26', '¿Cuál era el nombre de la mascota de tu universidad?', 1),
('PREG-26210-00014-96', '¿En qué ciudad se conocieron tus padres?', 1),
('PREG-26210-00015-02', '¿Cuál fue el primer país extranjero que visitaste?', 1),
('PREG-26210-00016-12', '¿Cómo se llamaba el hospital donde naciste?', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_seguridad_usuarios`
--

CREATE TABLE `preguntas_seguridad_usuarios` (
  `id_pregunta_usuario` varchar(20) NOT NULL,
  `id_pregunta` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `respuesta_pregunta` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas_seguridad_usuarios`
--

INSERT INTO `preguntas_seguridad_usuarios` (`id_pregunta_usuario`, `id_pregunta`, `cedula_usuario`, `respuesta_pregunta`, `status`) VALUES
('PRUS-26213-00001-85', 'PREG-26210-00001-26', 'V30485684', '$2y$10$oSAFQLHBQ.f0yXrP2EHvYOWmXbFQEK6ddsUSES5kZuRfvHTGrvkLO', 1),
('PRUS-26213-00002-33', 'PREG-26210-00003-77', 'V30485684', '$2y$10$ESrPpMPFFnpSTAKCOiAqVOApkQ7In.nwsj4H8oVj1nk5GTb96i7OW', 1),
('PRUS-26213-00003-13', 'PREG-26210-00015-02', 'V30485684', '$2y$10$CJ4N3mC.78fqwCK4eOvaFOK5MqaS5cIeAldIZ4N8NHnnwOrZGunW6', 1),
('PRUS-26213-00004-32', 'PREG-26210-00013-26', 'V30485684', '$2y$10$AAs.exVU1/hT.kK/2wYnRumBj0/81k6la0doYE7P81IEKCENLZ2Yi', 1),
('PRUS-26213-00005-91', 'PREG-26210-00012-08', 'V30485684', '$2y$10$nEuTtqbC4VHg/Fq7MjHDJuo8pAjGVNj.MQ1/DvKaH5lVu6FeminVe', 1),
('PRUS-26213-00006-15', 'PREG-26210-00010-46', 'V30485684', '$2y$10$.pudhcTVmpJqP56lu1guxOCQpi8czdVNALQv04oklr6X3byw/GHly', 1),
('PRUS-26221-00001-41', 'PREG-26210-00001-26', 'V30485688', '$2y$10$kkIvKOeLqnsJjPepiPbPaObftz5zu.Utme0AAci7k9jqbCwNdxb7.', 0),
('PRUS-26221-00002-20', 'PREG-26210-00002-21', 'V30485688', '$2y$10$WXVuBpVC/aD2eJjIjiGOzu5rMVQU9i.LDG77RAib4sAlNRE5hLgDG', 0),
('PRUS-26221-00003-40', 'PREG-26210-00003-77', 'V30485688', '$2y$10$xplVAHINlxboKlbtghJQFOGG/93Wa0ZmgDSIXQe7ymr.mRtfNYl8i', 0),
('PRUS-26221-00004-31', 'PREG-26210-00015-02', 'V30485688', '$2y$10$97U9ZxMj0mPK/rtsBgTM.e6A33jx6DFV./yd6R5aHTD9pVEpI4CEa', 0),
('PRUS-26221-00005-05', 'PREG-26210-00014-96', 'V30485688', '$2y$10$.vWhvgbmkDn0wSvhLpu5p.YRkVpg.Qy1r5sEV4REGFFmhSaPvJJoe', 0),
('PRUS-26221-00006-70', 'PREG-26210-00012-08', 'V30485688', '$2y$10$MSyyqq8/wDJedr3JCR/GeuXLtDX2fE4z/XR1LEAV4rWAdodyVmRYG', 0),
('PRUS-26221-00007-22', 'PREG-26210-00001-26', 'V30485681', '$2y$10$wU8cYibX8NLN27pihZMW4Obl1MtwCq60TaylCpisI7k3graGFoaHu', 0),
('PRUS-26221-00008-99', 'PREG-26210-00002-21', 'V30485681', '$2y$10$oCY9ov16IiozchUkb/jkhOZnuIwVC4HRPtkcjLoGoBQxkUDJW2auy', 0),
('PRUS-26221-00009-61', 'PREG-26210-00003-77', 'V30485681', '$2y$10$EJGejQQ/q8/bHep25huPnO9AKIXVy9q0Pkt/WV/rah7C4rBlUwKoq', 0),
('PRUS-26221-00010-42', 'PREG-26210-00014-96', 'V30485681', '$2y$10$F0WYELHGHtHBGMWGEJvE.ehIVVz5VMtDaa1x38KCl6Eh.Yx84sf1C', 0),
('PRUS-26221-00011-68', 'PREG-26210-00012-08', 'V30485681', '$2y$10$b3u2pHbQ24nWvTq8Tg8DXep2j6CQYU1ICmfpfrMmJXVP80Z2u/r7i', 0),
('PRUS-26221-00012-42', 'PREG-26210-00011-43', 'V30485681', '$2y$10$caw/tqyKBEMy7cZcRrMcMePBXzmAwEgzkbctT.h3Ax9ilGYtgfdv6', 0),
('PRUS-26227-00001-58', 'PREG-26210-00001-26', 'V30485631', '$2y$10$XtK3dfbu27H6smxaIwNBDuC/W3J5esegFOxzGYqmlYZUxMFOIF2qO', 0),
('PRUS-26227-00002-60', 'PREG-26210-00002-21', 'V30485631', '$2y$10$.Pi5SWuInsDZ/QE2EyQRGOfecbw5Rt2nOcMuKd4D4bPf/QkkP.bTC', 0),
('PRUS-26227-00003-26', 'PREG-26210-00003-77', 'V30485631', '$2y$10$AyfEGBCe5hHBQU/ZRmy9..7kMSEcOG1C2bX1Z/QVHhqPFJx6r6j0.', 0),
('PRUS-26227-00004-88', 'PREG-26210-00004-61', 'V30485631', '$2y$10$twjRhlLjk2E138yreZfCY.HfGtcd6YwS1OlrvG5.enL69ltKE1S5K', 0),
('PRUS-26227-00005-55', 'PREG-26210-00005-63', 'V30485631', '$2y$10$kuqj.R/XY26DnbiVRS7BXe9dVd2waEePUzG8EXNNnSXQogsJIk7ne', 0),
('PRUS-26227-00006-84', 'PREG-26210-00006-60', 'V30485631', '$2y$10$gvzXH7dcsGLwu2mlW2ILm.W0zAXRJKJgHelsEIyS23o.wmIK9aFfa', 0),
('PRUS-26227-00007-21', 'PREG-26210-00001-26', 'V30485688', '$2y$10$3gcuhRdZD7BKvZZDgFvKXOGwzupUMpukIjUFl/jbohDXTxXBhlSBa', 0),
('PRUS-26227-00008-47', 'PREG-26210-00003-77', 'V30485688', '$2y$10$h7IURdkgkEwdeHo5k7CAm.r.I69Uc6vL1/k3P1S5coycthy0E40Q.', 0),
('PRUS-26227-00009-82', 'PREG-26210-00002-21', 'V30485688', '$2y$10$MDb9SYu2UCMArqfd5KrEfu.PcIApHsjvAGJ72uAF7ragC0RUF7p4i', 0),
('PRUS-26227-00010-26', 'PREG-26210-00014-96', 'V30485688', '$2y$10$7jHbnoRdBc5eZB0qMCQWAecGqkflPj1BWblxToi9W4KwFvxPUeyTC', 0),
('PRUS-26227-00011-13', 'PREG-26210-00015-02', 'V30485688', '$2y$10$ca9XzzUtY.A5pOjuEIN8O.P5NXZ18ugezQA1IISYT01Fymg/LrC0W', 0),
('PRUS-26227-00012-54', 'PREG-26210-00012-08', 'V30485688', '$2y$10$S1Du8BywViYS1DV21mBfBu0v7nGbGGJwHe2CVMGoE8I9ThQNfAZqm', 0),
('PRUS-26240-00001-78', 'PREG-26210-00001-26', 'V15265756', '$2y$10$1z2V4rIg4irEUy.gdaqGG.1eHygmjI0k3Xb9ClgF6hThXl/M2oZWu', 1),
('PRUS-26240-00002-13', 'PREG-26210-00004-61', 'V15265756', '$2y$10$Jwzt.9u6hPgBiXDl/WzbaOsqkuz/SnANWvUwUetBDAm.T5OvxTt2K', 1),
('PRUS-26240-00003-64', 'PREG-26210-00007-27', 'V15265756', '$2y$10$bk01kHNqSvaYMjXqG5yaj.jdTUVbe0PxKLeQPfe0ixDgLBlCNl1Zi', 1),
('PRUS-26240-00004-68', 'PREG-26210-00014-96', 'V15265756', '$2y$10$yw74rejL1PM1Z2boG2YP/.VzBtsZ9eU2w43A5fOlFSbGNNUq9w2Zq', 1),
('PRUS-26240-00005-93', 'PREG-26210-00008-61', 'V15265756', '$2y$10$tMMI2PMYGTnPAw6/yeBxQ.7UxhUB9fWC44jVZnaBsWFW4HLe/Z87y', 1),
('PRUS-26240-00006-21', 'PREG-26210-00012-08', 'V15265756', '$2y$10$4eDWY.RFib6WdTx..5c1y.ijPYg01.Tb8x9Q0wK8ThnkjYqwkv4Ri', 1),
('PRUS-26241-00001-07', 'PREG-26210-00001-26', 'V29910249', '$2y$10$biZku818KJE9OQIxPku/6OB.ES/sF9IuONth05xgQCXF/4SCXwusa', 1),
('PRUS-26241-00002-20', 'PREG-26210-00016-12', 'V29910249', '$2y$10$3TuyqwB2J3.oTfc0pvNY4eaAXJPLEtZePkdZP2HkweLmnU4ACiYEa', 1),
('PRUS-26241-00003-95', 'PREG-26210-00003-77', 'V29910249', '$2y$10$Y9/uvXBt32WvQ8VU3yWrHeV3DT8n963qO8YbL4/SUBpqFnvCOeAwu', 1),
('PRUS-26241-00004-03', 'PREG-26210-00006-60', 'V29910249', '$2y$10$1TKKmGeuOvqW6FIjeYlCH.aCDG7vT8ARfAbqJey6KxLac6nURhtl6', 1),
('PRUS-26241-00005-83', 'PREG-26210-00004-61', 'V29910249', '$2y$10$fxGlgbf1VBzLdML1AZRb/uSfMrhe7HEfIqGxNpdWNjBCsYaIYvS2C', 1),
('PRUS-26241-00006-02', 'PREG-26210-00009-75', 'V29910249', '$2y$10$xMcPTOqAH4FXj9Pb55MGoueXk5bRo01/m4o7fQZptziRzXyuZEvNu', 1),
('PRUS-26241-00007-00', 'PREG-26210-00003-77', 'V11429340', '$2y$10$ixcevkJ/ETywhr/ei2sSYeiGcOc5QsF.an/HdiaXdmqF09qXoKM9O', 0),
('PRUS-26241-00008-68', 'PREG-26210-00007-27', 'V11429340', '$2y$10$EcGR7AbkY8h2RnhbkSOHKO287E//bDFtZFmmT/lynA2DKfazoeYbO', 0),
('PRUS-26241-00009-97', 'PREG-26210-00002-21', 'V11429340', '$2y$10$WH9SfAXSErHnrx66lMtrruT/blaytZKYeG5RY4NYr1GrUs2pAJit2', 0),
('PRUS-26241-00010-03', 'PREG-26210-00005-63', 'V11429340', '$2y$10$zF/K0BTIbNJmrYMJMV4vKutmvhW7bb6yNic0oHCqQOBHAPT36PJKG', 0),
('PRUS-26241-00011-83', 'PREG-26210-00012-08', 'V11429340', '$2y$10$.vsK8d7lDaDb8nuPO80Gv.Movy3A6MmA59DCH6CCGIU9gxx5sSCAy', 0),
('PRUS-26241-00012-51', 'PREG-26210-00014-96', 'V11429340', '$2y$10$kzvmQIQxjX.YlhJelVKSZOkVaMpONrdjuR3zaa2yxqsAYtjBLJFyy', 0),
('PRUS-26241-00013-26', 'PREG-26210-00003-77', 'V11429340', '$2y$10$cniY7/4Armvw2lMjryu7RutqwA05gFMTbATV5lSSIuHEP2y2iBMLi', 1),
('PRUS-26241-00014-35', 'PREG-26210-00007-27', 'V11429340', '$2y$10$K4BID8NtaNMUi9jglq9jpejOb65/88RoMmk1VY.ujmQvMUrdyyHci', 1),
('PRUS-26241-00015-33', 'PREG-26210-00002-21', 'V11429340', '$2y$10$AsBaPei8.rVlv2J8NWvzzOa/8OJlIcW2b7mW.9l2R8UCWRku0S5TO', 1),
('PRUS-26241-00016-05', 'PREG-26210-00005-63', 'V11429340', '$2y$10$aEKdzPDIWvfWvbgC/NbocO2n4quAzYg3.XWMzxIwosE.gvrHACbcW', 1),
('PRUS-26241-00017-72', 'PREG-26210-00012-08', 'V11429340', '$2y$10$vaDmHipKAkc4wb0R3ImOLOyv89HCOfhASN9AFyOe1FKM2xD7foHQi', 1),
('PRUS-26241-00018-18', 'PREG-26210-00014-96', 'V11429340', '$2y$10$kHWR91ZDKdcCnZ1SaRJn1.a7EwCBLJcRg6kmwXe5xnGitC.TeJVuO', 1),
('PRUS-26248-00001-03', 'PREG-26210-00001-26', 'V15265757', '$2y$10$mlpz2ot1CpOD8duET8ApSuucnLfox.QY5CQbQ45KR.Pjl4x29rzui', 1),
('PRUS-26248-00002-00', 'PREG-26210-00014-96', 'V15265757', '$2y$10$qYvk.JvlsJepEW/3tPkaoe5HHR8r5w/dDr5Jum/5uVa9dmX079WYu', 1),
('PRUS-26248-00003-44', 'PREG-26210-00007-27', 'V15265757', '$2y$10$3pN4QwW7XUVApFzdyGBT0.XoNWwt5jLSmZVr2ZS82jkUYX.yegN9K', 1),
('PRUS-26248-00004-12', 'PREG-26210-00008-61', 'V15265757', '$2y$10$k/y2mL0hGpwKToz0nsJ3/OmeRc9/.lyZIRPCQALHN87z4Sw5AdLAy', 1),
('PRUS-26248-00005-56', 'PREG-26210-00012-08', 'V15265757', '$2y$10$09NWGdSoDgnIMrCXX6cicO7cym3jHGmml7.lN.BvFNyy.CAAp3Yh6', 1),
('PRUS-26248-00006-46', 'PREG-26210-00004-61', 'V15265757', '$2y$10$2DJMkqWLWPJgPuxAq9HZ5u3x34zmOofUY.xL0a3NSq/mwmy4wmP/S', 1),
('PRUS-26258-00001-83', 'PREG-26210-00001-26', 'V31317406', '$2y$10$7kPH8hYmwNa9jL9BdeK9T.PTSPMlmL0vEhLW7G6xy26vcGRYfgEdi', 1),
('PRUS-26258-00002-09', 'PREG-26210-00003-77', 'V31317406', '$2y$10$bnmoT/WYZy.MrY3YMC4DyuWRU6u/uMzEfdq7Vnx/i5xrYShbnRPH6', 1),
('PRUS-26258-00003-28', 'PREG-26210-00013-26', 'V31317406', '$2y$10$mp8V442e.CYTMtmyY3xyi./QkmKYJWdSFdvjMuhCx.aOxA66wyZna', 1),
('PRUS-26258-00004-57', 'PREG-26210-00008-61', 'V31317406', '$2y$10$.0TgxYf9x2uCS/0RWq5ur.kOfFyvVHQzVu8EuZ5XPEUFBEOQgeZR6', 1),
('PRUS-26258-00005-28', 'PREG-26210-00006-60', 'V31317406', '$2y$10$etfSUWIgQPjphB6/Dx8Yf.t1uit.rY.Cc96OFM4EAUAB56/XJmTCm', 1),
('PRUS-26258-00006-44', 'PREG-26210-00009-75', 'V31317406', '$2y$10$/0r5glD01E0RNrIPo706rOydM78P.3GnlX8b98EOu6/Ae/npoc9IO', 1),
('PRUS-26260-00001-80', 'PREG-26210-00001-26', 'V12345678', '$2y$10$eebRGyMMtaVHrBXQi.GqLuUqJjgUHQVcq2LG9gQtvcSykH2IGFTAG', 1),
('PRUS-26260-00002-66', 'PREG-26210-00016-12', 'V12345678', '$2y$10$CvOXMw7QEnTtbqZK6GDZderZ0kaIkBh2Q0gEMdNwMNu7aBFHIWPQK', 1),
('PRUS-26260-00003-44', 'PREG-26210-00012-08', 'V12345678', '$2y$10$HT2pd31UZjcYeRRWtP7ek.HhhOLnlPj51WZLdo8hUxfW9ezQDfE.K', 1),
('PRUS-26260-00004-65', 'PREG-26210-00013-26', 'V12345678', '$2y$10$g3Z1KTtaJR6FQC8gPgpsGur81eLmfhpWaro4/hqFdq/U4Xb6ssoBO', 1),
('PRUS-26260-00005-95', 'PREG-26210-00010-46', 'V12345678', '$2y$10$R1htsqmzWOO/KKAtrzxrjOtC.GHNj7TvYxP7mNhSGK6GRf7kWpCmy', 1),
('PRUS-26260-00006-90', 'PREG-26210-00009-75', 'V12345678', '$2y$10$ta/gY4EEJDXcBKSChkVp4e8KOAwLA/yTKOAL4MHERYxFjhGzx.bgq', 1),
('PRUS-26311-00001-17', 'PREG-26210-00001-26', 'V72600792', '$2y$10$jlFZs0uOS6yUSkE4/oYR0O7UnKVk4LhR5UhSb5SoGhsGKBO9lyUnO', 1),
('PRUS-26311-00002-10', 'PREG-26210-00002-21', 'V72600792', '$2y$10$gulPGpUww4wjFBHgv6CdROAaYDiKGnI.KC8ZkW5U5.SpljTDeKG/O', 1),
('PRUS-26311-00003-46', 'PREG-26210-00003-77', 'V72600792', '$2y$10$K1il20Qyrmh9Hkg3vDjBb.1WQO.16EgPUrPjAhDbFObDdLfiYSDdC', 1),
('PRUS-26311-00004-55', 'PREG-26210-00004-61', 'V72600792', '$2y$10$JuvIxti23KimllIXPdjzT.33.DtEzMFMm0CzENycS3hEvD7zYtmju', 1),
('PRUS-26311-00005-25', 'PREG-26210-00005-63', 'V72600792', '$2y$10$rTTc7SB14LF2O53yakafcutoCCmMPTu/IDfQsgrPrjgMoOi8mV71C', 1),
('PRUS-26311-00006-43', 'PREG-26210-00006-60', 'V72600792', '$2y$10$3fgMIj3iMSRzc3Ew.Eqs9.czyUyzA6cc4EzPLBuUwBbBmFif3NggS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prompts_usuarios`
--

CREATE TABLE `prompts_usuarios` (
  `id_prompt_usuario` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `prompt` text NOT NULL,
  `respuesta_bot` text NOT NULL,
  `fecha_prompt` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prompts_usuarios`
--

INSERT INTO `prompts_usuarios` (`id_prompt_usuario`, `cedula_usuario`, `prompt`, `respuesta_bot`, `fecha_prompt`, `status`) VALUES
(2, 'V30485684', 'quiero u¿fumigar mi casa y tambien me gustaria 2 litros de cloro', '¡Claro que sí! Con gusto te preparo tu presupuesto.\r\n\r\nAquí tienes el detalle de tu pedido:\r\n\r\n*   <bFUMIGACION</b>: 1 servicio. Costo: 17.762,58 Bs (Ref: $22.00 USD)\r\n*   <bCLORO (LITRO)</b>: 2 unidades. Costo por unidad: 807,39 Bs (Ref: $1.00 USD). Total: 1.614,78 Bs (Ref: $2.00 USD)\r\n\r\n---\r\n\r\n<bTotal General</b>: 19.377,36 Bs (Ref: $24.00 USD)\r\n\r\n¿Deseas realizar alguna modificación o agregar algo más a tu presupuesto?', '2026-09-16 23:04:44', 1),
(3, 'V30485684', 'quiero uqe me hagas un presupuesto para 100 listros de brisol', '¡Claro que sí! Con gusto te preparo el presupuesto para 100 litros de Brisol.\r\n\r\nAquí tienes el detalle:\r\n\r\n*   <bBRISOL (LITRO)</b>: 100 unidades.\r\n    *   Costo por unidad: 1.211,09 Bs (Ref: $1.50 USD)\r\n    *   Costo total: 121.109,00 Bs (Ref: $150.00 USD)\r\n\r\n<br><br>\r\n\r\n<bTotal General</b>: 121.109,00 Bs (Ref: $150.00 USD)\r\n\r\n¿Deseas realizar alguna modificación o agregar algo más a tu presupuesto?', '2026-09-18 09:29:12', 1);

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
(1, 'SUPER ADMINISTRADOR', 1),
(2, 'ADMINISTRADOR', 1),
(3, 'CLIENTE', 1),
(35, 'Cajera', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_notificaciones`
--

CREATE TABLE `tipos_notificaciones` (
  `id_tipo_notificacion` int(11) NOT NULL,
  `nombre_tipo_notificacion` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_notificaciones`
--

INSERT INTO `tipos_notificaciones` (`id_tipo_notificacion`, `nombre_tipo_notificacion`, `status`) VALUES
(1, 'info', 1),
(2, 'simple', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_usuarios`
--

CREATE TABLE `tokens_usuarios` (
  `id_token_usuario` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `tipo_token` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `vencimiento_token` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_usuarios`
--

INSERT INTO `tokens_usuarios` (`id_token_usuario`, `cedula_usuario`, `tipo_token`, `token`, `vencimiento_token`, `status`) VALUES
('TOKU-26241-00002-62', 'V11429340', 1, 'eKYF0G7Mo8AMBwtcmobr+g==', '2026-08-30 21:07:13', 1),
('TOKU-26285-00001-89', 'V11429340', 2, '1DSGWT', '2026-10-13 08:43:02', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_usuario` varchar(20) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `usuario_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `telefono_usuario` varchar(11) NOT NULL,
  `correo_usuario` varchar(150) NOT NULL,
  `foto_usuario` varchar(255) NOT NULL,
  `direccion_usuario` varchar(255) NOT NULL,
  `ultimo_acceso_usuario` datetime NOT NULL DEFAULT current_timestamp(),
  `intentos_inicio_sesion_fallidos_usuario` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `id_rol`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `contrasena_usuario`, `telefono_usuario`, `correo_usuario`, `foto_usuario`, `direccion_usuario`, `ultimo_acceso_usuario`, `intentos_inicio_sesion_fallidos_usuario`, `status`) VALUES
('V11429340', 1, 'Jorge', 'Quintero', 'quinterojor72', '$2y$12$acNufX1S.WP6jv9VWVHVZeD4ewXs/ilbBdfoKOh5SHf71lNTel.me', '04141590801', 'quinterojor72@gmail.com', 'usuarios_2026_08_30_21_27_21_39.jpg', 'Cabudare', '2026-11-05 17:09:34', 0, 1),
('V12345678', 3, 'Nose', 'Quiensabe', 'Ander12345', '$2y$10$yD3QBVpFS1KAZHMI/OkcmeKSgpXZzHCoH7sAUwwOlnPVDc8kf9.Q6', '04160000000', 'anderson@gmail.com', '', 'SANARE', '2026-09-18 10:42:20', 0, 1),
('V15265756', 1, 'Jangely', 'Lacruz', 'jangely23', '$2y$10$aIuVo59BETuuJt.0yPHkzeO2vwKAyBEZVM2ZOEuH8j2snfrTA6eIG', '04267503595', 'jangelylacruz1@gmail.com', 'usuarios_2026_08_29_15_24_46_57.jpg', 'Barrio Jose Gregorio Hernandez', '2026-11-03 20:12:36', 0, 1),
('V15265757', 3, 'Jangely', 'Lacruz', 'jangely25', '$2y$10$Etf1BEQCvlkxfAk0PrRxGeHxSJtHFghi8hF8VcMaOJMEVLtP7o2zC', '04267503595', 'jangelylacruz@gmail.com', '', 'Jose Gregorio Hernandez', '2026-09-12 10:28:03', 0, 1),
('V29910249', 3, 'Yeison', 'Carreño', 'Yeisonyjcd24', '$2y$10$k1rSGhazChgs3oYEVO8yh.W7pMhG/wYlqomikoGLNXuj5JCHD4a6C', '04145641142', 'yeisoncarre.o01@gmail.com', '', 'carorita arriba', '2026-09-04 09:43:49', 0, 1),
('V30485631', 3, 'Ander', 'Mendoza', 'Ander1234', '$2y$10$q1kepCAa6lqPBN76ncHveuNSFzsU8azGLPCUADq2h13tB2hUahpMG', '04160484640', 'andersonfreitez63@gmail.com', 'usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47', 'El Molino', '2026-08-16 17:55:27', 0, 0),
('V30485681', 1, 'Anderson', 'Freitez', 'Ander1236', '$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk/y0bWnjNde', '04169484643', 'andersonfreitez66@gmail.com', 'usuarios_2026_08_10_16_58_55_14.jpg', 'BARQUISIMETO', '2026-08-11 04:58:55', 0, 0),
('V30485684', 1, 'Anderson', 'Freitez', 'Ander123', '$2y$10$GclIuFtWodZio618mmWCJuXOr0LYxmLAjyaR7s3TA8vKUZ8Jczuxm', '04169484649', 'andersonfreitez6@gmail.com', '', 'SANARE', '2026-11-08 20:41:11', 0, 1),
('V30485688', 2, 'Anderson', 'Freitez', 'Ander12399', '$2y$10$OFi7gH2tMZled2NsBfYyI.NWAYlnlg3CUuQN/mEoH3phwkhCk5jDO', '04169484640', 'andersonfreitenz6@gmail.com', 'usuarios_2026_08_16_19_51_07_2.png', 'BARQUISIMETO', '2026-08-11 04:45:15', 0, 0),
('V31317406', 3, 'Carlos', 'Hurtado', 'calo3015', '$2y$10$RxYgWBCpbnuqHCQbxm.neu51evJf0.g9b63xrdvfC7vpv3y8HGzCi', '04160208555', 'carloshurtado30e15@gmail.com', '', 'valles de uribana', '2026-09-16 10:07:19', 0, 1),
('V72600792', 1, 'PRUEBA', 'AUTOMATIZADA', 'qa60414212', '$2y$10$U61x.siKLpw8LvBzs.HMpOU56wpXb.60sPfdgL.6Mchf5uQ5Z/D0a', '04245551607', 'qa-604142@example.com', 'usuarios_2026_11_08_19_21_17_76.png', 'Direccion de prueba automatizada', '2026-11-08 19:21:17', 0, 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_usuarios_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_usuarios_todos` (
`direccion_usuario` varchar(255)
,`cedula_usuario` varchar(20)
,`id_rol` int(11)
,`nombre_rol` varchar(50)
,`nombre_usuario` varchar(50)
,`apellido_usuario` varchar(50)
,`telefono_usuario` varchar(11)
,`correo_usuario` varchar(150)
,`usuario_usuario` varchar(50)
,`foto_usuario` varchar(255)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_usuarios_todos`
--
DROP TABLE IF EXISTS `v_usuarios_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_usuarios_todos`  AS SELECT `u`.`direccion_usuario` AS `direccion_usuario`, `u`.`cedula_usuario` AS `cedula_usuario`, `u`.`id_rol` AS `id_rol`, `ro`.`nombre_rol` AS `nombre_rol`, `u`.`nombre_usuario` AS `nombre_usuario`, `u`.`apellido_usuario` AS `apellido_usuario`, `u`.`telefono_usuario` AS `telefono_usuario`, `u`.`correo_usuario` AS `correo_usuario`, `u`.`usuario_usuario` AS `usuario_usuario`, `u`.`foto_usuario` AS `foto_usuario`, `u`.`status` AS `status` FROM (`usuarios` `u` join `roles` `ro` on(`ro`.`id_rol` = `u`.`id_rol`)) WHERE `u`.`cedula_usuario` <> 30485684 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `id_rol_accesos_fk` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_permiso_accesos_fk` (`id_permiso`);

--
-- Indices de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD PRIMARY KEY (`id_accion_resagada_usuario`),
  ADD KEY `id_modulo_acciones_resagadas_usuarios_fk` (`id_modulo`),
  ADD KEY `cedula_usuario_acciones_resagadas_usuarios_fk` (`cedula_usuario`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `cedula_usuario_bitacora_fk` (`cedula_usuario`),
  ADD KEY `id_modulo_bitacora_fk` (`id_modulo`);

--
-- Indices de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  ADD PRIMARY KEY (`id_icono_notificacion`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`),
  ADD KEY `nombre_modulo_indice` (`nombre_modulo`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `cedula_usuario_notificaciones_fk` (`cedula_usuario`),
  ADD KEY `icono_notificacion_notificaciones_fk` (`id_icono_notificacion`),
  ADD KEY `id_tipo_notificacion_notificaciones_fk` (`id_tipo_notificacion`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `nombre_permiso_indice` (`nombre_permiso`);

--
-- Indices de la tabla `preguntas_seguridad`
--
ALTER TABLE `preguntas_seguridad`
  ADD PRIMARY KEY (`id_pregunta`);

--
-- Indices de la tabla `preguntas_seguridad_usuarios`
--
ALTER TABLE `preguntas_seguridad_usuarios`
  ADD PRIMARY KEY (`id_pregunta_usuario`),
  ADD KEY `id_pregunta_preguntas_seguridad_usuarios_fk` (`id_pregunta`),
  ADD KEY `cedula_usuario_preguntas_seguridad_usuarios_fk` (`cedula_usuario`);

--
-- Indices de la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  ADD PRIMARY KEY (`id_prompt_usuario`),
  ADD KEY `cedula_usuario_prompts_usuarios_fk` (`cedula_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD KEY `nombre_rol_indice` (`nombre_rol`) USING BTREE;

--
-- Indices de la tabla `tipos_notificaciones`
--
ALTER TABLE `tipos_notificaciones`
  ADD PRIMARY KEY (`id_tipo_notificacion`);

--
-- Indices de la tabla `tokens_usuarios`
--
ALTER TABLE `tokens_usuarios`
  ADD PRIMARY KEY (`id_token_usuario`),
  ADD KEY `cedula_usuario_tokens_usuarios` (`cedula_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `id_rol_usuarios_fk` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1367;

--
-- AUTO_INCREMENT de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  MODIFY `id_accion_resagada_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1527;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3493;

--
-- AUTO_INCREMENT de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  MODIFY `id_icono_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=407;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=685;

--
-- AUTO_INCREMENT de la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  MODIFY `id_prompt_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `tipos_notificaciones`
--
ALTER TABLE `tipos_notificaciones`
  MODIFY `id_tipo_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `id_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_permiso_accesos_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_rol_accesos_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD CONSTRAINT `cedula_usuario_acciones_resagadas_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_acciones_resagadas_usuarios_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `cedula_usuario_bitacora_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_bitacora_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `cedula_usuario_notificaciones_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `icono_notificacion_notificaciones_fk` FOREIGN KEY (`id_icono_notificacion`) REFERENCES `iconos_notificaciones` (`id_icono_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_tipo_notificacion_notificaciones_fk` FOREIGN KEY (`id_tipo_notificacion`) REFERENCES `tipos_notificaciones` (`id_tipo_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `preguntas_seguridad_usuarios`
--
ALTER TABLE `preguntas_seguridad_usuarios`
  ADD CONSTRAINT `cedula_usuario_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pregunta_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_seguridad` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  ADD CONSTRAINT `cedula_usuario_prompts_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tokens_usuarios`
--
ALTER TABLE `tokens_usuarios`
  ADD CONSTRAINT `cedula_usuario_tokens_usuarios` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuarios_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
