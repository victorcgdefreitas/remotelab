# sql file for remote lab module

CREATE TABLE `remote_labs` (
  `remote_lab_id` smallint(8) NOT NULL,
  `gateway_url` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`remote_lab_id`)
) ENGINE=MyISAM;

INSERT INTO `language_text` VALUES ('en', '_module','remote_lab','Remote Laboratory',NOW(),'');