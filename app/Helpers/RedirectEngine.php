<?php namespace App\Helpers;

use App\Campaign;
use App\CampaignDomain;
use App\Creative;
use App\DNSRecord;
use App\Helpers\AppHelper;
use App\Helpers\MyHelper;
use App\Offer;
use App\Rule;
use App\Service;
use App\Source;
use App\User;
use DB;

require __DIR__ . '/Agent_Detect.php';

class RedirectEngine {
	public $test_mode = 0; //Live - 1 Test
	public $pixel_redirect = 0;
	public $referer = '';
	public $cookie_expiry = '';
	public $test_traffic = false;
	public $tracking_url_append = '';

	public function __construct() {
		$this->test_mode = (isset($_REQUEST['test']) && $_REQUEST['test'] == 'cobralalala' ? true : false);

		if (!isset($_SERVER['GEOIP_COUNTRY_CODE'])) {
			$_SERVER['GEOIP_COUNTRY_CODE'] = 'CA';
		}

		if (!isset($_SERVER['GEOIP_COUNTRY_NAME'])) {
			$_SERVER['GEOIP_COUNTRY_NAME'] = 'Canada';
		}

		if (!isset($_SERVER['GEOIP_REGION'])) {
			$_SERVER['GEOIP_REGION'] = 'ON';
		}

		if (!isset($_SERVER['GEOIP_REGION_NAME'])) {
			$_SERVER['GEOIP_REGION_NAME'] = 'Ontario';
		}

		if (!isset($_SERVER['GEOIP_CITY'])) {
			$_SERVER['GEOIP_CITY'] = 'Toronto';
		}

		if (!isset($_SERVER['GEOIP_LATITUDE'])) {
			$_SERVER['GEOIP_LATITUDE'] = 1;
		}

		if (!isset($_SERVER['GEOIP_LONGITUDE'])) {
			$_SERVER['GEOIP_LONGITUDE'] = 1;
		}

		$this->cookie_expiry = time() + 60 * 60 * 24 * 7;
	}

