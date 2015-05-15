-- phpMyAdmin SQL Dump
-- version 4.4.6.1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost:3306
-- Vytvořeno: Pát 15. kvě 2015, 20:39
-- Verze serveru: 5.6.23-1~dotdeb.3
-- Verze PHP: 5.6.8-1~dotdeb+wheezy.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `czcafe`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `desk_id` int(11) NOT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `booking`
--

INSERT INTO `booking` (`id`, `customer_id`, `time`, `desk_id`, `finished`) VALUES
(1, 3, '2015-03-20 18:00:00', 1, 0),
(2, 3, '2015-03-20 19:00:00', 7, 0),
(5, 3, '2015-04-04 15:30:00', 6, 0),
(6, 3, '2015-04-04 13:28:21', 6, 0),
(7, 3, '2015-04-04 15:15:22', 6, 0),
(8, 3, '1970-01-01 01:00:00', 2, 0),
(9, 3, '1970-01-01 01:00:00', 1, 0),
(10, 3, '1970-01-01 01:00:00', 3, 0),
(11, 3, '2015-04-04 17:13:14', 9, 0),
(12, 3, '2015-04-04 17:13:30', 8, 0),
(13, 3, '2015-04-04 17:13:44', 4, 0),
(14, 3, '2015-04-04 17:13:59', 3, 0),
(15, 3, '2015-04-07 10:59:53', 7, 0),
(16, 3, '2015-04-07 11:00:32', 9, 0),
(17, 3, '2015-04-07 11:08:18', 8, 0),
(18, 3, '2015-04-07 15:07:36', 9, 0),
(19, 3, '2015-04-07 16:02:07', 3, 0),
(20, 15, '2015-04-08 20:09:05', 4, 0),
(22, 16, '2015-04-09 10:21:13', 4, 0),
(23, 16, '2015-04-09 10:00:38', 6, 0),
(24, 15, '2015-04-09 10:57:30', 7, 0),
(25, 16, '2015-04-09 11:56:38', 3, 0),
(26, 17, '2015-04-14 17:59:35', 2, 0),
(27, 17, '2015-04-14 17:00:04', 4, 0),
(29, 3, '2015-04-12 19:00:00', 8, 0),
(30, 3, '2015-04-17 18:00:00', 5, 0),
(31, 3, '2015-04-12 18:00:00', 9, 0),
(32, 3, '2015-04-19 18:00:00', 7, 0),
(35, 3, '2015-04-12 18:00:00', 5, 0),
(36, 3, '2015-04-12 18:00:00', 2, 0),
(38, 3, '2015-04-18 19:00:00', 9, 0),
(40, 16, '2015-04-20 19:21:14', 8, 0),
(41, 15, '2015-04-25 21:08:44', 12, 0),
(43, 15, '2015-04-21 16:17:21', 12, 0),
(44, 15, '2015-04-21 16:17:26', 10, 0),
(45, 32, '2015-04-21 16:19:41', 13, 0),
(49, 15, '2015-04-25 12:51:15', 4, 0),
(51, 16, '2015-04-24 16:00:00', 12, 0),
(52, 15, '2015-04-23 18:00:00', 12, 0),
(53, 15, '2015-04-23 19:00:00', 5, 0),
(54, 15, '2015-04-23 20:00:00', 3, 0),
(56, 16, '2015-04-29 16:00:00', 7, 0),
(58, 15, '2015-04-26 18:00:00', 8, 0),
(59, 15, '2015-04-26 15:00:00', 5, 0),
(60, 3, '2015-05-01 16:00:00', 8, 0),
(61, 16, '2015-04-29 16:00:00', 12, 0),
(62, 3, '2015-04-29 17:00:00', 5, 0),
(63, 3, '2015-04-29 09:00:00', 4, 0),
(64, 3, '2015-04-30 18:00:00', 2, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `uri` varchar(25) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`, `active`, `uri`) VALUES
(1, 'Kávy', 1, 'kavy'),
(2, 'Čaje', 1, 'caje'),
(3, 'Alkohol', 1, 'alkohol'),
(4, 'Dezerty', 1, 'dezerty'),
(5, 'Zmrzliny', 1, 'zmrzliny'),
(6, 'Teplý', 0, 'teply'),
(7, 'zajm', 0, 'zajm'),
(9, 'znak', 0, 'znak'),
(10, 'lama', 0, 'lama');

-- --------------------------------------------------------

--
-- Struktura tabulky `category_product`
--

CREATE TABLE IF NOT EXISTS `category_product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `category_product`
--

INSERT INTO `category_product` (`product_id`, `category_id`) VALUES
(1, 2),
(1, 3),
(1, 6),
(3, 1),
(3, 4),
(3, 6),
(13, 1),
(13, 6),
(19, 1),
(19, 6),
(20, 3),
(21, 3),
(21, 9),
(24, 1),
(24, 6),
(29, 3),
(29, 5),
(29, 9),
(29, 10);

-- --------------------------------------------------------

--
-- Struktura tabulky `desk`
--

CREATE TABLE IF NOT EXISTS `desk` (
  `id` int(11) NOT NULL,
  `seats` int(11) NOT NULL DEFAULT '2',
  `smoking` tinyint(1) NOT NULL DEFAULT '1',
  `indoor` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `desk`
--

INSERT INTO `desk` (`id`, `seats`, `smoking`, `indoor`, `active`) VALUES
(1, 2, 1, 1, 1),
(2, 6, 1, 1, 1),
(3, 8, 0, 1, 1),
(4, 4, 1, 0, 1),
(5, 2, 1, 1, 1),
(6, 4, 1, 0, 0),
(7, 4, 1, 1, 1),
(8, 4, 0, 1, 1),
(9, 2, 1, 1, 0),
(10, 20, 0, 1, 0),
(11, 15, 1, 1, 0),
(12, 50, 0, 0, 1),
(13, 8, 0, 0, 0),
(14, 8, 1, 0, 0),
(15, 2, 1, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pickup_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `solved` timestamp NULL DEFAULT NULL,
  `prepared` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `order`
--

INSERT INTO `order` (`id`, `product_id`, `customer_id`, `employee_id`, `creation_date`, `pickup_time`, `solved`, `prepared`) VALUES
(1, 1, 3, 15, '2015-02-18 12:25:33', '2015-02-18 12:35:00', NULL, '2015-04-08 12:11:38'),
(2, 1, 3, NULL, '2015-02-18 12:27:09', '2015-02-18 12:42:00', NULL, '2015-04-08 12:11:38'),
(3, 1, 3, NULL, '2015-02-18 12:27:18', '2015-02-18 12:42:00', NULL, '2015-04-08 12:11:38'),
(4, 1, 3, NULL, '2015-02-18 12:27:20', '2015-02-18 12:42:00', NULL, '2015-04-08 12:11:38'),
(5, 1, 3, NULL, '2015-02-18 12:27:24', '2015-02-18 12:45:00', NULL, '2015-04-08 12:11:38'),
(6, 1, 3, 15, '2015-02-18 13:06:50', '2015-04-02 00:31:00', NULL, '2015-04-08 12:11:38'),
(7, 1, 3, NULL, '2015-02-18 13:08:46', '2015-02-18 13:23:00', NULL, '2015-04-08 12:11:38'),
(8, 1, 3, NULL, '2015-02-18 18:31:18', '2015-02-18 18:46:00', NULL, '2015-04-08 12:11:38'),
(9, 1, 3, NULL, '2015-02-23 11:12:33', '2015-02-23 11:24:00', NULL, '2015-04-08 12:11:38'),
(10, 1, 3, 15, '2015-02-23 11:13:19', '2015-02-23 11:28:00', NULL, '2015-04-08 12:11:38'),
(11, 1, 3, NULL, '2015-02-23 11:13:42', '2015-02-23 11:28:00', NULL, '2015-04-08 12:11:38'),
(12, 1, 3, NULL, '2015-02-23 11:14:28', '2015-02-23 11:29:00', NULL, '2015-04-08 12:11:38'),
(13, 1, 3, NULL, '2015-02-23 11:15:02', '2015-02-23 11:29:00', NULL, '2015-04-08 12:11:38'),
(117, 1, 3, 15, '2014-09-10 23:32:28', '2014-09-11 01:25:00', NULL, '2015-04-08 12:11:38'),
(120, 1, 3, NULL, '2014-09-10 23:33:40', '2014-09-10 23:43:00', NULL, '2015-04-08 12:11:38'),
(124, 1, 3, NULL, '2014-09-11 12:48:40', '2014-09-11 12:58:00', NULL, '2015-04-08 12:11:38'),
(126, 13, 3, 15, '2014-09-12 20:36:07', '2014-09-12 20:45:00', NULL, '2015-04-08 12:11:38'),
(127, 3, 3, NULL, '2014-09-12 20:36:24', '2014-09-12 20:46:00', NULL, '2015-04-08 12:11:38'),
(128, 13, 3, NULL, '2014-09-13 00:46:22', '2014-09-13 09:04:00', NULL, '2015-04-08 12:11:38'),
(129, 1, 3, NULL, '2014-09-13 20:55:10', '2014-09-13 21:05:00', NULL, '2015-04-08 12:11:38'),
(130, 13, 3, NULL, '2014-09-13 20:55:40', '2014-09-13 21:04:00', '2015-04-01 23:09:00', '2015-04-08 12:11:38'),
(131, 3, 3, 15, '2014-09-13 20:57:26', '2014-09-13 21:07:00', '2015-04-01 23:09:21', '2015-04-08 12:11:38'),
(132, 1, 3, NULL, '2014-09-16 12:14:15', '2014-09-16 12:24:00', '2015-04-02 10:18:42', '2015-04-08 12:11:38'),
(133, 3, 3, NULL, '2014-09-16 13:07:03', '2014-09-16 13:17:00', '2015-04-02 10:18:48', '2015-04-08 12:11:38'),
(134, 1, 3, NULL, '2015-02-09 15:24:25', '2015-02-09 15:39:00', '2015-04-02 13:43:50', '2015-04-08 12:11:38'),
(135, 1, 3, NULL, '2015-02-09 15:27:04', '2015-02-09 15:42:00', '2015-04-02 15:02:53', '2015-04-08 12:11:38'),
(136, 13, 3, 15, '2015-02-09 15:29:30', '2015-04-01 20:00:00', NULL, '2015-04-08 12:11:38'),
(137, 1, 3, 15, '2015-03-20 23:24:39', '2015-03-20 23:39:00', NULL, '2015-04-08 12:11:38'),
(138, 19, 3, 15, '2015-03-30 17:01:12', '2015-03-30 17:16:00', NULL, '2015-04-08 12:11:38'),
(139, 20, 3, NULL, '2015-04-01 20:37:13', '2015-04-01 20:52:00', '2015-04-17 01:00:00', '2015-04-08 12:11:38'),
(140, 3, 3, 15, '2015-04-02 13:22:29', '2015-04-02 13:37:00', '2015-04-07 16:04:00', '2015-04-08 12:11:38'),
(141, 19, 3, 15, '2015-04-07 19:17:41', '1970-01-01 01:00:00', NULL, '2015-04-08 12:11:38'),
(142, 20, 3, 15, '2015-04-07 19:31:43', '2015-04-07 19:46:00', '2015-04-07 19:53:36', '2015-04-08 12:11:38'),
(143, 13, 3, 15, '2015-04-07 19:31:58', '2015-04-07 19:46:00', NULL, '2015-04-08 12:11:38'),
(144, 13, 3, 15, '2015-04-07 19:55:47', '2015-04-07 20:10:00', NULL, '2015-04-08 12:11:38'),
(145, 20, 3, NULL, '2015-04-07 19:56:04', '2015-04-07 20:11:00', NULL, '2015-04-08 12:11:38'),
(146, 19, 16, 15, '2015-04-08 08:51:59', '2015-04-08 09:06:00', '2015-04-08 09:45:07', '2015-04-08 12:11:38'),
(147, 13, 16, 15, '2015-04-08 08:52:18', '2015-04-08 09:07:00', '2015-04-08 11:32:54', '2015-04-08 12:11:38'),
(148, 19, 16, 15, '2015-04-08 09:02:10', '2015-04-08 09:17:00', '2015-04-08 11:33:02', '2015-04-08 12:11:38'),
(149, 1, 16, 15, '2015-04-08 09:13:08', '2015-04-08 09:28:00', '2015-04-08 11:32:58', '2015-04-08 12:11:38'),
(150, 20, 16, 15, '2015-04-08 09:35:06', '2015-04-08 09:50:00', '2015-04-08 11:32:55', '2015-04-08 12:11:38'),
(151, 19, 16, 15, '2015-04-08 10:37:43', '2015-04-08 10:52:00', '2015-04-08 11:32:59', '2015-04-08 12:11:38'),
(152, 3, 16, 15, '2015-04-08 10:42:22', '2015-04-08 10:57:00', '2015-04-08 11:33:30', '2015-04-08 12:11:38'),
(153, 1, 16, 15, '2015-04-08 10:42:53', '2015-04-08 10:57:00', '2015-04-08 11:42:43', '2015-04-08 12:11:38'),
(154, 3, 16, 15, '2015-04-08 11:18:58', '2015-04-08 11:33:00', '2015-04-08 11:42:46', '2015-04-08 12:11:38'),
(155, 1, 16, 15, '2015-04-08 11:21:50', '2015-04-08 11:36:00', '2015-04-08 11:33:05', '2015-04-08 12:11:38'),
(156, 19, 16, 15, '2015-04-08 11:24:11', '2015-04-08 11:39:00', '2015-04-08 11:33:07', '2015-04-08 12:11:38'),
(157, 20, 16, 15, '2015-04-08 11:27:23', '2015-04-08 11:42:00', '2015-04-08 11:33:10', '2015-04-08 12:11:38'),
(158, 3, 16, 15, '2015-04-08 11:28:31', '2015-04-08 11:43:00', '2015-04-08 12:22:12', '2015-04-08 12:11:38'),
(159, 19, 16, 15, '2015-04-08 11:33:48', '2015-04-08 11:48:00', '2015-04-08 12:44:04', '2015-04-08 12:11:38'),
(160, 19, 16, 15, '2015-04-08 11:43:01', '2015-04-08 11:57:00', '2015-04-08 12:44:05', '2015-04-08 12:22:18'),
(161, 20, 16, 15, '2015-04-08 11:44:11', '2015-04-08 11:59:00', '2015-04-08 12:44:06', '2015-04-08 12:11:38'),
(162, 20, 16, 15, '2015-04-08 11:45:09', '2015-04-08 12:00:00', '2015-04-08 12:44:09', '2015-04-08 12:11:38'),
(163, 3, 16, 15, '2015-04-08 11:45:46', '2015-04-08 12:00:00', '2015-04-08 12:44:02', '2015-04-08 12:21:54'),
(164, 13, 16, 15, '2015-04-08 12:23:48', '2015-04-08 12:38:00', NULL, '2015-04-08 12:44:44'),
(165, 1, 16, 15, '2015-04-08 12:44:16', '2015-04-08 12:59:00', '2015-04-07 18:37:00', '2015-04-08 18:31:10'),
(166, 1, 3, NULL, '2015-04-08 18:41:25', '2015-04-08 18:56:00', NULL, NULL),
(167, 19, 16, 15, '2015-04-09 09:12:30', '2015-04-09 09:27:00', '2015-04-09 10:08:25', '2015-04-09 09:20:36'),
(168, 20, 16, 15, '2015-04-09 09:12:57', '2015-04-09 09:27:00', '2015-04-09 09:20:32', '2015-04-09 09:13:10'),
(169, 19, 3, 15, '2015-04-09 09:23:46', '2015-04-09 14:11:00', NULL, '2015-04-09 14:05:57'),
(170, 1, 16, 15, '2015-04-09 10:00:28', '2015-04-09 10:15:00', '2015-04-09 10:09:41', '2015-04-09 10:08:43'),
(171, 3, 16, 15, '2015-04-09 10:00:52', '2015-04-09 10:16:00', '2015-04-09 10:09:54', '2015-04-09 10:09:48'),
(172, 13, 17, 15, '2015-04-09 13:57:32', '2015-04-09 15:00:00', NULL, '2015-04-09 14:02:42'),
(173, 20, 17, 15, '2015-04-09 14:01:56', '2015-04-09 14:43:00', '2015-04-09 14:05:06', '2015-04-09 14:02:12'),
(174, 3, 3, NULL, '2015-04-09 16:13:38', '2015-04-09 16:28:00', '2015-04-09 05:00:00', NULL),
(175, 19, 3, NULL, '2015-04-10 10:49:55', '2015-04-10 11:04:00', NULL, NULL),
(176, 21, 3, NULL, '2015-04-10 13:38:23', '2015-04-10 14:09:00', NULL, NULL),
(177, 21, 3, NULL, '2015-04-16 15:27:22', '2015-04-16 16:00:00', '2015-04-16 15:36:28', NULL),
(178, 21, 3, NULL, '2015-04-16 15:28:12', '2015-04-16 15:02:00', '0000-00-00 00:00:00', NULL),
(179, 1, 3, NULL, '2015-04-16 15:31:47', '2015-04-16 15:31:00', '2015-04-16 15:36:18', NULL),
(180, 21, 3, NULL, '2015-04-16 15:36:09', '2015-04-16 14:11:00', '2015-04-16 15:36:38', NULL),
(181, 1, 3, NULL, '2015-04-16 15:39:21', '2015-04-16 15:18:00', '2015-04-16 15:46:19', NULL),
(182, 1, 3, NULL, '2015-04-16 22:26:25', '2015-04-16 22:41:00', NULL, NULL),
(183, 1, 3, NULL, '2015-04-16 22:26:54', '2015-04-16 22:41:00', NULL, NULL),
(184, 1, 16, NULL, '2015-04-20 21:43:47', '2015-04-20 21:58:00', '0000-00-00 00:00:00', NULL),
(185, 24, 16, NULL, '2015-04-21 07:38:19', '2015-04-21 07:53:15', '0000-00-00 00:00:00', NULL),
(186, 21, 16, NULL, '2015-04-21 07:40:06', '2015-04-21 08:15:03', '0000-00-00 00:00:00', NULL),
(187, 3, 16, NULL, '2015-04-21 08:29:11', '2015-04-21 08:44:06', '0000-00-00 00:00:00', NULL),
(188, 24, 16, 15, '2015-04-21 14:46:08', '2015-04-21 15:01:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(189, 13, 32, NULL, '2015-04-21 15:19:42', '2015-04-21 15:34:00', NULL, NULL),
(190, 3, 16, NULL, '2015-04-21 18:32:38', '2015-04-21 18:47:27', NULL, NULL),
(191, 3, 16, NULL, '2015-04-21 21:36:13', '2015-04-21 19:47:00', NULL, NULL),
(192, 1, 16, NULL, '2015-04-21 21:36:42', '2015-04-21 20:51:00', '2015-05-02 16:00:00', NULL),
(193, 19, 16, 15, '2015-04-22 12:50:28', '2015-04-22 13:05:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(194, 24, 16, NULL, '2015-04-22 12:55:12', '2015-04-22 13:10:06', NULL, NULL),
(195, 21, 16, NULL, '2015-04-23 19:59:48', '2015-04-23 20:34:00', '0000-00-00 00:00:00', NULL),
(196, 1, 16, NULL, '2015-04-23 20:12:00', '2015-04-23 20:26:00', '0000-00-00 00:00:00', NULL),
(197, 1, 16, NULL, '2015-04-23 21:27:56', '2015-04-23 21:42:00', '0000-00-00 00:00:00', NULL),
(198, 3, 3, NULL, '2015-04-25 07:55:34', '2015-04-25 08:10:00', '0000-00-00 00:00:00', NULL),
(199, 3, 3, NULL, '2015-04-25 07:58:03', '2015-04-25 08:13:00', '0000-00-00 00:00:00', NULL),
(200, 3, 3, NULL, '2015-04-25 07:58:10', '2015-04-25 08:13:00', '0000-00-00 00:00:00', NULL),
(201, 19, 3, NULL, '2015-04-25 09:09:40', '2015-04-25 09:24:00', NULL, NULL),
(202, 3, 3, NULL, '2015-04-28 10:09:46', '2015-04-28 10:24:00', NULL, NULL),
(203, 1, 3, NULL, '2015-04-28 10:10:10', '2015-04-28 10:25:00', '2015-05-10 12:00:00', NULL),
(204, 1, 3, NULL, '2015-04-28 10:10:21', '2015-04-28 10:25:00', NULL, NULL),
(205, 20, 3, 15, '2015-04-29 12:43:30', '2015-04-29 12:58:00', NULL, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `price` decimal(10,0) DEFAULT NULL,
  `ingredients` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `img_ext` varchar(10) NOT NULL,
  `uri` varchar(50) NOT NULL,
  `preparation_time` int(11) DEFAULT NULL,
  `manager_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `ingredients`, `active`, `img_ext`, `uri`, `preparation_time`, `manager_id`) VALUES
(1, 'Grog', 'Bylinkový čaj s citronem a je do něj nalit panák rumu', '31', 'Voda, bylinky, rum, citron, med', 1, 'jpg', 'grog', 5, 31),
(3, 'Turecká káva', 'Káva připravovaná zalitím a spařením mleté zrnkové kávy vroucí vodou. Součástí přípravy je namletí zrnkové kávy.', '23', 'Voda, káva, cukr', 1, 'jpg', 'turecka-kava', 5, 14),
(13, 'Vídeňská káva', 'Nápoj s dlouholetou historií je charakteristický štědrou porcí šlehačky, která mu dodává nejen neobvyklý, majestátní vzhled s nádechem luxusu, ale díky tomu, že šlehačka nahrazuje zároveň mléko i cukr, tak i chuť.', '29', 'Voda, káva, šlehaná smetana, práškové kakao', 1, 'png', 'videnska-kava', 8, 14),
(19, 'Latte Macchiato', 'Dal bych si jedno latéčko.', '25', 'šlehačka', 1, 'jpg', 'latte-macchiato', 10, 14),
(20, 'Pilsner Urquell 0,5 l', 'Pivo pilsner Urquell 0,5 l točené', '29', 'voda, slad, cukr', 1, 'jpg', 'pilsner-urquell-0-5-l', 2, 14),
(21, 'teff', 'Nápoj s dlouholetou historií je charakteristický štědrou porcí šlehačky, která mu dodává nejen neobvyklý, majestátní vzhled s nádechem luxusu, ale díky tomu, že šlehačka nahrazuje zároveň mléko i cukr, tak i chuť.', '454', 'dffdfdfdfdkfjdkfdfjdfldlfd', 1, 'jpg', 'teff', 30, 14),
(24, 'Latte Machiatte', 'Duis elementum eros a tincidunt tristique. Donec mattis dignissim arcu id viverra. Suspendisse a magna velit. Nulla sit amet nulla enim. In at justo in metus iaculis maximus. Maecenas sit amet commodo neque, nec mollis leo. In sagittis quis nulla eu egestas. Sed finibus lobortis tincidunt.', '35', 'fdfjd', 1, 'jpg', 'latte-machiatte', 9, 14),
(29, 'xobrtlik', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vel turpis at nulla scelerisque rhoncus. Cras dignissim mollis libero convallis iaculis. Vestibulum in feugiat magna, id gravida felis. Nullam ut diam nec justo lacinia ultrices. Donec erat leo, tempor ut laoreet lacinia, interdum nec libero. Cras hendrerit ac turpis vitae iaculis. Morbi mattis sem non mauris gravida cursus. Nulla eleifend ut enim quis gravida. Aliquam egestas eu nisl a pretium. Nullam sit amet ultricies dui. Etiam id ultrices erat. Quisque viverra auctor urna quis hendrerit. Suspendisse potenti.', '25', 'šlehačka', 0, 'png', 'xobrtlik', NULL, 14);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `password` varchar(60) NOT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `role` enum('customer','manager','employee') DEFAULT 'customer',
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `email`, `name`, `surname`, `password`, `newsletter`, `active`, `gender`, `role`, `registered`, `blocked`) VALUES
(3, 'ahoj', 'Radim', 'Zřídlo', '$2y$10$kfgn3idCPiLs8K1i0x1pteTPSNLLWvLXRf97bhQ2WadHq9AupBUZS', 1, 1, 1, 'customer', '2015-04-01 10:59:59', 0),
(10, 'auto@atuo.cz', 'Radim', 'Zp', '$2y$10$v5fyq2bCoW/qxejNstcb..RNdKrTPO5OK3DGsmiU7fSPgLxXU4fvS', 1, 0, 1, 'customer', '2015-04-01 10:59:59', 0),
(12, 'pat@dar.cz', 'Patrik', 'Darek', '$2y$10$PYs.JWkCBblfHRP5gBGQcO./iGrUC2dpR4Nb.ug5VzLhT7irTpQbS', 0, 0, 1, 'employee', '2015-04-01 10:59:59', 0),
(13, 'fdjkl@dffd.cu', 'Grog', 'Smažil', '$2y$10$pVsyXl3IqX26.bZjoqgiEul5nYjeGUQCpm3Cyw.CbpNONVQ9W3CM.', 0, 0, 1, 'customer', '2015-04-01 10:59:59', 0),
(14, 'manager@cafe.cz', 'Radek', 'Krůta', '$2y$10$kfgn3idCPiLs8K1i0x1pteTPSNLLWvLXRf97bhQ2WadHq9AupBUZS', 0, 1, 1, 'manager', '2015-04-01 10:59:59', 0),
(15, 'employee@cafe.cz', 'Světlana', 'Nárožná', '$2y$10$kfgn3idCPiLs8K1i0x1pteTPSNLLWvLXRf97bhQ2WadHq9AupBUZS', 0, 1, 0, 'employee', '2015-04-01 10:59:59', 0),
(16, 'zdar@zdar.cz', 'Albert', 'Manek', '$2y$10$arJWffWpKHXsB48Sjf3njOXTdvvY.Ql4ozfIz3EN1UR6bHgXeTzBO', 1, 1, 1, 'customer', '2015-04-08 08:20:11', 0),
(17, 'barca7@gmail.com', 'Barborka', 'Chumlenková', '$2y$10$XkP6GbM8sFb6XtscXi94GO9xotL1CWh3ceJrdEwZAgTdWB8vSY2Lu', 0, 0, 0, 'customer', '2015-04-09 13:56:19', 0),
(29, 'peca@localhost.cz', 'aldkf', 'kdfjf', '$2y$10$9G/cGqPT8K8By0INEcONhu4Qh2TPgR259cu5TQ1LphcpzBIEY5Olu', 0, 1, 1, 'customer', '2015-04-10 11:10:44', 0),
(30, 'kkvap@gmail.com', 'Katka', 'Kvapilová', '$2y$10$gkfky1zgVVFhG5goxJ5PfeaW4pS5dNbNlHBhkt50J6eOZU7BN/7xa', 1, 0, 0, 'customer', '2015-04-15 23:02:28', 0),
(31, 'manazer@cafe.cz', 'Alfons', 'Mucha', '$2y$10$kfgn3idCPiLs8K1i0x1pteTPSNLLWvLXRf97bhQ2WadHq9AupBUZS', 0, 1, 0, 'manager', '2015-04-18 22:20:49', 0),
(32, 'james.alex@centrum.cz', 'James', 'Alex', '$2y$10$nH6/8trI1rtoDldM4PJ5q.jX5gAyTXazY.wgmna.bOY9gmt9R3rOC', 0, 1, 1, 'customer', '2015-04-21 15:18:23', 0),
(33, 'g2916002@trbvm.com', 'Petrohard', 'Radek', '$2y$10$QU3CAsWAoN5rpjCMYMOi..pc2Pv5DC1uchXU.GtZ6sNYtnnm445d2', 0, 0, 1, 'customer', '2015-05-03 12:03:37', 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `desk_id` (`desk_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`category_id`,`product_id`),
  ADD KEY `category_product_ibfk_1` (`product_id`);

--
-- Klíče pro tabulku `desk`
--
ALTER TABLE `desk`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `product_id_2` (`product_id`);

--
-- Klíče pro tabulku `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pro tabulku `desk`
--
ALTER TABLE `desk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT pro tabulku `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=206;
--
-- AUTO_INCREMENT pro tabulku `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`desk_id`) REFERENCES `desk` (`id`);

--
-- Omezení pro tabulku `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `category_product_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Omezení pro tabulku `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `order_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `user` (`id`);

--
-- Omezení pro tabulku `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `user` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
