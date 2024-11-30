<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <script>
        // Check if there's a success message in the session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000 // Auto-close the alert after 2 seconds
            });
        @endif
    
        // Check if there's an error message in the session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                showConfirmButton: true // Show the "OK" button
            });
        @endif
    </script>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-lg sm:rounded-lg p-6">

                <!-- Add User Form -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Add New User</h2>
                    <form action="{{ route('user-management.addUser') }}" method="POST"
                        class="flex items-center space-x-4">
                        @csrf
                        <div class="relative">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" placeholder="juandelacruz12345@usep.edu.ph" required
                                class="mt-2 w-80 h-10 pl-3 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 hover:border-indigo-500" />
                            <h3 class="mt-2 text-xs text-gray-500 text-opacity-35">Only Employee's USeP emails
                                are accepted .</h3>
                        </div>
                        <button type="reset" class="px-4 py-2 bg-[#4b5563] p-2 hover:bg-[#6b7280] text-white rounded">
                            Clear Fields</button>
                        <button type="submit" class="px-4 py-2 bg-[#9d1e18] p-2 hover:bg-red-500 text-white rounded">
                            Add User</button>
                    </form>
                </div>

                <h2 class="text-2xl font-bold mb-6 text-center sm:text-left" style="color: var(--primary-color);">
                    List of Users
                </h2>

                <table
                    class="text-center min-w-full border rounded-md overflow-hidden shadow-sm transition-shadow duration-300 hover:shadow-md">
                    <thead>
                        <tr class=" bg-gray-700">
                            <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                                Name</th>
                            <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                                Email</th>
                            <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                                Role</th>
                            <th class="py-2 px-4 border-b text-xs font-medium text-white uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class=" divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-red-100 transition-colors duration-300">
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->name }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->email }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->role ?? 'None' }}</td>
                                <td class="border border-gray-700 px-4 py-2">
                                    <form action="{{ route('user-management.updateRole', $user->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <!-- Hidden input to toggle the role -->
                                        <input type="hidden" name="role"
                                            value="{{ $user->role === 'admin' ? 'subadmin' : 'admin' }}">

                                        <button type="submit" class="rounded-full bg-[#9d1e18] p-2 hover:bg-red-500"
                                            title="Change Role">
                                            <img src="/svg/switch user.svg" alt="Change Role" class="h-5 w-5"
                                                style="filter: brightness(0) invert(1);">
                                        </button>
                                    </form>
                                    <form action="{{ route('user-management.removeRole', $user->id) }}" method="POST"
                                        class="inline-block mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-[#4b5563] p-2 hover:bg-[#6b7280]"
                                            title="Remove Access">
                                            <img src="/svg/remove access.svg" alt="Remove Access" class="h-5 w-5"
                                                style="filter: brightness(0) invert(1);">
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
