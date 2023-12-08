<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DWDomains extends Model {

	protected $table = 'dnswings_zone_domains';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	public $timestamps = false;

	public function get_name() {
		return $this->name;
	}

	public static function findWhere(User $user, $params) {
		$where = array();
		$where['user_id'] = $user->id;

		$where = array_merge($where, $params);
		return self::where($where)->first();
	}
		
	public static function get_sources($params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 25);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$date = date("Y-m-d");
		$bind = $where = $or = [];

		$sql = "SELECT 
					id,
					domain,
					type,
					ns_servers,
					updated_on,
					created_on,
					expires_on,
					registrar,
					whois_raw 
				FROM dnswings_zone_domains 
				WHERE 1";

		if(isset($search['type']) && $search['type']) {
			$where[] = "type = :type";
			$bind['type'] = $search['type'];
		}

		if(isset($search['date_start']) && isset($search['date_end'])) {
			$where[] = "expires_on between :start AND :end";
			$bind['start'] = date("Y-m-d", strtotime($search['date_start']));
			$bind['end'] = date("Y-m-d", strtotime($search['date_end']));
		} else {
			if(isset($search['expire_days']) && $search['expire_days'] == -1) {
				$where[] = "expires_on <= :now";
				$bind['now'] = date("Y-m-d");
			} else if(isset($search['expire_days']) && $search['expire_days'] < -1) {
				$expires = date("Y-m-d", strtotime($search['expire_days'] ." days"));
				$where[] = "expires_on between :expires AND :now";
				$bind['expires'] = $expires;
				$bind['now'] = date("Y-m-d");
			} else if(isset($search['expire_days']) && $search['expire_days'] > 0) {
				$expires = date("Y-m-d", strtotime("+" . $search['expire_days'] ." days"));
				$where[] = "expires_on between :now AND :expires";
				$bind['expires'] = $expires;
				$bind['now'] = date("Y-m-d");
			}
		}

		if(isset($search['name']) && $search['name']) {
			$or[] = "domain LIKE :name";
			$or[] = "ns_servers LIKE :ns_servers";
			$bind['name'] = $bind['ns_servers'] = '%'.$search['name'].'%';
		}

		if(count($where)) {
			$sql.= " AND ".implode(" AND ", $where);
		}

		if(count($or)) {
			$sql.= " AND (".implode(" OR ", $or).")";
		}
		
		$sql.= " ORDER BY {$sort} ".($order ? 'DESC' : 'ASC');
		
		$offset = (($page - 1) * $limit);
		$sql.= " LIMIT {$offset}, {$limit}";

		return DB::select( DB::raw($sql), $bind);
	}

	public static function update_whois(DWDomains $domain, $data) {

		$expire_date = null;
		if(isset($raw['registry_expiry_date'])) {
		    $expire_date = $raw['registry_expiry_date'];
		} elseif(isset($raw['expiration_date'])) {
		    $expire_date = $raw['expiration_date'];
		} elseif(isset($raw['expiration_time'])) {
		    $expire_date = $raw['expiration_time'];
		}elseif(isset($raw['registrar_registration_expiration_date'])) {
		    $expire_date = $raw['registrar_registration_expiration_date'];
		}

		$registrar = '';    
		if(isset($raw['sponsoring_registrar'])) {
		    $registrar = $raw['sponsoring_registrar'];
		} elseif(isset($raw['registrar'])) {
		    $registrar = $raw['registrar'];
		}

		$res->registrar = $registrar;

		if(isset($raw['updated_date'])) {
		    $res->updated_on = date("Y-m-d", strtotime($raw['updated_date']));
		}

		if(isset($raw['creation_date'])) {
		    $res->created_on = date("Y-m-d", strtotime($raw['creation_date']));
		}

		if($expire_date) {
		    $res->expires_on = date("Y-m-d", strtotime($expire_date));
		    $processed = 1;
		} else {
		    $processed = 2;
		}

		$res->whois_raw = json_encode($this->utf8ize($result['rawdata']));
		$res->whois_processed = $processed;
		$res->whois_last_update = date("Y-m-d");
		$res->save();
		$this->info("Updating whois - {$res->domain}");
	}
}
