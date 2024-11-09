<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Progress Bar (Hidden by Default) -->
            <div id="progress-bar" class="w-full bg-gray-200 rounded-full h-4 mb-4 hidden">
                <div id="progress" class="bg-blue-600 h-4 rounded-full" style="width: 0%;"></div>
            </div>


            <div class="bg-white p-6 rounded-lg shadow-md">

                <!-- Tabs for ALL, STUDENTS, EMPLOYEES -->
                <ul class="flex border-b">
                    <li class="border-gray-300">
                        <a href="{{ route('messages.index', ['tab' => 'all', 'campus' => request('campus')]) }}"
                            class="inline-block py-2 px-4 text-black font-semibold {{ request('tab') == 'all' ? 'border-b-2 border-blue-700 text-blue-600' : '' }}">ALL</a>
                    </li>
                    <li class="border-gray-300">
                        <a href="{{ route('messages.index', ['tab' => 'students', 'campus' => request('campus')]) }}"
                            class="inline-block py-2 px-4 text-black font-semibold {{ request('tab') == 'students' ? 'border-b-2 border-blue-700 text-blue-600' : '' }}">STUDENTS</a>
                    </li>
                    <li class="border-gray-300">
                        <a href="{{ route('messages.index', ['tab' => 'employees', 'campus' => request('campus')]) }}"
                            class="inline-block py-2 px-4 text-black font-semibold {{ request('tab') == 'employees' ? 'border-b-2 border-blue-700 text-blue-600' : '' }}">EMPLOYEES</a>
                    </li>
                </ul>

                <!-- Message Form based on selected tab -->
                <form action="{{ route('messages.store') }}" method="POST" id="message-form" class="mt-6">
                    @csrf
                    <!-- Hidden field to retain the selected tab -->
                    <input type="hidden" name="tab" value="{{ request('tab') }}">

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Campus Dropdown -->
                        <div>
                            <x-input-label for="campus" value="Campus" />
                            <select id="campus" name="campus"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option value="" disabled selected>Select Campus</option>
                                <option value="all">All Campuses</option>
                                <!-- Default option disabled and selected -->
                                @foreach ($campuses as $campus)
                                    <option value="{{ $campus->campus_id }}"
                                        {{ old('campus') == $campus->campus_id ? 'selected' : '' }}>
                                        {{ $campus->campus_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="template" value="Select Template" />
                            <select id="template" name="template"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option value="" disabled selected>Select a Template</option>
                                <!-- Default option disabled and selected -->
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
                                    <option value="" disabled selected>Select Academic Unit</option>
                                    <option value="all">All Academic Units</option>
                                    <!-- Disabled Option -->
                                    <!-- Options will be populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Program Dropdown -->
                            <div>
                                <x-input-label for="program" value="Program" />
                                <select id="program" name="program"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="" disabled selected>Select Program</option>
                                    <option value="all">All Programs</option>
                                    <!-- Disabled Option -->
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Major Dropdown -->
                            <div>
                                <x-input-label for="major" value="Major" />
                                <select id="major" name="major"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="" disabled selected>Select Major</option>
                                    <option value="all">All Majors</option>
                                    <!-- Disabled Option -->
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Year Dropdown -->
                            <div>
                                <x-input-label for="year" value="Year" />
                                <select id="year" name="year"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="" disabled selected>Select Year</option>
                                    <option value="all">All Years</option>
                                    <!-- Disabled Option -->
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
                                    <option value="" disabled selected>Select Office</option>
                                    <option value="all">All Offices</option>
                                    <!-- Disabled Option -->
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Type Dropdown -->
                            <div>
                                <x-input-label for="type" value="Type" />
                                <select id="type" name="type"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="all">All Types</option>
                                    <!-- Disabled Option -->
                                    <!-- Options populated dynamically using JavaScript -->
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="all">All Status</option>
                                    <!-- Disabled Option -->
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->status_id }}">{{ $status->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    @endif

                    <!-- Message Input -->
                    <div class="mt-6">
                        <x-input-label for="message" value="Message" />
                        <textarea id="message" name="message" rows="4" maxlength="160"
                            class="block w-full mt-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300 p-2 text-sm overflow-y-auto resize-none"
                            placeholder="Enter your message here..."></textarea>
                    </div>

                    <!-- Character count display -->
                    <div class="mt-1 text-sm text-gray-500 pb-10 pt-2">
                        <span id="char-count">0</span>/160 characters
                    </div>

                    <!-- Additional Controls -->
                    <div class="mb-6 flex items-center space-x-8">

                        <!-- Batch Size -->
                        <div>
                            <x-input-label for="batch_size" value="Batch Size" />
                            <x-text-input id="batch_size" name="batch_size" type="number" value="1"
                                min="1" step="1" />
                        </div>

                        <!-- Total Recipients -->
                        <div>
                            <x-input-label for="total_recipients" value="Total Recipients" />
                            <x-text-input id="total_recipients" name="total_recipients" type="number"
                                value="{{ $totalRecipients ?? 0 }}" readonly />
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Send Message:</label>
                            <div class="p-1 flex items-center space-x-2">
                                <input type="radio" id="send_now" name="send_message" value="now" checked />
                                <label for="send_now">Now</label>
                                <input type="radio" id="send_later" name="send_message" value="later" />
                                <label for="send_later">Send Later</label>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="send_date" value="Select Date and Time" />
                            <x-text-input id="send_date" name="send_date" type="datetime-local" />
                        </div>
                    </div>

                    <!-- Review Button -->
                    <div class="flex justify-end">
                        <x-primary-button id="open-review-modal">{{ __('Review Message') }}</x-primary-button>
                    </div>

                    <!-- Review Message Modal (Hidden by default) -->
                    <div id="reviewModal" class="fixed z-10 inset-0 overflow-y-auto hidden"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                aria-hidden="true"></div>

                            <!-- Modal Panel -->
                            <div
                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle lg:max-w-4xl sm:w-full">
                                <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <!-- Modal Content -->
                                    <div class="sm:flex sm:items-start">
                                        <!-- Preview Phone Mockup with Image -->
                                        <div
                                            class="flex-shrink-0 flex items-center justify-center w-full sm:w-1/3 mb-4 sm:mb-0">
                                            <!-- iPhone Mockup Image -->
                                            <div class="relative">
                                                <img src="{{ asset('images/iPhone15Mockup.png') }}"
                                                    alt="iPhone Mockup" class="w-64 h-auto mx-auto">
                                                <!-- Centered User Profile Info -->
                                                <div
                                                    class="absolute top-[10%] left-[50%] transform -translate-x-1/2 w-[80%] flex flex-col items-center space-y-1">
                                                    <!-- User Icon -->
                                                    <img src="{{ asset('images/profile-user.png') }}" alt="User Icon"
                                                        class="w-6 h-6">
                                                    <!-- User Name -->
                                                    <span class="font-regular text-gray-900 mt-1"
                                                        style="font-size: 9px;">USeP</span>
                                                </div>
                                                <!-- Message preview inside the phone mockup -->
                                                <div
                                                    class="absolute top-[20%] left-[14%] w-[70%] h-auto mx-auto overflow-y-auto bg-gray-200 border rounded-lg shadow-sm p-3 mt-2">
                                                    <p class="text-xs text-gray-800" id="preview-message">
                                                        <!-- Message Preview Here -->
                                                    </p>

                                                    <!-- Delivered Status, Checkmark, and Timestamp -->
                                                    <div class="flex justify-between items-center mt-1">
                                                        <!-- Checkmark Icon for Delivered Status -->
                                                        <div class="flex items-center space-x-1">
                                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L9 11.586l6.293-6.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span class="text-xs text-gray-600">Delivered</span>
                                                        </div>

                                                        <!-- Dynamic Timestamp -->
                                                        <span class="text-xs text-gray-500" id="message-timestamp">
                                                            <!-- JS will dynamically update this -->
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="mt-3 text-center sm:mt-0 sm:ml-6 sm:text-left w-full sm:w-2/3">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                Review Message
                                            </h3>
                                            <div class="mt-2">
                                                <h2>Selected:</h2>
                                                <p class="text-sm text-gray-500">Campus: <span
                                                        id="selected-campus"></span></p>

                                                <!-- For Students Tab -->
                                                <div id="student-options" class="hidden">
                                                    <p class="text-sm text-gray-500">Academic Unit: <span
                                                            id="selected-academic-unit"></span></p>
                                                    <p class="text-sm text-gray-500">Program: <span
                                                            id="selected-program"></span></p>
                                                    <p class="text-sm text-gray-500">Major: <span
                                                            id="selected-major"></span></p>
                                                    <p class="text-sm text-gray-500">Year: <span
                                                            id="selected-year"></span></p>
                                                </div>

                                                <!-- For Employees Tab -->
                                                <div id="employee-options" class="hidden">
                                                    <p class="text-sm text-gray-500">Office: <span
                                                            id="selected-office"></span></p>
                                                    <p class="text-sm text-gray-500">Type: <span
                                                            id="selected-type"></span></p>
                                                    <p class="text-sm text-gray-500">Status: <span
                                                            id="selected-status"></span></p>
                                                </div>

                                                <p class="text-sm text-gray-500">Total Recipients: <span
                                                        id="total-recipients"></span></p>

                                                <!-- Show Date and Time when "Send Later" is selected -->
                                                <p id="selected-send-datetime" class="text-sm text-gray-500 hidden">
                                                    Send Date & Time: <span id="send-datetime"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <x-primary-button id="confirm-send">{{ __('Send Message') }}</x-primary-button>
                                    <x-secondary-button id="close-review-modal" class="mt-3 sm:mt-0 sm:ml-3">
                                        {{ __('Edit Message') }}
                                    </x-secondary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of Modal -->

                </form>

            </div>
        </div>
    </div>

    @vite(['resources/js/progressBar.js', 'resources/js/messages.js', 'resources/js/dynamicFilters.js', 'resources/js/sendMessageToggle.js',  'resources/js/messagePreview.js'])

</x-app-layout>
