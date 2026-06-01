-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-06-2026 a las 19:00:42
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
-- Base de datos: `crm_ventas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo`
--

DROP TABLE IF EXISTS `catalogo`;
CREATE TABLE `catalogo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('Producto','Servicio') NOT NULL,
  `desc_corta` varchar(255) NOT NULL,
  `desc_larga` text NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_precios`
--

DROP TABLE IF EXISTS `catalogo_precios`;
CREATE TABLE `catalogo_precios` (
  `id` int(11) NOT NULL,
  `catalogo_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `fecha_vigencia` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `prospecto_id` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `google_event_id` varchar(255) DEFAULT NULL,
  `estado` enum('Pendiente','Realizada','Cancelada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_corporativas`
--

DROP TABLE IF EXISTS `metas_corporativas`;
CREATE TABLE `metas_corporativas` (
  `id` int(11) NOT NULL,
  `etapa` int(11) NOT NULL,
  `meta_diaria` int(11) NOT NULL,
  `rango_verde_min` int(11) NOT NULL,
  `rango_amarillo_min` int(11) NOT NULL,
  `rango_rojo_max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metas_corporativas`
--

INSERT INTO `metas_corporativas` (`id`, `etapa`, `meta_diaria`, `rango_verde_min`, `rango_amarillo_min`, `rango_rojo_max`) VALUES
(17, 1, 25, 80, 41, 40),
(18, 2, 10, 80, 41, 40),
(19, 3, 5, 80, 41, 40),
(20, 4, 5, 80, 41, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_vendedor`
--

DROP TABLE IF EXISTS `metas_vendedor`;
CREATE TABLE `metas_vendedor` (
  `id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `etapa` int(11) NOT NULL,
  `rango_verde_min` int(11) NOT NULL,
  `rango_amarillo_min` int(11) NOT NULL,
  `rango_rojo_max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prospectos`
--

DROP TABLE IF EXISTS `prospectos`;
CREATE TABLE `prospectos` (
  `id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `etapa` int(11) DEFAULT 1,
  `nombre` varchar(150) NOT NULL,
  `comuna` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `rut` varchar(12) DEFAULT NULL,
  `genero` enum('M','F','Otro') DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol` enum('Administrador','Vendedor') NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol`, `nombre`, `email`) VALUES
(1, 'Administrador', 'Admin', 'admin@admin.com'),
(2, 'Vendedor', 'Juan Vendedor', 'juan@funeraria.cl');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `prospecto_id` int(11) NOT NULL,
  `catalogo_id` int(11) NOT NULL,
  `precio_congelado` decimal(10,2) NOT NULL,
  `documento_ruta` varchar(255) NOT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `catalogo`
--
ALTER TABLE `catalogo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `catalogo_precios`
--
ALTER TABLE `catalogo_precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catalogo_id` (`catalogo_id`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospecto_id` (`prospecto_id`);

--
-- Indices de la tabla `metas_corporativas`
--
ALTER TABLE `metas_corporativas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `metas_vendedor`
--
ALTER TABLE `metas_vendedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Indices de la tabla `prospectos`
--
ALTER TABLE `prospectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospecto_id` (`prospecto_id`),
  ADD KEY `catalogo_id` (`catalogo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `catalogo`
--
ALTER TABLE `catalogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `catalogo_precios`
--
ALTER TABLE `catalogo_precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `metas_corporativas`
--
ALTER TABLE `metas_corporativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `metas_vendedor`
--
ALTER TABLE `metas_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prospectos`
--
ALTER TABLE `prospectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `catalogo_precios`
--
ALTER TABLE `catalogo_precios`
  ADD CONSTRAINT `catalogo_precios_ibfk_1` FOREIGN KEY (`catalogo_id`) REFERENCES `catalogo` (`id`);

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`prospecto_id`) REFERENCES `prospectos` (`id`);

--
-- Filtros para la tabla `metas_vendedor`
--
ALTER TABLE `metas_vendedor`
  ADD CONSTRAINT `metas_vendedor_ibfk_1` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `prospectos`
--
ALTER TABLE `prospectos`
  ADD CONSTRAINT `prospectos_ibfk_1` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`prospecto_id`) REFERENCES `prospectos` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`catalogo_id`) REFERENCES `catalogo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
