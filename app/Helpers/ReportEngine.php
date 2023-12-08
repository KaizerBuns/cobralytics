<?php namespace App\Helpers;

use App\Campaign;
use App\Helpers\MyHelper;
use App\Offer;
use App\Project;
use App\Rule;
use App\Source;
use App\User;
use Crate\PDO\PDO as PDO;
use DB;

class ReportEngine {

	public function __construct() {
		if (env('APP_ENV') == 'production' || env('APP_REPORT') == 'mysql') {
			$this->stats_table = 'stats_visitor_actions';
		} else {
			$this->stats_table = 'stats_visitor_actions';
		}

		if (env('APP_REPORT') == 'cratedb') {
			$dsn = 'crate:67.205.133.220:4200';
			$this->cratepdo = new PDO($dsn, null, null, null);
		}
	}

	public function realtime(User $user) {
		$date = date('Y-m-d');
		$date_time = date('Y-m-d H:00:00');
		$real_time = date("Y-m-d H:i:00");
		$project_id = $user->get_default_project();

		if (env('APP_REPORT') == 'cratedb') {

			$sql = "SELECT sum(visits) as visitors
				FROM {$this->stats_table}
				WHERE visit_time = '" . date("c", strtotime($real_time)) . "'
				AND user_id = {$user->id}";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results = $qry->fetch(PDO::FETCH_OBJ);
		} else {

			$sql = "SELECT sum(visits) as visitors
				FROM {$this->stats_table}
				WHERE visit_date = '{$date}'
				AND visit_time = '{$real_time}'
				AND user_id = {$user->id}";
			$results = DB::select(DB::raw($sql), array());
			$results = array_shift($results);
		}

		if (isset($results->visitors)) {
			return $results->visitors;
		}

		return 0;
	}

