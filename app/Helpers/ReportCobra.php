<?php namespace App\Helpers;

use App\Campaign;
use App\Helpers\MyHelper;
use App\Offer;
use App\Rule;
use App\Source;
use App\User;
use DB;

class ReportCobra {

	public function realtime(User $user) {
		$date = date('Y-m-d');
		$date_time = date('Y-m-d H:00:00');
		$real_time = date("Y-m-d H:i:s");
		$project_id = $user->get_default_project();

		$sql = "SELECT sum(visitors) as visitors
				FROM stats_visitors
				WHERE visit_date = '{$date}' AND visit_time = '{$real_time}'
				AND project_id = {$project_id} AND user_id = {$user->id}";

		$results = DB::select(DB::raw($sql), array());
		if (isset($results['visitors'])) {
			return $results['visitors'];
		}
		return 0;
	}

	public function custom(User $user, $params = array()) {

		$where = array();
		$column = array();
		$left_join = array();
		$group_by = array();

		$limit = (isset($params['limit']) ? $params['limit'] : 25);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$search = (isset($params['report']) ? (array) $params['report'] : array());

		$query_string = http_build_query($search);
		$params = array(
			'table_id' => 'table-custom-report',
			'action_url' => "/member/report/?view=custom&{$query_string}&submit=1",
			'enable_totals' => true,
		);

		if (!isset($search['start'])) {
			$search['start'] = date("Y-m-d");
		}

		if (!isset($search['end'])) {
			$search['end'] = date("Y-m-d");
		}

		$where['project_id'] = "ts.project_id = '" . $user->get_default_project() . "'";
		$where['user_id'] = "ts.user_id = '{$user->id}'";

		if ($search['start'] == $search['end']) {
			$where['date'] = "ts.date = '{$search['start']}'";
		} else {
			$where['date'] = "ts.date BETWEEN '{$search['start']}' AND '{$search['end']}'";
		}

		//Build TableMAP
		if (isset($search['campaign_id']) && $search['campaign_id']) {
			$where['campaign_id'] = "ts.campaign_id IN ({$search['campaign_id']})";
			$search['groupby']['campaign'] = 1;
		}

		if (isset($search['offer_id']) && $search['offer_id']) {
			$where['offer_id'] = "ts.offer_id IN ({$search['offer_id']})";
			$search['groupby']['offer'] = 1;
		}

		if (isset($search['source_id']) && $search['source_id']) {
			$where['source_id'] = "ts.source_id IN ({$search['source_id']})";
			$search['groupby']['source'] = 1;
		}

		if (isset($search['domain_id']) && $search['domain_id']) {
			$where['domain_id'] = "ts.domain_id IN ({$search['domain_id']})";
			$search['groupby']['domain'] = 1;
		}

		if (isset($search['traffic_type']) && $search['traffic_type']) {
			$traffic_type = implode("','", $search['traffic_type']);
			$where['traffic_type'] = "ts.traffic_type IN ('{$traffic_type}')";
			$column['traffic_type'] = "ts.traffic_type";
			$left_join['traffic_type'] = "";
			$group_by['traffic_type'] = "ts.traffic_type";

			$descriptor['Traffic Type'] = array('field' => 'traffic_type');
		}

		//Build Group BY
		if (isset($search['groupby']['date']) && $search['groupby']['date']) {
			$column['date'] = "ts.date";
			$group_by['date'] = 'date';

			$descriptor['Date'] = array('field' => 'date', 'format' => 'nice-date');
		}

		if (isset($search['groupby']['campaign']) && $search['groupby']['campaign']) {
			$column['campaign_id'] = "c.id campaign_id, c.name campaign_name";
			$left_join['campaign_id'] = "LEFT JOIN campaigns c ON (c.id = ts.campaign_id)";
			$group_by['campaign_id'] = "ts.campaign_id";

			$descriptor['Campaign'] = array('field' => 'campaign_name');
		}

		if (isset($search['groupby']['offer']) && $search['groupby']['offer']) {
			$column['offer_id'] = "o.id offer_id, o.name offer_name";
			$left_join['offer_id'] = "LEFT JOIN offers o ON (o.id = ts.offer_id)";
			$group_by['offer_id'] = "ts.offer_id";

			$descriptor['Offer'] = array('field' => 'offer_name');
		}

		if (isset($search['groupby']['source']) && $search['groupby']['source']) {
			$column['source_id'] = "s.id source_id, s.name source_name";
			$left_join['source_id'] = "LEFT JOIN sources s ON (s.id = ts.source_id AND s.type = 'traffic')";
			$group_by['source_id'] = "ts.source_id";

			$descriptor['Source'] = array('field' => 'source_name');
		}

		if (isset($search['groupby']['domain']) && $search['groupby']['domain']) {
			$column['domain_id'] = "d.id domain_id, d.name domain_name";
			$left_join['domain_id'] = "LEFT JOIN sources d ON (d.id = ts.domain_id AND d.type = 'domain')";
			$group_by['domain_id'] = "ts.domain_id";

			$descriptor['Domain'] = array('field' => 'domain_name');
		}

		if (isset($search['groupby']['traffic_type']) && $search['groupby']['traffic_type']) {
			$column['traffic_type'] = "ts.traffic_type";
			$group_by['traffic_type'] = "ts.traffic_type";

			$descriptor['TrafficType'] = array('field' => 'traffic_type');
		}

		if (isset($search['groupby']['device']) && $search['groupby']['device']) {
			$column['device'] = "concat(ts.platform,' ',ts.browser) as device";
			$group_by['device'] = "ts.platform,ts.browser";

			$descriptor['Device'] = array('field' => 'device');
		}

		if (isset($search['groupby']['referrers']) && $search['groupby']['referrers']) {
			$column['referrers'] = "ts.referrers";
			$group_by['referrers'] = "ts.referrers";

			$descriptor['Referer'] = array('field' => 'referrers');
		}

		if (isset($search['groupby']['subids']) && $search['groupby']['subids']) {
			$column['subids'] = "ts.subid";
			$group_by['subids'] = "ts.subid";

			$descriptor['SubID'] = array('field' => 'subids');
		}

		if (isset($search['groupby']['country']) && $search['groupby']['country']) {
			$column['country'] = "ts.country";
			$group_by['country'] = "ts.country";

			$descriptor['Country'] = array('field' => 'country');
		}

		if (isset($group_by['date']) && count($group_by) == 1) {
			unset($descriptor['Name']);
		} else {
			//fail sale
			if (count($group_by) == 0) {
				$group_by = array('date');
			}
		}

		$descriptor['Visitors'] = array('field' => 'visitors', 'enable_totals' => 1, 'custom_totals' => 'visitors');
		$descriptor['Uniques'] = array('field' => 'uniques', 'enable_totals' => 1, 'custom_totals' => 'uniques');
		$descriptor['Clicks'] = array('field' => 'clicks', 'enable_totals' => 1, 'custom_totals' => 'clicks');
		$descriptor['Conversions'] = array('field' => 'conversion', 'enable_totals' => 1, 'custom_totals' => 'conversion');
		$descriptor['Revenue'] = array('field' => 'revenue', 'format' => 'money', 'enable_totals' => 1, 'custom_totals' => 'revenue');
		$descriptor['cRatio'] = array('field' => 'ratio', 'enable_totals' => 1, 'custom_totals' => 'ratio');

		//Define columns to calculate
		$calc = array(
			"sum(ts.visitors) visitors",
			"count(distinct unique_key) uniques",
			"sum(ts.clicks) clicks",
			"sum(ts.conversion) conversion",
			"sum(ts.revenue) revenue",
			"sum(ts.conversion)/sum(ts.visitors) ratio"
		);

		//Normal SQL
		$columns = implode(",", array_merge($column, $calc));
		$left_joins = implode(" ", $left_join);

		$sql = "SELECT {$columns} FROM stats_visitors ts {$left_joins} WHERE ";
		$sql .= implode(" AND ", $where);
		$sql .= " GROUP BY " . implode(",", $group_by);

		//Paginate
		$offset = (($page - 1) * $limit);
		$sql .= " LIMIT {$offset}, {$limit}";

		//Run Normal Query
		$results = DB::select(DB::raw($sql), array());

		//Run Totals Query
		$sql_total = "SELECT " . implode(",", $calc) . " FROM stats_visitors ts WHERE " . implode(" AND ", $where) . " LIMIT 1";
		$totals = DB::select(DB::raw($sql_total), array());
		$params['custom_totals'] = (array) $totals;

		$report = array(
			'results' => $results,
			'descriptor' => $descriptor,
			'params' => $params,
		);

		return $report;
	}

