<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MemberController;

use App\Http\Modals;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\User;
use App\Helpers\TableMap;

class AccountController extends MemberController 
{
	public function index() {
		$view = $this->request->input('view');

		switch($view) {
			case 'save':
				User::save_user($this->request->input("user"));
				return redirect("/member/account/?msg=saved");
				break;
			default:
				$header = array(
					'title' => 'Profile',
					'desc' => $this->user->email
				);

				return view('shared.profile', ['header' => $header]);
				break;	
		}
	}
}
?>