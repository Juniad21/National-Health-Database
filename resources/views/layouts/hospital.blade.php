<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'National Health DB') }} - Hospital Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex h-screen overflow-hidden">

    <!-- Emergency Banner Container for Flashing Alerts -->
    @yield('emergency_banner')

    <!-- Sidebar -->
    <aside class="w-64 bg-indigo-900 text-white flex flex-col shadow-xl hidden md:flex">
        <div class="h-20 flex items-center justify-center border-b border-indigo-800">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-100">Care<span class="text-indigo-400">Hub</span>
            </h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('hospital.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('hospital.dashboard') ? 'bg-indigo-800 text-white font-semibold shadow-md' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('hospital.billing.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('hospital.billing.*') ? 'bg-indigo-800 text-white font-semibold shadow-md' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>
                Billing & Insurance
            </a>
        </nav>

        <div class="p-4 border-t border-indigo-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-800 hover:text-white transition-colors">
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
            <h2 class="text-xl font-semibold text-gray-700">@yield('header_title', 'Hospital Admin')</h2>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span
                        class="text-sm font-semibold text-gray-800">{{ Auth::user()->hospital->name ?? 'Hospital' }}</span>
                    <span
                        class="text-xs text-indigo-600 font-medium tracking-wide border px-2 py-0.5 rounded-full bg-indigo-50 border-indigo-100 mt-1">Facility</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50/50 p-8">
            <div class="max-w-7xl mx-auto space-y-6">
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