-- Create hpttix database version 0.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Table structure for table `activities`

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL auto_increment,
  `incident` int(11) NOT NULL,
  `oper` int(11) NOT NULL,
  `start` bigint(20) NOT NULL COMMENT 'activity start time',
  `duration` int(11) NOT NULL COMMENT 'activity duration in minutes',
  `desc` text NOT NULL,
  `reportable` int(11) NOT NULL COMMENT '1=yes, 0=no',
  `billable` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`),
  KEY `incident` (`incident`,`oper`)
) ENGINE=MyISAM AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


-- Table structure for table `addresses`

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL auto_increment,
  `cust` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `addr1` varchar(100) NOT NULL,
  `addr2` varchar(100) default NULL,
  `addr3` varchar(100) default NULL,
  `addr4` varchar(100) default NULL,
  `city` varchar(100) NOT NULL,
  `state` char(2) NOT NULL,
  `zip` char(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cust` (`cust`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `billingItems`

DROP TABLE IF EXISTS `billingItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billingItems` (
  `id` int(11) NOT NULL auto_increment,
  `cust` int(11) NOT NULL,
  `recurId` int(11) default NULL COMMENT 'parent id of recurring item',
  `date` bigint(20) NOT NULL,
  `desc` text NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cust` (`cust`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=223 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `callers`

DROP TABLE IF EXISTS `callers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callers` (
  `id` int(11) NOT NULL auto_increment,
  `cust` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(25) default NULL,
  `email` varchar(100) default NULL,
  `serviceAddress` int(11) default NULL,
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  `password` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `categories`

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL auto_increment,
  `cust` int(11) NOT NULL default '0' COMMENT '0 = all customers',
  `name` varchar(100) NOT NULL,
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`),
  KEY `cust` (`cust`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for inital `categories`

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,0,'Hardware Support',1),(2,0,'Software Support',1),(3,0,'Consulting',1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `customers`

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL auto_increment,
  `acctgName` varchar(50) default NULL,
  `name` varchar(100) NOT NULL,
  `billingPhone` varchar(25) default NULL,
  `billingAddress` int(11) default NULL,
  `serviceAddress` int(11) default NULL,
  `plan` int(11) default NULL,
  `annualFeeMonth` int(11) NOT NULL COMMENT 'plan renewal month',
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `defaults`

DROP TABLE IF EXISTS `defaults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defaults` (
  `id` int(11) NOT NULL auto_increment,
  `defSeverity` int(11) NOT NULL,
  `defPriority` int(11) NOT NULL,
  `defCategory` int(11) NOT NULL,
  `defStatus` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for table `defaults`

LOCK TABLES `defaults` WRITE;
/*!40000 ALTER TABLE `defaults` DISABLE KEYS */;
INSERT INTO `defaults` VALUES (1,1,1,5,1);
/*!40000 ALTER TABLE `defaults` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `incidents`

DROP TABLE IF EXISTS `incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incidents` (
  `id` int(11) NOT NULL auto_increment,
  `caller` int(11) NOT NULL,
  `start` bigint(20) NOT NULL COMMENT 'time ticket opened',
  `stop` bigint(20) default NULL COMMENT 'time ticket closed',
  `shortDesc` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  `openInd` int(11) NOT NULL COMMENT '1 = open, 0 = closed',
  `status` int(11) NOT NULL,
  `severity` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `openedBy` int(11) NOT NULL,
  `assignedTo` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `billable` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`),
  KEY `caller` (`caller`,`status`,`assignedTo`,`category`)
) ENGINE=MyISAM AUTO_INCREMENT=10025 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `operators`

DROP TABLE IF EXISTS `operators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operators` (
  `id` int(11) NOT NULL auto_increment,
  `userid` varchar(25) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) default NULL,
  `password` varchar(100) default NULL,
  `adminUser` int(11) NOT NULL COMMENT '0=no, 1=yes',
  `canLogin` int(11) NOT NULL COMMENT '0=no, 1=yes',
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for table `operators`

LOCK TABLES `operators` WRITE;
/*!40000 ALTER TABLE `operators` DISABLE KEYS */;
INSERT INTO `operators` VALUES (2,'operator','Default Operator','operator@localhost','',1,1,1);
/*!40000 ALTER TABLE `operators` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `plans`

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `incr` int(11) NOT NULL COMMENT 'billing increment in minutes',
  `annualFee` double NOT NULL COMMENT 'annual fee',
  `baseFee` double NOT NULL COMMENT 'base monthly charge',
  `baseHours` int(11) NOT NULL COMMENT 'base hours included',
  `overFee` double NOT NULL COMMENT 'hourly fee for overage',
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `priorities`

DROP TABLE IF EXISTS `priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `priorities` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL,
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for table `priorities`

LOCK TABLES `priorities` WRITE;
/*!40000 ALTER TABLE `priorities` DISABLE KEYS */;
INSERT INTO `priorities` VALUES (1,'3 - low',1),(2,'2 - standard',1),(3,'1 - critical',1);
/*!40000 ALTER TABLE `priorities` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `recurringItems`

DROP TABLE IF EXISTS `recurringItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurringItems` (
  `id` int(11) NOT NULL auto_increment,
  `cust` int(11) NOT NULL,
  `desc` text NOT NULL,
  `amount` double NOT NULL,
  `startDate` bigint(20) NOT NULL,
  `stopDate` bigint(20) NOT NULL,
  `recur` char(1) NOT NULL COMMENT 'A-annual, M-monthly, Q-quarterly',
  PRIMARY KEY  (`id`),
  KEY `cust` (`cust`),
  KEY `startDate` (`startDate`),
  KEY `stopDate` (`stopDate`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `severities`

DROP TABLE IF EXISTS `severities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `severities` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL,
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for table `severities`

LOCK TABLES `severities` WRITE;
/*!40000 ALTER TABLE `severities` DISABLE KEYS */;
INSERT INTO `severities` VALUES (1,'4 - single user',1),(2,'3 - multiple users',1),(3,'2 - multiple sites',1),(4,'1 - total outage',1),(5,'5 - Question / Info Req',1);
/*!40000 ALTER TABLE `severities` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `states`

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `abbr` char(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`abbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Populate data for table `states`

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES ('AK','Alaska'),('AL','Alabama'),('AR','Arkansas'),('AZ','Arizona'),('CA','California'),('CO','Colorado'),('CT','Connecticut'),('DC','District of Columbia'),('DE','Delaware'),('FL','Florida'),('GA','Georgia'),('HI','Hawaii'),('IA','Iowa'),('ID','Idaho'),('IL','Illinois'),('IN','Indiana'),('KS','Kansas'),('KY','Kentucky'),('LA','Louisianna'),('MA','Massachusetts'),('MD','Maryland'),('ME','Maine'),('MI','Michigan'),('MN','Minnisota'),('MO','Missouri'),('MS','Mississippi'),('MT','Montana'),('NC','North Carolina'),('ND','North Dakota'),('NE','Nebraska'),('NH','New Hampshire'),('NJ','New Jersey'),('NM','New Mexico'),('NV','Nevada'),('NY','New York'),('OH','Ohio'),('OK','Oklahoma'),('OR','Oregon'),('PA','Pennsylvania'),('RI','Rhode Island'),('SC','South Carolina'),('SD','South Dakota'),('TN','Tennessee'),('TX','Texas'),('UT','Utah'),('VA','Virginia'),('VT','Vermont'),('WA','Washington'),('WI','Wisconsin'),('WV','West Virginia'),('WY','Wyoming');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `statuses`

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `active` int(11) NOT NULL COMMENT '1=yes, 0=no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Poplulate data for table `statuses`

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'Active',1),(2,'Resolved',1),(3,'Cancelled',1),(4,'Waiting on Vendor',1),(5,'Waiting on Customer',1),(7,'Researching',1),(9,'Complete',1);
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
