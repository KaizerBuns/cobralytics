<?php namespace App;

use App\Advertiser;
use App\Helpers\MyHelper;
use App\Vertical;
use DB;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model {

	protected $table = 'offers';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function get_name() {
		return $this->name;
	}

	public function is_owner(User $user) {
		if ($this->user_id == $user->id) {
			return true;
		}

		return false;
	}

	public function get_type() {
		return 'offer';
	}

	public static function findWhere(User $user, $params) {
		$where = array();
		$where['user_id'] = $user->id;

		$where = array_merge($where, $params);
		return self::where($where)->first();
	}

	public static function get_offer_groups(User $user) {
		$sql = "SELECT
					group_name,
					count(id) total_offers
				FROM offers
				WHERE user_id = :user_id
				AND group_name != ''
				GROUP BY group_name
				HAVING total_offers > 0";

		$bind = [];
		$bind['user_id'] = $user->id;
		return DB::select(DB::raw($sql), $bind);
	}

	public static function get_offers(User $user, $params = array()) {

		$limit = (isset($params['limit']) ? $params['limit'] : 10);
		$search = (isset($params['search']) ? $params['search'] : null);
		$status = (isset($params['status']) ? $params['status'] : null);
		$page = (isset($params['page']) ? $params['page'] : 1);
		$sort = (isset($params['sort']) ? $params['sort'] : 'id');
		$order = (isset($params['order']) ? (int) $params['order'] : 1);
		$bind = $where = $or = [];

		$where[] = "o.user_id = :user_id";
		$bind['user_id'] = $user->id;

		if (isset($search['name']) && $search['name']) {
			$or[] = "o.id LIKE :id";
			$or[] = "o.name LIKE :name";
			$or[] = "o.group_name LIKE :group_name";
			$or[] = "o.url LIKE :url";

			$bind['id'] = '%' . $search['name'] . '%';
			$bind['name'] = '%' . $search['name'] . '%';
			$bind['group_name'] = '%' . $search['name'] . '%';
			$bind['url'] = '%' . $search['name'] . '%';
		}

		$sql = "SELECT o.*, if(o.internal = 1, concat(o.name,' <span class=\"label label-success\">Internal</span>'), o.name) name_internal,
					v.name as vertical_name,
					a.name as advertiser_name,
					count(r.id) as rule_count
				FROM offers o
				LEFT JOIN verticals v ON (v.id = o.vertical_id)
				LEFT JOIN advertisers a ON (a.id = o.advertiser_id)
				LEFT JOIN rules r ON (r.rule_type = 'offer' AND r.rule_type_id = o.id) ";

		if (count($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		if (count($or)) {
			$sql .= " AND (" . implode(" OR ", $or) . ")";
		}

		$sql .= " GROUP BY o.id";
		$sql .= " ORDER BY {$sort} " . ($order ? 'DESC' : 'ASC');
		if ($limit > 0) {
			$offset = (($page - 1) * $limit);
			$sql .= " LIMIT {$offset}, {$limit}";
		}

		return DB::select(DB::raw($sql), $bind);
	}

	public static function save_offer(User $user, $data) {
		$offer = new Offer();
		if (isset($data['id']) && (int) $data['id']) {
			$offer = Offer::find($data['id']);
		}

		$offer->fill($data);
		$offer->user_id = $user->id;
		$offer->internal = isset($data['internal']) ? 1 : 0;
		$offer->save();

		return $offer;
	}

	public static function save_bulk(User $user, $request) {
		$check_headers = array('offer_name', 'landing_page', 'advertiser_name', 'vertical_name', 'url', 'revenue');
		if ($request->hasFile('file')) {
			ini_set('auto_detect_line_endings', TRUE);

			$handle = fopen($request->file('file'), 'r');
			$csv_rows = array();
			while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
				$data = MyHelper::array_remove_empty($data);
				if (is_array($data) && count($data) > 1) {
					//clean
					$tmp = array();
					foreach ($data as $d) {
						$tmp[] = trim($d);
					}
					$csv_rows[] = $tmp;
				}
			}

			ini_set('auto_detect_line_endings', FALSE);

			//check if headers are present
			$count = 0;
			$headers = $csv_rows[0];
			foreach ($check_headers as $header) {
				if (in_array(trim($header), $headers)) {
					$count++;
				}
			}

			if ($count != count($check_headers)) {
				return 'bulk_error';
			}

			//add offers
			unset($csv_rows[0]);
			$added_offers = array();
			if (count($csv_rows)) {

				$tmp_offers = array();
				foreach ($csv_rows as $row) {

					$offer_name = trim($row[0]);
					$landing_page = trim($row[1]);
					$advertiser_name = trim($row[2]);
					$vertical_name = trim($row[3]);
					$url = trim($row[4]);
					$revenue = (float) $row[5];
					$country = (isset($row[6]) && trim($row[6]) ? trim($row[6]) : '?');
					$region = (isset($row[7]) && trim($row[7]) ? trim($row[7]) : '?');

					if (!isset($tmp_offers[$offer_name])) {
						$tmp_offers[$offer_name] = array();
					}

					$tmp_offers[$offer_name]['offer_name'] = $offer_name;
					$tmp_offers[$offer_name]['advertiser_name'] = $advertiser_name;
					$tmp_offers[$offer_name]['vertical_name'] = $vertical_name;
					$tmp_offers[$offer_name]['revenue'] = $revenue;
					$tmp_offers[$offer_name]['landingpages'][] = array(
						'name' => $landing_page,
						'url' => $url,
						'country' => $country,
						'region' => $region,
					);
				}

				foreach ($tmp_offers as $tmp_offer) {
					//create offer
					$offer = Offer::findWhere($user, array('name' => $tmp_offer['offer_name']));
					if (is_null($offer)) {
						$offer = new Offer();
					}

					$offer->name = $tmp_offer['offer_name'];
					$offer->revenue = $tmp_offer['revenue'];

					//check if advertiser exists
					$advertiser = Advertiser::findWhere($user, array('name' => $tmp_offer['advertiser_name']));
					if (is_null($advertiser)) {
						$advertiser = new Advertiser();
					}

					if (!$advertiser->is_owner($user)) {
						$advertiser->name = $tmp_offer['advertiser_name'];
						$advertiser->user_id = $user->id;
						$advertiser->active = 1;
						$advertiser->save();
					}

					//check if vertical exists
					$vertical = Vertical::findWhere($user, array('name' => $tmp_offer['vertical_name']));
					if (is_null($vertical)) {
						$vertical = new Vertical();
					}

					if (!$vertical->is_owner($user)) {
						$vertical->name = $tmp_offer['vertical_name'];
						$vertical->user_id = $user->id;
						$vertical->active = 1;
						$vertical->save();
					}

					$offer->group_name = '';
					$offer->advertiser_id = (int) $advertiser->id;
					$offer->vertical_id = (int) $vertical->id;
					$offer->user_id = $user->id;
					$offer->active = 1;

					if ($offer->save()) {
						//add landing pages
						foreach ($tmp_offer['landingpages'] as $landingpage) {

							//create rule
							$params = array(
								'name' => $landingpage['name'],
								'rule_type' => 'offer',
								'rule_type_id' => $offer->id,
								'type' => 'landingpage',
								'url' => $landingpage['url'],
								'country' => $country,
								'region' => $region,
								'city' => '?',
								'agent' => '?',
								'weight' => 100,
							);

							Rule::save_rule($params);
						}

						$added_offers[] = array(
							'name' => $offer->name,
							'advertiser_id' => $advertiser->id,
							'advertiser' => $advertiser->name,
							'vertical_id' => $vertical->id,
							'vertical' => $vertical->name,
							'offer_id' => $offer->id,
							'rules' => count($tmp_offer['landingpages']),
							'revenue' => $revenue,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
						);
					}
				}
			}

			return $added_offers;
		}

		return array();
	}

	public static function delete_offer(User $user, $offer_id) {

		$offer = Offer::find($offer_id);
		if (!$offer->is_owner($user)) {
			return false;
		}

		//delete any rules with this offer
		$sql = "DELETE FROM rules WHERE offer_id = '{$offer->id}'";
		self::query($sql, array());

		return $offer->delete();
	}

	public function get_rules($return_objects = false) {
		return Rule::get_rules('offer', $this->id, $return_objects);
	}

	public function get_rules_by_country($active = 1) {
		return Rule::get_rules_by_country('offer', $this->id, $active);
	}

	public function has_iprule() {
		return Rule::has_iprule('offer', $this->id);
	}
}
