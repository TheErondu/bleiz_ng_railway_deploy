<?php

// app/Http/Controllers/Api/LoanController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends BaseApiController
{
    /**
     * Get user's loans.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('customer')) {
            return $this->sendError('Access denied. Customer role required.', [], 403);
        }

        $customer = $user->customer;
        if (!$customer) {
            return $this->sendError('Customer profile not found', [], 404);
        }

        $loans = $customer->loans()
            ->with(['schedules' => function ($query) {
                $query->orderBy('due_date');
            }])
            ->latest()
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'principal' => $loan->principal,
                    'interest_rate' => $loan->interest_rate,
                    'tenure_months' => $loan->tenure_months,
                    'start_date' => $loan->start_date->format('Y-m-d'),
                    'end_date' => $loan->end_date->format('Y-m-d'),
                    'status' => $loan->status,
                    'loan_balance' => $loan->loan_balance,
                    'total_obligation' => $loan->total_obligation,
                    'payment_progress' => $loan->getPaymentProgressAttribute(),
                    'remaining_balance' => $loan->getRemainingBalanceAttribute(),
                    'is_overdue' => $loan->isOverdue(),
                    'overdue_amount' => $loan->overdue_payment,
                    'next_payment_date' => $loan->schedules
                        ->where('status', 'pending')
                        ->first()?->due_date?->format('Y-m-d'),
                    'next_payment_amount' => $loan->schedules
                        ->where('status', 'pending')
                        ->first()?->amount_due ?? 0,
                ];
            });

        return $this->sendResponse($loans, 'Loans retrieved successfully');
    }

    /**
     * Get specific loan details.
     */
    public function show(Request $request, Loan $loan)
    {
        $user = $request->user();

        // Check if user owns this loan
        if ($loan->customer->user_id !== $user->id) {
            return $this->sendError('Access denied', [], 403);
        }

        $loan->load(['schedules', 'repayments' => function ($query) {
            $query->latest();
        }]);

        $loanData = [
            'id' => $loan->id,
            'principal' => $loan->principal,
            'interest_rate' => $loan->interest_rate,
            'tenure_months' => $loan->tenure_months,
            'start_date' => $loan->start_date->format('Y-m-d'),
            'end_date' => $loan->end_date->format('Y-m-d'),
            'repayment_cycle' => $loan->repayment_cycle,
            'status' => $loan->status,
            'loan_balance' => $loan->loan_balance,
            'total_obligation' => $loan->total_obligation,
            'repaid_principal' => $loan->repaid_principal,
            'overdue_payment' => $loan->overdue_payment,
            'payment_progress' => $loan->getPaymentProgressAttribute(),
            'remaining_balance' => $loan->getRemainingBalanceAttribute(),
            'monthly_payment' => $loan->getMonthlyPaymentAttribute(),
            'schedules' => $loan->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'due_date' => $schedule->due_date->format('Y-m-d'),
                    'amount_due' => $schedule->amount_due,
                    'amount_paid' => $schedule->amount_paid,
                    'status' => $schedule->status,
                    'paid_at' => $schedule->paid_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'recent_payments' => $loan->repayments->take(5)->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'paid_on' => $payment->paid_on,
                    'payment_method' => $payment->payment_method,
                ];
            }),
        ];

        return $this->sendResponse($loanData, 'Loan details retrieved successfully');
    }

    /**
     * Get loan repayment schedules.
     */
    public function getSchedules(Request $request, Loan $loan)
    {
        $user = $request->user();

        if ($loan->customer->user_id !== $user->id) {
            return $this->sendError('Access denied', [], 403);
        }

        $schedules = $loan->schedules()
            ->orderBy('due_date')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'due_date' => $schedule->due_date->format('Y-m-d'),
                    'amount_due' => $schedule->amount_due,
                    'amount_paid' => $schedule->amount_paid,
                    'remaining_due' => $schedule->amount_due - $schedule->amount_paid,
                    'status' => $schedule->status,
                    'is_overdue' => $schedule->status === 'overdue',
                    'paid_at' => $schedule->paid_at?->format('Y-m-d H:i:s'),
                ];
            });

        return $this->sendResponse($schedules, 'Repayment schedules retrieved successfully');
    }
}
