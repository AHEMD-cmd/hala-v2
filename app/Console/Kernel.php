<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // شغّل queue worker كل دقيقة
        $schedule->command('queue:work --stop-when-empty --tries=3 --max-jobs=100')
            ->everyMinute()
            ->withoutOverlapping() // مهم جداً! منع تشغيل أكتر من worker في نفس الوقت
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}