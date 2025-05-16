<?php

namespace App\Console;

use App\Console\Commands\PingTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        PingTask::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:ping-task')->everyThirtySeconds()->appendOutputTo(base_path('logs/ping.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
