-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2019 at 07:21 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sh`
--
CREATE DATABASE IF NOT EXISTS `sh` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `sh`;

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `unid` varchar(4) NOT NULL DEFAULT '',
  `setid` varchar(4) NOT NULL DEFAULT '',
  `code` int(4) NOT NULL,
  `text` text NOT NULL,
  `time` varchar(25) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `unid` (`unid`),
  KEY `setid` (`setid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `unid` varchar(4) NOT NULL,
  `setid` varchar(4) NOT NULL,
  `level` varchar(16) NOT NULL,
  `event` varchar(128) NOT NULL,
  `time` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `time` varchar(25) NOT NULL DEFAULT '',
  `lastTimeRun` varchar(25) NOT NULL DEFAULT '',
  `nextTimeRun` varchar(25) NOT NULL DEFAULT '',
  `script_name` varchar(64) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `type`, `time`, `lastTimeRun`, `nextTimeRun`, `script_name`, `active`) VALUES
(-3, 2, '0 3 */5 * *', '2019.10.19 17:14:01', '2019.10.21 03:00:00', 'SYS/updateCheck.py', 1),
(-2, 2, '0 3 */14 * *', '', '2019.10.29 03:00:00', 'SYS/clearOldValuesDb.py', 1),
(-1, 2, '*/1 * * * *', '2019.10.20 20:06:00', '2019.10.20 20:07:00', 'SYS/checkUnitsAlive.py', 1),
(1, 1, '2019.10.02 17:04:35', '2019.10.02 17:04:35', '', 'power/allUp.py', 0);

-- --------------------------------------------------------

--
-- Table structure for table `triggers`
--

CREATE TABLE IF NOT EXISTS `triggers` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `unid` varchar(4) NOT NULL DEFAULT '',
  `setid` varchar(4) NOT NULL DEFAULT '',
  `type` varchar(11) NOT NULL,
  `value` varchar(18) NOT NULL,
  `direct` int(1) NOT NULL DEFAULT '0' COMMENT '0 - both, 1 - get, 2 - set',
  `scriptName` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `units_def`
--

