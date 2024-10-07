<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Tabs for ALL, STUDENTS, EMPLOYEES -->
                <ul class="flex border-b">
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'all']) }}" class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'all' ? 'border-b-2 border-red-500' : '' }}">ALL</a>
                    </li>
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'students']) }}" class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'students' ? 'border-b-2 border-red-500' : '' }}">STUDENTS</a>
                    </li>
                    <li class="-mb-px mr-1">
                        <a href="{{ route('messages.index', ['tab' => 'employees']) }}" class="bg-white inline-block py-2 px-4 text-blue-700 font-semibold {{ request('tab') == 'employees' ? 'border-b-2 border-red-500' : '' }}">EMPLOYEES</a>
                    </li>
                </ul>

                <!-- Message Form based on selected tab -->
                <form action="#" method="POST" class="mt-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                            <select id="campus" name="campus" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option>Select Campus</option>
                                <!-- Add Campus Options Here -->
                            </select>
                        </div>

                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700">Select Template</label>
                            <select id="template" name="template" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                <option>Select a Template</option>
                                <!-- Add Template Options Here -->
                            </select>
                        </div>
                    </div>

                    @if(request('tab') == 'students')
                        <!-- Additional filters for students -->
                        <div class="grid grid-cols-4 gap-4 mt-6">
                            <div>
                                <label for="academic_unit" class="block text-sm font-medium text-gray-700">Academic Unit</label>
                                <select id="academic_unit" name="academic_unit" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Academic Unit</option>
                                    <!-- Add Academic Unit Options Here -->
                                </select>
                            </div>
                            <div>
                                <label for="program" class="block text-sm font-medium text-gray-700">Academic Program</label>
                                <select id="program" name="program" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Program</option>
                                    <!-- Add Program Options Here -->
                                </select>
                            </div>
                            <div>
                                <label for="major" class="block text-sm font-medium text-gray-700">Major</label>
                                <select id="major" name="major" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Major</option>
                                    <!-- Add Major Options Here -->
                                </select>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                <select id="year" name="year" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Year</option>
                                    <!-- Add Year Options Here -->
                                </select>
                            </div>
                        </div>
                    @elseif(request('tab') == 'employees')
                        <!-- Additional filters for employees -->
                        <div class="grid grid-cols-3 gap-4 mt-6">
                            <div>
                                <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                                <select id="office" name="office" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Office</option>
                                    <!-- Add Office Options Here -->
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Status</option>
                                    <!-- Add Status Options Here -->
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select id="type" name="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    <option>Select Type</option>
                                    <!-- Add Type Options Here -->
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Message Input -->
                    <div class="mt-6">
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md" placeholder="Enter your message here..."></textarea>
                    </div>

                    <!-- Additional Controls -->
                    <div class="grid grid-cols-4 gap-4 mt-6">
                        <div>
                            <label for="batch_size" class="block text-sm font-medium text-gray-700">Batch Size</label>
                            <input type="number" id="batch_size" name="batch_size" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md" value="1" />
                        </div>
                        <div>
                            <label for="total_recipients" class="block text-sm font-medium text-gray-700">Total Recipients</label>
                            <input type="number" id="total_recipients" name="total_recipients" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md" value="0" readonly />
                        </div>
                        <div class="flex items-center mt-6">
                            <label class="text-sm font-medium text-gray-700">Send Message:</label>
                            <div class="ml-4">
                                <input type="radio" id="send_now" name="send_message" value="now" class="mr-2" checked />
                                <label for="send_now" class="text-sm font-medium text-gray-700">Now</label>
                                <input type="radio" id="send_later" name="send_message" value="later" class="ml-4 mr-2" />
                                <label for="send_later" class="text-sm font-medium text-gray-700">Send Later</label>
                            </div>
                        </div>
                        <div>
                            <label for="send_date" class="block text-sm font-medium text-gray-700">Select Date and Time</label>
                            <input type="datetime-local" id="send_date" name="send_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md" />
                        </div>
                    </div>

                    <!-- Review Button -->
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Review Message</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
