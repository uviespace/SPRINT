-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 29. Jun 2021 um 16:09
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
-- Datenbank: `test`
--

--
-- Daten für Tabelle `parameterrole`
--

INSERT INTO `parameterrole` (`id`, `filter`, `name`, `desc`, `setting`) VALUES
(0, 0, 'None', NULL, NULL),
(1, 1, 'Type (T)', NULL, NULL),
(2, 1, 'Subtype (S)', NULL, NULL),
(3, 2, 'Discriminant', NULL, NULL),
(4, 1, 'APID (A)', NULL, NULL),
(5, 1, 'Acknowledgement Flags (K)', NULL, NULL),
(6, 3, 'Parameter ID', NULL, NULL),
(7, 3, 'Command ID', NULL, NULL),
(8, 2, 'Spare', NULL, NULL);
(9, 1, 'Auto (P)', NULL, NULL);
(10, 1, 'Fixed (F)', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
