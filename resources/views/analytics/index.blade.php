<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Warning Message -->
        @if ($lowBalance)
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                <h2 class="text-xl font-bold">Warning: Low SMS Balance</h2>
                <p>Your credit balance is running low. Please recharge to avoid service interruption.</p>
            </div>
        @endif

        <!-- Total Messages by Status Card with Grouped Bar Chart -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Total Messages Sent by Status</h3>
            <p class="text-gray-500 text-sm">Overview of the total number of messages sent by message status.</p>
            <div class="relative mb-4" style="height: 300px; width: 100%;">
                <canvas id="messagesByStatusChart" class="absolute inset-0 w-full h-full"
                    data-status-dates='@json($dates)'
                    data-status-data='@json($statusData)'></canvas>
            </div>
            <button id="exportStatusData" class="bg-blue-500 text-white px-4 py-2 rounded">Export to CSV</button>
        </div>

        <!-- Grid Layout for Other Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Messages Sent by Category Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Messages Sent by Category</h3>
                    <p class="text-gray-500 text-sm">Overview of the total number of messages sent by each category.</p>
                </div>
                <div class="p-6">
                    <div class="relative mb-4" style="height: 0; padding-bottom: 50%;">
                        <canvas id="messagesByCategoryChart" class="absolute inset-0 w-full h-full"
                            data-category-labels='@json($categoryLabels)'
                            data-category-counts='@json($categoryCounts)'></canvas>
                    </div>
                    <button id="exportCategoryData" class="bg-blue-500 text-white px-4 py-2 rounded">Export to CSV</button>
                </div>
            </div>

            <!-- Messages Sent by Recipient Type Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Messages Sent by Recipient Type</h3>
                    <p class="text-gray-500 text-sm">Total messages distributed by recipient type (e.g., Student,
                        Employee).</p>
                </div>
                <div class="p-6">
                    <div class="relative mb-4" style="height: 0; padding-bottom: 50%;">
                        <canvas id="messagesByRecipientTypeChart" class="absolute inset-0 w-full h-full"
                            data-recipient-types='@json($recipientTypes)'
                            data-recipient-counts='@json($recipientCounts)'></canvas>
                    </div>
                    <button id="exportRecipientTypeData" class="bg-blue-500 text-white px-4 py-2 rounded">Export to CSV</button>
                </div>
            </div>

        </div>
    </div>

    @vite(['resources/js/analytics.js'])
</x-app-layout>
