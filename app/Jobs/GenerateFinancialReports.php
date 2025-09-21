<?php

namespace App\Jobs;

use App\Models\CapitalPool;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateFinancialReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportType;
    protected $period;

    public function __construct(string $reportType = 'monthly', Carbon $period = null)
    {
        $this->reportType = $reportType;
        $this->period = $period ?? now();
    }

    public function handle()
    {
        switch ($this->reportType) {
            case 'daily':
                $this->generateDailyReport();
                break;
            case 'weekly':
                $this->generateWeeklyReport();
                break;
            case 'monthly':
                $this->generateMonthlyReport();
                break;
            case 'quarterly':
                $this->generateQuarterlyReport();
                break;
        }
    }

    private function generateMonthlyReport()
    {
        $startDate = $this->period->copy()->startOfMonth();
        $endDate = $this->period->copy()->endOfMonth();

        $report = [
            'period' => $startDate->format('F Y'),
            'generated_at' => now()->toISOString(),

            // Capital Pool Status
            'capital_pool' => $this->getCapitalPoolStatus(),

            // Loan Portfolio
            'loans' => $this->getLoanPortfolioData($startDate, $endDate),

            // Investor Performance
            'investors' => $this->getInvestorPerformanceData($startDate, $endDate),

            // Financial Performance
            'financial_performance' => $this->getFinancialPerformanceData($startDate, $endDate),

            // Risk Metrics
            'risk_metrics' => $this->getRiskMetricsData(),
        ];

        // Save report to storage
        $filename = "financial_reports/monthly/" . $startDate->format('Y-m') . "_financial_report.json";
        Storage::put($filename, json_encode($report, JSON_PRETTY_PRINT));

        Log::channel('financial')->info("Monthly financial report generated for {$report['period']}");

        return $report;
    }

    private function getCapitalPoolStatus(): array
    {
        $pool = CapitalPool::first();

        return [
            'total_capital' => $pool->total_amount ?? 0,
            'available_capital' => $pool->available_amount ?? 0,
            'deployed_capital' => ($pool->total_amount ?? 0) - ($pool->available_amount ?? 0),
            'utilization_rate' => $pool->total_amount > 0 ?
                (($pool->total_amount - $pool->available_amount) / $pool->total_amount) * 100 : 0,
        ];
    }

    private function getLoanPortfolioData(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_active_loans' => Loan::where('status', 'ongoing')->count(),
            'new_loans_issued' => Loan::whereBetween('created_at', [$startDate, $endDate])->count(),
            'loans_completed' => Loan::where('status', 'completed')
                ->whereBetween('updated_at', [$startDate, $endDate])->count(),
            'total_principal_outstanding' => Loan::where('status', 'ongoing')->sum('loan_balance'),
            'overdue_loans_count' => Loan::where('overdue_payment', '>', 0)->count(),
            'total_overdue_amount' => Loan::sum('overdue_payment'),
        ];
    }

    private function getInvestorPerformanceData(Carbon $startDate, Carbon $endDate): array
    {
        $totalWithdrawals = Withdrawal::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $totalROIAccrued = Investor::sum('roi_accrued');
        $totalCapital = Investor::sum('capital');

        return [
            'total_investors' => Investor::count(),
            'active_investors' => Investor::where('status', 'active')->count(),
            'total_capital_invested' => $totalCapital,
            'total_roi_accrued' => $totalROIAccrued,
            'withdrawals_this_period' => $totalWithdrawals,
            'average_roi_rate' => Investor::avg('roi_percentage'),
            'roi_to_capital_ratio' => $totalCapital > 0 ? ($totalROIAccrued / $totalCapital) * 100 : 0,
        ];
    }

    private function getFinancialPerformanceData(Carbon $startDate, Carbon $endDate): array
    {
        $totalRevenue = Transaction::where('type', 'repayment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalExpenses) / $totalRevenue) * 100 : 0,
        ];
    }

    private function getRiskMetricsData(): array
    {
        $totalLoans = Loan::count();
        $overdueLoans = Loan::where('overdue_payment', '>', 0)->count();
        $defaultedLoans = Loan::where('status', 'defaulted')->count();

        return [
            'default_rate' => $totalLoans > 0 ? ($defaultedLoans / $totalLoans) * 100 : 0,
            'overdue_rate' => $totalLoans > 0 ? ($overdueLoans / $totalLoans) * 100 : 0,
            'portfolio_at_risk' => $this->calculatePortfolioAtRisk(),
        ];
    }

    private function calculatePortfolioAtRisk(): float
    {
        $totalPortfolio = Loan::where('status', 'ongoing')->sum('loan_balance');
        $portfolioAtRisk = Loan::where('status', 'ongoing')
            ->where('overdue_payment', '>', 0)
            ->sum('loan_balance');

        return $totalPortfolio > 0 ? ($portfolioAtRisk / $totalPortfolio) * 100 : 0;
    }

    private function generateDailyReport()
    {
        // Implementation for daily reports
    }

    private function generateWeeklyReport()
    {
        // Implementation for weekly reports
    }

    private function generateQuarterlyReport()
    {
        // Implementation for quarterly reports
    }
}
