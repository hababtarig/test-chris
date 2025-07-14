<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Admin Welcome Page') }}
        </h2>
    </x-slot>

    <div class="flex space-x-4">
    <a href="{{ route('dashboard') }}"
       class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Admin Dashboard
    </a>
    <a href="{{ route('verquin') }}"
       class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Verquin App
    </a>
</div>

</x-app-layout>
