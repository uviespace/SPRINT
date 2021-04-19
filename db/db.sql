-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 19. Apr 2021 um 16:34
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
-- Datenbank: `db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `application`
--

CREATE TABLE `application` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `address` int(11) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `applicationcomponent`
--

CREATE TABLE `applicationcomponent` (
  `idApplication` int(10) UNSIGNED NOT NULL,
  `idComponent` int(10) UNSIGNED NOT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `applicationfeature`
--

CREATE TABLE `applicationfeature` (
  `idApplication` int(10) UNSIGNED NOT NULL,
  `idFeature` int(10) UNSIGNED NOT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `applicationpacket`
--

CREATE TABLE `applicationpacket` (
  `idApplication` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED NOT NULL,
  `idPacket` int(10) UNSIGNED NOT NULL,
  `cmdUsrCheckEnable` text DEFAULT NULL,
  `cmdUsrCheckReady` text DEFAULT NULL,
  `cmdUsrCheckRepeat` text DEFAULT NULL,
  `cmdPrvCheckAcceptance` text DEFAULT NULL,
  `cmdPrvCheckReady` text DEFAULT NULL,
  `cmdUsrActionUpdate` text DEFAULT NULL,
  `cmdPrvActionStart` text DEFAULT NULL,
  `cmdPrvActionProgress` text DEFAULT NULL,
  `cmdPrvActionTermination` text DEFAULT NULL,
  `cmdPrvActionAbort` text DEFAULT NULL,
  `repPrvCheckEnable` text DEFAULT NULL,
  `repPrvCheckReady` text DEFAULT NULL,
  `repPrvCheckRepeat` text DEFAULT NULL,
  `repUsrCheckAcceptance` text DEFAULT NULL,
  `repPrvActionUpdate` text DEFAULT NULL,
  `repUsrActionUpdate` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `applicationstandard`
--

CREATE TABLE `applicationstandard` (
  `idApplication` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED NOT NULL,
  `relation` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0 = service user\n1 = service provider';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `component`
--

CREATE TABLE `component` (
  `id` int(10) UNSIGNED NOT NULL,
  `shortName` varchar(32) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A SW Component / Extension, e.g. CordetFw, Datapool, etc\n';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `constants`
--

CREATE TABLE `constants` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED DEFAULT NULL,
  `domain` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `value` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `enumeration`
--

CREATE TABLE `enumeration` (
  `id` int(10) UNSIGNED NOT NULL,
  `idType` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL,
  `schema` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `feature`
--

CREATE TABLE `feature` (
  `id` int(10) UNSIGNED NOT NULL,
  `idComponent` int(10) UNSIGNED NOT NULL,
  `shortName` varchar(32) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `limit`
--

CREATE TABLE `limit` (
  `id` int(10) UNSIGNED NOT NULL,
  `idParameter` int(10) UNSIGNED NOT NULL,
  `type` int(10) UNSIGNED DEFAULT NULL,
  `lvalue` varchar(32) DEFAULT NULL,
  `hvalue` varchar(45) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `packet`
--

CREATE TABLE `packet` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED NOT NULL,
  `idParent` int(10) UNSIGNED DEFAULT NULL,
  `idProcess` int(10) UNSIGNED DEFAULT NULL,
  `kind` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` int(10) UNSIGNED DEFAULT NULL,
  `subtype` int(10) UNSIGNED DEFAULT NULL,
  `discriminant` varchar(256) DEFAULT NULL,
  `domain` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `shortDesc` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `descParam` text DEFAULT NULL,
  `descDest` text DEFAULT NULL,
  `code` varchar(128) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parameter`
--

CREATE TABLE `parameter` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED DEFAULT NULL,
  `idType` int(10) UNSIGNED DEFAULT NULL,
  `kind` int(10) UNSIGNED DEFAULT 0,
  `domain` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `shortDesc` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `value` text DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `unit` varchar(45) DEFAULT NULL,
  `multiplicity` varchar(256) DEFAULT NULL,
  `setting` text DEFAULT NULL,
  `role` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='kind = 0 for pre-defined parameters accessible everywhere\nkind = 1 for parameters in the header\nkind = 2 for parameters in the body\nkind = 3 for datapool parameters\nkind = 4 for datapool variables';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parameterrole`
