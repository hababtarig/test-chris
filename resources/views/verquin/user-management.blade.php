@extends('verquin.layouts.app')

@section('content')

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-8">

    {{-- Create Linux User --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-700 dark:text-gray-300">

        <h3 class="text-lg font-semibold mb-2">Create Linux User</h3>
        <form id="linux-prompt-form" action="{{ route('user.create') }}" method="POST" autocomplete="off"  class="space-y-4">
            @csrf
            <div>
                <label for="server" class="block text-sm font-medium mb-1">Select Server:</label>
                <select name="server" id="server" required
                    class="border border-gray-300 rounded-md px-3 py-2 w-96 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="" disabled selected>Server</option>
                    <option value="openvpn">OpenVPN</option>
                    <option value="ftp">FTP</option>
                    <option value="haproxy">HA-Proxy</option>
                </select>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required autocomplete="off"      
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password:</label>
                <input type="password" name="password" placeholder="Password" required autocomplete="new-password"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            </div>
            <div>
                <label for="public_key" class="block text-sm font-medium mb-1">SSH Public Key:</label>
                <textarea name="public_key" id="public_key" placeholder="Paste SSH Public Key Here" required rows="3"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit"
                    class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition shadow">
                    Create User
                </button>
            </div>
        </form>
        <div id="create-user-log" style="display: none;" 
     class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm font-mono whitespace-pre-wrap mt-2 text-gray-700"></div>
    </div>

    {{-- Create OpenVPN Client User --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-700 dark:text-gray-300">
        <h3 class="text-lg font-semibold mb-2">Create OpenVPN Client User</h3>
        <form id="openvpn-client-form" action="{{ route('openvpn.client.create') }}" method="POST" autocomplete="off" class="space-y-4">
            @csrf
            <div>
                <label for="client_name" class="block text-sm font-medium mb-1">VPN Client Username</label>
                <input
                    id="client_name"
                    name="client_name"
                    type="text"      
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                    placeholder="Enter new VPN client username"
                    required
                    autocomplete="off"
                    value="{{ old('client_name') }}"
                >
                @error('client_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="pt-2">
                <button
                    type="submit"
                    class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition shadow"
                >Create Client</button>
            </div>
        </form>
        <div id="openvpn-create-log" style="display: none;"
         class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm font-mono whitespace-pre-wrap mt-2 text-gray-700"></div>
</div>
    </div>

    {{-- Delete OpenVPN Client User --}}
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-700 dark:text-gray-300">
    <h3 class="text-lg font-semibold mb-2">Delete OpenVPN Client User</h3>
    <form id="openvpn-delete-form" action="{{ route('openvpn.client.delete') }}" method="POST" class="space-y-4">

        @csrf
        <div>
            <label for="client_name" class="block text-sm font-medium mb-1">VPN Client Username</label>
            <input
                id="client_name"
                name="client_name"
                type="text"
                class="border border-gray-300 rounded-md px-3 py-2 w-80 dark:bg-gray-700 dark:text-white"
                placeholder="Enter VPN client username to delete"
                required
            >
        </div>
        <div>
            <button
                type="submit"
                class="bg-red-600 text-white text-sm px-4 py-2 rounded-md hover:bg-red-700 transition"
            >Delete Client</button>
        </div>
    </form>
    <div id="openvpn-delete-log" style="display: none;"
     class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm font-mono whitespace-pre-wrap mt-2 text-gray-700"></div>

</div>


    {{-- Delete Linux User --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-700 dark:text-gray-300">
        <h3 class="text-lg font-semibold mb-2">Delete Linux User</h3>
        <form id="delete-linux-user-form" action="{{ route('user.delete') }}" method="POST" autocomplete="off" class="space-y-4">
            @csrf
            <div>
                <label for="server" class="block text-sm font-medium mb-1">Select Server:</label>
                <select name="server" id="server" required
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="" disabled {{ old('server') ? '' : 'selected' }}>Server</option>
                    <option value="openvpn" {{ old('server') === 'openvpn' ? 'selected' : '' }}>OpenVPN</option>
                    <option value="ftp" {{ old('server') === 'ftp' ? 'selected' : '' }}>FTP</option>
                    <option value="haproxy" {{ old('server') === 'haproxy' ? 'selected' : '' }}>HA-Proxy</option>
                </select>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required autocomplete="off"
                    class="border border-gray-300 rounded-md px-3 py-2 w-80 focus:ring focus:ring-blue-200 focus:border-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            </div>
            <div class="pt-2">
                <button type="submit"
                    class="bg-red-600 text-white text-sm px-4 py-2 rounded-md hover:bg-red-700 transition shadow">
                    Delete User
                </button>
            </div>
        </form>
        <div id="delete-error-log" style="display: none;" 
             class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm font-mono whitespace-pre-wrap mt-2 text-gray-700"></div>
    </div>

</div>

<script>
    document.querySelectorAll('#delete-linux-user-form, #openvpn-delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                e.preventDefault();
            }
        });
    });

function setLogText(logDiv, text) {
    logDiv.textContent = text;

    // Reset classes
    logDiv.classList.remove('text-green-700', 'text-red-700');
    logDiv.classList.add('text-gray-700');

    const lower = text.toLowerCase();
    if (lower.includes('success') || lower.includes('created') || lower.includes('done')) {
        logDiv.classList.remove('text-gray-700');
        logDiv.classList.add('text-green-700');
    } else if (lower.includes('error') || lower.includes('failed') || lower.includes('fail') || lower.includes('not')) {
        logDiv.classList.remove('text-gray-700');
        logDiv.classList.add('text-red-700');
    }
}

let logIntervals = {
    create: null,
    delete: null,
    ovpnCreate: null,
    ovpnDelete: null
};

async function fetchLog(route, logId, type) {
    try {
        const res = await fetch(route);
        const text = await res.text();
        const logDiv = document.getElementById(logId);
        setLogText(logDiv, text || 'Pending...');
    } catch (err) {
        console.error(`Failed to fetch ${type} log:`, err);
        setLogText(document.getElementById(logId), `⚠️ Error loading ${type} log.`);
    }
}

function setupFormListener(formId, route, logId, intervalKey, type) {
    const form = document.getElementById(formId);
    const logDiv = document.getElementById(logId);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Clear and show empty log
        logDiv.style.display = 'block';
        setLogText(logDiv, 'Pending...');

        // Clear previous polling
        if (logIntervals[intervalKey]) {
            clearInterval(logIntervals[intervalKey]);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                setLogText(logDiv, `⚠️ ${type} request failed.`);
                return;
            }

            // Start polling AFTER request goes through (1.5s delay to avoid old log)
            setTimeout(() => {
                fetchLog(route, logId, type);
                logIntervals[intervalKey] = setInterval(() => {
                    fetchLog(route, logId, type);
                }, 5000);
            }, 1500);

        } catch (err) {
            setLogText(logDiv, `⚠️ ${type} request error.`);
            console.error(`${type} request error:`, err);
        }
    });
}

// Assign listeners
setupFormListener('linux-prompt-form', '{{ route('task.latest-create-log') }}', 'create-user-log', 'create', 'Create');
setupFormListener('delete-linux-user-form', '{{ route('task.latest-delete-log') }}', 'delete-error-log', 'delete', 'Delete');
setupFormListener('openvpn-client-form', '{{ route('task.latest-openvpn-create-log') }}', 'openvpn-create-log', 'ovpnCreate', 'OpenVPN Create');
setupFormListener('openvpn-delete-form', '{{ route('task.latest-openvpn-delete-log') }}', 'openvpn-delete-log', 'ovpnDelete', 'OpenVPN Delete');
</script>


@endsection
