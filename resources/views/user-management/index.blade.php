<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-black dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white dark:text-gray-100">User Management</h3>

                <!-- Add User Form -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold mb-2 text-white dark:text-gray-100">Add New User</h4>
                    <form action="{{ route('user-management.addUser') }}" method="POST" class="flex items-center space-x-4">
                        @csrf
                        <input type="email" name="email" placeholder="Enter USeP Email" required
                            class="px-4 py-2 rounded border dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-black rounded">Add User</button>
                    </form>
                    @if (session('error'))
                        <p class="mt-2 text-red-500">{{ session('error') }}</p>
                    @endif
                </div>

                <table class="table-auto w-full border dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700 text-left">
                            <th class="px-4 py-2 border dark:border-gray-600 text-gray-800 dark:text-gray-100">Name</th>
                            <th class="px-4 py-2 border dark:border-gray-600 text-gray-800 dark:text-gray-100">Email</th>
                            <th class="px-4 py-2 border dark:border-gray-600 text-gray-800 dark:text-gray-100">Role</th>
                            <th class="px-4 py-2 border dark:border-gray-600 text-gray-800 dark:text-gray-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="bg-white dark:bg-gray-900">
                                <td class="border dark:border-gray-700 px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->email }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->role ?? 'None' }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">
                                    <form action="{{ route('user-management.updateRole', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <select name="role" class="border dark:border-gray-700 rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                            <option value="">Select Role</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="subadmin" {{ $user->role === 'subadmin' ? 'selected' : '' }}>Sub Admin</option>
                                        </select>
                                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-black rounded">Update</button>
                                    </form>
                                    <form action="{{ route('user-management.removeRole', $user->id) }}" method="POST" class="inline-block mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-black rounded">Remove Role</button>
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
