-- ============================================
-- Backup: proyecto_lacruz + proyecto_lacruz_seguridad
-- Fecha: 2026-09-18 12:58:33
-- ============================================

SET FOREIGN_KEY_CHECKS=0;


-- ============================================
-- BASE DE DATOS: proyecto_lacruz_seguridad
-- ============================================

CREATE DATABASE IF NOT EXISTS `proyecto_lacruz_seguridad`;
USE `proyecto_lacruz_seguridad`;

-- Tabla: accesos
DROP TABLE IF EXISTS `accesos`;
CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_acceso`),
  KEY `id_rol_accesos_fk` (`id_rol`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_permiso_accesos_fk` (`id_permiso`),
  CONSTRAINT `id_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_permiso_accesos_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_rol_accesos_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1357 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `accesos` WRITE;
INSERT INTO `accesos` VALUES ('813', '1', '251', '618', '1');
INSERT INTO `accesos` VALUES ('814', '1', '251', '619', '1');
INSERT INTO `accesos` VALUES ('815', '1', '251', '620', '1');
INSERT INTO `accesos` VALUES ('816', '1', '251', '621', '1');
INSERT INTO `accesos` VALUES ('817', '1', '251', '622', '1');
INSERT INTO `accesos` VALUES ('818', '1', '18', '622', '1');
INSERT INTO `accesos` VALUES ('819', '1', '18', '621', '1');
INSERT INTO `accesos` VALUES ('820', '1', '18', '620', '1');
INSERT INTO `accesos` VALUES ('821', '1', '18', '619', '1');
INSERT INTO `accesos` VALUES ('822', '1', '18', '618', '1');
INSERT INTO `accesos` VALUES ('823', '1', '249', '618', '1');
INSERT INTO `accesos` VALUES ('824', '1', '249', '619', '1');
INSERT INTO `accesos` VALUES ('825', '1', '249', '620', '1');
INSERT INTO `accesos` VALUES ('826', '1', '249', '621', '1');
INSERT INTO `accesos` VALUES ('827', '1', '249', '622', '1');
INSERT INTO `accesos` VALUES ('828', '1', '1', '621', '1');
INSERT INTO `accesos` VALUES ('829', '1', '1', '622', '1');
INSERT INTO `accesos` VALUES ('830', '1', '1', '620', '1');
INSERT INTO `accesos` VALUES ('831', '1', '1', '619', '1');
INSERT INTO `accesos` VALUES ('832', '1', '1', '618', '1');
INSERT INTO `accesos` VALUES ('833', '1', '8', '618', '1');
INSERT INTO `accesos` VALUES ('834', '1', '8', '619', '1');
INSERT INTO `accesos` VALUES ('835', '1', '8', '620', '1');
INSERT INTO `accesos` VALUES ('836', '1', '8', '621', '1');
INSERT INTO `accesos` VALUES ('837', '1', '8', '622', '1');
INSERT INTO `accesos` VALUES ('838', '1', '250', '622', '1');
INSERT INTO `accesos` VALUES ('839', '1', '250', '621', '1');
INSERT INTO `accesos` VALUES ('840', '1', '250', '620', '1');
INSERT INTO `accesos` VALUES ('841', '1', '250', '619', '1');
INSERT INTO `accesos` VALUES ('842', '1', '250', '618', '1');
INSERT INTO `accesos` VALUES ('843', '1', '25', '618', '1');
INSERT INTO `accesos` VALUES ('844', '1', '25', '619', '1');
INSERT INTO `accesos` VALUES ('845', '1', '25', '620', '1');
INSERT INTO `accesos` VALUES ('846', '1', '25', '621', '1');
INSERT INTO `accesos` VALUES ('847', '1', '25', '622', '1');
INSERT INTO `accesos` VALUES ('848', '1', '24', '622', '1');
INSERT INTO `accesos` VALUES ('849', '1', '24', '621', '1');
INSERT INTO `accesos` VALUES ('850', '1', '24', '619', '1');
INSERT INTO `accesos` VALUES ('851', '1', '24', '620', '1');
INSERT INTO `accesos` VALUES ('852', '1', '24', '618', '1');
INSERT INTO `accesos` VALUES ('853', '1', '19', '618', '1');
INSERT INTO `accesos` VALUES ('854', '1', '23', '618', '1');
INSERT INTO `accesos` VALUES ('855', '1', '23', '619', '1');
INSERT INTO `accesos` VALUES ('856', '1', '19', '619', '1');
INSERT INTO `accesos` VALUES ('857', '1', '19', '620', '1');
INSERT INTO `accesos` VALUES ('858', '1', '23', '620', '1');
INSERT INTO `accesos` VALUES ('859', '1', '23', '621', '1');
INSERT INTO `accesos` VALUES ('860', '1', '19', '621', '1');
INSERT INTO `accesos` VALUES ('861', '1', '19', '622', '1');
INSERT INTO `accesos` VALUES ('862', '1', '23', '622', '1');
INSERT INTO `accesos` VALUES ('863', '1', '248', '618', '1');
INSERT INTO `accesos` VALUES ('864', '1', '248', '619', '1');
INSERT INTO `accesos` VALUES ('865', '1', '248', '620', '1');
INSERT INTO `accesos` VALUES ('866', '1', '248', '621', '1');
INSERT INTO `accesos` VALUES ('867', '1', '248', '622', '1');
INSERT INTO `accesos` VALUES ('868', '1', '28', '622', '1');
INSERT INTO `accesos` VALUES ('869', '1', '28', '621', '1');
INSERT INTO `accesos` VALUES ('870', '1', '28', '620', '1');
INSERT INTO `accesos` VALUES ('871', '1', '28', '619', '1');
INSERT INTO `accesos` VALUES ('872', '1', '28', '618', '1');
INSERT INTO `accesos` VALUES ('873', '1', '21', '618', '1');
INSERT INTO `accesos` VALUES ('874', '1', '21', '619', '1');
INSERT INTO `accesos` VALUES ('875', '1', '21', '620', '1');
INSERT INTO `accesos` VALUES ('876', '1', '21', '621', '1');
INSERT INTO `accesos` VALUES ('877', '1', '21', '622', '1');
INSERT INTO `accesos` VALUES ('878', '1', '246', '622', '1');
INSERT INTO `accesos` VALUES ('879', '1', '246', '621', '1');
INSERT INTO `accesos` VALUES ('880', '1', '246', '620', '1');
INSERT INTO `accesos` VALUES ('881', '1', '246', '619', '1');
INSERT INTO `accesos` VALUES ('882', '1', '246', '618', '1');
INSERT INTO `accesos` VALUES ('883', '1', '4', '618', '1');
INSERT INTO `accesos` VALUES ('884', '1', '4', '619', '1');
INSERT INTO `accesos` VALUES ('885', '1', '4', '620', '1');
INSERT INTO `accesos` VALUES ('886', '1', '4', '621', '1');
INSERT INTO `accesos` VALUES ('887', '1', '4', '622', '1');
INSERT INTO `accesos` VALUES ('888', '1', '15', '622', '1');
INSERT INTO `accesos` VALUES ('889', '1', '15', '621', '1');
INSERT INTO `accesos` VALUES ('890', '1', '15', '620', '1');
INSERT INTO `accesos` VALUES ('891', '1', '15', '619', '1');
INSERT INTO `accesos` VALUES ('892', '1', '15', '618', '1');
INSERT INTO `accesos` VALUES ('893', '1', '7', '618', '1');
INSERT INTO `accesos` VALUES ('894', '1', '7', '619', '1');
INSERT INTO `accesos` VALUES ('895', '1', '7', '620', '1');
INSERT INTO `accesos` VALUES ('896', '1', '7', '621', '1');
INSERT INTO `accesos` VALUES ('897', '1', '7', '622', '1');
INSERT INTO `accesos` VALUES ('898', '1', '252', '618', '1');
INSERT INTO `accesos` VALUES ('899', '1', '17', '618', '1');
INSERT INTO `accesos` VALUES ('900', '1', '247', '618', '1');
INSERT INTO `accesos` VALUES ('901', '1', '247', '619', '1');
INSERT INTO `accesos` VALUES ('902', '1', '17', '619', '1');
INSERT INTO `accesos` VALUES ('903', '1', '252', '619', '1');
INSERT INTO `accesos` VALUES ('904', '1', '252', '620', '1');
INSERT INTO `accesos` VALUES ('905', '1', '17', '620', '1');
INSERT INTO `accesos` VALUES ('906', '1', '247', '620', '1');
INSERT INTO `accesos` VALUES ('907', '1', '247', '621', '1');
INSERT INTO `accesos` VALUES ('908', '1', '17', '621', '1');
INSERT INTO `accesos` VALUES ('909', '1', '252', '621', '1');
INSERT INTO `accesos` VALUES ('910', '1', '252', '622', '1');
INSERT INTO `accesos` VALUES ('911', '1', '17', '622', '1');
INSERT INTO `accesos` VALUES ('912', '1', '247', '622', '1');
INSERT INTO `accesos` VALUES ('913', '1', '5', '618', '1');
INSERT INTO `accesos` VALUES ('914', '1', '22', '618', '1');
INSERT INTO `accesos` VALUES ('915', '1', '9', '618', '1');
INSERT INTO `accesos` VALUES ('916', '1', '3', '618', '1');
INSERT INTO `accesos` VALUES ('917', '1', '3', '619', '1');
INSERT INTO `accesos` VALUES ('918', '1', '9', '619', '1');
INSERT INTO `accesos` VALUES ('919', '1', '22', '619', '1');
INSERT INTO `accesos` VALUES ('920', '1', '5', '619', '1');
INSERT INTO `accesos` VALUES ('921', '1', '5', '620', '1');
INSERT INTO `accesos` VALUES ('922', '1', '22', '620', '1');
INSERT INTO `accesos` VALUES ('923', '1', '9', '620', '1');
INSERT INTO `accesos` VALUES ('924', '1', '3', '620', '1');
INSERT INTO `accesos` VALUES ('925', '1', '3', '621', '1');
INSERT INTO `accesos` VALUES ('926', '1', '9', '621', '1');
INSERT INTO `accesos` VALUES ('927', '1', '22', '621', '1');
INSERT INTO `accesos` VALUES ('928', '1', '5', '621', '1');
INSERT INTO `accesos` VALUES ('929', '1', '5', '622', '1');
INSERT INTO `accesos` VALUES ('930', '1', '22', '622', '1');
INSERT INTO `accesos` VALUES ('931', '1', '9', '622', '1');
INSERT INTO `accesos` VALUES ('932', '1', '3', '622', '1');
INSERT INTO `accesos` VALUES ('933', '1', '257', '618', '1');
INSERT INTO `accesos` VALUES ('934', '1', '257', '619', '1');
INSERT INTO `accesos` VALUES ('935', '1', '257', '620', '1');
INSERT INTO `accesos` VALUES ('936', '1', '257', '621', '1');
INSERT INTO `accesos` VALUES ('937', '1', '257', '622', '1');
INSERT INTO `accesos` VALUES ('938', '1', '258', '618', '1');
INSERT INTO `accesos` VALUES ('939', '1', '258', '619', '1');
INSERT INTO `accesos` VALUES ('940', '1', '258', '620', '1');
INSERT INTO `accesos` VALUES ('941', '1', '258', '621', '1');
INSERT INTO `accesos` VALUES ('942', '1', '258', '622', '1');
INSERT INTO `accesos` VALUES ('943', '1', '29', '623', '1');
INSERT INTO `accesos` VALUES ('944', '1', '10', '625', '1');
INSERT INTO `accesos` VALUES ('945', '1', '18', '627', '1');
INSERT INTO `accesos` VALUES ('946', '1', '3', '629', '1');
INSERT INTO `accesos` VALUES ('947', '1', '3', '631', '1');
INSERT INTO `accesos` VALUES ('948', '1', '3', '633', '1');
INSERT INTO `accesos` VALUES ('949', '1', '11', '635', '1');
INSERT INTO `accesos` VALUES ('950', '1', '11', '637', '1');
INSERT INTO `accesos` VALUES ('951', '1', '9', '639', '1');
INSERT INTO `accesos` VALUES ('952', '1', '9', '641', '1');
INSERT INTO `accesos` VALUES ('953', '1', '253', '643', '1');
INSERT INTO `accesos` VALUES ('954', '1', '13', '645', '1');
INSERT INTO `accesos` VALUES ('955', '1', '15', '644', '1');
INSERT INTO `accesos` VALUES ('956', '1', '9', '642', '1');
INSERT INTO `accesos` VALUES ('957', '1', '9', '640', '1');
INSERT INTO `accesos` VALUES ('958', '1', '9', '638', '1');
INSERT INTO `accesos` VALUES ('959', '1', '11', '636', '1');
INSERT INTO `accesos` VALUES ('960', '1', '11', '634', '1');
INSERT INTO `accesos` VALUES ('961', '1', '3', '632', '1');
INSERT INTO `accesos` VALUES ('962', '1', '3', '630', '1');
INSERT INTO `accesos` VALUES ('963', '1', '3', '628', '1');
INSERT INTO `accesos` VALUES ('964', '1', '18', '626', '1');
INSERT INTO `accesos` VALUES ('965', '1', '10', '624', '1');
INSERT INTO `accesos` VALUES ('966', '1', '259', '618', '1');
INSERT INTO `accesos` VALUES ('967', '1', '260', '618', '1');
INSERT INTO `accesos` VALUES ('968', '1', '260', '619', '1');
INSERT INTO `accesos` VALUES ('969', '1', '259', '619', '1');
INSERT INTO `accesos` VALUES ('970', '1', '259', '620', '1');
INSERT INTO `accesos` VALUES ('971', '1', '260', '620', '1');
INSERT INTO `accesos` VALUES ('972', '1', '260', '621', '1');
INSERT INTO `accesos` VALUES ('973', '1', '259', '621', '1');
INSERT INTO `accesos` VALUES ('974', '1', '259', '622', '1');
INSERT INTO `accesos` VALUES ('975', '1', '260', '622', '1');
INSERT INTO `accesos` VALUES ('976', '1', '261', '618', '1');
INSERT INTO `accesos` VALUES ('977', '1', '261', '619', '1');
INSERT INTO `accesos` VALUES ('978', '1', '261', '620', '1');
INSERT INTO `accesos` VALUES ('979', '1', '261', '621', '1');
INSERT INTO `accesos` VALUES ('980', '1', '261', '622', '1');
INSERT INTO `accesos` VALUES ('981', '1', '262', '618', '1');
INSERT INTO `accesos` VALUES ('982', '1', '262', '619', '1');
INSERT INTO `accesos` VALUES ('983', '1', '262', '620', '1');
INSERT INTO `accesos` VALUES ('984', '1', '262', '621', '1');
INSERT INTO `accesos` VALUES ('985', '1', '262', '622', '1');
INSERT INTO `accesos` VALUES ('986', '1', '29', '618', '1');
INSERT INTO `accesos` VALUES ('987', '1', '29', '619', '1');
INSERT INTO `accesos` VALUES ('988', '1', '29', '621', '1');
INSERT INTO `accesos` VALUES ('989', '1', '29', '620', '1');
INSERT INTO `accesos` VALUES ('990', '1', '29', '622', '1');
INSERT INTO `accesos` VALUES ('991', '1', '248', '647', '1');
INSERT INTO `accesos` VALUES ('992', '1', '248', '648', '1');
INSERT INTO `accesos` VALUES ('993', '1', '248', '649', '1');
INSERT INTO `accesos` VALUES ('994', '1', '263', '618', '1');
INSERT INTO `accesos` VALUES ('995', '1', '263', '619', '1');
INSERT INTO `accesos` VALUES ('996', '1', '263', '620', '1');
INSERT INTO `accesos` VALUES ('997', '1', '263', '621', '1');
INSERT INTO `accesos` VALUES ('998', '1', '263', '622', '1');
INSERT INTO `accesos` VALUES ('999', '1', '248', '650', '1');
INSERT INTO `accesos` VALUES ('1000', '1', '264', '618', '1');
INSERT INTO `accesos` VALUES ('1001', '1', '264', '619', '1');
INSERT INTO `accesos` VALUES ('1002', '1', '264', '620', '1');
INSERT INTO `accesos` VALUES ('1003', '1', '264', '621', '1');
INSERT INTO `accesos` VALUES ('1004', '1', '264', '622', '1');
INSERT INTO `accesos` VALUES ('1005', '1', '248', '651', '1');
INSERT INTO `accesos` VALUES ('1006', '1', '248', '652', '1');
INSERT INTO `accesos` VALUES ('1007', '2', '18', '618', '1');
INSERT INTO `accesos` VALUES ('1008', '3', '18', '618', '0');
INSERT INTO `accesos` VALUES ('1009', '3', '9', '618', '0');
INSERT INTO `accesos` VALUES ('1010', '3', '8', '618', '0');
INSERT INTO `accesos` VALUES ('1011', '3', '1', '618', '0');
INSERT INTO `accesos` VALUES ('1012', '3', '249', '618', '0');
INSERT INTO `accesos` VALUES ('1013', '3', '264', '618', '0');
INSERT INTO `accesos` VALUES ('1014', '3', '251', '618', '0');
INSERT INTO `accesos` VALUES ('1015', '3', '261', '618', '0');
INSERT INTO `accesos` VALUES ('1016', '3', '261', '619', '1');
INSERT INTO `accesos` VALUES ('1017', '3', '251', '619', '1');
INSERT INTO `accesos` VALUES ('1018', '3', '18', '619', '1');
INSERT INTO `accesos` VALUES ('1019', '3', '264', '619', '1');
INSERT INTO `accesos` VALUES ('1020', '3', '249', '619', '1');
INSERT INTO `accesos` VALUES ('1021', '3', '1', '619', '1');
INSERT INTO `accesos` VALUES ('1022', '3', '8', '619', '1');
INSERT INTO `accesos` VALUES ('1023', '3', '29', '618', '0');
INSERT INTO `accesos` VALUES ('1024', '3', '250', '618', '0');
INSERT INTO `accesos` VALUES ('1025', '3', '250', '619', '1');
INSERT INTO `accesos` VALUES ('1026', '1', '248', '653', '1');
INSERT INTO `accesos` VALUES ('1027', '3', '9', '622', '1');
INSERT INTO `accesos` VALUES ('1028', '1', '267', '618', '1');
INSERT INTO `accesos` VALUES ('1029', '1', '267', '619', '1');
INSERT INTO `accesos` VALUES ('1030', '1', '267', '620', '1');
INSERT INTO `accesos` VALUES ('1031', '1', '267', '621', '1');
INSERT INTO `accesos` VALUES ('1032', '1', '303', '618', '1');
INSERT INTO `accesos` VALUES ('1033', '1', '266', '619', '1');
INSERT INTO `accesos` VALUES ('1034', '1', '266', '618', '1');
INSERT INTO `accesos` VALUES ('1035', '1', '266', '620', '1');
INSERT INTO `accesos` VALUES ('1036', '1', '266', '621', '1');
INSERT INTO `accesos` VALUES ('1037', '1', '253', '655', '1');
INSERT INTO `accesos` VALUES ('1038', '1', '253', '657', '1');
INSERT INTO `accesos` VALUES ('1039', '1', '253', '656', '1');
INSERT INTO `accesos` VALUES ('1040', '1', '253', '654', '1');
INSERT INTO `accesos` VALUES ('1041', '1', '261', '658', '1');
INSERT INTO `accesos` VALUES ('1042', '1', '261', '659', '1');
INSERT INTO `accesos` VALUES ('1043', '1', '261', '660', '1');
INSERT INTO `accesos` VALUES ('1047', '1', '248', '662', '1');
INSERT INTO `accesos` VALUES ('1048', '1', '4', '663', '1');
INSERT INTO `accesos` VALUES ('1049', '1', '23', '664', '1');
INSERT INTO `accesos` VALUES ('1050', '1', '261', '665', '1');
INSERT INTO `accesos` VALUES ('1051', '1', '308', '618', '1');
INSERT INTO `accesos` VALUES ('1052', '1', '308', '620', '1');
INSERT INTO `accesos` VALUES ('1053', '1', '308', '619', '1');
INSERT INTO `accesos` VALUES ('1054', '1', '308', '621', '1');
INSERT INTO `accesos` VALUES ('1055', '1', '308', '622', '1');
INSERT INTO `accesos` VALUES ('1061', '1', '307', '618', '1');
INSERT INTO `accesos` VALUES ('1062', '1', '307', '619', '1');
INSERT INTO `accesos` VALUES ('1063', '1', '307', '620', '1');
INSERT INTO `accesos` VALUES ('1064', '1', '307', '621', '1');
INSERT INTO `accesos` VALUES ('1065', '1', '307', '622', '1');
INSERT INTO `accesos` VALUES ('1066', '1', '303', '619', '1');
INSERT INTO `accesos` VALUES ('1067', '1', '303', '620', '1');
INSERT INTO `accesos` VALUES ('1068', '1', '303', '621', '1');
INSERT INTO `accesos` VALUES ('1069', '1', '303', '622', '1');
INSERT INTO `accesos` VALUES ('1070', '1', '307', '666', '1');
INSERT INTO `accesos` VALUES ('1071', '1', '307', '667', '1');
INSERT INTO `accesos` VALUES ('1072', '1', '309', '618', '1');
INSERT INTO `accesos` VALUES ('1073', '1', '309', '619', '1');
INSERT INTO `accesos` VALUES ('1074', '1', '309', '670', '1');
INSERT INTO `accesos` VALUES ('1075', '1', '309', '620', '1');
INSERT INTO `accesos` VALUES ('1076', '1', '309', '621', '1');
INSERT INTO `accesos` VALUES ('1077', '1', '309', '622', '1');
INSERT INTO `accesos` VALUES ('1078', '1', '309', '671', '1');
INSERT INTO `accesos` VALUES ('1079', '1', '309', '675', '1');
INSERT INTO `accesos` VALUES ('1080', '1', '309', '672', '1');
INSERT INTO `accesos` VALUES ('1081', '1', '305', '618', '1');
INSERT INTO `accesos` VALUES ('1082', '1', '305', '619', '1');
INSERT INTO `accesos` VALUES ('1083', '1', '305', '620', '1');
INSERT INTO `accesos` VALUES ('1084', '1', '305', '621', '1');
INSERT INTO `accesos` VALUES ('1085', '1', '305', '622', '1');
INSERT INTO `accesos` VALUES ('1086', '1', '311', '620', '1');
INSERT INTO `accesos` VALUES ('1087', '1', '311', '619', '1');
INSERT INTO `accesos` VALUES ('1088', '1', '311', '618', '1');
INSERT INTO `accesos` VALUES ('1089', '1', '311', '621', '1');
INSERT INTO `accesos` VALUES ('1090', '1', '311', '622', '1');
INSERT INTO `accesos` VALUES ('1091', '1', '311', '674', '1');
INSERT INTO `accesos` VALUES ('1092', '1', '1', '676', '1');
INSERT INTO `accesos` VALUES ('1093', '1', '252', '677', '1');
INSERT INTO `accesos` VALUES ('1094', '2', '261', '665', '1');
INSERT INTO `accesos` VALUES ('1095', '2', '251', '619', '1');
INSERT INTO `accesos` VALUES ('1096', '2', '251', '620', '1');
INSERT INTO `accesos` VALUES ('1097', '2', '251', '621', '1');
INSERT INTO `accesos` VALUES ('1098', '2', '251', '622', '1');
INSERT INTO `accesos` VALUES ('1099', '2', '10', '624', '1');
INSERT INTO `accesos` VALUES ('1100', '2', '10', '625', '1');
INSERT INTO `accesos` VALUES ('1101', '2', '18', '619', '1');
INSERT INTO `accesos` VALUES ('1102', '2', '18', '620', '1');
INSERT INTO `accesos` VALUES ('1103', '2', '18', '626', '1');
INSERT INTO `accesos` VALUES ('1104', '2', '18', '621', '1');
INSERT INTO `accesos` VALUES ('1105', '2', '18', '622', '1');
INSERT INTO `accesos` VALUES ('1106', '2', '18', '627', '1');
INSERT INTO `accesos` VALUES ('1107', '2', '264', '618', '1');
INSERT INTO `accesos` VALUES ('1108', '2', '264', '619', '1');
INSERT INTO `accesos` VALUES ('1109', '2', '264', '620', '1');
INSERT INTO `accesos` VALUES ('1110', '2', '264', '621', '1');
INSERT INTO `accesos` VALUES ('1111', '2', '264', '622', '1');
INSERT INTO `accesos` VALUES ('1112', '2', '249', '618', '1');
INSERT INTO `accesos` VALUES ('1113', '2', '249', '619', '1');
INSERT INTO `accesos` VALUES ('1114', '2', '249', '620', '1');
INSERT INTO `accesos` VALUES ('1115', '2', '249', '621', '1');
INSERT INTO `accesos` VALUES ('1116', '2', '249', '622', '1');
INSERT INTO `accesos` VALUES ('1117', '2', '307', '618', '1');
INSERT INTO `accesos` VALUES ('1118', '2', '307', '619', '1');
INSERT INTO `accesos` VALUES ('1119', '2', '307', '620', '1');
INSERT INTO `accesos` VALUES ('1120', '2', '307', '621', '1');
INSERT INTO `accesos` VALUES ('1121', '2', '307', '622', '1');
INSERT INTO `accesos` VALUES ('1122', '2', '307', '667', '1');
INSERT INTO `accesos` VALUES ('1123', '2', '307', '666', '1');
INSERT INTO `accesos` VALUES ('1124', '2', '1', '618', '1');
INSERT INTO `accesos` VALUES ('1125', '2', '1', '619', '1');
INSERT INTO `accesos` VALUES ('1126', '2', '1', '620', '1');
INSERT INTO `accesos` VALUES ('1127', '2', '1', '621', '1');
INSERT INTO `accesos` VALUES ('1128', '2', '1', '622', '1');
INSERT INTO `accesos` VALUES ('1129', '2', '1', '676', '1');
INSERT INTO `accesos` VALUES ('1130', '2', '8', '618', '1');
INSERT INTO `accesos` VALUES ('1131', '2', '8', '619', '1');
INSERT INTO `accesos` VALUES ('1132', '2', '8', '620', '1');
INSERT INTO `accesos` VALUES ('1133', '2', '8', '621', '1');
INSERT INTO `accesos` VALUES ('1134', '2', '8', '622', '1');
INSERT INTO `accesos` VALUES ('1135', '2', '29', '618', '1');
INSERT INTO `accesos` VALUES ('1136', '2', '29', '619', '1');
INSERT INTO `accesos` VALUES ('1137', '2', '29', '620', '1');
INSERT INTO `accesos` VALUES ('1138', '2', '29', '621', '1');
INSERT INTO `accesos` VALUES ('1139', '2', '29', '622', '1');
INSERT INTO `accesos` VALUES ('1140', '2', '29', '623', '1');
INSERT INTO `accesos` VALUES ('1141', '2', '250', '618', '1');
INSERT INTO `accesos` VALUES ('1142', '2', '250', '619', '1');
INSERT INTO `accesos` VALUES ('1143', '2', '250', '620', '1');
INSERT INTO `accesos` VALUES ('1144', '2', '250', '621', '1');
INSERT INTO `accesos` VALUES ('1145', '2', '250', '622', '1');
INSERT INTO `accesos` VALUES ('1146', '2', '3', '618', '1');
INSERT INTO `accesos` VALUES ('1147', '2', '3', '619', '1');
INSERT INTO `accesos` VALUES ('1148', '2', '3', '620', '1');
INSERT INTO `accesos` VALUES ('1149', '2', '3', '621', '1');
INSERT INTO `accesos` VALUES ('1150', '2', '3', '622', '1');
INSERT INTO `accesos` VALUES ('1151', '2', '266', '618', '1');
INSERT INTO `accesos` VALUES ('1152', '2', '266', '619', '1');
INSERT INTO `accesos` VALUES ('1153', '2', '266', '620', '1');
INSERT INTO `accesos` VALUES ('1154', '2', '266', '621', '1');
INSERT INTO `accesos` VALUES ('1155', '2', '266', '622', '1');
INSERT INTO `accesos` VALUES ('1156', '2', '25', '618', '1');
INSERT INTO `accesos` VALUES ('1157', '2', '25', '619', '1');
INSERT INTO `accesos` VALUES ('1158', '2', '25', '620', '1');
INSERT INTO `accesos` VALUES ('1159', '2', '25', '621', '1');
INSERT INTO `accesos` VALUES ('1160', '2', '25', '622', '1');
INSERT INTO `accesos` VALUES ('1161', '2', '253', '643', '1');
INSERT INTO `accesos` VALUES ('1162', '2', '253', '655', '1');
INSERT INTO `accesos` VALUES ('1163', '2', '253', '656', '1');
INSERT INTO `accesos` VALUES ('1164', '2', '253', '654', '1');
INSERT INTO `accesos` VALUES ('1165', '2', '253', '657', '1');
INSERT INTO `accesos` VALUES ('1166', '2', '258', '619', '1');
INSERT INTO `accesos` VALUES ('1167', '2', '258', '618', '1');
INSERT INTO `accesos` VALUES ('1168', '2', '258', '620', '1');
INSERT INTO `accesos` VALUES ('1169', '2', '258', '621', '1');
INSERT INTO `accesos` VALUES ('1170', '2', '258', '622', '1');
INSERT INTO `accesos` VALUES ('1171', '2', '24', '618', '1');
INSERT INTO `accesos` VALUES ('1172', '2', '24', '619', '1');
INSERT INTO `accesos` VALUES ('1173', '2', '24', '620', '1');
INSERT INTO `accesos` VALUES ('1174', '2', '24', '621', '1');
INSERT INTO `accesos` VALUES ('1175', '2', '24', '622', '1');
INSERT INTO `accesos` VALUES ('1176', '2', '267', '618', '1');
INSERT INTO `accesos` VALUES ('1177', '2', '267', '619', '1');
INSERT INTO `accesos` VALUES ('1178', '2', '267', '620', '1');
INSERT INTO `accesos` VALUES ('1179', '2', '267', '621', '1');
INSERT INTO `accesos` VALUES ('1180', '2', '267', '622', '1');
INSERT INTO `accesos` VALUES ('1181', '2', '19', '618', '1');
INSERT INTO `accesos` VALUES ('1182', '2', '19', '619', '1');
INSERT INTO `accesos` VALUES ('1183', '2', '19', '620', '1');
INSERT INTO `accesos` VALUES ('1184', '2', '19', '621', '1');
INSERT INTO `accesos` VALUES ('1185', '2', '19', '622', '1');
INSERT INTO `accesos` VALUES ('1186', '2', '303', '618', '1');
INSERT INTO `accesos` VALUES ('1187', '2', '303', '619', '1');
INSERT INTO `accesos` VALUES ('1188', '2', '303', '620', '1');
INSERT INTO `accesos` VALUES ('1189', '2', '303', '621', '1');
INSERT INTO `accesos` VALUES ('1190', '2', '303', '622', '1');
INSERT INTO `accesos` VALUES ('1191', '2', '262', '618', '0');
INSERT INTO `accesos` VALUES ('1192', '2', '262', '619', '1');
INSERT INTO `accesos` VALUES ('1193', '2', '262', '620', '0');
INSERT INTO `accesos` VALUES ('1194', '2', '262', '621', '0');
INSERT INTO `accesos` VALUES ('1195', '2', '262', '622', '0');
INSERT INTO `accesos` VALUES ('1196', '2', '23', '618', '1');
INSERT INTO `accesos` VALUES ('1197', '2', '23', '619', '1');
INSERT INTO `accesos` VALUES ('1198', '2', '23', '620', '1');
INSERT INTO `accesos` VALUES ('1199', '2', '23', '621', '1');
INSERT INTO `accesos` VALUES ('1200', '2', '23', '622', '1');
INSERT INTO `accesos` VALUES ('1201', '2', '23', '664', '1');
INSERT INTO `accesos` VALUES ('1202', '2', '309', '618', '1');
INSERT INTO `accesos` VALUES ('1203', '2', '309', '619', '1');
INSERT INTO `accesos` VALUES ('1204', '2', '309', '620', '1');
INSERT INTO `accesos` VALUES ('1205', '2', '309', '621', '1');
INSERT INTO `accesos` VALUES ('1206', '2', '309', '622', '1');
INSERT INTO `accesos` VALUES ('1207', '2', '309', '671', '1');
INSERT INTO `accesos` VALUES ('1208', '2', '309', '675', '1');
INSERT INTO `accesos` VALUES ('1209', '2', '309', '670', '1');
INSERT INTO `accesos` VALUES ('1210', '2', '309', '672', '1');
INSERT INTO `accesos` VALUES ('1211', '2', '260', '618', '1');
INSERT INTO `accesos` VALUES ('1212', '2', '260', '619', '1');
INSERT INTO `accesos` VALUES ('1213', '2', '260', '620', '1');
INSERT INTO `accesos` VALUES ('1214', '2', '260', '621', '1');
INSERT INTO `accesos` VALUES ('1215', '2', '260', '622', '1');
INSERT INTO `accesos` VALUES ('1216', '2', '259', '618', '1');
INSERT INTO `accesos` VALUES ('1217', '2', '259', '619', '1');
INSERT INTO `accesos` VALUES ('1218', '2', '259', '620', '1');
INSERT INTO `accesos` VALUES ('1219', '2', '259', '621', '1');
INSERT INTO `accesos` VALUES ('1220', '2', '259', '622', '1');
INSERT INTO `accesos` VALUES ('1221', '2', '248', '618', '1');
INSERT INTO `accesos` VALUES ('1222', '2', '248', '619', '1');
INSERT INTO `accesos` VALUES ('1223', '2', '248', '620', '1');
INSERT INTO `accesos` VALUES ('1224', '2', '248', '621', '1');
INSERT INTO `accesos` VALUES ('1225', '2', '248', '622', '1');
INSERT INTO `accesos` VALUES ('1226', '2', '248', '648', '1');
INSERT INTO `accesos` VALUES ('1227', '2', '248', '647', '1');
INSERT INTO `accesos` VALUES ('1228', '2', '248', '650', '1');
INSERT INTO `accesos` VALUES ('1229', '2', '248', '662', '1');
INSERT INTO `accesos` VALUES ('1230', '2', '248', '649', '1');
INSERT INTO `accesos` VALUES ('1231', '2', '248', '653', '1');
INSERT INTO `accesos` VALUES ('1232', '2', '248', '652', '1');
INSERT INTO `accesos` VALUES ('1233', '2', '248', '651', '1');
INSERT INTO `accesos` VALUES ('1234', '2', '28', '618', '0');
INSERT INTO `accesos` VALUES ('1235', '2', '28', '619', '1');
INSERT INTO `accesos` VALUES ('1236', '2', '308', '619', '1');
INSERT INTO `accesos` VALUES ('1237', '2', '21', '619', '1');
INSERT INTO `accesos` VALUES ('1238', '2', '21', '618', '1');
INSERT INTO `accesos` VALUES ('1239', '2', '21', '620', '1');
INSERT INTO `accesos` VALUES ('1240', '2', '21', '621', '1');
INSERT INTO `accesos` VALUES ('1241', '2', '21', '622', '1');
INSERT INTO `accesos` VALUES ('1242', '2', '305', '618', '1');
INSERT INTO `accesos` VALUES ('1243', '2', '305', '619', '1');
INSERT INTO `accesos` VALUES ('1244', '2', '305', '620', '1');
INSERT INTO `accesos` VALUES ('1245', '2', '305', '621', '1');
INSERT INTO `accesos` VALUES ('1246', '2', '305', '622', '1');
INSERT INTO `accesos` VALUES ('1247', '2', '246', '618', '1');
INSERT INTO `accesos` VALUES ('1248', '2', '246', '619', '1');
INSERT INTO `accesos` VALUES ('1249', '2', '246', '620', '1');
INSERT INTO `accesos` VALUES ('1250', '2', '246', '621', '1');
INSERT INTO `accesos` VALUES ('1251', '2', '246', '622', '1');
INSERT INTO `accesos` VALUES ('1252', '2', '4', '618', '1');
INSERT INTO `accesos` VALUES ('1253', '2', '4', '619', '1');
INSERT INTO `accesos` VALUES ('1254', '2', '4', '620', '1');
INSERT INTO `accesos` VALUES ('1255', '2', '4', '621', '1');
INSERT INTO `accesos` VALUES ('1256', '2', '4', '622', '1');
INSERT INTO `accesos` VALUES ('1257', '2', '4', '663', '1');
INSERT INTO `accesos` VALUES ('1258', '2', '15', '618', '1');
INSERT INTO `accesos` VALUES ('1259', '2', '15', '619', '1');
INSERT INTO `accesos` VALUES ('1260', '2', '15', '620', '1');
INSERT INTO `accesos` VALUES ('1261', '2', '15', '621', '1');
INSERT INTO `accesos` VALUES ('1262', '2', '15', '622', '1');
INSERT INTO `accesos` VALUES ('1263', '2', '7', '618', '1');
INSERT INTO `accesos` VALUES ('1264', '2', '7', '619', '1');
INSERT INTO `accesos` VALUES ('1265', '2', '7', '620', '1');
INSERT INTO `accesos` VALUES ('1266', '2', '7', '621', '1');
INSERT INTO `accesos` VALUES ('1267', '2', '7', '622', '1');
INSERT INTO `accesos` VALUES ('1268', '2', '252', '618', '1');
INSERT INTO `accesos` VALUES ('1269', '2', '252', '619', '1');
INSERT INTO `accesos` VALUES ('1270', '2', '252', '620', '1');
INSERT INTO `accesos` VALUES ('1271', '2', '252', '621', '1');
INSERT INTO `accesos` VALUES ('1272', '2', '252', '622', '1');
INSERT INTO `accesos` VALUES ('1273', '2', '252', '677', '1');
INSERT INTO `accesos` VALUES ('1274', '2', '11', '634', '1');
INSERT INTO `accesos` VALUES ('1275', '2', '11', '636', '1');
INSERT INTO `accesos` VALUES ('1276', '2', '11', '635', '1');
INSERT INTO `accesos` VALUES ('1277', '2', '11', '637', '1');
INSERT INTO `accesos` VALUES ('1278', '2', '311', '618', '1');
INSERT INTO `accesos` VALUES ('1279', '2', '311', '620', '1');
INSERT INTO `accesos` VALUES ('1280', '2', '311', '619', '1');
INSERT INTO `accesos` VALUES ('1281', '2', '311', '621', '1');
INSERT INTO `accesos` VALUES ('1282', '2', '311', '622', '1');
INSERT INTO `accesos` VALUES ('1283', '2', '311', '674', '1');
INSERT INTO `accesos` VALUES ('1284', '2', '17', '619', '1');
INSERT INTO `accesos` VALUES ('1285', '2', '247', '618', '1');
INSERT INTO `accesos` VALUES ('1286', '2', '247', '619', '1');
INSERT INTO `accesos` VALUES ('1287', '2', '247', '620', '1');
INSERT INTO `accesos` VALUES ('1288', '2', '247', '621', '1');
INSERT INTO `accesos` VALUES ('1289', '2', '247', '622', '1');
INSERT INTO `accesos` VALUES ('1290', '2', '5', '618', '1');
INSERT INTO `accesos` VALUES ('1291', '2', '5', '619', '1');
INSERT INTO `accesos` VALUES ('1292', '2', '5', '620', '1');
INSERT INTO `accesos` VALUES ('1293', '2', '5', '621', '1');
INSERT INTO `accesos` VALUES ('1294', '2', '5', '622', '1');
INSERT INTO `accesos` VALUES ('1295', '2', '257', '618', '1');
INSERT INTO `accesos` VALUES ('1296', '2', '257', '619', '1');
INSERT INTO `accesos` VALUES ('1297', '2', '257', '620', '1');
INSERT INTO `accesos` VALUES ('1298', '2', '257', '621', '1');
INSERT INTO `accesos` VALUES ('1299', '2', '257', '622', '1');
INSERT INTO `accesos` VALUES ('1300', '2', '22', '619', '1');
INSERT INTO `accesos` VALUES ('1301', '2', '22', '618', '1');
INSERT INTO `accesos` VALUES ('1302', '2', '22', '620', '1');
INSERT INTO `accesos` VALUES ('1303', '2', '22', '621', '1');
INSERT INTO `accesos` VALUES ('1304', '2', '22', '622', '1');
INSERT INTO `accesos` VALUES ('1305', '2', '9', '618', '1');
INSERT INTO `accesos` VALUES ('1306', '2', '9', '619', '1');
INSERT INTO `accesos` VALUES ('1307', '2', '9', '620', '1');
INSERT INTO `accesos` VALUES ('1308', '2', '9', '621', '1');
INSERT INTO `accesos` VALUES ('1309', '2', '9', '622', '1');
INSERT INTO `accesos` VALUES ('1310', '2', '9', '638', '1');
INSERT INTO `accesos` VALUES ('1311', '2', '9', '640', '1');
INSERT INTO `accesos` VALUES ('1312', '2', '9', '639', '1');
INSERT INTO `accesos` VALUES ('1313', '2', '9', '641', '1');
INSERT INTO `accesos` VALUES ('1314', '2', '9', '642', '1');
INSERT INTO `accesos` VALUES ('1315', '3', '261', '665', '1');
INSERT INTO `accesos` VALUES ('1316', '3', '3', '619', '1');
INSERT INTO `accesos` VALUES ('1317', '3', '266', '619', '1');
INSERT INTO `accesos` VALUES ('1318', '3', '25', '619', '1');
INSERT INTO `accesos` VALUES ('1319', '3', '258', '619', '1');
INSERT INTO `accesos` VALUES ('1320', '3', '24', '619', '1');
INSERT INTO `accesos` VALUES ('1321', '3', '267', '619', '1');
INSERT INTO `accesos` VALUES ('1322', '3', '267', '620', '1');
INSERT INTO `accesos` VALUES ('1323', '3', '19', '619', '1');
INSERT INTO `accesos` VALUES ('1324', '3', '303', '619', '1');
INSERT INTO `accesos` VALUES ('1325', '3', '262', '619', '1');
INSERT INTO `accesos` VALUES ('1326', '3', '23', '619', '1');
INSERT INTO `accesos` VALUES ('1327', '3', '309', '619', '1');
INSERT INTO `accesos` VALUES ('1328', '3', '260', '619', '1');
INSERT INTO `accesos` VALUES ('1329', '3', '259', '619', '1');
INSERT INTO `accesos` VALUES ('1330', '3', '248', '619', '1');
INSERT INTO `accesos` VALUES ('1331', '3', '28', '619', '1');
INSERT INTO `accesos` VALUES ('1332', '3', '308', '619', '1');
INSERT INTO `accesos` VALUES ('1333', '3', '21', '619', '1');
INSERT INTO `accesos` VALUES ('1334', '3', '305', '619', '1');
INSERT INTO `accesos` VALUES ('1335', '3', '246', '619', '1');
INSERT INTO `accesos` VALUES ('1336', '3', '4', '619', '1');
INSERT INTO `accesos` VALUES ('1337', '3', '15', '619', '1');
INSERT INTO `accesos` VALUES ('1338', '3', '7', '619', '1');
INSERT INTO `accesos` VALUES ('1339', '3', '252', '619', '1');
INSERT INTO `accesos` VALUES ('1340', '3', '11', '635', '0');
INSERT INTO `accesos` VALUES ('1341', '3', '311', '619', '0');
INSERT INTO `accesos` VALUES ('1342', '3', '17', '619', '1');
INSERT INTO `accesos` VALUES ('1343', '3', '247', '619', '1');
INSERT INTO `accesos` VALUES ('1344', '3', '5', '619', '1');
INSERT INTO `accesos` VALUES ('1345', '3', '257', '619', '1');
INSERT INTO `accesos` VALUES ('1346', '3', '22', '619', '1');
INSERT INTO `accesos` VALUES ('1347', '3', '9', '619', '1');
INSERT INTO `accesos` VALUES ('1348', '3', '9', '621', '1');
INSERT INTO `accesos` VALUES ('1349', '3', '9', '639', '1');
INSERT INTO `accesos` VALUES ('1350', '3', '9', '641', '1');
INSERT INTO `accesos` VALUES ('1351', '3', '9', '642', '1');
INSERT INTO `accesos` VALUES ('1352', '3', '9', '640', '1');
INSERT INTO `accesos` VALUES ('1353', '3', '248', '618', '1');
INSERT INTO `accesos` VALUES ('1354', '3', '248', '620', '1');
INSERT INTO `accesos` VALUES ('1355', '3', '248', '651', '1');
INSERT INTO `accesos` VALUES ('1356', '3', '248', '647', '1');
UNLOCK TABLES;

-- Tabla: acciones_resagadas_usuarios
DROP TABLE IF EXISTS `acciones_resagadas_usuarios`;
CREATE TABLE `acciones_resagadas_usuarios` (
  `id_accion_resagada_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `accion_resagada` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_accion_resagada_usuario`),
  KEY `id_modulo_acciones_resagadas_usuarios_fk` (`id_modulo`),
  KEY `cedula_usuario_acciones_resagadas_usuarios_fk` (`cedula_usuario`),
  CONSTRAINT `cedula_usuario_acciones_resagadas_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_modulo_acciones_resagadas_usuarios_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1395 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `acciones_resagadas_usuarios` WRITE;
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1139', '9', 'V30485688', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1140', '9', 'V30485688', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1141', '9', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1143', '9', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1151', '248', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1153', '248', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1159', '247', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1161', '247', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1167', '250', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1169', '250', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1191', '19', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1193', '19', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1227', '4', 'V30485681', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1229', '4', 'V30485681', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1285', '9', 'V30485631', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1289', '4', 'V30485631', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1301', '9', 'V30485631', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1313', '9', 'V12345666', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1315', '9', 'V12345666', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1325', '248', 'V12345666', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1327', '248', 'V12345666', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1330', '248', 'V30485684', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1331', '308', 'V12345666', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1333', '308', 'V12345666', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1334', '308', 'V30485684', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1341', '9', 'V12345678', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1342', '9', 'V12345678', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1344', '247', 'V30485688', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1346', '247', 'V30485688', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1350', '250', 'V30485688', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1352', '250', 'V30485688', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1374', '19', 'V30485688', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1375', '19', 'V30485684', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1376', '19', 'V30485688', 'actDT', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1378', '4', 'V30485688', 'borrarDataModuloSS', '1');
INSERT INTO `acciones_resagadas_usuarios` VALUES ('1380', '4', 'V30485688', 'actDT', '1');
UNLOCK TABLES;

