<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Services\CapitalPoolService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $loan->load('customer.user', 'schedules', 'repayments');
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
}
