-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 20, 2019 at 03:41 PM
-- Server version: 5.5.35
-- PHP Version: 5.4.6-1ubuntu1.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wpmu_culturadigital`
--

-- --------------------------------------------------------

--
-- Table structure for table `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `id` int(11) NOT NULL,
  `nome` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sigla` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `uf`
--

INSERT INTO `uf` (`id`, `nome`, `sigla`) VALUES
(35, 'São Paulo', 'SP'),
(41, 'Paraná', 'PR'),
(42, 'Santa Catarina', 'SC'),
(43, 'Rio Grande do Sul', 'RS'),
(50, 'Mato Grosso do Sul', 'MS'),
(11, 'Rondônia', 'RO'),
(12, 'Acre', 'AC'),
(13, 'Amazonas', 'AM'),
(14, 'Roraima', 'RR'),
(15, 'Pará', 'PA'),
(16, 'Amapá', 'AP'),
(17, 'Tocantins', 'TO'),
(21, 'Maranhão', 'MA'),
(24, 'Rio Grande do Norte', 'RN'),
(25, 'Paraíba', 'PB'),
(26, 'Pernambuco', 'PE'),
(27, 'Alagoas', 'AL'),
(28, 'Sergipe', 'SE'),
(29, 'Bahia', 'BA'),
(31, 'Minas Gerais', 'MG'),
(33, 'Rio de Janeiro', 'RJ'),
(51, 'Mato Grosso', 'MT'),
(52, 'Goiás', 'GO'),
(53, 'Distrito Federal', 'DF'),
(22, 'Piauí', 'PI'),
(23, 'Ceará', 'CE'),
(32, 'Espírito Santo', 'ES');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
