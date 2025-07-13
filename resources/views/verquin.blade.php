<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Welcome to Verquin App') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <p class="text-gray-700 dark:text-gray-300">You are now in the Verquin App.</p>
        </div>
    </div>
</x-app-layout>
