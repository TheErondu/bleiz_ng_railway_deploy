<?php

namespace App\Services;

use App\Models\CapitalPool;
use App\Models\DashboardStats;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    protected $capitalPoolService;

    public function __construct(CapitalPoolService $capitalPoolService)
    {
        $this->capitalPoolService = $capitalPoolService;
    }

    public static function getDashboardStats(): DashboardStats
    {
        try {
            $month = request()->query('month', Carbon::now()->month);
            $month = max(1, min(12, (int)$month));
            $year = 2025;

            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $pool = app(\App\Services\CapitalPoolService::class)->getPool();
            if (!$pool) {
                throw new \Exception('Capital pool not found.');
            }

            $totalCapital = $pool->total_amount;
            $idleCapital = $pool->available_amount;

            $loanPortfolio = Loan::where('status', 'ongoing')->sum('loan_balance');

            $totalWithdrawals = Withdrawal::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $investorStats = Investor::with(['user', 'withdrawals' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            }])->get()->map(function ($investor) {
                return [
                    'name' => $investor->user->name,
                    'capital' => $investor->capital,
                    'roi_percentage' => $investor->roi_percentage,
                    'roi_accrued' => $investor->roi_accrued,
                    'withdrawn' => $investor->withdrawals->sum('amount'),
                    'available' => $investor->roi_accrued - $investor->withdrawals->sum('amount'),
                ];
            });

            $totalROI = $investorStats->sum('roi_accrued');
            $availableROIPool = $investorStats->sum('available');
            $totalIdleFunds = $idleCapital + $availableROIPool;

            $currentROI = Repayment::join('repayment_schedules', 'repayments.repayment_schedule_id', '=', 'repayment_schedules.id')
                ->join('loans', 'repayments.loan_id', '=', 'loans.id')
                ->where('repayment_schedules.status', 'paid')
                ->whereBetween('repayments.paid_on', [$startOfMonth, $endOfMonth])
                ->sum('repayments.amount');

            $nextPotentialROI = Loan::where('status', 'ongoing')->sum('current_interest');

            $realizedProfit = Repayment::join('repayment_schedules', 'repayments.repayment_schedule_id', '=', 'repayment_schedules.id')
                ->join('loans', 'repayments.loan_id', '=', 'loans.id')
                ->where('repayment_schedules.status', 'paid')
                ->whereBetween('repayments.paid_on', [$startOfMonth, $endOfMonth])
                ->sum('repayments.amount');

            $bleizProfit = $realizedProfit - $totalROI;

            $months = collect(range(1, 12))->map(function ($m) use ($year) {
                return [
                    'value' => $m,
                    'label' => Carbon::create($year, $m, 1)->format('F'),
                ];
            });

            return new DashboardStats([
                'pool' => $pool,
                'totalCapital' => $totalCapital,
                'idleCapital' => $idleCapital,
                'loanPortfolio' => $loanPortfolio,
                'totalWithdrawals' => $totalWithdrawals,
                'totalROI' => $totalROI,
                'availableROIPool' => $availableROIPool,
                'totalIdleFunds' => $totalIdleFunds,
                'currentROI' => $currentROI,
                'nextPotentialROI' => $nextPotentialROI,
                'realizedProfit' => $realizedProfit,
                'bleizProfit' => $bleizProfit,
                'investorStats' => $investorStats,
                'months' => $months,
                'month' => $month,
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());

            return new DashboardStats([
                'pool' => null,
                'totalCapital' => 0,
                'idleCapital' => 0,
                'loanPortfolio' => 0,
                'totalWithdrawals' => 0,
                'totalROI' => 0,
                'availableROIPool' => 0,
                'totalIdleFunds' => 0,
                'currentROI' => 0,
                'nextPotentialROI' => 0,
                'realizedProfit' => 0,
                'bleizProfit' => 0,
                'investorStats' => collect([]),
                'months' => collect([]),
                'month' => Carbon::now()->month,
            ]);
        }
    }
    
}
