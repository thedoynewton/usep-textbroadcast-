<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-lg md:text-xl text-white leading-tight">
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
                timer: 2000
            });
        @endif

        // Check if there's an error message in the session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                showConfirmButton: true
            });
        @endif
    </script>

    <div class="container mx-auto p-6">
        <!-- Add User Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl sm:text-lg md:text-xl font-bold text-gray-800 mb-4">Add New User</h2>
            <form action="{{ route('user-management.addUser') }}" method="POST" class="flex flex-wrap items-center gap-4">
                @csrf
                <div class="flex-1">
                    <label for="email"
                        class="block text-sm sm:text-xs md:text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" placeholder="juandelacruz12345@usep.edu.ph" required
                        class="mt-2 w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="text-xs sm:text-[10px] md:text-xs text-gray-500 mt-1">Only USeP employee emails are
                        accepted.</p>
                </div>
                <div class="flex space-x-4">
                    <button type="reset" class="px-4 py-2 bg-[#4b5563] p-2 hover:bg-[#6b7280] text-white rounded-md">
                        Clear Fields
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#9d1e18] p-2 hover:bg-red-500 text-white rounded-md">
                        Add User
                    </button>
                </div>
            </form>
        </div>

        <!-- User List Section -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl sm:text-lg md:text-xl font-bold text-gray-800 mb-4">List of Users</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-600">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs sm:text-[10px] md:text-xs font-medium text-white uppercase tracking-wider">
                                Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs sm:text-[10px] md:text-xs font-medium text-white uppercase tracking-wider">
                                Email
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs sm:text-[10px] md:text-xs font-medium text-white uppercase tracking-wider">
                                Role
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs sm:text-[10px] md:text-xs font-medium text-white uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 bg-white">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm sm:text-xs md:text-sm text-gray-700">{{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm sm:text-xs md:text-sm text-gray-700">{{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-sm sm:text-xs md:text-sm text-gray-700">
                                    {{ $user->role ?? 'None' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <!-- Only show actions if user is not logged in user -->
                                    @if (Auth::user()->id !== $user->id)
                                        <form action="{{ route('user-management.updateRole', $user->id) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            <!-- Hidden input to toggle the role -->
                                            <input type="hidden" name="role"
                                                value="{{ $user->role === 'admin' ? 'subadmin' : 'admin' }}">
                                            <button type="submit"
                                                class="rounded-full bg-[#9d1e18] p-2 hover:bg-red-500"
                                                title="Change Role">
                                                <img src="/svg/switch user.svg" alt="Change Role" class="h-5 w-5"
                                                    style="filter: brightness(0) invert(1);">
                                            </button>
                                        </form>
                                        <form action="{{ route('user-management.removeRole', $user->id) }}"
                                            method="POST" class="inline-block mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-full bg-[#4b5563] p-2 hover:bg-[#6b7280]"
                                                title="Remove Access">
                                                <img src="/svg/remove access.svg" alt="Remove Access" class="h-5 w-5"
                                                    style="filter: brightness(0) invert(1);">
                                            </button>
                                        </form>
                                    @else
                                        <!-- Optionally, you can display a message or hide the actions -->
                                        <span class="text-sm sm:text-xs md:text-sm text-gray-400">Actions
                                            disabled</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
