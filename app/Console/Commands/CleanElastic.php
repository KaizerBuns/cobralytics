<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CleanElastic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans ElasticSearch Indexes';

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
        $this->info("Starting cleaning processs...\n");

        $date = date("Y.m.d", strtotime("-30 days"));
        $curl_urls = array(
            "curl -XDELETE 'localhost:9200/.marvel-{$date}'",
            "curl -XDELETE 'localhost:9200/logstash-{$date}'"
        );

        foreach($curl_urls as $url) {
            echo "Processing - {$url}\n";
            exec($url);    
        }
        
        $this->info("Complete\n");
    }
}