-- Tabla: bitacora
DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `resultado_bitacora` varchar(50) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `ip_dispositivo` varchar(25) NOT NULL,
  `cambios_efectuados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fecha_bitacora` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_bitacora`),
  KEY `cedula_usuario_bitacora_fk` (`cedula_usuario`),
  KEY `id_modulo_bitacora_fk` (`id_modulo`),
  CONSTRAINT `cedula_usuario_bitacora_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_modulo_bitacora_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2559 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `bitacora` WRITE;
INSERT INTO `bitacora` VALUES ('2247', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-02 19:16:47\"}}', '2026-08-02 19:16:47', '1');
INSERT INTO `bitacora` VALUES ('2248', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:20:56\"}}', '2026-08-03 15:20:56', '1');
INSERT INTO `bitacora` VALUES ('2249', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:37:44\"}}', '2026-08-03 15:37:44', '1');
INSERT INTO `bitacora` VALUES ('2250', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:26:26\"}}', '2026-08-03 16:26:26', '1');
INSERT INTO `bitacora` VALUES ('2251', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:34:09\"}}', '2026-08-03 16:34:09', '1');
INSERT INTO `bitacora` VALUES ('2252', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:44:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-03 16:44:58', '1');
INSERT INTO `bitacora` VALUES ('2253', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-06 14:34:11\"}}', '2026-08-06 14:34:11', '1');
INSERT INTO `bitacora` VALUES ('2254', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:27:54\"}}', '2026-08-07 11:27:54', '1');
INSERT INTO `bitacora` VALUES ('2255', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:45:45\"}}', '2026-08-07 11:45:45', '1');
INSERT INTO `bitacora` VALUES ('2256', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:48:07\"}}', '2026-08-07 11:48:07', '1');
INSERT INTO `bitacora` VALUES ('2257', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:50:21\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 11:50:21', '1');
INSERT INTO `bitacora` VALUES ('2258', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:52:24\"}}', '2026-08-07 11:52:24', '1');
INSERT INTO `bitacora` VALUES ('2259', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:59:48\"}}', '2026-08-07 11:59:48', '1');
INSERT INTO `bitacora` VALUES ('2260', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:02:20\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 12:02:20', '1');
INSERT INTO `bitacora` VALUES ('2261', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:04:32\"}}', '2026-08-07 12:04:32', '1');
INSERT INTO `bitacora` VALUES ('2262', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:11:47\"}}', '2026-08-07 12:11:47', '1');
INSERT INTO `bitacora` VALUES ('2263', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:29:25\"}}', '2026-08-07 12:29:25', '1');
INSERT INTO `bitacora` VALUES ('2264', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:57:18\"}}', '2026-08-07 12:57:19', '1');
INSERT INTO `bitacora` VALUES ('2265', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:17:54\"}}', '2026-08-07 13:17:54', '1');
INSERT INTO `bitacora` VALUES ('2266', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:20:00\"}}', '2026-08-07 13:20:00', '1');
INSERT INTO `bitacora` VALUES ('2267', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:24:42\"}}', '2026-08-07 14:24:42', '1');
INSERT INTO `bitacora` VALUES ('2268', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:31:05\"}}', '2026-08-07 14:31:05', '1');
INSERT INTO `bitacora` VALUES ('2269', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:34:53\"}}', '2026-08-07 14:34:53', '1');
INSERT INTO `bitacora` VALUES ('2270', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:36:19\"}}', '2026-08-07 14:36:19', '1');
INSERT INTO `bitacora` VALUES ('2271', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:39:01\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:39:01', '1');
INSERT INTO `bitacora` VALUES ('2272', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:40:56\"}}', '2026-08-07 14:40:56', '1');
INSERT INTO `bitacora` VALUES ('2273', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:44:05\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:44:05', '1');
INSERT INTO `bitacora` VALUES ('2274', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:53:46\"}}', '2026-08-07 14:48:46', '1');
INSERT INTO `bitacora` VALUES ('2275', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:57:49\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:52:49', '1');
INSERT INTO `bitacora` VALUES ('2276', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:01:59\"}}', '2026-08-07 14:56:59', '1');
INSERT INTO `bitacora` VALUES ('2277', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:07:59\"}}', '2026-08-07 15:02:59', '1');
INSERT INTO `bitacora` VALUES ('2278', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:24:04\"}}', '2026-08-07 15:19:04', '1');
INSERT INTO `bitacora` VALUES ('2279', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:29:35\"}}', '2026-08-07 15:24:35', '1');
INSERT INTO `bitacora` VALUES ('2280', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 11:44:29\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 11:39:29', '1');
INSERT INTO `bitacora` VALUES ('2281', 'V30485684', '28', 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:13', '1');
INSERT INTO `bitacora` VALUES ('2282', 'V30485684', '28', 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:55', '1');
INSERT INTO `bitacora` VALUES ('2283', 'V30485684', '28', 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:59', '1');
INSERT INTO `bitacora` VALUES ('2284', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:13:55\"}}', '2026-08-08 12:08:55', '1');
INSERT INTO `bitacora` VALUES ('2285', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:30:09\"}}', '2026-08-08 12:25:09', '1');
INSERT INTO `bitacora` VALUES ('2286', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:31:23\"}}', '2026-08-08 13:26:23', '1');
INSERT INTO `bitacora` VALUES ('2287', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:42:01\"}}', '2026-08-08 13:37:01', '1');
INSERT INTO `bitacora` VALUES ('2288', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:53:41\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 13:48:41', '1');
INSERT INTO `bitacora` VALUES ('2289', 'V30485684', '28', 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pagos\"}}', '2026-08-08 13:49:57', '1');
INSERT INTO `bitacora` VALUES ('2290', 'V30485684', '28', 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pago\"}}', '2026-08-08 13:50:09', '1');
INSERT INTO `bitacora` VALUES ('2291', 'V30485684', '28', 'Éxito', 'registrar', '190.97.229.57', '{\"id_permiso\":{\"Registrado\":673},\"nombre_permiso\":{\"Registrado\":\"permisox\"},\"status\":{\"Registrado\":1}}', '2026-08-08 13:50:22', '1');
INSERT INTO `bitacora` VALUES ('2292', 'V30485684', '28', 'Éxito', 'Eliminar con id 673', '190.97.229.57', NULL, '2026-08-08 13:50:26', '1');
INSERT INTO `bitacora` VALUES ('2294', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 16:41:32\"}}', '2026-08-08 16:36:32', '1');
INSERT INTO `bitacora` VALUES ('2295', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 17:30:34\"}}', '2026-08-08 17:25:34', '1');
INSERT INTO `bitacora` VALUES ('2296', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:25:37\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:20:37', '1');
INSERT INTO `bitacora` VALUES ('2297', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:34:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:29:26', '1');
INSERT INTO `bitacora` VALUES ('2298', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:35:09\"}}', '2026-08-10 14:30:09', '1');
INSERT INTO `bitacora` VALUES ('2299', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:47:03\"}}', '2026-08-10 14:42:03', '1');
INSERT INTO `bitacora` VALUES ('2300', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 15:04:51\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:59:51', '1');
INSERT INTO `bitacora` VALUES ('2301', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:17:40\"}}', '2026-08-10 16:12:40', '1');
INSERT INTO `bitacora` VALUES ('2302', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:29:33\"}}', '2026-08-10 16:24:33', '1');
INSERT INTO `bitacora` VALUES ('2303', 'V30485684', '9', 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfremitez6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1239\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$petVY5x\\/7OGMh1gk\\/PuBt.R2SxsI20QXItsUrfqIG.XrEk4aScQ5m\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:45:17', '1');
INSERT INTO `bitacora` VALUES ('2304', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:01:59\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 16:56:59', '1');
INSERT INTO `bitacora` VALUES ('2305', 'V30485684', '9', 'Éxito', 'registrar usuario con la cédula/rif: V30485681', '181.208.252.213', '{\"cedula_usuario\":{\"Registrado\":\"V30485681\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitez66@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484643\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1236\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk\\/y0bWnjNde\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_58_55_14.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:58:57', '1');
INSERT INTO `bitacora` VALUES ('2306', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"nombre_usuario\":{\"Modificado\":\"Andersoa\"},\"cedula_usuario\":{\"Eliminado\":\"V30485688\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:16', '1');
INSERT INTO `bitacora` VALUES ('2307', 'V30485684', '9', 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"foto_usuario\":{\"name\":{\"Registrado\":\"Imagen2.jpg\"},\"full_path\":{\"Registrado\":\"Imagen2.jpg\"},\"type\":{\"Registrado\":\"image\\/jpeg\"},\"tmp_name\":{\"Registrado\":\"C:\\\\xampp\\\\tmp\\\\phpCA63.tmp\"},\"error\":{\"Registrado\":0},\"size\":{\"Registrado\":134384}},\"fofo_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:26', '1');
INSERT INTO `bitacora` VALUES ('2308', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V30485688', '181.208.252.213', NULL, '2026-08-10 17:00:36', '1');
INSERT INTO `bitacora` VALUES ('2309', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:21:15\"}}', '2026-08-10 17:16:15', '1');
INSERT INTO `bitacora` VALUES ('2310', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:27:17\"}}', '2026-08-10 17:22:17', '1');
INSERT INTO `bitacora` VALUES ('2311', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:36:12\"}}', '2026-08-10 17:31:12', '1');
INSERT INTO `bitacora` VALUES ('2312', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:45:38\"}}', '2026-08-10 17:40:38', '1');
INSERT INTO `bitacora` VALUES ('2313', 'V30485684', '248', 'Éxito', 'registrar', '181.208.252.213', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26183-00015-86\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":304}}],\"delivery\":{\"latitud\":{\"Registrado\":9.93103},\"longitud\":{\"Registrado\":-69.621948}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_10_17_44_30_56.jpg\"]}]}', '2026-08-10 17:44:32', '1');
INSERT INTO `bitacora` VALUES ('2314', 'V30485684', '248', 'Éxito', 'Asignar Repartidor al pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-08-10 17:47:34', '1');
INSERT INTO `bitacora` VALUES ('2315', 'V30485684', '248', 'Éxito', 'Actualizar pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":8}}', '2026-08-10 17:48:13', '1');
INSERT INTO `bitacora` VALUES ('2317', 'V30485684', '247', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"100\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-10 17:55:28', '1');
INSERT INTO `bitacora` VALUES ('2318', 'V30485684', '247', 'Éxito', 'Actualizar', '190.97.229.57', '{\"precio_ruta\":{\"Modificado\":\"0.20\"}}', '2026-08-10 17:55:38', '1');
INSERT INTO `bitacora` VALUES ('2319', 'V30485684', '247', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-10 17:55:45', '1');
INSERT INTO `bitacora` VALUES ('2320', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:09:37\"}}', '2026-08-10 18:04:37', '1');
INSERT INTO `bitacora` VALUES ('2321', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:22:58\"}}', '2026-08-10 18:17:58', '1');
INSERT INTO `bitacora` VALUES ('2322', 'V30485684', '250', 'Éxito', 'Registrar', '181.208.252.213', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMM\"}}', '2026-08-10 18:25:23', '1');
INSERT INTO `bitacora` VALUES ('2323', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:30:58\"}}', '2026-08-10 18:25:58', '1');
INSERT INTO `bitacora` VALUES ('2324', 'V30485684', '250', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:26:39', '1');
INSERT INTO `bitacora` VALUES ('2325', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 38', '190.97.229.57', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMmm.\"}}', '2026-08-10 18:27:01', '1');
INSERT INTO `bitacora` VALUES ('2326', 'V30485684', '250', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:31:06', '1');
INSERT INTO `bitacora` VALUES ('2327', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 39', '190.97.229.57', NULL, '2026-08-10 18:31:10', '1');
INSERT INTO `bitacora` VALUES ('2328', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 38', '190.97.229.57', NULL, '2026-08-10 18:31:13', '1');
INSERT INTO `bitacora` VALUES ('2329', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 37', '190.97.229.57', NULL, '2026-08-10 18:31:17', '1');
INSERT INTO `bitacora` VALUES ('2330', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:19:22\"}}', '2026-08-11 11:14:22', '1');
INSERT INTO `bitacora` VALUES ('2331', 'V30485684', '247', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"11\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-11 11:15:25', '1');
INSERT INTO `bitacora` VALUES ('2332', 'V30485684', '247', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 11:15:41', '1');
INSERT INTO `bitacora` VALUES ('2333', 'V30485684', '250', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMs\"}}', '2026-08-11 11:15:59', '1');
INSERT INTO `bitacora` VALUES ('2334', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 40', '190.97.229.57', NULL, '2026-08-11 11:16:03', '1');
INSERT INTO `bitacora` VALUES ('2335', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:45:30\"}}', '2026-08-11 11:40:30', '1');
INSERT INTO `bitacora` VALUES ('2336', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:53:02\"}}', '2026-08-11 11:48:02', '1');
INSERT INTO `bitacora` VALUES ('2337', 'V30485684', '19', 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOm\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":1},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 11:50:12', '1');
INSERT INTO `bitacora` VALUES ('2338', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"nombre_metodo_pago\":{\"Modificado\":\"EFECTIVOmm\"}}', '2026-08-11 11:54:44', '1');
INSERT INTO `bitacora` VALUES ('2339', 'V30485684', '19', 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:54:59', '1');
INSERT INTO `bitacora` VALUES ('2340', 'V30485684', '19', 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:45', '1');
INSERT INTO `bitacora` VALUES ('2341', 'V30485684', '19', 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:57', '1');
INSERT INTO `bitacora` VALUES ('2342', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:09:33\"}}', '2026-08-11 12:04:33', '1');
INSERT INTO `bitacora` VALUES ('2343', 'V30485684', '19', 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 12:04:51', '1');
INSERT INTO `bitacora` VALUES ('2344', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_moneda\":{\"Modificado\":0},\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:07:35', '1');
INSERT INTO `bitacora` VALUES ('2345', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0}}', '2026-08-11 12:07:43', '1');
INSERT INTO `bitacora` VALUES ('2346', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:07:49', '1');
INSERT INTO `bitacora` VALUES ('2347', 'V30485684', '19', 'Éxito', 'registtrar', '181.208.252.213', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOs\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 12:08:10', '1');
INSERT INTO `bitacora` VALUES ('2348', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:09:39', '1');
INSERT INTO `bitacora` VALUES ('2349', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:09:52', '1');
INSERT INTO `bitacora` VALUES ('2350', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:18:14\"}}', '2026-08-11 12:13:14', '1');
INSERT INTO `bitacora` VALUES ('2351', 'V30485684', '19', 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOb\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-08-11 12:13:43', '1');
INSERT INTO `bitacora` VALUES ('2352', 'V30485684', '19', 'Éxito', 'Actualizar', '190.97.229.57', '{\"necesita_banco_emisor\":{\"Modificado\":1},\"necesita_banco_receptor\":{\"Modificado\":1},\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:15:24', '1');
INSERT INTO `bitacora` VALUES ('2353', 'V30485684', '19', 'Éxito', 'Actualizar', '190.97.229.57', '{\"mostrar_ecommerce\":{\"Modificado\":1}}', '2026-08-11 12:15:46', '1');
INSERT INTO `bitacora` VALUES ('2354', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":1}}', '2026-08-11 12:17:52', '1');
INSERT INTO `bitacora` VALUES ('2355', 'V30485684', '19', 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:17:56', '1');
INSERT INTO `bitacora` VALUES ('2356', 'V30485684', '19', 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:02', '1');
INSERT INTO `bitacora` VALUES ('2357', 'V30485684', '19', 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:06', '1');
INSERT INTO `bitacora` VALUES ('2358', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:18:27', '1');
INSERT INTO `bitacora` VALUES ('2359', 'V30485684', '19', 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0}}', '2026-08-11 12:18:54', '1');
INSERT INTO `bitacora` VALUES ('2360', 'V30485684', '4', 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00001-86\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00002-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00003-67\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00004-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00005-00\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00006-27\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:22:42', '1');
INSERT INTO `bitacora` VALUES ('2361', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:32:46\"}}', '2026-08-11 12:27:46', '1');
INSERT INTO `bitacora` VALUES ('2362', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00006-27)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_26.jpg?v=2026-08-11_12_33_26\"}}', '2026-08-11 12:33:26', '1');
INSERT INTO `bitacora` VALUES ('2363', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00005-00)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_44.jpg?v=2026-08-11_12_33_44\"}}', '2026-08-11 12:33:44', '1');
INSERT INTO `bitacora` VALUES ('2364', 'V30485684', '4', 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00001-40\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00003-78\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00004-81\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00005-06\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00006-01\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"2.00\"}}]}}', '2026-08-11 12:34:12', '1');
INSERT INTO `bitacora` VALUES ('2365', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:34:38', '1');
INSERT INTO `bitacora` VALUES ('2366', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:36:17', '1');
INSERT INTO `bitacora` VALUES ('2367', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00009-69\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00010-25\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00011-11\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00012-62\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:05', '1');
INSERT INTO `bitacora` VALUES ('2368', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00013-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00014-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00015-48\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00016-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00017-82\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00018-55\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:59', '1');
INSERT INTO `bitacora` VALUES ('2369', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:44:05\"}}', '2026-08-11 12:39:05', '1');
INSERT INTO `bitacora` VALUES ('2370', 'V30485684', '4', 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROf\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00019-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00020-14\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00021-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00022-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00023-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00024-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:48:20', '1');
INSERT INTO `bitacora` VALUES ('2371', 'V30485684', '4', 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLOROm\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00025-15\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00026-98\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00027-61\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00028-84\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00029-56\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00030-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-08-11 12:48:53', '1');
INSERT INTO `bitacora` VALUES ('2372', 'V30485684', '4', 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROs\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00031-33\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_50_58_55.jpg\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00032-91\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00033-85\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00034-70\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00035-71\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00036-95\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-08-11 12:50:58', '1');
INSERT INTO `bitacora` VALUES ('2373', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00032-91)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_51_14.png?v=2026-08-11_12_51_14\"}}', '2026-08-11 12:51:14', '1');
INSERT INTO `bitacora` VALUES ('2374', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:52:04', '1');
INSERT INTO `bitacora` VALUES ('2375', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:53:54', '1');
INSERT INTO `bitacora` VALUES ('2376', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:10:17\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:05:17', '1');
INSERT INTO `bitacora` VALUES ('2377', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-23\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-74\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-91\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-84\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-33\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:05:38', '1');
INSERT INTO `bitacora` VALUES ('2378', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00042-33)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_05_54.png?v=2026-08-11_13_05_54\"}}', '2026-08-11 13:05:54', '1');
INSERT INTO `bitacora` VALUES ('2379', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-09\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-77\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-44\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-60\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:06:02', '1');
INSERT INTO `bitacora` VALUES ('2380', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-85\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-61\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-26\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-64\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-74\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-30\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:43', '1');
INSERT INTO `bitacora` VALUES ('2381', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-27\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-10\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-14\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-16\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-48\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-85\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:58', '1');
INSERT INTO `bitacora` VALUES ('2382', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:18:52\"}}', '2026-08-11 13:13:52', '1');
INSERT INTO `bitacora` VALUES ('2383', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:30:45\"}}', '2026-08-11 13:25:46', '1');
INSERT INTO `bitacora` VALUES ('2384', 'V30485684', '4', 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-67\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-54\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-27\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-32\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:26:08', '1');
INSERT INTO `bitacora` VALUES ('2385', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:45:19\"}}', '2026-08-11 13:40:19', '1');
INSERT INTO `bitacora` VALUES ('2386', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:55:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:50:26', '1');
INSERT INTO `bitacora` VALUES ('2387', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-12\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-22\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-89\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-07\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-17\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":212}}]}}', '2026-08-11 13:56:49', '1');
INSERT INTO `bitacora` VALUES ('2388', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-68\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-34\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-45\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-23\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00041-07\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Eliminado\":1},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00042-17\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":213}}]}}', '2026-08-11 13:56:57', '1');
INSERT INTO `bitacora` VALUES ('2389', 'V30485684', '4', 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-00\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-12\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-70\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-06\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00041-20\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":214}}]}}', '2026-08-11 13:57:05', '1');
INSERT INTO `bitacora` VALUES ('2390', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00041-20)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_32.png?v=2026-08-11_13_57_32\"}}', '2026-08-11 13:57:32', '1');
INSERT INTO `bitacora` VALUES ('2391', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00039-70)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_40.png?v=2026-08-11_13_57_40\"}}', '2026-08-11 13:57:40', '1');
INSERT INTO `bitacora` VALUES ('2392', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:14:01\"}}', '2026-08-11 14:09:01', '1');
INSERT INTO `bitacora` VALUES ('2393', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:15:35\"}}', '2026-08-11 14:10:35', '1');
INSERT INTO `bitacora` VALUES ('2394', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 15:28:51\"}}', '2026-08-11 15:23:51', '1');
INSERT INTO `bitacora` VALUES ('2395', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-12 11:05:48\"}}', '2026-08-12 11:00:48', '1');
INSERT INTO `bitacora` VALUES ('2396', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:03:33\"}}', '2026-08-14 10:58:33', '1');
INSERT INTO `bitacora` VALUES ('2397', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:04:40\"}}', '2026-08-14 10:59:40', '1');
INSERT INTO `bitacora` VALUES ('2398', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:07:14\"}}', '2026-08-14 11:02:14', '1');
INSERT INTO `bitacora` VALUES ('2399', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:18:46\"}}', '2026-08-14 11:13:47', '1');
INSERT INTO `bitacora` VALUES ('2400', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:25:05\"}}', '2026-08-14 11:20:05', '1');
INSERT INTO `bitacora` VALUES ('2401', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:08:33\"}}', '2026-08-14 12:03:33', '1');
INSERT INTO `bitacora` VALUES ('2402', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:23:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 12:18:58', '1');
INSERT INTO `bitacora` VALUES ('2403', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:42:03\"}}', '2026-08-14 12:37:03', '1');
INSERT INTO `bitacora` VALUES ('2404', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:59:48\"}}', '2026-08-14 12:54:48', '1');
INSERT INTO `bitacora` VALUES ('2405', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:15:12\"}}', '2026-08-14 13:10:12', '1');
INSERT INTO `bitacora` VALUES ('2406', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:40:53\"}}', '2026-08-14 13:35:53', '1');
INSERT INTO `bitacora` VALUES ('2407', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:56:25\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 13:51:25', '1');
INSERT INTO `bitacora` VALUES ('2408', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 14:04:41\"}}', '2026-08-14 13:59:41', '1');
INSERT INTO `bitacora` VALUES ('2409', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 11:01:46\"}}', '2026-08-15 10:56:46', '1');
INSERT INTO `bitacora` VALUES ('2410', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:30:26\"}}', '2026-08-15 18:25:26', '1');
INSERT INTO `bitacora` VALUES ('2411', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:40:36\"}}', '2026-08-15 18:35:36', '1');
INSERT INTO `bitacora` VALUES ('2412', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-15 18:49:19\"}}', '2026-08-15 18:44:19', '1');
INSERT INTO `bitacora` VALUES ('2413', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:36:42\"}}', '2026-08-16 15:31:42', '1');
INSERT INTO `bitacora` VALUES ('2414', 'V30485684', '17', 'Éxito', 'eliminar', '190.97.229.57', '{\"id_rol\":{\"Eliminado\":2},\"nombre_rol\":{\"Eliminado\":\"OFICINISTA\"},\"status\":{\"Eliminado\":1}}', '2026-08-16 15:35:40', '1');
INSERT INTO `bitacora` VALUES ('2416', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:43:57\"}}', '2026-08-16 15:38:57', '1');
INSERT INTO `bitacora` VALUES ('2422', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 15:51:48\"}}', '2026-08-16 15:46:48', '1');
INSERT INTO `bitacora` VALUES ('2424', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:04:30\"}}', '2026-08-16 15:59:30', '1');
INSERT INTO `bitacora` VALUES ('2426', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:07:52\"}}', '2026-08-16 16:02:52', '1');
INSERT INTO `bitacora` VALUES ('2428', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V30485681', '181.208.252.213', NULL, '2026-08-16 16:03:15', '1');
INSERT INTO `bitacora` VALUES ('2429', 'V30485631', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:10:37\"}}', '2026-08-16 16:05:37', '1');
INSERT INTO `bitacora` VALUES ('2430', 'V30485684', '17', 'Éxito', 'actualizar', '190.97.229.57', '{\"nombre_rol\":{\"Modificado\":\"CLIENTE\"},\"status\":{\"Eliminado\":1}}', '2026-08-16 16:05:58', '1');
INSERT INTO `bitacora` VALUES ('2431', 'V30485631', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:19:39\"}}', '2026-08-16 16:14:39', '1');
INSERT INTO `bitacora` VALUES ('2432', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:28:37\"}}', '2026-08-16 16:23:37', '1');
INSERT INTO `bitacora` VALUES ('2434', 'V30485631', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 16:30:41\"}}', '2026-08-16 16:25:41', '1');
INSERT INTO `bitacora` VALUES ('2435', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 17:55:08\"}}', '2026-08-16 17:50:08', '1');
INSERT INTO `bitacora` VALUES ('2437', 'V30485631', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-16 17:55:27\"}}', '2026-08-16 17:50:27', '1');
INSERT INTO `bitacora` VALUES ('2438', 'V30485684', '4', 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-16 18:57:00', '1');
INSERT INTO `bitacora` VALUES ('2439', 'V30485684', '4', 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-16 18:57:05', '1');
INSERT INTO `bitacora` VALUES ('2440', 'V30485631', '248', 'Fallido', 'registrar', '190.97.229.57', NULL, '2026-08-16 18:59:25', '1');
INSERT INTO `bitacora` VALUES ('2441', 'V30485631', '248', 'Éxito', 'registrar', '190.97.229.57', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":113}}],\"delivery\":{\"latitud\":{\"Registrado\":9.861855646518277},\"longitud\":{\"Registrado\":-69.61206931161935}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_16_19_00_02_21.jpg\"]}]}', '2026-08-16 19:00:05', '1');
INSERT INTO `bitacora` VALUES ('2442', 'V30485684', '248', 'Éxito', 'Asignar Repartidor al pedido (FACT-26227-00001-55)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V30485654\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-08-16 19:25:14', '1');
INSERT INTO `bitacora` VALUES ('2443', 'V30485684', '248', 'Éxito', 'Actualizar pedido (FACT-26227-00001-55)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":8}}', '2026-08-16 19:25:36', '1');
INSERT INTO `bitacora` VALUES ('2445', 'V30485684', '9', 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"foto_usuario\":{\"name\":{\"Registrado\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\"},\"full_path\":{\"Registrado\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\"},\"type\":{\"Registrado\":\"image\\/jpeg\"},\"tmp_name\":{\"Registrado\":\"C:\\\\xampp\\\\tmp\\\\phpD44.tmp\"},\"error\":{\"Registrado\":0},\"size\":{\"Registrado\":118154}},\"fofo_usuario\":{\"Eliminado\":\"\"}}', '2026-08-16 19:49:47', '1');
INSERT INTO `bitacora` VALUES ('2446', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"apellido_usuario\":{\"Modificado\":\"Mendozam\"},\"cedula_usuario\":{\"Eliminado\":\"V30485631\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47\"}}', '2026-08-16 19:49:57', '1');
INSERT INTO `bitacora` VALUES ('2447', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485631', '190.97.229.57', '{\"apellido_usuario\":{\"Modificado\":\"Mendoza\"},\"cedula_usuario\":{\"Eliminado\":\"V30485631\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47\"}}', '2026-08-16 19:50:04', '1');
INSERT INTO `bitacora` VALUES ('2448', 'V30485684', '9', 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitenz6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":2},\"usuario_usuario\":{\"Registrado\":\"Ander12399\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$OFi7gH2tMZled2NsBfYyI.NWAYlnlg3CUuQN\\/mEoH3phwkhCk5jDO\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_16_19_51_07_2.png\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-16 19:51:10', '1');
INSERT INTO `bitacora` VALUES ('2449', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V30485631', '190.97.229.57', NULL, '2026-08-16 19:51:25', '1');
INSERT INTO `bitacora` VALUES ('2450', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V30485688', '190.97.229.57', NULL, '2026-08-16 19:51:31', '1');
INSERT INTO `bitacora` VALUES ('2451', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-17 13:27:41\"}}', '2026-08-17 13:22:41', '1');
INSERT INTO `bitacora` VALUES ('2453', 'V30485684', '23', 'Éxito', 'actualizar', '181.208.252.213', '{\"valor_moneda\":{\"Modificado\":\"772.54\"}}', '2026-08-17 13:25:30', '1');
INSERT INTO `bitacora` VALUES ('2454', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 10:30:06\"}}', '2026-09-06 10:25:06', '1');
INSERT INTO `bitacora` VALUES ('2456', 'V30485684', '9', 'Éxito', 'registrar usuario con la cédula/rif: V12345666', '181.208.252.213', '{\"cedula_usuario\":{\"Registrado\":\"V12345666\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitez69@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":3},\"usuario_usuario\":{\"Registrado\":\"Ander12398\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$dhukfTGqIjZ.y3ViyOzCfeGa6HTrKo9UkIja7NixkZtOKo0e5zI16\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_09_06_13_06_06_87.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-09-06 13:06:09', '1');
INSERT INTO `bitacora` VALUES ('2458', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485684', '181.208.252.213', '{\"direccion_usuario\":{\"Modificado\":\"SANARES\"},\"cedula_usuario\":{\"Eliminado\":\"V30485684\"},\"foto_usuario\":{\"Eliminado\":\"\"}}', '2026-09-06 17:25:58', '1');
INSERT INTO `bitacora` VALUES ('2459', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485684', '181.208.252.213', '{\"direccion_usuario\":{\"Modificado\":\"SANARE\"},\"cedula_usuario\":{\"Eliminado\":\"V30485684\"},\"foto_usuario\":{\"Eliminado\":\"\"}}', '2026-09-06 17:26:05', '1');
INSERT INTO `bitacora` VALUES ('2460', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 18:41:43\"}}', '2026-09-06 18:36:43', '1');
INSERT INTO `bitacora` VALUES ('2462', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V30485684', '181.208.252.213', '{\"contrasena_usuario\":{\"Modificado\":\"$2y$10$lvgwVqGtaGEq\\/M9hBWs13.7TQKMxtcsJ8DgJXu2UMZ8XOx4K8dV5e\"},\"cedula_usuario\":{\"Eliminado\":\"V30485684\"},\"foto_usuario\":{\"Eliminado\":\"\"}}', '2026-09-06 19:03:13', '1');
INSERT INTO `bitacora` VALUES ('2463', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:11:35\"}}', '2026-09-06 19:06:36', '1');
INSERT INTO `bitacora` VALUES ('2466', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:21:55\"}}', '2026-09-06 19:16:56', '1');
INSERT INTO `bitacora` VALUES ('2468', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-06 19:26:30\"}}', '2026-09-06 19:21:30', '1');
INSERT INTO `bitacora` VALUES ('2470', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-07 07:58:30\"}}', '2026-09-07 07:53:30', '1');
INSERT INTO `bitacora` VALUES ('2473', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '82.86.69.239', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 10:53:00\"}}', '2026-09-10 10:48:00', '1');
INSERT INTO `bitacora` VALUES ('2476', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '186.167.226.150', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-10 17:27:05\"}}', '2026-09-10 17:22:05', '1');
INSERT INTO `bitacora` VALUES ('2479', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-15 20:05:51\"}}', '2026-09-15 20:00:51', '1');
INSERT INTO `bitacora` VALUES ('2481', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 09:35:11\"}}', '2026-09-16 09:30:11', '1');
INSERT INTO `bitacora` VALUES ('2483', 'V30485684', '248', 'Éxito', 'registrar', '190.97.229.57', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00012-62\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"1\"},\"id_moneda\":{\"Registrado\":\"2\"},\"id_banco_emisor\":{\"Registrado\":\"2865\"},\"id_banco_receptor\":{\"Registrado\":\"2868\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":97000}}],\"delivery\":{\"latitud\":{\"Registrado\":9.861770000000002},\"longitud\":{\"Registrado\":-69.61205650000001}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_09_16_09_49_09_86.jpg\"]}]}', '2026-09-16 09:49:11', '1');
INSERT INTO `bitacora` VALUES ('2484', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 16:43:12\"}}', '2026-09-16 16:38:12', '1');
INSERT INTO `bitacora` VALUES ('2486', 'V30485684', '248', 'Éxito', 'Asignar Repartidor al pedido (FACT-26258-00001-89)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-09-16 17:36:52', '1');
INSERT INTO `bitacora` VALUES ('2487', 'V30485684', '248', 'Éxito', 'Actualizar pedido (FACT-26258-00001-89)', '190.97.229.57', '{\"status_pedido\":{\"Modificado\":8}}', '2026-09-16 18:31:26', '1');
INSERT INTO `bitacora` VALUES ('2488', 'V30485684', '248', 'Éxito', 'Imprimir pedido (FACT-26190-00001-44)', '190.97.229.57', NULL, '2026-09-16 18:32:32', '1');
INSERT INTO `bitacora` VALUES ('2489', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-16 20:21:00\"}}', '2026-09-16 20:16:01', '1');
INSERT INTO `bitacora` VALUES ('2491', 'V30485684', '248', 'Éxito', 'Imprimir pedido (FACT-26258-00001-89)', '181.208.252.213', NULL, '2026-09-16 20:16:13', '1');
INSERT INTO `bitacora` VALUES ('2492', 'V30485684', '248', 'Éxito', 'Imprimir pedido (FACT-26258-00001-89)', '181.208.252.213', NULL, '2026-09-16 20:17:44', '1');
INSERT INTO `bitacora` VALUES ('2493', 'V30485684', '248', 'Éxito', 'Imprimir pedido (FACT-26258-00001-89)', '181.208.252.213', NULL, '2026-09-16 20:18:13', '1');
INSERT INTO `bitacora` VALUES ('2494', 'V30485684', '248', 'Éxito', 'Imprimir pedido (FACT-26258-00001-89)', '181.208.252.213', NULL, '2026-09-16 20:18:33', '1');
INSERT INTO `bitacora` VALUES ('2495', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 17:28:54\"}}', '2026-09-17 17:23:54', '1');
INSERT INTO `bitacora` VALUES ('2497', 'V30485684', '251', 'Éxito', 'registrar', '190.97.229.57', '{\"id_pregunta\":{\"Registrado\":1},\"texto_pregunta\":{\"Registrado\":\"¿Cómo se llamaba tu primera mascota0?\"}}', '2026-09-17 17:31:45', '1');
INSERT INTO `bitacora` VALUES ('2498', 'V30485684', '308', 'Éxito', 'actualizar', '190.97.229.57', '{\"texto_pregunta\":{\"Modificado\":\"¿Cómo se llamaba tu primera mascota00?\"},\"status\":{\"Eliminado\":1}}', '2026-09-17 17:38:21', '1');
INSERT INTO `bitacora` VALUES ('2499', 'V30485684', '9', 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreite9z6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484647\"},\"id_rol\":{\"Registrado\":3},\"usuario_usuario\":{\"Registrado\":\"Ander1239\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$0Y9xE0tZKb4bIHmlzrpv9.xihEL9qCtYfzzc6.sYE7RZQF.TfoCta\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_09_17_17_41_57_45.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-09-17 17:42:20', '1');
INSERT INTO `bitacora` VALUES ('2500', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V12345666', '190.97.229.57', '{\"usuario_usuario\":{\"Modificado\":\"Ander123989\"},\"cedula_usuario\":{\"Eliminado\":\"V12345666\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026_09_06_13_06_06_87.jpg\"}}', '2026-09-17 17:42:50', '1');
INSERT INTO `bitacora` VALUES ('2501', 'V30485684', '9', 'Éxito', 'Actualizar usuario con la cedula/rif: V12345666', '190.97.229.57', '{\"apellido_usuario\":{\"Modificado\":\"Freitezjj\"},\"cedula_usuario\":{\"Eliminado\":\"V12345666\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026_09_06_13_06_06_87.jpg\"}}', '2026-09-17 17:43:07', '1');
INSERT INTO `bitacora` VALUES ('2502', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V12345666', '190.97.229.57', NULL, '2026-09-17 17:44:39', '1');
INSERT INTO `bitacora` VALUES ('2503', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-17 17:52:54\"}}', '2026-09-17 17:47:55', '1');
INSERT INTO `bitacora` VALUES ('2505', 'V30485684', '9', 'Éxito', 'Eliminar usuario con la cedula/rif: V12345678', '190.97.229.57', NULL, '2026-09-17 17:48:07', '1');
INSERT INTO `bitacora` VALUES ('2506', 'V30485684', '247', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"100\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-09-17 17:53:33', '1');
INSERT INTO `bitacora` VALUES ('2507', 'V30485684', '247', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 17:54:03', '1');
INSERT INTO `bitacora` VALUES ('2508', 'V30485684', '250', 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMM\"}}', '2026-09-17 17:54:56', '1');
INSERT INTO `bitacora` VALUES ('2509', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 41', '190.97.229.57', NULL, '2026-09-17 17:55:01', '1');
INSERT INTO `bitacora` VALUES ('2511', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 6', '190.97.229.57', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMmm\"}}', '2026-09-17 17:55:59', '1');
INSERT INTO `bitacora` VALUES ('2512', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 6', '190.97.229.57', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMmmnn\"}}', '2026-09-17 17:56:09', '1');
INSERT INTO `bitacora` VALUES ('2515', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 6', '181.208.252.213', '{\"nombre_empresa\":{\"Modificado\":\"ZOOM\"}}', '2026-09-17 17:58:43', '1');
INSERT INTO `bitacora` VALUES ('2516', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 6', '181.208.252.213', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMi\"}}', '2026-09-17 18:01:54', '1');
INSERT INTO `bitacora` VALUES ('2517', 'V30485684', '250', 'Éxito', 'actualizar Empresa de envíos con id: 6', '181.208.252.213', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMim\"}}', '2026-09-17 18:02:02', '1');
INSERT INTO `bitacora` VALUES ('2518', 'V30485684', '250', 'Éxito', 'Eliminar Empresa de envíos con id: 6', '181.208.252.213', NULL, '2026-09-17 18:02:09', '1');
INSERT INTO `bitacora` VALUES ('2519', 'V30485684', '250', 'Éxito', 'Registrar', '181.208.252.213', '{\"nombre_empresa\":{\"Registrado\":\"ZOOM\"}}', '2026-09-17 18:02:17', '1');
INSERT INTO `bitacora` VALUES ('2521', 'V30485684', '19', 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"mmm\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-09-17 18:47:56', '1');
INSERT INTO `bitacora` VALUES ('2522', 'V30485684', '19', 'Éxito', 'Actualizar', '190.97.229.57', '{\"necesita_banco_receptor\":{\"Modificado\":1}}', '2026-09-17 18:48:46', '1');
INSERT INTO `bitacora` VALUES ('2523', 'V30485684', '19', 'Éxito', 'Actualizar', '190.97.229.57', '{\"necesita_referencia\":{\"Modificado\":1}}', '2026-09-17 18:49:47', '1');
INSERT INTO `bitacora` VALUES ('2524', 'V30485684', '19', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 18:49:58', '1');
INSERT INTO `bitacora` VALUES ('2525', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"HIPOCLORITO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"10.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00001-84\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00002-90\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00003-78\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00004-26\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00005-18\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00006-07\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00001-46\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":215},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-09-17 18:50:52', '1');
INSERT INTO `bitacora` VALUES ('2526', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26259-00003-78)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-09-17_18_51_04.jpg?v=2026-09-17_18_51_04\"}}', '2026-09-17 18:51:04', '1');
INSERT INTO `bitacora` VALUES ('2527', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 18:51:15', '1');
INSERT INTO `bitacora` VALUES ('2528', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 18:56:12', '1');
INSERT INTO `bitacora` VALUES ('2529', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00007-39\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00008-95\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00009-22\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00010-11\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00011-29\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00012-00\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00002-49\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":216},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.10\"}}]}}', '2026-09-17 18:58:08', '1');
INSERT INTO `bitacora` VALUES ('2530', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 18:58:24', '1');
INSERT INTO `bitacora` VALUES ('2531', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"0.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00013-48\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00014-34\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00015-30\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00016-37\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00017-08\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00018-39\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00003-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":217},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-09-17 19:00:30', '1');
INSERT INTO `bitacora` VALUES ('2532', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 19:00:35', '1');
INSERT INTO `bitacora` VALUES ('2534', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00019-95\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00020-17\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00021-28\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00022-02\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00023-33\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00024-91\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00004-17\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":219},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-09-17 19:05:05', '1');
INSERT INTO `bitacora` VALUES ('2535', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 19:05:23', '1');
INSERT INTO `bitacora` VALUES ('2536', 'V30485684', '4', 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00025-48\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00026-17\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00027-01\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00028-25\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00029-32\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00030-54\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00005-07\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-09-17 19:17:51', '1');
INSERT INTO `bitacora` VALUES ('2537', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26259-00030-54)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-09-17_19_19_27.jpg?v=2026-09-17_19_19_27\"}}', '2026-09-17 19:19:27', '1');
INSERT INTO `bitacora` VALUES ('2538', 'V30485684', '305', 'Éxito', 'Actualizar foto de la presentacion (PRPR-26259-00029-32)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-09-17_19_21_59.jpg?v=2026-09-17_19_21_59\"}}', '2026-09-17 19:21:59', '1');
INSERT INTO `bitacora` VALUES ('2539', 'V30485684', '4', 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-09-17 19:22:26', '1');
INSERT INTO `bitacora` VALUES ('2542', 'V30485684', '4', 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00031-92\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00032-58\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00033-94\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00034-20\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00035-24\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26259-00036-63\"},\"id_producto\":{\"Registrado\":\"PROD-26259-00006-02\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Registrado\":222},\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-09-17 19:50:39', '1');
INSERT INTO `bitacora` VALUES ('2544', 'V30485684', '9', 'Éxito', 'Iniciar sesión', '186.167.219.206', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-09-18 11:48:40\"}}', '2026-09-18 11:43:40', '1');
UNLOCK TABLES;

-- Tabla: iconos_notificaciones
DROP TABLE IF EXISTS `iconos_notificaciones`;
CREATE TABLE `iconos_notificaciones` (
  `id_icono_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `path_icono_notificacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_icono_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `iconos_notificaciones` WRITE;
INSERT INTO `iconos_notificaciones` VALUES ('1', 'info', '1');
INSERT INTO `iconos_notificaciones` VALUES ('3', 'success', '1');
UNLOCK TABLES;

-- Tabla: modulos
DROP TABLE IF EXISTS `modulos`;
CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_modulo`),
  KEY `nombre_modulo_indice` (`nombre_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `modulos` WRITE;
INSERT INTO `modulos` VALUES ('1', 'clientes', '1');
INSERT INTO `modulos` VALUES ('3', 'facturacion', '1');
INSERT INTO `modulos` VALUES ('4', 'productos', '1');
INSERT INTO `modulos` VALUES ('5', 'servicios', '1');
INSERT INTO `modulos` VALUES ('7', 'proveedores', '1');
INSERT INTO `modulos` VALUES ('8', 'compras', '1');
INSERT INTO `modulos` VALUES ('9', 'usuarios', '1');
INSERT INTO `modulos` VALUES ('10', 'cambios', '1');
INSERT INTO `modulos` VALUES ('11', 'reportes', '1');
INSERT INTO `modulos` VALUES ('13', 'bitacora', '1');
INSERT INTO `modulos` VALUES ('15', 'promociones', '1');
INSERT INTO `modulos` VALUES ('16', 'imagenes', '1');
INSERT INTO `modulos` VALUES ('17', 'roles', '1');
INSERT INTO `modulos` VALUES ('18', 'cambiosIva', '1');
INSERT INTO `modulos` VALUES ('19', 'metodos-pago', '1');
INSERT INTO `modulos` VALUES ('21', 'presentaciones', '1');
INSERT INTO `modulos` VALUES ('22', 'unidadesMedidas', '1');
INSERT INTO `modulos` VALUES ('23', 'monedas', '1');
INSERT INTO `modulos` VALUES ('24', 'materiasPrimas', '1');
INSERT INTO `modulos` VALUES ('25', 'insumos', '1');
INSERT INTO `modulos` VALUES ('28', 'permisos', '1');
INSERT INTO `modulos` VALUES ('29', 'dashboard', '1');
INSERT INTO `modulos` VALUES ('246', 'producciones', '1');
INSERT INTO `modulos` VALUES ('247', 'rutas', '1');
INSERT INTO `modulos` VALUES ('248', 'pedidos', '1');
INSERT INTO `modulos` VALUES ('249', 'categoriasProductos', '1');
INSERT INTO `modulos` VALUES ('250', 'empresasEnvios', '1');
INSERT INTO `modulos` VALUES ('251', 'bancos', '1');
INSERT INTO `modulos` VALUES ('252', 'repartidores', '1');
INSERT INTO `modulos` VALUES ('253', 'inventario', '1');
INSERT INTO `modulos` VALUES ('257', 'sucursalesEmpresasEnvios', '1');
INSERT INTO `modulos` VALUES ('258', 'Materias Primas', '1');
INSERT INTO `modulos` VALUES ('259', 'pagos', '1');
INSERT INTO `modulos` VALUES ('260', 'ordenesServicios', '1');
INSERT INTO `modulos` VALUES ('261', 'accesos', '1');
INSERT INTO `modulos` VALUES ('262', 'modulos', '1');
INSERT INTO `modulos` VALUES ('263', 'ventas', '0');
INSERT INTO `modulos` VALUES ('264', 'Categorias de Productos', '1');
INSERT INTO `modulos` VALUES ('266', 'facturas', '1');
INSERT INTO `modulos` VALUES ('267', 'mensajesWS', '1');
INSERT INTO `modulos` VALUES ('303', 'metodos-pagos', '1');
INSERT INTO `modulos` VALUES ('305', 'presentaciones_productos', '1');
INSERT INTO `modulos` VALUES ('306', 'mamm', '0');
INSERT INTO `modulos` VALUES ('307', 'chatbot', '1');
INSERT INTO `modulos` VALUES ('308', 'preguntas-seguridad', '1');
INSERT INTO `modulos` VALUES ('309', 'ordenesEntregasPresupuestos', '1');
INSERT INTO `modulos` VALUES ('311', 'reportesEstadisticos', '1');
UNLOCK TABLES;

-- Tabla: notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_icono_notificacion` int(11) NOT NULL,
  `id_tipo_notificacion` int(11) NOT NULL,
  `tiempo_notificacion` int(11) NOT NULL,
  `titulo_notificacion` varchar(30) NOT NULL,
  `texto_notificacion` varchar(255) NOT NULL,
  `fecha_creacion_notificacion` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_notificacion`),
  KEY `cedula_usuario_notificaciones_fk` (`cedula_usuario`),
  KEY `icono_notificacion_notificaciones_fk` (`id_icono_notificacion`),
  KEY `id_tipo_notificacion_notificaciones_fk` (`id_tipo_notificacion`),
  CONSTRAINT `cedula_usuario_notificaciones_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `icono_notificacion_notificaciones_fk` FOREIGN KEY (`id_icono_notificacion`) REFERENCES `iconos_notificaciones` (`id_icono_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_tipo_notificacion_notificaciones_fk` FOREIGN KEY (`id_tipo_notificacion`) REFERENCES `tipos_notificaciones` (`id_tipo_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=406 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `notificaciones` WRITE;
INSERT INTO `notificaciones` VALUES ('398', 'V30485684', '1', '2', '0', 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:49:57', '0');
INSERT INTO `notificaciones` VALUES ('399', 'V30485684', '1', '2', '0', 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:50:09', '0');
INSERT INTO `notificaciones` VALUES ('400', 'V30485684', '1', '2', '0', 'Permisos', 'Un permiso ha sido registrado en el sistema.', '2026-08-08 13:50:22', '0');
INSERT INTO `notificaciones` VALUES ('401', 'V30485684', '1', '2', '0', 'Permisos', 'Un permiso ha sido eliminado del sistema.', '2026-08-08 13:50:26', '0');
INSERT INTO `notificaciones` VALUES ('402', 'V30485681', '1', '2', '0', 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', '1');
INSERT INTO `notificaciones` VALUES ('403', 'V30485684', '1', '2', '0', 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', '0');
INSERT INTO `notificaciones` VALUES ('404', 'V30485684', '1', '2', '0', 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-16 19:00:03', '0');
INSERT INTO `notificaciones` VALUES ('405', 'V30485684', '1', '2', '0', 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-09-16 09:49:09', '1');
UNLOCK TABLES;

-- Tabla: permisos
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_permiso` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_permiso`),
  KEY `nombre_permiso_indice` (`nombre_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=678 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `permisos` WRITE;
INSERT INTO `permisos` VALUES ('618', 'ver', '1');
INSERT INTO `permisos` VALUES ('619', 'listar', '1');
INSERT INTO `permisos` VALUES ('620', 'registrar', '1');
INSERT INTO `permisos` VALUES ('621', 'actualizar', '1');
INSERT INTO `permisos` VALUES ('622', 'eliminar', '1');
INSERT INTO `permisos` VALUES ('623', 'ver dashboard', '1');
INSERT INTO `permisos` VALUES ('624', 'ver historial de cambio', '1');
INSERT INTO `permisos` VALUES ('625', 'actualizar cambio de divisas', '1');
INSERT INTO `permisos` VALUES ('626', 'ver historial de cambio del iva', '1');
INSERT INTO `permisos` VALUES ('627', 'actualizar cambio del iva', '1');
INSERT INTO `permisos` VALUES ('628', 'ver detalles de las ventas', '1');
INSERT INTO `permisos` VALUES ('629', 'ver ventas despachadas', '1');
INSERT INTO `permisos` VALUES ('630', 'ver ventas sin cancelar', '1');
INSERT INTO `permisos` VALUES ('631', 'ver pedidos en espera', '1');
INSERT INTO `permisos` VALUES ('632', 'ver pedidos rechazados', '1');
INSERT INTO `permisos` VALUES ('633', 'gestionar ventas', '1');
INSERT INTO `permisos` VALUES ('634', 'ver reportes', '1');
INSERT INTO `permisos` VALUES ('635', 'imprimir reportes de ventas', '1');
INSERT INTO `permisos` VALUES ('636', 'imprimir reportes de productos', '1');
INSERT INTO `permisos` VALUES ('637', 'imprimir comandas', '1');
INSERT INTO `permisos` VALUES ('638', 'asignar roles a usuarios', '1');
INSERT INTO `permisos` VALUES ('639', 'ver el precio del dólar', '1');
INSERT INTO `permisos` VALUES ('640', 'ver notificaciones', '1');
INSERT INTO `permisos` VALUES ('641', 'ver modal de ayuda', '1');
INSERT INTO `permisos` VALUES ('642', 'ver carrito de compra', '1');
INSERT INTO `permisos` VALUES ('643', 'ver inventario', '1');
INSERT INTO `permisos` VALUES ('644', 'ver detalles de promociones', '1');
INSERT INTO `permisos` VALUES ('645', 'ver bitácora', '1');
INSERT INTO `permisos` VALUES ('646', 'PERMISOSN', '0');
INSERT INTO `permisos` VALUES ('647', 'ver detalles de pedidos propios', '1');
INSERT INTO `permisos` VALUES ('648', 'cambiar estado de los pedidos', '1');
INSERT INTO `permisos` VALUES ('649', 'cancelar pedidos', '1');
INSERT INTO `permisos` VALUES ('650', 'despachar pedidos', '1');
INSERT INTO `permisos` VALUES ('651', 'ver pedidos propios', '1');
INSERT INTO `permisos` VALUES ('652', 'ver pedidos de los clientes', '1');
INSERT INTO `permisos` VALUES ('653', 'imprimir pedidos', '1');
INSERT INTO `permisos` VALUES ('654', 'registrar cargas o descargas de productos', '1');
INSERT INTO `permisos` VALUES ('655', 'ver historial de e/s de los productos', '1');
INSERT INTO `permisos` VALUES ('656', 'registrar cargas o descargas de materias primas', '1');
INSERT INTO `permisos` VALUES ('657', 'ver historial de e/s de las materias primas', '1');
INSERT INTO `permisos` VALUES ('658', 'ver permisos generales', '1');
INSERT INTO `permisos` VALUES ('659', 'ver permisos especiales', '1');
INSERT INTO `permisos` VALUES ('660', 'actualizar permisos', '1');
INSERT INTO `permisos` VALUES ('662', 'asignar repartidores a pedidos', '1');
INSERT INTO `permisos` VALUES ('663', 'ver detalles de los productos', '1');
INSERT INTO `permisos` VALUES ('664', 'ver historial de cambio de las divisas', '1');
INSERT INTO `permisos` VALUES ('665', 'listar permisos', '1');
INSERT INTO `permisos` VALUES ('666', 'ver chatbot', '1');
INSERT INTO `permisos` VALUES ('667', 'enviar mensaje chatbot', '1');
INSERT INTO `permisos` VALUES ('668', 'imprimir reportes de anomalias de productos', '1');
INSERT INTO `permisos` VALUES ('669', 'imprimir reportes de anomalias de materias primas', '1');
INSERT INTO `permisos` VALUES ('670', 'anular', '1');
INSERT INTO `permisos` VALUES ('671', 'despachar orden', '1');
INSERT INTO `permisos` VALUES ('672', 'agregar pago', '1');
INSERT INTO `permisos` VALUES ('673', 'permisox', '0');
INSERT INTO `permisos` VALUES ('674', 'ver reportes estadísticos', '1');
INSERT INTO `permisos` VALUES ('675', 'agregar cliente', '1');
INSERT INTO `permisos` VALUES ('676', 'ver detalles de los clientes', '1');
INSERT INTO `permisos` VALUES ('677', 'ver detalles de los repartidores', '1');
UNLOCK TABLES;

-- Tabla: preguntas_seguridad
DROP TABLE IF EXISTS `preguntas_seguridad`;
CREATE TABLE `preguntas_seguridad` (
  `id_pregunta` varchar(20) NOT NULL,
  `texto_pregunta` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_pregunta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `preguntas_seguridad` WRITE;
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00001-26', '¿Cuál es tu comida favorita?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00002-21', '¿En qué calle vivías cuando tenías diez años?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00003-77', '¿Cómo se llamaba tu primera mascota?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00004-61', '¿Cuál es el segundo nombre de tu abuelo materno?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00005-63', '¿Cómo se llamaba tu primer maestro de escuela?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00006-60', '¿Cuál era tu apodo favorito durante la infancia?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00007-27', '¿Cuál fue el primer modelo de auto que tuviste?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00008-61', '¿A qué ciudad viajaste en tu primer vuelo en avión?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00009-75', '¿Cuál es el título de tu libro favorito de la infancia?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00010-46', '¿Cuál fue el primer concierto de música al que asististe?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00011-43', '¿En qué hotel te hospedaste durante tus vacaciones favoritas?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00012-08', '¿Cómo se llamaba la primera empresa donde trabajaste?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00013-26', '¿Cuál era el nombre de la mascota de tu universidad?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00014-96', '¿En qué ciudad se conocieron tus padres?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00015-02', '¿Cuál fue el primer país extranjero que visitaste?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26210-00016-12', '¿Cómo se llamaba el hospital donde naciste?', '1');
INSERT INTO `preguntas_seguridad` VALUES ('PREG-26259-00001-35', '¿Cómo se llamaba tu primera mascota00?', '0');
UNLOCK TABLES;

-- Tabla: preguntas_seguridad_usuarios
DROP TABLE IF EXISTS `preguntas_seguridad_usuarios`;
CREATE TABLE `preguntas_seguridad_usuarios` (
  `id_pregunta_usuario` varchar(20) NOT NULL,
  `id_pregunta` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `respuesta_pregunta` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_pregunta_usuario`),
  KEY `id_pregunta_preguntas_seguridad_usuarios_fk` (`id_pregunta`),
  KEY `cedula_usuario_preguntas_seguridad_usuarios_fk` (`cedula_usuario`),
  CONSTRAINT `cedula_usuario_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_pregunta_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_seguridad` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `preguntas_seguridad_usuarios` WRITE;
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00001-85', 'PREG-26210-00001-26', 'V30485684', '$2y$10$oSAFQLHBQ.f0yXrP2EHvYOWmXbFQEK6ddsUSES5kZuRfvHTGrvkLO', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00002-33', 'PREG-26210-00003-77', 'V30485684', '$2y$10$ESrPpMPFFnpSTAKCOiAqVOApkQ7In.nwsj4H8oVj1nk5GTb96i7OW', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00003-13', 'PREG-26210-00015-02', 'V30485684', '$2y$10$CJ4N3mC.78fqwCK4eOvaFOK5MqaS5cIeAldIZ4N8NHnnwOrZGunW6', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00004-32', 'PREG-26210-00013-26', 'V30485684', '$2y$10$AAs.exVU1/hT.kK/2wYnRumBj0/81k6la0doYE7P81IEKCENLZ2Yi', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00005-91', 'PREG-26210-00012-08', 'V30485684', '$2y$10$nEuTtqbC4VHg/Fq7MjHDJuo8pAjGVNj.MQ1/DvKaH5lVu6FeminVe', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26213-00006-15', 'PREG-26210-00010-46', 'V30485684', '$2y$10$.pudhcTVmpJqP56lu1guxOCQpi8czdVNALQv04oklr6X3byw/GHly', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00001-41', 'PREG-26210-00001-26', 'V30485688', '$2y$10$kkIvKOeLqnsJjPepiPbPaObftz5zu.Utme0AAci7k9jqbCwNdxb7.', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00002-20', 'PREG-26210-00002-21', 'V30485688', '$2y$10$WXVuBpVC/aD2eJjIjiGOzu5rMVQU9i.LDG77RAib4sAlNRE5hLgDG', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00003-40', 'PREG-26210-00003-77', 'V30485688', '$2y$10$xplVAHINlxboKlbtghJQFOGG/93Wa0ZmgDSIXQe7ymr.mRtfNYl8i', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00004-31', 'PREG-26210-00015-02', 'V30485688', '$2y$10$97U9ZxMj0mPK/rtsBgTM.e6A33jx6DFV./yd6R5aHTD9pVEpI4CEa', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00005-05', 'PREG-26210-00014-96', 'V30485688', '$2y$10$.vWhvgbmkDn0wSvhLpu5p.YRkVpg.Qy1r5sEV4REGFFmhSaPvJJoe', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00006-70', 'PREG-26210-00012-08', 'V30485688', '$2y$10$MSyyqq8/wDJedr3JCR/GeuXLtDX2fE4z/XR1LEAV4rWAdodyVmRYG', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00007-22', 'PREG-26210-00001-26', 'V30485681', '$2y$10$wU8cYibX8NLN27pihZMW4Obl1MtwCq60TaylCpisI7k3graGFoaHu', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00008-99', 'PREG-26210-00002-21', 'V30485681', '$2y$10$oCY9ov16IiozchUkb/jkhOZnuIwVC4HRPtkcjLoGoBQxkUDJW2auy', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00009-61', 'PREG-26210-00003-77', 'V30485681', '$2y$10$EJGejQQ/q8/bHep25huPnO9AKIXVy9q0Pkt/WV/rah7C4rBlUwKoq', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00010-42', 'PREG-26210-00014-96', 'V30485681', '$2y$10$F0WYELHGHtHBGMWGEJvE.ehIVVz5VMtDaa1x38KCl6Eh.Yx84sf1C', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00011-68', 'PREG-26210-00012-08', 'V30485681', '$2y$10$b3u2pHbQ24nWvTq8Tg8DXep2j6CQYU1ICmfpfrMmJXVP80Z2u/r7i', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26221-00012-42', 'PREG-26210-00011-43', 'V30485681', '$2y$10$caw/tqyKBEMy7cZcRrMcMePBXzmAwEgzkbctT.h3Ax9ilGYtgfdv6', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00001-58', 'PREG-26210-00001-26', 'V30485631', '$2y$10$XtK3dfbu27H6smxaIwNBDuC/W3J5esegFOxzGYqmlYZUxMFOIF2qO', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00002-60', 'PREG-26210-00002-21', 'V30485631', '$2y$10$.Pi5SWuInsDZ/QE2EyQRGOfecbw5Rt2nOcMuKd4D4bPf/QkkP.bTC', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00003-26', 'PREG-26210-00003-77', 'V30485631', '$2y$10$AyfEGBCe5hHBQU/ZRmy9..7kMSEcOG1C2bX1Z/QVHhqPFJx6r6j0.', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00004-88', 'PREG-26210-00004-61', 'V30485631', '$2y$10$twjRhlLjk2E138yreZfCY.HfGtcd6YwS1OlrvG5.enL69ltKE1S5K', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00005-55', 'PREG-26210-00005-63', 'V30485631', '$2y$10$kuqj.R/XY26DnbiVRS7BXe9dVd2waEePUzG8EXNNnSXQogsJIk7ne', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00006-84', 'PREG-26210-00006-60', 'V30485631', '$2y$10$gvzXH7dcsGLwu2mlW2ILm.W0zAXRJKJgHelsEIyS23o.wmIK9aFfa', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00007-21', 'PREG-26210-00001-26', 'V30485688', '$2y$10$3gcuhRdZD7BKvZZDgFvKXOGwzupUMpukIjUFl/jbohDXTxXBhlSBa', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00008-47', 'PREG-26210-00003-77', 'V30485688', '$2y$10$h7IURdkgkEwdeHo5k7CAm.r.I69Uc6vL1/k3P1S5coycthy0E40Q.', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00009-82', 'PREG-26210-00002-21', 'V30485688', '$2y$10$MDb9SYu2UCMArqfd5KrEfu.PcIApHsjvAGJ72uAF7ragC0RUF7p4i', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00010-26', 'PREG-26210-00014-96', 'V30485688', '$2y$10$7jHbnoRdBc5eZB0qMCQWAecGqkflPj1BWblxToi9W4KwFvxPUeyTC', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00011-13', 'PREG-26210-00015-02', 'V30485688', '$2y$10$ca9XzzUtY.A5pOjuEIN8O.P5NXZ18ugezQA1IISYT01Fymg/LrC0W', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26227-00012-54', 'PREG-26210-00012-08', 'V30485688', '$2y$10$S1Du8BywViYS1DV21mBfBu0v7nGbGGJwHe2CVMGoE8I9ThQNfAZqm', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00001-63', 'PREG-26210-00001-26', 'V12345666', '$2y$10$MPbzScNScySHDPcwaMrugOA6Rvra1sHY41UatlIhb3gVfBF6tyaVq', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00002-69', 'PREG-26210-00002-21', 'V12345666', '$2y$10$FWg7yQ/8iWNACeHyGhTahuvgILYPb8v1qwvCDeH2ItqjVfJioY19W', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00003-45', 'PREG-26210-00003-77', 'V12345666', '$2y$10$.sC6WdfL7JPCM2tJvvj49ej9Ke1RajHRI9MJ8Dqcik2V3SRgAp71u', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00004-13', 'PREG-26210-00004-61', 'V12345666', '$2y$10$dMyy0C8gFmcTwBbjUO9OROriksl6CvfYl38MFuIPGcpo3dXb.CSKa', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00005-45', 'PREG-26210-00005-63', 'V12345666', '$2y$10$ktTRebHKUXTSwDqhsWHN8.azrUctqnH7So3GwTyQ7V9lqTIqEjYtS', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26248-00006-18', 'PREG-26210-00006-60', 'V12345666', '$2y$10$E8LmIBg14rsAbJ57BTZIpuE4mjrEqISD0oEhOgpkMrPK3zHwklIh6', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00001-36', 'PREG-26210-00001-26', 'V30485688', '$2y$10$ePlYyiEUgMrQ8xlfdMp0fung7b9Is0MeYeIR.FNciN4lBMr/6/ih2', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00002-22', 'PREG-26210-00002-21', 'V30485688', '$2y$10$HrnpzwY5W51iSS6PlDfHvusjsZXkLeFquI8Gf8D3Yp93tmkY3xfWS', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00003-13', 'PREG-26210-00015-02', 'V30485688', '$2y$10$curiCNW2L37HbWZoWy/.cu2ZPhK3jHIrxQ3xGHpGfJVSzc.isQIBy', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00004-76', 'PREG-26210-00014-96', 'V30485688', '$2y$10$rDcXLg8EdaCAHqEbnWFxjeDNNOAd5VCoYO3C2xzCA8wlVgq8PgMYG', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00005-96', 'PREG-26210-00013-26', 'V30485688', '$2y$10$8cb9Ryb/ZC.4se.lSK/JNuk4V8bsJHExPSmMpt1Vk42jt.VOQtk.O', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00006-47', 'PREG-26210-00011-43', 'V30485688', '$2y$10$4j/l2i4uUMxzYjG19xq0Xu4Bxdegf9tkFN0T.xdVOYxn8EAYqTtwK', '1');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00007-56', 'PREG-26210-00001-26', 'V12345678', '$2y$10$wN0it2NoDg3.LhT4/eOYS.vZsGXqD9x5fUaA.lqscgsZBF3sIcCAm', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00008-80', 'PREG-26210-00002-21', 'V12345678', '$2y$10$PmYo8V0sUZTd7QqNlko1X.71cLgkJE0uncDJMQWtBgLrSVnnBnBaC', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00009-95', 'PREG-26210-00015-02', 'V12345678', '$2y$10$BBViq3HvBe3PQuV.bUA3neAfoDnpAzdLBbCWfVZsg6bDKUfnuNMj6', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00010-31', 'PREG-26210-00012-08', 'V12345678', '$2y$10$nDPKNcufT79Omgf9JLhxQOYVhErBZ14nlUkW/g5PObivQCAGpPf7W', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00011-25', 'PREG-26210-00011-43', 'V12345678', '$2y$10$BLuT/9oOiFh.V8MPl/Q/I..4WTlKAQripO7H4onO1rjWDDkIeuyNW', '0');
INSERT INTO `preguntas_seguridad_usuarios` VALUES ('PRUS-26259-00012-26', 'PREG-26210-00008-61', 'V12345678', '$2y$10$uSZKRwQetBheJjur0zZSSuix1XWrWuybAMdoK3t.z.c0nt..osCo6', '0');
UNLOCK TABLES;

-- Tabla: prompts_usuarios
DROP TABLE IF EXISTS `prompts_usuarios`;
CREATE TABLE `prompts_usuarios` (
  `id_prompt_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_usuario` varchar(20) NOT NULL,
  `prompt` text NOT NULL,
  `respuesta_bot` text NOT NULL,
  `fecha_prompt` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_prompt_usuario`),
  KEY `cedula_usuario_prompts_usuarios_fk` (`cedula_usuario`),
  CONSTRAINT `cedula_usuario_prompts_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_rol`),
  KEY `nombre_rol_indice` (`nombre_rol`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `roles` WRITE;
INSERT INTO `roles` VALUES ('1', 'SUPER ADMINISTRADOR', '1');
INSERT INTO `roles` VALUES ('2', 'ADMINISTRADOR', '1');
INSERT INTO `roles` VALUES ('3', 'CLIENTE', '1');
UNLOCK TABLES;

-- Tabla: tipos_notificaciones
DROP TABLE IF EXISTS `tipos_notificaciones`;
CREATE TABLE `tipos_notificaciones` (
  `id_tipo_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_notificacion` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_tipo_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `tipos_notificaciones` WRITE;
INSERT INTO `tipos_notificaciones` VALUES ('1', 'info', '1');
INSERT INTO `tipos_notificaciones` VALUES ('2', 'simple', '1');
UNLOCK TABLES;

-- Tabla: tokens_usuarios
DROP TABLE IF EXISTS `tokens_usuarios`;
CREATE TABLE `tokens_usuarios` (
  `id_token_usuario` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `tipo_token` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `vencimiento_token` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_token_usuario`),
  KEY `cedula_usuario_tokens_usuarios` (`cedula_usuario`),
  CONSTRAINT `cedula_usuario_tokens_usuarios` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
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
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cedula_usuario`),
  KEY `id_rol_usuarios_fk` (`id_rol`),
  CONSTRAINT `id_rol_usuarios_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `usuarios` WRITE;
INSERT INTO `usuarios` VALUES ('V12345666', '3', 'Anderson', 'Freitezjj', 'Ander123989', '$2y$10$dhukfTGqIjZ.y3ViyOzCfeGa6HTrKo9UkIja7NixkZtOKo0e5zI16', '04169484640', 'andersonfreitez69@gmail.com', 'usuarios_2026_09_06_13_06_06_87.jpg', 'BARQUISIMETO', '2026-09-06 13:06:06', '0', '0');
INSERT INTO `usuarios` VALUES ('V12345678', '3', 'Ander', 'Freitez', 'ANDER1235', '$2y$10$kMO1AxVdCN5OjhkCqaK1veCxUUuCrZprzS/bRLuvTZ9D5eLGCSkiC', '04160000000', 'ander@gmail.com', '', 'QUIBOR', '2026-09-17 17:46:50', '0', '0');
INSERT INTO `usuarios` VALUES ('V30485631', '3', 'Ander', 'Mendoza', 'Ander1234', '$2y$10$q1kepCAa6lqPBN76ncHveuNSFzsU8azGLPCUADq2h13tB2hUahpMG', '04160484640', 'andersonfreitez63@gmail.com', 'usuarios_2026-08-16_19_49_47.jpg?v=2026-08-16_19_49_47', 'El Molino', '2026-08-16 17:55:27', '0', '0');
INSERT INTO `usuarios` VALUES ('V30485681', '1', 'Anderson', 'Freitez', 'Ander1236', '$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk/y0bWnjNde', '04169484643', 'andersonfreitez66@gmail.com', 'usuarios_2026_08_10_16_58_55_14.jpg', 'BARQUISIMETO', '2026-08-11 04:58:55', '0', '0');
INSERT INTO `usuarios` VALUES ('V30485684', '1', 'Anderson', 'Freitez', 'Ander123', '$2y$10$lvgwVqGtaGEq/M9hBWs13.7TQKMxtcsJ8DgJXu2UMZ8XOx4K8dV5e', '04169484649', 'andersonfreitez6@gmail.com', '', 'SANARE', '2026-09-18 11:48:40', '0', '1');
INSERT INTO `usuarios` VALUES ('V30485688', '3', 'Anderson', 'Freitez', 'Ander1239', '$2y$10$0Y9xE0tZKb4bIHmlzrpv9.xihEL9qCtYfzzc6.sYE7RZQF.TfoCta', '04169484647', 'andersonfreite9z6@gmail.com', 'usuarios_2026_09_17_17_41_57_45.jpg', 'BARQUISIMETO', '2026-08-11 04:45:15', '0', '1');
UNLOCK TABLES;

-- Tabla: v_usuarios_todos
DROP TABLE IF EXISTS `v_usuarios_todos`;
-- ERROR CREATE TABLE: Undefined array key "Create Table"

