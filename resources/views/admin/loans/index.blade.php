@extends('layouts.admin')
@include('components.spacer',['direction'=>'vertical','size'=>'10'])
@section('content')
    <div class="p-4 sm:p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Loan Management</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage and approve loan applications</p>
                </div>

                <!-- Stats Overview -->
                <div class="flex gap-3">
                    <div class="text-center px-4 py-2 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg border border-yellow-200 dark:border-yellow-500/20">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $loans->where('status', 'pending')->count() }}</div>
                        <div class="text-xs text-yellow-700 dark:text-yellow-400">Pending</div>
                    </div>
                    <div class="text-center px-4 py-2 bg-green-50 dark:bg-green-500/10 rounded-lg border border-green-200 dark:border-green-500/20">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $loans->where('status', 'ongoing')->count() }}</div>
                        <div class="text-xs text-green-700 dark:text-green-400">Ongoing</div>
                    </div>
                    <div class="text-center px-4 py-2 bg-gray-50 dark:bg-gray-500/10 rounded-lg border border-gray-200 dark:border-gray-500/20">
                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $loans->where('status', 'completed')->count() }}</div>
                        <div class="text-xs text-gray-700 dark:text-gray-400">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 flex items-center p-4 text-sm text-green-800 dark:text-green-200 rounded-lg bg-green-50 dark:bg-green-500/10 border border-green-300 dark:border-green-500/20"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-center p-4 text-sm text-red-800 dark:text-red-200 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-300 dark:border-red-500/20"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Filter and Search Section -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Status Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterLoans('all')" id="filter-all"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        All Loans
                    </button>
                    <button onclick="filterLoans('pending')" id="filter-pending"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Pending
                        <span class="ml-1.5 px-2 py-0.5 text-xs bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400 rounded-full">
                            {{ $loans->where('status', 'pending')->count() }}
                        </span>
                    </button>
                    <button onclick="filterLoans('approved')" id="filter-approved"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Approved
                    </button>
                    <button onclick="filterLoans('ongoing')" id="filter-ongoing"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Ongoing
                    </button>
                    <button onclick="filterLoans('completed')" id="filter-completed"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Completed
                    </button>
                    <button onclick="filterLoans('defaulted')" id="filter-defaulted"
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Defaulted
                    </button>
                </div>

                <!-- Search Box -->
                <div class="relative w-full lg:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="search-input"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by customer, reference..." onkeyup="searchLoans()">
                </div>
            </div>
        </div>

        <!-- Loans Table -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3">Reference</th>
                        <th scope="col" class="px-4 py-3">Customer</th>
                        <th scope="col" class="px-4 py-3">Amount</th>
                        <th scope="col" class="px-4 py-3">Interest</th>
                        <th scope="col" class="px-4 py-3">Tenure</th>
                        <th scope="col" class="px-4 py-3">Start Date</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="loans-tbody">
                    @forelse($loans as $loan)
                        <tr class="loan-row bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                            data-status="{{ $loan->status }}"
                            data-search="{{ strtolower($loan->customer->user->name . ' ' . $loan->customer->user->email . ' ' . ($loan->reference ?? '')) }}">

                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $loan->reference ?? 'Pending' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $loan->customer->user->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $loan->customer->user->email }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                ₦{{ number_format($loan->principal, 0) }}
                            </td>

                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $loan->interest_rate }}%
                            </td>

                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $loan->tenure_months }}m
                            </td>

                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $loan->start_date ? $loan->start_date->format('d M, Y') : '-' }}
                            </td>

                            <td class="px-4 py-3">
                                @switch($loan->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400 whitespace-nowrap">
                                            Pending
                                        </span>
                                    @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-400 whitespace-nowrap">
                                            Approved
                                        </span>
                                    @break
                                    @case('ongoing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400 whitespace-nowrap">
                                            Ongoing
                                        </span>
                                    @break
                                    @case('completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-500/20 text-gray-800 dark:text-gray-300 whitespace-nowrap">
                                            Completed
                                        </span>
                                    @break
                                    @case('defaulted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-500/20 text-red-800 dark:text-red-400 whitespace-nowrap">
                                            Defaulted
                                        </span>
                                    @break
                                @endswitch
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if ($loan->status === 'pending')
                                        <button type="button" data-modal-target="approve-loan-modal-{{ $loan->id }}"
                                            data-modal-toggle="approve-loan-modal-{{ $loan->id }}"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg whitespace-nowrap">
                                            Approve
                                        </button>

                                        <button type="button" data-modal-target="reject-loan-modal-{{ $loan->id }}"
                                            data-modal-toggle="reject-loan-modal-{{ $loan->id }}"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg whitespace-nowrap">
                                            Reject
                                        </button>
                                    @else
                                        <a href="{{ route('admin.loans.show', $loan) }}"
                                            class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                                            View
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Include Modals for pending loans -->
                        @if ($loan->status === 'pending')
                            @include('admin.loans.approve', ['loan' => $loan, 'customers' => $customers ?? []])
                            @include('admin.loans.reject', ['loan' => $loan])
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No loans found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Empty State for Filtered Results -->
        <div id="no-results" class="hidden mt-4 text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No results found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter to find what you're looking for.</p>
        </div>

        <!-- Pagination -->
        @if($loans->hasPages())
            <div class="mt-6">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

    <script>
        let currentFilter = 'all';

        function filterLoans(status) {
            currentFilter = status;
            const rows = document.querySelectorAll('.loan-row');
            const noResults = document.getElementById('no-results');
            const table = document.querySelector('table');
            let visibleCount = 0;

            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                btn.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
            });

            const activeBtn = document.getElementById('filter-' + status);
            activeBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
            activeBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');

            // Filter rows
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');

                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide table and no results message
            if (visibleCount === 0) {
                table.style.display = 'none';
                noResults.classList.remove('hidden');
            } else {
                table.style.display = 'table';
                noResults.classList.add('hidden');
            }
        }

        function searchLoans() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.loan-row');
            const noResults = document.getElementById('no-results');
            const table = document.querySelector('table');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const rowStatus = row.getAttribute('data-status');

                const matchesSearch = searchData.includes(searchTerm);
                const matchesFilter = currentFilter === 'all' || rowStatus === currentFilter;

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide table and no results message
            if (visibleCount === 0) {
                table.style.display = 'none';
                noResults.classList.remove('hidden');
            } else {
                table.style.display = 'table';
                noResults.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Filters initialized');
        });
    </script>
@endsection