--

CREATE TABLE `parameterrole` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `filter` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parametersequence`
--

CREATE TABLE `parametersequence` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED NOT NULL,
  `idParameter` int(10) UNSIGNED NOT NULL,
  `idPacket` int(10) UNSIGNED DEFAULT NULL,
  `type` int(10) UNSIGNED NOT NULL,
  `role` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `group` int(10) UNSIGNED DEFAULT NULL,
  `repetition` int(10) UNSIGNED DEFAULT NULL,
  `value` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='type = 0 for TC header\ntype = 1 for TM header';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `process`
--

CREATE TABLE `process` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `address` int(10) UNSIGNED DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project`
--

CREATE TABLE `project` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `isPublic` tinyint(1) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_audit`
--

CREATE TABLE `project_audit` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED DEFAULT NULL,
  `idUser` int(10) UNSIGNED DEFAULT NULL,
  `dateTime` datetime DEFAULT NULL,
  `data` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

CREATE TABLE `resource` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED NOT NULL,
  `type` int(11) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role`
--

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `permissionWrite` tinyint(1) DEFAULT NULL,
  `permissionDelete` tinyint(1) DEFAULT NULL,
  `permissionGrantAccess` tinyint(1) DEFAULT NULL,
  `permissionRename` tinyint(1) DEFAULT NULL,
  `permissionPublish` tinyint(1) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `service`
--

CREATE TABLE `service` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `type` int(10) UNSIGNED DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `setting`
--

CREATE TABLE `setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `schema` text DEFAULT NULL,
  `default` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Domain is used to allow only a single table for all pre-defined settings, e.g. "app", "datapool", "datapool/tc31"\nType is used to define the datatype of this setting.';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standard`
--

CREATE TABLE `standard` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standardstandard`
--

CREATE TABLE `standardstandard` (
  `idStandardParent` int(10) UNSIGNED NOT NULL,
  `idStandardChild` int(10) UNSIGNED NOT NULL,
  `relation` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `setting` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0 = conforms\n1 = extends';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `type`
--

CREATE TABLE `type` (
  `id` int(10) UNSIGNED NOT NULL,
  `idStandard` int(10) UNSIGNED DEFAULT NULL,
  `domain` varchar(32) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `nativeType` varchar(45) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `value` text DEFAULT NULL,
  `setting` text DEFAULT NULL,
  `schema` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A value > 0 for size means, it is a fixed size. Otherwise, it can be set in Parameter.';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `signedUp` datetime DEFAULT NULL,
  `lastSignedIn` datetime DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userproject`
--

