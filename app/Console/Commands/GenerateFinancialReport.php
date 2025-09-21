<?php

namespace App\Console\Commands;

use App\Jobs\GenerateFinancialReports;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateFinancialReport extends Command
{
    protected $signature = 'reports:generate
                            {type : Report type (daily|weekly|monthly|quarterly)}
                            {--date= : Specific date for the report (YYYY-MM-DD)}';

    protected $description = 'Generate financial reports for specified period';

    public function handle()
    {
        $reportType = $this->argument('type');
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();

        if (!in_array($reportType, ['daily', 'weekly', 'monthly', 'quarterly'])) {
            $this->error('Invalid report type. Use: daily, weekly, monthly, or quarterly');
            return 1;
        }

        $this->info("Generating {$reportType} financial report for {$date->format('Y-m-d')}...");

        try {
            GenerateFinancialReports::dispatch($reportType, $date);
            $this->info('Financial report generation job dispatched successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to generate report: ' . $e->getMessage());
            return 1;
        }
    }
}
