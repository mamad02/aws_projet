-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2015 at 08:39 PM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `c9`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `title_event` varchar(128) NOT NULL,
  `desc_event` varchar(256) NOT NULL,
  `start_event` datetime NOT NULL,
  `end_event` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_event`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_event`, `title_event`, `desc_event`, `start_event`, `end_event`, `id_user`) VALUES
(28, 'test d''event', 'yooo ', '2015-05-04 01:00:00', '2015-05-04 10:30:00', 1),
(29, 'event de ouf', ' ', '2015-05-06 14:00:00', '2015-05-06 20:00:00', 1),
(30, 'pas mal', ' pas mal', '2015-05-08 11:00:00', '2015-05-08 12:00:00', 29),
(31, 'muscu', 'muscu ', '2015-05-09 03:00:00', '2015-05-09 11:00:00', 29),
(32, 'fdp', 'fdp ', '2015-05-06 08:00:00', '2015-05-06 12:00:00', 30);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `login_user` varchar(128) NOT NULL,
  `password_user` varchar(128) NOT NULL,
  `right_user` int(11) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `login_user`, `password_user`, `right_user`) VALUES
(1, 'louis', 'test', 0),
(26, 'mamarfhuje', 'nzerojnlet', 0),
(27, 'mamad', 'tenjte', 0),
(28, 'blabbla', 'beiohu', 0),
(29, 'dem', 'salutlouis', 0),
(30, 'hype', 'hype', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
