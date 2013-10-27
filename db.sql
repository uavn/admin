-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dictator
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.13.10.1

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

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author`
--

LOCK TABLES `author` WRITE;
/*!40000 ALTER TABLE `author` DISABLE KEYS */;
INSERT INTO `author` VALUES (1,'Автор 1'),(2,'Автор 2'),(3,'Автор 3');
/*!40000 ALTER TABLE `author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `author_book`
--

DROP TABLE IF EXISTS `author_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authorId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `authorId` (`authorId`),
  KEY `bookId` (`bookId`),
  CONSTRAINT `author_book_ibfk_1` FOREIGN KEY (`authorId`) REFERENCES `author` (`id`) ON DELETE CASCADE,
  CONSTRAINT `author_book_ibfk_2` FOREIGN KEY (`bookId`) REFERENCES `book` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author_book`
--

LOCK TABLES `author_book` WRITE;
/*!40000 ALTER TABLE `author_book` DISABLE KEYS */;
INSERT INTO `author_book` VALUES (4,1,2),(52,2,3),(61,1,20),(62,2,20),(77,1,1),(78,2,1),(79,3,1),(90,1,22),(91,3,22),(92,2,5),(93,3,4);
/*!40000 ALTER TABLE `author_book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `desc` longtext,
  `date` datetime DEFAULT NULL,
  `isSold` tinyint(1) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `book_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (1,'Книга 1',1,NULL,NULL,NULL,NULL,NULL),(2,'Книга 2',1,NULL,NULL,NULL,NULL,NULL),(3,'Книга 3',1,NULL,NULL,NULL,NULL,NULL),(4,'Книга 4',1,NULL,NULL,'2013-10-27 19:33:28',NULL,NULL),(5,'Книга 5',1,NULL,'Описание','2013-10-27 19:33:21',NULL,NULL),(20,'sadasd',2,NULL,'Национальная футбольная сборная Украины сегодня сыграет с командой Польши отборочный матч к Чемпионату мира-2014. Игра состоится в Харькове на стадионе \"Металлист\" и начнется в 21:00, прямая трансляция - на телеканале Интер.\r\n\r\nДо завершения квалификации остается всего два матча, но ситуация в нашей турнирной группе остается очень запутанной. На первое место и прямую путевку в Бразилию претендуют четыре команды и как раз сегодня претенденты играют друг с другом.','2013-12-10 10:10:10',NULL,NULL),(22,'Тест',1,'525884d671b23.jpg','Описание тест','2013-10-27 19:29:09',1,NULL);
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Категория 1'),(2,'Категория 2'),(3,'Категория 3');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publisher`
--

DROP TABLE IF EXISTS `publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publisher`
--

LOCK TABLES `publisher` WRITE;
/*!40000 ALTER TABLE `publisher` DISABLE KEYS */;
INSERT INTO `publisher` VALUES (1,'Издательство 1'),(2,'Издательство 2'),(3,'Издательство 3'),(4,'Издательство 4'),(5,'Издательство 5'),(6,'Издательство 6'),(7,'Издательство 7'),(8,'Издательство 8'),(9,'Издательство 9'),(10,'Издательство 10'),(11,'Издательство 11'),(12,'Издательство 12'),(13,'Издательство 13'),(14,'Издательство 14'),(15,'Издательство 15');
/*!40000 ALTER TABLE `publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publisher_book`
--

DROP TABLE IF EXISTS `publisher_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publisher_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookId` int(11) NOT NULL,
  `publisherId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookId` (`bookId`),
  KEY `publisherId` (`publisherId`),
  CONSTRAINT `publisher_book_ibfk_1` FOREIGN KEY (`bookId`) REFERENCES `book` (`id`) ON DELETE CASCADE,
  CONSTRAINT `publisher_book_ibfk_2` FOREIGN KEY (`publisherId`) REFERENCES `publisher` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publisher_book`
--

LOCK TABLES `publisher_book` WRITE;
/*!40000 ALTER TABLE `publisher_book` DISABLE KEYS */;
INSERT INTO `publisher_book` VALUES (50,3,1),(51,3,2),(85,20,1),(86,20,2),(87,20,3),(113,1,1),(114,1,3),(135,22,5),(136,22,6),(137,22,12),(138,22,13),(139,5,1),(140,5,2),(141,5,3),(142,5,7),(143,5,8),(144,5,9),(145,5,10),(146,5,11),(147,5,12),(148,5,13),(149,5,14);
/*!40000 ALTER TABLE `publisher_book` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-27 19:33:44
