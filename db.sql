-- MySQL dump 10.13  Distrib 5.5.60, for Win64 (AMD64)
--
-- Host: localhost    Database: auto_script
-- ------------------------------------------------------
-- Server version	5.5.60

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
-- Table structure for table `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_tokens` (
  `userID` int(11) NOT NULL,
  `selector` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_tokens`
--

LOCK TABLES `auth_tokens` WRITE;
/*!40000 ALTER TABLE `auth_tokens` DISABLE KEYS */;
INSERT INTO `auth_tokens` VALUES (1,'Eeo4jG/doWob','3ccd84e3e0c1fcf0b862b30acf9d240bfff72acdcc563ea3c27cad018864c9d2','2018-12-30 17:09:48'),(2,'5Ud4OJjeiFH2','f802506bc3a92a11d2a51aa6ccc29fde71f1660bec6610e4ef1a5fef722282ba','2018-12-28 15:19:50'),(3,'BNJVTZjMkR4A','adbee03f68f2452aae50c5398732df9edb2ac1d73369cd542d7879ff2f109f02','2019-01-28 20:55:51'),(9,'pqpDrvs2UNcv','ee1aed906196c03f7079b53bdc689a2ce0fcc47f0d42f11bcf87f280dce871d8','2019-02-08 18:21:40'),(10,'xNyaBImICRGI','54057de1651818f9f7d7e5710b7fa767b2b00090fdb73ef8bb27d9f91891eab7','2018-12-28 17:58:52'),(12,'pKMAzV6DUhE0','bdef45f4e1343eb6a5c1795c1e1bccabd63e3e4883ee517740a378afad8e4293','2018-12-29 00:24:45'),(13,'3FvKix1++TA9','5a5f23098473e7f7449d5c39fb4fa150f050ffe5af56beebd85a896d4c80c676','2019-01-19 16:09:33'),(14,'gpe7Bi5gzsee','2011c15ac08097de12ccdab421f829e5cee0c7d0663b73027bd09eae88eb1b5a','2019-01-21 21:57:46'),(15,'5fomcporD8+w','cc45c1db809f41700b65a6189435e0237af477d173727f26a57c08105866b49f','2019-01-23 12:11:15'),(26,'z0SChI+OiZ8C','cb71d3ca83e1abb373fb1e7b2e39abd8b78bc610e869567b941235fbc4d87540','2019-01-24 19:54:52'),(27,'aw3SEysTUygZ','1ea541e17a81a3355f96dca302345a092254dadbf2cb491e44fbbf1e0717aa0a','2019-01-29 12:33:22'),(28,'dcWpFV1k7qmb','a0fecefd61cc4fa84f3a9d33c66fa78d59ea3a61dc3a8ab35d48b8b4a184ff92','2019-01-30 15:18:53');
/*!40000 ALTER TABLE `auth_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0 - under review  1 - solved',
  `scriptID` int(11) NOT NULL,
  `requestUser` int(11) NOT NULL,
  `responseAdministor` int(11) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`feedbackID`) USING BTREE,
  KEY `fr_userID_idx` (`requestUser`) USING BTREE,
  KEY `fr_responseID_idx` (`responseAdministor`) USING BTREE,
  KEY `fr_scriptID_idx` (`scriptID`) USING BTREE,
  CONSTRAINT `fr_requestID` FOREIGN KEY (`requestUser`) REFERENCES `user` (`userID`),
  CONSTRAINT `fr_responseID` FOREIGN KEY (`responseAdministor`) REFERENCES `user` (`userID`),
  CONSTRAINT `fr_scriptID` FOREIGN KEY (`scriptID`) REFERENCES `script` (`scriptID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,1,54,9,9,'2019-01-28 12:53:15');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `script`
--

DROP TABLE IF EXISTS `script`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `script` (
  `scriptID` int(11) NOT NULL AUTO_INCREMENT,
  `scriptTitle` varchar(50) NOT NULL,
  `scriptDeveloper` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0 - active 1 - closed',
  `goodCount` int(11) NOT NULL DEFAULT '0',
  `okCount` int(11) NOT NULL DEFAULT '0',
  `badCount` int(11) NOT NULL DEFAULT '0',
  `love` int(11) NOT NULL DEFAULT '0',
  `scriptIntro` varchar(100) NOT NULL,
  `scriptlanguage` varchar(25) DEFAULT NULL,
  `version` varchar(20) NOT NULL,
  `clickCount` int(11) NOT NULL DEFAULT '0',
  `usageCount` int(11) NOT NULL DEFAULT '0',
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`scriptID`),
  KEY `fr_developer_idx` (`scriptDeveloper`) USING BTREE,
  CONSTRAINT `fr_developer` FOREIGN KEY (`scriptDeveloper`) REFERENCES `user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `script`
--

LOCK TABLES `script` WRITE;
/*!40000 ALTER TABLE `script` DISABLE KEYS */;
INSERT INTO `script` VALUES (14,'第一个贴吧',9,0,0,0,0,0,'本吧天下第一','PHP','',239,2,'2018-01-20 17:05:35',NULL),(21,'阿森纳',3,0,0,0,0,0,'test','Python','',48,0,'2019-01-01 20:27:53',NULL),(25,'EDG',3,0,0,0,0,0,'dfgdsfg','Python','',1,0,'2019-01-01 20:47:51',NULL),(29,'宠物蜘蛛',3,0,0,0,0,0,'sdf','Perl','',10,0,'2019-01-01 21:07:55',NULL),(34,'国际米兰',3,0,0,0,0,0,'sdafasdf','JavaScript','',2,0,'2019-01-01 21:14:06',NULL),(35,'知音漫客',3,0,0,0,0,0,'sdafasdf','JavaScript','',4,0,'2019-01-01 21:14:09',NULL),(36,'沈阳二中',3,0,0,0,0,0,'sdafasdf','JavaScript','',20,0,'2019-01-01 21:14:13',NULL),(37,'王卓',3,0,0,0,0,0,'sdafsadf','JavaScript','',14,0,'2019-01-01 21:14:32',NULL),(39,'辽宁男篮',3,0,0,0,0,0,'sdafsadf','JavaScript','',8,0,'2019-01-01 21:14:34',NULL),(40,'比熊',3,0,0,0,0,0,'sdafsadf','JavaScript','',0,0,'2019-01-01 21:14:34',NULL),(41,'新垣结衣',3,0,0,0,0,0,'xcvxcv','JavaScript','',4,0,'2019-01-01 21:16:39',NULL),(48,'RNG',9,0,0,0,0,0,'msi冠军','Python','',2,0,'2019-01-25 15:10:41',NULL),(49,'EDG',9,0,0,0,0,0,'1','Python','',2,0,'2019-01-25 15:19:13',NULL),(53,'RYL',9,0,0,0,0,0,'white','Python','',2,0,'2019-01-25 15:43:47',NULL),(54,'南方科技大学',9,0,0,0,0,0,'nkd','Python','',57,0,'2019-01-25 16:19:51',NULL),(55,'正畸',9,0,0,0,0,0,'我爱牙套','Python','',8,0,'2019-01-25 17:18:51',NULL),(56,'辽宁宏运',9,0,0,0,0,0,'保级队','Ruby','',5,0,'2019-01-27 20:08:33',NULL),(57,'太平洋咖啡',28,0,0,0,0,0,'真贵','Python','',5,0,'2019-01-29 15:22:14',NULL);
/*!40000 ALTER TABLE `script` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `script_comment`
--

DROP TABLE IF EXISTS `script_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `script_comment` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `scriptID` int(11) NOT NULL,
  `commentType` int(1) NOT NULL DEFAULT '0' COMMENT '0 - review  1 - bug report',
  `userComment` varchar(100) DEFAULT NULL,
  `postTime` datetime NOT NULL,
  `thread_content` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`commentID`,`userID`,`scriptID`),
  KEY `fr_scriptID_2` (`scriptID`),
  KEY `fr_userID` (`userID`),
  CONSTRAINT `fr_scriptID_2` FOREIGN KEY (`scriptID`) REFERENCES `script` (`scriptID`),
  CONSTRAINT `fr_userID` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `script_comment`
--

LOCK TABLES `script_comment` WRITE;
/*!40000 ALTER TABLE `script_comment` DISABLE KEYS */;
INSERT INTO `script_comment` VALUES (1,9,14,0,'1','2019-01-19 12:20:31',NULL),(2,9,14,0,'你好你好','2019-01-19 13:50:49',NULL),(3,9,36,0,'你好好','2019-01-19 13:57:57',NULL),(4,9,14,0,'gg','2019-01-19 14:32:28',NULL),(5,14,14,0,'小刘的第一个主题','2019-01-20 21:58:05',NULL),(6,15,29,0,'我已占领此吧','2019-01-22 12:11:52',NULL),(7,15,39,0,'终于轮到我了 我是郭艾伦','2019-01-22 12:14:19',NULL),(8,9,14,0,'内容','2019-01-23 16:22:13','标题'),(9,9,14,0,'标题11','2019-01-23 16:30:08','内容11'),(10,9,14,0,'标题222','2019-01-23 16:34:16','222'),(11,9,14,0,'标题333','2019-01-23 16:38:41','内容 33'),(12,9,14,0,'标题44','2019-01-23 16:40:03','44'),(13,9,14,0,'主题6666','2019-01-23 16:40:40','66666'),(14,9,14,0,'主题777','2019-01-23 16:47:11','内容7'),(15,9,14,0,'主题8','2019-01-23 17:10:32','内容8'),(16,9,21,0,'阿吧的第一个主题由亚索完成','2019-01-23 17:17:37','E往无前就是我'),(17,26,36,0,'我是主题 1-23','2019-01-23 19:55:37','我是内容1-23'),(18,9,14,0,'一月二十五的主题','2019-01-25 11:53:47','我是沙发 弟弟'),(19,9,14,0,'一月二十五的主题2','2019-01-25 12:06:38','55'),(20,9,14,0,'成功了吗','2019-01-25 12:09:14','试试'),(21,9,14,0,'此条不能立刻点击 怎么办','2019-01-25 12:12:35','内容不能为空'),(22,9,14,0,'我是888吗','2019-01-25 12:17:43','试试'),(23,9,14,0,'希望可以','2019-01-25 12:19:07','哦哦'),(24,9,14,0,'希望可以 第二波','2019-01-25 12:19:42','哦哦'),(25,9,14,0,'改过了111','2019-01-25 13:01:09','11111'),(26,9,14,0,'改过了2626','2019-01-25 13:05:21','2626'),(27,9,14,0,'27','2019-01-25 13:06:32','272727'),(28,9,14,0,'28','2019-01-25 13:07:06','28'),(29,9,14,0,'29','2019-01-25 13:07:27','29'),(30,9,14,0,'30','2019-01-25 13:08:57','3030303030'),(31,9,14,0,'3131','2019-01-25 13:10:01','31313131'),(32,9,14,0,'32','2019-01-25 13:11:01','323232'),(33,9,14,0,'33333333333','2019-01-25 13:11:24','3333333333'),(34,9,14,0,'这次应该成功了吧','2019-01-25 13:12:07','求你了'),(35,9,14,0,'没失灵吧 ','2019-01-25 13:43:25','谢谢'),(36,9,36,0,'没活人？','2019-01-26 12:54:26','亚索到此一游'),(37,3,36,0,'韩国队输喽~~','2019-01-26 13:44:13','吼嗨森'),(38,27,54,0,'欢迎大家报考','2019-01-26 19:16:29','1'),(39,9,54,0,'一个只有一楼的主题','2019-01-27 10:27:18','我是一楼'),(40,9,54,0,'测试','2019-01-27 11:11:17','1'),(41,9,56,0,'辽足已凉 呜呜','2019-01-27 20:09:16','呜'),(42,28,56,0,'我爱辽足','2019-01-29 15:19:27','咱们辽宁！！！'),(43,28,57,0,'我是星巴克狗粉丝','2019-01-29 15:23:01','太平洋快滚！！'),(44,28,57,0,'很多人说富豪找老婆不看颜值，不看身材，看重学历，气质涵养。看','2019-01-29 15:27:50','一楼我的');
/*!40000 ALTER TABLE `script_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `script_parameter`
--

DROP TABLE IF EXISTS `script_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `script_parameter` (
  `parameterID` int(11) NOT NULL AUTO_INCREMENT,
  `scriptID` int(11) NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`parameterID`,`scriptID`),
  KEY `fr_script_id_2` (`scriptID`),
  CONSTRAINT `fr_script_id_2` FOREIGN KEY (`scriptID`) REFERENCES `script` (`scriptID`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `script_parameter`
--

LOCK TABLES `script_parameter` WRITE;
/*!40000 ALTER TABLE `script_parameter` DISABLE KEYS */;
INSERT INTO `script_parameter` VALUES (9,14,'a','add'),(10,14,'b','add'),(22,21,'a','sdf'),(30,25,'sadfsad','sdafsad'),(31,25,'sdafsadf','sdfsd'),(35,29,'a','sad'),(41,34,'sdfsdf','sdfsdf'),(42,35,'sdfsdf','sdfsdf'),(43,36,'sdfsdf','sdfsdf'),(44,37,'fsdfsd','asdfsda'),(48,41,'dfsfdfs','sdfsd');
/*!40000 ALTER TABLE `script_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `script_result`
--

DROP TABLE IF EXISTS `script_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `script_result` (
  `resultID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `scriptID` int(11) NOT NULL,
  `iterationID` int(11) NOT NULL,
  `result` mediumtext NOT NULL,
  PRIMARY KEY (`resultID`,`userID`,`scriptID`,`iterationID`),
  KEY `fr_user_id_3` (`userID`),
  KEY `fr_script_id_3` (`scriptID`),
  CONSTRAINT `fr_history_id_1` FOREIGN KEY (`resultID`) REFERENCES `user_history` (`historyID`),
  CONSTRAINT `fr_script_id_3` FOREIGN KEY (`scriptID`) REFERENCES `script` (`scriptID`),
  CONSTRAINT `fr_user_id_3` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `script_result`
--

LOCK TABLES `script_result` WRITE;
/*!40000 ALTER TABLE `script_result` DISABLE KEYS */;
INSERT INTO `script_result` VALUES (54,3,14,0,'6\n '),(56,1,14,0,'0\n ');
/*!40000 ALTER TABLE `script_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiezi`
--

DROP TABLE IF EXISTS `tiezi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiezi` (
  `tieziID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `threadID` int(11) NOT NULL,
  `commentType` int(1) NOT NULL DEFAULT '0' COMMENT '0 - review  1 - bug report',
  `userComment` varchar(10000) DEFAULT NULL,
  `postTime` datetime NOT NULL,
  `notify_cz_tiezi_id` int(11) NOT NULL DEFAULT '0',
  `notify_lz` int(11) NOT NULL DEFAULT '0',
  `notify_cz` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tieziID`,`userID`,`threadID`),
  KEY `fr_scriptID_2` (`threadID`),
  KEY `fr_userID` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiezi`
--

LOCK TABLES `tiezi` WRITE;
/*!40000 ALTER TABLE `tiezi` DISABLE KEYS */;
INSERT INTO `tiezi` VALUES (2,9,2,0,'1','2019-01-20 19:55:22',0,0,0),(3,9,2,0,'主题2 第二条','2019-01-20 19:55:43',0,0,0),(4,9,2,0,'主题2 第三条','2019-01-20 20:05:21',0,0,0),(5,9,2,0,'第四条','2019-01-20 20:06:03',0,0,0),(6,9,4,0,'主题四的第一个','2019-01-20 20:07:49',0,0,0),(7,9,2,0,'我是第二个主题的第五条回复','2019-01-20 21:56:42',0,0,0),(8,14,5,0,'小刘第一个主题的第一个回复 科目三科目三','2019-01-20 21:58:28',0,0,0),(9,15,6,0,'楼主真牛逼','2019-01-22 12:12:14',0,0,0),(10,15,6,0,'测试评论数','2019-01-22 12:16:44',0,0,0),(11,9,14,0,'沙发','2019-01-23 16:57:08',0,0,0),(12,26,17,0,'我是二楼','2019-01-23 19:55:58',0,0,0),(13,3,14,0,'板凳','2019-01-23 22:50:30',0,0,0),(14,9,2,0,'1','2019-01-24 11:10:21',0,0,0),(15,9,2,0,'2','2019-01-24 11:10:40',0,0,0),(16,9,2,0,'999','2019-01-24 11:18:02',0,0,0),(17,9,2,0,'777','2019-01-24 11:18:16',0,0,0),(18,9,2,0,'1','2019-01-24 13:59:31',0,0,0),(19,9,2,0,'2','2019-01-24 14:03:00',0,0,0),(20,9,2,0,'我是十三楼','2019-01-25 10:38:51',0,0,0),(21,3,2,0,'我是十四楼','2019-01-25 10:43:27',0,0,0),(22,3,2,0,'我是十五楼','2019-01-25 10:45:41',0,0,0),(23,3,2,0,'1','2019-01-25 10:46:09',0,0,0),(24,9,2,0,'十七楼','2019-01-25 11:02:01',0,0,0),(25,9,2,0,'十八楼','2019-01-25 11:02:19',0,0,0),(26,9,2,0,'我是十九楼','2019-01-25 11:03:57',0,0,0),(27,9,2,0,'哈哈','2019-01-25 11:04:41',0,0,0),(28,9,2,0,'嘻嘻','2019-01-25 11:06:29',0,0,0),(29,9,2,0,'22 22 22','2019-01-25 11:07:24',0,0,0),(30,9,2,0,' 23 23 23','2019-01-25 11:07:52',0,0,0),(31,9,2,0,'24 24 24','2019-01-25 11:07:57',0,0,0),(32,9,2,0,'二十五','2019-01-25 11:22:49',0,0,0),(33,9,4,0,'三楼','2019-01-25 11:31:34',0,0,0),(34,9,15,0,'有了沙发会好吗','2019-01-25 11:34:50',0,0,0),(35,9,13,0,'好起来了','2019-01-25 11:50:36',0,0,0),(36,3,37,0,'你就是热刺球迷？希望孙儿子赶紧归队 ？？?热翔滚啊','2019-01-26 13:44:59',0,0,0),(37,27,38,0,'2','2019-01-26 19:40:08',0,0,0),(38,27,38,0,'3','2019-01-26 19:41:46',0,0,0),(39,27,38,0,'网站写的辣鸡不让说 你这辈子注定一事无成','2019-01-26 19:49:17',0,0,0),(40,27,38,0,'网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n','2019-01-26 19:51:23',0,0,0),(41,27,38,0,'网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成\n网站写的辣鸡不让说 你这辈子注定一事无成','2019-01-26 19:51:34',0,0,0),(42,27,38,0,'网站写的辣鸡不让说 你这辈子注定一事无成\n                                            网站写的辣鸡不让说 你这辈子注定一事无成\n','2019-01-26 19:56:31',0,0,0),(43,27,38,0,'网站写的辣鸡不让说 你这辈子注定一事无成                    网站写的辣鸡不让说 你这辈子注定一事无成\n                    网站写的辣鸡不让说 你这辈子注定一事无成\n\n','2019-01-26 19:56:49',0,0,0),(44,9,39,0,'我是二楼','2019-01-27 10:27:29',0,0,0),(45,9,38,0,'1','2019-01-27 11:08:08',0,0,0),(46,9,38,0,'10','2019-01-27 11:10:25',0,0,0),(47,9,38,0,'11','2019-01-27 11:10:29',0,0,0),(48,9,38,0,'12','2019-01-27 11:11:04',0,0,0),(49,9,38,0,'13','2019-01-27 11:11:07',0,0,0),(50,9,40,0,'2','2019-01-27 11:11:30',0,0,0),(51,9,40,0,'3','2019-01-27 11:11:34',0,0,0),(52,9,38,0,'你好','2019-01-27 17:14:16',0,0,0),(53,9,38,0,'出现了新问题','2019-01-27 17:17:22',0,0,0),(54,9,38,0,'解决了吗','2019-01-27 17:27:12',0,0,0),(55,9,38,0,'或许这就是天才吧','2019-01-27 17:27:20',0,0,0),(56,9,38,0,'能跳吗','2019-01-27 17:41:50',0,0,0),(57,9,38,0,'还真跳了','2019-01-27 17:42:37',0,0,0),(58,9,38,0,'记住哪个主题了吧 ','2019-01-27 18:01:20',0,0,0),(59,9,38,0,'啊啊啊','2019-01-27 18:02:58',0,0,0),(60,9,38,0,'不行啊','2019-01-27 18:10:52',0,0,0),(61,9,38,0,'1','2019-01-27 18:16:19',0,0,0),(62,9,38,0,'1','2019-01-27 18:22:05',0,0,0),(63,9,38,0,'1','2019-01-27 18:24:47',0,0,0),(64,9,38,0,'1','2019-01-27 18:26:20',0,0,0),(65,9,38,0,'1','2019-01-27 18:27:01',0,0,0),(66,9,38,0,'1','2019-01-27 18:44:18',0,0,0),(67,9,38,0,'1','2019-01-27 18:45:03',0,0,0),(68,9,38,0,'1','2019-01-27 18:45:36',0,0,0),(69,9,38,0,'1','2019-01-27 18:46:21',0,0,0),(70,9,38,0,'1','2019-01-27 18:46:50',0,0,0),(71,9,38,0,'1','2019-01-27 18:47:36',0,0,0),(72,9,38,0,'1','2019-01-27 18:49:54',0,0,0),(73,9,38,0,'1','2019-01-27 18:50:41',0,0,0),(74,9,38,0,'1','2019-01-27 18:51:55',0,0,0),(75,9,38,0,'1','2019-01-27 19:08:30',0,0,0),(76,9,38,0,'1','2019-01-27 19:09:30',0,0,0),(77,9,38,0,'试试楼中楼','2019-01-27 19:09:52',0,0,0),(78,9,38,0,'有点绿色嗷','2019-01-27 19:18:48',0,0,0),(79,9,38,0,'回复43楼回复八楼','2019-01-27 19:28:35',0,0,0),(80,9,38,0,'回复40 楼回复40层 你可真是个弟弟','2019-01-27 19:29:37',0,0,0),(81,9,38,0,'回复2楼:沙发wsnd','2019-01-27 19:30:23',0,0,0),(82,9,38,0,'回复2楼: 嗯','2019-01-27 19:53:36',27,0,0),(83,9,38,0,'回复10楼: 你是亚索？','2019-01-27 19:53:56',9,0,0),(84,9,38,0,'回复4楼: nmsl','2019-01-27 20:02:38',27,0,0),(85,9,41,0,'你辽 滚啊 我恒天哈第一','2019-01-27 20:09:42',0,0,0),(86,9,41,0,'回复2楼: 新发的不能回复吧','2019-01-27 20:10:21',9,0,0),(87,9,41,0,'两个问题 1.正常回复刚发出来 不能回复 2。楼中楼刚发出来不能显示','2019-01-27 20:11:05',0,0,0),(88,9,41,0,'玩的绿色','2019-01-27 20:29:01',0,0,0),(89,9,41,0,'这下刚发的帖子可以回复了吧 相当于省了一次刷新 ','2019-01-27 20:31:21',0,0,0),(90,9,41,0,'这下刚发的帖子可以回复了吧 相当于省了一次刷新2','2019-01-27 20:32:38',0,0,0),(91,9,41,0,'新发一个','2019-01-27 20:41:52',0,0,0),(92,9,41,0,'回复8楼: 1','2019-01-27 20:43:24',9,0,0),(93,9,41,0,'上一次不算','2019-01-27 20:43:49',0,0,0),(94,9,41,0,'这次应该可以了','2019-01-27 20:46:14',0,0,0),(95,9,41,0,'回复11楼: 来来来','2019-01-27 20:46:23',9,0,0),(96,9,41,0,'再试一次','2019-01-27 20:50:53',0,0,0),(97,9,41,0,'回复13楼: 又不同吗','2019-01-27 20:51:07',9,0,0),(98,9,41,0,'回复13楼: 有不同吗','2019-01-27 20:51:43',9,0,0),(99,9,41,0,'连续1','2019-01-27 20:54:31',0,0,0),(100,9,41,0,'连续2','2019-01-27 20:54:35',0,0,0),(101,9,41,0,'回复17楼: 没问题吧','2019-01-27 20:54:54',9,0,0),(102,9,38,0,'回复7楼: 你好','2019-01-27 21:14:43',27,0,0),(103,9,38,0,'回复2楼: 你好啊','2019-01-27 21:18:01',0,0,27),(104,9,2,0,'回复15楼: 回复你试试','2019-01-27 21:19:34',22,0,3),(105,9,2,0,'回复14楼: 成功了','2019-01-27 22:45:38',21,9,3),(106,9,2,0,'1','2019-01-27 22:52:15',0,9,0),(107,9,2,0,'回复16楼: 666','2019-01-27 22:52:45',23,9,3),(108,27,38,0,'回复17楼: 谢谢侬','2019-01-28 12:33:59',55,27,9),(109,9,40,0,'绿色','2019-01-28 14:58:48',0,9,0),(110,9,40,0,'回复4楼: 我人晕了','2019-01-28 14:58:58',109,9,9),(111,9,40,0,'ok的ok','2019-01-28 19:52:51',0,9,0),(112,9,40,0,'回复6楼: 又有','2019-01-28 19:52:57',111,9,9),(113,9,16,0,'你好','2019-01-28 20:00:40',0,9,0),(114,9,16,0,'回复2楼: 问题出在哪里','2019-01-28 20:04:52',113,9,9),(115,9,16,0,'回复3楼: 问题出在哪里','2019-01-28 20:08:09',114,9,9),(116,9,16,0,'回复4楼: 求你了','2019-01-28 20:09:58',115,9,9),(117,9,16,0,'回复5楼: 求你了2','2019-01-28 20:12:25',116,9,9),(118,9,16,0,'回复6楼: 我是搞不懂了','2019-01-28 20:16:28',117,9,9),(119,9,16,0,'我是晕了','2019-01-28 20:19:42',0,9,0),(120,9,16,0,'回复7楼: 我佛了','2019-01-28 20:19:55',118,9,9),(121,9,16,0,'回复9楼: 难顶了','2019-01-28 20:26:15',120,9,9),(122,9,16,0,'回复10楼: 无限递归','2019-01-28 20:26:24',121,9,9),(123,9,16,0,'回复11楼: 还行嗷','2019-01-28 20:26:39',122,9,9),(124,9,16,0,'我永远支持托儿所','2019-01-28 20:34:02',0,9,0),(125,9,16,0,'回复13楼: 成了嗷','2019-01-28 20:34:09',124,9,9),(126,9,16,0,'回复14楼: 你说尼玛的弟弟','2019-01-28 20:34:20',125,9,9),(127,9,7,0,'你是尼玛的郭艾伦呢','2019-01-29 13:48:20',0,15,0),(128,9,7,0,'回复2楼: wsnd','2019-01-29 13:48:26',127,15,9),(129,28,42,0,'我是沙发','2019-01-29 15:20:01',0,28,0),(130,28,42,0,'回复2楼: 沙发nmsl','2019-01-29 15:20:09',129,28,28),(131,28,38,0,'回复48楼: 切碎机风前行','2019-01-29 15:29:10',103,27,9),(132,9,42,0,'回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n回复2楼: 沙发nmsl\n','2019-01-29 17:00:15',0,28,0);
/*!40000 ALTER TABLE `tiezi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` int(11) NOT NULL DEFAULT '0' COMMENT '0-user(normal user+developer) 1-administrator',
  `loved_tieba` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'zhangyuqun','$2y$10$.9IIi5qySrg32lcuTjomOevv2m12BOgCobSAK5UOPOoLyvRS084cC',0,NULL),(2,'zhangjin','$2y$10$Kh.4ZWWMDiP2mf45jeoJ9uqPlG6/CQK7SmwW6dWeKeLGYRCy9UbBC',0,NULL),(3,'shiyuhui','$2y$10$mkmJXue3QFE6Rh/k1DoI6OwTpK4jNUCY5eI.HLgNoMwtxfX/rFryG',0,' 39 21 21 21'),(4,'tangke','$2y$10$nKyem8RatalzGXo3sQtOYuED86AFYxEXKRAR8nKB5rOs6hxxsPsfq',0,NULL),(5,'chenshiyi','$2y$10$uLb4B/Q93bg4WQoA/LDluOoeUvKwiZNfk6PM9YHbKdAZ3dfcSWy8G',0,NULL),(6,'luozongwei','$2y$10$WzC954TZgsWYF6/jMgs2g.dvP/nQ2xydRDXJMj3sRDuC7Z9E.l9yu',0,NULL),(7,'tangbo','$2y$10$NTSHfey75YmkowkRwlg5IeyzD5gVK.e./z/cjcLRfxR7.jzj34bBe',0,NULL),(8,'zhuyueming','$2y$10$d7651hxo/5ioj62g9UgBTeG2pl19tjL/hwfO/aFVGjCGHtN5QuGya',0,NULL),(9,'yasuo','$2y$10$m1WoRSUxvp8sct6fvNIe0eRg3m7MMKrWF8qeXIn5N6lP14wJ.OvvS',1,' 21 39 36 48 56'),(10,'nicole','$2y$10$mqkUhe9AFuPWdvh85oL/k.UlgsJNkPnk6ZbMtQ74.Lg.zdTRH9wqW',0,NULL),(11,'nicolemei','$2y$10$pvy9f4Qdaxjw/PTH93aYHePljUIzC0Q2xuQGRR1gnNShQDHKxcmbC',0,NULL),(12,'zhang','$2y$10$ExwA0itsQOBkMsAOEn27beUiTKh4/gHNh1HAjMvigOLfoEoevJBJG',0,NULL),(13,'woshinigege','$2y$10$73QCH05sAimlNg5kGdFkc.HSqAG0tEdcp1jTduk3G31kGf6B1Xqj2',0,NULL),(14,'liuli','$2y$10$j5zyPko0naHAunVxi3QlP.vWZyJvirPktJrwRB6XxGgqKyWOQFvpi',0,NULL),(15,'mayanqi','$2y$10$XUUlDzJmu2MUymJdVa3KveeoPxApWfXSz48EGxNnpyDDtZ.8Sd1Tu',0,NULL),(16,'1','$2y$10$xp8e5lbt67F6hzXYPOXAUOJ0FFoI.Wsvp55UnHk5aQHTrz7ezbl8m',0,NULL),(17,'test','$2y$10$LCVVZ9JcesX1gOVi4fOXEe0q8XFYS6a3aybYfWfnTbK8vdFHZO6XW',0,NULL),(18,'test2','$2y$10$IPEIZawYp/MyfO4SYq.Ztu.HQGNHrEEzAWhI.T.tBmAx7ekXo.oeC',0,NULL),(19,'yyyy','$2y$10$7ZxlUXFtNAMMiyXNuuLlD.P3MF3J5HIB/J59waWcaVUF4Y.7cYCi6',0,NULL),(20,'yasuo22','$2y$10$/cjDqeswSlqs9sl8VcdyKeEQESi5QzHI7iap4I8v/CYVZ5t3XW6xy',0,NULL),(21,'hahahehe','$2y$10$y6WwkBxK.4RheEA5sLUPDOnTVJ2RKMbBdWtjf8G4.mdrATmErof0u',0,NULL),(22,'yyyy2','$2y$10$h7DLbzmXeCuL.4BHk82YGulV8XZBR3klGY/MjRIdurPYZ3Uz7wIWe',0,NULL),(23,'tt','$2y$10$uSfuXFj25hCq6SIZs7kX5e9W8s3CcX5qGQcTy3aGL7Q4UphUTvIOe',0,NULL),(24,'yasuo6','$2y$10$gwlyACWxkaYvrdjhL6fOfOrhKJUrG0n4c8dVsjyVEGvG2sV3WuIM6',0,NULL),(25,'yasuo5555','$2y$10$w2km2k.Ige2i1KYNLzCOmOMzzjY6OMJ0bi59807iZy7SoEVCyD.TS',0,NULL),(26,'liuli2','$2y$10$7dlsrMd0J7nRyujxKC9OD./l97894E/lBqjehO.snVA.clc1Vg2Sq',0,NULL),(27,'wuxingjian','$2y$10$8oD4.yafQsEkn0wsffIVSOgDBjAxFyEk5A6oFDDjUTuUGyMPTPzGS',0,' 54'),(28,'我爱六十名','$2y$10$gjTdxYfnLvfPHFqlxWXjIO3/rQd.w/ZpD44fX8Y2twchsRHO9Ekb.',0,' 39');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_history`
--

DROP TABLE IF EXISTS `user_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_history` (
  `historyID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `scriptID` int(11) NOT NULL,
  `type` int(1) NOT NULL COMMENT '0 - run script  1 - give a score  2 - love',
  `time` datetime NOT NULL,
  `parameter` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`historyID`,`userID`,`scriptID`),
  KEY `fr_script_id` (`scriptID`),
  KEY `fr_user_id` (`userID`),
  CONSTRAINT `fr_script_id` FOREIGN KEY (`scriptID`) REFERENCES `script` (`scriptID`),
  CONSTRAINT `fr_user_id` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_history`
--

LOCK TABLES `user_history` WRITE;
/*!40000 ALTER TABLE `user_history` DISABLE KEYS */;
INSERT INTO `user_history` VALUES (54,3,14,0,'2018-12-29 17:06:14','2 4'),(56,1,14,0,'2018-12-29 17:35:00','hello laoshi');
/*!40000 ALTER TABLE `user_history` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-02-08 11:14:39
