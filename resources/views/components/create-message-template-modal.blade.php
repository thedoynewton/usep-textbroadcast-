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
