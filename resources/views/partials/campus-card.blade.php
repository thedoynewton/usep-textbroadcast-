<!-- resources/views/partials/campus-card.blade.php -->
<div class="bg-gray-100 border border-gray-300 rounded-lg p-6 shadow-md max-w-md cursor-pointer my-4"
    data-campus-id="{{ $campus->campus_id }}" data-campus-name="{{ $campus->campus_name }}">
    <h4 class="text-xl font-semibold mb-2 text-gray-800">{{ $campus->campus_name }}</h4>
    <p class="text-gray-600">Click to see import options for {{ $campus->campus_name }}.</p>
</div>