	public function custom(User $user, $params = array()) {
		$where = array();
		$column = array();
		$group_by = array();

		$limit = (isset($params['limit']) ? $params['limit'] : $user->pref_page_limit);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$search = (isset($params['report']) ? (array) $params['report'] : array());

		$query_string = http_build_query($search);
		
		if (!isset($search['start'])) {
			$search['start'] = date("Y-m-d");
		}

		if (!isset($search['end'])) {
			$search['end'] = date("Y-m-d");
		}

		//$where['project_id'] = "project_id = ".$user->get_default_project();
		$where['user_id'] = "user_id = {$user->id}";

		if (env('APP_REPORT') == 'cratedb') {
			if ($search['start'] == $search['end']) {
				$where['date'] = "visit_date = '{$search['start']}'";
			} else {
				$where['date'] = "visit_date BETWEEN '{$search['start']}' AND '{$search['end']}'";
			}
		} else {
			if ($search['start'] == $search['end']) {
				$where['date'] = "visit_date = '{$search['start']}'";
			} else {
				$where['date'] = "visit_date BETWEEN '{$search['start']}' AND '{$search['end']}'";
			}
		}

		//Build TableMAP
		if (isset($search['campaign_id']) && $search['campaign_id']) {
			$where['campaign_id'] = "campaign_id IN ({$search['campaign_id']})";
			$search['groupby']['campaign'] = 1;
		}

		if (isset($search['offer_id']) && $search['offer_id']) {
			$where['offer_id'] = "offer_id IN ({$search['offer_id']})";
			$search['groupby']['offer'] = 1;
		}

		if (isset($search['source_id']) && $search['source_id']) {
			$where['source_id'] = "source_id IN ({$search['source_id']})";
			$search['groupby']['source'] = 1;
		}

		if (isset($search['domain_id']) && $search['domain_id']) {
			$where['domain_id'] = "domain_id IN ({$search['domain_id']})";
			$search['groupby']['domain'] = 1;
		}

		if (isset($search['traffic_type']) && $search['traffic_type']) {
			$traffic_type = implode("','", $search['traffic_type']);
			$where['traffic_type'] = "traffic_type IN ('{$traffic_type}')";
			$column['traffic_type'] = "traffic_type";
			$group_by['traffic_type'] = "traffic_type";

			$descriptor['Traffic Type'] = array('field' => 'traffic_type');
		}

		//Build Group BY
		if (isset($search['groupby']['date']) && $search['groupby']['date']) {
			$column['date'] = "visit_date";
			$group_by['date'] = 'visit_date';

			$descriptor['Date'] = array('field' => 'visit_date', 'format' => 'nice-date');
		}

		if (isset($search['groupby']['campaign']) && $search['groupby']['campaign']) {
			$column['campaign_id'] = "campaign_id, campaign_name";
			$group_by['campaign_id'] = "campaign_id, campaign_name";

			$descriptor['Campaign'] = array('field' => 'campaign_name', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['offer']) && $search['groupby']['offer']) {
			$column['offer_id'] = "offer_id, offer_name";
			$group_by['offer_id'] = "offer_id, offer_name";

			$descriptor['Offer'] = array('field' => 'offer_name', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['source']) && $search['groupby']['source']) {
			$column['source_id'] = "source_id, source_name";
			$group_by['source_id'] = "source_id, source_name";

			$descriptor['Source'] = array('field' => 'source_name', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['domain']) && $search['groupby']['domain']) {
			$column['domain_id'] = "domain_id, domain_name";
			$group_by['domain_id'] = "domain_id, domain_name";

			$descriptor['Domain'] = array('field' => 'domain_name', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['traffic_type']) && $search['groupby']['traffic_type']) {
			$column['traffic_type'] = "traffic_type";
			$group_by['traffic_type'] = "traffic_type";

			$descriptor['TrafficType'] = array('field' => 'traffic_type', 'if_empty' => 'unknown');
		}

		if (isset($search['groupby']['device']) && $search['groupby']['device']) {
			$column['device'] = "concat(platform,' ',browser) as device";
			$group_by['device'] = "platform, browser";

			$descriptor['Device'] = array('field' => 'device', 'if_empty' => 'unknown');
		}

		if (isset($search['groupby']['referrers']) && $search['groupby']['referrers']) {
			$column['referrers'] = "referer";
			$group_by['referrers'] = "referer";

			$descriptor['Referer'] = array('field' => 'referrers', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['subids']) && $search['groupby']['subids']) {
			$column['subids'] = "sub1, sub2, sub3, sub4, sub5";
			$group_by['subids'] = "sub1, sub2, sub3, sub4, sub5";

			$descriptor['SubID1'] = array('field' => 'sub1', 'if_empty' => 'none');
			$descriptor['SubID2'] = array('field' => 'sub2', 'if_empty' => 'none');
			$descriptor['SubID3'] = array('field' => 'sub3', 'if_empty' => 'none');
			$descriptor['SubID4'] = array('field' => 'sub4', 'if_empty' => 'none');
			$descriptor['SubID5'] = array('field' => 'sub5', 'if_empty' => 'none');
		}

		if (isset($search['groupby']['country']) && $search['groupby']['country']) {
			$column['country'] = "country";
			$group_by['country'] = "country";

			$descriptor['Country'] = array('field' => 'country', 'if_empty' => 'unknown');
		}

		if (isset($group_by['date']) && count($group_by) == 1) {
			unset($descriptor['Name']);
		} else {
			//fail sale
			if (count($group_by) == 0) {
				if (env('APP_REPORT') == 'cratedb') {
					$group_by = array('visit_date');
				} else {
					$group_by = array('visit_date');
				}
			}
		}

		$descriptor['Visitors'] = array('field' => 'visitors', 'enable_totals' => 1, 'custom_totals' => 'visitors');
		$descriptor['Uniques'] = array('field' => 'uniques', 'enable_totals' => 1, 'custom_totals' => 'uniques');
		$descriptor['Clicks'] = array('field' => 'clicks', 'enable_totals' => 1, 'custom_totals' => 'clicks');
		$descriptor['Conversions'] = array('field' => 'conversions', 'enable_totals' => 1, 'custom_totals' => 'conversions');
		$descriptor['Revenue'] = array('field' => 'revenue', 'format' => 'money', 'enable_totals' => 1, 'custom_totals' => 'revenue');
		$descriptor['cRatio'] = array('field' => 'ratio', 'enable_totals' => 1, 'custom_totals' => 'ratio');

		//Define columns to calculate
		$calc = array(
			"sum(visits) visitors",
			"count(Distinct(visitor_id)) uniques",
			"sum(clicks) clicks",
			"sum(conversions) conversions",
			"sum(revenue) revenue",
			"sum(conversions)/sum(visits) ratio",
		);

		$columns = implode(",", array_merge($column, $calc));

		if (env('APP_REPORT') == 'cratedb') {
			//Normal SQL
			$sql = "SELECT {$columns} FROM {$this->stats_table} WHERE ";
			$sql .= implode(" AND ", $where);
			$sql .= " GROUP BY " . implode(",", $group_by);

			//Paginate
			$offset = (($page - 1) * $limit);
			$sql .= " LIMIT {$offset}, {$limit}";

			//Run Normal Query
			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results = $qry->fetchAll(PDO::FETCH_OBJ);

			//Run Totals Query
			$sql_total = "SELECT " . implode(",", $calc) . " FROM {$this->stats_table} WHERE " . implode(" AND ", $where) . " LIMIT 1";
			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$totals = $qry->fetch(PDO::FETCH_OBJ);
		} else {
			//Normal SQL
			$sql = "SELECT {$columns} FROM {$this->stats_table} WHERE ";
			$sql .= implode(" AND ", $where);
			$sql .= " GROUP BY " . implode(",", $group_by);

			//Paginate
			$offset = (($page - 1) * $limit);
			$sql .= " LIMIT {$offset}, {$limit}";

			//Run Normal Query
			$results = DB::select(DB::raw($sql), array());

			//Run Totals Query
			$sql_total = "SELECT " . implode(",", $calc) . " FROM {$this->stats_table} WHERE " . implode(" AND ", $where) . " LIMIT 1";
			$totals = DB::select(DB::raw($sql_total), array());
			$totals = array_shift($totals);
		}

		$params = array(
			'table_id' => 'table-custom-report',
			'action_url' => "/member/report/?view=custom&{$query_string}&submit=1",
			'enable_totals' => true,
			'custom_totals' => (array) $totals
		);

		$report = array(
			'results' => $results,
			'descriptor' => $descriptor,
			'params' => $params,
		);

		return $report;
	}

