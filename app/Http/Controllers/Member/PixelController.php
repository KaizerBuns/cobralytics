<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Pixel;
use App\Helpers\TableMap;

class PixelController extends MemberController {
	
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

	private function create() {
		$header = array(
			'icon' => '<i class="fa fa-bolt"></i>',
			'title' => 'Pixels',
			'desc' => 'New Pixel'
		);

		return view ('member.pixel.new', ['header' => $header]);
	}

	private function manage() {
		$header = array(
			'icon' => '<i class="fa fa-bolt"></i>',
			'title' => 'Pixels',
			'desc' => 'Manage Pixels'
		);

		$params = array(
			'limit' => $this->user->pref_page_limit, 
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order')
		);
	
		$table['results'] = Pixel::get_pixels($this->user, $params);
		
		$table['params'] = array(
			'table_id' => 'table-pixels',
			'action_url' => "/member/pixel/?view=manage",
			'table_class' => 'xsmall-text'
		);
		
		$table['descriptor'] = array(
			'ID' => array(
				'field' => 'id', 
				'linkto' => array('url' => '/member/pixel/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Name' => array(
				'field' => 'name', 
				'linkto' => array('url' => '/member/pixel/?view=edit&id={VALUE}', 'value_field' => 'id'),
			),
			'Pixel' => array('field' => 'pixel', 'if_empty' => 'Not Set', 'format' => 'code'),
			'Type' => array('field' => 'type'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='/member/pixel/?view=edit&id={ID}' title='Edit' class='btn btn-xs btn-default cmd-tip' style='cursor:pointer'><i class='fa fa-pencil-square-o'></i></a>
					<a onclick=\"bootbox.confirm('Are you sure you want to delete this Pixel?', function(result) { if (result===true) { window.location.href='/member/pixel/?view=delete&id={ID}'; }});\" class=\"btn btn-xs btn-danger cmd-tip\" title=\"Delete\" style=\"cursor:pointer;\">
					<i class='fa fa-trash-o'></i></a>",
					'value_field' => array('ID' => 'id'),
					'class' => 'col-xs-1'
				),
			)
		);
		
		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	private function edit() {
		$pixel = Pixel::find($this->request->input('id'));

		$header = array(
			'icon' => '<i class="fa fa-bolt"></i>',
			'title' => $pixel->get_name(),
			'desc' => 'Edit Pixel'
		);

		return view ('member.pixel.new', ['header' => $header, 'pixel' => $pixel]);
	}

	private function save() {
		$pixel = Pixel::save_pixel($this->user, $this->request->input("pixel"));

		if(is_null($pixel)) {
			die("Save failed");
		}

		return redirect("/member/pixel/?view=manage");
	}

	private function delete() {
		$result = Pixel::delete_pixel($this->user, $this->request->input('id'));
		if(!$result) {
			return redirect("/member/pixel/?view=manage&msg=nofound");
		}				
		return redirect("/member/pixel/?view=manage&msg=deleted");
	}
}
?>