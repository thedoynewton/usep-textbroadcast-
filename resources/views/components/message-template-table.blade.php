<div>
    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Message Templates</h3>

    <!-- Button to Open Create Modal -->
    <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" x-data
            @click="$dispatch('open-modal', 'create-message-template')">
        Create New Template
    </button>

    <!-- Message Templates Table -->
    <table class="table-auto w-full border dark:border-gray-700">
        <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                <th class="px-4 py-2 border dark:border-gray-600">Title</th>
                <th class="px-4 py-2 border dark:border-gray-600">Content</th>
                <th class="px-4 py-2 border dark:border-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($messageTemplates as $template)
                <tr class="bg-white dark:bg-gray-900">
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
                        <button class="bg-yellow-500 text-white px-4 py-2 rounded" x-data
                                @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')">
                            Edit
                        </button>

                        <!-- Form to Delete Template -->
                        <form action="{{ route('message-templates.destroy', $template) }}" method="POST"
                              class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- Include the modal components for Read More and Edit -->
                <x-read-more-modal :template="$template" />
                <x-edit-message-template-modal :template="$template" />
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $messageTemplates->links() }}
    </div>
</div>
