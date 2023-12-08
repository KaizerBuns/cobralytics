<?
chdir(dirname(__FILE__));
require_once('../config/config.php');
require_once(ROOT_PATH.'/config/includes.php');
require_once(ROOT_PATH.'/system/utils/functions.php');

$tables = array(
	'users',
	'projects',
	'services',
	'sources',
	'campaigns',
	'campaign_domains',
	'pixels',
	'campaign_pixels',
	'advertisers',
	'offers',
	'verticals',
	'rules'
);

foreach($tables as $table) {
	$sql = "ALTER TABLE {$table} DROP `created_date`";
	DbRecord::query($sql, array());

	$sql = "ALTER TABLE {$table} DROP `updated_date`";
	DbRecord::query($sql, array());

	$sql = "ALTER TABLE {$table} CHANGE `created_time` `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"; 
	DbRecord::query($sql, array());

	$sql = "ALTER TABLE {$table} CHANGE `updated_time` `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
	DbRecord::query($sql, array());
}
?>