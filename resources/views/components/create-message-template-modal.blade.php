<x-modal name="create-message-template" :show="false">
    <form method="POST" action="{{ route('message-templates.store') }}">
        @csrf
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Create New Message Template</h2>

            <!-- Category Dropdown -->
            <div class="mb-4">
                <x-input-label for="category_id" value="Select Category" />
                <select id="category_id" name="category_id" class="block w-full mt-1 border rounded" required>
                    <option value="" disabled selected>Choose a category</option>
                    @foreach ($messageCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Title Input -->
            <div class="mb-4">
                <x-input-label for="name" value="Title" />
                <x-text-input id="name" name="name" type="text" class="block w-full mt-1" required />
            </div>

            <!-- Content Input -->
            <div class="mb-4">
                <x-input-label for="content" value="Content" />
                <textarea id="content" name="content" class="block w-full mt-1" rows="5" required maxlength="160"></textarea>
            </div>

            <div class="flex justify-end">
                <x-primary-button>Create</x-primary-button>
            </div>
        </div>
    </form>
</x-modal>
