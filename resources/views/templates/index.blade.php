<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Templates') }}
        </h2>
    </x-slot>

    <script>
        // Check if there's a success message in the session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000 // Auto-close the alert after 2 seconds
            });
        @endif
    
        // Check if there's an error message in the session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                showConfirmButton: true // Show the "OK" button
            });
        @endif
    </script>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Sub-navigation -->
                <div class="flex-wrap flex border-b-2">
                    <nav class="flex" aria-label="Sub-navigation">
                        <a href="{{ route('templates.index', ['section' => 'message-templates']) }}"
                            class="px-3 py-2 font-medium focus:outline-none rounded-tl-lg rounded-tr-lg transition duration-200 ease-in-out 
                {{ request('section', 'message-templates') == 'message-templates'
                    ? 'text-white bg-[#333333] font-semibold border-b-2'
                    : 'text-black dark:text-[#4b5563] hover:bg-gray-100' }}">
                            Communication Templates
                        </a>
                    </nav>
                </div>

                <!-- Button to Open Create Modal -->
                <div class="pt-10">
                <button class="bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700" x-data
                    @click="$dispatch('open-modal', 'create-message-template')">
                    Create New Template
                </button>
            </div>


                <!-- Message Templates Section -->
                <x-message-template-table :messageTemplates="$messageTemplates" :messageCategories="$messageCategories" />
                <x-create-message-template-modal :messageCategories="$messageCategories" />

            </div>
        </div>
    </div>
</x-app-layout>
