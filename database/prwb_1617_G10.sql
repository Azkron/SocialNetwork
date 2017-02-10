-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 10, 2017 at 09:40 AM
-- Server version: 5.7.11
-- PHP Version: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prwb_1617_g10`
--
CREATE DATABASE IF NOT EXISTS `prwb_1617_g10` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `prwb_1617_g10`;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE `calendar` (
  `idcalendar` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `color` char(6) NOT NULL,
  `iduser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`idcalendar`, `description`, `color`, `iduser`) VALUES
(31, 'EPFC', '0080ff', 14),
(32, 'FAMILY', 'ff80c0', 14),
(34, 'EPFC', 'ff0000', 15),
(35, 'Job', '000000', 15),
(36, 'Personal', 'ff80ff', 15);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `idevent` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime DEFAULT NULL,
  `whole_day` tinyint(1) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `idcalendar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`idevent`, `start`, `finish`, `whole_day`, `title`, `description`, `idcalendar`) VALUES
(82, '2017-02-07 09:00:00', '2017-02-10 18:00:00', 0, 'Correct Web Projects', 'Starting by the project of Hugo and Tidianne', 31),
(83, '2017-02-06 18:00:00', '2017-02-06 20:00:00', 0, 'Go to the park', 'It is a nice park', 32),
(84, '2017-02-06 17:00:00', NULL, 0, 'Net class', 'I am going to make my students sweat. Poor bastards, they don´t know what they are in for.', 31),
(85, '2017-02-11 00:00:00', '2017-02-12 00:00:00', 1, 'Weekend!!', '', 32),
(86, '2017-02-12 21:03:00', '2017-02-18 13:21:00', 0, 'Important teacher stuff that matters', 'Important things that matter', 31),
(87, '2017-02-02 23:04:00', NULL, 1, 'Random thing', '', 31),
(88, '2017-02-11 08:00:00', '2017-02-11 16:30:00', 0, 'Work at market', 'This job sucks balls', 35),
(89, '2017-02-12 04:45:00', '2017-02-12 17:45:00', 0, 'Work at market', 'This job sucks even more on Sunday.', 35),
(90, '2017-02-06 08:00:00', '2017-02-10 18:00:00', 0, 'Study', '', 34),
(91, '2017-02-01 23:21:00', '2017-02-16 21:03:00', 1, 'Making videogames', '', 36);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `pseudo` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `full_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`iduser`, `pseudo`, `password`, `email`, `full_name`) VALUES
(14, 'Ben', 'e82688a4779e3d15f38c3e83f0002d9a', 'ben@gmail.com', 'Benoit'),
(15, 'Hugo', 'e82688a4779e3d15f38c3e83f0002d9a', 'hugobeny@gmail.com', 'Hugo Barbachano');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`idcalendar`),
  ADD KEY `fk_calendar_user_idx` (`iduser`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`idevent`),
  ADD KEY `fk_event_calendar1_idx` (`idcalendar`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `pseudo_UNIQUE` (`pseudo`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
  MODIFY `idcalendar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `fk_calendar_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_calendar` FOREIGN KEY (`idcalendar`) REFERENCES `calendar` (`idcalendar`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