	public function get_dns_by_day($domain) {
		$start_date = date("Y-m-d", strtotime("-7 days"));
		$end_date = date("Y-m-d");

		if (env('APP_REPORT') == 'cratedb') {
			$sql = "SELECT
						visit_date as date,
						count(*) total_requests
					FROM stats_powerdns
					WHERE visit_time BETWEEN '" . date("c", strtotime($start_date . " 00:00:00")) . "' AND '" . date("c", strtotime($end_date . " 23:59:59")) . "'
					AND (lower(domain) = '{$domain}' OR lower(domain) = 'www.{$domain}')
					GROUP BY visit_date";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$stats_by_day = $qry->fetchAll(PDO::FETCH_OBJ);
			$stats_by_day = MyHelper::rekey_array((array) $stats_by_day, 'date');
		} else {
			$sql = "SELECT
					visit_date as date,
					count(id) total_requests
				FROM stats_powerdns
				WHERE visit_date BETWEEN '{$start_date}' AND '{$end_date}'
				AND (lower(domain) = '{$domain}' OR lower(domain) = 'www.{$domain}')
				GROUP BY visit_date";

			$stats_by_day = array();
			//$stats_by_day = DB::select( DB::raw($sql));
			//$stats_by_day = MyHelper::rekey_array((array)$stats_by_day, 'date');
		}

		$tmp = array();
		$current_date = $start_date;
		while ($current_date <= $end_date) {

			if (isset($stats_by_day[$current_date])) {
				$tmp[$current_date] = $stats_by_day[$current_date];
			} else {
				$tmp[$current_date] = (object) array(
					'date' => $current_date,
					'total_requests' => 0,
				);
			}
			$current_date = date("Y-m-d", strtotime("{$current_date} +1 day"));
		}

		$stats_by_day = $tmp;
		return $stats_by_day;
	}

	public function get_stats_overview(User $user, $tab = 'campaign', $params = array()) {
		$report = $params['report'];
		$filter = $params['filter'];
		$limit = (isset($params['limit']) ? $params['limit'] : 25);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'visitors');
		$order = (isset($params['order']) ? (int) $params['order'] : 1);
		$left_join = "";

		$filter_on = (count($params['filter']) > 0 ? true : false);
		$start_date = date("Y-m-d", strtotime("today"));
		if (isset($report['start'])) {
			$start_date = $report['start'];
		}

		$end_date = date("Y-m-d");
		if (isset($report['end'])) {
			$end_date = $report['end'];
		}

		//replace {EDITLINK}
		$edit_link = "<a href=\"/member/{TAB}/?view=view&id={ID}\" target=\"_new\" title=\"View {NAME}\" class=\"cmd-tip\"><i class=\"fa fa-search-plus\"></i></a>&nbsp;";
		$map_descriptor = array();
		$map_descriptor['Name'] = array('html' => array(
			'html' => "<div style=\"word-break:break-all;\">{EDIT_LINK}<a href=\"/member/report/?view=overview&tab={TAB}&{QUERY_STRING}&filter[$tab]={ID}\">({ID}) {NAME}</a></div>",
			'value_field' => array('ID' => 'id', 'NAME' => 'name'),
			'if_empty' => 'Unknown',
		), 'not_sortable' => true);

