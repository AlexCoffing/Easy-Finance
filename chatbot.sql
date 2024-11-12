-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2024 a las 21:41:25
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
-- Base de datos: `chatbot`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

CREATE TABLE `datos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `apellidos` varchar(128) NOT NULL,
  `correo` varchar(128) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `balance_ingreso` float NOT NULL,
  `balance_egreso` double NOT NULL,
  `balance_total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos`
--

INSERT INTO `datos` (`id`, `nombre`, `apellidos`, `correo`, `pass`, `balance_ingreso`, `balance_egreso`, `balance_total`) VALUES
(1, 'Julio', 'Gonzalez', 'prueba1chatbot@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 50, 10, 40),
(2, 'Prueba', 'Dos', 'prueba2chatbot@gmail.com', '12345', 10, 0, 10),
(3, 'Alberto', 'Perez', 'albertoperez@chatbot.com', '827ccb0eea8a706c4c34a16891f84e7b', 0, 0, 0),
(4, 'Juan Jose', 'Arreola', 'JuanJoo@gmail.com', '8fdf7d5d6720418693f205285e0d6f1e', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `usuario_id` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `sender` enum('user','bot') NOT NULL,
  `fecha_mensaje` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `usuario_id`, `mensaje`, `sender`, `fecha_mensaje`) VALUES
(1, 'JuanJoo@gmail.com', 'pepe', 'user', '2024-11-12 18:11:39'),
(2, 'JuanJoo@gmail.com', ': pepe', 'bot', '2024-11-12 18:11:39'),
(3, 'JuanJoo@gmail.com', 'Quiergo gastar 15 pesos en refrescos', 'user', '2024-11-12 18:11:43'),
(4, 'JuanJoo@gmail.com', ': Quiergo gastar 15 pesos en refrescos', 'bot', '2024-11-12 18:11:43'),
(5, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:26'),
(6, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:26'),
(7, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:28'),
(8, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:28'),
(9, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:28'),
(10, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:28'),
(11, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:29'),
(12, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:29'),
(13, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:29'),
(14, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:29'),
(15, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:36'),
(16, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:36'),
(17, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:36'),
(18, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:36'),
(19, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(20, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(21, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(22, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(23, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(24, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(25, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(26, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(27, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(28, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(29, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:37'),
(30, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:37'),
(31, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:29:38'),
(32, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:29:38'),
(33, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:30:20'),
(34, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:30:20'),
(35, 'JuanJoo@gmail.com', 'hola', 'user', '2024-11-12 20:30:52'),
(36, 'JuanJoo@gmail.com', ': hola', 'bot', '2024-11-12 20:30:52'),
(37, 'JuanJoo@gmail.com', 'Hola', 'user', '2024-11-12 20:34:00'),
(38, 'JuanJoo@gmail.com', 'Â¡Buenos dias!', 'bot', '2024-11-12 20:34:02'),
(39, 'JuanJoo@gmail.com', '¿Quien eres?', 'user', '2024-11-12 20:34:08'),
(40, 'JuanJoo@gmail.com', 'Me llamo AsesorBot y estoy aqui para ayudarte en lo que necesites', 'bot', '2024-11-12 20:34:08'),
(41, 'JuanJoo@gmail.com', '¿Que haces?', 'user', '2024-11-12 20:34:52'),
(42, 'JuanJoo@gmail.com', 'Â¡Estoy aqui para ayudarte a mejorar tus finanzas!', 'bot', '2024-11-12 20:34:52'),
(43, 'JuanJoo@gmail.com', 'Quiergo gastar 15 pesos en refrescos', 'user', '2024-11-12 20:40:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datos`
--
ALTER TABLE `datos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datos`
--
ALTER TABLE `datos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
