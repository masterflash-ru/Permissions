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
INSERT INTO `users` VALUES (1,'root',3,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW','root','root',NULL,NULL,NULL,NULL,NULL);
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
INSERT INTO `users2group` VALUES (1,1);
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
INSERT INTO `users_ext` VALUES (1);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Группы';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_group`
--

LOCK TABLES `users_group` WRITE;
/*!40000 ALTER TABLE `users_group` DISABLE KEYS */;
INSERT INTO `users_group` VALUES (1,'Администраторы','Администратор - полный доступ ко всем ресурсам'),(2,'Редакторы','Редакторы информации'),(6,'Регистрированные посетители','Регистрированные посетители'),(9,'Гости','Гостевой вход - группа с минимальным уровнем доступа');
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





/* Data for the `design_tables` table  (Records 1 - 62) */

INSERT INTO `design_tables` (`interface_name`, `table_name`, `table_type`, `col_name`, `caption_style`, `row_type`, `col_por`, `pole_spisok_sql`, `pole_global_const`, `pole_prop`, `pole_type`, `pole_style`, `pole_name`, `default_sql`, `functions_befo`, `functions_after`, `functions_befo_out`, `functions_befo_del`, `properties`, `value`, `validator`, `sort_item_flag`, `col_function_array`) VALUES 
  ('users_group', 'users_group', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:484;}', 0, 0, 'select * from users_group where id<10 order by id', '', '0,0,0,0', '', '', 'id', '', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, ''),
  ('users_group', 'users_group', 0, 'name', '', 2, 3, '', '', 'size=60', '1', '', 'name', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'name', '', 3, 0, '', '', '', '1', '', 'name', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'id', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'id', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'description', '', 2, 3, '', '', '', '1', '', 'description', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'description', '', 3, 0, '', '', '', '1', '', 'description', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', 'a:3:{s:10:\"owner_user\";s:1:\"0\";s:11:\"owner_group\";s:1:\"0\";s:10:\"permission\";i:484;}', 0, 0, 'select users_group.*, (select group_concat(parent_id) from users_group_tree where users_group.id=users_group_tree.id) as parent_group from users_group where id>=10 order by name', '', '0,0,0,0', 'parent_group', '', 'id', 'delete from users_group where id=$id and id>=10', '', '', '', '', 'Mf\\Permissions\\Lib\\Func\\SaveGroupTree', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, ''),
  ('users_group_nonsystem', 'users_group', 0, 'name', '', 2, 3, '', '', 'size=55', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'name', '', 3, 0, '', '', 'size=55', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'description', '', 2, 4, '', '', 'size=55', '2', '', 'description', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'description', '', 3, 0, '', '', 'size=55', '2', '', 'description', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, '1', '', 2, 8, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:16:\"Добавить\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, '1', '', 3, 0, '', '', ',', '17', '', 'save,del', '', '', '', '', '', 'a:4:{i:0;s:1:\"1\";i:1;s:1:\"0\";i:2;s:33:\"Сохранить,Удалить\";i:3;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', '', 2, 5, 'select id,name from users_group order by id', '', '', '55', '', 'parent_group', '', '', '', '', '', 'a:3:{i:0;s:3:\"600\";i:1;s:0:\"\";i:2;s:1:\"2\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', '', 3, 0, 'select id,name from users_group order by id', '', '', '55', '', 'parent_group', '', '', '', '', '', 'a:3:{i:0;s:3:\"600\";i:1;s:0:\"\";i:2;s:1:\"2\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'id', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'id', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"0\";s:11:\"owner_group\";s:1:\"0\";s:10:\"permission\";i:484;}', 0, 0, 'select * from users where status=\"$pole_dop2\" and (date_registration>=\"$pole_dop0 00:00:00\" and  date_registration<=\"$pole_dop1 23:59:59\" or isnull(date_registration)) and\r\n(\"$pole_dop4\">0 and login like concat(char(\"$pole_dop4\"),\"%\") or \"$pole_dop4\"=0) /*and id in(select users from users2group where users_group=$pole_dop3)*/', '50', '0,0,0,0', '', '', 'id', 'delete from users where id=$id and id>=10', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, ''),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit(),', '34', '', '', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', '', 0, ''),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit(),', '34', '', '', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', '', 0, ''),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit()', '4', '', '', '', '', '', '\\Mf\\Permissions\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', '', 0, ''),
  ('users', 'users', 0, 'id', '', 2, 2, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'id', '', 3, 0, '', '', '', '56', '', '', '', '', '', '', '', 'a:5:{i:0;s:3:\"0,0\";i:1;s:30:\"users_edit_base,users_password\";i:2;s:6:\"button\";i:3;s:3:\"500\";i:4;s:3:\"400\";}', 0xD091D0B0D0B7D0BED0B2D18BD0B920D0BFD180D0BED184D0B8D0BBD18C20D0BFD0BED0BBD18CD0B7D0BED0B2D0B0D182D0B5D0BBD18F2CD098D0B7D0BCD0B5D0BDD0B8D182D18C20D0BFD0B0D180D0BED0BBD18C, 'N;', 0, 'N;'),
  ('users', 'users', 0, 'login', '', 2, 3, '', '', '', '2', '', 'login', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'login', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'name', '', 2, 4, '', '', '', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'name', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'full_name', '', 2, 5, '', '', '', '2', '', 'full_name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'full_name', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_registration', '', 2, 6, '', '', ',', '34', '', 'date_registration', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_registration', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_last_login', '', 2, 7, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_last_login', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '1', '', 2, 17, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:16:\"Добавить\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '1', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '', '', 1, 0, 'select id,name from users_group order by name', '', 'onChange=this.form.submit()', '4', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', '', 0, ''),
  ('users', 'users', 0, '', '', 1, 0, '', '', '', '47', '', '', '', '', '', '', '', 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";}', '', '', 0, ''),
  ('users', 'users', 0, 'status', '', 2, 9, '', '', '', '4', '', 'status', '', '', '', '\\Mf\\Permissions\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'status', '', 3, 0, '', '', '', '4', '', '', '', '', '', '\\Mf\\Permissions\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', 'a:3:{s:10:\"owner_user\";s:1:\"0\";s:11:\"owner_group\";s:1:\"0\";s:10:\"permission\";i:484;}', 0, 1, 'select users.*, (select group_concat(users_group) from users2group where users=users.id) as gr from users where id=$get_interface_input', '', '0,0,0,0', 'name', '', 'id', '', '', '', '', '', 'Mf\\Permissions\\Lib\\Func\\SaveUserDetal', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, ''),
  ('users_edit_base', 'users', 0, 'login', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'login', '', 3, 0, '', '', 'size=60', '2', '', 'login', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'name', '', 2, 3, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'name', '', 3, 0, '', '', 'size=60', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'full_name', '', 2, 4, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'full_name', '', 3, 0, '', '', 'size=60', '2', '', 'full_name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'status', '', 2, 6, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'status', '', 3, 0, '', '', '', '4', '', 'status', '', '', '', '\\Mf\\Permissions\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_registration', '', 2, 7, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_registration', '', 3, 0, '', '', ',', '34', '', 'date_registration', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_last_login', '', 2, 8, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_last_login', '', 3, 0, '', '', ',', '34', '', 'date_last_login', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, '1', '', 2, 17, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, '1', '', 3, 0, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:18:\"Сохранить\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"0\";s:11:\"owner_group\";s:1:\"0\";s:10:\"permission\";i:484;}', 0, 0, 'select * from users where id=$get_interface_input', '', '0,0,0,0', '', '', 'id', '', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, ''),
  ('users_password', 'users', 0, 'password', '', 2, 2, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, 'password', '', 3, 0, '', '', ',', '13', '', 'password', '', '', '', '', '', 'N;', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '1', '', 2, 19, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '1', '', 3, 0, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:18:\"Сохранить\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', '', 2, 10, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', '', 3, 0, 'select id,name from users_group order by name', '', '', '55', '', 'gr', '', '', '', '', '', 'a:3:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";}', '', 'N;', 0, 'N;');

INSERT INTO `design_tables_text_interfase` (`language`, `table_type`, `interface_name`, `item_name`, `text`) VALUES 
  ('ru_RU', 1, 'admin_menu', 'caption_dop_', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_dop_1', 'Роль'),
  ('ru_RU', 1, 'admin_menu', 'caption0', 'Меню администраторов'),
  ('ru_RU', 1, 'admin_menu', 'caption_col_roles', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_col_language', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_col_modul', 'Модуль'),
  ('ru_RU', 1, 'admin_menu', 'coment0', 'Страница редактирования панели администрирования сайта. Вы можете менять дерево меню по вашему усмотрению. После создания пункта меню, необходимо выбрать модуль, который будет вызван, когда администратор щелкнет по данному пункту меню. Если Выбранный модуль предоставляет автоматические параметры (список), то необходимо выбрать из предложенного списка нужный вариант. Этот выбранный пункт передается в константе param_a. Список вариантов автомараметров определяется инструкцией \"автовыборка параметров из модуля SQL иструкция\" в модуле управления модулями системы.'),
  ('ru_RU', 1, 'admin_menu', 'caption_dop_0', 'Локаль:'),
  ('ru_RU', 1, 'admin_menu', 'caption_col_url', 'URL'),
  ('ru_RU', 0, 'users_group', 'caption0', 'Системные группы пользователей'),
  ('ru_RU', 0, 'users_group', 'caption_col_name', 'Название'),
  ('ru_RU', 0, 'users_group', 'caption_col_id', 'ID'),
  ('ru_RU', 0, 'users_group', 'caption_col_description', 'Описание'),
  ('ru_RU', 0, 'users_group', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_group', 'coment0', '<br><b>Редактировать можно только разработчику</b>'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption0', 'Группы сайта'),
  ('ru_RU', 0, 'users_group_nonsystem', 'coment0', 'Не системные группы пользователей, которые можно редактировать'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_name', 'Имя группы'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_description', 'Описание'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_id', 'ID группы'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_parent_group', 'Является членом'),
  ('ru_RU', 0, 'users', 'caption_dop_0', 'Дата регистрации (фильтр, начало)'),
  ('ru_RU', 0, 'users', 'caption_dop_1', 'Дата регистрации (фильтр, конец)'),
  ('ru_RU', 0, 'users', 'caption_dop_2', 'Статус'),
  ('ru_RU', 0, 'users', 'caption_col_id', 'Подробно'),
  ('ru_RU', 0, 'users', 'caption_col_login', 'Логин'),
  ('ru_RU', 0, 'users', 'caption_col_name', 'Имя'),
  ('ru_RU', 0, 'users', 'caption_col_full_name', 'Полное имя'),
  ('ru_RU', 0, 'users', 'caption_col_date_registration', 'Дата регистрации'),
  ('ru_RU', 0, 'users', 'caption_col_date_last_login', 'Дата посл.входа'),
  ('ru_RU', 0, 'users', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users', 'caption_dop_4', 'Логин начинается на '),
  ('ru_RU', 0, 'users', 'caption_col_status', 'Статус'),
  ('ru_RU', 0, 'users', 'caption_dop_3', 'Группа'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_gr', 'Член групп'),
  ('ru_RU', 0, 'users', 'caption0', 'Редактирование пользователей'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Редактировать'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Редактировать'),
  ('ru_RU', 0, 'users_edit_base', 'caption0', 'Базовая информация о пользователе'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_login', 'Логин'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_name', 'Имя'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_full_name', 'Полное имя'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_status', 'Статус'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_date_registration', 'Дата регистрации'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_date_last_login', 'дата последнего входа'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_password', 'caption0', 'Смена пароля пользователя'),
  ('ru_RU', 0, 'users_password', 'caption_col_password', 'Новый пароль'),
  ('ru_RU', 0, 'users_password', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Базовый профиль пользователя,Изменить пароль');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-28 17:52:51
