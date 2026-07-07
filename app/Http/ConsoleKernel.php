<?php

namespace App\Http;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class ConsoleKernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule($schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/../Console/Commands');

        require base_path('routes/console.php');
    }
}
