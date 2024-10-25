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
                    <h3 class="text-lg font-semibold mb-4">Message Delivery Overview</h3>

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

                    <!-- Donut Chart container for Message Delivery Overview -->
                    <div class="chart-container mb-10" style="position: relative; height:40vh; width:80vw">
                        <canvas id="messageDeliveryChart"></canvas>
                    </div>

                    <!-- Line Chart container for Message Success Rate Over Time -->
                    <h3 class="text-lg font-semibold mb-4">Message Success Rate Over Time</h3>
                    <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                        <canvas id="successRateChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Donut Chart for Message Delivery Overview
        var ctx1 = document.getElementById('messageDeliveryChart').getContext('2d');
        var deliveryChart = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statuses) !!}, // Status labels
                datasets: [
                    {
                        label: 'Message Status',
                        backgroundColor: ['#36A2EB', '#4BC0C0', '#FFCE56', '#FF6384'], // Colors for each status
                        data: {!! json_encode($counts) !!} // Counts of each status
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    
        // Line Chart for Message Success Rate Over Time
        var ctx2 = document.getElementById('successRateChart').getContext('2d');
        var successRateChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!}, // Dates for the x-axis
                datasets: [
                    {
                        label: 'Sent Count',
                        data: {!! json_encode($sentCounts) !!}, // Sent counts over time
                        borderColor: 'green',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Failed Count',
                        data: {!! json_encode($failedCounts) !!}, // Failed counts over time
                        borderColor: 'red',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time', // Sets up the x-axis to handle dates
                        time: {
                            unit: 'day', // Use 'day' granularity
                            tooltipFormat: 'MMM d', // Tooltip format for date
                            displayFormats: {
                                day: 'MMM d' // Display format for date
                            }
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
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    </script>
     
</x-app-layout>
