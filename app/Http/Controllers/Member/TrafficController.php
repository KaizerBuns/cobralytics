<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Project;
use App\Helpers\TableMap;

class TrafficController extends SourceController {
	
	public function index() {
		$this->source_type = 'traffic';
		$this->source_type_name = 'Traffic Source';
		return parent::index();
	}
}
?>