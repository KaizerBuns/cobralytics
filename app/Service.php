<?php namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Service extends Model {

	protected $table = 'services';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function is_owner(User $user) {
		if ($this->user_id == $user->id) {
			return true;
		}

		return false;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_type() {
		return 'service';
	}

	public function get_service_sources() {
		return Source::where('service_id', '=', $this->id)->get();
	}

	public static function get_default_service_by_user_id($user_id) {
		if (!$object = self::where(array('user_id' => $user_id, 'is_default' => 1))->first()) {
			return false;
		}

		return $object;
	}

	public function add_source_to_service(Source $source) {
		//clean out old rule ips
		$source->delete_rule_ips();

		//new rule ips
		if ($this->id > 0) {
			$rules = $this->get_rules(true);
			foreach ($rules as $rule) {
				if ($rule->type == 'ip') {
					$source->save_ip_record($rule);
				}
			}
		}
	}

	public static function get_services(User $user, $params = array()) {

		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int) $params['order'] : 1);
		$bind = $where = $or = [];
		$having = '';

		$where[] = "s.user_id = :user_id";

		if (isset($search['id']) && $search['id']) {
			$or[] = "s.id = :id";
			$bind['id'] = $search['id'];
		}

		if (isset($search['name']) && $search['name']) {
			$or[] = "s.name LIKE :name";
			$bind['name'] = '%' . $search['name'] . '%';
		}

		if (isset($search['show'])) {
			if ($search['show'] == 1) {
				$having = "rule_count = 0";
			} else if ($search['show'] == 2) {
				$having = "rule_count > 1";
			}
		}

		$sql = "SELECT
			s.id,
			s.name,
			s.created_at,
			s.updated_at,
			count(r.id) rule_count,
			if(is_monetizer = 1, concat(s.name,' <span class=\"label label-success\">Optimizer</span>'), s.name) name_optimizer
			FROM services s
			LEFT JOIN rules r ON (r.rule_type = 'service' AND r.rule_type_id = s.id)";
		if (count($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		if (count($or)) {
			$sql .= " AND (" . implode(" OR ", $or) . ")";
		}

		$sql .= " GROUP BY s.id";

		if ($having) {
			$sql .= " HAVING {$having}";
		}

		$sql .= " ORDER BY {$sort} " . ($order ? 'DESC' : 'ASC');

		if ($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql .= " LIMIT {$offset}, {$limit}";
		}

		$bind['user_id'] = $user->id;
		return DB::select(DB::raw($sql), $bind);
	}

	public function get_rules($return_objects = false) {
		return Rule::get_rules('service', $this->id, $return_objects);
	}

	public function get_rules_by_country($active = 1) {
		return Rule::get_rules_by_country('service', $this->id, $active);
	}

	public function has_iprule() {
		return Rule::has_iprule('service', $this->id);
	}

	public static function reset_default(User $user) {
		DB::table('services')->where('user_id', $user->id)->update(['is_default' => 0]);
	}

	public function activate_rules() {
		$sql = "UPDATE rules SET active = 1 WHERE rule_type = 'service' AND rule_type_id = {$this->id} AND active = 0";
		DbRecord::query($sql, array('connection' => 'default'));
	}

	public static function save_service(User $user, $data) {
		if (isset($data['is_default']) && $data['is_default']) {
			self::reset_default($user);
		}

		$service = new Service();
		if (isset($data['id']) && (int) $data['id']) {
			$service = Service::find($data['id']);
		}

		$service->fill($data);
		$service->user_id = $user->id;
		$service->is_default = (isset($data['is_default']) ? 1 : 0);
		$service->save();

		return $service;
	}

	public function delete_service(User $user, $service_id) {

		//Verify campaign;
		$service = Service::find(array('id' => $service_id));
		if (!$service->is_owner($user)) {
			return false;
		}

		//delete service rules
		DB::delete("DELETE FROM rules WHERE rule_type = 'service' AND rule_type_id = :id", array('id' => $service->id));

		//delete rules with service
		DB::delete('DELETE FROM rules WHERE service_id = :id', array('id' => $service->id));

		//delete service
		return $service->delete();
	}
}
