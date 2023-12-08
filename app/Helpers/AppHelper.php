<?php namespace App\Helpers;
use App\Campaign;
use App\Creative;
use App\Helpers\MyHelper;
use App\Offer;
use App\Project;
use App\Rule;
use App\Service;
use App\Source;
use App\User;
use Crate\PDO\PDO as PDO;
use DB;

class AppHelper {
	public static function create_log(Array $data) {
		if (isset($data['datetime'])) {
			$now = strtotime($data['datetime']);
		} else {
			$now = time();
		}

		$detect = new Mobile_Detect();
		$visit_time = date("Y-m-d H:i:00", $now);
		$visit_date = date("Y-m-d", $now);

		if (isset($data['platform']) && isset($data['browser']) && isset($data['version'])) {
			$agent = array(
				'platform' => $data['platform'],
				'browser' => $data['browser'],
				'version' => $data['version'],
			);

			$raw_agent = implode(" ", $agent);
			$mobile = (preg_match("/(android|ios|WindowsPhone|Phone)/i", $raw_agent) ? 1 : 0);
		} else {
			$raw_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$agent = parse_user_agent($raw_agent);
			$mobile = ($detect->isMobile() ? 1 : 0);
		}

		//from google analytics
		if (isset($data['country']) && isset($data['region']) && isset($data['city'])) {
			$country = $data['country'];
			$country_name = $data['country_name'];
			$region = $data['region'];
			$region_name = $data['region_name'];
			$city = $data['city'];
			$geo_location = (isset($data['geo_location']) ? $data['geo_location'] : ''); //google helper GAHelper
		} else {
			$country = ($_SERVER['GEOIP_COUNTRY_CODE'] ? $_SERVER['GEOIP_COUNTRY_CODE'] : '?');
			$country_name = ($_SERVER['GEOIP_COUNTRY_NAME'] ? $_SERVER['GEOIP_COUNTRY_NAME'] : '?');
			$region = ($_SERVER['GEOIP_REGION'] ? $_SERVER['GEOIP_REGION'] : '?');
			$region_name = ($_SERVER['GEOIP_REGION_NAME'] ? $_SERVER['GEOIP_REGION_NAME'] : '?');
			$city = ($_SERVER['GEOIP_CITY'] ? $_SERVER['GEOIP_CITY'] : '?');

			$lat = ($_SERVER['GEOIP_LATITUDE'] ? $_SERVER['GEOIP_LATITUDE'] : 0);
			$long = ($_SERVER['GEOIP_LONGITUDE'] ? $_SERVER['GEOIP_LONGITUDE'] : 0);
			$geo_location = "{$lat},{$long}";
		}

		if (isset($data['referer'])) {
			$referer = $data['referer'];
		} else {
			$referer = (isset($_SERVER['HTTP_REFERER']) ? urldecode($_SERVER['HTTP_REFERER']) : '');
		}

		if (isset($data['ip'])) {
			$ip = $data['ip'];
		} else {
			$ip = MyHelper::get_real_ip();
		}

		$sub1 = (isset($data['sub1']) ? (string) substr($data['sub1'], 0, 255) : "");
		$sub2 = (isset($data['sub2']) ? (string) substr($data['sub2'], 0, 255) : "");
		$sub3 = (isset($data['sub3']) ? (string) substr($data['sub3'], 0, 255) : "");
		$sub4 = (isset($data['sub4']) ? (string) substr($data['sub4'], 0, 255) : "");
		$sub5 = (isset($data['sub5']) ? (string) substr($data['sub5'], 0, 255) : "");
		if (isset($data['visitors'])) {
			$visitors = (int) $data['visitors'];
		} else {
			$visitors = 1;
		}

		$clicks = (isset($data['clicks']) && $data['clicks'] ? $data['clicks'] : 0);
		$revenue = (isset($data['revenue']) && $data['revenue'] ? $data['revenue'] : 0);
		$cost = (isset($data['cost']) && $data['cost'] ? $data['cost'] : 0);
		$conversion = (isset($data['conversion']) && $data['conversion'] ? $data['conversion'] : 0);
		$user_id = (isset($data['user_id']) && $data['user_id'] ? $data['user_id'] : 0);
		$source_id = (isset($data['source_id']) && $data['source_id'] ? $data['source_id'] : 0);
		$campaign_id = (isset($data['campaign_id']) && $data['campaign_id'] ? $data['campaign_id'] : 0);
		$domain_id = (isset($data['domain_id']) && $data['domain_id'] ? $data['domain_id'] : 0);
		$service_id = (isset($data['service_id']) && $data['service_id'] ? $data['service_id'] : 0);
		$offer_id = (isset($data['offer_id']) && $data['offer_id'] ? $data['offer_id'] : 0);
		$linkhash = (isset($data['linkhash']) && $data['linkhash'] ? $data['linkhash'] : "");
		$project_id = (isset($data['project_id']) && $data['project_id'] ? $data['project_id'] : 0);
		$rule_id = (isset($data['rule_id']) && $data['rule_id'] ? $data['rule_id'] : 0);
		$rotator_id = (isset($data['rotator_id']) && $data['rotator_id'] ? $data['rotator_id'] : 0);
		$destination_url = (isset($data['destination']) && $data['destination'] ? $data['destination'] : "");
		$querystring = (isset($_SERVER['QUERY_STRING']) ? urldecode($_SERVER['QUERY_STRING']) : '');

		if (isset($data['unique'])) {
			$unique = $data['unique'];
		} else {
			$unique = $ip . $raw_agent;
		}

		$duration = (isset($data['duration']) && $data['duration'] ? $data['duration'] : 0);
		$page = (isset($data['page']) && $data['page'] ? urldecode($data['page']) : $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$click_id = (isset($data['click_id']) && $data['click_id'] ? $data['click_id'] : MyHelper::generate_numAlpha(16));
		$screen = (isset($data['screen']) && $data['screen'] ? $data['screen'] : '');

		//clean up long variables
		$referer = ($referer ? (string) substr($referer, 0, 255) : "");
		$page = ($page ? (string) substr($page, 0, 255) : "");
		$destination_url = ($destination_url ? (string) substr($destination_url, 0, 255) : "");

		if (isset($_REQUEST['test']) && $_REQUEST['test']) {
			$hour = rand(0, 23);
			$visit_date = (isset($_REQUEST['test_date']) && $_REQUEST['test_date'] ? $_REQUEST['test_date'] : date("Y-m-d"));
			$visit_time = date("Y-m-d {$hour}:i:00", strtotime($visit_date));
		}

		/*
			Organic Search
			Referral
			Direct
			Social
			Campaign
		*/

		if ($campaign_id > 0) {
			$traffic_type = 'Campaign';
		} else if (empty($referer) || preg_match("/" . $_SERVER['HTTP_HOST'] . "/", $referer)) {
			$traffic_type = 'Direct';
		} else if (preg_match("/(google.|bing.|yahoo.|ask.fm|aol|webcrawler.|duckduckgo.)/", $referer)) {
			$traffic_type = 'Search Organic';
		} else if (preg_match("/(facebook.|twitter.|tumblr.|myspace.|pinterest.)/", $referer)) {
			$traffic_type = 'Social';
		} else {
			$traffic_type = 'Referral';
		}

		//replace cobrasub with clickID to send
		$replacements = array(
			'{CLICKID}' => $click_id,
			'{SUB1}' => $sub1,
			'{SUB2}' => $sub2,
			'{SUB3}' => $sub3,
			'{SUB4}' => $sub4,
			'{SUB5}' => $sub5,
			'{COUNTRY}' => $country,
			'{IP}' => $ip,
			'{REF}' => $referer,
			'{AGENT}' => $agent['browser'],
		);

		foreach ($replacements as $search => $replace) {
			if ($replace) {
				$destination_url = str_replace($search, $replace, $destination_url);
			}
		}

		if (isset($_COOKIE['_cbkeyid_']) && $_COOKIE['_cbkeyid_']) {
			list($id, $visitstart_time, $requesttime) = explode(".", $_COOKIE['_cbkeyid_']);
			$visitstart_time = date("Y-m-d H:i:s", $visitstart_time);
			//if the user is still in the current session - this adds the first time he came to visit.

			if (isset($data['update_click_id']) && $data['update_click_id']) {
				$requesttime = round($requesttime / 1000);
				$now = time();
				$duration = round($now - $requesttime);
				$sql = "UPDATE stats_actions SET clicks = 1, duration = '{$duration}' WHERE click_id = '{$id}' LIMIT 1";
				DB::update($sql);
				$clicks = 0;
				$duration = 0;
			}
		}

		if (isset($_COOKIE['_cbuniqueid_']) && $_COOKIE['_cbuniqueid_']) {
			$visitor_id = $_COOKIE['_cbuniqueid_'];
		} else {
			$visitor_id = md5($unique);
		}

		$log = array();
		$log['info'] = array(
			'visitor_id' => $visitor_id,
			'visit_date' => $visit_date,
			'visit_time' => $visit_time,
			'country' => $country,
			'country_name' => $country_name,
			'region' => $region,
			'region_name' => $region_name,
			'city' => $city,
			'latitude' => $lat,
			'longitude' => $long,
			'screen' => $screen,
			'ip_address' => $ip,
			'platform' => (trim($agent['platform']) ? $agent['platform'] : 'unknown'),
			'browser' => (trim($agent['browser']) ? $agent['browser'] : 'unknown'),
			'version' => (trim($agent['version']) ? $agent['version'] : 'unknown'),
			'mobile' => $mobile,
			'last_visit' => $visit_time,
		);

		$log['actions'] = array(
			'click_id' => $click_id,
			'visit_date' => $visit_date,
			'visit_time' => $visit_time,
			'visitor_id' => $visitor_id,
			'country' => $country,
			'country_name' => $country_name,
			'region' => $region,
			'region_name' => $region_name,
			'city' => $city,
			'latitude' => $lat,
			'longitude' => $long,
			'screen' => $screen,
			'ip_address' => $ip,
			'platform' => (trim($agent['platform']) ? $agent['platform'] : 'unknown'),
			'browser' => (trim($agent['browser']) ? $agent['browser'] : 'unknown'),
			'version' => (trim($agent['version']) ? $agent['version'] : 'unknown'),
			'mobile' => $mobile,
			'user_id' => $user_id,
			'campaign_id' => $campaign_id,
			'project_id' => $project_id,
			'source_id' => $source_id,
			'domain_id' => $domain_id,
			'service_id' => $service_id,
			'offer_id' => $offer_id,
			'linkhash' => $linkhash,
			'rule_id' => $rule_id,
			'rotator_id' => $rotator_id,
			'sub1' => $sub1,
			'sub2' => $sub2,
			'sub3' => $sub3,
			'sub4' => $sub4,
			'sub5' => $sub5,
			'page' => $page,
			'query' => $querystring,
			'referer' => $referer,
			'destination' => $destination_url,
			'traffic_type' => $traffic_type,
			'visits' => $visitors,
			'clicks' => $clicks,
			'click_time' => $visit_time,
		);

		//add details
		$log['actions'] = self::get_details($log['actions']);

		//log action details
		self::log_action($log);

		//return log
		$log = array_merge($log['info'], $log['actions']);
		return $log;
	}

	public static function get_details($log) {
		$log['user_name'] = '';
		if ($log['user_id']) {
			$log['user_name'] = User::find($log['user_id'])->email;
		}

		$log['campaign_name'] = '';
		if ($log['campaign_id']) {
			$log['campaign_name'] = Campaign::find($log['campaign_id'])->name;
		}

		$log['project_name'] = '';
		if ($log['project_id']) {
			$log['project_name'] = Project::find($log['project_id'])->name;
		}

		$log['source_name'] = '';
		if ($log['source_id']) {
			$log['source_name'] = Source::find($log['source_id'])->name;
		}

		$log['domain_name'] = '';
		if ($log['domain_id']) {
			$log['domain_name'] = Source::find($log['domain_id'])->name;
		}

		$log['service_name'] = '';
		if ($log['service_id']) {
			$log['service_name'] = Service::find($log['service_id'])->name;
		}

		$log['offer_name'] = '';
		if ($log['offer_id']) {
			$log['offer_name'] = Offer::find($log['offer_id'])->name;
		}

		$log['rule_name'] = '';
		$log['rule_type'] = '';
		if ($log['rule_id']) {
			$rule = Rule::find($log['rule_id']);
			$log['rule_name'] = $rule->name;
			$log['rule_type'] = $rule->type;
		}

		$log['rotator_name'] = '';
		$log['rotator_type'] = '';

		if ($log['rotator_id']) {
			$rotator = Rule::find($log['rotator_id']);
			if ($rotator->type == 'creative') {
				$creative = Creative::find($rotator->creative_id);
				$log['rotator_id'] = $creative->id;
				$log['rotator_name'] = $creative->name;
			} else {
				$rule = Rule::find($rotator->id);
				$log['rotator_id'] = $rule->id;
				$log['rotator_name'] = $rule->name;
			}
			$log['rotator_type'] = $rotator->type;
		}

		return $log;
	}

	public static function log_click($click_id, $log) {
		//log MySQL
		$now = date("Y-m-d H:i:00");
		if (env('LOG_MYSQL')) {
			$sql = "UPDATE stats_visitor_actions
			SET clicks = clicks + 1, click_time = '{$now}'
			WHERE click_id = '{$click_id}'";

			$result = DB::statement($sql);
		}

		//crate DB
		if (env('LOG_CRATEDB')) {
			$dsn = 'crate:67.205.133.220:4200';
			$pdo = new PDO($dsn, null, null, null);

			$now = date("c", strtotime($now));
			$sql = "UPDATE stats_visitor_actions
			SET clicks = clicks + 1, click_time = '{$now}'
			WHERE click_id = '{$click_id}'";

			$query = $pdo->prepare($sql);
			$results = $query->execute();
		}
	}

	public static function log_conversion($click_id, $revenue, $payout = 0) {

		//log MySQL
		$now = date("Y-m-d H:i:00");
		if (env('LOG_MYSQL')) {
			$sql = "UPDATE stats_visitor_actions
			SET conversions = 1, conversion_time = '{$now}', revenue = {$revenue}, payout = {$payout}
			WHERE click_id = '{$click_id}'";
			$result = DB::statement($sql);
		}

		//crate DB
		if (env('LOG_CRATEDB')) {
			$dsn = 'crate:67.205.133.220:4200';
			$pdo = new PDO($dsn, null, null, null);

			$now = date("c", strtotime($now));
			$sql = "UPDATE stats_visitor_actions
			SET conversions = 1, conversion_time = '{$now}', revenue = {$revenue}, payout = {$payout}
			WHERE click_id = '{$click_id}'";

			$query = $pdo->prepare($sql);
			$results = $query->execute();
		}
	}

	public static function log_action($log) {
		//log MySQL
		if (env('LOG_MYSQL')) {
			if (isset($log['info'])) {
				$columns = array_keys($log['info']);
				$values = array_values($log['info']);

				$prepare_values = array();
				foreach ($values as $v) {
					$prepare_values[] = '?';
				}

				$sql = "INSERT INTO stats_visitor_info (" . implode(",", $columns) . ") VALUES (" . implode(",", $prepare_values) . ") ";
				$sql .= "ON DUPLICATE KEY UPDATE last_visit = '{$log['info']['last_visit']}'";
				$result = DB::insert($sql, $values);
			}

			if (isset($log['actions'])) {
				$columns = array_keys($log['actions']);
				$values = array_values($log['actions']);

				$prepare_values = array();
				foreach ($values as $v) {
					$prepare_values[] = '?';
				}

				$sql = "INSERT INTO stats_visitor_actions (" . implode(",", $columns) . ") VALUES ";
				$sql .= "(" . implode(",", $prepare_values) . ")";

				$result = DB::insert($sql, $values);
			}
		}

		//crate DB
		if (env('LOG_CRATEDB')) {
			$dsn = 'crate:67.205.133.220:4200';
			$pdo = new PDO($dsn, null, null, null);
			if (isset($log['info'])) {
				$log['info'] = self::format_log2cratedb($log['info']);
				$columns = array_keys($log['info']);
				$values = array_values($log['info']);

				$sql = "INSERT INTO stats_visitor_info (" . implode(",", $columns) . ") VALUES ";
				$sql .= "(" . implode(",", $values) . ") ON DUPLICATE KEY UPDATE last_visit = '{$log['info']['last_visit']}'";

				$query = $pdo->prepare($sql);
				$results = $query->execute();
			}

			if (isset($log['actions'])) {
				$log['actions'] = self::format_log2cratedb($log['actions']);
				$columns = array_keys($log['actions']);
				$values = array_values($log['actions']);

				$sql = "INSERT INTO stats_visitor_actions (" . implode(",", $columns) . ") VALUES ";
				$sql .= "(" . implode(",", $values) . ")";

				$query = $pdo->prepare($sql);
				$results = $query->execute();
			}
		}

		//always log to backup - actions
		error_log(json_encode($log) . "\n", 3, env('APP_LOGS') . "/redirect-visitors.log");
		return;
	}

	public static function format_log2cratedb($log) {
		$logfile = array();
		foreach ($log as $key => $value) {
			if ($key == 'last_visit') {
				$logfile[$key] = "'" . date("c", strtotime($value)) . "'";
			} elseif (preg_match('/_time/', $key)) {
				$logfile[$key] = "'" . date("c", strtotime($value)) . "'";
			} else {
				if (!is_numeric($value)) {
					$logfile[$key] = "'" . $value . "'";
				} elseif ($value) {
					$logfile[$key] = $value;
				} else {
					$logfile[$key] = 0;
				}
			}
		}

		return $logfile;
	}
}
?>