-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Nov 2021 um 16:18
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
-- Daten für Tabelle `category`
--

INSERT INTO `category` (`id`, `name`, `shortDesc`, `desc`) VALUES
(1, 'GENERAL', 'General', ''),
(2, 'RB', 'Requirement Baseline', ''),
(3, 'TS', 'Technical Specification', ''),
(4, 'DDF', 'Design Definition File', ''),
(5, 'DJF', 'Design Justification File', ''),
(6, 'MGT', 'Management File', ''),
(7, 'MF', 'Maintenance File', ''),
(8, 'OP', 'Operational', ''),
(9, 'PAF', 'Product Assurance File', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
