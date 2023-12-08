<?php
namespace App;
use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use Notifiable;

	protected $table = 'users';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = ['password', 'remember_token'];

	public function get_name() {
		$name = "$this->name";
		if (trim($name)) {
			return $name;
		} else {
			return $this->email;
		}
	}

	public function has_first_project() {
		$projects = Project::get_projects($this);
		if (count($projects) == 0) {
			return false;
		}

		return true;
	}

	public function is_piwik() {
		if ($this->piwik_login) {
			return true;
		}

		return false;
	}

	public function toggle_welcome() {
		$this->pref_show_welcome = 1;
		$this->save();
	}

	public function get_address($noaddress = false) {
		if ($noaddress) {
			$location = array($this->city, $this->state, $this->country);
		} else {
			$location = array($this->address, $this->address2, $this->city, $this->state, $this->country);
		}
		$location = array_remove_empty($location);
		if (empty($location)) {
			return 'N/A';
		}
		return implode(", ", $location);
	}

	public function load_user($id) {
		if ($user = User::find($id)) {
			return $user;
		}
		return false;
	}

	public function load_user_by_email($email) {
		if ($user = User::where('email', '=', $email)->firstOrFail()) {
			return $user;
		}
		return false;
	}

	public function is_admin() {
		//future roles
		if ($this->user_type == 'admin' || $this->user_type == 'master') {
			return true;
		}

		if ($this->is_admin == 1) {
			return true;
		}

		return false;
	}

	public function is_active() {
		if ($this->status == 'active') {
			return true;
		}
		return false;
	}

	public function is_pending() {
		if ($this->status == 'pending') {
			return true;
		}
		return false;
	}

	public function is_twitter_user() {
		if ($this->twitter_id) {
			return true;
		}
		return false;
	}

	public function is_facebook_user() {
		if ($this->facebook_id) {
			return true;
		}
		return false;
	}

	public function is_gplus_user() {
		if ($this->gplus_id) {
			return true;
		}
		return false;
	}

	public static function get_parents() {
		return self::where('user_type', 'admin')
			->orderBy('name', 'desc')
			->get();
	}

	public static function get_users($params = array()) {

		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int) $params['order'] : 1);
		$bind = $where = $or = [];

		if (isset($search['name']) && $search['name']) {
			$where[] = "name LIKE :name OR email like :email";
			$bind['name'] = $bind['email'] = '%' . $search['name'] . '%';
		}

		$sql = "SELECT u.* FROM users u";
		if (count($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		$sql .= " ORDER BY $sort " . ($order ? 'DESC' : 'ASC');
		if ($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql .= " LIMIT $offset, $limit";
		}

		return DB::select(DB::raw($sql), $bind);
	}

	public function get_default_project($return_object = false) {

		if (!$project_id = $this->get_settings('default_project_id')) {
			$project_id = $this->default_project_id;
		}

		if ($this->default_project_id == 0) {
			$project = \App\Project::where(array('user_id' => $this->id))->first();
		} else {
			if (!$project = \App\Project::find($project_id)) {
				return false;
			}
		}

		if (!$project instanceof Project) {
			return false;
		}

		if (!$project->is_owner($this)) {
			return false;
		}

		if ($return_object) {
			return $project;
		}

		return $project->id;
	}

	public function set_default_project($project_id = 0) {
		if ($project_id == 0) {
			$project_id = $this->get_default_project();
		}

		if (!$project = Project::find($project_id)) {
			return false;
		}

		if (!$project->is_owner($this)) {
			return false;
		}

		$this->set_settings(array('default_project_id' => $project->id));
	}

	public function get_settings($name = '') {

		//default settings
		$settings = array(
			'default_project_id' => $this->default_project_id,
			'pref_quick_menu' => $this->pref_quick_menu,
			'pref_show_welcome' => $this->pref_show_welcome,
			'pref_page_limit' => $this->pref_page_limit,
			'pref_alerts' => $this->pref_alerts,
		);

		if (isset($_COOKIE['_cbsettings_']) && $_COOKIE['_cbsettings_']) {
			$cookie = json_decode(stripslashes($_COOKIE['_cbsettings_']));
			if (is_array($cookie) && count($cookie)) {
				foreach ($cookie as $name => $value) {
					$settings[$name] = $value;
				}
			}
		}

		if (isset($settings[$name])) {
			return $settings[$name];
		}

		return $settings;
	}

	public function set_settings($params = array()) {
		$settings = array(
			'default_project_id' => $this->default_project_id,
			'pref_quick_menu' => $this->pref_quick_menu,
			'pref_show_welcome' => $this->pref_show_welcome,
			'pref_page_limit' => $this->pref_page_limit,
			'pref_alerts' => $this->pref_alerts,
		);

		foreach ($params as $name => $value) {
			$settings[$name] = $value;
		}

		$this->fill($settings);
		$this->save();

		setcookie('_cbsettings_', json_encode($settings), time() + 60 * 60 * 24, '/');
	}

	public function is_enabled($feature) {
		$feature = "enable_{$feature}";
		if ($this->$feature) {
			return true;
		}

		return false;
	}

	public static function save_user($params) {
		if (isset($params['id']) && $params['id']) {
			$user = User::find($params['id']);
		} else {
			$user = new User();
		}

		$user->fill($params);
		//update password
		if (($params['password'] && $params['confirm_password']) && $params['password'] == $params['confirm_password']) {
			$user->password = \Hash::make($params['password']);
		} else {
			unset($user->password);
		}

		if (isset($params['pref_show_welcome'])) {
			$user->pref_show_welcome = 1;
		}
		if (isset($params['pref_alerts'])) {
			$user->pref_alerts = 1;
		}
		if (isset($params['enable_offers'])) {
			$user->enable_offers = 1;
		}
		if (isset($params['enable_campaigns'])) {
			$user->enable_campaigns = 1;
		}
		if (isset($params['enable_analytics'])) {
			$user->enable_analytics = 1;
		}
		if (isset($params['enable_reports'])) {
			$user->enable_reports = 1;
		}
		if (isset($params['enable_monitors'])) {
			$user->enable_monitors = 1;
		}
		if (isset($params['is_admin'])) {
			$user->is_admin = 1;
		}

		unset($user->confirm_password); //don't need it
		return $user->save();
	}
}
