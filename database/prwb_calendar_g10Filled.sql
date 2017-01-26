-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-01-2017 a las 08:32:08
-- Versión del servidor: 5.7.11
-- Versión de PHP: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prwb_calendar_g10`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendar`
--

CREATE TABLE `calendar` (
  `idCalendar` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `color` char(6) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `event`
--

CREATE TABLE `event` (
  `idevent` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime DEFAULT NULL,
  `whole_day` tinyint(1) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `idCalendar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `idUser` int(11) NOT NULL,
  `pseudo` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `full_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`idUser`, `pseudo`, `password`, `email`, `full_name`) VALUES
(4, 'Hugo', 'c6275e1d877a92a9accd5475e6fbb0ca', 'hugobeny@gmail.com', 'Hugo Barbachano'),
(5, 'Ben', 'a6af94877dadc6e20b597a3282c8e36c', 'ben@gmail.com', 'Benoit Penelle'),
(6, 'Tidiane', 'db14d1c737532b12f3fec8dd471812bb', 'toure@gmail.com', 'Tidiane Toure');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`idCalendar`),
  ADD KEY `fk_calendar_user_idx` (`idUser`);

--
-- Indices de la tabla `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`idevent`),
  ADD KEY `fk_event_calendar1_idx` (`idCalendar`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `pseudo_UNIQUE` (`pseudo`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calendar`
--
ALTER TABLE `calendar`
  MODIFY `idCalendar` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `event`
--
ALTER TABLE `event`
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `fk_calendar_user` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`);

--
-- Filtros para la tabla `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_calendar` FOREIGN KEY (`idCalendar`) REFERENCES `calendar` (`idCalendar`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
