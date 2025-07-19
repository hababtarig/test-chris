<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Admin Welcome Page') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 transition">
                    🛠 Admin Dashboard
                </a>
                <a href="{{ route('verquin') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-green-700 transition">
                    🚀 Verquin App
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-gray-700 dark:text-gray-300">
            <p class="text-base">
                Welcome to the Admin Panel! Use the buttons above to navigate to the dashboard or the Verquin App.
            </p>
        </div>
    </div>
</x-app-layout>
