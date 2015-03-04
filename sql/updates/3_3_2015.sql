/**
 * SQL Update -- 3/3/2015
 * Updates structure for LOA table and adds an entry for LOA management tool to the user tools table
 *
 * Signed off by: Guybrush
 */

--
-- LOA table structure update and content
--

DROP TABLE IF EXISTS `loa`;
CREATE TABLE `loa` (
  `member_id` int(11) NOT NULL,
  `date_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reason` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `loa` VALUES ('26000','2015-03-01 20:39:45','Extended Military Deployment','1'), ('29234','2015-03-01 20:40:05','Work','1'), ('26995','2015-03-01 20:41:16','Military','1');

--
-- user tools table structure update and content
--

DROP TABLE IF EXISTS `user_tools`;
CREATE TABLE `user_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_name` text NOT NULL,
  `class` text NOT NULL,
  `tool_descr` text NOT NULL,
  `tool_path` text NOT NULL,
  `role_id` int(11) NOT NULL,
  `icon` text NOT NULL,
  `disabled` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `user_tools` VALUES ('1','Add new recruit','addRct','Start the recruiting process with a division candidate','/recruiting','1','plus-square text-success','0'), ('2','Manage inactive members','revInactives','View inactive members and flag for removal','/manage/inactive-members','1','flag','0'), ('3','Generate division structure','divGenerator','Generate a new division structure skeleton','#','2','cog text-info','0'), ('4','Manage LOAs','mngLoa','Manage division leaves of absence','/manage/leaves-of-absence','2','clock-o','0');