CREATE TABLE IF NOT EXISTS `units_def` (
  `unid` varchar(4) NOT NULL,
  `description` varchar(34) NOT NULL,
  `units` varchar(8) NOT NULL,
  `valueType` varchar(32) NOT NULL DEFAULT 'int' COMMENT 'int - integer positive value, sint - signed integer, str - string. Types splits with '';'' if numerous o values used',
  `direction` varchar(2) NOT NULL DEFAULT '',
  `timeout` int(4) NOT NULL DEFAULT '1440',
  `type` varchar(10) NOT NULL DEFAULT '',
  `possValues` varchar(32) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT 'sensor.png',
  PRIMARY KEY (`unid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `units_def`
--

INSERT INTO `units_def` (`unid`, `description`, `units`, `valueType`, `direction`, `timeout`, `type`, `possValues`, `icon`) VALUES
('0014', 'Датчик открытого пламени', '', 'int', 'I', 1440, '', '1,0', 'icons8-fire-100-2.png'),
('0028', 'Датчик наличия угарного газа MQ-4', '', 'int', 'I', 1440, '', '1,0', 'icons8-smoke-100.png'),
('003C', 'Датчик открытия двери', '', 'int', 'IO', 1440, '', '1,0', 'icons8-door-sensor-alarmed-48.png'),
('0050', 'Датчик движения микроволновый', '', 'int', 'IO', 1440, '', '1,0', 'icons8-stick-figure-running-100.png'),
('0064', 'Датчик движения оптический', '', 'int', 'IO', 1440, '', '1,0', 'icons8-stick-figure-running-100.png'),
('0078', 'GSM-модем', '', 'str', 'IO', 1440, '', '', 'icons8-sim-card-100.png'),
('008C', 'Система видео наблюдения WansCam', '', 'str', 'IO', 1440, '', '0,1', 'icons8-cctv-100.png'),
('00A0', 'Датчик заряда внутренней батареи', 'V', 'int', 'I', 5, '', '', 'icons8-half-charged-battery-100.png'),
('00B4', 'Задатчик температуры на котле SG90', '°', 'int', 'O', 1440, '', '8:1:25', 'icons8-heating-automation-100.png'),
('00C8', 'Датчик наличия внешнего питания', '', 'int', 'I', 5, '', '0,1', 'icons8-electricity-power-100.png'),
('00DC', 'NFC-модуль', '', 'str', 'I', 5, '', '', 'icons8-nfc-tag-100.png'),
('00F0', 'Датчик уровня топлива в ёмкости', '%', 'int', 'I', 10, '', '', 'icons8-gasoline-pump-100.png'),
('0104', 'Кнопка-переключатель', '', 'int', 'I', 1440, '', '', 'icons8-switch-100.png'),
('0118', 'Кнопка', '', 'int', 'I', 1440, '', '', 'icons8-switch-100.png'),
('012C', 'Модуль реле', '', 'int', 'O', 1440, '', '0,1', 'icons8-switch-100.png'),
('0140', 'Датчик интенсивности света', 'лкс', 'int', 'I', 20, '', '', 'icons8-solar-panel-48.png'),
('0154', 'Термометр и датчик влажности DHT22', '°;%', 'sint;int', 'I', 20, '', '', 'icons8-dew-point-100.png'),
('0168', 'Датчик дождя', 'мм/сут', 'int', 'I', 120, '', '', 'icons8-rain-100.png'),
('017C', 'Датчик уровня жидкости в почве', '%', 'int', 'I', 120, '', '', 'icons8-water-hose-48.png'),
('0190', 'Датчик влажности', '%', 'int', 'I', 30, '', '', 'icons8-humidity-100.png'),
('01A4', 'Датчик атмосферного давления', 'мм.рт.ст', 'int', 'I', 30, '', '', 'icons8-atmospheric-pressure-100.png'),
('01B8', 'Цифровой термометр на базе DS1820B', '°', 'sint', 'I', 30, '', '', 'icons8-temperature-100.png'),
('01C8', 'LCD-модуль с клавиатурой', '', 'str', 'IO', 30, '', '', 'icons8-display-filled-100.png');

-- --------------------------------------------------------

--
-- Table structure for table `units_run`
--

CREATE TABLE IF NOT EXISTS `units_run` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `unid` varchar(4) NOT NULL COMMENT 'Unit ID depend on its type',
  `setid` varchar(4) NOT NULL COMMENT 'ID of unit, set at first auth. Use for determine from a few of same type units',
  `lastValue` varchar(64) NOT NULL DEFAULT '' COMMENT 'The last value was recieved/set',
  `needSetValue` int(1) NOT NULL DEFAULT '0',
  `lastTime` varchar(25) NOT NULL DEFAULT '' COMMENT 'TimeStamp lastValue was set/recieved',
  `interface` int(2) NOT NULL DEFAULT '0' COMMENT 'ID of interface the device connected',
  `timeAdded` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL COMMENT 'Friendly name of sensor, user specify. E.g.: termometer outside or heating sink relay',
  `alive` int(1) NOT NULL DEFAULT '0' COMMENT 'Is unit alive: if time since lastValue is less then unit timeout',
  `sectId` int(3) NOT NULL DEFAULT '1',
  `uiShow` int(1) NOT NULL DEFAULT '1',
  `color` varchar(6) NOT NULL DEFAULT '2ecc71',
  `iconCust` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `setid` (`setid`) USING BTREE,
  KEY `unid` (`unid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `units_sections`
--

CREATE TABLE IF NOT EXISTS `units_sections` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `isDefHidden` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `units_sections`
--

INSERT INTO `units_sections` (`id`, `name`, `isDefHidden`) VALUES
(1, 'Default', 0);

-- --------------------------------------------------------

--
-- Table structure for table `units_values`
--

CREATE TABLE IF NOT EXISTS `units_values` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `unrId` int(5) NOT NULL,
  `value` varchar(64) NOT NULL,
  `timeStamp` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `unrId` (`unrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL DEFAULT '',
  `isSetAble` int(1) NOT NULL DEFAULT '1',
  `isAdmin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `email`, `isSetAble`, `isAdmin`) VALUES
(1, 'admin', '', 'admin@localhost', 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
