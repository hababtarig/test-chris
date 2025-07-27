@extends('verquin.layouts.app')
@section('content')
{{-- OpenVPN Device Management --}}
 {{-- <div class="space-y-6">
    <h2 class="text-xl font-semibold mb-4">OpenVPN Device Management</h2>
    <div class="bg-white shadow rounded-lg border p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Create Device Credentials --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Create Device Credentials</h3>
                <form id="form-create-vpn-device" action="{{ route('vpn-device-create') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="device-name-create" class="form-label">Device Name:</label>
                        <input type="text" name="device_name" id="device-name-create" required class="form-input w-80" placeholder="Device Name">
                    </div>
                    <div>
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" required class="form-input w-80" placeholder="Password">
                        <input type="password" name="password_confirmation" required class="form-input w-80 mt-2" placeholder="Confirm Password">
                    </div>
                    <div>
                        <label for="config" class="form-label">Additional Config (optional):</label>
                        <textarea name="config" id="config" class="form-input w-80" rows="3" placeholder="Any extra configuration"></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-blue">Create Device</button>
                    </div>
                </form>
                <div id="log-create-vpn-device" class="log-box hidden mt-4"></div>
            </div>

            {{-- Delete Device Credentials --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Delete Device Credentials</h3>
                <form id="form-delete-vpn-device" action="{{ route('vpn-device-delete') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="device-name-delete" class="form-label">Device Name:</label>
                        <input type="text" name="device_name" id="device-name-delete" required class="form-input w-80" placeholder="Device Name">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-red">Delete Device</button>
                    </div>
                </form>
                <div id="log-delete-vpn-device" class="log-box hidden mt-4"></div>
            </div>

        </div>
    </div>
</div>
{{-- JavaScript --}}
<script>
    const logIntervals = {};

    document.querySelectorAll('#form-delete-vpn-device').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm("Are you sure you want to delete this device?")) {
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

    setupFormListener('form-create-vpn-device', 'log-create-vpn-device', '{{ route('task.latest-vpn-device-create-log') }}', 'create_vpn_device', 'Create VPN Device');
    setupFormListener('form-delete-vpn-device', 'log-delete-vpn-device', '{{ route('task.latest-vpn-device-delete-log') }}', 'delete_vpn_device', 'Delete VPN Device');
</script> --}}
@endsection

