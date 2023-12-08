<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\MyHelper;
use App\Helpers\PiwikAPI;
use App\Source;
use App\PiwikUser;
use App\PiwikSite;
use App\User;
use DB;


class PiwikMigrate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:PiwikMigrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate users/domains to Piwik.';

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
		$this->info("Starting migration");

		$sql = "DELETE FROM `access`";
		DB::connection('mysql2')->statement($sql, array());

		//Add users;
		$users = User::all();

		foreach($users as $user) 
		{
			if(PiwikAPI::UsersManager_addUser($user->email, 'letmein99', $user->email, $user->email)) {
				$this->info("Load User: ({$user->id}) $user->email - Created");	
			} else {
				$this->info("Load User: ({$user->id}) $user->email - Already Exists");
			} 
			
			$pUser = PiwikUser::where(array('login' => $user->email))->first();

			//Save to Cobralytics
			$user->piwik_login = $pUser->login;
			$user->piwik_auth_token = $pUser->token_auth;
			$user->save();
			
			$sources = Source::where(array('user_id' => $user->id, 'type' => 'domain'))->get();
			foreach($sources as $s) {
				$this->info("Load Site: ({$s->id}) {$s->name}");
				$pSite = PiwikSite::where(array('name' => $s->name))->first();
				
				if(is_null($pSite)) {
					$pSite = new PiwikSite();
					$pSite->name = $s->name;
					$pSite->main_url = 'http://'.strtolower($s->name);
					$pSite->ts_created = date("Y-m-d H:i:s");
					$pSite->timezone = 'America/New_York';
					$pSite->currency = 'USD';
					$pSite->sitesearch = 1;
					$pSite->type = 'website';
					$pSite->sitesearch_keyword_parameters = '';
					$pSite->sitesearch_category_parameters = '';
					$pSite->excluded_ips = '';
					$pSite->excluded_parameters = '';
					$pSite->excluded_user_agents = '';
					$pSite->group = '';
					$pSite->type = '';
					if($pSite->save()) {
						$this->info("Created");
					} else {
						$this->info("Failed");
					}
				} else {
					$this->info("Updating");
					$pSite->name = $s->name;
					$pSite->main_url = 'http://'.strtolower($s->name);
					$pSite->save();
				}

				if($pSite) {
					$s->name = strtolower($s->name);
					$s->piwik_idsite = (int)$pSite->idsite;
					$s->save();

					$sql = "INSERT INTO access (idsite, login, access) VALUES ('{$pSite->idsite}','{$user->piwik_login}', 'view')";
					DB::connection('mysql2')->insert($sql, array());
				}
			}
		}
		$this->info("Done");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['', InputArgument::OPTIONAL, ''],
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
