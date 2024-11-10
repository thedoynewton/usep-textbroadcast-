<x-modal name="create-message-category" :show="false">
    <form method="POST" action="{{ route('message-categories.store') }}">
        @csrf
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Create New Message Category</h2>
            <div class="mb-4">
                <x-input-label for="name" value="Category Name" />
                <x-text-input id="name" name="name" type="text" class="block w-full mt-1" required />
            </div>
            <div class="flex justify-end">
                <x-primary-button>Create</x-primary-button>
            </div>
        </div>
    </form>
</x-modal>
