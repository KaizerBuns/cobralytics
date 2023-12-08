<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Project;
use App\Advertiser;
use App\Vertical;
use App\Service;
use App\Offer;
use App\Campaign;
use App\CampaignDomain;
use App\CampaignPixel;
use App\User;
use App\Rule;
use App\Source;
use App\Pixel;
use App\DNSRecord;
use App\Creative;

use App\Helpers\TableMap;
use App\Helpers\ReportEngine;
use App\Helpers\MyHelper;
use DB;

class MemberController extends Controller {

	public $system_alert = array(
		'account_created' => array('class' => 'alert-success', 'text' => 'Your account has been successfully created.', 'icon' => 'fa-check'),
		'account_confirmed' => array('class' => 'alert-success', 'text' => 'Your account has been confirmed.', 'icon' => 'fa-check'),
		'account_notexists' => array('class' => 'alert-warning', 'text' => 'The account does not exists.', 'icon' => 'fa-check'),
		'confirm_sent' => array('class' => 'alert-success', 'text' => 'An email confirmation link has been sent. Please click on the link to confirm your account.', 'icon' => 'fa-check'),
		'saved' => array('class' => 'alert-success', 'text' => 'Your changes have been updated.', 'icon' => 'fa-check'),
		'deleted' => array('class' => 'alert-success', 'text' => 'The selected item(s) have been deleted.', 'icon' => 'fa-check'),
		'loggedin' => array('class' => 'alert-success', 'text' => 'Welcome back, you have been logged in automatically.', 'icon' => 'fa-check'),
		'crop_success' => array('class' => 'alert-success', 'text' => 'Photo updated.', 'icon' => 'fa-check'),
		'account_welcome' => array('class' => 'alert-success', 'text' => 'Welcome, please update your Username and your account details.', 'icon' => 'fa-check'),
		'invite_sent' => array('class' => 'alert-success', 'text' => 'You have successfully sent invites to your friends.', 'icon' => 'fa-check'),
		'invitecode_success' => array('class' => 'alert-success', 'text' => 'Welcome! Your invite code was successful.', 'icon' => 'fa-check'),
		'crop_error1' => array('class' => 'alert-danger', 'text' => 'Please select an area to crop.', 'icon' => 'fa-ban'),
		'crop_error2' => array('class' => 'alert-danger', 'text' => 'Unknow error occured. Unable to crop the image, please try again.', 'icon' => 'fa-ban'),
		'login_failed' => array('class' => 'alert-warning', 'text' => 'Your email / password are incorrect please try again.', 'icon' => 'fa-ban'),
		'unknown_error' => array('class' => 'alert-danger', 'text' => 'An unknown error occurred please try again.', 'icon' => 'fa-ban'),
		'fbaccount_error' => array('class' => 'alert-danger', 'text' => 'An error occurred with Facebook connect please use our registration form.', 'icon' => 'fa-ban'),
		'gplusaccount_error' => array('class' => 'alert-danger', 'text' => 'An error occurred with Google+ connect please use our registration form.', 'icon' => 'fa-ban'),
		'session_timeout' => array('class' => 'alert-warning', 'text' => 'Your session timed out, please login again.', 'icon' => 'fa-warning'),
		'logout' => array('class' => 'alert-success', 'text' => 'You have successfully logged out.', 'icon' => 'fa-warning'),
		'notfound' => array('class' => 'alert-warning', 'text' => 'The ID / Object you are trying access does not exists.', 'icon' => 'fa-warning'),
		'corruptdata' => array('class' => 'alert-warning', 'text' => 'The submitted data has been modified/corrupted please try again.', 'icon' => 'fa-warning'),
		'duplicate' => array('class' => 'alert-warning', 'text' => 'The submitted data is a duplicate please try again.', 'icon' => 'fa-warning'),
		'forgotpasswd' => array('class' => 'alert-warning', 'text' => 'Please reset your account password.', 'icon' => 'fa-warning'),
		'sendpasswd' => array('class' => 'alert-success', 'text' => 'An email has been sent to your account.', 'icon' => 'fa-check'),
		'newproject' => array('class' => 'alert-info', 'text' => 'Before you can begin, please create a new project in your account.', 'icon' => 'fa-info-circle'),
		'setproject' => array('class' => 'alert-info', 'text' => 'Your default project has been changed', 'icon' => 'fa-info-circle'),
		'bulk_error' => array('class' => 'alert-danger', 'text' => 'Please upload a valid csv file. Missing headers.', 'icon' => 'fa-info-circle'),	
		'denied' => array('class' => 'alert-warning', 'text' => 'Your account does not have access to this section', 'icon' => 'fa-warning'),	
	);

