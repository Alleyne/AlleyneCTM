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
        Commands\crearNuevoPeriodo::class,
        Commands\facturar::class,
        //Commands\penalizar::class, 
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //$schedule->command('ctmaster:penalizar')->daily();        
        //$schedule->command('ctmaster:crearNuevoPeriodo', ['dia' => 1])->cron('0 * 1 * * *');
        //$schedule->command('ctmaster:autofacturar')->everyFiveMinutes();

        /*----------------------------------------------------------------------
        Ejemplos:
        //This will run once a month, on the 1th day of the month at minute cero 
        //(i.e. January 1th 00:01am, February 1th 00:01am etc.):
        $schedule->command('ctmaster:facturar', ['dia' => 1])->cron('0 * 1 * * *');
        
        // This will run once a month, on the 16th day of the month at minute cero 
        //(i.e. January 16th 00:01am, February 16th 00:01am etc.):
        $schedule->command('ctmaster:facturar', ['dia' => 16])->cron('0 * 16 * * *');

        Runs once a week on Monday at 13:00...
        ->weekly()->mondays()->at('13:00');
        ->dailyAt('14:28');
        ->monthly();        
        
        // Este cron corre cada hora 
        //$schedule->command('ctmaster:autofacturar')->hourly();        
        
        $schedule->command('ctmaster:monthyJob1')->cron('0 * 1 * * *');
        $schedule->command('ctmaster:monthyJob1')->cron('0 * 16 * * *');         
        $schedule->command('inspire')->hourly();
        ------------------------------------------------------------------------*/
    }
}