	public function get_dns_by_day($type_id) {
		$start_date = date("Y-m-d", strtotime("-7 days"));
		$end_date = date("Y-m-d");
		$table_name = 'stats_summary_dns';

		$sql = "SELECT
					visit_date,
					sum(total_requests) total_requests
				FROM {$table_name}
				WHERE domain_id = '{$type_id}'
				AND visit_date BETWEEN '{$start_date}' AND '{$end_date}'
				GROUP BY visit_date";

		$stats_by_day = DB::select(DB::raw($sql));
		$stats_by_day = MyHelper::rekey_array($stats_by_day, 'date');

		$tmp = array();
		$current_date = $start_date;
		while ($current_date <= $end_date) {

			if (isset($stats_by_day[$current_date])) {
				$tmp[$current_date] = $stats_by_day[$current_date];
			} else {
				$tmp[$current_date] = array(
					'date' => $current_date,
					'total_requests' => 0,
				);
			}
			$current_date = date("Y-m-d", strtotime("{$current_date} +1 day"));
		}

		$stats_by_day = $tmp;
		return $stats_by_day;
	}

	public function get_traffic_by_day(User $user, $type = 'all', $type_id = 0) {
		$start_date = date("Y-m-d", strtotime("-7 days"));
		$end_date = date("Y-m-d");
		$table_name = 'stats_summary_manage';
		$where = array();
		$where[] = "date BETWEEN '{$start_date}' AND '{$end_date}'";
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

		$sql = "SELECT
					date,
					sum(uniques) uniques,
					sum(visitors) visitors,
					sum(clicks) clicks,
					sum(revenue) revenue,
					sum(conversion) conversion,
					sum(conversion)/sum(visitors) ratio
				FROM {$table_name}
				WHERE " . implode(" AND ", $where) . "
				GROUP BY date";

		$stats_by_day = DB::select(DB::raw($sql));
		$stats_by_day = MyHelper::rekey_array($stats_by_day, 'date');

		$tmp = array();
		$current_date = $start_date;
		while ($current_date <= $end_date) {

			if (isset($stats_by_day[$current_date])) {
				$tmp[$current_date] = $stats_by_day[$current_date];
			} else {
				$tmp[$current_date] = array(
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
			'html' => "<div style=\"word-break:break-all;\">{EDIT_LINK}<a href=\"/member/report/?view=overview&tab={TAB}&{QUERY_STRING}&filter[$tab]={ID}\">{NAME}</a></div>",
			'value_field' => array('ID' => 'id', 'NAME' => 'name'),
			'if_empty' => 'Unknown',

		), 'not_sortable' => true);

		$where = array();
		$where['projectid'] = "t.project_id = '" . $user->get_default_project() . "'";
		$where['userid'] = "t.user_id = '{$user->id}'";
		$where['daterange'] = "ts.date BETWEEN '{$start_date}' AND '{$end_date}'";

		switch ($tab) {
		default:
		case 'campaign':
			$table_name = "projects t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.project_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.project_id = t.id)";

				if (isset($filter['source'])) {
					$where['source_id'] = "ts.source_id = {$filter['source']}";
				}
			}

			$columns = "t.id, t.name";
			$group_by = "t.id";
			break;
		case 'campaign':
			$table_name = "campaigns t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.campaign_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.campaign_id = t.id)";

				if (isset($filter['source'])) {
					$where['source_id'] = "ts.source_id = {$filter['source']}";
				}
			}

			$columns = "t.id, t.name";
			$group_by = "t.id";
			break;
		case 'domain':
			$table_name = "sources t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.domain_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.domain_id = t.id)";
			}

