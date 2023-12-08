drop database if exists `cobracmd`;
create database `cobracmd`;
use cobracmd;

/**
run scripts/db_fix.php to add created_at/updated_ad
**/

CREATE TABLE `users` ( 
    `id` int unsigned not null AUTO_INCREMENT,
    `twitter_id` varchar(30) not null default '0',
    `facebook_id` varchar(30) not null default '0',
    `gplus_id` varchar(30) not null default '0',
    `email` varchar(50) not null,
    `password` varchar(60) not null,
    `remember_token` varchar(100) default null,
    `name` varchar(255) not null default '',    
    `address` varchar(75) not null default '',
    `address2` varchar(75) not null default '',
    `city` varchar(75) not null default '',
    `state` varchar(10) not null default '', 
    `country` varchar(10) not null default '', 
    `zip` varchar(10) not null default '', 
    `is_admin` tinyint(1) not null default '0',
    `default_project_id` int not null default '0',
    `status` enum('active', 'pending', 'disabled') not null default 'pending',
    `pref_show_welcome` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `pref_alerts` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `pref_page_limit` int unsigned NOT NULL DEFAULT 0,
    `pref_quick_menu` tinyint unsigned NOT NULL DEFAULT 0,
    `pref_all_rule` varchar(255) NOT NULL default '',
    `enable_campaigns` tinyint(1) not null default 1,
    `enable_offers` tinyint(1) not null default 1,
    `enable_monitors` tinyint(1) not null default 1,
    `enable_reports` tinyint(1) not null default 1,
    `enable_analytics` tinyint(1) not null default 1,
    `piwik_login` varchar(50) NOT NULL default 0,
    `piwik_auth_token` varchar(50) NOT NULL default 0,
    `user_parent_id` int unsigned not null default 0,
    `user_type` enum('master', 'admin', 'publisher', 'advertiser') not null default 'admin',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    `last_login` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `status` (`status`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `projects` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL,
    `name`  varchar(50) not null default '',
    `description`  varchar(100) not null default '',
    `is_default` tinyint unsigned not null default '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`user_id`, `name`),
    KEY `user_id` (`user_id`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `services` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL,
    `name`  varchar(50) not null default '',
    `is_default` tinyint unsigned not null default '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`user_id`, `name`),
    KEY `user_id` (`user_id`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `sources` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL,
    `project_id` int unsigned NOT NULL default 0, 
    `service_id` int unsigned NOT NULL default 0,
    `parent_id` int unsigned NOT NULL DEFAULT 0,
    `piwik_idsite` int unsigned NOT NULL DEFAULT 0,
    `name` varchar(100) NOT NULL,
    `type` enum('domain', 'traffic') NOT NULL default 'domain',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    `active` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`, `user_id`, `type`),
    KEY `user_id` (`user_id`),
    KEY `piwik_idsite` (`piwik_idsite`),
    KEY `parent_id` (`parent_id`),
    KEY `service_id` (`service_id`),
    KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

CREATE TABLE `geo_countries` (
    `id` int unsigned not null AUTO_INCREMENT,
    `geoname_id` int not null default '0',
    `continent_code` varchar(50) not null default '',
    `continent_name` varchar(50) not null default '',
    `country_iso_code` varchar(50) not null default '',
    `country_name` varchar(50) not null default '',
    `subdivision_iso_code` varchar(50) not null default '',
    `subdivision_name` varchar(50) not null default '',
    `city_name` varchar(50) not null default '',
    `metro_code` varchar(50) not null default '',
    `time_zone` varchar(50) not null default '',
    `sort` int(1) not null default 0,
    PRIMARY KEY (`id`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `geo_cities` (
    `id` int unsigned not null AUTO_INCREMENT,
    `geoname_id` int not null default '0',
    `continent_code` varchar(50) not null default '',
    `continent_name` varchar(50) not null default '',
    `country_iso_code` varchar(50) not null default '',
    `country_name` varchar(50) not null default '',
    `subdivision_iso_code` varchar(50) not null default '',
    `subdivision_name` varchar(50) not null default '',
    `city_name` varchar(100) not null default '',
    `metro_code` varchar(50) not null default '',
    `time_zone` varchar(50) not null default '',
    `sort` int(1) not null default 0,
    PRIMARY KEY (`id`),
    KEY `country_iso_code` (`country_iso_code`),
    KEY `subdivision_iso_code` (`subdivision_iso_code`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `campaigns` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `project_id` int unsigned NOT NULL default 0,
    `source_id` int unsigned NOT NULL default 0,
    `service_id` int unsigned NOT NULL default 0,
    `linkhash` varchar(7) NOT NULL,
    `name`  varchar(50) NOT NULL,
    `medium` varchar(50) NOT NULL default '',
    `tracking_url_append` varchar(255) NOT NULL default '',
    `content` enum('Redirect','Banner','Template') NOT NULL default 'Redirect',
    `type` enum('CPC','CPA','CPM') NOT NULL default 'CPC',
    `cost` double(8,4) NOT NULL default 0,
    `payout` double(8,4) NOT NULL default 0,
    `revenue` double(8,4) NOT NULL default 0,
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY (`linkhash`),
    KEY `user_id` (`user_id`),
    KEY `project_id` (`project_id`),
    KEY `source_id` (`source_id`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `campaign_domains` (
    `id` int unsigned not null AUTO_INCREMENT,
    `campaign_id` int unsigned NOT NULL default 0,
    `source_id` int unsigned NOT NULL default 0,
    `linkhash` varchar(7) NOT NULL,
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `campaign_id` (`campaign_id`),
    KEY `source_id` (`source_id`),
    KEY `active` (`active`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `pixels` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `name` varchar(50) NOT NULL,
    `type` enum('image','javascript','iframe', 's2s') default 'image',
    `pixel` text,
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `scope` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),   
    KEY `user_id` (`user_id`),
    KEY `active` (`active`),
    KEY `scope` (`scope`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `campaign_pixels` (
    `id` int unsigned not null AUTO_INCREMENT,
    `campaign_id` int unsigned NOT NULL default 0,
    `pixel_id` int unsigned NOT NULL default 0,
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),   
    KEY `campaign_id` (`campaign_id`),
    KEY `pixel_id` (`pixel_id`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `campaign_creatives` (
    `id` int unsigned not null AUTO_INCREMENT,
    `campaign_id` int unsigned NOT NULL default 0,
    `creative_id` int unsigned NOT NULL default 0,
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),   
    KEY `campaign_id` (`campaign_id`),
    KEY `creative_id` (`creative_id`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `advertisers` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `name`  varchar(50) NOT NULL,
    `internal` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `creatives` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `name`  varchar(100) NOT NULL,
    `thumb` varchar(100) NOT NULL,
    `storage` enum('local', 's3') NOT NULL,
    `file_url` varchar(100) NOT NULL,
    `file_bucket` varchar(25) NOT NULL,
    `file_folder` varchar(25) NOT NULL,
    `file_size` int unsigned NOT NULL default 0,
    `file_type` varchar(25) NOT NULL,
    `file_width` int unsigned NOT NULL default 0,
    `file_height` int unsigned NOT NULL default 0,
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB default CHARSET=utf8;

/*
Vertical    
Niche   
Language    
Geo Target  
Audience Demo Target 
Gender Demo Target 
Age Demo Target 
Ethnicity   
Network Brand   
Landing Page    
Device  
OS  
URL 
Payout 1    
Payout 2

*/

CREATE TABLE `offers` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `advertiser_id` int unsigned NOT NULL default 0,
    `vertical_id` int unsigned NOT NULL default 0,
    `xoffer_id` varchar(10) NOT NULL default '',
    `name`  varchar(100) NOT NULL,
    `url` varchar(150) NOT NULL DEFAULT '',
    `group_name` varchar(50) NOT NULL DEFAULT '',
    `revenue` double(8,4) NOT NULL default '0.00',
    `internal` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `advertiser_id` (`advertiser_id`),
    KEY `vertical_id` (`vertical_id`) 
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `verticals` (
    `id` int unsigned not null AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL default 0,
    `name`  varchar(50) NOT NULL,
    `internal` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB default CHARSET=utf8;

CREATE TABLE `rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `user_id` int unsigned NOT NULL DEFAULT 0,
  `rule_type` enum('service', 'source', 'campaign', 'offer') NOT NULL DEFAULT 'source',
  `rule_type_id` int unsigned NOT NULL DEFAULT '0',
  `country` char(4) NOT NULL DEFAULT '?',
  `region` char(4) NOT NULL DEFAULT '?',
  `city` varchar(50) NOT NULL DEFAULT '?',
  `agent` varchar(50) NOT NULL DEFAULT '?',
  `weight` int unsigned NOT NULL DEFAULT '0',
  `type` enum('redirect','landingpage','ip','html','sale','offer','campaign', 'service', 'creative', 'banner') NOT NULL DEFAULT 'redirect',
  `url` varchar(150) NOT NULL DEFAULT '',
  `secure` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `framed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `path_forwarding` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `qstring_forwarding` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hide_referrer` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `skip_tracking_url_append` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `meta_desc` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `sale_id` int unsigned NOT NULL DEFAULT '0',
  `html_id` int unsigned NOT NULL DEFAULT '0',
  `offer_id` int unsigned NOT NULL DEFAULT '0',
  `campaign_id` int unsigned NOT NULL DEFAULT '0',
  `service_id` int unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime not null default '0000-00-00 00:00:00',
  `updated_at` datetime not null default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `rule_type` (`rule_type`),
  KEY `rule_type_id` (`rule_type_id`),
  KEY `offer_id` (`offer_id`),
  KEY `active` (`active`),
  KEY `idx_1` (`rule_type`,`rule_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `email_forwarding` (
    `id` int unsigned NOT NULL auto_increment,
    `source_id` int unsigned NOT NULL default '0',
    `mbox` varchar(255) NOT NULL,
    `fwd_destination` varchar(255) NOT NULL,
    `domain` varchar(255) default NULL,
    PRIMARY KEY (`id`),
    key `source_id` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `geo_locations` (
    `id` int unsigned NOT NULL auto_increment,
    `country_code` varchar(20) NOT NULL,
    `country_name` varchar(255) NOT NULL,
    `region_code` varchar(20) NOT NULL,
    `region_name` varchar(255) NOT NULL,
    `city_name` varchar(255) NOT NULL,
    `lat` decimal(10, 4) NOT NULL,
    `long` decimal(10, 4) NOT NULL,
    PRIMARY KEY (`id`),
    key `lat` (`lat`),
    key `long` (`long`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `monitor_list` (
    `id` int unsigned NOT NULL auto_increment,
    `user_id` int unsigned NOT NULL default '0',
    `source_id` int unsigned NOT NULL default '0',
    `url` varchar(255) not null default '',
    `status` enum('ok', 'flagged', 'unknown') not null default 'ok',
    `alert` enum('email', 'sms', 'email_sms') not null default 'email',
    `email` varchar(255) not null default '',
    `sms` varchar(255) not null default '',
    `created_at` datetime not null default '0000-00-00 00:00:00',
    `updated_at` datetime not null default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    key `source_id` (`source_id`),
    key `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
