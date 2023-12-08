<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Helpers\TableMap;
use App\Helpers\ReportCobra;

class ReportController extends MemberController {

	public function index() {
		$view = $this->request->input('view');
		return $this->$view();
	}

	private function custom() {
		if(!$this->request->input('submit')) {
			$_REQUEST['report']['start'] = date("Y-m-d", strtotime("-7 days"));
			$_REQUEST['report']['end'] = date("Y-m-d");
		}
						
		$report = $this->report->custom($this->user, array('report' => $this->request->input('report'), 'limit' => $this->user->pref_page_limit, 'page' => $this->page_number));
						
		$report_header = date("M j, Y", strtotime($_REQUEST['report']['start'])).' to '.date('M j, Y', strtotime($_REQUEST['report']['end']));				
		$header = array(
			'icon' => '<i class="fa fa-bar-chart-o"></i>',
			'title' => 'Report',
			'desc' => 'Custom Report - ' . $report_header
		);
						
		$tablemap = TableMap::create($report['results'], $report['descriptor'], $report['params']);
		return view('member.report.custom', ['header' => $header, 'tablemap' => $tablemap, 'countries' => $this->get_countries()]);
	}

	private function overview() {
		$header = array(
			'icon' => '<i class="fa fa-bar-chart-o"></i>',
			'title' => 'Overview Report',
			'desc' => '',
		);

		if(is_null($this->request->input('report'))) {
			$default = array(
				'start' => date("Y-m-d", strtotime("-7 days")),
				'end' => date('Y-m-d')
			);
			$this->request->merge(array('report' => $default));
		}
		
		$tab = $this->request->input('tab');

		$params = array(
			'report' => $this->request->input('report'),
			'filter' => $this->request->input('filter'),
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);	

		$results = $this->report->get_stats_overview($this->user, $tab, $params);

		$dashboard_boxes = $this->dashboard_view('boxes', $results['stats_by_day']);
		$dashboard_daily = $this->dashboard_view('daily-simple', $results['stats_by_day']);
										
		$tablemap = TableMap::create($results['stats'], $results['descriptor'], $results['params']);
		$query_string = "/member/report/?view=overview&tab=%TAB%&".$results['query_string'];
		$breadcrumbs = $results['breadcrumbs'];
		
		return view('member.report.overview', ['header' => $header,'tablemap' => $tablemap, 'query_string' => $query_string, 'breadcrumbs' => $breadcrumbs, 'dashboard_boxes' => $dashboard_boxes, 'dashboard_daily' => $dashboard_daily]);
	}
}