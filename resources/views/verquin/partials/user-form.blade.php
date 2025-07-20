<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-700 dark:text-gray-300">
    <h3 class="text-lg font-semibold mb-2">{{ $title }}</h3>

    <form id="form-{{ $id }}" action="{{ $route }}" method="POST" class="space-y-4" autocomplete="off">
        @csrf

        @if (!empty($hasServer))
            <div>
                <label class="block text-sm font-medium mb-1">Select Server</label>
                <select name="server"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-gray-200" required>
                    <option value="" disabled selected>Server</option>
                    @foreach ($servers as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" required placeholder="Username"
                class="border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-gray-200">
        </div>

        @if (!empty($hasPassword))
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required placeholder="Password"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-gray-200">
                <input type="password" name="password_confirmation" required placeholder="Confirm Password"
                    class="mt-2 border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-gray-200">
            </div>
        @endif

        @if (!empty($hasSSHKey))
            <div>
                <label class="block text-sm font-medium mb-1">SSH Public Key</label>
                <textarea name="public_key" rows="3" required placeholder="Paste SSH Public Key"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-gray-200"></textarea>
            </div>
        @endif

        <button type="submit"
            class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition">
            {{ Str::contains($title, 'Delete') ? 'Delete' : 'Create' }}
        </button>
    </form>

    <div id="log-{{ $id }}" class="log-output" style="display:none;"></div>
</div>
