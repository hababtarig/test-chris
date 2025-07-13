<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Admin Welcome Page') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                Welcome, Admin! Where would you like to go?
            </p>

            <div class="flex space-x-4">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Admin Dashboard
                </a>
                <a href="{{ route('verquin') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Verquin App
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
