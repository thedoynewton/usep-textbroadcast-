<div>
    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Students (Total: {{ $totalStudents }})</h3>
    <table class="table-auto w-full border dark:border-gray-700">
        <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                <th class="px-4 py-2 border dark:border-gray-600">Name</th>
                <th class="px-4 py-2 border dark:border-gray-600">Email</th>
                <th class="px-4 py-2 border dark:border-gray-600">Contact Number</th>
                <th class="px-4 py-2 border dark:border-gray-600">Campus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr class="bg-white dark:bg-gray-900">
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_fname }} {{ $student->stud_lname }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_email }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $student->stud_contact }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $student->campus->campus_name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links for Students -->
    <div class="mt-4">
        {{ $students->appends(['search' => request('search'), 'campus_id' => request('campus_id')])->links() }}
    </div>
</div>