		$where = array();
		if (env('APP_REPORT') == 'cratedb') {
			$where['daterange'] = "visit_time BETWEEN '" . date("c", strtotime($start_date . " 00:00:00")) . "' AND '" . date("c", strtotime($end_date . " 23:59:59")) . "'";
		} else {
			$where['daterange'] = "visit_date BETWEEN '{$start_date}' AND '{$end_date}'";
		}

		//$where['projectid'] = "project_id = ".$user->get_default_project();
		$where['userid'] = "user_id = {$user->id}";

		switch ($tab) {
		default:
		case 'project':
			$columns = "project_id as id, project_name as name";
			$group_by = "project_id, project_name";
			break;
		case 'campaign':
			if ($filter_on) {
				if (isset($filter['source'])) {
					$where['source_id'] = "source_id = {$filter['source']}";
				}
			}

			$columns = "campaign_id as id, campaign_name as name";
			$group_by = "campaign_id, campaign_name";
			break;
		case 'domain':
			$columns = "domain_id as id, domain_name as name";
			$group_by = "domain_id, domain_name";
			break;
		case 'source':
			$columns = "source_id as id, source_name as name";
			$group_by = "source_id, source_name";
			break;
		case 'traffic':
			$columns = "traffic_type as id, traffic_type as name";
			$group_by = "traffic_type";

			//overrides
			$edit_link = "";
			break;
		case 'offer':
			$columns = "offer_id as id, offer_name as name";
			$group_by = "offer_id, offer_name";

			//overrides
			break;
		case 'lp': //landing pages
			$columns = "rule_id as id, rule_name as name";
			$group_by = "rule_id, rule_name";

			//overrides
			$edit_link = "";
			break;
		case 'ref':
			$columns = "referer as id, referer as name";
			$group_by = "referer";

			//overrides
			$descriptor['Name']['html']['if_empty'] = "Direct/Bookmark";
			$edit_link = "";
			break;
		case 'device':
			$columns = "concat(platform,' ',browser) as id, concat(platform,' ',browser) as name";
			$group_by = "platform, browser";

			//overrides
			$descriptor['Name']['html']['if_empty'] = "Uknown Device";
			$edit_link = "";
			break;
		case 'country':
			$columns = "country as id, country_name as name";
			$group_by = "country, country_name";

			//overrides
			$edit_link = "";
			break;
		case 'sub1':
			$columns = "sub1 as id, sub1 as name";
			$group_by = "sub1";

			//overrides
			$edit_link = "";
			break;
		case 'day':
			$columns = "visit_date as id, visit_date as name";
			$group_by = "visit_date";

			//overrides
			$edit_link = "";
			break;
		case 'hour':
			if (env('APP_REPORT') == 'cratedb') {
				$columns = "extract(hour from visit_time) as id, concat(extract(hour from visit_time),':00') as name";
			} else {
				$columns = "hour(visit_time) as id, TIME_FORMAT(visit_time, '%H:%i') as name";
			}

			$group_by = "name";

			//overrides
			$map_descriptor['Name']['html']['value_field']['NAME'] = array('field' => 'name', 'format' => 'nice-time');
			$edit_link = "";
			break;
		}

		if (env('APP_REPORT') == 'cratedb') {
			$group_by = "id, name";
		}

		$visitor_count = "count(distinct visitor_id) uniques, sum(visits) visitors";
		$breadcrumbs = array();
		$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=project"><i class="fa fa-line-chart"></i> All Projects</a></li>';

