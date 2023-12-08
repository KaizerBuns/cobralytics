<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Vertical;
use App\Helpers\TableMap;

class VerticalController extends MemberController {
	
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
			'icon' => '<i class="fa fa-chevron-up"></i>',
			'title' => 'Verticals',
			'desc' => 'New Vertical'
		);

		return view('member.vertical.new', ['header' => $header]);
	}

	public function edit() {
		$vertical = Vertical::find($this->request->input('id'));
				
		if(!$vertical->is_owner($this->user)) {
			$this->redirect('/member/vertical/?view=manage&msg=notfound');
		}

		$header = array(
			'icon' => '<i class="fa fa-chevron-up"></i>',
			'title' => $vertical->get_name(),
			'desc' => 'Edit Vertical'
		);

		return view('member.vertical.new', ['header' => $header, 'vertical' => $vertical]);
	}

	public function save() {
		$vertical = Vertical::save_vertical($this->user, $this->request->input("vertical"));
				
		if(is_null($vertical)) {
			die('Saved failed');
		}
	
		return redirect("/member/vertical/?view=manage");
	}

	public function manage() {
		$header = array(
			'icon' => '<i class="fa fa-chevron-up"></i>',
			'title' => 'Vertical',
			'desc' => 'Manage Vertical'
		);

		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);
		
		$table['results'] = Vertical::get_verticals($this->user, $params);
		
		$table['params'] = array(
			'table_id' => 'table-verticals',
			'action_url' => "/member/vertical/?view=manage"
		);
		
		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/vertical/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name_internal', 
				'linkto' => array('url' => '/member/vertical/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);
		
		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}
}
?>