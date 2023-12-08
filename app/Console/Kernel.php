<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
		'App\Console\Commands\Inspire',
		'App\Console\Commands\SummaryStats',
		'App\Console\Commands\GoogleStats',
		'App\Console\Commands\MonitorCheck',
		//'App\Console\Commands\PiwikMigrate',
		//'App\Console\Commands\PiwikStats',
		'App\Console\Commands\ProjectMigrate',
		'App\Console\Commands\CleanElastic',
		'App\Console\Commands\CreateDomainTraffic',
		'App\Console\Commands\CreateCampaignTraffic',
		'App\Console\Commands\CreateDNSRequest',
		'App\Console\Commands\Optimizer',
		'App\Console\Commands\ReadZoneFile',
		'App\Console\Commands\TestCommand',
		'App\Console\Commands\TestVisitorData',
		'App\Console\Commands\TestVisitorData',
		'App\Console\Commands\bulkDNSWingWhois',
		'App\Console\Commands\bulkDNSWingWhoisAPI',
		'App\Console\Commands\bulkDNSWingTraffic',
	];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
