<div>
    <h3 class="text-lg font-semibold mb-4 text-black">Message Categories</h3>

    <!-- Button to Open Create Modal -->
    <button class="bg-[#9d1e18] text-white px-4 py-2 rounded mb-4" x-data
        @click="$dispatch('open-modal', 'create-message-category')">
        Create New Category
    </button>

    <!-- Message Categories Table -->
    <table class="min-w-full bg-white border border-gray-300 rounded-lg text-center">
        <thead class="bg-gray-700">
            <tr class="bg-gray-700 text-center">
                <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">Name</th>
                <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messageCategories as $category)
                <tr class="hover:bg-red-100 transition duration-150 ease-in-out">
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $category->name }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        <!-- Button to Open Edit Modal -->
                        <button class="rounded-full bg-blue-500 p-2 hover:bg-blue-600 items-center justify-center"
                            x-data @click="$dispatch('open-modal', 'edit-message-category-{{ $category->id }}')">
                            <img src="{{ asset('images/edit.png') }}" alt="Edit" class="h-5 w-5"
                                style="filter: brightness(0) invert(1);">
                        </button>

                        {{-- <!-- Form to Delete Category -->
                        <form action="{{ route('message-categories.destroy', $category) }}" method="POST"
                            class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-full bg-red-500 p-2 hover:bg-red-600 flex items-center justify-center">
                                <img src="{{ asset('images/delete.png') }}" alt="Delete" class="h-5 w-5"
                                    style="filter: brightness(0) invert(1);">
                            </button>
                        </form> --}}
                    </td>
                </tr>

                <!-- Include the edit modal for each category -->
                <x-edit-message-category-modal :category="$category" />
                
            @empty
                <!-- Display this row when there are no categories -->
                <tr>
                    <td colspan="2" class="py-8">
                        <div class="flex flex-col items-center justify-center">
                            <p class="text-gray-500">No message categories found</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $messageCategories->links() }}
    </div>
</div>