			$where[] = "t.type = 'domain'";
			$columns = "t.id, t.name";
			$group_by = "t.id";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			break;
		case 'source':
			$table_name = "sources t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.source_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.source_id = t.id)";
			}

			$where[] = "t.type = 'traffic'";

			$columns = "t.id, t.name";
			$group_by = "t.id";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			break;
		case 'traffic':
			$table_name = "stats_summary_traffic ts";
			$left_join = "";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "ts.traffic_type as id, ts.traffic_type as name";
			$group_by = "ts.traffic_type";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$edit_link = "";
			break;
		case 'content':
			$table_name = "campaigns t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.campaign_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.campaign_id = t.id)";
			}

			$columns = "t.content as id, t.content as name";
			$group_by = "t.content";

			//overrides
			$edit_link = "";
			break;
		case 'medium':
			$table_name = "campaigns t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.campaign_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.campaign_id = t.id)";
			}

			$columns = "t.medium as id, t.medium as name";
			$group_by = "t.medium";

			//overrides
			$edit_link = "";
			break;
		case 'offer':
			$table_name = "offers t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.offer_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.offer_id = t.id)";
			}

			$columns = "t.id, t.name";
			$group_by = "t.id";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			break;
		case 'lp': //landing pages
			$table_name = "rules t";
			$left_join = "LEFT JOIN stats_summary_manage ts ON (ts.rule_id = t.id)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.rule_id = t.id)";
			}

			$left_join .= "LEFT JOIN offers o ON (o.id = t.offer_id)";
			$left_join .= "LEFT JOIN campaigns c ON (c.id = t.campaign_id)";

			$columns = "t.id, IF(t.offer_id > 0, o.name, IF (t.campaign_id > 0, c.name, concat('(',t.name,') ', t.url))) name";
			$group_by = "t.id";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$edit_link = "";
			break;
		case 'ref':
			$table_name = "stats_summary_traffic ts";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "referer as id, referer as name";
			$group_by = "referer";

			//overrides
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$descriptor['Name']['html']['if_empty'] = "Direct/Bookmark";
			$edit_link = "";
			break;

		/* This was when we had pixel-js tracking
			case 'page':
				$table_name = "stats_summary_traffic ts";

				if($filter_on) {
					$table_name = "stats_visitors ts";
				}

				$left_join.= "LEFT JOIN sources s ON (s.id = ts.source_id)";
				$columns = "page as id, concat(s.name,'',ts.page) as name";
				$group_by = "page";

				//overrides
				$where['userid'] = "ts.user_id = '{$user->id}'";
				$where['projectid'] = "ts.project_id = '".$user->get_default_project()."'";
				$where['page'] = "ts.page != ''";
				$descriptor['Name']['html']['if_empty'] = "/";
				break;
			*/

		case 'device':
			$table_name = "stats_summary_traffic ts";
			$left_join = "";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "concat(ts.platform,' ',ts.browser) as id, concat(ts.platform,' ',ts.browser) as name";
			$group_by = "platform, browser";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$descriptor['Name']['html']['if_empty'] = "Uknown Device";
			$edit_link = "";
			break;
		case 'country':
			$table_name = "geo_countries g";
			$left_join = "LEFT JOIN stats_summary_country ts ON (ts.country = g.country_iso_code)";

			if ($filter_on) {
				$left_join = "LEFT JOIN stats_visitors ts ON (ts.country = g.country_iso_code)";
			}

			$columns = "ts.country as id, g.country_name as name";
			$group_by = "ts.country";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$edit_link = "";
			break;
		case 'subid':
			$table_name = "stats_summary_traffic ts";
			$left_join = "";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "ts.subid as id, ts.subid as name";
			$group_by = "ts.subid";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$edit_link = "";
			break;
		case 'day':
			$table_name = "stats_summary_traffic ts";
			$left_join = "";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "ts.date as id, ts.date as name";
			$group_by = "ts.date";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$edit_link = "";
			break;
		case 'hour':
			$table_name = "stats_summary_hourly ts";
			$left_join = "";

			if ($filter_on) {
				$table_name = "stats_visitors ts";
			}

			$columns = "hour(ts.visit_time) as id, TIME_FORMAT(ts.visit_time, '%H:%i') as name";
			$group_by = "TIME_FORMAT(ts.date_time, '%H:%i')";

			//overrides
			$where['projectid'] = "ts.project_id = '" . $user->get_default_project() . "'";
			$where['userid'] = "ts.user_id = '{$user->id}'";
			$map_descriptor['Name']['html']['value_field']['NAME'] = array('field' => 'name', 'format' => 'nice-time');
			$edit_link = "";
			break;
		}

		$visitor_count = "sum(ts.uniques) uniques, sum(ts.visitors) visitors";
		$breadcrumbs = array();
		$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign"><i class="fa fa-line-chart"></i> All Campaigns</a></li>';

		if ($filter_on) {
			$visitor_count = "count(distinct unique_key) uniques, sum(visitors) visitors";
			if (isset($filter['campaign'])) {
				$where['campaign_id'] = "ts.campaign_id = '{$filter['campaign']}'";
				//Find Campaign
				$campaign = Campaign::find($filter['campaign']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[campaign]=' . $filter['campaign'] . '">' . $campaign->name . '</a></li>';
			}

			if (isset($filter['domain'])) {
				$where['source_id'] = "ts.domain_id = '{$filter['domain']}'";

				//Find Domain
				$domain = Source::find($filter['domain']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[domain]=' . $filter['domain'] . '">' . $domain->name . '</a></li>';
			}

			if (isset($filter['source'])) {
				$where['source_id'] = "ts.source_id = '{$filter['source']}'";

				//Find Source
				$source = Source::find($filter['source']);
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[source]=' . $filter['source'] . '">' . $source->name . '</a></li>';
			}

			if (isset($filter['traffic'])) {
				$where['traffic'] = "ts.traffic_type = '{$filter['traffic']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[traffic]=' . $filter['traffic'] . '">' . $filter['traffic'] . '</a></li>';
			}

			if (isset($filter['content'])) {
				$where['content'] = "t.content = '{$filter['content']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[content]=' . $filter['content'] . '">' . $filter['content'] . '</a></li>';
			}

			if (isset($filter['medium'])) {
				$where['medium'] = "t.medium = '{$filter['medium']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[medium]=' . $filter['medium'] . '">' . $filter['medium'] . '</a></li>';
			}

			if (isset($filter['offer'])) {
				$where['offer'] = "ts.offer_id = '{$filter['offer']}'";
				$offer = Offer::find($filter['offer']);

				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=offer&filter[offer]=' . $filter['offer'] . '">(' . $offer->id . ') ' . $offer->name . '</a></li>';
			}

			if (isset($filter['lp'])) {
				$where['lp'] = "ts.rule_id = '{$filter['lp']}'";
				$rule = Rule::find($filter['lp']);

				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[lp]=' . $filter['lp'] . '">(' . $rule->name . ') ' . $rule->url . '</a></li>';
			}

			if (isset($filter['ref'])) {
				$where['referer'] = "ts.referer = '{$filter['ref']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[ref]=' . $filter['ref'] . '">' . $filter['ref'] . '</a></li>';
			}

			if (isset($filter['page'])) {
				$where['page'] = "ts.page = '{$filter['page']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[page]=' . $filter['page'] . '">' . $filter['page'] . '</a></li>';
			}

			if (isset($filter['device'])) {
				$where['device'] = "concat(ts.platform,' ',ts.browser) LIKE '%{$filter['device']}%'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[device]=' . $filter['device'] . '">' . $filter['device'] . '</a></li>';
			}

			if (isset($filter['country'])) {
				$where['country'] = "ts.country = '{$filter['country']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[country]=' . $filter['country'] . '">' . $filter['country'] . '</a></li>';
			}

			if (isset($filter['subid'])) {
				$where['country'] = "ts.subid = '{$filter['subid']}'";
				$breadcrumbs[] = '<li><a href="/member/report/?view=overview&tab=campaign&filter[subid]=' . $filter['subid'] . '">' . $filter['subid'] . '</a></li>';
			}

			if (isset($filter['day'])) {
				$params['report']['start'] = $params['report']['end'] = $filter['day'];
				$where['daterange'] = "ts.date = '{$filter['day']}'";
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

		$sql = "SELECT
					{$columns},
					{$visitor_count},
					sum(ts.hits) hits,
					sum(ts.clicks) clicks,
					sum(ts.revenue) revenue,
					sum(ts.conversion) conversion,
					sum(ts.conversion)/sum(ts.visitors) ratio
				FROM {$table_name} {$left_join}
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
					sum(ts.hits) hits,
					sum(ts.clicks) clicks,
					sum(ts.revenue) revenue,
					sum(ts.conversion) conversion,
					sum(ts.conversion)/sum(ts.visitors) ratio
				FROM {$table_name} {$left_join}
				WHERE " . implode(" AND ", $where) . " LIMIT 1";

		$totals = DB::select(DB::raw($sql_total), array());
		$totals = array_shift($totals);

		$where['daterange'] = "(ts.date BETWEEN '{$start_date}' AND '{$end_date}' OR date = '" . date("Y-m-d") . "')";
		$sql_by_day = "SELECT date,
				{$visitor_count},
				sum(ts.hits) hits,
				sum(ts.clicks) clicks,
				sum(ts.revenue) revenue,
				sum(ts.conversion) conversion,
				sum(ts.conversion)/sum(ts.visitors) ratio
				FROM {$table_name} {$left_join}
				WHERE " . implode(" AND ", $where);

		$sql_by_day .= " GROUP BY date";

		$stats_by_day = DB::select(DB::raw($sql_by_day), array());
		$stats_by_day = MyHelper::rekey_array((array) $stats_by_day, 'date');

		$current_date = $start_date;
		while ($current_date <= $end_date) {
			if (!isset($stats_by_day[$current_date])) {
				$stats_by_day[$current_date] = array(
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
		//$map_descriptor['Impressions'] = array('field' => 'hits', 'enable_totals' => 1, 'custom_totals' => 'hits');
		$map_descriptor['Clicks'] = array('field' => 'clicks', 'enable_totals' => 1, 'custom_totals' => 'clicks');
		$map_descriptor['Conversions'] = array('field' => 'conversion', 'enable_totals' => 1, 'custom_totals' => 'conversion');
		$map_descriptor['Revenue'] = array('field' => 'revenue', 'format' => 'money', 'enable_totals' => 1, 'custom_totals' => 'revenue');
		$map_descriptor['cRatio'] = array('field' => 'ratio', 'enable_totals' => 1, 'custom_totals' => 'ratio');

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

	public function get_top_countries(User $user, $type = 'all', $type_id = 0) {
		$today = date("Y-m-d");

		$where = array();
		$table_name = 'stats_summary_hourly';
		switch ($type) {
		case 'project':
			$where[] = "project_id = '{$type_id}'";
			break;
		case 'source':
			$where[] = "source_id = '{$type_id}'";
			break;
		}

		$where[] = "user_id = '{$user->id}'";
		$where[] = "date = '{$today}'";

		$sql = "SELECT
				s.country,
				gc.country_name country_name,
				hour(date_time) date_hour,
				sum(uniques) uniques,
				sum(visitors) visitors,
				sum(clicks) clicks,
				sum(hits) hits,
				sum(revenue) revenue,
				sum(conversions) conversions,
				sum(conversions)/sum(visitors) ratio
				FROM {$table_name} s
				LEFT JOIN geo_countries gc ON (gc.country_iso_code = s.country) ";

		$sql .= "WHERE " . implode(" AND ", $where) . " ";
		$sql .= "GROUP BY country, hour(date_time)";
		$sql .= "ORDER BY visitors ";
		$sql .= "DESC LIMIT 240";

		$results = DB::select(DB::raw($sql), array());

		$country_by_hour = array();
		foreach ($results as $r) {

			//add missing hours
			$current_hour = $start_hour = 0;
			$end_hour = date("H");

			while ($current_hour <= $end_hour) {

				if (!isset($country_by_hour[$r['country']]['hours'][$current_hour])) {
					$country_by_hour[$r['country']]['hours'][$current_hour] = array(
						'country_name' => $r['country'],
						'name' => $r['country_name'],
						'date_hour' => $current_hour,
						'visitors' => 0,
						'uniques' => 0,
						'hits' => 0,
						'clicks' => 0,
						'revenue' => 0,
						'conversions' => 0,
						'ratio' => 0
					);
				}

				$current_hour++;
			}

			$country_by_hour[$r['country']]['hours'][$r['date_hour']] = array(
				'country' => $r['country'],
				'name' => $r['country_name'],
				'date_hour' => $r['date_hour'],
				'visitors' => $r['visitors'],
				'uniques' => $r['uniques'],
				'clicks' => $r['clicks'],
				'hits' => $r['hits'],
				'revenue' => $r['revenue'],
				'conversions' => $r['conversions'],
				'ratio' => $r['ratio'],
			);

			if (!isset($country_by_hour[$r['country']]['summary'])) {
				$country_by_hour[$r['country']]['summary']['visitors'] = 0;
				$country_by_hour[$r['country']]['summary']['uniques'] = 0;
				$country_by_hour[$r['country']]['summary']['clicks'] = 0;
				$country_by_hour[$r['country']]['summary']['hits'] = 0;
				$country_by_hour[$r['country']]['summary']['revenue'] = 0;
				$country_by_hour[$r['country']]['summary']['conversions'] = 0;
				$country_by_hour[$r['country']]['summary']['ratio'] = 0;
			}

			$country_by_hour[$r['country']]['summary']['country'] = $r['country'];
			$country_by_hour[$r['country']]['summary']['country_name'] = $r['country_name'];
			$country_by_hour[$r['country']]['summary']['visitors'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['visitors'];
			$country_by_hour[$r['country']]['summary']['uniques'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['uniques'];
			$country_by_hour[$r['country']]['summary']['clicks'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['clicks'];
			$country_by_hour[$r['country']]['summary']['hits'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['hits'];
			$country_by_hour[$r['country']]['summary']['revenue'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['revenue'];
			$country_by_hour[$r['country']]['summary']['conversions'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['conversions'];
			$country_by_hour[$r['country']]['summary']['ratio'] += $country_by_hour[$r['country']]['hours'][$r['date_hour']]['ratio'];
		}

		if (count($country_by_hour) == 0) {
			$country_by_hour['NA']['hours'][0] = array(
				'country_name' => 'NA',
				'name' => 'NA',
				'date_hour' => 0,
				'visitors' => 0,
				'uniques' => 0,
				'hits' => 0,
				'clicks' => 0,
				'revenue' => 0,
				'conversions' => 0,
				'ratio' => 0
			);

			$country_by_hour['NA']['summary']['country'] = 'NA';
			$country_by_hour['NA']['summary']['country_name'] = 'NA';
			$country_by_hour['NA']['summary']['visitors'] = 0;
			$country_by_hour['NA']['summary']['uniques'] = 0;
			$country_by_hour['NA']['summary']['clicks'] = 0;
			$country_by_hour['NA']['summary']['hits'] = 0;
			$country_by_hour['NA']['summary']['revenue'] = 0;
			$country_by_hour['NA']['summary']['conversions'] = 0;
			$country_by_hour['NA']['summary']['ratio'] = 0;
		}

		return $country_by_hour;
	}

	public function get_top_sources(User $user) {
		$today = date("Y-m-d");

		$where = array();
		$table_name = 'stats_summary_hourly';

		$where[] = "t.project_id = '" . $user->get_default_project() . "'";
		$where[] = "t.user_id = '{$user->id}'";
		$where[] = "t.date = '{$today}'";

		$sql = "SELECT
				t.source_id as id,
				s.name,
				hour(date_time) date_hour,
				sum(visitors) visitors,
				sum(uniques) uniques,
				sum(clicks) clicks,
				sum(hits) hits,
				sum(revenue) revenue,
				sum(conversions) conversions,
				sum(conversions)/sum(visitors) ratio 
				FROM {$table_name} t
				LEFT JOIN sources s ON (s.id = t.source_id) ";

		$sql .= "WHERE " . implode(" AND ", $where) . " ";
		$sql .= "GROUP BY t.source_id, hour(date_time)";
		$sql .= "ORDER BY visitors ";
		$sql .= "DESC LIMIT 360";

		$results = DB::select(DB::raw($sql), array());

		$source_by_hour = array();
		foreach ($results as $r) {

			//add missing hours
			$current_hour = $start_hour = 0;
			$end_hour = date("H");

			while ($current_hour <= $end_hour) {

				if (!isset($source_by_hour[$r['id']]['hours'][$current_hour])) {
					$source_by_hour[$r['id']]['hours'][$current_hour] = array(
						'id' => $r['id'],
						'name' => $r['name'],
						'date_hour' => $current_hour,
						'visitors' => 0,
						'uniques' => 0,
						'hits' => 0,
						'clicks' => 0,
						'revenue' => 0,
						'conversions' => 0,
						'ratio' => 0
					);
				}

				$current_hour++;
			}

			$source_by_hour[$r['id']]['hours'][$r['date_hour']] = array(
				'id' => $r['id'],
				'name' => $r['name'],
				'date_hour' => $r['date_hour'],
				'visitors' => $r['visitors'],
				'uniques' => $r['uniques'],
				'clicks' => $r['clicks'],
				'hits' => $r['hits'],
				'revenue' => $r['revenue'],
				'conversions' => $r['conversions'],
				'ratio' => $r['ratio']
			);

			if (!isset($source_by_hour[$r['id']]['summary'])) {
				$source_by_hour[$r['id']]['summary']['visitors'] = 0;
				$source_by_hour[$r['id']]['summary']['uniques'] = 0;
				$source_by_hour[$r['id']]['summary']['clicks'] = 0;
				$source_by_hour[$r['id']]['summary']['hits'] = 0;
				$source_by_hour[$r['id']]['summary']['revenue'] = 0;
				$source_by_hour[$r['id']]['summary']['conversions'] = 0;
				$source_by_hour[$r['id']]['summary']['ratio'] = 0;
			}

			$source_by_hour[$r['id']]['summary']['id'] = $r['id'];
			$source_by_hour[$r['id']]['summary']['name'] = $r['name'];
			$source_by_hour[$r['id']]['summary']['visitors'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['visitors'];
			$source_by_hour[$r['id']]['summary']['uniques'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['uniques'];
			$source_by_hour[$r['id']]['summary']['clicks'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['clicks'];
			$source_by_hour[$r['id']]['summary']['hits'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['hits'];
			$source_by_hour[$r['id']]['summary']['revenue'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['revenue'];
			$source_by_hour[$r['id']]['summary']['conversions'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['conversions'];
			$source_by_hour[$r['id']]['summary']['ratio'] += $source_by_hour[$r['id']]['hours'][$r['date_hour']]['ratio'];
		}

		if (count($source_by_hour) == 0) {
			$source_by_hour['NA']['hours'][0] = array(
				'id' => 0,
				'name' => 'NA',
				'date_hour' => 0,
				'visitors' => 0,
				'uniques' => 0,
				'hits' => 0,
				'clicks' => 0,
				'revenue' => 0,
				'conversions' => 0,
				'ratio' => 0
			);

			$source_by_hour['NA']['summary']['id'] = 'NA';
			$source_by_hour['NA']['summary']['name'] = 'NA';
			$source_by_hour['NA']['summary']['visitors'] = 0;
			$source_by_hour['NA']['summary']['uniques'] = 0;
			$source_by_hour['NA']['summary']['clicks'] = 0;
			$source_by_hour['NA']['summary']['hits'] = 0;
			$source_by_hour['NA']['summary']['revenue'] = 0;
			$source_by_hour['NA']['summary']['conversions'] = 0;
			$source_by_hour['NA']['summary']['ratio'] = 0;
		}

		return $source_by_hour;
	}
}
?>