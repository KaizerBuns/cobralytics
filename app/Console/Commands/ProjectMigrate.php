<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Helpers\MyHelper;
use App\Campaign;
use App\CampaignPixel;
use App\CampaignDomain;
use App\Project;
use App\Source;
use App\Rule;
use App\Offer;
use App\User;
use App\Pixel;
use App\Vertical;
use App\Advertiser;

class ProjectMigrate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'command:ProjectMigrate {user_id} {project_id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrates ProjectIDs to a UserID.';

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
		/*
		user_id = 10 Helix Project_id = 6
		user_id = 11 Parkbench Project_id = 2
		user_id = 12 Oakville Academy Project_id = 20
		*/

		$user_id = $this->argument('user_id');
		$project_id = $this->argument('project_id');
		
		$this->info("Starting Project Migration - ProjectID {$project_id} to UserID {$user_id} New Project\n");

		//verify if the migrate project exists
		$new_project = Project::where(array('name' => "MigrateProject-{$project_id}"))->first();
		if(is_null($new_project)) {
			$new_project = new Project();
			$new_project->user_id = $user_id;
			$new_project->name =  "MigrateProject-{$project_id}";
			$new_project->save();
		}

		$campaigns = Campaign::where(array('project_id' => $project_id))->get();

		//Check sources belong to the project
		$update_sources = array();
		foreach($campaigns as $campaign) {

			$this->info("Migrating Campaign ($campaign->id) to User");

			$source = Source::find($campaign->source_id);
			if(!$new_source = Source::where(array('name' => $source->name, 'user_id' => $user_id))->first()) {
				$this->info("--> Creating Source from Campaign");
				$new_source = clone $source;
				unset($new_source->id);
				$new_source->exists = 0;
				$new_source->project_id = $new_project->id;
				$new_source->user_id = $user_id;
				$new_source->save();
			}

			//new migrated source;
			$source = $new_source;

			//Load Rules
			$campaign_rules = Rule::where(array('rule_type' => 'campaign', 'rule_type_id' => $campaign->id))->get();

			if(count($campaign_rules)) {
				foreach($campaign_rules as $cr) {

					if($cr->offer_id) {
						if($offer = Offer::find($cr->offer_id)) {
							//create new vertical
							$vertical = Vertical::find($offer->vertical_id);
							if(!$new_vertical = Vertical::where(array('name' => $vertical->name, 'user_id' => $user_id))->first()) {
								$this->info("---> Creating new Vertical from Offer");
								$new_vertical = clone $vertical;
								unset($new_vertical->id);
								$new_vertical->exists = 0;
								$new_vertical->user_id = $user_id;
								$new_vertical->save();
							}

							$vertical = $new_vertical;
							
							//create new advertiser
							$advertiser = Advertiser::find($offer->advertiser_id);
							if(!$new_advertiser = Advertiser::where(array('name' => $advertiser->name, 'user_id' => $user_id))->first()) {
								$this->info("---> Creating new Advertiser from Offer");
								$new_advertiser = clone $advertiser;
								unset($new_advertiser->id);
								$new_advertiser->exists = 0;
								$new_advertiser->user_id = $user_id;
								$new_advertiser->save();
							}

							$advertiser = $new_advertiser;

							$offer->vertical_id = $vertical->id;
							$offer->advertiser_id = $advertiser->id;
							$offer->user_id = $user_id;
							$offer->save();

							$offer_rules = Rule::where(array('rule_type' => 'offer', 'rule_type_id' => $offer->id))->get();
							foreach($offer_rules as $or) {
								$or->user_id = $user_id;
								$or->save();
							}
						}
					}

					$cr->user_id = $user_id;
					$cr->save();
				}

				//load pixels
				$this->info("---> Migrating Pixels");
				$campaign_pixels = CampaignPixel::where(array('campaign_id' => $campaign->id))->get();
				foreach($campaign_pixels as $cp) {
					$this->info("---> CampaignPixel ($cp->id) to User");
					if($pixel = Pixel::find($cp->id)) {
						$pixel->user_id = $user_id;
						$pixel->save();
					}
				}

				//load tracking domain
				$this->info("---> Migrating Tracking Domain");
				$campaign_domains = CampaignDomain::where(array('campaign_id' => $campaign->id))->get();
				foreach($campaign_domains as $cd) {
					$this->info("---> CampaignDomain ($cd->id) to User");
					if($domain = Source::find($cd->id)) {
						$domain->user_id = $user_id;
						$domain->project_id = $new_project->id;
						$domain->save();
					}
				}
			
				$update_sources[] = $source;

				$campaign->user_id = $user_id;
				$campaign->project_id = $new_project->id;
				$campaign->save();
			}
		}

		if(count($update_sources)) {
			foreach($update_sources as $source) {
				$source->project_id = $new_project->id;
				$source->user_id = $user_id;
				$source->save();
			}
		}

		//finish migration any domains
		$this->info("Migrating domains");
		$domains = Source::where(array('project_id' => $project_id, 'type' => 'domain'))->get();
		foreach($domains as $d) {
			$d->user_id = $user_id;
			$d->project_id = $new_project->id;
			$d->save();
		}

		$this->info("Migration complete");
	}
}
