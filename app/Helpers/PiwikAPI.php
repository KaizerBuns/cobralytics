<?php namespace App\Helpers;

use App\Helpers\Curl;

class PiwikAPI {

	public static function UsersManager_addUser($login, $password, $email, $alias) {

		$params = array(
			'userLogin' => $login,
			'password' => $password,
			'email' => $email,
			'alias' => $alias,
		);

		$result = self::query_piwik('UsersManager.addUser', $params);
		if (preg_match("/ok/", $result->message)) {
			return true;
		}

		return false;
	}

	public static function UsersManager_updateUser($login, $password, $email, $alias) {
		$params = array(
			'userLogin' => $login,
			'password' => $password,
			'email' => $email,
			'alias' => $alias,
		);

		$result = self::query_piwik('UsersManager.updateUser', $params);
		if (preg_match("/ok/", $result->message)) {
			return true;
		}

		return false;
	}

	public static function VisitsSummary_get($token_auth, $date, Array $site_ids) {
		$params = array(
			'idSite' => implode(",", $site_ids),
			'period' => 'day',
			'date' => $date,
		);

		$result = self::query_piwik('VisitsSummary.get', $params, $token_auth, true);

		if (isset($result['result']) && preg_match("/error/", $result['result'])) {
			echo $result['message'] . "\n";
			return array();
		}

		return $result;
	}

	public static function Metrics_get($token_auth, $date, Array $site_ids) {
		$params = array(
			'idSite' => implode(",", $site_ids),
			'period' => 'day',
			'date' => $date,
		);

		$result = self::query_piwik('API.get', $params, $token_auth, true);

		if (isset($result['result']) && preg_match("/error/", $result['result'])) {
			echo $result['message'] . "\n";
			return array();
		}

		return $result;
	}

	public static function query_piwik($method, $params, $token_auth = '', $return_array = false) {

		if (!$token_auth) {
			$token_auth = env('PIWIK_TOKEN');
		}

		$api_url = env('PIWIK_URL') . "/?module=API&format=JSON&token_auth={$token_auth}&method={$method}&" . urldecode(http_build_query($params));

		echo "Querying Piwik\n";
		echo "Url: {$api_url}\n\n";

		$curl = new Curl();
		$result = $curl->get($api_url, array());
		$result = json_decode($result->body, $return_array);

		return $result;
	}
}

?>