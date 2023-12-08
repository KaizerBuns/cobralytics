<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class TemplateController extends MemberController {

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

		die('Create');
	}
}
?>