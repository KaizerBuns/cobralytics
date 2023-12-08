#April 28, 2015

ALTER TABLE records ADD INDEX `name` (`name`);

ALTER TABLE stats_overall ADD `rotator_id` int(10) unsigned NOT NULL AFTER `rule_id`;

ALTER TABLE rules ADD `rule_key` varchar(25) NOT NULL default '' AFTER rule_type_id;
ALTER TABLE rules ADD `rotator` tinyint(1) unsigned NOT NULL default '0' AFTER url;


ALTER TABLE stats_summary_manage ADD `service_id` int unsigned NOT NULL AFTER `rule_id`;
ALTER TABLE stats_summary_hourly ADD `service_id` int unsigned NOT NULL AFTER `rule_id`;

#Nov 2, 2016
alter table users ADD `user_parent_id` int unsigned not null default 0 after `piwik_auth_token`;
alter table users ADD `user_type` enum('master', 'admin', 'publisher', 'advertiser') not null default 'publisher' after `piwik_auth_token`;
alter table users ADD `publisher_signup_url` varchar(100) not null default '' after `piwik_auth_token`;

CREATE TABLE `dnswings_zone_domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `tld` varchar(255) NOT NULL DEFAULT '',
  `type` enum('domain','ns') NOT NULL DEFAULT 'domain',
  `ns_root` varchar(255) NOT NULL DEFAULT '',
  `ns_servers` varchar(255) NOT NULL DEFAULT '',
  `updated_on` date DEFAULT null,
  `created_on` date DEFAULT null,
  `expires_on` date DEFAULT null,
  `registrar` varchar(255) NOT NULL DEFAULT '',
  `whois_raw` json DEFAULT NULL,
  `whois_processed` tinyint(4) NOT NULL DEFAULT '0',
  `whois_last_update` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`),
  KEY `key_1` (`type`, `expires_on`),
  KEY `key_2` (`whois_processed`),
  KEY `key_3` (`whois_last_update`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#May 30, 2019
ALTER TABLE campaigns ADD `tracking_url_append` varchar(255) NOT NULL DEFAULT '' AFTER `linkhash`;
ALTER TABLE rules ADD `skip_tracking_url_append` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `hide_referrer`;
