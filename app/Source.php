<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DNSRecord;
use DB;

class Source extends Model {

	protected $table = 'sources';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function is_owner(User $user) {
		if($this->user_id == $user->id) {
			return true;
		}
		
		return false;
	}

	public function get_type() {
		return 'source';
	}

	public function is_domain() {
		if($this->type == 'domain') {
			return true;
		}
	}

	public function get_ad_content () {
		return 'redirect';
	}

	public function get_name() {
		return $this->name;
	}

	public static function findWhere(User $user, $params) {
		$where = array();
		$where['user_id'] = $user->id;

		$where = array_merge($where, $params);
		return self::where($where)->first();
	}
		
	public static function get_sources(User $user = null, $params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 25);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$date = date("Y-m-d");
		$bind = $where = $or = [];

		if($user instanceof User) {
			$where[] = "s1.user_id = :user_id";
			$bind['user_id'] = $user->id;
		}

		if(isset($search['project_id'])) {
			if($search['project_id']) {
				$where['project_id'] = "s1.project_id = :project_id";	
				$bind['project_id'] = $search['project_id'];
			} else {
				$where['project_id'] = "s1.project_id > 0";	
			}
		}

		$sql = "SELECT 
					s1.id,
					s1.name,
					s1.user_id,
					u.email as user_email,
					u.name as user_name,
					s1.created_at,
					s1.updated_at,
					if(s2.name IS NOT NULL, s2.name, '') service_name, 
					if(p.name IS NOT NULL, p.name, '') project_name,
					group_concat(DISTINCT CONCAT(r.name,' (', r.content,')') SEPARATOR '<BR>') as dns_records 
				FROM sources s1 
				LEFT JOIN users u ON (u.id = s1.user_id) 
				LEFT JOIN services s2 ON (s2.id = s1.service_id) 
				LEFT JOIN projects p ON (p.id = s1.project_id) 
				LEFT JOIN cobracmd_dns.records r ON (r.domain_id = s1.id AND r.type = 'A') 
				";

		if(isset($search['type']) && $search['type']) {
			switch($search['type']) {
				case 'traffic':
					$type = 'traffic';
					break;
				case 'domain':
				default:
					$type = 'domain';
					break;
			}

			$where[] = "s1.type = :type";
			$bind['type'] = $type;
		}

		if(isset($search['name']) && $search['name']) {
			$or[] = "s1.name LIKE :source_name";
			$or[] = "r.name LIKE :record_name";
			$or[] = "r.content LIKE :ip_name";
			$bind['source_name'] = $bind['record_name'] = $bind['ip_name'] = '%'.$search['name'].'%';
		}

		if(isset($search['group']) && $search['group']) {
			switch($search['group']) 
			{
				case '09':
					$where[] = "s1.name REGEXP '^[0-9]'";
					break;
				case 'AE':
					$where[] = "s1.name REGEXP '^[a-e]'";
					break;
				case 'FJ':
					$where[] = "s1.name REGEXP '^[f-j]'";
					break;
				case 'KO':
					$where[] = "s1.name REGEXP '^[k-o]'";
					break;
				case 'PT':
					$where[] = "s1.name REGEXP '^[p-t]'";
					break;
				case 'UZ':
					$where[] = "s1.name REGEXP '^[u-z]'";
					break;
			}
			
		}

		if(count($where)) {
			$sql.= " WHERE ".implode(" AND ", $where);
		}

		if(count($or)) {
			$sql.= " AND (".implode(" OR ", $or).")";
		}
		
		$sql.= " GROUP BY s1.id";
		$sql.= " ORDER BY {$sort} ".($order ? 'DESC' : 'ASC');
		
		$offset = (($page - 1) * $limit);
		$sql.= " LIMIT {$offset}, {$limit}";

