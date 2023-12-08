<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Project;
use App\Helpers\TableMap;
use App\Helpers\ReportCobra;
use App\Helpers\ReportClickhouse;

class DashboardController extends MemberController {

	public function index() {
		if($this->user->has_first_project() == false) {
			return redirect('/member/project/?view=new&msg=newproject');
		}

		$header = array(
			'icon' => '<i class="fa fa-dashboard"></i>',
			'title' => 'Dashboard',
			'desc' => ''
		);

		$country_stats = $this->report->get_top_countries($this->user);
		$daily_stats = $this->report->get_traffic_by_day($this->user);

		$views = array(
			'header' => $header,
			'dashboard_realtime' => $this->dashboard_view('realtime'),
			'dashboard_daily' => $this->dashboard_view('daily', $daily_stats),
			'dashboard_boxes' => $this->dashboard_view('boxes', $daily_stats),
			'dashboard_country' => $this->dashboard_view('country', $country_stats),
			'dashboard_sources' => $this->dashboard_view('sources')
		);
		
		return view('member.dashboard.view', $views);
	}
}

?>