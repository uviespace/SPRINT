-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Jun 2021 um 16:43
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
-- Daten für Tabelle `component`
--

INSERT INTO `component` (`id`, `shortName`, `name`, `desc`, `setting`) VALUES
(1, 'icd', 'ICD Generator', 'Generates *.csv and *.tex files.', NULL),
(2, 'dp', 'Datapool', 'Generates C code which can be used together with the Datapool SW component.', NULL),
(3, 'spec', 'Specification', 'Generates *.tex files which can be used to build a specification of the application.', NULL),
(4, 'mib', 'MIB Generator', 'Generates MIB files according to SCOS-2000.', NULL),
(5, 'pck', 'Packet access functions', 'Generates marshalling/unmarshalling functions for the packets', NULL),
(6, 'cfw', 'CordetFw', 'Generates all files for CordetFw instantiation', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
