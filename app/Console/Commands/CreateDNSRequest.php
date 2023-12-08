<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RedirectEngine;
use App\Source;
use DB;

class CreateDNSRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateDNSRequest {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test dns requests for a domain';

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
                
        foreach($domains as $domain) 
        {
            $this->info("Creating DNS traffic for - {$domain->name} ... ");
            $types = array('AAAA', 'A', 'TXT', 'MX', 'SOA', 'CNAME');
            $cache = array('MISS', 'HIT');
            $data = array();
		    for($x=0;$x<100;$x++) {

		    	$data[] = array(
		    		'visit_date' => date("Y-m-d", strtotime("-2 days")),
                    'visit_time' => date("Y-m-d H:i:s", strtotime("-2 days")),
		    		'domain' => $domain->name,
		    		'type' => $types[rand(0, 5)],
		    		'ipaddress' => '129.11.50.'.rand(0, 10),
		    		'buffer' => '512',
		    		'cache' => $cache[rand(0,1)],
		    		'processing' => 0
		    	);	            
            }

            DB::table('stats_powerdns')->insert($data);    
            $this->info("Done");      
        }

        $this->info("Complete");
    }
}