	public function execute($test_traffic = false) {
		//defaults
		if (isset($_REQUEST['referer']) && $_REQUEST['referer']) {
			$_SERVER['HTTP_REFERER'] = $_REQUEST['referer'];
		}

		//Detect
		$this->detect = new Mobile_Detect;
		$this->agent = parse_user_agent($_SERVER['HTTP_USER_AGENT']);
		$this->sub1 = (isset($_REQUEST['sub1']) && trim($_REQUEST['sub1']) ? trim($_REQUEST['sub1']) : '');
		$this->sub2 = (isset($_REQUEST['sub2']) && trim($_REQUEST['sub2']) ? trim($_REQUEST['sub2']) : '');
		$this->sub3 = (isset($_REQUEST['sub3']) && trim($_REQUEST['sub3']) ? trim($_REQUEST['sub3']) : '');
		$this->sub4 = (isset($_REQUEST['sub4']) && trim($_REQUEST['sub4']) ? trim($_REQUEST['sub4']) : '');
		$this->sub5 = (isset($_REQUEST['sub5']) && trim($_REQUEST['sub5']) ? trim($_REQUEST['sub5']) : '');
		$this->cookie_domain = $_SERVER['HTTP_HOST'];
		$this->test_traffic = $test_traffic;

		//get dns info
		$_SERVER['HTTP_HOST'] = DNSRecord::get_cname($_SERVER['HTTP_HOST']);

		//Get Campaign
		$this->get_campaign($_SERVER['HTTP_HOST'], (isset($_REQUEST['t']) && $_REQUEST['t'] ? $_REQUEST['t'] : ""));

		//Check if theres a tracking stream
		$this->tracking_source();

		if ($this->test_mode) {
			echo "Source: {$this->source->source_name}<br>";
			echo "Source Type: " . $this->source->get_type() . "<br>";
			echo "Source Ad Content: " . $this->source->get_ad_content() . "<br>";
			MyHelper::print_rf($this->source);
			unset($_SERVER['DOCUMENT_ROOT']);
			unset($_SERVER['SERVER_ADMIN']);
			unset($_SERVER['SCRIPT_FILENAME']);
			unset($_SERVER['PHP_SELF']);
			MyHelper::print_rf($_SERVER);
		}

		//Get Rotators
		//This is for campaigns with rotators (banners/templates)
		$rotator_selected = array();
		if ($this->source->get_type() == 'campaign' && $this->source->is_banner()) {
			$rotator_rules = $this->get_rules($this->source->get_type(), $this->source->id, 'creative', 1);
			$rotator_count = count($rotator_rules);

			if ($rotator_count > 0) {
				$rotator_selected = $this->arrange_select($rotator_rules);

				$creative = Creative::find($rotator_selected->creative_id);
				$creative_id = $creative->id;

				$creative_selected['image_url'] = $creative->getPublicUrl();
				$creative_selected['file_width'] = $creative->file_width;
				$creative_selected['file_height'] = $creative->file_height;
			} else {
				echo "Invalid campaign - Please setup your campaign properly.";
				exit;
			}
		}

		//get the selected rule
		$selected = $this->get_selected_rule();
		if (count($rotator_selected)) {
			$selected->rotator_id = $rotator_selected->id;
		} else {
			$selected->rotator_id = 0;
		}

		//set the redirect options
		$selected->url = $this->redirect_options($selected);

		//create log
		$log = $this->init_log($selected);
		$log_info = AppHelper::create_log($log);
		$destination_url = $log_info['destination'];

		if ($this->test_mode) {
			echo "Skipping Cookie Creation<br>";
		} else {
			//update/set the redirect engine cookie only for campaigns
			if ($this->source->get_type() == 'campaign') {
				setcookie('_cbclick_', $log_info['click_id'], $this->cookie_expiry, '/', '.' . $this->cookie_domain);
				setcookie('_cbcampaign_', $log_info['campaign_id'], $this->cookie_expiry, '/', '.' . $this->cookie_domain);
			}

			//create unique_cookie to expire in a day
			if (!isset($_COOKIE['_cbuniqueid_'])) {
				setcookie('_cbuniqueid_', $log_info['visitor_id'], $this->cookie_expiry, '/', '.' . $this->cookie_domain);
			}

			if (isset($_REQUEST['cid']) && $_REQUEST['cid']) {
				setcookie('_networkCID_', $_REQUEST['cid'], $this->cookie_expiry, '/', '.' . $this->cookie_domain);
			}
		}

		//DO REPLACEMENT
		$dynamic_params = array(
			'{CLICKID}' => $log_info['click_id'],
			'{SUB1}' => $log_info['sub1'],
			'{SUB2}' => $log_info['sub2'],
			'{SUB3}' => $log_info['sub3'],
			'{SUB4}' => $log_info['sub4'],
			'{SUB5}' => $log_info['sub5'],
		);

		foreach ($dynamic_params as $key_param => $key_value) {
			$destination_url = str_replace($key_param, $key_value, $destination_url);
		}

		if ($this->test_mode) {
			echo "LogInfo:<br>";
			echo MyHelper::print_rf($log_info);

			echo "Usage: " . memory_get_usage() . "<br>";
			echo "Bytes: " . strlen($destination_url) . "<br>";
			echo "DestinationURL: {$destination_url}<br>";
			exit;
		}

		if ($this->test_traffic) {
			return $log_info['click_id'];
		}

		if ($this->source->get_type() == 'campaign' && $this->source->is_banner()) {
			echo "<a href='/clck/?r={$destination_url}&cobraCID={$log_info['click_id']}&hr={$selected->hide_referrer}'><img src='" . $creative_selected['image_url'] . "' width='" . $creative_selected['file_width'] . "' height='" . $creative_selected['file_height'] . "'></a>";
			exit();
		} else {
			if ($this->source->get_type() == 'campaign' && $this->source->is_template()) {
				$destination_url = $this->add_clickid($destination_url, $log_info['click_id']);
			}

			if (isset($selected->framed) && $selected->framed) {
				echo "<!DOCTYPE html>";
				echo "<head>";
				echo "<title>{$selected->page_title}</title>";
				echo "<meta charset='utf-8'>";
				echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
				echo "<meta name='description' content='{$selected->meta_desc}'>";
				echo "<meta name='keywords' content='{$selected->meta_keywords}'>";
				echo "<style>body { margin: 0; overflow:hidden; }  #iframe1 { height: 100%; left: 0px; position: absolute; top: 0px; width: 100%; }</style>";
				echo "</head>";
				echo "<body>";
				echo '<iframe id="iframe1" src="' . $destination_url . '" style="border: 0; width: 100%; height: 100%;" frameborder="0"></iframe>';
				echo "</body>";
				echo "</html>";
			} else {
				if (isset($selected->hide_referrer) && $selected->hide_referrer) {
					echo '<meta http-equiv="refresh" content="0;url=' . $destination_url . '">';exit();
				} else {
					if (preg_match("/nosettings$/", $destination_url)) {
						$this->show_error();
					} else {
						header("HTTP/1.1 301 Moved Permanently");
						header("location: {$destination_url}");exit();
					}
				}
			}
		}
	}

