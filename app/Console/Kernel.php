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
        Commands\UpdateBookingStatus::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('update:booking-status')->everyFiveMinutes();
        // $schedule->command('astro:update-active-status')->everyMinute()->withoutOverlapping();
        $schedule->command('appointment:notification')->everyFiveMinutes();
        $schedule->command('update:instant-call')->everyMinute()->withoutOverlapping();
        $schedule->command('process:booking')->everyFifteenMinutes();
        $schedule->command('process:wallet')->everyFifteenMinutes();
        $schedule->command('process:order')->everyFifteenMinutes(); 
        // $schedule->command('chmod:permission')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
