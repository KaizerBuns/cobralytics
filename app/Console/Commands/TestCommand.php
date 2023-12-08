<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RedirectEngine;
use App\Source;
use DB;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestCommand {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing Artisan Call';

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
        //$bar1 = $this->output->createProgressBar(10);
        //$bar2 = $this->output->createProgressBar(10);

        $this->info("Replacement - Start");
        for($x=0;$x<10;$x++) {
            //sleep(1);
            $this->info("Proxy name $x");
            //$bar1->advance();
        }        
        
        for($x=0;$x<10;$x++) {
            //sleep(1);
            $this->info("Domain name $x");
            //$bar2->advance();
        }        
        $this->info("Replacement - End");
        
        //$bar1->finish();
        //$bar2->finish();
    }
}
