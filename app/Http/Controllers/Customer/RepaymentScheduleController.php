<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\Transaction;
use App\Services\CapitalPoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepaymentScheduleController extends Controller
{
    protected $capitalPoolService;

    public function __construct(CapitalPoolService $capitalPoolService)
    {
        $this->middleware(['auth', 'role:customer', 'verified']);
        $this->capitalPoolService = $capitalPoolService;
    }

    /**
     * Display repayment schedule for a loan.
     */
    public function index(Loan $loan)
    {
        // Ensure customer can only view their own loans
        if ($loan->customer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to loan.');
        }

        $loan->load([
            'schedules' => function ($query) {
                $query->orderBy('due_date');
            },
            'repayments' => function ($query) {
                $query->latest();
            }
        ]);

        // Calculate summary metrics
        $totalPaid = $loan->repayments->sum('amount');
        $totalDue = $loan->schedules->sum('amount_due');
        $remainingBalance = $totalDue - $totalPaid;
        $nextDueDate = $loan->schedules->where('status', 'pending')->first()?->due_date;
        $nextDueAmount = $loan->schedules->where('status', 'pending')->first()?->amount_due ?? 0;

        // Payment history
        $paymentHistory = $loan->repayments()
            ->with('schedule')
            ->latest()
            ->limit(10)
            ->get();

        return view('customer.loans.repayments.index', compact(
            'loan',
            'totalPaid',
            'totalDue',
            'remainingBalance',
            'nextDueDate',
            'nextDueAmount',
            'paymentHistory'
        ));
    }

    /**
     * Show payment form.
     */
    public function makePayment(Loan $loan)
    {
        // Ensure customer can only access their own loans
        if ($loan->customer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to loan.');
        }

        // Get next pending schedule
        $nextSchedule = $loan->schedules()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->first();

        if (!$nextSchedule) {
            return redirect()->route('customer.repayments.index', $loan)
                ->with('info', 'No pending payments for this loan.');
        }

        $paymentMethods = [
            'transfer' => 'Bank Transfer',
            'pos' => 'POS Payment',
            'cash' => 'Cash Payment'
        ];

        return view('customer.loans.repayments.pay', compact('loan', 'nextSchedule', 'paymentMethods'));
    }

    /**
     * Process payment submission.
     */
    public function storePayment(Request $request, Loan $loan)
    {
        // Ensure customer can only pay for their own loans
        if ($loan->customer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to loan.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100', // Minimum payment 100
            'payment_method' => 'required|in:transfer,pos,cash',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get next pending schedule
        $schedule = $loan->schedules()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->first();

        if (!$schedule) {
            return redirect()->back()
                ->with('error', 'No pending repayments found for this loan.');
        }

        // Validate payment amount doesn't exceed what's due
        $remainingDue = $schedule->amount_due - $schedule->amount_paid;
        if ($request->amount > $remainingDue) {
            return redirect()->back()
                ->with('error', "Payment amount cannot exceed remaining due amount of " . format_currency($remainingDue));
        }

        DB::transaction(function () use ($request, $loan, $schedule) {
            // Create repayment record
            $repayment = Repayment::create([
                'repayment_schedule_id' => $schedule->id,
                'loan_id' => $loan->id,
                'amount' => $request->amount,
                'paid_on' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'received_by' => Auth::id(), // Customer initiated
            ]);

            // Update repayment schedule
            $schedule->increment('amount_paid', $request->amount);

            if ($schedule->amount_paid >= $schedule->amount_due) {
                $schedule->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            // Update loan balances
            $loan->increment('repaid_principal', $request->amount);
            $loan->decrement('loan_balance', min($request->amount, $loan->loan_balance));

            // Handle overdue payments
            if ($loan->overdue_payment > 0) {
                $overdueReduction = min($request->amount, $loan->overdue_payment);
                $loan->decrement('overdue_payment', $overdueReduction);
            }

            // Check if loan is fully paid
            $totalPaid = $loan->repayments()->sum('amount');
            if ($totalPaid >= $loan->total_obligation) {
                $loan->update([
                    'status' => 'completed',
                    'loan_balance' => 0
                ]);
            }

            // Return funds to capital pool
            $this->capitalPoolService->receiveRepayment($request->amount);

            // Create transaction record
            Transaction::create([
                'type' => 'repayment',
                'reference_id' => Transaction::generateReferenceId('RPY'),
                'amount' => $request->amount,
                'description' => "Loan repayment for loan #{$loan->id}",
                'date' => $request->payment_date,
                'user_id' => Auth::id(),
            ]);

            activity()->log("Customer payment: " . format_currency($request->amount) . " for loan #{$loan->id}");
        });

        return redirect()->route('customer.repayments.index', $loan)
            ->with('success', 'Payment recorded successfully. Thank you!');
    }

    /**
     * Get payment history for AJAX requests.
     */
    public function getPaymentHistory(Loan $loan)
    {
        if ($loan->customer->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = $loan->repayments()
            ->with('schedule')
            ->latest()
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => format_currency($payment->amount),
                    'date' => $payment->paid_on,
                    'method' => ucfirst($payment->payment_method),
                    'due_date' => $payment->schedule->due_date->format('M d, Y'),
                    'status' => $payment->schedule->status,
                ];
            });

        return response()->json($payments);
    }

    /**
     * Download payment receipt.
     */
    public function downloadReceipt(Loan $loan, Repayment $repayment)
    {
        if ($loan->customer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($repayment->loan_id !== $loan->id) {
            abort(404, 'Payment not found for this loan.');
        }

        // Generate PDF receipt (you'll need to install a PDF library like dompdf)
        // For now, return a simple view that can be printed
        return view('customer.loans.repayments.receipt', compact('loan', 'repayment'));
    }
}
