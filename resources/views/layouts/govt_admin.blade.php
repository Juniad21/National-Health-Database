<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'National Health DB') }} - Govt Admin</title>

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
    <aside class="w-64 bg-indigo-900 text-white flex flex-col shadow-xl hidden md:flex">
        <div class="h-20 flex items-center justify-center border-b border-indigo-800">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-100">Care<span class="text-indigo-400">Hub</span>
            </h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('govt_admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('govt_admin.dashboard') ? 'bg-indigo-800 text-white font-semibold shadow-md' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Govt Health Admin
            </a>

            <!-- Placeholder for other items -->
            <div class="pt-4 pb-2 px-4 text-xs font-semibold text-indigo-400 uppercase tracking-wider">
                Administration
            </div>
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-indigo-100 hover:bg-indigo-800 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Doctor Verifications
            </a>
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-indigo-100 hover:bg-indigo-800 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Hospital Monitoring
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-indigo-100 hover:bg-indigo-800 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                National Analytics
            </a>

            <a href="{{ route('govt_admin.audit_logs') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('govt_admin.audit_logs') ? 'bg-indigo-800 text-white font-semibold shadow-md' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Access Logs & Audit Trail
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
            <h2 class="text-xl font-semibold text-gray-700">@yield('header_title', 'Govt Health Administration')</h2>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <span class="text-xs text-indigo-600 font-medium tracking-wide border px-2 py-0.5 rounded-full bg-indigo-50 border-indigo-100 mt-1">DGHS Authority</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50/50 p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
