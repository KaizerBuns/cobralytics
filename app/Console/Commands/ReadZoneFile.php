<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ReadZoneFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ReadZoneFile';

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
        $domains = array();
        $count = 0;
        if ($file = fopen("/home/cobra/dumps/net.zone", "r")) {
            while(!feof($file)) {
                $count++;
                $line = fgets($file, 4096);
                
                if($count > 50) {
                    list($domain, $type, $ns) = explode(' ', trim(strtolower($line)));
                    $ns = rtrim($ns, '.');
                    $tmp = explode(".", $ns);

                    //add missing tld from .net ns domains
                    if(count($tmp) == 2)  {
                        $ns .= '.net';
                    } 

                    $tmp = explode(".", $ns);
                    if(count($tmp) == 3) {
                        unset($tmp[0]);
                    } elseif(count($tmp) > 3) {
                        unset($tmp[0]);
                    }

                    $ns_domain = implode(".", $tmp);
                    if(!isset($domains[$domain])) {
                        $domains[$domain] = array(
                               'name' => $domain.'.net',
                               'tld' => '',
                               'ns_root' => $ns_domain,
                               'type' => 'domain'
                        );
                        $domains[$domain]['ns_servers'] = $ns;
                    } else {
                        $domains[$domain]['ns_servers'].= ','.$ns;
                    }

                    //add ns root as domain
                    $domains[$ns_domain] = array(
                        'name' => $ns_domain,
                        'tld' => '',
                        'ns_root' => '', 
                        'type' => 'ns',
                        'ns_servers' => ''
                    );

                    if(count($domains) > 2000) {
                        
                        $insert_values = array();
                        foreach($domains as $d) {
                            $insert_values[] = "('".implode("','", $d)."')";
                        }

                        $sql = "INSERT IGNORE INTO cobracmd.dnswings_zone_domains (`domain`, `tld`, `ns_root`, `type`, `ns_servers`) VALUES ".implode(',', $insert_values);
                        $result = DB::insert(DB::raw($sql));
                        $domains = array();
                        $this->info("Inserted 2000 Domains");
                    }
                }
            }
            fclose($file);
        }

        $this->info("Complete");
    }
}
