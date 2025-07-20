@extends('verquin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-gray-800 dark:text-gray-200 text-center">
        <h1 style="font-size: 64px; font-weight: 800; margin-bottom: 1rem;">Welcome</h1>

        <p class="text-2xl font-light">Navigate to your desired tab</p>
    </div>

</div>
{{-- VPN Clients Table --}}
    <div class="mt-12 bg-white dark:bg-gray-900 shadow rounded-2xl p-6 overflow-x-auto">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-100 mb-4 flex items-center">
            🧑‍💻 OpenVPN Clients
        </h2>

        @if(count($vpnClients))
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($vpnClients as $index => $clientName)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $clientName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500 dark:text-gray-400">No OpenVPN clients found.</p>
        @endif
    </div>

@endsection

