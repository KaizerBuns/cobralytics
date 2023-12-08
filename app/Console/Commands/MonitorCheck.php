<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MonitorList;
use Mail;

class MonitorCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MonitorCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of Monitors to check against AVG';

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
        $send_email = array();
        $monitor_list = MonitorList::all();
        foreach($monitor_list as $list) {
            $status = shell_exec ("python /var/www/html/pyavast/avast_url_check.py {$list->url}");
            switch(trim($status)) {
                case 'Good':
                    $list->status = 'ok';
                    break;
                case 'Blocked':
                    $list->status = 'flagged';
                    $send_email[$list->email][] = $list->url;
                    break;
                default:
                    $list->status = 'unknown';
                    break;
            }
            $this->info("Checking {$list->url} -> {$list->status}");
            $list->save();
        }

        if(count($send_email) > 0) {
            foreach($send_email as $email => $list) {
                $this->info("Sending alert email to {$email}");
                
                 Mail::send('emails.monitor', ['list' => $list, 'email' => $email], function ($m) use ($email) {
                    $m->from('admin@cobralytics.com');
                    $m->to($email)->subject('Cobralytics Monitor Alert');
                });    
            } 
        }

        $this->info("Completed");
    }
}
