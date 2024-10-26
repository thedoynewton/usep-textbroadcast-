<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

                    <!-- Date Range and Message Type Filter Form -->
                    <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                        <form method="GET" action="{{ route('analytics.index') }}"
                            class="flex flex-wrap gap-4 items-center">
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
                            <!-- Recipient Type Dropdown -->
                            <div>
                                <label for="recipient_type" class="block text-gray-700">Recipient Type</label>
                                <select name="recipient_type" id="recipient_type"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="" {{ empty($recipientType) ? 'selected' : '' }}>All</option>
                                    <option value="Student" {{ $recipientType === 'Student' ? 'selected' : '' }}>Student
                                    </option>
                                    <option value="Employee" {{ $recipientType === 'Employee' ? 'selected' : '' }}>
                                        Employee</option>
                                </select>
                            </div>

                            <!-- Campus Dropdown (Always Visible) -->
                            <div id="campusField">
                                <label for="campus" class="block text-gray-700">Campus</label>
                                <select name="campus" id="campus" <!-- Added id="campus" here -->
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
                            <div id="studentFields" class="hidden flex gap-4">
                                <div>
                                    <label for="academic_unit" class="block text-gray-700">Academic Unit</label>
                                    <select name="college_id" id="academic_unit"
                                        class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Academic Unit</option>
                                        <!-- Options will be dynamically populated by JavaScript based on campus -->
                                    </select>
                                </div>

                                <!-- Program Dropdown -->
                                <div>
                                    <label for="program" class="block text-gray-700">Program</label>
                                    <select name="program_id" id="program"
                                        class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Program</option>
                                        <!-- Options populated dynamically based on Academic Unit -->
                                    </select>
                                </div>

                                <!-- Major Dropdown -->
                                <div>
                                    <label for="major" class="block text-gray-700">Major</label>
                                    <select name="major_id" id="major" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Major</option>
                                        <!-- Options populated dynamically based on Program -->
                                    </select>
                                </div>

                                <!-- Year Dropdown -->
                                <div>
                                    <label for="year" class="block text-gray-700">Year</label>
                                    <select name="year_id" id="year" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Year</option>
                                        <!-- Options will be dynamically populated by JavaScript -->
                                    </select>
                                </div>

                            </div>

                            <!-- Additional Fields for Employees -->
                            <div id="employeeFields" class="hidden flex gap-4">
                                <div>
                                    <label for="office" class="block text-gray-700">Office</label>
                                    <select name="office" class="border-gray-300 rounded-md shadow-sm">
                                        <!-- Add office options here -->
                                    </select>
                                </div>
                                <div>
                                    <label for="type" class="block text-gray-700">Type</label>
                                    <select name="type" class="border-gray-300 rounded-md shadow-sm">
                                        <!-- Add type options here -->
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-gray-700">Status</label>
                                    <select name="status" class="border-gray-300 rounded-md shadow-sm">
                                        <!-- Add status options here -->
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                    Filter
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

                    <!-- Date Range and Message Type Filter Form -->
                    <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                        <form method="GET" action="{{ route('analytics.index') }}"
                            class="flex flex-wrap gap-4 items-center">
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
                            <div class="mt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

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
