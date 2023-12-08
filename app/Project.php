<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Project extends Model {

	protected $table = 'projects';
	protected $fillable = ['user_id', 'name', 'description'];
	protected $guarded = [];
	protected $hidden = [];
	
	public function get_name() {
		return $this->name;
	}
	
	public function is_owner(User $user) {
		if($this->user_id == $user->id) {
			return true;
		}
	}

	public static function get_projects(User $user) {

		$sql = "SELECT 
				p.*,  
				count(c.id) as campaign_count, 
				(SELECT count(id) FROM sources WHERE project_id = p.id AND type = 'traffic') as traffic_count,
				(SELECT count(id) FROM sources WHERE project_id = p.id AND type = 'domain') as domain_count 
				FROM projects p 
				LEFT JOIN campaigns c ON (c.project_id = p.id) 
				WHERE p.user_id = :user_id 
				GROUP BY p.id";

		$bind = array('user_id' => $user->id);
		return DB::select( DB::raw($sql), $bind);
	}
}
