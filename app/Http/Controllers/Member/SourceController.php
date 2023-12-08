<?php namespace App\Http\Controllers\Member;

use App\Helpers\TableMap;
use App\Http\Controllers\MemberController;
use App\Source;
use Illuminate\Http\Request;

class SourceController extends MemberController {

	public function index() {
		$view = $this->request->input('view');

		if (!isset($this->source_type)) {
			$this->source_type = 'domain';
			$this->source_type_name = 'Domain';
		}

		if ($this->source_type == 'domain') {
			$this->header_icon = '<i class="fa fa-puzzle-piece"></i>';
		} else {
			$this->header_icon = '<i class="fa fa-signal"></i>';
		}

		switch ($view) {
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
			'icon' => $this->header_icon,
			'title' => ucfirst($this->source_type),
			'desc' => "New {$this->source_type_name}",
		);

		return view('member.source.new', ['header' => $header, 'source_type' => $this->source_type, 'source_name' => $this->source_type_name]);
	}

	private function save() {
		$header = array(
			'icon' => $this->header_icon,
			'title' => ucfirst($this->source_type),
			'desc' => "Summary of added {$this->source_type_name}s",
		);

		$list = explode("\n", str_replace(" ", "\n", $this->request->input("source")["list"]));

		$service_id = 0; //$this->request->input("source")["service_id"];
		$project_id = $this->request->input("source")["project_id"];
		$type = $this->request->input("source")["type"];

		$results = Source::bulk_save($this->user, $list, $service_id, $project_id, $type);
		return view('member.source.summary', ['header' => $header, 'results' => $results]);
	}

	private function update() {
		$result = Source::save_source($this->user, $this->request->input("source"));
		if (!$result) {
			die('Save failed');
		}

		$source_id = $this->request->input("source")['id'];
		return redirect("/member/{$this->source_type}/?view=view&id={$source_id}&msg=saved");
	}

	private function view() {
		$source = Source::find($this->request->input("id"));
		if (is_null($source) || !$source->is_owner($this->user)) {
			return redirect("/member/{$this->source_type}/?view=manage&msg=notfound");
		}

		$traffic_stats = $this->report->get_traffic_by_day($this->user, $this->source_type, $source->id);
		$dns_stats = $this->report->get_dns_by_day($source->name);

		$dashboard_boxes = $this->dashboard_view('boxes', $traffic_stats);
		$dashboard_daily = $this->dashboard_view('daily', $traffic_stats);
		//$dashboard_daily_dns = $this->dashboard_view('daily-dns', $dns_stats);

		$header = array(
			'icon' => $this->header_icon,
			'title' => strtoupper($source->name),
			'desc' => 'View ' . $this->source_type_name,
		);

		$view = ['header' => $header, 'source' => $source, 'dashboard_boxes' => $dashboard_boxes, 'dashboard_daily' => $dashboard_daily]; //, 'dashboard_daily_dns' => $dashboard_daily_dns];

		$rules = $this->display_rules($source);
		$view = array_merge($view, $rules);

		$dns = $this->display_dns($source);
		$view = array_merge($view, $dns);

		return view('member.source.view', $view);
	}

	private function delete() {
		$source = Source::find($this->request->input("id"));
		if (is_null($source) || !$source->is_owner($this->user)) {
			return redirect("/member/{$this->source_type}/?view=manage&msg=notfound");
		}

		$source->delete_source();
		return redirect("/member/{$this->source_type}/?view=manage&msg=deleted");
	}

	public function manage() {
		$header = array(
			'icon' => $this->header_icon,
			'title' => ucfirst($this->source_type),
			'desc' => "Manage {$this->source_type_name}s",
		);

		$search = array(
			'type' => $this->source_type,
		);

		$search = array_merge((array) $this->request->input('search'), $search);
		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $search,
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = Source::get_sources($this->user, $params);

		$table['params'] = array(
			'table_id' => 'table-sources',
			'action_url' => "/member/{$this->source_type}/?view=manage",
		);

		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id',
				'linkto' => array('url' => "/member/{$this->source_type}/?view=view&id={VALUE}", 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name',
				'linkto' => array('url' => "/member/{$this->source_type}/?view=view&id={VALUE}", 'value_field' => 'id'),
			),
			'Project' => array('field' => 'project_name', 'if_empty' => 'None'),
			/*'Service' => array(
				'field' => 'service_name',
				'linkto' => array('url' => '/member/service/?view=view&id={VALUE}', 'value_field' => 'service_id'),
				'if_empty' => 'None'
			),*/
			//'Visits' => array('field' => 'visits', 'format' => 'number'),
			//'Pageviews' => array('field' => 'pageviews', 'format' => 'number'),
			'DNS' => array(
				'html' => array(
					'html' => "{DNS}",
					'value_field' => array(
						'DNS' => 'dns_records',
					),
					'class' => 'col-xs-3',
				),
				'sort_field' => 'dns_records',
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/{$this->source_type}/?view=view&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete?', function(result) { if (result===true) { window.location.href='/member/{$this->source_type}/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1',
				),
			),
		);

		//if($this->source_type == 'traffic') {
		unset($this->descriptor['Service']);
		//}

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}
}

?>