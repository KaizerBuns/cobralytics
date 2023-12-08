<?php namespace App\Http\Controllers\Member;

use App\Helpers\TableMap;
use App\Http\Controllers\MemberController;
use App\Offer;
use App\Service;
use Illuminate\Http\Request;

class ServiceController extends MemberController {

	public function index() {
		if (!$this->user->is_admin()) {
			return redirect("/member/project/?view=manage&msg=denied");
		}

		$view = $this->request->input('view');

		switch ($view) {
		case 'new':
			return $this->create();
			break;
		default:
			return $this->$view();
			break;
		}
	}

	public function create() {
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Services',
			'desc' => 'New Service',
		);

		$offer_groups = Offer::get_offer_groups($this->user);
		return view('member.service.new', ['header' => $header, 'offer_groups' => $offer_groups]);
	}

	public function save() {
		$service = Service::save_service($this->user, $this->request->input("service"));

		if (is_null($service)) {
			die('Saved failed');
		}

		return redirect("/member/service/?view=view&id={$service->id}");
	}

	public function delete() {
		$service = new Service();
		$result = $service->delete_service($this->user, $this->request->input("id"));
		if (!$result) {
			return redirect("/member/service/?view=manage&msg=nofound");
		}
		return redirect("/member/service/?view=manage&msg=deleted");
	}

	public function view() {
		$service = Service::find($this->request->input("id"));

		if (!$service->is_owner($this->user)) {
			return redirect("/member/service/?view=manage&msg=notfound");
		}

		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => strtoupper($service->name),
			'desc' => 'View Service',
		);

		$traffic_stats = $this->report->get_traffic_by_day($this->user, 'service', $service->id);
		$dashboard_boxes = $this->dashboard_view('boxes', $traffic_stats);
		$dashboard_daily = $this->dashboard_view('daily', $traffic_stats);

		$view = ['header' => $header, 'service' => $service, 'dashboard_boxes' => $dashboard_boxes, 'dashboard_daily' => $dashboard_daily];
		$rules = $this->display_rules($service);

		return view('member.service.view', array_merge($view, $rules));
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Services',
			'desc' => 'Manage Services',
		);

		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = Service::get_services($this->user, $params);

		$table['params'] = array(
			'table_id' => 'table-services',
			'action_url' => "/member/service/?view=manage",
		);

		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id',
				'linkto' => array('url' => '/member/service/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name_optimizer',
				'linkto' => array('url' => '/member/service/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Rules' => array('field' => 'rule_count'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/service/?view=view&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Service?', function(result) { if (result===true) { window.location.href='/member/service/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1',
				),
			),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}
}
?>