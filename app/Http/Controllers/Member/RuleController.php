<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Rule;
use App\Helpers\TableMap;

class RuleController extends MemberController {
	
	public function index() {
		$view = $this->request->input('view');
		switch($view) {
			case 'new':
			case 'edit':
				return $this->details();
				break;
			case 'save':
				return $this->save();
				break;
			case 'json':
				$rule = Rule::get_rule($this->request->input('id'));
				return json_encode($rule);
				break;
			case 'delete':
				return $this->delete();
				break;
		}
	}

	public function save() {
		$result = Rule::save_rule($this->request->input('rule'));				
		$url = "/member/".$this->request->input('rule')["rule_type"]."/?view=view&id=".$this->request->input('rule')["rule_type_id"];
		if($result) {
			$url.="&msg=saved";
		} else {
			$url.="&msg=unknown_error";
		}
		
		return redirect($url);
	}

	public function delete() {
		if($this->request->input('id')) {
			$result = Rule::delete_rule($this->user, $this->request->input('id'));	
		} else {
			$rule_ids = explode(",", urldecode($this->request->input('ids')));
			$result = Rule::delete_rule($this->user, $rule_ids);
		}

		$url = "/member/".$this->request->input('type')."/?view=view&id=".$this->request->input('type_id');
		if($result) {
			$url.="&msg=saved";
		} else {
			$url.="&msg=unknown_error";
		}

		return redirect($url);
	}

	public function details() {
		$rule_countries = $this->get_countries();
		$rule_regions = $this->get_regions();
		$rule_cities = $this->get_cities();
		$rule_agents = $this->get_user_agents();

		$object = Rule::get_object($this->request->input('type'), $this->request->input('type_id'));
		$header = array(
			'title' => 'Rule',
			'desc' => 'New Rule'
		);

		$view_object = 'Rule';
		if($object->get_type() == 'offer') {
			$view_object = 'Landing Page';	
		}

		$rule = new Rule();
		if($this->request->input('view') == 'edit') {
			if($this->request->input('bulk')) {
				$header['desc'] = "[".ucfirst($object->get_type())." - ".$object->get_name()."] Edit Bulk Rules";
			} else {
				$rule = Rule::find($this->request->input('id'));
				$header['desc'] = "[".ucfirst($object->get_type())." - ".$object->get_name()."] Edit Rule";
			}
		} else {
			$header['desc'] = "[".ucfirst($object->get_type())." - ".$object->get_name()."] New Rule";
		}

		$params = array(
			'header' => $header, 
			'rule' => $rule, 
			'view_object' => $view_object, 
			'object' => $object, 
			'rule_countries' => $rule_countries, 
			'rule_regions' => $rule_regions, 
			'rule_agents' => $rule_agents, 
			'rule_cities' => $rule_cities,
			'bulk' => $this->request->input('bulk'),
			'bulk_ids' => $this->request->input('ids'),
			'rotator' => $this->request->input('rotator')
		);

		return view('member.rule.new', $params);
	}

}
?>