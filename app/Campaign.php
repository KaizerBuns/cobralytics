<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Campaign extends Model {

	protected $table = 'campaigns';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function get_name() {
		return $this->name;
	}
	
	public function is_owner(User $user) {
		if($user->id == $this->user_id) {
			return true;
		}

		return false;
	}

	public function get_type() {
		return 'campaign';
	}

	public function get_ad_content() {
		return $this->content;
	}

	public function is_redirect() {
		return ($this->content == 'redirect' ? true : false);
	}

	public function is_banner() {
		return ($this->content == 'banner' ? true : false);
	}

	public function is_template() {
		return ($this->content == 'template' ? true : false);
	}

	public static function findWhere(User $user, $params) {
		$where = array();
		$where['user_id'] = $user->id;

		$where = array_merge($where, $params);
		$campaign = self::where($where)->first();
		if($campaign) {

			$sql = "SELECT cd.campaign_id, cd.source_id, cd.linkhash, s.name as source_name FROM campaign_domains cd 
			LEFT JOIN sources s ON (s.id = cd.source_id) WHERE cd.campaign_id = :id";
			
			$results = DB::select( DB::raw($sql), array('id' => $campaign->id));

			$domains = array();
			foreach($results as $r) {
				$domains[$r->source_id] = $r;
			}

			$campaign->domains = $domains;

			$sql = "SELECT * FROM campaign_pixels WHERE campaign_id = :id";
			$results = DB::select( DB::raw($sql), array('id' => $campaign->id));

			$pixels = array();
			foreach($results as $r) {
				$pixels[$r->pixel_id] = $r;
			}

			$campaign->pixels = $pixels;
			$campaign->rules = $campaign->get_rules(true);
		}

		return $campaign;
	}

	public static function get_campaign_by_hash($linkhash, $http_host = '') {

		$where = '';
		if($http_host) {
			$where = "AND d.name LIKE '%{$http_host}%'";
		}

		$sql = "SELECT 
				c.id,
				c.id campaign_id, 
				c.source_id, 
				s.name as source_name, 
				cd.source_id domain_id,
				d.name as domain_name,
				c.project_id, 
				c.user_id, 
				c.cost, 
				c.revenue, 
				c.type,
				c.content,
				c.tracking_url_append,
				cd.linkhash 
				FROM campaigns c 
				LEFT JOIN sources s ON (s.id = c.source_id) 
				LEFT JOIN campaign_domains cd ON (cd.campaign_id = c.id) 
				LEFT JOIN sources d ON (d.id = cd.source_id) 
				WHERE cd.linkhash = :hash {$where}
				AND c.active = 1 LIMIT 1";
				
		return Campaign::hydrate(DB::select(DB::raw($sql), array('hash' => $linkhash)))->first();
	}
	
	public static function get_campaigns(User $user, $params = array()) {
		
		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int)$params['order'] : 1);
		$bind = $where = $or = [];
		$having = '';

		$where[] = "user_id = '{$user->id}'";
		$where['project_id'] = "project_id = '{$user->get_default_project()}'";
		
		if(isset($search['project_id'])) {
			if($search['project_id']) {
				$where['project_id'] = "project_id = '{$search['project_id']}'";	
			} else {
				$where['project_id'] = "project_id > 0";	
			}
		}

		if(isset($search['name']) && $search['name']) {
			$or[] = "id LIKE :id";
			$or[] = "campaign_name LIKE :c_name";
			$or[] = "source_name LIKE :s_name";
			$or[] = "tracking_domains LIKE :t_domains";

			$bind = array(
				'id' => '%'.$search['name'].'%',
				'c_name' => '%'.$search['name'].'%',
				's_name' => '%'.$search['name'].'%',
				't_domains' => '%'.$search['name'].'%',
			);
		}
		
		if(isset($search['show'])) {
			if($search['show'] == 1) {
				$having = "rule_count = 0";				
			} else if($search['show'] == 2) {
				$having = "rule_count >= 1";				
			}
		}

		$sql = "SELECT * FROM (SELECT 
					c.id, 
					c.user_id,
					c.name,
					c.name as campaign_name, 
					p.id as project_id,
					p.name as project_name,
					c.linkhash, c.type, 
					c.medium, c.content, 
					c.cost, c.revenue, 
					c.created_at, 
					c.updated_at, 
					c.source_id, 
					s.name as source_name, 
					sum(if(r.type = 'creative' AND r.active = 1, 1, 0)) rotator_count, 
					sum(if(r.type != 'creative' AND r.active = 1, 1, 0)) rule_count, 
					(SELECT group_concat(name) FROM campaign_domains cd LEFT JOIN sources s ON (s.id = cd.source_id) WHERE cd.campaign_id = c.id) tracking_domains 
				FROM campaigns c
				LEFT JOIN sources s ON (s.id = c.source_id) 
				LEFT JOIN projects p ON (p.id = c.project_id) 
				LEFT JOIN rules r ON (r.rule_type = 'campaign' AND r.rule_type_id = c.id) 
				GROUP BY c.id) tmp ";
		
		if(count($where)) {
			$sql.= " WHERE ".implode(" AND ", $where);
		}

		if(count($or)) {
			$sql.= " AND (".implode(" OR ", $or) .")";
		}
		
		$sql.= " GROUP BY id";

		if($having) {
			$sql.= " HAVING {$having}";
		}

		$sql.= " ORDER BY {$sort} ".($order ? 'DESC' : 'ASC');
		if($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql.= " LIMIT {$offset}, {$limit}";
		}
		
		return DB::select( DB::raw($sql), $bind);
	}

	public static function delete_campaign(User $user, $campaign_id) {
		//Verify campaign;
		$campaign = Campaign::find($campaign_id);
		if(!$campaign->is_owner($user)) {
			return false;
		}

		//delete campaign_pixels
		DB::delete('DELETE FROM campaign_pixels WHERE campaign_id = :id', array('id' => $campaign->id));

		//deletee campaign_domains
		DB::delete('DELETE FROM campaign_domains WHERE campaign_id = :id', array('id' => $campaign->id));

		//delete campaign_rules
		DB::delete('DELETE FROM rules WHERE rule_type = :type AND rule_type_id = :id', array('type' => 'campaign', 'id' => $campaign->id));
		
		//delete rules with campaign
		DB::delete('DELETE FROM rules WHERE campaign_id = :id', array('id' => $campaign->id));

		//delete campaign
		return $campaign->delete();
	}

	public function get_3rdparty_pixels() {
		$sql = "SELECT p.* FROM pixels p LEFT JOIN campaign_pixels cp ON (cp.pixel_id = p.id) WHERE cp.campaign_id = '{$this->id}'";
		$results = DB::select(DB::raw($sql), array());
		return Pixel::hydrate($results);
	}

	/*
	public function has_rotators($type = 'creative') {
		$rotators = Rule::get_rules('campaign', $this->id, true, null, 1);

		if(count($rotators) > 0) {
			return true;
		}

		return false;
	}
	*/

	public function get_all_rotators() {
		return self::get_rotators_by_type('all');
	}

	public function get_rotators_by_type($type = null, $return_objects = false) {
		return Rule::get_rules('campaign', $this->id, $return_objects, $type, 1);
	}
	
	public function get_rules($return_objects = false) {
		return Rule::get_rules('campaign', $this->id, $return_objects);
	}
	
	public function get_rules_by_country($active = 1) {
		return Rule::get_rules_by_country('campaign', $this->id, $active);
	}
	
	public function has_iprule() {
		return Rule::has_iprule('campaign', $this->id);
	}

}
