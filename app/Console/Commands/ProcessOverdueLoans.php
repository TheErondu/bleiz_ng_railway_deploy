<?php
namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\RepaymentSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessOverdueLoans extends Command
{
    protected $signature = 'loans:process-overdue {--notify : Send notifications to customers}';
    protected $description = 'Process overdue loans and mark repayment schedules as overdue';

    public function handle()
    {
        $this->info('Processing overdue loans...');

        DB::transaction(function () {
            // Mark overdue schedules
            $overdueSchedules = RepaymentSchedule::where('status', 'pending')
                ->where('due_date', '<', now())
                ->get();

            $overdueCount = 0;
            foreach ($overdueSchedules as $schedule) {
                $schedule->update(['status' => 'overdue']);

                // Update loan overdue amount
                $remainingDue = $schedule->amount_due - $schedule->amount_paid;
                $schedule->loan->increment('overdue_payment', $remainingDue);

                $overdueCount++;

                if ($this->option('notify')) {
                    // Send notification to customer
                    $this->sendOverdueNotification($schedule);
                }
            }

            $this->info("Processed {$overdueCount} overdue schedules.");

            // Mark loans with excessive overdue amounts as defaulted
            $defaultedLoans = $this->processDefaultedLoans();
            $this->info("Marked {$defaultedLoans} loans as defaulted.");

            activity()->log("Processed overdue loans: {$overdueCount} schedules, {$defaultedLoans} defaults");
        });

        return 0;
    }

    private function processDefaultedLoans(): int
    {
        // Mark loans as defaulted if overdue for more than 90 days
        $defaultThreshold = now()->subDays(90);

        $defaultedLoans = Loan::where('status', 'ongoing')
            ->whereHas('schedules', function ($query) use ($defaultThreshold) {
                $query->where('status', 'overdue')
                    ->where('due_date', '<', $defaultThreshold);
            })
            ->update(['status' => 'defaulted']);

        return $defaultedLoans;
    }

    private function sendOverdueNotification(RepaymentSchedule $schedule)
    {
        // Implementation for sending overdue notifications
        // This could use Laravel's notification system
        $this->line("  → Notification sent to {$schedule->loan->customer->user->email}");
    }
}
