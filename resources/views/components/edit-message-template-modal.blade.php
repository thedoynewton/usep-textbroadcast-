<x-modal name="edit-message-template-{{ $template->id }}" :show="false">
    <form method="POST" action="{{ route('message-templates.update', $template) }}">
        @csrf
        @method('PATCH')
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Edit Message Template</h2>
            <div class="mb-4">
                <x-input-label for="name" value="Category Name" />
                <x-text-input id="name" name="name" type="text"
                              value="{{ $template->name }}" class="block w-full mt-1" required />
            </div>
            <div class="mb-4">
                <x-input-label for="content" value="Message Content" />
                <textarea id="content" name="content" class="block w-full mt-1" rows="5" required>{{ $template->content }}</textarea>
            </div>
            <div class="flex justify-end">
                <x-primary-button>Update</x-primary-button>
            </div>
        </div>
    </form>
</x-modal>
