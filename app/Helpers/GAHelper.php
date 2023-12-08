<?php
namespace App\Helpers;

use App\GeoLocation;
use App\Helpers\MyHelper;
use Carbon\Carbon;
use Exception;
use LaravelAnalytics;

class GAHelper {
	public function __construct($site_name) {
		$this->site_name = str_replace(array("http://www.", "http://"), "", $site_name);
		$this->site_id = null;
		$this->start = '';
		$this->end = '';
	}

	public function getSiteID() {

		try {
			$this->site_id = LaravelAnalytics::getSiteIdByUrl('http://www.' . $this->site_name);
		} catch (Exception $e) {
			try {
				$this->site_id = LaravelAnalytics::getSiteIdByUrl('http://' . $this->site_name);
			} catch (Exception $e) {
				echo $e->getMessage() . "\n";
			}
		}

		if ($this->site_id) {
			return true;
		}

		return false;
	}

	public function getVisitors() {
		if (!$this->getSiteID()) {
			return array();
		}

		$analyticsData = LaravelAnalytics::setSiteId($this->site_id)->getVisitorsAndPageViews(7);
		$stats = array();
		foreach ($analyticsData as $ad) {
			$date = $ad['date']->format("Y-m-d");
			unset($ad['date']);
			$stats[$date] = $ad;
		}

		return $stats;
	}

	public function getKeywords($total = 30) {
		if (!$this->getSiteID()) {
			return array();
		}

		$stats = LaravelAnalytics::setSiteId($this->site_id)->getTopKeywords(7, $total);
		return $stats->toArray();
	}

	public function getTopReferrers($total = 30) {
		if (!$this->getSiteID()) {
			return array();
		}

		$stats = LaravelAnalytics::setSiteId($this->site_id)->getTopReferrers(7, $total);
		return $stats->toArray();
	}

	public function getTopBrowsers($total = 5) {
		if (!$this->getSiteID()) {
			return array();
		}

		$stats = LaravelAnalytics::setSiteId($this->site_id)->getTopBrowsers(7, $total);
		return $stats->toArray();
	}

	public function getTopPages($total = 20) {
		if (!$this->getSiteID()) {
			return array();
		}

		$stats = LaravelAnalytics::setSiteId($this->site_id)->getMostVisitedPages(7, $total);
		return $stats->toArray();
	}

	public function getActiveUsers() {
		if (!$this->getSiteID()) {
			return array();
		}

		return LaravelAnalytics::setSiteId($this->site_id)->getActiveUsers();
	}

	public function getTopCountries() {
		if (!$this->getSiteID()) {
			return array();
		}

		$startDate = Carbon::now();
		$endDate = new Carbon("-7 days");
		$metrics = 'ga:sessions';
		$others = ['dimensions' => 'ga:country', 'sort' => '-ga:sessions', 'max-results' => 10];

		$stats = LaravelAnalytics::setSiteId($this->site_id)->performQuery($endDate, $startDate, $metrics, $others)->rows;
		MyHelper::print_rf($stats);
	}

	public function getCustomReport($date = "") {
		if (!$this->getSiteID()) {
			return array();
		}

		if ($date) {
			$startDate = new Carbon($date);
			$endDate = new Carbon($date);
		} else {
			$startDate = new Carbon("yesterday");
			$endDate = new Carbon("yesterday");
		}

		$metrics = 'ga:sessions,ga:pageviews';
		$others = ['dimensions' => 'ga:fullReferrer,ga:browser,ga:browserVersion,ga:operatingSystem,ga:latitude,ga:longitude,ga:hour', 'sort' => '-ga:sessions', 'max-results' => 9999];

		$analyticsData = LaravelAnalytics::setSiteId($this->site_id)->performQuery($endDate, $startDate, $metrics, $others);
		$analyticsData = $analyticsData->rows;

		$stats = array();
		foreach ((array) $analyticsData as $ad) {
			$unique_key = md5(implode(".", $ad));
			$geo_info = $this->getGeoCounty($ad[4], $ad[5]);
			$stats[] = array(
				'referer' => $ad[0],
				'platform' => $ad[3],
				'browser' => $ad[1],
				'version' => $ad[2],
				'country' => $geo_info['country'],
				'region' => $geo_info['region'],
				'city' => $geo_info['city'],
				'ip' => '0.0.0.0',
				'geo_location' => "{$ad[4]},{$ad[5]}",
				'hits' => $ad[8],
				'visitors' => $ad[7],
				'date' => $startDate->format("Y-m-d"),
				'datetime' => date("Y-m-d {$ad[6]}:00:00", strtotime($startDate->format("Y-m-d"))),
				'unique_key' => $unique_key,
			);
		}

		return $stats;
	}

	private function getGeoCounty($lat, $long) {
		$geo_info = array(
			'country_code' => '',
			'country_name' => '',
			'region_code' => '',
			'region_name' => '',
			'city_name' => '',
			'lat' => $lat,
			'long' => $long,
		);

		if ($gl = GeoLocation::where(array('lat' => $lat, 'long' => $long))->first()) {
			return array('country' => $gl->country_code, 'region' => $gl->region_code, 'city' => $gl->city_name);
		}

		$geoAddress = "{$lat},{$long}";
		$url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($geoAddress) . '&sensor=false';
		$get = file_get_contents($url);
		$geoData = json_decode($get);

		if (isset($geoData->results[0])) {
			foreach ($geoData->results[0]->address_components as $addressComponent) {
				if (in_array('political', $addressComponent->types)) {
					if ($addressComponent->types[0] == 'locality') {
						$geo_info['city_name'] = $addressComponent->short_name;
					} else if ($addressComponent->types[0] == 'administrative_area_level_1') {
						$geo_info['region_code'] = $addressComponent->short_name;
						$geo_info['region_name'] = $addressComponent->long_name;
					} else if ($addressComponent->types[0] == 'country') {
						$geo_info['country_code'] = $addressComponent->short_name;
						$geo_info['country_name'] = $addressComponent->long_name;
					}
				}
			}
		}

		if ($geo_info['city_name']) {
			$gl = new GeoLocation();
			$gl->fill($geo_info);
			$gl->save();

			return array('country' => $gl->country_code, 'region' => $gl->region_code, 'city' => $gl->city_name);
		}

		return array('country' => '?', 'region' => '?', 'city' => '?');
	}
}
?>