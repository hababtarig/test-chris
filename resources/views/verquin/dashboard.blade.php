@extends('verquin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

    {{-- Welcome Section --}}
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-12 text-gray-800 dark:text-gray-100 text-center">
        <h1 class="text-5xl sm:text-6xl font-extrabold mb-4 tracking-tight">Welcome</h1>
    </div>

    {{-- OpenVPN Clients Table --}}
    {{--
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

        @if(count($vpnClients))
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
    --}}

</div>
@endsection

@push('scripts')
{{--
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
--}}
@endpush
