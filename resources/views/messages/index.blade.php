<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-black dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Tabs for ALL, STUDENTS, EMPLOYEES -->
                <ul class="flex border-b">
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'all', 'campus' => request('campus')]) }}"
                            class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'all' ? 'border-b-2 border-red-500' : '' }}">ALL</a>
                    </li>
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'students', 'campus' => request('campus')]) }}"
                            class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'students' ? 'border-b-2 border-red-500' : '' }}">STUDENTS</a>
                    </li>
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'employees', 'campus' => request('campus')]) }}"
                            class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'employees' ? 'border-b-2 border-red-500' : '' }}">EMPLOYEES</a>
                    </li>
                </ul>

                <!-- Message Form based on selected tab -->
                <form action="#" method="GET" class="mt-6">
                    @csrf
                    <!-- Hidden field to retain the selected tab -->
                    <input type="hidden" name="tab" value="{{ request('tab') }}">

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Campus Dropdown -->
                        <div>
                            <x-input-label for="campus" value="Campus" />
                            <select id="campus" name="campus"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option value="">Select Campus</option>
                                @foreach ($campuses as $campus)
                                    <option value="{{ $campus->campus_id }}"
                                        {{ $campusId == $campus->campus_id ? 'selected' : '' }}>
                                        {{ $campus->campus_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="template" value="Select Template" />
                            <select id="template" name="template"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option value="">Select a Template</option>
                                @foreach ($messageTemplates as $template)
                                    <option value="{{ $template->id }}" data-content="{{ $template->content }}">
                                        {{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    @if (request('tab') == 'students')
                        <!-- Filters for students -->
                        <div class="grid grid-cols-4 gap-4 mt-6">
                            <!-- Academic Unit (College) Dropdown -->
                            <div>
                                <x-input-label for="academic_unit" value="Academic Unit" />
                                <select id="academic_unit" name="academic_unit"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Academic Unit</option>
                                    <!-- Options will be populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Program Dropdown -->
                            <div>
                                <x-input-label for="program" value="Program" />
                                <select id="program" name="program"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Program</option>
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Major Dropdown -->
                            <div>
                                <x-input-label for="major" value="Major" />
                                <select id="major" name="major"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Major</option>
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Year Dropdown -->
                            <div>
                                <x-input-label for="year" value="Year" />
                                <select id="year" name="year"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Year</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @elseif(request('tab') == 'employees')
                        <!-- Filters for employees -->
                        <div class="grid grid-cols-3 gap-4 mt-6">
                            <!-- Office Dropdown -->
                            <div>
                                <x-input-label for="office" value="Office" />
                                <select id="office" name="office"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Office</option>
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->status_id }}">{{ $status->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Type Dropdown -->
                            <div>
                                <x-input-label for="type" value="Type" />
                                <select id="type" name="type"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="">Select Type</option>
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Message Input -->
                    <div class="mt-6">
                        <x-input-label for="message" value="Message" />
                        <textarea id="message" name="message" rows="4"
                            class="mt-1 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md"
                            placeholder="Enter your message here..."></textarea>
                    </div>

                    <!-- Character count display -->
                    <div class="text-right mt-2 text-sm text-white  ">
                        <span id="char-count">0</span>/160 characters
                    </div>

                    <!-- Additional Controls -->
                    <div class="grid grid-cols-4 gap-4 mt-6">
                        <div>
                            <x-input-label for="batch_size" value="Batch Size" />
                            <x-text-input id="batch_size" name="batch_size" type="number" value="1" />
                        </div>
                        <!-- Total Recipients -->
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="total_recipients" value="Total Recipients" />
                                <x-text-input id="total_recipients" name="total_recipients" type="number"
                                    value="{{ $totalRecipients ?? 0 }}" readonly />
                            </div>
                        </div>

                        <div class="flex items-center mt-6">
                            <label class="text-sm font-medium text-white">Send Message:</label>
                            <div class="ml-4">
                                <input type="radio" id="send_now" name="send_message" value="now"
                                    class="mr-2" checked />
                                <label for="send_now" class="text-sm font-medium text-white">Now</label>
                                <input type="radio" id="send_later" name="send_message" value="later"
                                    class="ml-4 mr-2" />
                                <label for="send_later" class="text-sm font-medium text-white">Send Later</label>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="send_date" value="Select Date and Time" />
                            <x-text-input id="send_date" name="send_date" type="datetime-local" />
                        </div>
                    </div>

                    <!-- Review Button -->
                    <div class="flex justify-end mt-6">
                        <x-primary-button>{{ __('Review Message') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @vite(['resources/js/messages.js', 'resources/js/dynamicFilters.js'])

</x-app-layout>