	private function get_campaign($http_host, $linkhash = "") {
		$tmp = explode(".", $http_host);
		if (count($tmp) > 2) {
			if ($this->test_mode) {
				echo "Original Host - {$http_host}<br>";
				foreach ($tmp as $k => $v) {
					echo "Part $k - {$v}<br>";
				}
				echo "Linkhash - {$linkhash}<br>";
			}

			if (in_array($http_host, config('app.app_redirects'))) {
				$cd = CampaignDomain::where(array("linkhash" => $linkhash))->first();
			} else {
				//try to break out the linkhash from the domain
				if ($linkhash == "") {
					$linkhash = $tmp[0];
					$host = str_replace($linkhash . '.', '', $http_host);
				} else {
					$host = $http_host;
				}

				$sql = "SELECT cd.* FROM campaign_domains cd
						LEFT JOIN sources s ON (s.id = cd.source_id AND s.type = 'domain')
						WHERE cd.linkhash = :hash AND s.name = :host";

				$cd = CampaignDomain::hydrate(DB::select(DB::raw($sql), array('hash' => $linkhash, 'host' => $host)))->first();
			}
		} else {
			if ($this->test_mode) {
				echo "Original Host - {$http_host}<br>";
				echo "Linkhash - {$linkhash}<br>";
			}
			$sql = "SELECT cd.* FROM campaign_domains cd
					LEFT JOIN sources s ON (s.id = cd.source_id AND s.type = 'domain')
					WHERE cd.linkhash = :hash AND s.name = :host LIMIT 1";

			$cd = CampaignDomain::hydrate(DB::select(DB::raw($sql), array('hash' => $linkhash, 'host' => $http_host)))->first();
		}

		$this->source = null;
		if (isset($cd) && $cd instanceof CampaignDomain) {
			$this->source = Campaign::get_campaign_by_hash($cd->linkhash, $http_host);
			if (!$this->source instanceof Campaign) {
				$this->source = Campaign::get_campaign_by_hash($cd->linkhash);
			}

			//$this->source_type = 'campaign';
			if (in_array($http_host, config('app.app_redirects'))) {
				$domain = Source::where(array('name' => $http_host))->first();
				if ($domain instanceof Source) {
					$this->source->domain_id = $domain->id;
				} else {
					$this->source->domain_id = 0;
				}
			}

			//Set the tracking url append
			$this->tracking_url_append = trim($this->source->tracking_url_append);
		}

		if (!$this->source instanceof Campaign) {
			$source = $http_host;
			$sql = "SELECT id, name as source_name, project_id, user_id FROM sources WHERE name = :source AND active = 1 LIMIT 1";
			$this->source = Source::hydrate(DB::select(DB::raw($sql), array('source' => $source)))->first();
			//$this->source_type = 'source';
		}

		if (!$this->source instanceof Campaign) {
			if (!$this->source instanceof Source) {
				$this->show_error();
			}
		}
	}

