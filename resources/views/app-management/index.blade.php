<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('App Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Search Form and Filters -->
                <form method="GET" action="{{ route('app-management.index') }}" class="mb-6">
                    <div class="flex items-center space-x-4">
                        <!-- Search Input -->
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email"
                               class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100" />

                        <!-- Campus Filter Dropdown -->
                        <select name="campus_id" class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Campuses</option>
                            @foreach ($campuses as $campus)
                                <option value="{{ $campus->campus_id }}" {{ request('campus_id') == $campus->campus_id ? 'selected' : '' }}>
                                    {{ $campus->campus_name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
                    </div>
                </form>

                <!-- Students Section -->
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Students (Total: {{ $totalStudents }})</h3>
                <table class="table-auto w-full border dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                            <th class="px-4 py-2 border dark:border-gray-600">Name</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Email</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Contact Number</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Campus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr class="bg-white dark:bg-gray-900">
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_fname }} {{ $student->stud_lname }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_email }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_contact }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $student->campus->campus_name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Links for Students -->
                <div class="mt-4">
                    {{ $students->appends(['search' => request('search'), 'campus_id' => request('campus_id')])->links() }}
                </div>

                <!-- Employees Section -->
                <h3 class="text-lg font-semibold mt-8 mb-4 text-gray-800 dark:text-gray-100">Employees (Total: {{ $totalEmployees }})</h3>
                <table class="table-auto w-full border dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                            <th class="px-4 py-2 border dark:border-gray-600">Name</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Email</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Contact Number</th>
                            <th class="px-4 py-2 border dark:border-gray-600">Campus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr class="bg-white dark:bg-gray-900">
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_fname }} {{ $employee->emp_lname }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_email }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_contact }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->campus->campus_name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Links for Employees -->
                <div class="mt-4">
                    {{ $employees->appends(['search' => request('search'), 'campus_id' => request('campus_id')])->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
