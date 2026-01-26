-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-01-2026 a las 03:56:12
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
-- Base de datos: `scrimsync`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidad`
--

CREATE TABLE `disponibilidad` (
  `dis_id` int(11) NOT NULL,
  `equ_id` int(11) NOT NULL,
  `dis_dia_semana` tinyint(4) NOT NULL COMMENT '1=Lunes ... 7=Domingo',
  `dis_hora_inicio` time NOT NULL,
  `dis_hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `equ_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL COMMENT 'Capitán/Dueño',
  `jue_id` int(11) NOT NULL,
  `equ_nombre` varchar(50) NOT NULL,
  `equ_logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_scrim`
--

CREATE TABLE `estado_scrim` (
  `est_id` int(11) NOT NULL,
  `est_descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `gen_id` int(11) NOT NULL,
  `gen_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juego`
--

CREATE TABLE `juego` (
  `jue_id` int(11) NOT NULL,
  `jue_nombre` varchar(50) NOT NULL,
  `gen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `not_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `equ_id` int(11) DEFAULT NULL,
  `scr_id` int(11) DEFAULT NULL,
  `per_id` int(11) DEFAULT NULL,
  `not_tipo` varchar(20) NOT NULL COMMENT 'INVITACION, SCRIM, SISTEMA',
  `not_asunto` varchar(50) NOT NULL,
  `not_mensaje` varchar(255) NOT NULL,
  `not_estado_leido` tinyint(1) DEFAULT 0 COMMENT '0: No, 1: Si',
  `not_fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso_equipo`
--

CREATE TABLE `permiso_equipo` (
  `per_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `equ_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `per_modif_horario` tinyint(1) DEFAULT 0,
  `per_enviar_scrim` tinyint(1) DEFAULT 0,
  `per_elim_miembro` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_predefinido`
--

CREATE TABLE `rol_predefinido` (
  `rol_id` int(11) NOT NULL,
  `rol_nombre` varchar(50) NOT NULL,
  `jue_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scrim`
--

CREATE TABLE `scrim` (
  `scr_id` int(11) NOT NULL,
  `equ_id_emisor` int(11) NOT NULL,
  `equ_id_receptor` int(11) NOT NULL,
  `est_id` int(11) NOT NULL,
  `scr_fecha_envio` datetime DEFAULT current_timestamp(),
  `scr_fecha_juego` date NOT NULL,
  `scr_hora_inicio` time NOT NULL,
  `scr_hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usu_id` int(11) NOT NULL,
  `usu_tipo` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Admin, 1: Jugador',
  `usu_username` varchar(50) NOT NULL,
  `usu_email` varchar(100) NOT NULL,
  `usu_password` varchar(255) NOT NULL COMMENT 'Hash de seguridad',
  `usu_alias` varchar(50) NOT NULL,
  `usu_descripcion` text DEFAULT NULL,
  `usu_foto` varchar(255) DEFAULT NULL COMMENT 'Ruta relativa o URL',
  `usu_fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usu_id`, `usu_tipo`, `usu_username`, `usu_email`, `usu_password`, `usu_alias`, `usu_descripcion`, `usu_foto`, `usu_fecha_registro`) VALUES
(1, 0, 'Admin', 'admin@scrimsync.com', '$2y$10$kUijeCLGjn2INOqU.sXezeUPsaZFF4qNkf3dTz4qPQgctSr5kaQZS', 'SystemAdmin', 'Cuenta principal del sistema', NULL, '2026-01-20 22:44:57'),
(2, 1, 'chucrut', 'chucrut@test.com', '$2y$10$qvjZ10SiiIYz8g4UgO/V1ejllQskhlqKJaloc4kuuVByZBoSWP9qm', 'chucrut', NULL, NULL, '2026-01-20 23:30:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD PRIMARY KEY (`dis_id`),
  ADD KEY `fk_disp_equipo` (`equ_id`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`equ_id`),
  ADD UNIQUE KEY `equ_nombre` (`equ_nombre`),
  ADD KEY `fk_equipo_usuario` (`usu_id`),
  ADD KEY `fk_equipo_juego` (`jue_id`);

--
-- Indices de la tabla `estado_scrim`
--
ALTER TABLE `estado_scrim`
  ADD PRIMARY KEY (`est_id`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`gen_id`),
  ADD UNIQUE KEY `gen_nombre` (`gen_nombre`);

--
-- Indices de la tabla `juego`
--
ALTER TABLE `juego`
  ADD PRIMARY KEY (`jue_id`),
  ADD UNIQUE KEY `jue_nombre` (`jue_nombre`),
  ADD KEY `fk_juego_genero` (`gen_id`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`not_id`),
  ADD KEY `fk_not_usuario` (`usu_id`),
  ADD KEY `fk_not_equipo` (`equ_id`),
  ADD KEY `fk_not_scrim` (`scr_id`),
  ADD KEY `fk_not_permiso` (`per_id`);

--
-- Indices de la tabla `permiso_equipo`
--
ALTER TABLE `permiso_equipo`
  ADD PRIMARY KEY (`per_id`),
  ADD KEY `fk_perm_usuario` (`usu_id`),
  ADD KEY `fk_perm_equipo` (`equ_id`),
  ADD KEY `fk_perm_rol` (`rol_id`);

--
-- Indices de la tabla `rol_predefinido`
--
ALTER TABLE `rol_predefinido`
  ADD PRIMARY KEY (`rol_id`),
  ADD KEY `fk_rol_juego` (`jue_id`);

--
-- Indices de la tabla `scrim`
--
ALTER TABLE `scrim`
  ADD PRIMARY KEY (`scr_id`),
  ADD KEY `fk_scrim_emisor` (`equ_id_emisor`),
  ADD KEY `fk_scrim_receptor` (`equ_id_receptor`),
  ADD KEY `fk_scrim_estado` (`est_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usu_id`),
  ADD UNIQUE KEY `usu_username` (`usu_username`),
  ADD UNIQUE KEY `usu_email` (`usu_email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  MODIFY `dis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `equ_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_scrim`
--
ALTER TABLE `estado_scrim`
  MODIFY `est_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `gen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `juego`
--
ALTER TABLE `juego`
  MODIFY `jue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `not_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permiso_equipo`
--
ALTER TABLE `permiso_equipo`
  MODIFY `per_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol_predefinido`
--
ALTER TABLE `rol_predefinido`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `scrim`
--
ALTER TABLE `scrim`
  MODIFY `scr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD CONSTRAINT `fk_disp_equipo` FOREIGN KEY (`equ_id`) REFERENCES `equipo` (`equ_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `fk_equipo_juego` FOREIGN KEY (`jue_id`) REFERENCES `juego` (`jue_id`),
  ADD CONSTRAINT `fk_equipo_usuario` FOREIGN KEY (`usu_id`) REFERENCES `usuario` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `juego`
--
ALTER TABLE `juego`
  ADD CONSTRAINT `fk_juego_genero` FOREIGN KEY (`gen_id`) REFERENCES `genero` (`gen_id`);

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_not_equipo` FOREIGN KEY (`equ_id`) REFERENCES `equipo` (`equ_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_not_permiso` FOREIGN KEY (`per_id`) REFERENCES `permiso_equipo` (`per_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_not_scrim` FOREIGN KEY (`scr_id`) REFERENCES `scrim` (`scr_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_not_usuario` FOREIGN KEY (`usu_id`) REFERENCES `usuario` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permiso_equipo`
--
ALTER TABLE `permiso_equipo`
  ADD CONSTRAINT `fk_perm_equipo` FOREIGN KEY (`equ_id`) REFERENCES `equipo` (`equ_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_perm_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol_predefinido` (`rol_id`),
  ADD CONSTRAINT `fk_perm_usuario` FOREIGN KEY (`usu_id`) REFERENCES `usuario` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rol_predefinido`
--
ALTER TABLE `rol_predefinido`
  ADD CONSTRAINT `fk_rol_juego` FOREIGN KEY (`jue_id`) REFERENCES `juego` (`jue_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `scrim`
--
ALTER TABLE `scrim`
  ADD CONSTRAINT `fk_scrim_emisor` FOREIGN KEY (`equ_id_emisor`) REFERENCES `equipo` (`equ_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_scrim_estado` FOREIGN KEY (`est_id`) REFERENCES `estado_scrim` (`est_id`),
  ADD CONSTRAINT `fk_scrim_receptor` FOREIGN KEY (`equ_id_receptor`) REFERENCES `equipo` (`equ_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
