<?php namespace App\Http\Controllers\Member;

use App\DWDomains;
use App\Helpers\TableMap;
use App\Http\Controllers\MemberController;
use App\Source;
use App\User;
use Illuminate\Http\Request;

class AdminController extends MemberController {

	public function index() {
		if (!$this->user->is_admin()) {
			return redirect("/member/project/?view=manage&msg=denied");
		}

		$view = $this->request->input('view');
		$section = $this->request->input('section');

		switch ($section) {
		case 'user':
			switch ($view) {
			case 'new':
				return $this->user_create();
				break;
			case 'edit':
				return $this->user_edit();
				break;
			case 'save':
				return $this->user_save();
				break;
			case 'manage':
				return $this->user_manage();
				break;
			case 'loginas':
				return $this->user_loginas();
				break;
			}
		case 'domains':
			return $this->manage_domains();
			break;
		case 'dnswings':
			return $this->dnswings_domains();
			break;
			break;
		}
	}

	public function user_create() {
		$header = array(
			'icon' => '<i class="fa fa-wrench"></i>',
			'title' => 'Admin',
			'desc' => 'New User',
		);

		$account = new User();
		$parent = User::get_parents();

		return view('member.admin.user.new', ['header' => $header, 'account' => $account, 'parent' => $parent]);
	}

	public function user_edit() {
		$account = User::find($this->request->input('id'));

		$header = array(
			'icon' => '<i class="fa fa-wrench"></i>',
			'title' => $account->email,
			'desc' => 'Edit User',
		);

		$parent = User::get_parents();
		return view('member.admin.user.new', ['header' => $header, 'account' => $account, 'parent' => $parent]);
	}

	public function user_save() {
		$user = User::save_user($this->request->input("account"));

		if (is_null($user)) {
			die('Saved failed');
		}

		return redirect("/member/admin/?section=user&view=manage&msg=saved");
	}

	public function user_manage() {
		$header = array(
			'icon' => '<i class="fa fa-wrench"></i>',
			'title' => 'Admin',
			'desc' => 'Manage Users',
		);

		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $this->request->input('search'),
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = User::get_users($params);

		$table['params'] = array(
			'table_id' => 'table-users',
			'action_url' => "/member/admin/?section=user&view=manage",
		);

		$table['descriptor'] = array(
			'ID' => array('field' => 'id'),
			'Email' => array(
				'html' => array(
					'html' => "<a href='javascript:void(0);' title='Login as {NAME}' class='cmd-tip' onclick=\"bootbox.confirm('Are you sure you want to login as {EMAIL}? You will be signed out of this account, continue?', function(result) { if (result===true) { window.location.href='/member/admin/?section=user&view=loginas&id={ID}'; }});\"><i class='fa fa-user'></i></a>&nbsp;<a href='/member/admin/?section=user&view=edit&id={ID}'>{EMAIL}</a>",
					'value_field' => array('ID' => 'id', 'NAME' => 'name', 'EMAIL' => 'email'),
				),
			),
			'Name' => array('field' => 'name'),
			'Status' => array('field' => 'status'),
			'Type' => array('field' => 'user_type'),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	public function user_loginas() {
		$account = User::find($this->request->input('id'));

		if (!$account->default_project_id) {
			$redirect = "/member/project/?view=new";
		} else {
			$redirect = "/member/project/?view=manage";
		}

		$account->set_default_project($account->default_project_id);
		\Auth::loginUsingId($account->id);

		return redirect($redirect);
	}

	private function manage_domains() {
		$header = array(
			'icon' => '<i class="fa fa-wrench"></i>',
			'title' => 'Admin',
			'desc' => "Manage Domains",
		);

		$search = array(
			'type' => 'domain',
		);

		$search = array_merge((array) $this->request->input('search'), $search);
		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $search,
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = Source::get_sources(null, $params);

		$table['params'] = array(
			'table_id' => 'table-allsources',
			'action_url' => "/member/admin/?section=domains&view=manage",
		);

		$table['descriptor'] = array(
			'ID' => array('field' => 'id'),
			'Name' => array('field' => 'name'),
			'Username' => array(
				'html' => array(
					'html' => "<a href='javascript:void(0);' title='Login as {NAME}' class='cmd-tip' onclick=\"bootbox.confirm('Are you sure you want to login as {EMAIL}? You will be signed out of this account, continue?', function(result) { if (result===true) { window.location.href='/member/admin/?section=user&view=loginas&id={ID}'; }});\"><i class='fa fa-user'></i></a>&nbsp;<a href='/member/admin/?section=user&view=edit&id={ID}'>{EMAIL}</a>",
					'value_field' => array('ID' => 'user_id', 'NAME' => 'user_name', 'EMAIL' => 'user_email'),
				),
			),
			'DNS' => array(
				'html' => array(
					'html' => "{DNS}",
					'value_field' => array(
						'DNS' => 'dns_records',
					),
					'class' => 'col-xs-3',
				),
			),
			'Created' => array('field' => 'created_at', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_at', 'format' => 'nice-date-time'),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.manage', ['header' => $header, 'tablemap' => $tablemap]);
	}

	private function dnswings_domains() {
		$header = array(
			'icon' => '<i class="fa fa-wrench"></i>',
			'title' => 'Admin',
			'desc' => "Manage Domains",
		);

		if (!$this->request->input('submit')) {
			$_REQUEST['search']['date_start'] = date("Y-m-d", strtotime("-7 days"));
			$_REQUEST['search']['date_end'] = date("Y-m-d");
		}

		$search = (array) $this->request->input('search');
		$params = array(
			'limit' => $this->user->pref_page_limit,
			'page' => $this->page_number,
			'search' => $search,
			'sort' => $this->request->input('sort'),
			'order' => $this->request->input('order'),
		);

		$table['results'] = DWDomains::get_sources($params);

		$table['params'] = array(
			'table_id' => 'table-allsources',
			'action_url' => "/member/admin/?section=domains&view=manage",
		);

		$table['descriptor'] = array(
			'ID' => array('field' => 'id'),
			'Name' => array('field' => 'domain'),
			'Type' => array('field' => 'type'),
			'DNS' => array('field' => 'ns_servers'),
			'Registar' => array('field' => 'registrar'),
			'Created' => array('field' => 'created_on', 'format' => 'nice-date-time'),
			'Updated' => array('field' => 'updated_on', 'format' => 'nice-date-time'),
			'Expires' => array('field' => 'expires_on', 'format' => 'nice-date-time'),
			'Actions' => array(
				'html' => array(
					'html' => "
					<a href='https://www.whoxy.com/{DOMAIN}'  title='Verify Expiry Date' class='cmd-tip' target='_new'>Verify</a> |
					<a href='javascript:void(0)' title='View Raw Details' class='cmd-tip' onclick=\"bootbox.alert({title:'Whois Details',message:'{JSON}'})\">Details</span>",
					'value_field' => array(
						'JSON' => array('field' => 'whois_raw', 'format' => 'json_decode_sort'),
						'EXPIRES' => array('field' => 'expires_on', 'format' => 'nice-date-time'),
						'DOMAIN' => 'domain',
					),
					'if_empty' => '',
				),
				'sort_field' => 'expires_on',
			),
		);

		$tablemap = TableMap::create($table['results'], $table['descriptor'], $table['params']);
		return view('shared.dnswings', ['header' => $header, 'tablemap' => $tablemap]);
	}
}
?>