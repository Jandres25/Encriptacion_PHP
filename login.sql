-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-02-2025 a las 18:19:04
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `login`
--
CREATE DATABASE IF NOT EXISTS `login` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `login`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELACIONES PARA LA TABLA `password_resets`:
--

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `expires_at`, `used`) VALUES
(1, 'jandrespb4@gmail.com', 'd1d20e859b6892ac0b845c3b1b2d3d8811ac0927e6767d7c381bcdb0c812be8a', '2025-02-16 16:37:55', '2025-02-16 22:37:55', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombres` varchar(100) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `Usuario` varchar(50) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `EsAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELACIONES PARA LA TABLA `usuario`:
--

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID`, `Nombres`, `Apellidos`, `correo`, `Usuario`, `Clave`, `EsAdmin`) VALUES
(1, 'Jose Andres', 'Meneces Lopez', 'jandrespb4@gmail.com', 'Admin', '$2y$10$3T3hu9AM7shPtkFQfM7jluFTMLfL474gUjbfN7eMTcSlPTrb.ktLK', 1),
(2, 'Lucas', 'Martinez Peralta', 'usuario2@gmail.com', 'Luca', '$2y$10$yZcR40UMne.eQxTVNFb0jOIYrEC6Tj2dj1yJt2FGxzMizNHc0hZWS', 0),
(3, 'Juan Juanito', 'Perez Mamio', '', 'Juan', '0000', 0),
(4, 'Sofia', 'Oropesa Cespedez', '', 'Sofy', '0000', 0),
(5, 'Maria Christina', 'Johnson Smith', '', 'Mary', '0000', 0),
(6, 'Martin', 'Morales', '', 'Martins', '$2y$10$x.bQLZ9TfGsOHmIl5lwM8.badBz9G9kqLRlYhxw25Lpzq0HN0wK8m', 0),
(9, 'Gustavo', 'Aguilar Mendoza', '', 'Gus', '$2y$10$3YQR0fPiN4xeMIL3QYDXBOQrOWr6BQfRgwjeFdB1Bf18OlOM6M0NO', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
