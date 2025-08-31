@extends('layouts.admin')

@section('content')
    <div class="p-6 mt-14">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Create Loan</h1>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg dark:bg-red-200 dark:text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer -->
                    <div>
                        <label for="customer_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Customer
                        </label>
                        <select id="customer_id" name="customer_id" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Select a customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->user->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Principal -->
                    <div>
                        <label for="principal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Principal (Available: {{ format_currency($availableAmount) }})
                        </label>
                        <input type="number" id="principal" name="principal" step="0.01" min="1000" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter principal amount">
                        @error('principal')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Interest Rate -->
                    <div>
                        <label for="interest_rate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Interest Rate (%)
                        </label>
                        <input type="number" id="interest_rate" name="interest_rate" step="0.01" min="0" max="100" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter interest rate">
                        @error('interest_rate')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tenure Months -->
                    <div>
                        <label for="tenure_months" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Tenure (Months)
                        </label>
                        <input type="number" id="tenure_months" name="tenure_months" min="1" max="60" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter tenure in months">
                        @error('tenure_months')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Start Date
                        </label>
                        <input type="date" id="start_date" name="start_date" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            min="{{ now()->format('Y-m-d') }}">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Repayment Cycle -->
                    <div>
                        <label for="repayment_cycle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Repayment Cycle
                        </label>
                        <select id="repayment_cycle" name="repayment_cycle" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                        </select>
                        @error('repayment_cycle')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        Create Loan
                    </button>
                    <a href="{{ route('loans.index') }}"
                        class="ml-4 text-gray-500 dark:text-gray-400 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
