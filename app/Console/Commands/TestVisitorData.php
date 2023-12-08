<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Source;
use App\Helpers\MyHelper;


class TestVisitorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestVisitorData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test visitor data';

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
        $visitors = 0;
        $clicks = 0;
        $actions = 0;
        $revenue = 0;

        $domains = Source::where(array('type' => 'domain'))->get();
        foreach($domains as $domain) 
        {
            $this->info("Creating traffic for - {$domain->name} ... ");
            $_SERVER['HTTP_HOST'] = $domain->name;

            $countries = array('US' => 'United States', 'CA' => 'Canada', 'MX' => 'Mexico', 'CZ' => 'Czech', 'CU' => 'Cuba', 'GB' => 'Great Britain', 'AG' => 'Angola');
            $user_agents = array('Mozilla','Chrome', 'Safari');
            $user_platforms = array('MacOSX','WindowsXP', 'Windows Vista', 'Windows 10', 'Linux', 'Android', "iOS");
            $sources = array('','','search','blast', 'bing', 'blob', 'kw_dasda');
            $destination_url = array('google.com', 'yahoo.com', 'ign.com', 'reddit.com', 'tumblr.com', 'tsn.ca', 'thestar.com');
            $referers = array('yahooo.com', 'yaho.com', 'goggle.com', 'gog.com', 'google.ca', 'nubg.com', 'bing.ca', 'bibg.ca', 'pinterest.com', 'facebook.com');

            for($x=0;$x < rand(10, 1000); $x++) {

                $rand = rand(0,6);
                $country_name = array_values($countries);
                $country_code = array_keys($countries);
                
                $_SERVER['REMOTE_ADDR'] = '101.101.101'.rand(200, 255);
                $_SERVER['HTTP_USER_AGENT'] = $user_agents[rand(0,2)];
                $_SERVER['GEOIP_COUNTRY_CODE'] = $country_code[$rand];
                $_SERVER['GEOIP_COUNTRY_NAME'] = $country_name[$rand];
                $_SERVER['GEOIP_REGION'] = '?';
                $_SERVER['GEOIP_REGION_NAME'] = '?';
                $_SERVER['GEOIP_CITY'] = '?';
                $_SERVER['HTTP_REFERER'] = 'http://'.$referers[rand(0,8)].'?blah=1&blah1=1';
                $_SERVER['GEOIP_LATITUDE'] = rand(0,200);
                $_SERVER['GEOIP_LONGITUDE'] = rand(100,400);
                $_SERVER['REQUEST_URI'] = '';
             
                $user_agent = iconv(mb_detect_encoding($_SERVER['HTTP_USER_AGENT'], mb_detect_order(), true), "UTF-8", $_SERVER['HTTP_USER_AGENT']);
                $lat = (isset($_SERVER['GEOIP_LATITUDE']) && $_SERVER['GEOIP_LATITUDE'] ? $_SERVER['GEOIP_LATITUDE'] : 0);
                $long = (isset($_SERVER['GEOIP_LONGITUDE']) && $_SERVER['GEOIP_LONGITUDE'] ? $_SERVER['GEOIP_LONGITUDE'] : 0);
                $geo_location = "{$lat},{$long}";
                $hour = (int)date("G", strtotime("now"));
                $minute = intval(date("i", strtotime("now")));
                $today = date("Y-m-d");
                $account_id = rand(1,1000);
                $network_redirect_id = rand(1,1000);
                $partition_id = rand(1, 4);
                $source_id = rand(1, 100000000);
                $country = $_SERVER['GEOIP_COUNTRY_CODE'];
                $browser = $user_agents[rand(0,2)];
                $platform = $user_platforms[rand(0,6)];
                $mobile = rand(0,1);
                $clone_id = rand(0, 10000000);
                $group_id = rand(1, 100);
                $campaign_id = rand(1, 1000);
                $impression_id = MyHelper::generate_numAlpha(16);
                $ip = $_SERVER['REMOTE_ADDR'];
                $region = $_SERVER['GEOIP_REGION'];
                $city = $_SERVER['GEOIP_CITY'];
                $redirect_sub = MyHelper::generate_numAlpha(5);
                $rand_clicks = rand(0, 100);

                $log = array(
                    'log_type' => 'visit',
                    'log_datetime' => date("c"),
                    'log_timestamp' => (int) (microtime(true) * 1000),
                    'impression_id' => $impression_id,
                    'visitor_id' => md5($ip.$user_agent),
                    'ip' => $ip,
                    'country' => $country,
                    'region' => $region,
                    'city' => $city,
                    'user_agent' => $user_agent,
                    'carrier' => 'unknown',
                    'isp' => '',
                    'browser' => $browser,
                    'browser_version' => rand(0,10),
                    'platform' => $platform,
                    'mobile' => (int)$mobile,
                    'geo_location' => $geo_location,
                    'device' => '',
                    'lat' => $lat,
                    'long' => $long,
                    'resolution' => '',
                    'gender' => '',
                    'email' => '',
                    'phone' => '',
                    'connection' => '',
                    'language' => 'en',
                    'query_string' => (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : ''),
                    'vertical_id' => (isset($settings['vertical']) && $settings['vertical'] ? $settings['vertical'] : ''),
                    'source_id' => (int)$source_id, //sov/site_id
                    'parent_source_id' => (int)$clone_id,
                    'source_name' => '',
                    'publisher_id' => (int)$account_id,
                    'publisher_login' => '',
                    'group_id' => (int)$group_id,
                    'group_name' => '',
                    'campaign_id' => (int)$campaign_id,
                    'campaign_name' => '',
                    'redirect_id' => (int)$network_redirect_id,
                    'redirect_subdomain' => $redirect_sub,
                    'redirect_domain' => $_SERVER['HTTP_HOST'],
                    'redirect_ip' => $ip,
                    'mini_name' => '',
                    'mini_url' => '',
                    'mini_sub' => '', //might not be needed
                    'rotation_id' => 0,
                    'jumplink_id' => 0,
                    'jumplink_name' => '',
                    'jumplink_url' => '',
                    'template_id' => 0,
                    'template_name' => '',
                    'advertiser_id' => 0,
                    'advertiser_name' => '',
                    'advertiser_account_id' => 0,
                    'advertiser_account_name' => '',
                    'advertiser_offer_id' => '',
                    's1' => (isset($_REQUEST['s1']) ? $_REQUEST['s1'] : ''),
                    's2' => (isset($_REQUEST['s2']) ? $_REQUEST['s2'] : ''),
                    's3' => (isset($_REQUEST['s3']) ? $_REQUEST['s3'] : ''),
                    's4' => (isset($_REQUEST['s4']) ? $_REQUEST['s4'] : ''),
                    's5' => (isset($_REQUEST['s5']) ? $_REQUEST['s5'] : ''),
                    'kw' => (isset($_REQUEST['kw']) ? $_REQUEST['kw'] : ''),
                    'referer' => (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : ''),
                    'referer_domain' => (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : ''),
                    'referer_query' => (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) : ''),
                    'rule_id' => rand(1,100),
                    'impressions' => 1
                );

                MyHelper::print_r($log);
                die;

                $visitors++;        
                error_log(json_encode($log)."\n", 3, env('APP_LOGS')."/ytz-visitors.log");

                if($rand_clicks < 50) {
                    $rand_action = rand(0, 100);

                    $log = array(
                        'log_type' => 'click',
                        'log_datetime' => date("c"),
                        'log_timestamp' => (int) (microtime(true) * 1000),
                        'country' => $country,
                        'impression_id' => $impression_id,
                        'clicks' => 1
                    );

                    $clicks++;
                    error_log(json_encode($log)."\n", 3, env('APP_LOGS')."/ytz-visitors.log");

                    if($rand_action < 50) {

                        $rev = (rand(0,10000)/1000);
                        $log = array(
                            'log_type' => 'action',
                            'log_datetime' => date("c"),
                            'log_timestamp' => (int) (microtime(true) * 1000),
                            'country' => $country,
                            'impression_id' => $impression_id,
                            'actions' => 1,
                            'revenue' => $rev
                        );

                        $actions++;
                        $revenue+=$rev;
                        error_log(json_encode($log)."\n", 3, env('APP_LOGS')."/ytz-visitors.log");
                    }
                }
            }

            sleep(2);
        }
        
        $this->info("Total Visitors: {$visitors} Clicks: {$clicks} Actions: {$actions} Revenue: {$revenue}");
        $this->info("Complete");
    }
}