	private function get_selected_rule($rules = array()) {
		if (empty($rules)) {
			$rules = $this->get_rules($this->source->get_type(), $this->source->id);
		}

		if ($this->test_mode) {
			echo "Raw Rules:";
			MyHelper::print_rf($rules);
		}

		//check if theres a default service set by the source user
		if (empty($rules)) {
			if ($service = Service::get_default_service_by_user_id($this->source->user_id)) {
				$rules = $this->get_rules('service', $service->id);
			} else {
				$rules = [];
			}
		}

		//arrange rules and select
		do {
			$selected = $this->arrange_select($rules);

			//catch all
			if (empty($selected)) {
				if ($this->test_mode) {
					echo "Triggering Catch All - Loop 1<br>";
				}

				$selected = (object) array(
					'id' => 0,
					'weight' => 100,
					'url' => $this->catchall(),
					'agent' => '?',
					'type' => 'redirect',
					'path_forwarding' => 0,
					'qstring_forwarding' => 0,
					'hide_referrer' => 0,
					'secure' => 0,
				);
			}

			if ($this->test_mode) {
				echo "Selected Rule:";
				MyHelper::print_rf($selected);
			}

			//check if the selected rule is a Offer Rule or Campaign Rule
			switch ($selected->type) {
			case 'redirect':
			case 'landingpage':
				$selected->offer_id = 0;
				if (isset($selected->rule_type) && $selected->rule_type == 'offer') {
					$selected->offer_id = (int) $selected->rule_type_id;
				}
				$rules = array();
				break;
			case 'offer':
				$rules = $this->get_rules('offer', $selected->offer_id);

				if ($this->test_mode) {
					echo "Offers Rules:";
					MyHelper::print_rf($rules);
				}

				if (empty($rules)) {
					$selected->offer_id = 0;
					$offer = Offer::find($selected->offer_id);
					if ($offer instanceof Offer && $offer->url) {
						$selected->offer_id = $offer->id;
						$selected->url = $offer->url;
					} else {
						if ($this->test_mode) {
							echo "Triggering Catch All - Loop 2<br>";
						}
						$selected = (object) array(
							'id' => 0,
							'weight' => 100,
							'url' => $this->catchall(),
							'agent' => '?',
							'type' => 'redirect',
							'path_forwarding' => 0,
							'qstring_forwarding' => 0,
							'hide_referrer' => 0,
							'secure' => 0,
							'offer_id' => 0,
							'rotator_id' => 0,
							'service_id' => 0,
						);
					}
					$rules = array();
				}
				if ($this->test_mode) {
					if (count($rules) > 0) {
						echo "Offer Found - using new method<br>";
					} else {
						echo "Offer Found - using old method<br>";
					}
				}
				break;
			case 'campaign':
				$rules = $this->get_rules('campaign', $selected->campaign_id);
				if ($this->test_mode) {
					echo "Campaign Found - looping around<br>";
				}
				break;
			case 'service':
				$rules = $this->get_rules('service', $selected->service_id);
				if ($this->test_mode) {
					echo "Service Found - looping around<br>";
				}
				break;
			case 'forsale':

				break;
			}
		} while (!empty($rules));

		$selected->service_id = 0;
		if (isset($selected->rule_type) && $selected->rule_type == 'service') {
			$selected->service_id = $selected->rule_type_id;
		}

		return $selected;
	}

	private function tracking_source() {
		if (isset($_REQUEST['ts']) && $_REQUEST['ts']) {
			$tstream = Source::where(array('user_id' => $this->source->user_id, 'name' => $_REQUEST['ts']))->first();

			//add it
			if (is_null($tstream)) {
				$data = array(
					'name' => strtolower($_REQUEST['ts']),
					'user_id' => $this->source->user_id,
					'project_id' => $this->source->project_id,
					'type' => 'traffic',
					'active' => 1,
				);

				$tstream = new Source();
				$tstream->fill($data);
				$tstream->save();
			}

			//assign it back to the source
			$this->source->source_id = $tstream->id;
			$this->source->source_name = $tstream->name;
		}
	}

	private function catchall() {
		if ($this->pixel_redirect) {
			return "noredir";
		}

		if ($this->test_mode) {
			echo "Catch All<br>";
		}

		//retrieve users catch all
		if ($user = User::find($this->source->user_id)) {
			if ($user->pref_all_rule) {
				return $user->pref_all_rule;
			}
		}

		return 'nosettings';
	}

