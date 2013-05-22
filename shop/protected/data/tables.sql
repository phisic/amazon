CREATE TABLE `price` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `PriceNew` float DEFAULT NULL,
  `PriceUsed` float DEFAULT NULL,
  `Delta` float DEFAULT NULL,
  `Date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB;

CREATE  TABLE `amazon`.`price_log` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Price` INT NULL ,
  `DateStart` TIMESTAMP NULL ,
  `ItemsRead` INT NULL ,
  `DateEnd` TIMESTAMP NULL ,
  PRIMARY KEY (`Id`) );

CREATE TABLE `watch` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `UserId` int(11) DEFAULT '0',
  `Email` varchar(64) DEFAULT NULL,
  `NewUsed` enum('new','used') DEFAULT 'new',
  `DateCreate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Active` tinyint(4) DEFAULT '1',
  `FirstName` varchar(45) DEFAULT NULL,
  `Price` int(11) DEFAULT NULL,
  `PriceDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `listing` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `Data` longtext,
  `Date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LogId` int(11) DEFAULT NULL,
  `SalesRank` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `listingdata` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(45) DEFAULT NULL,
  `Data` longtext,
  PRIMARY KEY (`Id`),
  KEY `ASIN` (`ASIN`),
) ENGINE=InnoDb DEFAULT CHARSET=latin1;

CREATE TABLE `partmatch` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `Relevance` int(11) DEFAULT NULL,
  `Type` enum('cpu','vga','hdd') DEFAULT NULL,
  `PartId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ASINType` (`ASIN`,`Type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `part` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('cpu','hdd','vga','ram','screen') DEFAULT NULL,
  `Model` varchar(255) DEFAULT NULL,
  `Score` int(11) DEFAULT NULL,
  `Description` longtext,
  `Image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `typemodel` (`Type`,`Model`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;