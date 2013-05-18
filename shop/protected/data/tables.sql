CREATE TABLE `listing` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `PriceNew` float DEFAULT NULL,
  `PriceUsed` float DEFAULT NULL,
  `Delta` float DEFAULT NULL,
  `Attr` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB;


CREATE TABLE `listing2` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ASIN` varchar(15) DEFAULT NULL,
  `PriceNew` float DEFAULT NULL,
  `PriceUsed` float DEFAULT NULL,
  `Delta` float DEFAULT NULL,
  `Attr` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB;

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

CREATE  TABLE `part` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Type` ENUM('cpu','hdd','vga','ram','screen') NULL ,
  `Model` VARCHAR(255) NULL ,
  `Score` INT NULL ,
  `Description` LONGTEXT NULL ,
  `Image` VARCHAR(255) NULL ,
  PRIMARY KEY (`Id`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `amazon`.`listing` ADD COLUMN `CPU` INT NULL DEFAULT 0  AFTER `SalesRank` , ADD COLUMN `VGA` INT NULL DEFAULT 0  AFTER `CPU` , ADD COLUMN `RAM` INT NULL DEFAULT 0  AFTER `VGA` , ADD COLUMN `HDD` INT NULL DEFAULT 0  AFTER `RAM` , ADD COLUMN `Screen` INT NULL DEFAULT 0  AFTER `HDD` ;
ALTER TABLE `amazon`.`part` ADD INDEX `typemodel` (`Type` ASC, `Model` ASC) ;
ALTER TABLE `amazon`.`listing` ADD COLUMN `Title` VARCHAR(300) NULL  AFTER `SalesRank` ;