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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:restart')->hourly();
        $schedule->call('App\Http\Controllers\CronController@check_expired_document')
            ->timezone('Asia/Jakarta')
            ->dailyAt('01:30');
        $schedule->call('App\Http\Controllers\CronController@check_expired_contract')
            ->timezone('Asia/Jakarta')
            ->dailyAt('01:30');
        $schedule->call('App\Http\Controllers\CronController@generate_allowance')
            ->timezone('Asia/Jakarta')
            ->monthly();
        $schedule->call('App\Http\Controllers\CronController@check_reset_leave')
            ->timezone('Asia/Jakarta')
            ->dailyAt('00:30');
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