	public $user;
	public $my_project;
	public $settings;

	public function __construct(Request $request, Route $route)
	{
		$this->request = $request;
		$this->route = $route;
		$this->app_name = env('APP_NAME');
		$this->app_desc = env('APP_DESC');
		$this->report = new ReportEngine();				
			
		//Set the controller name and action name to a format I'am used to :)
		list($controller, $action) = explode('@', class_basename($route->getActionName()));
	
		$app = array(
			'app_name' => $this->app_name,
			'app_desc' => $this->app_desc,
			'controller_name' => strtolower(str_replace('Controller', '', $controller)),
			'action_name' => $this->request->input('view'),
		);

		ini_set("date.timezone","America/New_York");
		$this->controller_name = $app['controller_name'];
		$this->action_name = $app['action_name'];

		$this->middleware(function ($request, $next) {
	        $this->user = $request->user();
	        $this->my_project = $this->user->get_default_project(true);
	    	$this->settings = $this->user->get_settings();
	    	    
	        view()->share('user', $this->user);
			view()->share('my_project', $this->my_project); //user current project
			view()->share('projects', Project::get_projects($this->user)); //user projects
			view()->share('settings', $this->settings);
	
	        return $next($request);
	    });
	
		if(!$this->page_number = $this->request->input('page')) {
			$this->page_number = 1;
		}

		view()->share('app', $app);
		view()->share('page_number', $this->page_number);
		view()->share('request', $this->request);
		if($this->request->input('msg')) {
			view()->share('alert', $this->system_alert[$this->request->input('msg')]);
		}
	}

	public function dashboard_view($view_name, $stats = null) {
		switch($view_name)
		{
			case 'boxes':
				$today = (isset($stats[date("Y-m-d")]) ? $stats[date("Y-m-d")] : array());
				return view('member.dashboard.'.$view_name, ['today' => $today])->render();
			case 'daily':
			case 'daily-simple':
			case 'daily-dns':
				$top_stats = $stats;
				foreach($top_stats as $key => $ts) {
					$top_stats[$key]->date = date("M d", strtotime($ts->date));
				}

				$view_type = '';	
				if($view_name == 'daily-simple') {
					$view_name = 'daily';
					$view_type = 'simple';
				} else if($view_name == 'daily-dns') {
					$view_name = 'daily-dns';
				}

				return view('member.dashboard.'.$view_name, ['top_stats' => $top_stats, 'view_type' => $view_type])->render();
				break;
			case 'realtime':
				return view('member.dashboard.realtime')->render();
				break;
			case 'sources':
				$sources = $this->report->get_top_sources($this->user);
				return view('member.dashboard.'.$view_name, ['sources' => $sources])->render();
				break;
			case 'country':
				return view('member.dashboard.'.$view_name, ['countries' => $stats])->render();
				break;
		}
	}
	
