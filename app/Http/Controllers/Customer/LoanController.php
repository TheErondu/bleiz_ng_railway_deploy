<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $loans = collect(); // Initialize as empty collection

        if ($user->customer) {
            $loans = $user->customer->loans()
                ->with(['repaymentSchedules' => function($query) {
                    $query->orderBy('due_date', 'asc');
                }, 'repayments'])
                ->latest()
                ->get();
        }

        return view('customer.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validation rules
        $rules = [
            'principal' => 'required|numeric|min:50000|max:5000000',
            'tenure_months' => 'required|integer|in:3,6,9,12,18,24',
            'purpose' => 'required|string',
            'monthly_income' => 'required|numeric|min:50000',
            'agree_terms' => 'required|accepted',
        ];

        // If user doesn't have a customer profile, require profile fields
        if (!$user->customer) {
            $profileRules = [
                'address' => 'required|string|min:10|max:255',
                'phone' => 'required|string|min:10|max:20',
                'employer' => 'required|string|max:255',
                'employer_address' => 'required|string|max:255',
                'employment_id' => 'required|string|max:100',
                'bank_name' => 'required|string|max:100',
                'account_name' => 'required|string|max:100',
                'account_number' => 'required|string|size:10',
                'bvn' => 'nullable|string|size:11',
            ];
            $rules = array_merge($rules, $profileRules);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            // Create customer profile if it doesn't exist
            if (!$user->customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'address' => $validated['address'],
                    'phone_number' => $validated['phone'],
                    'employer' => $validated['employer'],
                    'employer_address' => $validated['employer_address'],
                    'employee_id' => $validated['employment_id'],
                    'bank_name' => $validated['bank_name'],
                    'bank_account_name' => $validated['account_name'],
                    'bank_account_number' => $validated['account_number'],
                    'bvn' => $validated['bvn'] ?? null,
                ]);
            } else {
                $customer = $user->customer;
            }

            $interestRate = 5.0; // Fixed interest rate for all loans

            $tenureMonths = (int) $validated['tenure_months'];

            // Calculate dates
            $startDate = now();
            $endDate = $startDate->copy()->addMonths($tenureMonths);

            // Create loan application
            $loan = Loan::create([
                'customer_id' => $customer->id,
                'principal' => $validated['principal'],
                'interest_rate' => $interestRate,
                'tenure_months' => $validated['tenure_months'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending',
                'total_obligation' => $this->calculateTotalObligation(
                    $validated['principal'],
                    $interestRate,
                    $validated['tenure_months']
                ),
                'current_interest' => $this->calculateTotalInterest(
                    $validated['principal'],
                    $interestRate,
                    $validated['tenure_months']
                ),
                'loan_balance' => $validated['principal'],
                'notes' => json_encode([
                    'purpose' => $validated['purpose'],
                    'monthly_income' => $validated['monthly_income'],
                    'application_date' => now()->toDateString(),
                ]),
            ]);

            DB::commit();

            return redirect()->route('customer.dashboard')
                ->with('success', 'Your loan application has been submitted successfully! We will review your application and get back to you within 24 hours.');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'There was an error processing your application. Please try again.'
            ])->withInput();
        }
    }

    public function show(Loan $loan)
    {
        // Ensure the loan belongs to the authenticated customer
        if ($loan->customer->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load(['repaymentSchedules', 'repayments.receivedBy']);

        return view('customer.loans.show', compact('loan'));
    }

    private function calculateTotalObligation($principal, $interestRate, $tenureMonths)
    {
        $totalInterest = ($principal * $interestRate / 100) * ($tenureMonths / 12);
        return $principal + $totalInterest;
    }

    private function calculateTotalInterest($principal, $interestRate, $tenureMonths)
    {
        return ($principal * $interestRate / 100) * ($tenureMonths / 12);
    }

}
