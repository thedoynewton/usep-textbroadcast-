<div>
    <h3 class="text-lg font-semibold mt-8 mb-4 text-white">Employees (Total: {{ $totalEmployees }})</h3>
    <table class="table-auto w-full border dark:border-gray-700">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border dark:border-gray-600">Name</th>
                <th class="px-4 py-2 border dark:border-gray-600">Email</th>
                <th class="px-4 py-2 border dark:border-gray-600">Contact Number</th>
                <th class="px-4 py-2 border dark:border-gray-600">Campus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr class="bg-white">
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_fname }} {{ $employee->emp_lname }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_email }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->emp_contact }}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">{{ $employee->campus->campus_name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links for Employees -->
    <div class="mt-4">
        {{ $employees->appends(['search' => request('search'), 'campus_id' => request('campus_id')])->links() }}
    </div>
</div>
