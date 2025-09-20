<?php
namespace App\Services;

use App\Models\Loan;
use App\Models\Investor;
use App\Models\Transaction;
use App\Models\Repayment;
use App\Models\RepaymentSchedule;
use App\Models\CapitalPool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Get overview metrics for dashboard.
     */
    public function getOverviewMetrics(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;

        return [
            'total_loans_disbursed' => Loan::whereBetween('created_at', [$startDate, $endDate])->sum('principal'),
            'total_repayments_received' => Repayment::whereBetween('paid_on', [$startDate, $endDate])->sum('amount'),
            'active_loans_count' => Loan::where('status', 'ongoing')->count(),
            'overdue_loans_count' => Loan::where('overdue_payment', '>', 0)->count(),
            'total_investors' => Investor::where('status', 'active')->count(),
            'capital_utilization' => $this->getCapitalUtilizationRate(),
            'portfolio_performance' => $this->getPortfolioPerformanceScore(),
            'growth_metrics' => $this->getGrowthMetrics($dateRange),
        ];
    }

    /**
     * Get loan performance data.
     */
    public function getLoanPerformanceData(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;

        $loansByStatus = Loan::selectRaw('status, COUNT(*) as count, SUM(principal) as total_amount')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $monthlyDisbursements = $this->getMonthlyDisbursements($startDate, $endDate);
        $repaymentTrends = $this->getRepaymentTrends($startDate, $endDate);

        return [
            'loan_distribution' => [
                'ongoing' => $loansByStatus->get('ongoing', (object)['count' => 0, 'total_amount' => 0]),
                'completed' => $loansByStatus->get('completed', (object)['count' => 0, 'total_amount' => 0]),
                'defaulted' => $loansByStatus->get('defaulted', (object)['count' => 0, 'total_amount' => 0]),
            ],
            'monthly_disbursements' => $monthlyDisbursements,
            'repayment_trends' => $repaymentTrends,
            'average_loan_size' => Loan::whereBetween('created_at', [$startDate, $endDate])->avg('principal'),
            'loan_completion_rate' => $this->getLoanCompletionRate($dateRange),
            'top_performing_loans' => $this->getTopPerformingLoans(10),
        ];
    }

    /**
     * Get investor analytics data.
     */
    public function getInvestorAnalytics(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;

        $investorPerformance = Investor::with('user')
            ->withSum(['withdrawals' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'amount')
            ->get()
            ->map(function ($investor) {
                $totalWithdrawn = $investor->withdrawals->sum('amount');
                $availableROI = $investor->roi_accrued - $totalWithdrawn;
                $roiRate = $investor->capital > 0 ? ($investor->roi_accrued / $investor->capital) * 100 : 0;

                return [
                    'id' => $investor->id,
                    'name' => $investor->user->name,
                    'capital' => $investor->capital,
                    'roi_accrued' => $investor->roi_accrued,
                    'roi_percentage' => $investor->roi_percentage,
                    'total_withdrawn' => $totalWithdrawn,
                    'available_roi' => $availableROI,
                    'effective_roi_rate' => $roiRate,
                    'investment_duration' => $investor->created_at->diffInMonths(now()),
                ];
            });

        return [
            'total_capital_invested' => $investorPerformance->sum('capital'),
            'total_roi_distributed' => $investorPerformance->sum('roi_accrued'),
            'average_roi_rate' => $investorPerformance->avg('roi_percentage'),
            'investor_performance' => $investorPerformance,
            'roi_distribution_trend' => $this->getROIDistributionTrend($startDate, $endDate),
            'investor_retention_rate' => $this->getInvestorRetentionRate(),
        ];
    }

    /**
     * Get risk assessment data.
     */
    public function getRiskAssessmentData(array $dateRange): array
    {
        $totalLoans = Loan::count();
        $overdueLoans = Loan::where('overdue_payment', '>', 0)->count();
        $defaultedLoans = Loan::where('status', 'defaulted')->count();
        $totalPortfolioValue = Loan::where('status', 'ongoing')->sum('loan_balance');
        $portfolioAtRisk = Loan::where('status', 'ongoing')
            ->where('overdue_payment', '>', 0)
            ->sum('loan_balance');

        return [
            'default_rate' => $totalLoans > 0 ? ($defaultedLoans / $totalLoans) * 100 : 0,
            'overdue_rate' => $totalLoans > 0 ? ($overdueLoans / $totalLoans) * 100 : 0,
            'portfolio_at_risk_rate' => $totalPortfolioValue > 0 ? ($portfolioAtRisk / $totalPortfolioValue) * 100 : 0,
            'total_portfolio_value' => $totalPortfolioValue,
            'portfolio_at_risk_value' => $portfolioAtRisk,
            'risk_distribution' => $this->getRiskDistributionByTenure(),
            'overdue_aging_analysis' => $this->getOverdueAgingAnalysis(),
            'concentration_risk' => $this->getConcentrationRisk(),
        ];
    }

    /**
     * Get financial trends data.
     */
    public function getFinancialTrends(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;

        $monthlyRevenue = $this->getMonthlyRevenue($startDate, $endDate);
        $monthlyExpenses = $this->getMonthlyExpenses($startDate, $endDate);
        $profitabilityTrend = $this->getProfitabilityTrend($startDate, $endDate);

        return [
            'monthly_revenue' => $monthlyRevenue,
            'monthly_expenses' => $monthlyExpenses,
            'profitability_trend' => $profitabilityTrend,
            'revenue_growth_rate' => $this->getRevenueGrowthRate($dateRange),
            'cost_to_income_ratio' => $this->getCostToIncomeRatio($dateRange),
            'net_interest_margin' => $this->getNetInterestMargin($dateRange),
        ];
    }

    /**
     * Get charts data for dashboard visualizations.
     */
    public function getChartsData(array $dateRange): array
    {
        return [
            'loan_status_pie' => $this->getLoanStatusChartData(),
            'monthly_disbursement_line' => $this->getMonthlyDisbursementChartData($dateRange),
            'repayment_trend_area' => $this->getRepaymentTrendChartData($dateRange),
            'investor_performance_bar' => $this->getInvestorPerformanceChartData(),
            'risk_metrics_radar' => $this->getRiskMetricsChartData(),
        ];
    }

    /**
     * Generate comprehensive report.
     */
    public function generateReport(string $reportType, array $dateRange, string $format): array
    {
        switch ($reportType) {
            case 'loans':
                $data = $this->generateLoanReport($dateRange);
                break;
            case 'investors':
                $data = $this->generateInvestorReport($dateRange);
                break;
            case 'financial':
                $data = $this->generateFinancialReport($dateRange);
                break;
            case 'risk':
                $data = $this->generateRiskReport($dateRange);
                break;
            default:
                throw new \InvalidArgumentException("Invalid report type: {$reportType}");
        }

        return $this->formatReportForDownload($data, $format, $reportType, $dateRange);
    }

    /**
     * Get portfolio risk analysis.
     */
    public function getPortfolioRiskAnalysis(): array
    {
        $riskMetrics = [
            'value_at_risk' => $this->calculateValueAtRisk(),
            'expected_shortfall' => $this->calculateExpectedShortfall(),
            'loan_concentration' => $this->getLoanConcentrationRisk(),
            'geographic_concentration' => $this->getGeographicConcentration(),
            'sector_concentration' => $this->getSectorConcentration(),
            'vintage_analysis' => $this->getVintageAnalysis(),
        ];

        return $riskMetrics;
    }

    /**
     * Get real-time metrics for dashboard updates.
     */
    public function getRealTimeMetrics(): array
    {
        $pool = CapitalPool::first();

        return [
            'active_loans' => Loan::where('status', 'ongoing')->count(),
            'pending_repayments' => RepaymentSchedule::where('status', 'pending')
                ->where('due_date', '<=', now())
                ->count(),
            'total_overdue_amount' => Loan::sum('overdue_payment'),
            'available_capital' => $pool->available_amount ?? 0,
            'capital_utilization_rate' => $this->getCapitalUtilizationRate(),
            'today_repayments' => Repayment::whereDate('paid_on', today())->sum('amount'),
            'today_disbursements' => Loan::whereDate('created_at', today())->sum('principal'),
            'alerts' => $this->getSystemAlerts(),
        ];
    }

    // Helper methods for calculations
    private function getCapitalUtilizationRate(): float
    {
        $pool = CapitalPool::first();
        if (!$pool || $pool->total_amount <= 0) return 0;

        $deployed = $pool->total_amount - $pool->available_amount;
        return ($deployed / $pool->total_amount) * 100;
    }

    private function getPortfolioPerformanceScore(): float
    {
        $totalLoans = Loan::count();
        if ($totalLoans === 0) return 0;

        $ongoingLoans = Loan::where('status', 'ongoing')->count();
        $completedLoans = Loan::where('status', 'completed')->count();
        $overdueLoans = Loan::where('overdue_payment', '>', 0)->count();

        // Calculate score based on completion rate and overdue rate
        $completionRate = ($completedLoans / $totalLoans) * 100;
        $overdueRate = ($overdueLoans / $totalLoans) * 100;

        return max(0, $completionRate - ($overdueRate * 2)); // Penalize overdue loans more heavily
    }

    private function getGrowthMetrics(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;
        $previousPeriod = [
            $startDate->copy()->subDays($startDate->diffInDays($endDate)),
            $startDate
        ];

        $currentRevenue = Repayment::whereBetween('paid_on', $dateRange)->sum('amount');
        $previousRevenue = Repayment::whereBetween('paid_on', $previousPeriod)->sum('amount');

        $currentLoans = Loan::whereBetween('created_at', $dateRange)->count();
        $previousLoans = Loan::whereBetween('created_at', $previousPeriod)->count();

        return [
            'revenue_growth' => $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0,
            'loan_volume_growth' => $previousLoans > 0 ? (($currentLoans - $previousLoans) / $previousLoans) * 100 : 0,
        ];
    }

    private function getMonthlyDisbursements(Carbon $startDate, Carbon $endDate): Collection
    {
        return Loan::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(principal) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getRepaymentTrends(Carbon $startDate, Carbon $endDate): Collection
    {
        return Repayment::selectRaw('DATE_FORMAT(paid_on, "%Y-%m") as month, SUM(amount) as total')
            ->whereBetween('paid_on', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getLoanCompletionRate(array $dateRange): float
    {
        [$startDate, $endDate] = $dateRange;

        $totalLoans = Loan::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedLoans = Loan::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $totalLoans > 0 ? ($completedLoans / $totalLoans) * 100 : 0;
    }

    private function getTopPerformingLoans(int $limit): Collection
    {
        return Loan::with('customer.user')
            ->withSum('repayments', 'amount')
            ->where('status', '!=', 'defaulted')
            ->orderByDesc('repayments_sum_amount')
            ->limit($limit)
            ->get();
    }

    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Check for loans overdue by more than 30 days
        $criticalOverdue = Loan::where('overdue_payment', '>', 0)
            ->whereHas('schedules', function ($query) {
                $query->where('status', 'overdue')
                    ->where('due_date', '<', now()->subDays(30));
            })
            ->count();

        if ($criticalOverdue > 0) {
            $alerts[] = [
                'type' => 'critical',
                'message' => "{$criticalOverdue} loans are overdue by more than 30 days",
                'action_url' => route('admin.loans.index', ['filter' => 'critical_overdue']),
            ];
        }

        // Check capital pool utilization
        $utilizationRate = $this->getCapitalUtilizationRate();
        if ($utilizationRate > 90) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Capital pool utilization is at {$utilizationRate}%",
                'action_url' => route('admin.dashboard'),
            ];
        }

        return $alerts;
    }

    // Additional helper methods would go here...
    private function getROIDistributionTrend(Carbon $startDate, Carbon $endDate): Collection
    {
        // Implementation for ROI distribution trend
        return collect();
    }

    private function getInvestorRetentionRate(): float
    {
        // Implementation for investor retention rate calculation
        return 0.0;
    }

    private function getRiskDistributionByTenure(): array
    {
        // Implementation for risk distribution by loan tenure
        return [];
    }

    private function getOverdueAgingAnalysis(): array
    {
        // Implementation for overdue aging analysis
        return [];
    }

    private function getConcentrationRisk(): array
    {
        // Implementation for concentration risk analysis
        return [];
    }

    // Chart data methods
    private function getLoanStatusChartData(): array
    {
        $statusCounts = Loan::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_keys($statusCounts),
            'datasets' => [
                [
                    'data' => array_values($statusCounts),
                    'backgroundColor' => ['#10B981', '#F59E0B', '#EF4444'], // Green, Yellow, Red
                ]
            ]
        ];
    }

    private function getMonthlyDisbursementChartData(array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;
        $data = $this->getMonthlyDisbursements($startDate, $endDate);

        return [
            'labels' => $data->pluck('month')->toArray(),
            'datasets' => [
                [
                    'label' => 'Monthly Disbursements',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ]
            ]
        ];
    }

    // Additional chart methods and report generation methods...
    private function getRepaymentTrendChartData(array $dateRange): array
    {
        // Implementation
        return [];
    }

    private function getInvestorPerformanceChartData(): array
    {
        // Implementation
        return [];
    }

    private function getRiskMetricsChartData(): array
    {
        // Implementation
        return [];
    }

    private function generateLoanReport(array $dateRange): array
    {
        // Implementation for detailed loan report
        return [];
    }

    private function generateInvestorReport(array $dateRange): array
    {
        // Implementation for investor report
        return [];
    }

    private function generateFinancialReport(array $dateRange): array
    {
        // Implementation for financial report
        return [];
    }

    private function generateRiskReport(array $dateRange): array
    {
        // Implementation for risk report
        return [];
    }

    private function formatReportForDownload(array $data, string $format, string $reportType, array $dateRange): array
    {
        // Implementation for formatting and generating downloadable reports
        return [
            'download_response' => response()->download('/path/to/file')
        ];
    }

    // Risk calculation methods
    private function calculateValueAtRisk(): float
    {
        // Implementation for Value at Risk calculation
        return 0.0;
    }

    private function calculateExpectedShortfall(): float
    {
        // Implementation for Expected Shortfall calculation
        return 0.0;
    }

    private function getLoanConcentrationRisk(): array
    {
        // Implementation
        return [];
    }

    private function getGeographicConcentration(): array
    {
        // Implementation
        return [];
    }

    private function getSectorConcentration(): array
    {
        // Implementation
        return [];
    }

    private function getVintageAnalysis(): array
    {
        // Implementation
        return [];
    }

    // Financial metrics methods
    private function getMonthlyRevenue(Carbon $startDate, Carbon $endDate): Collection
    {
        return collect(); // Implementation
    }

    private function getMonthlyExpenses(Carbon $startDate, Carbon $endDate): Collection
    {
        return collect(); // Implementation
    }

    private function getProfitabilityTrend(Carbon $startDate, Carbon $endDate): Collection
    {
        return collect(); // Implementation
    }

    private function getRevenueGrowthRate(array $dateRange): float
    {
        return 0.0; // Implementation
    }

    private function getCostToIncomeRatio(array $dateRange): float
    {
        return 0.0; // Implementation
    }

    private function getNetInterestMargin(array $dateRange): float
    {
        return 0.0; // Implementation
    }
}
