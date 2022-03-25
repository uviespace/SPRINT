-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Nov 2021 um 16:26
-- Server-Version: 10.4.8-MariaDB
-- PHP-Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `dbeditor`
--

--
-- Daten für Tabelle `doctype`
--

INSERT INTO `doctype` (`id`, `idCategory`, `name`, `shortDesc`, `desc`) VALUES
(1, 1, 'TN', 'Technical Note', NULL),
(2, 2, 'SSS', 'Software System Specification', NULL),
(3, 2, 'IRD', 'Interface Requirements Document', NULL),
(4, 3, 'SRS', 'Software Requirements Document', NULL),
(5, 3, 'ICD', 'Software Interface Control Document', NULL),
(6, 1, 'PL', 'Project Management Plan', NULL),
(7, 1, 'TS', 'Technical Specification', NULL),
(8, 1, 'ECSS Standard', 'ECSS Standard', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
