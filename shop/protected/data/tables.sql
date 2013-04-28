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