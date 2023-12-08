/* Old table for TESTING DO NOT USE*/
DROP TABLE if exists `stats_local`;
CREATE TABLE `stats_local` (
  `clickid` String,
  `unique_key` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `user_id` UInt32,
  `project_id` UInt32,
  `campaign_id` UInt32,
  `rule_id` UInt32,
  `rotator_id` UInt32,
  `service_id` UInt32,
  `source_id` UInt32,
  `domain_id` UInt32,
  `offer_id` UInt32,
  `linkhash` String,
  `ip` String,
  `traffic_type` String,
  `country` String,
  `region` String,
  `lat` string,
  `long` string,
  `city` String,
  `platform` String,
  `browser` String,
  `version` String,
  `mobile` UInt8,
  `screen` String,
  `referer` String,
  `subid1` String,
  `subid2` String,
  `subid3` String,
  `subid4` String,
  `subid5` String,
  `destination` String,
  `page` String,
  `query` String,
  `duration` UInt32,
  `visit` UInt8,
  `click` UInt8,
  `revenue` Float32,
  `cost` Float32,
  `conversion` UInt8,
  `conversion_time` DateTime 
) ENGINE = MergeTree(visitdate, visitdate, 8192);

DROP TABLE if exists stats_all;
CREATE TABLE stats_all AS stats_local ENGINE = Distributed(cobrastats_3shards_1replicas, default, stats_local, rand());

DROP TABLE if exists `stats_powerdns_local`;
CREATE TABLE `stats_powerdns_local` (
  `visit_date` Date,
  `visit_time` DateTime,
  `domain` String,
  `type` String,
  `ipaddress` String,
  `buffer` UInt32,
  `cache` String 
) ENGINE = MergeTree(visitdate, visitdate, 8192);

DROP TABLE if exists stats_powerdns_all;
CREATE TABLE stats_powerdns_all AS stats_powerdns_local ENGINE = Distributed(cobrastats_3shards_1replicas, default, stats_powerdns_local, rand());

/* Production replication table --------------------------------------------------------------------------*/
DROP TABLE if exists `stats_overall`;
CREATE TABLE `stats_overall` (
  `clickid` String,
  `unique_key` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `user_id` UInt32,
  `user_name` String,
  `project_id` UInt32,
  `project_name` String,
  `campaign_id` UInt32,
  `campaign_name` String,
  `rule_id` UInt32,
  `rule_type` String,
  `rule_name` String,
  `rotator_id` UInt32,
  `rotator_type` String,
  `rotator_name` String,
  `service_id` UInt32,
  `service_name` String,
  `source_id` UInt32,
  `source_name` String,
  `domain_id` UInt32,
  `domain_name` String,
  `offer_id` UInt32,
  `offer_name` String,
  `linkhash` String,
  `ip` String,
  `traffic_type` String,
  `country` String,
  `country_name` String,
  `region` String,
  `region_name` String,
  `lat` String,
  `long` String,
  `city` String,
  `platform` String,
  `browser` String,
  `version` String,
  `mobile` UInt8,
  `screen` String,
  `referer` String,
  `sub1` String,
  `sub2` String,
  `sub3` String,
  `sub4` String,
  `sub5` String,
  `destination` String,
  `page` String,
  `query` String,
  `duration` UInt32,
  `visitors` UInt8,
  `clicks` UInt8,
  `revenue` Float32,
  `cost` Float32,
  `leads` UInt8,
  `lead_time` DateTime, 
  `conversions` UInt8,
  `conversion_time` DateTime 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_overall',
'{replica}',
visitdate,
visitdate,
8192);

