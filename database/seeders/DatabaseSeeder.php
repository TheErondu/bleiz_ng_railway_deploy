<?php

namespace Database\Seeders;

use App\Models\CapitalPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Investor;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\RepaymentSchedule;
use App\Models\Transaction;
use DateTime;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables first
        User::truncate();
        Investor::truncate();
        Customer::truncate();
        Loan::truncate();
        RepaymentSchedule::truncate();
        Transaction::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('capital_pools')->truncate();
        DB::table('roles')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Create Capital Pool
        if (!CapitalPool::exists()) {
            CapitalPool::create([
                'total_amount' => 50000000,
                'available_amount' => 45000000,
            ]);
        }
        // Create roles
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $investorRole = \Spatie\Permission\Models\Role::create(['name' => 'investor']);
        $customerRole = \Spatie\Permission\Models\Role::create(['name' => 'customer']);

        // Create admin
        $admin = User::create([
            'name' => 'Bleiz Admin',
            'email' => 'admin@bleiz.ng',
            'password' => Hash::make('Test123#'),
        ]);
        $admin->assignRole($adminRole);

        // Create investors
        $investors = [
            ['name' => 'Investor A', 'email' => 'investorA@bleiz.com', 'capital' => 300000, 'roi_percentage' => 10],
            ['name' => 'Investor B', 'email' => 'investorB@bleiz.com', 'capital' => 500000, 'roi_percentage' => 8],
        ];

        foreach ($investors as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('Test123#'),
            ]);
            $user->assignRole($investorRole);

            Investor::create([
                'user_id' => $user->id,
                'capital' => $data['capital'],
                'amount_provided' => 300000,
                'date_contributed' => Carbon::now(),
                'roi_percentage' => $data['roi_percentage'],
                'roi_accrued' => 0,
                'roi_withdrawn' => 0,
            ]);
        }

        // Create customers
        $customers = [
            ['name' => 'Customer One', 'email' => 'customer1@bleiz.com'],
            ['name' => 'Customer Two', 'email' => 'customer2@bleiz.com'],
        ];

        foreach ($customers as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole($customerRole);

            Customer::create([
                'user_id' => $user->id,
                'customer_code' => '34/50/FB19/25-' . $user->id,
            ]);
        }

        // Create sample loans and repayment schedules
        $customer = Customer::first();
        $loan = Loan::create([
            'customer_id' => $customer->id,
            'principal' => 100000,
            'interest_rate' => 20,
            'tenure_months' => 3,
            'repayment_cycle' => 'monthly',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(12),
        ]);

        $scheduleAmount = 40000; // Equal monthly payment for simplicity
        for ($i = 1; $i <= 3; $i++) {
            RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'due_date' => Carbon::now()->addMonths($i),
                'amount_due' => $scheduleAmount,
                'amount_paid' => 0,
                'status' => 'pending',
            ]);
        }

        // Add sample repayment transaction
        Transaction::create([
            'type' => 'repayment',
            'reference_id' => Transaction::generateReferenceId(),
            'amount' => $scheduleAmount,
            'description' => 'Initial repayment',
            'date' => now(),
            'user_id' => $customer->id,
        ]);
    }
}
