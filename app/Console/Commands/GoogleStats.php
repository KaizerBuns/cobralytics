<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MyHelper;
use App\Helpers\GAHelper;
use App\Helpers\AppHelper;
use App\Source;
use Carbon\Carbon;
use DB;
use Exception;

class GoogleStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GoogleStats {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use to retrieve GoogleAnyltics stats for a website.';

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
        $current_date = date("Y-m-d");

        if($current_hour < 2) {
            $current_date = date("Y-m-d", strtotime("-1 day"));
        } else {
            if ($this->argument('date')) {
                $current_date = date("Y-m-d", strtotime($this->argument('date')));
            }
        }

        $sources = Source::where(array('google_analytics' => 1))->get();
        foreach($sources as $source) {
            $sql = "DELETE FROM stats_overall WHERE date = '{$current_date}' AND domain_id = '{$source->id}' AND campaign_id = 0";
            DB::statement($sql, array());

            $ga = new GAHelper($source->name);      
            $stats = $ga->getCustomReport($current_date);
        
            $this->info("Done processing ({$source->id}) {$source->name} - {$current_date} ... ".count($stats). " rows");
            foreach($stats as $stat) {
                $stat['source'] = '';
                $stat['source_id'] = 0;
                $stat['domain_id'] = $source->id;
                $stat['user_id'] = $source->user_id;
                $stat['project_id'] = $source->project_id;
                AppHelper::create_log($stat);
            }
        }

        $this->info("Complete\n");
    }
}
