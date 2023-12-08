<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\MyHelper;
use App\Helpers\PiwikAPI;
use App\Source;
use App\User;
use DB;

class PiwikStats extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:PiwikStats';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve daily stats from Piwik.';

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
	public function fire()
	{
	
		$current_date = date("Y-m-d");
		if ($this->argument('date')) {
			$current_date = date("Y-m-d", strtotime($this->argument('date')));
		}	

		$cron_start = MyHelper::get_time();
		$table = "stats_piwik_overall";
		$sql = "UPDATE stats_piwik_overall SET processing = 2 WHERE processing = 0 AND date = '{$current_date}'";
		DB::statement($sql, array());

		$users = User::all();
		foreach($users as $user) {
			if($user->is_piwik()) 
			{
				$this->info("Retrieving sources for {$user->email}");
				$all_sources = Source::where(array('user_id' => $user->id, 'type' => 'domain'))->get();

				if(count($all_sources) == 0) {
					continue;
				}
				
				$all_sources = MyHelper::rekey_array($all_sources, 'piwik_idsite');
				$total_sources = count($all_sources);
				
				$this->info("Total sources for user ($user->id) {$user->email}: {$total_sources}");
				if($total_sources > 10) {
					$chuck_size = round(count($all_sources) / 10);
					$all_sources = array_chunk($all_sources, $chuck_size, true);
					foreach($all_sources as $sources) {
						$this->collect_stats($user, $current_date, $sources);
					}
				} else {
					$this->collect_stats($user, $current_date, $all_sources);
				}
			}
		}

		$sql = "DELETE FROM stats_piwik_overall WHERE processing = 2 AND date = '{$current_date}'";
		DB::statement($sql, array());

		$sql = "UPDATE stats_piwik_overall SET processing = 0 WHERE processing = 1 AND date = '{$current_date}'";
		DB::statement($sql, array());

		$cron_end = MyHelper::get_time();
		$seconds = (($cron_end - $cron_start) / 60) * 100;
		$minutes = ($seconds/60);
		echo "[ CRON COMPLETE ] ".round($minutes, 2)." minutes ( ".round($seconds, 2)." seconds )\n";
	}

	public function collect_stats(User $user, $date, Array $sources) {
		$sql_start = MyHelper::get_time();
		$idsites = explode(",", MyHelper::implode_on_field(",", $sources, "piwik_idsite"));
		$piwik_stats = PiwikAPI::Metrics_get($user->piwik_auth_token, $date, $idsites);

		$values = array();
		foreach($sources as $source) {
			if($piwik_id = $source->piwik_idsite) {
				if(isset($piwik_stats[$piwik_id])) {
					$data = $piwik_stats[$piwik_id];
				} else {
					$data = $piwik_stats;
				}

				if(count($data) == 0) {
					$data = array();
				}

				$data['date'] = $date;
				$data['project_id'] = $source->project_id;
				$data['user_id'] = $source->user_id;
				$data['source_id'] = $source->id;
				$data['source'] = $source->name;
				$data['piwik_idsite'] = $source->piwik_idsite;
				$data['processing'] = 1;

				$sql = "INSERT INTO stats_piwik_overall (`".implode("`,`", array_keys($data))."`) VALUES ('".implode("','", array_values($data))."')";
				DB::insert($sql, array());
			}	
		}		

		$sql_end = MyHelper::get_time();
		$seconds = (($sql_end - $sql_start) / 60) * 100;
		$minutes = ($seconds/60);
		echo "[ SQL COMPLETE ] ".round($minutes, 2)." minutes ( ".round($seconds, 2)." seconds )\n\n";
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['date', InputArgument::OPTIONAL, 'A valid date'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
