-- MySQL dump 10.13  Distrib 5.6.39, for FreeBSD11.1 (i386)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version	5.6.39

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
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='доступы';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES (1,'admin.login','Вход в админку','2017-09-22 13:15:49'),(3,'admin.manage','Управление администраторами','2017-09-23 07:02:03'),(4,'permission.manage','Управление привелегиями','2017-09-23 07:02:03'),(5,'role.manage','Управление ролями','2017-09-23 07:02:03'),(6,'profile.any.view','Управление любыми профилями','2017-09-23 07:02:03'),(7,'profile.own.view','Управление своим профилем','2017-09-23 07:02:03');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='роли';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'admin','Администратор','2017-09-22 13:14:39'),(2,'guest','Гостевой вход','2017-09-22 13:16:53');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role2permission`
--

DROP TABLE IF EXISTS `role2permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role2permission` (
  `role` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  `code` int(11) DEFAULT NULL COMMENT 'битовая структура доступа',
  PRIMARY KEY (`role`,`permission`),
  KEY `permission` (`permission`),
  KEY `role` (`role`),
  CONSTRAINT `role2permission_fk` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role2permission_fk1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='роль-доступ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role2permission`
--

LOCK TABLES `role2permission` WRITE;
/*!40000 ALTER TABLE `role2permission` DISABLE KEYS */;
INSERT INTO `role2permission` VALUES (1,1,NULL);
/*!40000 ALTER TABLE `role2permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_tree`
--

DROP TABLE IF EXISTS `role_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_tree` (
  `role` int(11) NOT NULL,
  `parent_role` int(11) NOT NULL,
  PRIMARY KEY (`role`,`parent_role`),
  KEY `role` (`role`),
  KEY `parent_role` (`parent_role`),
  CONSTRAINT `role_tree_fk` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_tree_fk1` FOREIGN KEY (`parent_role`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_tree`
--

LOCK TABLES `role_tree` WRITE;
/*!40000 ALTER TABLE `role_tree` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_tree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` char(127) NOT NULL COMMENT 'логин, можно мыло',
  `status` int(11) NOT NULL COMMENT 'состояние юзера 1-нормальный',
  `password` char(127) NOT NULL COMMENT 'текущий пароль',
  `name` char(127) DEFAULT NULL COMMENT 'псевдоним',
  `full_name` char(255) DEFAULT NULL COMMENT 'ФИО',
  `temp_password` char(127) DEFAULT NULL COMMENT 'временный пароль для восстановления',
  `temp_date` datetime DEFAULT NULL COMMENT 'дата годности временного пароля для активации',
  `confirm_hash` char(50) DEFAULT NULL COMMENT 'строка для подтверждения регистрации',
  `date_registration` datetime DEFAULT NULL COMMENT 'дата регистрации',
  `date_last_login` datetime DEFAULT NULL COMMENT 'дата входа',
  PRIMARY KEY (`id`),
  KEY `temp_date` (`temp_date`),
  KEY `confirm_hash` (`confirm_hash`),
  KEY `status` (`status`),
  KEY `date_registration` (`date_registration`),
  KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='регистрированные юзеры (база)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'root',1,'$2y$10$TryLBUTSX7lZdSD8NBUFMOu8.vzvfoqaFlHgsv2C460EgxGkkYff6',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users2permission`
--

DROP TABLE IF EXISTS `users2permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users2permission` (
  `users` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  `code` int(11) DEFAULT NULL COMMENT 'Битовая структура доступа',
  PRIMARY KEY (`users`,`permission`),
  KEY `users` (`users`),
  KEY `permission` (`permission`),
  CONSTRAINT `users2permission_fk` FOREIGN KEY (`users`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users2permission_fk1` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users2permission`
--

LOCK TABLES `users2permission` WRITE;
/*!40000 ALTER TABLE `users2permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `users2permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users2role`
--

DROP TABLE IF EXISTS `users2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users2role` (
  `users` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`users`,`role`),
  KEY `users` (`users`),
  KEY `role` (`role`),
  CONSTRAINT `users2role_fk` FOREIGN KEY (`users`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users2role_fk1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='юзер-роль';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users2role`
--

LOCK TABLES `users2role` WRITE;
/*!40000 ALTER TABLE `users2role` DISABLE KEYS */;
INSERT INTO `users2role` VALUES (1,1);
/*!40000 ALTER TABLE `users2role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-13  8:35:26
