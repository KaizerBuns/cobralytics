<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\DWDomains;
use phpWhois\Whois;

class bulkDNSWingWhois extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:bulkDNSWingWhois';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $whois_count=0;
        $whois = new Whois();
        foreach (DWDomains::where(['whois_processed' => 0, 'type' => 'ns'])->cursor() as $res) 
        {

            if($whois_count > 1000) {
                break;
            }

            try {
                $result = $whois->lookup($res->domain, false);

                if(!isset($result['rawdata'])) {
                    continue;
                }

                $raw = $this->clean_raw($result['rawdata']);


                print_r($result['rawdata']);

                continue;

                if(count($raw) > 5) {

                   
                } else {
                    $res->whois_raw = json_encode($this->utf8ize($result['rawdata']));
                    $res->whois_processed = 88;
                    $res->whois_last_update = date("Y-m-d");
                    $res->save();
                    $this->error("Skipping - {$res->domain}");
                }
            } catch (\Exception $e) {
                $this->error("Fatal Error - {$res->domain} - ".$e->getMessage());
                $res->whois_raw = json_encode($this->utf8ize($result['rawdata']));
                $res->whois_processed = 99;
                $res->whois_last_update = date("Y-m-d");
                $res->save();
            }

            $whois_count++;
        }

        $this->info("completed");
    }

    public function clean_raw($raw) {
        $new = array();
        foreach($raw as $r) {
            $tmp = explode(": ", strtolower(trim($r)));
            if(count($tmp) > 1) {
                $new[str_replace(" ","_", $tmp[0])] = $tmp[1];
            }
        }
        return $new;
    }

    public function utf8ize($d)
    { 
        if (is_array($d) || is_object($d))
            foreach ($d as &$v) $v = $this->utf8ize($v);
        else
            return utf8_encode($d);

        return $d;
    }
}
