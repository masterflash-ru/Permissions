-- MySQL dump 10.13  Distrib 5.6.39, for FreeBSD11.1 (i386)
--
-- Host: localhost    Database: simba4
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



DROP TABLE IF EXISTS `role_tree`;
DROP TABLE IF EXISTS `role2permission`;
DROP TABLE IF EXISTS `users2role`;
DROP TABLE IF EXISTS `role`;
DROP TABLE IF EXISTS `permission`;

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
-- Table structure for table `users2group`
--

DROP TABLE IF EXISTS `users2group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users2group` (
  `users` int(11) NOT NULL,
  `users_group` int(11) NOT NULL,
  PRIMARY KEY (`users`,`users_group`),
  KEY `users` (`users`),
  KEY `users_group` (`users_group`),
  CONSTRAINT `users2group_fk` FOREIGN KEY (`users`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users2group_fk1` FOREIGN KEY (`users_group`) REFERENCES `users_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='привязка юзер-группа';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users2group`
--

LOCK TABLES `users2group` WRITE;
/*!40000 ALTER TABLE `users2group` DISABLE KEYS */;
/*!40000 ALTER TABLE `users2group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_ext`
--

DROP TABLE IF EXISTS `users_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_ext` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `users_ext_fk` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='расширение юзеров';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_ext`
--

LOCK TABLES `users_ext` WRITE;
/*!40000 ALTER TABLE `users_ext` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_ext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_group`
--

DROP TABLE IF EXISTS `users_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='роли';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_group`
--

LOCK TABLES `users_group` WRITE;
/*!40000 ALTER TABLE `users_group` DISABLE KEYS */;
INSERT INTO `users_group` VALUES (1,'Группа администраторы','Администратор - полный доступ ко всем ресурсам'),(2,'Группа редакторы','Редакторы информации'),(6,'Группа регистрированные посетители','Регистрированные посетители'),(9,'Группа гости','Гостевой вход - группа с минимальным уровнем доступа');
/*!40000 ALTER TABLE `users_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_group_tree`
--

DROP TABLE IF EXISTS `users_group_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_group_tree` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`parent_id`),
  KEY `role` (`id`),
  KEY `parent_role` (`parent_id`),
  CONSTRAINT `users_group_tree_fk` FOREIGN KEY (`id`) REFERENCES `users_group` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_group_tree_fk1` FOREIGN KEY (`parent_id`) REFERENCES `users_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_group_tree`
--

LOCK TABLES `users_group_tree` WRITE;
/*!40000 ALTER TABLE `users_group_tree` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_group_tree` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-28  9:38:56
