/**
 * Create structure for AOD RCT System
 */

 DROP TABLE IF EXISTS `games`;
 CREATE TABLE `games` (
   `id` int(10) NOT NULL AUTO_INCREMENT,
   `description` varchar(500) NOT NULL,
   `short_name` varchar(10) NOT NULL,
   `full_name` varchar(30) NOT NULL,
   `subforum` varchar(60) NOT NULL,
   PRIMARY KEY (`id`)
   );

 DROP TABLE IF EXISTS `rules_threads`;
 CREATE TABLE `games_threads` (
  `game_id` int(20) NOT NULL,
  `thread_url` varchar(60) NOT NULL,
  `thread_title` varchar(60) NOT NULL,
  PRIMARY KEY (`thread_url`),
  KEY `thread_url` (`thread_url`,`thread_title`)
);

 DROP TABLE IF EXISTS `squad_leaders`;
 CREATE TABLE `squad_leaders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `game_id` tinyint(4) NOT NULL,
  `name` varchar(30) NOT NULL COMMENT 'username',
  `email` text NOT NULL,
  PRIMARY KEY (`id`)
);

 DROP TABLE IF EXISTS `squad_members`;
 CREATE TABLE `squad_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `game_id` tinyint(4) DEFAULT NULL,
  `name` int(11) NOT NULL,
  `rank` enum('RCT','CDT','PVT','PFC','SPEC') NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`id`)
);