DROP TABLE if exists `stats_visitors`;
CREATE TABLE `stats_visitors` (
  `clickid` String,
  `unique_key` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `user_id` UInt32,
  `user_name` String,
  `project_id` UInt32,
  `project_name` String,
  `campaign_id` UInt32,
  `campaign_name` String,
  `rule_id` UInt32,
  `rule_type` String,
  `rule_name` String,
  `rotator_id` UInt32,
  `rotator_type` String,
  `rotator_name` String,
  `service_id` UInt32,
  `service_name` String,
  `source_id` UInt32,
  `source_name` String,
  `domain_id` UInt32,
  `domain_name` String,
  `offer_id` UInt32,
  `offer_name` String,
  `linkhash` String,
  `ip` String,
  `traffic_type` String,
  `country` String,
  `country_name` String,
  `region` String,
  `region_name` String,
  `latitude` String,
  `longitude` String,
  `city` String,
  `platform` String,
  `browser` String,
  `version` String,
  `mobile` UInt8,
  `screen` String,
  `referer` String,
  `sub1` String,
  `sub2` String,
  `sub3` String,
  `sub4` String,
  `sub5` String,
  `destination` String,
  `page` String,
  `query` String,
  `duration` UInt32,
  `visitors` UInt8 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_visitors',
'{replica}',
visit_date,
visit_date,
8192);

DROP TABLE if exists `stats_actions`;
CREATE TABLE `stats_actions` (
  `clickid` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `clicks` UInt8,
  `click_time` DateTime,
  `revenue` Float32,
  `payout` Float32,
  `cost` Float32,
  `leads` UInt8,
  `lead_time` DateTime, 
  `conversions` UInt8,
  `conversion_time` DateTime 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_actions',
'{replica}',
visit_date,
visit_date,
8192);


DROP TABLE if exists `stats_powerdns`;
CREATE TABLE `stats_powerdns` (
`visit_date` Date,
`visit_time` String,
`domain` String,
`type` String,
`ipaddress` String,
`buffer` UInt32,
`cache` String 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_powerdns',
'{replica}',
visit_date,
visit_date,
8192);

/** DEV------------------------------------------------------------------------*/
DROP TABLE if exists `stats_visitors_dev`;
CREATE TABLE `stats_visitors_dev` (
  `clickid` String,
  `unique_key` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `user_id` UInt32,
  `user_name` String,
  `project_id` UInt32,
  `project_name` String,
  `campaign_id` UInt32,
  `campaign_name` String,
  `rule_id` UInt32,
  `rule_type` String,
  `rule_name` String,
  `rotator_id` UInt32,
  `rotator_type` String,
  `rotator_name` String,
  `service_id` UInt32,
  `service_name` String,
  `source_id` UInt32,
  `source_name` String,
  `domain_id` UInt32,
  `domain_name` String,
  `offer_id` UInt32,
  `offer_name` String,
  `linkhash` String,
  `ip` String,
  `traffic_type` String,
  `country` String,
  `country_name` String,
  `region` String,
  `region_name` String,
  `latitude` String,
  `longitude` String,
  `city` String,
  `platform` String,
  `browser` String,
  `version` String,
  `mobile` UInt8,
  `screen` String,
  `referer` String,
  `sub1` String,
  `sub2` String,
  `sub3` String,
  `sub4` String,
  `sub5` String,
  `destination` String,
  `page` String,
  `query` String,
  `duration` UInt32,
  `visitors` UInt8 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_visitors_dev',
'{replica}',
visit_date,
visit_date,
8192);

DROP TABLE if exists `stats_actions_dev`;
CREATE TABLE `stats_actions_dev` (
  `clickid` String,
  `visit_date` Date,
  `visit_time` DateTime,
  `clicks` UInt8,
  `click_time` DateTime,
  `revenue` Float32,
  `payout` Float32,
  `cost` Float32,
  `leads` UInt8,
  `lead_time` DateTime, 
  `conversions` UInt8,
  `conversion_time` DateTime 
) ENGINE = ReplicatedMergeTree(
'/clickhouse/tables/{shard}/stats_actions_dev',
'{replica}',
visit_date,
visit_date,
8192);



/** Summary Tables ------------------------------------------------------------*/

DROP TABLE if exists `stats_summary`;
CREATE TABLE IF NOT EXISTS stats_summary (
  visitdate Date,
  source_id UInt32,
  user_id UInt32,
  visitors UInt32,
  clicks UInt32,
  revenue Float32 
) ENGINE = SummingMergeTree(visitdate, (source_id, user_id, visitdate), 8192)
