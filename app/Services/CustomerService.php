<?php
// app/Services/CustomerService.php
namespace App\Services;

use App\Exceptions\FinancialException;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use App\Services\CapitalPoolService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerService
{
    protected $capitalPoolService;

    public function __construct(CapitalPoolService $capitalPoolService)
    {
        $this->capitalPoolService = $capitalPoolService;
    }

    /**
     * Create a customer profile for the given user.
     */
    public function createCustomerForUser(User $user, array $data = []): Customer
    {
        return DB::transaction(function () use ($user, $data) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'business_name' => $data['business_name'] ?? null,
                'address' => $data['address'] ?? $user->address,
                'phone_number' => $data['phone_number'] ?? $user->phone_number,
                'employer' => $data['employer'] ?? null,
                'employer_address' => $data['employer_address'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bvn' => $data['bvn'] ?? null,
            ]);

            activity()
                ->causedBy($user)
                ->performedOn($customer)
                ->log('Customer profile created');

            return $customer;
        });
    }

    /**
     * Update customer profile with comprehensive validation.
     */
    public function updateCustomerProfile(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            // Validate BVN if provided
            if (!empty($data['bvn']) && $data['bvn'] !== $customer->bvn) {
                $this->validateBVN($data['bvn']);
            }

            // Validate bank account if provided
            if (!empty($data['bank_account_number']) && !empty($data['bank_name'])) {
                $this->validateBankAccount($data['bank_account_number'], $data['bank_name'], $data['bvn'] ?? $customer->bvn);
            }

            $customer->update($data);

            activity()
                ->causedBy($customer->user)
                ->performedOn($customer)
                ->log('Customer profile updated');

            return $customer->fresh();
        });
    }

    /**
     * Check customer eligibility for loan.
     */
    public function checkLoanEligibility(Customer $customer, float $requestedAmount): array
    {
        $eligibilityData = [
            'eligible' => false,
            'max_loan_amount' => 0,
            'reasons' => [],
            'requirements' => [],
            'credit_score' => 0,
        ];

        // Check if customer profile is complete
        if (!$this->isProfileComplete($customer)) {
            $eligibilityData['reasons'][] = 'Customer profile incomplete';
            $eligibilityData['requirements'][] = 'Complete your profile information';
            return $eligibilityData;
        }

        // Check for existing active loans
        $activeLoans = $customer->loans()->where('status', 'ongoing')->count();
        if ($activeLoans >= 3) {
            $eligibilityData['reasons'][] = 'Maximum number of active loans reached';
            return $eligibilityData;
        }

        // Check for overdue payments
        $overdueLoans = $customer->loans()->where('overdue_payment', '>', 0)->count();
        if ($overdueLoans > 0) {
            $eligibilityData['reasons'][] = 'Has overdue payments on existing loans';
            $eligibilityData['requirements'][] = 'Clear all overdue payments';
            return $eligibilityData;
        }

        // Calculate credit score
        $creditScore = $this->calculateCreditScore($customer);
        $eligibilityData['credit_score'] = $creditScore;

        // Determine maximum loan amount based on credit history
        $maxLoanAmount = $this->calculateMaxLoanAmount($customer, $creditScore);
        $eligibilityData['max_loan_amount'] = $maxLoanAmount;

        // Check if requested amount is within limits
        if ($requestedAmount > $maxLoanAmount) {
            $eligibilityData['reasons'][] = 'Requested amount exceeds maximum eligible amount';
            $eligibilityData['requirements'][] = "Maximum eligible amount: " . format_currency($maxLoanAmount);
            return $eligibilityData;
        }

        // Check capital pool availability
        $pool = $this->capitalPoolService->getPool();
        if (!$pool || $pool->available_amount < $requestedAmount) {
            $eligibilityData['reasons'][] = 'Insufficient funds in capital pool';
            $eligibilityData['requirements'][] = 'Please try again later or request a smaller amount';
            return $eligibilityData;
        }

        $eligibilityData['eligible'] = true;
        return $eligibilityData;
    }

    /**
     * Apply for loan with automated approval process.
     */
    public function applyForLoan(Customer $customer, array $loanData): array
    {
        return DB::transaction(function () use ($customer, $loanData) {
            // Check eligibility
            $eligibility = $this->checkLoanEligibility($customer, $loanData['principal']);

            if (!$eligibility['eligible']) {
                throw new FinancialException('Loan application rejected: ' . implode(', ', $eligibility['reasons']));
            }

            // Create loan application
            $loan = Loan::create([
                'customer_id' => $customer->id,
                'principal' => $loanData['principal'],
                'interest_rate' => $this->calculateInterestRate($customer, $loanData['principal']),
                'tenure_months' => $loanData['tenure_months'],
                'start_date' => $loanData['start_date'] ?? now()->addDay(),
                'end_date' => now()->addMonths($loanData['tenure_months']),
                'repayment_cycle' => $loanData['repayment_cycle'] ?? 'monthly',
                'status' => 'pending', // Will be approved automatically if meets criteria
                'loan_balance' => $loanData['principal'],
                'repaid_principal' => 0,
                'overdue_payment' => 0,
                'total_obligation' => $this->calculateTotalObligation($loanData['principal'], $this->calculateInterestRate($customer, $loanData['principal']), $loanData['tenure_months']),
                'current_interest' => $this->calculateTotalInterest($loanData['principal'], $this->calculateInterestRate($customer, $loanData['principal']), $loanData['tenure_months']),
            ]);

            // Auto-approve if meets criteria
            $approvalResult = $this->processAutoApproval($loan);

            activity()
                ->causedBy($customer->user)
                ->performedOn($loan)
                ->log("Loan application submitted: " . format_currency($loanData['principal']));

            return [
                'loan' => $loan,
                'approval_status' => $approvalResult['status'],
                'message' => $approvalResult['message'],
                'next_steps' => $approvalResult['next_steps'],
            ];
        });
    }

    /**
     * Get customer loan history with performance metrics.
     */
    public function getLoanHistory(Customer $customer): array
    {
        $loans = $customer->loans()
            ->with(['schedules', 'repayments'])
            ->latest()
            ->get();

        $metrics = [
            'total_loans' => $loans->count(),
            'active_loans' => $loans->where('status', 'ongoing')->count(),
            'completed_loans' => $loans->where('status', 'completed')->count(),
            'total_borrowed' => $loans->sum('principal'),
            'total_repaid' => $loans->sum('repaid_principal'),
            'current_outstanding' => $loans->where('status', 'ongoing')->sum('loan_balance'),
            'payment_history_score' => $this->calculatePaymentHistoryScore($loans),
        ];

        return [
            'loans' => $loans,
            'metrics' => $metrics,
            'credit_score' => $this->calculateCreditScore($customer),
            'next_payment' => $this->getNextPaymentDue($customer),
        ];
    }

    /**
     * Calculate recommended loan amount based on customer profile.
     */
    public function getRecommendedLoanAmount(Customer $customer): array
    {
        $creditScore = $this->calculateCreditScore($customer);
        $maxAmount = $this->calculateMaxLoanAmount($customer, $creditScore);

        // Recommend 70% of maximum for conservative lending
        $recommendedAmount = $maxAmount * 0.7;

        return [
            'recommended_amount' => $recommendedAmount,
            'max_amount' => $maxAmount,
            'interest_rate' => $this->calculateInterestRate($customer, $recommendedAmount),
            'suggested_tenure' => $this->getSuggestedTenure($recommendedAmount),
            'monthly_payment' => $this->calculateMonthlyPayment($recommendedAmount, $this->calculateInterestRate($customer, $recommendedAmount), $this->getSuggestedTenure($recommendedAmount)),
        ];
    }

    // Private helper methods
    private function validateBVN(string $bvn): bool
    {
        // Implement BVN validation logic
        // This could integrate with third-party BVN verification services
        if (strlen($bvn) !== 11 || !is_numeric($bvn)) {
            throw new FinancialException('Invalid BVN format');
        }

        // Mock validation - in production, integrate with actual BVN verification API
        Log::info("BVN validation attempted: {$bvn}");
        return true;
    }

    private function validateBankAccount(string $accountNumber, string $bankName, string $bvn): bool
    {
        // Implement bank account validation
        // This could integrate with Paystack, Flutterwave, or other Nigerian payment providers
        try {
            // Mock validation - replace with actual API call
            Log::info("Bank account validation: {$accountNumber} at {$bankName}");
            return true;
        } catch (\Exception $e) {
            throw new FinancialException('Bank account validation failed: ' . $e->getMessage());
        }
    }

    private function isProfileComplete(Customer $customer): bool
    {
        $requiredFields = [
            'address', 'phone_number', 'employer', 'bank_name',
            'bank_account_name', 'bank_account_number', 'bvn'
        ];

        foreach ($requiredFields as $field) {
            if (empty($customer->$field)) {
                return false;
            }
        }

        return true;
    }

    private function calculateCreditScore(Customer $customer): int
    {
        $score = 300; // Base score

        // Add points for completed loans
        $completedLoans = $customer->loans()->where('status', 'completed')->count();
        $score += $completedLoans * 50;

        // Add points for timely payments
        $timelyPayments = $customer->loans()
            ->whereHas('repayments', function ($query) {
                $query->whereRaw('paid_on <= (SELECT due_date FROM repayment_schedules WHERE repayment_schedules.id = repayments.repayment_schedule_id)');
            })
            ->count();
        $score += $timelyPayments * 30;

        // Deduct points for overdue payments
        $overduePayments = $customer->loans()->where('overdue_payment', '>', 0)->count();
        $score -= $overduePayments * 100;

        // Deduct points for defaults
        $defaultedLoans = $customer->loans()->where('status', 'defaulted')->count();
        $score -= $defaultedLoans * 200;

        // Cap score between 300-850 (FICO-like scale)
        return max(300, min(850, $score));
    }

    private function calculateMaxLoanAmount(Customer $customer, int $creditScore): float
    {
        $baseAmount = 50000; // Base loan amount

        // Increase based on credit score
        if ($creditScore >= 750) {
            $multiplier = 10;
        } elseif ($creditScore >= 650) {
            $multiplier = 5;
        } elseif ($creditScore >= 550) {
            $multiplier = 3;
        } else {
            $multiplier = 1;
        }

        $maxAmount = $baseAmount * $multiplier;

        // Increase based on successful loan history
        $completedLoans = $customer->loans()->where('status', 'completed')->count();
        $maxAmount += $completedLoans * 25000;

        return min($maxAmount, 5000000); // Cap at 5M
    }

    private function calculateInterestRate(Customer $customer, float $principal): float
    {
        $baseRate = 20; // Base annual interest rate

        $creditScore = $this->calculateCreditScore($customer);

        // Adjust rate based on credit score
        if ($creditScore >= 750) {
            $rate = $baseRate - 5; // 15%
        } elseif ($creditScore >= 650) {
            $rate = $baseRate - 3; // 17%
        } elseif ($creditScore >= 550) {
            $rate = $baseRate - 1; // 19%
        } else {
            $rate = $baseRate + 5; // 25%
        }

        // Adjust based on loan amount (larger loans get better rates)
        if ($principal >= 1000000) {
            $rate -= 2;
        } elseif ($principal >= 500000) {
            $rate -= 1;
        }

        // Adjust based on customer history
        $completedLoans = $customer->loans()->where('status', 'completed')->count();
        if ($completedLoans >= 3) {
            $rate -= 2; // Loyalty discount
        }

        return max(10, min(30, $rate)); // Cap between 10-30%
    }

    private function calculateTotalObligation(float $principal, float $interestRate, int $tenureMonths): float
    {
        $monthlyRate = $interestRate / 100 / 12;
        $totalInterest = $principal * $monthlyRate * $tenureMonths;
        return $principal + $totalInterest;
    }

    private function calculateTotalInterest(float $principal, float $interestRate, int $tenureMonths): float
    {
        $monthlyRate = $interestRate / 100 / 12;
        return $principal * $monthlyRate * $tenureMonths;
    }

    private function processAutoApproval(Loan $loan): array
    {
        $customer = $loan->customer;
        $creditScore = $this->calculateCreditScore($customer);

        // Auto-approve if credit score is good and amount is reasonable
        if ($creditScore >= 650 && $loan->principal <= 500000) {
            $loan->update(['status' => 'ongoing']);

            // Generate repayment schedule
            $this->generateRepaymentSchedule($loan);

            // Disburse funds from capital pool
            $this->capitalPoolService->disburseLoan($loan->principal);

            return [
                'status' => 'approved',
                'message' => 'Your loan has been automatically approved and will be disbursed within 24 hours.',
                'next_steps' => ['Wait for disbursement notification', 'Review your repayment schedule'],
            ];
        } else {
            return [
                'status' => 'pending',
                'message' => 'Your loan application is under review. You will be notified within 2-3 business days.',
                'next_steps' => ['Wait for manual review', 'Ensure all documents are complete'],
            ];
        }
    }

    private function generateRepaymentSchedule(Loan $loan): void
    {
        $monthlyPayment = $loan->total_obligation / $loan->tenure_months;
        $currentDate = $loan->start_date;

        for ($i = 1; $i <= $loan->tenure_months; $i++) {
            $dueDate = $currentDate->copy()->addMonths($i);

            $loan->schedules()->create([
                'due_date' => $dueDate,
                'amount_due' => $monthlyPayment,
                'amount_paid' => 0,
                'status' => 'pending',
            ]);
        }
    }

    private function calculatePaymentHistoryScore(Collection $loans): float
    {
        if ($loans->isEmpty()) return 0;

        $totalPayments = 0;
        $timelyPayments = 0;

        foreach ($loans as $loan) {
            foreach ($loan->repayments as $repayment) {
                $totalPayments++;
                $schedule = $repayment->schedule;

                if ($repayment->paid_on <= $schedule->due_date) {
                    $timelyPayments++;
                }
            }
        }

        return $totalPayments > 0 ? ($timelyPayments / $totalPayments) * 100 : 0;
    }

    private function getNextPaymentDue(Customer $customer): ?array
    {
        $nextSchedule = $customer->loans()
            ->where('status', 'ongoing')
            ->with('schedules')
            ->get()
            ->flatMap(fn($loan) => $loan->schedules)
            ->where('status', 'pending')
            ->sortBy('due_date')
            ->first();

        if (!$nextSchedule) return null;

        return [
            'loan_id' => $nextSchedule->loan_id,
            'due_date' => $nextSchedule->due_date,
            'amount' => $nextSchedule->amount_due - $nextSchedule->amount_paid,
            'days_until_due' => now()->diffInDays($nextSchedule->due_date, false),
            'is_overdue' => $nextSchedule->status === 'overdue',
        ];
    }

    private function getSuggestedTenure(float $amount): int
    {
        if ($amount >= 1000000) return 12;      // 1 year for large loans
        if ($amount >= 500000) return 9;       // 9 months for medium loans
        if ($amount >= 100000) return 6;       // 6 months for small loans
        return 3;                              // 3 months for micro loans
    }

    private function calculateMonthlyPayment(float $principal, float $annualRate, int $months): float
    {
        $monthlyRate = $annualRate / 100 / 12;
        $totalInterest = $principal * $monthlyRate * $months;
        return ($principal + $totalInterest) / $months;
    }

    /**
     * Get customer dashboard data.
     */
    public function getDashboardData(Customer $customer): array
    {
        $activeLoans = $customer->loans()->where('status', 'ongoing')->get();
        $nextPayment = $this->getNextPaymentDue($customer);
        $creditScore = $this->calculateCreditScore($customer);
        $recommendedLoan = $this->getRecommendedLoanAmount($customer);

        return [
            'customer' => $customer,
            'active_loans_count' => $activeLoans->count(),
            'total_outstanding' => $activeLoans->sum('loan_balance'),
            'next_payment' => $nextPayment,
            'credit_score' => $creditScore,
            'recommended_loan' => $recommendedLoan,
            'recent_transactions' => $this->getRecentTransactions($customer, 5),
            'loan_performance' => $this->getLoanPerformanceMetrics($customer),
        ];
    }

    private function getRecentTransactions(Customer $customer, int $limit): Collection
    {
        return $customer->loans()
            ->with('repayments')
            ->get()
            ->flatMap(fn($loan) => $loan->repayments->map(function ($repayment) use ($loan) {
                return [
                    'id' => $repayment->id,
                    'type' => 'repayment',
                    'amount' => $repayment->amount,
                    'date' => $repayment->paid_on,
                    'loan_id' => $loan->id,
                    'description' => "Loan repayment for loan #{$loan->id}",
                ];
            }))
            ->sortByDesc('date')
            ->take($limit);
    }

    private function getLoanPerformanceMetrics(Customer $customer): array
    {
        $loans = $customer->loans;

        return [
            'total_loans' => $loans->count(),
            'completed_loans' => $loans->where('status', 'completed')->count(),
            'active_loans' => $loans->where('status', 'ongoing')->count(),
            'defaulted_loans' => $loans->where('status', 'defaulted')->count(),
            'total_borrowed' => $loans->sum('principal'),
            'total_repaid' => $loans->sum('repaid_principal'),
            'payment_history_score' => $this->calculatePaymentHistoryScore($loans),
        ];
    }

    /**
     * Simulate payment to check loan status changes.
     */
    public function simulatePayment(Loan $loan, float $amount): array
    {
        $nextSchedule = $loan->schedules()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->first();

        if (!$nextSchedule) {
            return [
                'success' => false,
                'message' => 'No pending payments found for this loan.',
            ];
        }

        $remainingDue = $nextSchedule->amount_due - $nextSchedule->amount_paid;

        $simulation = [
            'success' => true,
            'payment_amount' => $amount,
            'remaining_due_before' => $remainingDue,
            'remaining_due_after' => max(0, $remainingDue - $amount),
            'schedule_will_be_paid' => $amount >= $remainingDue,
            'loan_balance_after' => max(0, $loan->loan_balance - $amount),
            'total_remaining_obligation' => max(0, $loan->getRemainingBalanceAttribute() - $amount),
        ];

        // Check if loan will be completed
        if ($simulation['total_remaining_obligation'] <= 0) {
            $simulation['loan_will_be_completed'] = true;
            $simulation['message'] = 'This payment will complete your loan!';
        } else {
            $simulation['loan_will_be_completed'] = false;
            $nextDue = $loan->schedules()
                ->where('status', 'pending')
                ->where('id', '!=', $nextSchedule->id)
                ->orderBy('due_date')
                ->first();

            if ($nextDue) {
                $simulation['next_payment_due'] = $nextDue->due_date;
                $simulation['next_payment_amount'] = $nextDue->amount_due;
            }
        }

        return $simulation;
    }

    /**
     * Generate loan amortization schedule for customer review.
     */
    public function generateAmortizationSchedule(float $principal, float $interestRate, int $tenureMonths, string $startDate): array
    {
        $monthlyRate = $interestRate / 100 / 12;
        $monthlyInterest = $principal * $monthlyRate;
        $monthlyPrincipal = $principal / $tenureMonths;
        $monthlyPayment = $monthlyPrincipal + $monthlyInterest;

        $schedule = [];
        $remainingBalance = $principal;
        $currentDate = \Carbon\Carbon::parse($startDate);

        for ($month = 1; $month <= $tenureMonths; $month++) {
            $dueDate = $currentDate->copy()->addMonths($month);

            $schedule[] = [
                'month' => $month,
                'due_date' => $dueDate->format('Y-m-d'),
                'payment_amount' => $monthlyPayment,
                'principal_portion' => $monthlyPrincipal,
                'interest_portion' => $monthlyInterest,
                'remaining_balance' => max(0, $remainingBalance - $monthlyPrincipal),
            ];

            $remainingBalance -= $monthlyPrincipal;
        }

        return [
            'loan_details' => [
                'principal' => $principal,
                'interest_rate' => $interestRate,
                'tenure_months' => $tenureMonths,
                'monthly_payment' => $monthlyPayment,
                'total_interest' => $monthlyInterest * $tenureMonths,
                'total_amount' => $principal + ($monthlyInterest * $tenureMonths),
            ],
            'schedule' => $schedule,
        ];
    }
}