	private function init_log($selected) {
		$rule_id = $selected->id;
		$offer_id = $selected->offer_id;
		$rotator_id = $selected->rotator_id;
		$destination_url = $selected->url;
		$service_id = $selected->service_id;
		$clicks = 1;
		if ($rotator_id > 0) {
			$clicks = 0;
		}

		$source = $this->source;
		if ($source->get_type() == 'campaign') {
			$log = array(
				'project_id' => $source->project_id,
				'campaign_id' => $source->campaign_id,
				'source_id' => $source->source_id,
				'domain_id' => $source->domain_id,
				'offer_id' => $offer_id,
				'user_id' => $source->user_id,
				'destination' => $destination_url,
				'linkhash' => $source->linkhash,
				'rule_id' => $rule_id,
				'rotator_id' => $rotator_id,
				'service_id' => $service_id,
				'sub1' => $this->sub1,
				'sub2' => $this->sub2,
				'sub3' => $this->sub3,
				'sub4' => $this->sub4,
				'sub5' => $this->sub5,
				'clicks' => $clicks,
				'revenue' => 0,
				'cost' => 0,
			);
		} else {
			$log = array(
				'source_id' => $source->source_id,
				'domain_id' => $source->id,
				'user_id' => $source->user_id,
				'project_id' => $source->project_id,
				'destination' => $destination_url,
				'rule_id' => $rule_id,
				'service_id' => $service_id,
				'sub1' => $this->sub1,
				'sub2' => $this->sub2,
				'sub3' => $this->sub3,
				'sub4' => $this->sub4,
				'sub5' => $this->sub5,
				'clicks' => $clicks,
				'revenue' => 0,
			);
		}

		//$log['screen'] = $this->get_screen();
		return $log;
	}

	private function get_rules($object_type, $object_id, $type = null, $rotator = 0, $rotator_key = null) {
		$active = 1; //normal
		if ($this->pixel_redirect) {
			$active = 0; //pixel redirects
		}

		$rules = Rule::get_rules_by_country($object_type, $object_id, $active, $type, $rotator, $rotator_key);

		if (isset($rules[$_SERVER['GEOIP_COUNTRY_CODE']][$_SERVER['GEOIP_REGION']][$_SERVER['GEOIP_CITY']])) {
			$rules = $rules[$_SERVER['GEOIP_COUNTRY_CODE']][$_SERVER['GEOIP_REGION']][$_SERVER['GEOIP_CITY']];
		} else if (isset($rules[$_SERVER['GEOIP_COUNTRY_CODE']][$_SERVER['GEOIP_REGION']]['?'])) {
			$rules = $rules[$_SERVER['GEOIP_COUNTRY_CODE']][$_SERVER['GEOIP_REGION']]['?'];
		} else if (isset($rules[$_SERVER['GEOIP_COUNTRY_CODE']]['?']['?'])) {
			$rules = $rules[$_SERVER['GEOIP_COUNTRY_CODE']]['?']['?'];
		} else if (isset($rules['?']['?']['?'])) {
			$rules = $rules['?']['?']['?'];
		} else {
			$rules = array();
		}

		return $rules;
	}

