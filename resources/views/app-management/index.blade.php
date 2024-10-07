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

                <!-- Error Message -->
                @if (session('error'))
                    <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Sub-navigation for different sections -->
                <nav class="mb-6">
                    <ul class="flex space-x-4">
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'contacts']) }}"
                               class="{{ request('section') == 'contacts' ? 'text-blue-500 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'templates']) }}"
                               class="{{ request('section') == 'templates' ? 'text-blue-500 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                Message Templates
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Display section based on active tab -->
                @if (request('section') == 'templates')
                    <!-- Message Templates Section -->
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Message Templates</h3>

                    <!-- Button to Open Create Modal -->
                    <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" x-data
                            @click="$dispatch('open-modal', 'create-message-template')">
                        Create New Template
                    </button>

                    <!-- Message Templates Table -->
                    <table class="table-auto w-full border dark:border-gray-700">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                                <th class="px-4 py-2 border dark:border-gray-600">Title</th>
                                <th class="px-4 py-2 border dark:border-gray-600">Content</th>
                                <th class="px-4 py-2 border dark:border-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messageTemplates as $template)
                                <tr class="bg-white dark:bg-gray-900">
                                    <td class="border dark:border-gray-700 px-4 py-2">{{ $template->name }}</td>
                                    <td class="border dark:border-gray-700 px-4 py-2">
                                        <!-- Display short content with "Read More" link if content exceeds 30 characters -->
                                        @if (strlen($template->content) > 30)
                                            {{ Str::limit($template->content, 30) }}...
                                            <button class="text-blue-500 hover:underline" x-data
                                                    @click="$dispatch('open-modal', 'read-more-{{ $template->id }}')">
                                                Read More
                                            </button>
                                        @else
                                            {{ $template->content }}
                                        @endif
                                    </td>
                                    <td class="border dark:border-gray-700 px-4 py-2">
                                        <!-- Button to Open Edit Modal -->
                                        <button class="bg-yellow-500 text-white px-4 py-2 rounded" x-data
                                                @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')">
                                            Edit
                                        </button>

                                        <!-- Form to Delete Template -->
                                        <form action="{{ route('message-templates.destroy', $template) }}" method="POST"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Read More Modal -->
                                <x-modal name="read-more-{{ $template->id }}" :show="false">
                                    <div class="p-4">
                                        <h2 class="text-lg font-semibold mb-4">Message Template Content</h2>
                                        <p class="mb-4">{{ $template->content }}</p>
                                        <div class="flex justify-end">
                                            <x-primary-button @click="$dispatch('close-modal', 'read-more-{{ $template->id }}')">
                                                Close
                                            </x-primary-button>
                                        </div>
                                    </div>
                                </x-modal>

                                <!-- Edit Message Template Modal -->
                                <x-modal name="edit-message-template-{{ $template->id }}" :show="false">
                                    <form method="POST" action="{{ route('message-templates.update', $template) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="p-4">
                                            <h2 class="text-lg font-semibold mb-4">Edit Message Template</h2>
                                            <div class="mb-4">
                                                <x-input-label for="name" value="Template Name" />
                                                <x-text-input id="name" name="name" type="text"
                                                              value="{{ $template->name }}" class="block w-full mt-1" required />
                                            </div>
                                            <div class="mb-4">
                                                <x-input-label for="content" value="Template Content" />
                                                <textarea id="content" name="content" class="block w-full mt-1" rows="5" required>{{ $template->content }}</textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <x-primary-button>Update</x-primary-button>
                                            </div>
                                        </div>
                                    </form>
                                </x-modal>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links for Message Templates -->
                    <div class="mt-4">
                        {{ $messageTemplates->links() }}
                    </div>

                    <!-- Create Message Template Modal -->
                    <x-modal name="create-message-template" :show="false">
                        <form method="POST" action="{{ route('message-templates.store') }}">
                            @csrf
                            <div class="p-4">
                                <h2 class="text-lg font-semibold mb-4">Create New Message Template</h2>
                                <div class="mb-4">
                                    <x-input-label for="name" value="Template Name" />
                                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1" required />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="content" value="Template Content" />
                                    <textarea id="content" name="content" class="block w-full mt-1" rows="5" required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button>Create</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </x-modal>

                @else
                    <!-- Contacts Section (Students and Employees) -->
                    <!-- Search Form and Filters -->
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
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
