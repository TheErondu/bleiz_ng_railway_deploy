<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily tasks
        $schedule->command('loans:process-overdue --notify')
                ->daily()
                ->at('06:00')
                ->description('Process overdue loans and notify customers');

        $schedule->command('db:backup --compress')
                ->daily()
                ->at('02:00')
                ->description('Create daily database backup');

        $schedule->command('system:health')
                ->hourly()
                ->description('Perform system health checks');

        $schedule->command('auth:cleanup-tokens')
                ->weekly()
                ->sundays()
                ->at('03:00')
                ->description('Clean up expired authentication tokens');

        // Monthly tasks
        $schedule->command('roi:calculate')
                ->monthlyOn(1, '09:00')
                ->description('Calculate monthly ROI for investors');

        $schedule->command('reports:generate monthly')
                ->monthlyOn(1, '10:00')
                ->description('Generate monthly financial reports');

        // Weekly tasks
        $schedule->command('capital:sync --fix')
                ->weekly()
                ->mondays()
                ->at('08:00')
                ->description('Synchronize capital pool data');

        $schedule->command('reports:generate weekly')
                ->weekly()
                ->mondays()
                ->at('07:00')
                ->description('Generate weekly reports');

        // System maintenance
        $schedule->command('queue:prune-batches --hours=48')
                ->daily()
                ->description('Prune old job batches');

        $schedule->command('model:prune')
                ->daily()
                ->description('Prune old model records');
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
