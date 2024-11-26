<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
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

                <!-- Sub-navigation -->
                <nav class="mb-6">
                    <ul class="flex space-x-4">
                        <li>
                            <a href="{{ route('templates.index', ['section' => 'message-templates']) }}"
                                class="{{ request('section', 'message-templates') == 'message-templates' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Communication Templates
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Message Templates Section -->
                <x-message-template-table :messageTemplates="$messageTemplates" :messageCategories="$messageCategories" />
                <x-create-message-template-modal :messageCategories="$messageCategories" />

            </div>
        </div>
    </div>
</x-app-layout>
