<?php namespace App\Console;
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
        Commands\autofacturar::class,
        Commands\penalizar::class,  
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('ctmaster:autofacturar')->everyMinute()->timezone('America/Panama');
        $schedule->command('ctmaster:autofacturar')->everyFiveMinutes()->timezone('America/Panama');
        $schedule->command('ctmaster:penalizar')->daily()->timezone('America/Panama');
    }
}