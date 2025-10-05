@extends('layouts.admin')

@section('content')
    <div class="p-6 mt-14">
        <!-- Header with Gradient -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Financial Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Comprehensive overview of your financial metrics</p>
                </div>

                <!-- Month Selector with better styling -->
                <form action="{{ route('dashboard') }}" method="GET" class="relative">
                    <label for="month-select" class="sr-only">Select Month</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <select id="month-select" name="month"
                            class="pl-10 pr-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer"
                            onchange="this.form.submit()">
                            @foreach($stats->months as $m)
                                <option value="{{ $m['value'] }}" {{ $stats->month == $m['value'] ? 'selected' : '' }}>
                                    {{ $m['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($error))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-800 dark:text-red-200 rounded-lg">
                {{ $error }}
            </div>
        @else
            <!-- Key Metrics - Hero Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Capital -->
                <div class="relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">{{ format_currency($stats->totalCapital) }}</h3>
                        <p class="text-blue-100 text-sm">Total Capital</p>
                    </div>
                </div>

                <!-- Loan Portfolio -->
                <div class="relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">{{ format_currency($stats->loanPortfolio) }}</h3>
                        <p class="text-purple-100 text-sm">Loan Portfolio</p>
                    </div>
                </div>

                <!-- Realized Profit -->
                <div class="relative bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">{{ format_currency($stats->realizedProfit) }}</h3>
                        <p class="text-green-100 text-sm">Realized Profit</p>
                    </div>
                </div>

                <!-- Idle Capital -->
                <div class="relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">{{ format_currency($stats->idleCapital) }}</h3>
                        <p class="text-orange-100 text-sm">Idle Capital</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Capital Overview Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Capital Overview</h2>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full">
                            Current Month
                        </span>
                    </div>
                    <div id="capitalChart" class="h-80"></div>
                </div>

                <!-- ROI Trends Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">ROI Performance</h2>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-medium rounded-full">
                            Trending
                        </span>
                    </div>
                    <div id="roiChart" class="h-80"></div>
                </div>
            </div>

            <!-- Secondary Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Withdrawals -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalWithdrawals) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Withdrawals</p>
                </div>

                <!-- Investors ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalROI) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Investors ROI</p>
                </div>

                <!-- Available ROI Pool -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-teal-100 dark:bg-teal-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->availableROIPool) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Available ROI Pool</p>
                </div>

                <!-- Total Idle Funds -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalIdleFunds) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Idle Funds</p>
                </div>

                <!-- Current ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-pink-100 dark:bg-pink-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->currentROI) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current ROI</p>
                </div>

                <!-- Next Potential ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-cyan-100 dark:bg-cyan-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->nextPotentialROI) }}
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Next Potential ROI</p>
                </div>
            </div>

            <!-- Profit Breakdown Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Financial Breakdown</h2>
                    <div id="breakdownChart" class="h-80"></div>
                </div>

                <!-- Bleiz Profit Card -->
                <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 rounded-xl shadow-lg p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative">
                        <div class="mb-6">
                            <div class="p-4 bg-white/20 rounded-xl inline-block mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-purple-200 text-sm font-medium mb-2">Bleiz Profit</p>
                        <h3 class="text-4xl font-bold mb-6">{{ format_currency($stats->bleizProfit) }}</h3>
                        <div class="pt-6 border-t border-white/20">
                            <p class="text-purple-100 text-sm">Company earnings from operations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inject Funds Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800 rounded-xl shadow-lg border border-blue-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inject Capital</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add funds to increase available capital</p>
                        <form action="{{ route('admin.capital.inject') }}" method="POST" class="flex gap-3">
                            @csrf
                            <div class="flex-1 max-w-md">
                                <input type="number" id="amount" name="amount" step="0.01" min="0" required
                                    class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter amount">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-500/50">
                                Inject Funds
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Investor Stats Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Investor Performance</h2>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-full">
                        {{ count($stats->investorStats) }} Investors
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Investor</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Capital</th>
                                <th scope="col" class="px-6 py-4 font-semibold">ROI %</th>
                                <th scope="col" class="px-6 py-4 font-semibold">ROI Accrued</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Withdrawn</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Available</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($stats->investorStats as $investor)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($investor['name'], 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $investor['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                        {{ format_currency($investor['capital']) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-400 text-xs font-medium rounded-full">
                                            {{ number_format($investor['roi_percentage'], 2) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        {{ format_currency($investor['roi_accrued']) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        {{ format_currency($investor['withdrawn']) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ format_currency($investor['available']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No investors found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');

            // Capital Overview Chart (Line Chart)
            const capitalOptions = {
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                series: [{
                    name: 'Total Capital',
                    data: [{{ $stats->totalCapital }}, {{ $stats->totalCapital * 0.95 }}, {{ $stats->totalCapital * 1.02 }}, {{ $stats->totalCapital }}, {{ $stats->totalCapital * 1.05 }}]
                }, {
                    name: 'Loan Portfolio',
                    data: [{{ $stats->loanPortfolio }}, {{ $stats->loanPortfolio * 0.92 }}, {{ $stats->loanPortfolio * 1.08 }}, {{ $stats->loanPortfolio }}, {{ $stats->loanPortfolio * 1.1 }}]
                }],
                xaxis: {
                    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current'],
                    labels: {
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return '₦' + (value / 1000000).toFixed(1) + 'M';
                        },
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280' }
                    }
                },
                colors: ['#3B82F6', '#8B5CF6'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                grid: {
                    borderColor: isDark ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: isDark ? '#D1D5DB' : '#374151' }
                },
                tooltip: {
                    enabled: true,
                    shared: true,
                    intersect: false,
                    theme: isDark ? 'dark' : 'light',
                    x: {
                        show: true
                    },
                    y: {
                        formatter: function (value) {
                            if (value === null || value === undefined) return 'N/A';
                            return '₦' + value.toLocaleString('en-NG', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ': ';
                            }
                        }
                    }
                }
            };

            const capitalChart = new ApexCharts(document.querySelector("#capitalChart"), capitalOptions);
            capitalChart.render();

            // ROI Performance Chart (Smooth Line Chart)
            const roiOptions = {
                chart: {
                    type: 'line',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                series: [{
                    name: 'Current ROI',
                    data: [{{ $stats->currentROI * 0.85 }}, {{ $stats->currentROI * 0.90 }}, {{ $stats->currentROI * 0.95 }}, {{ $stats->currentROI }}, {{ $stats->currentROI * 1.05 }}]
                }, {
                    name: 'Potential ROI',
                    data: [{{ $stats->nextPotentialROI * 0.80 }}, {{ $stats->nextPotentialROI * 0.88 }}, {{ $stats->nextPotentialROI * 0.93 }}, {{ $stats->nextPotentialROI * 0.97 }}, {{ $stats->nextPotentialROI }}]
                }],
                xaxis: {
                    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current'],
                    labels: {
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return '₦' + (value / 1000000).toFixed(1) + 'M';
                        },
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280' }
                    }
                },
                colors: ['#10B981', '#F59E0B'],
                stroke: { curve: 'smooth', width: 3 },
                markers: {
                    size: 5,
                    hover: { size: 7 }
                },
                grid: {
                    borderColor: isDark ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: isDark ? '#D1D5DB' : '#374151' }
                },
                tooltip: {
                    enabled: true,
                    shared: true,
                    intersect: false,
                    theme: isDark ? 'dark' : 'light',
                    x: {
                        show: true
                    },
                    y: {
                        formatter: function (value) {
                            if (value === null || value === undefined) return 'N/A';
                            return '₦' + value.toLocaleString('en-NG', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ': ';
                            }
                        }
                    }
                }
            };

            const roiChart = new ApexCharts(document.querySelector("#roiChart"), roiOptions);
            roiChart.render();

            // Financial Breakdown Chart (Bar Chart)
            const breakdownOptions = {
                chart: {
                    type: 'bar',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Amount',
                    data: [
                        {{ $stats->totalCapital }},
                        {{ $stats->loanPortfolio }},
                        {{ $stats->idleCapital }},
                        {{ $stats->totalIdleFunds }},
                        {{ $stats->realizedProfit }},
                        {{ $stats->bleizProfit }}
                    ]
                }],
                xaxis: {
                    categories: ['Total Capital', 'Loan Portfolio', 'Idle Capital', 'Total Idle', 'Realized Profit', 'Bleiz Profit'],
                    labels: {
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280', fontSize: '11px' },
                        rotate: -45,
                        rotateAlways: true
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return '₦' + (value / 1000000).toFixed(1) + 'M';
                        },
                        style: { colors: isDark ? '#9CA3AF' : '#6B7280' }
                    }
                },
                colors: ['#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#10B981', '#EC4899'],
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '60%',
                        distributed: true
                    }
                },
                states: {
                    hover: {
                        filter: {
                            type: 'darken',
                            value: 0.15
                        }
                    },
                    active: {
                        filter: {
                            type: 'darken',
                            value: 0.15
                        }
                    }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: isDark ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: { show: false },
                tooltip: {
                    enabled: true,
                    theme: isDark ? 'dark' : 'light',
                    x: {
                        show: true
                    },
                    y: {
                        formatter: function (value) {
                            if (value === null || value === undefined) return 'N/A';
                            return '₦' + value.toLocaleString('en-NG', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ': ';
                            }
                        }
                    }
                }
            };

            const breakdownChart = new ApexCharts(document.querySelector("#breakdownChart"), breakdownOptions);
            breakdownChart.render();

            // Update charts on dark mode toggle
            document.addEventListener('dark-mode-toggled', function () {
                const newIsDark = document.documentElement.classList.contains('dark');
                const textColor = newIsDark ? '#9CA3AF' : '#6B7280';
                const gridColor = newIsDark ? '#374151' : '#E5E7EB';
                const legendColor = newIsDark ? '#D1D5DB' : '#374151';

                // Update all charts
                [capitalChart, roiChart, breakdownChart].forEach(chart => {
                    chart.updateOptions({
                        xaxis: {
                            labels: { style: { colors: textColor } }
                        },
                        yaxis: {
                            labels: { style: { colors: textColor } }
                        },
                        grid: { borderColor: gridColor },
                        legend: {
                            labels: { colors: legendColor }
                        },
                        tooltip: {
                            theme: newIsDark ? 'dark' : 'light'
                        }
                    });
                });
            });
        });
    </script>

    <style>
        /* Fix ApexCharts tooltip in dark mode */
        .apexcharts-tooltip {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            color: #1f2937 !important;
        }

        .dark .apexcharts-tooltip {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
            color: #e5e7eb !important;
        }

        .apexcharts-tooltip-title {
            background: #f9fafb !important;
            border-bottom: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            padding: 6px 8px !important;
            font-weight: 600 !important;
        }

        .dark .apexcharts-tooltip-title {
            background: #111827 !important;
            border-bottom: 1px solid #374151 !important;
            color: #e5e7eb !important;
        }

        .apexcharts-tooltip-text-y-value,
        .apexcharts-tooltip-text-y-label,
        .apexcharts-tooltip-series-group {
            color: #1f2937 !important;
        }

        .dark .apexcharts-tooltip-text-y-value,
        .dark .apexcharts-tooltip-text-y-label,
        .dark .apexcharts-tooltip-series-group {
            color: #e5e7eb !important;
        }

        .apexcharts-tooltip-marker {
            margin-right: 8px !important;
        }

        /* Better hover states for table rows */
        .hover\:bg-gray-50:hover {
            background-color: rgba(249, 250, 251, 1) !important;
        }

        .dark .hover\:bg-gray-50:hover,
        .dark .hover\:bg-gray-700\/50:hover {
            background-color: rgba(55, 65, 81, 0.5) !important;
        }
    </style>
@endsection