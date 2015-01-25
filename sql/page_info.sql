-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Pon 22. pro 2014, 13:29
-- Verze serveru: 5.5.40-MariaDB-0ubuntu0.14.10.1
-- Verze PHP: 5.5.12-2ubuntu4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Struktura tabulky `page_info`
--

CREATE TABLE IF NOT EXISTS `page_info` (
`id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `page` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_attribute` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `img` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `page_info`
--

INSERT INTO `page_info` (`id`, `parent_id`, `page`, `sub_attribute`, `title`, `description`, `keywords`, `img`) VALUES
(1, NULL, 'Homepage', NULL, 'Title', 'keywords', 'description', 'logo.png');

--
-- Klíče pro tabulku `page_info`
--
ALTER TABLE `page_info`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `parent_id_2` (`parent_id`,`page`), ADD KEY `parent_id` (`parent_id`);

--
-- AUTO_INCREMENT pro tabulku `page_info`
--
ALTER TABLE `page_info`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- Omezení pro tabulku `page_info`
--
ALTER TABLE `page_info`
ADD CONSTRAINT `page_info_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `page_info` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
