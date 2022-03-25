-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Nov 2021 um 16:27
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
-- Daten für Tabelle `package`
--

INSERT INTO `package` (`id`, `name`, `desc`) VALUES
(1, 'BRB / SIM', 'Breadboard / Simulator'),
(2, 'SRR', 'System Requirements Review'),
(3, 'PDR', 'Preliminary Design Review'),
(4, 'CDR', 'Critical Design Review'),
(5, 'QR', 'Qualification Review'),
(6, 'AR', 'Acceptance Review'),
(7, 'QAR', 'Qualification/Acceptance Review'),
(8, 'ORR', 'Operational Readiness Review');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
