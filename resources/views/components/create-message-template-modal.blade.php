<x-modal name="create-message-template" :show="false">
    <form method="POST" action="{{ route('message-templates.store') }}">
        @csrf
        <div class="p-4 sm:p-6 lg:p-8">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">Create New Message Template</h2>

            <!-- Category Dropdown -->
            <div class="mb-4">
                <x-input-label for="category_id" value="Select Category" />
                <select id="category_id" name="category_id" class="block w-full mt-1 border rounded">
                    <option value="" disabled selected>Choose a category</option>
                    @foreach ($messageCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- New Category Input -->
            <div class="mb-4">
                <x-input-label for="new_category" value="Add New Category" />
                <x-text-input id="new_category" name="new_category" type="text" class="block w-full mt-1"
                              placeholder="Enter new category name" maxlength="100" />
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

<script>
    // Wait for the DOM to fully load before attaching event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const newCategoryInput = document.getElementById('new_category');
        const categoryDropdown = document.getElementById('category_id');

        // Disable or enable the category dropdown based on the new category input
        newCategoryInput.addEventListener('input', function() {
            categoryDropdown.disabled = newCategoryInput.value !== '';
        });
    });
</script>
