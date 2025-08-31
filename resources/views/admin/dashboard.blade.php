@extends('layouts.admin')

@section('content')
    <div class="p-6 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bleiz Credit Dashboard</h1>
            <!-- Month Dropdown -->
            <form action="{{ route('dashboard') }}" method="GET" class="relative">
                <label for="month-select" class="sr-only">Select Month</label>
                <select id="month-select" name="month"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    onchange="this.form.submit()">
                    @foreach($stats->months as $m)
                        <option value="{{ $m['value'] }}" {{ $stats->month == $m['value'] ? 'selected' : '' }}>
                            {{ $m['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(isset($error))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg dark:bg-red-200 dark:text-red-800">
                {{ $error }}
            </div>
        @else
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- Total Capital -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalCapital) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Capital</p>
                </div>

                <!-- Loan Portfolio -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->loanPortfolio) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loan Portfolio</p>
                </div>

                <!-- Idle Capital -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->idleCapital) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Idle Capital</p>
                </div>

                <!-- Total Withdrawals -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalWithdrawals) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Withdrawals</p>
                </div>

                <!-- Investors ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalROI) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Investors ROI</p>
                </div>

                <!-- Available ROI Pool -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->availableROIPool) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Available ROI Pool</p>
                </div>

                <!-- Total Idle Funds -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->totalIdleFunds) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Idle Funds</p>
                </div>

                <!-- Current ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->currentROI) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current ROI</p>
                </div>

                <!-- Next Potential ROI -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->nextPotentialROI) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Next Potential ROI</p>
                </div>

                <!-- Realized Profit -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->realizedProfit) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Realized Profit</p>
                </div>

                <!-- Bleiz Profit -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ format_currency($stats->bleizProfit) }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bleiz Profit</p>
                </div>
            </div>

            <!-- ApexCharts Visualization -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Financial Metrics Overview</h2>
                <div id="metricsChart" class="h-96"></div>
                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var options = {
                            chart: {
                                type: 'bar',
                                height: 350,
                                fontFamily: 'Inter, sans-serif',
                                toolbar: {
                                    show: false
                                }
                            },
                            series: [{
                                name: 'Financial Metrics',
                                data: [
                                    {{ $stats->totalCapital }},
                                    {{ $stats->loanPortfolio }},
                                    {{ $stats->idleCapital }},
                                    {{ $stats->totalIdleFunds }},
                                    {{ $stats->currentROI }},
                                    {{ $stats->realizedProfit }}
                                ]
                            }],
                            xaxis: {
                                categories: ['Total Capital', 'Loan Portfolio', 'Idle Capital', 'Total Idle Funds', 'Current ROI', 'Realized Profit'],
                                labels: {
                                    style: {
                                        colors: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151',
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Amount (₦)',
                                    style: {
                                        color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                                    }
                                },
                                labels: {
                                    formatter: function (value) {
                                        return '₦' + (value / 1000000).toFixed(1) + 'M';
                                    },
                                    style: {
                                        colors: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                                    }
                                }
                            },
                            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#f472b6'],
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            grid: {
                                borderColor: document.documentElement.classList.contains('dark') ? '#4b5563' : '#e5e7eb'
                            },
                            theme: {
                                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#metricsChart"), options);
                        chart.render();

                        // Update chart on dark mode toggle
                        document.addEventListener('dark-mode-toggled', function () {
                            chart.updateOptions({
                                theme: {
                                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                                },
                                xaxis: {
                                    labels: {
                                        style: {
                                            colors: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        style: {
                                            colors: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                                        }
                                    },
                                    title: {
                                        style: {
                                            color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                                        }
                                    }
                                },
                                grid: {
                                    borderColor: document.documentElement.classList.contains('dark') ? '#4b5563' : '#e5e7eb'
                                }
                            });
                        });
                    });
                </script>
            </div>

            <!-- Inject Funds Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Inject Funds</h2>
                <form action="{{ route('admin.capital.inject') }}" method="POST">
                    @csrf
                    <div class="flex gap-4">
                        <div class="flex-grow">
                            <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Amount
                            </label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Enter amount to inject">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="mt-6 text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                            Inject
                        </button>
                    </div>
                </form>
            </div>

            <!-- Investor Stats Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Investor Statistics</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Capital</th>
                                <th scope="col" class="px-6 py-3">ROI %</th>
                                <th scope="col" class="px-6 py-3">ROI Accrued</th>
                                <th scope="col" class="px-6 py-3">Withdrawn</th>
                                <th scope="col" class="px-6 py-3">Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats->investorStats as $investor)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $investor['name'] }}</td>
                                    <td class="px-6 py-4">{{ format_currency($investor['capital']) }}</td>
                                    <td class="px-6 py-4">{{ number_format($investor['roi_percentage'], 2) }}%</td>
                                    <td class="px-6 py-4">{{ format_currency($investor['roi_accrued']) }}</td>
                                    <td class="px-6 py-4">{{ format_currency($investor['withdrawn']) }}</td>
                                    <td class="px-6 py-4">{{ format_currency($investor['available']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No investors found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
