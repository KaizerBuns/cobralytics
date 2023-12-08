<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Creative extends Model {

	protected $table = 'creatives';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function is_owner(User $user) {
		if($user->id == $this->user_id) {
			return true;
		}

		return false;
	}

	public function is_local(){
		return $this->storage == 'local' ? true : false;
	}

	public function getPublicUrl() {
		return $this->file_url.'/'.$this->name;
	}

	public function getPublicThumbUrl() {
		return $this->file_url.'/'.$this->thumb;
	}

	public static function get_creatives(User $user, $params = array()) {
		
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
		
		$sql = "SELECT 
					*, 
					concat(file_url,'/',name) as public_url, 
					concat(file_url,'/',thumb) as public_thumb_url 
				FROM creatives";
		
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

	public static function save_creative(User $user, $data) {		
		
		$creative = new Creative();
		if(isset($data['id']) && (int)$data['id']) {
			$creative = Creative::find($data['id']);
		}
		
		$creative->fill($data);
		$creative->user_id = $user->id;

		return $creative->save();
	}
}
