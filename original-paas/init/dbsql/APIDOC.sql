-- MySQL dump 10.16  Distrib 10.2.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: APIDOC
-- ------------------------------------------------------
-- Server version	10.2.25-MariaDB-10.2.25+maria~xenial-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- create database paas_dashboard;
-- create database APIDOC;

--                                                                                                                                                                                                                                          
-- Current Database: `APIDOC`
--

-- CREATE DATABASE /*!32312 IF NOT EXISTS*/ `APIDOC` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `APIDOC`;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `devID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '設備編號',
  `devName` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '設備名稱',
  `category` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '類別',
  `noumenonType` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '隸屬類別',
  `noumenonID` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '隸屬編號',
  `devInfo` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `placeID` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '場域編號',
  `devTypeID` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '場域種類編號',
  `creatorID` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '創建者ID',
  `createTime` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `lastUpdateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最後修改時間',
  PRIMARY KEY (`devID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='設備';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place`
--

DROP TABLE IF EXISTS `place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `place` (
  `placeID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '場域編號',
  `placeName` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '場域名稱',
  `creatorID` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '創建者ID',
  `createTime` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間',
  `lastUpdateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最後修改時間',
  PRIMARY KEY (`placeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='場域';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place`
--

LOCK TABLES `place` WRITE;
/*!40000 ALTER TABLE `place` DISABLE KEYS */;
/*!40000 ALTER TABLE `place` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-12-02 17:37:34
