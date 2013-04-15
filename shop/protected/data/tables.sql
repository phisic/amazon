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
ALTER TABLE `price` RENAME TO  `price_tmp` ;
ALTER TABLE `price2` RENAME TO  `price` ;
ALTER TABLE `price_tmp` RENAME TO  `price2` ;
