<?php

namespace App\Console\Commands;

use App\Models\CapitalPool;
use App\Models\Investor;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCapitalPool extends Command
{
    protected $signature = 'capital:sync {--fix : Fix discrepancies automatically}';
    protected $description = 'Synchronize capital pool with actual data';

    public function handle()
    {
        $this->info('Synchronizing capital pool...');

        DB::transaction(function () {
            $pool = CapitalPool::first();

            if (!$pool) {
                $this->error('Capital pool not found. Creating new one...');
                $pool = CapitalPool::create([
                    'total_amount' => 0,
                    'available_amount' => 0,
                ]);
            }

            // Calculate expected values
            $totalInvestorCapital = Investor::where('status', 'active')->sum('capital');
            $totalLoansOutstanding = Loan::where('status', 'ongoing')->sum('loan_balance');
            $expectedAvailable = $totalInvestorCapital - $totalLoansOutstanding;

            $this->table(
                ['Metric', 'Current', 'Expected', 'Difference'],
                [
                    ['Total Capital', format_currency($pool->total_amount), format_currency($totalInvestorCapital), format_currency($totalInvestorCapital - $pool->total_amount)],
                    ['Available Capital', format_currency($pool->available_amount), format_currency($expectedAvailable), format_currency($expectedAvailable - $pool->available_amount)],
                    ['Deployed Capital', format_currency($pool->total_amount - $pool->available_amount), format_currency($totalLoansOutstanding), format_currency($totalLoansOutstanding - ($pool->total_amount - $pool->available_amount))],
                ]
            );

            // Check for discrepancies
            $totalDiscrepancy = abs($totalInvestorCapital - $pool->total_amount);
            $availableDiscrepancy = abs($expectedAvailable - $pool->available_amount);

            if ($totalDiscrepancy > 0.01 || $availableDiscrepancy > 0.01) {
                $this->warn('Discrepancies found in capital pool!');

                if ($this->option('fix') || $this->confirm('Fix discrepancies automatically?')) {
                    $pool->update([
                        'total_amount' => $totalInvestorCapital,
                        'available_amount' => $expectedAvailable,
                    ]);

                    $this->info('Capital pool synchronized successfully!');
                    activity()->log('Capital pool synchronized - discrepancies fixed');
                } else {
                    $this->warn('Capital pool not synchronized. Use --fix flag to auto-fix.');
                }
            } else {
                $this->info('Capital pool is already synchronized.');
            }
        });

        return 0;
    }
}
