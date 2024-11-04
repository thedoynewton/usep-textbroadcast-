<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="bg-white p-6 rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Display success message -->
                @if (session('success'))
                    <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 2000)" x-show="open"
                        class="fixed inset-0 flex items-center justify-center z-50">
                        <div class="bg-black bg-opacity-50 absolute inset-0 backdrop-blur-sm"></div>
                        <div class="bg-green-500 text-white px-6 py-4 rounded-md shadow-lg z-10">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <!-- Display error message -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Display error like the success message -->

                {{-- @if (session('error'))
                <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 2500)" x-show="open"
                    class="fixed inset-0 flex items-center justify-center z-50">
                    <div class="bg-black bg-opacity-50 absolute inset-0 backdrop-blur-sm"></div>
                    <div class="bg-red-500 text-white px-6 py-4 rounded-md shadow-lg z-10">
                        {{ session('success') }}
                    </div>
                </div>
                @endif --}}

                <!-- Add User Form -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Add New User</h2>
                    <form action="{{ route('user-management.addUser') }}" method="POST"
                        class="flex items-center space-x-4">
                        @csrf
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" placeholder="juandelacruz12345@usep.edu.ph" required
                            class="mt-2 w-full h-10 pl-3 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 hover:border-indigo-500" />
                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-red rounded">Add
                            User</button>
                        <h3 class="mt-2 text-xs text-gray-500 text-opacity-35">Only Employee's USeP emails
                            are accepted .</h3>
                    </form>
                </div>

                <h2 class="text-2xl font-bold mb-6 text-center sm:text-left" style="color: var(--primary-color);">
                    List of Users
                </h2>

                <table
                    class="min-w-full bg-white border rounded-md overflow-hidden shadow-sm transition-shadow duration-300 hover:shadow-md">
                    <thead class="bg-gray-50">
                        <tr class=" dark:bg-gray-700 text-left">
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100 transition-colors duration-300">
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->name }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->email }}</td>
                                <td class="py-2 px-4 text-xs text-gray-700">
                                    {{ $user->role ?? 'None' }}</td>
                                <td class="border dark:border-gray-700 px-4 py-2">
                                    <form action="{{ route('user-management.updateRole', $user->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <!-- Hidden input to toggle the role -->
                                        <input type="hidden" name="role"
                                            value="{{ $user->role === 'admin' ? 'subadmin' : 'admin' }}">

                                        <button type="submit" class="rounded-full bg-[#9d1e18] p-2 hover:bg-yellow-500"
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