		if ($filter_on) {
			$visitor_count = "count(distinct visitor_id) uniques, sum(visits) visitors";
			if (isset($filter['project'])) {
				$where['project_id'] = "project_id = {$filter['project']}";
				//Find Project
				$project = Project::find($filter['project']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=project&filter[project]=' . $filter['project'] . '">' . $project->name . '</a></li>';
			}

			if (isset($filter['campaign'])) {
				$where['campaign_id'] = "campaign_id = {$filter['campaign']}";
				//Find Campaign
				$campaign = Campaign::find($filter['campaign']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[campaign]=' . $filter['campaign'] . '">' . $campaign->name . '</a></li>';
			}

			if (isset($filter['domain'])) {
				$where['source_id'] = "domain_id = {$filter['domain']}";

				//Find Domain
				$domain = Source::find($filter['domain']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[domain]=' . $filter['domain'] . '">' . $domain->name . '</a></li>';
			}

			if (isset($filter['source'])) {
				$where['source_id'] = "source_id = {$filter['source']}";

				//Find Source
				$source = Source::find($filter['source']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[source]=' . $filter['source'] . '">' . $source->name . '</a></li>';
			}

			if (isset($filter['traffic'])) {
				$where['traffic'] = "traffic_type = '{$filter['traffic']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[traffic]=' . $filter['traffic'] . '">' . $filter['traffic'] . '</a></li>';
			}

			if (isset($filter['offer'])) {
				$where['offer'] = "offer_id = {$filter['offer']}";
				$offer = Offer::find($filter['offer']);

				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=offer&filter[offer]=' . $filter['offer'] . '">(' . $offer->id . ') ' . $offer->name . '</a></li>';
			}

			if (isset($filter['lp'])) {
				$where['lp'] = "rule_id = {$filter['lp']}";
				$rule = Rule::find($filter['lp']);

				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[lp]=' . $filter['lp'] . '">(' . $rule->name . ') ' . $rule->url . '</a></li>';
			}

			if (isset($filter['ref'])) {
				$where['referer'] = "referer = '{$filter['ref']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[ref]=' . $filter['ref'] . '">' . $filter['ref'] . '</a></li>';
			}

			if (isset($filter['page'])) {
				$where['page'] = "page = '{$filter['page']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[page]=' . $filter['page'] . '">' . $filter['page'] . '</a></li>';
			}

			if (isset($filter['device'])) {
				$where['device'] = "concat(platform,' ',browser) LIKE '%{$filter['device']}%'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[device]=' . $filter['device'] . '">' . $filter['device'] . '</a></li>';
			}

			if (isset($filter['country'])) {
				$where['country'] = "country = '{$filter['country']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[country]=' . $filter['country'] . '">' . $filter['country_name'] . '</a></li>';
			}

			if (isset($filter['sub1'])) {
				$where['country'] = "sub1 = '{$filter['sub1']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[subid]=' . $filter['subid'] . '">' . $filter['subid'] . '</a></li>';
			}

			if (isset($filter['day'])) {
				$params['report']['start'] = $params['report']['end'] = $filter['day'];
				$where['daterange'] = "visit_date = '{$filter['day']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[day]=' . $filter['day'] . '">' . $filter['day'] . '</a></li>';
			}

			if (isset($filter['hour'])) {
				$where['datetime'] = "hour(ts.date_time) = '{$filter['hour']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[hour]=' . $filter['hour'] . '">' . date("g:00a", strtotime("{$filter['hour']}:00")) . '</a></li>';
			}
		}

		$query_string = http_build_query($params);
		$map_descriptor['Name']['html']['html'] = str_replace('{EDIT_LINK}', $edit_link, $map_descriptor['Name']['html']['html']);
		$map_descriptor['Name']['html']['html'] = str_replace('{TAB}', $tab, $map_descriptor['Name']['html']['html']);
		$map_descriptor['Name']['html']['html'] = str_replace('{QUERY_STRING}', $query_string, $map_descriptor['Name']['html']['html']);

		if (env('APP_REPORT') == 'cratedb') {
			$sql = "SELECT
						{$columns},
						{$visitor_count},
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where);

			$sql .= " GROUP BY {$group_by}";
			$sql .= " ORDER BY {$sort} " . ($order ? 'DESC' : 'ASC');

			if ($limit > 0) {
				$offset = (($page - 1) * $limit);
				$sql .= " LIMIT {$limit} OFFSET $offset";
			}

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$stats = $qry->fetchAll(PDO::FETCH_OBJ);

			//Run Totals Query
			$sql_total = "SELECT
						{$visitor_count},
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . " LIMIT 1";

			$qry = $this->cratepdo->prepare($sql_total);
			$qry->execute();
			$totals = $qry->fetch(PDO::FETCH_OBJ);

			//$where['daterange'] = "(visit_time BETWEEN '".date("c", strtotime($start_date." 00:00:00"))."' AND '".date("c", strtotime($start_date." 23:59:59"))."' OR visit_date = '".date("Y-m-d")."')";
			$sql_by_day = "SELECT visit_date as date,
					{$visitor_count},
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where);

			$sql_by_day .= " GROUP BY date";
			$qry = $this->cratepdo->prepare($sql_by_day);
			$qry->execute();
			$stats_by_day = $qry->fetchAll(PDO::FETCH_OBJ);
			$stats_by_day = MyHelper::rekey_array((array) $stats_by_day, 'date');
		} else {
			$sql = "SELECT
						{$columns},
						{$visitor_count},
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where);

			$sql .= " GROUP BY {$group_by}";
			$sql .= " ORDER BY {$sort} " . ($order ? 'DESC' : 'ASC');

			if ($limit > 0) {
				$offset = (($page - 1) * $limit);
				$sql .= " LIMIT {$offset}, {$limit}";
			}

			$stats = DB::select(DB::raw($sql), array());

			//Run Totals Query
			$sql_total = "SELECT
						{$visitor_count},
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . " LIMIT 1";

			$totals = DB::select(DB::raw($sql_total), array());
			$totals = array_shift($totals);

			//$where['daterange'] = "(visit_date BETWEEN '{$start_date}' AND '{$end_date}' OR visit_date = '".date("Y-m-d")."')";
			$sql_by_day = "SELECT visit_date as date,
					{$visitor_count},
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where);

			$sql_by_day .= " GROUP BY visit_date";

			$stats_by_day = DB::select(DB::raw($sql_by_day), array());
			$stats_by_day = MyHelper::rekey_array((array) $stats_by_day, 'date');
		}

