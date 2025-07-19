 <div x-data="{ showForm: false }">
<x-app-layout>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
                <button @click="showForm = !showForm"
                        class="bg-gray-200 dark:bg-gray-700 text-sm px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    + Add User
                </button>
            </div>
        </x-slot>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 text-green-600 dark:text-green-400 font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Add User Form (Hidden by Default) -->
            <div x-show="showForm" x-transition
                 class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6"
                 @click.outside="showForm = false">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Create New User</h3>
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Name</label>
                            <input type="text" name="name" autocomplete="off" required
                                   class="w-full p-2 border rounded dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Email</label>
                            <input type="email" name="email" autocomplete="off" required
                                   class="w-full p-2 border rounded dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Password</label>
                            <input type="password" name="password" autocomplete="new-password" required
                                   class="w-full p-2 border rounded dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password" required
                                   class="w-full p-2 border rounded dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Create User
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
<thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
    <tr>
        <th class="px-4 py-3">Name</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3 text-center">Approve</th>
        <th class="px-4 py-3 text-center">Role</th>
        <th class="px-4 py-3 text-center">Actions</th> {{-- Only promote action --}}
        <th class="px-4 py-3 text-center">Delete</th> {{-- Separate delete column --}}
    </tr>
</thead>
<tbody>
@foreach ($users as $user)
    <tr class="border-b border-gray-200 dark:border-gray-700">
        <td class="px-4 py-2">{{ $user->name }}</td>
        <td class="px-4 py-2">{{ $user->email }}</td>
        <td class="px-4 py-2 text-center">
            <form method="POST" action="{{ route('users.approve', $user) }}">
                @csrf
                <input type="checkbox"
                       onchange="this.form.submit()"
                       {{ $user->approved ? 'checked disabled' : '' }}>
            </form>
        </td>
        <td class="px-4 py-2 text-center">
            @if ($user->is_admin)
                <span class="text-green-600 font-semibold">Admin</span>
            @else
                <span class="text-gray-500">User</span>
            @endif
        </td>
        <td class="px-4 py-2 text-center">
            @if (!$user->is_admin && $user->id !== auth()->id())
                <form method="POST" action="{{ route('users.makeAdmin', $user) }}">
                    @csrf
                    <button type="submit" class="text-yellow-500 hover:text-yellow-700">
                        🔑 Make Admin
                    </button>
                </form>
            @else
                -- {{-- Show dash when user is already admin or is the logged-in user --}}
            @endif
        </td>
        <td class="px-4 py-2 text-center">
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700">
                        🗑️
                    </button>
                </form>
            @else
                --
            @endif
        </td>
    </tr>
@endforeach
</tbody>


                </table>
            </div>
        </div>
</x-app-layout>
</div>