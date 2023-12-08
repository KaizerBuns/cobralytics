<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class MonitorList extends Model {

	protected $table = 'monitor_list';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function is_owner(User $user) {
		if($this->user_id == $user->id) {
			return true;
		}
		
		return false;
	}

	public static function findWhere(User $user, $params) {
		$where = array();
		$where['user_id'] = $user->id;

		$where = array_merge($where, $params);
		return self::where($where)->first();
	}
		
	public static function get_monitor_list(User $user, $params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 25);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$date = date("Y-m-d");
		$bind = $where = $or = [];
		
		$where[] = "user_id = :user_id";
		$bind['user_id'] = $user->id;

		$sql = "SELECT  *
				FROM monitor_list
				";

		if(isset($search['status']) && $search['status']) {
			$where[] = "status = :status";
			$bind['status'] = $search['status'];
		}

		if(isset($search['name']) && $search['name']) {
			$where[] = "url LIKE :url";
			$bind['url'] = '%'.$search['name'].'%';
		}

		if(count($where)) {
			$sql.= " WHERE ".implode(" AND ", $where);
		}
		
		$sql.= " GROUP BY id";
		$sql.= " ORDER BY {$sort} ".($order ? 'DESC' : 'ASC');
		
		$offset = (($page - 1) * $limit);
		$sql.= " LIMIT {$offset}, {$limit}";

		return DB::select( DB::raw($sql), $bind);
	}
	
	public static function bulk_save(User $user, Array $monitor) 
	{
		$results = array();
		foreach($monitor['list'] as $url) 
		{
			$list = array(
				'source_id' => 0,
				'url' => self::clean_domain($url),
				'user_id' => $user->id,
				'alert' => $monitor['alert'],
				'email' => $monitor['email'],
				'sms' => $monitor['sms'],			
				'status' => 'unknown'
			);

			$monitor_list = MonitorList::whereUrl($url)->first();
			if(is_null($monitor_list)) 
			{
				$monitor_list = new MonitorList();
				$monitor_list->fill($list);
				$monitor_list->save();
				
				$id = $monitor_list->id;
				$status = 'Success';
			} elseif($monitor_list) {
				$id = 0;
				$status = 'Duplicate';
			} else {
				$id = 0;
				$status = 'Error';
			}

			$results[] = array(
				'id' => $id,
				'name' => $url,
				'status' => $status
			);
		}

		return $results;
	}
	
	public static function save_monitor(User $user, $data) 
	{
		$monitor_list = new MonitorList();
		if(isset($data['id']) && (int)$data['id']) {
			$monitor_list = MonitorList::find($data['id']);
		}
		
		$data['url'] = self::clean_domain($monitor_list->url);
		$monitor_list->fill($data);
		$monitor_list->user_id = $user->id;		
		$monitor_list->save();
		
		return $monitor_list;		
	}

	public static function clean_domain($name) 
	{
		return \App\Helpers\MyHelper::clean_domain($name);
	}
}