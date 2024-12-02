<!-- Partial view: resources/views/partials/contacts-table.blade.php -->

<div id="contactsResults">
    <h3 class="text-base font-semibold mb-4 text-black">Contacts (Total: {{ $contacts->total() }})</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-black rounded-lg text-center">
            <thead>
                <tr class="bg-gray-600 text-center">
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Name</th>
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Email</th>
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Contact
                        Number</th>
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Campus</th>
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Type</th>
                    <th class="py-2 px-4 border border-black text-xs font-medium text-white tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr class="hover:bg-red-100 transition duration-150 ease-in-out">
                        <td class="border dark:border-gray-700 px-4 py-2 text-xs">
                            {{ $contact->stud_lname ?? $contact->emp_lname }}
                            {{ $contact->stud_fname ?? $contact->emp_fname }}
                        </td>
                        <td class="border dark:border-gray-700 px-2 py-1 text-xs">
                            {{ $contact->stud_email ?? $contact->emp_email }}
                        </td>
                        <td class="border dark:border-gray-700 px-2 py-1 text-xs">
                            {{ $contact->stud_contact ?? $contact->emp_contact }}
                        </td>
                        <td class="border dark:border-gray-700 px-2 py-1 text-xs">
                            {{ $contact->campus->campus_name ?? 'N/A' }}
                        </td>
                        <td class="border dark:border-gray-700 px-2 py-1 text-xs">
                            {{ $contact instanceof \App\Models\Student ? 'Student' : 'Employee' }}
                        </td>
                        <td class="border dark:border-gray-700 px-2 text-xs">
                            <button type="button" class="edit-btn text-white bg-blue-500 px-3 py-1 rounded-lg"
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
    </div>

    <!-- Pagination Links -->
    <div class="my-4">
        {{ $contacts->links() }}
    </div>
</div>
