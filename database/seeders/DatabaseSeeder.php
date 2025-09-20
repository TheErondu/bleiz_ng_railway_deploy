<?php

namespace Database\Seeders;

use App\Models\CapitalPool;
use App\Models\Customer;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\RepaymentSchedule;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Skip seeding if users already exist (production safety check)
        if (User::count() > 0) {
            $this->command->info('Users already exist. Skipping seeder to prevent data conflicts.');
            $this->command->info('Use --force or clear users table if you want to re-seed.');
            return;
        }

        $this->command->info('🌱 Starting database seeding...');

        DB::transaction(function () {
            $this->seedRoles();
            $this->seedCapitalPool();
            $this->seedUsers();
            $this->seedLoansAndRepayments();
            $this->seedWithdrawals();
            $this->updateCalculatedFields();
        });

        $this->command->info('✅ Database seeding completed successfully!');
    }

    private function seedRoles(): void
    {
        $this->command->info('👥 Creating roles...');

        $roles = ['admin', 'investor', 'customer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    private function seedCapitalPool(): void
    {
        $this->command->info('💰 Creating capital pool...');

        CapitalPool::firstOrCreate([], [
            'total_amount' => 15000000.00, // 15M total capital
            'available_amount' => 8500000.00, // 8.5M available (realistic utilization)
        ]);
    }

    private function seedUsers(): void
    {
        $this->command->info('👤 Creating users...');

        // Admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@bleiz.ng',
            'password' => Hash::make('Admin@123'),
            'phone_number' => '+234801234567',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create realistic investors with varying profiles
        $investors = [
            [
                'name' => 'Adebayo Investment Ltd',
                'email' => 'adebayo@investors.ng',
                'phone' => '+234802345678',
                'capital' => 5000000.00,
                'roi_percentage' => 12.00,
                'months_active' => 18,
            ],
            [
                'name' => 'Lagos Angels Syndicate',
                'email' => 'info@lagosangels.com',
                'phone' => '+234803456789',
                'capital' => 3500000.00,
                'roi_percentage' => 10.00,
                'months_active' => 24,
            ],
            [
                'name' => 'Kemi Okafor',
                'email' => 'kemi.okafor@gmail.com',
                'phone' => '+234804567890',
                'capital' => 2000000.00,
                'roi_percentage' => 15.00,
                'months_active' => 6,
            ],
            [
                'name' => 'Port Harcourt Capital',
                'email' => 'contact@phcapital.ng',
                'phone' => '+234805678901',
                'capital' => 4500000.00,
                'roi_percentage' => 8.00,
                'months_active' => 30,
            ],
        ];

        foreach ($investors as $investorData) {
            $user = User::create([
                'name' => $investorData['name'],
                'email' => $investorData['email'],
                'password' => Hash::make('Investor@123'),
                'phone_number' => $investorData['phone'],
                'email_verified_at' => now(),
            ]);
            $user->assignRole('investor');

            // Create investor profile with realistic ROI accrual
            $monthsActive = $investorData['months_active'];
            $monthlyROI = ($investorData['capital'] * $investorData['roi_percentage'] / 100) / 12;
            $totalROIAccrued = $monthlyROI * $monthsActive;

            Investor::create([
                'user_id' => $user->id,
                'amount_provided' => $investorData['capital'],
                'date_contributed' => Carbon::now()->subMonths($monthsActive),
                'status' => 'active',
                'capital' => $investorData['capital'],
                'roi_percentage' => $investorData['roi_percentage'],
                'roi_accrued' => $totalROIAccrued,
                'roi_withdrawn' => 0,
            ]);
        }

        // Create diverse customer profiles
        $customers = [
            [
                'name' => 'Chioma Okwu',
                'email' => 'chioma.okwu@gmail.com',
                'phone' => '+234806789012',
                'address' => '15 Admiralty Way, Lekki Phase 1, Lagos',
                'employer' => 'MTN Nigeria Communications Plc',
                'employer_address' => '30 Oregun Road, Ikeja, Lagos',
                'employee_id' => 'MTN/LA/2019/0234',
                'bank_name' => 'Access Bank',
                'account_name' => 'Chioma Okwu',
                'account_number' => '0987654321',
                'bvn' => '22334455667',
                'loan_history' => 'good',
            ],
            [
                'name' => 'Ibrahim Musa',
                'email' => 'ibrahim.musa@yahoo.com',
                'phone' => '+234807890123',
                'address' => '42 Yakubu Gowon Crescent, Asokoro, Abuja',
                'employer' => 'Federal Ministry of Finance',
                'employer_address' => 'Federal Secretariat Complex, Abuja',
                'employee_id' => 'FMF/2020/ABJ/789',
                'bank_name' => 'First Bank of Nigeria',
                'account_name' => 'Ibrahim Musa',
                'account_number' => '2011223344',
                'bvn' => '33445566778',
                'loan_history' => 'excellent',
            ],
            [
                'name' => 'Blessing Eze',
                'email' => 'blessing.eze@outlook.com',
                'phone' => '+234808901234',
                'address' => '7 New Haven Road, Enugu',
                'employer' => 'Zenith Bank Plc',
                'employer_address' => '84 Ajose Adeogun Street, Victoria Island, Lagos',
                'employee_id' => 'ZEN/EN/2018/567',
                'bank_name' => 'Zenith Bank',
                'account_name' => 'Blessing Eze',
                'account_number' => '3344556677',
                'bvn' => '44556677889',
                'loan_history' => 'good',
            ],
            [
                'name' => 'Yusuf Abdullahi',
                'email' => 'yusuf.abdullahi@gmail.com',
                'phone' => '+234809012345',
                'address' => '23 Ahmadu Bello Way, Kaduna',
                'employer' => 'Nigerian National Petroleum Corporation',
                'employer_address' => 'NNPC Towers, Central Business District, Abuja',
                'employee_id' => 'NNPC/2021/KD/123',
                'bank_name' => 'UBA',
                'account_name' => 'Yusuf Abdullahi',
                'account_number' => '4455667788',
                'bvn' => '55667788990',
                'loan_history' => 'new',
            ],
            [
                'name' => 'Grace Okonkwo',
                'email' => 'grace.okonkwo@hotmail.com',
                'phone' => '+234800123456',
                'address' => '12 Aba Road, Port Harcourt',
                'employer' => 'Shell Petroleum Development Company',
                'employer_address' => '21 Old Port Harcourt Road, Port Harcourt',
                'employee_id' => 'SPDC/PH/2017/890',
                'bank_name' => 'GTBank',
                'account_name' => 'Grace Okonkwo',
                'account_number' => '5566778899',
                'bvn' => '66778899001',
                'loan_history' => 'fair',
            ],
        ];

        foreach ($customers as $customerData) {
            $user = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => Hash::make('Customer@123'),
                'phone_number' => $customerData['phone'],
                'email_verified_at' => now(),
            ]);
            $user->assignRole('customer');

            Customer::create([
                'user_id' => $user->id,
                'address' => $customerData['address'],
                'phone_number' => $customerData['phone'],
                'employer' => $customerData['employer'],
                'employer_address' => $customerData['employer_address'],
                'employee_id' => $customerData['employee_id'],
                'bank_name' => $customerData['bank_name'],
                'bank_account_name' => $customerData['account_name'],
                'bank_account_number' => $customerData['account_number'],
                'bvn' => $customerData['bvn'],
            ]);
        }
    }

    private function seedLoansAndRepayments(): void
    {
        $this->command->info('💳 Creating loans and repayment schedules...');

        $customers = Customer::all();

        foreach ($customers as $index => $customer) {
            $loanCount = match($index) {
                0 => 2, // Chioma - 2 loans (1 completed, 1 ongoing)
                1 => 3, // Ibrahim - 3 loans (2 completed, 1 ongoing)
                2 => 1, // Blessing - 1 loan (ongoing)
                3 => 1, // Yusuf - 1 loan (new customer, ongoing)
                4 => 2, // Grace - 2 loans (1 completed with late payments, 1 ongoing)
            };

            $this->createLoansForCustomer($customer, $loanCount, $index);
        }
    }

    private function createLoansForCustomer(Customer $customer, int $loanCount, int $customerIndex): void
    {
        for ($i = 0; $i < $loanCount; $i++) {
            $isFirstLoan = $i === 0;
            $isCompleted = $i < $loanCount - 1; // All but last loan are completed

            // Loan amounts based on customer history
            $loanAmount = match($customerIndex) {
                0 => $isFirstLoan ? 150000 : 300000, // Chioma - growing amounts
                1 => $isFirstLoan ? 200000 : ($i === 1 ? 400000 : 600000), // Ibrahim - progressive
                2 => 250000, // Blessing - single loan
                3 => 100000, // Yusuf - small first loan
                4 => $isFirstLoan ? 180000 : 350000, // Grace
            };

            $interestRate = $this->calculateInterestRate($customer, $loanAmount, $customerIndex, $i);
            $tenureMonths = rand(3, 12);

            $startDate = $isFirstLoan
                ? Carbon::now()->subMonths(rand(6, 18))
                : Carbon::now()->subMonths(rand(2, 6));

            $endDate = $startDate->copy()->addMonths($tenureMonths);

            // Calculate loan financials
            $monthlyInterestRate = $interestRate / 100 / 12;
            $totalInterest = $loanAmount * $monthlyInterestRate * $tenureMonths;
            $totalObligation = $loanAmount + $totalInterest;
            $monthlyPayment = $totalObligation / $tenureMonths;

            $loan = Loan::create([
                'customer_id' => $customer->id,
                'principal' => $loanAmount,
                'interest_rate' => $interestRate,
                'tenure_months' => $tenureMonths,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'repayment_cycle' => 'monthly',
                'status' => $isCompleted ? 'completed' : 'ongoing',
                'loan_balance' => $isCompleted ? 0 : ($loanAmount - rand(0, $loanAmount * 0.6)),
                'repaid_principal' => $isCompleted ? $loanAmount : rand($loanAmount * 0.2, $loanAmount * 0.8),
                'overdue_payment' => $isCompleted ? 0 : ($customerIndex === 4 && !$isFirstLoan ? rand(10000, 50000) : 0),
                'total_obligation' => $totalObligation,
                'current_interest' => $totalInterest,
            ]);

            $this->createRepaymentSchedule($loan, $monthlyPayment, $isCompleted, $customerIndex);
        }
    }

    private function calculateInterestRate(Customer $customer, float $amount, int $customerIndex, int $loanNumber): float
    {
        $baseRate = 20;

        // Rate based on customer history
        $historyAdjustment = match($customerIndex) {
            0, 1 => -3, // Good customers get better rates
            2 => -1, // Fair customer
            3 => 2, // New customer pays higher
            4 => 1, // Customer with issues
        };

        // Rate decreases with loan number (loyalty)
        $loyaltyDiscount = $loanNumber * -1;

        // Amount-based discount
        $amountDiscount = $amount >= 500000 ? -2 : ($amount >= 200000 ? -1 : 0);

        return max(10, min(30, $baseRate + $historyAdjustment + $loyaltyDiscount + $amountDiscount));
    }

    private function createRepaymentSchedule(Loan $loan, float $monthlyPayment, bool $isCompleted, int $customerIndex): void
    {
        for ($month = 1; $month <= $loan->tenure_months; $month++) {
            $dueDate = $loan->start_date->copy()->addMonths($month);

            $status = 'pending';
            $amountPaid = 0;
            $paidAt = null;

            if ($isCompleted) {
                // Completed loan - all payments made
                $status = 'paid';
                $amountPaid = $monthlyPayment;
                $paidAt = $dueDate->copy()->addDays(rand(-5, 10)); // Some paid early, some late
            } else {
                // Ongoing loan - some payments made
                if ($dueDate->isPast()) {
                    if ($customerIndex === 4 && rand(1, 100) <= 20) {
                        // Grace has 20% chance of overdue payments
                        $status = 'overdue';
                        $amountPaid = rand(0, $monthlyPayment * 0.5);
                    } else {
                        $status = 'paid';
                        $amountPaid = $monthlyPayment;
                        $paidAt = $dueDate->copy()->addDays(rand(-3, 5));
                    }
                }
            }

            $schedule = RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'due_date' => $dueDate,
                'amount_due' => $monthlyPayment,
                'amount_paid' => $amountPaid,
                'status' => $status,
                'paid_at' => $paidAt,
            ]);

            // Create repayment records for paid schedules
            if ($amountPaid > 0) {
                Repayment::create([
                    'repayment_schedule_id' => $schedule->id,
                    'loan_id' => $loan->id,
                    'amount' => $amountPaid,
                    'paid_on' => $paidAt ?? $dueDate,
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'received_by' => User::role('admin')->first()->id,
                ]);

                // Create transaction record
                Transaction::create([
                    'type' => 'repayment',
                    'reference_id' => Transaction::generateReferenceId('RPY'),
                    'amount' => $amountPaid,
                    'description' => "Loan repayment for loan #{$loan->id}",
                    'date' => $paidAt ?? $dueDate,
                    'user_id' => $loan->customer->user_id,
                ]);
            }
        }
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = ['transfer', 'pos', 'cash'];
        $weights = [60, 30, 10]; // Transfer most common, cash least

        $rand = rand(1, 100);
        if ($rand <= $weights[0]) return $methods[0];
        if ($rand <= $weights[0] + $weights[1]) return $methods[1];
        return $methods[2];
    }

    private function seedWithdrawals(): void
    {
        $this->command->info('💸 Creating withdrawal records...');

        $investors = Investor::all();

        foreach ($investors as $index => $investor) {
            // Create some withdrawal history
            $withdrawalCount = rand(1, 4);

            for ($i = 0; $i < $withdrawalCount; $i++) {
                $withdrawalAmount = rand(50000, min(500000, $investor->roi_accrued * 0.8));

                Withdrawal::create([
                    'investor_id' => $investor->user_id,
                    'amount' => $withdrawalAmount,
                    'status' => 'approved',
                    'note' => 'Regular ROI withdrawal',
                    'approved_at' => Carbon::now()->subDays(rand(30, 365)),
                ]);

                // Create transaction record
                Transaction::create([
                    'type' => 'expense',
                    'reference_id' => Transaction::generateReferenceId('WTH'),
                    'amount' => $withdrawalAmount,
                    'description' => 'Investor withdrawal',
                    'date' => Carbon::now()->subDays(rand(30, 365)),
                    'user_id' => $investor->user_id,
                ]);
            }
        }
    }

    private function updateCalculatedFields(): void
    {
        $this->command->info('🔄 Updating calculated fields...');

        // Update investor ROI withdrawn amounts
        $investors = Investor::with('withdrawals')->get();
        foreach ($investors as $investor) {
            $totalWithdrawn = $investor->withdrawals->sum('amount');
            $investor->update(['roi_withdrawn' => $totalWithdrawn]);
        }

        // Update capital pool based on actual loan deployments
        $totalDeployed = Loan::where('status', 'ongoing')->sum('loan_balance');
        $pool = CapitalPool::first();
        if ($pool) {
            $pool->update([
                'available_amount' => $pool->total_amount - $totalDeployed
            ]);
        }
    }
}
