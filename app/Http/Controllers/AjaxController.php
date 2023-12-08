<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Rule;
use App\Source;
use App\Pixel;
use App\Campaign;
use App\Offer;
use App\Creative;
use App\Service;
use DB;

class AjaxController extends MemberController {

	public function search_pixel() {
		if($pixel_id = $this->request->input('id')) {
			$sql = "SELECT * FROM pixels WHERE user_id = '{$this->user->id}' AND id IN ($pixel_id)";
			$pixels = DB::select( DB::raw($sql), array());

			foreach($pixels as $s) {
				$tmp[] = array(
					'id' => $s->id,
					'text' => $s->name." - ".stripslashes($s->pixel)
				);
			}
			
			echo json_encode($tmp);
			die;
		}
		
		$name = $this->request->input('name');		
		$results = Pixel::get_pixels($this->user, array('search' => array('name' => $name)));
		
		$tmp = array();
		foreach($results as $res) {
			$tmp[] = array('id' => $res->id, 'text' => $res->name." - ".stripslashes($res->pixel));
		}
		
		return json_encode($tmp);		
	}

	public function search_source() {

		if($source_id = $this->request->input('id')) {	
			$sql = "SELECT * FROM sources WHERE user_id = '{$this->user->id}' AND id IN ({$source_id})";
			$sources = DB::select(DB::raw($sql), array());
		
			$tmp = array();
			foreach($sources as $s) {
				$tmp[] = array(
					'id' => $s->id,
					'text' => $s->name
				);
			}

			if(count($tmp) == 1) {
				$tmp = array_shift($tmp);	
			}
			
			return json_encode($tmp);
		}
		
		$name = $this->request->input('name');
		$type = $this->request->input('type');
		$results = Source::get_sources($this->user, array('search' => array('name' => $name, 'type' => $type)));
		
		$tmp = array();
		if($this->request->input('add')) {
			$tmp[] = array(
				'id' => $name,
				'text' => "$name - New Source"
			);
		}
		
		foreach($results as $res) {
			$tmp[] = array('id' => $res->id, 'text' => $res->name);
		}
		
		return json_encode($tmp);
	}

	public function get_image() {
		$id = $this->request->input('id');
		$creative = Creative::find($id);
		return "<img src='".$creative->getPublicThumbUrl()."'>";
	}

	public function search_rule_types() {

		$name = $this->request->input('name');
		$type = $this->request->input('type');
		$results = array();

		if($this->request->input('add')) {
			$results[] = array(
				'id' => "R|$name",
				'text' => "$name - A valid URL"
			);
		}

		if($this->request->input('type') == 'campaign' || $this->request->input('type') == 'all') {
			$campaigns = Campaign::get_campaigns($this->user, array('search' => array('id' => $name, 'name' => $name)));
			foreach($campaigns as $res) {
				$results[] = array(	
					'id' => "{$res->id}", 
					'text' => "({$res->id}) {$res->name} - Campaign"
				);
			}
		}
		
		if($this->request->input('type') == 'offer' || $this->request->input('type') == 'all') {
			$offers = Offer::get_offers($this->user, array('search' => array('id' => $name, 'name' => $name)));
			foreach($offers as $res) {
				$results[] = array(	
					'id' => "{$res->id}", 
					'text' => "({$res->id}) {$res->name} - Offer"
				);
			}
		}

		if($this->request->input('type') == 'service' || $this->request->input('type') == 'all') {
			$services = Service::get_services($this->user, array('search' => array('id' => $name, 'name' => $name)));
			foreach($services as $res) {
				$results[] = array(	
					'id' => "{$res->id}", 
					'text' => "({$res->id}) {$res->name} - Service"
				);
			}
		}

		if($this->request->input('type') == 'creative' || $this->request->input('type') == 'all') {
			$creatives = Creative::get_creatives($this->user, array('search' => array('id' => $name, 'name' => $name)));
			foreach($creatives as $res) {
				$results[] = array(	
					'id' => "{$res->id}", 
					'text' => "({$res->id}) {$res->name} - Creative"
				);
			}
		}

		return json_encode($results);
	}

	public function search_regions() {

		$country = $this->request->input('country');

		$results = array();	
		$regions = $this->get_regions($country);

		foreach($regions as $key => $value) {
			$results[] = array('id' => $key, 'text' => $value);
		}
		
		return json_encode($results);
	}

	public function search_cities() {

		$country = $this->request->input('country');
		$region = $this->request->input('region');

		$results = array();	
		$cities = $this->get_cities($country, $region);

		foreach($cities as $key => $value) {
			$results[] = array('id' => $key, 'text' => $value);
		}
		
		return json_encode($results);
	}

	public function update_rule() {
		$key = $this->request->input('key');
		$value = $this->request->input('value');

		$rule = Rule::find($this->request->input('id'));
		$allowed_keys = array('weight','secure','framed','path_forwarding','qstring_forwarding','hide_referrer','active');
		if(!$rule->is_owner($this->user)) {
			return "0";	
		}

		if(in_array($key, $allowed_keys)) {

			if($key == 'active') {
				$rule->active = $value;
				if($value == 0) {
					$rule->weight = 0;
				} else {
					$rule->weight = 1;
				}
			} elseif ($key == 'weight') {
				$rule->weight = $value;
				if($value == 0) {
					$rule->active = 0;
				} else {
					$rule->active = 1;
				}	
			} else {
				$rule->$key = $value;
			}
			$rule->save();
		}

		return "1";
	}

	public function switch_menu() {
		$quick_menu = $this->user->get_settings('pref_quick_menu');
		
		if($quick_menu == 0) {
			$quick_menu = 1;
		} else {
			$quick_menu = 0;
		}
		
		$this->user->set_settings(array('pref_quick_menu' => $quick_menu));
		return "1";
	}

	public function realtime() {
		// Set the JSON header
		header("Content-type: text/json");
		$time = time() * 1000;
		
		//if(env('APP_ENV') == 'local') {		
			//$ret = array($time, rand(0, 100));
		//} else {
			$visits = $this->report->realtime($this->user);
			$ret = array($time, (int)$visits);
		//}

		return json_encode($ret);		
	}
}
