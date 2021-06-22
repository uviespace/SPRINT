-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Jun 2021 um 16:44
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
-- Daten für Tabelle `role`
--

INSERT INTO `role` (`id`, `name`, `desc`, `permissionWrite`, `permissionDelete`, `permissionGrantAccess`, `permissionRename`, `permissionPublish`, `setting`) VALUES
(1, 'Administrator', 'Administrator account who is allowed to do anything.', 1, 1, 1, 1, 1, NULL),
(2, 'Maintainer', 'Owner of a project. Is allowed to give access rights.', 1, 1, 1, 1, 0, NULL),
(3, 'Contributor', 'Contributor of a project.', 1, 0, 0, 1, 0, NULL),
(4, 'Guest', 'Guest of a project. May only read.', 0, 0, 0, 0, 0, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
