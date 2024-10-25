<!-- resources/views/analytics/index.blade.php -->
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
                    <!-- Analytics content goes here -->
                    <h3 class="text-lg font-semibold mb-4">Analytics Dashboard</h3>
                    <!-- Add analytics widgets, charts, or tables here -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
