-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Jun 2021 um 16:50
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
-- Daten für Tabelle `parameter`
--

INSERT INTO `parameter` (`id`, `idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`, `setting`, `role`) VALUES
(1, NULL, 101, 0, 'predefined', 'Dummy', 'Zero-length dummy parameter', 'Zero length dummy parameter used for defining parameter groups with fixed repetition.', 'N/A', 0, 'N/A', NULL, NULL, NULL),
(2, NULL, 101, 0, 'predefined', 'Spare 1-bit', '1-bit Filler', NULL, '0', 1, NULL, NULL, NULL, NULL),
(3, NULL, 101, 0, 'predefined', 'Spare 2-bit', '2-bit Filler', NULL, '0', 2, NULL, NULL, NULL, NULL),
(4, NULL, 101, 0, 'predefined', 'Spare 3-bit', '3-bit Filler', NULL, '0', 3, NULL, NULL, NULL, NULL),
(5, NULL, 101, 0, 'predefined', 'Spare 4-bit', '4-bit Filler', NULL, '0', 4, NULL, NULL, NULL, NULL),
(6, NULL, 101, 0, 'predefined', 'Spare 5-bit', '5-bit Filler', NULL, '0', 5, NULL, NULL, NULL, NULL),
(7, NULL, 101, 0, 'predefined', 'Spare 6-bit', '6-bit Filler', NULL, '0', 6, NULL, NULL, NULL, NULL),
(8, NULL, 101, 0, 'predefined', 'Spare 7-bit', '7-bit Filler', NULL, '0', 7, NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
