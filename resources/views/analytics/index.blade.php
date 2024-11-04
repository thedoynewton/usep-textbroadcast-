<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Warning Message -->
            @if ($lowBalance)
                <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                    <h2 class="text-xl font-bold">Warning: Low SMS Balance</h2>
                    <p>Your SMS balance is running low. Please recharge to avoid service interruption.</p>
                </div>
            @endif

            <!-- Unified Date Range Filter Form (applies to both Message and Costs Overviews) -->
            <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                <form id="dateRangeFilterForm" class="flex flex-wrap gap-4 items-center">
                    <!-- Date Range Filters -->
                    <div>
                        <label for="start_date" class="block text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Filter Date Range
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabs for Message Overview and Costs Overview -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-center mb-4 border-b">
                    <button id="messageOverviewTab" onclick="showTab('messageOverviewContent', 'messageOverviewTab')"
                        class="px-4 py-2 border-b-2 border-indigo-500 text-indigo-500">Message Overview</button>
                    <button id="costOverviewTab" onclick="showTab('costOverviewContent', 'costOverviewTab')"
                        class="px-4 py-2 text-gray-600 border-b-2 border-transparent hover:text-indigo-500">Costs
                        Overview</button>
                </div>

                <!-- Message Overview Content -->
                <div id="messageOverviewContent" class="tab-content">
                    <!-- Additional Filters for Message Overview -->
                    <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                        <form id="messageFilterForm" class="flex flex-wrap gap-4 items-center">
                            <!-- Recipient Type Dropdown -->
                            <div>
                                <label for="recipient_type" class="block text-gray-700">Recipient Type</label>
                                <select name="recipient_type" id="recipient_type"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="" {{ empty($recipientType) ? 'selected' : '' }}>All Recipient
                                        Type</option>
                                    <option value="Student" {{ $recipientType === 'Student' ? 'selected' : '' }}>Student
                                    </option>
                                    <option value="Employee" {{ $recipientType === 'Employee' ? 'selected' : '' }}>
                                        Employee</option>
                                </select>
                            </div>

                            <!-- Campus Dropdown -->
                            <div id="campusField">
                                <label for="campus" class="block text-gray-700">Campus</label>
                                <select name="campus" id="campus"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="" {{ empty($campusId) ? 'selected' : '' }}>All</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->campus_id }}"
                                            {{ $campusId == $campus->campus_id ? 'selected' : '' }}>
                                            {{ $campus->campus_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Additional Fields for Students -->
                            <div id="studentFields" class="hidden gap-4">
                                <div>
                                    <label for="academic_unit" class="block text-gray-700">Academic Unit</label>
                                    <select name="college_id" id="academic_unit"
                                        class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Academic Unit</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="program" class="block text-gray-700">Program</label>
                                    <select name="program_id" id="program"
                                        class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="major" class="block text-gray-700">Major</label>
                                    <select name="major_id" id="major" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Major</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="year" class="block text-gray-700">Year</label>
                                    <select name="year_id" id="year" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Year</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Fields for Employees -->
                            <div id="employeeFields" class="hidden gap-4">
                                <div>
                                    <label for="office" class="block text-gray-700">Office</label>
                                    <select name="office_id" id="office"
                                        class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Office</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="type" class="block text-gray-700">Type</label>
                                    <select name="type" id="type" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Type</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-gray-700">Status</label>
                                    <select name="status" id="status" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Status</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                    Filter Message Overview
                                </button>
                            </div>
                        </form>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Total Messages Overview</h3>
                    <canvas id="messagesOverviewChart" class="w-full h-64 mb-6"
                        data-message-dates='@json($messageDates)'
                        data-success-counts='@json($successCounts)'
                        data-failed-counts='@json($failedCounts)'></canvas>
                </div>

                <!-- Costs Overview Content (Initially hidden) -->
                <div id="costOverviewContent" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-4">Total Costs Overview</h3>
                    <canvas id="costsChart" class="w-full h-64" data-cost-dates='@json($costDates)'
                        data-costs='@json($costs)'></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the JavaScript files via Vite -->
    @vite(['resources/js/analytics.js', 'resources/js/analyticsFilter.js'])
</x-app-layout>