		$current_date = $start_date;
		while ($current_date <= $end_date) {
			if (!isset($stats_by_day[$current_date])) {
				$stats_by_day[$current_date] = (object) array(
					'date' => $current_date,
					'visitors' => 0,
					'hits' => 0,
					'clicks' => 0,
					'revenue' => 0,
					'uniques' => 0,
					'conversions' => 0,
					'ratio' => 0
				);
			}
			$current_date = date("Y-m-d", strtotime("{$current_date} +1 day"));
		}

		ksort($stats_by_day);

		$map_descriptor['Visitors'] = array('field' => 'visitors', 'enable_totals' => 1, 'custom_totals' => 'visitors');
		$map_descriptor['Uniques'] = array('field' => 'uniques', 'enable_totals' => 1, 'custom_totals' => 'uniques');
		$map_descriptor['Clicks'] = array('field' => 'clicks', 'enable_totals' => 1, 'custom_totals' => 'clicks');
		$map_descriptor['Conversions'] = array('field' => 'conversions', 'enable_totals' => 1, 'custom_totals' => 'conversions', 'if_empty' => 0);
		$map_descriptor['Revenue'] = array('field' => 'revenue', 'format' => 'money', 'enable_totals' => 1, 'custom_totals' => 'revenue');
		$map_descriptor['cRatio'] = array('field' => 'ratio', 'enable_totals' => 1, 'custom_totals' => 'ratio', 'if_empty' => 0);

		$map_params = array(
			'table_id' => 'table-breakdown',
			'enable_totals' => true,
			'custom_totals' => (array) $totals,
			'action_url' => "/member/report/?view=overview&tab=$tab&{$query_string}",
		);

		$results = array();
		$results['descriptor'] = $map_descriptor;
		$results['params'] = $map_params;
		$results['stats'] = $stats;
		$results['stats_by_day'] = $stats_by_day;
		$results['query_string'] = $query_string;

