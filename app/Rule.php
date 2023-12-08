<?php namespace App;

use App\Creative;
use DB;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model {

	protected $table = 'rules';
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

	public static function get_rule($rule_id) {

		$rule = Rule::find($rule_id);

		if ($rule->offer_id > 0) {
			if (!$rule->offer = Offer::find($rule->offer_id)) {
				$rule->offer = new Offer();
			}
		}

		if ($rule->campaign_id > 0) {
			if (!$rule->campaign = Campaign::find($rule->campaign_id)) {
				$rule->campaign = new Campaign();
			}
		}

		if ($rule->service_id > 0) {
			if (!$rule->service = Service::find($rule->service_id)) {
				$rule->service = new Service();
			}
		}

		if ($rule->creative_id > 0) {
			if (!$rule->creative = Creative::find($rule->creative_id)) {
				$rule->creative = new Creative();
			}
		}

		//set to 0
		$items = array('country', 'region', 'city', 'agent');
		foreach ($items as $i) {
			$rule->$i = ($rule->$i == '?' ? 0 : $rule->$i);
		}
		//--------
		return $rule;
	}

	public static function get_rules($object_type, $object_id, $return_objects = false, $type = null, $rotator = 0, $rotator_key = null) {
		$option = array();
		$option['type'] = "r.type != 'creative'";
		$option['rotator'] = "r.rotator = '{$rotator}'";

		if ($type == 'all') {
			unset($option['type']);
		} else if ($type) {
			$option['type'] = "r.type = '{$type}'";
		}

		if ($rotator) {
			$option['rotator'] = "r.rotator = '{$rotator}'";
		}

		if ($rotator_key) {
			$option['rotator_key'] = "r.rule_key = '{$rotator_key}'";
		}

		$where = implode(" AND ", $option);
		$sql = "SELECT r.*, o.name offer_name, c.name campaign_name, s.name service_name FROM rules r
		LEFT JOIN offers o ON (o.id = r.offer_id)
		LEFT JOIN campaigns c ON (c.id = r.campaign_id)
		LEFT JOIN services s ON (s.id = r.service_id)
		LEFT JOIN creatives cr ON (cr.id = r.creative_id)
		WHERE rule_type = :type
		AND rule_type_id = :id
		AND {$where}
		ORDER BY active DESC, country ASC, id DESC";

		$bind = array('id' => $object_id, 'type' => $object_type);
		$results = DB::select(DB::raw($sql), $bind);

		if ($return_objects) {
			return Offer::hydrate($results);
		}

		$rules = array();
		$weight_total = array();
		foreach ($results as $r) {
			$rules[$r->id] = $r;
			$rules[$r->id]->options = '';
			switch ($r->type) {
			case 'redirect':
			case 'landingpage':
				$opts = array();
				$options = array(
					'secure' => array('title' => 'HTTPs', 'icon' => 'fa fa-lock'),
					'framed' => array('title' => 'iFrame', 'icon' => 'fa fa-code'),
					'path_forwarding' => array('title' => 'Path FWD', 'icon' => 'fa fa-repeat'),
					'qstring_forwarding' => array('title' => 'QueryString FWD', 'icon' => 'fa fa-exchange'),
					'hide_referrer' => array('title' => 'Hide Referer', 'icon' => 'fa fa-eye-slash'),
					'skip_tracking_url_append' => array('title' => 'Skip Tracking Url Append', 'icon' => 'fa fa-link'),
				);

				$rules[$r->id]->options = array();
				foreach ($options as $name => $info) {

					$opt_display = 'text-muted';
					$opt_text = 'OFF';
					$opt_value = 1;
					$opt_icon = $info['icon'];
					$opt_title = $info['title'];
					if ($r->{$name}) {
						$opt_display = 'text-success';
						$opt_text = 'ON';
						$opt_value = 0;
					}

					$opts[] = "<a id='optlink_{$r->id}_{$name}' href='javascript:void(0)' onclick=\"update_rule({$r->id},'{$name}', {$opt_value})\"><span id='opt_{$r->id}_{$name}' class='cmd-tip {$opt_display}' title='{$opt_title} {$opt_text}'><i class='fa {$opt_icon}'></i></span></a>";
				}

				$rules[$r->id]->options = implode("&nbsp;", $opts);
				$r->url = ($r->secure ? 'https://' : 'http://') . str_replace(array("http://", "https://"), "", $r->url);
				$rules[$r->id]->value = "<a href='{$r->url}'>{$r->url}</a>";
				break;
			case 'offer':
			case 'campaign':
			case 'service':
				if ($r->type == 'campaign') {
					$rules[$r->id]->value = "<a href='/member/campaign/?view=view&id={$r->campaign_id}' target='_blank' class='cmd-tip' title='Click to Edit'>({$r->campaign_id}) {$r->campaign_name}</a>";
				} else if ($r->type == 'service') {
					$rules[$r->id]->value = "<a href='/member/service/?view=view&id={$r->service_id}' target='_blank' class='cmd-tip' title='Click to Edit'>({$r->service_id}) {$r->service_name}</a>";
				} else {
					$rules[$r->id]->value = "<a href='/member/offer/?view=view&id={$r->offer_id}' target='_blank' class='cmd-tip' title='Click to Edit'>({$r->offer_id}) {$r->offer_name}</a>";
				}
				break;
			case 'ip':
				$rules[$r->id]->value = $r->ip_address;
				break;
			case 'html':
				break;
			case 'sale':
				break;
			case 'creative':
				$creative = Creative::find($r->creative_id);
				if (is_null($creative)) {
					$rules[$r->id]->value = "Image Not Found! - Error";
				} else {
					$rules[$r->id]->value = "<a href='" . $creative->getPublicUrl() . "' title='" . $creative->name . "'><img src='" . $creative->getPublicThumbUrl() . "'></a>";
				}
				break;

			}

			$active_label = 'label-warning';
			$active_value = 1;
			if ($r->active) {
				$active_label = 'label-success';
				$active_value = 0;
			}

			$rules[$r->id]->status = "<a id='optlink_{$r->id}_active' href='javascript:void(0)' onclick=\"update_rule({$r->id},'active',{$active_value})\"><span id='opt_{$r->id}_active' class='label {$active_label}'>Enabled</span>";

			$key = $r->country . $r->region . $r->city . $r->agent;
			if (isset($weight_total[$key])) {
				$weight_total[$key] += $r->weight;
			} else {
				$weight_total[$key] = $r->weight;
			}
		}

		//loop again for percentage values
		$final_rules = array();
		foreach ($rules as $rule) {
			$key = $r->country . $r->region . $r->city . $r->agent;
			if (isset($weight_total[$key]) && $weight_total[$key]) {
				$rule->weight_percent = ($rule->weight / $weight_total[$key]) * 100;
			} else {
				$rule->weight_percent = 0;
			}
			$final_rules[] = $rule;
		}

		return $final_rules;
	}

	public static function get_rules_by_country($object_type, $object_id, $active = 1, $type = null, $rotator = 0, $rotator_key = null) {

		$option = array();
		$option['type'] = "r.type != 'creative'";
		$option['rotator'] = "r.rotator = '{$rotator}'";

		if ($type == 'all') {
			unset($option['type']);
		} else if ($type) {
			$option['type'] = "r.type = '{$type}'";
		}

		if ($rotator) {
			$option['rotator'] = "r.rotator = '{$rotator}'";
		}

		if ($rotator_key) {
			$option['rotator_key'] = "r.rule_key = '{$rotator_key}'";
		}

		$where = implode(" AND ", $option);
		$sql = "SELECT * FROM rules r WHERE rule_type = :type AND rule_type_id = :id AND type != 'ip' AND {$where} AND active = :active";
		$results = DB::select(DB::raw($sql), array('type' => $object_type, 'id' => $object_id, 'active' => $active));

		$rules = array();
		foreach ($results as $r) {
			$rules[$r->country][$r->region][$r->city][] = $r;
		}

		return $rules;
	}

	public static function has_iprule($object_type, $object_id) {
		$sql = "SELECT count(id) total FROM rules r WHERE rule_type = :type AND rule_type_id = :id AND type = 'ip'";
		$result = DB::select(DB::raw($sql), array('type' => $object_type, 'id' => $object_id));

		if (isset($result->total) && $result->total > 0) {
			return true;
		}
		return false;
	}

	public static function get_object($type, $type_id) {
		switch ($type) {
		case 'service':
			$object = Service::find($type_id);
			break;
		case 'source':
		case 'domain':
			$object = Source::find($type_id);
			break;
		case 'campaign':
			$object = Campaign::find($type_id);
			break;
		case 'offer':
			$object = Offer::find($type_id);
			break;
		case 'creative':
			$object = Creative::find($type_id);
			break;
		}

		return $object;
	}

	public static function save_rule(Array $data) {
		//Get Source
		$active = 1;
		$object = self::get_object($data['rule_type'], $data['rule_type_id']);

		$rules = array();
		if (isset($data['bulk_update']) && $data['bulk_update']) {
			$bulk_ids = $data['bulk_update_ids'];
			$rules = DB::select("SELECT * FROM rules r WHERE rule_type = '" . $object->get_type() . "' AND rule_type_id = '{$object->id}' AND id IN ({$bulk_ids})");
			$rules = Rule::hydrate($rules);
			unset($data['id']);
			unset($data['rotator']);

			if ($data['country'] != 'nochange') {
				$data['country'] = ((is_numeric($data['country']) && $data['country'] == 0) ? '?' : $data['country']);
				$data['region'] = ((is_numeric($data['region']) && $data['region'] == 0) ? '?' : $data['region']);
				$data['city'] = ((is_numeric($data['city']) && $data['city'] == 0) ? '?' : $data['city']);
				$data['agent'] = ((is_numeric($data['agent']) && $data['agent'] == 0) ? '?' : $data['agent']);
			}

			$tmp = array();
			foreach ($data as $name => $value) {
				if ($value) {
					$tmp[$name] = $value;
				}
			}
			$data = $tmp;

			if (isset($data['option_off']) && $data['option_off']) {
				//options to turn off
				$options = array('hide_referrer', 'secure', 'framed', 'path_forwarding', 'qstring_forwarding', 'hide_referrer', 'skip_tracking_url_append');

				foreach ($options as $option) {
					if (!isset($data[$option])) {
						$data[$option] = 0;
					}
				}
			}
		} else {
			$rule = new Rule();
			if (isset($data['id']) && (int) $data['id']) {
				$rule = Rule::find($data['id']);
			}

			$rules[] = $rule;
			$data['weight'] = (int) $data['weight'];
			$data['country'] = ((is_numeric($data['country']) && $data['country'] == 0) ? '?' : $data['country']);
			$data['region'] = ((is_numeric($data['region']) && $data['region'] == 0) ? '?' : $data['region']);
			$data['city'] = ((is_numeric($data['city']) && $data['city'] == 0) ? '?' : $data['city']);
			$data['agent'] = ((is_numeric($data['agent']) && $data['agent'] == 0) ? '?' : $data['agent']);
		}

		$success = 0;
		foreach ($rules as $rule) {
			$rule->fill($data);
			$rule->rule_type = $object->get_type();
			$rule->rule_type_id = $object->id;
			$rule->active = $active;

			if ($object->has_iprule() && $rule->type != 'ip') {
				$rule->active = 0;
			}

			$rule->secure = (isset($data['secure']) ? 1 : 0);
			$rule->framed = (isset($data['framed']) ? 1 : 0);
			$rule->path_forwarding = (isset($data['path_forwarding']) ? 1 : 0);
			$rule->qstring_forwarding = (isset($data['qstring_forwarding']) ? 1 : 0);
			$rule->hide_referrer = (isset($data['hide_referrer']) ? 1 : 0);
			$rule->skip_tracking_url_append = (isset($data['skip_tracking_url_append']) ? 1 : 0);
			$rule->page_title = $data['page_title'] ?? '';
			$rule->meta_keywords = $data['meta_keywords'] ?? '';
			$rule->meta_desc = $data['meta_desc'] ?? '';
			$rule->ip_address = $data['ip_address'] ?? '';
			$rule->rule_key = $data['rule_key'] ?? '';

			if (isset($data['oc'])) {
				if ($rule->type == 'offer') {
					$rule->offer_id = (int) $data['oc'];
				} else if ($rule->type == 'campaign') {
					$rule->campaign_id = (int) $data['oc'];
				} else if ($rule->type == 'service') {
					$rule->service_id = (int) $data['oc'];
				} else if ($rule->type == 'creative') {
					$rule->creative_id = (int) $data['oc'];
				}
			}

			$rule->user_id = $object->user_id;
			if ($rule->user_id > 0) {
				$rule->url = str_replace(array("http://", "https://"), "", $rule->url);

				unset($rule->oc);
				unset($rule->bulk_update);
				unset($rule->bulk_update_ids);
				unset($rule->option_off);
				if ($result = $rule->save()) {
					if ($rule->type == 'ip') {
						//Deactive other types of service if we are adding an IP service
						DB::query("UPDATE rules SET active = 0 WHERE rule_type = '{$rule->rule_type}' AND rule_type_id = '{$rule->rule_type_id}' AND type != 'ip'");

						$sources = array();
						if ($object->get_type() == 'service') {
							$sources = $object->get_service_sources();
						} else {
							$sources[] = $object;
						}

						foreach ($sources as $source) {
							//save ip address to records
							$source->save_ip_record($rule);
						}
					}
					$success++;
				}
			}
		}

		return $success;
	}

	public static function delete_rule(User $user, $rule_id) {

		if (is_array($rule_id) && count($rule_id) > 1) {

			$rules = self::find($rule_id);
			$success = 0;
			foreach ($rules as $rule) {
				if (self::delete_rule($user, $rule->id)) {
					$success++;
				}
			}

			return $success;
		} else {
			if ($rule_id instanceof Rule) {
				$rule = $rule_id;
			} else {
				$rule = Rule::find($rule_id);
			}

			if (!$rule->is_owner($user)) {
				return 0;
			}

			//delete rule
			$delete_id = $rule->id;
			if ($rule->delete()) {
				//delete any rules from records
				DB::delete('DELETE FROM cobracmd_dns.records WHERE rule_id = :id', array('id' => $delete_id));
				return 1;
			}
		}
	}

	public static function update_weights() {

		return $new_weight;
	}
}
