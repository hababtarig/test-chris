<div class="w-64 bg-blue-100 p-6">
    <h2 class="text-lg font-bold mb-6">Task Automation</h2>
    <nav>
        <ul class="space-y-4">
            <li><a href="{{ route('verquin.user') }}" class="block">User Management</a></li>
            <li><a href="{{ route('verquin.device') }}" class="block">Device Management</a></li>
            <li><a href="{{ route('verquin.stream') }}" class="block">Streaming Configuration</a></li>
        </ul>
    </nav>
    <form action="{{ route('logout') }}" method="POST" class="mt-10">
        @csrf
        <button class="bg-red-500 text-white py-2 px-4 rounded w-full">Sign out</button>
    </form>
</div>
