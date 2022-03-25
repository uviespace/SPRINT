-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Nov 2021 um 16:01
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `acronym`
--

CREATE TABLE `acronym` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 DEFAULT NULL,
  `desc` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `acronymclassification`
--

CREATE TABLE `acronymclassification` (
  `id` int(10) UNSIGNED NOT NULL,
  `idAcronym` int(10) UNSIGNED DEFAULT NULL,
  `idClassification` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `category`
--

CREATE TABLE `category` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `classification`
--

CREATE TABLE `classification` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `country`
--

CREATE TABLE `country` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docdatapack`
--

CREATE TABLE `docdatapack` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDataPack` int(10) UNSIGNED DEFAULT NULL,
  `idDocVersion` int(10) UNSIGNED DEFAULT NULL,
  `note` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doclevel`
--

CREATE TABLE `doclevel` (
  `id` int(10) UNSIGNED NOT NULL,
  `shortDesc` text CHARACTER SET utf8 DEFAULT NULL,
  `desc` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docprefix`
--

CREATE TABLE `docprefix` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `prefix` varchar(128) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docreference`
--

CREATE TABLE `docreference` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDocVersion` int(10) UNSIGNED DEFAULT NULL,
  `idDocVersionRef` int(10) UNSIGNED DEFAULT NULL,
  `idDocLevel` int(10) UNSIGNED DEFAULT NULL,
  `note` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docrelation`
--

CREATE TABLE `docrelation` (
  `id` int(10) UNSIGNED NOT NULL,
  `shortDesc` text CHARACTER SET utf8 DEFAULT NULL,
  `desc` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doctype`
--

CREATE TABLE `doctype` (
  `id` int(10) UNSIGNED NOT NULL,
  `idCategory` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 DEFAULT NULL,
  `desc` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `document`
--

CREATE TABLE `document` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDocType` int(10) UNSIGNED DEFAULT NULL,
  `idDocRelation` int(10) UNSIGNED DEFAULT NULL,
  `idOrg` int(10) UNSIGNED DEFAULT NULL,
  `shortName` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `number` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docversion`
--

CREATE TABLE `docversion` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDocument` int(10) UNSIGNED DEFAULT NULL,
  `version` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `date` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `identifier` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `filename` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `note` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `organisation`
--

CREATE TABLE `organisation` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` varbinary(256) DEFAULT NULL,
  `idCountry` int(10) UNSIGNED DEFAULT NULL,
  `desc` varbinary(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `package`
--

CREATE TABLE `package` (
  `id` int(10) NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `desc` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectacronym`
--

CREATE TABLE `projectacronym` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idAcronym` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectdatapack`
--

CREATE TABLE `projectdatapack` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idPackage` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `note` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectdocument`
--

CREATE TABLE `projectdocument` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idDocument` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectorganisation`
--

CREATE TABLE `projectorganisation` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idOrg` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `acronym`
--
ALTER TABLE `acronym`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `acronymclassification`
--
ALTER TABLE `acronymclassification`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idAcronym` (`idAcronym`);

--
-- Indizes für die Tabelle `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `classification`
--
ALTER TABLE `classification`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `docdatapack`
--
ALTER TABLE `docdatapack`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idDataPack` (`idDataPack`),
  ADD KEY `idDocVersion` (`idDocVersion`);

--
-- Indizes für die Tabelle `doclevel`
--
ALTER TABLE `doclevel`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `docprefix`
--
ALTER TABLE `docprefix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`);

--
-- Indizes für die Tabelle `docreference`
--
ALTER TABLE `docreference`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idDocument` (`idDocVersion`),
  ADD KEY `idDocumentRef` (`idDocVersionRef`),
  ADD KEY `idDocLevel` (`idDocLevel`);

--
-- Indizes für die Tabelle `docrelation`
--
ALTER TABLE `docrelation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `doctype`
--
ALTER TABLE `doctype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCategory` (`idCategory`);

--
-- Indizes für die Tabelle `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idDocType` (`idDocType`),
  ADD KEY `idDocRelation` (`idDocRelation`),
  ADD KEY `idOrg` (`idOrg`);

--
-- Indizes für die Tabelle `docversion`
--
ALTER TABLE `docversion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idDocument` (`idDocument`);

--
-- Indizes für die Tabelle `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `projectacronym`
--
ALTER TABLE `projectacronym`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idAcronym` (`idAcronym`);

--
-- Indizes für die Tabelle `projectdatapack`
--
ALTER TABLE `projectdatapack`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idPackage` (`idPackage`);

--
-- Indizes für die Tabelle `projectdocument`
--
ALTER TABLE `projectdocument`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idDocument` (`idDocument`);

--
-- Indizes für die Tabelle `projectorganisation`
--
ALTER TABLE `projectorganisation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idOrg` (`idOrg`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `acronym`
--
ALTER TABLE `acronym`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `acronymclassification`
--
ALTER TABLE `acronymclassification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `category`
--
ALTER TABLE `category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `classification`
--
ALTER TABLE `classification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `country`
--
ALTER TABLE `country`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docdatapack`
--
ALTER TABLE `docdatapack`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `doclevel`
--
ALTER TABLE `doclevel`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docprefix`
--
ALTER TABLE `docprefix`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docreference`
--
ALTER TABLE `docreference`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docrelation`
--
ALTER TABLE `docrelation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `doctype`
--
ALTER TABLE `doctype`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `document`
--
ALTER TABLE `document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docversion`
--
ALTER TABLE `docversion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `organisation`
--
ALTER TABLE `organisation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `package`
--
ALTER TABLE `package`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectacronym`
--
ALTER TABLE `projectacronym`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectdatapack`
--
ALTER TABLE `projectdatapack`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectdocument`
--
ALTER TABLE `projectdocument`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectorganisation`
--
ALTER TABLE `projectorganisation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
