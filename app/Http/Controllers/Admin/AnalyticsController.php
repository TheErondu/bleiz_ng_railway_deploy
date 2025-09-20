<?php

// app/Http/Controllers/Admin/AnalyticsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->middleware('role:admin');
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'last_30_days');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'overview' => $this->analyticsService->getOverviewMetrics($dateRange),
            'loan_performance' => $this->analyticsService->getLoanPerformanceData($dateRange),
            'investor_analytics' => $this->analyticsService->getInvestorAnalytics($dateRange),
            'risk_assessment' => $this->analyticsService->getRiskAssessmentData($dateRange),
            'financial_trends' => $this->analyticsService->getFinancialTrends($dateRange),
            'charts_data' => $this->analyticsService->getChartsData($dateRange),
        ];

        return view('admin.analytics.index', compact('analytics', 'period'));
    }

    /**
     * Get loan analytics data for AJAX requests.
     */
    public function getLoanAnalytics(Request $request)
    {
        $period = $request->get('period', 'last_30_days');
        $dateRange = $this->getDateRange($period);

        $data = $this->analyticsService->getLoanPerformanceData($dateRange);
        return response()->json($data);
    }

    /**
     * Get investor performance data.
     */
    public function getInvestorPerformance(Request $request)
    {
        $period = $request->get('period', 'last_30_days');
        $dateRange = $this->getDateRange($period);

        $data = $this->analyticsService->getInvestorAnalytics($dateRange);
        return response()->json($data);
    }

    /**
     * Generate and download comprehensive report.
     */
    public function downloadReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:loans,investors,financial,risk',
            'period' => 'required|in:last_7_days,last_30_days,last_90_days,last_year,custom',
            'start_date' => 'required_if:period,custom|date',
            'end_date' => 'required_if:period,custom|date|after:start_date',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $dateRange = $request->period === 'custom'
            ? [Carbon::parse($request->start_date), Carbon::parse($request->end_date)]
            : $this->getDateRange($request->period);

        $reportData = $this->analyticsService->generateReport(
            $request->report_type,
            $dateRange,
            $request->format
        );

        activity()->log("Generated {$request->report_type} report for period: {$request->period}");

        return $reportData['download_response'];
    }

    /**
     * Get portfolio risk analysis.
     */
    public function getRiskAnalysis(Request $request)
    {
        $data = $this->analyticsService->getPortfolioRiskAnalysis();
        return response()->json($data);
    }

    /**
     * Get real-time dashboard metrics.
     */
    public function getRealTimeMetrics()
    {
        $metrics = $this->analyticsService->getRealTimeMetrics();
        return response()->json($metrics);
    }

    /**
     * Get date range based on period.
     */
    private function getDateRange(string $period): array
    {
        switch ($period) {
            case 'last_7_days':
                return [Carbon::now()->subDays(7), Carbon::now()];
            case 'last_30_days':
                return [Carbon::now()->subDays(30), Carbon::now()];
            case 'last_90_days':
                return [Carbon::now()->subDays(90), Carbon::now()];
            case 'last_year':
                return [Carbon::now()->subYear(), Carbon::now()];
            default:
                return [Carbon::now()->subDays(30), Carbon::now()];
        }
    }
}