	private function arrange_select($rules = array()) {
		//Check the user-agent
		$any_rules = array();
		$agent_rules = array();

		if (count($rules) == 0) {
			return array();
		}

		foreach ($rules as $rule) {
			//check all the agent rules first
			if ($rule->agent == '?') {
				$any_rules[] = $rule;
			} else if ($rule->agent != '?') {
				if ($rule->agent == 'mobile only') {
					if ($this->detect->isMobile()) {
						$agent_rules[] = $rule;
					}
				} else if ($rule->agent == 'tablet only') {
					if ($this->detect->isTablet()) {
						$agent_rules[] = $rule;
					}
				} else if ($rule->agent == 'desktop only') {
					if (!$this->detect->isMobile() && !$this->detect->isTablet()) {
						$agent_rules[] = $rule;
					}
				} else if ($rule->agent == 'ios only') {
					if ($this->detect->isiOS()) {
						$agent_rules[] = $rule;
					}
				} else if ($rule->agent == 'android only') {
					if ($this->detect->isAndroidOS()) {
						$agent_rules[] = $rule;
					}
				} else {
					$user_agent = implode(" ", $this->agent);
					if (preg_match("/{$rule->agent}/is", $user_agent)) {
						$agent_rules[] = $rule;
					}
				}
			}
		}

		/*
			if($this->test_mode) {
				echo "Rules:<br>";
				print_rf($rules);
				echo "Any rules:<br>";
				print_rf($any_rules);
				echo "Agent rules:<br>";
				print_rf($agent_rules);
				echo "Agent:<br>";
				print_rf($this->agent);
		*/

		$rules = $any_rules;
		if (count($agent_rules)) {
			$rules = $agent_rules;
		}

		//get the weights first
		$weight = 0;
		foreach ($rules as $key => $rule) {
			$weight += $rules[$key]->weight;
			$rules[$key]->weight = $weight;
		}

		$selection = rand(1, $weight);

		if ($this->test_mode) {
			echo "Re-arrange rules with weights<br>";
			MyHelper::print_rf($rules);
			echo "Selection - Random (1 to {$weight}) - {$selection}<br>";
		}

		//make the selection
		$selected = null;
		foreach ($rules as $rule) {
			if ($selection <= $rule->weight) {
				$selected = $rule;
				break;
			}
		}

		return $selected;
	}

	private function redirect_options($selected) {
		$destination_url = str_replace(array("http://", "https://"), "", $selected->url);

		$path_fwd = "";
		if ($selected->path_forwarding) {
			$path_fwd = $_SERVER['REQUEST_URI'];
			$request = parse_url($path_fwd);
			$destination_url .= $path_fwd;
		}

		$query_string = "";
		if ($selected->qstring_forwarding) {
			$query = parse_url($destination_url);
			$destination_url = $query['path'];

			if (isset($query['query'])) {
				$query_string = '?' . $query['query'] . '&';
			} else {
				$query_string = '?';
			}
			$query_string .= $_SERVER['QUERY_STRING'];
		}

		$protocol = "http://";
		if ($selected->secure) {
			$protocol = "https://";
		}

		$append_params = '';
		if ($this->tracking_url_append) {
			$append_params = "{$this->tracking_url_append}";
		}
		
		if ($selected->skip_tracking_url_append) {
			$append_params = '';
		}

		$destination_url = "{$protocol}{$destination_url}{$query_string}";
		if (strpos($destination_url, '?') === false) {
			$destination_url .= "?{$append_params}";
		} else {
			$destination_url .= "&{$append_params}";			
		}
		return $destination_url;
	}

	private function add_clickid($destination_url, $click_id) {
		$query = parse_url($destination_url);
		$destination_url = $query['path'];

		if (isset($query['query'])) {
			$query_string = "?{$query['query']}&cobraCID={$click_id}&";
		} else {
			$query_string = "?cobraCID={$click_id}";
		}

		//$query_string.= $_SERVER['QUERY_STRING'];
		$destination_url = "{$destination_url}{$query_string}";
		return $destination_url;
	}

