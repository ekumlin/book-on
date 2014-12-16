-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2014 at 10:39 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `author`
--

CREATE TABLE IF NOT EXISTS `author` (
  `AuthorId` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Birthdate` date DEFAULT NULL,
  `HomeCountry` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`AuthorId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`AuthorId`, `FirstName`, `LastName`, `Birthdate`, `HomeCountry`) VALUES
(1, 'Carlos', 'Coronel', NULL, NULL),
(2, 'Steven', 'Morris', NULL, NULL),
(3, 'Peter', 'Rob', NULL, NULL),
(4, 'Andrew', 'Tanenbaum', NULL, NULL),
(5, 'Herbert', 'Bos', NULL, NULL),
(6, 'Martin', 'Fowler', NULL, NULL),
(7, 'Peter', 'Linz', NULL, NULL),
(8, 'Ira', 'Forman', NULL, NULL),
(9, 'Nate', 'Forman', NULL, NULL),
(10, 'David', 'Wetherall', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `ISBN` bigint(13) NOT NULL,
  `Title` varchar(511) NOT NULL,
  `SalePrice` float NOT NULL,
  `PageCount` int(6) NOT NULL,
  `Edition` smallint(3) NOT NULL,
  `Language` varchar(2) NOT NULL,
  `Publisher` int(11) DEFAULT NULL,
  PRIMARY KEY (`ISBN`),
  KEY `Publisher` (`Publisher`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`ISBN`, `Title`, `SalePrice`, `PageCount`, `Edition`, `Language`, `Publisher`) VALUES
(9780131485211, 'Structured Computer Organization', 70.2, 800, 5, 'en', 2),
(9780132126953, 'Computer Networks', 159.8, 960, 5, 'en', 2),
(9780133591620, 'Modern Operating Systems', 71.99, 1136, 4, 'en', 2),
(9780201433050, 'Putting Metaclasses to Work', 139.11, 320, 1, 'en', 3),
(9780201485677, 'Refactoring: Improving the Design of Existing Code', 42.83, 464, 1, 'en', 3),
(9781285196145, 'Database Systems: Design, Implementation, & Management', 49.99, 784, 11, 'en', 1),
(9781423902010, 'Database Systems: Design, Implementation, and Management', 28.99, 728, 8, 'en', 1),
(9781449615529, 'An Introduction To Formal Languages And Automata', 175.98, 437, 5, 'en', 4),
(9781890774691, 'Murach''s SQL Server 2012 for Developers: Training & Reference', 60.84, 796, 1, 'en', NULL),
(9781932394184, 'Java Reflection in Action', 42.81, 273, 2, 'en', 5);

-- --------------------------------------------------------

--
-- Table structure for table `bookauthor`
--

CREATE TABLE IF NOT EXISTS `bookauthor` (
  `ISBN` bigint(13) NOT NULL,
  `AuthorId` int(11) NOT NULL,
  KEY `ISBN` (`ISBN`),
  KEY `AuthorId` (`AuthorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookauthor`
--

INSERT INTO `bookauthor` (`ISBN`, `AuthorId`) VALUES
(9781423902010, 1),
(9781423902010, 3),
(9781285196145, 1),
(9781285196145, 2),
(9780133591620, 4),
(9780133591620, 5),
(9780131485211, 4),
(9780201485677, 6),
(9781449615529, 7),
(9781932394184, 8),
(9781932394184, 9),
(9780201433050, 8),
(9780132126953, 10);

-- --------------------------------------------------------

--
-- Table structure for table `bookcollected`
--

CREATE TABLE IF NOT EXISTS `bookcollected` (
  `CollectionId` int(11) NOT NULL,
  `ISBN` bigint(13) NOT NULL,
  KEY `CollectionId` (`CollectionId`),
  KEY `ISBN` (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookcollected`
--

INSERT INTO `bookcollected` (`CollectionId`, `ISBN`) VALUES
(2, 9781285196145),
(2, 9781423902010),
(3, 9781449615529);

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy`
--

CREATE TABLE IF NOT EXISTS `bookcopy` (
  `BookCopyId` bigint(20) NOT NULL AUTO_INCREMENT,
  `IsForSale` tinyint(1) NOT NULL,
  `HeldBy` int(11) DEFAULT NULL,
  `ISBN` bigint(13) NOT NULL,
  PRIMARY KEY (`BookCopyId`),
  KEY `ISBN` (`ISBN`),
  KEY `HeldBy` (`HeldBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `bookcopy`
--

INSERT INTO `bookcopy` (`BookCopyId`, `IsForSale`, `HeldBy`, `ISBN`) VALUES
(1, 0, NULL, 9781285196145),
(2, 0, 412100001, 9781285196145),
(3, 1, NULL, 9781285196145),
(4, 1, NULL, 9780133591620),
(5, 1, NULL, 9780133591620),
(6, 0, 412100002, 9780131485211),
(7, 1, NULL, 9780131485211),
(8, 1, NULL, 9780131485211),
(9, 0, NULL, 9780201485677),
(10, 0, NULL, 9780201485677),
(11, 1, NULL, 9780201485677),
(12, 1, NULL, 9781449615529),
(13, 0, 412100003, 9781449615529),
(14, 0, NULL, 9781449615529),
(15, 0, NULL, 9781932394184),
(16, 1, NULL, 9781932394184),
(17, 1, NULL, 9781932394184),
(18, 1, NULL, 9780201433050),
(19, 1, NULL, 9780201433050),
(20, 0, NULL, 9780201433050),
(21, 0, 412100002, 9780201433050),
(22, 0, 412100001, 9780132126953),
(23, 0, NULL, 9780132126953);

-- --------------------------------------------------------

--
-- Table structure for table `bookrated`
--

CREATE TABLE IF NOT EXISTS `bookrated` (
  `RatingId` int(11) NOT NULL AUTO_INCREMENT,
  `CardNumber` int(11) NOT NULL,
  `ISBN` bigint(13) NOT NULL,
  `Rating` tinyint(1) NOT NULL,
  `Review` text NOT NULL,
  PRIMARY KEY (`RatingId`),
  KEY `CardNumber` (`CardNumber`),
  KEY `ISBN` (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `booktransaction`
--

CREATE TABLE IF NOT EXISTS `booktransaction` (
  `BookTransactionId` bigint(20) NOT NULL AUTO_INCREMENT,
  `BookCopyId` bigint(20) NOT NULL,
  `Time` datetime NOT NULL,
  `ExpectedReturn` datetime DEFAULT NULL,
  `ActualReturn` datetime DEFAULT NULL,
  `CardNumber` int(11) NOT NULL,
  PRIMARY KEY (`BookTransactionId`),
  KEY `BookCopyId` (`BookCopyId`),
  KEY `CardNumber` (`CardNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `booktransaction`
--

INSERT INTO `booktransaction` (`BookTransactionId`, `BookCopyId`, `Time`, `ExpectedReturn`, `ActualReturn`, `CardNumber`) VALUES
(1, 13, '2014-12-15 15:36:58', '2014-12-12 04:00:00', NULL, 412100003),
(2, 21, '2014-12-15 05:30:00', '2014-12-07 05:30:00', NULL, 412100002),
(3, 6, '2014-12-16 14:35:14', '2014-12-18 07:20:00', NULL, 412100002),
(4, 22, '2014-12-16 09:20:00', '2014-12-18 16:20:00', NULL, 412100001);

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE IF NOT EXISTS `collection` (
  `CollectionId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `CardNumber` int(11) NOT NULL,
  PRIMARY KEY (`CollectionId`),
  KEY `CardNumber` (`CardNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`CollectionId`, `Name`, `CardNumber`) VALUES
(1, 'Favorites', 412100001),
(2, 'Textbooks', 412100001),
(3, 'Expensive Books', 412100003);

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE IF NOT EXISTS `publisher` (
  `PublisherId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(511) NOT NULL,
  `Phone` bigint(11) NOT NULL,
  PRIMARY KEY (`PublisherId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`PublisherId`, `Name`, `Address`, `Phone`) VALUES
(1, 'Cengage Learning', '10650 Toebben Drive, Independence, KY 41051', 18003549706),
(2, 'Prentice Hall', 'Upper Saddle River, NJ 07458', 18008489500),
(3, 'Addison Wesley', '26 Prince Andrew Place Don Mills, ON M3C 2T8', 18002639965),
(4, 'Jones & Bartlett Learning', '5 Wall Street Burlington, MA 01803', 18008320034),
(5, 'Manning Publications', '20 Baldwin Road,Shelter Island, NY 11963', 2036261510);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`CardNumber`, `IsEmployee`, `Name`, `Password`, `Email`, `AccountStatus`) VALUES
(412100001, 1, 'Eric Kumlin', '$2y$10$9pPv.7ymnYg9eFc1LBK1tOZKBgB1UoOEzxDpF7MYZBYw99Uk/3Q2q', 'eric.kumlin@gmail.com', 0),
(412100002, 2, 'Sarah Durant', '$2y$10$Ye0QxeXNC2OphK7cklHOzudZDCgYbNKVEPC0WlJ13OmKv4Ta9osQi', 'sarah.ash.durant@gmail.com', 0),
(412100003, 2, 'Desmond Chan', '$2y$10$kf0cU6g5Sr14PkNmp7cm2.DFF5lQu2la1BgQKlmHkJfaXOEpu6QhW', 'desmond.chan@live.ca', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `Book_ibfk_1` FOREIGN KEY (`Publisher`) REFERENCES `publisher` (`PublisherId`);

--
-- Constraints for table `bookauthor`
--
ALTER TABLE `bookauthor`
  ADD CONSTRAINT `BookAuthor_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`),
  ADD CONSTRAINT `BookAuthor_ibfk_2` FOREIGN KEY (`AuthorId`) REFERENCES `author` (`AuthorId`);

--
-- Constraints for table `bookcollected`
--
ALTER TABLE `bookcollected`
  ADD CONSTRAINT `BookFave_ibfk_1` FOREIGN KEY (`CollectionId`) REFERENCES `collection` (`CollectionId`),
  ADD CONSTRAINT `BookFave_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`);

--
-- Constraints for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD CONSTRAINT `BookCopy_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`),
  ADD CONSTRAINT `BookCopy_ibfk_2` FOREIGN KEY (`HeldBy`) REFERENCES `user` (`CardNumber`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `booktransaction`
--
ALTER TABLE `booktransaction`
  ADD CONSTRAINT `BookTransaction_ibfk_1` FOREIGN KEY (`BookCopyId`) REFERENCES `bookcopy` (`BookCopyId`),
  ADD CONSTRAINT `BookTransaction_ibfk_2` FOREIGN KEY (`CardNumber`) REFERENCES `user` (`CardNumber`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
