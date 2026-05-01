<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'National Health DB') }} - Doctor Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col shadow-xl hidden md:flex">
        <div class="h-20 flex items-center justify-center border-b border-blue-800">
            <h1 class="text-2xl font-bold tracking-wider text-blue-100">Care<span class="text-blue-400">Hub</span></h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('doctor.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('doctor.dashboard') ? 'bg-blue-800 text-white font-semibold shadow-md' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('doctor.profile.show') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('doctor.profile.*') ? 'bg-blue-800 text-white font-semibold shadow-md' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile Management
            </a>

        </nav>

        <div class="p-4 border-t border-blue-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-blue-200 hover:bg-blue-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </a>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-700">@yield('header_title', 'Doctor Portal')</h2>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="text-sm font-semibold text-gray-800">Dr.
                        {{ Auth::user()->doctor->first_name ?? 'Doctor' }}</span>
                    <span
                        class="text-xs text-blue-600 font-medium tracking-wide border px-2 py-0.5 rounded-full bg-blue-50 border-blue-100 mt-1">Medical
                        Provider</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50/50 p-8">
            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Notifications -->
                @if (session('success'))
                    <div class="px-6 py-4 bg-teal-50 border-l-4 border-teal-500 text-teal-800 rounded-r-xl shadow-sm rounded-l-md"
                        role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="px-6 py-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-xl shadow-sm rounded-l-md"
                        role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="px-6 py-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-xl shadow-sm rounded-l-md"
                        role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>