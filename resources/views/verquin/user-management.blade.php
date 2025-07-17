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

<div class="max-w-md bg-white shadow rounded-xl p-6 space-y-4">
    <form id="linux-prompt-form" action="{{ route('user.create') }}" method="POST" autocomplete="off" class="space-y-4">
        @csrf

        <div>
            <label for="server" class="block text-sm font-medium mb-1">Select Server:</label>
            <select name="server" id="server" required
                class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400">
                <option value="" disabled selected>Select Server</option>
                <option value="openvpn">OpenVPN</option>
                <option value="ftp">FTP</option>
                <option value="haproxy">HA-Proxy</option>
            </select>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium mb-1">Username:</label>
            <input type="text" name="username" id="username" placeholder="Username" required autocomplete="off"
                class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password:</label>
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password"
                class="border border-gray-300 rounded-md px-3 py-2 w-full mb-2 focus:ring focus:ring-blue-200 focus:border-blue-400">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password"
                class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400">
        </div>

        <div>
            <label for="public_key" class="block text-sm font-medium mb-1">SSH Public Key:</label>
            <textarea name="public_key" id="public_key" placeholder="Paste SSH Public Key Here" required rows="3"
                class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400"></textarea>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 transition shadow">
                Execute
            </button>
        </div>
    </form>
</div>


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
        class="border border-gray-300 rounded p-2 w-full mb-2"
        placeholder="Enter new VPN client username"
        required
        autocomplete="off"
        value="{{ old('client_name') }}"
    >
    @error('client_name')
        <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
    @enderror

    <button
        type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
    >Create Client</button>
</form>
{{-- Linux user deletion form --}}
{{-- Linux user deletion form --}}
<h2 class="text-xl font-semibold mb-4">Delete Linux User</h2>

<div class="max-w-md bg-white shadow rounded-xl p-6 space-y-4">
    <form id="delete-linux-user-form" action="{{ route('user.delete') }}" method="POST" autocomplete="off" class="space-y-4">
        @csrf

        <div>
            <label for="server" class="block text-sm font-medium mb-1">Select Server:</label>
            <select name="server" id="server" required
    class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400">
    <option value="" disabled {{ old('server') ? '' : 'selected' }}>Select Server</option>
    <option value="openvpn" {{ old('server') === 'openvpn' ? 'selected' : '' }}>OpenVPN</option>
    <option value="ftp" {{ old('server') === 'ftp' ? 'selected' : '' }}>FTP</option>
    <option value="haproxy" {{ old('server') === 'haproxy' ? 'selected' : '' }}>HA-Proxy</option>
</select>

        </div>

        <div>
            <label for="username" class="block text-sm font-medium mb-1">Username:</label>
            <input type="text" name="username" id="username" placeholder="Username" required autocomplete="off"
                class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400">
        </div>

        <div class="pt-2">
            <button type="submit"
                class="bg-red-600 text-white text-sm px-4 py-2 rounded-md hover:bg-red-700 transition shadow">
                Delete User
            </button>
        </div>
    </form>
</div>


{{-- Polling feedback for OpenVPN client status --}}
<div id="vpn-status-msg" class="mt-4 font-medium whitespace-pre-line"></div>



@endsection
