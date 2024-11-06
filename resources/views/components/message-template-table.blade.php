<div>
    <h3 class="text-lg font-semibold mb-4 text-black">Message Templates</h3>

    <!-- Button to Open Create Modal -->
    <button class="bg-[#9d1e18] text-white px-4 py-2 rounded mb-4" x-data
        @click="$dispatch('open-modal', 'create-message-template')">
        Create New Template
    </button>

    <!-- Message Templates Table -->
    <table class="min-w-full bg-white border border-gray-300 rounded-lg">
        <thead class="bg-gray-50">
            <tr class="dark:bg-gray-700 text-center">
                <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">Title
                </th>
                <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                    Content</th>
                <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messageTemplates as $template)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $template->name }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        <!-- Display short content with "Read More" link if content exceeds 30 characters -->
                        @if (strlen($template->content) > 30)
                            {{ Str::limit($template->content, 30) }}...
                            <button class="text-blue-500 hover:underline" x-data
                                @click="$dispatch('open-modal', 'read-more-{{ $template->id }}')">
                                Read More
                            </button>
                        @else
                            {{ $template->content }}
                        @endif
                    </td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        <!-- Button to Open Edit Modal -->
                        <button class="rounded-full bg-blue-500 p-2 hover:bg-blue-600 items-center justify-center"
                            x-data @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')">
                            <img src="{{ asset('images/edit.png') }}" alt="Edit" class="h-5 w-5"
                                style="filter: brightness(0) invert(1);">
                        </button>

                        <!-- Form to Delete Template -->
                        <form action="{{ route('message-templates.destroy', $template) }}" method="POST"
                            class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-full bg-red-500 p-2 hover:bg-red-600 flex items-center justify-center">
                                <img src="{{ asset('images/delete.png') }}" alt="Delete" class="h-5 w-5"
                                    style="filter: brightness(0) invert(1);">
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Include the modal components for Read More and Edit -->
                <x-read-more-modal :template="$template" />
                <x-edit-message-template-modal :template="$template" />
                
                @empty
                <!-- Display this row when there are no templates -->
                <tr>
                    <td colspan="3" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <img src="{{ asset('svg/msgTemplate.svg') }}" alt="No Templates" class="h-40 w-40 mb-4">
                            <p class="text-gray-500">No message templates found</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $messageTemplates->links() }}
    </div>
</div>
