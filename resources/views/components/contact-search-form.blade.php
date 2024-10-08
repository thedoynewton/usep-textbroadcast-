<div>
    <form method="GET" action="{{ route('app-management.index') }}" class="mb-6">
        <div class="flex items-center space-x-4">
            <!-- Search Input -->
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or email"
                   class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100" />

            <!-- Campus Filter Dropdown -->
            <select name="campus_id" class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100">
                <option value="">All Campuses</option>
                @foreach ($campuses as $campus)
                    <option value="{{ $campus->campus_id }}"
                        {{ request('campus_id') == $campus->campus_id ? 'selected' : '' }}>
                        {{ $campus->campus_name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
        </div>
    </form>
</div>
