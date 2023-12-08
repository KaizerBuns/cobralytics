<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\MyHelper;
use DB;

class SummaryStats extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'command:SummaryStats {date?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Summarize stats_overall into the summary tables';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$current_hour = date('G');
		$start_date = $end_date = date("Y-m-d");

		if($current_hour < 2) {
			$start_date = date("Y-m-d", strtotime("-1 day"));
		} else {
			if ($this->argument('date')) {
				$start_date = date("Y-m-d", strtotime($this->argument('date')));
			}
		}
		
		$cron_start = MyHelper::get_time();
		$tables = array(
			'stats_summary_manage',
			'stats_summary_country',
			'stats_summary_traffic',
			'stats_summary_hourly',
			'stats_summary_dns',
		);

		$current_date = $start_date;
		while ($current_date <= $end_date) 
		{
			$this->info("Processing {$current_date}");
			foreach($tables as $table) 
			{
				$sql_start = MyHelper::get_time();

				if($table == 'stats_summary_dns') {
					$calc_columns = "count(sp.id) as total_requests, sum(if(cache='MISS',0,1)) as cache_requests, sum(buffer) as buffer_size, 1 as processing";
				} else {
					$calc_columns = "0 as hits, count(distinct unique_key) as uniques, sum(visitors) visitors, sum(clicks) clicks, sum(conversions) conversion, sum(cost) cost, sum(revenue) revenue, 1 as processing";
				}
				
				$sql = 'DESC ' . $table . ';';
				$results = DB::select($sql, array());

				$columns = array();
				foreach($results as $r) {
					if($r['Field'] != 'id') {
						$columns[] = $r['Field'];
					}
				}
	 
				switch($table) 
				{
					case 'stats_summary_manage':
						$table_fields = "sv.visit_date as date, user_id, project_id, campaign_id, source_id, domain_id, offer_id, rule_id, service_id, {$calc_columns}";
						$sql = "
						INSERT INTO {$table} (".  implode(",", $columns).") 
						SELECT {$table_fields} 
						FROM stats_visitors sv LEFT JOIN stats_actions sa ON (sv.clickid = sa.clickid) 
						WHERE sv.visit_date = '{$current_date}' 
						GROUP BY user_id, project_id, campaign_id, source_id, domain_id, offer_id, rule_id, service_id
						";
						break;
					case 'stats_summary_country':		 
						$table_fields = "sv.visit_date as date, user_id, project_id, country, region, city, ip, {$calc_columns}";
						$sql = "
						INSERT INTO {$table} (".  implode(",", $columns).") 
						SELECT {$table_fields} 
						FROM stats_visitors sv LEFT JOIN stats_actions sa ON (sv.clickid = sa.clickid) 
						WHERE sv.visit_date = '{$current_date}' 
						GROUP BY user_id, project_id, country, region, city, ip 
						";
						break;
					case 'stats_summary_traffic':
						$table_fields = "sv.visit_date as date, user_id, project_id, traffic_type, platform, browser, version, screen, mobile, referer, page, destination, sub1, {$calc_columns}";
						$sql = "
						INSERT INTO {$table} (".  implode(",", $columns).") 
						SELECT {$table_fields} 
						FROM stats_visitors sv LEFT JOIN stats_actions sa ON (sv.clickid = sa.clickid) 
						WHERE sv.visit_date = '{$current_date}' 
						GROUP BY user_id, project_id, traffic_type, platform, browser, version, screen, mobile, referer, page, destination, sub1 
						";
						break;
					case 'stats_summary_hourly':
						$table_fields = "sv.visit_date as date, sv.visit_time as date_time, user_id, project_id, campaign_id, source_id, domain_id, offer_id, rule_id, service_id, country, {$calc_columns}";
						$sql = "INSERT INTO {$table} (".  implode(",", $columns).") 
						SELECT $table_fields 
						FROM stats_visitors sv LEFT JOIN stats_actions sa ON (sv.clickid = sa.clickid) 
						WHERE sv.visit_date = '{$current_date}' 
						GROUP BY date_time, user_id, project_id, campaign_id, source_id, domain_id, offer_id, rule_id, service_id, country";
						break;
					case 'stats_summary_dns':
						$table_fields = "sp.visit_date as date, r.domain_id as domain_id, sp.type, {$calc_columns}";
						$sql = "INSERT INTO {$table} (".  implode(",", $columns).") 
						SELECT $table_fields FROM stats_powerdns sp 
						LEFT JOIN cobracmd_dns.records r ON (r.name = sp.domain)
						WHERE visit_date = '{$current_date}' and r.domain_id > 0
						GROUP BY domain, type";
						break;
				}

				$this->info("Summarize {$table} - {$current_date}");
				DB::insert($sql, array());

				$sql = "UPDATE {$table} SET processing = 2 WHERE processing = 0 AND date = '{$current_date}'";
				DB::statement($sql, array());

				$sql = "UPDATE {$table} SET processing = 0 WHERE processing = 1 AND date = '{$current_date}'";
				DB::statement($sql, array());

				$sql = "DELETE FROM {$table} WHERE processing = 2 AND date = '{$current_date}'";
				DB::statement($sql, array());
			}
			
			$sql_end = MyHelper::get_time();
			$seconds = (($sql_end - $sql_start) / 60) * 100;
			$minutes = ($seconds/60);
			$this->info("[ SQL COMPLETE ] ".round($minutes, 2)." minutes ( ".round($seconds, 2)." seconds )");
			$current_date = date('Y-m-d', strtotime($current_date." +1 day"));
		}

		$cron_end = MyHelper::get_time();
		$seconds = (($cron_end - $cron_start) / 60) * 100;
		$minutes = ($seconds/60);
		$this->info("[ CRON COMPLETE ] ".round($minutes, 2)." minutes ( ".round($seconds, 2)." seconds )");
	}
}
