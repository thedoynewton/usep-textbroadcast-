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
                    <!-- Date and Type Filters -->
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
                            <option value="scheduled" {{ $messageType == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
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

            <!-- Tabs for Message Overview and Costs Overview -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-center mb-4 border-b">
                    <button id="messageOverviewTab" onclick="showTab('messageOverviewContent', 'messageOverviewTab')" class="px-4 py-2 border-b-2 border-indigo-500 text-indigo-500">Message Overview</button>
                    <button id="costOverviewTab" onclick="showTab('costOverviewContent', 'costOverviewTab')" class="px-4 py-2 text-gray-600 border-b-2 border-transparent hover:text-indigo-500">Costs Overview</button>
                </div>

                <!-- Message Overview Content -->
                <div id="messageOverviewContent" class="tab-content">
                    <h3 class="text-lg font-semibold mb-4">Delivered Messages to Recipients</h3>
                    <canvas id="messagesOverviewChart" class="w-full h-64 mb-6"></canvas>
                </div>

                <!-- Costs Overview Content (Initially hidden) -->
                <div id="costOverviewContent" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-4">Total Costs Overview</h3>
                    <canvas id="costsChart" class="w-full h-64"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switch function
        function showTab(contentId, tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

            // Remove active state from all tabs
            document.getElementById('messageOverviewTab').classList.remove('border-indigo-500', 'text-indigo-500');
            document.getElementById('messageOverviewTab').classList.add('text-gray-600', 'border-transparent');
            document.getElementById('costOverviewTab').classList.remove('border-indigo-500', 'text-indigo-500');
            document.getElementById('costOverviewTab').classList.add('text-gray-600', 'border-transparent');

            // Show the selected tab content and set the selected tab to active
            document.getElementById(contentId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('border-indigo-500', 'text-indigo-500');
            document.getElementById(tabId).classList.remove('text-gray-600', 'border-transparent');
        }

        // Data for Messages Overview Chart (Bar)
        const messageDates = @json($messageDates);
        const successCounts = @json($successCounts);
        const failedCounts = @json($failedCounts);

        const ctxMessages = document.getElementById('messagesOverviewChart').getContext('2d');
        new Chart(ctxMessages, {
            type: 'bar',
            data: {
                labels: messageDates,
                datasets: [
                    { label: 'Success', data: successCounts, backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 },
                    { label: 'Failed', data: failedCounts, backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Message Count' } } } }
        });

        // Data for Costs Overview Chart (Line)
        const costDates = @json($costDates);
        const costs = @json($costs);

        const ctxCosts = document.getElementById('costsChart').getContext('2d');
        new Chart(ctxCosts, {
            type: 'line',
            data: {
                labels: costDates,
                datasets: [{
                    label: 'Cost ($)',
                    data: costs,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Cost in USD ($)' },
                        ticks: { callback: function(value) { return value.toFixed(4); } }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: { label: function(context) { return 'Cost ($): ' + context.raw.toFixed(4); } }
                    }
                }
            }
        });
    </script>
</x-app-layout>
