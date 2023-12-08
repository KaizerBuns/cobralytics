use cobracmd;

/****
NEW Jan 23, 2017
*/

DROP TABLE if exists `stats_visitor_info`;
CREATE TABLE `stats_visitor_info` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`visitor_id` char(32) NOT NULL,
`visit_date` date NOT NULL,
`visit_time` datetime NOT NULL,
`ip_address` varchar(20) NOT NULL,
`country` char(4) NOT NULL,
`country_name` varchar(255) NOT NULL DEFAULT '',
`region` char(4) NOT NULL,
`region_name` varchar(255) NOT NULL DEFAULT '',
`city` varchar(50) NOT NULL,
`latitude` varchar(25) NOT NULL DEFAULT '',
`longitude` varchar(25) NOT NULL DEFAULT '',
`platform` varchar(25) NOT NULL,
`browser` varchar(25) NOT NULL,
`version` varchar(25) NOT NULL,
`mobile` tinyint(1) NOT NULL DEFAULT '0',
`screen` varchar(15) NOT NULL DEFAULT '0',
`last_visit` datetime NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY(`visitor_id`),
KEY `visit_date` (`visit_date`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE if exists `stats_visitor_actions`;
CREATE TABLE `stats_visitor_actions` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`click_id` char(16) NOT NULL,
`visitor_id` char(32) NOT NULL DEFAULT '',
`visit_date` date NOT NULL,
`visit_time` datetime NOT NULL,
`user_id` int(10) unsigned NOT NULL,
`user_name` varchar(100) NOT NULL DEFAULT '',
`project_id` int(10) unsigned NOT NULL,
`project_name` varchar(100) NOT NULL DEFAULT '',
`campaign_id` int(10) unsigned NOT NULL,
`campaign_name` varchar(100) NOT NULL DEFAULT '',
`rule_id` int(10) unsigned NOT NULL,
`rule_name` varchar(100) NOT NULL DEFAULT '',
`rule_type` varchar(50) NOT NULL DEFAULT '',
`rotator_id` int(10) unsigned NOT NULL,
`rotator_name` varchar(100) NOT NULL DEFAULT '',
`rotator_type` varchar(50) NOT NULL DEFAULT '',
`service_id` int(10) unsigned NOT NULL,
`service_name` varchar(100) NOT NULL DEFAULT '',
`source_id` int(10) unsigned NOT NULL,
`source_name` varchar(100) NOT NULL DEFAULT '',
`domain_id` int(10) unsigned NOT NULL,
`domain_name` varchar(100) NOT NULL DEFAULT '',
`offer_id` int(10) unsigned NOT NULL,
`offer_name` varchar(100) NOT NULL DEFAULT '',
`linkhash` varchar(7) NOT NULL,
`ip_address` varchar(20) NOT NULL,
`traffic_type` enum('Search Organic','Direct','Referral','Social','Campaign') NOT NULL DEFAULT 'Referral',
`country` char(4) NOT NULL,
`country_name` varchar(255) NOT NULL DEFAULT '',
`region` char(4) NOT NULL,
`region_name` varchar(255) NOT NULL DEFAULT '',
`city` varchar(50) NOT NULL,
`latitude` varchar(25) NOT NULL DEFAULT '',
`longitude` varchar(25) NOT NULL DEFAULT '',
`platform` varchar(25) NOT NULL,
`browser` varchar(25) NOT NULL,
`version` varchar(25) NOT NULL,
`mobile` tinyint(1) NOT NULL DEFAULT '0',
`screen` varchar(15) NOT NULL DEFAULT '0',
`referer` varchar(255) NOT NULL DEFAULT '',
`sub1` varchar(255) DEFAULT '',
`sub2` varchar(255) DEFAULT '',
`sub3` varchar(255) DEFAULT '',
`sub4` varchar(255) DEFAULT '',
`sub5` varchar(255) DEFAULT '',
`destination` varchar(255) DEFAULT '',
`page` varchar(255) NOT NULL DEFAULT '',
`query` varchar(255) NOT NULL DEFAULT '',
`duration` int(11) NOT NULL DEFAULT '0',
`visits` int(11) NOT NULL DEFAULT '0',
`clicks` int(11) NOT NULL DEFAULT '0',
`click_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`revenue` double(8,4) NOT NULL DEFAULT '0.0000',
`payout` double(8,4) NOT NULL DEFAULT '0.0000',
`cost` double(8,4) NOT NULL DEFAULT '0.0000',
`leads` int(11) NOT NULL DEFAULT '0',
`lead_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`conversions` tinyint(4) NOT NULL DEFAULT '0',
`conversion_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
UNIQUE KEY(`click_id`),
KEY `visit_date` (`visit_date`),
KEY `idx1` (`project_id`,`user_id`,`visit_date`),
KEY `idx2` (`project_id`,`user_id`,`visit_time`),
KEY `visit_time` (`visit_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* 
Replaces 
    stats_campaign
    stats_offer
    stats_projects
    stats_source 
*/
DROP TABLE if exists `stats_summary_manage`;
CREATE TABLE `stats_summary_manage` (
    `id` int unsigned not null AUTO_INCREMENT,
    `date` date NOT NULL,
    `user_id` int unsigned NOT NULL,
    `project_id` int unsigned NOT NULL,    
    `campaign_id` int unsigned NOT NULL,
    `source_id` int unsigned NOT NULL,
    `domain_id` int unsigned NOT NULL,
    `offer_id` int unsigned NOT NULL,
    `rule_id` int unsigned NOT NULL,
    `service_id` int unsigned NOT NULL,
    `hits` int NOT NULL DEFAULT '0',
    `uniques` int NOT NULL DEFAULT '0',
    `visitors` int NOT NULL DEFAULT '0',
    `clicks` int NOT NULL DEFAULT '0',
    `conversion` int NOT NULL DEFAULT '0',    
    `cost` double(8, 4) NOT NULL default '0.00',
    `revenue` double(8, 4) NOT NULL default '0.00',
    `processing` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `date` (`date`),    
    KEY `idx1` (`project_id`, `user_id`, `date`),
    KEY `idx2` (`processing`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* 
Replaces 
    stats_country
    stats_ipaddress
*/
DROP TABLE if exists `stats_summary_country`;
CREATE TABLE `stats_summary_country` (
    `id` int unsigned not null AUTO_INCREMENT,
    `date` date NOT NULL,
    `user_id` int unsigned NOT NULL,
    `project_id` int unsigned NOT NULL,    
    `country` char(4) NOT NULL,
    `region` char(4) NOT NULL,
    `city` varchar(50) NOT NULL,
    `ip` varchar(20) NOT NULL,
    `hits` int NOT NULL DEFAULT '0',
    `uniques` int NOT NULL DEFAULT '0',
    `visitors` int NOT NULL DEFAULT '0',
    `clicks` int NOT NULL DEFAULT '0',
    `conversion` int NOT NULL DEFAULT '0',    
    `cost` double(8, 4) NOT NULL default '0.00',
    `revenue` double(8, 4) NOT NULL default '0.00',
    `processing` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `date` (`date`),    
    KEY `idx1` (`project_id`, `user_id`, `date`),
    KEY `idx2` (`processing`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* 
Replaces 
    stats_traffic
    stats_subids
    stats_pages
    stats_agents
*/
DROP TABLE if exists `stats_summary_traffic`;
CREATE TABLE `stats_summary_traffic` (
    `id` int unsigned not null AUTO_INCREMENT,
    `date` date NOT NULL,
    `user_id` int unsigned NOT NULL,
    `project_id` int unsigned NOT NULL,
    `traffic_type` enum('Search Organic', 'Direct', 'Referral', 'Social', 'Campaign') NOT NULL default 'Direct',
    `platform` varchar(25) NOT NULL,
    `browser` varchar(25) NOT NULL,
    `version` varchar(25) NOT NULL,
    `screen` varchar(15) NOT NULL,
    `mobile` tinyint NOT NULL DEFAULT '0',
    `referer` varchar(500) NOT NULL,
    `page` varchar(100) NOT NULL,
    `destination` varchar(255) NOT NULL,
    `subid` varchar(100) NOT NULL, 
    `hits` int NOT NULL DEFAULT '0',
    `uniques` int NOT NULL DEFAULT '0',
    `visitors` int NOT NULL DEFAULT '0',
    `clicks` int NOT NULL DEFAULT '0',
    `conversion` int NOT NULL DEFAULT '0',    
    `cost` double(8, 4) NOT NULL default '0.00',
    `revenue` double(8, 4) NOT NULL default '0.00',
    `processing` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `date` (`date`),    
    KEY `idx1` (`project_id`, `user_id`, `date`),
    KEY `idx2` (`processing`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE if exists `stats_summary_hourly`;
CREATE TABLE `stats_summary_hourly` (
    `id` int unsigned not null AUTO_INCREMENT,
    `date` date NOT NULL, 
    `date_time` datetime NOT NULL,
    `user_id` int unsigned NOT NULL,
    `project_id` int unsigned NOT NULL,    
    `campaign_id` int unsigned NOT NULL,
    `source_id` int unsigned NOT NULL,
    `domain_id` int unsigned NOT NULL,
    `offer_id` int unsigned NOT NULL,
    `rule_id` int unsigned NOT NULL,
    `service_id` int unsigned NOT NULL,
    `country` char(4) NOT NULL,
    `hits` int NOT NULL DEFAULT '0',
    `uniques` int NOT NULL DEFAULT '0',
    `visitors` int NOT NULL DEFAULT '0',
    `clicks` int NOT NULL DEFAULT '0',
    `conversion` tinyint NOT NULL DEFAULT '0',
    `cost` double(8, 4) NOT NULL default '0.00',
    `revenue` double(8, 4) NOT NULL default '0.00',
    `processing` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `date` (`date`),
    KEY `date_time` (`date_time`),
    KEY `idx1` (`project_id`, `user_id`, `date`, `date_time`),
    KEY `idx2` (`source_id`, `user_id`, `date`, `date_time`),
    KEY `idx3` (`campaign_id`, `user_id`, `date`, `date_time`),
    KEY `idx4` (`processing`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE if exists `stats_summary_dns`;
CREATE TABLE `stats_summary_dns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `domain_id` int unsigned NOT NULL default '0',
  `type_requests` char(10) NOT NULL default '',
  `total_requests` int NOT NULL default '0',
  `cache_requests` int NOT NULL default '0',
  `buffer_size` int NOT NULL default '0',
  `processing` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `idx1` (`domain_id`, `date`),
  KEY `idx2` (`processing`, `date`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE if exists `stats_powerdns`;
CREATE TABLE `stats_powerdns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL DEFAULT '0000-00-00',
  `visit_time` datetime NOT NULL DEFAULT '000-00-00 00:00:00',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `ipaddress` varchar(255) NOT NULL DEFAULT '',
  `buffer` int(11) NOT NULL DEFAULT '0',
  `cache` varchar(10) NOT NULL DEFAULT '',
  `processing` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visit_date` (`visit_date`),
  KEY `domain` (`domain`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
