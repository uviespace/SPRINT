-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Nov 2021 um 15:49
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
-- Tabellenstruktur für Tabelle `categoryrequirement`
--

CREATE TABLE `categoryrequirement` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL,
  `note` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectrequirement`
--

CREATE TABLE `projectrequirement` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idDocRelation` int(10) UNSIGNED DEFAULT NULL,
  `requirementId` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `justification` text CHARACTER SET utf8 NOT NULL,
  `applicability` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `applicableToPayloads` varchar(32) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectrequirementcategory`
--

CREATE TABLE `projectrequirementcategory` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idSwComponent` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projectrequirementstandard`
--

CREATE TABLE `projectrequirementstandard` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idRequirementStandard` int(10) UNSIGNED DEFAULT NULL,
  `applicable` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `output` text CHARACTER SET utf8 NOT NULL,
  `remarks` text CHARACTER SET utf8 NOT NULL,
  `closeout` text CHARACTER SET utf8 NOT NULL,
  `responsibility` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `requirement`
--

CREATE TABLE `requirement` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDocVersion` int(10) UNSIGNED DEFAULT NULL,
  `idCategoryRequirement` int(10) UNSIGNED DEFAULT NULL,
  `clause` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `shortDesc` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `requirementrequirement`
--

CREATE TABLE `requirementrequirement` (
  `idProjectRequirementExternal` int(10) UNSIGNED NOT NULL,
  `idProjectRequirementInternal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `requirementstandard`
--

CREATE TABLE `requirementstandard` (
  `idProjectStandard` int(10) UNSIGNED NOT NULL,
  `idProjectRequirement` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `categoryrequirement`
--
ALTER TABLE `categoryrequirement`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `projectrequirement`
--
ALTER TABLE `projectrequirement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`);

--
-- Indizes für die Tabelle `projectrequirementcategory`
--
ALTER TABLE `projectrequirementcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `projectrequirementstandard`
--
ALTER TABLE `projectrequirementstandard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idRequirementStandard` (`idRequirementStandard`);

--
-- Indizes für die Tabelle `requirement`
--
ALTER TABLE `requirement`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `requirementrequirement`
--
ALTER TABLE `requirementrequirement`
  ADD PRIMARY KEY (`idProjectRequirementExternal`,`idProjectRequirementInternal`) USING BTREE,
  ADD KEY `idProjectRequirementInternal` (`idProjectRequirementInternal`),
  ADD KEY `idProjectRequirementExternal` (`idProjectRequirementExternal`);

--
-- Indizes für die Tabelle `requirementstandard`
--
ALTER TABLE `requirementstandard`
  ADD PRIMARY KEY (`idProjectStandard`,`idProjectRequirement`) USING BTREE,
  ADD KEY `idProjectStandard` (`idProjectStandard`),
  ADD KEY `idProjectRequirement` (`idProjectRequirement`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `categoryrequirement`
--
ALTER TABLE `categoryrequirement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectrequirement`
--
ALTER TABLE `projectrequirement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectrequirementcategory`
--
ALTER TABLE `projectrequirementcategory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `projectrequirementstandard`
--
ALTER TABLE `projectrequirementstandard`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `requirement`
--
ALTER TABLE `requirement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
