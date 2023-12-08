<?php namespace App\Http\Controllers\Member;

use App\Campaign;
use App\CampaignDomain;
use App\CampaignPixel;
use App\Helpers\MyHelper;
use App\Helpers\TableMap;
use App\Http\Controllers\MemberController;
use App\Source;
use Illuminate\Http\Request;

class CampaignController extends MemberController {

	public function index() {
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
			'icon' => '<i class="fa fa-link"></i>',
			'title' => 'Campaign',
			'desc' => 'New Campaign',
		);
		$campaign = new Campaign();
		$campaign->rules = array();

		return view('member.campaign.new', ['header' => $header, 'campaign' => $campaign]);
	}

	public function view() {
		$campaign = Campaign::findWhere($this->user, array('id' => $this->request->input('id')));

		if (is_null($campaign)) {
			return redirect('/member/campaign/?view=manage&msg=notfound');
		}

		$header = array(
			'icon' => '<i class="fa fa-link"></i>',
			'title' => $campaign->get_name(),
			'desc' => 'Edit Campaign',
		);

		$campaign->key_domains = implode(",", array_keys($campaign->domains));
		$campaign->key_pixels = implode(",", array_keys($campaign->pixels));

		$traffic_stats = $this->report->get_traffic_by_day($this->user, 'campaign', $campaign->id);
		$dashboard_boxes = $this->dashboard_view('boxes', $traffic_stats);
		$dashboard_daily = $this->dashboard_view('daily', $traffic_stats);

		$view = ['header' => $header, 'campaign' => $campaign, 'dashboard_boxes' => $dashboard_boxes, 'dashboard_daily' => $dashboard_daily];
		$rules = $this->display_rules($campaign);

		$params = array_merge($view, $rules);
		$rotators = $this->display_rotators($campaign);
		$params = array_merge($params, $rotators);

		return view('member.campaign.view', $params);
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-link"></i>',
			'title' => 'Campaign',
			'desc' => 'Manage Campaigns',
		);

		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = Campaign::get_campaigns($this->user, $params);

		$table['params'] = array(
			'table_id' => 'table-links',
			'action_url' => "/member/campaign/?view=manage",
			'table_class' => 'xsmall-text',
		);

		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id',
				'linkto' => array('url' => '/member/campaign/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'campaign_name',
				'linkto' => array('url' => '/member/campaign/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Project' => array('field' => 'project_name'),
			'LinkID' => array('field' => 'linkhash'),
			'Source' => array('field' => 'source_name'),
			'Content' => array('field' => 'content', 'format' => 'ucfirst'),
			'Info' => array(
				'html' => array(
					'html' => "Medium: {MEDIUM}<br>Type: {TYPE}<br>AdCost: {COST}<br>AdRev: {REV}<br>Tracking: {TRACK}<br>",
					'value_field' => array(
						'MEDIUM' => array('field' => 'medium', 'format' => 'checkblank'),
						'TYPE' => array('field' => 'type', 'format' => 'checkblank'),
						'COST' => array('field' => 'cost', 'format' => 'currency'),
						'REV' => array('field' => 'revenue', 'format' => 'currency'),
						'TRACK' => array('field' => 'tracking_domains', 'format' => 'lowercase'),
					),
				),
			),
			'Rules' => array(
				'html' => array(
					'html' => "Rules: {OTHER}<br>Rotators: {CREATIVE}",
					'value_field' => array(
						'CREATIVE' => array('field' => 'rotator_count'),
						'OTHER' => array('field' => 'rule_count'),
					),
				),
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/campaign/?view=view&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Campaign?', function(result) { if (result===true) { window.location.href='/member/campaign/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1',
				),
			),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	public function save() {
		$success = true;
		$project_id = $this->user->get_default_project();

		$campaign = Campaign::find($this->request->input('campaign')['id']);
		if (is_null($campaign)) {
			$campaign = new Campaign;
			$campaign->linkhash = strtolower(MyHelper::generate_numAlpha(7));
		}

		$linkhash = $campaign->linkhash;
		$campaign->user_id = $this->user->id;
		$campaign->fill($this->request->input('campaign'));
		//$campaign->project_id = $project_id;

		//New SourceID
		$source_id = $campaign->source_id;
		if ((int) $source_id == 0) {
			$results = Source::bulk_save($this->user, array($campaign->source_id), 0, $campaign->project_id, 'traffic');
			if ($results[0]['status'] != 'Error') {
				$campaign->source_id = $results[0]['id'];
			}
		}

		$campaign->revenue = (float) $campaign->revenue;
		$campaign->cost = (float) $campaign->cost;
		$campaign->active = 1;

		$domain_ids = $campaign->domain_id;
		$pixel_ids = $campaign->pixel_id;

		//not needed
		unset($campaign->domain_id);
		unset($campaign->pixel_id);

		if ($campaign->save()) {
			//Save tracking domains
			$domains = explode(',', $domain_ids);
			$campaign_domains = CampaignDomain::where(array('campaign_id' => $campaign->id))->get();

			//delete any removed ones;
			foreach ($campaign_domains as $cd) {
				if (!in_array($cd->source_id, $domains)) {
					$cd->delete();
				}
			}

			//Save Tracking Domains
			foreach ($domains as $d) {
				if ((int) $d) {
					if (!$cd = CampaignDomain::where(array('campaign_id' => $campaign->id, 'source_id' => $d))->first()) {
						$cd = new CampaignDomain();
						$cd->campaign_id = $campaign->id;
						$cd->linkhash = $linkhash;
						$cd->source_id = $d;
					} else {
						$cd->linkhash = $linkhash;
					}

					$cd->save();
				}
			}

			$pixels = explode(',', $pixel_ids);
			$campaign_pixels = CampaignPixel::where(array('campaign_id' => $campaign->id))->get();

			//delete any removed ones;
			foreach ($campaign_pixels as $cp) {
				if (!in_array($cp->pixel_id, $pixels)) {
					$cp->delete();
				}
			}

			//Save Pixels
			foreach ($pixels as $p) {
				if ((int) $p) {
					if (!$cp = CampaignPixel::where(array('campaign_id' => $campaign->id, 'pixel_id' => $p))->first()) {
						$cp = new CampaignPixel();
						$cp->campaign_id = $campaign->id;
						$cp->pixel_id = $p;
					}
					$cp->save();
				}
			}

		} else {
			die('Saved failed');
			$success = false;
		}

		if ($success) {
			return redirect('/member/campaign/?view=view&id=' . $campaign->id . '&msg=saved');
		} else {
			die("ERROR!");
		}
	}

	public function delete() {
		$result = Campaign::delete_campaign($this->user, $this->request->input('id'));
		if (!$result) {
			return redirect('/member/campaign/?view=manage&msg=notfound');
		}

		return redirect('/member/campaign/?view=manage&msg=deleted');
	}
}