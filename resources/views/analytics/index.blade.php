<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Message Performance Overview</h3>
                    
                    <!-- Date range filter form -->
                    <form method="GET" action="{{ route('analytics.index') }}" class="mb-6 flex space-x-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="self-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </form>

                    <!-- Chart container for Message Performance -->
                    <div class="chart-container mb-6" style="position: relative; height:40vh; width:80vw">
                        <canvas id="messagePerformanceChart"></canvas>
                    </div>

                    <!-- Chart container for Cost Analytics -->
                    <h3 class="text-lg font-semibold mb-4">Cost Analytics</h3>
                    <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                        <canvas id="costAnalyticsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ctx1 = document.getElementById('messagePerformanceChart').getContext('2d');
        var performanceChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dates) !!}, // Dates for the x-axis
                datasets: [
                    {
                        label: 'Success',
                        backgroundColor: 'green',
                        data: {!! json_encode($successCounts) !!} // Success counts
                    },
                    {
                        label: 'Failed',
                        backgroundColor: 'red',
                        data: {!! json_encode($failedCounts) !!} // Failed counts
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var label = tooltipItem.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += tooltipItem.raw;
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Cost Analytics Chart
        var ctx2 = document.getElementById('costAnalyticsChart').getContext('2d');
        var costChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!}, // Dates for the x-axis
                datasets: [
                    {
                        label: 'Cost (USD)',
                        backgroundColor: 'blue',
                        borderColor: 'blue',
                        fill: false,
                        data: {!! json_encode($costs) !!}, // Costs calculated for each day (3 decimal places)
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(3); // Display 3 decimal places on the y-axis
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var label = tooltipItem.dataset.label || '';
                                if (label) {
                                    label += ': $';
                                }
                                label += tooltipItem.raw.toFixed(3); // Show 3 decimal places in tooltips
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
