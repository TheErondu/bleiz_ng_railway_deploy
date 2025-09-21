<?php
namespace App\Console\Commands;

use App\Jobs\CalculateMonthlyROI as CalculateMonthlyROIJob;
use Illuminate\Console\Command;

class CalculateMonthlyROI extends Command
{
    protected $signature = 'roi:calculate {--force : Force calculation even if already done this month}';
    protected $description = 'Calculate and distribute monthly ROI to investors';

    public function handle()
    {
        $this->info('Starting monthly ROI calculation...');

        // Check if already calculated this month
        if (!$this->option('force')) {
            $lastCalculation = cache('last_roi_calculation');
            if ($lastCalculation && $lastCalculation->isCurrentMonth()) {
                $this->warn('ROI already calculated for this month. Use --force to recalculate.');
                return 0;
            }
        }

        try {
            CalculateMonthlyROIJob::dispatch();
            cache(['last_roi_calculation' => now()], now()->addMonth());

            $this->info('Monthly ROI calculation job dispatched successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to dispatch ROI calculation: ' . $e->getMessage());
            return 1;
        }
    }
}
