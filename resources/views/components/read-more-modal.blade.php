<x-modal name="read-more-{{ $template->id }}" :show="false">
    <div class="p-4">
        <h2 class="text-lg font-semibold mb-4">Message Template Content</h2>
        <p class="mb-4">{{ $template->content }}</p>
        <div class="flex justify-end">
            <x-primary-button @click="$dispatch('close-modal', 'read-more-{{ $template->id }}')">
                Close
            </x-primary-button>
        </div>
    </div>
</x-modal>
