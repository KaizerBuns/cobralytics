<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RedirectEngine;
use DB;
use App\Campaign;
use App\CampaignDomain;

class CreateCampaignTraffic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateCampaignTraffic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test traffic for campaigns';

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
        $test_traffic = true;
        $sql = "SELECT s.name, cd.linkhash, campaign_id 
                FROM campaign_domains cd 
                LEFT JOIN sources s on (s.id = cd.source_id)";

        $results = DB::select( DB::raw($sql), array());
        $domains = CampaignDomain::hydrate($results);

        $redirect = new RedirectEngine();
        foreach($domains as $domain) 
        {
            $this->info("Creating traffic for - {$domain->name}/?t={$domain->linkhash} ... ");
            $_SERVER['HTTP_HOST'] = $domain->name;
            $_REQUEST['t'] = $_GET['t'] = $domain->linkhash;

            $countries = array('US' => 'United States', 'CA' => 'Canada', 'MX' => 'Mexico', 'CZ' => 'Czech', 'CU' => 'Cuba', 'GB' => 'Great Britain', 'AG' => 'Angola');
            $user_agents = 'Mozilla';
            $sources = array('','','search','blast', 'bing', 'blob', 'kw_dasda');
            $destination_url = array('google.com', 'yahoo.com', 'ign.com', 'reddit.com', 'tumblr.com', 'tsn.ca', 'thestar.com');
            $referers = array('yahooo.com', 'yaho.com', 'goggle.com', 'gog.com', 'google.ca', 'nubg.com', 'bing.ca', 'bibg.ca', 'pinterest.com', 'facebook.com');

            for($x=0;$x<rand(10, 30);$x++) {

                $rand = rand(0,6);
                $country_name = array_values($countries);
                $country_code = array_keys($countries);

                $_SERVER['REMOTE_ADDR'] = '101.'.rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255);
                $_SERVER['HTTP_USER_AGENT'] = $user_agents;
                $_SERVER['GEOIP_COUNTRY_CODE'] = $country_code[$rand];
                $_SERVER['GEOIP_COUNTRY_NAME'] = $country_name[$rand];
                $_SERVER['GEOIP_REGION'] = '?';
                $_SERVER['GEOIP_REGION_NAME'] = '?';
                $_SERVER['GEOIP_CITY'] = '?';
                $_SERVER['HTTP_REFERER'] = $referers[rand(0,8)];
                $_SERVER['GEOIP_LATITUDE'] = 0;
                $_SERVER['GEOIP_LONGITUDE'] = 0;
                $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_HOST'];
                $_REQUEST['ts'] = $sources[rand(0,4)];
                $_REQUEST['cobraCID'] = $redirect->execute($test_traffic); //test returns clickid
                $_REQUEST['revenue'] = rand(1, 10);
                $_REQUEST['test_campaign_id'] = $domain->campaign_id;

                //fire conversion
                if(rand(1,10) > 3) {
                    if($redirect->conversion($test_traffic)) {
                        $this->info("Fired conversion ...");    
                    }
                }   
            }
        }

        $this->info("Complete");
    }
}