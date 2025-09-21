<?php
namespace App\Jobs;

use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessPendingWithdrawals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24)) // Auto-approve after 24 hours
            ->get();

        foreach ($pendingWithdrawals as $withdrawal) {
            DB::transaction(function () use ($withdrawal) {
                $withdrawal->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);

                // Create transaction record
                Transaction::create([
                    'type' => 'expense',
                    'reference_id' => Transaction::generateReferenceId('WTH'),
                    'amount' => $withdrawal->amount,
                    'description' => 'Investor withdrawal approval',
                    'date' => now()->toDateString(),
                    'user_id' => $withdrawal->investor_id,
                ]);

                activity()
                    ->causedBy($withdrawal->investor)
                    ->log("Withdrawal approved: " . format_currency($withdrawal->amount));
            });
        }
    }
}
