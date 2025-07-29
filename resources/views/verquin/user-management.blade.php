@extends('verquin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-12">

    {{-- Linux User Management --}}
    <div class="space-y-6">
        <h2 class="text-xl font-semibold mb-4">Linux User Management</h2>
        <div class="bg-white shadow rounded-lg border p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Create Linux User --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4">Create Linux User</h3>
                    <form id="form-create-linux-user" action="{{ route('user.create') }}" method="POST" autocomplete="off" class="space-y-4">
                        @csrf
                        <div>
                            <label for="server-create" class="form-label">Select Server:</label>
                            <select name="server" id="server-create" required class="form-select w-80">
                                <option value="" disabled selected>Server</option>
                                <option value="openvpn">OpenVPN</option>
                                <option value="ftp">FTP</option>
                                <option value="haproxy">HA-Proxy</option>
                            </select>
                        </div>
                        <div>
                            <label for="username-create" class="form-label">Username:</label>
                            <input type="text" name="username" id="username-create" required class="form-input w-80" placeholder="Username" autocomplete="new-username">
                        </div>
                        <div>
                            <label class="form-label">Password:</label>
                            <input type="password" name="password" required class="form-input w-80" placeholder="Password">
                            <input type="password" name="password_confirmation" required class="form-input w-80 mt-2" placeholder="Confirm Password" autocomplete="new-password">
                        </div>
                        <div>
                            <label for="ssh-key" class="form-label">SSH Public Key:</label>
                            <textarea name="public_key" id="ssh-key" required class="form-input w-80" rows="3" placeholder="Paste SSH Public Key Here"></textarea>
                        </div>
                        <div class="pt-2">
    <button type="submit" class="form-button-blue">Create User</button>
</div>

                    </form>
                    <div id="log-create-linux-user" class="log-box hidden mt-4"></div>
                </div>

                {{-- Delete Linux User --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4">Delete Linux User</h3>
                    <form id="form-delete-linux-user" action="{{ route('user.delete') }}" method="POST" autocomplete="off" class="space-y-4">
                        @csrf
                        <div>
                            <label for="server-delete" class="form-label">Select Server:</label>
                            <select name="server" id="server-delete" required class="form-select w-80">
                                <option value="" disabled {{ old('server') ? '' : 'selected' }}>Server</option>
                                <option value="openvpn" {{ old('server') === 'openvpn' ? 'selected' : '' }}>OpenVPN</option>
                                <option value="ftp" {{ old('server') === 'ftp' ? 'selected' : '' }}>FTP</option>
                                <option value="haproxy" {{ old('server') === 'haproxy' ? 'selected' : '' }}>HA-Proxy</option>
                            </select>
                        </div>
                        <div>
                            <label for="username-delete" class="form-label">Username:</label>
                            <input type="text" name="username" id="username-delete" required class="form-input w-80" placeholder="Username">
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="form-button-red">Delete User</button>
                        </div>
                    </form>
                    <div id="log-delete-linux-user" class="log-box hidden mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- OpenVPN Client Management --}}
    <div class="space-y-6">
        <h2 class="text-xl font-semibold mb-4">OpenVPN Client Management</h2>
        <div class="bg-white shadow rounded-lg border p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Create OpenVPN Client User --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4">Create OpenVPN Client User</h3>
                    <form id="form-create-openvpn" action="{{ route('openvpn.client.create') }}" method="POST" autocomplete="off" class="space-y-4">
                        @csrf
                        <div>
                            <label for="vpn-client-create" class="form-label">VPN Client Username</label>
                            <input type="text" id="vpn-client-create" name="client_name" required class="form-input w-80" value="{{ old('client_name') }}" placeholder="Enter new VPN client username">
                            @error('client_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="form-button-blue">Create Client</button>
                        </div>
                    </form>
                    <div id="log-create-openvpn" class="log-box hidden mt-4"></div>
                </div>

                {{-- Delete OpenVPN Client User --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4">Delete OpenVPN Client User</h3>
                    <form id="form-delete-openvpn" action="{{ route('openvpn.client.delete') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="vpn-client-delete" class="form-label">VPN Client Username</label>
                            <input type="text" id="vpn-client-delete" name="client_name" required class="form-input w-80" placeholder="Enter VPN client username to delete">
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="form-button-red">Delete Client</button>
                        </div>
                    </form>
                    <div id="log-delete-openvpn" class="log-box hidden mt-4"></div>
                </div>
            </div>
            {{-- Create VPN Config File --}}
<div>
    <h3 class="text-lg font-semibold mb-4">Create VPN Config File</h3>
    <form id="form-create-vpn-file" action="{{ route('vpn.file.create') }}" method="POST" autocomplete="off" class="space-y-4">
    @csrf
    <input type="hidden" name="server" value="openvpn">
    <div>
        <label for="vpn-file-create" class="form-label">VPN Config Filename</label>
        <input type="text" id="vpn-file-create" name="client_name" required class="form-input w-80" placeholder="Enter client name">
        @error('client_name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="pt-2">
        <button type="submit" class="form-button-blue">Create File</button>
    </div>
</form>
    <div id="log-create-vpn-file" class="log-box hidden mt-4"></div>
</div>

{{-- Delete VPN Config File --}}
<div>
    <h3 class="text-lg font-semibold mb-4">Delete VPN Config File</h3>
    <form id="form-delete-vpn-file" action="{{ route('vpn.file.delete') }}" method="POST" autocomplete="off" class="space-y-4">
    @csrf
    <input type="hidden" name="server" value="openvpn">
    <div>
        <label for="vpn-file-delete" class="form-label">VPN Config Filename</label>
        <input type="text" id="vpn-file-delete" name="client_name" required class="form-input w-80" placeholder="Enter client name to delete">
    </div>
    <div class="pt-2">
        <button type="submit" class="form-button-red">Delete File</button>
    </div>
</form>

    <div id="log-delete-vpn-file" class="log-box hidden mt-4"></div>
</div>

        </div>
    </div>

</div>

{{-- OpenVPN Clients Table --}}
    
    <div class="mt-12 bg-white dark:bg-gray-900 shadow rounded-2xl p-6 overflow-x-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-4 md:space-y-0">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                OpenVPN Clients
            </h2>

            <div>
                <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Search clients..."
                    autocomplete="off"
                    autocorrect="off"
                    spellcheck="false"
                    class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100"
                />
            </div>
        </div>

        @if (!empty($vpnClients))
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left" id="vpn-table">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($vpnClients as $index => $clientName)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100 vpn-client-name">{{ $clientName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500 dark:text-gray-400">No OpenVPN clients found.</p>
        @endif
    </div>
    
{{-- JavaScript --}}
<script>
    const logIntervals = {};

    document.querySelectorAll('#form-delete-linux-user, #form-delete-openvpn').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm("Are you sure you want to delete this user?")) {
                e.preventDefault();
            }
        });
    });

    function setLogText(logEl, message) {
        logEl.classList.remove('text-green-700', 'text-red-700', 'text-gray-700');
        logEl.classList.add('text-gray-700');
        logEl.textContent = message;

        const lower = message.toLowerCase();
        if (lower.includes('success') || lower.includes('created') || lower.includes('done')) {
            logEl.classList.replace('text-gray-700', 'text-green-700');
        } else if (lower.includes('error') || lower.includes('fail') || lower.includes('not')) {
            logEl.classList.replace('text-gray-700', 'text-red-700');
        }
    }

    async function fetchLog(route, logEl, type) {
        try {
            const res = await fetch(route);
            const txt = await res.text();
            setLogText(logEl, txt || 'Pending...');
        } catch (err) {
            setLogText(logEl, `⚠️ Error fetching ${type} log.`);
        }
    }

    function setupFormListener(formId, logId, route, intervalKey, type) {
        const form = document.getElementById(formId);
        const logEl = document.getElementById(logId);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            logEl.classList.remove('hidden');
            setLogText(logEl, 'Pending...');

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
                    setLogText(logEl, `⚠️ ${type} request failed.`);
                    return;
                }

                setTimeout(() => {
                    fetchLog(route, logEl, type);
                    logIntervals[intervalKey] = setInterval(() => fetchLog(route, logEl, type), 5000);
                }, 1500);
            } catch (err) {
                console.error(`${type} request error:`, err);
                setLogText(logEl, `⚠️ ${type} request error.`);
            }
        });
    }

    setupFormListener('form-create-linux-user', 'log-create-linux-user', '{{ route('task.latest-create-log') }}', 'create_linux', 'Create Linux');
    setupFormListener('form-delete-linux-user', 'log-delete-linux-user', '{{ route('task.latest-delete-log') }}', 'delete_linux', 'Delete Linux');
    setupFormListener('form-create-openvpn', 'log-create-openvpn', '{{ route('task.latest-openvpn-create-log') }}', 'create_ovpn', 'Create OVPN');
    setupFormListener('form-delete-openvpn', 'log-delete-openvpn', '{{ route('task.latest-openvpn-delete-log') }}', 'delete_ovpn', 'Delete OVPN');
    setupFormListener('form-create-vpn-file', 'log-create-vpn-file', '{{ route('task.latest-vpn-file-create-log') }}', 'create_vpn_file', 'Create VPN File');
setupFormListener('form-delete-vpn-file', 'log-delete-vpn-file', '{{ route('task.latest-vpn-file-delete-log') }}', 'delete_vpn_file', 'Delete VPN File');

</script>
@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const rows = document.querySelectorAll('#vpn-table tbody tr');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                const name = row.querySelector('.vpn-client-name').textContent.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>

@endpush