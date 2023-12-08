<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pixel extends Model {

	protected $table = 'pixels';
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

	public static function get_pixels(User $user, $params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$bind = $where = $or = [];
		
		$where[] = "p.user_id = :user_id";
		$bind['user_id'] = $user->id;

		if(isset($search['name']) && $search['name']) {
			$or[] = "p.id LIKE :id";
			$or[] = "p.name LIKE :name";
			$bind['id'] = '%'.$search['name'].'%';
			$bind['name'] = '%'.$search['name'].'%';
		}
		
		$sql = "SELECT * FROM pixels p";
		if(count($where)) {
			$sql.= " WHERE ".implode(" AND ", $where);
		}

		if(count($or)) {
			$sql.= " AND (".implode(" OR ", $or).")";
		}
		$sql.= " ORDER BY {$sort} ".($order ? 'DESC' : 'ASC');
		if($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql.= " LIMIT {$offset}, {$limit}";
		}

		return DB::select( DB::raw($sql), $bind);
	}

	public static function save_pixel(User $user, $data) {	
		
		$pixel = new Pixel();
		if(isset($data['id']) && (int)$data['id']) {
			$pixel = Pixel::find($data['id']);
		}
		
		$pixel->fill($data);
		$pixel->pixel = stripslashes($pixel->pixel);
		$pixel->user_id = $user->id;
		return $pixel->save();
	}

	public function fire_pixel() {
		$pixel_code = $this->pixel;
		if (isset($_COOKIE['_networkCID_']) && $_COOKIE['_networkCID_']) {
			$pixel_code = str_replace("{CID}", $_COOKIE['_networkCID_'], $pixel_code);
		}

		//if (isset($_REQUEST['cid']) && $_REQUEST['cid']) {
		//	$pixel_code = str_replace("{CID}", $_REQUEST['cid'], $pixel_code);
		//}
		//if (isset($_REQUEST['CLICKID']) && $_REQUEST['CLICKID']) {
		//	$pixel_code = str_replace("{CLICKID}", $_REQUEST['CLICKID'], $pixel_code);
		//}

		switch($this->type) {
			case 'image':
			case 'iframe':
			case 'javascript':	
				echo stripslashes($pixel_code);
				break;
			case 's2s':
				$curl = new Curl();
				$curl->post($pixel_code);
				break;
		}
		return;
	}

	public static function delete_pixel(User $user, $pixel_id) {

		$pixel = Pixel::find($pixel_id);
		if(!$pixel->is_owner($user)) {
			return false;
		}

		//delete any assign pixels
		$sql = "DELETE FROM campaign_pixels WHERE pixel_id = '{$pixel->id}'";
		DB::statement($sql, array());

		return $pixel->delete();
	}
}
