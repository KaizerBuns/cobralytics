<?
chdir(dirname(__FILE__));
require_once('../config/config.php');
require_once(ROOT_PATH.'/config/includes.php');
require_once(ROOT_PATH.'/system/utils/functions.php');


$sql = "SELECT * FROM records where type = 'A' and content IN ('".COBRA_IP1."', '".COBRA_IP2."')";
$results = DbRecord::query($sql, array('connection' => 'default'));

$cobra_ips = array(
	COBRA_IP1
);

foreach($results as $result) {
	$setting = array();

	if($source = Source::find(array('id' => $result['domain_id']))) {
		foreach($cobra_ips as $cobra_ip) {
			$setting[] = array('domain_id' => $source->id, 'name' => $source->name, 'type' => 'A', 'content' => $cobra_ip, 'ttl' => 300);
			$setting[] = array('domain_id' => $source->id, 'name' => '*.'.$source->name, 'type' => 'A', 'content' => $cobra_ip, 'ttl' => 300);
		}

		$sql = "DELETE FROM records where domain_id = '{$result['domain_id']}' AND type = 'A' and content IN ('".COBRA_IP1."', '".COBRA_IP2."')";
		$results = DbRecord::query($sql, array('connection' => 'default'));

		foreach($setting as $d) {
			$r = new DNSRecord();
			$r->populate($d);
			$r->prio = 0;
			$r->rule_id = 0;
			$r->internal = 1;
			$r->save();
		}
	}
}
?>