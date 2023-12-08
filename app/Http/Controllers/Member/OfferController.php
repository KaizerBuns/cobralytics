<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Offer;
use App\Vertical;
use App\Advertiser;
use App\Helpers\TableMap;
use App\Helpers\ReportCobra;

class OfferController extends MemberController {
	
	public function index() {
		$view = $this->request->input('view');
		$this->verticals = Vertical::get_verticals($this->user, array('limit' => 0));
		$this->advertisers = Advertiser::get_advertisers($this->user, array("limit" => 0));

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
			'icon' => '<i class="fa fa-cubes"></i>',
			'title' => 'Offers',
			'desc' => 'New Offer'
		);

		$offer = new Offer();
		return view('member.offer.new', ['header' => $header, 'verticals' => $this->verticals, 'advertisers' => $this->advertisers]);
	}

	private function bulk() {
		$header = array(
			'icon' => '<i class="fa fa-cubes"></i>',
			'title' => 'Offers',
			'desc' => 'Bulk Offers'
		);				

		return view ('member.offer.bulk', ['header' => $header, 'tablemap' => "No offers uploaded."]);
	}

	private function view() {
		$offer = Offer::find($this->request->input('id'));

		if(!$offer->is_owner($this->user)) {
			return redirect("/member/offer/?view=manage&msg=notfound");
		}
		
		$header = array(
			'icon' => '<i class="fa fa-cubes"></i>',
			'title' => $offer->get_name(),
			'desc' => 'Edit Offer'
		);
		
		$traffic_stats = $this->report->get_traffic_by_day($this->user, 'offer', $offer->id);
		$dashboard_boxes = $this->dashboard_view('boxes', $traffic_stats);
		$dashboard_daily = $this->dashboard_view('daily', $traffic_stats);

		$view = ['header' => $header, 'offer' => $offer, 'verticals' => $this->verticals, 'advertisers' => $this->advertisers, 'dashboard_boxes' => $dashboard_boxes, 'dashboard_daily' => $dashboard_daily];
		$rules = $this->display_rules($offer);

		return view('member.offer.view', array_merge($view, $rules));
	}

	private function save() {
		$offer = Offer::save_offer($this->user, $this->request->input("offer"));

		if(is_null($offer)) {
			die("Save failed");
		}	

		return redirect("/member/offer/?view=view&id=".$offer->id."&msg=saved");
	}

	private function save_bulk() {
		$header = array(
			'icon' => '<i class="fa fa-cubes"></i>',	
			'title' => 'Offers',
			'desc' => 'Bulk Offers'
		);

		$table['results'] = Offer::save_bulk($this->user, $this->request);

		if(!$table['results']) {
			die('Missing file name');			
		}

		$table['descriptor'] = array(
			'Name' => array(
				'field' => 'name', 
				'linkto' => array('url' => '/member/offer/?view=view&id={VALUE}', 'value_field' => 'offer_id'),
			),
			'Advertiser' => array(
				'field' => 'advertiser', 
				'linkto' => array('url' => '/member/advertiser/?view=view&id={VALUE}', 'value_field' => 'advertiser_id'),
			),
			'Vertical' => array(
				'field' => 'vertical', 
				'linkto' => array('url' => '/member/vertical/?view=view&id={VALUE}', 'value_field' => 'vertical_id'),
			),
			'Rules' => array('field' => 'rules'),
			'Revenue' => array('field' => 'revenue', 'format' => 'money'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);

		$table['params'] = array(
			'table_id' => 'tbl-bulk-offers'
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('member.offer.bulk', ['header' => $header, 'tablemap' => $tablemap]);
	}

	private function manage() {
		$header = array(
			'icon' => '<i class="fa fa-cubes"></i>',
			'title' => 'Offer',
			'desc' => 'Manage Offers'
		);

		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);
		
		$table['results'] = Offer::get_offers($this->user, $params);
		
		$table['params'] = array(
			'table_id' => 'table-offers',
			'action_url' => "/member/offer/?view=manage",
			'table_class' => 'xsmall-text'
		);
		
		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/offer/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name', 
				'linkto' => array('url' => '/member/offer/?view=view&id={VALUE}', 'value_field' => 'id'),
			),
			'Group' => array('field' => 'group_name', 'if_empty' => 'Not Set'),
			'Vertical' => array('field' => 'vertical_name', 'if_empty' => 'Not Set'),
			'Advertiser' => array('field' => 'advertiser_name', 'if_empty' => 'Not Set'),
			'Revenue' => array('field' => 'revenue', 'format' => 'money'),
			'Rules' => array('field' => 'rule_count'),
			'URL' => array('field' => 'url'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/offer/?view=view&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil-square-o'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Offer?', function(result) { if (result===true) { window.location.href='/member/offer/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);
		
		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	private function delete() {
		$result = Offer::delete_offer($this->user, $this->request->input("id"));
		if(!$result) {
			return redirect("/member/offer/?view=manage&msg=nofound");
		}				
		return redirect("/member/offer/?view=manage&msg=deleted");
	}
}
?>