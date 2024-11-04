<!-- Partial view: resources/views/partials/contacts-table.blade.php -->

<div id="contactsResults">
    <h3 class="text-lg font-semibold mb-4 text-black">Contacts (Total: {{ $contacts->total() }})</h3>
    <table class="min-w-full bg-white border border-gray-300 rounded-lg">
        <thead class="bg-gray-50 sticky top-0 z-10">
            <tr class="bg-gray-200 text-left">
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                </th>
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                </th>
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contact Number</th>
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Campus</th>
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                </th>
                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contacts as $contact)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
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
