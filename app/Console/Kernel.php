<?php

namespace App\Console;

use App\Console\Commands\ExpireCourseCloneRequests;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        ExpireCourseCloneRequests::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:expire-course-clone-requests')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