CREATE TABLE `userproject` (
  `id` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  `idProject` int(10) UNSIGNED NOT NULL,
  `idRole` int(10) UNSIGNED NOT NULL,
  `email` varchar(256) DEFAULT NULL,
  `setting` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `view_applicationpacket`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `view_applicationpacket` (
`idApplication` int(10) unsigned
,`idStandard` int(10) unsigned
,`idPacket` int(10) unsigned
,`cmdUsrCheckEnable` text
,`cmdUsrCheckReady` text
,`cmdUsrCheckRepeat` text
,`cmdPrvCheckAcceptance` text
,`cmdPrvCheckReady` text
,`cmdUsrActionUpdate` text
,`cmdPrvActionStart` text
,`cmdPrvActionProgress` text
,`cmdPrvActionTermination` text
,`cmdPrvActionAbort` text
,`repPrvCheckEnable` text
,`repPrvCheckReady` text
,`repPrvCheckRepeat` text
,`repUsrCheckAcceptance` text
,`repPrvActionUpdate` text
,`repUsrActionUpdate` text
,`setting` text
,`type` int(10) unsigned
,`subtype` int(10) unsigned
,`discriminant` varchar(256)
);

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `view_packetcomb`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `view_packetcomb` (
`name` varchar(256)
,`id` int(10) unsigned
,`idStandard` int(10) unsigned
,`type` int(10) unsigned
,`subtype` int(10) unsigned
,`discriminant` varchar(256)
);

-- --------------------------------------------------------

--
-- Struktur des Views `view_applicationpacket`
--
DROP TABLE IF EXISTS `view_applicationpacket`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dbo680150756`@`%` SQL SECURITY DEFINER VIEW `view_applicationpacket`  AS  select `ap`.`idApplication` AS `idApplication`,`ap`.`idStandard` AS `idStandard`,`ap`.`idPacket` AS `idPacket`,`ap`.`cmdUsrCheckEnable` AS `cmdUsrCheckEnable`,`ap`.`cmdUsrCheckReady` AS `cmdUsrCheckReady`,`ap`.`cmdUsrCheckRepeat` AS `cmdUsrCheckRepeat`,`ap`.`cmdPrvCheckAcceptance` AS `cmdPrvCheckAcceptance`,`ap`.`cmdPrvCheckReady` AS `cmdPrvCheckReady`,`ap`.`cmdUsrActionUpdate` AS `cmdUsrActionUpdate`,`ap`.`cmdPrvActionStart` AS `cmdPrvActionStart`,`ap`.`cmdPrvActionProgress` AS `cmdPrvActionProgress`,`ap`.`cmdPrvActionTermination` AS `cmdPrvActionTermination`,`ap`.`cmdPrvActionAbort` AS `cmdPrvActionAbort`,`ap`.`repPrvCheckEnable` AS `repPrvCheckEnable`,`ap`.`repPrvCheckReady` AS `repPrvCheckReady`,`ap`.`repPrvCheckRepeat` AS `repPrvCheckRepeat`,`ap`.`repUsrCheckAcceptance` AS `repUsrCheckAcceptance`,`ap`.`repPrvActionUpdate` AS `repPrvActionUpdate`,`ap`.`repUsrActionUpdate` AS `repUsrActionUpdate`,`ap`.`setting` AS `setting`,if(`p`.`idParent` is null,`p`.`type`,`parent`.`type`) AS `type`,if(`p`.`idParent` is null,`p`.`subtype`,`parent`.`subtype`) AS `subtype`,if(`p`.`idParent` is null,`p`.`discriminant`,`parent`.`discriminant`) AS `discriminant` from ((`applicationpacket` `ap` left join `packet` `p` on(`ap`.`idPacket` = `p`.`id`)) left join `packet` `parent` on(`p`.`idParent` = `parent`.`id`)) order by if(`p`.`idParent` is null,`p`.`type`,`parent`.`type`),if(`p`.`idParent` is null,`p`.`subtype`,`parent`.`subtype`),if(`p`.`idParent` is null,`p`.`discriminant`,`parent`.`discriminant`) ;

-- --------------------------------------------------------

--
-- Struktur des Views `view_packetcomb`
--
DROP TABLE IF EXISTS `view_packetcomb`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dbo680150756`@`%` SQL SECURITY DEFINER VIEW `view_packetcomb`  AS  select `p`.`name` AS `name`,`p`.`id` AS `id`,`p`.`idStandard` AS `idStandard`,if(`p`.`idParent` is null,`p`.`type`,`parent`.`type`) AS `type`,if(`p`.`idParent` is null,`p`.`subtype`,`parent`.`subtype`) AS `subtype`,`p`.`discriminant` AS `discriminant` from (`packet` `p` left join `packet` `parent` on(`p`.`idParent` = `parent`.`id`)) order by if(`p`.`idParent` is null,`p`.`type`,`parent`.`type`),if(`p`.`idParent` is null,`p`.`subtype`,`parent`.`subtype`),`p`.`discriminant` ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Application_Project1_idx` (`idProject`);

--
-- Indizes für die Tabelle `applicationcomponent`
--
ALTER TABLE `applicationcomponent`
  ADD PRIMARY KEY (`idComponent`,`idApplication`),
  ADD KEY `fk_ApplicationComponent_Application1_idx` (`idApplication`),
  ADD KEY `fk_ApplicationComponent_Component1_idx` (`idComponent`);

--
-- Indizes für die Tabelle `applicationfeature`
--
ALTER TABLE `applicationfeature`
  ADD PRIMARY KEY (`idFeature`,`idApplication`),
  ADD KEY `fk_ApplicationFeature_Application1_idx` (`idApplication`),
  ADD KEY `fk_ApplicationFeature_Feature1_idx` (`idFeature`);

--
-- Indizes für die Tabelle `applicationpacket`
--
ALTER TABLE `applicationpacket`
  ADD PRIMARY KEY (`idApplication`,`idStandard`,`idPacket`),
  ADD KEY `fk_ApplicationPacket_2_idx` (`idPacket`);

--
-- Indizes für die Tabelle `applicationstandard`
--
ALTER TABLE `applicationstandard`
  ADD PRIMARY KEY (`idApplication`,`idStandard`),
  ADD KEY `fk_ApplicationStandard_2_idx` (`idStandard`);

--
-- Indizes für die Tabelle `component`
--
ALTER TABLE `component`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shortName_UNIQUE` (`shortName`);

--
-- Indizes für die Tabelle `constants`
--
ALTER TABLE `constants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Constants_1_idx` (`idStandard`);

--
-- Indizes für die Tabelle `enumeration`
--
ALTER TABLE `enumeration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Enumeration_1_idx` (`idType`);

--
-- Indizes für die Tabelle `feature`
--
ALTER TABLE `feature`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shortName_UNIQUE` (`shortName`),
  ADD KEY `fk_Feature_Component1_idx` (`idComponent`);

--
-- Indizes für die Tabelle `limit`
--
ALTER TABLE `limit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Limit_Parameter1_idx` (`idParameter`);

--
-- Indizes für die Tabelle `packet`
--
ALTER TABLE `packet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Standard_idx` (`idStandard`),
  ADD KEY `fk_Packet_1_idx` (`idParent`),
  ADD KEY `fk_Packet_2_idx` (`idProcess`);

--
-- Indizes für die Tabelle `parameter`
--
ALTER TABLE `parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Parameter_Type1_idx` (`idType`),
  ADD KEY `fk_Parameter_1_idx` (`idStandard`),
  ADD KEY `fk_Parameter_2_idx` (`role`);

--
-- Indizes für die Tabelle `parameterrole`
--
ALTER TABLE `parameterrole`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `parametersequence`
--
ALTER TABLE `parametersequence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ParameterPacket_2_idx` (`idPacket`),
  ADD KEY `fk_ParameterSequence_1_idx` (`idStandard`),
  ADD KEY `fk_ParameterSequence_2_idx` (`role`),
  ADD KEY `fk_ParameterPacket_1` (`idParameter`);

--
-- Indizes für die Tabelle `process`
--
ALTER TABLE `process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Process_1_idx` (`idProject`);

--
-- Indizes für die Tabelle `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `project_audit`
--
ALTER TABLE `project_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Project_Audit_1_idx` (`idUser`),
  ADD KEY `fk_Project_Audit_2_idx` (`idProject`);

--
-- Indizes für die Tabelle `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Resource_Project1_idx` (`idProject`);

--
-- Indizes für die Tabelle `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Service_Standard1_idx` (`idStandard`),
  ADD KEY `index3` (`type`);

--
-- Indizes für die Tabelle `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indizes für die Tabelle `standard`
--
ALTER TABLE `standard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Standard_Project1_idx` (`idProject`);

--
-- Indizes für die Tabelle `standardstandard`
--
ALTER TABLE `standardstandard`
  ADD PRIMARY KEY (`idStandardParent`,`idStandardChild`),
  ADD KEY `fk_StandardStandard_2_idx` (`idStandardChild`);

--
-- Indizes für die Tabelle `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Type_1_idx` (`idStandard`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `userproject`
--
ALTER TABLE `userproject`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_UserProject_1_idx` (`idProject`),
  ADD KEY `fk_UserProject_Role1_idx` (`idRole`),
  ADD KEY `fkUser` (`idUser`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `application`
--
ALTER TABLE `application`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `component`
--
ALTER TABLE `component`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `constants`
--
ALTER TABLE `constants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `enumeration`
--
ALTER TABLE `enumeration`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `feature`
--
ALTER TABLE `feature`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `limit`
--
ALTER TABLE `limit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `packet`
--
ALTER TABLE `packet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `parameter`
--
ALTER TABLE `parameter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `parametersequence`
--
ALTER TABLE `parametersequence`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `process`
--
ALTER TABLE `process`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `project`
--
ALTER TABLE `project`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `project_audit`
--
ALTER TABLE `project_audit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `resource`
--
ALTER TABLE `resource`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `role`
--
ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `standard`
--
ALTER TABLE `standard`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `type`
--
ALTER TABLE `type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `userproject`
--
ALTER TABLE `userproject`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `fk_Application_Project1` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `applicationcomponent`
--
ALTER TABLE `applicationcomponent`
  ADD CONSTRAINT `fk_ApplicationComponent_Application1` FOREIGN KEY (`idApplication`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ApplicationComponent_Component1` FOREIGN KEY (`idComponent`) REFERENCES `component` (`id`) ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `applicationfeature`
--
ALTER TABLE `applicationfeature`
  ADD CONSTRAINT `fk_ApplicationFeature_Application1` FOREIGN KEY (`idApplication`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ApplicationFeature_Feature1` FOREIGN KEY (`idFeature`) REFERENCES `feature` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `applicationpacket`
--
ALTER TABLE `applicationpacket`
  ADD CONSTRAINT `fk_ApplicationPacket_1` FOREIGN KEY (`idApplication`,`idStandard`) REFERENCES `applicationstandard` (`idApplication`, `idStandard`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ApplicationPacket_2` FOREIGN KEY (`idPacket`) REFERENCES `packet` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `applicationstandard`
--
ALTER TABLE `applicationstandard`
  ADD CONSTRAINT `fk_ApplicationStandard_1` FOREIGN KEY (`idApplication`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ApplicationStandard_2` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `constants`
--
ALTER TABLE `constants`
  ADD CONSTRAINT `fk_Constants_1` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `enumeration`
--
ALTER TABLE `enumeration`
  ADD CONSTRAINT `fk_Enumeration_1` FOREIGN KEY (`idType`) REFERENCES `type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `feature`
--
ALTER TABLE `feature`
  ADD CONSTRAINT `fk_Feature_Component1` FOREIGN KEY (`idComponent`) REFERENCES `component` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `limit`
--
ALTER TABLE `limit`
  ADD CONSTRAINT `fk_Limit_Parameter1` FOREIGN KEY (`idParameter`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `packet`
--
ALTER TABLE `packet`
  ADD CONSTRAINT `fk_Packet_1` FOREIGN KEY (`idParent`) REFERENCES `packet` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Packet_2` FOREIGN KEY (`idProcess`) REFERENCES `process` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Standard` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `parameter`
--
ALTER TABLE `parameter`
  ADD CONSTRAINT `fk_Parameter_1` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Parameter_2` FOREIGN KEY (`role`) REFERENCES `parameterrole` (`id`),
  ADD CONSTRAINT `fk_Parameter_Type1` FOREIGN KEY (`idType`) REFERENCES `type` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `parametersequence`
--
ALTER TABLE `parametersequence`
  ADD CONSTRAINT `fk_ParameterPacket_1` FOREIGN KEY (`idParameter`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ParameterPacket_2` FOREIGN KEY (`idPacket`) REFERENCES `packet` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ParameterSequence_1` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ParameterSequence_2` FOREIGN KEY (`role`) REFERENCES `parameterrole` (`id`);

--
-- Constraints der Tabelle `process`
--
ALTER TABLE `process`
  ADD CONSTRAINT `fk_Process_1` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `project_audit`
--
ALTER TABLE `project_audit`
  ADD CONSTRAINT `fk_Project_Audit_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Project_Audit_2` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `fk_Resource_Project1` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `fk_Service_Standard1` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `standard`
--
ALTER TABLE `standard`
  ADD CONSTRAINT `fk_Project` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `standardstandard`
--
ALTER TABLE `standardstandard`
  ADD CONSTRAINT `fk_StandardStandard_1` FOREIGN KEY (`idStandardParent`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_StandardStandard_2` FOREIGN KEY (`idStandardChild`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `type`
--
ALTER TABLE `type`
  ADD CONSTRAINT `fk_Type_1` FOREIGN KEY (`idStandard`) REFERENCES `standard` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `userproject`
--
ALTER TABLE `userproject`
  ADD CONSTRAINT `fkProject` FOREIGN KEY (`idProject`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fkUser` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_UserProject_Role1` FOREIGN KEY (`idRole`) REFERENCES `role` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