	public function conversion($test_traffic = false) {
		$subid = (isset($_REQUEST['subid']) && $_REQUEST['subid'] ? trim($_REQUEST['subid']) : '');
		$click_id = (isset($_REQUEST['cobraCID']) && $_REQUEST['cobraCID'] ? trim($_REQUEST['cobraCID']) : '');
		$revenue = (isset($_REQUEST['revenue']) && $_REQUEST['revenue'] ? $_REQUEST['revenue'] : 0);
		$test_campaign_id = (isset($_REQUEST['test_campaign_id']) && $_REQUEST['test_campaign_id'] ? $_REQUEST['test_campaign_id'] : '');
		$host = $_SERVER['HTTP_HOST'];

		$click_cookie = '_cbclick_';
		$conversion_cookie = '_cbconversion_';
		$campaign_cookie = '_cbcampaign_';

		if ($click_id == '') {
			if (isset($_COOKIE[$click_cookie]) && $_COOKIE[$click_cookie]) {
				$click_id = $_COOKIE[$click_cookie];
			}
		}

		$fire_success = 0;
		if ($click_id) {

			/*
			if ($test_traffic) {
				$fire_pixel = true;
			} else {
				$fire_pixel = false;
				if (!isset($_COOKIE[$conversion_cookie])) {
					$fire_pixel = true;
				}
			}
			*/

			//if ($fire_pixel) {
				if ($test_traffic) {
					$campaign_id = $test_campaign_id;
				} elseif (isset($_COOKIE[$campaign_cookie])) {
					$campaign_id = $_COOKIE[$campaign_cookie];
				} else {
					$campaign_id = 0;
				}

				if ($campaign_id) {
					//retrieve campaign info
					$campaign = Campaign::find($campaign_id);
					if ($revenue) {
						$log_info['revenue'] = $revenue;
					} else if (isset($log_info['offer_id']) && $log_info['offer_id'] > 0) {
						$offer = Offer::find($log_info['offer_id']);
						$log_info['revenue'] = $offer->revenue;
					} else {
						$log_info['revenue'] = $campaign->revenue;
					}

					$payout = 0;
					AppHelper::log_conversion($click_id, $revenue, $payout);

					if (!$test_traffic) {
						setcookie($conversion_cookie, $click_id, $this->cookie_expiry, '/', '.' . $host);

						//fire 3rd party pixels
						$pixels = $campaign->get_3rdparty_pixels();
						foreach ($pixels as $pixel) {
							$pixel->fire_pixel();
						}
					}

					$fire_success = 1;
				}
			//}
		}

		return $fire_success;
	}

	public function click() {
		if (isset($_REQUEST['cobraCID']) && $_REQUEST['cobraCID']) {
			$click_id = $_REQUEST['cobraCID'];

			//update click id
			AppHelper::log_click($click_id);

			if (isset($_REQUEST['hr']) && $_REQUEST['hr']) {
				echo '<meta http-equiv="refresh" content="0;url=' . $_REQUEST['r'] . '">';
			} else {
				header("location: {$_REQUEST['r']}");
			}

			return;
		}
	}

	public function rotator() {

		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400'); // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
			}

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			}

			exit(0);
		}

		$campaign_id = $_REQUEST['campaign_id'];
		$rotator_key = $_REQUEST['rotator_key'];
		$click_id = (isset($_REQUEST['cobraCID']) && $_REQUEST['cobraCID'] ? $_REQUEST['cobraCID'] : '');

		//get campaign
		$this->source = Campaign::find($campaign_id);

		//create tracking source
		$this->tracking_source();

		//get rule
		$rules = $this->get_rules('campaign', $campaign_id, null, 1, $rotator_key);
		$selected = $this->get_selected_rule($rules);
		$selected->url = $this->redirect_options($selected);

		$log_info = array(
			'project_id' => $this->source->project_id,
			'campaign_id' => $campaign_id,
			'domain_id' => 0,
			'offer_id' => $selected->offer_id,
			'user_id' => $this->source->user_id,
			'destination' => $selected->url,
			'linkhash' => $this->source->linkhash,
			'rule_id' => $selected->id,
			'service_id' => $selected->service_id,
		);

		$log_info['source_id'] = $this->source->source_id;
		$log_info['rotator_id'] = $selected->id;
		$log_info['date_time'] = date("Y-m-d H:i:s");
		$log_info['visitors'] = 1;
		$log_info['clicks'] = 1;
		$log_info['destination'] = $selected->url;

		if ($click_id) {
			$log_info['click_id'] = $click_id;
		}

		AppHelper::create_log($log_info);
		echo $selected->url;
		exit;
	}

	private function show_error() {
		echo "Invalid domain/campaign settings";
		exit();
	}

	public function get_screen() {
		ob_start();
		echo "<script>document.write(screen.width+screen.height)</script>";
		$var = ob_get_contents();
		ob_end_clean();
		return (int) $var;
	}

	private function lookup_cname() {

	}
}
?>
