<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'National Health DB') }} - Patient Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-teal-900 text-white flex flex-col shadow-xl hidden md:flex">
        <div class="h-20 flex items-center justify-center border-b border-teal-800">
            <h1 class="text-2xl font-bold tracking-wider text-teal-100">Care<span class="text-teal-400">Hub</span></h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('patient.dashboard') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Overview
            </a>

            <a href="{{ route('patient.scheduling') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.scheduling') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Appointments
            </a>

            <a href="{{ route('patient.medical_records') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.medical_records') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Medical Records
            </a>

            <a href="{{ route('patient.referrals') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.referrals') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                My Referrals
            </a>

            <a href="{{ route('patient.bills') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.bills') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                Bills & Payments
            </a>

            <a href="{{ route('patient.consents') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.consents') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
                Access Control
            </a>

            <a href="{{ route('patient.symptoms') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.symptoms') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                    </path>
                </svg>
                Symptom Checker
            </a>

            <a href="{{ route('patient.profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.profile.*') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Management
            </a>

            <a href="{{ route('patient.health_analytics') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('patient.health_analytics') ? 'bg-teal-800 text-white font-semibold shadow-md' : 'text-teal-100 hover:bg-teal-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Health Analytics
            </a>

            <a href="{{ route('patient.emergency.sos') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors bg-red-600 text-white font-bold shadow-lg shadow-red-100 hover:bg-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                Emergency SOS
            </a>
        </nav>

        <div class="p-4 border-t border-teal-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-teal-200 hover:bg-teal-800 hover:text-white transition-colors">
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
            <h2 class="text-xl font-semibold text-gray-700">@yield('header_title', 'Dashboard')</h2>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}</span>
                    <span class="text-xs text-gray-500">{{ Auth::user()->email }}</span>
                    <span
                        class="text-xs text-teal-600 font-medium tracking-wide border px-2 py-0.5 rounded-full bg-teal-50 border-teal-100 mt-1">Patient</span>
                </div>
                <div
                    class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold border-2 border-teal-200">
                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
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
    @stack('scripts')
</body>

</html>