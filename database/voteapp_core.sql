-- MySQL dump 10.13  Distrib 5.1.72, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: voteapp
-- ------------------------------------------------------
-- Server version	5.1.72-0ubuntu0.10.04.1

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
-- Table structure for table `EANs`
--

DROP TABLE IF EXISTS `EANs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EANs` (
  `ean_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ean_code` varchar(48) DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `verified` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`ean_id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competitions`
--

DROP TABLE IF EXISTS `competitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competitions` (
  `competition_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `description` text,
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `show_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `compo_enabled` tinyint(1) unsigned DEFAULT '0',
  `submit_enabled` tinyint(1) unsigned DEFAULT '0',
  `hard_deadline` tinyint(1) unsigned DEFAULT '0',
  `locked` tinyint(1) unsigned DEFAULT '0',
  `voting` tinyint(1) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`competition_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configurations`
--

DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurations` (
  `configuration_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `value` text,
  `description` text,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`configuration_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contributers`
--

DROP TABLE IF EXISTS `contributers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contributers` (
  `contributer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `ean_id` bigint(20) unsigned DEFAULT NULL,
  `voter_only` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`contributer_id`),
  UNIQUE KEY `event_id` (`event_id`,`username`,`email`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contributions`
--

DROP TABLE IF EXISTS `contributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contributions` (
  `contribution_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contributer` varchar(128) DEFAULT NULL,
  `entry_name` varchar(128) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `beamer_info` varchar(128) DEFAULT NULL,
  `filename` varchar(96) DEFAULT NULL,
  `thumbnail_filename` varchar(102) DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `competition_id` bigint(20) unsigned DEFAULT NULL,
  `ean_id` bigint(20) unsigned DEFAULT NULL,
  `approved` tinyint(1) DEFAULT '-1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`contribution_id`),
  UNIQUE KEY `event_id` (`event_id`,`competition_id`,`ean_id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fake_event_id` bigint(20) unsigned DEFAULT '0',
  `event_name` varchar(128) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enabled` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remote_addr` varchar(20) DEFAULT NULL,
  `method_call` varchar(32) DEFAULT NULL,
  `severity` tinyint(1) unsigned DEFAULT NULL,
  `string` text,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=94917 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `new_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) DEFAULT NULL,
  `subtitle` varchar(128) DEFAULT NULL,
  `body` text,
  `enabled` tinyint(1) DEFAULT '1',
  `event_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`new_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  `salt` char(64) DEFAULT NULL,
  `access` tinyint(1) unsigned DEFAULT '1',
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


INSERT INTO `users` VALUES (1,'admin','10d522c031e691d475542faccbb57fcfb3c0836d1d033c38921c0e72dbae174b','WGm1rAzrQG9K3VGwgwaT4q6pRSnv30wO2gh6zbPPp9Uxx9YwdMmkfVxjnfWbEQsc',4,1,now(), now());

--
-- Temporary table structure for view `view_calculate_results`
--

DROP TABLE IF EXISTS `view_calculate_results`;
/*!50001 DROP VIEW IF EXISTS `view_calculate_results`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_calculate_results` (
 `ean_id` tinyint NOT NULL,
  `result` tinyint NOT NULL,
  `contribution_id` tinyint NOT NULL,
  `competition_id` tinyint NOT NULL,
  `event_id` tinyint NOT NULL,
  `vote_value` tinyint NOT NULL,
  `value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_results`
--

DROP TABLE IF EXISTS `view_results`;
/*!50001 DROP VIEW IF EXISTS `view_results`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_results` (
 `result` tinyint NOT NULL,
  `contribution_id` tinyint NOT NULL,
  `competition_id` tinyint NOT NULL,
  `event_id` tinyint NOT NULL,
  `score` tinyint NOT NULL,
  `voters` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_vote_weight`
--

DROP TABLE IF EXISTS `view_vote_weight`;
/*!50001 DROP VIEW IF EXISTS `view_vote_weight`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_vote_weight` (
 `ean_id` tinyint NOT NULL,
  `competition_id` tinyint NOT NULL,
  `votes` tinyint NOT NULL,
  `vote_value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `vote_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contribution_id` bigint(20) unsigned DEFAULT NULL,
  `competition_id` bigint(20) unsigned DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `ean_id` bigint(20) unsigned DEFAULT NULL,
  `result` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `contribution_id` (`contribution_id`,`competition_id`,`event_id`,`ean_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1260 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Final view structure for view `view_calculate_results`
--

/*!50001 DROP TABLE IF EXISTS `view_calculate_results`*/;
/*!50001 DROP VIEW IF EXISTS `view_calculate_results`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`voteapp`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_calculate_results` AS select `votes`.`ean_id` AS `ean_id`,`votes`.`result` AS `result`,`votes`.`contribution_id` AS `contribution_id`,`votes`.`competition_id` AS `competition_id`,`votes`.`event_id` AS `event_id`,`weight`.`vote_value` AS `vote_value`,((`votes`.`result` * `weight`.`vote_value`) * 0.50) AS `value` from (`votes` join `view_vote_weight` `weight`) where ((`votes`.`ean_id` = `weight`.`ean_id`) and (`votes`.`competition_id` = `weight`.`competition_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_results`
--

/*!50001 DROP TABLE IF EXISTS `view_results`*/;
/*!50001 DROP VIEW IF EXISTS `view_results`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`voteapp`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_results` AS select `view_calculate_results`.`result` AS `result`,`view_calculate_results`.`contribution_id` AS `contribution_id`,`view_calculate_results`.`competition_id` AS `competition_id`,`view_calculate_results`.`event_id` AS `event_id`,sum(`view_calculate_results`.`value`) AS `score`,count(`view_calculate_results`.`contribution_id`) AS `voters` from `view_calculate_results` group by `view_calculate_results`.`contribution_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_vote_weight`
--

/*!50001 DROP TABLE IF EXISTS `view_vote_weight`*/;
/*!50001 DROP VIEW IF EXISTS `view_vote_weight`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`voteapp`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_vote_weight` AS select `votes`.`ean_id` AS `ean_id`,`votes`.`competition_id` AS `competition_id`,count(`votes`.`ean_id`) AS `votes`,(count(`votes`.`ean_id`) * 3.14) AS `vote_value` from `votes` group by `votes`.`ean_id`,`votes`.`competition_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-27  9:43:19
