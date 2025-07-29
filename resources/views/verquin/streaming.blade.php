@extends('verquin.layouts.app')

@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold mb-4">HAProxy Sensor Management</h2>
    <div class="bg-white shadow rounded-lg border p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Create Sensor --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Create HAProxy Sensor</h3>
                <form id="form-create-haproxy-sensor" action="{{ route('haproxy.sensor.create') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="sensor-name-create" class="form-label">Sensor Name:</label>
                        <input type="text" name="sensor_name" id="sensor-name-create" required class="form-input w-80" placeholder="Sensor Name">
                    </div>
                    <div>
                        <label for="frontend-port-create" class="form-label">Frontend Port:</label>
                        <input type="text" name="frontend_port" id="frontend-port-create" required class="form-input w-80" placeholder="e.g. 8080">
                    </div>
                    <div>
                        <label for="backend-ip-create" class="form-label">Backend IP:</label>
                        <input type="text" name="backend_ip" id="backend-ip-create" required class="form-input w-80" placeholder="e.g. 192.168.1.100">
                    </div>
                    <div>
                        <label for="backend-port-create" class="form-label">Backend Port:</label>
                        <input type="text" name="backend_port" id="backend-port-create" required class="form-input w-80" placeholder="e.g. 80">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-blue">Add Sensor</button>
                    </div>
                </form>
                <div id="log-create-haproxy-sensor" class="log-box hidden mt-4"></div>
            </div>

            {{-- Delete Sensor --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Delete HAProxy Sensor</h3>
                <form id="form-delete-haproxy-sensor" action="{{ route('haproxy.sensor.delete') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="sensor-name-delete" class="form-label">Sensor Name:</label>
                        <input type="text" name="sensor_name" id="sensor-name-delete" required class="form-input w-80" placeholder="Sensor Name">
                    </div>
                    <div>
                        <label for="frontend-port-delete" class="form-label">Frontend Port:</label>
                        <input type="text" name="frontend_port" id="frontend-port-delete" required class="form-input w-80" placeholder="e.g. 8080">
                    </div>
                    <div>
                        <label for="backend-ip-delete" class="form-label">Backend IP:</label>
                        <input type="text" name="backend_ip" id="backend-ip-delete" required class="form-input w-80" placeholder="e.g. 192.168.1.100">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-red">Delete Sensor</button>
                    </div>
                </form>
                <div id="log-delete-haproxy-sensor" class="log-box hidden mt-4"></div>
            </div>

        </div>
    </div>
</div>


<div class="mt-12 space-y-6">
    <h2 class="text-xl font-semibold mb-4">FTP Alerts Management</h2>
    <div class="bg-white shadow rounded-lg border p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Create FTP Sensor --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Create FTP Alert</h3>
                <form id="form-create-ftp-sensor" action="{{ route('ftp.sensor.create') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="ftp-sensor-name-create" class="form-label">Sensor Name:</label>
                        <input type="text" name="sensor_name" id="ftp-sensor-name-create" required class="form-input w-80" placeholder="Sensor Name">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-blue">Add FTP Alert</button>
                    </div>
                </form>
                <div id="log-create-ftp-sensor" class="log-box hidden mt-4"></div>
            </div>

            {{-- Delete FTP Sensor --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Delete FTP Alert</h3>
                <form id="form-delete-ftp-sensor" action="{{ route('ftp.sensor.delete') }}" method="POST" autocomplete="off" class="space-y-4">
                    @csrf
                    <div>
                        <label for="ftp-sensor-name-delete" class="form-label">Sensor Name:</label>
                        <input type="text" name="sensor_name" id="ftp-sensor-name-delete" required class="form-input w-80" placeholder="Sensor Name">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="form-button-red">Delete FTP Alert</button>
                    </div>
                </form>
                <div id="log-delete-ftp-sensor" class="log-box hidden mt-4"></div>
            </div>

        </div>
    </div>
</div>




{{-- JS --}}
<script>
const logIntervals = {};

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

setupFormListener(
    'form-create-haproxy-sensor',
    'log-create-haproxy-sensor',
    '{{ route('task.latest-haproxy-sensor-create-log') }}',
    'create_haproxy_sensor',
    'Create HAProxy Sensor'
);

setupFormListener(
    'form-delete-haproxy-sensor',
    'log-delete-haproxy-sensor',
    '{{ route('task.latest-haproxy-sensor-delete-log') }}',
    'delete_haproxy_sensor',
    'Delete HAProxy Sensor'
);
setupFormListener(
    'form-create-ftp-sensor',
    'log-create-ftp-sensor',
    '{{ route('task.latest-ftp-sensor-create-log') }}',
    'create_ftp_sensor',
    'Create FTP Sensor'
);

setupFormListener(
    'form-delete-ftp-sensor',
    'log-delete-ftp-sensor',
    '{{ route('task.latest-ftp-sensor-delete-log') }}',
    'delete_ftp_sensor',
    'Delete FTP Sensor'
);

</script>
@endsection

