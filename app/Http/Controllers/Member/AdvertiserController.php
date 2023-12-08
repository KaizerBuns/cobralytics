<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Advertiser;
use App\Helpers\TableMap;

class AdvertiserController extends MemberController {

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

	public function create() {
		$header = array(
			'icon' => '<i class="fa fa-paper-plane"></i>',
			'title' => 'Advertisers',
			'desc' => 'New Advertiser'
		);

		return view('member.advertiser.new', ['header' => $header ]);
	}

	public function edit() {
		$advertiser = Advertiser::find($this->request->input('id'));
				
		if(!$advertiser->is_owner($this->user)) {
			$this->redirect('/member/advertiser/?view=manage&msg=notfound');
		}

		$header = array(
			'icon' => '<i class="fa fa-paper-plane"></i>',
			'title' => $advertiser->get_name(),
			'desc' => 'Edit Advertiser'
		);

		return view('member.advertiser.new', ['header' => $header, 'advertiser' => $advertiser]);
	}

	public function save() {
		$advertiser = Advertiser::save_advertiser($this->user, $this->request->input("advertiser"));

		if(is_null($advertiser)) {
			die('Saved failed');
		}

		return redirect("/member/advertiser/?view=manage");
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-paper-plane"></i>',
			'title' => 'Advertisers',
			'desc' => 'Manage Advertisers'
		);

		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);
		
		$table['results'] = Advertiser::get_advertisers($this->user, $params);
		$table['params'] = array(
			'table_id' => 'table-advertisers',
			'action_url' => "/member/advertiser/?view=manage"
		);
		
		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/advertiser/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name_internal', 
				'linkto' => array('url' => '/member/advertiser/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);
		
		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}
}

?>