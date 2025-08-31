@extends('layouts.admin')

@section('content')
    <div class="p-6 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Loans</h1>
            <a href="{{ route('admin.loans.create') }}"
                class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Create Loan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg dark:bg-red-200 dark:text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3">Principal</th>
                            <th scope="col" class="px-6 py-3">Interest Rate</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Loan Balance</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                           <tr class="bg-white dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">{{ $loan->customer->user->name }}</td>
                                <td class="px-6 py-4">{{ format_currency($loan->principal) }}</td>
                                <td class="px-6 py-4">{{ number_format($loan->interest_rate, 2) }}%</td>
                                <td class="px-6 py-4">{{ ucfirst($loan->status) }}</td>
                                <td class="px-6 py-4">{{ format_currency($loan->loan_balance) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.loans.show', $loan) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No loans found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $loans->links('vendor.pagination.flowbite') }}
            </div>
        </div>
    </div>
@endsection
