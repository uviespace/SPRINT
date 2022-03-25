-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Mrz 2022 um 14:09
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `packetidentifier`
--

CREATE TABLE `packetidentifier` (
  `idProject` int(10) NOT NULL,
  `nrPacket` int(10) NOT NULL,
  `idPacket` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parameteridentifier`
--

CREATE TABLE `parameteridentifier` (
  `idProject` int(10) NOT NULL,
  `nrParameter` int(10) NOT NULL,
  `idParameter` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `typeidentifier`
--

CREATE TABLE `typeidentifier` (
  `idProject` int(10) NOT NULL,
  `nrType` int(10) NOT NULL,
  `idType` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `packetidentifier`
--
ALTER TABLE `packetidentifier`
  ADD UNIQUE KEY `idProject` (`idProject`,`nrPacket`);

--
-- Indizes für die Tabelle `parameteridentifier`
--
ALTER TABLE `parameteridentifier`
  ADD UNIQUE KEY `idProject` (`idProject`,`nrParameter`);

--
-- Indizes für die Tabelle `typeidentifier`
--
ALTER TABLE `typeidentifier`
  ADD UNIQUE KEY `idProject` (`idProject`,`nrType`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