	public function display_rotators($object) {
		$type = $object->get_type();
		$rules = $object->get_all_rotators();
		
		$rule_countries = $this->get_countries();
		$rule_regions = $this->get_regions();
		$rule_cities = $this->get_cities();
		$rule_agents = $this->get_user_agents();

		$descriptor = array(
			'Rotators' => array(
				'html' => array(
					'html' => "
						<div>{VALUE}</div>
						<div>{STATUS}</div>
					",
					'value_field' => array(
						'ID' => 'id',
						'NAME' => 'name',
						'TYPE' => array('field' => 'type', 'format' => 'uppercase'),
						'VALUE' => 'value',
						'STATUS' => 'status',
					),
				),
			),
			'Country' => array('field' => 'country', 'format' => 'display_rule'),
			'Region' => array('field' => 'region', 'format' => 'display_rule'),
			'City' => array('field' => 'city', 'format' => 'display_rule'),
			'Agent' => array('field' => 'agent', 'format' => 'display_rule'),
			'Weight' => array(
				'html' => array(
					'html' => "<div><input id='rule-weight_{ID}' value='{WEIGHT}' placeholder='Weight %' class='form-control' onchange=\"update_rule({ID}, 'weight', this.value)\" maxlength='3'></div>",
					'value_field' => array(
						'ID' => 'id',
						'WEIGHT' => 'weight'
					),
					'class' => 'col-xs-1'
				),
			),
			'Weight %' => array('field' => 'weight_percent', 'format' => 'percentage'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time-short', 'class' => 'col-xs-1'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<div style='width:60px;'><a href='/member/rule/?view=edit&id={ID}&type={$type}&type_id={$object->id}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil-square-o'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Rule?', function(result) { if (result===true) { window.location.href='/member/rule/?view=delete&id={ID}&type={$type}&type_id={$object->id}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a></div>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);
		
		$params = array(
			'table_id' => 'tblrotators',
			'table_class' => '',
			'enable_checkbox' => array('value_field' => 'id')
		);

		$view_header = 'Rotators';
		$view_object = 'Rotator';

		$tablemap = TableMap::create($rules, $descriptor, $params);
		return ['tablemap_rotators' => $tablemap, 'rotator_object' => $object, 'rotator_view_header' => $view_header, 'rotator_view_object' => $view_object];
	}

	public function display_rules($object) {
		$type = $object->get_type();
		$rules = $object->get_rules();
		
		$rule_countries = $this->get_countries();
		$rule_regions = $this->get_regions();
		$rule_cities = $this->get_cities();
		$rule_agents = $this->get_user_agents();

		$descriptor = array(
			'Name' => array(
				'html' => array(
					'html' => "
						<div>{TYPE} {NAME}</div>
						<div>{VALUE}</div>
						<div>{STATUS}</div>
					",
					'value_field' => array(
						'ID' => 'id',
						'NAME' => 'name',
						'TYPE' => array('field' => 'type', 'format' => 'uppercase'),
						'VALUE' => 'value',
						'STATUS' => 'status',
					),
				),
			),
			'Country' => array('field' => 'country', 'format' => 'display_rule'),
			'Region' => array('field' => 'region', 'format' => 'display_rule'),
			'City' => array('field' => 'city', 'format' => 'display_rule'),
			'Agent' => array('field' => 'agent', 'format' => 'display_rule'),
			/*'Rule' => array(
				'html' => array(
					'html' => "
						<div>Country: {COUNTRY}</div>
						<div>Region: {REGION}</div>
						<div>City: {CITY}</div>
						<div>Agent: {AGENT}</div>						
					",
					'value_field' => array(
						'ID' => 'id',
						'COUNTRY' => array('field' => 'country', 'format' => 'display_rule'),
						'REGION' => array('field' => 'region', 'format' => 'display_rule'),
						'CITY' => array('field' => 'city', 'format' => 'display_rule'),
						'AGENT' => array('field' => 'agent', 'format' => 'display_rule')
					),
					'class' => 'col-sm-2'
				),
			),*/
			'Weight' => array(
				'html' => array(
					'html' => "<div><input id='rule-weight_{ID}' value='{WEIGHT}' placeholder='Weight %' class='form-control' onchange=\"update_rule({ID}, 'weight', this.value)\" maxlength='3'></div>",
					'value_field' => array(
						'ID' => 'id',
						'WEIGHT' => 'weight'
					),
					'class' => 'col-xs-1'
				),
			),
			'Weight %' => array('field' => 'weight_percent', 'format' => 'percentage'),
			'Options' => array('field' => 'options', 'class' => 'col-sm-1'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time-short', 'class' => 'col-sm-1'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<div style='width:60px;'><a href='/member/rule/?view=edit&id={ID}&type={$type}&type_id={$object->id}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil-square-o'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Rule?', function(result) { if (result===true) { window.location.href='/member/rule/?view=delete&id={ID}&type={$type}&type_id={$object->id}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a></div>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);
		
		$params = array(
			'table_id' => 'tblrules',
			'table_class' => '',
			'enable_checkbox' => array('value_field' => 'id')
		);

		$view_header = 'Rules';
		$view_object = 'Rule';
		if($type == 'offer') {
			$view_header = 'Landing Pages';	
			$view_object = 'Landing Page';	
		}

		$tablemap = TableMap::create($rules, $descriptor, $params);
		return ['tablemap_rules' => $tablemap, 'object' => $object, 'view_header' => $view_header, 'view_object' => $view_object];
	}
	
	public function display_dns($object) {
				
		//Get the DNS
		$dns = $object->get_dns();
		$descriptor = array(
			'Name' => array('field' => 'name'),
			'Type' => array('field' => 'type', 'format' => 'uppercase'),
			'Content' => array('field' => 'content', 'format' => 'trimtext-50'),
			'Prio' => array('field' => 'prio','if_empty' => 'na'),
			'TTL' => array('field' => 'ttl'),
			'Change Date' => array('field' => 'change_date', 'format' => 'nice-date-time-short'),
			'Actions' => array(
					'html' => array(
						'html' => "<a onclick=\"bootbox.confirm('Are you sure you want to delete this DNS row?', function(result) { if (result===true) { window.location.href='/member/dns/?view=delete&id={ID}&object_id=$object->id'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
						'value_field' => array('ID' => 'id'),
						'class' => 'col-xs-1'
					),
				),
		);
	
		$params = array(
			'table_id' => 'tbldns',
			'table_class' => ''
		);

		$tablemap = TableMap::create($dns, $descriptor, $params);
		return ['tablemap_dns' => $tablemap];
	}

	public function get_user_agents() {
		$user_agents = array(
			//'0' => 'Select one',
			'0' => 'All',
			'msie' => 'Internet Explorer',
			'chrome' => 'Chrome',
			'safari' => 'Safari',
			'opera' => 'Opera',
			'iphone' => 'iPhone',
			'ipad' => 'iPad',
			'android' => 'Android',
			'blackberry' => 'Blackberry',
			'iemobile' => 'Windows Phone',
			'mobile only' => 'Mobile Only',
			'tablet only' => 'Tablet Only',
			'desktop only' => 'Desktop Only',
			'ios only' => 'iOS Only',
			'android only' => 'AndroidOS Only',
		);

		return $user_agents;
	}

	public function get_countries() {
		$sql = "SELECT country_iso_code, country_name FROM geo_countries WHERE country_iso_code != '' ORDER BY country_iso_code ASC";
		$results = DB::select( DB::raw($sql));
		
		$countries = array();
		//$countries[0] = 'Select one';
		$countries[0] = 'All';
		if(count($results) > 0) {
			foreach($results as $r) {
				if($r->country_iso_code && $r->country_name) {
					$countries[$r->country_iso_code] = utf8_encode($r->country_name);
				}
			}
		}

		return $countries;
	}

	public function get_regions($country = '') {

		$states = array();
		$sql = "SELECT subdivision_iso_code, subdivision_name FROM geo_cities WHERE country_iso_code = :country AND subdivision_iso_code != '' GROUP BY subdivision_iso_code, subdivision_name ORDER BY subdivision_iso_code ASC";
		$results = DB::select( DB::raw($sql), array('country' => $country));

		//$states[0] = 'Select one';
		$states[0] = 'All';
		if(count($results) > 0) {
			foreach($results as $r) {
				if($r->subdivision_iso_code && $r->subdivision_name) {
					$states[$r->subdivision_iso_code] = utf8_encode($r->subdivision_name);
				}
			}
		}

		return $states;
	}

	public function get_cities($country = '', $state = '') {

		$cities = array();
		$sql = "SELECT subdivision_iso_code, city_name FROM geo_cities WHERE country_iso_code = :country AND subdivision_iso_code = :state AND city_name != '' GROUP BY city_name";
		$results = DB::select( DB::raw($sql), array('country' => $country, 'state' => $state));
		
		//$cities[0] = 'Select one';
		$cities[0] = 'All';
		if(count($results) > 0) {
			foreach($results as $r) {
				$cities[$r->city_name] = utf8_encode($r->city_name);
			}
		}

		return $cities;
	}
}
