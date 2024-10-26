<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Date Range and Message Type Filter Form -->
            <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('analytics.index') }}" class="flex flex-wrap gap-4 items-center">
                    <div>
                        <label for="start_date" class="block text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="end_date" class="block text-gray-700">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="message_type" class="block text-gray-700">Message Type</label>
                        <select name="message_type"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">All Types</option>
                            <option value="instant" {{ $messageType == 'instant' ? 'selected' : '' }}>Instant</option>
                            <option value="scheduled" {{ $messageType == 'scheduled' ? 'selected' : '' }}>Scheduled
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Messages Overview Chart -->
            <h3>Delivered Messages to Recipients</h3>
            <canvas id="messagesOverviewChart" class="w-full h-64 mb-6"></canvas>

            <!-- Costs Overview Chart -->
            <h3>Total Costs Overview</h3>
            <canvas id="costsChart" class="w-full h-64"></canvas>
        </div>
    </div>

    <script>
        // Data from the controller for Messages Overview
        const messageDates = @json($messageDates);
        const successCounts = @json($successCounts);
        const failedCounts = @json($failedCounts);
    
        // Messages Overview Chart (Bar)
        const ctxMessages = document.getElementById('messagesOverviewChart').getContext('2d');
        new Chart(ctxMessages, {
            type: 'bar',
            data: {
                labels: messageDates,
                datasets: [{
                        label: 'Success',
                        data: successCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Failed',
                        data: failedCounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Message Count'
                        }
                    }
                }
            }
        });
    
        // Data from the controller for Costs Overview
        const costDates = @json($costDates);
        const costs = @json($costs); // Preserves full precision
    
        // Costs Overview Chart (Line)
        const ctxCosts = document.getElementById('costsChart').getContext('2d');
        new Chart(ctxCosts, {
            type: 'line',
            data: {
                labels: costDates,
                datasets: [{
                    label: 'Cost ($)',
                    data: costs, // Uses non-rounded values
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1 // Smooth curve line
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cost in USD ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(4); // Display up to 4 decimal places on the y-axis
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Cost ($): ' + context.raw.toFixed(4); // Display up to 4 decimal places in the tooltip
                            }
                        }
                    }
                }
            }
        });
    </script>
    
</x-app-layout>
