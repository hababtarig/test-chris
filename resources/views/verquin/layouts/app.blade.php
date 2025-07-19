<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verquin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

  <div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-72 m-4 bg-white shadow-xl rounded-2xl flex flex-col overflow-y-auto">
      <div class="px-6 py-4 text-2xl font-bold text-gray-800 border-b border-gray-300">
        Verquin Application
      </div>
      <nav class="flex-1 px-3 py-4 space-y-2">
        <a href="{{ route('verquin') }}"
           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 font-medium">
          Dashboard
        </a>
        <a href="{{ route('verquin.user') }}"
           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 font-medium">
          User Management
        </a>
        <a href="{{ route('verquin.device') }}"
           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 font-medium">
          Device Credentials
        </a>
        <a href="{{ route('verquin.stream') }}"
           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 font-medium">
          Streaming Configuration
        </a>
      </nav>
      <div class="flex justify-center py-4">
        <img src="{{ asset('images/logo_chris_proj.png') }}" alt="Logo" class="h-16">
      </div>
      <form method="POST" action="{{ route('logout') }}" class="px-4 pb-4">
        @csrf
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
          Logout
        </button>
      </form>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto p-8 bg-gray-100">
      @yield('content')
    </main>

  </div>
</body>
</html>