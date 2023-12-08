<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vertical extends Model {

	protected $table = 'verticals';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function get_name() {
		return $this->name;
	}

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
		return Vertical::where($where)->first();	
	}

	public static function get_verticals(User $user, $params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$bind = $where = $or = [];

		$where[] = "user_id = :user_id";

		if(isset($search['name']) && $search['name']) {
			$or[] = "id LIKE :id";
			$or[] = "name LIKE :name";
			$bind['id'] = $bind['name'] = '%'.$search['name'].'%';
		}
		
		$sql = "SELECT *, if(internal = 1, concat(name,' <span class=\"label label-success\">Internal</span>'), name) name_internal FROM verticals";
		
		if(count($where)) {
			$sql.= " WHERE ".implode(" AND ", $where);
		}

		if(count($or)) {
			$sql.= " AND (".implode(" OR ", $or) .")";
		}

		$sql.= " ORDER BY $sort ".($order ? 'DESC' : 'ASC');
		if($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql.= " LIMIT $offset, $limit";
		}
		
		$bind['user_id'] = $user->id;
		
		return DB::select( DB::raw($sql), $bind);
	}

	public static function save_vertical(User $user, $data) {		
		
		$vertical = new Vertical();
		if(isset($data['id']) && (int)$data['id']) {
			$vertical = Vertical::find($data['id']);
		}
		
		$vertical->fill($data);
		$vertical->user_id = $user->id;
		$vertical->internal = (isset($data['internal']) ? 1 : 0);

		return $vertical->save();
	}
}
