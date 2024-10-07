<div>
    <h3 class="text-lg font-semibold mt-8 mb-4 text-gray-800 dark:text-gray-100">Message Templates</h3>
    <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" x-data @click="$dispatch('open-modal', 'create-message-template')">Create New Template</button>

    <table class="table-auto w-full border dark:border-gray-700">
        <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                <th class="px-4 py-2 border dark:border-gray-600">Title</th>
                <th class="px-4 py-2 border dark:border-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($messageTemplates as $template)
                <tr class="bg-white dark:bg-gray-900">
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $template->name }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        <button class="bg-yellow-500 text-white px-4 py-2 rounded" x-data @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')">Edit</button>
                        <form action="{{ route('message-templates.destroy', $template) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- Edit Message Template Modal -->
                <x-modal name="edit-message-template-{{ $template->id }}" :show="false">
                    <form method="POST" action="{{ route('message-templates.update', $template) }}">
                        @csrf
                        @method('PATCH')
                        <div class="p-4">
                            <h2 class="text-lg font-semibold mb-4">Edit Message Template</h2>
                            <div class="mb-4">
                                <x-input-label for="name" value="Template Name" />
                                <x-text-input id="name" name="name" type="text" value="{{ $template->name }}" class="block w-full mt-1" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="content" value="Template Content" />
                                <textarea id="content" name="content" class="block w-full mt-1" rows="5" required>{{ $template->content }}</textarea>
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button>Update</x-primary-button>
                            </div>
                        </div>
                    </form>
                </x-modal>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links for Message Templates -->
    <div class="mt-4">
        {{ $messageTemplates->links() }}
    </div>

    <!-- Create Message Template Modal -->
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
</div>
