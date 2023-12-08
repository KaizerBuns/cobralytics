<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RedirectEngine;
use App\Source;

class CreateDomainTraffic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateDomainTraffic {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test traffic for domains';

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
        if($this->argument('domain')) {
            $domains = Source::where(array('name' => $this->argument('domain'), 'type' => 'domain'))->get();
        } else {
            $domains = Source::where(array('type' => 'domain'))->get();
        }
        
        $redirect = new RedirectEngine();
        foreach($domains as $domain) 
        {
            $this->info("Creating traffic for - {$domain->name} ... ");
            $_SERVER['HTTP_HOST'] = $domain->name;

            $countries = array('US' => 'United States', 'CA' => 'Canada', 'MX' => 'Mexico', 'CZ' => 'Czech', 'CU' => 'Cuba', 'GB' => 'Great Britain', 'AG' => 'Angola');
            $user_agents = 'Mozilla';
            $sources = array('','','search','blast', 'bing', 'blob', 'kw_dasda');
            $destination_url = array('google.com', 'yahoo.com', 'ign.com', 'reddit.com', 'tumblr.com', 'tsn.ca', 'thestar.com');
            $referers = array('yahooo.com', 'yaho.com', 'goggle.com', 'gog.com', 'google.ca', 'nubg.com', 'bing.ca', 'bibg.ca', 'pinterest.com', 'facebook.com');

            for($x=0;$x<rand(10, 30);$x++) {

                $rand = rand(0,6);
                $country_name = array_values($countries);
                $country_code = array_keys($countries);
                
                $_SERVER['REMOTE_ADDR'] = '101.101.101'.rand(200, 255);
                $_SERVER['HTTP_USER_AGENT'] = $user_agents;
                $_SERVER['GEOIP_COUNTRY_CODE'] = $country_code[$rand];
                $_SERVER['GEOIP_COUNTRY_NAME'] = $country_name[$rand];
                $_SERVER['GEOIP_REGION'] = '?';
                $_SERVER['GEOIP_REGION_NAME'] = '?';
                $_SERVER['GEOIP_CITY'] = '?';
                $_SERVER['HTTP_REFERER'] = $referers[rand(0,8)];
                $_SERVER['GEOIP_LATITUDE'] = 0;
                $_SERVER['GEOIP_LONGITUDE'] = 0;
                $_SERVER['REQUEST_URI'] = '';
                $_REQUEST['ts'] = $sources[rand(0,4)];
                $redirect->execute(true);
            }
        }
        
        $this->info("Complete");
    }
}