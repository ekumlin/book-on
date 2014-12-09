-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 09, 2014 at 03:04 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bookon`
--

-- --------------------------------------------------------

--
-- Table structure for table `Author`
--

CREATE TABLE IF NOT EXISTS `Author` (
  `AuthorId` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Birthdate` date DEFAULT NULL,
  `HomeCountry` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`AuthorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Book`
--

CREATE TABLE IF NOT EXISTS `Book` (
  `ISBN` bigint(13) NOT NULL,
  `Title` varchar(511) NOT NULL,
  `SalePrice` float NOT NULL,
  `PageCount` int(6) NOT NULL,
  `Edition` smallint(3) NOT NULL,
  `Language` varchar(2) NOT NULL,
  `Publisher` int(11) NOT NULL,
  PRIMARY KEY (`ISBN`),
  KEY `Publisher` (`Publisher`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BookAuthor`
--

CREATE TABLE IF NOT EXISTS `BookAuthor` (
  `ISBN` bigint(13) NOT NULL,
  `AuthorId` int(11) NOT NULL,
  KEY `ISBN` (`ISBN`),
  KEY `AuthorId` (`AuthorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BookCopy`
--

CREATE TABLE IF NOT EXISTS `BookCopy` (
  `BookCopyId` bigint(20) NOT NULL AUTO_INCREMENT,
  `IsForSale` tinyint(1) NOT NULL,
  `ISBN` bigint(13) NOT NULL,
  PRIMARY KEY (`BookCopyId`),
  KEY `ISBN` (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `BookFave`
--

CREATE TABLE IF NOT EXISTS `BookFave` (
  `CollectionId` int(11) NOT NULL,
  `ISBN` bigint(13) NOT NULL,
  `Rating` tinyint(1) NOT NULL,
  KEY `CollectionId` (`CollectionId`),
  KEY `ISBN` (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BookTransaction`
--

CREATE TABLE IF NOT EXISTS `BookTransaction` (
  `BookTransactionId` bigint(20) NOT NULL AUTO_INCREMENT,
  `BookCopyId` bigint(20) NOT NULL,
  `Time` datetime NOT NULL,
  `ExpectedReturn` datetime DEFAULT NULL,
  `ActualReturn` datetime DEFAULT NULL,
  `CardNumber` int(11) NOT NULL,
  PRIMARY KEY (`BookTransactionId`),
  KEY `BookCopyId` (`BookCopyId`),
  KEY `CardNumber` (`CardNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Collection`
--

CREATE TABLE IF NOT EXISTS `Collection` (
  `CollectionId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `CardNumber` int(11) NOT NULL,
  PRIMARY KEY (`CollectionId`),
  KEY `CardNumber` (`CardNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Publisher`
--

CREATE TABLE IF NOT EXISTS `Publisher` (
  `PublisherId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(511) NOT NULL,
  `Phone` bigint(11) NOT NULL,
  PRIMARY KEY (`PublisherId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `CardNumber` int(11) NOT NULL,
  `IsEmployee` tinyint(1) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Password` varchar(72) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `AccountStatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`CardNumber`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Book`
--
ALTER TABLE `Book`
  ADD CONSTRAINT `Book_ibfk_1` FOREIGN KEY (`Publisher`) REFERENCES `Publisher` (`PublisherId`);

--
-- Constraints for table `BookAuthor`
--
ALTER TABLE `BookAuthor`
  ADD CONSTRAINT `BookAuthor_ibfk_2` FOREIGN KEY (`AuthorId`) REFERENCES `Author` (`AuthorId`),
  ADD CONSTRAINT `BookAuthor_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `Book` (`ISBN`);

--
-- Constraints for table `BookCopy`
--
ALTER TABLE `BookCopy`
  ADD CONSTRAINT `BookCopy_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `Book` (`ISBN`);

--
-- Constraints for table `BookFave`
--
ALTER TABLE `BookFave`
  ADD CONSTRAINT `BookFave_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `Book` (`ISBN`),
  ADD CONSTRAINT `BookFave_ibfk_1` FOREIGN KEY (`CollectionId`) REFERENCES `Collection` (`CollectionId`);

--
-- Constraints for table `BookTransaction`
--
ALTER TABLE `BookTransaction`
  ADD CONSTRAINT `BookTransaction_ibfk_2` FOREIGN KEY (`CardNumber`) REFERENCES `User` (`CardNumber`),
  ADD CONSTRAINT `BookTransaction_ibfk_1` FOREIGN KEY (`BookCopyId`) REFERENCES `BookCopy` (`BookCopyId`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
