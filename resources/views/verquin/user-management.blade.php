@extends('verquin.layouts.app')

@section('content')

@if(session('success'))
    <div style="color: green; white-space: pre-line;">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div style="color: red; white-space: pre-line;">{{ session('error') }}</div>
@endif

{{-- Linux user creation form --}}
<h2 class="text-xl font-semibold mb-4">Create Linux User</h2>

<form id="linux-prompt-form" action="{{ route('user.create') }}" method="POST" autocomplete="off">
    @csrf

    <div id="step-server">
        <label>Select Server:</label>
        <select name="server" required>
            <option value="" disabled selected>Select Server</option>
            <option value="openvpn">OpenVPN</option>
            <option value="ftp">FTP</option>
            <option value="haproxy">HA-Proxy</option>
        </select>
        <button type="button" onclick="nextStep('server')">Enter</button>
    </div>

    <div id="step-username" style="display:none;">
        <input type="text" name="username" placeholder="Username" required autocomplete="off">
        <button type="button" onclick="nextStep('username')">Enter</button>
    </div>

    <div id="step-password" style="display:none;">
        <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
        <button type="button" onclick="nextStep('password')">Enter</button>
    </div>

    <div id="step-publickey" style="display:none;">
        <textarea name="public_key" placeholder="Paste SSH Public Key Here" required autocomplete="off"></textarea>
        <button type="submit">Execute</button>
    </div>
</form>

<hr class="my-8">

{{-- OpenVPN client creation form --}}
<h2 class="text-xl font-semibold mb-4">Create OpenVPN Client User</h2>

<form action="{{ route('openvpn.client.create') }}" method="POST" autocomplete="off" novalidate>
    @csrf

    <label for="client_name" class="block mb-2 font-medium">VPN Client Username</label>
    <input
        id="client_name"
        name="client_name"
        type="text"
        class="border border-gray-300 rounded p-2 w-full mb-4"
        placeholder="Enter new VPN client username"
        required
        autocomplete="off"
        value="{{ old('client_name') }}"
    >
    @error('client_name')
        <p class="text-red-600">{{ $message }}</p>
    @enderror

    <button
        type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >Create Client</button>
</form>

<script>
function nextStep(step) {
    if (step === 'server') {
        document.getElementById('step-username').style.display = 'block';
    } else if (step === 'username') {
        document.getElementById('step-password').style.display = 'block';
    } else if (step === 'password') {
        document.getElementById('step-publickey').style.display = 'block';
    }
}
</script>

@endsection