		return DB::select( DB::raw($sql), $bind);
	}
	
	public static function bulk_save(User $user, Array $sources, $service_id = 0, $project_id = 0, $type = 'domain') {
		$results = array();

		//clean names first
		//$clean_sources = array();
		//foreach($sources as $name) 
		//{
		//	$name = self::clean_domain($name);
		//	$clean_sources[$name] = $name;
		//}

		foreach($sources as $name) 
		{
			$list = array(
				'name' => self::clean_domain($name),
				'user_id' => $user->id,
				'service_id' => (int)$service_id,
				'project_id' => (int)$project_id,
				'type' => $type,
				'active' => 1
			);

			if($type == 'traffic') {
				$source = Source::findWhere($user, array('name' => $list['name']));
			} else {
				$source = Source::whereName($list['name'])->first();
			}
			if(is_null($source)) 
			{
				$source = new Source();
				$source->fill($list);
				$source->save();
				$source->default_dns(true);

				$id = $source->id;
				$status = 'Success';
			} elseif($source) {
				$id = 0;
				$status = 'Duplicate';
			} else {
				$id = 0;
				$status = 'Error';
			}

			$results[] = array(
				'id' => $id,
				'name' => $list['name'],
				'status' => $status
			);
		}

		return $results;
	}
	
	public function default_dns($clean = false) {

		//traffic sources do not create DNS entries
		if($this->type == 'traffic') {
			return;
		}
		
		if($clean) {
			DB::delete('DELETE FROM cobracmd_dns.records WHERE domain_id = :id', array('id' => $this->id));
		}
		
		$datetime = date("Y-m-d H:i:s");
		$soa_date = date("Ymd").'01';
		
		$cobra_ips = array(
			getenv('COBRA_IP1'),
			getenv('COBRA_IP2')
		);

		$setting = array();
		foreach($cobra_ips as $cobra_ip) {
			$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'A', 'content' => $cobra_ip, 'ttl' => 300);		
		}
		
		$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'MX', 'content' => "mail.cobralytics.com", 'ttl' => 900);
		$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'NS', 'content' => getenv('COBRA_NS1'), 'ttl' => 3600);
		$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'NS', 'content' => getenv('COBRA_NS2'), 'ttl' => 3600);

		//SOA primary hostmaster serial refresh retry expire default_ttl
		//refresh	10800 (3 hours)
		//retry	3600 (1 hour)
		//expire	604800 (1 week)
		//default_ttl	3600 (1 hour)

		$soa = getenv('COBRA_NS1')." admin@cobralytics.com {$soa_date} 10800 3600 604800 3600";
		$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'SOA', 'content' => $soa, 'ttl' => 3600);
		
		foreach($setting as $d) {
			$add = true;
			if($d['type'] == 'SOA') {
				$add = false;
				$dns_exists = DNSRecord::where(array('domain_id' => $this->id, 'type' => 'SOA'))->first();	
				if(is_null($dns_exists)) {
					$add = true;
				}
			}

			if($add) {
				$r = new DNSRecord();
				$r->fill($d);
				$r->prio = 0;
				$r->rule_id = 0;
				$r->internal = ($d['type'] == 'MX' ? 0 : 1);
				$r->save();
			}
		}

		return true;
	}
	
	public function get_rules($return_objects = false) {		
		return Rule::get_rules('source', $this->id, $return_objects);
	}
	
	public function get_rules_by_country($active = 1) {
		return Rule::get_rules_by_country('source', $this->id, $active);
	}
	
	public function activate_rules() {
		$sql = "UPDATE rules SET active = 1 WHERE rule_type = 'source' AND rule_type_id = {$this->id} AND active = 0";
		DbRecord::query($sql, array('connection' => 'default'));
	}
	
	public function has_iprule() {
		return Rule::has_iprule('source', $this->id);
	}
	
	public function get_dns() {
		$sql = "SELECT * FROM cobracmd_dns.records WHERE domain_id = :id ORDER BY type, name";	
		return DB::select( DB::raw($sql), array('id' => $this->id));
	}

	/**
	 * Delete internal redirect engine ips when adding an IP service
	 */
	public function delete_internal_ips() {
		DB::delete("DELETE FROM cobracmd_dns.records WHERE domain_id = :id AND type = 'A' AND internal = 1", array('id' => $this->id));
	}
	
	public static function save_source(User $user, $data) 
	{
		$source = new Source();
		if(isset($data['id']) && (int)$data['id']) {
			$source = Source::find($data['id']);
		}
		
		$data['name'] = self::clean_domain($source->name);
		$source->fill($data);
		$source->user_id = $user->id;		
		$source->save();

		if(!$source->has_dns()) {
			$source->default_dns(true);
		}
				
		return $source;		
	}

	public static function clean_domain($name) 
	{
		return \App\Helpers\MyHelper::clean_domain($name);
	}
	
	public function set_service($service_id) {
		$service = Service::get_service('id', $service_id);
		$service->add_source_to_service($this);
	}

	public function has_dns() {
		$sql = "SELECT * FROM cobracmd_dns.records WHERE domain_id = :id LIMIT 1";
		$result = DB::select( DB::raw($sql), array('id' => $this->id));

		if(count($result) > 0) {
			return true;
		}

		return false;
	}
	
	public function delete_rule_ips() {
		DB::delete("DELETE FROM cobracmd_dns.records WHERE domain_id = :id AND type = 'A' AND rule_id > 0", array('id' => $this->id));
	}

	/**
	 * 
	 * @param Rule $rule
	 * @param type $type IP/CNAME
	 */
	public function save_ip_record(Rule $rule) {
		//Delete internal IPs
		$this->delete_internal_ips();
		
		//Add to records table;
		$setting = array();
		$setting[] = array('domain_id' => $this->id, 'name' => $this->name, 'type' => 'A', 'content' => $rule->ip_address, 'ttl' => 300);
		$setting[] = array('domain_id' => $this->id, 'name' => '*.'.$this->name, 'type' => 'A', 'content' => $rule->ip_address, 'ttl' => 300);
		
		foreach($setting as $d) {
			$r = new DNSRecord();
			$r->fill($d);
			$r->prio = 0;
			$r->rule_id = $rule->id;
			$r->internal = 1;
			$r->save();
		}
	}

	public function delete_source() {

		//DELETE DNS entries
		DB::delete("DELETE FROM cobracmd_dns.records WHERE domain_id = :id", array('id' => $this->id));

		//DELETE campaign associations
		DB::delete("DELETE FROM cobracmd.campaign_domains WHERE source_id = :id", array('id' => $this->id));

		//DELETE campaign associations
		DB::delete("DELETE FROM cobracmd.rules WHERE rule_type = 'source' AND rule_type_id = :id", array('id' => $this->id));

		$this->delete();
	}
}
