<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\User;
use App\MonitorList;
use App\Helpers\TableMap;

class MonitorController extends MemberController 
{
	public function index() {
		$view = $this->request->input('view');
		switch($view) {
			case 'new':
				return $this->create();
				break;
			default:
				return $this->$view();
				break;
		}
	}

	private function create() {
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Monitor List',
			'desc' => "New Monitors"
		);

		return view('member.monitor.new', ['header' => $header]);
	}

	private function save() {
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Monitor List',
			'desc' => "Summary of Added Monitors"
		);

		$monitor = $this->request->input("monitor");
		$list = explode("\n", str_replace(" ", "\n", $this->request->input("monitor")["list"]));
		$monitor['list'] = $list;

		$results = MonitorList::bulk_save($this->user, $monitor);
		return view('member.monitor.summary', ['header' => $header, 'results' => $results]);
	}

	private function update() {
		$result = MonitorList::save_monitor($this->user, $this->request->input("monitor"));
		if(!$result) {
			die('Save failed');	
		}

		$monitor_id = $this->request->input("monitor")['id'];
		return redirect("/member/monitor/?view=view&id={$monitor_id}&msg=saved");
	}
	
	private function view() {
		$monitor = MonitorList::find($this->request->input("id"));
		if(is_null($monitor) || !$monitor->is_owner($this->user)) {
			return redirect("/member/monitor/?view=manage&msg=notfound");
		}
		
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Monitor',
			'desc' => 'View '.$monitor->url
		);
		
		$view = ['header' => $header, 'monitor' => $monitor];
		return view('member.monitor.view', $view);
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-exchange"></i>',
			'title' => 'Monitors',
			'desc' => "Manage Monitors"
		);
			
		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => (array)$this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);

		$table['results'] = MonitorList::get_monitor_list($this->user, $params);
		
		$table['params'] = array(
			'table_id' => 'table-sources',
			'action_url' => "/member/monitor/?view=manage",
		);
		
		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => "/member/monitor/?view=view&id={VALUE}", 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'url', 
				'linkto' => array('url' => "/member/monitor/?view=view&id={VALUE}", 'value_field' => 'id'),
			),
			'Alert' => array(
				'field' => 'alert', 
			),
			'Email' => array(
				'field' => 'email', 
			),
			'SMS' => array(
				'field' => 'sms', 
			),
			'Status' => array(
				'html' => array(
					'html' => "",
					'value_field' => array('STATUS' => 'status'),
					'value_cases' => array(
						'status' => array(
							'ok' => '<span class="btn btn-success btn-icon fa fa-thumbs-up"></span>',
							'flagged' => "<span class='btn btn-danger btn-icon btn-circle fa fa-times'></span>",
							'unknown' => "<span class='btn btn-warning btn-icon btn-circle fa fa-exclamation-triangle'></span>",
						)
					),
				),
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}	
}
?>