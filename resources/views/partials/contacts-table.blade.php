<!-- Partial view: resources/views/partials/contacts-table.blade.php -->

<div id="contactsResults">
    <h3 class="text-lg font-semibold mb-4 text-white">Contacts (Total: {{ $contacts->total() }})</h3>
    <table class="table-auto w-full border dark:border-gray-700">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border dark:border-gray-600">Name</th>
                <th class="px-4 py-2 border dark:border-gray-600">Email</th>
                <th class="px-4 py-2 border dark:border-gray-600">Contact Number</th>
                <th class="px-4 py-2 border dark:border-gray-600">Campus</th>
                <th class="px-4 py-2 border dark:border-gray-600">Type</th>
                <th class="px-4 py-2 border dark:border-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contacts as $contact)
                <tr class="bg-white">
                    <td class="border dark:border-gray-700 px-4 py-2">
                        {{ $contact->stud_fname ?? $contact->emp_fname }}
                        {{ $contact->stud_lname ?? $contact->emp_lname }}
                    </td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $contact->stud_email ?? $contact->emp_email }}
                    </td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        {{ $contact->stud_contact ?? $contact->emp_contact }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $contact->campus->campus_name ?? 'N/A' }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        {{ $contact instanceof \App\Models\Student ? 'Student' : 'Employee' }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">
                        <button type="button" class="edit-btn text-blue-500"
                            data-id="{{ $contact->stud_id ?? $contact->emp_id }}"
                            data-id-type="{{ $contact instanceof \App\Models\Student ? 'stud_id' : 'emp_id' }}"
                            data-name="{{ $contact->stud_fname ?? $contact->emp_fname }} {{ $contact->stud_lname ?? $contact->emp_lname }}"
                            data-contact="{{ $contact->stud_contact ?? $contact->emp_contact }}"
                            data-type="{{ $contact instanceof \App\Models\Student ? 'Student' : 'Employee' }}">
                            Edit
                        </button>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>
