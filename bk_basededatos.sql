-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para citas_medicas_db
CREATE DATABASE IF NOT EXISTS `citas_medicas_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `citas_medicas_db`;

-- Volcando estructura para tabla citas_medicas_db.archivos_paciente
CREATE TABLE IF NOT EXISTS `archivos_paciente` (
  `id_archivo` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_archivo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_archivo`),
  KEY `id_paciente` (`id_paciente`),
  CONSTRAINT `archivos_paciente_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.archivos_paciente: ~0 rows (aproximadamente)
DELETE FROM `archivos_paciente`;

-- Volcando estructura para tabla citas_medicas_db.auditoria
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tabla_afectada` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_registro_afectado` int DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `ip_usuario` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.auditoria: ~0 rows (aproximadamente)
DELETE FROM `auditoria`;

-- Volcando estructura para tabla citas_medicas_db.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_medico` int NOT NULL,
  `id_servicio` int DEFAULT NULL,
  `fecha_cita` datetime NOT NULL,
  `motivo` text COLLATE utf8mb4_general_ci,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `presion_arterial` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diagnostico` text COLLATE utf8mb4_general_ci,
  `prescripcion` text COLLATE utf8mb4_general_ci,
  `dias_reposo` int DEFAULT '0',
  `fecha_fin_reposo` date DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Finalizada') COLLATE utf8mb4_general_ci DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cita`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_medico` (`id_medico`),
  KEY `fk_cita_servicio` (`id_servicio`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`),
  CONSTRAINT `fk_cita_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.citas: ~2 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_medico`, `id_servicio`, `fecha_cita`, `motivo`, `peso`, `talla`, `temperatura`, `presion_arterial`, `diagnostico`, `prescripcion`, `dias_reposo`, `fecha_fin_reposo`, `estado`, `created_at`) VALUES
	(1, 14, 1, 2, '2025-12-29 09:00:00', 'cita 1', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Pendiente', '2025-12-21 05:24:52'),
	(2, 14, 1, 2, '2025-12-29 09:30:00', 'cita 2', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Pendiente', '2025-12-21 05:33:25'),
	(3, 17, 2, 2, '2025-12-29 13:30:00', 'cita 3', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Pendiente', '2025-12-21 06:34:12');

-- Volcando estructura para tabla citas_medicas_db.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL,
  `nombre_clinica` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `moneda` varchar(5) COLLATE utf8mb4_general_ci DEFAULT 'S/.',
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.configuracion: ~1 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `nombre_clinica`, `direccion`, `telefono`, `email`, `moneda`, `logo`) VALUES
	(1, 'Centro Médico Salud', 'Av. Principal 123, Lima', '(01) 555-0000', 'contacto@saludtotal.com', 'S/.', 'logo_clinica_1772176162.png');

-- Volcando estructura para tabla citas_medicas_db.especialidades
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id_especialidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.especialidades: ~2 rows (aproximadamente)
DELETE FROM `especialidades`;
INSERT INTO `especialidades` (`id_especialidad`, `nombre`, `estado`) VALUES
	(1, 'MEDICINA GENERAL', 1),
	(2, 'PEDIATRIA', 1);

-- Volcando estructura para tabla citas_medicas_db.gastos
CREATE TABLE IF NOT EXISTS `gastos` (
  `id_gasto` int NOT NULL AUTO_INCREMENT,
  `id_sesion` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_gasto` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gasto`),
  KEY `id_sesion` (`id_sesion`),
  CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`id_sesion`) REFERENCES `sesiones_caja` (`id_sesion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.gastos: ~0 rows (aproximadamente)
DELETE FROM `gastos`;
INSERT INTO `gastos` (`id_gasto`, `id_sesion`, `descripcion`, `monto`, `fecha_gasto`) VALUES
	(1, 1, 'pasaje medico 1', 10.00, '2025-12-21 01:42:15');

-- Volcando estructura para tabla citas_medicas_db.horarios_medicos
CREATE TABLE IF NOT EXISTS `horarios_medicos` (
  `id_horario` int NOT NULL AUTO_INCREMENT,
  `id_medico` int NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') COLLATE utf8mb4_general_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_medico` (`id_medico`),
  CONSTRAINT `horarios_medicos_ibfk_1` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.horarios_medicos: ~3 rows (aproximadamente)
DELETE FROM `horarios_medicos`;
INSERT INTO `horarios_medicos` (`id_horario`, `id_medico`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
	(1, 1, 'Lunes', '08:00:00', '10:00:00'),
	(2, 1, 'Martes', '11:00:00', '14:00:00'),
	(3, 2, 'Lunes', '11:30:00', '17:00:00');

-- Volcando estructura para tabla citas_medicas_db.medicamentos
CREATE TABLE IF NOT EXISTS `medicamentos` (
  `id_medicamento` int NOT NULL AUTO_INCREMENT,
  `nombre_comercial` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_generico` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `presentacion` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock` int DEFAULT '0',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  PRIMARY KEY (`id_medicamento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.medicamentos: ~0 rows (aproximadamente)
DELETE FROM `medicamentos`;
INSERT INTO `medicamentos` (`id_medicamento`, `nombre_comercial`, `nombre_generico`, `presentacion`, `stock`, `estado`) VALUES
	(1, 'panadol', '', 'caja 100mg', 10, 'Activo');

-- Volcando estructura para tabla citas_medicas_db.medicos
CREATE TABLE IF NOT EXISTS `medicos` (
  `id_medico` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_especialidad` int NOT NULL,
  `colegiatura` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_medico`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_especialidad` (`id_especialidad`),
  CONSTRAINT `medicos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `medicos_ibfk_2` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.medicos: ~2 rows (aproximadamente)
DELETE FROM `medicos`;
INSERT INTO `medicos` (`id_medico`, `id_usuario`, `id_especialidad`, `colegiatura`) VALUES
	(1, 12, 1, '34567'),
	(2, 13, 2, '4667');

-- Volcando estructura para tabla citas_medicas_db.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_cita` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  KEY `id_cita` (`id_cita`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.pagos: ~0 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id_pago`, `id_cita`, `monto`, `metodo_pago`, `observaciones`, `fecha_pago`) VALUES
	(1, 1, 60.00, 'Efectivo', 'pagado', '2025-12-21 01:41:54');

-- Volcando estructura para tabla citas_medicas_db.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.roles: ~3 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
	(1, 'Administrador'),
	(2, 'Medico'),
	(3, 'Paciente'),
	(4, 'Recepcionista');

-- Volcando estructura para tabla citas_medicas_db.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre_servicio` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `precio` decimal(10,2) NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.servicios: ~0 rows (aproximadamente)
DELETE FROM `servicios`;
INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion`, `precio`, `estado`) VALUES
	(2, 'CONSULTA GENERAL', '', 60.00, 'Activo');

-- Volcando estructura para tabla citas_medicas_db.sesiones_caja
CREATE TABLE IF NOT EXISTS `sesiones_caja` (
  `id_sesion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_general_ci DEFAULT 'abierta',
  `observaciones` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_sesion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.sesiones_caja: ~0 rows (aproximadamente)
DELETE FROM `sesiones_caja`;
INSERT INTO `sesiones_caja` (`id_sesion`, `id_usuario`, `monto_apertura`, `monto_cierre`, `fecha_apertura`, `fecha_cierre`, `estado`, `observaciones`) VALUES
	(1, 3, 0.00, NULL, '2025-12-21 01:36:23', NULL, 'abierta', NULL);

-- Volcando estructura para tabla citas_medicas_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `documento_identidad` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_general_ci,
  `enfermedades_cronicas` text COLLATE utf8mb4_general_ci,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_rol` int DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.usuarios: ~7 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `documento_identidad`, `email`, `telefono`, `grupo_sanguineo`, `alergias`, `enfermedades_cronicas`, `password`, `avatar`, `id_rol`, `estado`, `fecha_creacion`) VALUES
	(3, 'Administrador Principal', NULL, 'admin@medico.com', NULL, NULL, NULL, NULL, '$2y$10$4b1F8dm6AbyBMzb5lfjEe.5fqJbN11cn5p.kHtki4xZCggDtQxQlm', NULL, 1, 1, '2025-11-29 14:40:16'),
	(12, 'MEDICO 1', NULL, 'medico1@medico.com', NULL, NULL, NULL, NULL, '$2y$10$JdTtbBvxg3Wao8j0p0iJoOrCuGv2MrW88kldSERY0Q87zxWiClTda', NULL, 2, 1, '2025-12-20 17:06:19'),
	(13, 'MEDICO 2', NULL, 'medico2@medico.com', NULL, NULL, NULL, NULL, '$2y$10$fxdFurftBT9Ad149Ar8f8u6mbVFJdd1yZMdMqrJibthHzI47ylODW', NULL, 2, 1, '2025-12-20 17:06:58'),
	(14, 'paciente 1', '21212121', 'paciente1@correo.com', '70707071', 'O+', 'ninguna', 'ninguna', '$2y$10$MJslqa1IrCEl18Bv9.s/XOjLA7R0vIC.naTbodcc2QZnWux.mAC2W', NULL, 3, 1, '2025-12-21 05:03:51'),
	(15, 'paciente 2', '60606061', 'paciente2@correo.com', '90909090', 'A+', 'ningunas', 'ninguna', '$2y$10$oQqUReUKb2X3F1jr9atrs.RekC1h.Q5MB88kxWkGdCGkZ06L8fJl.', NULL, 3, 1, '2025-12-21 05:36:50'),
	(16, 'paciente 3', '70707070', 'paciente3@correo.com', '90909090', 'O-', 'ninguna', 'ninguna', '$2y$10$IOKTqg7FJvuYW9q/Ywu9wewrB4fgIgxA8TsBED03ilnBhb9usZ5eW', NULL, 3, 1, '2025-12-21 05:56:47'),
	(17, 'paciente 4', '50505050', 'paciente4@correo.com', '9191919191', 'AB-', 'ninguna', 'ninguna', '$2y$10$ULY54t5OoVgy.H2EuqN34.OTR7464T.IuyeEuU8IumxZZpb0dgA.e', NULL, 3, 1, '2025-12-21 06:30:12');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