		$breadcrumb_html = "<ol class='breadcrumb'>" . implode("", $breadcrumbs) . "</ol>";
		$results['breadcrumbs'] = $breadcrumb_html;
		return $results;
	}

	public function get_traffic_by_day(User $user, $type = 'all', $type_id = 0) {
		$start_date = date("Y-m-d", strtotime("-7 days"));
		$end_date = date("Y-m-d");
		$where = array();

		if (env('APP_REPORT') == 'cratedb') {
			$where[] = "visit_time BETWEEN '" . date("c", strtotime($start_date . " 00:00:00")) . "' AND '" . date("c", strtotime($end_date . " 23:59:59")) . "'";
		} else {
			$where[] = "visit_date BETWEEN '{$start_date}' AND '{$end_date}'";
		}
		$where[] = "user_id = {$user->id}";

		switch ($type) {
		case 'project':
			$where[] = "project_id = {$type_id}";
			break;
		case 'campaign':
			$where[] = "campaign_id = {$type_id}";
			break;
		case 'domain':
			$where[] = "domain_id = {$type_id}";
			break;
		case 'source':
		case 'traffic':
			$where[] = "source_id = {$type_id}";
			break;
		case 'offer':
			$where[] = "offer_id = {$type_id}";
			break;
		case 'service':
			$where[] = "service_id = {$type_id}";
			break;
		default:
			break;
		}

		if (env('APP_REPORT') == 'cratedb') {
			$sql = "SELECT
						visit_date date,
						count(Distinct(visitor_id)) uniques,
						sum(visits) visitors,
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . "
					GROUP BY visit_date";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$stats_by_day = $qry->fetchAll(PDO::FETCH_OBJ);
			$stats_by_day = MyHelper::rekey_array($stats_by_day, 'date');
		} else {
			$sql = "SELECT
						visit_date as date,
						count(distinct(visitor_id)) uniques,
						sum(visits) visitors,
						sum(clicks) clicks,
						sum(revenue) revenue,
						sum(conversions) conversions,
						sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . "
					GROUP BY visit_date";

			$stats_by_day = DB::select(DB::raw($sql));
			$stats_by_day = MyHelper::rekey_array($stats_by_day, 'date');
		}

		$tmp = array();
		$current_date = $start_date;
		while ($current_date <= $end_date) {

			if (isset($stats_by_day[$current_date])) {
				$tmp[$current_date] = $stats_by_day[$current_date];
			} else {
				$tmp[$current_date] = (object) array(
					'date' => $current_date,
					'uniques' => 0,
					'visitors' => 0,
					'clicks' => 0,
					'revenue' => 0,
					'conversions' => 0,
					'ratio' => 0
				);
			}
			$current_date = date("Y-m-d", strtotime("{$current_date} +1 day"));
		}

		$stats_by_day = $tmp;
		return $stats_by_day;
	}

	public function get_top_countries(User $user, $type = 'all', $type_id = 0) {
		$today = date("Y-m-d");
		$where = array();
		if (env('APP_REPORT') == 'cratedb') {
			$where[] = "visit_date = '{$today}'";
		} else {
			$where[] = "visit_date = '{$today}'";
		}
		$where[] = "user_id = {$user->id}";

		switch ($type) {
		case 'project':
			$where[] = "project_id = {$type_id}";
			break;
		case 'source':
			$where[] = "source_id = {$type_id}";
			break;
		default:
			break;
		}

		if (env('APP_REPORT') == 'cratedb') {
			$sql = "SELECT
					country,
					country_name,
					count(Distinct(visitor_id)) uniques,
					sum(visits) visitors,
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table} sv
					WHERE " . implode(" AND ", $where) . "
					GROUP BY country, country_name
					ORDER BY visitors DESC
					LIMIT 20";

			//top countrys
			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results_countries = $qry->fetchAll(PDO::FETCH_OBJ);
			$results_countries = MyHelper::rekey_array($results_countries, 'country');

			//countries by hourly.
			$sql = "SELECT
						country,
						extract(hour from visit_time) as date_hour,
						sum(visits) visitors
						FROM {$this->stats_table}
						WHERE visit_date = '{$today}'
						AND country IN ('" . implode("','", array_keys($results_countries)) . "')
						GROUP BY country, date_hour
						ORDER BY country, date_hour ASC
					";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results_hourly = $qry->fetchAll(PDO::FETCH_OBJ);
		} else {
			$sql = "SELECT
					country,
					country_name,
					count(Distinct(visitor_id)) uniques,
					sum(visits) visitors,
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . "
					GROUP BY country
					ORDER BY visitors
					DESC LIMIT 20";

			$results_countries = DB::select(DB::raw($sql), array());
			$results_countries = MyHelper::rekey_array($results_countries, 'country');

			//countries by hourly.
			$sql = "SELECT
						country,
						hour(visit_time) date_hour,
						sum(visits) visitors
						FROM {$this->stats_table}
						WHERE visit_date = '{$today}'
						AND country IN ('" . implode("','", array_keys($results_countries)) . "')
						GROUP BY country, date_hour
						ORDER BY country, date_hour ASC
					";

			$results_hourly = DB::select(DB::raw($sql), array());
		}

		//re-arrange hourly
		$country_by_hour = array();
		foreach ($results_hourly as $rs) {
			$country_by_hour[$rs->country][$rs->date_hour] = $rs->visitors;
		}

		foreach ($results_countries as $r) {
			//add missing hours
			$current_hour = $start_hour = 0;
			$end_hour = date("H");

			while ($current_hour <= $end_hour) {
				if (!isset($country_by_hour[$r->country][$current_hour])) {
					$country_by_hour[$r->country][$current_hour] = 0;
				}

				$current_hour++;
			}
		}

		$country_summary = array();
		foreach ($results_countries as $r) {
			$country_summary[$r->visitors . '-' . $r->country]['summary'] = $r;
			asort($country_by_hour[$r->country]);
			$country_summary[$r->visitors . '-' . $r->country]['hours'] = $country_by_hour[$r->country];
		}

		if (count($country_summary) == 0) {
			$country_summary['NA']['summary'] = (object) array(
				'country' => 'NA',
				'name' => 'NA',
				'visitors' => 0,
				'uniques' => 0,
				'clicks' => 0,
				'revenue' => 0,
				'conversions' => 0,
				'ratio' => 0
			);

			$country_summary['NA']['hours'] = (object) array();
		}

		$country_summary = array_reverse($country_summary);
		return $country_summary;
	}

	public function get_top_sources(User $user) {
		$today = date("Y-m-d");

		$where = array();
		//$where[] = "t.project_id = '".$user->get_default_project()."'";
		$where[] = "user_id = {$user->id}";
		if (env('APP_REPORT') == 'cratedb') {
			$where[] = "visit_date = '{$today}'";
		} else {
			$where[] = "visit_date = '{$today}'";
		}

		if (env('APP_REPORT') == 'cratedb') {
			$sql = "SELECT
					source_id id,
					source_name name,
					sum(visits) visitors,
					count(Distinct(visitor_id)) uniques,
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . "
					GROUP BY source_id, source_name
					ORDER BY visitors DESC
					LIMIT 20";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results_sources = $qry->fetchAll(PDO::FETCH_OBJ);
			$results_sources = MyHelper::rekey_array((array) $results_sources, 'id');

			//sources by hourly.
			$sql = "SELECT
						source_id id,
						extract(hour from visit_time) as date_hour,
						sum(visits) visitors
						FROM {$this->stats_table}
						WHERE visit_date = '{$today}'
						AND source_id IN (" . implode(",", array_keys($results_sources)) . ")
						GROUP BY source_id, date_hour
						ORDER BY source_id, date_hour ASC
					";

			$qry = $this->cratepdo->prepare($sql);
			$qry->execute();
			$results_hourly = $qry->fetchAll(PDO::FETCH_OBJ);

		} else {

			$sql = "SELECT
					source_id id,
					source_name name,
					sum(visits) visitors,
					count(Distinct(visitor_id)) uniques,
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversions) conversions,
					sum(conversions)/sum(visits) ratio
					FROM {$this->stats_table}
					WHERE " . implode(" AND ", $where) . "
					GROUP BY source_id
					ORDER BY visitors DESC
					LIMIT 20";

			$results_sources = DB::select(DB::raw($sql), array());
			$results_sources = MyHelper::rekey_array((array) $results_sources, 'id');

			//countries by hourly.
			$results_hourly = array();
			if ($results_sources) {
				$sql = "SELECT
							source_id id,
							hour(visit_time) date_hour,
							sum(visits) visitors
							FROM {$this->stats_table}
							WHERE visit_date = '{$today}' AND source_id IN (" . implode(",", array_keys($results_sources)) . ")
							GROUP BY source_id, date_hour
							ORDER BY source_id, date_hour ASC
						";

				$results_hourly = DB::select(DB::raw($sql), array());
			}
		}

		//re-arrange hourly
		$source_by_hour = array();
		foreach ((array) $results_hourly as $rs) {
			$source_by_hour[$rs->id][$rs->date_hour] = $rs->visitors;
		}

		foreach ($results_sources as $r) {
			//add missing hours
			$current_hour = $start_hour = 0;
			$end_hour = date("H");

			while ($current_hour <= $end_hour) {
				if (!isset($source_by_hour[$r->id][$current_hour])) {
					$source_by_hour[$r->id][$current_hour] = 0;
				}

				$current_hour++;
			}
		}

		$source_summary = array();
		foreach ($results_sources as $r) {
			$source_summary[$r->visitors . '-' . $r->id]['summary'] = $r;
			asort($source_by_hour[$r->id]);
			$source_summary[$r->visitors . '-' . $r->id]['hours'] = $source_by_hour[$r->id];
		}

		if (count($source_summary) == 0) {
			$source_summary['NA']['summary'] = (object) array(
				'id' => 'NA',
				'name' => 'NA',
				'visitors' => 0,
				'uniques' => 0,
				'clicks' => 0,
				'revenue' => 0,
				'conversions' => 0,
				'ratio' => 0
			);

			$source_summary['NA']['hours'] = array();
		}

		$source_summary = array_reverse($source_summary);
		return $source_summary;
	}
}
?>