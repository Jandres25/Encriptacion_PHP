-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2024 a las 21:37:01
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET FOREIGN_KEY_CHECKS=0;
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
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombres` varchar(100) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `Usuario` varchar(50) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `EsAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID`, `Nombres`, `Apellidos`, `Usuario`, `Clave`, `EsAdmin`) VALUES
(1, 'Jose Andres', 'Meneces Lopez', 'Admin', '$2y$10$2OetdWQYOG6br4neYoYPGOiNZQsM79b3GWPUmAkV59pcdM0Zx/Pje', 1),
(2, 'Lucas', 'Martinez Peralta', 'Luca', '$2y$10$yZcR40UMne.eQxTVNFb0jOIYrEC6Tj2dj1yJt2FGxzMizNHc0hZWS', 0),
(3, 'Juan Juanito', 'Perez Mamio', 'Juan', '0000', 0),
(4, 'Sofia', 'Oropesa Cespedez', 'Sofy', '0000', 0),
(5, 'Maria Christina', 'Johnson Smith', 'Mary', '0000', 0),
(6, 'Martin', 'Morales', 'Martins', '$2y$10$x.bQLZ9TfGsOHmIl5lwM8.badBz9G9kqLRlYhxw25Lpzq0HN0wK8m', 0),
(9, 'Gustavo', 'Aguilar Mendoza', 'Gus', '$2y$10$3YQR0fPiN4xeMIL3QYDXBOQrOWr6BQfRgwjeFdB1Bf18OlOM6M0NO', 0);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
