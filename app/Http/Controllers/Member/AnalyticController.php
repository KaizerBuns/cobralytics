<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Advertiser;
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
use App\Helpers\GAHelper;

class AnalyticController extends MemberController {

	public function index() {
		$header = array(
			'icon' => '<i class="fa fa-bar-chart-o"></i>',
			'title' => 'Analytics Report',
			'desc' => 'Access your google analytics reports',
		);

		$ga = new GAHelper('http://www.crazystylelove.com');

		$ga->getCustomReport();
		die;

		$stats = array(
			'visitors' => $ga->getVisitors(),
			'keywords' => $ga->getKeywords(),
			'referers' => $ga->getTopReferrers(),
			'browsers' => $ga->getTopBrowsers(),
			'pages' => $ga->getTopPages(),
			'actives' => $ga->getActiveUsers()
		);
		
		return view('member.analytics.dashboard', ['header' => $header, 'stats' => $stats]);
	}
}

?>