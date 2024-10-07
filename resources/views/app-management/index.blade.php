<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('App Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Search Form and Filters -->
                <form method="GET" action="{{ route('app-management.index') }}" class="mb-6">
                    <div class="flex items-center space-x-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email"
                               class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100" />
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

                <!-- Include Students Table -->
                <x-student-table :students="$students" :totalStudents="$totalStudents" />

                <!-- Include Employees Table -->
                <x-employee-table :employees="$employees" :totalEmployees="$totalEmployees" />

                <!-- Include Message Template Section -->
                <x-message-template-table :messageTemplates="$messageTemplates" />
            </div>
        </div>
    </div>
</x-app-layout>
