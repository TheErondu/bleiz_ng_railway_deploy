<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Services\CapitalPoolService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    protected $capitalPoolService;

    public function __construct(CapitalPoolService $capitalPoolService)
    {
        $this->middleware('role:admin');
        $this->capitalPoolService = $capitalPoolService;
    }

    /**
     * Display a listing of loans.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $loans = Loan::with('customer.user')->latest()->paginate(10);
        return view('admin.loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customers = Customer::with('user')->get();
        $pool = $this->capitalPoolService->getPool();
        $availableAmount = $pool ? $pool->available_amount : 0;
        return view('admin.loans.create', compact('customers', 'availableAmount'));
    }

    /**
     * Store a newly created loan in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'principal' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'tenure_months' => 'required|integer|min:1|max:60',
            'start_date' => 'required|date|after_or_equal:today',
            'repayment_cycle' => 'required|in:monthly,weekly,daily'
        ]);

        try {
            $pool = $this->capitalPoolService->getPool();
            if (!$pool || $pool->available_amount < $request->principal) {
                return redirect()->back()->with('error', 'Insufficient funds in capital pool.');
            }

            // Calculate interest and total obligation
            $principal = $request->principal;
            $interestRate = $request->interest_rate / 100; // Convert to decimal
            $tenureMonths = $request->tenure_months;
            $monthlyInterest = $principal * $interestRate / 12; // Simple interest per month
            $currentInterest = $monthlyInterest * $tenureMonths;
            $totalObligation = $principal + $currentInterest;

            // Create loan
            $loan = Loan::create([
                'customer_id' => $request->customer_id,
                'principal' => $principal,
                'interest_rate' => $request->interest_rate,
                'tenure_months' => $tenureMonths,
                'start_date' => $request->start_date,
                'end_date' => Carbon::parse($request->start_date)->addMonths($tenureMonths),
                'repayment_cycle' => $request->repayment_cycle,
                'status' => 'ongoing',
                'loan_balance' => $principal,
                'repaid_principal' => 0,
                'overdue_payment' => 0,
                'total_obligation' => $totalObligation,
                'current_interest' => $currentInterest
            ]);

            // Generate repayment schedules
            $this->generateRepaymentSchedules($loan);

            // Update capital pool
            $this->capitalPoolService->disburseLoan($principal);

            activity()->log("Created loan ID {$loan->id} for customer ID {$request->customer_id} with principal " . format_currency($principal));

            return redirect()->route('loans.index')->with('success', 'Loan created successfully.');
        } catch (\Exception $e) {
            Log::error('Loan creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create loan.');
        }
    }

    /**
     * Display the specified loan.
     *
     * @param \App\Models\Loan $loan
     * @return \Illuminate\View\View
     */
    public function show(Loan $loan)
    {
        $loan->load('customer.user', 'repaymentSchedules', 'repayments');
        return view('admin.loans.show', compact('loan'));
    }

    /**
     * Generate repayment schedules for a loan.
     *
     * @param \App\Models\Loan $loan
     * @return void
     */
    protected function generateRepaymentSchedules(Loan $loan)
    {
        $monthlyInterest = ($loan->principal * ($loan->interest_rate / 100)) / 12;
        $schedules = [];
        $currentDate = Carbon::parse($loan->start_date);

        for ($i = 1; $i <= $loan->tenure_months; $i++) {
            $dueDate = $currentDate->copy()->addMonths($i)->startOfMonth();
            $amountDue = $i == $loan->tenure_months ? ($loan->principal / $loan->tenure_months) + $monthlyInterest : $monthlyInterest;

            $schedules[] = [
                'loan_id' => $loan->id,
                'due_date' => $dueDate,
                'amount_due' => $amountDue,
                'amount_paid' => 0,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        \App\Models\RepaymentSchedule::insert($schedules);
    }
    /**
     * Approve a pending loan application.
     *
     * @param \App\Models\Loan $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Loan $loan)
    {
        // Check if loan is already approved
        if ($loan->status !== 'pending') {
            return back()->with('error', 'This loan has already been ' . $loan->status . '.');
        }

        try {
            // Check if capital pool has sufficient funds
            $pool = $this->capitalPoolService->getPool();
            if (!$pool || $pool->available_amount < $loan->principal) {
                return back()->with('error', 'Insufficient funds in capital pool. Available: ' . format_currency($pool->available_amount ?? 0));
            }

            // Generate unique reference number
            $referenceNumber = $this->generateLoanReference($loan);

            // Update loan status
            $loan->update([
                'status' => 'ongoing',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'disbursed_at' => now(),
                'loan_terms' => json_encode([
                    'reference' => $referenceNumber,
                    'approved_date' => now()->toDateString(),
                    'approved_by' => auth()->user()->name,
                ]),
            ]);

            // Generate repayment schedules
            $this->generateRepaymentSchedules($loan);

            // Record transaction for loan disbursement
            \App\Models\Transaction::create([
                'type' => 'loan_disbursement',
                'reference_id' => $referenceNumber,
                'amount' => $loan->principal,
                'description' => "Loan disbursement for loan #{$loan->id} - {$loan->customer->user->name}",
                'date' => now()->toDateString(),
                'user_id' => $loan->customer->user_id,
            ]);

            // Update capital pool - disburse loan amount
            $this->capitalPoolService->disburseLoan($loan->principal);

            // Log activity
            activity('loan')
                ->performedOn($loan)
                ->causedBy(auth()->user())
                ->log("Approved loan ID {$loan->id} with reference {$referenceNumber} for customer ID {$loan->customer_id} with principal " . format_currency($loan->principal));

            return redirect()->route('admin.loans.show', $loan)
                ->with('success', "Loan #{$loan->id} has been approved successfully! Reference: {$referenceNumber}");
        } catch (\Exception $e) {
            Log::error('Loan approval failed: ' . $e->getMessage(), [
                'loan_id' => $loan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to approve loan. Please try again or contact support.');
        }
    }

    /**
     * Generate unique loan reference number.
     * Format: customer_id/interest_with_zero/3-letter-month/day/2-digit-year
     * Example: 86/60/JAN/09/25
     *
     * @param \App\Models\Loan $loan
     * @return string
     */
    private function generateLoanReference($loan)
    {
        $customerId = $loan->customer_id;

        // Format interest rate with zero padding (e.g., 5% becomes 50, 15% becomes 150)
        $interestWithZero = str_pad((int)($loan->interest_rate * 10), 2, '0', STR_PAD_LEFT);

        // Get current date components
        $month = strtoupper(now()->format('M')); // JAN, FEB, MAR, etc.
        $day = now()->format('d'); // 01-31
        $year = now()->format('y'); // 25 for 2025

        // Build reference: customer_id/interest_with_zero/month/day/year
        $reference = "{$customerId}/{$interestWithZero}/{$month}/{$day}/{$year}";

        // Check for uniqueness - if reference exists, append a counter
        $counter = 1;
        $originalReference = $reference;

        while (\App\Models\Transaction::where('reference_id', $reference)->exists()) {
            $reference = "{$originalReference}-{$counter}";
            $counter++;
        }

        return $reference;
    }

    /**
     * Reject a loan application
     */
    public function reject(Request $request, Loan $loan)
    {
        // Check if user has permission
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if loan can be rejected
        if ($loan->status !== 'pending') {
            return back()->withErrors([
                'error' => 'Only pending loans can be rejected.'
            ]);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        DB::beginTransaction();

        try {
            $loan->update([
                'status' => 'defaulted', // Or create a 'rejected' status
                'notes' => json_encode(array_merge(
                    json_decode($loan->notes, true) ?? [],
                    [
                        'rejection_reason' => $validated['rejection_reason'],
                        'rejected_by' => auth()->user()->name,
                        'rejected_at' => now()->toDateString(),
                    ]
                )),
            ]);

            // Log activity
            activity('loan')
                ->performedOn($loan)
                ->causedBy(auth()->user())
                ->withProperties(['reason' => $validated['rejection_reason']])
                ->log('Loan rejected');

            DB::commit();

            return redirect()->route('admin.loans.index')
                ->with('success', "Loan #{$loan->id} has been rejected.");
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Failed to reject loan. Please try again.'
            ]);
        }
    }
}
