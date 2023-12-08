<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\DWDomains;
use phpWhois\Whois;
use GuzzleHttp\Client;

class bulkDNSWingWhoisAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:bulkDNSWingWhoisAPI';

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
                $client = new Client();
                $response = $client->request('GET', 'http://api.bulkwhoisapi.com/whoisAPI.php', [
                    'query' => ['domain' => $res->domain, 'token' => 'usemeforfree']
                ]);
                
                $result = json_decode($response->getBody());
                if($result->response_code == 'success' && $result->formatted_data->DomainName) {
                    if(isset($result->formatted_data->RegistrarRegistrationExpirationDate)) {
                        $res->expires_on = date("Y-m-d", strtotime($result->formatted_data->RegistrarRegistrationExpirationDate));
                    }

                    if(isset($result->formatted_data->Registrar)) {
                        $res->registrar = $result->formatted_data->Registrar;
                    }

                    if(isset($result->formatted_data->LastupdateofWHOISdatabase)) {
                        $res->updated_on = date("Y-m-d", strtotime($result->formatted_data->LastupdateofWHOISdatabase));
                    }

                    if(isset($result->formatted_data->CreationDate)) {
                        $res->created_on = date("Y-m-d", strtotime($result->formatted_data->CreationDate));
                    }
                    
                    $this->info("Success - {$res->domain}");
                    $res->whois_raw = json_encode($result->formatted_data);
                    $res->whois_processed = 1;
                    $res->whois_last_update = date("Y-m-d");
                    $res->save();
                } else {
                    $res->whois_processed = 88;
                    $res->whois_last_update = date("Y-m-d");
                    $res->save();
                    $this->error("Skipping - {$res->domain}");
                }
                continue;
            } catch (\Exception $e) {
                $this->error("Fatal Error - {$res->domain} - ".$e->getMessage());
                $res->whois_processed = 99;
                $res->whois_last_update = date("Y-m-d");
                $res->save();
            }

            $whois_count++;
        }

        $this->info("completed");
    }
}
