<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Templates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

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
                            <a href="{{ route('templates.index', ['section' => 'categories']) }}"
                                class="{{ request('section', 'categories') == 'categories' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Message Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('templates.index', ['section' => 'message-templates']) }}"
                                class="{{ request('section', 'categories') == 'message-templates' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Communication Templates
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Display section based on active tab -->
                @if (request('section', 'categories') == 'message-templates')
                    <!-- Message Templates Section -->
                    <x-message-template-table :messageTemplates="$messageTemplates" :messageCategories="$messageCategories" />
                    <x-create-message-template-modal :messageCategories="$messageCategories" />
                @else
                    <!-- Message Categories Section -->
                    <x-message-category-table :messageCategories="$messageCategories" />
                    <x-create-message-category-modal />
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
