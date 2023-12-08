<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DNSRecord extends Model {

	protected $table = 'records';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	protected $connection = 'mysql_dns';
	public $timestamps = false;

	public static function delete_dns($dns_id) {
		if ($dns = DNSRecord::find($dns_id)) {
			if (($dns->type == 'A' || $dns->type == 'CNAME') && $dns->rule_id) {
				Rule::delete_rule($dns->rule_id);
			} else {
				$dns->delete();
			}
		}
	}

	public static function save_dns(Source $source, Array $data) {
		//create a rule for A records

		$add_rule = (isset($data['add_rule']) ? true : false);
		$add_wildcard = (isset($data['add_wildcard']) ? true : false);

		unset($data['add_rule']);
		unset($data['add_wildcard']);

		if ($add_rule && $data['type'] == 'A') {
			$params = array(
				'rule_type' => 'source',
				'rule_type_id' => $source->id,
				'country' => '?',
				'weight' => 100,
				'ip_address' => $data['content'],
				'type' => 'ip',
			);

			Rule::save_rule($params);
		} else {
			$dns = new DNSRecord();
			$dns->fill($data);
			if (empty($dns->name)) {
				$dns->name = $source->name;
			} else {
				$dns->name = "{$dns->name}.{$source->name}";
			}
			$dns->prio = (int) $data['prio'];
			$dns->change_date = date("Y-m-d H:i:s");
			$dns->save();

			if ($add_wildcard) {
				$dns = new DNSRecord();
				$dns->fill($data);
				if (empty($dns->name)) {
					$dns->name = "*.{$source->name}";
				} else {
					$dns->name = "*.{$dns->name}.{$source->name}";
				}
				$dns->prio = (int) $data['prio'];
				$dns->change_date = date("Y-m-d H:i:s");
				$dns->save();
			}
		}
	}

	public static function get_cname($host_name) {
		$where = ['name' => $host_name, 'type' => 'CNAME'];
		$dns = self::where($where)->first();

		if ($dns) {
			return $dns->content;
		} else {
			return $host_name;
		}
	}
}
