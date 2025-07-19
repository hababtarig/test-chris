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
     class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm text-green-700 font-mono whitespace-pre-wrap mt-2"></div>
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
         class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm text-green-700 font-mono whitespace-pre-wrap mt-2"></div>
</div>
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
             class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm text-red-700 font-mono whitespace-pre-wrap mt-2"></div>
    </div>

</div>

<script>
//delete linux user status 
let deleteLogInterval = null;

async function fetchDeleteErrorLog() {
    try {
        const response = await fetch('{{ route('task.latest-delete-log') }}');
        const text = await response.text();
        document.getElementById('delete-error-log').textContent = text || 'Pending...';
    } catch (err) {
        console.error('Failed to fetch deletion log:', err);
        document.getElementById('delete-error-log').textContent = '⚠️ Error loading deletion log.';
    }
}

document.getElementById('delete-linux-user-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    document.getElementById('delete-error-log').style.display = 'block';
    document.getElementById('delete-error-log').textContent = 'Pending...';

    if (deleteLogInterval) clearInterval(deleteLogInterval);
    fetchDeleteErrorLog(); // initial fetch
    deleteLogInterval = setInterval(fetchDeleteErrorLog, 5000);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (response.ok) {
            console.log('Delete request sent successfully');
            // optionally, show a success message or keep polling
        } else {
            console.error('Delete request failed:', response.status);
            document.getElementById('delete-error-log').textContent = '⚠️ Delete request failed.';
        }
    } catch (err) {
        console.error('Request error:', err);
        document.getElementById('delete-error-log').textContent = '⚠️ Request failed.';
    }
});

//create linux user status
let createLogInterval = null;

async function fetchCreateUserLog() {
    try {
        const response = await fetch('{{ route('task.latest-create-log') }}');
        const text = await response.text();
        document.getElementById('create-user-log').textContent = text || 'Pending...';
    } catch (err) {
        console.error('Failed to fetch create log:', err);
        document.getElementById('create-user-log').textContent = '⚠️ Error loading create log.';
    }
}

document.getElementById('linux-prompt-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    document.getElementById('create-user-log').style.display = 'block';
    document.getElementById('create-user-log').textContent = 'Pending...';

    if (createLogInterval) clearInterval(createLogInterval);
    fetchCreateUserLog(); // initial fetch
    createLogInterval = setInterval(fetchCreateUserLog, 5000);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (response.ok) {
            console.log('Create request sent successfully');
        } else {
            console.error('Create request failed:', response.status);
            document.getElementById('create-user-log').textContent = '⚠️ Create request failed.';
        }
    } catch (err) {
        console.error('Request error:', err);
        document.getElementById('create-user-log').textContent = '⚠️ Request failed.';
    }
});

//ovpn creds creation
let openVpnLogInterval = null;

async function fetchOpenVpnCreateLog() {
    try {
        const response = await fetch('{{ route('task.latest-openvpn-create-log') }}');
        const text = await response.text();
        document.getElementById('openvpn-create-log').textContent = text || 'Pending...';
    } catch (err) {
        console.error('Failed to fetch OpenVPN create log:', err);
        document.getElementById('openvpn-create-log').textContent = '⚠️ Error loading OpenVPN create log.';
    }
}

document.getElementById('openvpn-client-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const logDiv = document.getElementById('openvpn-create-log');
    logDiv.style.display = 'block';
    logDiv.textContent = 'Pending...';

    if (openVpnLogInterval) clearInterval(openVpnLogInterval);
    fetchOpenVpnCreateLog(); // initial fetch
    openVpnLogInterval = setInterval(fetchOpenVpnCreateLog, 5000);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (response.ok) {
            console.log('OpenVPN client create request sent successfully');
        } else {
            console.error('OpenVPN client create request failed:', response.status);
            logDiv.textContent = '⚠️ OpenVPN create request failed.';
        }
    } catch (err) {
        console.error('OpenVPN client create request error:', err);
        logDiv.textContent = '⚠️ Request failed.';
    }
});

</script>

@endsection
