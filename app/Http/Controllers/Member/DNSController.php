<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\DNSRecord;
use App\Source;
use App\Helpers\TableMap;

class DNSController extends MemberController {

	public function index() {
		$view = $this->request->input('view');
		$this->object = Source::find($this->request->input('object_id'));

		if(is_null($this->object) || !$this->object->is_owner($this->user)) {
			return redirect('/member/domain/?view=view&id='.$this->request->input('object_id'));
		}

		switch($view) {
			case 'new':
			case 'edit':
				return $this->create();
				break;
			case 'save':
				return $this->save();
				break;
			case 'edit':
				break;
			case 'delete':
				return $this->delete();
				break;			
		}
	}

	public function create() {
		$header = array(
			'title' => 'DNS',
			'desc' => 'New DNS'
		);

		return view('member.dns.new', ['header' => $header, 'object' => $this->object]);
	}

	public function save() {
		DNSRecord::save_dns($this->object, $this->request->input('dns'));
		return redirect("/member/domain?view=view&id=".$this->request->input('object_id')."&msg=saved");
	}

	public function delete() {
		DNSRecord::delete_dns($this->request->input('id'));
		return redirect("/member/domain/?view=view&id=".$this->request->input('object_id')."&msg=saved");
	}
}	
?>