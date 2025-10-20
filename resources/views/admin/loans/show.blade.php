@extends('layouts.admin')

@section('content')
    <div class="p-6 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Loan Details</h1>
            <a href="{{ route('admin.loans.index') }}"
                class="text-blue-600 dark:text-blue-400 hover:underline">Back to Loans</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Loan Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="text-gray-900 dark:text-white">{{ $loan->customer->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Principal</p>
                    <p class="text-gray-900 dark:text-white">{{ format_currency($loan->principal) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Interest Rate</p>
                    <p class="text-gray-900 dark:text-white">{{ number_format($loan->interest_rate, 2) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loan Balance</p>
                    <p class="text-gray-900 dark:text-white">{{ format_currency($loan->loan_balance) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current Interest</p>
                    <p class="text-gray-900 dark:text-white">{{ format_currency($loan->current_interest) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Obligation</p>
                    <p class="text-gray-900 dark:text-white">{{ format_currency($loan->total_obligation) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue Payment</p>
                    <p class="text-gray-900 dark:text-white">{{ format_currency($loan->overdue_payment) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="text-gray-900 dark:text-white">{{ ucfirst($loan->status) }}</p>
                </div>
                 <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Repayment Cycle</p>
                    <p class="text-gray-900 dark:text-white">{{ ucfirst($loan->repayment_cycle) }}</p>
                </div>
                 <div>
                    <p style="padding-bottom: 10px;" class="text-sm text-gray-500 dark:text-gray-400">Loan Reference</p>
                  <p class="text-gray-900 dark:text-white">{{ $loan->reference }} <span>@include('components.copy-button', ['text' =>  $loan->reference, 'size' => 'sm'])</span></p>

                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Start Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $loan->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">End Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $loan->end_date->format('M d, Y') }}</p>
                </div>

            </div>

            <!-- Repayment Schedules -->
            <h2 class="text-lg font-semibold mt-6 mb-4 text-gray-900 dark:text-white">Repayment Schedules</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Due Date</th>
                            <th scope="col" class="px-6 py-3">Amount Due</th>
                            <th scope="col" class="px-6 py-3">Amount Paid</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loan->repaymentSchedules as $schedule)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $schedule->due_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">{{ format_currency($schedule->amount_due) }}</td>
                                <td class="px-6 py-4">{{ format_currency($schedule->amount_paid) }}</td>
                                <td class="px-6 py-4">{{ ucfirst($schedule->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No schedules found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Repayments -->
            <h2 class="text-lg font-semibold mt-6 mb-4 text-gray-900 dark:text-white">Repayments</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Payment Date</th>
                            <th scope="col" class="px-6 py-3">Amount</th>
                            <th scope="col" class="px-6 py-3">Schedule ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loan->repayments as $repayment)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $repayment->paid_on->format('M d, Y') }}</td>
                                <td class="px-6 py-4">{{ format_currency($repayment->amount) }}</td>
                                <td class="px-6 py-4">{{ $repayment->repayment_schedule_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No repayments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
