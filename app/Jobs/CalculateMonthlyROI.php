<?php

namespace App\Jobs;

use App\Models\Investor;
use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateMonthlyROI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        DB::transaction(function () {
            $this->calculateInvestorROI();
            $this->checkOverdueLoans();
        });
    }

    private function calculateInvestorROI()
    {
        $investors = Investor::where('status', 'active')->get();
        $totalCapital = $investors->sum('capital');

        // Get total monthly interest from active loans
        $monthlyInterestEarned = Loan::where('status', 'ongoing')
            ->sum(DB::raw('(principal * interest_rate / 100) / 12'));

        if ($monthlyInterestEarned <= 0 || $totalCapital <= 0) {
            return;
        }

        foreach ($investors as $investor) {
            $capitalRatio = $investor->capital / $totalCapital;
            $monthlyROI = $monthlyInterestEarned * $capitalRatio * ($investor->roi_percentage / 100);

            $investor->increment('roi_accrued', $monthlyROI);

            Log::info("ROI calculated for investor {$investor->id}: " . format_currency($monthlyROI));
        }

        activity()->log("Monthly ROI calculated for all investors. Total: " . format_currency($monthlyInterestEarned));
    }

    private function checkOverdueLoans()
    {
        $overdueSchedules = \App\Models\RepaymentSchedule::where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueSchedules as $schedule) {
            $schedule->update(['status' => 'overdue']);

            // Add overdue amount to loan
            $schedule->loan->increment('overdue_payment', $schedule->amount_due);
        }

        if ($overdueSchedules->count() > 0) {
            activity()->log("Marked {$overdueSchedules->count()} repayment schedules as overdue");
        }
    }
}
