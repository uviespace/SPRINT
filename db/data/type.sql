-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Jun 2021 um 16:47
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
-- Daten für Tabelle `type`
--

INSERT INTO `type` (`id`, `idStandard`, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, `setting`, `schema`) VALUES
(100, NULL, 'General', 'bit', '', 'Individual bit', 1, NULL, NULL, NULL),
(101, NULL, 'General', 'generic', '', 'A generic type of undefined length', NULL, NULL, NULL, NULL),
(102, NULL, 'General', 'deduced', '', 'Deduced value from previous parameter.', NULL, NULL, NULL, NULL),
(200, NULL, 'C99', 'uint8_t', '', 'Capable of containing the [0, +2^8 -1] range', 8, NULL, NULL, NULL),
(201, NULL, 'C99', 'uint16_t', '', 'Capable of containing the [0, +2^16 -1] range', 16, NULL, NULL, NULL),
(202, NULL, 'C99', 'uint32_t', '', 'Capable of containing the [0, +2^32 -1] range', 32, NULL, NULL, NULL),
(203, NULL, 'C99', 'uint64_t', '', 'Capable of containing the [0, +2^64 -1] range', 64, NULL, NULL, NULL),
(204, NULL, 'C99', 'uint128_t', '', 'Capable of containing the [0, +2^128 -1] range', 128, NULL, NULL, NULL),
(210, NULL, 'C99', 'int8_t', '', 'Capable of containing the [-2^7, +2^7 -1] range', 8, NULL, NULL, NULL),
(211, NULL, 'C99', 'int16_t', '', 'Capable of containing the [-2^15, +2^15 -1] range', 16, NULL, NULL, NULL),
(212, NULL, 'C99', 'int32_t', '', 'Capable of containing the [-2^15, +2^15 -1] range', 32, NULL, NULL, NULL),
(213, NULL, 'C99', 'int64_t', '', 'Capable of containing the [-2^63, +2^63 -1] range', 64, NULL, NULL, NULL),
(214, NULL, 'C99', 'int128_t', '', 'Capable of containing the [-2^127, +2^127 -1] range', 128, NULL, NULL, NULL),
(220, NULL, 'C99', 'float', '', 'The IEEE 754 single-precision binary floating-point format', 32, NULL, NULL, NULL),
(221, NULL, 'C99', 'double', '', 'The IEEE 754 double-precision binary floating-point format', 64, NULL, NULL, NULL),
(300, NULL, 'SCOS-2000', 'Absolute time CDS w/o μs', '', 'Absolute time CDS format without microseconds', 48, NULL, NULL, NULL),
(301, NULL, 'SCOS-2000', 'Absolute time CDS with μs', '', 'Absolute time CDS format with microseconds', 64, NULL, NULL, NULL),
(302, NULL, 'SCOS-2000', 'Absolute time CUC (1/0)', '', 'Absolute time CUC format (1 byte coarse time, 0 byte fine time)', 8, NULL, NULL, NULL),
(303, NULL, 'SCOS-2000', 'Absolute time CUC (1/1)', '', 'Absolute time CUC format (1 byte coarse time, 1 byte fine time)', 16, NULL, NULL, NULL),
(304, NULL, 'SCOS-2000', 'Absolute time CUC (1/2)', '', 'Absolute time CUC format (1 byte coarse time, 2 byte fine time)', 24, NULL, NULL, NULL),
(305, NULL, 'SCOS-2000', 'Absolute time CUC (1/3)', '', 'Absolute time CUC format (1 byte coarse time, 3 byte fine time)', 32, NULL, NULL, NULL),
(306, NULL, 'SCOS-2000', 'Absolute time CUC (2/0)', '', 'Absolute time CUC format (2 byte coarse time, 0 byte fine time)', 16, NULL, NULL, NULL),
(307, NULL, 'SCOS-2000', 'Absolute time CUC (2/1)', '', 'Absolute time CUC format (2 byte coarse time, 1 byte fine time)', 24, NULL, NULL, NULL),
(308, NULL, 'SCOS-2000', 'Absolute time CUC (2/2)', '', 'Absolute time CUC format (2 byte coarse time, 2 byte fine time)', 32, NULL, NULL, NULL),
(309, NULL, 'SCOS-2000', 'Absolute time CUC (2/3)', '', 'Absolute time CUC format (2 byte coarse time, 3 byte fine time)', 40, NULL, NULL, NULL),
(310, NULL, 'SCOS-2000', 'Absolute time CUC (3/0)', '', 'Absolute time CUC format (3 byte coarse time, 0 byte fine time)', 24, NULL, NULL, NULL),
(311, NULL, 'SCOS-2000', 'Absolute time CUC (3/1)', '', 'Absolute time CUC format (3 byte coarse time, 1 byte fine time)', 32, NULL, NULL, NULL),
(312, NULL, 'SCOS-2000', 'Absolute time CUC (3/2)', '', 'Absolute time CUC format (3 byte coarse time, 2 byte fine time)', 40, NULL, NULL, NULL),
(313, NULL, 'SCOS-2000', 'Absolute time CUC (3/3)', '', 'Absolute time CUC format (3 byte coarse time, 3 byte fine time)', 48, NULL, NULL, NULL),
(314, NULL, 'SCOS-2000', 'Absolute time CUC (3/4)', '', 'Absolute time CUC format (3 byte coarse time, 4 byte fine time)', 56, NULL, NULL, NULL),
(315, NULL, 'SCOS-2000', 'Absolute time CUC (4/0)', '', 'Absolute time CUC format (4 byte coarse time, 0 byte fine time)', 32, NULL, NULL, NULL),
(316, NULL, 'SCOS-2000', 'Absolute time CUC (4/1)', '', 'Absolute time CUC format (4 byte coarse time, 1 byte fine time)', 40, NULL, NULL, NULL),
(317, NULL, 'SCOS-2000', 'Absolute time CUC (4/2)', '', 'Absolute time CUC format (4 byte coarse time, 2 byte fine time)', 48, NULL, NULL, NULL),
(318, NULL, 'SCOS-2000', 'Absolute time CUC (4/3)', '', 'Absolute time CUC format (4 byte coarse time, 3 byte fine time)', 56, NULL, NULL, NULL),
(320, NULL, 'SCOS-2000', 'Relative time CUC (1/0)', '', 'Relative time CUC format (1 byte coarse time, 0 byte fine time)', 8, NULL, NULL, NULL),
(321, NULL, 'SCOS-2000', 'Relative time CUC (1/1)', '', 'Relative time CUC format (1 byte coarse time, 1 byte fine time)', 16, NULL, NULL, NULL),
(322, NULL, 'SCOS-2000', 'Relative time CUC (1/2)', '', 'Relative time CUC format (1 byte coarse time, 2 byte fine time)', 24, NULL, NULL, NULL),
(323, NULL, 'SCOS-2000', 'Relative time CUC (1/3)', '', 'Relative time CUC format (1 byte coarse time, 3 byte fine time)', 32, NULL, NULL, NULL),
(324, NULL, 'SCOS-2000', 'Relative time CUC (2/0)', '', 'Relative time CUC format (2 byte coarse time, 0 byte fine time)', 16, NULL, NULL, NULL),
(325, NULL, 'SCOS-2000', 'Relative time CUC (2/1)', '', 'Relative time CUC format (2 byte coarse time, 1 byte fine time)', 24, NULL, NULL, NULL),
(326, NULL, 'SCOS-2000', 'Relative time CUC (2/2)', '', 'Relative time CUC format (2 byte coarse time, 2 byte fine time)', 32, NULL, NULL, NULL),
(327, NULL, 'SCOS-2000', 'Relative time CUC (2/3)', '', 'Relative time CUC format (2 byte coarse time, 3 byte fine time)', 40, NULL, NULL, NULL),
(328, NULL, 'SCOS-2000', 'Relative time CUC (3/0)', '', 'Relative time CUC format (3 byte coarse time, 0 byte fine time)', 24, NULL, NULL, NULL),
(329, NULL, 'SCOS-2000', 'Relative time CUC (3/1)', '', 'Relative time CUC format (3 byte coarse time, 1 byte fine time)', 32, NULL, NULL, NULL),
(330, NULL, 'SCOS-2000', 'Relative time CUC (3/2)', '', 'Relative time CUC format (3 byte coarse time, 2 byte fine time)', 40, NULL, NULL, NULL),
(331, NULL, 'SCOS-2000', 'Relative time CUC (3/3)', '', 'Relative time CUC format (3 byte coarse time, 3 byte fine time)', 48, NULL, NULL, NULL),
(332, NULL, 'SCOS-2000', 'Relative time CUC (3/4)', '', 'Relative time CUC format (3 byte coarse time, 4 byte fine time)', 56, NULL, NULL, NULL),
(333, NULL, 'SCOS-2000', 'Relative time CUC (4/0)', '', 'Relative time CUC format (4 byte coarse time, 0 byte fine time)', 32, NULL, NULL, NULL),
(334, NULL, 'SCOS-2000', 'Relative time CUC (4/1)', '', 'Relative time CUC format (4 byte coarse time, 1 byte fine time)', 40, NULL, NULL, NULL),
(335, NULL, 'SCOS-2000', 'Relative time CUC (4/2)', '', 'Relative time CUC format (4 byte coarse time, 2 byte fine time)', 48, NULL, NULL, NULL),
(336, NULL, 'SCOS-2000', 'Relative time CUC (4/3)', '', 'Relative time CUC format (4 byte coarse time, 3 byte fine time)', 56, NULL, NULL, NULL),
(340, NULL, 'SCOS-2000', 'Unsigned Integer', '', 'Unsigned integer parameter', NULL, NULL, NULL, NULL),
(341, NULL, 'SCOS-2000', 'Signed Integer', '', 'Signed integer parameter', NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
