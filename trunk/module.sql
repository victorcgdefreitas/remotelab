# sql file for remote lab module
-- 
-- Tablo yapısı: `experiments`
-- 

CREATE TABLE `rl_experiments` (
  `experiment_id` int(8) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `package_id` mediumint(8) default NULL,
  `maxallowedtimes` int(8) default NULL,
  `visible` tinyint(1) default NULL,
  `reservation_duration` mediumint(8) default NULL,
  `start` timestamp NULL default NULL,
  `end` timestamp NULL default NULL,
  `time` timestamp NULL default NULL,
  PRIMARY KEY  (`experiment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Tablo döküm verisi `experiments`
-- 


-- --------------------------------------------------------

-- 
-- Tablo yapısı: `experiments_es`
-- 

CREATE TABLE `rl_experiments_es` (
  `experiments_es_id` int(8) NOT NULL auto_increment,
  `experiment_set_id` int(8) NOT NULL,
  `experiment_id` int(8) NOT NULL,
  PRIMARY KEY  (`experiments_es_id`),
  KEY `fk_experiments_es_experiment_sets` (`experiment_set_id`),
  KEY `fk_experiments_es_experiments` (`experiment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Tablo döküm verisi `experiments_es`
-- 


-- --------------------------------------------------------

-- 
-- Tablo yapısı: `experiment_sets`
-- 

CREATE TABLE `rl_experiment_sets` (
  `experiment_set_id` int(11) NOT NULL auto_increment,
  `remote_lab_id` int(11) NOT NULL,
  `set_code` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`experiment_set_id`),
  KEY `fk_experiment_sets_remote_labs` (`remote_lab_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Tablo döküm verisi `experiment_sets`
-- 


-- --------------------------------------------------------

-- 
-- Tablo yapısı: `remote_labs`
-- 

CREATE TABLE `rl_remote_labs` (
  `remote_lab_id` int(8) NOT NULL auto_increment,
  `gateway_url` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `type` tinyint(3) default NULL,
  `course_id` mediumint(8) default NULL,
  PRIMARY KEY  (`remote_lab_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Tablo döküm verisi `remote_labs`
-- 


-- --------------------------------------------------------

-- 
-- Tablo yapısı: `reservations`
-- 

CREATE TABLE `rl_reservations` (
  `reservation_id` int(8) NOT NULL auto_increment,
  `experiments_es_id` int(8) NOT NULL,
  `member_id` int(8) default NULL,
  `start_time` timestamp NULL default NULL,
  PRIMARY KEY  (`reservation_id`),
  KEY `fk_reservations_experiments_es` (`experiments_es_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Tablo döküm verisi `reservations`
--
-- --------------------------------------------------------

--
-- Tablo yapısı: `rl_scorm_1_2_org`
--
CREATE TABLE `rl_scorm_1_2_org` (
  `org_id` mediumint(8) unsigned NOT NULL auto_increment,
  `package_id` mediumint(8) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `credit` varchar(15) NOT NULL default 'no-credit',
  `lesson_mode` varchar(15) NOT NULL default 'browse',
  PRIMARY KEY  (`org_id`),
  KEY `package_id` (`package_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `rl_scorm_1_2_item`
--

CREATE TABLE `rl_scorm_1_2_item` (
  `item_id` mediumint(8) unsigned NOT NULL auto_increment,
  `org_id` mediumint(8) unsigned NOT NULL,
  `idx` varchar(15) NOT NULL,
  `title` varchar(255) default NULL,
  `href` varchar(255) default NULL,
  `scormtype` varchar(15) default NULL,
  `prerequisites` varchar(255) default NULL,
  `maxtimeallowed` varchar(255) default NULL,
  `timelimitaction` varchar(255) default NULL,
  `datafromlms` varchar(255) default NULL,
  `masteryscore` mediumint(8) default NULL,
  PRIMARY KEY  (`item_id`),
  KEY `org_id` (`org_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `rl_packages`
--

CREATE TABLE `rl_packages` (
  `package_id` mediumint(8) unsigned NOT NULL auto_increment,
  `source` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `course_id` mediumint(8) unsigned NOT NULL,
  `ptype` varchar(63) NOT NULL,
  PRIMARY KEY  (`package_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `rl_cmi`
--

CREATE TABLE `rl_cmi` (
  `cmi_id` mediumint(8) unsigned NOT NULL auto_increment,
  `item_id` mediumint(8) unsigned NOT NULL,
  `member_id` mediumint(8) unsigned NOT NULL,
  `lvalue` varchar(63) NOT NULL,
  `rvalue` blob,
  PRIMARY KEY  (`cmi_id`),
  UNIQUE KEY `item_id` (`item_id`,`member_id`,`lvalue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `language_text` VALUES ('en', '_module','remotelab','Remote Laboratory',NOW(),'');
INSERT INTO `language_text` VALUES ('tr', '_module','remotelab','Uzaktan Erişimli Laboratuar',NOW(),' ');