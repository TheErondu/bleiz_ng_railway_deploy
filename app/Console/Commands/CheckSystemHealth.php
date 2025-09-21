<?php

namespace App\Console\Commands;

use App\Models\CapitalPool;
use App\Models\Loan;
use App\Models\Investor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckSystemHealth extends Command
{
    protected $signature = 'system:health {--fix : Attempt to fix issues automatically}';
    protected $description = 'Perform system health checks';

    public function handle()
    {
        $this->info('Running system health checks...');

        $issues = [];

        // Check database connectivity
        $issues = array_merge($issues, $this->checkDatabaseHealth());

        // Check capital pool integrity
        $issues = array_merge($issues, $this->checkCapitalPoolIntegrity());

        // Check for data inconsistencies
        $issues = array_merge($issues, $this->checkDataConsistency());

        // Check disk space
        $issues = array_merge($issues, $this->checkDiskSpace());

        // Check for stale jobs
        $issues = array_merge($issues, $this->checkJobQueue());

        if (empty($issues)) {
            $this->info('✅ All health checks passed!');
            return 0;
        } else {
            $this->error('❌ Health check issues found:');
            foreach ($issues as $issue) {
                $this->line("  • {$issue['message']} " . ($issue['severity'] === 'critical' ? '[CRITICAL]' : '[WARNING]'));
            }

            if ($this->option('fix')) {
                $this->info('Attempting to fix issues...');
                $this->fixIssues($issues);
            }

            return count(array_filter($issues, fn($issue) => $issue['severity'] === 'critical')) > 0 ? 1 : 0;
        }
    }

    private function checkDatabaseHealth(): array
    {
        $issues = [];

        try {
            DB::connection()->getPdo();
            $this->line('✅ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'database',
                'severity' => 'critical',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }

        return $issues;
    }

    private function checkCapitalPoolIntegrity(): array
    {
        $issues = [];

        $pool = CapitalPool::first();
        if (!$pool) {
            $issues[] = [
                'type' => 'capital_pool',
                'severity' => 'critical',
                'message' => 'Capital pool not found',
            ];
            return $issues;
        }

        $totalInvestorCapital = Investor::where('status', 'active')->sum('capital');
        $totalLoansOutstanding = Loan::where('status', 'ongoing')->sum('loan_balance');

        // Check if total capital matches investor contributions
        if (abs($pool->total_amount - $totalInvestorCapital) > 1) {
            $issues[] = [
                'type' => 'capital_pool',
                'severity' => 'warning',
                'message' => 'Capital pool total does not match investor contributions',
            ];
        }

        // Check if available amount makes sense
        $expectedAvailable = $totalInvestorCapital - $totalLoansOutstanding;
        if (abs($pool->available_amount - $expectedAvailable) > 1) {
            $issues[] = [
                'type' => 'capital_pool',
                'severity' => 'warning',
                'message' => 'Capital pool available amount inconsistent with loan deployments',
            ];
        }

        if (empty($issues)) {
            $this->line('✅ Capital pool integrity: OK');
        }

        return $issues;
    }

    private function checkDataConsistency(): array
    {
        $issues = [];

        // Check for loans without customers
        $orphanedLoans = Loan::whereDoesntHave('customer')->count();
        if ($orphanedLoans > 0) {
            $issues[] = [
                'type' => 'data_consistency',
                'severity' => 'warning',
                'message' => "{$orphanedLoans} loans found without customer records",
            ];
        }

        // Check for repayment schedules without loans
        $orphanedSchedules = DB::table('repayment_schedules')
            ->leftJoin('loans', 'repayment_schedules.loan_id', '=', 'loans.id')
            ->whereNull('loans.id')
            ->count();

        if ($orphanedSchedules > 0) {
            $issues[] = [
                'type' => 'data_consistency',
                'severity' => 'warning',
                'message' => "{$orphanedSchedules} repayment schedules found without loan records",
            ];
        }

        // Check for negative balances
        $negativeBalances = Loan::where('loan_balance', '<', 0)->count();
        if ($negativeBalances > 0) {
            $issues[] = [
                'type' => 'data_consistency',
                'severity' => 'critical',
                'message' => "{$negativeBalances} loans have negative balances",
            ];
        }

        if (empty($issues)) {
            $this->line('✅ Data consistency: OK');
        }

        return $issues;
    }

    private function checkDiskSpace(): array
    {
        $issues = [];

        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usedPercentage > 90) {
            $issues[] = [
                'type' => 'disk_space',
                'severity' => 'critical',
                'message' => "Disk space critically low: {$usedPercentage}% used",
            ];
        } elseif ($usedPercentage > 80) {
            $issues[] = [
                'type' => 'disk_space',
                'severity' => 'warning',
                'message' => "Disk space getting low: {$usedPercentage}% used",
            ];
        } else {
            $this->line('✅ Disk space: OK');
        }

        return $issues;
    }

    private function checkJobQueue(): array
    {
        $issues = [];

        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > 0) {
            $issues[] = [
                'type' => 'job_queue',
                'severity' => 'warning',
                'message' => "{$failedJobs} failed jobs in queue",
            ];
        }

        // Check for jobs stuck in processing
        $stuckJobs = DB::table('jobs')
            ->where('reserved_at', '<', now()->subHours(1)->timestamp)
            ->where('reserved_at', '>', 0)
            ->count();

        if ($stuckJobs > 0) {
            $issues[] = [
                'type' => 'job_queue',
                'severity' => 'warning',
                'message' => "{$stuckJobs} jobs appear to be stuck in processing",
            ];
        }

        if ($failedJobs === 0 && $stuckJobs === 0) {
            $this->line('✅ Job queue: OK');
        }

        return $issues;
    }

    private function fixIssues(array $issues): void
    {
        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'capital_pool':
                    $this->call('capital:sync', ['--fix' => true]);
                    break;

                case 'job_queue':
                    if (strpos($issue['message'], 'failed jobs') !== false) {
                        $this->line('  → Clearing failed jobs...');
                        DB::table('failed_jobs')->truncate();
                    }
                    break;

                case 'data_consistency':
                    $this->line('  → Data consistency issues require manual review');
                    break;

                default:
                    $this->line("  → Cannot auto-fix issue of type: {$issue['type']}");
            }
        }
    }
}
