@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Breadcrumb -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('customer.dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('customer.loans.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 md:ml-2">My Loans</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-400 md:ml-2">Loan #{{ $loan->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loan Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Loan #{{ $loan->id }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Applied on {{ $loan->created_at->format('F j, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        @switch($loan->status)
                            @case('pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Under Review
                                </span>
                                @break
                            @case('ongoing')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Active
                                </span>
                                @break
                            @case('completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Completed
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>

                <!-- Loan Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Principal Amount</div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">
                            ₦{{ number_format($loan->principal) }}
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-green-600 dark:text-green-400 text-sm font-medium">Amount Repaid</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">
                            ₦{{ number_format($loan->repaid_principal) }}
                        </div>
                    </div>

                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-orange-600 dark:text-orange-400 text-sm font-medium">Outstanding Balance</div>
                        <div class="text-2xl font-bold text-orange-900 dark:text-orange-100 mt-1">
                            ₦{{ number_format($loan->loan_balance) }}
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-purple-600 dark:text-purple-400 text-sm font-medium">Interest Rate</div>
                        <div class="text-2xl font-bold text-purple-900 dark:text-purple-100 mt-1">
                            {{ $loan->interest_rate }}% p.a.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Loan Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Loan Details</h2>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Loan Amount</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">₦{{ number_format($loan->principal) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Interest Rate</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->interest_rate }}% per annum</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tenure</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->tenure_months }} months</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Obligation</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">₦{{ number_format($loan->total_obligation) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($loan->start_date)->format('M j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($loan->end_date)->format('M j, Y') }}</dd>
                            </div>
                        </dl>

                        @if($loan->notes)
                            @php $notes = json_decode($loan->notes, true); @endphp
                            @if(isset($notes['purpose']))
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Purpose</dt>
                                    <dd class="text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $notes['purpose']) }}</dd>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Repayment Schedule -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Repayment Schedule</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Payment #
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Due Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Amount Due
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Amount Paid
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date Paid
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($loan->repaymentSchedules->sortBy('due_date') as $index => $schedule)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($schedule->due_date)->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            ₦{{ number_format($schedule->amount_due) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            ₦{{ number_format($schedule->amount_paid) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($schedule->status)
                                                @case('paid')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Paid
                                                    </span>
                                                    @break
                                                @case('overdue')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        Overdue
                                                    </span>
                                                    @break
                                                @case('pending')
                                                    @if(\Carbon\Carbon::parse($schedule->due_date)->isPast())
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                            Overdue
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            Pending
                                                        </span>
                                                    @endif
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($schedule->paid_at)
                                                {{ \Carbon\Carbon::parse($schedule->paid_at)->format('M j, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History -->
                @if($loan->repayments->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Payment History</h2>
                        </div>
                        <div class="px-6 py-4">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($loan->repayments->sortByDesc('paid_on') as $index => $repayment)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                Payment of <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($repayment->amount) }}</span>
                                                            </p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                Method: <span class="capitalize">{{ str_replace('_', ' ', $repayment->payment_method) }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ \Carbon\Carbon::parse($repayment->paid_on)->format('M j, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                @if($loan->status === 'ongoing')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>

                        @php
                            $nextPayment = $loan->repaymentSchedules->where('status', 'pending')->sortBy('due_date')->first();
                        @endphp

                        @if($nextPayment)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Next Payment Due</h4>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">₦{{ number_format($nextPayment->amount_due) }}</p>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                    Due {{ \Carbon\Carbon::parse($nextPayment->due_date)->format('M j, Y') }}
                                    @if(\Carbon\Carbon::parse($nextPayment->due_date)->isPast())
                                        <span class="text-red-600 font-medium">(Overdue)</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Make Payment
                            </button>
                            <button class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                                Download Statement
                            </button>
                            <button class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                                Contact Support
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Loan Progress -->
                @if($loan->status === 'ongoing')
                    @php
                        $totalSchedules = $loan->repaymentSchedules->count();
                        $paidSchedules = $loan->repaymentSchedules->where('status', 'paid')->count();
                        $progress = $totalSchedules > 0 ? ($paidSchedules / $totalSchedules) * 100 : 0;
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Loan Progress</h3>

                        <div class="mb-4">
                            <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <span>Progress</span>
                                <span>{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Payments Made</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $paidSchedules }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Remaining</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalSchedules - $paidSchedules }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Support -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Need Help?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Have questions about your loan? Our support team is here to help.
                    </p>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            Live Chat Support
                        </a>
                        <a href="mailto:support@bleiz.ng" class="flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            Email Support
                        </a>
                        <a href="tel:+234800000000" class="flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            Call Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
