<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | National Health Database</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Custom Keyframes for shifting background blobs */
        @keyframes blob-bounce {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            30% {
                transform: translate(30px, -50px) scale(1.1);
            }

            60% {
                transform: translate(-20px, 30px) scale(0.9);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-blob-slow {
            animation: blob-bounce 25s infinite ease-in-out;
        }

        .animate-blob-medium {
            animation: blob-bounce 18s infinite ease-in-out reverse;
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 15s infinite ease;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #22d3ee;
            box-shadow: 0 0 15px rgba(34, 211, 238, 0.2);
        }
    </style>
</head>

<body class="antialiased">

    <div
        class="relative min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-gray-950 via-[#063b2c] to-gray-950 animate-gradient overflow-hidden font-sans">

        <div class="absolute inset-0 z-0">
            <div
                class="absolute -top-40 -left-40 w-96 h-96 bg-[#0B5C45] rounded-full blur-[160px] opacity-60 animate-blob-slow">
            </div>
            <div
                class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-600 rounded-full blur-[160px] opacity-40 animate-blob-medium">
            </div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-900 rounded-full blur-[200px] opacity-30">
            </div>
        </div>

        <div class="relative z-10 w-full max-w-lg glass-card p-10 md:p-14 rounded-[32px]">

            <div class="text-center mb-10">
                <div class="inline-flex p-3 rounded-2xl bg-white/5 border border-white/10 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Seal" class="w-20 h-20">
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                    National Health <span class="text-cyan-400">Database</span>
                </h1>
                <p class="mt-3 text-sm font-medium text-gray-400 uppercase tracking-widest">
                    Authorized Access Portal
                </p>
            </div>

            @if (session('status'))
                <div
                    class="mb-5 text-sm font-medium text-emerald-400 text-center bg-emerald-950/30 p-3 rounded-lg border border-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 text-sm font-medium text-red-400 p-4 bg-red-950/50 rounded-xl border border-red-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="relative group">
                    <label for="email"
                        class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2 ml-1">Credential
                        ID</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-cyan-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="glass-input w-full pl-12 pr-4 py-4 rounded-xl focus:outline-none transition-all"
                            placeholder="your.email@gov.health.bd">
                    </div>
                </div>

                <div class="relative group">
                    <div class="flex items-center justify-between mb-2 ml-1">
                        <label for="password"
                            class="block text-xs font-bold text-gray-300 uppercase tracking-wider">Access Key</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-semibold text-cyan-400 hover:text-cyan-200 transition-colors">Forgot?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-cyan-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="glass-input w-full pl-12 pr-4 py-4 rounded-xl focus:outline-none transition-all"
                            placeholder="••••••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 text-[#0B5C45] bg-gray-900 border-gray-700 rounded focus:ring-[#0B5C45]">
                    <label for="remember_me" class="ml-2.5 block text-sm text-gray-300 font-medium cursor-pointer">Keep
                        session active</label>
                </div>

                <div class="relative group pt-2">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-emerald-600 to-cyan-500 rounded-xl blur-lg opacity-60 group-hover:opacity-100 transition duration-300">
                    </div>
                    <button type="submit"
                        class="relative w-full flex justify-center items-center py-4 px-6 rounded-xl text-sm font-bold tracking-widest uppercase text-white bg-gradient-to-r from-[#0B5C45] to-[#084534] shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Authorize Access
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="mt-10 text-center pt-8 border-t border-white/10">
                    <p class="text-sm text-gray-400">New User?</p>
                    <a href="{{ route('register') }}"
                        class="mt-2 inline-block font-bold text-cyan-400 hover:text-cyan-200 uppercase tracking-wider text-sm transition-colors">
                        Request Secure Account
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>