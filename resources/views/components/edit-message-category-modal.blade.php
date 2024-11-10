<x-modal name="edit-message-category-{{ $category->id }}" :show="false">
    <form method="POST" action="{{ route('message-categories.update', $category) }}">
        @csrf
        @method('PATCH')
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Edit Message Category</h2>
            <div class="mb-4">
                <x-input-label for="name" value="Category Name" />
                <x-text-input id="name" name="name" type="text"
                              value="{{ $category->name }}" class="block w-full mt-1" required />
            </div>
            <div class="flex justify-end">
                <x-primary-button>Update</x-primary-button>
            </div>
        </div>
    </form>
</x-modal>
